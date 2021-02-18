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
function kps_Privacy()
{
    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('manage_privacy_options'))
    {
        wp_die(esc_html__('Access denied!', 'kps'));
    }

    // Javascript einladen
    kps_admin_enqueue();

    $kps_tab = 'kps_AgbSetting'; // Start-Tab

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
            </div>

            <h2 class="nav-tab-wrapper kps_nav_tab_wrapper">
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_AgbSetting') { echo "nav-tab-active";} ?>" rel="kps_AgbSetting">
                    <div style="text-align: center;"><?php  esc_html_e('Terms of Service', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_DsgvoSetting') { echo "nav-tab-active";} ?>" rel="kps_DsgvoSetting">
                    <div style="text-align: center;"><?php  esc_html_e('General Data Protection Regulation', 'kps'); ?></div>
                </a>
    		</h2>

            <form name="kps_options" class="kps_options kps_AgbSetting <?php if ($kps_tab == 'kps_AgbSetting') { echo "active";} ?>" method="post" action="">
                <?php kps_AgbSetting(); ?>
    		</form>

    		<form name="kps_options" class="kps_options kps_DsgvoSetting <?php if ($kps_tab == 'kps_DsgvoSetting') { echo "active";} ?>" method="post" action="">
    			<?php kps_DsgvoSetting(); ?>
    		</form>

            </div>
        </div>
    <?php
}

/**
 * Funktion AGB
 * Einstellung der AGB's
 */
function kps_AgbSetting()
{
    $verification   = false;
    $error          = '';

    // Token erstellen
    $token = wp_create_nonce('AGBToken');

    if (isset($_POST['submitAGB']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsAGB',
            'AGBToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['AGBToken'], 'AGBToken');

        // Verifizieren
        if ($verification == true)
        {
            // Select escapen
            $setAGBPage = (isset($postVars['kpsAGB'])
                            && !empty($postVars['kpsAGB'])
                            && is_numeric($postVars['kpsAGB'])
                            && is_int((int)$postVars['kpsAGB'])
                            && $postVars['kpsAGB'] > 0 ) ? absint($postVars['kpsAGB']) : 0;

            // AGB Einstellungen aktualisieren
            if (is_numeric($setAGBPage))
            {
                // True --> Update DB
                update_option('kps_agb', $setAGBPage, 'yes');
                echo '
                <div class="notice notice-success is-dismissible">
                	<p><strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Terms of Service', 'kps') . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
                if (empty($setAGBPage))
                {
                    echo '
                    <div class="notice notice-warning is-dismissible">
                    	<p><strong>' . esc_html__('Warning', 'kps') . ':&#160;' . esc_html__('No terms and conditions selected', 'kps') . '</strong></p>
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
            <div class="kps_container" style="width: 50%">
                <h5>
' . esc_html__('The General Terms and Conditions (GTC) are contractual clauses that standardize
and regulate mass contracts. They are unilaterally provided by a contracting party
and therefore require some control to prevent their misuse. We recommend this
to explain to the user (author) that the contact details, which the user (author), passes to the requester
(User) are left without the control, checking and verification of the site operator or the
Requester. Here you can assign your own terms and conditions in the form.', 'kps') . '
                </h5>
                <table class="table" cellpadding="2" cellspacing="2">
                    <tbody>
                        <tr>
                            <td style="text-align: center;">';
                            wp_dropdown_pages(
                        		array(
                        			'name'              => 'kpsAGB',
                                    'class'             => '',
                        			'show_option_none'  => '--- ' . esc_html__('Selection', 'kps') . ' ---',
                        			'option_none_value' => '0',
                        			'selected'          => get_option('kps_agb', false),
                        			'post_status'       => array( 'publish' ),
                        		)
                        	);
    echo '                  </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="kps-br"></td>
                        </tr>
                        <tr>
                            <td class="hr"></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_AgbSetting" />
                                <input type="hidden" id="AGBToken" name="AGBToken" value="' . $token . '" />
                                <input class="button-primary" type="submit" name="submitAGB" value="' . esc_html__('Save', 'kps') . '" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
}

/**
 * Funktion DSGVO
 * Einstellung der DSGVO
 */
function kps_DsgvoSetting()
{
    $verification   = false;
    $error          = '';

    // Token erstellen
    $token = wp_create_nonce('DSGVOToken');

    if (isset($_POST['submitDSGVO']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsDSGVO',
            'DSGVOToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['DSGVOToken'], 'DSGVOToken');

        // Verifizieren
        if ($verification == true && isset($_POST['submitDSGVO']))
        {
            // Select escapen
            $setDSGVOPage = (isset($postVars['kpsDSGVO'])
                                && !empty($postVars['kpsDSGVO'])
                                && is_numeric($postVars['kpsDSGVO'])
                                && is_int((int)$postVars['kpsDSGVO'])
                                && $postVars['kpsDSGVO'] > 0 ) ? absint($postVars['kpsDSGVO']) : 0;

            // DSVGO Einstellungen aktualisieren
            if (is_numeric($setDSGVOPage))
            {
                // True --> Update DB
                update_option('kps_dsgvo', $setDSGVOPage, 'yes');
                echo '
                <div class="notice notice-success is-dismissible">
                	<p><strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('General Data Protection Regulation', 'kps') . '</strong></p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
                if (empty($setDSGVOPage))
                {
                    echo '
                    <div class="notice notice-warning is-dismissible">
                    	<p><strong>' . esc_html__('Warning', 'kps') . ':&#160;' . esc_html__('No General-Data-Protection-Regulation selected', 'kps') . '</strong></p>
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
            <div class="kps_container" style="width: 50%"><h5>
' . esc_html__('GPDR is the common abbreviation for the General Data Protection Regulation.
With this the EU (European Union) wants create a single legal framework for the processing and
storage of personal data. In this plugin, Personal data can be processed, stored and retrieved
without checking and verification. Here you can assign your own GPDR to the form.', 'kps') .
                '&#160;(<a href="https://de.wikipedia.org/wiki/Datenschutz-Grundverordnung" target="_blank">' . esc_html__('GDPR', 'kps') . '</a>)
                </h5>
                <table class="table" cellpadding="2" cellspacing="2">
                    <tbody>
                        <tr>
                            <td style="text-align: center;">';
                            wp_dropdown_pages(
                        		array(
                        			'name'              => 'kpsDSGVO',
                                    'class'             => '',
                        			'show_option_none'  => '--- ' . esc_html__('Selection', 'kps') . ' ---',
                        			'option_none_value' => '0',
                        			'selected'          => get_option('kps_dsgvo', false),
                        			'post_status'       => array( 'publish' ),
                        		)
                        	);
    echo '                  </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="kps-br"></td>
                        </tr>
                        <tr>
                            <td class="hr"></td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_DsgvoSetting" />
                                <input type="hidden" id="DSGVOToken" name="DSGVOToken" value="' . $token . '" />
                                <input class="button-primary" type="submit" name="submitDSGVO" value="' . esc_html__('Save', 'kps') . '!" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        ';
}