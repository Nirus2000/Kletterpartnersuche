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
 * Eintrag melden
 */
function kps_report($reportId = '0')
{
    global $wpdb;

    // Token erstellen
    $token = wp_create_nonce('kpsReportEntryToken');

    // Hole Formulareinstellungen für Captcha
    $captchaIsActivated = get_option('kps_captcha', false);
    $captchaKeys        = kps_unserialize(get_option('kps_captchakeys', false));

    if (isset($_POST['submit']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kps_reportEntryChoice',
            'kps_report',
            'g-recaptcha-response',
            'kps_ReportEntryToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kps_ReportEntryToken'], 'kpsReportEntryToken');

        // Positiv-Numerisch
        $reportChoice = absint($postVars['kps_reportEntryChoice']);

        // ID escapten
        $kps_report = ($postVars['kps_report'] > '0') ? absint($postVars['kps_report']) : 0;
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

    // Eintrag melden
    if ($verification == true
        && is_numeric($kps_report)
        && $kps_report > '0'
        && is_numeric($reportChoice)
        && isset($_POST['submit'])
        && $captchaValid === true)
    {
        // Report
        $report = new kps_reporting($kps_report, $reportChoice);

        if ($report->show_isReport())
        {
            // Messagebox Ausgabe
            $messageboxContent = '
            <div style="text-align: center;">' . esc_html__('We will process your message as soon as possible!', 'kps') . '</div>
            ';
            $output = kps_messagebox(esc_html__('Entry reported', 'kps') , $messageboxContent);
        }
        elseif ($report->show_isReport() === false && $report->show_isFound() === false)
        {
            // Messagebox Ausgabe
            $messageboxContent = '
            <div style="text-align: center; color: red">' . esc_html__('The entry has been deleted!', 'kps') . '</div>
            ';

            // Klasse meldet Eintrag nicht vorhanden
            $output = kps_messagebox(esc_html__('Error! Entry not available!', 'kps') , $messageboxContent);
        }
        else
        {
            // Messagebox Ausgabe
            $messageboxContent = '
            <div style="text-align: center; color: red">' . esc_html__('Please contact the administrator if the problem reappears!', 'kps') . '</div>
            ';

            // Klasse meldet Fehler
            $output = kps_messagebox(esc_html__('Error! Entry could not be reported!', 'kps') , $messageboxContent);
        }
    }
    elseif (($verification == false
            OR $captchaValid === false
            OR $kps_report < 0
            OR !is_numeric($kps_report)
            OR !is_numeric($reportChoice)
            )
            && isset($_POST['submit']))
    {
            if ($captchaIsActivated === 'true' && $captchaValid === false)
            {
                $captchaFail =  '<li>' . esc_html__('Google reCaptcha has not been confirmed!', 'kps') . '</li>';
            }

            // Messagebox Ausgabe
            $messageboxContent = '
            <ul>
                <li>' . esc_html__('No message reason was selected!', 'kps') . '</li>
                <li>' . esc_html__('The Entry-ID is wrong!', 'kps') . '</li>
                ' . $captchaFail . '
                <li>' . esc_html__('Please contact the administrator if the problem reappears!', 'kps') . '</li>
            </ul>';

            // Verifizierung falsch
            $output = kps_messagebox(esc_html__('Error! Entry could not be reported!', 'kps') , $messageboxContent);

            // Meldeformular
            $output .= kps_reportbox($token, $reportId, $captchaIsActivated, $captchaSiteKey);
    }
    else
    {
        // Meldeformular
        $output = kps_reportbox($token, $reportId, $captchaIsActivated, $captchaSiteKey);
    }

    return $output; // Rückgabe
}