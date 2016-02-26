Redaxo 5 Addon - Email-Obfuscator
=================================

Dieses kleine aber praktische AddOn "verschüsselt" Emailadressen im Quelltext und "entschlüsselt" sie wieder via Javascript.

##Einbindung

Benötigt wird dafür ein CSS Snippet zur Darstellung und eine Javascriptfunktion zur entschlüsselung. Beide Files werden mit diesem AddOn mitgeliefert und sollten wie folgt eingebunden werden:

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

Grundsätzlich kann man die Inhalte der beiden Files auch in eigene Files kopieren jedoch müsste man die Files im Falle eines AddOn-Updates manuell anpassen.