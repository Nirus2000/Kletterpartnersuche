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
 * Delete-Class
 */
class kps_delete
{
    private $_codeData, // Datensatz
            $_isFound,  // Eintrag gefunden
            $_isDelete; // Eintrag gelöscht

    /**
    * Konstrukteur
    * Löschung des Eintrages
    */
    public function __construct($code = '', $password = '')
    {
        $this->_codeData    = (object)'';
        $this->_isDelete    = false;
        $this->_isFound     = false;

        // Lösch-Code finden, mit Passwort verifizieren
        if ($this->find($code, $password))
        {
            // Wenn True, dann in DB löschen
            if ($this->_isFound === true)
            {
                // Lösche Datensatz
                $this->set_delete($code, $this->_codeData->password);
            }
        }
        else
        {
            $this->_isDelete = false; // Eintrag wurde nicht gelöscht
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
     * Überprüfung, ob Code vorhanden ist
     */
    public function find($code = '', $password = '')
    {
        global $wpdb;

        if (isset($code) && isset($password))
        {
            // Suche in Datenbank
            $data = $wpdb->get_row("SELECT password, hash FROM " . KPS_TABLE_ENTRIES . " WHERE deleteHash = '" . $code . "'", object);
            $rowcount = $wpdb->num_rows; // Einträge zählen

            // Ist der Lösch-Code vorhanden
            if ($rowcount == 1)
            {
                $this->_codeData = $data; // Nimm das erste und einzige Suchergebnis

                // Verifiziere mit Passwort
                if( $this->_codeData->password === Hash::make( $password, $this->_codeData->hash ) )
                {
                    $this->_isFound = true; // Eintrag gefunden
                    return true; // Rückgabe des Wertes
                }
                else
                {
                    $this->_isFound = false; // Eintrag gefunden
                    return false; // Rückgabe des Wertes
                }
            }
            $this->_isFound = false; // Eintrag gefunden
            return false; // Rückgabe des Wertes
        }
        return false; // Rückgabe des Wertes
    }

    /**
     * Datensatz löschen
     */
    public function set_delete($code, $password = '')
    {
        global $wpdb;

        $this->_isDelete = $wpdb->delete(KPS_TABLE_ENTRIES, array(  'password'      => $password,
                                                                    'deleteHash'    => $code
                                                            ),
                                                            array(  '%s',
                                                                    '%s'
                                                            )
        );

        // Gesamtzähler für Statistik
        $countKPSCounter = kps_unserialize(get_option('kps_kpsCounter', false));
        foreach ($countKPSCounter AS $key => $value)
        {
            if ($key == 'kpsAllDeleteEntrys') { $countKPSCounter[$key]++; }
        }
        update_option('kps_kpsCounter', serialize($countKPSCounter));

        return $this->_isDelete; // Rückgabe des Wertes
    }

    /**
     * Überprüfung, ob Löschen des Eintrages erfolgreich
     */
    public function show_isDelete()
    {
        return $this->_isDelete; // Rückgabe des Wertes
    }

    /**
     * Eintrag gefunden
     */
    public function show_isFound()
    {
        return $this->_isFound; // Rückgabe des Wertes

    }
}