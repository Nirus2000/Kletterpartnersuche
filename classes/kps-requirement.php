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

/**
 * Andorderung-Class
 */
class kps_requirement
{
    private     $_codeData,         // Verifizierung
                $_isVerify,         // Verifiziert
                $_isFound,          // Eintrag gefunden
                $_isSend,           // Email versendet
                $_isClosed,         // Verifizierung abgeschlossen
                $_authorName,       // Autorname
                $_authorEmail,      // Autor Email-Adresse
                $_authorContent,    // Autor Eintrag
                $_setDateTime,      // Eintrag Timestamp
                $_unlockDateTime,   // Freigabe Timestamp
                $_deleteDateTime,   // Löschen Timestamp
                $_isExpire;         // Gültigkeitszeit

    /**
     * Konstrukteur
     * Konatkdaten an Anforderer versenden
     */
    public function __construct($id = 0, $password = '')
    {
        $this->_codeData            = (object)'';
        $this->_authorName          = (string)'';
        $this->_authorEmail         = (string)'';
        $this->_authorContent       = (string)'';
        $this->_authorContactData   = (array)'';
        $this->_setDate             = (string)'';
        $this->_deleteDateTime      = (string)'';
        $this->_unlockDateTime      = (string)'';
        $this->_isSend              = false;
        $this->_isFound             = false;
        $this->_isVerify            = false;
        $this->_isClosed            = false;
        $this->_isExpire            = false;

        // Hole Anforderung
        $this->find($id, $password);

        // Hole Eintrag und Versende Email
        $this->get_entry($this->_codeData->entryId);

        // Wenn True dann Update DB
        if ($this->_isVerify === true
            && $this->_isFound === true
            && $this->_isClosed === false
            && $this->_isExpire === false)
        {
            // Kontaktdaten versenden
            $this->sendEmail($this->_codeData->userEmail, $this->_authorContactData);
        }

        // Als versendet markieren
        $this->set_verify($id);
    }

    /**
     * DoS
     */
    private function __clone()
    {

        // Denial of Service
    }

    /**
     * Eintrag aus Datenbank laden
     */
    public function get_entry($id = 0)
    {
        global $wpdb;

        // Hole Eintrag aus Datenbank
        $data = new kps_entry_read($id);

        if (!empty($data))
        {
            $this->_authorName          = $data->show_authorName_raw();
            $this->_authorEmail         = $data->show_authorEmail_raw();
            $this->_authorContactData   = $data->show_authorContactData();
            $this->_authorContent       = esc_html($data->show_authorContent());
            $this->_setDateTime         = $data->show_setDateTime();
            $this->_unlockDateTime      = $data->show_unlockDateTime();
            $this->_deleteDateTime      = $data->show_deleteDateTime();
            $this->_isFound             = $data->_isFound; // Datensatz gefunden
            return true; // Rückgabe des Wertes
        }
    }

    /**
     * Anforderung aus Datenbank laden
     */
    public function find($id = '', $password = '')
    {
        global $wpdb;

        // Zeitstempel für Gültigkeit des Abrufes
        $timestemp = time();

        if (isset($id) && isset($password))
        {
            // Suche in Datenbank
            $data = $wpdb->get_row("SELECT * FROM " . KPS_TABLE_REQUIREMENT . " WHERE id  = '" . $id . "'", object);
            $rowcount = $wpdb->num_rows; // Einträge zählen

            // Ist die Verifizierung vorhanden
            if ($rowcount == 1)
            {
                $this->_codeData = $data; // Nimm das erste und einzige Suchergebnis

                // Verifiziere mit Passwort
                if( $this->_codeData->password === Hash::make( $password, $this->_codeData->hash ) )
                {
                    if ($this->_codeData->sendData === '1')
                    {
                        $this->_isClosed = true;
                    }

                    if ($this->_codeData->expire < time())
                    {
                        $this->_isExpire = true;
                    }

                    $this->_isVerify = true; // Eintrag gefunden
                    return true; // Rückgabe des Wertes
                }
                else
                {
                    $this->_isVerify = false; // Eintrag gefunden
                    return false; // Rückgabe des Wertes
                }
            }
            $this->_isVerify = false; // Eintrag gefunden
            return false; // Rückgabe des Wertes
        }
        return false; // Rückgabe des Wertes
    }

    /**
     * Verifizierung updated
     */
    public function set_verify($id = 0)
    {
        global $wpdb;

        if ($this->_isSend === true)
        {
            $this->_isClosed = $wpdb->update(KPS_TABLE_REQUIREMENT, array(  'sendTimestamp' => time(),
                                                                            'sendData'      => 1
                                                                    ),
                                                                    array(  'id' => $id
                                                                    ),
                                                                    array(  '%d'
                                                                    )
            );

            return $this->_isClosed; // Rückgabe des Wertes
        }

        return false; // Rückgabe des Wertes
    }

    /**
     * Emailversenden
     */
    public function sendEmail($email = '', $authorContactData = array())
    {
        // Hole Email-Vorlage an Autor
        $userMailSettings = kps_unserialize(get_option('kps_userMailSettings', false));

        if ($userMailSettings === false )
        {
            $userMailSubject    = esc_html(__('Anforderung', 'kps'));
            $userMailContent    =
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
            $userMailSubject    = esc_attr($userMailSettings['kpsContactDataSubject']);
            $userMailContent    = esc_attr($userMailSettings['kpsContactDataContent']);
        }

        // zusätzliche Kontaktdaten
        $setAuthorContactInfo = kps_unserialize($authorContactData);

        // Übersetzung der zusätzlichen Kontaktinformationen
        if (!empty($setAuthorContactInfo) AND $setAuthorContactInfo != "" AND is_array($setAuthorContactInfo))
        {
            foreach ($setAuthorContactInfo AS $key => $value)
            {
                if( $key == 'authorTelephone')
                {
                    $setAuthorContactInfoData .= esc_html(__('Telefon', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorMobile')
                {
                    $setAuthorContactInfoData .= esc_html(__('Handy', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorSignal')
                {
                    $setAuthorContactInfoData .= esc_html(__('Signal-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorViper')
                {
                    $setAuthorContactInfoData .= esc_html(__('Viper-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorTelegram')
                {
                    $setAuthorContactInfoData .= esc_html(__('Telegram-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorWhatsapp')
                {
                    $setAuthorContactInfoData .= esc_html(__('Whatsapp-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorHoccer')
                {
                    $setAuthorContactInfoData .= esc_html(__('Hoccer-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorWire')
                {
                    $setAuthorContactInfoData .= esc_html(__('Wire-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorSkype')
                {
                    $setAuthorContactInfoData .= esc_html(__('Skype-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorFacebookMessenger')
                {
                    $setAuthorContactInfoData .= esc_html(__('Facebook-Messenger', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorWebsite')
                {
                    $setAuthorContactInfoData .= esc_html(__('Webseite', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorFacebook')
                {
                    $setAuthorContactInfoData .= esc_html(__('Facebook', 'kps')) . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorInstagram')
                {
                    $setAuthorContactInfoData .= esc_html(__('Instagram', 'kps')) . ": " . $value . " \r\n";
                }
                else
                {
                    $setAuthorContactInfoData .= ' \r\n';
                }
            }
        }

        // Ersetze Shorttags
        $postShorttags = array(
            '%blogname%'            => get_bloginfo('name'),
            '%blogurl%'             => get_bloginfo('wpurl'),
            '%blogemail%'           => get_option('kps_mailFrom', false),
            '%authorname%'          => $this->_authorName,
            '%authoremail%'         => $this->_authorEmail,
            '%authorcontactdata%'   => $setAuthorContactInfoData,
            '%entrycontent%'        => $this->_authorContent,
            '%setdate%'             => $this->_setDateTime,
            '%erasedatetime%'       => $this->_deleteDateTime,
            '%unlockdatetime%'      => $this->_unlockDateTime
        );

        $userMailContent = str_replace(array_keys($postShorttags) , $postShorttags, $userMailContent);

        // Email versenden
        $this->_isSend = wp_mail( $email, $userMailSubject, $userMailContent );

        // Gesamtzähler für Statistik
        $countActivation    = kps_unserialize(get_option('kps_kpsCounter', false));
        $newSetKPSCounter['kpsAllEntrys']           = $countActivation['kpsAllEntrys'];
        $newSetKPSCounter['kpsAllActivatedEntrys']  = $countActivation['kpsAllActivatedEntrys'];
        $newSetKPSCounter['kpsAllVerfifications']   = $countActivation['kpsAllVerfifications'];
        $newSetKPSCounter['kpsAllSendRequirements'] = $countActivation['kpsAllSendRequirements'] + 1;
        $newSetKPSCounter['kpsAllDeleteEntrys']     = $countActivation['kpsAllDeleteEntrys'];
        $newSetKPSCounter = serialize($newSetKPSCounter);
        update_option('kps_kpsCounter', $newSetKPSCounter);
    }

    /**
     * Ausgabe
     */
    public function show_isExpire()
    {
        return $this->_isExpire; // Rückgabe des Wertes

    }

    public function show_isClosed()
    {
        return $this->_isClosed; // Rückgabe des Wertes

    }

    public function show_isVerify()
    {
        return $this->_isVerify; // Rückgabe des Wertes

    }

    public function show_isSend()
    {
        return $this->_isSend; // Rückgabe des Wertes

    }

    public function show_isFound()
    {
        return $this->_isFound; // Rückgabe des Wertes

    }
}