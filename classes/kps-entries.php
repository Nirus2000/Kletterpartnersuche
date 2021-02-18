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
 * Formular-Klasse
 *
 */
class kps_entry_read
{
    private     $_userSettings;             // Formulareinstellungen

    protected   $_id,                       // ID
                $_activationcode,           // Aktivierungs-Schlüssel
                $_deletecode,               // Lösch-Schlüssel
                $_authorContactData,        // Forumoptionen
                $_authorIp,                 // Autor-IP
                $_authorHost,               // Autor-Host
                $_reportCount,              // Report Counter
                $_lockedAutoReport,         // Auto-Sperre
                $_isLockedByAdmin,          // Adminfreigabe
                $_isLocked,                 // Userfreigabe
                $_isReported;               // Report-Mail

    public      $_authorId,                 // Autor Id
                $_authorAvatar,             // Autoravatar
                $_authorName,               // Autorname
                $_authorName_raw,           // Autorname RAW
                $_authorEmail,              // Autor Email-Adresse
                $_authorEmail_raw,          // Autor Email-Adresse RAW
                $_authorContent,            // Content
                $_allowedReport,            // Reorting erlauben
                $_outputSettings,           // Ausgabe-Einstellungen
                $_setDateTime,              // Eintrag Timestamp
                $_emailSetDateTime,         // Eintrag Timestamp Email
                $_unlockDateTime,           // Freigabe Timestamp
                $_emailUnlockDateTime,      // Freigabe Timestamp Email
                $_unlockDateTimeWidget,     // Freigabe Timestamp Widget
                $_deleteDateTime,           // Löschen Timestamp
                $_emailDeleteDateTime,      // Löschen Timestamp Email
                $_authorSearchfor,          // Form-Option
                $_authorSearchforWidget,    // Form-Option Widget
                $_authorSearchfor_raw,      // Form-Option RAW
                $_authorRule,               // Form-Option
                $_authorRuleWidget,         // Form-Option Widget
                $_authorRule_raw,           // Form-Option RAW
                $_yourRule,                 // Form-Option
                $_yourRuleWidget,           // Form-Option Widget
                $_yourRule_raw,             // Form-Option RAW
                $_isFound;                  // Eintrag gefunden

    /**
     * Konstrukteur
     */
    public function __construct($id = 0)
    {
        $this->_id                      = absint($id);
        $this->_authorId                = (int)0;
        $this->_authorAvatar            = (string)'';
        $this->_authorName              = (string)'';
        $this->_authorName_raw          = (string)'';
        $this->_authorEmail             = (string)'';
        $this->_authorEmail_raw         = (string)'';
        $this->_authorContactData       = (array)'';
        $this->_authorContent           = (string)'';
        $this->_setDateTime             = (string)'';
        $this->_unlockDateTime          = (string)'';
        $this->_emailUnlockDateTime     = (string)'';
        $this->_deleteDateTime          = (string)'';
        $this->_authorSearchfor         = (string)'';
        $this->_authorSearchforWidget   = (string)'';
        $this->_authorSearchfor_raw     = (string)'';
        $this->_authorRule              = (string)'';
        $this->_authorRuleWidget        = (string)'';
        $this->_authorRule_raw          = (string)'';
        $this->_yourRule                = (string)'';
        $this->_yourRuleWidget          = (string)'';
        $this->_yourRule_raw            = (string)'';
        $this->_isLocked                = false;
        $this->_isLockedByAdmin         = false;
        $this->_userSettings            = (array)kps_unserialize(get_option('kps_userSettings', false));
        $this->_allowedReport           = false;
        $this->_outputSettings          = (array)kps_unserialize( get_option( 'kps_output', false ) );
        $this->_isFound                 = false;
        $this->_authorIp                = (string)'';
        $this->_authorHost              = (string)'';
        $this->_reportCount             = (string)'';
        $this->_lockedAutoReport        = false;
        $this->_isReported              = false;
        $iconPak                        = array();

        // Lösche abgelaufene Einträge
        $this->delete_expire_entrys();

        // Lösche abgelaufene Anforderungen
        $this->delete_expire_verifications();

        // Wenn keine Numerische ID
        if (!is_numeric($this->_id))
        {
            $this->_isFound = false;
            return false; // Rückgabe des Wertes
        }
        else
        {
            // Hole Icon-Pak
            $iconPak = kps_iconPak();

            // Report erlauben
            $this->get_allowedReport($this->_userSettings['kpsUserReport']);

            // Hole Eintrag
            $this->get_entry($this->_id, $iconPak);
        }
    }

    /**
     * DoS
     */
    private function __clone()
    {
        // Denial of Service
    }

    /**
     * Lösche abgelaufene Anforderungen, wenn
     * Eintrag nicht mehr vorhanden ist
     * Dies dient für die DSGVO zur Anfrage, wohin
     * die Kontaktdaten versendet wurden
     */
    private function delete_expire_verifications()
    {
        global $wpdb;

        // Löschen
        $wpdb->query("DELETE " . KPS_TABLE_REQUIREMENT . " FROM " . KPS_TABLE_REQUIREMENT . " LEFT JOIN " . KPS_TABLE_ENTRIES . "
                        ON " . KPS_TABLE_REQUIREMENT . ".entryId = " . KPS_TABLE_ENTRIES . ".id
                        WHERE " . KPS_TABLE_ENTRIES . ".id IS NULL

        ");

        return true; // Rückgabe des Wertes
    }

    /**
     * Lösche abgelaufene Einträge
     */
    private function delete_expire_entrys()
    {
        global $wpdb;

        // Löschen
        $wpdb->query("DELETE FROM " . KPS_TABLE_ENTRIES . " WHERE deleteDateTime < " . time() . "");

        return true; // Rückgabe des Wertes
    }

    /**
     * Datensatz aus Datenbank laden
     */
    public function get_entry($id = 0, $iconPak = array())
    {
        global $wpdb;

        // Hole Eintrag aus Datenbank
        $data = $wpdb->get_row("SELECT * FROM " . KPS_TABLE_ENTRIES . " WHERE id = '" . $id . "' ", object);

        if (!empty($data))
        {
            $this->get_authorData($data->authorId, $data->authorName, $data->authorEmail);
            $this->get_content($data->content);
            $this->get_setDateTime($data->setDateTime);
            $this->get_unlockDateTime($data->unlockDateTime);
            $this->get_deleteDateTime($data->deleteDateTime);
            $this->get_authorSearchfor($data->authorSearchfor, $iconPak);
            $this->get_authorRule($data->authorRule, $iconPak);
            $this->get_yourRule($data->yourRule, $iconPak);
            $this->get_isLocked($data->isLocked);
            $this->get_isLockedByAdmin($data->isLockedByAdmin);
            $this->get_lockedAutoReport($data->lockedAutoReport);
            $this->get_isReported($data->isReported);
            $this->_activationcode      = $data->activationHash;
            $this->_deletecode          = $data->deleteHash;
            $this->_authorContactData   = $data->formOptions;
            $this->_authorIp            = $data->authorIp;
            $this->_authorHost          = $data->authorHost;
            $this->_reportCount         = $data->reportCount;

            $this->_isFound = true; // Eintrag gefunden
            return true; // Rückgabe des Wertes
        }
        else
        {
            $this->_isFound = false; // Eintrag nicht gefunden
            return false; // Rückgabe des Wertes
        }
    }

    /**
     * Autordaten
     */
    public function get_authorData($authorId, $authorName, $authorEmail)
    {
        // Userdaten anhand der ID holen
        $authorData = get_userdata($authorId);

        // Autordaten, wenn registiert
        if (is_numeric($authorId) && $authorId > 0 && $authorData !== false)
        {
            // Display-Namen benutzen, wenn verfügbar, ansonsten Login
            if (isset($authorData->display_name) && !empty($authorData->display_name))
            {
                // Option Profil-Link anzeigen, außer für Administatoren oder Redakteure
                if ($this->_userSettings['kpsUserProfilLink'] === 'true')
                {
                    $this->_authorName = '<a href="' . esc_url(get_edit_user_link($authorData->id)) . '" title="' . esc_html(get_the_author_meta('display_name', $authorData->id)) . '">' . esc_html(get_the_author_meta('display_name', $authorData->id)) . '</a>';
                }
                elseif ($this->_userSettings['kpsUserProfilLink'] === 'false' && current_user_can('moderate_comments'))
                {
                    // User kann moderieren
                    $this->_authorName = '<a href="' . esc_url(get_edit_user_link($authorData->id)) . '" title="' . esc_html(get_the_author_meta('display_name', $authorData->id)) . '">' . esc_html(get_the_author_meta('display_name', $authorData->id)) . '</a>';
                }
                else
                {
                    $this->_authorName = esc_html(get_the_author_meta('display_name', $authorData->id));
                }

                // Autoren-Name RAW
                $this->_authorName_raw = esc_html(get_the_author_meta('display_name', $authorData->id));

                // Autor-Id
                $this->_authorId = (int)get_the_author_meta('ID', $authorData->id);
            }
            else
            {
                // Option Profil-Link anzeigen, außer für Administatoren oder Redakteure
               if ($this->_userSettings['kpsUserProfilLink'] === 'true')
                {
                    $this->_authorName = '<a href="' . esc_url(get_edit_user_link($authorData->id)) . '" title="' . esc_html(get_the_author_meta('user_login', $authorData->id)) . '">' . esc_html(get_the_author_meta('user_login', $authorData->id)) . '</a>';
                }
                elseif ($this->_userSettings['kpsUserProfilLink'] === 'false' && (current_user_can('editor') || current_user_can('administrator')))
                {
                    $this->_authorName = '<a href="' . esc_url(get_edit_user_link($authorData->id)) . '" title="' . esc_html(get_the_author_meta('user_login', $authorData->id)) . '">' . esc_html(get_the_author_meta('user_login', $authorData->id)) . '</a>';
                }
                else
                {
                    $this->_authorName      = esc_html(get_the_author_meta('user_login', $authorData->id));
                    $this->_authorName_raw  = esc_html(get_the_author_meta('user_login', $authorData->id));
                }

                // Autoren-Name RAW
                $this->_authorName_raw  = esc_html(get_the_author_meta('user_login', $authorData->id));

                // Autor-Id
                $this->_authorId = 0;
            }

            // Autoren-Email RAW
            $this->_authorEmail_raw = esc_html(get_the_author_meta('user_email', $authorData->id));

            // Autoren-Email
            $this->_authorEmail = '<a href="mailto:' . esc_html(get_the_author_meta('user_email', $authorData->id)) . '">' . esc_html(get_the_author_meta('user_email', $authorData->id)) . '</a>';
        }
        else
        {
            // Autor-Id
            $this->_authorId = 0;

            // Autoren-Name RAW
            $this->_authorName_raw  = esc_html($authorName);

            // Autoren-Name
            $this->_authorName = esc_html($authorName);

            // Autoren-Email RAW
            $this->_authorEmail_raw =  esc_html($authorEmail);

            // Autoren-Email
            $this->_authorEmail = '<a href="mailto:' . esc_html($authorEmail) . '">' . esc_html($authorEmail) . '</a>';
        }

        // Avatar erlaubt?
        if ($this->_userSettings['kpsUserAvatar'] === 'true' && get_option('show_avatars') === '1' && $authorData !== false)
        {
            // Avatar holen
            $isAvatar = get_avatar($authorId, 32, '', $this->_authorName, array(
                                                                                'size'          => 96,
                                                                                'height'        => null,
                                                                                'width'         => null,
                                                                                'default'       => get_option( 'avatar_default', 'mystery' ),
                                                                                'force_default' => false,
                                                                                'rating'        => get_option( 'avatar_rating' ),
                                                                                'scheme'        => null,
                                                                                'alt'           => $this->_authorName,
                                                                                'class'         => 'kps-avatar',
                                                                                'force_display' => false,
                                                                                'extra_attr'    => '',
                                                                                )
            );

            // Avatar vorhanden
            if ($isAvatar)
            {
                $this->_authorAvatar = $isAvatar;
            }
        }
    }

    /**
     * Eintrag
     */
    public function get_content($authorContent)
    {
        $this->_authorContent = esc_html($authorContent);
    }

    /**
     * Eintrags-Zeit
     */
    public function get_setDateTime($setDateTime)
    {
        if (is_numeric($setDateTime))
        {
            if ($this->_outputSettings['kpsEmailSetTime'] === 'true')
            {
                $this->_setDateTime = date_i18n(get_option('date_format'), $setDateTime) . ', ' . date_i18n(get_option('time_format'), $setDateTime);
            }
            else
            {
                $this->_setDateTime = date_i18n(get_option('date_format'), $setDateTime);
            }

            // Für Freigabe-Email, wenn Autor noch nicht freigegeben hat.
            if ($this->_outputSettings['kpsEmailSetTime'] === 'true')
            {
                $this->_emailSetDateTime = date_i18n(get_option('date_format'), $setDateTime) . ', ' . date_i18n(get_option('time_format'), $setDateTime);
            }
            else
            {
                $this->_emailSetDateTime = date_i18n(get_option('date_format'), $setDateTime);
            }

            // Dashboard erkennen
            if ( is_admin() && (current_user_can('editor') || current_user_can('administrator')))
            {
                $this->_setDateTime = date_i18n(get_option('date_format'), $setDateTime) . ', ' . date_i18n(get_option('time_format'), $setDateTime);
            }
        }
    }

    /**
     * Unlock Zeit
     */
    public function get_unlockDateTime($unlockDateTime)
    {
        if (is_numeric($unlockDateTime))
        {
            if ($unlockDateTime !== 0)
            {
                if ($this->_outputSettings['kpsUnlockTime'] === 'true')
                {
                    $this->_unlockDateTime      = date_i18n(get_option('date_format'), $unlockDateTime) . ', ' . date_i18n(get_option('time_format'), $unlockDateTime);
                }
                else
                {
                    $this->_unlockDateTime      = date_i18n(get_option('date_format'), $unlockDateTime);
                }

                $this->_unlockDateTimeWidget    = date_i18n(get_option('date_format'), $unlockDateTime);
            }
            else
            {
                $this->_unlockDateTime = "---";
            }

            // Für Freigabe-Email, wenn Autor noch nicht freigegeben hat.
            if ($unlockDateTime === 0)
            {
                $this->_emailUnlockDateTime = esc_html__('Wait for release from the Author', 'kps');
            }
            else
            {
                if ($this->_outputSettings['kpsEmailUnlockTime'] === 'true')
                {
                    $this->_emailUnlockDateTime = date_i18n(get_option('date_format'), $unlockDateTime) . ', ' . date_i18n(get_option('time_format'), $unlockDateTime);
                }
                else
                {
                    $this->_emailUnlockDateTime = date_i18n(get_option('date_format'), $unlockDateTime);
                }
            }

            // Dashboard erkennen
            if (is_admin() && (current_user_can('editor') || current_user_can('administrator')))
            {
                if ($unlockDateTime != 0)
                {
                    $this->_unlockDateTime  = date_i18n(get_option('date_format'), $unlockDateTime) . ', ' . date_i18n(get_option('time_format'), $unlockDateTime);
                }
                else
                {
                    $this->_unlockDateTime = "---";
                }
            }
        }
    }

    /**
     * Lösch-Zeit
     */
    public function get_deleteDateTime($deleteDateTime)
    {
        if (is_numeric($deleteDateTime))
        {
            if ($this->_outputSettings['kpsEmailDeleteTime'] === 'true')
            {
                $this->_deleteDateTime = date_i18n(get_option('date_format'), $deleteDateTime) . ', ' . date_i18n(get_option('time_format'), $deleteDateTime);
            }
            else
            {
                $this->_deleteDateTime = date_i18n(get_option('date_format'), $deleteDateTime);
            }

            // Für Freigabe-Email, wenn Autor noch nicht freigegeben hat.
            if ($this->_outputSettings['kpsEmailDeleteTime'] === 'true')
            {
                $this->_emailDeleteDateTime = date_i18n(get_option('date_format'), $deleteDateTime) . ', ' . date_i18n(get_option('time_format'), $deleteDateTime);
            }
            else
            {
                $this->_emailDeleteDateTime = date_i18n(get_option('date_format'), $deleteDateTime);
            }

            // Dashboard erkennen
            if ( is_admin() && (current_user_can('editor') || current_user_can('administrator')))
            {
                $this->_deleteDateTime = date_i18n(get_option('date_format'), $deleteDateTime) . ', ' . date_i18n(get_option('time_format'), $deleteDateTime);
            }
        }
    }

    /**
     * Autoren-Suche
     */
    public function get_authorSearchfor($authorSearchfor = NULL, $iconPak = array('color' => 'green', 'size' => '45'))
    {
        if (is_numeric($authorSearchfor))
        {
            switch ($authorSearchfor)
            {
                case '0':
                    $alt    = kps_getFormTranslation('Hall');
                    $pic    = "hall.svg";
                break;
                case '1':
                    $alt    = kps_getFormTranslation('Climbing');
                    $pic    = "nature.svg";
                break;
                case '2':
                    $alt    = kps_getFormTranslation('Travels');
                    $pic    = "travel.svg";
                break;
                case '3':
                    $alt    = kps_getFormTranslation('Walking');
                    $pic    = "trekking.svg";
                break;
                case '4':
                    $alt    = kps_getFormTranslation('Alpine tours');
                    $pic    = "alpine.svg";
                break;
                case '5':
                    $alt    = kps_getFormTranslation('Kayak');
                    $pic    = "kayak.svg";
                break;
                case '6':
                    $alt    = kps_getFormTranslation('Ferratas');
                    $pic    = "ferratas.svg";
                break;
                case '7':
                    $alt    = kps_getFormTranslation('Mountain bike');
                    $pic    = "mountainbike.svg";
                break;
                case '8':
                    $alt    = kps_getFormTranslation('Winter sports');
                    $pic    = "wintersports.svg";
                break;
                default:
                    $alt    = kps_getFormTranslation('Unknown');;
                    $pic    = "unknown.svg";
            }

            $this->_authorSearchfor_raw     = $alt;
            $this->_authorSearchforWidget   = '<img class="kps-icon-pak" src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $iconPak['color'] . '/' . $pic . '" width="30" height="30" alt="' . $alt . '" title="' . $alt . '" />';
            $this->_authorSearchfor         = '<img class="kps-icon-pak" src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $iconPak['color'] . '/' . $pic . '" width="' . $iconPak['size'] . '" height="' . $iconPak['size'] . '" alt="' . $alt . '" title="' . $alt . '" />';
        }
    }

    /**
     * Autoren-Regel
     */
    public function get_authorRule($authorRule = NULL, $iconPak = array('color' => 'green', 'size' => '45'))
    {
        if (is_numeric($authorRule))
        {
            switch ($authorRule)
            {
                case '0':
                    $alt    = kps_getFormTranslation('Unique');
                    $pic    = "onetime.svg";
                break;
                case '1':
                    $alt    = kps_getFormTranslation('Regularly');
                    $pic    = "moretime.svg";
                break;
                default:
                    $alt    = kps_getFormTranslation('Unknown');;
                    $pic    = "unknown.svg";
            }

            $this->_authorRule_raw      = $alt;
            $this->_authorRuleWidget    = '<img class="kps-icon-pak" src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $iconPak['color'] . '/' . $pic . '" width="30" height="30" alt="' . $alt . '" title="' . $alt . '" />';
            $this->_authorRule          = '<img class="kps-icon-pak" src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $iconPak['color'] . '/' . $pic . '" width="' . $iconPak['size'] . '" height="' . $iconPak['size'] . '" alt="' . $alt . '" title="' . $alt . '" />';
        }
    }

    /**
     * User-Regel
     */
    public function get_yourRule($yourRule = NULL, $iconPak = array('color' => 'green', 'size' => '45'))
    {
        if (is_numeric($yourRule))
        {
            switch ($yourRule)
            {
                case '0':
                    $alt    = kps_getFormTranslation('Single person');
                    $pic    = "goalone.svg";
                break;
                case '1':
                    $alt    = kps_getFormTranslation('Family');
                    $pic    = "family.svg";
                break;
                case '2':
                    $alt    = kps_getFormTranslation('Club/Group');
                    $pic    = "comeclub.svg";
                break;
                default:
                    $alt    = kps_getFormTranslation('Unknown');;
                    $pic    = "unknown.svg";
            }

            $this->_yourRule_raw    = $alt;
            $this->_yourRuleWidget  = '<img class="kps-icon-pak" src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $iconPak['color'] . '/' . $pic . '" width="30" height="30" alt="' . $alt . '" title="' . $alt . '" />';
            $this->_yourRule        = '<img class="kps-icon-pak" src="' . KPS_RELATIV_FRONTEND_GFX . '/' . $iconPak['color'] . '/' . $pic . '" width="' . $iconPak['size'] . '" height="' . $iconPak['size'] . '" alt="' . $alt . '" title="' . $alt . '" />';
        }
    }

    /**
     * Autoren-Freigabe
     */
    public function get_isLocked($isLocked)
    {
        if (is_numeric($isLocked))
        {
            $this->_isLocked = ($isLocked !== '0') ? true : false;
        }
    }

    /**
     * Admin-Freigabe
     */
    public function get_isLockedByAdmin($isLockedByAdmin)
    {
        if (is_numeric($isLockedByAdmin))
        {
            $this->_isLockedByAdmin = ($isLockedByAdmin !== '0') ? true : false;
        }
    }

    /**
     * Report-Status
     */
    public function get_isReported($isReported)
    {
        if (is_numeric($isReported))
        {
            $this->_isReported = ($isReported === '1') ? true : false;
        }
    }

    /**
     * Auto-Sperre
     */
    public function get_lockedAutoReport($lockedAutoReport)
    {
        if (is_numeric($lockedAutoReport))
        {
            $this->_lockedAutoReport = ($lockedAutoReport === '1') ? true : false;
        }
    }

    /**
     * Report
     */
    public function get_allowedReport($allowedReport)
    {
        $this->_allowedReport = ($allowedReport === 'true' && get_option('users_can_register') === '1') ? true : false;
    }

    /**
     * Ausgabe
     */
    public function show_isFound()
    {
        return $this->_isFound;
    }
    public function show_id()
    {
        return $this->_id;
    }
    public function show_authorId()
    {
        return $this->_authorId;
    }

    public function show_authorAvatar()
    {
        return $this->_authorAvatar;
    }

    public function show_authorName()
    {
        return $this->_authorName;
    }

    public function show_authorName_raw()
    {
        return $this->_authorName_raw;
    }

    public function show_authorEmail()
    {
        return $this->_authorEmail;
    }

    public function show_authorEmail_raw()
    {
        return $this->_authorEmail_raw;
    }

    public function show_authorContent()
    {
        return $this->_authorContent;
    }

    public function show_authorContactData()
    {
        return $this->_authorContactData;
    }

    public function show_setDateTime()
    {
        return $this->_setDateTime;
    }

    public function show_emailSetDateTime()
    {
        return $this->_emailSetDateTime;
    }

    public function show_unlockDateTime()
    {
        return $this->_unlockDateTime;
    }

    public function show_emailUnlockDateTime()
    {
        return $this->_emailUnlockDateTime;
    }

    public function show_unlockDateTimeWidget()
    {
        return $this->_unlockDateTimeWidget;
    }

    public function show_deleteDateTime()
    {
        return $this->_deleteDateTime;
    }

    public function show_emailDeleteDateTime()
    {
        return $this->_emailDeleteDateTime;
    }

    public function show_authorSearchfor()
    {
        return $this->_authorSearchfor;
    }

    public function show_authorSearchforWidget()
    {
        return $this->_authorSearchforWidget;
    }

    public function show_authorSearchfor_raw()
    {
        return $this->_authorSearchfor_raw;
    }

    public function show_authorRule()
    {
        return $this->_authorRule;
    }

    public function show_authorRuleWidget()
    {
        return $this->_authorRuleWidget;
    }

    public function show_authorRule_raw()
    {
        return $this->_authorRule_raw;
    }

    public function show_yourRule()
    {
        return $this->_yourRule;
    }

    public function show_yourRuleWidget()
    {
        return $this->_yourRuleWidget;
    }

    public function show_yourRule_raw()
    {
        return $this->_yourRule_raw;
    }

    public function show_isLocked()
    {
        return $this->_isLocked;
    }

    public function show_isLockedByAdmin()
    {
        return $this->_isLockedByAdmin;
    }

    public function show_lockedAutoReport()
    {
        return $this->_lockedAutoReport;
    }

    public function show_isReported()
    {
        return $this->_isReported;
    }

    public function show_authorIp()
    {
        return $this->_authorIp;
    }

    public function show_authorHost()
    {
        return $this->_authorHost;
    }

    public function show_reportCount()
    {
        return $this->_reportCount;
    }

    public function show_activationcode()
    {
        return $this->_activationcode;
    }

    public function show_deletecode()
    {
        return $this->_deletecode;
    }

    public function show_allowedReport()
    {
        return $this->_allowedReport;
    }
}