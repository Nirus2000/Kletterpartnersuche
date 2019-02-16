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
function kps_agb()
{
    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('manage_privacy_options'))
    {
        die(esc_html(__('Access denied!', 'kps')));
    }

    kps_admin_enqueue();

    // Metabox erstellen
    add_meta_box('kps_agb_setting', esc_html(__('Terms of Service', 'kps')) , 'kps_agb_setting', 'kps_agb', 'left');
    add_meta_box('kps_dsgvo_setting', esc_html(__('General Data Protection Regulation', 'kps')) , 'kps_dsgvo_setting', 'kps_agb', 'right');

?>
      <div id="kps" class="wrap kps">
            <div>
                <h3>
                    <?php echo esc_html(__('Climbing-Partner-Search', 'kps')); ?> - <?php echo esc_html(__('Overview', 'kps')); ?>
               </h3>
            </div>
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="metabox-holder">
                    <div class="postbox-container"><?php do_meta_boxes('kps_agb', 'left', ''); ?></div>
                    <div class="postbox-container"><?php do_meta_boxes('kps_agb', 'right', ''); ?></div>
                </div>
            </div>
        </div>
    <?php
}

/**
 * Funktion AGB
 * Einstellung der AGB's
 */
function kps_agb_setting()
{
    $saved = false; // Iniziierung
    $verification = false; // Iniziierung

    // Token erstellen
    $token = wp_create_nonce('kpsFormAGBToken');

    if (isset($_POST['submitFormAGB']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsFormPageAGB',
            'kpsFormAGBToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsFormAGBToken'], 'kpsFormAGBToken');
    }

    // Verifizieren
    if ($verification == true && isset($_POST['submitFormAGB']))
    {
        // Select escapen
        $setAGBPage = (isset($postVars['kpsFormPageAGB'])
                        && !empty($postVars['kpsFormPageAGB'])
                        && is_numeric($postVars['kpsFormPageAGB'])
                        && is_int((int)$postVars['kpsFormPageAGB'])
                        && $postVars['kpsFormPageAGB'] > 0 ) ? absint($postVars['kpsFormPageAGB']) : 0;

        // AGB Einstellungen aktualisieren
        if (is_array($postVars) && !empty($postVars) && is_numeric($setAGBPage))
        {
            // True --> Update DB
            update_option('kps_agb', $setAGBPage, 'yes');
            $saved = esc_html(__('Saved', 'kps'));
        }
    }

    // Hole AGB Einstellungen
    $checkedAGBPage = get_option('kps_agb', false);

    // Keine AGB ausgewählt
    $errorAGBpage = ($checkedAGBPage) ? '' : 'form_glowing';

    echo '
            <div>
                <h5>
' . esc_html(__('The General Terms and Conditions (GTC) are contractual clauses that standardize
and concretisation of mass contracts. They are unilaterally provided by a contracting party
and therefore require some control to prevent their misuse. We recommend this
to explain to the user (author) that the contact data, which the user (author), the requester
(User) leaves without the control, verification and verification of the site operator or the
Requester happens. Here you can assign your terms and conditions in the form.', 'kps')) . '
                </h5>
                <form class="form" action="' . KPS_ADMIN_URL . '/agb.php" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td style="text-align: center;">';
                                wp_dropdown_pages(
                            		array(
                            			'name'              => 'kpsFormPageAGB',
                                        'class'             => $errorAGBpage,
                            			'show_option_none'  => '--- ' . esc_html(__('Selection', 'kps')) . ' ---',
                            			'option_none_value' => '0',
                            			'selected'          => $checkedAGBPage,
                            			'post_status'       => array( 'publish' ),
                            		)
                            	);
    echo '                      </td>
                            </tr>
                            <tr>
                                <td class="hr"></td>
                            </tr>
                            <tr>
                                <td class="save" style="text-align: center;">' . $saved . '</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <input type="hidden" id="kpsFormAGBToken" name="kpsFormAGBToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitFormAGB" value="' . esc_html(__('Save', 'kps')) . '" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        ';
}

/**
 * Funktion DSGVO
 * Einstellung der DSGVO
 */
function kps_dsgvo_setting()
{
    $saved = false; // Iniziierung
    $verification = false; // Iniziierung

    // Token erstellen
    $token = wp_create_nonce('kpsFormDSGVOToken');

    if (isset($_POST['submitFormDSGVO']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsFormPageDSGVO',
            'kpsFormDSGVOToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsFormDSGVOToken'], 'kpsFormDSGVOToken');
    }

    // Verifizieren
    if ($verification == true && isset($_POST['submitFormDSGVO']))
    {
        // Select escapen
        $setDSGVOPage = (isset($postVars['kpsFormPageDSGVO'])
                            && !empty($postVars['kpsFormPageDSGVO'])
                            && is_numeric($postVars['kpsFormPageDSGVO'])
                            && is_int((int)$postVars['kpsFormPageDSGVO'])
                            && $postVars['kpsFormPageDSGVO'] > 0 ) ? absint($postVars['kpsFormPageDSGVO']) : 0;

        // DSVGO Einstellungen aktualisieren
        if (is_array($postVars) && !empty($postVars) && is_numeric($setDSGVOPage))
        {
            // True --> Update DB
            update_option('kps_dsgvo', $setDSGVOPage, 'yes');
            $saved = esc_html(__('Saved', 'kps'));
        }
    }

    // Hole DSVGO Einstellungen
    $checkedDSGVOPage = get_option('kps_dsgvo', false);

    // Keine DSGVO ausgewählt
    $errorDSGVOpage = ($checkedDSGVOPage) ? '' : 'form_glowing';

    echo '
            <div><h5>
' . esc_html(__('GPDR is the common abbreviation for the General Data Protection Regulation. With this the EU (European Union) wants
create a single legal framework for the processing and storage of personal data. In this plugin, will be
Personal data are processed, stored and retrieved without verification and verification.
Here you can assign your GPDR to the form.', 'kps')) .
                '&#160;(<a href="https://de.wikipedia.org/wiki/Datenschutz-Grundverordnung" target="_blank">' . esc_html(__('GDPR', 'kps')) . '</a>)</h5>
                <form class="form" action="' . KPS_ADMIN_URL . '/agb.php" method="post">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td style="text-align: center;">';
                                wp_dropdown_pages(
                            		array(
                            			'name'              => 'kpsFormPageDSGVO',
                                        'class'             => $errorDSGVOpage,
                            			'show_option_none'  => '--- ' . esc_html(__('Selection', 'kps')) . ' ---',
                            			'option_none_value' => '0',
                            			'selected'          => $checkedDSGVOPage,
                            			'post_status'       => array( 'publish' ),
                            		)
                            	);
    echo '                      </td>
                            </tr>
                            <tr>
                                <td class="hr"></td>
                            </tr>
                            <tr>
                                <td  class="save" style="text-align: center;">' . $saved . '</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <input type="hidden" id="kpsDSGVOToken" name="kpsFormDSGVOToken" value="' . $token . '" />
                                    <input class="button-primary" type="submit" name="submitFormDSGVO" value="' . esc_html(__('Save', 'kps')) . '!" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        ';
}