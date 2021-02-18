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
            $this->_setDateTime         = $data->show_emailSetDateTime();
            $this->_unlockDateTime      = $data->show_emailUnlockDateTime();
            $this->_deleteDateTime      = $data->show_emailDeleteDateTime();
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
     * Email versenden
     */
    public function sendEmail($email = '', $authorContactData = array())
    {
        // Hole Email-Vorlage an Autor
        $requirementMailSettings = kps_unserialize(get_option('kps_userMailSettings', false));
        $requirementMail = kps_mailcontent_requirement($requirementMailSettings);

        // zusätzliche Kontaktdaten
        $authorContactData = kps_contact_informations($authorContactData);

        // Ersetze Shorttags
        $postShorttags = array(
            '%blogname%'            => get_bloginfo('name'),
            '%blogurl%'             => get_bloginfo('wpurl'),
            '%blogemail%'           => get_option('kps_mailFrom', false),
            '%authorname%'          => $this->_authorName,
            '%authoremail%'         => $this->_authorEmail,
            '%authorcontactdata%'   => $authorContactData,
            '%entrycontent%'        => $this->_authorContent,
            '%setdate%'             => $this->_setDateTime,
            '%unlockdatetime%'      => $this->_unlockDateTime,
            '%erasedatetime%'       => $this->_deleteDateTime
        );

        $requirementMail['Content'] = str_replace(array_keys($postShorttags) , $postShorttags, $requirementMail['Content']);

        // Email versenden
        $headers = 'From: ' . get_bloginfo('name'). ' <' .  esc_attr(get_option('kps_MailFrom', false)) . '>';
        $this->_isSend = wp_mail( $email, $requirementMail['Subject'], $requirementMail['Content'], $headers);

        // Gesamtzähler für Statistik
        $countKPSCounter = kps_unserialize(get_option('kps_kpsCounter', false));
        foreach ($countKPSCounter AS $key => $value)
        {
            if ($key == 'kpsAllSendRequirements') { $countKPSCounter[$key]++; }
        }
        update_option('kps_kpsCounter', serialize($countKPSCounter));

        return $this->_isSend; // Rückgabe des Wertes
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