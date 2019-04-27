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
 * Hauptfunktion
 */
function kps_Settings()
{
    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('manage_options'))
    {
        die(esc_html(__('Access denied!', 'kps')));
    }

    // Javascript einladen
    kps_admin_enqueue();

    add_meta_box('kps_admin_pagination', esc_html(__('Entries / Page', 'kps')) , 'kps_admin_pagination', 'kps_Settings', 'left');

    $kps_tab = 'kps_BasicSettings'; // Start-Tab
    if (isset($_POST['kps_tab']))
    {
        $kps_tab = $_POST['kps_tab'];
    }
?>
        <div id="kps" class="wrap kps">
            <div>
                <h3>
                    <?php echo esc_html(__('Climbing-Partner-Search', 'kps')); ?> - <?php echo esc_html(__('Overview', 'kps')); ?>
               </h3>
            </div>
    		<h2 class="nav-tab-wrapper kps_nav_tab_wrapper">
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_BasicSettings') { echo "nav-tab-active";} ?>" rel="kps_BasicSettings">
                    <div style="text-align: center;"><?php  esc_html_e('Default-Settings', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_UserSettings') { echo "nav-tab-active";} ?>" rel="kps_UserSettings">
                    <div style="text-align: center;"><?php  esc_html_e('Usersettings', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Reporting') { echo "nav-tab-active";} ?>" rel="kps_Reporting">
                    <div style="text-align: center;"><?php  esc_html_e('Reporting', 'kps'); ?></div>
                </a>
                <a href="" class="nav-tab <?php if ($kps_tab == 'kps_EmailSettings') { echo "nav-tab-active";} ?>" rel="kps_EmailSettings">
                    <div style="text-align: center;"><?php  esc_html_e('Email notification', 'kps'); ?></div>
                </a>
                <a href="" class="nav-tab <?php if ($kps_tab == 'kps_Spam') { echo "nav-tab-active";} ?>" rel="kps_Spam">
                    <div style="text-align: center;"><?php  esc_html_e('Spam-Protection', 'kps'); ?></div>
                </a>
                <a href="" class="nav-tab <?php if ($kps_tab == 'kps_Optionfields') { echo "nav-tab-active";} ?>" rel="kps_Optionfields">
                    <div style="text-align: center;"><?php  esc_html_e('Form options', 'kps'); ?></div>
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
            // Reportanzahl escapen -> default sind 50 Meldungen
            $setReport['kpsAdminSendReportAfter'] = kps_min_max_default_range($postVars['kpsAdminSendReportAfter'], 10, 499, 50);

            // Werbuns/Spam escapen -> default ist 25
            $setReport['kpsReportSpam'] = kps_min_max_default_range($postVars['kpsReportSpam'], 25, 499, 50);

            // Auto-Sperre Werbuns/Spam escapen -> default ist Werbuns/Spam + 1
            $setReport['kpsAutoReportSpam'] = kps_min_max_default_range($postVars['kpsAutoReportSpam'], $setReport['kpsReportSpam'], 500, $setReport['kpsReportSpam'] + 1);

            // Unangemessen/Gewalt escapen -> default ist 25
            $setReport['kpsReportUnreasonable'] = kps_min_max_default_range($postVars['kpsReportUnreasonable'], 1, 499, 25);

            // Auto-Sperre Unangemessen/Gewalt escapen -> default ist Werbuns/Spam + 1
            $setReport['kpsAutoReportUnreasonable'] = kps_min_max_default_range($postVars['kpsAutoReportUnreasonable'], $setReport['kpsReportUnreasonable'], 500, $setReport['kpsReportUnreasonable'] + 1);

            // Doppelter Eintrag escapen -> default ist 25
            $setReport['kpsReportDouble'] = kps_min_max_default_range($postVars['kpsReportDouble'], 1, 499, 25);

            // Auto-Sperre Doppelter Eintrag escapen -> default ist Werbuns/Spam + 1
            $setReport['kpsAutoReportDouble'] = kps_min_max_default_range($postVars['kpsAutoReportDouble'], $setReport['kpsReportDouble'], 500, $setReport['kpsReportDouble'] + 1);

            // Personlichkeitsrecht escapen -> default ist 25
            $setReport['kpsReportPrivacy'] = kps_min_max_default_range($postVars['kpsReportPrivacy'], 1, 499, 25);

            // Auto-Sperre Personlichkeitsrecht escapen -> default ist Werbuns/Spam + 1
            $setReport['kpsAutoReportPrivacy'] = kps_min_max_default_range($postVars['kpsAutoReportPrivacy'], $setReport['kpsReportPrivacy'], 500, $setReport['kpsReportPrivacy'] + 1);

            // Sonstiges escapen -> default ist 25
            $setReport['kpsReportOthers'] = kps_min_max_default_range($postVars['kpsReportOthers'], 1, 499, 25);

            // Auto-Sperre Sonstiges escapen -> default ist Werbuns/Spam + 1
            $setReport['kpsAutoReportOthers'] = kps_min_max_default_range($postVars['kpsAutoReportOthers'], $setReport['kpsReportOthers'], 500, $setReport['kpsReportOthers'] + 1);

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
                    	<p><strong>' . esc_html(__('Saved', 'kps')) . ':&#160;' . esc_html(__('Reporting', 'kps')) . '</strong></p>
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
                    	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Error serializing the data', 'kps')) . '</strong></p>
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
                	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Error validating the data', 'kps')) . '</strong></p>
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
            	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Token invalid', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole Report Einstellungen
    $checked                    = kps_unserialize(get_option('kps_report', false));
    $checkedReportIsActivated   = ($checked['kpsReportActivation'] === 'true') ? 'checked' : '';

    echo '
            <div class="kps_container" style="width: 33%"><h5>
' . esc_html(__('Set the number of messages per entry until the administrator is informed.
The automatic lock kicks in as soon as the number is reached. The range to the notification is
between 1-499 messages. The range up to the automatic lock is between 2-500 messages.', 'kps')) . '.</h5>
                <form class="form" action="" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><label for="kpsAdminSendReportAfter">' . esc_html(__('Total Message-Reporting', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAdminSendReportAfter" id="kpsAdminSendReportAfter" class="form_num" value="' . $checked['kpsAdminSendReportAfter'] . '" min="10" max="499" /> ' . esc_html(_n('report', 'reports', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label for="kpsReportSpam">' . esc_html(__('Spam/Advertising', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsReportSpam" id="kpsReportSpam" class="form_num" value="' . $checked['kpsReportSpam'] . '" min="1" max="499" /> ' . esc_html(_n('report', 'reports', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsAutoReportSpam">' . esc_html(__('Automatic-Lock after', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAutoReportSpam" id="kpsAutoReportSpam" class="form_num" value="' . $checked['kpsAutoReportSpam'] . '" min="2" max="500" /> ' . esc_html(_n('report', 'reports', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label for="kpsReportUnreasonable">' . esc_html(__('Inappropriate/Violence', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsReportUnreasonable" id="kpsReportUnreasonable" class="form_num" value="' . $checked['kpsReportUnreasonable'] . '" min="1" max="499" /> ' . esc_html(_n('report', 'reports', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsAutoReportUnreasonable">' . esc_html(__('Automatic-Lock after', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAutoReportUnreasonable" id="kpsAutoReportUnreasonable" class="form_num" value="' . $checked['kpsAutoReportUnreasonable'] . '" min="2" max="500" /> ' . esc_html(_n('report', 'reports', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label for="kpsReportDouble">' . esc_html(__('Double entry', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsReportDouble" id="kpsReportDouble" class="form_num" value="' . $checked['kpsReportDouble'] . '" min="1" max="499" /> ' . esc_html(_n('report', 'reports', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsAutoReportDouble">' . esc_html(__('Automatic-Lock after', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAutoReportDouble" id="kpsAutoReportDouble" class="form_num" value="' . $checked['kpsAutoReportDouble'] . '" min="2" max="500" /> ' . esc_html(_n('report', 'reports', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label for="kpsReportPrivacy">' . esc_html(__('Personality rights', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsReportPrivacy" id="kpsReportPrivacy" class="form_num" value="' . $checked['kpsReportPrivacy'] . '" min="1" max="499" /> ' . esc_html(_n('report', 'reports', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsAutoReportPrivacy">' . esc_html(__('Automatic-Lock after', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAutoReportPrivacy" id="kpsAutoReportPrivacy" class="form_num" value="' . $checked['kpsAutoReportPrivacy'] . '" min="2" max="500" /> ' . esc_html(_n('report', 'reports', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label for="kpsReportOthers">' . esc_html(__('Others', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsReportOthers" id="kpsReportOthers" class="form_num" value="' . $checked['kpsReportOthers'] . '" min="1" max="499" /> ' . esc_html(_n('report', 'reports', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsAutoReportOthers">' . esc_html(__('Automatic-Lock after', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsAutoReportOthers" id="kpsAutoReportOthers" class="form_num" value="' . $checked['kpsAutoReportOthers'] . '" min="2" max="500" /> ' . esc_html(_n('report', 'reports', $countAction->isLockedBoth, 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hr"></td>
                            </tr>
                            <tr>
                                <td><label class="labelCheckbox" for="kpsReportActivation">' . esc_html(__('Automatic-Lock enable', 'kps')) . '</label></td>
                                <td><input type="checkbox" name="kpsReportActivation" id="kpsReportActivation" value="1" ' . $checkedReportIsActivated . ' /></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Reporting" />
                                    <input type="hidden" id="kpsReportToken" name="kpsReportToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitReport" value="' . esc_html(__('Save', 'kps')) . '" />
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
            'kpsFormOptionThreema',
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
                    	<p><strong>' . esc_html(__('Saved', 'kps')) . ':&#160;' . esc_html(__('Form options', 'kps')) . '</strong></p>
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
                    	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Error serializing the data', 'kps')) . '</strong></p>
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
                	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Error validating the data', 'kps')) . '</strong></p>
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
            	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Token invalid', 'kps')) . '</strong></p>
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
    $checkedFormOptionThreema               = ($checked['kpsFormOptionThreema'] === 'true') ? 'checked' : '';
    $checkedFormOptionWire                  = ($checked['kpsFormOptionWire'] === 'true') ? 'checked' : '';
    $checkedFormOptionHoccer                = ($checked['kpsFormOptionHoccer'] === 'true') ? 'checked' : '';
    $checkedFormOptionFacebookMessenger     = ($checked['kpsFormOptionFacebookMessenger'] === 'true') ? 'checked' : '';
    $checkedFormOptionSkype                 = ($checked['kpsFormOptionSkype'] === 'true') ? 'checked' : '';
    $checkedFormOptionWebsite               = ($checked['kpsFormOptionWebsite'] === 'true') ? 'checked' : '';
    $checkedFormOptionFacebook              = ($checked['kpsFormOptionFacebook'] === 'true') ? 'checked' : '';
    $checkedFormOptionInstagram             = ($checked['kpsFormOptionInstagram'] === 'true') ? 'checked' : '';

    echo '
            <div class="kps_container" style="width: 33%"><h5>' . esc_html(__('Setting the input options, which information the author, the requestor, makes available for contacting.', 'kps')) . '</h5>
                <form class="form" action="" method="post">
                    <table class="table" cellpadding="5" cellspacing="5">
                        <tbody>
                            <tr>
                                <td colspan="6"><u>' . esc_html(__('Direct Contact', 'kps')) . '</u></td>
                            </tr>
                            <tr>
                                <td width="25"><input type="checkbox" name="kpsFormOptionMobile" id="kpsFormOptionMobile" value="1" ' . $checkedFormOptionMobile . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionMobile">' . esc_html(__('Mobile Phone', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionTelephone" id="kpsFormOptionTelephone" value="1" ' . $checkedFormOptionTelephone . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionTelephone">' . esc_html(__('Telephone', 'kps')) . '</label></td>
                                <td width="25"></td>
                                <td width="33%"></td>
                            </tr>
                            <tr>
                                <td colspan="6"><hr></td>
                            </tr>
                            <tr>
                                <td colspan="6"><u>' . esc_html(__('Messenger-Services', 'kps')) . '</u></td>
                            </tr>
                            <tr>
                                <td width="25"><input type="checkbox" name="kpsFormOptionFacebookMessenger" id="kpsFormOptionFacebookMessenger" value="1" ' . $checkedFormOptionFacebookMessenger . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionFacebookMessenger">' . esc_html(__('Facebook-Messenger', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionSkype" id="kpsFormOptionSkype" value="1" ' . $checkedFormOptionSkype . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionSkype">' . esc_html(__('Skype', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionWhatsapp" id="kpsFormOptionWhatsapp" value="1" ' . $checkedFormOptionWhatsapp . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionWhatsapp">' . esc_html(__('Whatsapp', 'kps')) . '</label></td>
                            </tr>
                            <tr>
                                <td width="25"><input type="checkbox" name="kpsFormOptionHoccer" id="kpsFormOptionHoccer" value="1" ' . $checkedFormOptionHoccer . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionHoccer">' . esc_html(__('Hoccer', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionTelegram" id="kpsFormOptionTelegram" value="1" ' . $checkedFormOptionTelegram . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionTelegram">' . esc_html(__('Telegram', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionThreema" id="kpsFormOptionThreema" value="1" ' . $checkedFormOptionThreema . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionThreema">' . esc_html(__('Threema', 'kps')) . '</label></td>
                            </tr>
                            <tr>
                                <td width="25"><input type="checkbox" name="kpsFormOptionWire" id="kpsFormOptionWire" value="1" ' . $checkedFormOptionWire . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionWire">' . esc_html(__('Wire', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionSignal" id="kpsFormOptionSignal" value="1" ' . $checkedFormOptionSignal . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionSignal">' . esc_html(__('Signal', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionViper" id="kpsFormOptionViper" value="1" ' . $checkedFormOptionViper . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionViper">' . esc_html(__('Viper', 'kps')) . '</label></td>
                            </tr>
                            <tr>
                                <td colspan="6"><hr></td>
                            </tr>
                            <tr>
                                <td colspan="6"><u>' . esc_html(__('Web- / Profilpages', 'kps')) . '</u></td>
                            </tr>
                            <tr>
                                <td width="25"><input type="checkbox" name="kpsFormOptionFacebook" id="kpsFormOptionFacebook" value="1" ' . $checkedFormOptionFacebook . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionFacebook">' . esc_html(__('Facebook', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionInstagram" id="kpsFormOptionInstagram" value="1" ' . $checkedFormOptionInstagram . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionInstagram">' . esc_html(__('Instagram', 'kps')) . '</label></td>
                                <td width="25"><input type="checkbox" name="kpsFormOptionWebsite" id="kpsFormOptionWebsite" value="1" ' . $checkedFormOptionWebsite . ' /></td>
                                <td width="33%"><label class="labelCheckbox" for="kpsFormOptionWebsite">' . esc_html(__('Website', 'kps')) . '</label></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="6" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Optionfields" />
                                    <input type="hidden" id="kpsFormOptionToken" name="kpsFormOptionToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitFormOptions" value="' . esc_html(__('Save', 'kps')) . '" />
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
                $error[] = esc_html(__('Error validating the data', 'kps'));
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
                    	<p><strong>' . esc_html(__('Saved', 'kps')) . ':&#160;' . esc_html(__('Usersettings', 'kps')) . '</strong></p>
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
                    	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Error validating the data', 'kps')) . '</strong></p>
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
                    	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . $error[$key] . '</strong></p>
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
            	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Token invalid', 'kps')) . '</strong></p>
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
        $inputShowAvatarDisabledInfo = '<font color="red">(' . esc_html(__('Disabled in Wordpress', 'kps')) . ')</font>';
    }

    // Prüfe, ob User sich registieren dürfen
    if (get_option('users_can_register') !== '1')
    {
        $inputDisabled = 'disabled="disabled"';
        $inputDisabledInfo = '<font color="red">(' . esc_html(__('Disabled in Wordpress', 'kps')) . ')</font>';
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
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">(' . esc_html(__('GTC in draft status', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_agb') > '0' && get_post_status(get_option('kps_agb')) == 'private')
            {
                // im Privat-Status
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">(' . esc_html(__('GTC in privat status', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_agb') > '0' && get_post_status(get_option('kps_agb')) == 'pending')
            {
                // im Muster-Status
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">(' . esc_html(__('GTC in pending status', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_agb') > '0' && get_post_status(get_option('kps_agb')) == 'auto-draft')
            {
                // ohne Content
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">(' . esc_html(__('GTC without content', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_agb') > '0' && get_post_status(get_option('kps_agb')) == 'inherit')
            {
                // Revision
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">(' . esc_html(__('GTC in revision', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_agb') > '0' && get_post_status(get_option('kps_agb')) == 'trash')
            {
                // Revision
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">(' . esc_html(__('GTC in trash', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_agb') > '0' && post_password_required(get_option('kps_agb')) === true)
            {
                // hat Passwort
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">(' . esc_html(__('GTC with password', 'kps')) . ')</font>';
            }
            else
            {
                $inputUserPrivacyAGBDisabledInfo = '<font color="red">(' . esc_html(__('GTC not seclected', 'kps')) . ')</font>';
            }
        }
        else
        {
            // Post-ID existiert nicht
            $inputUserPrivacyAGBDisabledInfo = '<font color="red">(' . esc_html(__('GTC not seclected', 'kps')) . ')</font>';
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
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">(' . esc_html(__('GDPR in draft status', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && get_post_status(get_option('kps_dsgvo')) == 'private')
            {
                // im Privat-Status
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">(' . esc_html(__('GDPR in private status', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && get_post_status(get_option('kps_dsgvo')) == 'pending')
            {
                // im Muster-Status
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">(' . esc_html(__('GDPR in the sample status', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && get_post_status(get_option('kps_dsgvo')) == 'auto-draft')
            {
                // ohne Content
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">(' . esc_html(__('GDPR without content', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && get_post_status(get_option('kps_dsgvo')) == 'inherit')
            {
                // Revision
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">(' . esc_html(__('GDPR in revision', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && get_post_status(get_option('kps_dsgvo')) == 'trash')
            {
                // Revision
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">(' . esc_html(__('GDPR in trash', 'kps')) . ')</font>';
            }
            elseif (get_option('kps_dsgvo') > '0' && post_password_required(get_option('kps_dsgvo')) === true)
            {
                // hat Passwort
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">(' . esc_html(__('GDPR with password', 'kps')) . ')</font>';
            }
            else
            {
                $inputUserPrivacyDSGVODisabledInfo = '<font color="red">(' . esc_html(__('GDPR not set', 'kps')) . ')</font>';
            }
        }
        else
        {
            // Post-ID existiert nicht
            $inputUserPrivacyDSGVODisabledInfo = '<font color="red">(' . esc_html(__('GDPR not set', 'kps')) . ')</font>';
        }
    }

    echo '
            <div class="kps_container" style="width: 33%"><h5>' . esc_html(__('Settings for the form and the output itself.', 'kps')) . '</h5>
                <form class="form" action="" method="post">
                    <table class="table" cellpadding="5" cellspacing="5">
                        <tbody>
                            <tr>
                                <td><input id="kpsUserRequireRegistration" type="checkbox" name="kpsUserRequireRegistration" value="1" ' . $inputDisabled . ' ' . $checkedUserRequireRegistration . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserRequireRegistration">' . esc_html(__('Author must be registration', 'kps')) . '</label> ' . $inputDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserRequirementRegistration" type="checkbox"  name="kpsUserRequirementRegistration" value="1" ' . $inputDisabled . ' ' . $checkedUserRequirementRegistration . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserRequirementRegistration">' . esc_html(__('Requester must register', 'kps')) . '</label> ' . $inputDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserRequirementReport" type="checkbox" name="kpsUserRequirementReport" value="1" ' . $inputDisabled . ' ' . $checkedUserRequirementReport . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserRequirementReport">' . esc_html(__('Register to report entries', 'kps')) . '</label> ' . $inputDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserRequireAdminUnlock" type="checkbox" name="kpsUserRequireAdminUnlock" value="1" ' . $checkedUserRequireAdminUnlock . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserRequireAdminUnlock">' . esc_html(__('Unlock entries by admin', 'kps')) . '</label></td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserReport" type="checkbox" name="kpsUserReport" value="1"  ' . $inputDisabled . ' ' . $checkedUserReport . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserReport">' . esc_html(__('Can report entries', 'kps')) . '</label> ' . $inputDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserPrivacyAGB" type="checkbox" name="kpsUserPrivacyAGB" value="1" ' . $inputUserPrivacyAGBDisabled . ' ' . $checkedUserPrivacyAGB . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserPrivacyAGB">' . esc_html(__('Author must accept terms and conditions', 'kps')) . '</label> ' . $inputUserPrivacyAGBDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserPrivacyDSGVO" type="checkbox" name="kpsUserPrivacyDSGVO" value="1" ' . $inputUserPrivacyDSGVODisabled . ' ' . $checkedUserPrivacyDSGVO . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserPrivacyDSGVO">' . esc_html(__('Author must accept GDPR', 'kps')) . '</label> ' . $inputUserPrivacyDSGVODisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserProfilLink" type="checkbox" name="kpsUserProfilLink" value="1" ' . $checkedUserProfilLink . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserProfilLink">' . esc_html(__('Link to the user profile (register authors)', 'kps')) . '</label></td>
                            </tr>
                            <tr>
                                <td><input id="kpsUserAvatar" type="checkbox" name="kpsUserAvatar" value="1" ' . $inputShowAvatarDisabled . ' ' . $checkedUserAvatar . ' /></td>
                                <td><label class="labelCheckbox" for="kpsUserAvatar">' . esc_html(__('View author avatar (registered authors)', 'kps')) . '</label> ' . $inputShowAvatarDisabledInfo . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_UserSettings" />
                                    <input type="hidden" id="kpsUserSettingsToken" name="kpsUserSettingsToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitUserSettings" value="' . esc_html(__('Save', 'kps')) . '" />
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
            $setBackendPagination = kps_min_max_default_range(absint($postVars['kpsBackendPagination']), 1, 100, 10);

            // Seitenanzahl Frontend escapen -> default sind 5 Seiten
            $setFrontendPagination = kps_min_max_default_range(absint($postVars['kpsFrontendPagination']), 1, 100, 10);

            // Pagination aktualisieren
            if (is_numeric($setBackendPagination)
                && is_numeric($setFrontendPagination))
            {
                // True --> Update DB
                update_option('kps_backendPagination', $setBackendPagination, 'yes');
                update_option('kps_frontendPagination', $setFrontendPagination, 'yes');
                echo '
                <div class="notice notice-success is-dismissible">
                	<p><strong>' . esc_html(__('Saved', 'kps')) . ':&#160;' . esc_html(__('Navigation', 'kps')) . '</strong></p>
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
                	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Error validating the data', 'kps')) . '</strong></p>
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
            	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Token invalid', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    echo '
            <div class="kps_container" style="width: 33%"><h5>
' . esc_html(__('Setting the number of entries per page in the administration area,
as well as in the output (frontend). The range can be set between 1-99 entries per page.', 'kps')) . '.</h5>
                <form class="form" action="" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><label for="kpsBackendPagination">' . esc_html(__('Adminpanel', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsBackendPagination" id="kpsBackendPagination" class="form_num" value="' . get_option('kps_backendPagination', false) . '" min="1" max="99" /> ' . esc_html(_n('entry per page', 'entries per page', get_option('kps_backendPagination', false) , 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td><label for="kpsFrontendPagination">' . esc_html(__('Output frontend', 'kps')) . '</label></td>
                                <td><input type="number" name="kpsFrontendPagination" id="kpsFrontendPagination" class="form_num" value="' . get_option('kps_frontendPagination', false) . '" min="1" max="99" /> ' . esc_html(_n('entry per page', 'entries per page', get_option('kps_frontendPagination', false) , 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Pagination" />
                                    <input type="hidden" id="kpsPaginationToken" name="kpsPaginationToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitPagination" value="' . esc_html(__('Save', 'kps')) . '" />
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
                	<p><strong>' .  esc_html(__('Warning', 'kps')) . ':&#160;' . esc_html(__('No spam protection possible', 'kps')) . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }

            if (!is_array($setCaptchaSettings))
            {
                $error[] = esc_html(__('Error validating the data', 'kps'));
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
                	<p><strong>' . esc_html(__('Saved', 'kps')) . ':&#160;' . esc_html(__('Spam-Protection', 'kps')) . '</strong></p>
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
                    	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Error validating the data', 'kps')) . '</strong></p>
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
                    	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . $error[$key] . '</strong></p>
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
            	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Token invalid', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole Captcha Einstellungen
    $checkedKeys                = kps_unserialize(get_option('kps_captchakeys', false));
    $checkedCaptchaActivated    = (get_option('kps_captcha', false) === 'true') ? 'checked' : '';

    echo '
            <div class="kps_container" style="width: 33%"><h5>
' . esc_html(__('Enter the Google reCaptcha Keys here. The site key is used for the robot. The secret key will be used
for communication between your website and Google. Keep the key secret. You can sign in under:', 'kps')) .
                ' <a href="https://www.google.com/recaptcha/" target="_blank" rel="noopener">https://www.google.com/recaptcha/</a>.</h5>
                <form class="form" action="" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><label for="kpsCaptchaSiteKey">' . esc_html(__('Site-Key', 'kps')) . '</label></td>
                                <td><input type="text" name="kpsCaptchaSiteKey" id="kpsCaptchaSiteKey" autocomplete="off" class="form_field" value="' . esc_attr($checkedKeys['kpsCaptchaSiteKey']) . '" /></td>
                            </tr>
                            <tr>
                                <td><label for="kpsCaptchaSecretKey">' . esc_html(__('Secret-Key', 'kps')) . '</label></td>
                                <td><input type="text" name="kpsCaptchaSecretKey" id="kpsCaptchaSecretKey" autocomplete="off" class="form_field" value="' . esc_attr($checkedKeys['kpsCaptchaSecretKey']) . '" /></td>
                            </tr>
                            <tr>
                                <td><label class="labelCheckbox" for="kpsCaptchaActivated">' . esc_html(__('Google reCaptcha enable', 'kps')) . '</label></td>
                                <td><input type="checkbox" name="kpsCaptchaActivated" id="kpsCaptchaActivated" value="1" ' . $checkedCaptchaActivated . ' /></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Spam" />
                                    <input type="hidden" id="kpsCaptchaToken" name="kpsCaptchaToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitCaptcha" value="' . esc_html(__('Save', 'kps')) . '" />
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
            // Zeit darf nicht kleiner als 1 Tag sein -> default sind 90 Tage
            // Zeit darf nicht größer als 180 Tag sein -> default sind 60 Tage
            $setDeleteTimeEntry     = kps_min_max_default_range(absint($postVars['kpsDeleteTimeEntry']) * 24 * 60 * 60, 86400, 15552000, 7776000);

            // Wartende Einträge
            // Zeit darf nicht kleiner als 30 Tage sein -> default sind 60 Tage
            // Zeit darf nicht größer als 180 Tag sein -> default sind 60 Tage
            $setDeleteTimeNoEntry   = kps_min_max_default_range(absint($postVars['kpsDeleteTimeNoEntry']) * 24 * 60 * 60, 2592000, 15552000, 5184000);

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
                	<p><strong>' . esc_html(__('Saved', 'kps')) . ':&#160;' . esc_html(__('Default-Settings', 'kps')) . '</strong></p>
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
                	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Error validating the data', 'kps')) . '</strong></p>
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
            	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Token invalid', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    echo '
            <div class="kps_container" style="width: 33%"><h5>
' . esc_html(__('The deletion times specify when a "shared entry" or a "waiting entry" is deleted from the system (database).
The range for released entries is between 1-180 days. The range for waiting entries is between 30-180 days.
In the textarea the minimum number of words to be written by the author is determined.', 'kps')) . '</h5>
                <form class="form" action="" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td width="50%" style="text-align: right;"><label for="kpsDeleteTimeEntry">' . esc_html(__('Released entries', 'kps')) . '</label></td>
                                <td width="50%" style="text-align: left;"><input type="number" name="kpsDeleteTimeEntry" id="kpsDeleteTimeEntry" class="form_num" value="' . get_option('kps_deleteEntryTime', false) / 24 / 60 / 60 . '" min="1" max="180" aria-required="true" required="required" /> ' . esc_html(_n('day', 'days', $checkedDeleteTimeEntry , 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td width="50%" style="text-align: right;"><label for="kpsDeleteTimeNoEntry">' . esc_html(__('Waiting entries', 'kps')) . '</label></td>
                                <td width="50%" style="text-align: left;"><input type="number" name="kpsDeleteTimeNoEntry" id="kpsDeleteTimeNoEntry" class="form_num" value="' . get_option('kps_deleteNoEntryTime', false) / 24 / 60 / 60 . '" min="30" max="180" aria-required="true" required="required" /> ' . esc_html(_n('day', 'days', $checkedDeleteTimeNoEntry , 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td width="50%" style="text-align: right;"><label for="kpsFormWordCount">' . esc_html(__('Form textarea', 'kps')) . '</label></td>
                                <td width="50%" style="text-align: left;"><input type="number" name="kpsFormWordCount" id="kpsFormWordCount" class="form_num" value="' . get_option('kps_formWordCount', false) . '" min="1" /> ' . esc_html(_n('word', 'words', get_option('kps_formWordCount', false) , 'kps')) . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="kps-br"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <input type="hidden" id="kps_tab" name="kps_tab" value="kps_BasicSettings" />
                                    <input type="hidden" id="kpsBasicSettingsToken" name="kpsBasicSettingsToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitBasicSettings" value="' . esc_html(__('Save', 'kps')) . '" />
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

            // Haupt-Email-Adresse prüfen
            $setEmail                           = (is_email($setEmail) !== false) ? $setEmail : get_bloginfo('admin_email', 'raw');

            // Email-Kopie prüfen
            $setEmailCC['kpsEmailCC']           = (is_email($setEmailCC['kpsEmailCC']) !== false) ? $setEmailCC['kpsEmailCC'] : $setEmail;

            // Email Report prüfen
            $setEmailCC['kpsEmailReport']       = (is_email($setEmailCC['kpsEmailReport']) !== false) ? $setEmailCC['kpsEmailReport'] : $setEmail;

            // Checkbox escapen
            $setEmailCC['kpsEmailInformation']  = ($postVars['kpsEmailInformation'] === '1') ? 'true' : 'false';

            // Fehlermeldungen
            if ((is_email($setEmail) === false) OR (is_email($setEmailCC['kpsEmailCC']) !== false) OR (is_email($setEmailCC['kpsEmailReport']) !== false))
            {
                $error[] = esc_html(__('Email was set to default', 'kps'));
            }
            if (!is_array($setEmailCC))
            {
                $error[] = esc_html(__('Error validating the data', 'kps'));
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
                    update_option('kps_mailFrom', $setEmail, 'yes');
                    update_option('kps_mailFromCC', $setEmailCC, 'yes');
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' . esc_html(__('Saved', 'kps')) . ':&#160;' . esc_html(__('Email notification', 'kps')) . '</strong></p>
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
                    	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Error serializing the data', 'kps')) . '</strong></p>
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
                    	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . $error[$key] . '</strong></p>
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
            	<p><strong>' . esc_html(__('Error!', 'kps')) . ':&#160;' . esc_html(__('Token invalid', 'kps')) . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole EmailCC Einstellungen
    $checked = kps_unserialize(get_option('kps_mailFromCC', false));
    $checkedEmailInformation = ($checked['kpsEmailInformation'] === 'true') ? 'checked' : '';

    echo '
            <div class="kps_container" style="width: 33%"><h5>
' . esc_html(__('The email addresses are an optional input. If no email address is entered,
the script uses the email address set in the board. Furthermore, this is used when the entries must be released  by the
Administrator. Additional information about the activities can be retrieved.', 'kps')) . '</h5>
                <form class="form" action="" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><label for="kpsMailFrom">' . esc_html(__('Email', 'kps')) . '</label></td>
                                <td><input type="email" name="kpsMailFrom" id="kpsMailFrom" autocomplete="off" class="form_field" value="' . esc_attr(get_option('kps_MailFrom', false)) . '" /></td>
                            </tr>
                            <tr>
                                <td><label for="kpsEmailCC">' . esc_html(__('Email copy', 'kps')) . '</label></td>
                                <td><input type="email" name="kpsEmailCC" id="kpsEmailCC" autocomplete="off" class="form_field" value="' . esc_attr($checked['kpsEmailCC']) . '"  /></td>
                            </tr>
                            <tr>
                                <td><label for="kpsEmailReport">' . esc_html(__('Report-Email', 'kps')) . '</label></td>
                                <td><input type="email" name="kpsEmailReport" id="kpsEmailReport" autocomplete="off" class="form_field" value="' . esc_attr($checked['kpsEmailReport']) . '"  /></td>
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
                                    <input class="button-primary" type="submit" name="submitEmail" value="' . esc_html(__('Save', 'kps')) . '" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        ';
}
