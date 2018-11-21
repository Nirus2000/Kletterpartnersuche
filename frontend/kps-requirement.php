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
 * Anforderung
 */
function kps_requirement($kps_require = '0')
{
    // Token erstellen
    $token = wp_create_nonce('kpsRequirementToken');

    // Hole Usereinstellungen
    $checkedUserSettings = kps_unserialize(get_option('kps_userSettings', false));

    // Hole Formulareinstellungen für Captcha
    $captchaIsActivated = get_option('kps_captcha', false);
    $captchaKeys        = kps_unserialize(get_option('kps_captchakeys', false));

    if (isset($_POST['submit']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kps_RequirementPassword',
            'g-recaptcha-response',
            'kps_require',
            'kps_RequirementToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kps_RequirementToken'], 'kpsRequirementToken');

        // ID escapten
        $kps_require = ($postVars['kps_require'] > '0') ? absint($postVars['kps_require']) : 0;

        // Password escapen
        $requirePassword = kps_sanitize_field($postVars['kps_RequirementPassword']);
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

    // Wenn Option Registierungspflicht
    if (is_user_logged_in() !== true
        && $checkedUserSettings['kpsUserRequirementRegistration'] === 'true')
    {
        // Wordpress Registieren
        $output .= '<div>
                        <div style="text-align: center;">
                            <h6>' . esc_html(__('Du musst eingeloggt sein, um Kontaktdaten abzurufen!', 'kps')) . '</h6>
                        </div>
                        <div style="text-align: center;">
                            <a href="' . esc_url(wp_login_url(get_permalink())) . '">' . esc_html(__('Login', 'kps')) . '</a>&#160;&#124;&#160;
                            <a href="' . esc_url(wp_registration_url()) . '">' . esc_html(__('Registrierung', 'kps')) . '</a>
                        </div>
                    </div>';
    }
    else
    {
        // Anforderung
        if ($verification == true
            && isset($_POST['submit'])
            && is_numeric($kps_require)
            && $kps_require > '0'
            && !empty($requirePassword)
            && $captchaValid === true)
        {
            // Variabeln an Klasse übergeben
            $requirement = new kps_requirement($postVars['kps_require'], $requirePassword);

            // Wenn Klasse meldet, das Email versendet wurde
            if ($requirement->show_isSend())
            {
                // Messagebox Ausgabe
                $messageboxContent = '
                <div style="text-align: center;">' . esc_html(__('Vielen Dank!', 'kps')) . '</div>
                ';

                $output = kps_messagebox(esc_html(__('Kontaktdaten versendet!', 'kps')) , $messageboxContent);
            }
            elseif ($requirement->show_isSend() === false && $requirement->show_isFound() === false)
            {
                // Messagebox Ausgabe
                $messageboxContent = '
                <div style="text-align: center; color: red">' . esc_html(__('Der Eintrag wurde gelöscht!', 'kps')) . '</div>
                ';

                // Klasse meldet Eintrag nicht vorhanden
                $output = kps_messagebox(esc_html(__('Fehler! Eintrag nicht vorhanden!', 'kps')) , $messageboxContent);
            }
            elseif ($requirement->show_isSend() === false && $requirement->show_isClosed() === true)
            {
                // Messagebox Ausgabe
                $messageboxContent = '
                <div style="text-align: center; color: red">' . esc_html(__('Die Daten können nur einmalig abgerufen werden!', 'kps')) . '</div>
                ';

                // Klasse meldet keine Verifizierung
                $output = kps_messagebox(esc_html(__('Fehler! Anforderung!', 'kps')) , $messageboxContent);
            }
            elseif ($requirement->show_isSend() === false && $requirement->show_isExpire() === true)
            {
                // Messagebox Ausgabe
                $messageboxContent = '
                <div style="text-align: center; color: red">' . esc_html(__('Der Anforderungsschlüssel ist abgelaufen! Rufe die Kontaktdaten erneut ab!', 'kps')) . '</div>
                ';

                // Klasse meldet keine Verifizierung
                $output = kps_messagebox(esc_html(__('Fehler! Anforderung!', 'kps')) , $messageboxContent);
            }
            elseif ($requirement->show_isSend() === false && $requirement->show_isVerify() === false)
            {
                // Messagebox Ausgabe
                $messageboxContent = '
                <ul>
                    <li>' . esc_html(__('Du hast dein Passwort falsch eingegeben. Achten bitte auf Groß-/ und Kleinschreibung!', 'kps')) . '</li>
                    <li>' . esc_html(__('Bitte kontaktiere den Administrator, falls das Problem erneut erscheint!', 'kps')) . '</li>
                </ul>';

                // Klasse meldet keine Verifizierung
                $output = kps_messagebox(esc_html(__('Fehler! Anforderungsschlüssel!', 'kps')) , $messageboxContent);
            }
            else
            {
                // Messagebox Ausgabe
                $messageboxContent = '
                <div style="text-align: center; color: red">' . esc_html(__('Bitte kontaktiere den Administrator, falls das Problem erneut erscheint!', 'kps')) . '</div>
                ';

                // Klasse meldet Fehler
                $output = kps_messagebox(esc_html(__('Fehler! Kontaktdaten konnte nicht versendet werden!', 'kps')) , $messageboxContent);
            }

        }
        elseif (($verification == false
                OR $captchaValid === false)
                && isset($_POST['submit']))
        {
            if ($captchaIsActivated === 'true' && $captchaValid === false)
            {
                $captchaFail =  '<li>' . esc_html(__('Google reCaptcha wurde nicht bestätigt!', 'kps')) . '</li>';
            }

            // Messagebox Ausgabe
            $messageboxContent = '
            <ul>
                <li>' . esc_html(__('Das eingegebene Passwort ist ungültig!', 'kps')) . '</li>
                <li>' . esc_html(__('Die Anforderungs-ID ist falsch!', 'kps')) . '</li>
                ' . $captchaFail . '
                <li>' . esc_html(__('Bitte kontaktiere den Administrator, falls das Problem erneut erscheint!', 'kps')) . '</li>
            </ul>';

            // Verifizierung oder Email falsch
            $output = kps_messagebox(esc_html(__('Fehler! Kontaktdaten konnte nicht versendet werden!', 'kps')) , $messageboxContent);

            // Anforderungsformular
            $output .= kps_requirementbox($token, $kps_require, $captchaIsActivated, $captchaSiteKey);
        }
        else
        {
            // Anforderungsformular
            $output .= kps_requirementbox($token, $kps_require, $captchaIsActivated, $captchaSiteKey);
        }
    }

    return $output; // Rückgabe

}

