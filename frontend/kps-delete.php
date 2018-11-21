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
 * Löschung
 */
function kps_deletecode($deleteCode = '0')
{
    // Token erstellen
    $token = wp_create_nonce('kpsdeleteToken');

    // Hole Formulareinstellungen für Captcha
    $captchaIsActivated = get_option('kps_captcha', false);
    $captchaKeys        = kps_unserialize(get_option('kps_captchakeys', false));

    if (isset($_POST['submit']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kps_DeletePassword',
            'kps_dkey',
            'g-recaptcha-response',
            'kps_DeleteToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification   = wp_verify_nonce($postVars['kps_DeleteToken'], 'kpsdeleteToken');

        // Deletecode escapten
        $authorPassword = kps_sanitize_field($postVars['kps_DeletePassword']);
    }

    // Prüfung des Captcha durch https://www.google.com/recaptcha/api/siteverify
    if ($captchaIsActivated === 'true')
    {
        $captchaSiteKey = esc_html($captchaKeys['kpsCaptchaSiteKey']);
        $captchaSecretKey = esc_html($captchaKeys['kpsCaptchaSecretKey']);
        $captchaUrl = 'https://www.google.com/recaptcha/api/siteverify';

        if (isset($_POST['submit']))
        {
            $captchaData = array(
                'secret'    => $captchaSecretKey,
                'response'  => $postVars['g-recaptcha-response']
            );
            $captchaOptions = array(
                'http'      => array(
                'method'    => 'POST',
                'content'   => http_build_query($captchaData)
                )
            );

            $captchaContext = stream_context_create($captchaOptions);
            $captchaVerify = file_get_contents($captchaUrl, false, $captchaContext);
            $captchaSuccess = json_decode($captchaVerify);
            $captchaValid = $captchaSuccess->success;
        }
    }
    else
    {
        $captchaValid = true;
    }

    // Eintrag löschen
    if ($verification == true
        && isset($_POST['submit'])
        && isset($authorPassword)
        && !empty($authorPassword)
        && strlen($postVars['kps_dkey']) == 32
        && $captchaValid === true)
    {
        // Löschung
        $deleteCode = new kps_delete($postVars['kps_dkey'], $authorPassword);

        // Wenn Klasse meldet, das Eintrag gelöscht wurde
        if ($deleteCode->show_isDelete())
        {
            // Messagebox Ausgabe
            $messageboxContent = '
            <div style="text-align: center;">' . esc_html(__('Vielen Dank! Bis zum nächsten mal!', 'kps')) . '</div>
            ';

            $output = kps_messagebox(esc_html(__('Eintrag wurde gelöscht!', 'kps')) , $messageboxContent);
        }
        elseif ($deleteCode->show_isDelete() === false && $deleteCode->show_isFound() === false)
        {
            // Messagebox Ausgabe
            $messageboxContent = '
            <div style="text-align: center; color: red">' . esc_html(__('Lösch-Schlüssel unbekannt!', 'kps')) . '</div>
            ';

            // Klasse meldet Fehler
            $output = kps_messagebox(esc_html(__('Eintrag konnte nicht gelöscht werden!', 'kps')) , $messageboxContent);
        }
        else
        {
            // Messagebox Ausgabe
            $messageboxContent = '
            <div style="text-align: center; color: red">' . esc_html(__('Bitte kontaktiere den Administrator, falls das Problem erneut erscheint!', 'kps')) . '</div>
            ';

            // Klasse meldet Fehler
            $output = kps_messagebox(esc_html(__('Eintrag konnte nicht gelöscht werden!', 'kps')) , $messageboxContent);
        }
    }
    elseif (($verification == false
            OR $captchaValid === false
            OR isset($authorPassword)
            OR empty($authorPassword)
            OR strlen($postVars['kps_dkey']) != 32
            )
            && isset($_POST['submit']) )
    {
            if ($captchaIsActivated === 'true' && $captchaValid === false)
            {
                $captchaFail =  '<li>' . esc_html(__('Google reCaptcha wurde nicht bestätigt!', 'kps')) . '</li>';
            }

            // Messagebox Ausgabe
            $messageboxContent = '
            <ul>
                <li>' . esc_html(__('Du hast dein Passwort falsch eingegeben. Achten bitte auf Groß-/ und Kleinschreibung!', 'kps')) . '</li>
                ' . $captchaFail . '
                <li>' . esc_html(__('Bitte kontaktiere den Administrator, falls das Problem erneut erscheint!', 'kps')) . '</li>
            </ul>';

            // Verifizierung falsch
            $output = kps_messagebox(esc_html(__('Eintrag konnte nicht gelöscht werden!', 'kps')) , $messageboxContent);

            // Löschformular
            $output .= kps_deletebox($token, $deleteCode, $captchaIsActivated, $captchaSiteKey);
    }
    else
    {
        // Löschformular
        $output = kps_deletebox($token, $deleteCode, $captchaIsActivated, $captchaSiteKey);

    }

    return $output; // Rückgabe

}