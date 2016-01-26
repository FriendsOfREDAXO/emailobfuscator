<?php
	if (!rex::isBackend()) {
		function encodeEmailLinks ($matches) {
			$mail = '';
			for ($i = 0; $i < strlen($matches[2]); $i++) {
				$mail .= chr(ord(substr($matches[2],$i,1))+1);
			}
			
			if ($matches[1] == '"' || $matches[1] == '') {
				return $matches[1]."javascript:decryptUnicorn('".$mail."')";
			} else {
				return $matches[1]."javascript:decryptUnicorn(\"".$mail."\")";
			}
		}
		
		function encodeEmail ($matches) {
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && in_array($matches[0], $_POST)) {
				return $matches[0];
			}
			return $matches[1].'<span class="unicorn">_at_</span>'.$matches[2];
		}
		
		rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep) {
			$subject = $ep->getSubject();
			$subject = preg_replace_callback('/(\'|\"|)(mailto:[A-Za-z0-9]([A-Za-z0-9._+-]*[A-Za-z0-9]|())@[A-Za-z0-9]([A-Za-z0-9.\-]*[A-Za-z0-9]|())\.[A-Za-z]+)/', 'encodeEmailLinks', $subject);
			$subject = preg_replace_callback('/([A-Za-z0-9](?:[A-Za-z0-9._+-]*[A-Za-z0-9]|(?:)))@([A-Za-z0-9](?:[A-Za-z0-9.\-]*[A-Za-z0-9]|(?:))\.[A-Za-z]+)/', 'encodeEmail', $subject);
			
			$ep->setSubject($subject);
		});
	}
?>