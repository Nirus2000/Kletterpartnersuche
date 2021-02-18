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
 * Activation-Class
 */
class kps_activation
{
    private $_codeData,     // Datensatz
            $_isFound,      // Eintrag gefunden
            $_isActivated,  // Überprüfter Aktivierungs-Email-Code
            $_isChecked;    // Überprüfter Aktivierungs-Email-Code

    /**
     * Konstrukteur
     * Aktivierungs-Email-Code überprüfen
     * Aktivierung des Eintrages
     */
    public function __construct($code = '', $email = '')
    {
        $this->_codeData    = (object)'';
        $this->_isChecked   = false;
        $this->_isFound     = false;
        $this->_isActivated = false;

        // Aktvierungscode finden und Freischalten
        if ($this->find($code, $email))
        {
            // Wenn True dann Eintrag aktivieren
            if ($this->_isFound === true)
            {
                $this->set_activation($this->_codeData->activationHash, $this->_codeData->authorEmail);
            }

            if ($this->_isChecked)
            {
                // Eintrag freigegeben
                $this->_isActivated = true;
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
     * Überprüfung, ob Aktivierungs-Email-Code vorhanden ist
     * als Gegenprüfung zur Email-Adresse
     */
    public function find($code = '', $email = '')
    {
        global $wpdb;

        // Überprüfen, ob der Aktivierungs-Email-Code existiert
        if (isset($code) && isset($email))
        {
            // Suche Aktivierungs-Email-Code in Datenbank
            $data = $wpdb->get_row("SELECT authorEmail, activationHash, isLocked FROM " . KPS_TABLE_ENTRIES . " WHERE activationHash = '" . $code . "' AND  authorEmail = '" . $email . "'", object);
            $rowcount = $wpdb->num_rows; // Einträge zählen

            // Ist der Aktivierungs-Email-Code vorhanden und es gibt nur einen Eintrag
            if ($rowcount == 1)
            {
                $this->_codeData = $data; // Nimm das erste und einzige Suchergebnis

                if ($this->_codeData->isLocked === '1')
                {
                    $this->_isActivated = true; // Eintrag schon freigegeben
                    $this->_isFound     = true; // Datensatz gefunden
                }
                else
                {
                    $this->_isFound = true; // Datensatz gefunden
                    return true; // Rückgabe des Wertes
                }
            }
        }
        return false; // Rückgabe des Wertes
    }

    /**
     * Eintrag aktivieren
     */
    public function set_activation($code = '', $email = '')
    {
        global $wpdb;

        if ($this->_isActivated === false)
        {
            // Löschzeit für freigebene Einträge holen
            $deleteTime = get_option('kps_deleteEntryTime', false);

            $this->_isChecked = $wpdb->update(KPS_TABLE_ENTRIES,array(  'isLocked'          => 1,
                                                                        'unlockDateTime'    => time(),
                                                                        'deleteDateTime'    => time() + $deleteTime
                                                                ),
                                                                array(  'authorEmail'       => $email,
                                                                        'activationHash'    => $code
                                                                ),
                                                                array(  '%d',
                                                                        '%d',
                                                                        '%d'
                                                                )
            );

            // Gesamtzähler für Statistik
            $countKPSCounter = kps_unserialize(get_option('kps_kpsCounter', false));
            foreach ($countKPSCounter AS $key => $value)
            {
                if ($key == 'kpsAllActivatedEntrys') { $countKPSCounter[$key]++; }
            }
            update_option('kps_kpsCounter', serialize($countKPSCounter));

            return $this->_isChecked; // Rückgabe des Wertes
        }
    }

    /**
     * Ausgabe
     */
    public function show_isActivated()
    {
        return $this->_isActivated; // Rückgabe des Wertes
    }

    public function show_isChecked()
    {
        return $this->_isChecked; // Rückgabe des Wertes
    }

    public function show_isFound()
    {
        return $this->_isFound; // Rückgabe des Wertes

    }
}