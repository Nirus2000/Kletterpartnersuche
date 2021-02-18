<?php
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

// Kein direkten Zugriff erlauben
if (strpos($_SERVER['PHP_SELF'], basename(__FILE__)))
{
    wp_die(__('No direct calls allowed!'));
}

/**
 * Hauptfunktion
 */
function kps_welcome() {
    global $noMailinPhpDetect;

    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('moderate_comments'))
    {
        wp_die(esc_html__('Access denied!', 'kps'));
    }

    // Javascript einladen
    kps_admin_enqueue();

    // Metabox erstellen
    if ( !function_exists( 'mail' ) ) {
        // Email-Funktion deaktiviert
        $noMailinPhpDetect = true;
        add_meta_box( 'kps_adminNoMailInPhp', esc_html__('Error! Mail()-Function!', 'kps'), 'kps_admin_overview_nomailinphp', 'kps_welcome', 'left' );
    }
    add_meta_box( 'kps_adminEntries', esc_html__('Overview', 'kps'), 'kps_admin_overview_entries', 'kps_welcome', 'left' );
    add_meta_box( 'kps_adminSettings', esc_html__('Settings-Overview (short)', 'kps'), 'kps_admin_overview_setting', 'kps_welcome', 'left' );
    add_meta_box( 'kps_adminManuel', esc_html__('Manual', 'kps'), 'kps_admin_overview_manuel', 'kps_welcome', 'normal' );
    add_meta_box( 'kps_adminProoved', esc_html__('Checked', 'kps'), 'kps_admin_overview_prooved', 'kps_welcome', 'normal' );
    add_meta_box( 'kps_adminStatistics', esc_html__('Statistics', 'kps'), 'kps_admin_overview_statistics', 'kps_welcome', 'right' );
    add_meta_box( 'kps_adminCopyright', esc_html__('Copyright', 'kps'), 'kps_admin_overview_copyright', 'kps_welcome', 'right' );
?>
      <div id="kps" class="wrap kps">
            <div>
                <h3>
                    <?php echo esc_html__('Climbing-Partner-Search', 'kps'); ?> - <?php  echo esc_html__('Overview', 'kps'); ?>
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
esc_html__('The function mail () is used to send emails and is not active in your PHP configuration.
You can install a Wordpress plugin that uses SMTP instead of the mail () function, or you can contact your provider,
that he unlocks this feature.', 'kps') .
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

    // Hole Email-Kopie/Email-Report Einstellungen
    $emailCC = kps_unserialize(get_option('kps_mailFromCC', false));

    // Hole Report Einstellungen
    $report = kps_unserialize(get_option('kps_report', false));
    $adminSendReportAfter = $report['kpsAdminSendReportAfter'];
    $reportIsActivated = ($report['kpsReportActivation'] === 'true') ? '<span class="dashicons dashicons-yes" style="color: green"></span>' : '<span class="dashicons dashicons-no-alt" style="color: red"></span>';

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
        $isUserPrivacyAGB   = '<span class="dashicons dashicons-yes" style="color: green"></span>';
        $errorAGBpage       = '';
    }
    else
    {
        $isUserPrivacyAGB = '<span class="dashicons  dashicons-no-alt" style="color: red"></span>';
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
        $isUserPrivacyDSGVO = '<span class="dashicons dashicons-yes" style="color: green"></span>';
        $errorDSGVOpage     = '';
    }
    else
    {
        $isUserPrivacyDSGVO = '<span class="dashicons dashicons-no-alt" style="color: red"></span>';
        $errorDSGVOpage     = 'form_glowing';

    }

    // Hole Captcha Einstellungen
    $captchaSiteKey     = get_option('kps_captcha', false);
    $isCaptchaActivated = ($captchaSiteKey === 'true') ? '<span class="dashicons dashicons-yes" style="color: green"></span>' : '<span class="dashicons dashicons-no-alt" style="color: red"></span>';
    $errorCaptcha       = ($captchaSiteKey === 'true') ? '' : 'form_glowing';

    echo '
            <div>
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <table class="table">
                    <tbody>
                        <tr class="' . $errorAGBpage . '">
                            <td>' . esc_html__('GTC', 'kps') . '</td>
                            <td><span>' . $isUserPrivacyAGB . '</span></td>
                        </tr>
                        <tr class="' . $errorDSGVOpage . '">
                            <td>' . esc_html__('GDPR', 'kps') . '</td>
                            <td><span>' . $isUserPrivacyDSGVO . '</span></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html__('Released entries', 'kps') . '</td>
                            <td><b>' . $delete_entry_time . '</b> ' . esc_html(_n('delete day', 'delete days', get_option('kps_formWordCount', false) , 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td>' . esc_html__('Waiting entries', 'kps') . '</td>
                            <td><b>' . $delete_noentry_time . '</b> ' . esc_html(_n('delete day', 'delete days', get_option('kps_formWordCount', false) , 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td>' . esc_html__('Form textarea', 'kps') . '</td>
                            <td><b>' . get_option('kps_formWordCount', false) . '</b> ' . esc_html(_n('word', 'words', get_option('kps_formWordCount', false) , 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html__('Automatic-Lock after', 'kps') . '</td>
                            <td><b>' . $adminSendReportAfter . '</b> ' . esc_html(_n('report', 'reports', $adminSendReportAfter , 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td>' . esc_html__('Lock active', 'kps') . '</td>
                            <td><b>' . $reportIsActivated . '</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html__('Email', 'kps') . '</td>
                            <td><b>' . get_option('kps_mailFrom', false) . '</b></td>
                        </tr>
                        <tr>
                            <td>' . esc_html__('Email copy', 'kps') . '</td>
                            <td><b>' . esc_attr($emailCC['kpsEmailCC']) . '</b></td>
                        </tr>
                        <tr>
                            <td>' . esc_html__('Report-Email', 'kps') . '</td>
                            <td><b>' . esc_attr($emailCC['kpsEmailReport']) . '</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html__('Backend', 'kps') . '</td>
                            <td><b>' . get_option('kps_backendPagination', false) . '</b> ' . esc_html(_n('entry per page', 'entries per page', get_option('kps_backendPagination', false) , 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td>' . esc_html__('Frontend', 'kps') . '</td>
                            <td><b>' . get_option('kps_frontendPagination', false) . '</b> ' . esc_html(_n('entry per page', 'entries per page', get_option('kps_frontendPagination', false) , 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr class="' . $errorCaptcha . '">
                            <td>' . esc_html__('Google reCaptcha', 'kps') . '</td>
                            <td><span>' . $isCaptchaActivated . '</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
}

/**
 * Funktion Statistik
 * -> alle Einträge
 * -> aktivierte Einträge
 * -> verifizierte Einträge
 * -> versendete Einträge
 * -> Anforderungen
 * -> gelöschte Einträge
 */
function kps_admin_overview_statistics() {

    $verification   = false;

    // Token erstellen
    $token = wp_create_nonce('kpsResetStatisticsToken');

    if (isset($_POST['kpsResetStatistics']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsResetStatisticsToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsResetStatisticsToken'], 'kpsResetStatisticsToken');

        // Statistik zurücksetzen
        if ($verification == true)
        {
            // KPS-Counter
            $kpsCounter = serialize(array(
                'kpsAllEntrys'                      => 0,
                'kpsAllActivatedEntrys'             => 0,
                'kpsAllVerfifications'              => 0,
                'kpsAllSendRequirements'            => 0,
                'kpsAllDeleteEntrys'                => 0
            ));

            update_option('kps_kpsCounter', $kpsCounter);   // KPS-Counter
        }
    }

    // Gesamtzähler für Statistik
    $kpsCounter                 = kps_unserialize(get_option('kps_kpsCounter', false));
    $kpsCounterAllEntrys        = ($kpsCounter['kpsAllEntrys'] === NULL) ? 0 : $kpsCounter['kpsAllEntrys'];
    $kpsCounterActivatedEntrys  = ($kpsCounter['kpsAllActivatedEntrys'] === NULL) ? 0 : $kpsCounter['kpsAllActivatedEntrys'];
    $kpsCounterVerfifications   = ($kpsCounter['kpsAllVerfifications'] === NULL) ? 0 : $kpsCounter['kpsAllVerfifications'];
    $kpsCounterSendRequirements = ($kpsCounter['kpsAllSendRequirements'] === NULL) ? 0 : $kpsCounter['kpsAllSendRequirements'];
    $kpsCounterDeleteEntrys     = ($kpsCounter['kpsAllDeleteEntrys'] === NULL) ? 0 : $kpsCounter['kpsAllDeleteEntrys'];

    echo '
            <div>
                <form name="kpsResetStatistics" method="post" action="">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><b>' . esc_html__('Submitted searches', 'kps') . '</b></td>
                                <td>' . $kpsCounterAllEntrys . ' ' . esc_html(_n('Entry', 'Entries', $kpsCounterAllEntrys, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><b>' . esc_html__('Activated entries', 'kps') . '</b></td>
                                <td>' . $kpsCounterActivatedEntrys . ' ' . esc_html(_n('Activation', 'Activations', $kpsCounterActivatedEntrys, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><b>' . esc_html__('Reqirement contact details', 'kps') . '</b></td>
                                <td>' . $kpsCounterVerfifications . ' ' . esc_html(_n('Verification', 'Verifications', $kpsCounterVerfifications, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><b>' . esc_html__('Sent contact information', 'kps') . '</b></td>
                                <td>' . $kpsCounterSendRequirements . ' ' . esc_html(_n('Request', 'Requests', $kpsCounterSendRequirements, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><b>' . esc_html__('Manually deleted entries', 'kps') . '</b></td>
                                <td>' . $kpsCounterDeleteEntrys . ' ' . esc_html(_n('Entry', 'Entries', $kpsCounterDeleteEntrys, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="checkbox" name="kpsResetStatisticsConfirmed" id="kpsResetStatisticsConfirmed" />
                                    <label class="labelCheckbox" for="kpsResetStatisticsConfirmed">' . esc_html__('Reset statistics?', 'kps') . '</label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="hidden" id="kpsResetStatisticsToken" name="kpsResetStatisticsToken" value="' . $token . '" />
                                    <input class="button" type="submit" name="kpsResetStatistics" id="kpsResetStatistics" disabled value="' . esc_html__('Yes, reset statistics.', 'kps') . '" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
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
    $translationAllEntries        = ( $countAction->allEntries != 0 ) ? esc_html(_n('open entry', 'open entries', $countAction->allEntries, 'kps')) : esc_html__('open entries', 'kps');
    $translationIsLocked          = ( $countAction->isLocked != 0 ) ? esc_html(_n('entry blocked by author', 'entries blocked by author', $countAction->isLocked, 'kps')) : esc_html__('entries blocked by author', 'kps');
    $translationIsUnlock          = ( $countAction->isUnLocked != 0 ) ? esc_html(_n('entry released by author', 'entries released by author', $countAction->isUnLocked, 'kps')) : esc_html__('entries released by author', 'kps');
    $translationIsLockedByAdmin   = ( $countAction->isLockedByAdmin != 0 ) ? esc_html(_n('entry blocked by admin', 'entries blocked by admin', $countAction->isLockedByAdmin, 'kps')) : esc_html__('entries blocked by admin', 'kps');
    $translationIsUnLockedByAdmin = ( $countAction->isUnLockedByAdmin != 0 ) ? esc_html(_n('entry released by admin', 'entries released by admin', $countAction->isUnLockedByAdmin, 'kps')) : esc_html__('entries released by admin', 'kps');

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
                            <td>' . esc_html__('So easy to implement via shortcut', 'kps') . '!</td>
                        </tr>
                        <tr>
                            <td>
                                <ul class="ul-square">
                                    <li>' . esc_html__('Create a new page', 'kps') . '</li>
                                    <li>' . esc_html__('Give the page a name', 'kps') . '</li>
                                    <li>' . esc_html__('Write in the page context', 'kps') . ' <input type="text" name="kps-shortcode" size="15" readonly="readonly" value="[kps-shortcode]" id="kps-shortcode" /></li>
                                    <li>' . esc_html__('Publish this page', 'kps') . '</li>
                                    <li>' . esc_html__('Go to the main menu under Design -> Menus and add a page to a menu', 'kps') . '</li>
                                    <ul class="ul-square">
                                        <li>' . esc_html__('Alternatively, you can create a new menu and add the page there', 'kps') . '</li>
                                    </ul>
                                    <li>' . esc_html__('Save, Publish and Done!', 'kps') . '</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="hr"></td>
                        </tr>
                        <tr>
                            <td>' . esc_html__('Of course, you can also implement the shortcode in a post.', 'kps') . '</td>
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
                            <td><i class="fab fa-wordpress-simple"></i>&#160;' . esc_html__('WordPress', 'kps') . '</td>
                            <td><a href="https://de.wordpress.org/plugins/kletterpartner-suche/" target="_blank">' . esc_html__('Climbing-Partner-Search', 'kps') . '</a></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-code-branch"></i>&#160;' . esc_html__('Version', 'kps') . '</td>
                            <td>' . get_option('kps_version') . '</td>
                        </tr>
                        <tr>
                            <td><i class="far fa-copyright"></i>&#160;' . esc_html__('Copyright', 'kps') . '</td>
                            <td>2018 - '. date("Y") . '</td>
                        </tr>
                        <tr>
                            <td><i class="far fa-question-circle"></i>&#160;' . esc_html__('Support', 'kps') . '</td>
                            <td><a href="https://wordpress.org/support/plugin/kletterpartner-suche" target="_blank">' . esc_html__('Support-Forum', 'kps') . '</a></td>
                        </tr>
                        <tr>
                            <td><i class="fab fa-github"></i>&#160;' . esc_html__('GitHub', 'kps') . '</td>
                            <td><a href="https://github.com/Nirus2000/Kletterpartnersuche" target="_blank">' . esc_html__('GitHub', 'kps') . '</a></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="hr"></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-at"></i>&#160;' . esc_html__('Author', 'kps') . '</td>
                            <td>Alexander Ott</td>
                        </tr>
                        <tr>
                            <td><i class="far fa-envelope"></i>&#160;' . esc_html__('Email', 'kps') . '</td>
                            <td><a href="mailto:kps@nirus-online.d?subject='. esc_html__('Climbing-Partner-Search', 'kps') . ' - ' . KPS_VER . '">kps@nirus-online.de</a></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-globe"></i>&#160;' . esc_html__('Internet', 'kps') . '</td>
                            <td><a href="http://www.nirus-online.de" target="_blank">http://www.nirus-online.de</a></td>
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
                            <td><img src="' . KPS_RELATIV_ADMIN . "/gfx/html5.png" . '" alt="' . esc_html__('HTML5', 'kps') . '" title="' . esc_html__('HTML5', 'kps') . '"/></td>
                            <td><img src="' . KPS_RELATIV_ADMIN . "/gfx/valid-html401-blue.png" . '" alt="' . esc_html__('W3C401', 'kps') . '" title="' . esc_html__('W3C401', 'kps') . '"/></td>
                        </tr>
                        <tr>
                            <td><img src="' . KPS_RELATIV_ADMIN . "/gfx/mysql.png" . '" alt="' . esc_html__('Mysql', 'kps') . '" title="' . esc_html__('Mysql', 'kps') . '"/></td>
                            <td><img src="' . KPS_RELATIV_ADMIN . "/gfx/php7.png" . '" alt="' . esc_html__('PHP', 'kps') . '" title="' . esc_html__('PHP', 'kps') . '"/></td>
                        </tr>
                        <tr>
                            <td><img src="' . KPS_RELATIV_ADMIN . "/gfx/jquery.png" . '" alt="' . esc_html__('JQuery', 'kps') . '" title="' . esc_html__('JQuery', 'kps') . '"/></td>
                            <td><img src="' . KPS_RELATIV_ADMIN . "/gfx/invisible_badge.png" . '" alt="' . esc_html__('Google reCaptcha', 'kps') . '" title="' . esc_html__('Google reCaptcha', 'kps') . '"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
}