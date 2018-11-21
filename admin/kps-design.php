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
function kps_DesignSettings()
{
    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('manage_options'))
    {
        die(esc_html(__('Zugriff verweigert!', 'kps')));
    }

    // Javascript einladen
    kps_admin_enqueue();

    $kps_tab = 'kps_Icons'; // Start-Tab

    // Tab nach $_POST wieder aktiv setzen
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

            <h2 class="nav-tab-wrapper kps_nav_tab_wrapper">

    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Icons') { echo "nav-tab-active";} ?>" rel="kps_Icons">
                    <div style="text-align: center;"><?php  esc_html_e('Icons', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Legend') { echo "nav-tab-active";} ?>" rel="kps_Legend">
                    <div style="text-align: center;"><?php  esc_html_e('Legende', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_Widget') { echo "nav-tab-active";} ?>" rel="kps_Widget">
                    <div style="text-align: center;"><?php  esc_html_e('Widget', 'kps'); ?></div>
                </a>
    		</h2>

            <form name="kps_options" class="kps_options kps_Icons <?php if ($kps_tab == 'kps_Icons') { echo "active";} ?>" method="post" action="">
                <?php kps_Icons(); ?>
    		</form>

            <form name="kps_options" class="kps_options kps_Legend <?php if ($kps_tab == 'kps_Legend') { echo "active";} ?>" method="post" action="">
                <?php kps_Legend(); ?>
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
                $error[] = esc_html(__('Keine Icons ausgewählt', 'kps'));
            }

            // Icons aktualisieren
            if (isset($setIcon)
                && is_int($setIcon))
            {
                update_option('kps_icon', $setIcon);
                echo '
                <div class="notice notice-success is-dismissible">
                	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Icons', 'kps')) . '</strong></p>
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
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('Keine Transparenz', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                			<td></td>
                                            <td></td>
                                            <td><b>' . esc_html(__('Halle', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Klettern', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Wandern/Trekking', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Reisen', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Einmalig', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Regelmäßig', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Einzelperson', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Familie', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Club/Gruppe', 'kps')) . '</b></td>
                                		</tr>
                                		<tr>
                                			<td><input id="kps_icon45" name="kpsIconChoise" value="0" aria-required="true" required="required" type="radio" ' . $checkedIcon45 . '><label for="kps_icon45"></label></td>
                                            <td class="kps-vert-text"><b>45x45</b></td>
                                            <td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_45.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_45.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_45.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_45.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_45.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_45.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_45.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_45.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_45.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon40" name="kpsIconChoise" value="1" aria-required="true" required="required" type="radio" ' . $checkedIcon40 . '><label for="kps_icon40"></label></td>
                                            <td class="kps-vert-text"><b>40x40</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_40.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_40.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_40.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_40.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_40.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_40.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_40.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_40.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_40.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon35" name="kpsIconChoise" value="2" aria-required="true" required="required" type="radio" ' . $checkedIcon35 . '><label for="kps_icon35"></label></td>
                                            <td class="kps-vert-text"><b>35x35</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_35.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_35.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_35.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_35.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_35.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_35.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_35.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_35.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_35.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon30" name="kpsIconChoise" value="3" aria-required="true" required="required" type="radio" ' . $checkedIcon30 . '><label for="kps_icon30"></label></td>
                                            <td class="kps-vert-text"><b>30x30</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_30.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_30.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_30.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_30.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_30.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_30.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_30.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_30.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_30.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon25" name="kpsIconChoise" value="4" aria-required="true" required="required" type="radio" ' . $checkedIcon25 . '><label for="kps_icon25"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                        <tr>
                                            <td colspan="11" class="hr"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('Transparenz', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                			<td><input id="kps_icon45_t" name="kpsIconChoise" value="5" aria-required="true" required="required" type="radio" ' . $checkedIcon45_t . '><label for="kps_icon45_t"></label></td>
                                            <td class="kps-vert-text"><b>45x45</b></td>
                                            <td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_45_t.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_45_t.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_45_t.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_45_t.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_45_t.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_45_t.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_45_t.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_45_t.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_45_t.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon40_t" name="kpsIconChoise" value="6" aria-required="true" required="required" type="radio" ' . $checkedIcon40_t . '><label for="kps_icon40_t"></label></td>
                                            <td class="kps-vert-text"><b>40x40</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_40_t.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_40_t.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_40_t.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_40_t.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_40_t.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_40_t.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_40_t.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_40_t.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_40_t.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon35_t" name="kpsIconChoise" value="7" aria-required="true" required="required" type="radio" ' . $checkedIcon35_t . '><label for="kps_icon35_t"></label></td>
                                            <td class="kps-vert-text"><b>35x35</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_35_t.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_35_t.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_35_t.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_35_t.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_35_t.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_35_t.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_35_t.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_35_t.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_35_t.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon30_t" name="kpsIconChoise" value="8" aria-required="true" required="required" type="radio" ' . $checkedIcon30_t . '><label for="kps_icon30_t"></label></td>
                                            <td class="kps-vert-text"><b>30x30</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_30_t.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_30_t.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_30_t.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_30_t.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_30_t.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_30_t.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_30_t.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_30_t.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_30_t.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_icon25_t" name="kpsIconChoise" value="9" aria-required="true" required="required" type="radio" ' . $checkedIcon25_t . '><label for="kps_icon25_t"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25_t.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25_t.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25_t.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25_t.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25_t.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25_t.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25_t.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25_t.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25_t.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                		<tr>
                                            <td colspan="11" style="text-align: center">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Icons" />
                                                <input type="hidden" id="kpsIconToken" name="kpsIconToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitIcon" value="' . esc_html(__('Speichern', 'kps')) . '">
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
                $error[] = esc_html(__('Keine Legenden-Icons ausgewählt', 'kps'));
            }
            if (!is_array($setLegend))
            {
                $error[] = esc_html(__('Fehler bei der Validierung der Daten', 'kps'));
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
                    	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Legende', 'kps')) . '</strong></p>
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
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('Keine Transparenz', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                			<td></td>
                                            <td></td>
                                            <td><b>' . esc_html(__('Halle', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Klettern', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Wandern/Trekking', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Reisen', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Einmalig', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Regelmäßig', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Einzelperson', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Familie', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Club/Gruppe', 'kps')) . '</b></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_legend25" name="kpsLegendIconPak" value="0" aria-required="true" required="required" type="radio" ' . $checkedLegend25 . '><label for="kps_legend25"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                        <tr>
                                            <td colspan="11" class="hr"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('Transparenz', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                            <td><input id="kps_legend25_t" name="kpsLegendIconPak" value="1" aria-required="true" required="required" type="radio" ' . $checkedLegend25_t . '><label for="kps_legend25_t"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25_t.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25_t.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25_t.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25_t.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25_t.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25_t.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25_t.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25_t.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25_t.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                        <tr>
                                            <td colspan="11" class="hr"></td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="kpsLegendActivated" id="kpsLegendActivated" value="1" ' . $checkedLegendActivated . ' /></td>
                                            <td colspan="3"><label class="labelCheckbox" for="kpsLegendActivated">' . esc_html(__('Legende aktivieren', 'kps')) . '</label></td>
                                            <td colspan="7"></td>
                                        </tr>
                                		<tr>
                                            <td colspan="11" style="text-align: center">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Legend" />
                                                <input type="hidden" id="kpsLegendToken" name="kpsLegendToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitLegend" value="' . esc_html(__('Speichern', 'kps')) . '">
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
                $error[] = esc_html(__('Keine Widget-Icons ausgewählt', 'kps'));
            }
            if (!is_array($setWidget))
            {
                $error[] = esc_html(__('Fehler bei der Validierung der Daten', 'kps'));
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
                    	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Widget', 'kps')) . '</strong></p>
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
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('Keine Transparenz', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                			<td></td>
                                            <td></td>
                                            <td><b>' . esc_html(__('Halle', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Klettern', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Wandern/Trekking', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Reisen', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Einmalig', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Regelmäßig', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Einzelperson', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Familie', 'kps')) . '</b></td>
                                			<td><b>' . esc_html(__('Club/Gruppe', 'kps')) . '</b></td>
                                		</tr>
                                		<tr>
                                            <td><input id="kps_widget25" name="kpsWidgetIconPak" value="0" aria-required="true" required="required" type="radio" ' . $checkedWidget25 . '><label for="kps_widget25"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                        <tr>
                                            <td colspan="11" class="hr"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="11" style="text-align: center"><b>' . esc_html(__('Transparenz', 'kps')) . '</b></td>
                                        </tr>
                                		<tr>
                                            <td><input id="kps_widget25_t" name="kpsWidgetIconPak" value="1" aria-required="true" required="required" type="radio" ' . $checkedWidget25_t . '><label for="kps_widget25_t"></label></td>
                                            <td class="kps-vert-text"><b>25x25</b></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/hall_25_t.png" alt="' . esc_html(__('Halle', 'kps')) . '" title="' . esc_html(__('Halle', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/nature_25_t.png" alt="' . esc_html(__('Klettern', 'kps')) . '" title="' . esc_html(__('Klettern', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/trekking_25_t.png" alt="' . esc_html(__('Wandern/Trekking', 'kps')) . '" title="' . esc_html(__('Wandern/Trekking', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/travel_25_t.png" alt="' . esc_html(__('Reisen', 'kps')) . '" title="' . esc_html(__('Reisen', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/onetime_25_t.png" alt="' . esc_html(__('Einmalig', 'kps')) . '" title="' . esc_html(__('Einmalig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/moretime_25_t.png" alt="' . esc_html(__('Regelmäßig', 'kps')) . '" title="' . esc_html(__('Regelmäßig', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/goalone_25_t.png" alt="' . esc_html(__('Einzelperson', 'kps')) . '" title="' . esc_html(__('Einzelperson', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/family_25_t.png" alt="' . esc_html(__('Familie', 'kps')) . '" title="' . esc_html(__('Familie', 'kps')) . '" /></td>
                                			<td style="text-align: center"><img src="' . KPS_RELATIV . '/frontend/gfx/comeclub_25_t.png" alt="' . esc_html(__('Club/Gruppe', 'kps')) . '" title="' . esc_html(__('Club/Gruppe', 'kps')) . '" /></td>
                                		</tr>
                                        <tr>
                                            <td colspan="11" class="hr"></td>
                                        </tr>
                                		<tr>
                                            <td colspan="11" style="text-align: center">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_Widget" />
                                                <input type="hidden" id="kpsWidgetToken" name="kpsWidgetToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitWidget" value="' . esc_html(__('Speichern', 'kps')) . '">
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