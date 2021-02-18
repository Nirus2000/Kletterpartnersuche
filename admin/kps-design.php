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
function kps_DesignSettings()
{
    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('manage_options'))
    {
        wp_die(esc_html__('Access denied!', 'kps'));
    }

    // Javascript einladen
    kps_admin_enqueue();

    $kps_tab = 'kps_Shortcodes'; // Start-Tab

    // Tab nach $_POST wieder aktiv setzen
    if (isset($_POST['kps_tab']))
    {
        $kps_tab = $_POST['kps_tab'];
    }
?>
      <div id="kps" class="wrap kps">
            <div>
                <h3>
                    <?php echo esc_html__('Climbing-Partner-Search', 'kps'); ?> - <?php echo esc_html__('Overview', 'kps'); ?>
               </h3>

            <h2 class="nav-tab-wrapper kps_nav_tab_wrapper">

    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Shortcodes') { echo "nav-tab-active";} ?>" rel="kps_Shortcodes">
                    <div style="text-align: center;"><?php  esc_html_e('Shortcodes', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Icons') { echo "nav-tab-active";} ?>" rel="kps_Icons">
                    <div style="text-align: center;"><?php  esc_html_e('Icons', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Output') { echo "nav-tab-active";} ?>" rel="kps_Output">
                    <div style="text-align: center;"><?php  esc_html_e('Output', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Output') { echo "nav-tab-active";} ?>" rel="kps_Translations">
                    <div style="text-align: center;"><?php  esc_html_e('Translations', 'kps'); ?></div>
                </a>
    		</h2>

            <form name="kps_options" class="kps_options kps_Shortcodes <?php if ($kps_tab == 'kps_Shortcodes') { echo "active";} ?>" method="post" action="">
                <?php
                    kps_Shortcodes();
                ?>
    		</form>

            <form name="kps_options" class="kps_options kps_Translations <?php if ($kps_tab == 'kps_Translations') { echo "active";} ?>" method="post" action="">
                <?php
                    kps_Translations();
                ?>
    		</form>

            <form name="kps_options" class="kps_options kps_Icons <?php if ($kps_tab == 'kps_Icons') { echo "active";} ?>" method="post" action="">
                <?php
                    kps_Icons();
                ?>
    		</form>

            <form name="kps_options" class="kps_options kps_Output <?php if ($kps_tab == 'kps_Output') { echo "active";} ?>" method="post" action="">
                <?php
                    kps_Output();
                ?>
    		</form>
            </div>
        </div>
    <?php
}

/**
 * Funktion Icons
 */
function kps_Icons()
{
    $verification   = false;
    $error          = array();

    // Token erstellen
    $token = wp_create_nonce('kpsIconToken');

    if (isset($_POST['submitIcon']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsIconChoise',
            'kpsIconToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsIconToken'], 'kpsIconToken');

        // Verifizieren
        if ($verification == true)
        {
            // Option escapen
            if (is_numeric($postVars['kpsIconChoise']))
            {
                $setIcon = abs(kps_min_max_default_range($postVars['kpsIconChoise'], 0, 24, 3));
            }

            // Fehlermeldungen
            if (!isset($setIcon)
                OR !is_int($setIcon))
            {
                $error[] = esc_html__('No icons selected', 'kps');
            }

            // Icons aktualisieren
            if (isset($setIcon)
                && is_int($setIcon))
            {
                update_option('kps_icon', $setIcon);
                echo '
                <div class="notice notice-success is-dismissible">
                	<p><strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Icons', 'kps') . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
            else
            {
                foreach ($error as $key => $errors)
                {
                    echo '
                    <div class="notice notice-error is-dismissible">
                    	<p><strong>' . esc_html__('Error!', 'kps') . '&#160;' . $error[$key] . '</strong></p>
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
            	<p><strong>' . esc_html__('Error!', 'kps') . '&#160;' . esc_html__('Token invalid', 'kps') . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole Icon-Pak Einstellungen
    $kpsIcons = get_option('kps_icon', false);

    echo '
            <div class="kps-divTable kps_container" style="width: 80%;">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 100%; vertical-align: top">
                            <form class="form" action="" method="post">
                                <table class="table_list">
                                    <thead>
                                        <tr>
                                            <th class="th_list_top" scope="col"></th>
                                            <th class="th_list_top" scope="col"></th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Hall') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Climbing') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Walking') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Alpine tours') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Kayak') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Ferratas') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Mountain bike') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Winter sports') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Travels') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Unique') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Regularly') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Single person') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Family') . '</th>
                                            <th class="th_list_top" scope="col">' . kps_getFormTranslation('Club/Group') . '</th>
                                        </tr>
                                    </thead>
                                	<tbody>
        ';

        $fc = 0;
        $colors = array( 1 => 'black', 2 => 'blue', 3 => 'orange', 4 => 'green', 5 => 'yellow');

        foreach ($colors AS $key => $color)
        {
            for ($i = 55; $i > 30; $i -= 5)
            {
                $checked  = ($kpsIcons == $fc) ? 'checked' : '';

                echo '
                    <tr class="tr_list">
            			<td class="td_list" style="text-align: center;"><input id="kps_icon' . $i . '" name="kpsIconChoise" value="' . $fc . '" aria-required="true" required="required" type="radio" ' . $checked . '></td>
                        <td class="td_list kps-vert-text" style="text-align: center;"><b>' . $i . 'x' . $i . '</b></td>
                        <td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/hall.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Hall') . '" title="' . kps_getFormTranslation('Hall') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/nature.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Climbing') . '" title="' . kps_getFormTranslation('Climbing') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/trekking.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Walking') . '" title="' . kps_getFormTranslation('Walking') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/alpine.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Alpine tours') . '" title="' . kps_getFormTranslation('Alpine tours') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/kayak.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Kayak') . '" title="' . kps_getFormTranslation('Kayak') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/ferratas.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Ferratas') . '" title="' . kps_getFormTranslation('Ferratas') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/mountainbike.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Mountain bike') . '" title="' . kps_getFormTranslation('Mountain bike') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/wintersports.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Winter sports') . '" title="' . kps_getFormTranslation('Winter sports') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/travel.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Travels') . '" title="' . kps_getFormTranslation('Travels') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/onetime.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Unique') . '" title="' . kps_getFormTranslation('Unique') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/moretime.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Regularly') . '" title="' . kps_getFormTranslation('Regularly') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/goalone.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Single person') . '" title="' . kps_getFormTranslation('Single person') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/family.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Family') . '" title="' . kps_getFormTranslation('Family') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/comeclub.svg" height="' . $i . '" alt="' . kps_getFormTranslation('Club/Group') . '" title="' . kps_getFormTranslation('Club/Group') . '" /></td>
            		</tr>
                ';

                $fc++;
            }
        }

    echo '
                                        <tr>
                                            <td colspan="16" class="kps-br"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="16" class="hr"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="16" style="text-align: center">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Icons" />
                                                <input type="hidden" id="kpsIconToken" name="kpsIconToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitIcon" value="' . esc_html__('Save', 'kps') . '">
                                            </td>
                                		</tr>
                                	</tbody>
                                </table>
                            </form>
                        </div>
            		</div>
            	</div>
            </div>
        ';
}

/**
 * Funktion Output
 */
function kps_Output()
{
    $verification   = false;
    $error          = array();

    // Token erstellen
    $token = wp_create_nonce('kpsOutputToken');

    if (isset($_POST['submitOutput']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsUnlockTime',
            'kpsEmailSetTime',
            'kpsEmailUnlockTime',
            'kpsEmailDeleteTime',
            'kpsLegendActivated',
            'kpsOutputToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsOutputToken'], 'kpsOutputToken');

        // Verifizieren
        if ($verification == true)
        {
            // Checkbox escapen
            $setOutput['kpsUnlockTime']         = ($postVars['kpsUnlockTime'] === '1') ? 'true' : 'false';
            $setOutput['kpsEmailSetTime']       = ($postVars['kpsEmailSetTime'] === '1') ? 'true' : 'false';
            $setOutput['kpsEmailUnlockTime']    = ($postVars['kpsEmailUnlockTime'] === '1') ? 'true' : 'false';
            $setOutput['kpsEmailDeleteTime']    = ($postVars['kpsEmailDeleteTime'] === '1') ? 'true' : 'false';
            $setOutput['kpsLegendActivated']    = ($postVars['kpsLegendActivated'] === '1') ? 'true' : 'false';

            // Fehlermeldungen
            if (!is_array($setOutput))
            {
                $error[] = esc_html__('Error validating the data', 'kps');
            }

            // Captcha aktualisieren
            if (is_array($setOutput)
                && !empty($setOutput))
            {
                // Serialisieren
                $setOutput = serialize($setOutput);

                // Serialieren True --> Update DB
                if (is_serialized($setOutput))
                {
                    update_option('kps_output', $setOutput);
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' . esc_html__('Saved', 'kps') . ':&#160;' .  esc_html__('Output', 'kps') . '</strong></p>
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
                    	<p><strong>' . esc_html__('Error!', 'kps') . '&#160;' . esc_html__('Error serializing the data', 'kps') . '</strong></p>
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
                    	<p><strong>' . esc_html__('Error!', 'kps') . '&#160;' . $error[$key] . '</strong></p>
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
            	<p><strong>' . esc_html__('Error!', 'kps') . '&#160;' . esc_html__('Token invalid', 'kps') . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole Output Einstellungen
    $kpsOutput              = kps_unserialize(get_option('kps_output', false));
    $kpsUnlockTime          = ($kpsOutput['kpsUnlockTime'] == 'true') ? 'checked' : '';
    $kpsEmailSetTime        = ($kpsOutput['kpsEmailSetTime'] == 'true') ? 'checked' : '';
    $kpsEmailUnlockTime     = ($kpsOutput['kpsEmailUnlockTime'] == 'true') ? 'checked' : '';
    $kpsEmailDeleteTime     = ($kpsOutput['kpsEmailDeleteTime'] == 'true') ? 'checked' : '';
    $kpsLegendActivated     = ($kpsOutput['kpsLegendActivated'] == 'true') ? 'checked' : '';

    echo '
            <div class="kps-divTable kps_container" style="width: 50%;">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 100%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table" cellpadding="2" cellspacing="2">
                                	<tbody>
                                        <tr>
                                            <td width="25"><input type="checkbox" name="kpsUnlockTime" id="kpsUnlockTime" value="1" ' . $kpsUnlockTime . ' /></td>
                                            <td><label class="labelCheckbox" for="kpsUnlockTime">' . esc_html__('Show time in entry', 'kps') . '</label></td>
                                        </tr>
                                        <tr>
                                            <td width="25"><input type="checkbox" name="kpsEmailSetTime" id="kpsEmailSetTime" value="1" ' . $kpsEmailSetTime . ' /></td>
                                            <td><label class="labelCheckbox" for="kpsEmailSetTime">' . esc_html__('Show time in Activation-Email', 'kps') . '</label></td>
                                        </tr>
                                        <tr>
                                            <td width="25"><input type="checkbox" name="kpsEmailUnlockTime" id="kpsEmailUnlockTime" value="1" ' . $kpsEmailUnlockTime . ' /></td>
                                            <td><label class="labelCheckbox" for="kpsEmailUnlockTime">' . esc_html__('Show time in Unlock-Email', 'kps') . '</label></td>
                                        </tr>
                                        <tr>
                                            <td width="25"><input type="checkbox" name="kpsEmailDeleteTime" id="kpsEmailDeleteTime" value="1" ' . $kpsEmailDeleteTime . ' /></td>
                                            <td><label class="labelCheckbox" for="kpsEmailDeleteTime">' . esc_html__('Show time in Delete-Email', 'kps') . '</label></td>
                                        </tr>
                                        <tr>
                                            <td width="25"><input type="checkbox" name="kpsLegendActivated" id="kpsLegendActivated" value="1" ' . $kpsLegendActivated . ' /></td>
                                            <td><label class="labelCheckbox" for="kpsLegendActivated">' . esc_html__('Legend activate', 'kps') . '</label></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="kps-br"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="hr"></td>
                                        </tr>
                                		<tr>
                                            <td colspan="2" style="text-align: center">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Output" />
                                                <input type="hidden" id="kpsOutputToken" name="kpsOutputToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitOutput" value="' . esc_html__('Save', 'kps') . '">
                                            </td>
                                		</tr>
                                	</tbody>
                                </table>
                            </form>
                        </div>
            		</div>
            	</div>
            </div>
        ';
}

/**
 * Funktion Shortcodes
 */
function kps_Shortcodes()
{
    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 100%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table" style="border-spacing: 20px;">
                                	<tbody>
                                        <tr>
                                            <td><b>' . esc_html__('Standard', 'kps') . '</b></td>
                                            <td><input type="text" name="kps-shortcode" size="50" readonly="readonly" value="[kps-shortcode]" /></td>
                                            <td>' . esc_html__('Standard-Edition', 'kps') . '</td>
                                        </tr>
                                        <tr>
                                            <td><b>' . esc_html__('Form', 'kps') . '</b></td>
                                            <td><input type="text" name="kps-shortcode" size="50" readonly="readonly" value="[kps-shortcode show-form-only=“true“]" /></td>
                                            <td>' . esc_html__('Only the form will be displayed without entries.', 'kps') . '</td>
                                        </tr>
                                        <tr>
                                            <td><b>' . esc_html__('Entries', 'kps') . '</b></td>
                                            <td><input type="text" name="kps-shortcode" size="50" readonly="readonly" value="[kps-shortcode button-write=“false“]" /></td>
                                            <td>' . esc_html__('Only the entries are displayed without form and form button.', 'kps') . '</td>
                                        </tr>
                                        <tr>
                                            <td><b>' . esc_html__('Button Name', 'kps') . '</b></td>
                                            <td><input type="text" name="kps-shortcode" size="50" readonly="readonly" value="[kps-shortcode button-text=“' . esc_html__('Write an entry', 'kps') . '“]" /></td>
                                            <td>' . esc_html__('Change button name in the form.', 'kps') . '</td>
                                        </tr>
                                	</tbody>
                                </table>
                            </form>
                        </div>
            		</div>
            	</div>
            </div>
        ';
}

/**
 * Funktion Übersetzung
 */
function kps_Translations()
{
    $verification   = false;
    $error          = array();

    // Token erstellen
    $token = wp_create_nonce('kpsTranslationsToken');

    if (isset($_POST['submitResetTranslations']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsTranslationsToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsTranslationsToken'], 'kpsTranslationsToken');

        // Statistik zurücksetzen
        if ($verification == true)
        {
            update_option('kps_translations', '');
            echo '
            <div class="notice notice-success is-dismissible">
            	<p><strong>' . esc_html__('Reset', 'kps') . ':&#160;' .  esc_html__('Translations', 'kps') . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    if (isset($_POST['submitTranslations']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsTranslationsHall',
            'kpsTranslationsClimbing',
            'kpsTranslationsWalking',
            'kpsTranslationsAlpineTours',
            'kpsTranslationsKayak',
            'kpsTranslationsFerratas',
            'kpsTranslationsMountainBike',
            'kpsTranslationsWinterSports',
            'kpsTranslationsTravels',
            'kpsTranslationsUnique',
            'kpsTranslationsRegularly',
            'kpsTranslationsGoalone',
            'kpsTranslationsFamily',
            'kpsTranslationsComeClub',
            'kpsTranslationsToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsTranslationsToken'], 'kpsTranslationsToken');

        // Verifizieren
        if ($verification == true)
        {
            $setTranslations['Hall']            = sanitize_text_field($postVars['kpsTranslationsHall']);
            $setTranslations['Climbing']        = sanitize_text_field($postVars['kpsTranslationsClimbing']);
            $setTranslations['Walking']         = sanitize_text_field($postVars['kpsTranslationsWalking']);
            $setTranslations['Alpine Tours']    = sanitize_text_field($postVars['kpsTranslationsAlpineTours']);
            $setTranslations['Kayak']           = sanitize_text_field($postVars['kpsTranslationsKayak']);
            $setTranslations['Ferratas']        = sanitize_text_field($postVars['kpsTranslationsFerratas']);
            $setTranslations['Mountain bike']   = sanitize_text_field($postVars['kpsTranslationsMountainBike']);
            $setTranslations['Winter Sports']   = sanitize_text_field($postVars['kpsTranslationsWinterSports']);
            $setTranslations['Travels']         = sanitize_text_field($postVars['kpsTranslationsTravels']);
            $setTranslations['Unique']          = sanitize_text_field($postVars['kpsTranslationsUnique']);
            $setTranslations['Regularly']       = sanitize_text_field($postVars['kpsTranslationsRegularly']);
            $setTranslations['Single person']   = sanitize_text_field($postVars['kpsTranslationsGoalone']);
            $setTranslations['Family']          = sanitize_text_field($postVars['kpsTranslationsFamily']);
            $setTranslations['Club/Group']      = sanitize_text_field($postVars['kpsTranslationsComeClub']);

            foreach( $setTranslations AS $key => $translationItem )
            {
                if (empty($translationItem))
                {
                    $error[] = $translationItem . '&#160;' . esc_html__('Content is missing', 'kps');
                }

                if (strlen($translationItem) < 5)
                {
                    $error[] = kps_getFormTranslation($key) . '&#160;' . esc_html__('is to short', 'kps');
                }
                if (strlen($translationItem) > 25)
                {
                    $error[] = kps_getFormTranslation($key) . '&#160;' . esc_html__('is to long', 'kps');
                }
            }

            // Fehlermeldungen
            if (!is_array($setTranslations))
            {
                $error[] = esc_html__('Error validating the data', 'kps');
            }
            // Captcha aktualisieren
            if (is_array($setTranslations)
                && !empty($setTranslations)
                && empty($error))
            {
                // Serialisieren
                $setTranslations = serialize($setTranslations);

                // Serialieren True --> Update DB
                if (is_serialized($setTranslations))
                {
                    update_option('kps_translations', $setTranslations);
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' . esc_html__('Saved', 'kps') . ':&#160;' .  esc_html__('Translations', 'kps') . '</strong></p>
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
                    	<p><strong>' . esc_html__('Error!', 'kps') . '&#160;' . esc_html__('Error serializing the data', 'kps') . '</strong></p>
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
                    	<p><strong>' . esc_html__('Error!', 'kps') . '&#160;' . $error[$key] . '</strong></p>
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
            	<p><strong>' . esc_html__('Error!', 'kps') . '&#160;' . esc_html__('Token invalid', 'kps') . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    echo '
            <div class="kps-divTable kps_container" style="width: 50%;">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 100%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table" cellpadding="2" cellspacing="2">
                                	<tbody>
                                        <tr>
                                            <td colspan="2"><u>' . esc_html__('Translations', 'kps') . '</u></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><i>' . esc_html__('I am looking for', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsHall">' . kps_getSingleIcon('kpsFormOptionHall')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsHall" id="kpsTranslationsHall" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Hall')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Hall', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsClimbing">' . kps_getSingleIcon('kpsFormOptionClimbing')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsClimbing" id="kpsTranslationsClimbing" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Climbing')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Climbing', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsWalking">' . kps_getSingleIcon('kpsFormOptionWalking')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsWalking" id="kpsTranslationsWalking" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Walking')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Walking', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsAlpineTours">' . kps_getSingleIcon('kpsFormOptionAlpineTours')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsAlpineTours" id="kpsTranslationsAlpineTours" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Alpine tours')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Alpine tours', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsKayak">' . kps_getSingleIcon('kpsFormOptionKayak')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsKayak" id="kpsTranslationsKayak" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Kayak')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Kayak', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsFerratas">' . kps_getSingleIcon('kpsFormOptionFerratas')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsFerratas" id="kpsTranslationsFerratas" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Ferratas')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Ferratas', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsMountainBike">' . kps_getSingleIcon('kpsFormOptionMountainBike')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsMountainBike" id="kpsTranslationsMountainBike" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Mountain bike')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Mountain bike', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsWinterSports">' . kps_getSingleIcon('kpsFormOptionWinterSports')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsWinterSports" id="kpsTranslationsWinterSports" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Winter sports')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Winter sports', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsTravels">' . kps_getSingleIcon('kpsFormOptionTravels')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsTravels" id="kpsTranslationsTravels" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Travels')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Travels', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="kps-br"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><i>' . esc_html__('Kind of search', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsUnique">' . kps_getSingleIcon('kpsFormOptionOneTime')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsUnique" id="kpsTranslationsUnique" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Unique')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Unique', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsRegularly">' . kps_getSingleIcon('kpsFormOptionMoreTime')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsRegularly" id="kpsTranslationsRegularly" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Regularly')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Regularly', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="kps-br"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><i>' . esc_html__('I am', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsGoalone">' . kps_getSingleIcon('kpsFormOptionSinglePerson')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsGoalone" id="kpsTranslationsGoalone" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Single person')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Single person', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsFamily">' . kps_getSingleIcon('kpsFormOptionFamily')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsFamily" id="kpsTranslationsFamily" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Family')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Family', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td width="40"><label for="kpsTranslationsComeClub">' . kps_getSingleIcon('kpsFormOptionClubGroup')[0] . '</label></td>
                                            <td><input type="text" name="kpsTranslationsComeClub" id="kpsTranslationsWalking" class="form_field" aria-required="true" required="required" value="' . esc_attr(kps_getFormTranslation('Club/Group')) . '" minlength="5" maxlength="25" />&#160;<i>' . esc_html__('Default', 'kps') . ':&#160;' . esc_html__('Club/Group', 'kps') . '</i></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="kps-br"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="hr"></td>
                                        </tr>
                                		<tr>
                                            <td colspan="2" style="text-align: center">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Translations" />
                                                <input type="hidden" id="kpsTranslationsToken" name="kpsTranslationsToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitTranslations" value="' . esc_html__('Save', 'kps') . '">
                                                <input class="button-primary" type="submit" name="submitResetTranslations" id="submitResetTranslations" value="' . esc_html__('Reset', 'kps') . '" />
                                            </td>
                                		</tr>
                                	</tbody>
                                </table>

                            </form>
                        </div>
            		</div>
            	</div>
            </div>
        ';
}