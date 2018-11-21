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
function kps_EmailSetting()
{
    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('manage_options'))
    {
        die(esc_html(__('Zugriff verweigert!', 'kps')));
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
                    <?php echo esc_html(__('Kletterpartner-Suche', 'kps')); ?> - <?php echo esc_html(__('Übersicht', 'kps')); ?>
               </h3>

            <h2 class="nav-tab-wrapper kps_nav_tab_wrapper">
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_ActivationEmail') { echo "nav-tab-active";} ?>" rel="kps_ActivationEmail">
                    <div style="text-align: center;"><?php  esc_html_e('Aktivierung', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_UnlockEmail') { echo "nav-tab-active";} ?>" rel="kps_UnlockEmail">
                    <div style="text-align: center;"><?php  esc_html_e('Freischaltung', 'kps'); ?></div>
                </a>
    			<a href="" class="nav-tab <?php if ($kps_tab == 'kps_VerificationEmail') { echo "nav-tab-active";} ?>" rel="kps_VerificationEmail">
                    <div style="text-align: center;"><?php  esc_html_e('Anforderung Kontaktdaten', 'kps'); ?></div>
                </a>
                <a href="" class="nav-tab <?php if ($kps_tab == 'kps_ContactDataEmail') { echo "nav-tab-active";} ?>" rel="kps_ContactDataEmail">
                    <div style="text-align: center;"><?php  esc_html_e('Versendung Kontaktdaten', 'kps'); ?></div>
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
                $error[] = esc_html(__('Email-Betreff fehlt', 'kps'));
            }
            if (empty($setMailSettings['kpsUnlockContent']))
            {
                $error[] = esc_html(__('Content fehlt', 'kps'));
            }
            if (strpos($setMailSettings['kpsUnlockContent'], '%linkactivation%') !== false )
            {
                $error[] = esc_html(__('Shorttag %linkactivation% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsUnlockContent'], '%linkdelete%') !== false )
            {
                $error[] = esc_html(__('Shorttag %linkdelete% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsUnlockContent'], '%erasepassword%') !== false )
            {
                $error[] = esc_html(__('Shorttag %erasepassword% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsUnlockContent'], '%linkreg%') !== false )
            {
                $error[] = esc_html(__('Shorttag %linkreg% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsUnlockContent'], '%regpassword%') !== false )
            {
                $error[] = esc_html(__('Shorttag %regpassword% nicht erlaubt', 'kps'));
            }
            if (!is_array($setMailSettings))
            {
                $error[] = esc_html(__('Fehler bei der Validierung der Daten', 'kps'));
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
                    	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Freischaltung', 'kps')) . '</strong></p>
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
    // Hole Email-Vorlagen Einstellungen
    $checkedMailSettings = kps_unserialize(get_option('kps_adminUnlockMailSettings', false));

    if ($checkedMailSettings === false )
    {
        $checkedUnlockSubject   = esc_html(__('Freischaltung', 'kps'));
        $checkedUnlockContent   =
esc_html(__('Ihr Eintrag wurde soeben freigeschaltet!

Der Löschzeitpunkt für diesen Eintrag wurde auf %erasedatetime% gesetzt.

Dein Eintrag:
*******************
Eintrag geschrieben am: %setdate%
Eintrag freigegeben am: %unlockdatetime%
Eintrag wird gelöscht am: %erasedatetime%

%entrycontent%

Deine Kontaktdaten:
*******************
Name: %authorname%
Email: %authoremail%
%authorcontactdata%

Vielen Dank!
Eurer Team
%blogname%
%blogurl%
%blogemail%', 'kps'));
    }
    else
    {
        $checkedUnlockSubject   = esc_attr($checkedMailSettings['kpsUnlockSubject']);
        $checkedUnlockContent   = esc_attr($checkedMailSettings['kpsUnlockContent']);
    }

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td><label for="kpsUnlockSubject"><b>' . esc_html(__('Email-Betreff', 'kps')) . '</b></label></td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" name="kpsUnlockSubject" id="kpsUnlockSubject" autocomplete="off" class="form_field" value="' . $checkedUnlockSubject . '" /></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="kpsUnlockContent"></label>
                                                <textarea name="kpsUnlockContent" id="kpsUnlockContent" class="textarea" aria-required="true" required="required">' . esc_textarea($checkedUnlockContent) . '</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="kps-br"></td>
                                        </tr>
                                        <tr>
                                            <td  style="text-align: center;">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_UnlockEmail" />
                                                <input type="hidden" id="kpsUnlockToken" name="kpsUnlockToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitUnlock" value="' . __('Speichern', 'kps') . '" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html(__('Allgemeine Shorttags', 'kps')) . '</b></td>
                                        <td><b>' . esc_html(__('Ergebnis', 'kps')) . '</b></td>
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
                                        <td>' . esc_html(__('Autor Name', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authoremail%</td>
                                        <td>' . esc_html(__('Autor Email', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authorcontactdata%</td>
                                        <td>' . esc_html(__('Autor Kontaktdaten', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%entrycontent%</td>
                                        <td>' . esc_html(__('Eintrag', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authorcontactdata%</td>
                                        <td>' . esc_html(__('Autor Kontaktdaten', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%setdate%</td>
                                        <td>' . esc_html(__('Eintrag geschrieben am', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%erasedatetime%</td>
                                        <td>' . esc_html(__('Eintrag wird gelöscht am', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%unlockdatetime%</td>
                                        <td>' . esc_html(__('Eintrag freigegeben am', 'kps')) . '</td>
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
                $error[] = esc_html(__('Email-Betreff fehlt', 'kps'));
            }
            if (empty($setMailSettings['kpsContactDataContent']))
            {
                $error[] = esc_html(__('Content fehlt', 'kps'));
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%authorname%') === false )
            {
                $error[] = esc_html(__('Shorttag %authorname% fehlt', 'kps'));
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%authoremail%') === false )
            {
                $error[] = esc_html(__('Shorttag %authoremail% fehlt', 'kps'));
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%authorcontactdata%') === false )
            {
                $error[] = esc_html(__('Shorttag %authorcontactdata% fehlt', 'kps'));
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%linkactivation%') !== false )
            {
                $error[] = esc_html(__('Shorttag %linkactivation% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%linkdelete%') !== false )
            {
                $error[] = esc_html(__('Shorttag %linkdelete% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%erasepassword%') !== false )
            {
                $error[] = esc_html(__('Shorttag %erasepassword% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%linkreg%') !== false )
            {
                $error[] = esc_html(__('Shorttag %linkreg% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%regpassword%') !== false )
            {
                $error[] = esc_html(__('Shorttag %regpassword% nicht erlaubt', 'kps'));
            }
            if (!is_array($setMailSettings))
            {
                $error[] = esc_html(__('Fehler bei der Validierung der Daten', 'kps'));
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
                    	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Versendung Kontaktdaten', 'kps')) . '</strong></p>
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

    // Hole Email-Vorlagen Einstellungen
    $checkedMailSettings    = kps_unserialize(get_option('kps_userMailSettings', false));

    if ($checkedMailSettings === false )
    {
        $checkedContactDataSubject = esc_html(__('Versendung Kontaktdaten', 'kps'));
        $checkedContactDataContent =
esc_html(__('Sie haben die Kontaktdaten für folgenden Eintrag angefordert.

Eintrag:
*******************
Eintrag geschrieben am: %setdate%
Eintrag freigegeben am: %unlockdatetime%
Eintrag wird gelöscht am: %erasedatetime%

%entrycontent%

Die Kontaktdaten lauten:
************************
Name: %authorname%
Email: %authoremail%
%authorcontactdata%

Wir wünschen Euch viel Spaß. Berg Heil!
Eurer Team
%blogname%
%blogurl%
%blogemail%', 'kps'));
    }
    else
    {
        $checkedContactDataSubject = esc_attr($checkedMailSettings['kpsContactDataSubject']);
        $checkedContactDataContent = esc_attr($checkedMailSettings['kpsContactDataContent']);
    }

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td><label for="kpsContactDataSubject"><b>' . esc_html(__('Email-Betreff', 'kps')) . '</b></label></td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" name="kpsContactDataSubject" id="kpsContactDataSubject" autocomplete="off" class="form_field" value="' . $checkedContactDataSubject . '" /></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="kpsContactDataContent"></label>
                                                <textarea name="kpsContactDataContent" id="kpsContactDataContent" class="textarea" aria-required="true" required="required">' . esc_textarea($checkedContactDataContent) . '</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="kps-br"></td>
                                        </tr>
                                        <tr>
                                            <td  style="text-align: center;">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_ContactDataEmail" />
                                                <input type="hidden" id="kpsContactDataToken" name="kpsContactDataToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitContactData" value="' . esc_html(__('Speichern', 'kps')) . '">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html(__('Pflicht-Shorttags', 'kps')) . '</td>
                                        <td><b>' . esc_html(__('Ergebnis', 'kps')) . '</b></td>
                                    </tr>
                                    <tr>
                                        <td>%authorname%</td>
                                        <td>' . esc_html(__('Autor Name', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authoremail%</td>
                                        <td>' . esc_html(__('Autor Email', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authorcontactdata%</td>
                                        <td>' . esc_html(__('Autor Kontaktdaten', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html(__('Allgemeine Shorttags', 'kps')) . '</b></td>
                                        <td><b>' . esc_html(__('Ergebnis', 'kps')) . '</b></td>
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
                                        <td>' . esc_html(__('Eintrag', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%setdate%</td>
                                        <td>' . esc_html(__('Eintrag geschrieben am', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%erasedatetime%</td>
                                        <td>' . esc_html(__('Eintrag wird gelöscht am', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%unlockdatetime%</td>
                                        <td>' . esc_html(__('Eintrag freigegeben am', 'kps')) . '</td>
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
                $error[] = esc_html(__('Email-Betreff fehlt', 'kps'));
            }
            if (empty($setMailSettings['kpsVerifictionContent']))
            {
                $error[] = esc_html(__('Content fehlt', 'kps'));
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%authorname%') !== false )
            {
                $error[] = esc_html(__('Shorttag %authorname% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsContactDataContent'], '%authoremail%') !== false )
            {
                $error[] = esc_html(__('Shorttag %authoremail% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%authorcontactdata%') !== false )
            {
                $error[] = esc_html(__('Shorttag %authorcontactdata% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%linkactivation%') !== false )
            {
                $error[] = esc_html(__('Shorttag %linkactivation% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%linkdelete%') !== false )
            {
                $error[] = esc_html(__('Shorttag %linkdelete% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%erasepassword%') !== false )
            {
                $error[] = esc_html(__('Shorttag %erasepassword% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%linkreg%') === false )
            {
                $error[] = esc_html(__('Shorttag %linkreg% fehlt', 'kps'));
            }
            if (strpos($setMailSettings['kpsVerifictionContent'], '%regpassword%') === false )
            {
                $error[] = esc_html(__('Shorttag %regpassword% fehlt', 'kps'));
            }
            if (!is_array($setMailSettings))
            {
                $error[] = esc_html(__('Fehler bei der Validierung der Daten', 'kps'));
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
                    	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Anforderung Kontaktdaten', 'kps')) . '</strong></p>
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

    // Hole Email-Vorlagen Einstellungen
    $checkedMailSettings    = kps_unserialize(get_option('kps_userMailContactSettings', false));

    if ($checkedMailSettings === false )
    {
        $checkedVerifictionSubject = esc_html(__('Anforderung Kontaktdaten', 'kps'));
        $checkedVerifictionContent =
esc_html(__('Sie möchten die Kontaktdaten für folgenden Eintrag.

Um die Kontaktdaten abrufen zu können, klicken Sie auf den Link und geben das Password ein.
Der Anforderungsschlüssel ist 24 Stunden gültig!

Anforderungs-Link:
*******************
Passwort: %regpassword%
%linkreg%

Eintrag:
*******************
%entrycontent%

Vielen Dank!
Eurer Team
%blogname%
%blogurl%
%blogemail%', 'kps'));
    }
    else
    {
        $checkedVerifictionSubject = esc_attr($checkedMailSettings['kpsVerifictionSubject']);
        $checkedVerifictionContent = esc_attr($checkedMailSettings['kpsVerifictionContent']);
    }

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 50%;">
                            <form class="form" action="" method="post">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td><label for="kpsVerifictionSubject"><b>' . esc_html(__('Email-Betreff', 'kps')) . '</b></label></td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" name="kpsVerifictionSubject" id="kpsVerifictionSubject" autocomplete="off" class="form_field" value="' . $checkedVerifictionSubject . '" /></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="kpsVerifictionContent"></label>
                                                <textarea name="kpsVerifictionContent" id="kpsVerifictionContent" class="textarea" aria-required="true" required="required">' . esc_textarea($checkedVerifictionContent) . '</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="kps-br"></td>
                                        </tr>
                                        <tr>
                                            <td  style="text-align: center;">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_VerificationEmail" />
                                                <input type="hidden" id="kpsVerifictionToken" name="kpsVerifictionToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitVerifiction" value="' . esc_html(__('Speichern', 'kps')) . '" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html(__('Pflicht-Shorttags', 'kps')) . '</td>
                                        <td><b>' . esc_html(__('Ergebnis', 'kps')) . '</b></td>
                                    </tr>
                                    <tr>
                                        <td>%linkreg%</td>
                                        <td>' . esc_html(__('Anforderungs-Link', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%regpassword%</td>
                                        <td>' . esc_html(__('Passwort', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html(__('Allgemeine Shorttags', 'kps')) . '</b></td>
                                        <td><b>' . esc_html(__('Ergebnis', 'kps')) . '</b></td>
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
                                        <td>' . esc_html(__('Eintrag', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%setdate%</td>
                                        <td>' . esc_html(__('Eintrag geschrieben am', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%erasedatetime%</td>
                                        <td>' . esc_html(__('Eintrag wird gelöscht am', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%unlockdatetime%</td>
                                        <td>' . esc_html(__('Eintrag freigegeben am', 'kps')) . '</td>
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
                $error[] = esc_html(__('Email-Betreff fehlt', 'kps'));
            }
            if (empty($setMailSettings['kpsActivationContent']))
            {
                $error[] = esc_html(__('Content fehlt', 'kps'));
            }
            if (strpos($setMailSettings['kpsActivationContent'], '%linkactivation%') === false )
            {
                $error[] = esc_html(__('Shorttag %linkactivation% fehlt', 'kps'));
            }
            if (strpos($setMailSettings['kpsActivationContent'], '%linkdelete%') === false )
            {
                $error[] = esc_html(__('Shorttag %linkdelete% fehlt', 'kps'));
            }
            if (strpos($setMailSettings['kpsActivationContent'], '%erasepassword%') === false )
            {
                $error[] = esc_html(__('Shorttag %erasepassword% fehlt', 'kps'));
            }
            if (strpos($setMailSettings['kpsActivationContent'], '%linkreg%') !== false )
            {
                $error[] = esc_html(__('Shorttag %linkreg% nicht erlaubt', 'kps'));
            }
            if (strpos($setMailSettings['kpsActivationContent'], '%regpassword%') !== false )
            {
                $error[] = esc_html(__('Shorttag %regpassword% nicht erlaubt', 'kps'));
            }
            if (!is_array($setMailSettings))
            {
                $error[] = esc_html(__('Fehler bei der Validierung der Daten', 'kps'));
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
                    	<p><strong>' .  esc_html(__('Gespeichert', 'kps')) . ':&#160;' .  esc_html(__('Aktivierung', 'kps')) . '</strong></p>
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

    // Hole Email-Vorlagen Einstellungen
    $checkedMailSettings = kps_unserialize(get_option('kps_authorMailSettings', false));

    if ($checkedMailSettings === false )
    {
        $checkedActivationSubject   = esc_html(__('Aktivierung', 'kps'));
        $checkedActivationContent   =
esc_html(__('Sie haben gerade einen neuen Eintrag auf %blogname% gepostet.

Damit dieser nun freigeschaltet werden kann, müssen Sie den
Eintrag über folgenden Link bestätigten und Ihre Email-Adresse eingeben.
Sollten Sie diesen Beitrag nicht freigeben, so wird dieser automatisch
am %erasedatetime% aus unserer Datenbank gelöscht.

Eintrag aktivieren:
*******************
%linkactivation%

Eintrag löschen:
*******************
Passwort: %erasepassword%
%linkdelete%

Dein Eintrag:
*******************
Eintrag geschrieben am: %setdate%
Eintrag freigegeben am: %unlockdatetime%
Eintrag wird gelöscht am: %erasedatetime%

%entrycontent%

Deine Kontaktdaten:
*******************
Name: %authorname%
Email: %authoremail%
%authorcontactdata%

Vielen Dank!
Eurer Team
%blogname%
%blogurl%
%blogemail%', 'kps'));
    }
    else
    {
        $checkedActivationSubject   = esc_attr($checkedMailSettings['kpsActivationSubject']);
        $checkedActivationContent   = esc_attr($checkedMailSettings['kpsActivationContent']);
    }

    echo '
            <div class="kps-divTable kps_container">
            	<div class="kps-divTableBody">
            		<div class="kps-divTableRow">
            			<div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <form class="form" action="" method="post">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td><label for="kpsActivationSubject"><b>' . esc_html(__('Email-Betreff', 'kps')) . '</b></label></td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" name="kpsActivationSubject" id="kpsActivationSubject" autocomplete="off" class="form_field" value="' . $checkedActivationSubject . '" /></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="kpsActivationContent"></label>
                                                <textarea name="kpsActivationContent" id="kpsActivationContent" class="textarea" data-autoresize aria-required="true" required="required">' . esc_textarea($checkedActivationContent) . '</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="kps-br"></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center;">
                                                <input type="hidden" id="kps_tab" name="kps_tab" value="kps_ActivationEmail" />
                                                <input type="hidden" id="kpsActivationToken" name="kpsActivationToken" value="' . $token . '" />
                                                <input class="button-primary" type="submit" name="submitActivation" value="' . esc_html(__('Speichern', 'kps')) . '" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="kps-divTableCell" style="width: 50%; vertical-align: top;">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html(__('Pflicht-Shorttags', 'kps')) . '</b></td>
                                        <td><b>' . esc_html(__('Ergebnis', 'kps')) . '</b></td>
                                    </tr>
                                    <tr>
                                        <td>%linkactivation%</td>
                                        <td>' . esc_html(__('Aktivierungs-Link', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%linkdelete%</td>
                                        <td>' . esc_html(__('Lösch-Link', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%erasepassword%</td>
                                        <td>' . esc_html(__('Passwort', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="kps-br"></td>
                                    </tr>
                                    <tr>
                                        <td><b>' . esc_html(__('Allgemeine Shorttags', 'kps')) . '</b></td>
                                        <td><b>' . esc_html(__('Ergebnis', 'kps')) . '</b></td>
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
                                        <td>' . esc_html(__('Autor Name', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authoremail%</td>
                                        <td>' . esc_html(__('Autor Email', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%authorcontactdata%</td>
                                        <td>' . esc_html(__('Autor Kontaktdaten', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%entrycontent%</td>
                                        <td>' . esc_html(__('Eintrag', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%setdate%</td>
                                        <td>' . esc_html(__('Eintrag geschrieben am', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%erasedatetime%</td>
                                        <td>' . esc_html(__('Eintrag wird gelöscht am', 'kps')) . '</td>
                                    </tr>
                                    <tr>
                                        <td>%unlockdatetime%</td>
                                        <td>' . esc_html(__('Eintrag freigegeben am', 'kps')) . '</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
            		</div>
            	</div>
            </div>
        ';
}