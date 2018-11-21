<?php
/**
 * @author        Alexander Ott
 * @copyright     2018
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
 * Sprachdatei laden für Frontend und Backend
 */
function kps_load_lang()
{
    load_plugin_textdomain('kps', false, KPS_FOLDER . '/lang');
}
add_action('plugins_loaded', 'kps_load_lang');

/**
 * Administrations-Menü laden
 */
function kps_adminmenu()
{
    global $wpdb;

    // offene Einträge zählen
    $openEntry  = $wpdb->get_var("SELECT COUNT(*) FROM " . KPS_TABLE_ENTRIES . " WHERE isLockedByAdmin = 0");
    $isAutoLock   = $wpdb->get_var("SELECT COUNT(*) FROM " . KPS_TABLE_ENTRIES . " WHERE lockedAutoReport  = 1");
    $isReport   = $wpdb->get_var("SELECT COUNT(*) FROM " . KPS_TABLE_ENTRIES . " WHERE isReported = 1");

    // AGB's und DSGVO gesetzt?
    $noAGB = ( get_option('kps_agb', false) ) ? 0 : 1;
    $noDSGVO = ( get_option('kps_dsgvo', false) ) ? 0 : 1;

    // offene Updates ermitteln
    $updatePluginCount = $openEntry + $isReport + $isAutoLock + $noAGB + $noDSGVO;

    // Ausgabe, was fehlt -> AGB oder DSGVO
    if ($noAGB && $noDSGVO) { $noAGBDSGVOInfo = '&#167;'; }
    elseif ($noAGB && !$noDSGVO) { $noAGBDSGVOInfo = esc_html(__( 'AGBs', 'kps' )); }
    elseif (!$noAGB && $noDSGVO) { $noAGBDSGVOInfo = esc_html(__( 'DSGVO', 'kps' )); }
    else { $noAGBDSGVOInfo = 0; }

    // Ausgabe gemeldete Einträge
    $isReportBubble = ($isReport > 0) ? "<span class='update-plugins count-{$isReport}' style='background-color: #d6f9ff; color: black;'><span class='theme-count'>" . $isReport. "</span></span>" : '';

    // Ausgabe gemeldete Einträge
    $isAutoLock     = ($isAutoLock > 0) ? "<span class='update-plugins count-{$isReport}' style='background-color: #f1da36; color: black;'><span class='theme-count'>" . $isReport. "</span></span>" : '';

    // Hauptnavigation laden
    add_menu_page(esc_html(__('Kletterpartner', 'kps')) , esc_html(__('Kletterpartner', 'kps')) .
    "<span class='update-plugins count-{$updatePluginCount}'><span class='theme-count'>" . $updatePluginCount. "</span></span>" . "",
    'moderate_comments',
    KPS_FOLDER . '/kps.php',
    'kps_welcome',
    'dashicons-groups');

    // Subnavigation laden
    add_submenu_page(KPS_FOLDER . '/kps.php', esc_html(__('Einstellungen', 'kps')) , esc_html(__('Einstellungen', 'kps')) , 'manage_options', KPS_FOLDER . '/settings.php', 'kps_Settings');
    add_submenu_page(KPS_FOLDER . '/kps.php', esc_html(__('AGB / DSGVO', 'kps')) , esc_html(__('AGB / DSGVO', 'kps')) . "<span class='update-plugins count-{$noAGBDSGVOInfo}'><span class='theme-count'>" . $noAGBDSGVOInfo . "</span></span>", 'manage_privacy_options', KPS_FOLDER . '/kps-privacy.php', 'kps_Privacy');
    add_submenu_page(KPS_FOLDER . '/kps.php', esc_html(__('Email-Vorlagen', 'kps')) , esc_html(__('Email-Vorlagen', 'kps')) , 'manage_options', KPS_FOLDER . '/email.php', 'kps_EmailSetting');
    add_submenu_page(KPS_FOLDER . '/kps.php', esc_html(__('Design', 'kps')) , esc_html(__('Design', 'kps')) , 'manage_options', KPS_FOLDER . '/design.php', 'kps_DesignSettings');
    add_submenu_page(KPS_FOLDER . '/kps.php', esc_html(__('Einträge', 'kps')) , esc_html(__('Einträge', 'kps')) . "<span class='update-plugins count-{$openEntry}'><span class='theme-count'>" . $openEntry . "</span></span>" . $isAutoLock . $isReportBubble , 'moderate_comments', KPS_FOLDER . '/entries.php', 'kps_entries');
    add_submenu_page(KPS_FOLDER . '/kps.php', esc_html(__('Deinstallation', 'kps')) ,esc_html( __('Deinstallation', 'kps')) , 'administrator', KPS_FOLDER . '/uninstall.php', 'kps_uninstall');
}
add_action('admin_menu', 'kps_adminmenu');

/**
 * Init
 * Registierte Settings
 */
function kps_register_settings()
{
    global $wpdb;

    register_setting('kps_options', 'kps_formOptions', 'strval');               // string
    register_setting('kps_options', 'kps_formWordCount', 'intval');             // int
    register_setting('kps_options', 'kps_captchakeys', 'strval');               // string
    register_setting('kps_options', 'kps_captcha', 'strval');                   // string
    register_setting('kps_options', 'kps_userSettings', 'strval');              // array
    register_setting('kps_options', 'kps_backendPagination', 'intval');         // int
    register_setting('kps_options', 'kps_frontendPagination', 'intval');        // int
    register_setting('kps_options', 'kps_deleteEntryTime', 'intval');           // int
    register_setting('kps_options', 'kps_deleteNoEntryTime', 'intval');         // int
    register_setting('kps_options', 'kps_userMailContent', 'strval');           // array
    register_setting('kps_options', 'kps_authorMailContent', 'strval');         // array
    register_setting('kps_options', 'kps_userMailContactSettings', 'strval');   // array
    register_setting('kps_options', 'kps_adminUnlockMailContent', 'strval');    // array
    register_setting('kps_options', 'kps_mailFrom', 'strval');                  // string
    register_setting('kps_options', 'kps_mailFromCC', 'strval');                // array,
    register_setting('kps_options', 'kps_version', 'strval');                   // string, Version
    register_setting('kps_options', 'kps_agb', 'intval');                       // int
    register_setting('kps_options', 'kps_dsgvo', 'intval');                     // int
    register_setting('kps_options', 'kps_report', 'strval');                    // array
    register_setting('kps_options', 'kps_icon', 'intval');                      // int
    register_setting('kps_options', 'kps_legend', 'strval');                    // array
    register_setting('kps_options', 'kps_widget', 'strval');                    // array
    register_setting('kps_options', 'kps_kpsCounter', 'strval');                // array

    if (function_exists('wp_add_privacy_policy_content'))
    {
    	$DSGVOSection = sprintf(
        '<p>' . esc_html(__( 'Wenn Sie das Kletterpartner-Suche Formular benutzen und einen Eintrag hinterlassen, speicher wir eingegebenen Daten in unserer Datenbank. Diese können je nachdem, wie der Seitenbetreiber dies eingestellt hat, abgerufen werden. Die gespeicherten Daten werden bei Abruf, automatisch, an die Email-Adresse des Anforderers versendet. Desweiteren speichern wir möglicherweise Ihre IP und den Hostnamen, um die Spam zu Erkennung und zu verbessern.', 'kps' )) . '</p>' .
        '<p>' . esc_html(__( 'Einträge, welche freigegeben wurden, werden minimal 1 Tag sichtbar angezeigt und nach maximal 180 Tagen automatisch gelöscht.', 'kps' )) . '</p>' .
        '<p>' . esc_html(__( 'Einträge, welche nicht freigegeben wurden, werden minimal 30 Tage gespeichert und nach maximal 180 Tagen automatisch, gelöscht.', 'kps' )) . '</p>');

    	wp_add_privacy_policy_content(esc_html(__('Kletterpartner-Suche', 'kps')), wp_kses_post(wpautop($DSGVOSection, false)));
    }
}
add_action('admin_init', 'kps_register_settings');

/**
 * CSS laden für Adminbereich
 */
function kps_backend_enqueue_style()
{
    wp_enqueue_style('kps-backend', plugins_url('/admin/css/kps-backend.css', __FILE__) , false, KPS_VER, 'all');
}
add_action('admin_enqueue_scripts', 'kps_backend_enqueue_style');

/**
 * CSS laden für Frontend
 */
function kps_frontend_enqueue_style()
{
    wp_enqueue_style('kps-frontend', plugins_url('/frontend/css/kps-frontend.css', __FILE__) , false, KPS_VER, 'all');
    wp_enqueue_style('kps-frontend-dashicons', 'https://use.fontawesome.com/releases/v5.4.1/css/all.css' , false, KPS_VER, 'all');
}
add_action('wp_enqueue_scripts', 'kps_frontend_enqueue_style');

/**
 * Javascript laden, wenn benötigt für Backend.
 * Wird in Funktion aufgerufen
 */
function kps_admin_enqueue()
{
    wp_enqueue_script('kps-backend', plugins_url('/admin/js/kps-admin.js', __FILE__) , array('jquery'), KPS_VER, false);
}

/**
 * Javascript laden
 * Thx to https://gist.github.com/mikeschinkel/5d43b23110fa23f733e01e02fb521755 for an Example to DataPicking
 */
function kps_frontend_enqueue()
{
    wp_enqueue_script('kps-frontend', plugins_url('/frontend/js/kps-frontend.js', __FILE__) , array('jquery'), KPS_VER, false);
	$dataSpamPicker = array('kps_spamtimer1'    => 'kps_spamtimer1',
                            'kps_spamtimer2'    => 'kps_spamtimer2'
	);
	wp_localize_script( 'kps-frontend', 'kps_spam', $dataSpamPicker );
}
add_action( 'wp_enqueue_scripts', 'kps_frontend_enqueue' );


/**
 * Plugin-Seite
 * Erstelle einen Link auf der Allgemeinen Plugin-Seite der zu den Einstellungen führt
 * Nice Feature ;-)
 */
function kps_links($links, $file)
{
    if ($file == plugin_basename(dirname(__FILE__) . '/kps.php'))
    {
        $links[] = '<a href="' . admin_url('admin.php?page=Kletterpartner-Suche/settings.php') . '">' . esc_html(__('Einstellungen', 'kps')) . '</a>';
    }
    return $links;
}
add_filter('plugin_action_links', 'kps_links', 10, 2);