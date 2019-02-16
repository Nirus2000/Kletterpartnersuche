# Kletterpartner-Suche
Contributors: Nirus
Tags: Klettern, Climb, Wandern, Walking, Trekking, Climbing, Climbpartner, Walkingpartner, Tekkingpartner, Kletterpartner, Wanderpartner
Requires at least: 4.9.8
Requires PHP: 7.0
Tested up to: 5.0.3
Stable tag: 1.7
License: GPLv2 or later

Die **Kletterpartner-Suche** verbindet Kletter-, Wander-, Tracking- und Sportfreunde miteinander. "Kletterpatner-Suche" wurde für den Sächsische Bergsteigerbund e. V. entwickelt. Sektion des Deutschen Alpenvereins ist ein Bund von Einzelmitgliedern, kleinen und größeren Clubs und der größte Bergsportverband der Region für bergsportliche Aktivitäten in der Sächsischen Schweiz und deren Umgebung.

## Was kann dieses Plugin alles?

- Einfache Einbindung durch Shortcodes
- Automatisches Versenden von Emails für die Aktivierung und Verifizierung
- User können Ihre Einträge selbständig löschen
- Ein Widget gibt dem Besucher eine schnelle Übersicht
- Die Einträge kann auf einer separaten Seite angezeigt werden
- Das Eingabe-Formular kann auf separaten Seite angezeigt werden
- Übersichtlicher Adminpanel
- Editierbare Email-Vorlagen für jeden Aktion
- Einträge melden
- Integierung der eigenen AGB's / DSGVO
- und und und...

## Installation

1. Laden Sie die Plugin-Dateien in das Verzeichnis /wp-content/plugins/plugin-name hoch oder installieren Sie das Plugin direkt über den WordPress Plugins-Bildschirm.
2. Aktivieren Sie das Plugin über den 'Plugins'-Bildschirm in WordPress
3. Verwenden Sie die Kletterpartner->Einstellungen, um das Plugin zu konfigurieren
3. Setzen Sie die AGB's und DSGVO unter Kletterpartner->AGB's / DSGVO um die Nutzer auf die Verwendung Ihrer Daten hinzuweisen
4. Erstellen Sie eine Seite unter Seiten
5. Geben Sie der Seite einen Titel.
6. Schreibe in den Seitenkontex den Shortcode: `[kps-shortcode]`
7. Veröffentlichen Sie diese Seite.
8.1. Geh im Hauptmenü unter Design->Menüs und fügen Sie Seite einem Menü hinzu.
8.2. Alternativ können Sie ein neues Menü erstellen und die Seite dort hinzufügen
8. Speichern. Veröffentlichen, Fertig

## Hinweis
*Natürlich können Sie den Shortcode auch in einem Beitrag implementieren.*


## Frequently Asked Questions

Sollten Sie Frage oder Probleme haben, nutzen Sie das Supportforum unter Wordpress.org
[Link zum Wordpress-Plugin Verzeichnis](https://wordpress.org/support/plugin/kletterpartner-suche)
Ich werde mein Bestes tun, um so schnell wie möglich zu antworten.

### Kann ich helfen beim Übersetzen des Plugins?

Super! Schreib uns einfach eine Nachricht ins Supportforum und ich werde Dich als Editor registrieren lassen. Sollten schon mehrere Editoren für deine Sprache geben, so nutz einfach die
[GlotPress](https://translate.wordpress.org/projects/wp-plugins/kletterpartner-suche). Hier kannst Du uns die String übersetzen.

### Wie kann ich aktiv am Plugin mitarbeiten oder modifizieren?

Das kannst du. Unter https://github.com/Nirus2000/Kletterpartnersuche kannst du aktiv am Quellcode mitarbeiten, Bugs bearbeiten und natürlich auch Verbesserungen mit einbringen. Wenn Du einen Wordpress-Account hast, so schreib mich an und ich werde Dich mit bei den Committer/Mitwirkende hinzufügen, sowie freue ich mich über jede Hilfe im Support-Team.

### Kann ich das Formular auch in einer anderen Seite/Menü einblenden?

Ja, kannst Du.
Gehe wie in der Installation vor und schreibe statt dem Standart-Shortcode, den Shortcode `[kps-shortcode show-form-only="true"]` ein.

### Kann ich die Einträge auch ohne Formular-Button anzeigen?

Ja, kannst Du.
Gehe wie in der Installation vor und schreibe statt dem Standart-Shortcode, den Shortcode `[kps-shortcode button-write="false"]` ein.


## Support

Bitte benutzen Sie das Support-Forum. (https://wordpress.org/support/plugin/kletterpartner-suche)
Um schnellen Support zu leisten, schreibe Sie Schritt für Schritt auf, wie Sie zu dem Problem/Fehler gekommen sind ggf. fügen Sie einzelne Teilschritte ein.

Wenn Sie mir eine E-Mail senden, werde ich nicht antworten.

## Screenshots

1. Eingabeformular
2. Ausgabe
3. Einstellungsmöglichkeiten im Adminpanel
4. Einstellungsmöglichkeiten im Adminpanel
5. Einstellungsmöglichkeiten im Adminpanel
6. Einstellungsmöglichkeiten im Adminpanel
7. Einfaches integieren der DSGVO oder AGB's
8. Einstellung der Email-Vorlagen für jedes Bedürfnis
9. Einstellung von verschiedenen Icons, Widget und anderen Dingen

## Upgrade Notice

Fehlerbehebung und Erweiterung des Plugins

## Changelog

### v. 1.7 =
*[16.02.2019]*

* Add: Design Uhrzeit in Datumsangabe
* Fix: CSS-Style Eintrag-Button
* Fix: Einstellungen Email-Adressen
* Fix: Shorttag Beispiele Email-Templates
* Fix: Aktivierungsmail Freigabezeit
* Fix: Übersetzungen (Singular/Plural)
* Fix: Fehlende/Falsche Übersetzungen
* Fix: Formular Close-SVG
* Fix: Email Header-Information
* Fix: Email Copy-Information
* Upd: Checkbox Background-Color
* Upd: FontAwesome to 5.7.2
* Upd: DashIcon im Formular
* Upd: Language-Source-Code Translation in English
* Upd: Readme-Datei
* Upd: Übersetzungen de_DE
* Upd: Übersetzungen de_DE_formal
* Upd: Übersetzungen en_US

### v. 1.6
*[21.11.2018]*

* Add: Design Shortcode-Übersicht
* Add: GitHub URL
* Fix: Einstellungsübersicht (kurz)
* Fix: Pflichtfelder im Formular
* Fix: Verifizierung
* Fix: Install
* Fix: Datenbank
* Upd: Readme-Datei
* Upd: FAQs
* Upd: Übersetzungen de_DE
* Upd: Übersetzungen de_DE_formal
* Upd: Übersetzungen en_US

### v. 1.5
*[20.11.2018]*

* Fix: Abstand zu den Optionsfeldern
* Fix: Readme-Datei

### v. 1.4
*[14.10.2018]*

* Add: DashIcon im Formular
* Add: Formular-Option Messenger Viper
* Add: Formular-Option Messenger Signal
* Add: Formular-Option Messenger Telegram
* Add: Formular-Option Messenger Whatsapp
* Add: Formular-Option Messenger Facebook
* Add: Formular-Option Messenger Hoccer
* Add: Formular-Option Messenger Skype
* Add: Formular-Option Messenger Wire
* Add: Formular-Option Webseite
* Fix: Widget Keine Einträge
* Fix: Formular Fehlermeldung Eintrag vorhanden
* Fix: Formular Fehlermeldung Eintrag Insert in Datenbank
* Fix: Javascript in header laden
* Fix: HTML5 Input-Type "tel"
* Fix: Email versenden, wenn Eintrag freigegeben wurde
* Fix: Legende
* Upd: Code-Cleaning
* Upd: Übersetzungen de_DE
* Upd: Übersetzungen de_DE_formal
* Upd: Übersetzungen en_US

### v. 1.3
*[11.10.2018]*

* Add: Widget
* Add: Export für personenbezogene Daten
* Add: Erasure für personenbezogene Daten
* Fix: zusätzliche Informationen per Email
* Fix: Formular-Option
* Upd: AGBs / DSGVO Status
* Upd: Aktivierung
* Upd: Code-Cleaning
* Upd: Übersetzungen de_DE
* Upd: Übersetzungen de_DE_formal
* Upd: Übersetzungen en_US
* Del: Form-Option Google+ (Closed Aug. 2019)


### v. 1.2
*[05.10.2018]*

* Add: Fastlink "gesperrte durch Meldung"
* Add: Fastlink "Auto-Sperre"
* Add: Meldungen werden geloggt
* Add: Einstellungen -> Meldungen
* Add: Reporting
* Add: Design
* Add: Icon-Pak
* Add: Legende
* Add: Legenden-Pak
* Add: Design
* Add: Tabs in den Einstellungen
* Fix: Link "Bearbeiten" für Moderatoren
* Fix: Autor-IP anzeigen
* Fix: Formlar Checkbox "AGBs/DSGVO"
* Fix: Prüfung Checkbox "AGBs/DSGVO"
* Fix: Shorttage %linkactivation%
* Fix: Shorttage %linkdelete%
* Fix: Formular Fehlermeldungen
* Fix: Kleine Übersetzungsfehler
* Upd: Komplettes Redesign im Adminbereich
* Upd: div. Variablen umbenannt
* Upd: CSS
* Upd: Code-Cleaning
* Upd: Übersetzungen de_DE
* Upd: Übersetzungen de_DE_formal


### v. 1.1
*[27.09.2018]*

* Fix: Berechtigungen im Adminbereich
* Fix: Umbruch der Options-Label im Formular
* Fix: Deinstallation nur durch Rolle "Administrator möglich"
* Upd: Code-Cleaning
* Upd: Übersetzungen de_DE
* Upd: Übersetzungen de_DE_formal


### v. 1.0
* 1st Release