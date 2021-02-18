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
if (strpos($_SERVER['PHP_SELF'], basename(__file__)))
{
    wp_die(__('No direct calls allowed!'));
}

/**
 * Anforderung Registierung
 */
function kps_verifyId($kps_data = '0')
{
    // Token erstellen
    $token = wp_create_nonce('kpsVerifyToken');

    // Hole die derzeitige Post-ID von Wordpress
    $pageUrl = get_post_permalink();

    // Hole Usereinstellungen
    $kpsUserSettings = kps_unserialize(get_option('kps_userSettings', false));

    // Hole Formulareinstellungen für Captcha
    $captchaIsActivated = get_option('kps_captcha', false);
    $captchaKeys        = kps_unserialize(get_option('kps_captchakeys', false));

    if (isset($_POST['submit']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kps_VerifyEmail',
            'kps_data',
            'g-recaptcha-response',
            'kps_VerifyToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kps_VerifyToken'], 'kpsVerifyToken');

        // ID escapten
        $kps_data = ($postVars['kps_data'] > '0') ? absint($postVars['kps_data']) : 0;
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

    // Wenn User eingeloggt ist, hole seinen Daten uns Schreibe Sie in die Formularfelder
    $current_user = wp_get_current_user();
    if ($current_user->id > 0)
    {
        $setUserEmail       = $current_user->user_email;
        $inputFieldDisabled = 'readonly="readonly"';
    }
    else
    {
        $setUserEmail = sanitize_email($postVars['kps_VerifyEmail']);
    }

    // Wenn Option Registierungspflicht
    if (is_user_logged_in() !== true
        && $kpsUserSettings['kpsUserRequirementRegistration'] === 'true')
    {
        // Wordpress Registieren
        $output .= '<div>
                        <div style="text-align: center;">
                            <h6>' . esc_html__('You need to register to require contact details!', 'kps') . '</h6>
                        </div>
                        <div style="text-align: center;">
                            <a href="' . esc_url(wp_login_url(get_permalink())) . '">' . esc_html__('Login', 'kps') . '</a>&#160;&#124;&#160;
                            <a href="' . esc_url(wp_registration_url()) . '">' . esc_html__('Registration', 'kps') . '</a>
                        </div>
                    </div>';
    }
    else
    {
        // // Verifizierung
        if ($verification == true
            && !empty($setUserEmail)
            && is_email($setUserEmail) !== false
            && is_numeric($kps_data)
            && $kps_data > '0'
            && isset($_POST['submit'])
            && $captchaValid === true)
        {
            // Variabeln an Klasse übergeben
            $verify = new kps_verify($postVars['kps_data'], $setUserEmail, $pageUrl);

            // Wenn Klasse meldet, das Email versendet wurde
            if ($verify->show_isSend())
            {
                // Messagebox Ausgabe
                $messageboxContent = '
                <ul>
                    <li>' . esc_html__('If you have not received any email from us, it may also be in your junk folder!', 'kps') . '</li>
                    <li>' . esc_html__('Please contact the administrator if the problem reappears!', 'kps') . '</li>
                </ul>';

                $output = kps_messagebox(esc_html__('Verification sent!', 'kps'), $messageboxContent);
            }
            elseif ($verify->show_isSend() === false && $verify->show_isFound() === false)
            {
                // Messagebox Ausgabe
                $messageboxContent = '
                <div style="text-align: center; color: red">' . esc_html__('The entry has been deleted!', 'kps') . '</div>
                ';

                // Klasse meldet Eintrag nicht vorhanden
                $output = kps_messagebox(esc_html__('Error! Entry not available!', 'kps') , $messageboxContent);
            }
            elseif ($verify->show_isSend() === false && $verify->show_isInsertDB() === false )
            {
                // Messagebox Ausgabe
                $messageboxContent = '
                <div style="text-align: center; color: red">' . esc_html__('Please contact the administrator if the problem reappears!', 'kps') . '!</div>
                ';

                // Eintrag ist schon in Datenbank verhanden
                $output = kps_messagebox(esc_html__('Error! Database connection disturbed!', 'kps') , $messageboxContent);

                $formClass .= ' kps-hide-form ';
            }
            else
            {
                // Messagebox Ausgabe
                $messageboxContent = '
                <div style="text-align: center; color: red">' . esc_html__('Please contact the administrator if the problem reappears!','kps') . '</div>
                ';

                // Klasse meldet Fehler
                $output = kps_messagebox(esc_html__('Error! Verification-Email could not be sent!', 'kps'), $messageboxContent);
            }

        }
        elseif (($verification == false
                OR is_email($setUserEmail) === false
                OR $captchaValid === false)
                && isset($_POST['submit']))
        {
            if ($captchaIsActivated === 'true' && $captchaValid === false)
            {
                $captchaFail =  '<li>' . esc_html__('Google reCaptcha has not been confirmed!', 'kps') . '</li>';
            }

            // Messagebox Ausgabe
            $messageboxContent = '
            <ul>
                <li>' . esc_html__('The data can only be retrieved once!', 'kps') . '</li>
                <li>' . esc_html__('The Requirement-ID is wrong!', 'kps') . '</li>
                ' . $captchaFail . '
                <li>' . esc_html__('Please contact the administrator if the problem reappears!', 'kps') . '</li>
            </ul>';

            // Verifizierungfehler
            $output = kps_messagebox(esc_html__('Error! Verification-Email could not be sent!', 'kps'), $messageboxContent);

            // Anforderungsformular
            $output .= kps_verifybox($token, $kps_data, $setUserEmail, $inputFieldDisabled, $captchaIsActivated, $captchaSiteKey);
        }
        else
        {
            // Verifizierungsformular
            $output .= kps_verifybox($token, $kps_data, $setUserEmail, $inputFieldDisabled, $captchaIsActivated, $captchaSiteKey);
        }
    }

    return $output; // Rückgabe

}
