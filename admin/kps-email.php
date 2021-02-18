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
function kps_EmailSetting()
{
    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('manage_options'))
    {
        wp_die(esc_html__('Access denied!', 'kps'));
    }

    // Javascript einladen
    kps_admin_enqueue();

    $kps_tab = 'kps_ActivationEmail'; // Start-Tab

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
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_ActivationEmail') { echo "nav-tab-active";} ?>" rel="kps_ActivationEmail">
                    <div style="text-align: center;"><?php  esc_html_e('Activation', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_UnlockEmail') { echo "nav-tab-active";} ?>" rel="kps_UnlockEmail">
                    <div style="text-align: center;"><?php  esc_html_e('Unlocked', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_VerificationEmail') { echo "nav-tab-active";} ?>" rel="kps_VerificationEmail">
                    <div style="text-align: center;"><?php  esc_html_e('Request contact information', 'kps'); ?></div>
                </a>
                <a href="" class="nav-tab <?php if ($kps_tab == 'kps_ContactDataEmail') { echo "nav-tab-active";} ?>" rel="kps_ContactDataEmail">
                    <div style="text-align: center;"><?php  esc_html_e('Contact details', 'kps'); ?></div>
                </a>
    		</h2>

            <form name="kps_options" class="kps_options kps_ActivationEmail <?php if ($kps_tab == 'kps_ActivationEmail') { echo "active";} ?>" method="post" action="">
                <?php kps_ActivationEmail(); ?>
    		</form>

    		<form name="kps_options" class="kps_options kps_UnlockEmail <?php if ($kps_tab == 'kps_UnlockEmail') { echo "active";} ?>" method="post" action="">
    			<?php kps_UnlockEmail(); ?>
    		</form>

    		<form name="kps_options" class="kps_options kps_VerificationEmail <?php if ($kps_tab == 'kps_VerificationEmail') { echo "active";} ?>" method="post" action="">
    			<?php kps_VerificationEmail(); ?>
    		</form>

    		<form name="kps_options" class="kps_options kps_ContactDataEmail <?php if ($kps_tab == 'kps_ContactDataEmail') { echo "active";} ?>" method="post" action="">
    			<?php kps_ContactDataEmail(); ?>
    		</form>

            </div>
        </div>
    <?php
}

/**
 * Funktion E-Mail Vorlage Freischaltung
 */
function kps_UnlockEmail()
{
    $verification   = false;
    $error          = array();

    // Token erstellen
    $token = wp_create_nonce('kpsUnlockToken');

    if (isset($_POST['submitReset']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsUnlockToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsUnlockToken'], 'kpsUnlockToken');

        // Verifizieren
        if ($verification == true)
        {
            delete_option('kps_adminUnlockMailSettings');

            echo '
            <div class="notice notice-success is-dismissible">
            	<p><strong>' . esc_html__('Reset', 'kps') . ':&#160;' . esc_html__('Unlocked', 'kps') . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    if (isset($_POST['submitUnlock']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsUnlockSubject',
            'kpsUnlockContent',
            'kpsUnlockToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsUnlockToken'], 'kpsUnlockToken');

        // Verifizieren
        if ($verification == true)
        {
            // Subject und Textarea escapen
            $setMailSettings['kpsUnlockSubject']    = kps_sanitize_field($postVars['kpsUnlockSubject']);
            $setMailSettings['kpsUnlockContent']    = kps_sanitize_textarea($postVars['kpsUnlockContent']);

            // Fehlermeldungen
            if (empty($setMailSettings['kpsUnlockSubject']))
            {
                $error[] = esc_html__('Email subject is missing', 'kps');
            }
            if (empty($setMailSettings['kpsUnlockContent']))
            {
                $error[] = esc_html__('Content is missing', 'kps');
            }
            if (strpos($setMailSettings['kpsUnlockContent'], '%linkactivation%') !== false )
            {
                $error[] = esc_html__('Shorttag %linkactivation% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsUnlockContent'], '%linkdelete%') !== false )
            {
                $error[] = esc_html__('Shorttag %linkdelete% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsUnlockContent'], '%erasepassword%') !== false )
            {
                $error[] = esc_html__('Shorttag %erasepassword% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsUnlockContent'], '%linkreg%') !== false )
            {
                $error[] = esc_html__('Shorttag %linkreg% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsUnlockContent'], '%regpassword%') !== false )
            {
                $error[] = esc_html__('Shorttag %regpassword% not allowed', 'kps');
            }
            if (!is_array($setMailSettings))
            {
                $error[] = esc_html__('Error validating the data', 'kps');
            }

            // Email-Content aktualisieren
            if (is_array($setMailSettings)
                && !empty($setMailSettings['kpsUnlockSubject'])
                && !empty($setMailSettings['kpsUnlockContent'])
                && strpos($setMailSettings['kpsUnlockContent'], '%linkactivation%') === false
                && strpos($setMailSettings['kpsUnlockContent'], '%linkdelete%') === false
                && strpos($setMailSettings['kpsUnlockContent'], '%erasepassword%') === false
                && strpos($setMailSettings['kpsUnlockContent'], '%linkreg%') === false
                && strpos($setMailSettings['kpsUnlockContent'], '%regpassword%') === false)
            {
                // Serialiseren
                $setMailSettings = serialize($setMailSettings);

                // Serialieren True --> Update DB
                if (is_serialized($setMailSettings))
                {
                    update_option('kps_adminUnlockMailSettings', $setMailSettings);
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Unlocked', 'kps') . '</strong></p>
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
    // Hole Email-Vorlagen Einstellungen
    $writeMailSettings  = kps_unserialize(get_option('kps_adminUnlockMailSettings', false));
    $writeMail          = kps_mailcontent_adminUnlock($writeMailSettings);

    // Zeit
    if (kps_unserialize(get_option( 'kps_output', false))['kpsEmailSetTime'] === 'true')
    {
        $exampleSetTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleSetTime = date_i18n(get_option('date_format'), time());
    }
    if (kps_unserialize(get_option('kps_output', false))['kpsEmailUnlockTime'] === 'true')
    {
        $exampleUnlockTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleUnlockTime = date_i18n(get_option('date_format'), time());
    }
    if (kps_unserialize(get_option('kps_output', false))['kpsEmailDeleteTime'] === 'true')
    {
        $exampleDeleteTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleDeleteTime = date_i18n(get_option('date_format'), time());
    }

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
        			<div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><label for="kpsUnlockSubject"><b>' . esc_html__('Email subject', 'kps') . '</b></label></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" name="kpsUnlockSubject" id="kpsUnlockSubject" autocomplete="off" class="form_field" value="' . $writeMail['Subject'] . '" /></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="kpsUnlockContent"></label>
                                            <textarea name="kpsUnlockContent" id="kpsUnlockContent" class="textarea" aria-required="true" required="required">' . esc_textarea($writeMail['Content']) . '</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td  style="text-align: center;">
                                            <input type="hidden" id="kps_tab" name="kps_tab" value="kps_UnlockEmail" />
                                            <input type="hidden" id="kpsUnlockToken" name="kpsUnlockToken" value="' . $token . '" />
                                            <input class="button-primary" type="submit" name="submitUnlock" value="' . esc_html__('Save', 'kps') . '" />
                                            <input class="button-primary" type="submit" name="submitReset" value="' . esc_html__('Reset', 'kps') . '">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html__('General Shorttags', 'kps') . '</b></td>
                                        <td><b>' . esc_html__('Result', 'kps') . '</b></td>
                                    </tr>
                                    <tr>
                                        <td>%blogname%</td>
                                        <td>' . get_bloginfo('name', 'raw') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%blogurl%</td>
                                        <td>' . get_bloginfo('url', 'raw') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%blogemail%</td>
                                        <td>' . get_option('kps_MailFrom', false) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authorname%</td>
                                        <td>' . esc_html__('Author Name', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authoremail%</td>
                                        <td>' . esc_html__('Author Email', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authorcontactdata%</td>
                                        <td>' . esc_html__('Author contact details', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%entrycontent%</td>
                                        <td>' . esc_html__('Entry', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authorcontactdata%</td>
                                        <td>' . esc_html__('Author contact details', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%setdate%</td>
                                        <td>' . $exampleSetTime . "&#160;(". esc_html__('Created', 'kps') . ')</td>
                                    </tr>
                                    <tr>
                                        <td>%erasedatetime%</td>
                                        <td>' . $exampleDeleteTime . "&#160;(". esc_html__('Delete Time', 'kps') . ')</td>
                                    </tr>
                                    <tr>
                                        <td>%unlockdatetime%</td>
                                        <td>' . $exampleUnlockTime . "&#160;(". esc_html__('Released', 'kps') . ')</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
            		</div>
            	</div>
            </div>
        ';
}

/**
 * Funktion E-Mail Vorlage Kontaktdaten
 */
function kps_ContactDataEmail()
{
    $verification   = false;
    $error          = array();

    // Token erstellen
    $token = wp_create_nonce('kpsContactDataToken');

    if (isset($_POST['submitReset']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsContactDataToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsContactDataToken'], 'kpsContactDataToken');

        // Verifizieren
        if ($verification == true)
        {
            delete_option('kps_userMailSettings');

            echo '
            <div class="notice notice-success is-dismissible">
            	<p><strong>' . esc_html__('Reset', 'kps') . ':&#160;' . esc_html__('Request contact information', 'kps') . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    if (isset($_POST['submitContactData']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsContactDataSubject',
            'kpsContactDataContent',
            'kpsContactDataToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsContactDataToken'], 'kpsContactDataToken');

        // Verifizieren
        if ($verification == true)
        {
            // Subject und Textarea escapen
            $setMailSettings['kpsContactDataSubject']   = kps_sanitize_field($postVars['kpsContactDataSubject']);
            $setMailSettings['kpsContactDataContent']   = kps_sanitize_textarea($postVars['kpsContactDataContent']);

            // Fehlermeldungen
            if (empty($setMailSettings['kpsContactDataSubject']))
            {
                $error[] = esc_html__('Email subject is missing', 'kps');
            }
            if (empty($setMailSettings['kpsContactDataContent']))
            {
                $error[] = esc_html__('Content is missing', 'kps');
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%authorname%') === false )
            {
                $error[] = esc_html__('Shorttag %authorname% missing', 'kps');
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%authoremail%') === false )
            {
                $error[] = esc_html__('Shorttag %authoremail% missing', 'kps');
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%authorcontactdata%') === false )
            {
                $error[] = esc_html__('Shorttag %authorcontactdata% missing', 'kps');
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%linkactivation%') !== false )
            {
                $error[] = esc_html__('Shorttag %linkactivation% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%linkdelete%') !== false )
            {
                $error[] = esc_html__('Shorttag %linkdelete% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%erasepassword%') !== false )
            {
                $error[] = esc_html__('Shorttag %erasepassword% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%linkreg%') !== false )
            {
                $error[] = esc_html__('Shorttag %linkreg% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%regpassword%') !== false )
            {
                $error[] = esc_html__('Shorttag %regpassword% not allowed', 'kps');
            }
            if (!is_array($setMailSettings))
            {
                $error[] = esc_html__('Error validating the data', 'kps');
            }

            // Email-Content aktualisieren
            if (is_array($setMailSettings)
                &&!empty($setMailSettings['kpsContactDataSubject'])
                && !empty($setMailSettings['kpsContactDataContent'])
                && strpos($setMailSettings['kpsContactDataContent'], '%authorname%') !== false
                && strpos($setMailSettings['kpsContactDataContent'], '%authoremail%') !== false
                && strpos($setMailSettings['kpsContactDataContent'], '%authorcontactdata%') !== false
                && strpos($setMailSettings['kpsContactDataContent'], '%linkactivation%') === false
                && strpos($setMailSettings['kpsContactDataContent'], '%linkdelete%') === false
                && strpos($setMailSettings['kpsContactDataContent'], '%erasepassword%') === false
                && strpos($setMailSettings['kpsContactDataContent'], '%linkreg%') === false
                && strpos($setMailSettings['kpsContactDataContent'], '%regpassword%') === false)
            {
                // Serialiseren
                $setMailSettings = serialize($setMailSettings);

                // Serialieren True --> Update DB
                if (is_serialized($setMailSettings))
                {
                    update_option('kps_userMailSettings', $setMailSettings);
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Contact details', 'kps') . '</strong></p>
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

    // Hole Email-Vorlagen Einstellungen
    $writeMailSettings  = kps_unserialize(get_option('kps_userMailSettings', false));
    $writeMail          = kps_mailcontent_requirement($writeMailSettings);

    // Zeit
    if (kps_unserialize(get_option('kps_output', false))['kpsEmailSetTime'] === 'true')
    {
        $exampleSetTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleSetTime = date_i18n(get_option('date_format'), time());
    }
    if (kps_unserialize(get_option('kps_output', false))['kpsEmailUnlockTime'] === 'true')
    {
        $exampleUnlockTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleUnlockTime = date_i18n(get_option('date_format'), time());
    }
    if (kps_unserialize(get_option('kps_output', false))['kpsEmailDeleteTime'] === 'true')
    {
        $exampleDeleteTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleDeleteTime = date_i18n(get_option('date_format'), time());
    }

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><label for="kpsContactDataSubject"><b>' . esc_html__('Email subject', 'kps') . '</b></label></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" name="kpsContactDataSubject" id="kpsContactDataSubject" autocomplete="off" class="form_field" value="' . $writeMail['Subject'] . '" /></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="kpsContactDataContent"></label>
                                            <textarea name="kpsContactDataContent" id="kpsContactDataContent" class="textarea" aria-required="true" required="required">' . esc_textarea($writeMail['Content']) . '</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td  style="text-align: center;">
                                            <input type="hidden" id="kps_tab" name="kps_tab" value="kps_ContactDataEmail" />
                                            <input type="hidden" id="kpsContactDataToken" name="kpsContactDataToken" value="' . $token . '" />
                                            <input class="button-primary" type="submit" name="submitContactData" value="' . esc_html__('Save', 'kps') . '">
                                            <input class="button-primary" type="submit" name="submitReset" value="' . esc_html__('Reset', 'kps') . '">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html__('Duty-Shorttags', 'kps') . '</b></td>
                                        <td><b>' . esc_html__('Result', 'kps') . '</b></td>
                                    </tr>
                                    <tr>
                                        <td>%authorname%</td>
                                        <td>' . esc_html__('Author Name', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authoremail%</td>
                                        <td>' . esc_html__('Author Email', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authorcontactdata%</td>
                                        <td>' . esc_html__('Author contact details', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html__('General Shorttags', 'kps') . '</b></td>
                                        <td><b>' . esc_html__('Result', 'kps') . '</b></td>
                                    </tr>
                                    <tr>
                                        <td>%blogname%</td>
                                        <td>' . get_bloginfo('name', 'raw') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%blogurl%</td>
                                        <td>' . get_bloginfo('url', 'raw') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%blogemail%</td>
                                        <td>' . get_option('kps_MailFrom', false) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%entrycontent%</td>
                                        <td>' . esc_html__('Entry', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%setdate%</td>
                                        <td>' . $exampleSetTime . "&#160;(". esc_html__('Created', 'kps') . ')</td>
                                    </tr>
                                    <tr>
                                        <td>%erasedatetime%</td>
                                        <td>' . $exampleDeleteTime . "&#160;(". esc_html__('Delete Time', 'kps') . ')</td>
                                    </tr>
                                    <tr>
                                        <td>%unlockdatetime%</td>
                                        <td>' . $exampleUnlockTime . "&#160;(". esc_html__('Released', 'kps') . ')</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
            		</div>
            	</div>
            </div>
        ';
}

/**
 * Funktion E-Mail Vorlage Verifizierung
 */
function kps_VerificationEmail()
{
    $verification   = false;
    $error          = array();

    // Token erstellen
    $token = wp_create_nonce('kpsVerifictionToken');

    if (isset($_POST['submitReset']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsVerifictionToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsVerifictionToken'], 'kpsVerifictionToken');

        // Verifizieren
        if ($verification == true)
        {
            delete_option('kps_userMailContactSettings');

            echo '
            <div class="notice notice-success is-dismissible">
            	<p><strong>' . esc_html__('Reset', 'kps') . ':&#160;' . esc_html__('Verification', 'kps') . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    if (isset($_POST['submitVerifiction']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsVerifictionSubject',
            'kpsVerifictionContent',
            'kpsVerifictionToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsVerifictionToken'], 'kpsVerifictionToken');

        // Verifizieren
        if ($verification == true)
        {
            // Subject und Textarea escapen
            $setMailSettings['kpsVerifictionSubject']   = kps_sanitize_field($postVars['kpsVerifictionSubject']);
            $setMailSettings['kpsVerifictionContent']   = kps_sanitize_textarea($postVars['kpsVerifictionContent']);

            // Fehlermeldungen
            if (empty($setMailSettings['kpsVerifictionSubject']))
            {
                $error[] = esc_html__('Email subject is missing', 'kps');
            }
            if (empty($setMailSettings['kpsVerifictionContent']))
            {
                $error[] = esc_html__('Content is missing', 'kps');
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%authorname%') !== false )
            {
                $error[] = esc_html__('Shorttag %authorname% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%authoremail%') !== false )
            {
                $error[] = esc_html__('Shorttag %authoremail% nicht not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%authorcontactdata%') !== false )
            {
                $error[] = esc_html__('Shorttag %authorcontactdata% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%linkactivation%') !== false )
            {
                $error[] = esc_html__('Shorttag %linkactivation% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%linkdelete%') !== false )
            {
                $error[] = esc_html__('Shorttag %linkdelete% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%erasepassword%') !== false )
            {
                $error[] = esc_html__('Shorttag %erasepassword% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%linkreg%') === false )
            {
                $error[] = esc_html__('Shorttag %linkreg% missing', 'kps');
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%regpassword%') === false )
            {
                $error[] = esc_html__('Shorttag %regpassword% missing', 'kps');
            }
            if (!is_array($setMailSettings))
            {
                $error[] = esc_html__('Error validating the data', 'kps');
            }

            // Email-Content aktualisieren
            if (is_array($setMailSettings)
                && !empty($setMailSettings['kpsVerifictionSubject'])
                && !empty($setMailSettings['kpsVerifictionContent'])
                && strpos($setMailSettings['kpsVerifictionContent'], '%linkreg%') !== false
                && strpos($setMailSettings['kpsVerifictionContent'], '%regpassword%') !== false
                && strpos($setMailSettings['kpsVerifictionContent'], '%authorname%') === false
                && strpos($setMailSettings['kpsVerifictionContent'], '%authoremail%') === false
                && strpos($setMailSettings['kpsVerifictionContent'], '%authorcontactdata%') === false
                && strpos($setMailSettings['kpsVerifictionContent'], '%linkactivation%') === false
                && strpos($setMailSettings['kpsVerifictionContent'], '%linkdelete%') === false
                && strpos($setMailSettings['kpsVerifictionContent'], '%erasepassword%') === false)
            {
                // Serialiseren
                $setMailSettings = serialize($setMailSettings);

                // Serialieren True --> Update DB
                if (is_serialized($setMailSettings))
                {
                    update_option('kps_userMailContactSettings', $setMailSettings);
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Request contact information', 'kps') . '</strong></p>
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

    // Hole Email-Vorlagen Einstellungen
    $writeMailSettings  = kps_unserialize(get_option('kps_userMailContactSettings', false));
    $writeMail          = kps_mailcontent_verify($writeMailSettings);

    // Zeit
    if (kps_unserialize(get_option( 'kps_output', false))['kpsEmailSetTime'] === 'true')
    {
        $exampleSetTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleSetTime = date_i18n(get_option('date_format'), time());
    }
    if (kps_unserialize(get_option('kps_output', false))['kpsEmailUnlockTime'] === 'true')
    {
        $exampleUnlockTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleUnlockTime = date_i18n(get_option('date_format'), time());
    }
    if (kps_unserialize(get_option('kps_output', false))['kpsEmailDeleteTime'] === 'true')
    {
        $exampleDeleteTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleDeleteTime = date_i18n(get_option('date_format'), time());
    }

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 50%;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><label for="kpsVerifictionSubject"><b>' . esc_html__('Email subject', 'kps') . '</b></label></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" name="kpsVerifictionSubject" id="kpsVerifictionSubject" autocomplete="off" class="form_field" value="' . $writeMail['Subject'] . '" /></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="kpsVerifictionContent"></label>
                                            <textarea name="kpsVerifictionContent" id="kpsVerifictionContent" class="textarea" aria-required="true" required="required">' . esc_textarea($writeMail['Content']) . '</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td  style="text-align: center;">
                                            <input type="hidden" id="kps_tab" name="kps_tab" value="kps_VerificationEmail" />
                                            <input type="hidden" id="kpsVerifictionToken" name="kpsVerifictionToken" value="' . $token . '" />
                                            <input class="button-primary" type="submit" name="submitVerifiction" value="' . esc_html__('Save', 'kps') . '" />
                                            <input class="button-primary" type="submit" name="submitReset" value="' . esc_html__('Reset', 'kps') . '">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html__('Duty-Shorttags', 'kps') . '</b></td>
                                        <td><b>' . esc_html__('Result', 'kps') . '</b></td>
                                    </tr>
                                    <tr>
                                        <td>%linkreg%</td>
                                        <td>' . esc_html__('Requirement-Link', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%regpassword%</td>
                                        <td>' . esc_html__('Password', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html__('General Shorttags', 'kps') . '</b></td>
                                        <td><b>' . esc_html__('Result', 'kps') . '</b></td>
                                    </tr>
                                    <tr>
                                        <td>%blogname%</td>
                                        <td>' . get_bloginfo('name', 'raw') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%blogurl%</td>
                                        <td>' . get_bloginfo('url', 'raw') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%blogemail%</td>
                                        <td>' . get_option('kps_MailFrom', false) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%entrycontent%</td>
                                        <td>' . esc_html__('Entry', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%setdate%</td>
                                        <td>' . $exampleSetTime . "&#160;(". esc_html__('Created', 'kps') . ')</td>
                                    </tr>
                                    <tr>
                                        <td>%erasedatetime%</td>
                                        <td>' . $exampleDeleteTime . "&#160;(". esc_html__('Delete Time', 'kps') . ')</td>
                                    </tr>
                                    <tr>
                                        <td>%unlockdatetime%</td>
                                        <td>' . $exampleUnlockTime . "&#160;(". esc_html__('Released', 'kps') . ')</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
            		</div>
            	</div>
            </div>
        ';
}

/**
 * Funktion E-Mail Vorlage Aktivierung
 */
function kps_ActivationEmail()
{
    $verification   = false;
    $error          = array();

    // Token erstellen
    $token = wp_create_nonce('kpsActivationToken');

    if (isset($_POST['submitReset']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsActivationToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsActivationToken'], 'kpsActivationToken');

        // Verifizieren
        if ($verification == true)
        {
            delete_option('kps_authorMailSettings');

            echo '
            <div class="notice notice-success is-dismissible">
            	<p><strong>' . esc_html__('Reset', 'kps') . ':&#160;' . esc_html__('Activation', 'kps') . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    if (isset($_POST['submitActivation']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsActivationSubject',
            'kpsActivationContent',
            'kpsActivationToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsActivationToken'], 'kpsActivationToken');

        // Verifizieren
        if ($verification == true)
        {
            // Subject und Textarea escapen
            $setMailSettings['kpsActivationSubject']    = kps_sanitize_field($postVars['kpsActivationSubject']);
            $setMailSettings['kpsActivationContent']    = kps_sanitize_textarea($postVars['kpsActivationContent']);

            // Fehlermeldungen
            if (empty($setMailSettings['kpsActivationSubject']))
            {
                $error[] = esc_html__('Email subject is missing', 'kps');
            }
            if (empty($setMailSettings['kpsActivationContent']))
            {
                $error[] = esc_html__('Content is missing', 'kps');
            }
            if (strpos($setMailSettings['kpsActivationContent'], '%linkactivation%') === false )
            {
                $error[] = esc_html__('Shorttag %linkactivation% missing', 'kps');
            }
            if (strpos($setMailSettings['kpsActivationContent'], '%linkdelete%') === false )
            {
                $error[] = esc_html__('Shorttag %linkdelete% missing', 'kps');
            }
            if (strpos($setMailSettings['kpsActivationContent'], '%erasepassword%') === false )
            {
                $error[] = esc_html__('Shorttag %erasepassword% missing', 'kps');
            }
            if (strpos($setMailSettings['kpsActivationContent'], '%linkreg%') !== false )
            {
                $error[] = esc_html__('Shorttag %linkreg% not allowed', 'kps');
            }
            if (strpos($setMailSettings['kpsActivationContent'], '%regpassword%') !== false )
            {
                $error[] = esc_html__('Shorttag %regpassword% not allowed', 'kps');
            }
            if (!is_array($setMailSettings))
            {
                $error[] = esc_html__('Error validating the data', 'kps');
            }

            // Email-Content aktualisieren
            if (is_array($setMailSettings)
                && !empty($setMailSettings['kpsActivationSubject'])
                && !empty($setMailSettings['kpsActivationContent'])
                && strpos($setMailSettings['kpsActivationContent'], '%linkactivation%') !== false
                && strpos($setMailSettings['kpsActivationContent'], '%linkdelete%') !== false
                && strpos($setMailSettings['kpsActivationContent'], '%erasepassword%') !== false
                && strpos($setMailSettings['kpsActivationContent'], '%linkreg%') === false
                && strpos($setMailSettings['kpsActivationContent'], '%regpassword%') === false)
            {
                // Serialisieren
                $setMailSettings = serialize($setMailSettings);

                // Serialieren True --> Update DB
                if (is_serialized($setMailSettings))
                {
                    update_option('kps_authorMailSettings', $setMailSettings);
                    echo '
                    <div class="notice notice-success is-dismissible">
                    	<p><strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Activation', 'kps') . '</strong></p>
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

    // Hole Email-Vorlagen Einstellungen
    $writeMailSettings  = kps_unserialize(get_option('kps_authorMailSettings', false));
    $writeMail          = kps_mailcontent_activation($writeMailSettings);

    // Zeit
    if (kps_unserialize(get_option('kps_output', false))['kpsEmailSetTime'] === 'true')
    {
        $exampleSetTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleSetTime = date_i18n(get_option('date_format'), time());
    }
    if (kps_unserialize(get_option('kps_output', false))['kpsEmailUnlockTime'] === 'true')
    {
        $exampleUnlockTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleUnlockTime = date_i18n(get_option('date_format'), time());
    }
    if (kps_unserialize(get_option('kps_output', false))['kpsEmailDeleteTime'] === 'true')
    {
        $exampleDeleteTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
    }
    else
    {
        $exampleDeleteTime = date_i18n(get_option('date_format'), time());
    }

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><label for="kpsActivationSubject"><b>' . esc_html__('Email subject', 'kps') . '</b></label></td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" name="kpsActivationSubject" id="kpsActivationSubject" autocomplete="off" class="form_field" value="' . $writeMail['Subject'] . '" /></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="kpsActivationContent"></label>
                                            <textarea name="kpsActivationContent" id="kpsActivationContent" class="textarea" data-autoresize aria-required="true" required="required">' . esc_textarea($writeMail['Content']) . '</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center;">
                                            <input type="hidden" id="kps_tab" name="kps_tab" value="kps_ActivationEmail" />
                                            <input type="hidden" id="kpsActivationToken" name="kpsActivationToken" value="' . $token . '" />
                                            <input class="button-primary" type="submit" name="submitActivation" value="' . esc_html__('Save', 'kps') . '" />
                                            <input class="button-primary" type="submit" name="submitReset" value="' . esc_html__('Reset', 'kps') . '">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html__('Duty-Shorttags', 'kps') . '</b></td>
                                        <td><b>' . esc_html__('Result', 'kps') . '</b></td>
                                    </tr>
                                    <tr>
                                        <td>%linkactivation%</td>
                                        <td>' . esc_html__('Activation-Link', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%linkdelete%</td>
                                        <td>' . esc_html__('Delete-Link', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%erasepassword%</td>
                                        <td>' . esc_html__('Password', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html__('General Shorttags', 'kps') . '</b></td>
                                        <td><b>' . esc_html__('Result', 'kps') . '</b></td>
                                    </tr>
                                    <tr>
                                        <td>%blogname%</td>
                                        <td>' . get_bloginfo('name', 'raw') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%blogurl%</td>
                                        <td>' . get_bloginfo('url', 'raw') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%blogemail%</td>
                                        <td>' . get_option('kps_MailFrom', false) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authorname%</td>
                                        <td>' . esc_html__('Author Name', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authoremail%</td>
                                        <td>' . esc_html__('Author Email', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authorcontactdata%</td>
                                        <td>' . esc_html__('Author contact details', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%entrycontent%</td>
                                        <td>' . esc_html__('Entry', 'kps') . '</td>
                                    </tr>
                                    <tr>
                                        <td>%setdate%</td>
                                        <td>' . $exampleSetTime . "&#160;(". esc_html__('Created', 'kps') . ')</td>
                                    </tr>
                                    <tr>
                                        <td>%erasedatetime%</td>
                                        <td>' . $exampleDeleteTime . "&#160;(". esc_html__('Delete Time', 'kps') . ')</td>
                                    </tr>
                                    <tr>
                                        <td>%unlockdatetime%</td>
                                        <td>' . $exampleUnlockTime . "&#160;(". esc_html__('Released', 'kps') . ')</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
            		</div>
            	</div>
            </div>
        ';
}