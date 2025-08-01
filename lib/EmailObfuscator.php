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
	 * XOR encrypt/decrypt function (symmetric)
	 * @param string $text Text to encrypt/decrypt
	 * @param string $key Encryption key
	 * @return string Encrypted/decrypted text
	 */
	private static function xorCrypt($text, $key) {
		$result = '';
		$keyLength = strlen($key);
		
		for ($i = 0; $i < strlen($text); $i++) {
			$result .= chr(ord($text[$i]) ^ ord($key[$i % $keyLength]));
		}
		
		return $result;
	}

	/**
	 * URL-safe base64 encode
	 * @param string $data Data to encode
	 * @return string Encoded data
	 */
	private static function base64UrlEncode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	/**
	 * URL-safe base64 decode
	 * @param string $data Data to decode
	 * @return string Decoded data
	 */
	private static function base64UrlDecode($data) {
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}

	/**
	 * Perform obfuscation
	 * @param string $content Content
	 * @return string Content
	 */
	public static function obfuscate($content) {
		$emailobfuscator = rex_addon::get('emailobfuscator');
		$method = $emailobfuscator->getConfig('method', '') == '' ? 'xor_simple' : $emailobfuscator->getConfig('method', '');

		if($method == 'rot13_unicorn') {
			// Ersetze mailto-Links (zuerst!)
			// Anmerkung: Attributwerte (hier: href) benötigen nicht zwingend Anführungsstriche drumrum,
			// deshalb prüfen wir zusätzlich noch auf '>' am Ende .
			$content = preg_replace_callback('/mailto:(.*?)(?=[\'\"\>])/', 'FriendsOfRedaxo\EmailObfuscator\EmailObfuscator::encodeEmailLinksUnicorn', $content);

			// Ersetze E-Mailadressen
			if (!$emailobfuscator->getConfig('mailto_only')) {
				$content = preg_replace_callback('/([\w\-\+\.]+@[\w\-\.]+\.[\w]{2,}(?:\?[^\s<>]*?)?)(?=[\s<>]|[.!?]\s|$)/i', 'FriendsOfRedaxo\EmailObfuscator\EmailObfuscator::encodeEmailUnicorn', $content);
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
		else if($method == 'xor_simple' || $method == 'xor_dynamic') {
			// New XOR-based methods
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
			$content = preg_replace_callback('`\<a([^>]+)href\=\"mailto\:([^">]+)\"([^>]*)\>(.*?)\<\/a\>`ism', function ($m) use ($method) {
				return self::encodeEmailXor($m[2], $m[4], $m[1], $m[3], $method);
			}, $content);

			// Inject external JavaScript file for XOR decryption
			$emailobfuscator = rex_addon::get('emailobfuscator');
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
	 * Encode E-Mail address using XOR encryption
	 * @param string $email E-mail address
	 * @param string $text E-mail link text
	 * @param string $attributesBeforeHref attributes within a tag before href
	 * @param string $attributesAfterHref attributes within a tag after href
	 * @param string $method XOR method (xor_simple or xor_dynamic)
	 * @return string Encoded email link
	 */
	private static function encodeEmailXor($email, $text = "", $attributesBeforeHref = '', $attributesAfterHref = '', $method = 'xor_simple') {
		if (empty($text)) {
			$text = $email;
		}

		$attributesBeforeHref = trim($attributesBeforeHref);
		$attributesAfterHref = trim($attributesAfterHref);

		if ($attributesBeforeHref != '') {
			$attributesBeforeHref = str_replace('"', '\\"', $attributesBeforeHref);
		}

		if ($attributesAfterHref != '') {
			$attributesAfterHref = str_replace('"', '\\"', $attributesAfterHref);
			$attributesAfterHref = ' ' . $attributesAfterHref;
		}
	
		// Whitelist
		if(in_array($email, self::$whitelist)) {
			return '<a ' . $attributesBeforeHref . 'href="mailto:' . $email . '"' . $attributesAfterHref . '>' . $text . '</a>';
		}

		// Generate context for dynamic method
		$context = '';
		if ($method === 'xor_dynamic') {
			// Use current article ID or page identifier as context
			$context = \rex_article::getCurrentId() ?: 'default';
		}

		// Encrypt email and text
		$encryptedEmail = self::encryptEmailData($email, $method, $context);
		$encryptedText = self::encryptEmailData($text, $method, $context);
		$encryptedAttributes = '';
		
		if ($attributesBeforeHref || $attributesAfterHref) {
			$allAttributes = trim($attributesBeforeHref . ' ' . $attributesAfterHref);
			$encryptedAttributes = self::encryptEmailData($allAttributes, $method, $context);
		}

		// Create obfuscated span with data attributes
		$dataMethod = $method === 'xor_simple' ? 'xor-simple' : 'xor-dynamic';
		$dataContext = $method === 'xor_dynamic' ? ' data-context="' . htmlspecialchars($context) . '"' : '';
		
		return '<span class="email-obfuscated" data-method="' . $dataMethod . '"' . $dataContext . 
			   ' data-email="' . htmlspecialchars($encryptedEmail) . '"' . 
			   ' data-text="' . htmlspecialchars($encryptedText) . '"' . 
			   ($encryptedAttributes ? ' data-attributes="' . htmlspecialchars($encryptedAttributes) . '"' : '') . 
			   '>' . htmlspecialchars($text) . '</span>';
	}

	/**
	 * Encrypt email data using specified method
	 * @param string $data Data to encrypt
	 * @param string $method Encryption method
	 * @param string $context Context for dynamic method
	 * @return string Encrypted data
	 */
	private static function encryptEmailData($data, $method, $context = '') {
		if ($method === 'xor_simple') {
			$key = 'EmailObfuscatorKey2024';
		} else {
			// Generate dynamic key using simple hash (compatible with JavaScript)
			$baseKey = 'EmailObfuscator';
			$fullString = $baseKey . $context;
			$hash = 0;
			for ($i = 0; $i < strlen($fullString); $i++) {
				$char = ord($fullString[$i]);
				$hash = (($hash << 5) - $hash) + $char;
				$hash = $hash & 0xFFFFFFFF; // Keep as 32-bit integer
			}
			// Convert to hex and take first 16 characters
			$hashHex = dechex(abs($hash));
			$key = substr(str_repeat($hashHex, 4), 0, 16);
		}
		
		$encrypted = self::xorCrypt($data, $key);
		return self::base64UrlEncode($encrypted);
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
		$fullEmail = $matches[1]; // Now contains the full email with parameters
		$emailPart = preg_replace('/\?.*$/', '', $fullEmail); // Extract email part for whitelist check
		
		// Whitelist
		if ((isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' && self::in_array_r($matches[0], $_POST)) || self::in_array_r($emailPart, self::$whitelist)) {
            return $matches[0];
        }
        
        // Split email part for obfuscation  
        $emailParts = explode('@', $emailPart);
        $parameters = str_replace($emailPart, '', $fullEmail); // Get the parameter part
        
        return $emailParts[0] . '<span class="unicorn"><span>_at_</span></span>' . $emailParts[1] . $parameters;
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
		// Simplified approach: match email + parameters until whitespace, <, >, or punctuation at end
		// This handles the most common real-world cases properly
		$ret = preg_replace_callback('#([\s>])([a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,}(?:\?[^\s<>]*?)?)(?=[\s<>]|[.!?]\s|$)#i', 'FriendsOfRedaxo\EmailObfuscator\EmailObfuscator::make_email_clickable_callback', $ret);
	 
		// this one is not in an array because we need it to run last, for cleanup of accidental links within links
		$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
		$ret = trim($ret);

		return $ret;
	}

	private static function make_email_clickable_callback($matches) {
		$fullEmail = $matches[2];
		// Extract just the email part for display (before the ?)
		$displayEmail = preg_replace('/\?.*$/', '', $fullEmail);

		return $matches[1] . "<a href=\"mailto:$fullEmail\">$displayEmail</a>";
	}
	
	/**
	 * Add email address to whitelist
	 * @param string $email email address
	 */
	public static function whitelistEmail($email) {
        self::$whitelist[] = $email;
    }
}
