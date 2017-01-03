<?php
	if (!rex::isBackend()) {
		rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
			// Bereite Ausnahmen vor: Templates und Artikel
			// Dort werden E-Mailadressen nicht verschlüsselt
			$whitelistTemplates = rex_addon::get('emailobfuscator')->getConfig('templates', []);
			$whitelistArticles = rex_addon::get('emailobfuscator')->getConfig('articles', '');
			if ($whitelistArticles != '') {
				$whitelistArticles = explode(',', $whitelistArticles);
			} else {
				$whitelistArticles = [];
			}
			
			if (!is_null(rex_article::getCurrent()) && !in_array(rex_article::getCurrent()->getTemplateId(), $whitelistTemplates) && !in_array(rex_article::getCurrentId(), $whitelistArticles)) {
				$subject = $ep->getSubject();
				
				// Ersetze mailto-Links (zuerst!)
				// Anmerkung: Attributwerte (hier: href) benötigen nicht zwingend Anführungsstriche drumrum,
				// deshalb prüfen wir zusätzlich noch auf '>' am Ende .
				$subject = preg_replace_callback('/mailto:(.*?)(?=[\'|"|\>])/', 'emailobfuscator::encodeEmailLinks', $subject);
				
				// Ersetze E-Mailadressen
				$subject = preg_replace_callback('/([\w\-\+\.]+)@([\w\-\.]+\.[\w]{2,})/', 'emailobfuscator::encodeEmail', $subject);
				
				// Injiziere CSS vors schließende </head> im Seitenkopf
				if ($this->getConfig('autoload_css')) {
					$cssFile = '<link rel="stylesheet" href="' . $this->getAssetsUrl('emailobfuscator.css?v=' . $this->getVersion()) . '">';
					$subject = str_replace('</head>', $cssFile . '</head>', $subject);
				}
				
				// Injiziere JavaScript vors schließende </body> der Seite
				if ($this->getConfig('autoload_js')) {
					$jsFile = '<script src="' . $this->getAssetsUrl('emailobfuscator.js?v=' . $this->getVersion()) . '"></script>';
					$subject = str_replace('</body>', $jsFile . '</body>', $subject);
				}
				
				$ep->setSubject($subject);
			}
		});
	}
?>