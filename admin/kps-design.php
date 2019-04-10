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
        die(esc_html(__('Access denied!', 'kps')));
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
                    <?php echo esc_html(__('Climbing-Partner-Search', 'kps')); ?> - <?php echo esc_html(__('Overview', 'kps')); ?>
               </h3>

            <h2 class="nav-tab-wrapper kps_nav_tab_wrapper">

    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Shortcodes') { echo "nav-tab-active";} ?>" rel="kps_Shortcodes">
                    <div style="text-align: center;"><?php  esc_html_e('Shortcodes', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Icons') { echo "nav-tab-active";} ?>" rel="kps_Icons">
                    <div style="text-align: center;"><?php  esc_html_e('Icons', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Legend') { echo "nav-tab-active";} ?>" rel="kps_Legend">
                    <div style="text-align: center;"><?php  esc_html_e('Legend', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Output') { echo "nav-tab-active";} ?>" rel="kps_Output">
                    <div style="text-align: center;"><?php  esc_html_e('Output', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Widget') { echo "nav-tab-active";} ?>" rel="kps_Widget">
                    <div style="text-align: center;"><?php  esc_html_e('Widget', 'kps'); ?></div>
                </a>
    		</h2>

            <form name="kps_options" class="kps_options kps_Shortcodes <?php if ($kps_tab == 'kps_Shortcodes') { echo "active";} ?>" method="post" action="">
                <?php kps_Shortcodes(); ?>
    		</form>

            <form name="kps_options" class="kps_options kps_Icons <?php if ($kps_tab == 'kps_Icons') { echo "active";} ?>" method="post" action="">
                <?php kps_Icons(); ?>
    		</form>

            <form name="kps_options" class="kps_options kps_Legend <?php if ($kps_tab == 'kps_Legend') { echo "active";} ?>" method="post" action="">
                <?php kps_Legend(); ?>
    		</form>

            <form name="kps_options" class="kps_options kps_Output <?php if ($kps_tab == 'kps_Output') { echo "active";} ?>" method="post" action="">
                <?php kps_Output(); ?>
    		</form>

            <form name="kps_options" class="kps_options kps_Widget <?php if ($kps_tab == 'kps_Widget') { echo "active";} ?>" method="post" action="">
                <?php kps_Widget(); ?>
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
                switch ($postVars['kpsIconChoise'])
                {
                    case '0':
                        $setIcon = 0;
                    break;
                    case '1':
                        $setIcon = 1;
                    break;
                    case '2':
                        $setIcon = 2;
                    break;
                    case '3':
                        $setIcon = 3;
                    break;
                    case '4':
                        $setIcon = 4;
                    break;
                    case '5':
                        $setIcon = 5;
                    break;
                    case '6':
                        $setIcon = 6;
                    break;
                    case '7':
                        $setIcon = 7;
                    break;
                    case '8':
                        $setIcon = 8;
                    break;
                    case '9':
                        $setIcon = 9;
                    break;
                    default:
                        $setIcon = NULL;
                }
            }

            // Fehlermeldungen
            if (!isset($setIcon)
                OR !is_int($setIcon))
            {
                $error[] = esc_html(__('No icons selected', 'kps'));
            }

            // Icons aktualisieren
            if (isset($setIcon)
                && is_int($setIcon))
            {
                update_option('kps_icon', $setIcon);
                echo '
                <div class="notice notice-success is-dismissible">
                	<p><strong>' . esc_html(__('Saved', 'kps')) . ':&#160;' . esc_html(__('Icons', 'kps')) . '</strong></p>
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

    // Hole Icon-Pak Einstellungen
    $checkedIcons       = get_option('kps_icon', false);
    $checkedIcon45      = ($checkedIcons == 0) ? 'checked' : '';
    $checkedIcon40      = ($checkedIcons == 1) ? 'checked' : '';
    $checkedIcon35      = ($checkedIcons == 2) ? 'checked' : '';
    $checkedIcon30      = ($checkedIcons == 3) ? 'checked' : '';
    $checkedIcon25      = ($checkedIcons == 4) ? 'checked' : '';
    $checkedIcon45_t    = ($checkedIcons == 5) ? 'checked' : '';
    $checkedIcon40_t    = ($checkedIcons == 6) ? 'checked' : '';
    $checkedIcon35_t    = ($checkedIcons == 7) ? 'checked' : '';
    $checkedIcon30_t    = ($checkedIcons == 8) ? 'checked' : '';
    $checkedIcon25_t    = ($checkedIcons == 9) ? 'checked' : '';
    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 100%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table" style="border-spacing: 20px;">
                                	<tbody>
                                        <tr>
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('No transparency', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                			<td></td>
                                            <td></td>
                                            <td><b>' . esc_html(__('Hall', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Climbing', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Walking/Trekking', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Travels', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Unique', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Regularly', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Single person', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Family', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Club/Group', 'kps')) . '</b></td>
                                		</tr>
                                		<tr>
                                			<td><input id="kps_icon45" name="kpsIconChoise" value="0" aria-required="true" required="required" type="radio" ' . $checkedIcon45 . '><label for="kps_icon45"></label></td>
                                            <td class="kps-vert-text"><b>45x45</b></td>
                                            <td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_45.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_45.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_45.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_45.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_45.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_45.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_45.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_45.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_45.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon40" name="kpsIconChoise" value="1" aria-required="true" required="required" type="radio" ' . $checkedIcon40 . '><label for="kps_icon40"></label></td>
                                            <td class="kps-vert-text"><b>40x40</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_40.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_40.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_40.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_40.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_40.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_40.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_40.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_40.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_40.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon35" name="kpsIconChoise" value="2" aria-required="true" required="required" type="radio" ' . $checkedIcon35 . '><label for="kps_icon35"></label></td>
                                            <td class="kps-vert-text"><b>35x35</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_35.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_35.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_35.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_35.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_35.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_35.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_35.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_35.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_35.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon30" name="kpsIconChoise" value="3" aria-required="true" required="required" type="radio" ' . $checkedIcon30 . '><label for="kps_icon30"></label></td>
                                            <td class="kps-vert-text"><b>30x30</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_30.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_30.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_30.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_30.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_30.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_30.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_30.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_30.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_30.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon25" name="kpsIconChoise" value="4" aria-required="true" required="required" type="radio" ' . $checkedIcon25 . '><label for="kps_icon25"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                        <tr>
                                            <td colspan="11" class="hr"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('Transparency', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                			<td><input id="kps_icon45_t" name="kpsIconChoise" value="5" aria-required="true" required="required" type="radio" ' . $checkedIcon45_t . '><label for="kps_icon45_t"></label></td>
                                            <td class="kps-vert-text"><b>45x45</b></td>
                                            <td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_45_t.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_45_t.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_45_t.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_45_t.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_45_t.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_45_t.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_45_t.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_45_t.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_45_t.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon40_t" name="kpsIconChoise" value="6" aria-required="true" required="required" type="radio" ' . $checkedIcon40_t . '><label for="kps_icon40_t"></label></td>
                                            <td class="kps-vert-text"><b>40x40</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_40_t.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_40_t.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_40_t.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_40_t.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_40_t.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_40_t.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_40_t.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_40_t.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_40_t.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon35_t" name="kpsIconChoise" value="7" aria-required="true" required="required" type="radio" ' . $checkedIcon35_t . '><label for="kps_icon35_t"></label></td>
                                            <td class="kps-vert-text"><b>35x35</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_35_t.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_35_t.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_35_t.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_35_t.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_35_t.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_35_t.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_35_t.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_35_t.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_35_t.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon30_t" name="kpsIconChoise" value="8" aria-required="true" required="required" type="radio" ' . $checkedIcon30_t . '><label for="kps_icon30_t"></label></td>
                                            <td class="kps-vert-text"><b>30x30</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_30_t.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_30_t.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_30_t.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_30_t.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_30_t.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_30_t.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_30_t.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_30_t.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_30_t.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon25_t" name="kpsIconChoise" value="9" aria-required="true" required="required" type="radio" ' . $checkedIcon25_t . '><label for="kps_icon25_t"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25_t.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25_t.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25_t.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25_t.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25_t.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25_t.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25_t.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25_t.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25_t.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td colspan="11" style="text-align: center">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Icons" />
                                                <input type="hidden" id="kpsIconToken" name="kpsIconToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitIcon" value="' . esc_html(__('Save', 'kps')) . '">
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

            // Fehlermeldungen
            if (!is_array($setOutput))
            {
                $error[] = esc_html(__('Error validating the data', 'kps'));
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
                    	<p><strong>' . esc_html(__('Saved', 'kps')) . ':&#160;' .  esc_html(__('Output', 'kps')) . '</strong></p>
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

    // Hole Output Einstellungen
    $checkedOutput              = kps_unserialize(get_option('kps_output', false));
    $checkedUnlockTime          = ($checkedOutput['kpsUnlockTime'] == 'true') ? 'checked' : '';
    $checkedEmailSetTime        = ($checkedOutput['kpsEmailSetTime'] == 'true') ? 'checked' : '';
    $checkedEmailUnlockTime     = ($checkedOutput['kpsEmailUnlockTime'] == 'true') ? 'checked' : '';
    $checkedEmailDeleteTime     = ($checkedOutput['kpsEmailDeleteTime'] == 'true') ? 'checked' : '';

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 100%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table" style="border-spacing: 20px;">
                                	<tbody>
                                        <tr>
                                            <td><label class="labelCheckbox" for="kpsUnlockTime">' . esc_html(__('Show time in entry', 'kps')) . '</label></td>
                                            <td><input type="checkbox" name="kpsUnlockTime" id="kpsUnlockTime" value="1" ' . $checkedUnlockTime . ' /></td>
                                        </tr>
                                        <tr>
                                            <td><label class="labelCheckbox" for="kpsEmailSetTime">' . esc_html(__('Show time in Activation-Email', 'kps')) . '</label></td>
                                            <td><input type="checkbox" name="kpsEmailSetTime" id="kpsEmailSetTime" value="1" ' . $checkedEmailSetTime . ' /></td>
                                        </tr>
                                        <tr>
                                            <td><label class="labelCheckbox" for="kpsEmailUnlockTime">' . esc_html(__('Show time in Unlock-Email', 'kps')) . '</label></td>
                                            <td><input type="checkbox" name="kpsEmailUnlockTime" id="kpsEmailUnlockTime" value="1" ' . $checkedEmailUnlockTime . ' /></td>
                                        </tr>
                                        <tr>
                                            <td><label class="labelCheckbox" for="kpsEmailDeleteTime">' . esc_html(__('Show time in Delete-Email', 'kps')) . '</label></td>
                                            <td><input type="checkbox" name="kpsEmailDeleteTime" id="kpsEmailDeleteTime" value="1" ' . $checkedEmailDeleteTime . ' /></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="hr"></td>
                                        </tr>
                                		<tr>
                                            <td colspan="2" style="text-align: center">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Output" />
                                                <input type="hidden" id="kpsOutputToken" name="kpsOutputToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitOutput" value="' . esc_html(__('Save', 'kps')) . '">
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
 * Funktion Legende
 */
function kps_Legend()
{
    $verification   = false;
    $error          = array();

    // Token erstellen
    $token = wp_create_nonce('kpsLegendToken');

    if (isset($_POST['submitLegend']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsLegendIconPak',
            'kpsLegendActivated',
            'kpsLegendToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsLegendToken'], 'kpsLegendToken');

        // Verifizieren
        if ($verification == true)
        {
            // Option escapen
            if (is_numeric($postVars['kpsLegendIconPak']))
            {
                switch ($postVars['kpsLegendIconPak'])
                {
                    case '0':
                        $setLegend['kpsLegendIconPak'] = 0;
                    break;
                    case '1':
                        $setLegend['kpsLegendIconPak'] = 1;
                    break;
                    default:
                        $setLegend['kpsLegendIconPak'] = 0;
                }
            }

            // Checkbox escapen
            $setLegend['kpslegendActivated'] = ($postVars['kpsLegendActivated'] === '1') ? 'true' : 'false';

            // Fehlermeldungen
            if (!isset($setLegend['kpsLegendIconPak'])
                OR !is_int($setLegend['kpsLegendIconPak']))
            {
                $error[] = esc_html(__('No legend-icons selected', 'kps'));
            }
            if (!is_array($setLegend))
            {
                $error[] = esc_html(__('Error validating the data', 'kps'));
            }

            // Captcha aktualisieren
            if (is_array($setLegend)
                && !empty($setLegend))
            {
                // Serialisieren
                $setLegend = serialize($setLegend);

                // Serialieren True --> Update DB
                if (is_serialized($setLegend))
                {
                    update_option('kps_legend', $setLegend);
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' . esc_html(__('Saved', 'kps')) . ':&#160;' .  esc_html(__('Legend', 'kps')) . '</strong></p>
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

    // Hole Legenden Einstellungen
    $checkedLegend          = kps_unserialize(get_option('kps_legend', false));
    $checkedLegend25        = ($checkedLegend['kpsLegendIconPak'] == 0) ? 'checked' : '';
    $checkedLegend25_t      = ($checkedLegend['kpsLegendIconPak'] == 1) ? 'checked' : '';
    $checkedLegendActivated = ($checkedLegend['kpslegendActivated'] === 'true') ? 'checked' : '';

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 100%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table" style="border-spacing: 20px;">
                                	<tbody>
                                        <tr>
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('No transparency', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                			<td></td>
                                            <td></td>
                                            <td><b>' . esc_html(__('Hall', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Climbing', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Walking/Trekking', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Travels', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Unique', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Regularly', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Single person', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Family', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Club/Group', 'kps')) . '</b></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_legend25" name="kpsLegendIconPak" value="0" aria-required="true" required="required" type="radio" ' . $checkedLegend25 . '><label for="kps_legend25"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                        <tr>
                                            <td colspan="11" class="hr"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('Transparency', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                            <td><input id="kps_legend25_t" name="kpsLegendIconPak" value="1" aria-required="true" required="required" type="radio" ' . $checkedLegend25_t . '><label for="kps_legend25_t"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25_t.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25_t.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25_t.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25_t.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25_t.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25_t.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25_t.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25_t.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25_t.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                        <tr>
                                            <td colspan="11" class="hr"></td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="kpsLegendActivated" id="kpsLegendActivated" value="1" ' . $checkedLegendActivated . ' /></td>
                                            <td colspan="3"><label class="labelCheckbox" for="kpsLegendActivated">' . esc_html(__('Legend activate', 'kps')) . '</label></td>
                                            <td colspan="7"></td>
                                        </tr>
                                		<tr>
                                            <td colspan="11" style="text-align: center">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Legend" />
                                                <input type="hidden" id="kpsLegendToken" name="kpsLegendToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitLegend" value="' . esc_html(__('Save', 'kps')) . '">
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
 * Funktion Widget
 */
function kps_Widget()
{
    $verification   = false;
    $error          = array();

    // Token erstellen
    $token = wp_create_nonce('kpsWidgetToken');

    if (isset($_POST['submitWidget']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsWidgetIconPak',
            'kpsWidgetToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsWidgetToken'], 'kpsWidgetToken');

        // Verifizieren
        if ($verification == true)
        {
            // Option escapen
            if (is_numeric($postVars['kpsWidgetIconPak']))
            {
                switch ($postVars['kpsWidgetIconPak'])
                {
                    case '0':
                        $setWidget['kpsWidgetIconPak'] = 0;
                    break;
                    case '1':
                        $setWidget['kpsWidgetIconPak'] = 1;
                    break;
                    default:
                        $setWidget['kpsWidgetIconPak'] = 0;
                }
            }

            // Fehlermeldungen
            if (!isset($setWidget['kpsWidgetIconPak'])
                OR !is_int($setWidget['kpsWidgetIconPak']))
            {
                $error[] = esc_html(__('No widget-icons selected', 'kps'));
            }
            if (!is_array($setWidget))
            {
                $error[] = esc_html(__('Error validating the data', 'kps'));
            }

            // Captcha aktualisieren
            if (is_array($setWidget)
                && !empty($setWidget))
            {
                // Serialisieren
                $setWidget = serialize($setWidget);

                // Serialieren True --> Update DB
                if (is_serialized($setWidget))
                {
                    update_option('kps_widget', $setWidget);
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' . esc_html(__('Saved', 'kps')) . ':&#160;' . esc_html(__('Widget', 'kps')) . '</strong></p>
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

    // Hole Widget Einstellungen
    $checkedWidget          = kps_unserialize(get_option('kps_legend', false));
    $checkedWidget25        = ($checkedWidget['kpsWidgetIconPak'] == 0) ? 'checked' : '';
    $checkedWidget25_t      = ($checkedWidget['kpsWidgetIconPak'] == 1) ? 'checked' : '';

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 100%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table" style="border-spacing: 20px;">
                                	<tbody>
                                        <tr>
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('No transparency', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                			<td></td>
                                            <td></td>
                                            <td><b>' . esc_html(__('Hall', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Climbing', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Walking/Trekking', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Travels', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Unique', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Regularly', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Single person', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Family', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Club/Group', 'kps')) . '</b></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_widget25" name="kpsWidgetIconPak" value="0" aria-required="true" required="required" type="radio" ' . $checkedWidget25 . '><label for="kps_widget25"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                        <tr>
                                            <td colspan="11" class="hr"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('Transparency', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                            <td><input id="kps_widget25_t" name="kpsWidgetIconPak" value="1" aria-required="true" required="required" type="radio" ' . $checkedWidget25_t . '><label for="kps_widget25_t"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25_t.png" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25_t.png" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25_t.png" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25_t.png" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25_t.png" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25_t.png" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25_t.png" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25_t.png" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25_t.png" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" /></td>
                                		</tr>
                                        <tr>
                                            <td colspan="11" class="hr"></td>
                                        </tr>
                                		<tr>
                                            <td colspan="11" style="text-align: center">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Widget" />
                                                <input type="hidden" id="kpsWidgetToken" name="kpsWidgetToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitWidget" value="' . esc_html(__('Save', 'kps')) . '">
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
                                            <td><b>' . esc_html(__('Standard', 'kps')) . '</b></td>
                                            <td><input type="text" name="kps-shortcode" size="50" readonly="readonly" value="[kps-shortcode]" /></td>
                                            <td>' . esc_html(__('Standard-Edition', 'kps')) . '</td>
                                        </tr>
                                        <tr>
                                            <td><b>' . esc_html(__('Form', 'kps')) . '</b></td>
                                            <td><input type="text" name="kps-shortcode" size="50" readonly="readonly" value="[kps-shortcode show-form-only=“true“]" /></td>
                                            <td>' . esc_html(__('Only the form will be displayed without entries.', 'kps')) . '</td>
                                        </tr>
                                        <tr>
                                            <td><b>' . esc_html(__('Entries', 'kps')) . '</b></td>
                                            <td><input type="text" name="kps-shortcode" size="50" readonly="readonly" value="[kps-shortcode button-write=“false“]" /></td>
                                            <td>' . esc_html(__('Only the entries are displayed without form and form button.', 'kps')) . '</td>
                                        </tr>
                                        <tr>
                                            <td><b>' . esc_html(__('Button Name', 'kps')) . '</b></td>
                                            <td><input type="text" name="kps-shortcode" size="50" readonly="readonly" value="[kps-shortcode button-text=“' . esc_html(__('Write an entry', 'kps')) . '“]" /></td>
                                            <td>' . esc_html(__('Change button name in the form.', 'kps')) . '</td>
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