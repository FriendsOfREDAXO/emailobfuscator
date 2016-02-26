Redaxo 5 Addon - Email-Obfuscator
=================================

Dieses kleine aber praktische AddOn "verschüsselt" Emailadressen im Quelltext und "entschlüsselt" sie wieder via Javascript.

##Einbindung

Damit die Emailverschleierung aktiv wird, muss lediglich ein CSS- und ein JS-File eingebunden werden.

###CSS-File

```
<?php
	if (rex_addon::get('rex_emailobfuscator')->isAvailable()) { 
		?>
		<link rel="stylesheet" type="text/css" href="<?=rex_url::addonAssets('rex_emailobfuscator', 'rex_emailobfuscator.css');?>">
		<?php
	}
?>
```

###JS-File

```
<?php
	if (rex_addon::get('rex_emailobfuscator')->isAvailable()) {
		?>
		<script src="<?=rex_url::addonAssets('rex_emailobfuscator', 'rex_emailobfuscator.js');?>"></script>
		<?php
	}
?>
```