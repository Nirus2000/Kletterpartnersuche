<?php
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

// Kein direkten Zugriff erlauben
if (strpos($_SERVER['PHP_SELF'], basename(__FILE__)))
{
    die('No direct calls allowed!');
}

/**
 * Installationsfunktion
 */
function kps_install()
{
    global $wpdb;

    // Tabelle existiert nicht, dann erstellen
    $result = $wpdb->query("SHOW TABLES LIKE '" . $wpdb->prefix . "kps_entries'");

    if ($result == 0)
    {
        $sql = "
                CREATE TABLE
                " . KPS_TABLE_ENTRIES . "
                (
                id int(11) NOT NULL auto_increment,
                authorName varchar(100) NOT NULL,
                authorId int(11) NOT NULL,
                authorEmail varchar(255) NOT NULL,
                password varchar(64) NOT NULL,
                activationHash varchar(32) NOT NULL,
                deleteHash varchar(32) NOT NULL,
                hash varchar(64) NOT NULL,
                content longtext NOT NULL,
                setDateTime bigint(8) UNSIGNED NOT NULL,
                unlockDateTime bigint(8) UNSIGNED NOT NULL,
                deleteDateTime bigint(8) UNSIGNED NOT NULL,
                authorSearchfor tinyint(1) NOT NULL,
                authorRule tinyint(1) NOT NULL,
                yourRule tinyint(1) NOT NULL,
                formOptions mediumtext NOT NULL,
                isLocked tinyint(1) NOT NULL,
                isLockedByAdmin tinyint(1) NOT NULL,
                lockedAutoReport TINYINT(1) NOT NULL,
                authorIp mediumtext NOT NULL,
                authorHost mediumtext NOT NULL,
                reportCount mediumtext NOT NULL,
                isReported tinyint(1) NOT NULL,
                PRIMARY KEY  (id)
                ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
        $result = $wpdb->query($sql);

        $sql = "
                CREATE TABLE
                " . KPS_TABLE_REQUIREMENT . "
                (
                id int(11) NOT NULL auto_increment,
                entryId int(11) NOT NULL,
                password varchar(64) NOT NULL,
                hash varchar(64) NOT NULL,
                userEmail varchar(255) NOT NULL,
                timestamp bigint(8) UNSIGNED NOT NULL,
                expire bigint(8) UNSIGNED NOT NULL,
                sendTimestamp BIGINT(8) UNSIGNED NOT NULL,
                sendData tinyint(1) NOT NULL,
                PRIMARY KEY  (id)
                ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
        $result2 = $wpdb->query($sql);
    }

    // Prüfen, ob Tabelle existiert nach Installation
    $resultkpsEntriesNotExist = $wpdb->query("SHOW TABLES LIKE '" . $wpdb->prefix . "kps_entries'");
    $resultkpsRequirementNotExist = $wpdb->query("SHOW TABLES LIKE '" . $wpdb->prefix . "kps_requirement'");

    if ($resultkpsEntriesNotExist != 0 && $resultkpsRequirementNotExist !=0)
    {
        // Hole Wordpress Haupt-Email-Adresse
        $sendEmail = get_bloginfo('admin_email', 'raw');

        // Setze Wordpress Haupt-Email-Adresse für Grundeinstellung
        $sendEmailCC = serialize(array(
            'kpsEmailCC'                        => get_bloginfo('admin_email', 'raw'),
            'kpsEmailReport'                    => get_bloginfo('admin_email', 'raw'),
            'kpsEmailInformation'               => 'false'
        ));

        // FormularOptionen
        $formOptions = serialize(array(
            'kpsFormOptionTelephone'            => 'false',
            'kpsFormOptionMobile'               => 'false',
            'kpsFormOptionWhatsapp'             => 'false',
            'kpsFormOptionSignal'               => 'false',
            'kpsFormOptionViper'                => 'false',
            'kpsFormOptionTelegram'             => 'false',
            'kpsFormOptionFacebookMessenger'    => 'false',
            'kpsFormOptionWire'                 => 'false',
            'kpsFormOptionHoccer'               => 'false',
            'kpsFormOptionSkype'                => 'false',
            'kpsFormOptionWebsite'              => 'false',
            'kpsFormOptionFacebook'             => 'false',
            'kpsFormOptionInstagram'            => 'false'
        ));

        // FormularOptionen Users
        $userSettings = serialize(array(
            'kpsUserRequireRegistration'        => 'false',
            'kpsUserRequirementRegistration'    => 'false',
            'kpsUserRequireAdminUnlock'         => 'false',
            'kpsUserProfilLink'                 => 'false',
            'kpsUserReport'                     => 'false',
            'kpsUserPrivacyAGB'                 => 'false',
            'kpsUserPrivacyDSGVO'               => 'false',
            'kpsUserAvatar'                     => 'false',
            'kpsUserRequirementReport'          => 'false',
            'kpsUserReport'                     => 'false'
        ));

        // Einträge melden
        $report = serialize(array(
            'kpsReportActivation'               => 'false',
            'kpsAdminSendReportAfter'           => 70,
            'kpsReportSpam'                     => 25,
            'kpsAutoReportSpam'                 => 50,
            'kpsReportUnreasonable'             => 25,
            'kpsAutoReportUnreasonable'         => 50,
            'kpsReportDouble'                   => 25,
            'kpsAutoReportDouble'               => 50,
            'kpsReportPrivacy'                  => 25,
            'kpsAutoReportPrivacy'              => 50,
            'kpsReportOthers'                   => 25,
            'kpsAutoReportOthers'               => 50
        ));

        // Legende anzeigen
        $legend = serialize(array(
            'kpsLegendActivated'                => 'true',
            'kpsLegendIconPak'                  => 1
        ));

        // Widget anzeigen
        $widget = serialize(array(
            'kpsWidgetIconPak'                  => 0
        ));

        // KPS-Counter
        $kpsCounter = serialize(array(
            'kpsAllEntrys'                      => 0,
            'kpsAllActivatedEntrys'             => 0,
            'kpsAllVerfifications'              => 0,
            'kpsAllSendRequirements'            => 0,
            'kpsAllDeleteEntrys'                => 0
        ));

        // Output
        $output = serialize(array(
            'kpsUnlockTime'                     => 'false',
            'kpsEmailSetTime'                   => 'true',
            'kpsEmailUnlockTime'                => 'true',
            'kpsEmailDeleteTime'                => 'true'
        ));

        // Einstellungen speichern inder der Wordpress Options-Tabelle
        add_option('kps_version', KPS_VER);                             // KPS-Version
        add_option('kps_formOptions', $formOptions);                    // Formularoptionen
        add_option('kps_formWordCount', 25);                            // mindestwörter im Formular
        add_option('kps_captchakeys', '');                              // Captcha-Keys
        add_option('kps_captcha', 'false');                             // Captcha
        add_option('kps_userSettings', $userSettings);                  // Registierung Pflicht für Einträge
        add_option('kps_backendPagination', 10);                        // Einträge pro Seite im Backend
        add_option('kps_frontendPagination', 5);                        // Einträge pro Seite im Frontend
        add_option('kps_mailFrom', $sendEmail);                         // Email-Versendungsadresse
        add_option('kps_mailFromCC', $sendEmailCC);                     // Email-Versendungsadresse Kopie
        add_option('kps_deleteEntryTime', 7776000);                     // Verfallszeit eines Eintrages -> entspricht 90 Tagen
        add_option('kps_deleteNoEntryTime', 5184000);                   // Verfallszeit eines nicht freigeschaltenen Eintrages -> entspricht 60 Tagen
        add_option('kps_agb', 0);                                       // AGB-Seite
        add_option('kps_dsgvo', 0);                                     // DSGVO-Seite
        add_option('kps_report', $report);                              // Einträge melden
        add_option('kps_icon', 3);                                      // FormOptions Icon-Pak
        add_option('kps_legend', $legend);                              // Legende
        add_option('kps_widget', $widget);                              // Widget
        add_option('kps_kpsCounter', $kpsCounter);                      // KPS-Counter
        add_option('kps_output', $output);                              // Ausgabeneinstellungen
    }
    else
    {
        die('Databases already exist. Installation aborted. Contact your administrator!');
    }
}

/**
 * Updatefunktion
 * Update von früheren Versionen des Plugins
 */
function kps_upgrade()
{
    global $wpdb;

    // Derzeitige Version holen
    $current_version = get_option('kps_version');

    if (version_compare($current_version, '1.1', '<'))
    {
        die('This version is discontinued. Please uninstall and delete this plugin. A new version is available!');
        exit;
    }

    if (version_compare($current_version, '1.2', '<='))
    {
        // Versendungszeitstempel einfügen
        $sql =  "ALTER TABLE
                " . KPS_TABLE_REQUIREMENT . "
                ADD sendTimestamp BIGINT(8) NOT NULL AFTER expire";
        $result = $wpdb->query($sql);

        // Widget anzeigen
        $widget = serialize(array(
            'kpsWidgetIconPak'  => 0
        ));

        update_option('kps_widget', $widget, 'yes');
    }

    if (version_compare($current_version, '1.3', '<='))
    {
        // Formular-Optionen updaten
        $formOptions = kps_unserialize(get_option('kps_formOptions'));

        $setFormOption['kpsFormOptionTelephone']            = $formOptions['kpsFormOptionTelephone'];
        $setFormOption['kpsFormOptionMobile']               = $formOptions['kpsFormOptionMobile'];
        $setFormOption['kpsFormOptionWhatsapp']             = 'false';
        $setFormOption['kpsFormOptionSignal']               = 'false';
        $setFormOption['kpsFormOptionViper']                = 'false';
        $setFormOption['kpsFormOptionTelegram']             = 'false';
        $setFormOption['kpsFormOptionFacebookMessenger']    = 'false';
        $setFormOption['kpsFormOptionWire']                 = 'false';
        $setFormOption['kpsFormOptionHoccer']               = 'false';
        $setFormOption['kpsFormOptionSkype']                = 'false';
        $setFormOption['kpsFormOptionWebsite']              = 'false';
        $setFormOption['kpsFormOptionInstagram']            = $formOptions['kpsFormOptionInstagram'];
        $setFormOption['kpsFormOptionFacebook']             = $formOptions['kpsFormOptionFacebook'];

        $setFormOption = serialize(array(
                                        $setFormOption
        ));

        update_option('kps_formOptions', $setFormOption);
    }

    if (version_compare($current_version, '1.5', '<='))
    {
        $output = serialize(array(
            'kpsUnlockTime'                                 => 'false',
            'kpsEmailSetTime'                               => 'true',
            'kpsEmailUnlockTime'                            => 'true',
            'kpsEmailDeleteTime'                            => 'true'
        ));

        add_option('kps_output', $output);
    }

    // Version updaten
    update_option('kps_version', KPS_VER);
}

