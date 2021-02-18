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
 * Verify-Class
 */
class kps_verify
{
    private     $_hash,             // Hash
                $_verifyPassword,   // Passwort
                $_authorContent,    // Content
                $_setDateTime,      // Eintrag Timestamp
                $_unlockDateTime,   // Freigabe Timestamp
                $_deleteDateTime,   // Löschen Timestamp
                $_isInsertDB,       // Anforderung in Datenbank
                $_insertId,         // ID
                $_isFound,          // Eintrag gefunden
                $_isSend;           // Anforderung in Datenbank

    /**
     * Konstrukteur
     * Verifizierung für das zusenden von Kontaktdaten
     */
    public function __construct($id = 0, $email = '', $pageUrl = '')
    {
        $this->_authorContent   = (string )'';
        $this->_setDateTime     = (string )'';
        $this->_unlockDateTime  = (string )'';
        $this->_deleteDateTime  = (string )'';
        $this->_hash            = (string)'';
        $this->_verifyPassword  = (string)'';
        $this->_insertId        = (int)0;
        $this->_isInsertDB      = false;
        $this->_isFound         = false;
        $this->_isSend          = false;

        // Aktvierungscode finden und Freischalten
        if ($this->find($id))
        {
            // Wenn True dann Update DB
            if ($this->_isFound == true)
            {
                // Hash und Password erstellen
                $this->get_hash($email);

                // Anforderung in Datenbank schreiben
                $this->set_verify($id, $this->_hash, $this->_verifyPassword, $email);
            }
        }

        if ($this->_isInsertDB == true)
        {
            $this->sendEmail($this->_insertId, $email, $pageUrl);
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
     * Überprüfung, ob Eintrag vorhanden ist
     */
    public function find($id = 0)
    {
        global $wpdb;

        // Hole Eintrag aus Datenbank
        $data = new kps_entry_read($id);

        if (!empty($data))
        {
            $this->_authorContent   = $data->show_authorContent();
            $this->_setDateTime     = $data->show_emailSetDateTime();
            $this->_unlockDateTime  = $data->show_emailUnlockDateTime();
            $this->_deleteDateTime  = $data->show_emailDeleteDateTime();
            $this->_isFound         = $data->_isFound; // Datensatz gefunden
            return true; // Rückgabe des Wertes
        }

        return false; // Rückgabe des Wertes
    }

    /**
     * Email versenden
     */
    public function sendEmail($id = '', $email = '', $pageUrl='')
    {
        // Hole Email-Vorlagen Einstellungen
        $verifyMailSettings = kps_unserialize(get_option('kps_userMailContactSettings', false));
        $verifyMail = kps_mailcontent_verify($verifyMailSettings);

        // Shorttags
        $reglink        = esc_url_raw($pageUrl . '&kps_require=' . $id);
        $regPassword    = esc_url_raw($pageUrl . '&kps_require=' . $id);

        // Ersetze Shorttags
        $postShorttags = array(
            '%blogname%'        => get_bloginfo('name'),
            '%blogurl%'         => get_bloginfo('wpurl'),
            '%blogemail%'       => get_option('kps_mailFrom', false),
            '%entrycontent%'    => $this->_authorContent,
            '%regpassword%'     => $this->_verifyPassword,
            '%linkreg%'         => $reglink,
            '%setdate%'         => $this->_setDateTime,
            '%unlockdatetime%'  => $this->_unlockDateTime,
            '%erasedatetime%'   => $this->_deleteDateTime);

        $verifyMail['Content'] = str_replace(array_keys($postShorttags), $postShorttags, $verifyMail['Content']);

        // Email versenden
        $headers = 'From: ' . get_bloginfo('name'). ' <' .  esc_attr(get_option('kps_MailFrom', false)) . '>';
        $this->_isSend = wp_mail(esc_attr($email), $verifyMail['Subject'], $verifyMail['Content'], $headers);

        return $this->_isSend; // Rückgabe des Wertes
    }

    /**
     * Hash und Passwort erzeugen
     */
    public function get_hash($email = '')
    {
        // Überprüfen, ob der Aktivierungs-Email-Code existiert
        if (isset($email))
        {
            // Salt generieren
            $salt = Hash::salt(32); // Salt erstellen mit einer Zeichenlänge von 32

            // Hash generieren
            $this->_hash = Hash::make($email, $salt); // Hash erzeugen

            // Passwort generieren
            $this->_verifyPassword = Hash::generatePassword(); // Passwort generieren

            return true; // Rückgabe des Wertes
        }

        return false; // Rückgabe des Wertes
    }

    /**
     * Verifizierungaanfrage in Datenbank schreiben
     * Gültigkeit der Verifizierngsanfrage sind 24 Stunden
     */
    public function set_verify($id = '', $hash = '', $password = '', $email = '')
    {
        global $wpdb;

        // Verifizierung
        if (isset($id) && isset($hash) && isset($password) && isset($email))
        {
            // Schreibe in Datenbank
            $insertData = array(
                'id'            => '',
                'entryId'       => $id,
                'password'      => Hash::make($this->_verifyPassword, $this->_hash),
                'hash'          => $hash,
                'userEmail'     => $email,
                'timestamp'     => time(),
                'expire'        => time() + 86400,
                'sendTimestamp' => 0,
                'sendData'      => 0
            );
            $insertFormat = array(
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%d',
                '%d'
            );

            $this->_isInsertDB  = $wpdb->insert(KPS_TABLE_REQUIREMENT, $insertData, $insertFormat);
            $this->_insertId    = $wpdb->insert_id; // Datenbank-Id

            // Gesamtzähler für Statistik
            $countKPSCounter = kps_unserialize(get_option('kps_kpsCounter', false));
            foreach ($countKPSCounter AS $key => $value)
            {
                if ($key == 'kpsAllVerfifications') { $countKPSCounter[$key]++; }
            }
            update_option('kps_kpsCounter', serialize($countKPSCounter));

            return $this->_isInsertDB; // Rückgabe des Wertes
        }

        return false; // Rückgabe des Wertes
    }

    /**
     * Ausgabe
     */
    public function show_isFound()
    {
        return $this->_isFound; // Rückgabe des Wertes
    }

    public function show_isInsertDB()
    {
        return $this->_isInsertDB; // Rückgabe des Wertes
    }

    public function show_isSend()
    {
        return $this->_isSend; // Rückgabe des Wertes
    }
}
