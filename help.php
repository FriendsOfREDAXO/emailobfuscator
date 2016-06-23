<?php
	echo rex_view::info($this->i18n('help_infotext1').'<br><br>'.$this->i18n('help_infotext2'));
	
	$code = "";
	$code .= "<?php".PHP_EOL;
	$code .= "  if (rex_addon::get('rex_emailobfuscator')->isAvailable()) {".PHP_EOL;
	$code .= "    ?>".PHP_EOL;
	$code .= "    <link rel=\"stylesheet\" type=\"text/css\" href=\"<?=rex_url::addonAssets('rex_emailobfuscator', 'rex_emailobfuscator.css');?>\">".PHP_EOL;
	$code .= "    <?php".PHP_EOL;
	$code .= "  }".PHP_EOL;
	$code .= "?>";
	
	$fragment = new rex_fragment();
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'CSS File einbinden', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');
	
	///
	
	$code = "";
	$code .= "<?php".PHP_EOL;
	$code .= "  if (rex_addon::get('rex_emailobfuscator')->isAvailable()) {".PHP_EOL;
	$code .= "    ?>".PHP_EOL;
	$code .= "    <script src=\"<?=rex_url::addonAssets('rex_emailobfuscator', 'rex_emailobfuscator.js');?>\"></script>".PHP_EOL;
	$code .= "    <?php".PHP_EOL;
	$code .= "  }".PHP_EOL;
	$code .= "?>";
	
	$fragment = new rex_fragment();
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'JS File einbinden', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');
	
	///
	
	$code = "";
	$code .= "<?php".PHP_EOL;
	$code .= "  if (rex_addon::get('rex_emailobfuscator')->isAvailable()) {".PHP_EOL;
	$code .= "    rex_emailobfuscator::whitelistEmail('email@example.com');".PHP_EOL;
	$code .= "  }".PHP_EOL;
	$code .= "?>";
	
	$fragment = new rex_fragment();
	$fragment->setVar('class', 'info', false);
	$fragment->setVar('title', 'Verschlüsselung verhindern', false); //todo
	$fragment->setVar('body', rex_string::highlight($code), false);
	echo $fragment->parse('core/page/section.php');
?>