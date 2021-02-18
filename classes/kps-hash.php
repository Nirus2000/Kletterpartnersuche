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
 * Hash-Class
 */
class Hash
{
    /**
     * DoS
     */
    private function __clone()
    {
        // Denial of Service
    }

    /**
     * Erstellen eines Hash-Wertes
     */
    public static function make($string, $salt = '')
    {
        # Hash-Wert berechnen
        return hash('sha256', $string . $salt); // Rückgabe des Hash-Wertes
    }

    /**
     * Generiere den Salt
     */
    public static function salt($length)
    {
        # Erzeugt einen Initialisierungsvektor (IV) aus einer zufälligen Quelle
        return random_bytes($length); // Rückgabe des Salt-Wertes
    }

    /**
     * Erstelle ein einzigartigen Hast-Wert
     */
    public static function unique()
    {
        return self::make(uniqid());
    }

    /**
     * Erstelle Passwort
     */
    public static function generatePassword($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++)
        {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }
}

