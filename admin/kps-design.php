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
function kps_DesignSettings()
{
    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('manage_options'))
    {
        die(esc_html__('Access denied!', 'kps'));
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
    		</h2>

            <form name="kps_options" class="kps_options kps_Shortcodes <?php if ($kps_tab == 'kps_Shortcodes') { echo "active";} ?>" method="post" action="">
                <?php kps_Shortcodes(); ?>
    		</form>

            <form name="kps_options" class="kps_options kps_Icons <?php if ($kps_tab == 'kps_Icons') { echo "active";} ?>" method="post" action="">
                <?php
                    kps_Icons();
                ?>
    		</form>

            <form name="kps_options" class="kps_options kps_Output <?php if ($kps_tab == 'kps_Output') { echo "active";} ?>" method="post" action="">
                <?php kps_Output(); ?>
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
                    	<p><strong>' . esc_html__('Error!', 'kps') . ':&#160;' . $error[$key] . '</strong></p>
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
            	<p><strong>' . esc_html__('Error!', 'kps') . ':&#160;' . esc_html__('Token invalid', 'kps') . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole Icon-Pak Einstellungen
    $checkedIcons = get_option('kps_icon', false);

    echo '
            <div class="kps-divTable kps_container" style="width: 60%;">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 100%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table_list">
                                    <thead>
                                        <tr>
                                            <th class="th_list_top" scope="col"></th>
                                            <th class="th_list_top" scope="col"></th>
                                            <th class="th_list_top" scope="col">' . esc_html__('Hall', 'kps') . '</th>
                                            <th class="th_list_top" scope="col">' . esc_html__('Climbing', 'kps') . '</th>
                                            <th class="th_list_top" scope="col">' . esc_html__('Walking/Trekking', 'kps') . '</th>
                                            <th class="th_list_top" scope="col">' . esc_html__('Travels', 'kps') . '</th>
                                            <th class="th_list_top" scope="col">' . esc_html__('Unique', 'kps') . '</th>
                                            <th class="th_list_top" scope="col">' . esc_html__('Regularly', 'kps') . '</th>
                                            <th class="th_list_top" scope="col">' . esc_html__('Single person', 'kps') . '</th>
                                            <th class="th_list_top" scope="col">' . esc_html__('Family', 'kps') . '</th>
                                            <th class="th_list_top" scope="col">' . esc_html__('Club/Group', 'kps') . '</th>
                                        </tr>
                                    </thead>
                                	<tbody>
        ';

        $fc = 0;
        $colors = array( 1 => 'black', 2 => 'blue', 3 => 'red', 4 => 'green', 5 => 'yellow');

        foreach ($colors AS $key => $color)
        {
            for ($i = 55; $i > 30; $i -= 5)
            {
                $checked  = ($checkedIcons == $fc) ? 'checked' : '';

                echo '
                    <tr class="tr_list">
            			<td class="td_list" style="text-align: center;"><input id="kps_icon' . $i . '" name="kpsIconChoise" value="' . $fc . '" aria-required="true" required="required" type="radio" ' . $checked . '></td>
                        <td class="td_list kps-vert-text" style="text-align: center;"><b>' . $i . 'x' . $i . '</b></td>
                        <td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/hall.svg" height="' . $i . '" alt="' . esc_html__('Hall', 'kps') . '" title="' . esc_html__('Hall', 'kps') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/nature.svg" height="' . $i . '" alt="' . esc_html__('Climbing', 'kps') . '" title="' . esc_html__('Climbing', 'kps') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/trekking.svg" height="' . $i . '" alt="' . esc_html__('Walking/Trekking', 'kps') . '" title="' . esc_html__('Walking/Trekking', 'kps') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/travel.svg" height="' . $i . '" alt="' . esc_html__('Travels', 'kps') . '" title="' . esc_html__('Travels', 'kps') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/onetime.svg" height="' . $i . '" alt="' . esc_html__('Unique', 'kps') . '" title="' . esc_html__('Unique', 'kps') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/moretime.svg" height="' . $i . '" alt="' . esc_html__('Regularly', 'kps') . '" title="' . esc_html__('Regularly', 'kps') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/goalone.svg" height="' . $i . '" alt="' . esc_html__('Single person', 'kps') . '" title="' . esc_html__('Single person', 'kps') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/family.svg" height="' . $i . '" alt="' . esc_html__('Family', 'kps') . '" title="' . esc_html__('Family', 'kps') . '" /></td>
            			<td class="td_list" style="text-align: center;"><img src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $color . '/comeclub.svg" height="' . $i . '" alt="' . esc_html__('Club/Group', 'kps') . '" title="' . esc_html__('Club/Group', 'kps') . '" /></td>
            		</tr>
                ';

                $fc++;
            }
        }

    echo '
                                        <tr>
                                            <td colspan="11" class="hr"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="11" style="text-align: center">
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
                    	<p><strong>' . esc_html__('Error!', 'kps') . ':&#160;' . esc_html__('Error serializing the data', 'kps') . '</strong></p>
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
                    	<p><strong>' . esc_html__('Error!', 'kps') . ':&#160;' . $error[$key] . '</strong></p>
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
            	<p><strong>' . esc_html__('Error!', 'kps') . ':&#160;' . esc_html__('Token invalid', 'kps') . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Hole Output Einstellungen
    $checkedOutput              = kps_unserialize(get_option('kps_output', false));
    $checkedUnlockTime          = ($checkedOutput['kpsUnlockTime'] == 'true') ? 'checked' : '';
    $checkedEmailSetTime        = ($checkedOutput['kpsEmailSetTime'] == 'true') ? 'checked' : '';
    $checkedEmailUnlockTime     = ($checkedOutput['kpsEmailUnlockTime'] == 'true') ? 'checked' : '';
    $checkedEmailDeleteTime     = ($checkedOutput['kpsEmailDeleteTime'] == 'true') ? 'checked' : '';
    $checkedLegendActivated     = ($checkedOutput['kpsLegendActivated'] == 'true') ? 'checked' : '';

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 100%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table" style="border-spacing: 20px;">
                                	<tbody>
                                        <tr>
                                            <td><label class="labelCheckbox" for="kpsUnlockTime">' . esc_html__('Show time in entry', 'kps') . '</label></td>
                                            <td><input type="checkbox" name="kpsUnlockTime" id="kpsUnlockTime" value="1" ' . $checkedUnlockTime . ' /></td>
                                        </tr>
                                        <tr>
                                            <td><label class="labelCheckbox" for="kpsEmailSetTime">' . esc_html__('Show time in Activation-Email', 'kps') . '</label></td>
                                            <td><input type="checkbox" name="kpsEmailSetTime" id="kpsEmailSetTime" value="1" ' . $checkedEmailSetTime . ' /></td>
                                        </tr>
                                        <tr>
                                            <td><label class="labelCheckbox" for="kpsEmailUnlockTime">' . esc_html__('Show time in Unlock-Email', 'kps') . '</label></td>
                                            <td><input type="checkbox" name="kpsEmailUnlockTime" id="kpsEmailUnlockTime" value="1" ' . $checkedEmailUnlockTime . ' /></td>
                                        </tr>
                                        <tr>
                                            <td><label class="labelCheckbox" for="kpsEmailDeleteTime">' . esc_html__('Show time in Delete-Email', 'kps') . '</label></td>
                                            <td><input type="checkbox" name="kpsEmailDeleteTime" id="kpsEmailDeleteTime" value="1" ' . $checkedEmailDeleteTime . ' /></td>
                                        </tr>
                                        <tr>
                                            <td><label class="labelCheckbox" for="kpsLegendActivated">' . esc_html__('Legend activate', 'kps') . '</label></td>
                                            <td><input type="checkbox" name="kpsLegendActivated" id="kpsLegendActivated" value="1" ' . $checkedLegendActivated . ' /></td>
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