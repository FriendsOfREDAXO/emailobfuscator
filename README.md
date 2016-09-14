Email-Obfuscator: Verschlüsselung von E-Mailadressen zum Schutz vor Spam
========================================================================

Das [REDAXO](http://www.redaxo.org)-Addon sorgt dafür, dass alle E-Mailadressen auf deiner Website in verschlüsselter Form ausgegeben werden, so dass sie vor Spam geschützt sind. ✌️

## Funktionsweise

Das Addon findet alle E-Mailadressen und ersetzt deren `@` durch spezielles Einhorn-Markup: `<span class="unicorn"><span>_at_</span></span>`. Dadurch kann die E-Mailadresse nicht mehr so einfach von Bots ausgelesen werden und sollte ziemlich gut vor Spam geschützt sein.  
Weiterhin werden auch alle mailto-Links erkannt und verschlüsselt.

Beim Aufruf der Seite werden alle geschützten E-Mailadressen und mailto-Links mittels __JavaScript__ wieder entschlüsselt und in die ursprüngliche Form gebracht.

__CSS-Styles__ sorgen dafür, dass die geschützten E-Mailadressen auf der Website richtig angezeigt werden, also mit `@` statt Einhorn. Damit fällt der Wechsel von verschlüsselt nach unverschlüsselt nicht auf, und auch in Umgebungen ohne JavaScript wird eine verschlüsselte Adresse richtig dargestellt.

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
	if (rex_addon::get('emailobfuscator')->isAvailable()) { 
		?>
		<link rel="stylesheet" type="text/css" href="<?= rex_url::addonAssets('emailobfuscator', 'emailobfuscator.css'); ?>">
		<?php
	}
?>
```

__JavaScript__ am besten am Ende deiner Website vorm schließenden `</body>` einfügen:

```php
<?php
	if (rex_addon::get('emailobfuscator')->isAvailable()) {
		?>
		<script src="<?= rex_url::addonAssets('emailobfuscator', 'emailobfuscator.js'); ?>"></script>
		<?php
	}
?>
```

### b) Inhalte kopieren

Kopiere die Inhalte der CSS-Datei und der JS-Datei jeweils in deine Sourcen:

    assets/emailobfuscator.css
    assets/emailobfuscator.js

⚠️ Beachte dabei: Sollte eine neue Version des Addons erscheinen, in der das CSS oder JS geändert wurden, musst du diese Änderungen in deinen Sourcen anpassen!  
Bei Variante a) oben ist dies nicht notwendig.


## Sonstiges

### Verschlüsselung bestimmter E-Mailadressen verhindern

```php
<?php
  if (rex_addon::get('emailobfuscator')->isAvailable()) {
    emailobfuscator::whitelistEmail('email@example.com');
  }
?>
```

### Aufpassen bei Formularen und Attributen!

Das Addon filtert _alle_ E-Mailadressen im Code anhand eines Musters und verschlüsselt diese. In manchen Situationen ist das nicht unbedingt gewollt, z. B. wenn E-Mailadressen als HTML-Attribute oder in Formularen verwendet werden. Dort werden vom System natürlich die reinen, unverschlüsselten Adressen erwartet, und leider kann das Addon solche Umgebungen nicht eigenständig erkennen.

⚠️ Beachte bitte, dass du in manchen Umgebungen die E-Mailverschlüsselung unterbinden solltest, entweder durch Ausschließen bestimmter Templates oder Artikel in der Konfiguration, oder aber durch ein manuelles Whitelisting von Adressen wie im Abschnitt oben beschrieben.
