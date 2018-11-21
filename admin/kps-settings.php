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
 * Hauptfunktion
 */
function kps_Settings()
{
    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('manage_options'))
    {
        die(esc_html(__('Zugriff verweigert!', 'kps')));
    }

    // Javascript einladen
    kps_admin_enqueue();

    add_meta_box('kps_admin_pagination', esc_html(__('Einträge / Seite', 'kps')) , 'kps_admin_pagination', 'kps_Settings', 'left');

    $kps_tab = 'kps_BasicSettings'; // Start-Tab
    if (isset($_POST['kps_tab']))
    {
        $kps_tab = $_POST['kps_tab'];
    }
?>
        <div id="kps" class="wrap kps">
            <div>
                <h3>
                    <?php echo esc_html(__('Kletterpartner-Suche', 'kps')); ?> - <?php echo esc_html(__('Übersicht', 'kps')); ?>
               </h3>
            </div>
    		<h2 class="nav-tab-wrapper kps_nav_tab_wrapper">
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_BasicSettings') { echo "nav-tab-active";} ?>" rel="kps_BasicSettings">
                    <div style="text-align: center;"><?php  esc_html_e('Grundeinstellung', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_UserSettings') { echo "nav-tab-active";} ?>" rel="kps_UserSettings">
                    <div style="text-align: center;"><?php  esc_html_e('Benutzereinstellung', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Reporting') { echo "nav-tab-active";} ?>" rel="kps_Reporting">
                    <div style="text-align: center;"><?php  esc_html_e('Reporting', 'kps'); ?></div>
                </a>
                <a href="" class="nav-tab <?php if ($kps_tab == 'kps_EmailSettings') { echo "nav-tab-active";} ?>" rel="kps_EmailSettings">
                    <div style="text-align: center;"><?php  esc_html_e('Emailbenachrichtigung', 'kps'); ?></div>
                </a>
                <a href="" class="nav-tab <?php if ($kps_tab == 'kps_Spam') { echo "nav-tab-active";} ?>" rel="kps_Spam">
                    <div style="text-align: center;"><?php  esc_html_e('Spam-Schutz', 'kps'); ?></div>
                </a>
                <a href="" class="nav-tab <?php if ($kps_tab == 'kps_Optionfields') { echo "nav-tab-active";} ?>" rel="kps_Optionfields">
                    <div style="text-align: center;"><?php  esc_html_e('Formular-Optionen', 'kps'); ?></div>
                </a>
                <a href="" class="nav-tab <?php if ($kps_tab == 'kps_Pagination') { echo "nav-tab-active";} ?>" rel="kps_Pagination">
                    <div style="text-align: center;"><?php  esc_html_e('Navigation', 'kps'); ?></div>
                </a>
    		</h2>

            <form name="kps_options" class="kps_options kps_BasicSettings <?php if ($kps_tab == 'kps_BasicSettings') { echo "active";} ?>" method="post" action="">
               <?php kps_BasicSettings(); ?>
    		</form>

    		<form name="kps_options" class="kps_options kps_UserSettings <?php if ($kps_tab == 'kps_UserSettings') { echo "active";} ?>" method="post" action="">
    			<?php kps_UserSettings(); ?>
    		</form>

    		<form name="kps_options" class="kps_options kps_Reporting <?php if ($kps_tab == 'kps_Reporting') { echo "active";} ?>" method="post" action="">
    			<?php kps_Reporting(); ?>
    		</form>

    		<form name="kps_options" class="kps_options kps_EmailSettings <?php if ($kps_tab == 'kps_EmailSettings') { echo "active";} ?>" method="post" action="">
    			<?php kps_EmailSettings(); ?>
    		</form>

    		<form name="kps_options" class="kps_options kps_Spam <?php if ($kps_tab == 'kps_Spam') { echo "active";} ?>" method="post" action="">
    			<?php kps_Spam(); ?>
    		</form>

    		<form name="kps_options" class="kps_options kps_Optionfields <?php if ($kps_tab == 'kps_Optionfields') { echo "active";} ?>" method="post" action="">
    			<?php kps_Optionfields(); ?>
    		</form>

    		<form name="kps_options" class="kps_options kps_Pagination <?php if ($kps_tab == 'kps_Pagination') { echo "active";} ?>" method="post" action="">
    			<?php kps_Pagination(); ?>
    		</form>

        </div>
    <?php
}

/**
 * Funktion Eintrag melden
 */
function kps_Reporting()
{
    $verification   = false;

    // Token erstellen
    $token = wp_create_nonce('kpsReportToken');

    if (isset($_POST['submitReport']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsAdminSendReportAfter',
            'kpsReportSpam',
            'kpsAutoReportSpam',
            'kpsReportUnreasonable',
            'kpsAutoReportUnreasonable',
            'kpsReportDouble',
            'kpsAutoReportDouble',
            'kpsReportPrivacy',
            'kpsAutoReportPrivacy',
            'kpsReportOthers',
            'kpsAutoReportOthers',
            'kpsReportActivation',
            'kpsReportToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsReportToken'], 'kpsReportToken');

        // Verifizieren
        if ($verification == true)
        {
            /* Reportanzahl escapen -> default sind 50 Meldungen
            * Reportanzahl darf nicht kleiner gleich 10 sein
            * Reportanzahl darf nicht größer 499 sein
            * -> default sind 50 Meldungen
            */
            $setReport['kpsAdminSendReportAfter']   = (isset($postVars['kpsAdminSendReportAfter'])
                                                        && !empty($postVars['kpsAdminSendReportAfter'])
                                                        && is_numeric($postVars['kpsAdminSendReportAfter'])
                                                        && is_int((int)$postVars['kpsAdminSendReportAfter'])
                                                        && $postVars['kpsAdminSendReportAfter'] >= 10
                                                        && $postVars['kpsAdminSendReportAfter'] < 499)
                                                        ? absint($postVars['kpsAdminSendReportAfter']) : 50;

            /* Werbuns/Spam escapen
            *  nicht kleiner Sperre
            *  darf nicht größer als 500 sein
            *  -> default ist 25
            */
            $setReport['kpsReportSpam']             = (isset($postVars['kpsReportSpam'])
                                                        && !empty($postVars['kpsReportSpam'])
                                                        && is_numeric($postVars['kpsReportSpam'])
                                                        && is_int((int)$postVars['kpsReportSpam'])
                                                        && $postVars['kpsReportSpam'] >= 1
                                                        && $postVars['kpsReportSpam'] < 499)
                                                        ? absint($postVars['kpsReportSpam']) : 25;

            /* Auto-Sperre Werbuns/Spam escapen
            *  nicht kleiner Werbuns/Spam
            *  nicht größer als 500 sein
            *  -> default ist Werbuns/Spam + 1
            */
            $setReport['kpsAutoReportSpam']         = (isset($postVars['kpsAutoReportSpam'])
                                                        && !empty($postVars['kpsAutoReportSpam'])
                                                        && is_numeric($postVars['kpsAutoReportSpam'])
                                                        && is_int((int)$postVars['kpsAutoReportSpam'])
                                                        && $postVars['kpsAutoReportSpam'] > $setReport['kpsReportSpam']
                                                        && $postVars['kpsAutoReportSpam'] <= 500)
                                                        ? absint($postVars['kpsAutoReportSpam']) : $setReport['kpsReportSpam'] + 1;


            /* Unangemessen/Gewalt escapen
            *  nicht kleiner Sperre
            *  darf nicht größer als 500 sein
            *  -> default ist 25
            */
            $setReport['kpsReportUnreasonable']     = (isset($postVars['kpsReportUnreasonable'])
                                                        && !empty($postVars['kpsReportUnreasonable'])
                                                        && is_numeric($postVars['kpsReportUnreasonable'])
                                                        && is_int((int)$postVars['kpsReportUnreasonable'])
                                                        && $postVars['kpsReportUnreasonable'] >= 1
                                                        && $postVars['kpsReportUnreasonable'] < 499)
                                                        ? absint($postVars['kpsReportUnreasonable']) : 25;

            /* Auto-Sperre Unangemessen/Gewalt escapen
            *  nicht kleiner Unangemessen/Gewalt
            *  nicht größer als 500 sein
            *  -> default ist Werbuns/Spam + 1
            */
            $setReport['kpsAutoReportUnreasonable'] = (isset($postVars['kpsAutoReportUnreasonable'])
                                                        && !empty($postVars['kpsAutoReportUnreasonable'])
                                                        && is_numeric($postVars['kpsAutoReportUnreasonable'])
                                                        && is_int((int)$postVars['kpsAutoReportUnreasonable'])
                                                        && $postVars['kpsAutoReportUnreasonable'] > $setReport['kpsReportUnreasonable']
                                                        && $postVars['kpsAutoReportUnreasonable'] <= 500)
                                                        ? absint($postVars['kpsAutoReportUnreasonable']) : $setReport['kpsReportUnreasonable'] + 1;

            /* Doppelter Eintrag escapen
            *  nicht kleiner Sperre
            *  darf nicht größer als 500 sein
            *  -> default ist 25
            */
            $setReport['kpsReportDouble']           = (isset($postVars['kpsReportDouble'])
                                                        && !empty($postVars['kpsReportDouble'])
                                                        && is_numeric($postVars['kpsReportDouble'])
                                                        && is_int((int)$postVars['kpsReportDouble'])
                                                        && $postVars['kpsReportDouble'] >= 1
                                                        && $postVars['kpsReportDouble'] < 499)
                                                        ? absint($postVars['kpsReportDouble']) : 25;

            /* Auto-Sperre Doppelter Eintrag escapen
            *  nicht kleiner Doppelter Eintrag
            *  nicht größer als 500 sein
            *  -> default ist Werbuns/Spam + 1
            */
            $setReport['kpsAutoReportDouble']       = (isset($postVars['kpsAutoReportDouble'])
                                                        && !empty($postVars['kpsAutoReportDouble'])
                                                        && is_numeric($postVars['kpsAutoReportDouble'])
                                                        && is_int((int)$postVars['kpsAutoReportDouble'])
                                                        && $postVars['kpsAutoReportDouble'] > $setReport['kpsReportDouble']
                                                        && $postVars['kpsAutoReportDouble'] <= 500)
                                                        ? absint($postVars['kpsAutoReportDouble']) : $setReport['kpsReportDouble'] + 1;

            /* Personlichkeitsrecht escapen
            *  nicht kleiner Sperre
            *  darf nicht größer als 500 sein
            *  -> default ist 25
            */
            $setReport['kpsReportPrivacy']          = (isset($postVars['kpsReportPrivacy'])
                                                        && !empty($postVars['kpsReportPrivacy'])
                                                        && is_numeric($postVars['kpsReportPrivacy'])
                                                        && is_int((int)$postVars['kpsReportPrivacy'])
                                                        && $postVars['kpsReportPrivacy'] >= 1
                                                        && $postVars['kpsReportPrivacy'] < 499)
                                                        ? absint($postVars['kpsReportPrivacy']) : 25;

            /* Auto-Sperre Personlichkeitsrecht escapen
            *  nicht kleiner Personlichkeitsrecht
            *  nicht größer als 500 sein
            *  -> default ist Werbuns/Spam + 1
            */
            $setReport['kpsAutoReportPrivacy']      = (isset($postVars['kpsAutoReportPrivacy'])
                                                        && !empty($postVars['kpsAutoReportPrivacy'])
                                                        && is_numeric($postVars['kpsAutoReportPrivacy'])
                                                        && is_int((int)$postVars['kpsAutoReportPrivacy'])
                                                        && $postVars['kpsAutoReportPrivacy'] > $setReport['kpsReportPrivacy']
                                                        && $postVars['kpsAutoReportPrivacy'] <= 500)
                                                        ? absint($postVars['kpsAutoReportPrivacy']) : $setReport['kpsReportPrivacy'] + 1;

            /* Sonstiges escapen
            *  nicht kleiner Sperre
            *  darf nicht größer als 500 sein
            *  -> default ist 25
            */
            $setReport['kpsReportOthers']           = (isset($postVars['kpsReportOthers'])
                                                        && !empty($postVars['kpsReportOthers'])
                                                        && is_numeric($postVars['kpsReportOthers'])
                                                        && is_int((int)$postVars['kpsReportOthers'])
                                                        && $postVars['kpsReportOthers'] >= 1
                                                        && $postVars['kpsReportOthers'] < 499)
                                                        ? absint($postVars['kpsReportOthers']) : 25;

            /* Auto-Sperre Sonstiges escapen
            *  nicht kleiner Sonstiges
            *  nicht größer als 500 sein
            *  -> default ist Werbuns/Spam + 1
            */
            $setReport['kpsAutoReportOthers']       = (isset($postVars['kpsAutoReportOthers'])
                                                        && !empty($postVars['kpsAutoReportOthers'])
                                                        && is_numeric($postVars['kpsAutoReportOthers'])
                                                        && is_int((int)$postVars['kpsAutoReportOthers'])
                                                        && $postVars['kpsAutoReportOthers'] > $setReport['kpsReportOthers']
                                                        && $postVars['kpsAutoReportOthers'] <= 500)
                                                        ? absint($postVars['kpsAutoReportOthers']) : $setReport['kpsReportOthers'] + 1;

            // Checkbox escapen
            $setReport['kpsReportActivation'] = ($postVars['kpsReportActivation'] === '1') ? 'true' : 'false';

            // Einstellungen aktualisieren
            if (is_array($setReport)
                && !empty($setReport))
            {
                // Report serialisieren
                $setReport = serialize($setReport);

                if (is_serialized($setReport) == true)
                {
                    // True --> Update DB
                    update_option('kps_report', $setReport, 'yes');
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Reporting', 'kps')) . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
                else
                {
                    echo '
                    <div class="notice notice-error is-dismissible">
                    	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . esc_html(__('Fehler beim Serialieren der Daten', 'kps')) . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
            }
            else
            {
                echo '
                <div class="notice notice-error is-dismissible">
                	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . esc_html(__('Fehler bei der Validierung der Daten', 'kps')) . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
        }
        else
        {
            echo '
            <div class="notice notice-error is-dismissible">
            	<p><strong>' .  esc_html(__('Fehler: Token ungültig', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole Report Einstellungen
    $checked = kps_unserialize(get_option('kps_report', false));
    $checkedAdminSendReportAfter    = $checked['kpsAdminSendReportAfter'];
    $checkedReportSpam              = $checked['kpsReportSpam'];
    $checkedAutoReportSpam          = $checked['kpsAutoReportSpam'];
    $checkedReportUnreasonable      = $checked['kpsReportUnreasonable'];
    $checkedAutoReportUnreasonable  = $checked['kpsAutoReportUnreasonable'];
    $checkedReportDouble            = $checked['kpsReportDouble'];
    $checkedAutoReportDouble        = $checked['kpsAutoReportDouble'];
    $checkedReportPrivacy           = $checked['kpsReportPrivacy'];
    $checkedAutoReportPrivacy       = $checked['kpsAutoReportPrivacy'];
    $checkedReportOthers            = $checked['kpsReportPrivacy'];
    $checkedAutoReportOthers        = $checked['kpsAutoReportOthers'];
    $checkedReportIsActivated       = ($checked['kpsReportActivation'] === 'true') ? 'checked' : '';

    echo '
            <div class="kps_container" style="width: 33%"><h5>
' . esc_html(__('Einstellung der Anzahl der Meldungen pro Eintrag, bis der Administrator informiert wird.
Die Autosperre greift, sobald die Anzahl erreicht ist. Die Range bis zur Benachrichtigung liegt zwischen 1-499
Meldungen. Die Range bis zur Automatischen Sperre liegt zwischen 2-500 Meldungen.', 'kps')) . '.</h5>
                <form class="form" action="" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><label for="kpsAdminSendReportAfter">' . esc_html(__('Gesamtmeldungen Melden nach', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAdminSendReportAfter" id="kpsAdminSendReportAfter" class="form_num" value="' . $checkedAdminSendReportAfter . '" min="10" max="499" /> ' . esc_html(_n('Meldung', 'Meldungen', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label for="kpsReportSpam">' . esc_html(__('Werbung/Spam', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsReportSpam" id="kpsReportSpam" class="form_num" value="' . $checkedReportSpam . '" min="1" max="499" /> ' . esc_html(_n('Meldung', 'Meldungen', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsAutoReportSpam">' . esc_html(__('Automatische Sperre nach', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAutoReportSpam" id="kpsAutoReportSpam" class="form_num" value="' . $checkedAutoReportSpam . '" min="2" max="500" /> ' . esc_html(_n('Meldung', 'Meldungen', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label for="kpsReportUnreasonable">' . esc_html(__('Unangemessen/Gewalt', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsReportUnreasonable" id="kpsReportUnreasonable" class="form_num" value="' . $checkedReportUnreasonable . '" min="1" max="499" /> ' . esc_html(_n('Meldung', 'Meldungen', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsAutoReportUnreasonable">' . esc_html(__('Automatische Sperre nach', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAutoReportUnreasonable" id="kpsAutoReportUnreasonable" class="form_num" value="' . $checkedAutoReportUnreasonable . '" min="2" max="500" /> ' . esc_html(_n('Meldung', 'Meldungen', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label for="kpsReportDouble">' . esc_html(__('Doppelter Eintrag', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsReportDouble" id="kpsReportDouble" class="form_num" value="' . $checkedReportDouble . '" min="1" max="499" /> ' . esc_html(_n('Meldung', 'Meldungen', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsAutoReportDouble">' . esc_html(__('Automatische Sperre nach', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAutoReportDouble" id="kpsAutoReportDouble" class="form_num" value="' . $checkedAutoReportDouble . '" min="2" max="500" /> ' . esc_html(_n('Meldung', 'Meldungen', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label for="kpsReportPrivacy">' . esc_html(__('Persönlichkeitsrechte', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsReportPrivacy" id="kpsReportPrivacy" class="form_num" value="' . $checkedReportPrivacy . '" min="1" max="499" /> ' . esc_html(_n('Meldung', 'Meldungen', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsAutoReportPrivacy">' . esc_html(__('Automatische Sperre nach', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAutoReportPrivacy" id="kpsAutoReportPrivacy" class="form_num" value="' . $checkedAutoReportPrivacy . '" min="2" max="500" /> ' . esc_html(_n('Meldung', 'Meldungen', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label for="kpsReportOthers">' . esc_html(__('Sonstiges', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsReportOthers" id="kpsReportOthers" class="form_num" value="' . $checkedReportOthers . '" min="1" max="499" /> ' . esc_html(_n('Meldung', 'Meldungen', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsAutoReportOthers">' . esc_html(__('Automatische Sperre nach', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAutoReportOthers" id="kpsAutoReportOthers" class="form_num" value="' . $checkedAutoReportOthers . '" min="2" max="500" /> ' . esc_html(_n('Meldung', 'Meldungen', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label class="labelCheckbox" for="kpsReportActivation">' . esc_html(__('Automatische Sperren aktivieren', 'kps')) . '</label></td>
                                <td><input type="checkbox" name="kpsReportActivation" id="kpsReportActivation" value="1" ' . $checkedReportIsActivated . ' /></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Reporting" />
                                    <input type="hidden" id="kpsReportToken" name="kpsReportToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitReport" value="' . esc_html(__('Speichern', 'kps')) . '" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        ';
}

/**
 * Funktion Formlar Optionsfelder
 */
function kps_Optionfields()
{
    $verification   = false;

    // Token erstellen
    $token = wp_create_nonce('kpsFormOptionToken');

    if (isset($_POST['submitFormOptions']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsFormOptionTelephone',
            'kpsFormOptionMobile',
            'kpsFormOptionWhatsapp',
            'kpsFormOptionSignal',
            'kpsFormOptionViper',
            'kpsFormOptionTelegram',
            'kpsFormOptionFacebookMessenger',
            'kpsFormOptionWire',
            'kpsFormOptionHoccer',
            'kpsFormOptionSkype',
            'kpsFormOptionWebsite',
            'kpsFormOptionInstagram',
            'kpsFormOptionFacebook',
            'kpsFormOptionToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsFormOptionToken'], 'kpsFormOptionToken');

        // Verifizieren
        if ($verification == true)
        {
            // Foreach-Schleife für Checkbox
            foreach ($postList as $postItem)
            {
                if ($postItem !== 'kpsFormOptionToken')
                {
                    // Checkbox escapen
                    $setFormOption[$postItem] = ($postVars[$postItem] === '1') ? 'true' : 'false';
                }
            }

            // Formular Optionsfelder aktualisieren
            if (is_array($setFormOption)
                && !empty($setFormOption))
            {
                // Serialisieren
                $setFormOption = serialize($setFormOption);

                // Serialieren True --> Update DB
                if (is_serialized($setFormOption))
                {
                    update_option('kps_formOptions', $setFormOption);
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Formular-Optionen', 'kps')) . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
                else
                {
                    echo '
                    <div class="notice notice-error is-dismissible">
                    	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . esc_html(__('Fehler beim Serialieren der Daten', 'kps')) . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
            }
            else
            {
                echo '
                <div class="notice notice-error is-dismissible">
                	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . esc_html(__('Fehler bei der Validierung der Daten', 'kps')) . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
        }
        else
        {
            echo '
            <div class="notice notice-error is-dismissible">
            	<p><strong>' .  esc_html(__('Fehler: Token ungültig', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole Formular Optionsfelder Einstellungen
    $checked = kps_unserialize(get_option('kps_formOptions', false));
    $checkedFormOptionTelephone             = ($checked['kpsFormOptionTelephone'] === 'true') ? 'checked' : '';
    $checkedFormOptionMobile                = ($checked['kpsFormOptionMobile'] === 'true') ? 'checked' : '';
    $checkedFormOptionWhatsapp              = ($checked['kpsFormOptionWhatsapp'] === 'true') ? 'checked' : '';
    $checkedFormOptionSignal                = ($checked['kpsFormOptionSignal'] === 'true') ? 'checked' : '';
    $checkedFormOptionViper                 = ($checked['kpsFormOptionViper'] === 'true') ? 'checked' : '';
    $checkedFormOptionTelegram              = ($checked['kpsFormOptionTelegram'] === 'true') ? 'checked' : '';
    $checkedFormOptionWire                  = ($checked['kpsFormOptionWire'] === 'true') ? 'checked' : '';
    $checkedFormOptionHoccer                = ($checked['kpsFormOptionHoccer'] === 'true') ? 'checked' : '';
    $checkedFormOptionFacebookMessenger     = ($checked['kpsFormOptionFacebookMessenger'] === 'true') ? 'checked' : '';
    $checkedFormOptionSkype                 = ($checked['kpsFormOptionSkype'] === 'true') ? 'checked' : '';
    $checkedFormOptionWebsite               = ($checked['kpsFormOptionWebsite'] === 'true') ? 'checked' : '';
    $checkedFormOptionFacebook              = ($checked['kpsFormOptionFacebook'] === 'true') ? 'checked' : '';
    $checkedFormOptionInstagram             = ($checked['kpsFormOptionInstagram'] === 'true') ? 'checked' : '';

    echo '
            <div class="kps_container" style="width: 33%"><h5>' . esc_html(__('Einstellung der Eingabemöglichkeiten, welche Informationen der Autor, dem Anforderer, zur Kontaktaufnahme zur Verfügung stellt.', 'kps')) . '</h5>
                <form class="form" action="" method="post">
                    <table class="table" cellpadding="5" cellspacing="5">
                        <tbody>
                            <tr>
                                <td colspan="6"><u>' . esc_html(__('Direkter Kontakt', 'kps')) . '</u></td>
                            </tr>
                            <tr>
                                <td width="25"><input type="checkbox" name="kpsFormOptionTelephone" id="kpsFormOptionTelephone" value="1" ' . $checkedFormOptionTelephone . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionTelephone">' . esc_html(__('Telefon', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionMobile" id="kpsFormOptionMobile" value="1" ' . $checkedFormOptionMobile . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionMobile">' . esc_html(__('Handy', 'kps')) . '</label></td>
                                <td width="25"></td>
                                <td width="33%"></td>
                            </tr>
                            <tr>
                                <td colspan="6"><hr></td>
                            </tr>
                            <tr>
                                <td colspan="6"><u>' . esc_html(__('Messengerdienste', 'kps')) . '</u></td>
                            </tr>
                            <tr>
                                <td width="25"><input type="checkbox" name="kpsFormOptionSignal" id="kpsFormOptionSignal" value="1" ' . $checkedFormOptionSignal . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionSignal">' . esc_html(__('Signal', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionViper" id="kpsFormOptionViper" value="1" ' . $checkedFormOptionViper . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionViper">' . esc_html(__('Viper', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionTelegram" id="kpsFormOptionTelegram" value="1" ' . $checkedFormOptionTelegram . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionTelegram">' . esc_html(__('Telegram', 'kps')) . '</label></td>
                            </tr>
                            <tr>
                                <td width="25"><input type="checkbox" name="kpsFormOptionWhatsapp" id="kpsFormOptionWhatsapp" value="1" ' . $checkedFormOptionWhatsapp . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionWhatsapp">' . esc_html(__('Whatsapp', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionFacebookMessenger" id="kpsFormOptionFacebookMessenger" value="1" ' . $checkedFormOptionFacebookMessenger . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionFacebookMessenger">' . esc_html(__('Facebook-Messenger', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionHoccer" id="kpsFormOptionHoccer" value="1" ' . $checkedFormOptionHoccer . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionHoccer">' . esc_html(__('Hoccer', 'kps')) . '</label></td>
                            </tr>
                            <tr>
                                <td width="25"><input type="checkbox" name="kpsFormOptionSkype" id="kpsFormOptionSkype" value="1" ' . $checkedFormOptionSkype . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionSkype">' . esc_html(__('Skype', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionWire" id="kpsFormOptionWire" value="1" ' . $checkedFormOptionWire . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionWire">' . esc_html(__('Wire', 'kps')) . '</label></td>
                                <td width="25"></td>
                                <td width="33%"></td>
                            </tr>
                            <tr>
                                <td colspan="6"><hr></td>
                            </tr>
                            <tr>
                                <td colspan="6"><u>' . esc_html(__('Web-/ Profilseiten', 'kps')) . '</u></td>
                            </tr>
                            <tr>
                                <td width="25"><input type="checkbox" name="kpsFormOptionWebsite" id="kpsFormOptionWebsite" value="1" ' . $checkedFormOptionWebsite . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionWebsite">' . esc_html(__('Internetseite', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionFacebook" id="kpsFormOptionFacebook" value="1" ' . $checkedFormOptionFacebook . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionFacebook">' . esc_html(__('Facebook', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionInstagram" id="kpsFormOptionInstagram" value="1" ' . $checkedFormOptionInstagram . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionInstagram">' . esc_html(__('Instagram', 'kps')) . '</label></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="6" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Optionfields" />
                                    <input type="hidden" id="kpsFormOptionToken" name="kpsFormOptionToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitFormOptions" value="' . esc_html(__('Speichern', 'kps')) . '" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        ';
}

/**
 * Funktion Usereinstellungen
 */
function kps_UserSettings()
{
    $verification                       = false;
    $inputDisabled                      = '';
    $inputDisabledInfo                  = '';
    $inputShowAvatarDisabled            = '';
    $inputShowAvatarDisabledInfo        = '';
    $inputUserPrivacyAGBDisabled        = '';
    $inputUserPrivacyAGBDisabledInfo    = '';
    $inputUserPrivacyDSGVODisabled      = '';
    $inputUserPrivacyDSGVODisabledInfo  = '';

    // Token erstellen
    $token = wp_create_nonce('kpsUserSettingsToken');

    if (isset($_POST['submitUserSettings']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsUserRequireRegistration',
            'kpsUserRequirementRegistration',
            'kpsUserRequireAdminUnlock',
            'kpsUserProfilLink',
            'kpsUserReport',
            'kpsUserPrivacyAGB',
            'kpsUserPrivacyDSGVO',
            'kpsUserAvatar',
            'kpsUserRequirementReport',
            'kpsUserSettingsToken'
        );

        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsUserSettingsToken'], 'kpsUserSettingsToken');

        // Verifizieren
        if ($verification == true)
        {
            // Registierungspflicht
            $setUserSettings['kpsUserRequireRegistration']      = (!empty($postVars['kpsUserRequireRegistration'])
                                                                    && $postVars['kpsUserRequireRegistration'] === '1')
                                                                    && get_option('users_can_register') === '1'
                                                                    ? 'true' : 'false';

            // Anforderer muss sich registrieren
            $setUserSettings['kpsUserRequirementRegistration']  = (!empty($postVars['kpsUserRequirementRegistration'])
                                                                    && $postVars['kpsUserRequirementRegistration'] === '1')
                                                                    && get_option('users_can_register') === '1'
                                                                    ? 'true' : 'false';

            // Registieren um Einträge zu melden
            $setUserSettings['kpsUserRequirementReport']        = (!empty($postVars['kpsUserRequirementReport'])
                                                                    && $postVars['kpsUserRequirementReport'] == '1')
                                                                    && get_option('users_can_register') === '1'
                                                                    ? 'true' : 'false';

            // Anzeigen vom Avatar
            $setUserSettings['kpsUserAvatar']                   = (!empty($postVars['kpsUserAvatar'])
                                                                    && $postVars['kpsUserAvatar'] === '1')
                                                                    && get_option('show_avatars') === '1'
                                                                    ? 'true' : 'false';
            // Anzeigen der AGB's
            $setUserSettings['kpsUserPrivacyAGB']               = (!empty($postVars['kpsUserPrivacyAGB'])
                                                                    && $postVars['kpsUserPrivacyAGB'] === '1')
                                                                    && get_post_status(get_option('kps_agb')) !== false
                                                                    && post_password_required(get_option('kps_agb')) === false
                                                                    && get_option('kps_agb') > 0
                                                                    ? 'true' : 'false';

            // Anzeigen der DSGVO
            $setUserSettings['kpsUserPrivacyDSGVO']             = (!empty($postVars['kpsUserPrivacyDSGVO'])
                                                                    && $postVars['kpsUserPrivacyDSGVO'] === '1')
                                                                    && get_post_status(get_option('kps_dsgvo')) !== false
                                                                    && post_password_required(get_option('kps_dsgvo')) === false
                                                                    && get_option('kps_dsgvo') > 0
                                                                    ? 'true' : 'false';

            // Eintrage durch Administrator freigeben
            $setUserSettings['kpsUserRequireAdminUnlock']       = (!empty($postVars['kpsUserRequireAdminUnlock'])
                                                                    && $postVars['kpsUserRequireAdminUnlock'] === '1')
                                                                    ? 'true' : 'false';

            // Link zum User-Profil anzeigen
            $setUserSettings['kpsUserProfilLink']               = (!empty($postVars['kpsUserProfilLink'])
                                                                    && $postVars['kpsUserProfilLink'] === '1')
                                                                    ? 'true' : 'false';

            // Einträge melden
            $setUserSettings['kpsUserReport']                   = (!empty($postVars['kpsUserReport'])
                                                                    && get_option('users_can_register') === '1'
                                                                    && $postVars['kpsUserReport'] === '1')
                                                                    ? 'true' : 'false';

            // Zum Melden von Einträgen muss man sich registrieren
            if ($postVars['kpsUserRequirementReport'] === '1')
            {
                if(get_option('users_can_register') === '1')
                {
                    $setUserSettings['kpsUserRequirementReport'] = 'true';
                }
                else
                {
                    $setUserSettings['kpsUserRequirementReport'] = 'false';
                }
            }
            else
            {
                $setUserSettings['kpsUserRequirementReport'] = 'false';
            }

            // Fehlermeldungen
            if (!is_array($setUserSettings))
            {
                $error[] = esc_html(__('Fehler bei der Validierung der Daten', 'kps'));
            }

            // Usereinstellungen aktualisieren
            if (is_array($setUserSettings)
                && !empty($setUserSettings))
            {
                // Serialisieren
                $setUserSettings = serialize($setUserSettings);

                // Serialieren True --> Update DB
                if (is_serialized($setUserSettings))
                {
                    update_option('kps_userSettings', $setUserSettings);
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Benutzereinstellung', 'kps')) . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
                else
                {
                    echo '
                    <div class="notice notice-error is-dismissible">
                    	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . esc_html(__('Fehler bei der Validierung der Daten', 'kps')) . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
            }
            else
            {
                foreach ($error as $key => $errors)
                {
                    echo '
                    <div class="notice notice-error is-dismissible">
                    	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . $error[$key] . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
            }
        }
        else
        {
            echo '
            <div class="notice notice-error is-dismissible">
            	<p><strong>' .  esc_html(__('Fehler: Token ungültig', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole Usereinstellungen
    $checked = kps_unserialize(get_option('kps_userSettings', false));
    $checkedUserRequireRegistration     = ($checked['kpsUserRequireRegistration'] === 'true') ? 'checked' : '';
    $checkedUserRequirementRegistration = ($checked['kpsUserRequirementRegistration'] === 'true') ? 'checked' : '';
    $checkedUserRequireAdminUnlock      = ($checked['kpsUserRequireAdminUnlock'] === 'true') ? 'checked' : '';
    $checkedUserProfilLink              = ($checked['kpsUserProfilLink'] === 'true') ? 'checked' : '';
    $checkedUserPrivacyAGB              = ($checked['kpsUserPrivacyAGB'] === 'true') ? 'checked' : '';
    $checkedUserPrivacyDSGVO            = ($checked['kpsUserPrivacyDSGVO'] === 'true') ? 'checked' : '';
    $checkedUserAvatar                  = ($checked['kpsUserAvatar'] === 'true') ? 'checked' : '';
    $checkedUserRequirementReport       = ($checked['kpsUserRequirementReport'] === 'true') ? 'checked' : '';
    $checkedUserReport                  = ($checked['kpsUserReport'] === 'true') ? 'checked' : '';

    // Prüfe, ob Avatar anzeigen aktiviert ist
    if (get_option('show_avatars') !== '1')
    {
        $inputShowAvatarDisabled = 'disabled="disabled"';
        $inputShowAvatarDisabledInfo = '<font color="red">(' . esc_html(__('Deaktiviert im Wordpress', 'kps')) . ')</font>';
    }

    // Prüfe, ob User sich registieren dürfen
    if (get_option('users_can_register') !== '1')
    {
        $inputDisabled = 'disabled="disabled"';
        $inputDisabledInfo = '<font color="red">(' . esc_html(__('Deaktiviert im Wordpress', 'kps')) . ')</font>';
    }

    // Prüfe, ob AGB's gesetzt wurde, öffentlich ist und kein Passwortschutz hat
    if (get_option('kps_agb') == '0' OR get_post_status(get_option('kps_agb')) != 'publish' OR post_password_required(get_option('kps_agb')) === true)
    {
        $inputUserPrivacyAGBDisabled = 'disabled="disabled"';

        if (get_post_status(get_option('kps_agb')) !== false)
        {
            if (get_option('kps_agb') > '0' && get_post_status(get_option('kps_agb')) == 'draft')
            {
                // im Entwurfs-Status
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">' . esc_html(__('AGBs im Entwurfs-Status', 'kps')) . '</font>';
            }
            elseif (get_option('kps_agb') > '0' && get_post_status(get_option('kps_agb')) == 'private')
            {
                // im Privat-Status
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">' . esc_html(__('AGBs im Privat-Status', 'kps')) . '</font>';
            }
            elseif (get_option('kps_agb') > '0' && get_post_status(get_option('kps_agb')) == 'pending')
            {
                // im Muster-Status
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">' . esc_html(__('AGBs im Muster-Status', 'kps')) . '</font>';
            }
            elseif (get_option('kps_agb') > '0' && get_post_status(get_option('kps_agb')) == 'auto-draft')
            {
                // ohne Content
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">' . esc_html(__('AGBs ohne Inhalt', 'kps')) . '</font>';
            }
            elseif (get_option('kps_agb') > '0' && get_post_status(get_option('kps_agb')) == 'inherit')
            {
                // Revision
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">' . esc_html(__('AGBs in Revision', 'kps')) . '</font>';
            }
            elseif (get_option('kps_agb') > '0' && get_post_status(get_option('kps_agb')) == 'trash')
            {
                // Revision
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">' . esc_html(__('AGBs im Papierkorb', 'kps')) . '</font>';
            }
            elseif (get_option('kps_agb') > '0' && post_password_required(get_option('kps_agb')) === true)
            {
                // hat Passwort
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">' . esc_html(__('AGBs mit Passwort', 'kps')) . '</font>';
            }
            else
            {
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">' . esc_html(__('AGBs nicht gesetzt', 'kps')) . '</font>';
            }
        }
        else
        {
            // Post-ID existiert nicht
            $inputUserPrivacyAGBDisabledInfo = '<font color="red">' . esc_html(__('AGBs nicht gesetzt', 'kps')) . '</font>';
        }
    }

    // Prüfe, ob DSGVO gesetzt wurde, öffentlich ist und kein Passwortschutz hat
    if (get_option('kps_dsgvo') == '0' OR get_post_status(get_option('kps_dsgvo')) != 'publish' OR post_password_required(get_option('kps_dsgvo')) === true)
    {

        $inputUserPrivacyDSGVODisabled = 'disabled="disabled"';

        if (get_post_status(get_option('kps_dsgvo')) !== false)
        {
            if (get_option('kps_dsgvo') > '0' && get_post_status(get_option('kps_dsgvo')) == 'draft')
            {
                // im Entwurfs-Status
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">' . esc_html(__('DSGVO im Entwurfs-Status', 'kps')) . '</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && get_post_status(get_option('kps_dsgvo')) == 'private')
            {
                // im Privat-Status
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">' . esc_html(__('DSGVO im Privat-Status', 'kps')) . '</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && get_post_status(get_option('kps_dsgvo')) == 'pending')
            {
                // im Muster-Status
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">' . esc_html(__('DSGVO im Muster-Status', 'kps')) . '</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && get_post_status(get_option('kps_dsgvo')) == 'auto-draft')
            {
                // ohne Content
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">' . esc_html(__('DSGVO ohne Inhalt', 'kps')) . '</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && get_post_status(get_option('kps_dsgvo')) == 'inherit')
            {
                // Revision
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">' . esc_html(__('DSGVO in Revision', 'kps')) . '</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && get_post_status(get_option('kps_dsgvo')) == 'trash')
            {
                // Revision
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">' . esc_html(__('DSGVO im Papierkorb', 'kps')) . '</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && post_password_required(get_option('kps_dsgvo')) === true)
            {
                // hat Passwort
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">' . esc_html(__('DSGVO mit Passwort', 'kps')) . '</font>';
            }
            else
            {
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">' . esc_html(__('DSGVO nicht gesetzt', 'kps')) . '</font>';
            }
        }
        else
        {
            // Post-ID existiert nicht
            $inputUserPrivacyDSGVODisabledInfo = '<font color="red">' . esc_html(__('DSGVO nicht gesetzt', 'kps')) . '</font>';
        }
    }

    echo '
            <div class="kps_container" style="width: 33%"><h5>' . esc_html(__('Einstellungen zum Formular und der Ausgabe selbst.', 'kps')) . '</h5>
                <form class="form" action="" method="post">
                    <table class="table" cellpadding="5" cellspacing="5">
                        <tbody>
                            <tr>
                                <td><input id="kpsUserRequireRegistration" type="checkbox" name="kpsUserRequireRegistration" value="1" ' . $inputDisabled . ' ' . $checkedUserRequireRegistration . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserRequireRegistration">' . esc_html(__('Autor muss sich registieren', 'kps')) . '</label> ' . $inputDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserRequirementRegistration" type="checkbox"  name="kpsUserRequirementRegistration" value="1" ' . $inputDisabled . ' ' . $checkedUserRequirementRegistration . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserRequirementRegistration">' . esc_html(__('Anforderer muss sich registrieren', 'kps')) . '</label> ' . $inputDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserRequirementReport" type="checkbox" name="kpsUserRequirementReport" value="1" ' . $inputDisabled . ' ' . $checkedUserRequirementReport . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserRequirementReport">' . esc_html(__('Registrieren um Einträge zu melden', 'kps')) . '</label> ' . $inputDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserRequireAdminUnlock" type="checkbox" name="kpsUserRequireAdminUnlock" value="1" ' . $checkedUserRequireAdminUnlock . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserRequireAdminUnlock">' . esc_html(__('Einträge durch Admin freischalten', 'kps')) . '</label></td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserReport" type="checkbox" name="kpsUserReport" value="1"  ' . $inputDisabled . ' ' . $checkedUserReport . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserReport">' . esc_html(__('Einträge melden können', 'kps')) . '</label> ' . $inputDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserPrivacyAGB" type="checkbox" name="kpsUserPrivacyAGB" value="1" ' . $inputUserPrivacyAGBDisabled . ' ' . $checkedUserPrivacyAGB . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserPrivacyAGB">' . esc_html(__('Autor muss AGBs akzeptieren', 'kps')) . '</label> ' . $inputUserPrivacyAGBDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserPrivacyDSGVO" type="checkbox" name="kpsUserPrivacyDSGVO" value="1" ' . $inputUserPrivacyDSGVODisabled . ' ' . $checkedUserPrivacyDSGVO . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserPrivacyDSGVO">' . esc_html(__('Autor muss DSGVO akzeptieren', 'kps')) . '</label> ' . $inputUserPrivacyDSGVODisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserProfilLink" type="checkbox" name="kpsUserProfilLink" value="1" ' . $checkedUserProfilLink . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserProfilLink">' . esc_html(__('Links zum User-Profil (registriere Autoren)', 'kps')) . '</label></td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserAvatar" type="checkbox" name="kpsUserAvatar" value="1" ' . $inputShowAvatarDisabled . ' ' . $checkedUserAvatar . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserAvatar">' . esc_html(__('Autoren-Avatar anzeigen (registierte Autoren)', 'kps')) . '</label> ' . $inputShowAvatarDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_UserSettings" />
                                    <input type="hidden" id="kpsUserSettingsToken" name="kpsUserSettingsToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitUserSettings" value="' . esc_html(__('Speichern', 'kps')) . '" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        ';
}

/**
 * Funktion Pagination
 */
function kps_Pagination()
{
    $verification   = false;

    // Token erstellen
    $token = wp_create_nonce('kpsPaginationToken');

    if (isset($_POST['submitPagination']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsBackendPagination',
            'kpsFrontendPagination',
            'kpsPaginationToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsPaginationToken'], 'kpsPaginationToken');

        // Verifizieren
        if ($verification == true)
        {
            // Seitenanzahl Backend escapen -> default sind 10 Seiten
            $setBackendPagination   = (isset($postVars['kpsBackendPagination'])
                                        && !empty($postVars['kpsBackendPagination'])
                                        && is_numeric($postVars['kpsBackendPagination'])
                                        && is_int((int)$postVars['kpsBackendPagination']
                                        && $postVars['kpsBackendPagination'] < 100)
                                        && $postVars['kpsBackendPagination'] >= 1 )
                                        ? absint($postVars['kpsBackendPagination']) : 10;

            // Seitenanzahl Frontend escapen -> default sind 5 Seiten
            $setFrontendPagination  = (isset($postVars['kpsFrontendPagination'])
                                        && !empty($postVars['kpsFrontendPagination'])
                                        && is_numeric($postVars['kpsFrontendPagination'])
                                        && is_int((int)$postVars['kpsFrontendPagination'])
                                        && $postVars['kpsFrontendPagination'] >= 1
                                        && $postVars['kpsFrontendPagination']< 100)
                                        ? absint($postVars['kpsFrontendPagination']) : 5;

            // Pagination aktualisieren
            if (is_numeric($setBackendPagination)
                && is_numeric($setFrontendPagination))
            {
                // True --> Update DB
                update_option('kps_backendPagination', $setBackendPagination, 'yes');
                update_option('kps_frontendPagination', $setFrontendPagination, 'yes');
                echo '
                <div class="notice notice-success is-dismissible">
                	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Navigation', 'kps')) . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
            else
            {
                echo '
                <div class="notice notice-error is-dismissible">
                	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . esc_html(__('Fehler bei der Validierung der Daten', 'kps')) . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
        }
        else
        {
            echo '
            <div class="notice notice-error is-dismissible">
            	<p><strong>' .  esc_html(__('Fehler: Token ungültig', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    echo '
            <div class="kps_container" style="width: 33%"><h5>
' . esc_html(__('Einstellung der Anzahl der Einträge pro Seite im Administrationsbereich,
als auch in der Ausgabe (Frontend). Die Range kann eingestellt werden zwischen 1-99 Einträgen pro Seite.', 'kps')) . '.</h5>
                <form class="form" action="" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><label for="kpsBackendPagination">' . esc_html(__('Administrationsbereich', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsBackendPagination" id="kpsBackendPagination" class="form_num" value="' . get_option('kps_backendPagination', false) . '" min="1" max="99" /> ' . esc_html(_n('Eintrag pro Seite', 'Einträge pro Seite', get_option('kps_backendPagination', false)) , 'kps') . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsFrontendPagination">' . esc_html(__('Ausgabe Frontend', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsFrontendPagination" id="kpsFrontendPagination" class="form_num" value="' . get_option('kps_frontendPagination', false) . '" min="1" max="99" /> ' . esc_html(_n('Eintrag pro Seite', 'Einträge pro Seite', get_option('kps_frontendPagination', false)) , 'kps') . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Pagination" />
                                    <input type="hidden" id="kpsPaginationToken" name="kpsPaginationToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitPagination" value="' . esc_html(__('Speichern', 'kps')) . '" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        ';
}

/**
 * Funktion Captcha
 */
function kps_Spam()
{
    $verification   = false;

    // Token erstellen
    $token = wp_create_nonce('kpsCaptchaToken');

    if (isset($_POST['submitCaptcha']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsCaptchaSiteKey',
            'kpsCaptchaSecretKey',
            'kpsCaptchaActivated',
            'kpsCaptchaToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsCaptchaToken'], 'kpsCaptchaToken');

        // Verifizieren
        if ($verification == true)
        {
            // Key's escapen
            $setCaptchaSettings['kpsCaptchaSiteKey']    = sanitize_text_field($postVars['kpsCaptchaSiteKey']);
            $setCaptchaSettings['kpsCaptchaSecretKey']  = sanitize_text_field($postVars['kpsCaptchaSecretKey']);

            // Checkbox escapen
            $setCaptcha     = (!empty($setCaptchaSettings['kpsCaptchaSiteKey'])
                                && !empty($setCaptchaSettings['kpsCaptchaSecretKey'])
                                && $postVars['kpsCaptchaActivated'] === '1') ? 'true' : 'false';

            // Fehlermeldungen
            if (is_null($setCaptchaSettings['kpsCaptchaSiteKey'])
                OR $setCaptchaSettings['kpsCaptchaSiteKey'] == ''
                OR is_null($setCaptchaSettings['kpsCaptchaSecretKey'])
                OR $setCaptchaSettings['kpsCaptchaSecretKey'] == ''
                OR $setCaptcha === 'false'
                )
            {
                echo '
                <div class="notice notice-warning is-dismissible">
                	<p><strong>' .  esc_html(__('Warnung', 'kps')) . ':&#160;' . esc_html(__('Kein Spamschutz möglich', 'kps')) . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }

            if (!is_array($setCaptchaSettings))
            {
                $error[] = esc_html(__('Fehler bei der Validierung der Daten', 'kps'));
            }

            // Captcha aktualisieren
            if (is_array($setCaptchaSettings)
                && !empty($setCaptchaSettings))
            {
                // Serialisieren
                $setCaptchaSettings = serialize($setCaptchaSettings);

                // Serialieren True --> Update DB
                if (is_serialized($setCaptchaSettings))
                {
                    update_option('kps_captchakeys', $setCaptchaSettings);
                    update_option('kps_captcha', $setCaptcha);
                echo '
                <div class="notice notice-success is-dismissible">
                	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Spam-Schutz', 'kps')) . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
                }
                else
                {
                    echo '
                    <div class="notice notice-error is-dismissible">
                    	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . esc_html(__('Fehler bei der Validierung der Daten', 'kps')) . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
            }
            else
            {
                foreach ($error as $key => $errors)
                {
                    echo '
                    <div class="notice notice-error is-dismissible">
                    	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . $error[$key] . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
            }
        }
        else
        {
            echo '
            <div class="notice notice-error is-dismissible">
            	<p><strong>' .  esc_html(__('Fehler: Token ungültig', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole Captcha Einstellungen
    $checkedKeys                = kps_unserialize(get_option('kps_captchakeys', false));
    $checkedCaptchaSiteKey      = esc_attr($checkedKeys['kpsCaptchaSiteKey']);
    $checkedCaptchaSecretKey    = esc_attr($checkedKeys['kpsCaptchaSecretKey']);

    $checked = get_option('kps_captcha', false);
    $checkedCaptchaActivated = ($checked === 'true') ? 'checked' : '';

    echo '
            <div class="kps_container" style="width: 33%"><h5>
' . esc_html(__('Gib hier die Google-reCaptcha Keys ein. Der Site-Key wird für den Robot genutzt. Der Secret-Key wird verwenden
für die Kommunikation zwischen Ihrer Website und Google. Halte den Schlüssel geheim. Anmelden kannst Du dich unter', 'kps')) .
                ' <a href="https://www.google.com/recaptcha/" target="_blank" rel="noopener">https://www.google.com/recaptcha/</a>.</h5>
                <form class="form" action="" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><label for="kpsCaptchaSiteKey">' . esc_html(__('Site-Key', 'kps')) . '</label></td>
                                <td><input type="text" name="kpsCaptchaSiteKey" id="kpsCaptchaSiteKey" autocomplete="off" class="form_field" value="' . $checkedCaptchaSiteKey . '" /></td>
                            </tr>
                            <tr>
                                <td><label for="kpsCaptchaSecretKey">' . esc_html(__('Secret-Key', 'kps')) . '</label></td>
                                <td><input type="text" name="kpsCaptchaSecretKey" id="kpsCaptchaSecretKey" autocomplete="off" class="form_field" value="' . $checkedCaptchaSecretKey . '" /></td>
                            </tr>
                            <tr>
                                <td><label class="labelCheckbox" for="kpsCaptchaActivated">' . esc_html(__('Google reCaptcha aktivieren', 'kps')) . '</label></td>
                                <td><input type="checkbox" name="kpsCaptchaActivated" id="kpsCaptchaActivated" value="1" ' . $checkedCaptchaActivated . ' /></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Spam" />
                                    <input type="hidden" id="kpsCaptchaToken" name="kpsCaptchaToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitCaptcha" value="' . esc_html(__('Speichern', 'kps')) . '" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        ';
}

/**
 * Funktion Grundeinstellungen
 */
function kps_BasicSettings()
{
    $verification   = false;

    // Token erstellen
    $token = wp_create_nonce('kpsBasicSettingsToken');

    if (isset($_POST['submitBasicSettings']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsDeleteTimeEntry',
            'kpsDeleteTimeNoEntry',
            'kpsFormWordCount',
            'kpsBasicSettingsToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsBasicSettingsToken'], 'kpsBasicSettingsToken');

        // Verifizieren
        if ($verification == true)
        {
            // Freigegebene Einträge
            $setDeleteTimeEntry = floor(absint($postVars['kpsDeleteTimeEntry'])) * 24 * 60 * 60;

            // Zeit darf nicht kleiner als 1 Tag sein -> default sind 90 Tage
            $setDeleteTimeEntry     = (isset($setDeleteTimeEntry)
                                        && !empty($setDeleteTimeEntry)
                                        && is_numeric($setDeleteTimeEntry)
                                        && is_int((int)$setDeleteTimeEntry / 86400)
                                        && $setDeleteTimeEntry >= 86400) ? $setDeleteTimeEntry : 7776000;

            // Zeit darf nicht größer als 180 Tag sein -> default sind 90 Tage
            $setDeleteTimeEntry     = (isset($setDeleteTimeEntry)
                                        && !empty($setDeleteTimeEntry)
                                        && is_numeric($setDeleteTimeEntry)
                                        && is_int((int)$setDeleteTimeEntry / 86400)
                                        && $setDeleteTimeEntry <= 15552000) ? $setDeleteTimeEntry : 7776000;

            // Wartende Einträge
            $setDeleteTimeNoEntry = floor(absint($postVars['kpsDeleteTimeNoEntry'])) * 24 * 60 * 60;

            // Zeit darf nicht kleiner als 30 Tage sein -> default sind 60 Tage
            $setDeleteTimeNoEntry   = (isset($setDeleteTimeNoEntry)
                                        && !empty($setDeleteTimeNoEntry)
                                        && is_numeric($setDeleteTimeNoEntry)
                                        && is_int((int)$setDeleteTimeNoEntry / 86400)
                                        && $setDeleteTimeNoEntry >= 2592000) ? $setDeleteTimeNoEntry : 5184000;

            // Zeit darf nicht größer als 180 Tag sein -> default sind 60 Tage
            $setDeleteTimeNoEntry   = (isset($setDeleteTimeNoEntry)
                                        && !empty($setDeleteTimeNoEntry)
                                        && is_numeric($setDeleteTimeNoEntry)
                                        && is_int((int)$setDeleteTimeNoEntry / 86400)
                                        && $setDeleteTimeNoEntry <= 15552000) ? $setDeleteTimeNoEntry : 5184000;

            // Wortzahl escapen -> default ist 1 Wort
            $setFormWordCount =     (isset($postVars['kpsFormWordCount'])
                                        && !empty($postVars['kpsFormWordCount'])
                                        && is_numeric($postVars['kpsFormWordCount'])
                                        && is_int((int)$postVars['kpsFormWordCount'])
                                        && $postVars['kpsFormWordCount'] > 1 ) ? absint($postVars['kpsFormWordCount']) : 1;

            // Einstellungen aktualisieren
            if (is_numeric($setDeleteTimeEntry)
                && is_numeric($setDeleteTimeNoEntry)
                && is_numeric($setFormWordCount))
            {
                // True --> Update DB
                update_option('kps_deleteEntryTime', $setDeleteTimeEntry, 'yes');
                update_option('kps_deleteNoEntryTime', $setDeleteTimeNoEntry, 'yes');
                update_option('kps_formWordCount', $setFormWordCount, 'yes');
                echo '
                <div class="notice notice-success is-dismissible">
                	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Grundeinstellung', 'kps')) . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
            else
            {
                echo '
                <div class="notice notice-error is-dismissible">
                	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . esc_html(__('Fehler bei der Validierung der Daten', 'kps')) . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
        }
        else
        {
            echo '
            <div class="notice notice-error is-dismissible">
            	<p><strong>' .  esc_html(__('Fehler: Token ungültig', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // UNIX-Timestamp in Tage umrechnen
    $checkedDeleteTimeEntry     = get_option('kps_deleteEntryTime', false) / 24 / 60 / 60;
    $checkedDeleteTimeNoEntry   = get_option('kps_deleteNoEntryTime', false) / 24 / 60 / 60;

    echo '
            <div class="kps_container" style="width: 33%"><h5>
' . esc_html(__('Die Löschzeiten geben vor, wann ein "freigegebener Eintrag" oder ein "Wartender Eintrag" aus dem System (Datenbank) gelöscht wird.
Die Range für freigegebene Einträge liegt zwischen 1-180 Tagen. Die Range für wartende Einträge liegt zwischen 30-180 Tagen.
In der Textarea wird die mindestanzahl der Wörter, welche der Autor schreiben soll, festgelegt.', 'kps')) . '</h5>
                <form class="form" action="" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td width="50%" style="text-align: right;"><label for="kpsDeleteTimeEntry">' . esc_html(__('Freigegebene Einträge', 'kps')) . '</label></td>
                                <td width="50%" style="text-align: left;"><input type="number" name="kpsDeleteTimeEntry" id="kpsDeleteTimeEntry" class="form_num" value="' . $checkedDeleteTimeEntry . '" min="1" max="180" aria-required="true" required="required" /> ' . esc_html(_n('Tag', 'Tage', $checkedDeleteTimeEntry , 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td width="50%" style="text-align: right;"><label for="kpsDeleteTimeNoEntry">' . esc_html(__('Wartende Einträge', 'kps')) . '</label></td>
                                <td width="50%" style="text-align: left;"><input type="number" name="kpsDeleteTimeNoEntry" id="kpsDeleteTimeNoEntry" class="form_num" value="' . $checkedDeleteTimeNoEntry . '" min="30" max="180" aria-required="true" required="required" /> ' . esc_html(_n('Tag', 'Tage', $checkedDeleteTimeNoEntry , 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td width="50%" style="text-align: right;"><label for="kpsFormWordCount">' . esc_html(__('Formular-Textarea', 'kps')) . '</label></td>
                                <td width="50%" style="text-align: left;"><input type="number" name="kpsFormWordCount" id="kpsFormWordCount" class="form_num" value="' . get_option('kps_formWordCount', false) . '" min="1" /> ' . esc_html(_n('Wort', 'Wörter', get_option('kps_formWordCount', false) , 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_BasicSettings" />
                                    <input type="hidden" id="kpsBasicSettingsToken" name="kpsBasicSettingsToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitBasicSettings" value="' . esc_html(__('Speichern', 'kps')) . '" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        ';
}

/**
 * Funktion Emaileinstellung
 */
function kps_EmailSettings()
{
    $verification   = false;

    // Token erstellen
    $token = wp_create_nonce('kpsEmailToken');

    if (isset($_POST['submitEmail']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsMailFrom',
            'kpsEmailCC',
            'kpsEmailReport',
            'kpsEmailInformation',
            'kpsEmailToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsEmailToken'], 'kpsEmailToken');

        // Verifizieren
        if ($verification == true)
        {
            // Email-Adresse escapen
            $setEmail                       = sanitize_email($postVars['kpsMailFrom']);
            $setEmailCC['kpsEmailCC']       = sanitize_email($postVars['kpsEmailCC']);
            $setEmailCC['kpsEmailReport']   = sanitize_email($postVars['kpsEmailReport']);

            // Haupt-Email-Adressen prüfen
            if (is_email($setEmail) === false)
            {
                $setEmail = get_bloginfo('admin_email', 'raw');
            }

            // Email-Kopie prüfen
            $setEmailCC['kpsEmailCC']           = (is_email($setEmailCC['kpsEmailCC']) !== false) ? $setEmailCC['kpsEmailCC'] : $setEmail;

            // Email Report prüfen
            $setEmailCC['kpsEmailReport']       = (is_email($setEmailReport['kpsEmailReport']) !== false) ? $setEmailReport['kpsEmailReport'] : $setEmail;

            // Checkbox escapen
            $setEmailCC['kpsEmailInformation']  = ($postVars['kpsEmailInformation'] === '1') ? 'true' : 'false';

            // Fehlermeldungen
            if ((is_email($setEmail) === false) OR (is_email($setEmailCC['kpsEmailCC']) !== false) OR (is_email($setEmailCC['kpsEmailReport']) !== false))
            {
                $error[] = esc_html(__('Email-Adresse wurde auf Grundeinstellung gestellt', 'kps'));
            }
            if (!is_array($setEmailCC))
            {
                $error[] = esc_html(__('Fehler bei der Validierung der Daten', 'kps'));
            }

            // Einstellungen aktualisieren
            if (!empty($setEmail)
                && is_array($setEmailCC)
                && !empty($setEmailCC))
            {
                // Serialisieren
                $setEmailCC = serialize($setEmailCC);

                // Serialieren True --> Update DB
                if (is_serialized($setEmailCC))
                {
                    // True --> Update DB
                    update_option('kps_mailFromCC', $setEmailCC, 'yes');
                    update_option('kps_mailFrom', $setEmail, 'yes');
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Emailbenachrichtigung', 'kps')) . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
                else
                {
                    echo '
                    <div class="notice notice-error is-dismissible">
                    	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . esc_html(__('Fehler beim Serialieren der Daten', 'kps')) . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
            }
            else
            {
                foreach ($error as $key => $errors)
                {
                    echo '
                    <div class="notice notice-error is-dismissible">
                    	<p><strong>' .  esc_html(__('Fehler', 'kps')) . ':&#160;' . $error[$key] . '</strong></p>
                    	<button type="button" class="notice-dismiss">
                    		<span class="screen-reader-text">Dismiss this notice.</span>
                    	</button>
                    </div>
                    ';
                }
            }
        }
        else
        {
            echo '
            <div class="notice notice-error is-dismissible">
            	<p><strong>' .  esc_html(__('Fehler: Token ungültig', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole EmailCC Einstellungen
    $checked = kps_unserialize(get_option('kps_mailFromCC', false));
    $checkedEmailCC = esc_attr($checked['kpsEmailCC']);
    $checkedEmailReport = esc_attr($checked['kpsEmailReport']);
    $checkedEmailInformation = ($checked['kpsEmailInformation'] === 'true') ? 'checked' : '';

    echo '
            <div class="kps_container" style="width: 33%"><h5>
' . esc_html(__('Die Email-Adressen sind eine optionale Eingabe. Wenn keine Email-Adresse eingegeben wird,
nutzt das Script die im Board gesetzte Email-Adresse. Desweiteren wird diese genutzt, wenn die Einträge durch den
Administrator freigegeben werden müssen. Es können zusätzliche Information zu den Aktivitäten abgerufen werden.', 'kps')) . '</h5>
                <form class="form" action="" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><label for="kpsMailFrom">' . esc_html(__('Email', 'kps')) . '</label></td>
                                <td><input type="email" name="kpsMailFrom" id="kpsMailFrom" autocomplete="off" class="form_field" value="' . esc_attr(get_option('kps_MailFrom', false)) . '" /></td>
                            </tr>
                            <tr>
                                <td><label for="kpsEmailCC">' . esc_html(__('Email-Kopie', 'kps')) . '</label></td>
                                <td><input type="email" name="kpsEmailCC" id="kpsEmailCC" autocomplete="off" class="form_field" value="' . $checkedEmailCC . '"  /></td>
                            </tr>
                            <tr>
                                <td><label for="kpsEmailReport">' . esc_html(__('Report-Email', 'kps')) . '</label></td>
                                <td><input type="email" name="kpsEmailReport" id="kpsEmailReport" autocomplete="off" class="form_field" value="' . $checkedEmailReport . '"  /></td>
                            </tr>
                           <tr>
                                <td><label class="labelCheckbox" for="kpsEmailInformation">' . esc_html(__('Information', 'kps')) . '</label></td>
                                <td><input type="checkbox" name="kpsEmailInformation" id="kpsEmailInformation" value="1" ' . $checkedEmailInformation . '/></td>
                            </tr>
                          <tr>
                                <td colspan="2" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_EmailSettings" />
                                    <input type="hidden" id="kpsEmailToken" name="kpsEmailToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitEmail" value="' . esc_html(__('Speichern', 'kps')) . '" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        ';
}

