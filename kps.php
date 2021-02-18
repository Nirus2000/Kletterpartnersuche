<?php
/*
Plugin Name: Kletterpartner-Suche
Plugin URI: https://wordpress.org/plugins/kletterpartner-suche/
Description: „Kletterpartner-Suche“ verbindet Kletter-, Wander-, Tracking- und Sportfreunde miteinander.
Version: 2.4.4
Requires at least: 4.9.8
Requires PHP: 5.2.4
Author: Alexander Ott
Author URI: http://nirus-online.de
License: GPL Version 3 or later
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: kps
Domain Path: /lang/
*/

/**
 * @author        Alexander Ott
 * @copyright     2018-2021
 * @email         kps@nirus-online.de
 *
 * All rights reserved
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * Dieses Programm ist Freie Software: Sie können es unter den Bedingungen
 * der GNU General Public License, wie von der Free Software Foundation,
 * Version 3 der Lizenz oder (nach Ihrer Wahl) jeder neueren
 * veröffentlichten Version, weiterverbreiten und/oder modifizieren.

 * Dieses Programm wird in der Hoffnung, dass es nützlich sein wird, aber
 * OHNE JEDE GEWÄHRLEISTUNG, bereitgestellt; sogar ohne die implizite
 * Gewährleistung der MARKTFÄHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
 * Siehe die GNU General Public License für weitere Details.

 * Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
 * Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
 */

/**
 * Datenbankzugriff für Prefix
 */
global $wpdb;

/**
 * Definitionen
 */
define('KPS_VER', '2.4.4');
define('KPS_FOLDER', plugin_basename(dirname(__FILE__)));
define('KPS_DIR', WP_PLUGIN_DIR . '/' . KPS_FOLDER);
define('KPS_ADMIN', KPS_DIR . '/admin');
define('KPS_CLASSES', KPS_DIR . '/classes');
define('KPS_FUNCTIONS', KPS_DIR . '/functions');
define('KPS_FRONTEND', KPS_DIR . '/frontend');
define('KPS_RELATIV', plugins_url() . '/' . KPS_FOLDER);
define('KPS_RELATIV_ADMIN', KPS_RELATIV . '/admin');
define('KPS_RELATIV_FRONTEND_GFX', KPS_RELATIV . '/frontend/gfx');
define('KPS_ADMIN_URL', admin_url() . 'admin.php?page=' . KPS_FOLDER);

/**
 * Datenbank
 */
define('KPS_TABLE_ENTRIES', $wpdb->prefix . 'kps_entries');
define('KPS_TABLE_REQUIREMENT', $wpdb->prefix . 'kps_requirement');

/**
 * Backend laden
 */
include_once (KPS_ADMIN . '/kps.php');                          // Übersichtsseite / Startseite
include_once (KPS_ADMIN . '/kps-email.php');                    // Email Einstellungen + Vorlagen
include_once (KPS_ADMIN . '/kps-privacy.php');                  // AGB's und DSGVO
include_once (KPS_ADMIN . '/kps-settings.php');                 // Einstellungen
include_once (KPS_ADMIN . '/kps-entries.php');                  // Einträge administrieren
include_once (KPS_ADMIN . '/kps-design.php');                   // Design Einstellung
include_once (KPS_ADMIN . '/kps-install.php');                  // Installation
include_once (KPS_ADMIN . '/kps-uninstall.php');                // Deinstallation

/**
 * Klassen laden
 */
include_once (KPS_CLASSES . '/kps-entries.php');                // Eintrag lesen
include_once (KPS_CLASSES . '/kps-write.php');                  // Eintrag schreiben
include_once (KPS_CLASSES . '/kps-activation.php');             // Aktivierung
include_once (KPS_CLASSES . '/kps-delete.php');                 // Löschung
include_once (KPS_CLASSES . '/kps-requirement.php');            // Anforderung
include_once (KPS_CLASSES . '/kps-verify.php');                 // Anforderungsregistierung
include_once (KPS_CLASSES . '/kps-reporting.php');              // Reporting
include_once (KPS_CLASSES . '/kps-hash.php');                   // Hash-Generator

/**
 * Funktionen laden
 */
include_once (KPS_FUNCTIONS . '/kps-functions.php');            // Funktionen
include_once (KPS_FUNCTIONS . '/kps-backend-pagination.php');   // Blätterfunktion im Backend
include_once (KPS_FUNCTIONS . '/kps-frontend-pagination.php');  // Blätterfunktion im Frontend
include_once (KPS_FUNCTIONS . '/kps-mail-contents.php');        // Email-Vorlagen

/**
 * Exporter/ Erasure für personenbezogene Daten laden
 */
include_once (KPS_FUNCTIONS . '/kps-privacy.php');              // Exporter für peronenbezogene Daten

/**
 * Frontend laden
 */
include_once (KPS_FRONTEND . '/kps-shortcode.php');             // Shortcode -> Zusammensetzung der Ausgabe
include_once (KPS_FRONTEND . '/template/kps-template.php');     // Default Template
include_once (KPS_FRONTEND . '/kps-entries.php');               // Einträge
include_once (KPS_FRONTEND . '/kps-form.php');                  // Formular
include_once (KPS_FRONTEND . '/kps-activation.php');            // Aktivierung
include_once (KPS_FRONTEND . '/kps-delete.php');                // Löschung
include_once (KPS_FRONTEND . '/kps-requirement.php');           // Anforderung
include_once (KPS_FRONTEND . '/kps-verify.php');                // Anforderungsregistierung
include_once (KPS_FRONTEND . '/kps-report.php');                // Eintrag melden

/**
 * Frontend Widget laden
 */
include_once (KPS_FRONTEND . '/kps-widget.php');                // Widget

/**
 * Hook laden
 */
include_once (KPS_DIR . '/kps-hooks.php');                          // Hook

/**
 * Unterschied finden zwischen install/upgrade Funktion,
 * wenn Plugin aktiv ist.
 */
function kps_activation()
{
    $current_version = get_option('kps_version');

    if ($current_version == false)
    {
        kps_install();
    }
    elseif ($current_version != KPS_VER)
    {
        kps_upgrade();
    }
}
register_activation_hook(__FILE__, 'kps_activation');

/**
 * Deinstallation
 */
function kps_deinstalling()
{
    kps_uninstallproceed();
}
register_uninstall_hook( __FILE__, 'kps_deinstalling' );