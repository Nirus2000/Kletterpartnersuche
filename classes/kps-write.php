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
 * Schreib-Klasse
 *
 */
class kps_entry_write
{
    private     $_userSettings,             // Formulareinstellungen
                $_insertDB;                 // Eintrag in Datenbank schreiben

    public      $_authorId,                 // Autor-Id
                $_authorName,               // Autorname
                $_authorEmail,              // Autor Email-Adresse
                $_authorEmailCheck,         // Email prüfen
                $_authorEntry,              // Autor Eintrag
                $_authorSearchfor,          // Autor "Sucht für"
                $_authorSearchfor0,         // Formularoption
                $_authorSearchfor1,         // Formularoption
                $_authorSearchfor2,         // Formularoption
                $_authorSearchfor3,         // Formularoption
                $_authorRule,               // Autor "Art der Suche"
                $_authorRule0,              // Formularoption
                $_authorRule1,              // Formularoption
                $_yourRule,                 // Autor "íst"
                $_yourRule0,                // Formularoption
                $_yourRule1,                // Formularoption
                $_yourRule2,                // Formularoption
                $_outputSettings,           // Ausgabe-Einstellungen
                $_authorTelephone,          // Telefon (Festnetz)
                $_authorMobile,             // Telefon (Mobile)
                $_authorSignal,             // Signal
                $_authorViper,              // Viper
                $_authorTelegram,           // Telegram
                $_authorWhatsapp,           // Whatsapp
                $_authorFacebookMessenger,  // Facebook Messenger
                $_authorHoccer,             // Hoccer
                $_authorSkype,              // Skype
                $_authorWire,               // Wire
                $_authorWebsite,            // Website
                $_authorFacebook,           // Facebook
                $_authorInstagram,          // instagram
                $_authorContactData,        // Autor zusatzliche Kontaktdaten
                $_isNotFound,               // Eintrag gefunden
                $_isInsertDB,               // Eintrag in Datenbank geschrieben
                $_usernameNotExist,         // Autorename existiert als registierter User
                $_emailNotExist,            // Autor Email-Adresse existiert als registierter User
                $_activationEmailIsSend,    // Aktivierungs-Email versendet
                $_adminActivationIsSend,    // Freigabeinformation an Admin versendet
                $_wordCount,                // Anzahl der Wörter in der Textarea
                $_emailCopyIsSend,          // Email-Kopie versendet
                $_authorIp,                 // Autoren IP
                $_authorHost,               // Autoren Host
                $_emailCopyCC,              // Emaileinstellungen
                $_acceptedAGBDSGVO;         // Autor akzeptiert AGB und/oder DSGVO


    /**
     * Konstrukteur
     */
    public function __construct($write = '', $pageUrl = '')
    {
        $this->_authorId                = (int)0;
        $this->_authorName              = (string)'';
        $this->_authorEmail             = (string)'';
        $this->_authorEmailCheck        = (string)'false';
        $this->_authorEntry             = (string)'';
        $this->_authorSearchfor         = NULL;
        $this->_authorSearchfor0        = (string)'';
        $this->_authorSearchfor1        = (string)'';
        $this->_authorSearchfor2        = (string)'';
        $this->_authorSearchfor3        = (string)'';
        $this->_authorRule              = NULL;
        $this->_authorRule0             = (string)'';
        $this->_authorRule1             = (string)'';
        $this->_yourRule                = NULL;
        $this->_yourRule0               = (string)'';
        $this->_yourRule1               = (string)'';
        $this->_yourRule2               = (string)'';
        $this->_authorTelephone         = (string)'';
        $this->_authorMobile            = (string)'';
        $this->_authorSignal            = (string)'';
        $this->_authorViper             = (string)'';
        $this->_authorTelegram          = (string)'';
        $this->_authorWhatsapp          = (string)'';
        $this->_authorFacebookMessenger = (string)'';
        $this->_authorHoccer            = (string)'';
        $this->_authorSkype             = (string)'';
        $this->_authorWire              = (string)'';
        $this->_authorWebsite           = (string)'';
        $this->_authorFacebook          = (string)'';
        $this->_authorInstagram         = (string)'';
        $this->_userSettings            = (array)kps_unserialize(get_option('kps_userSettings', false));
        $this->_outputSettings          = (array)kps_unserialize(get_option('kps_output', false));
        $this->_authorContactData       = (array)'';
        $this->_isNotFound              = false;
        $this->_insertDB                = false;
        $this->_isInsertDB              = (int)0;
        $this->_usernameNotExist        = false;
        $this->_emailNotExist           = false;
        $this->_activationEmailIsSend   = (int)0;
        $this->_adminActivationIsSend   = (int)0;
        $this->_emailCopyIsSend         = (int)0;
        $this->_emailCopyCC             = (array)kps_unserialize(get_option('kps_mailFromCC', false));
        $this->_wordCount               = false;
        $this->_acceptedAGBDSGVO        = false;
        $this->_authorIp                = (string)'';
        $this->_authorHost              = (string)'';

        // Autordaten escapen
        $this->get_authorData($write['kps_authorId'], $write['kps_authorName'], $write['kps_authorEmail']);

        // Autor Eintrag escapen und Wortlimit prüfen
        $this->get_authorEntry($write['kps_authorEntry']);

        // Autor "Sucht für" escapen
        $this->get_authorSearchfor($write['kps_authorSearchfor']);

        // Autor "Art der Suche" escapen
        $this->get_authorRule($write['kps_authorRule']);

        // Autor "íst" escapen
        $this->get_yourRule($write['kps_yourRule']);

        // Autor "AGB und/oder DSGVO"
        $this->get_acceptedAGBDSGVO($write['kps_acceptedAGBDSGVO']);

        // Autor-IP und Host ermitteln
        $this->get_authorIpHost();

        // zusätzliche Kontaktmöglichkeiten escapen
        $this->_authorTelephone         = $this->get_AuthorURLEmailNumber($write['kps_authorTelephone'], false, false, true, false);
        $this->_authorMobile            = $this->get_AuthorURLEmailNumber($write['kps_authorMobile'], false, false, true, false);
        $this->_authorSignal            = $this->get_AuthorURLEmailNumber($write['kps_authorSignal'], false, false, true, false);
        $this->_authorViper             = $this->get_AuthorURLEmailNumber($write['kps_authorViper'], false, false, true, false);
        $this->_authorTelegram          = $this->get_AuthorURLEmailNumber($write['kps_authorTelegram'], false, false, true, false);
        $this->_authorWhatsapp          = $this->get_AuthorURLEmailNumber($write['kps_authorWhatsapp'], false, false, true, false);
        $this->_authorHoccer            = $this->get_AuthorURLEmailNumber($write['kps_authorHoccer'], true, false, false, false);
        $this->_authorWire              = $this->get_AuthorURLEmailNumber($write['kps_authorWire'], true, false, true, false);
        $this->_authorSkype             = $this->get_AuthorURLEmailNumber($write['kps_authorSkype'], true, false, true, true);
        $this->_authorFacebookMessenger = $this->get_AuthorURLEmailNumber($write['kps_authorFacebookMessenger'], false, true, false, false);
        $this->_authorWebsite           = $this->get_AuthorURLEmailNumber($write['kps_authorWebsite'], false, true, false, false);
        $this->_authorFacebook          = $this->get_AuthorURLEmailNumber($write['kps_authorFacebook'], false, true, false, false);
        $this->_authorInstagram         = $this->get_AuthorURLEmailNumber($write['kps_authorInstagram'], false, true, false, false);

        // Salt generieren
        $salt = Hash::salt(32); // Salt erstellen mit einer Zeichenlänge von 32

        // Hash generieren
        $hash = Hash::make($this->_authorEmail, $salt); // Hash erzeugen

        // Passwort generieren
        $deletePassword = Hash::generatePassword(); // Passwort generieren

        // Aktivierungs-Hash generieren
        $activationHash = wp_hash($this->_authorEmail . time() , 'auth'); // Aktivierungs-Hash erzeugen

        // Lösch-Hash generieren
        $deleteHash = wp_hash($this->_authorContent . time() , 'auth'); // Aktivierungs-Hash erzeugen

        // Prüfe, ob Eintrag schon vorhanden
        $this->_isNotFound = $this->find($this->_authorEmail, $this->_authorSearchfor, $this->_authorRule, $this->_yourRule);

        // Hole Usereinstellungen
        $userRequireAdminUnlock = ($this->_userSettings['kpsUserRequireAdminUnlock'] === 'false') ? 1 : 0;

        if ($this->_isNotFound === true)
        {
            // Serialisiere Kontaktdaten
            $this->get_serial(  $this->_authorTelephone,
                                $this->_authorMobile,
                                $this->_authorSignal,
                                $this->_authorViper,
                                $this->_authorTelegram,
                                $this->_authorWhatsapp,
                                $this->_authorHoccer,
                                $this->_authorWire,
                                $this->_authorSkype,
                                $this->_authorFacebookMessenger,
                                $this->_authorWebsite,
                                $this->_authorFacebook,
                                $this->_authorInstagram
            );

            // Eintragen in Datenbank
            if ($this->_acceptedAGBDSGVO === true
                    && !empty($this->_authorName)
                    && !empty($deletePassword)
                    && !empty($activationHash)
                    && !empty($deleteHash)
                    && !empty($hash)
                    && !empty($this->_authorEntry)
                    && is_numeric($this->_authorId)
                    && is_numeric($this->_authorSearchfor)
                    && is_numeric($this->_authorRule)
                    && is_numeric($this->_yourRule)
                    && is_numeric($userRequireAdminUnlock)
                    && is_serialized($this->_authorContactData)
                    && $this->_wordCount === true
                    && $this->_emailNotExist === true
                    && $this->_authorEmailCheck !== false)
            {
                // In Datenbank schreiben
                $this->_insertDB = true;
                $this->insert($hash, $deletePassword, $activationHash, $deleteHash, $userRequireAdminUnlock);

                // Aktivierungs-Email versenden
                if ($this->_isInsertDB > 0)
                {
                    $this->sendActivation($deletePassword, $activationHash, $deleteHash, $pageUrl);

                    // Admin-Email versenden, bei Freiabe der Einträge
                    if ($this->_userSettings['kpsUserRequireAdminUnlock'] === 'true')
                    {
                        $this->sendAdminActivation();
                    }

                    // Kopie-Email versenden, bei neuem Eintrag
                    if ($this->_emailCopyCC['kpsEmailInformation'] === 'true')
                    {
                        $this->sendEmailCopy();
                    }
                }
                else
                {
                    $this->_isInsertDB = false;
                    return false; // Rückgabe des Wertes
                }
            }
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
     * Eintrag in Datenbank schreiben
     */
    public function insert($hash = '', $deletePassword = '', $activationHash = '', $deleteHash = '', $userRequireAdminUnlock = 0)
    {
        global $wpdb;

        // Report Counter zusammensetzen
        $reportcounter = serialize(array(
                                    'spam'          => 0,
                                    'unreasonable'  => 0,
                                    'double'        => 0,
                                    'privacy'       => 0,
                                    'others'        => 0)
                        );

        // Eintragen in Datenbank
        if ($this->_insertDB === true)
        {
            // Schreibe in Datenbank
            $insertData = array(
                'id'                => '',
                'authorName'        => $this->_authorName,
                'authorId'          => $this->_authorId,
                'authorEmail'       => $this->_authorEmail,
                'password'          => Hash::make($deletePassword, $hash),
                'activationHash'    => $activationHash,
                'deleteHash'        => $deleteHash,
                'hash'              => $hash,
                'content'           => $this->_authorEntry,
                'setDateTime'       => time() ,
                'unlockDateTime'    => 0,
                'deleteDateTime'    => time() + get_option('kps_deleteNoEntryTime', false),
                'authorSearchfor'   => $this->_authorSearchfor,
                'authorRule'        => $this->_authorRule,
                'yourRule'          => $this->_yourRule,
                'formOptions'       => $this->_authorContactData,
                'isLocked'          => 0,
                'isLockedByAdmin'   => $userRequireAdminUnlock,
                'lockedAutoReport'  => 0,
                'authorIp'          => $this->_authorIp,
                'authorHost'        => $this->_authorHost,
                'reportCount'       => $reportcounter,
                'isReported'        => 0
            );
            $insertFormat = array(
                '%d',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%s',
                '%d',
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d'
            );
            $this->_isInsertDB = $wpdb->insert(KPS_TABLE_ENTRIES, $insertData, $insertFormat);

            // Gesamtzähler für Statistik
            $countActivation    = kps_unserialize(get_option('kps_kpsCounter', false));
            $newSetKPSCounter['kpsAllEntrys']           = $countActivation['kpsAllEntrys'] + 1;
            $newSetKPSCounter['kpsAllActivatedEntrys']  = $countActivation['kpsAllActivatedEntrys'];
            $newSetKPSCounter['kpsAllVerfifications']   = $countActivation['kpsAllVerfifications'];
            $newSetKPSCounter['kpsAllSendRequirements'] = $countActivation['kpsAllSendRequirements'];
            $newSetKPSCounter['kpsAllDeleteEntrys']     = $countActivation['kpsAllDeleteEntrys'];
            $newSetKPSCounter = serialize($newSetKPSCounter);
            update_option('kps_kpsCounter', $newSetKPSCounter);

            return $this->_isInsertDB; // Rückgabe des Wertes
        }
        else
        {
            $this->_insertDB    = false;    // Eintrag nicht in Datenbank schreiben
            $this->_isInsertDB  = 0;        // Eintrag nicht in Datenbank geschrieben
            return false;                   // Rückgabe bei Fehler
        }

        return false; // Rückgabe des Wertes
    }

    /**
     * Überprüfung, ob Eintrag vorhanden ist
     */
    public function find($authorEmail = '', $authorSearchfor = NULL, $authorRule = NULL, $yourRule = NULL)
    {
        global $wpdb;

        // Überprüfen, ob Eintrag schon existiert
        if ($authorEmail && $this->_authorEmailCheck !== false && isset($authorSearchfor) && isset($authorRule) && isset($yourRule))
        {
            // Suche Aktivierungs-Email-Code in Datenbank
            $wpdb->get_row("SELECT authorEmail, authorSearchfor, authorRule, yourRule FROM " . KPS_TABLE_ENTRIES . "
                            WHERE authorEmail = '" . $authorEmail . "' AND
                            authorSearchfor = '" . $authorSearchfor . "' AND
                            authorRule = '" . $authorRule . "' AND
                            yourRule = '" . $yourRule . "'", object);
            $rowcount = $wpdb->num_rows; // Einträge zählen

            // Ist der Eintrag vorhanden
            if ($rowcount == 0)
            {
                return true; // Rückgabe des Wertes
            }
            else
            {
                return false; // Rückgabe bei Fehler
            }
        }
        return false; // Rückgabe des Wertes
    }

    /**
     * Userdaten holen
     */
    public function get_authorData($authorId, $authorName, $authorEmail)
    {
        // Userdaten anhand der ID holen
        $authorData = get_userdata($authorId);

        // Autordaten, wenn registiert
        if (is_numeric($authorId) && $authorId > 0 && $authorData !== false)
        {
            if (isset($authorData->display_name) && !empty($authorData->display_name))
            {
                // Display-Name verfügbar
                $this->_authorName = esc_html($authorData->display_name);
            }
            else
            {
                // Login-Name nehmen
                $this->_authorName = esc_html($authorData->user_login);
            }

            // Autor-Id
            $this->_authorId = (int)$authorData->id;

            // Email aus System holen
            $this->_authorEmail = esc_html($authorData->user_email);
            $this->_authorEmailCheck = is_email($this->_authorEmail);

            // Registierter User -> Flagge setzen
            $this->_usernameNotExist = true;
            $this->_emailNotExist = true;

            return true; // Rückgabe des Wertes
        }
        else
        {
            // Prüfe, ob mit diesem Username, schon jemand existiert (Registierete User)
            if ( username_exists( esc_html($authorName) ) )
            {
                // Autorename existiert als registierter User
                $this->_authorName = '';
                $this->_usernameNotExist = false;

            }
            else
            {
                $this->_authorName = esc_html($authorName);
                $this->_usernameNotExist = true;
            }

            // Autor-Id
            $this->_authorId = 0;

            // Email Escapen
            $this->_authorEmail = esc_html(sanitize_email($authorEmail));

            // Prüfe, ob mit dieser Email, schon jemand existiert (Registierete User)
            if (email_exists($this->_authorEmail))
            {
                // Autorename existiert als registierter User
                $this->_emailNotExist = false;
            }
            else
            {
                $this->_emailNotExist = true;
            }

            $this->_authorEmailCheck = is_email($this->_authorEmail);

            return true; // Rückgabe des Wertes
        }
    }

    /**
     * Autor akzeptiert AGB und/oder DSGVO
     */
    public function get_acceptedAGBDSGVO($authorAcceptAGBDSGVO = '0')
    {
        if ($this->_userSettings['kpsUserPrivacyAGB'] === 'false' OR $this->_userSettings['kpsUserPrivacyDSGVO'] === 'false')
        {
            // Keine AGB's oder DSGVO verfügbar
            $this->_acceptedAGBDSGVO = true;
            return $this->_acceptedAGBDSGVO; // Rückgabe des Wertes
        }
        else
        {
            // Prüfe, ob AGB's und DSGVO gesetzt wurde, öffentlich ist und kein Passwortschutz hat
            if ($this->_userSettings['kpsUserPrivacyAGB'] === 'true' && $this->_userSettings['kpsUserPrivacyDSGVO'] === 'true')
            {
                if (get_option('kps_agb') > 0
                    && get_post_status(get_option('kps_agb')) !== false
                    && get_post_status(get_option('kps_agb')) == 'publish'
                    && post_password_required(get_option('kps_agb')) === false
                    && get_option('kps_dsgvo') > 0
                    && get_post_status(get_option('kps_dsgvo')) !== false
                    && get_post_status(get_option('kps_dsgvo')) == 'publish'
                    && post_password_required(get_option('kps_dsgvo')) === false)
                {
                    $this->_acceptedAGBDSGVO = ($authorAcceptAGBDSGVO === '1') ? true : false;
                    return $this->_acceptedAGBDSGVO; // Rückgabe des Wertes
                }
                else
                {
                    // AGBs und DSGVO nicht zugänglich
                    $this->_acceptedAGBDSGVO = true;
                    return $this->_acceptedAGBDSGVO; // Rückgabe des Wertes
                }
            }
            // Prüfe, ob DSGVO gesetzt wurde, öffentlich ist und kein Passwortschutz hat
            elseif ($this->_userSettings['kpsUserPrivacyDSGVO'] === 'true')
            {
                if (get_option('kps_dsgvo') > 0
                    && get_post_status(get_option('kps_dsgvo')) !== false
                    && get_post_status(get_option('kps_dsgvo')) == 'publish'
                    && post_password_required(get_option('kps_dsgvo')) === false)
                {
                    $this->_acceptedAGBDSGVO = ($authorAcceptAGBDSGVO === '1') ? true : false;
                    return $this->_acceptedAGBDSGVO; // Rückgabe des Wertes
                }
                else
                {
                    // DSGVO nicht zugänglich
                    $this->_acceptedAGBDSGVO = true;
                    return $this->_acceptedAGBDSGVO; // Rückgabe des Wertes
                }
            }
            // Prüfe, ob AGB's gesetzt wurde, öffentlich ist und kein Passwortschutz hat
            else
            {
                if (get_option('kps_agb') > 0
                    && get_post_status(get_option('kps_agb')) !== false
                    && get_post_status(get_option('kps_agb')) == 'publish'
                    && post_password_required(get_option('kps_agb')) === false)
                {
                    $this->_acceptedAGBDSGVO = ($authorAcceptAGBDSGVO === '1') ? true : false;
                    return $this->_acceptedAGBDSGVO; // Rückgabe des Wertes
                }
                else
                {
                    // AGBs nicht zugänglich
                    $this->_acceptedAGBDSGVO = true;
                    return $this->_acceptedAGBDSGVO; // Rückgabe des Wertes
                }
            }
        }
    }

    /**
     * Autor Eintrag escapen
     */
    public function get_authorEntry($authorEntry = '')
    {
        $this->_authorEntry = kps_sanitize_textarea($authorEntry);

        // Wörter zählen
        $textAreaWordCount = trim( strip_tags( $this->_authorEntry ) );
        $textAreaWordCount = substr_count($textAreaWordCount, ' ');

        // Vergleich, ob die Anzahl der geschriebenen Wörter größer/gleich
        if($textAreaWordCount >= get_option('kps_formWordCount', false))
        {
            $this->_wordCount = true; // Rückgabe des Wertes
        }
        else
        {
            $this->_wordCount = false; // Rückgabe des Wertes
        }
    }

    /**
     * Autor "Sucht für"
     */
    public function get_authorSearchfor($authorSearchfor = NULL)
    {
        // Positiv-Numerisch
        $this->_authorSearchfor = absint($authorSearchfor);

        // Fallunterscheidung
        switch ($this->_authorSearchfor)
        {
            case '0':
                $this->_authorSearchfor0    = 'checked';
            break;
            case '1':
                $this->_authorSearchfor1    = 'checked';
            break;
            case '2':
                $this->_authorSearchfor2    = 'checked';
            break;
            case '3':
                $this->_authorSearchfor3    = 'checked';
            break;
            default:
                $this->_authorSearchfor     = NULL;
                $this->_authorSearchfor0    = '';
                $this->_authorSearchfor1    = '';
                $this->_authorSearchfor2    = '';
                $this->_authorSearchfor3    = '';
        }
    }

    /**
     * Autor "Art der Suche"
     */
    public function get_authorRule($authorRule = NULL)
    {
        // Positiv-Numerisch
        $this->_authorRule = absint($authorRule);

        // Fallunterscheidung
        switch ($this->_authorRule)
        {
            case '0':
                $this->_authorRule0 = 'checked';
            break;
            case '1':
                $this->_authorRule1 = 'checked';
            break;
            default:
                $this->_authorRule  = NULL;
                $this->_authorRule0 = '';
                $this->_authorRule1 = '';
        }
    }

    /**
     * Autor "ist"
     */
    public function get_yourRule($yourRule = NULL)
    {
        // Positiv-Numerisch
        $this->_yourRule = absint($yourRule);

        // Fallunterscheidung
        switch ($this->_yourRule)
        {
            case '0':
                $this->_yourRule0   = 'checked';
            break;
            case '1':
                $this->_yourRule1   = 'checked';
            break;
            case '2':
                $this->_yourRule2   = 'checked';
            break;
            default:
                $this->_yourRule    = NULL;
                $this->_yourRule0   = '';
                $this->_yourRule1   = '';
                $this->_yourRule2   = '';
        }
    }

    /**
     * Autor Kontaktoptionen escapen
     * URL / Email / Nummern / Username
     */
    public function get_AuthorURLEmailNumber($string = '', $allowedEmail = false, $allowedURL = false, $allowedNumber = false, $allowedString = false)
    {
        if (!empty($string))
        {

            // Charset Telephone oder Mobilenummern
            $validChars = "<^((\\+|00)[1-9]\\d{0,3}|0 ?[1-9]|\\(00? ?[1-9][\\d ]*\\))[\\d\\-/ ]*$>";

            $string = kps_sanitize_field($string);

            $url    = $string; // URL
            $email  = esc_html(sanitize_email($string)); // Email
            $number = preg_replace("![^+0-9]!", "", $string); // nur Zahlen und + am Anfang des Strings

            // Email
            if ($allowedEmail === true AND is_email($email) !== false)
            {
                return $email;
            }

            // Telephone oder Mobilenumbers
            if ($allowedNumber === true AND preg_match($validChars, $number) !== 0)
            {
                $number = preg_replace("<^\\+>", "00", $number); // + durch 00 ersetzen
                $number = preg_replace("<\\D+>", "", $number); // Nur Zahlen erlauben mit einem +

                return $number;
            }

            // URL
            if ($allowedURL === true AND filter_var($url, FILTER_VALIDATE_URL) !== false)
            {
                return esc_url($url);
            }

            // String z.B. Username
            if ($allowedString === true AND $string != '')
            {
                return $string;
            }
        }
        else
        {
            $string = '';

            return $string;
        }
    }

    /**
     * Autoren IP und Host ermitteln
     */
    public function get_authorIpHost()
    {
        // Author-IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $this->_authorIp = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $this->_authorIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $this->_authorIp = $_SERVER['REMOTE_ADDR'];
        }

        // Autor-Host
        $this->_authorHost = gethostbyname($_SERVER['REMOTE_ADDR']);
    }

    /**
     * zusätzliche Kontaktdaten serialisieren
     * Array zusammensetzen, Key => Translation
     */
    public function get_serial( $authorTelephone = '',
                                $authorMobile = '',
                                $authorSignal = '',
                                $authorViper = '',
                                $authorTelegram = '',
                                $authorWhatsapp = '',
                                $authorHoccer = '',
                                $authorWire = '',
                                $authorSkype = '',
                                $authorFacebookMessenger = '',
                                $authorWebsite = '',
                                $authorFacebook = '',
                                $authorInstagram = ''
                            )
    {
        if (!empty($authorTelephone))
        {
            $authorContactData['authorTelephone'] = $authorTelephone;
        }

        if (!empty($authorMobile))
        {
            $authorContactData['authorMobile'] = $authorMobile;
        }

        if (!empty($authorSignal))
        {
            $authorContactData['authorSignal'] = $authorSignal;
        }

        if (!empty($authorViper))
        {
            $authorContactData['authorViper'] = $authorViper;
        }

        if (!empty($authorTelegram))
        {
            $authorContactData['authorTelegram'] = $authorTelegram;
        }

        if (!empty($authorWhatsapp))
        {
            $authorContactData['authorWhatsapp'] = $authorWhatsapp;
        }

        if (!empty($authorHoccer))
        {
            $authorContactData['authorHoccer'] = $authorHoccer;
        }

        if (!empty($authorWire))
        {
            $authorContactData['authorWire'] = $authorWire;
        }

        if (!empty($authorSkype))
        {
            $authorContactData['authorSkype'] = $authorSkype;
        }

        if (!empty($authorFacebookMessenger))
        {
            $authorContactData['authorFacebookMessenger'] = $authorFacebookMessenger;
        }

        if (!empty($authorWebsite))
        {
            $authorContactData['authorWebsite'] = $authorWebsite;
        }

        if (!empty($authorFacebook))
        {
            $authorContactData['authorFacebook'] = $authorFacebook;
        }

        if (!empty($authorInstagram))
        {
            $authorContactData['authorInstagram'] = $authorInstagram;
        }

        $this->_authorContactData = serialize($authorContactData);
    }

    /**
     * Aktivierungs-Email versenden
     */
    public function sendActivation($deletePassword = '', $activationHash = '', $deleteHash = '', $pageUrl= '')
    {
        // Hole Email-Vorlagen Einstellungen
        $authorMailSettings = kps_unserialize(get_option('kps_authorMailSettings', false));

        if ($authorMailSettings === false )
        {
            $authorMailSubject  = esc_html(__('Activation', 'kps'));
            $auhorMailContent   =
esc_html(__('You have just posted a new entry on %blogname%.

To be able to publish it, you have to confirm it via the link
below and enter your email address. If you do not release this
post, it will be deleted automatically on %erasedatetime% from
our database.

Activate entry:
*******************
%linkaactivation%

Delete entry:
*******************
Password: %erasepassword%
%linkdelete%

Your entry:
*******************
Entry written on: %setdate%
Entry released on: %unlockdatetime%
Entry will be deleted on: %erasedatetime%

%entrycontent%

Your contact details:
*******************
Name: %authorname%
Email: %authoremail%
%authorcontactdata%

Many Thanks!
Your team
%blogname%
%blogurl%
%blogemail%', 'kps'));
        }
        else
        {
            $authorMailSubject  = esc_attr($authorMailSettings['kpsActivationSubject']);
            $auhorMailContent   = esc_attr($authorMailSettings['kpsActivationContent']);
        }

        // zusätzliche Kontaktdaten
        $authorContactData = kps_unserialize($this->_authorContactData);

        // Übersetzung der zusätzlichen Kontaktinformationen
        if (!empty($authorContactData) AND $authorContactData != '' AND is_array($authorContactData) AND !is_string($authorContactData))
        {
            $authorContactInfo  = '';

            // Übersetzungen
            foreach ($authorContactData AS $key => $value)
            {
                if( $key == 'authorTelephone')
                {
                    $authorContactInfo .= esc_html(__('Telephone', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorMobile')
                {
                    $authorContactInfo .= esc_html(__('Mobile Phone', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorSignal')
                {
                    $authorContactInfo .= esc_html(__('Signal-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorViper')
                {
                    $authorContactInfo .= esc_html(__('Viper-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorTelegram')
                {
                    $authorContactInfo .= esc_html(__('Telegram-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorWhatsapp')
                {
                    $authorContactInfo .= esc_html(__('Whatsapp-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorHoccer')
                {
                    $authorContactInfo .= esc_html(__('Hoccer-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorWire')
                {
                    $authorContactInfo .= esc_html(__('Wire-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorSkype')
                {
                    $authorContactInfo .= esc_html(__('Skype-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorFacebookMessenger')
                {
                    $authorContactInfo .= esc_html(__('Facebook-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorWebsite')
                {
                    $authorContactInfo .= esc_html(__('Website', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorFacebook')
                {
                    $authorContactInfo .= esc_html(__('Facebook', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorInstagram')
                {
                    $authorContactInfo .= esc_html(__('Instagram', 'kps')) . ": " . $value . " \r\n";
                }
                else
                {
                    $authorContactInfo .= ' \r\n';
                }
            }
        }

        // Links
        $activationLink = esc_url_raw($pageUrl . '&kps_akey=' . $activationHash);
        $deletelink     = esc_url_raw($pageUrl . '&kps_dkey=' . $deleteHash);

        // Zeit
        if ($this->_outputSettings['kpsEmailDeleteTime'] === 'true')
        {
            $deleteDateTime = date_i18n(get_option('date_format'), time() + get_option('kps_deleteNoEntryTime', false)) . ', ' . date_i18n(get_option('time_format'), time() + get_option('kps_deleteNoEntryTime', false));
        }
        else
        {
            $deleteDateTime = date_i18n(get_option('date_format'), time() + get_option('kps_deleteNoEntryTime', false));
        }
        if ($this->_outputSettings['kpsEmailSetTime'] === 'true')
        {
            $setDateTime = date_i18n(get_option('date_format'), time()) . ', ' . date_i18n(get_option('time_format'), time());
        }
        else
        {
            $setDateTime = date_i18n(get_option('date_format'), time());
        }

        // Ersetze Shorttags
        $postShorttags = array(
            '%blogname%'            => get_bloginfo('name'),
            '%blogurl%'             => get_bloginfo('wpurl'),
            '%blogemail%'           => get_option('kps_mailFrom', false),
            '%authorname%'          => $this->_authorName,
            '%authoremail%'         => $this->_authorEmail,
            '%authorcontactdata%'   => $authorContactInfo,
            '%entrycontent%'        => $this->_authorEntry,
            '%linkactivation%'      => $activationLink,
            '%linkdelete%'          => $deletelink,
            '%erasepassword%'       => $deletePassword,
            '%setdate%'             => $setDateTime,
            '%unlockdatetime%'      => esc_html(__('Wait for release from the Author', 'kps')),
            '%erasedatetime%'       => $deleteDateTime
        );

        $auhorMailContent = str_replace(array_keys($postShorttags) , $postShorttags, $auhorMailContent);

        // Email versenden
        $headers = 'From: ' . get_bloginfo('name'). ' <' .  esc_attr(get_option('kps_MailFrom', false)) . '>';
        $this->_activationEmailIsSend = wp_mail( esc_attr($this->_authorEmail), $authorMailSubject, $auhorMailContent, $headers);

        return $this->_activationEmailIsSend; // Rückgabe des Wertes
    }

    /**
     * Admin-Email bei Freigabe von Einträgen versenden
     */
    public function sendAdminActivation()
    {
        global $wpdb;

        $adminActivationSubject = esc_html(__('Release required', 'kps')) . ': ' . get_bloginfo('name');
        $adminActivationMessage = esc_html(__('A new entry in the Climbing-Partner-Search is available and requires your approval!', 'kps'));
        $adminActivationMessage .= '

' . KPS_ADMIN_URL . '/entries.php&edit_id=' . $wpdb->insert_id . '

' . get_bloginfo('name') . '
' . get_bloginfo('wpurl');

        // Email versenden
        $headers = 'From: ' . get_bloginfo('name'). ' <' .  esc_attr(get_option('kps_MailFrom', false)) . '>';
        $this->_adminActivationIsSend = wp_mail( esc_attr(get_option('kps_MailFrom', false)), $adminActivationSubject, $adminActivationMessage, $headers);

        return $this->_adminActivationIsSend; // Rückgabe des Wertes
    }

    /**
     * Email-Kopie versenden
     */
    public function sendEmailCopy()
    {
        $emailCopySubject = esc_html(__('New entry', 'kps')) . ': ' . get_bloginfo('name');
        $emailCopyMessage = esc_html(__('There is a new entry in the Climbing-Partner-Search!', 'kps'));
        $emailCopyMessage .= '

' . get_bloginfo('name') . '
' . get_bloginfo('wpurl');

        // Email versenden
        $headers = 'From: ' . get_bloginfo('name'). ' <' .  esc_attr(get_option('kps_MailFrom', false)) . '>';
        $this->_emailCopyIsSend = wp_mail( esc_attr($this->_emailCopyCC['kpsEmailCC']), $emailCopySubject, $emailCopyMessage, $headers);

        return $this->_emailCopyIsSend; // Rückgabe des Wertes
    }

    /**
     * Ausgabe
     */
    public function show_activationEmailIsSend()
    {
        return $this->_activationEmailIsSend;
    }

    public function show_emailCopyIsSend()
    {
        return $this->_emailCopyIsSend;
    }

    public function show_isInsertDB()
    {
        return $this->_isInsertDB;
    }

    public function show_isNotFound()
    {
        return $this->_isNotFound;
    }
}