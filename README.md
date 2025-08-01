Email-Obfuscator: Verschlüsselung von E-Mailadressen zum Schutz vor Spam
========================================================================

Das [REDAXO](http://www.redaxo.org)-Addon sorgt dafür, dass alle E-Mailadressen auf deiner Website in verschlüsselter Form ausgegeben werden, so dass sie vor Spam geschützt sind.

## Funktionsweise

Durch die Integration des email_obfuscator Addons stehen verschiedene Verschleierungsmethoden für E-Mailadressen zur Verfügung:

### Sichere Methoden (empfohlen)

1. __XOR Verschlüsselung (Standard)__: Diese moderne Methode verwendet XOR-Verschlüsselung mit einem festen Schlüssel, um E-Mailadressen sicher zu verschleiern. Die verschlüsselten Daten werden in `data-*` Attributen gespeichert und automatisch per JavaScript entschlüsselt. Diese Methode ist exponentiell sicherer als die veralteten ROT13-Verfahren und bietet zuverlässigen Schutz vor Spam-Bots.
__Vorteile__: Starke Verschlüsselung, kein jQuery erforderlich, unterstützt mailto-Parameter wie `?subject=Betreff&body=Nachricht`

2. __XOR Verschlüsselung mit dynamischem Schlüssel__: Diese besonders sichere Variante generiert für jede Seite/jeden Artikel einen anderen Verschlüsselungsschlüssel basierend auf der Artikel-ID. Dadurch wird die gleiche E-Mailadresse auf verschiedenen Seiten unterschiedlich verschlüsselt, was die Sicherheit maximiert.
__Vorteile__: Höchste Sicherheitsstufe, kontextabhängige Verschlüsselung, kein jQuery erforderlich, unterstützt mailto-Parameter

### Veraltete Methoden (unsicher, nicht empfohlen)

3. __ROT13 Einhorn-Markup__ ⚠️ __Veraltet, unsicher__: Diese Methode findet alle E-Mailadressen und ersetzt deren `@` durch spezielles Einhorn-Markup: `<span class="unicorn"><span>_at_</span></span>`. ROT13 ist ein einfacher Caesar-Cipher, der von modernen Tools leicht geknackt werden kann.
__Bitte beachten__: Diese Methode benötigt __jQuery__ für die JavaScript-Funktionalität! jQuery wird seit Version 2.0 des Addons nicht mehr benötigt.

4. __ROT13 JavaScript Verschlüsselung__ ⚠️ __Veraltet, unsicher__: Diese Methode ersetzt E-Mailadressen durch JavaScript-Code mit ROT13-Verschlüsselung. ROT13 bietet keinen ausreichenden Schutz mehr gegen moderne Spam-Bots.
__Bitte beachten__: Diese Methode macht alle E-Mailadresse ohne klickbaren Link klickbar!

5. __CSS Methode ohne JavaScript__: Diese Methode verwendet "CSS display:none" zur Verschleierung und funktioniert ohne JavaScript. 
__Bitte beachten__: diese Methode entfernt den mailto-Link und verwandelt Adresse in name[at]domain.tld. Die Adresse ist damit nicht mehr klickbar.

6. __ROT13 JavaScript Verschlüsselung mit CSS Methode__ ⚠️ __Veraltet, unsicher__: Kombiniert ROT13-Verschlüsselung mit CSS-Fallback für deaktiviertes JavaScript.
__Bitte beachten__: Diese Methode macht alle E-Mailadresse ohne klickbaren Link klickbar!
__Bitte beachten__: diese Methode entfernt bei deaktiviertem JavaScript den mailto-Link und verwandelt Adresse in name[at]domain.tld. Die Adresse ist damit nicht mehr klickbar.

### Unterstützung für mailto-Parameter

Alle Verschlüsselungsmethoden unterstützen jetzt vollständig mailto-Links mit Parametern:
- __Einfache Parameter__: `info@example.com?subject=Anfrage`
- __Mehrere Parameter__: `info@example.com?subject=Hilfe&body=Bitte_kontaktieren_Sie_mich`
- __URL-kodierte Parameter__: `info@example.com?subject=Anfrage%20f%C3%BCr%20ein%20U-Boot`

Die Parameter bleiben nach der Entschlüsselung vollständig erhalten und funktionsfähig.

## Installation

Das Addon ist nach Aktivierung gleich funktionsfähig und verwendet standardmäßig die sichere __XOR Verschlüsselung__. Du brauchst keine weiteren Einstellungen vorzunehmen. Die benötigten Styles und Scripte werden automatisch geladen.

⚠️ __Wichtiger Sicherheitshinweis__: Falls du eine ältere Installation aktualisierst, die noch die veralteten ROT13-Methoden verwendet, solltest du in der Konfiguration auf eine der neuen XOR-Methoden wechseln, um die Sicherheit deiner E-Mailadressen zu gewährleisten.

Solltest du das benötigte CSS oder JavaScript manuell einbinden wollen, musst du in der Konfiguration das automatische Laden deaktivieren.

### Keine jQuery-Abhängigkeit mehr

Ab Version 2.0 des Addons wird __kein jQuery mehr benötigt__. Das JavaScript verwendet moderne Vanilla-DOM-APIs und ist mit allen aktuellen Browsern kompatibel. Bestehende Installationen funktionieren weiterhin ohne Änderungen.



### Automatische JavaScript-Einbindung

Das Addon lädt __automatisch__ das benötigte JavaScript für alle Verschlüsselungsmethoden, die eine clientseitige Entschlüsselung benötigen:

- XOR Verschlüsselung (einfach und dynamisch)
- ROT13 Einhorn-Markup
- ROT13 JavaScript Verschlüsselung
- ROT13 JavaScript mit CSS Fallback

Das JavaScript wird automatisch vor dem schließenden `</body>` Tag eingefügt. Du musst __nichts manuell einbinden__.

### Hinweise zur manuellen Einbindung (optional)

Falls du das automatische Laden deaktivieren möchtest, kannst du die Styles und Scripte manuell einbinden. Du kannst die Files, die das Addon bereitstellt, laden oder deren Inhalte in deine bestehenden CSS- und JavaScript-Files kopieren.

#### a) Dateien manuell laden (falls automatisches Laden deaktiviert)

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

#### b) Inhalte in eigene Dateien kopieren (falls automatisches Laden deaktiviert)

Falls du das automatische Laden deaktiviert hast, kannst du die Inhalte der CSS- und JS-Datei in deine eigenen Sourcen kopieren:

    assets/emailobfuscator.css
    assets/emailobfuscator.js

⚠️ Beachte dabei: Sollte eine neue Version des Addons erscheinen, in der das CSS oder JS geändert wurden, musst du diese Änderungen in deinen Sourcen anpassen!  
Bei Variante a) oben ist dies nicht notwendig.


## Sicherheit und Migration

### Warum XOR statt ROT13?

Die neuen XOR-Verschlüsselungsmethoden bieten exponentiell bessere Sicherheit als die veralteten ROT13-Verfahren:

- __ROT13__ ist ein einfacher Caesar-Cipher, der von modernen Tools und Spam-Bots leicht automatisch geknackt werden kann
- __XOR-Verschlüsselung__ mit Base64-URL-Safe-Kodierung ist deutlich komplexer und widerstandsfähiger gegen automatisierte Angriffe
- __Dynamische Schlüssel__ (XOR dynamic) machen die Entschlüsselung ohne Kontext praktisch unmöglich

### Migration von alten Methoden

Wenn du eine bestehende Installation verwendest:

1. Gehe zur Email-Verschlüsselung Konfigurationsseite im REDAXO Backend
2. Wähle __"XOR Verschlüsselung (Sicher, empfohlen)"__ für Standardsicherheit
3. Oder wähle __"XOR Verschlüsselung mit dynamischem Schlüssel (Sehr sicher, empfohlen)"__ für maximale Sicherheit
4. Speichere die Konfiguration

Keine Code-Änderungen, Template-Updates oder jQuery-Installation erforderlich. Alle bestehenden mailto-Links mit Parametern funktionieren sofort korrekt.

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

### Unterstützung für komplexe mailto-Parameter

Das Addon unterstützt jetzt vollständig komplexe mailto-Links mit allen gängigen Parametern:

```html
<!-- Einfache Betreff-Zeile -->
<a href="mailto:info@example.com?subject=Anfrage">Kontakt</a>

<!-- Betreff und Nachricht -->
<a href="mailto:support@example.com?subject=Hilfe%20benötigt&body=Bitte%20kontaktieren%20Sie%20mich">Support</a>

<!-- Mehrere Empfänger und Parameter -->
<a href="mailto:info@example.com,sales@example.com?subject=Angebot&cc=manager@example.com&body=Liebe%20Damen%20und%20Herren">Angebot anfordern</a>
```

Alle Parameter bleiben nach der Verschlüsselung und Entschlüsselung vollständig funktionsfähig.