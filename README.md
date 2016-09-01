Email-Obfuscator: Verschlüsselung von E-Mailadressen zum Schutz vor Spam
=================================

Das [REDAXO](http://www.redaxo.org)-Addon sorgt dafür, dass alle E-Mailadressen auf deiner Website in verschlüsselter Form ausgegeben werden, so dass sie vor Spam geschützt sind. ✌️

## Funktionsweise

Das Addon findet alle E-Mailadressen und ersetzt deren `@` durch spezielles Einhorn-Markup: `<span class="unicorn"><span>_at_</span></span>`. Dadurch kann die E-Mailadresse nicht mehr so einfach von Bots ausgelesen werden und sollte damit ziemlich gut vor Spam geschützt sein.

CSS-Styles sorgen dafür, dass die geschützten E-Mailadressen auf der Website wieder richtig ausgegeben werden, also mit `@` statt Einhorn.

Weiterhin werden alle mailto-Links erkannt und durch eine JavaScript-Funktion ersetzt, die die E-Mailadresse in verschlüsselter Form enthält. Erst per Klick wird sie wieder entschlüsselt.

## Installation

Das Addon ist nach Aktivierung gleich funktionsfähig, und du brauchst keine weiteren Einstellungen vorzunehmen. Die benötigten Styles und Scripte werden automatisch geladen.

Solltest du das benötigte CSS oder JavaScript manuell einbinden wollen, musst du in der Konfiguration das automatische Laden deaktivieren.

⚠️ Bitte beachten: Das Addon benötigt __jQuery__ für die JavaScript-Funktionalität!

## CSS und JavaScript manuell einbinden

Du kannst die Styles und Scripte auf zwei Arten einbinden: Entweder du lädst die Files, die das Addon bereitstellt, oder du kopierst deren Inhalte in deine bestehenden CSS- und JavaScript-Files.

### a) Dateien laden

__CSS__ im `<head>` deiner Website einfügen:

```php
<?php
	if (rex_addon::get('rex_emailobfuscator')->isAvailable()) { 
		?>
		<link rel="stylesheet" type="text/css" href="<?= rex_url::addonAssets('rex_emailobfuscator', 'rex_emailobfuscator.css'); ?>">
		<?php
	}
?>
```

__JavaScript__ am besten am Ende deiner Website vorm schließenden `</body>` einfügen:

```php
<?php
	if (rex_addon::get('rex_emailobfuscator')->isAvailable()) {
		?>
		<script src="<?= rex_url::addonAssets('rex_emailobfuscator', 'rex_emailobfuscator.js'); ?>"></script>
		<?php
	}
?>
```

### b) Inhalte kopieren

Kopiere die Inhalte der CSS-Datei und der JS-Datei jeweils in deine Sourcen:

    assets/rex_emailobfuscator.css
    assets/rex_emailobfuscator.js

⚠️ Beachte dabei: Sollte eine neue Version des Addons erscheinen, in der das CSS oder JS geändert wurden, musst du diese Änderungen in deinen Sourcen anpassen!  
Bei Variante a) oben ist dies nicht notwendig.


## Sonstiges

### Verschlüsselung bestimmter E-Mailadressen verhindern

```php
<?php
  if (rex_addon::get('rex_emailobfuscator')->isAvailable()) {
    rex_emailobfuscator::whitelistEmail('email@example.com');
  }
?>
```









