<p>Dieses kleine aber praktische AddOn "verschüsselt" Emailadressen (als Links oder Plain) im Quelltext und "entschlüsselt" sie wieder via Javascript.</p>

<p>Benötigt wird dafür ein CSS Snippet zur Darstellung und eine Javascriptfunktion zur entschlüsselung. Beide Files werden mit diesem AddOn mitgeliefert und sollten wie folgt eingebunden werden:</p>
<?php
	$css = "";
	$css .= "<?php".PHP_EOL;
	$css .= "  if (rex_addon::get('rex_emailobfuscator')->isAvailable()) {".PHP_EOL;
	$css .= "    ?>".PHP_EOL;
	$css .= "    <link rel=\"stylesheet\" type=\"text/css\" href=\"<?=rex_url::addonAssets('rex_emailobfuscator', 'emailobfuscator.css');?>\">".PHP_EOL;
	$css .= "    <?php".PHP_EOL;
	$css .= "  }".PHP_EOL;
	$css .= "?>";
?>
<pre><?=highlight_string($css,true);?></pre>

<?php
	$js = "";
	$js .= "<?php".PHP_EOL;
	$js .= "  if (rex_addon::get('rex_emailobfuscator')->isAvailable()) {".PHP_EOL;
	$js .= "    ?>".PHP_EOL;
	$js .= "    <script src=\"<?=rex_url::addonAssets('rex_emailobfuscator', 'emailobfuscator.js');?>\"></script>".PHP_EOL;
	$js .= "    <?php".PHP_EOL;
	$js .= "  }".PHP_EOL;
	$js .= "?>";
?>
<pre><?=highlight_string($js,true);?></pre>

<p>Grundsätzlich kann man die Inhalte der beiden Files auch in eigene Files kopieren jedoch müsste man die Files im Falle eines AddOn-Updates manuell anpassen.</p>
