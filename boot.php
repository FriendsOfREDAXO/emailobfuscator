<?php
	if (!rex::isBackend()) {
		
		rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep) {
			$whitelistTemplates = rex_addon::get('rex_emailobfuscator')->getConfig('templates', []);
			$whitelistArticles = rex_addon::get('rex_emailobfuscator')->getConfig('articles', []);
			if (!empty($whitelistArticles)) {
				$whitelistArticles = explode(',', $whitelistArticles);
			}
			
			
			
			if (!in_array(rex_article::getCurrent()->getTemplateId(), $whitelistTemplates) && !in_array(rex_article::getCurrentId(),$whitelistArticles)) {
				$subject = $ep->getSubject();
				$subject = preg_replace_callback('/(\'|\"|)(mailto:[A-Za-z0-9]([A-Za-z0-9._+-]*[A-Za-z0-9]|())@[A-Za-z0-9]([A-Za-z0-9.\-]*[A-Za-z0-9]|())\.[A-Za-z]+)/', 'rex_emailobfuscator::encodeEmailLinks', $subject);
				$subject = preg_replace_callback('/([A-Za-z0-9](?:[A-Za-z0-9._+-]*[A-Za-z0-9]|(?:)))@([A-Za-z0-9](?:[A-Za-z0-9.\-]*[A-Za-z0-9]|(?:))\.[A-Za-z]+)/', 'rex_emailobfuscator::encodeEmail', $subject);
				
				$ep->setSubject($subject);
			}
		});
	}
?>