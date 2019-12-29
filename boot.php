<?php
if (!rex::isBackend()) {
	rex_extension::register('OUTPUT_FILTER', function ($ep) {
		$content = $ep->getSubject();

		// Prepare article and template exceptions - email addresses will not be encrypted
		$emailobfuscator = rex_addon::get('emailobfuscator');
		// bc - 'rot13_unicorn' is default method
		$whitelistTemplates = $emailobfuscator->getConfig('templates', []);
		$whitelistArticles = preg_grep('/^\s*$/s', explode(",", $emailobfuscator->getConfig('articles', '')), PREG_GREP_INVERT);

		if (!is_null(rex_article::getCurrent()) && !in_array(rex_article::getCurrent()->getTemplateId(), $whitelistTemplates) && !in_array(rex_article::getCurrentId(), $whitelistArticles)) {
			return emailobfuscator::obfuscate($content);
		}
		
		return $content;
    }, rex_extension::LATE);
}