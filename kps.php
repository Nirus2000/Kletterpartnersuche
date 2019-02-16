<?php
/*
Plugin Name: Kletterpartner-Suche
Plugin URI: https://wordpress.org/plugins/kletterpartner-suche/
Description: „Kletterpartner-Suche“ verbindet Kletter-, Wander-, Tracking- und Sportfreunde miteinander.
Version: 1.7
Author: Alexander Ott
Author URI: http://nirus-online.de
License: GPLv2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: kps
Domain Path: /lang/
*/

/**
 * @author        Alexander Ott
 * @copyright     2018-2019
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
define('KPS_VER', '1.7');
define('KPS_FOLDER', plugin_basename(dirname(__FILE__)));
define('KPS_DIR', WP_PLUGIN_DIR . '/' . KPS_FOLDER);
define('KPS_RELATIV', plugins_url() . '/' . KPS_FOLDER);
define('KPS_ADMIN_URL', admin_url() . 'admin.php?page=' . KPS_FOLDER);
define('KPS_TABLE_ENTRIES', $wpdb->prefix . 'kps_entries');
define('KPS_TABLE_REQUIREMENT', $wpdb->prefix . 'kps_requirement');

/**
 * Backend laden
 */
include_once (KPS_DIR . '/admin/kps.php');                          // Übersichtsseite / Startseite
include_once (KPS_DIR . '/admin/kps-email.php');                    // Email Einstellungen + Vorlagen
include_once (KPS_DIR . '/admin/kps-privacy.php');                  // AGB's und DSGVO
include_once (KPS_DIR . '/admin/kps-settings.php');                 // Einstellungen
include_once (KPS_DIR . '/admin/kps-entries.php');                  // Einträge administrieren
include_once (KPS_DIR . '/admin/kps-design.php');                   // Design Einstellung
include_once (KPS_DIR . '/admin/kps-install.php');                  // Installation
include_once (KPS_DIR . '/admin/kps-uninstall.php');                // Deinstallation

/**
 * Klassen laden
 */
include_once (KPS_DIR . '/classes/kps-entries.php');                // Eintrag lesen
include_once (KPS_DIR . '/classes/kps-write.php');                  // Eintrag schreiben
include_once (KPS_DIR . '/classes/kps-activation.php');             // Aktivierung
include_once (KPS_DIR . '/classes/kps-delete.php');                 // Löschung
include_once (KPS_DIR . '/classes/kps-requirement.php');            // Anforderung
include_once (KPS_DIR . '/classes/kps-verify.php');                 // Anforderungsregistierung
include_once (KPS_DIR . '/classes/kps-reporting.php');              // Reporting
include_once (KPS_DIR . '/classes/kps-hash.php');                   // Hash-Generator

/**
 * Funktionen laden
 */
include_once (KPS_DIR . '/functions/kps-functions.php');            // Funktionen
include_once (KPS_DIR . '/functions/kps-backend-pagination.php');   // Blätterfunktion im Backend
include_once (KPS_DIR . '/functions/kps-frontend-pagination.php');  // Blätterfunktion im Frontend

/**
 * Exporter/ Erasure für personenbezogene Daten laden
 */
include_once (KPS_DIR . '/functions/kps-privacy.php');              // Exporter für peronenbezogene Daten

/**
 * Frontend laden
 */
include_once (KPS_DIR . '/frontend/kps-shortcode.php');             // Shortcode -> Zusammensetzung der Ausgabe
include_once (KPS_DIR . '/frontend/template/kps-template.php');     // Default Template
include_once (KPS_DIR . '/frontend/kps-entries.php');               // Einträge
include_once (KPS_DIR . '/frontend/kps-form.php');                  // Formular
include_once (KPS_DIR . '/frontend/kps-activation.php');            // Aktivierung
include_once (KPS_DIR . '/frontend/kps-delete.php');                // Löschung
include_once (KPS_DIR . '/frontend/kps-requirement.php');           // Anforderung
include_once (KPS_DIR . '/frontend/kps-verify.php');                // Anforderungsregistierung
include_once (KPS_DIR . '/frontend/kps-report.php');                // Eintrag melden

/**
 * Frontend Widget laden
 */
include_once (KPS_DIR . '/frontend/kps-widget.php');                // Widget

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