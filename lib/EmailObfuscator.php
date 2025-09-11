<?php

namespace FriendsOfRedaxo\EmailObfuscator;

use rex_addon;
use rex_config;

class EmailObfuscator {
	/**
	 * @var string[] Array with whitelisted email addresses  
	 */
    private static $whitelist = [];

	/**
	 * Perform obfuscation
	 * @param string $content Content
	 * @return string Content
	 */
	public static function obfuscate($content) {
		$emailobfuscator = rex_addon::get('emailobfuscator');
		$method = $emailobfuscator->getConfig('method', '') == '' ? 'rot13_unicorn' : $emailobfuscator->getConfig('method', '');

		if($method == 'rot13_unicorn') {
			// Ersetze mailto-Links (zuerst!)
			// Anmerkung: Attributwerte (hier: href) benötigen nicht zwingend Anführungsstriche drumrum,
			// deshalb prüfen wir zusätzlich noch auf '>' am Ende .
			$content = preg_replace_callback('/mailto:(.*?)(?=[\'\"\>])/', 'emailobfuscator::encodeEmailLinksUnicorn', $content);

			// Ersetze E-Mailadressen
			if (!$emailobfuscator->getConfig('mailto_only')) {
				$content = preg_replace_callback('/(?<![\/\w])([\w\-\+\.]+)@([\w\-\.]+\.[\w]{2,})(?![\w\/])/', 'emailobfuscator::encodeEmailUnicorn', $content);
			}

			// Injiziere CSS vors schließende </head> im Seitenkopf
			if ($emailobfuscator->getConfig('autoload_css')) {
				$cssFile = '<link rel="stylesheet" href="' . $emailobfuscator->getAssetsUrl('emailobfuscator.css?v=' . $emailobfuscator->getVersion()) . '">';
				$content = str_replace('</head>', $cssFile . '</head>', $content);
			}

			// Injiziere JavaScript vors schließende </body> der Seite
			if ($emailobfuscator->getConfig('autoload_js')) {
				$jsFile = '<script defer src="' . $emailobfuscator->getAssetsUrl('emailobfuscator.js?v=' . $emailobfuscator->getVersion()) . '"></script>';
				$content = str_replace('</body>', $jsFile . '</body>', $content);
			}
		}
		else {
			$atPos = strpos($content, '@');

			if ($atPos === false) {
				// nothing to do
				return $content;
			}

			if(!rex_config::get('emailobfuscator', 'mailto_only', false)) {
				// wrap anchor tag around email-adresses that don't have already an anchor tag around them
				$content = self::makeEmailClickable($content);
			}

			// replace all email addresses (now all wrapped in anchor tag) with spam aware version
			$content = preg_replace_callback('`\<a([^>]+)href\=\"mailto\:([^">]+)\"([^>]*)\>(.*?)\<\/a\>`ism', function ($m) {
				return self::encodeEmailJavaScript($m[2], $m[4], $m[1], $m[3]);
			}, $content);
		}
		return $content;
	}
	
	/**
	 * Encode E-Mail address
	 * @param string $email E-mail address
	 * @param string $text E-mail link text
	 * @param string $attributesBeforeHref attributes within a tag before href
	 * @param string $attributesAfterHref attributes within a tag after href
	 * @return string Encoded email link
	 */
	private static function encodeEmailJavaScript($email, $text = "", $attributesBeforeHref = '', $attributesAfterHref = '') {
		if (empty($text)) {
			$text = $email;
		}

		$attributesBeforeHref = trim($attributesBeforeHref);
		$attributesAfterHref = trim($attributesAfterHref);

		if ($attributesBeforeHref != '') {
			$attributesBeforeHref = str_replace('"', '\\"', $attributesBeforeHref);
			$attributesBeforeHref = $attributesBeforeHref;
		}

		if ($attributesAfterHref != '') {
			$attributesAfterHref = str_replace('"', '\\"', $attributesAfterHref);
			$attributesAfterHref = ' ' . $attributesAfterHref;
		}
	
		// Whitelist
		if(in_array($email, self::$whitelist)) {
			return '<a ' . $attributesBeforeHref . 'href="mailto:' . $email . '"' . $attributesAfterHref . '>' . $text . '</a>';
		}

		$emailobfuscator = rex_addon::get('emailobfuscator');
		
		$encoded = '';
		$method = $emailobfuscator->getConfig('method', '');

		if ($method == 'rot13_javascript' || $method == 'rot13_javascript_css') {
			// javascript version
			$encoded_mail_tag = str_rot13('<a ' . $attributesBeforeHref . 'href=\\"mailto:' . $email . '\\"' . $attributesAfterHref . '>' . $text . '</a>');
			$encoded = "<script>";
			$encoded .= "/* <![CDATA[ */";
			$encoded .= "document.write(\"" . $encoded_mail_tag . "\".replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<=\"Z\"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);}));";
			$encoded .= "/* ]]> */";
			$encoded .= "</script>";
		}
	
		// for users who have javascript disabled
		$exploded_email = explode("@", $email);
	
		if ($method == 'css' || $method == 'rot13_javascript_css') {
			if($method == 'rot13_javascript_css') {
				$encoded .= '<noscript>';
			}

			// make cryptic strings
			$string_snippet = strtolower(str_rot13(preg_replace("/[^a-zA-Z]/", "", $email)));
			$cryptValues = str_split($string_snippet, 5);

			$encoded .= '<span style="display: none;">' . $cryptValues[0] . '</span>' . $exploded_email[0] . '<span style="display: none;">' . strrev($cryptValues[0]) . '</span>[at]<span style="display: none;">' . $cryptValues[0] . "</span>" . $exploded_email[1];

			if($method == 'rot13_javascript_css') {
				$encoded .= '</noscript>';
			}
		}

		return $encoded;
	}
	
 	/**
	 * Encode E-Mail address links
	 * @param string[] $matches 
	 * @return string
	 */
	private static function encodeEmailLinksUnicorn($matches) {
		// Whitelist
		if(in_array($matches[1], self::$whitelist)) {
			return $matches[1];
		}

		$mail = $matches[1];
        $mail = str_rot13($mail); // ROT13-Transformation
        $mail = str_replace('@', '|', $mail); // Ersetze @ durch |, um E-Mailadressen von weiteren RegEx auszuschließen

        return 'javascript:decryptUnicorn(' . $mail . ')';
    }

 	/**
	 * Encode E-Mail address
	 * @param string[] $matches 
	 * @return string
	 */
	private static function encodeEmailUnicorn($matches) {
		// Whitelist
		if (($_SERVER['REQUEST_METHOD'] == 'POST' && self::in_array_r($matches[0], $_POST)) || self::in_array_r($matches[0], self::$whitelist)) {
            return $matches[0];
        }
        return $matches[1] . '<span class="unicorn"><span>_at_</span></span>' . $matches[2];
    }

    private static function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && self::in_array_r($needle, $item, $strict))) {
                return true;
            }
        }
        return false;
    }
	
	/**
	 * Make e-mail without links klickable.
	 * @param string $ret
	 * @return string
	 */
	private static function makeEmailClickable($ret) {
		$ret = ' ' . $ret;
		// in testing, using arrays here was found to be faster
		$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'emailobfuscator::make_email_clickable_callback', $ret);
	 
		// this one is not in an array because we need it to run last, for cleanup of accidental links within links
		$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
		$ret = trim($ret);

		return $ret;
	}

	private static function make_email_clickable_callback($matches) {
		$email = $matches[2] . '@' . $matches[3];

		return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
	}
	
	/**
	 * Add email address to whitelist
	 * @param string $email email address
	 */
	public static function whitelistEmail($email) {
        self::$whitelist[] = $email;
    }
}
