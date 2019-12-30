Email-Obfuscator: Verschlüsselung von E-Mailadressen zum Schutz vor Spam
========================================================================

Das [REDAXO](http://www.redaxo.org)-Addon sorgt dafür, dass alle E-Mailadressen auf deiner Website in verschlüsselter Form ausgegeben werden, so dass sie vor Spam geschützt sind.

## Funktionsweise

Durch die Integration des email_obfuscator Addons von RexDude stehen verschiedene Verschleierungsmethoden für E-Mailadressen zur Verfügung:

1. __ROT13 Einhorn-Markup__: Diese Methode findet alle E-Mailadressen und ersetzt deren `@` durch spezielles Einhorn-Markup: `<span class="unicorn"><span>_at_</span></span>`. Dadurch kann die E-Mailadresse nicht mehr so einfach von Bots ausgelesen werden und sollte ziemlich gut vor Spam geschützt sein. Weiterhin werden auch alle mailto-Links erkannt und verschlüsselt.
Beim Aufruf der Seite werden alle geschützten E-Mailadressen und mailto-Links mittels __JavaScript__ wieder entschlüsselt und in die ursprüngliche Form gebracht. __CSS-Styles__ sorgen dafür, dass die geschützten E-Mailadressen auf der Website richtig angezeigt werden, also mit `@` statt Einhorn. Damit fällt der Wechsel von verschlüsselt nach unverschlüsselt nicht auf, und auch in Umgebungen ohne JavaScript wird eine verschlüsselte Adresse richtig dargestellt.
__Bitte beachten__: Diese Methode benötigt für die Einhorn-Markup Methode __jQuery__ für die JavaScript-Funktionalität!

2. __ROT13 JavaScript Verschlüsselung__: Um die Email-Adressen zu schützen, wird die E-Mailadresse durch ein JavaScript ersetzt, das die E-Mailadresse ins Dokument schreibt. Zur Verschleierung wird die Technik "ROT13 Encryption" angewendet.
__Bitte beachten__: Diese Methode macht alle E-Mailadresse ohne klickbaren Link klickbar!

3. __CSS Methode ohne JavaScript__: Um die Email-Adressen zu schützen, wird die Technik "CSS display:none" angewendet. 
__Bitte beachten__: diese Methode entfernt den mailto-Link und verwandelt Adresse in name[at]domain.tld. Die Adresse ist damit nicht mehr klickbar.

4. __ROT13 JavaScript Verschlüsselung mit CSS Methode__: Um die Email-Adressen zu schützen, werden die Techniken "CSS display:none" und "ROT13 Encryption" angewendet. Die CSS Methode kommt im `<noscript>` Tag zum Einsatz, falls JavaScript im Browser des Besuchers deaktiviert ist.
__Bitte beachten__: Diese Methode macht alle E-Mailadresse ohne klickbaren Link klickbar!
__Bitte beachten__: diese Methode entfernt bei deaktiviertem JavaScript den mailto-Link und verwandelt Adresse in name[at]domain.tld. Die Adresse ist damit nicht mehr klickbar.

## Installation

Das Addon ist nach Aktivierung gleich funktionsfähig, und du brauchst keine weiteren Einstellungen vorzunehmen. Die benötigten Styles und Scripte werden automatisch geladen.

Solltest du das benötigte CSS oder JavaScript manuell einbinden wollen, musst du in der Konfiguration das automatische Laden deaktivieren.



### Hinweise zur __ROT13 Einhorn-Markup__ Methode: CSS und JavaScript manuell einbinden

Du kannst die Styles und Scripte auf zwei Arten einbinden: Entweder du lädst die Files, die das Addon bereitstellt, oder du kopierst deren Inhalte in deine bestehenden CSS- und JavaScript-Files.

#### a) Dateien laden

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

#### b) Inhalte kopieren

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