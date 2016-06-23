<?php
	if (!rex::isBackend()) {
		
		rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep) {
			$whitelistArticles = rex_addon::get('rex_emailobfuscator')->getConfig('articles');
			$whitelistTemplates = rex_addon::get('rex_emailobfuscator')->getConfig('templates');
			
			if (!in_array(rex_article::getCurrentId(),$whitelistArticles) && !in_array(rex_article::getCurrent()->getTemplateId(), $whitelistTemplates)) {
				$subject = $ep->getSubject();
				$subject = preg_replace_callback('/(\'|\"|)(mailto:[A-Za-z0-9]([A-Za-z0-9._+-]*[A-Za-z0-9]|())@[A-Za-z0-9]([A-Za-z0-9.\-]*[A-Za-z0-9]|())\.[A-Za-z]+)/', 'rex_emailobfuscator::encodeEmailLinks', $subject);
				$subject = preg_replace_callback('/([A-Za-z0-9](?:[A-Za-z0-9._+-]*[A-Za-z0-9]|(?:)))@([A-Za-z0-9](?:[A-Za-z0-9.\-]*[A-Za-z0-9]|(?:))\.[A-Za-z]+)/', 'rex_emailobfuscator::encodeEmail', $subject);
				
				$ep->setSubject($subject);
			}
		});
	}
?>