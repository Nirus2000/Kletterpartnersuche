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
 * Meldungsbox
 */
if (!function_exists( 'kps_reportbox')) {
    function kps_reportbox($token, $reportId, $captchaIsActivated, $captchaSiteKey) {

        // Template unterstützt HTML5
        $html5 = current_theme_supports('html5');

        // Ausgabe Start
        // HTML5
        if ($html5)
        {
            $output = '<main>';
            $output .= '<article>';
        }
        else
        {
            $output = '';
        }

        $output .= '<div>
                        <form id="kps-new-report" action="" method="post">
                            <div class="kps-divTable">
                                <div class="kps-divTableBody">
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <div class="kps-message" style="text-align: center;"><b>' . esc_html__('Report an entry', 'kps') . '!</b></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-divTable">
                                            	<div class="kps-divTableBody">
                                            		<div class="kps-divTableRow">
                                            			<div class="kps-divTableCell" style="text-align: right; width: 40%;"><input id="kps_reportEntry0" name="kps_reportEntryChoice" value="0" type="radio"><label for="kps_reportEntry0"></div>
                                            			<div class="kps-divTableCell" style="text-align: left; width: 60%;"><label for="kps_reportEntry0">&#160;' . esc_html__('Spam/Advertising', 'kps') . '</label></div>
                                            		</div>
                                            		<div class="kps-divTableRow">
                                            			<div class="kps-divTableCell" style="text-align: right; width: 40%;"><input id="kps_reportEntry1" name="kps_reportEntryChoice" value="1" type="radio"><label for="kps_reportEntry1"></div>
                                            			<div class="kps-divTableCell" style="text-align: left; width: 60%;"><label for="kps_reportEntry1">&#160;' . esc_html__('Inappropriate/Violence', 'kps') . '</label></div>
                                            		</div>
                                            		<div class="kps-divTableRow">
                                            			<div class="kps-divTableCell" style="text-align: right; width: 40%;"><input id="kps_reportEntry2" name="kps_reportEntryChoice" value="2" type="radio"><label for="kps_reportEntry2"></div>
                                            			<div class="kps-divTableCell" style="text-align: left; width: 60%;"><label for="kps_reportEntry2">&#160;' . esc_html__('Double entry', 'kps') . '</label></div>
                                            		</div>
                                            		<div class="kps-divTableRow">
                                            			<div class="kps-divTableCell" style="text-align: right; width: 40%;"><input id="kps_reportEntry3" name="kps_reportEntryChoice" value="3" type="radio"><label for="kps_reportEntry3"></div>
                                            			<div class="kps-divTableCell" style="text-align: left; width: 60%;"><label for="kps_reportEntry3">&#160;' . esc_html__('Personality rights', 'kps') . '</label></div>
                                            		</div>
                                            		<div class="kps-divTableRow">
                                            			<div class="kps-divTableCell" style="text-align: right; width: 40%;"><input id="kps_reportEntry4" name="kps_reportEntryChoice" value="4" type="radio"><label for="kps_reportEntry4"></div>
                                            			<div class="kps-divTableCell" style="text-align: left; width: 60%;"><label for="kps_reportEntry4">&#160;' . esc_html__('Others', 'kps') . '</label></div>
                                            		</div>
                                            	</div>
                                            </div>
                                        </div>
                                    </div>
                                    ';
        // Wenn Captcha aktiviert ist
        if ($captchaIsActivated === 'true')
        {
            $output .= '
                                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <div class="g-recaptcha" data-sitekey="' . $captchaSiteKey . '"></div>
                                            </div>
                                        </div>
                                    </div>';
        }
        $output .=                 '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <input type="hidden" id="kps_report" name="kps_report" value="' . $reportId . '" />
                                                <input type="hidden" id="kps_ReportEntryToken" name="kps_ReportEntryToken" value="' . $token . '" />
                                                <input class="submit" type="submit" name="submit" value="' . esc_html__('Report an entry', 'kps') . '!">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>';
        // Trennung
        $output .= '<div class="kps-br"></div>';

        // HTML5
        if ($html5)
        {
            $output .= '</article>';
            $output .= '</main>';
        }

        return $output; // Rückgabe
    }
}

/**
 * Anforderungsbox
 */
if ( !function_exists( 'kps_requirementbox' ) ) {
    function kps_requirementbox($token, $kps_require, $captchaIsActivated, $captchaSiteKey) {

        // Template unterstützt HTML5
        $html5 = current_theme_supports('html5');

        // Ausgabe Start
        // HTML5
        if ($html5)
        {
            $output = '<main>';
            $output .= '<article>';
        }
        else
        {
            $output = '';
        }

        $output .= '<div>
                        <form id="kps-requirement" action="" method="post">
                            <div class="kps-divTable">
                                <div class="kps-divTableBody">
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <div class="kps-message" style="text-align: center;"><b>' . esc_html__('Send contact information', 'kps') . '</b></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-br"></div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <label for="kps_RequirementPassword">' . esc_html__('Password', 'kps') . '</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <label for="kps_RequirementPassword"></label>
                                                <input class="form_field" style="text-align: center;" id="kps_RequirementPassword" name="kps_RequirementPassword" placeholder="' . esc_html__('Password', 'kps') . '" size="30" aria-required="true" required="required" type="text">
                                            </div>
                                        </div>
                                    </div>';
        // Wenn Captcha aktiviert ist
        if ($captchaIsActivated === 'true')
        {
            $output .= '
                                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <div class="g-recaptcha" data-sitekey="' . $captchaSiteKey . '"></div>
                                            </div>
                                        </div>
                                    </div>';
        }
        $output .=                 '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-br"></div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <input type="hidden" id="kps_require" name="kps_require" value="' . $kps_require . '" />
                                                <input type="hidden" id="kps_RequirementToken" name="kps_RequirementToken" value="' . $token . '" />
                                                <input class="submit" type="submit" name="submit" value="' . esc_html__('Requirement', 'kps') . '!">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>';
        // Trennung
        $output .= '<div class="kps-br"></div>';

        // HTML5
        if ($html5)
        {
            $output .= '</article>';
            $output .= '</main>';
        }

        return $output; // Rückgabe
    }
}

/**
 * Verifizierungsbox
 */
if ( !function_exists( 'kps_verifybox' ) ) {
    function kps_verifybox($token, $kps_data, $setUserEmail, $inputFieldDisabled, $captchaIsActivated, $captchaSiteKey) {

        // Template unterstützt HTML5
        $html5 = current_theme_supports('html5');

        // Ausgabe Start
        // HTML5
        if ($html5)
        {
            $output = '<main>';
            $output .= '<article>';
        }
        $html5TypEmail = isset($html5) ? 'email' : 'text';

        $output .= '<div>
                        <form id="kps-verify" action="" method="post">
                            <div class="kps-divTable">
                                <div class="kps-divTableBody">
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <div class="kps-message" style="text-align: center;"><b>' . esc_html__('Request contact information', 'kps') . '!</b></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-br"></div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <label for="kps_VerifyEmail">' . esc_html__('Email', 'kps') . '</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <input class="form_field" style="text-align: center;" ' . $inputFieldDisabled . ' id="kps_VerifyEmail" name="kps_VerifyEmail" size="5" maxlength="245" value="' . $setUserEmail . '" placeholder="' . esc_html__('Email', 'kps') . '" aria-required="true" required="required" type="' . $html5TypEmail . '">
                                            </div>
                                        </div>
                                    </div>';
        // Wenn Captcha aktiviert ist
        if ($captchaIsActivated === 'true')
        {
            $output .= '
                                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <div class="g-recaptcha" data-sitekey="' . $captchaSiteKey . '"></div>
                                            </div>
                                        </div>
                                    </div>';
        }
        $output .=  '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-br"></div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <input type="hidden" id="kps_data" name="kps_data" value="' . $kps_data . '" />
                                                <input type="hidden" id="kps_VerifyToken" name="kps_VerifyToken" value="' . $token . '" />
                                                <input class="submit" type="submit" name="submit" value="' . esc_html__('Requirement', 'kps') . '!">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>';

        // Trennung
        $output .= '<div class="kps-br"></div>';

        // HTML5
        if ($html5)
        {
            $output .= '</article>';
            $output .= '</main>';
        }

        return $output; // Rückgabe
    }
}

/**
 * Aktivierungsbox
 */
if ( !function_exists( 'kps_activationbox' ) ) {
    function kps_activationbox($token, $activationCode, $authorEmail, $captchaIsActivated, $captchaSiteKey) {

        // Template unterstützt HTML5
        $html5 = current_theme_supports('html5');

        // Ausgabe Start
        // HTML5
        if ($html5)
        {
            $output = '<main>';
            $output .= '<article>';
        }
        else
        {
            $output = '';
        }
        $html5TypEmail  = isset($html5) ? 'email' : 'text';

        $output .= '<div>
                        <form id="kps-new-activation" action="" method="post">
                            <div class="kps-divTable">
                                <div class="kps-divTableBody">
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <div class="kps-message" style="text-align: center;"><b>' . esc_html__('Activate entry', 'kps') . '</b></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <label for="kps_AuthorEmail"></label>
                                                <input class="form_field" style="text-align: center;" id="kps_AuthorEmail" name="kps_AuthorEmail" placeholder="' . esc_html__('Email', 'kps') . '" minlength="6" size="245" maxlength="245" aria-required="true" required="required" type="' . $html5TypEmail . '">
                                            </div>
                                        </div>
                                    </div>';
        // Wenn Captcha aktiviert ist
        if ($captchaIsActivated === 'true')
        {
            $output .= '
                                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <div class="g-recaptcha" data-sitekey="' . $captchaSiteKey . '"></div>
                                            </div>
                                        </div>
                                    </div>';
        }
        $output .= '
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-br"></div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <input type="hidden" id="kps_akey" name="kps_akey" value="' . $activationCode . '" />
                                                <input type="hidden" id="kps_ActivationToken" name="kps_ActivationToken" value="' . $token . '" />
                                                <input class="submit" type="submit" name="submit" value="' . esc_html__('Activate entry', 'kps') . '!">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>';
        // Trennung
        $output .= '<div class="kps-br"></div>';

        // HTML5
        if ($html5)
        {
            $output .= '</article>';
            $output .= '</main>';
        }

        return $output; // Rückgabe
    }
}

/**
 * Loschbox
 */
if ( !function_exists( 'kps_deletebox' ) ) {
    function kps_deletebox($token, $activationHash, $captchaIsActivated, $captchaSiteKey) {

        // Template unterstützt HTML5
        $html5 = current_theme_supports('html5');

        // Ausgabe Start
        // HTML5
        if ($html5)
        {
            $output = '<main>';
            $output .= '<article>';
        }
        else
        {
            $output = '';
        }

        $output .= '<div>
                        <form id="kps-delete" action="" method="post">
                            <div class="kps-divTable">
                                <div class="kps-divTableBody">
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <div class="kps-message" style="text-align: center;"><b>' . esc_html__('Delete entry', 'kps') . '</b></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <label for="kps_DeletePassword"></label>
                                                <input class="form_field" style="text-align: center;" id="kps_DeletePassword" name="kps_DeletePassword" placeholder="' . esc_html__('Password', 'kps') . '" size="30" aria-required="true" required="required" type="text">
                                            </div>
                                        </div>
                                    </div>';
        // Wenn Captcha aktiviert ist
        if ($captchaIsActivated === 'true')
        {
            $output .= '
                                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <div class="g-recaptcha" data-sitekey="' . $captchaSiteKey . '"></div>
                                            </div>
                                        </div>
                                    </div>';
        }
        $output .='
                                    <div class="kps-divTableRow">
                                        <div class="kps-divTableCell kps-br"></div>
                                    </div>
                                     <div class="kps-divTableRow">
                                        <div class="kps-divTableCell">
                                            <div class="kps-message" style="text-align: center;">
                                                <input type="hidden" id="kps_dkey" name="kps_dkey" value="' . $activationHash . '" />
                                                <input type="hidden" id="kps_DeleteToken" name="kps_DeleteToken" value="' . $token . '" />
                                                <input class="submit" type="submit" name="submit" value="' . esc_html__('Delete entry', 'kps') . '!">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>';
        // Trennung
        $output .= '<div class="kps-br"></div>';

        // HTML5
        if ($html5)
        {
            $output .= '</article>';
            $output .= '</main>';
        }

        return $output; // Rückgabe
    }
}

/**
 * Meldungsbox
 */
if ( !function_exists( 'kps_messagebox' ) ) {
    function kps_messagebox( $messageboxTitle = '', $messageboxContent = '') {

        // Template unterstützt HTML5
        $html5 = current_theme_supports('html5');

        // Ausgabe Start
        // HTML5
        if ( $html5 )
        {
            $output = '<main>';
            $output .= '<article>';
        }

        $output .= '<div>
                        <div class="kps-messagebox">
                            <div class="kps-message" style="text-align: center;"><b>' . $messageboxTitle . '</b></div>
                            <div class="kps-br"></div>
                            <div class="kps-message">
                                <div class="kps-message-reason">' . $messageboxContent . '</div>
                            </div>
                            <div class="kps-br"></div>
                        </div>
                    </div>';

        // Trennung
        $output .= '<div class="kps-br"></div>';

        // HTML5
        if ($html5)
        {
            $output .= '</article>';
            $output .= '</main>';
        }

        return $output; // Rückgabe
    }
}

/**
 * Standart-Template
 */
if ( !function_exists( 'kps_template' ) ) {
    function kps_template( $entry ) {

        // Hole die derzeitige Post-ID von Wordpress
        $pageUrl = get_post_permalink();

        // Klasse instanzieren
        $entry = new kps_entry_read($entry->id);

        // Eintrag melden
        if ($entry->show_allowedReport() === true)
        {
            $userReport = '<a class="kps-entry-moderate" href="' . $pageUrl . '&kps_report=' . $entry->show_id() . '" title="' . esc_html__('Report an entry', 'kps') . '">' . esc_html__('Report an entry', 'kps') . '</a><span>&#160;&#160;</span>';
        }

        // Anforderung der Kontaktdaten
        $requirement = '<a class="kps-entry-moderate" href="' . $pageUrl . '&kps_data=' . $entry->show_id() . '" title="' . esc_html__('Request contact information', 'kps') . '">' . esc_html__('Request contact information', 'kps') . '</a><span>&#160;&#160;</span>';

        // Sofortbearbeitung für Moderatoren
        if (function_exists('current_user_can') && current_user_can('moderate_comments'))
        {
            // Ausgabe Bearbeitung
            $moderate = '<a class="kps-entry-moderate" href="' . KPS_ADMIN_URL . '/entries.php&edit_id=' . $entry->show_id() . '" title="' . esc_html__('Edit', 'kps') . '">' . esc_html__('Edit', 'kps') . '</a>';
        }

        if ($entry->show_isFound() === true)
        {
            // Ausgabe Start
            $output = '<div class="kps-entry">';

            // Ausgabe Autordetails
            $output .= '<div class="kps-author">
                            ' . $entry->show_authorSearchfor() . ' ' . $entry->show_authorRule() . ' ' . $entry->show_yourRule() . ' <font size="4"><span>&#160;&#10140;&#160;</span></font>
                            ' . $entry->show_authorAvatar() . ' ' . $entry->show_authorName() . '
                        </div>';

            // Ausgabe Eintrag
            $output .= '<div class="kps-entry">';
            $output .= nl2br( $entry->show_authorContent() );
            $output .= '</div>';

            // Ausgabe Zeitstempel + Anforderung + Report + Moderation
            $output .= '
            <div class="kps-entry">
                <div class="kps-divTable">
                	<div class="kps-divTableBody">
                		<div class="kps-divTableRow">
                			<div class="kps-divTableCell kps-entry-datetime">' . $entry->show_unlockDateTime() . '</div>
                			<div class="kps-divTableCell kps-entry-moderate">' . $requirement. $userReport . $moderate . '</div>
                		</div>
                	</div>
                </div>
            </div>
            ';

            // Ausgabe Ende
            $output .= '</div>';

            // Trennung
            $output .= '<div class="kps-br"><hr></div>';

            return $output; // Rückgabe
        }
    }
}
?>