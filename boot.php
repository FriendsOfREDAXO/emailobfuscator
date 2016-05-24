<?php
	if (!rex::isBackend()) {
		rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep) {
			$subject = $ep->getSubject();
			$subject = preg_replace_callback('/(\'|\"|)(mailto:[A-Za-z0-9]([A-Za-z0-9._+-]*[A-Za-z0-9]|())@[A-Za-z0-9]([A-Za-z0-9.\-]*[A-Za-z0-9]|())\.[A-Za-z]+)/', 'rex_emailobfuscator::encodeEmailLinks', $subject);
			$subject = preg_replace_callback('/([A-Za-z0-9](?:[A-Za-z0-9._+-]*[A-Za-z0-9]|(?:)))@([A-Za-z0-9](?:[A-Za-z0-9.\-]*[A-Za-z0-9]|(?:))\.[A-Za-z]+)/', 'rex_emailobfuscator::encodeEmail', $subject);
			
			$ep->setSubject($subject);
		});
	}
?>