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
 * Formular
 */
function kps_frontend_form($shortCodeValues)
{
    global $wpdb;

    $verification           = false;
    $captchaValid           = false;
    $formErrors             = false;
    $formPass               = false;
    $spamblock              = false;
    $formClass              = (string)'';
    $kpsWriteButton         = (string)'';
    $outputmessage          = (string)'';
    $errorIsFound           = false;
    $errorIsNotDB           = false;
    $errorAuthorName        = (string)'';
    $errorAuthorSearchfor   = (string)'';
    $errorAuthorRule        = (string)'';
    $errorYourRule          = (string)'';
    $errorAuthorEntry       = (string)'';
    $errorAuthorEmail       = (string)'';
    $errorAuthorAGBDSGVO    = (string)'';
    $errorAuthorAGB         = (string)'';
    $errorAuthorDSGVO       = (string)'';
    $errorCaptcha           = (string)'';

    // Template unterstützt HTML5
    $html5 = current_theme_supports('html5');

    // Hole die derzeitige Post-ID von Wordpress
    $pageUrl = get_post_permalink();

    // Hole Kontaktoptionen
    $formOptions = kps_unserialize(get_option('kps_formOptions', false));

    // Hole Usereinstellungen
    $kpsUserSettings = kps_unserialize(get_option('kps_userSettings', false));

    // Hole Formulareinstellungen für Captcha
    $captchaIsActivated = get_option('kps_captcha', false);
    $captchaKeys        = kps_unserialize(get_option('kps_captchakeys', false));

    // Spamsperre Timer
    $timestamp = time();

    // Token erstellen
    $token = wp_create_nonce('kpsAuthorMadeEntry');

    if (isset($_POST['submit']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kps_authorId',
            'kps_authorName',
            'kps_authorEmail',
            'kps_authorSearchfor',
            'kps_authorRule',
            'kps_yourRule',
            'kps_authorEntry',
            'kps_authorEmail',
            'kps_authorTelephone',
            'kps_authorMobile',
            'kps_authorSignal',
            'kps_authorTelegram',
            'kps_authorThreema',
            'kps_authorViper',
            'kps_authorWhatsapp',
            'kps_authorFacebookMessenger',
            'kps_authorHoccer',
            'kps_authorSkype',
            'kps_authorWire',
            'kps_authorWebsite',
            'kps_authorFacebook',
            'kps_authorInstagram',
            'kps_acceptedAGBDSGVO',
            'kps_spamtimer1',
            'kps_spamtimer2',
            'g-recaptcha-response',
            'kps_AuthorMadeEntryToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kps_AuthorMadeEntryToken'], 'kpsAuthorMadeEntry');

        // Spam-Sperre -> default 3 Sekunden
        $spamblock = $postVars['kps_spamtimer2'] - $postVars['kps_spamtimer1'];
        $spamblock = ($spamblock < 3) ? true : false;
    }

    // Wenn User eingeloggt ist, hole seinen Daten uns Schreibe Sie in die Formularfelder
    if (is_user_logged_in() === true)
    {
        // Aktuellen User holen
        $current_user   = wp_get_current_user();

        // Userdaten anhand der ID holen
        $userData       = get_userdata($current_user->id);

        // Userdaten, wenn registiert
        if (isset($userData->display_name) && !empty($userData->display_name))
        {
            // Display-Name verfügbar
            $setUserName = esc_html($userData->display_name);
        }
        else
        {
            // Login-Name nehmen
            $setUserName = esc_html($userData->user_login);
        }

        // Autor-Id
        $setUserId = $userData->id;

        // Email aus System holen
        $setUserEmail       = esc_html( $userData->user_email);
        $inputFieldDisabled = 'readonly="readonly"';
    }
    else
    {
        $setUserId      = 0;
        $setUserName    = $postVars['kps_authorName'];
        $setUserEmail   = $postVars['kps_authorEmail'];
    }

    // Prüfung des Captcha durch https://www.google.com/recaptcha/api/siteverify
    if ($captchaIsActivated === 'true')
    {
        $captchaSiteKey     = esc_html($captchaKeys['kpsCaptchaSiteKey']);
        $captchaSecretKey   = esc_html($captchaKeys['kpsCaptchaSecretKey']);
        $captchaUrl         = 'https://www.google.com/recaptcha/api/siteverify';

        if (isset($_POST['submit']))
        {
            $captchaData    =   array(  'secret' => $captchaSecretKey,
                                        'response' => $postVars['g-recaptcha-response']
                                );
            $captchaOptions =   array(  'http' =>
                                    array(  'method'    => 'POST',
                                            'content'   => http_build_query($captchaData)
                                    )
                                );

            $captchaContext     = stream_context_create($captchaOptions);
            $captchaVerify      = file_get_contents($captchaUrl, false, $captchaContext);
            $captchaSuccess     = json_decode($captchaVerify);
            $captchaValid       = $captchaSuccess->success;
        }
    }
    else
    {
        $captchaValid = true;
    }

    // Verifzieren
    if ($verification == true
        && is_array($postVars)
        && !empty($postVars)
        && $spamblock === false
        && isset($_POST['submit'])
        && $captchaValid === true)
    {
        // Eintrag in Datenbank schreiben
        $write = new kps_entry_write($postVars, $pageUrl);

        if ($write->show_activationEmailIsSend() && $write->show_isInsertDB())
        {
            // Messagebox
            $messageboxContent = '
            <ul>
                <li>' . esc_html__('You should receive an Activation-Email in the next few minutes!', 'kps') . '</li>
                <li>' . esc_html__('If you have not received any email from us, it may also be in your junk folder!', 'kps') . '</li>
                <li>' . esc_html__('It could be that the entry only becomes visible after we have approved it!', 'kps') . '</li>
                <li>' . esc_html__('We reserve the right to edit, delete or not publish entries!', 'kps') . '</li>
            </ul>';

            // Einschreiben erfolgreich?
            $outputmessage = kps_messagebox(esc_html__('Many Thanks! Your entry will now be checked!', 'kps') , $messageboxContent);

            // Eintrag erfolgreich
            $formPass = true;
        }
        else
        {
            // Formular öffnen
            $formErrors = true;
        }
    }

    /**
     * Spam-Sperre
     * Löst bei $_POST kleiner 3 Sekunden aus.
     * Das Formular wird zurück gesetzt
     */
    if ($spamblock === true)
    {
        // Messagebox Ausgabe
        $messageboxContent = '
        <div style="text-align: center; color: red">' . esc_html__('The form was processed too fast. Always with tranquillity!', 'kps') . '</div>
        <div style="text-align: center; color: red">' . esc_html__('The Form has been reset!', 'kps') . '</div>
        ';

        // Eintrag ist schon in Datenbank verhanden
        $spammessage = kps_messagebox(esc_html__('Error! Spam-Block was triggered!', 'kps') , $messageboxContent);

        // Formular schließen
        $formErrors = true;
    }

    // HTML5
    if ($html5)
    {
        $output = '<main>';
        $output .= '<article>';
        $output .= '<div>';
    }
    else
    {
        $output = '<div>';
    }
    $html5TypEmail  = isset($html5) ? 'email' : 'text';
    $html5TypTele   = isset($html5) ? 'tel' : 'text';
    $html5TypUrl    = isset($html5) ? 'url' : 'text';

    $setAuthorTelephone         = (isset($write->_authorTelephone) && !empty($write->_authorTelephone)) ? $write->_authorTelephone : '';
    $setAuthorMobile            = (isset($write->_authorMobile) && !empty($write->_authorMobile)) ? $write->_authorMobile : '';
    $setAuthorSignal            = (isset($write->_authorSignal) && !empty($write->_authorSignal)) ? $write->_authorSignal : '';
    $setAuthorTelegram          = (isset($write->_authorTelegram) && !empty($write->_authorTelegram)) ? $write->_authorTelegram : '';
    $setAuthorThreema           = (isset($write->_authorThreema) && !empty($write->_authorThreema)) ? $write->_authorThreema : '';
    $setAuthorViper             = (isset($write->_authorViper) && !empty($write->_authorViper)) ? $write->_authorViper : '';
    $setAuthorWhatsapp          = (isset($write->_authorWhatsapp) && !empty($write->_authorWhatsapp)) ? $write->_authorWhatsapp : '';
    $setAuthorFacebookMessenger = (isset($write->_authorFacebookMessenger) && !empty($write->_authorFacebookMessenger)) ? $write->_authorFacebookMessenger : '';
    $setAuthorHoccer            = (isset($write->_authorHoccer) && !empty($write->_authorHoccer)) ? $write->_authorHoccer : '';
    $setAuthorSkype             = (isset($write->_authorSkype) && !empty($write->_authorSkype)) ? $write->_authorSkype : '';
    $setAuthorWire              = (isset($write->_authorWire) && !empty($write->_authorWire)) ? $write->_authorWire : '';
    $setAuthorWebsite           = (isset($write->_authorWebsite) && !empty($write->_authorWebsite)) ? $write->_authorWebsite : '';
    $setAuthorFacebook          = (isset($write->_authorFacebook) && !empty($write->_authorFacebook)) ? $write->_authorFacebook : '';
    $setAuthorInstagram         = (isset($write->_authorInstagram) && !empty($write->_authorInstagram)) ? $write->_authorInstagram : '';

    // Wenn AGB's und DSGVO gesetzt sind
    if ($kpsUserSettings['kpsUserPrivacyAGB'] === 'true'
        && $kpsUserSettings['kpsUserPrivacyDSGVO'] === 'true'
        && get_option('kps_agb') > 0
        && get_post_status(get_option('kps_agb')) !== false
        && get_post_status(get_option('kps_agb')) == 'publish'
        && post_password_required(get_option('kps_agb')) === false
        && get_option('kps_dsgvo') > 0
        && get_post_status(get_option('kps_dsgvo')) !== false
        && get_post_status(get_option('kps_dsgvo')) == 'publish'
        && post_password_required(get_option('kps_dsgvo')) === false)
    {
        $formCheckboxAGBDSGVO = '   <div class="kps-br"></div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"></div>
                                        <div class="kps-divTableCell"><label for="kps_acceptedAGBDSGVO">
                                            <input type="checkbox" name="kps_acceptedAGBDSGVO" id="kps_acceptedAGBDSGVO" class="' . $errorAuthorAGBDSGVO . '" value="1" ' . $kpsAcceptedAGBDSGVO . ' />
                                            ' . esc_html__('Yes, I accept the', 'kps') . '
                                            <a href="' . esc_url(get_post_permalink(get_option('kps_agb'))) . '" target="_blank">' . esc_html__('GTC', 'kps') . '</a>
                                            ' . esc_html__('and the', 'kps') . '&#160;<a href="' . esc_url(get_post_permalink(get_option('kps_dsgvo'))) . '" target="_blank">' . esc_html__('GDPR', 'kps') . '</a>.
                                            ' . '</label>
                                        </div>
                                    </div>
                                ';
    }

    // Wenn nur AGB's gesetzt ist
    if ($kpsUserSettings['kpsUserPrivacyAGB'] === 'true'
        && $kpsUserSettings['kpsUserPrivacyDSGVO'] === 'false'
        && get_option('kps_agb') > 0
        && get_post_status(get_option('kps_agb')) !== false
        && get_post_status(get_option('kps_agb')) == 'publish'
        && post_password_required(get_option('kps_agb')) === false)
    {
        $formCheckboxAGBDSGVO = '   <div class="kps-br"></div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"></div>
                                        <div class="kps-divTableCell"><label for="kps_acceptedAGBDSGVO">
                                            <input type="checkbox" name="kps_acceptedAGBDSGVO" id="kps_acceptedAGBDSGVO" class="' . $errorAuthorAGBDSGVO . '" value="1" ' . $kpsAcceptedAGBDSGVO . ' />
                                            ' . esc_html__('Yes, I accept the', 'kps') . '
                                            <a href="' . esc_url(get_post_permalink(get_option('kps_agb'))) . '" target="_blank">' . esc_html__('GTC', 'kps') . '</a>.
                                            ' . '</label>
                                        </div>
                                    </div>
                                ';
    }

    // Wenn nur DSGVO gesetzt ist
    if ($kpsUserSettings['kpsUserPrivacyAGB'] === 'false'
        && $kpsUserSettings['kpsUserPrivacyDSGVO'] === 'true'
        && get_option('kps_dsgvo') > 0
        && get_post_status(get_option('kps_dsgvo')) !== false
        && get_post_status(get_option('kps_dsgvo')) == 'publish'
        && post_password_required(get_option('kps_dsgvo')) === false)
    {
        $formCheckboxAGBDSGVO = '   <div class="kps-br"></div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"></div>
                                        <div class="kps-divTableCell"><label for="kps_acceptedAGBDSGVO">
                                            <input type="checkbox" name="kps_acceptedAGBDSGVO" id="kps_acceptedAGBDSGVO" class="' . $errorAuthorAGBDSGVO . '" value="1" ' . $kpsAcceptedAGBDSGVO . ' />
                                            ' . esc_html__('Yes, I accept the', 'kps') . '
                                            <a href="' . esc_url(get_post_permalink(get_option('kps_dsgvo'))) . '" target="_blank">' . esc_html__('GDPR', 'kps') . '</a>.
                                            ' . '</label>
                                        </div>
                                    </div>
                                ';
    }

    // Wenn Option Registierungspflicht
    if (is_user_logged_in() !== true
        && $kpsUserSettings['kpsUserRequireRegistration'] === 'true'
        && get_option( 'users_can_register' ) != false)
    {
        // Wordpress Registieren
        $output .= '<div>
                        <div style="text-align: center;">
                            <h6>' . esc_html__('You must be logged in to leave an entry!', 'kps') . '.</h6>
                        </div>
                        <div style="text-align: center;">
                            <a href="' . esc_url(wp_login_url(get_permalink())) . '">' . esc_html__('Login', 'kps') . '</a>&#160;&#124;&#160;
                            <a href="' . esc_url(wp_registration_url()) . '">' . esc_html__('Registration', 'kps') . '</a>
                        </div>
                    </div>';
    }
    else
    {
        // Button für das öffnen des Formulars
        if (!$formErrors)
        {
            if ($shortCodeValues['show-form-only'] === 'true'  && $formPass === false)
            {
                $formClass .= ' ';
            }
            elseif ($shortCodeValues['show-form-only'] === 'true' && $formPass === true)
            {
                $formClass .= ' kps-hide-form ';
            }
            else
            {
                // Button Standard-Text durch Custom ersetzen
                if ($shortCodeValues['button-text'] === '')
                {
                    $buttonText = esc_html__('Write an entry', 'kps');
                }
                else
                {
                    $buttonText = esc_html(trim($shortCodeValues['button-text']));

                    if (empty($buttonText) OR $buttonText == "")
                    {
                        $buttonText = esc_html__('Write an entry', 'kps');
                    }
                }

                $kpsWriteButton = '
                    <div id="kps-write-button" style="text-align: center;">
                        <input type="button" class="button btn btn-primary" value="' . $buttonText . '" />
                    </div>';
                $formClass .= ' kps-hide-form ';
            }
        }
        else
        {
            // Klassenfehler
            $errorIsFound           = ($write->show_isNotFound() === false) ? true : false;
            $errorIsNotDB           = ($write->show_isInsertDB() === false) ? true : false;

            // Formularfehler kenntlich machen
            $errorAuthorName        = (isset($write->_authorName) && !empty($write->_authorName) && $write->_usernameNotExist) ? '' : 'kps-form_glowing';
            $errorAuthorSearchfor   = (is_numeric($write->_authorSearchfor) && $errorIsFound === false ) ? '' : 'kps-form_glowing';
            $errorAuthorRule        = (is_numeric($write->_authorRule) && $errorIsFound === false) ? '' : 'kps-form_glowing';
            $errorYourRule          = (is_numeric($write->_yourRule) && $errorIsFound === false) ? '' : 'kps-form_glowing';
            $errorAuthorEntry       = ((isset($write->_authorEntry) && !empty($write->_authorEntry)) && $write->_wordCount) ? '' : 'kps-form_glowing';
            $errorAuthorEmail       = ($write->_authorEmailCheck && $write->_emailNotExist) ? '' : 'kps-form_glowing';
            $errorAuthorAGBDSGVO    = ($write->_acceptedAGBDSGVO) ? '' : 'kps-form_glowing';
            $errorCaptcha           = ($captchaValid === true) ? '' : 'kps-form_glowing';


            if (isset($write->_authorRule0)) { $authorRule0 = $write->_authorRule0;} else { $authorRule0 = '';}
            if (isset($write->_authorRule1)) { $authorRule1 = $write->_authorRule1;} else { $authorRule1 = '';}

            if (isset($write->_yourRule0)) { $yourRule0 = $write->_yourRule0;} else { $yourRule0 = '';}
            if (isset($write->_yourRule1)) { $yourRule1 = $write->_yourRule1;} else { $yourRule1 = '';}
            if (isset($write->_yourRule2)) { $yourRule2 = $write->_yourRule2;} else { $yourRule2 = '';}

            if (isset($write->_authorEntry )) { $authorEntry = $write->_authorEntry;} else { $authorEntry = '';}

            if (isset($write->_acceptedAGBDSGVO) && ($write->_acceptedAGBDSGVO) === true ) { $kpsAcceptedAGBDSGVO = 'checked';} else { $kpsAcceptedAGBDSGVO = '';}

            /**
             * Meldungen ausgeben
             */
            // Datenbankverbindung gestört
            if ($errorIsNotDB === true)
            {
                $entryNotWritten = '<li>' . esc_html__('Error! Database connection disturbed!', 'kps') . '</li>';
                $entryNotWritten .= '<li>' . esc_html__('Please contact the administrator if the problem reappears!', 'kps') . '</li>';
            }

            // Eintrag vorhanden
            if ($errorIsFound === true)
            {
                $entryIsFound = '<li>' . esc_html__('There is already an entry of you with the same intentions!', 'kps') . '</li>';
            }

            // Username existiert als registierter User
            if (!empty($errorAuthorName))
            {
                $userNameExist = '<li>' . esc_html__('The entered name is already taken. Please choose another!', 'kps') . '</li>';
            }

            // Email-Adresse existiert als registierter User
            if (!empty($errorAuthorEmail))
            {
                $entyToShort = '<li>' . esc_html__('The entered email address already exists. Please choose another!', 'kps') . '</li>';
            }

            // Mindestwortanzahl nicht erreicht
            if (!empty($errorAuthorEntry))
            {
                $entyToShort = '<li>' . esc_html__('Your entry is too short. The minimum number of words is', 'kps') . ':&#160;<b>' . get_option('kps_formWordCount', false) . '</b></li>';
            }

            // AGB's und DSGVO
            if (!empty($errorAuthorAGBDSGVO))
            {
                // Wenn AGB's und DSGVO gesetzt sind
                if ($kpsUserSettings['kpsUserPrivacyAGB'] === 'true' && $kpsUserSettings['kpsUserPrivacyDSGVO'] === 'true')
                {
                    $userNotAcceptAGBDSGVO = '<li>' . esc_html__('Please accept the Terms and Conditions and GDPR!', 'kps') . '</li>';
                }

                // Wenn nur AGB's gesetzt ist
                if ($kpsUserSettings['kpsUserPrivacyAGB'] === 'true' && $kpsUserSettings['kpsUserPrivacyDSGVO'] === 'false')
                {
                    $userNotAcceptAGBDSGVO = '<li>' . esc_html__('Please accept the Terms and Conditions!', 'kps') . '</li>';
                }

                // Wenn nur DSGVO gesetzt ist
                if ($kpsUserSettings['kpsUserPrivacyAGB'] === 'false' && $kpsUserSettings['kpsUserPrivacyDSGVO'] === 'true')
                {
                    $userNotAcceptAGBDSGVO = '<li>' . esc_html__('Please accept the GDPR!', 'kps') . '</li>';
                }
            }

            // Captcha-Fehler
            if (!empty($errorCaptcha))
            {
                $captchaFail = '<li>' . esc_html__('Google reCaptcha has not been confirmed!', 'kps') . '</li>';
            }

            // Messagebox
            if ($entryIsFound === true OR $entryNotWritten === true)
            {
                $fillTheForm = '<li>' . esc_html__('Please ensure you have completed every field in the form!', 'kps') . '</li>';
            }
            $messageboxContent = '
            <ul>
                ' . $fillTheForm . '
                ' . $entryIsFound . '
                ' . $entryNotWritten . '
                ' . $userNameExist . '
                ' . $emailExist . '
                ' . $entyToShort . '
                ' . $userNotAcceptAGBDSGVO . '
                ' . $captchaFail . '
            </ul>';

            $outputmessage = kps_messagebox(esc_html__('Error! Form is incorrect!', 'kps') , $messageboxContent);
        }

        // Toggeln vom Formular, außer wenn Formularfehler ist
        $hidebutton = '';
    	if (!$formErrors) {
            if ($shortCodeValues['show-form-only'] === 'true')
            {
                $hidebutton .= ' ';
            }
            else
            {
                $hidebutton = '<button type="button" class="kps-closeFrom"><span class="screen-reader-text">' . esc_html__('Close form', 'kps') . '</span></button>';
            }
    	}

        // Ausgabe Messagebox
        $output .= $spammessage;
        $output .= $outputmessage;

        // Formular anzeigen
        $output .= apply_filters('kps-write-button', $kpsWriteButton);

        $output .= '
                        <form id="kps-new-entry" class="' . $formClass . '" action="" method="post">
                            ' . $hidebutton . '
                            <fieldset style="border: none;">
                            <div class="kps-divTable">
                                <div class="kps-divTableBody">
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorName">' . esc_html__('Name', 'kps') . '&#160;<sup><span class="kps-required">*</span></sup></label></div>
                                        <div class="kps-divTableCell"><input class="form form_field ' . $errorAuthorName . '" ' . $inputFieldDisabled . ' id="kps_authorName" name="kps_authorName" value="' . $setUserName . '" placeholder="' . esc_html__('First name, name or nickname', 'kps') . '" size="245" maxlength="245" aria-required="true" required="required" type="text"></div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorSearchfor">' . esc_html__('I am looking for', 'kps') . '&#160;<sup><span class="kps-required">*</span></sup></label></div>
                                        <div class="kps-divTableCell kps-nobr">
                                            <div class="kps-option ' . $errorAuthorSearchfor . '">
                                                <select id="kps_authorSearchfor" name="kps_authorSearchfor" class="dropdown" required>
                                                    <option value=" " disabled selected>' . esc_html__('Please choose', 'kps') . '</option>
                    ';

        // Dropdown Auswahl
        $authorSearchFor =  array(
                                "0" => kps_getFormTranslation('Hall'),
                                "1" => kps_getFormTranslation('Climbing'),
                                "2" => kps_getFormTranslation('Travels'),
                                "3" => kps_getFormTranslation('Walking'),
                                "4" => kps_getFormTranslation('Alpine tours'),
                                "5" => kps_getFormTranslation('Kayak'),
                                "6" => kps_getFormTranslation('Ferratas'),
                                "7" => kps_getFormTranslation('Mountain bike'),
                                "8" => kps_getFormTranslation('Winter sports')
                            );
        // alphabetisch sortieren
        asort($authorSearchFor);

        foreach ($authorSearchFor AS $key => $value)
        {
                if ($key == $write->_authorSearchfor AND $write->_authorSearchfor !== NULL)
                {
                    // Ausgabe
                    $output .= '<option value="' . $key. '" selected>' . $value . '</option>';                                            }
                else
                {
                    // Ausgabe
                    $output .= '<option value="' . $key. '">' . $value . '</option>';
                }
        }

        $output .= '
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr" style="vertical-align: top;"><label for="kps_authorRule">' . esc_html__('Kind of search', 'kps') . '&#160;<sup><span class="kps-required">*</span></sup></label></div>
                                        <div class="kps-divTableCell">
                                            <div class="kps-option ' . $errorAuthorRule . '">
                                                <input id="kps_authorRule0" name="kps_authorRule" value="0" aria-required="true" required="required" type="radio" ' . $authorRule0 . '><label style="display: inline-block;" for="kps_authorRule0">&#160;' . kps_getFormTranslation('Unique') . '</label><br />
                                                <input id="kps_authorRule1" name="kps_authorRule" value="1" aria-required="true" required="required" type="radio" ' . $authorRule1 . '><label style="display: inline-block;" for="kps_authorRule1">&#160;' . kps_getFormTranslation('Regularly') . '</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr" style="vertical-align: top;"><label for="kps_yourRule">' . esc_html__('I am', 'kps') . '&#160;<sup><span class="kps-required">*</span></sup></label></div>
                                        <div class="kps-divTableCell">
                                            <div class="kps-option ' . $errorYourRule . '">
                    ';
                                                // Wenn "Einzelperson" aktiviert ist
                                                if ($formOptions['kpsFormOptionSinglePerson'] === 'true')
                                                {
                                                    $output .= '<input id="kps_yourRule0" name="kps_yourRule" value="0" aria-required="true" required="required" type="radio" ' . $yourRule0 . '><label style="display: inline-block;" for="kps_yourRule0">&#160;' . kps_getFormTranslation('Single person') . '</label><br />';
                                                }
                                                // Wenn "Familie" aktiviert ist
                                                if ($formOptions['kpsFormOptionFamily'] === 'true')
                                                {
                                                    $output .= '<input id="kps_yourRule1" name="kps_yourRule" value="1" aria-required="true" required="required" type="radio" ' . $yourRule1 . '><label style="display: inline-block;" for="kps_yourRule1">&#160;' . kps_getFormTranslation('Family') . '</label><br />';
                                                }
                                                // Wenn "Club/Gruppe" aktiviert ist
                                                if ($formOptions['kpsFormOptionClubGroup'] === 'true')
                                                {
                                                    $output .= '<input id="kps_yourRule2" name="kps_yourRule" value="2" aria-required="true" required="required" type="radio" ' . $yourRule2 . '><label style="display: inline-block;" for="kps_yourRule2">&#160;' . kps_getFormTranslation('Club/Group') . '</label>';
                                                }
        $output .= '
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr" style="vertical-align: top;"><label for="kps_authorEntry">' . esc_html__('Entry', 'kps') . '&#160;<sup><span class="kps-required">*</span></sup></label></div>
                                        <div class="kps-divTableCell"><textarea class="kps-textarea ' . $errorAuthorEntry . '" id="kps_authorEntry" name="kps_authorEntry" cols="45" rows="8" maxlength="65525" aria-required="true" required="required">' . $authorEntry . '</textarea></div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"></div>
                                        <div class="kps-divTableCell"><u>' . esc_html__('How would you like to be contacted?', 'kps') . '</u></div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorEmail"><i class="far fa-envelope-open"></i>&#160;' . esc_html__('Email', 'kps') . '&#160;<sup><span class="kps-required">*</span></sup></label></div>
                                        <div class="kps-divTableCell"><input class="form_field ' . $errorAuthorEmail . '" ' . $inputFieldDisabled . ' id="kps_authorEmail" name="kps_authorEmail" value="' . $setUserEmail . '" placeholder="' . esc_html__('Email', 'kps') . '" minlength="6" size="245" maxlength="245" aria-required="true" required="required" type="' . $html5TypEmail . '"></div>
                                    </div>
                    ';

        // Wenn "Festnetz" aktiviert ist
        if ($formOptions['kpsFormOptionTelephone'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorTelephone"><i class="fas fa-phone-volume"></i>&#160;' . esc_html__('Telephone', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorTelephone" name="kps_authorTelephone" value="' . $setAuthorTelephone . '" placeholder="' . esc_html__('0351/123456', 'kps') . '" size="60" maxlength="60" type="' . $html5TypTele . '"></div>
                                    </div>
                        ';
        }

        // Wenn "Mobil" aktiviert ist
        if ($formOptions['kpsFormOptionMobile'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorMobile"><i class="fas fa-mobile-alt"></i>&#160;' . esc_html__('Mobile Phone', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorMobile" name="kps_authorMobile" value="' . $setAuthorMobile . '" placeholder="' . esc_html__('0170/123456', 'kps') . '" size="60" maxlength="60" type="' . $html5TypTele . '"></div>
                                    </div>
                        ';
        }

        // Wenn "Facebook Messenger" aktiviert ist
        if ($formOptions['kpsFormOptionFacebookMessenger'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorFacebookMessenger"><i class="fab fa-facebook-messenger"></i>&#160;' . esc_html__('Facebook', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorFacebookMessenger" name="kps_authorFacebookMessenger" value="' . $setAuthorFacebookMessenger . '" placeholder="' . esc_html__('https://m.me/[Login-Name][.xyz]', 'kps') . '" minlength="6" size="245" maxlength="245" type="' . $html5TypUrl . '"></div>
                                    </div>
                        ';
        }

        // Wenn "Hoccer" aktiviert ist
        if ($formOptions['kpsFormOptionHoccer'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorHoccer"><i class="far fa-comments"></i>&#160;' . esc_html__('Hoccer', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorHoccer" name="kps_authorHoccer" value="' . $setAuthorHoccer . '" placeholder="' . esc_html__('Email', 'kps') . '" minlength="6" size="245" maxlength="245" type="' . $html5TypEmail . '"></div>
                                    </div>
                        ';
        }

        // Wenn "Signal" aktiviert ist
        if ($formOptions['kpsFormOptionSignal'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorSignal"><i class="fas fa-signal"></i>&#160;' . esc_html__('Signal', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorSignal" name="kps_authorSignal" value="' . $setAuthorSignal . '" placeholder="' . esc_html__('0170/123456', 'kps') . '" size="60" maxlength="60" type="' . $html5TypTele . '"></div>
                                    </div>
                        ';
        }

        // Wenn "Skype" aktiviert ist
        if ($formOptions['kpsFormOptionSkype'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorSkype"><i class="fab fa-skype"></i>&#160;' . esc_html__('Skype', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorSkype" name="kps_authorSkype" value="' . $setAuthorSkype . '" placeholder="' . esc_html__('Email, mobile number or Skype username', 'kps') . '" size="245" maxlength="245" type="text"></div>
                                    </div>
                        ';
        }

        // Wenn "Telegram" aktiviert ist
        if ($formOptions['kpsFormOptionTelegram'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorTelegram"><i class="fab fa-telegram-plane"></i>&#160;' . esc_html__('Telegram', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorTelegram" name="kps_authorTelegram" value="' . $setAuthorTelegram . '" placeholder="' . esc_html__('0170/123456', 'kps') . '" size="60" maxlength="60" type="' . $html5TypTele . '"></div>
                                    </div>
                        ';
        }

        // Wenn "Threema" aktiviert ist
        if ($formOptions['kpsFormOptionThreema'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorThreema"><i class="far fa-comment-alt"></i>&#160;' . esc_html__('Threema', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorThreema" name="kps_authorThreema" value="' . $setAuthorThreema . '" placeholder="' . esc_html__('Threema-ID', 'kps') . '" size="60" maxlength="8" type="text"></div>
                                    </div>
                        ';
        }

        // Wenn "Viper" aktiviert ist
        if ($formOptions['kpsFormOptionViper'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorViper"><i class="fab fa-viber"></i>&#160;' . esc_html__('Viper', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorViper" name="kps_authorViper" value="' . $setAuthorViper . '" placeholder="' . esc_html__('0170/123456', 'kps') . '" size="60" maxlength="60" type="' . $html5TypTele . '"></div>
                                    </div>
                        ';
        }

        // Wenn "Whatsapp" aktiviert ist
        if ($formOptions['kpsFormOptionWhatsapp'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorWhatsapp"><i class="fab fa-whatsapp"></i>&#160;' . esc_html__('WhatsApp', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorWhatsapp" name="kps_authorWhatsapp" value="' . $setAuthorWhatsapp . '" placeholder="' . esc_html__('0170/123456', 'kps') . '" size="60" maxlength="60" type="' . $html5TypTele . '"></div>
                                    </div>
                        ';
        }

        // Wenn "Wire" aktiviert ist
        if ($formOptions['kpsFormOptionWire'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorWire"><i class="far fa-comment-dots"></i>&#160;' . esc_html__('Wire', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorWire" name="kps_authorWire" value="' . $setAuthorWire . '"placeholder="' . esc_html__('Email or mobile number', 'kps') . '" size="245" maxlength="245" type="text"></div>
                                    </div>
                        ';
        }

        // Wenn "Autor-Web" aktiviert ist
        if ($formOptions['kpsFormOptionWebsite'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorWebsite"><i class="fas fa-globe-asia"></i>&#160;' . esc_html__('Website', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorWebsite" name="kps_authorWebsite" value="' . $setAuthorWebsite . '" placeholder="' . esc_html__('https://www.your-website.com', 'kps') . '" size="245" maxlength="245" type="' . $html5TypUrl . '"></div>
                                    </div>
                        ';
        }

        // Wenn "Facebook" aktiviert ist
        if ($formOptions['kpsFormOptionFacebook'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorFacebook"><i class="fab fa-facebook-f"></i>&#160;' . esc_html__('Facebook', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorFacebook" name="kps_authorFacebook" value="' . $setAuthorFacebook . '" placeholder="' . esc_html__('https://facebook.com/[Login-Name][.xyz]', 'kps') . '" size="245" maxlength="245" type="' . $html5TypUrl . '"></div>
                                    </div>
                        ';
        }

        // Wenn "Instagram" aktiviert ist
        if ($formOptions['kpsFormOptionInstagram'] === 'true')
        {
            $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"><label for="kps_authorInstagram"><i class="fab fa-instagram"></i>&#160;' . esc_html__('Instagram', 'kps') . '</label></div>
                                        <div class="kps-divTableCell"><input class="form_field" id="kps_authorInstagram" name="kps_authorInstagram" value="' . $setAuthorInstagram . '" placeholder="' . esc_html__('https://instagram.com/[Login-Name]', 'kps') . '" size="245" maxlength="245" type="' . $html5TypUrl . '"></div>
                                    </div>
                        ';
        }

        // Wenn "Captcha" aktiviert ist
        if ($captchaIsActivated === 'true')
        {
            $output .= '
                                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-nobr"></div>
                                        <div class="kps-divTableCell">
                                            <div class="g-recaptcha ' . $errorCaptcha . '" data-sitekey="' . $captchaSiteKey . '"></div>
                                        </div>
                                    </div>
                        ';
        }

        // Wenn "AGB's und/oder DSGVO" aktiv
        if ($kpsUserSettings['kpsUserPrivacyAGB'] === 'true' OR $kpsUserSettings['kpsUserPrivacyDSGVO'] === 'true')
        {
            $output .= $formCheckboxAGBDSGVO;
        }
            $output .= '
                                    <div class="kps-br"></div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell" style="visibility: hidden; display: none !important;"></div>
                                        <div class="kps-divTableCell" style="visibility: hidden; display: none !important;">
                                            <input type="hidden" value="' . $timestamp . '" type="text" id="kps_spamtimer1" name="kps_spamtimer1" />
                            				<input type="hidden" value="' . $timestamp . '" type="text" id="kps_spamtimer2" name="kps_spamtimer2" />
                                        </div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell"></div>
                                        <div class="kps-divTableCell">
                                            <input type="hidden" id="kps_AuthorId" name="kps_authorId" value="' . $setUserId . '" />
                                            <input type="hidden" id="kps_AuthorMadeEntryToken" name="kps_AuthorMadeEntryToken" value="' . $token . '" />
                                            <input class="submit" type="submit" name="submit" value="' . esc_html__('Send', 'kps') . '">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </fieldset>
                        <div class="kps-br"></div>
                        <div class="kps-form-info" style="display: inline-block; margin: 0; padding: 0; line-height: 1.2em;">
                            <ul>
                                <li><sup><span class="kps-required">*</span></sup>&#160;' . esc_html__('Required fields', 'kps') . '</li>
                                <li>' . esc_html__('As a registered user, the registered login / display name and the corresponding email address are automatically used. This can not be changed!', 'kps') . '</li>
                                <li>' . esc_html__('All contact details, except those that may be publicly accessible in the user profile, are not published, but can only be accessed!', 'kps') . '</li>
                                <li>' . esc_html__('It could be that the entry only becomes visible after we have approved it!', 'kps') . '</li>
                                <li>' . esc_html__('We reserve the right to edit, delete or not publish entries!', 'kps') . '</li>
                                <li>' . sprintf(esc_html__('Your entry is visible for a maximum of %d days.', 'kps'), get_option('kps_deleteEntryTime', false) / 24 / 60 / 60) . '</li>
                                <li>' . sprintf(esc_html__('Entries that have not been activated are automatically deleted after %d days.', 'kps'), get_option('kps_deleteNoEntryTime', false) / 24 / 60 / 60) . '</li>
                            </ul>
                        </div>
                        </form>';
    }

    $output .= '</div>';

    // HTML5
    if ($html5)
    {
        $output .= '</article>';
        $output .= '</main>';
    }

    // Ausgabe generieren
    return $output; // Rückgabe
}