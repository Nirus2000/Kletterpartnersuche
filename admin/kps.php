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
if ( strpos( $_SERVER[ 'PHP_SELF' ], basename( __FILE__ ) ) ) {
    die( 'No direct calls allowed!' );
}

/**
 * Hauptfunktion
 */
function kps_welcome() {
    global $noMailinPhpDetect;

    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('moderate_comments'))
    {
        die(esc_html(__('Zugriff verweigert!', 'kps')));
    }

    // Javascript einladen
    kps_admin_enqueue();

    // Metabox erstellen
    if ( !function_exists( 'mail' ) ) {
        // Email-Funktion deaktiviert
        $noMailinPhpDetect = true;
        add_meta_box( 'kps_adminNoMailInPhp', esc_html(__( 'Fehler! mail()-Funktion!', 'kps' )), 'kps_admin_overview_nomailinphp', 'kps_welcome', 'left' );
    }
    add_meta_box( 'kps_adminEntries', esc_html(__('Übersicht', 'kps')), 'kps_admin_overview_entries', 'kps_welcome', 'left' );
    add_meta_box( 'kps_adminSettings', esc_html(__('Einstellungsübersicht (kurz)', 'kps')), 'kps_admin_overview_setting', 'kps_welcome', 'left' );
    add_meta_box( 'kps_adminCopyright', esc_html(__('Copyright', 'kps')), 'kps_admin_overview_copyright', 'kps_welcome', 'left' );
    add_meta_box( 'kps_adminManuel', esc_html(__('Anleitung', 'kps')), 'kps_admin_overview_manuel', 'kps_welcome', 'normal' );
    add_meta_box( 'kps_adminProoved', esc_html(__('Geprüft', 'kps')), 'kps_admin_overview_prooved', 'kps_welcome', 'normal' );
    add_meta_box( 'kps_adminStatistics', esc_html(__('Statistik', 'kps')), 'kps_admin_overview_statistics', 'kps_welcome', 'right' );
?>
      <div id="kps" class="wrap kps">
            <div>
                <h3>
                    <?php echo esc_html(__('Kletterpartner-Suche', 'kps')); ?> - <?php  echo esc_html(__('Übersicht', 'kps')); ?>
              </h3>
            </div>

            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="metabox-holder">
                    <div class="postbox-container"><?php do_meta_boxes( 'kps_welcome', 'left', ''); ?></div>
                    <div class="postbox-container"><?php do_meta_boxes( 'kps_welcome', 'normal', ''); ?></div>
                    <div class="postbox-container"><?php do_meta_boxes( 'kps_welcome', 'right', ''); ?></div>
                </div>
            </div>
        </div>
    <?php
}

/**
 * Funktion PHP-Emailfunktion
 * Ausgabe, wenn mail() in PHP deaktiviert ist
 */
function kps_admin_overview_nomailinphp() {
    echo '
            <div><font color="red"><b>' .
esc_html(__('Die Funktion mail() wird benötigt um Emailszu versenden und ist nicht aktiv geschalten in deiner PHP Konfiguration.
Du kannst ein Wordpress-Plugin installieren, welches SMTP statt der mail()-Funktion nutzt, oder du kontaktierst deinen Provider,
dass er diese Funktion freischaltet.', 'kps')) .
            '<b></font></div>
        ';
}


/**
 * Funktion Settings
 * Übersicht der Einstellungen
 */
function kps_admin_overview_setting() {
    global $noMailinPhpDetect;

    // UNIX-Timestamp in Tage umrechnen
    $delete_entry_time   = get_option('kps_deleteEntryTime', false) / 24 / 60 / 60;
    $delete_noentry_time = get_option('kps_deleteNoEntryTime', false) / 24 / 60 / 60;

    // Hole EmailCC Einstellungen
    $emailCC = kps_unserialize(get_option('kps_mailFromCC', false));
    $setEmailCC = esc_attr($emailCC['kpsEmailCC']);

    // Hole Report Einstellungen
    $report = kps_unserialize(get_option('kps_report', false));
    $adminSendReportAfter = $report['kpsAdminSendReportAfter'];
    $autoReportLock = $report['kpsAutoReportLock'];
    $reportIsActivated = ($report['kpsReportActivation'] === 'true') ? '<span class="dashicons dashicons-yes"></span>' : '<span class="dashicons dashicons-no-alt"></span>';

    // Hole Usereinstellungen
    $userSettings = kps_unserialize(get_option('kps_userSettings', false));

    // Prüfe AGB's
    $isUserPrivacyAGB       = ($userSettings['kpsUserPrivacyAGB'] === 'true'
                                && get_post_status(get_option('kps_agb')) !== false
                                && get_post_status(get_option('kps_agb')) == 'publish'
                                && post_password_required(get_option('kps_agb')) === false)
                                ? true : false;

    if ($isUserPrivacyAGB === true)
    {
        $isUserPrivacyAGB   = '<span class="dashicons dashicons-yes"></span>';
        $errorAGBpage       = '';
    }
    else
    {
        $isUserPrivacyAGB = '<span class="dashicons dashicons-no-alt"></span>';
        $errorAGBpage       = 'form_glowing';

    }

    // Prüfe DSGVO
    $isUserPrivacyDSGVO     = ($userSettings['kpsUserPrivacyDSGVO'] === 'true'
                                && get_post_status(get_option('kps_dsgvo')) !== false
                                && get_post_status(get_option('kps_dsgvo')) == 'publish'
                                && post_password_required(get_option('kps_dsgvo')) === false)
                                ? true : false;

    if ($isUserPrivacyDSGVO === true)
    {
        $isUserPrivacyDSGVO = '<span class="dashicons dashicons-yes"></span>';
        $errorDSGVOpage     = '';
    }
    else
    {
        $isUserPrivacyDSGVO = '<span class="dashicons dashicons-no-alt"></span>';
        $errorDSGVOpage     = 'form_glowing';

    }

    // Hole Captcha Einstellungen
    $captchaSiteKey     = get_option('kps_captcha', false);
    $isCaptchaActivated = ($captchaSiteKey === 'true') ? '<span class="dashicons dashicons-yes"></span>' : '<span class="dashicons dashicons-no-alt"></span>';
    $errorCaptcha = ($captchaSiteKey === 'true') ? '' : 'form_glowing';

    echo '
            <div>
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <table class="table">
                    <tbody>
                        <tr class="' . $errorAGBpage . '">
                            <td>' . esc_html(__('AGBs', 'kps')) . '</td>
                            <td><span>' . $isUserPrivacyAGB . '</span></td>
                        </tr>
                        <tr class="' . $errorDSGVOpage . '">
                            <td>' . esc_html(__('DSGVO', 'kps')) . '</td>
                            <td><span>' . $isUserPrivacyDSGVO . '</span></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Freigegebene Einträge', 'kps')) . '</td>
                            <td><b>' . $delete_entry_time . '</b> ' . __( 'Tagen löschen', 'kps' ) . '</td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Wartende Einträge', 'kps')) . '</td>
                            <td><b>' . $delete_noentry_time . '</b> ' . esc_html(__('Tagen löschen', 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Formular-Textarea', 'kps')) . '</td>
                            <td><b>' . get_option('kps_formWordCount', false) . '</b> ' . esc_html(_n('Wort', 'Wörter', get_option('kps_formWordCount', false) , 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Eintrag melden nach', 'kps')) . '</td>
                            <td><b>' . $adminSendReportAfter . '</b> ' . esc_html(_n('Tag', 'Tage', $adminSendReportAfter , 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Automatische Sperre nach', 'kps')) . '</td>
                            <td><b>' . $autoReportLock . '</b> ' . esc_html(_n('Tag', 'Tage', $autoReportLock , 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Sperre aktiv', 'kps')) . '</td>
                            <td><b>' . $reportIsActivated . '</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Email', 'kps')) . '</td>
                            <td><b>' . get_option('kps_mailFrom', false) . '</b></td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Email-Kopie', 'kps')) . '</td>
                            <td><b>' . $setEmailCC . '</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Backend', 'kps')) . '</td>
                            <td><b>' . get_option('kps_backendPagination', false) . '</b> ' . esc_html(_n('Eintrag pro Seite', 'Einträge pro Seite', get_option('kps_backendPagination', false)) , 'kps') . '</td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Frontend', 'kps')) . '</td>
                            <td><b>' . get_option('kps_frontendPagination', false) . '</b> ' . esc_html(_n('Eintrag pro Seite', 'Einträge pro Seite', get_option('kps_frontendPagination', false)) , 'kps') . '</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr class="' . $errorCaptcha . '">
                            <td>' . esc_html(__('Google reCaptcha', 'kps')) . '</td>
                            <td><span>' . $isCaptchaActivated . '</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
}

/**
 * Funktion Einträge
 * Übersicht der Einträge
 */
function kps_admin_overview_statistics() {

    // Gesamtzähler für Statistik
    $kpsCounter                 = kps_unserialize(get_option('kps_kpsCounter', false));
    $kpsCounterAllEntrys        = ($kpsCounter['kpsAllEntrys'] === NULL) ? 0 : $kpsCounter['kpsAllEntrys'];
    $kpsCounterActivatedEntrys  = ($kpsCounter['kpsAllActivatedEntrys'] === NULL) ? 0 : $kpsCounter['kpsAllActivatedEntrys'];
    $kpsCounterVerfifications   = ($kpsCounter['kpsAllVerfifications'] === NULL) ? 0 : $kpsCounter['kpsAllVerfifications'];
    $kpsCounterSendRequirements = ($kpsCounter['kpsAllSendRequirements'] === NULL) ? 0 : $kpsCounter['kpsAllSendRequirements'];
    $kpsCounterDeleteEntrys     = ($kpsCounter['kpsAllDeleteEntrys'] === NULL) ? 0 : $kpsCounter['kpsAllDeleteEntrys'];



    echo '
            <div>
                <table class="table">
                    <tbody>
                        <tr>
                            <td><b>' . esc_html(__('Eingereichte Suchen', 'kps')) . '</b></td>
                            <td>' . $kpsCounterAllEntrys . ' ' . esc_html(_n('Eintrag', 'Einträge', $kpsCounterAllEntrys, 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td><b>' . esc_html(__('Aktivierte Einträge', 'kps')) . '</b></td>
                            <td>' . $kpsCounterActivatedEntrys . ' ' . esc_html(__('Aktivierung', 'Aktivierungen', $kpsCounterActivatedEntrys, 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td><b>' . esc_html(__('Angeforderten Kontaktdaten', 'kps')) . '</b></td>
                            <td>' . $kpsCounterVerfifications . ' ' . esc_html(_n('Verifizierung', 'Verifizierungen', $kpsCounterVerfifications, 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td><b>' . esc_html(__('Versendete Kontaktdaten', 'kps')) . '</b></td>
                            <td>' . $kpsCounterSendRequirements . ' ' . esc_html(_n('Anfrage', 'Anfragen', $kpsCounterSendRequirements, 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td><b>' . esc_html(__('Manuell gelöschte Einträge', 'kps')) . '</b></td>
                            <td>' . $kpsCounterDeleteEntrys . ' ' . esc_html(_n('Eintrag', 'Einträge', $kpsCounterDeleteEntrys, 'kps')) . '</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
}

/**
 * Funktion Einträge
 * Übersicht der Einträge
 */
function kps_admin_overview_entries() {
    global $wpdb;

    // Zähle alle Einträge
    $allEntries      = $wpdb->get_results( "SELECT * FROM " . KPS_TABLE_ENTRIES );
    $countAllEntries = $wpdb->num_rows;

    // Zähle alle offenen Einträge
    $openEntries        = $wpdb->get_results( "SELECT * FROM " . KPS_TABLE_ENTRIES . " WHERE isLocked = 0" );
    $countUnlockEntries = $wpdb->num_rows;

    // Zählen Einträge
    $countAction = $wpdb->get_row( "SELECT
                                    COUNT(*) AS allEntries,
                                    IFNULL(SUM(CASE WHEN isLocked = 0 THEN 1 ELSE 0 END), 0) AS isLocked,
                                    IFNULL(SUM(CASE WHEN isLocked = 1 THEN 1 ELSE 0 END), 0) AS isUnLocked,
                                    IFNULL(SUM(CASE WHEN isLockedByAdmin = 0 THEN 1 ELSE 0 END), 0) AS isLockedByAdmin,
                                    IFNULL(SUM(CASE WHEN isLockedByAdmin = 1 THEN 1 ELSE 0 END), 0) AS isUnLockedByAdmin,
                                    IFNULL(SUM(CASE WHEN isLocked = 0 OR isLockedByAdmin = 0 THEN 1 ELSE 0 END), 0) AS isLockedBoth,
                                    IFNULL(SUM(CASE WHEN deleteDateTime < " . time() . " THEN 1 ELSE 0 END), 0) AS deleteDateTime
                                FROM " . KPS_TABLE_ENTRIES, OBJECT );

    // Übersetzung Single, Plural, Null
    $translationAllEntries        = ( $countAction->allEntries != 0 ) ? esc_html(_n('offener Eintrag', 'offene Einträge', $countAction->allEntries, 'kps')) : esc_html(__('offene Einträge', 'kps'));
    $translationIsLocked          = ( $countAction->isLocked != 0 ) ? esc_html(_n('Eintrag gesperrt durch Autor', 'Einträge gesperrte durch Autor', $countAction->isLocked, 'kps')) : esc_html(__('gesperrte Einträge durch Autor', 'kps'));
    $translationIsUnlock          = ( $countAction->isUnLocked != 0 ) ? esc_html(_n('Eintrag freigegeben durch Autor', 'Einträge freigegeben durch Autor', $countAction->isUnLocked, 'kps')) : esc_html(__('freigegebene Einträge durch Autor', 'kps'));
    $translationIsLockedByAdmin   = ( $countAction->isLockedByAdmin != 0 ) ? esc_html(_n('Eintrag gesperrt durch Admin', 'Einträge gesperrt durch Admin', $countAction->isLockedByAdmin, 'kps')) : esc_html(__('gesperrte Einträge durch Admin', 'kps' ));
    $translationIsUnLockedByAdmin = ( $countAction->isUnLockedByAdmin != 0 ) ? esc_html(_n('Eintrag freigegeben durch Admin', 'Einträge freigegeben durch Admin', $countAction->isUnLockedByAdmin, 'kps')) : esc_html(__('freigegebene Einträge durch Admin', 'kps' ));

    echo '
            <div>
                <table class="table">
                    <tbody>
                        <tr>
                            <td><b>' . $countAction->allEntries . '</b></td>
                            <td>' . $translationAllEntries . '</td>
                        </tr>
                        <tr>
                            <td><b>' . $countAction->isLocked . '</b></td>
                            <td>' . $translationIsLocked . '</td>
                        </tr>
                        <tr>
                            <td><b>' . $countAction->isUnLocked . '</b></td>
                            <td>' . $translationIsUnlock . '</td>
                        </tr>
                        <tr>
                            <td><b>' . $countAction->isLockedByAdmin . '</b></td>
                            <td>' . $translationIsLockedByAdmin . '</td>
                        </tr>
                        <tr>
                            <td><b>' . $countAction->isUnLockedByAdmin . '</b></td>
                            <td>' . $translationIsUnLockedByAdmin . '</td>
                        </tr>
                        <tr>
                            <td><b>' . $countAction->isUnLockedByAdmin . '</b></td>
                            <td>' . $translationIsUnLockedByAdmin . '</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
}

/**
 * Funktion Manual
 * Anleitung für das Einbinden in Wordpress
 */
function kps_admin_overview_manuel() {
    echo '
            <div>
                <table class="table">
                    <tbody>
                        <tr>
                            <td>' . esc_html(__('So einfach per Shortcut implementieren', 'kps')) . '!</td>
                        </tr>
                        <tr>
                            <td>
                                <ul class="ul-square">
                                    <li>' . esc_html(__('Erstelle eine neue Seite', 'kps' )) . '</li>
                                    <li>' . esc_html(__('Gib der Seite einen Namen', 'kps' )) . '</li>
                                    <li>' . esc_html(__('Schreibe in den Seitenkontex', 'kps')) . ' <input type="text" name="kps-shortcode" size="15" readonly="readonly" value="[kps-shortcode]" id="kps-shortcode" /></li>
                                    <li>' . esc_html(__('Veröffentliche diese Seite', 'kps')) . '</li>
                                    <li>' . esc_html(__('Geh im Hauptmenü unter Design->Menüs und fügen Sie Seite einem Menü hinzu', 'kps')) . '</li>
                                    <ul class="ul-square">
                                        <li>' . esc_html(__('Alternativ können Sie ein neues Menü erstellen und die Seite dort hinzufügen', 'kps')) . '</li>
                                    </ul>
                                    <li>' . esc_html(__('Speicher, Veröffentlichen und Fertig!', 'kps')) . '</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Natürlich können Sie den Shortcode auch in einem Beitrag implementieren', 'kps')) . '</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
}

/**
 * Funktion Copyright
 * Don't delete this!
 */
function kps_admin_overview_copyright() {
    echo '
            <div>
                <table class="table">
                    <tbody>
                        <tr>
                            <td>' . esc_html(__('Support', 'kps')) . '</td>
                            <td><a href="https://wordpress.org/support/plugin/kletterpartner-suche" target="_blank">' . esc_html(__('Support-Forum', 'kps')) . '</a></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Version', 'kps')) . '</td>
                            <td>' . get_option('kps_version') . '</td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Copyright', 'kps')) . '</td>
                            <td>Alexander Ott</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('SBB-Mitgliedsnummer', 'kps')) . '</td>
                            <td>320/00/245813</td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Email', 'kps')) . '</td>
                            <td>kps@nirus-online.de</td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Internet', 'kps')) . '</td>
                            <td>http://www.nirus-online.de</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
}
/**
 * Funktion Prooved
 * Übersicht des geprüften Codes
 */
function kps_admin_overview_prooved( ) {
    echo '
            <div>
                <table class="table">
                    <tbody>
                        <tr>
                            <td><img src="' . KPS_RELATIV . "/admin/gfx/html5.png" . '" alt="' . esc_html(__('HTML5', 'kps')) . '" title="' . esc_html(__('HTML5', 'kps')) . '"/></td>
                            <td><img src="' . KPS_RELATIV . "/admin/gfx/valid-html401-blue.png" . '" alt="' . esc_html(__('W3C401', 'kps')) . '" title="' . esc_html(__('W3C401', 'kps')) . '"/></td>
                        </tr>
                        <tr>
                            <td><img src="' . KPS_RELATIV . "/admin/gfx/mysql.png" . '" alt="' . esc_html(__('Mysql', 'kps')) . '" title="' . esc_html(__('Mysql', 'kps')) . '"/></td>
                            <td><img src="' . KPS_RELATIV . "/admin/gfx/php7.png" . '" alt="' . esc_html(__('PHP', 'kps')) . '" title="' . esc_html(__('PHP', 'kps')) . '"/></td>
                        </tr>
                        <tr>
                            <td colspan="2"><img src="' . KPS_RELATIV . "/admin/gfx/jquery.png" . '" alt="' . esc_html(__('JQuery', 'kps')) . '" title="' . esc_html(__('JQuery', 'kps')) . '"/></td>
                        </tr>
                        <tr>
                            <td colspan="2"><img src="' . KPS_RELATIV . "/admin/gfx/invisible_badge.png" . '" alt="' . esc_html(__('Google reCaptcha', 'kps')) . '" title="' . esc_html(__('Google reCaptcha', 'kps')) . '"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
}