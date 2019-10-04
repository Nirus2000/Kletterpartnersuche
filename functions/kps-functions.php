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
    wp_die(__('No direct calls allowed!'));
}

/**
 * Funktion IconPak
 */
if (!function_exists('kps_iconPak'))
{
    function kps_iconPak() {
        // Hole Icon-Pak
        $iconPak = get_option('kps_icon', false);

        // Icons Schwarz
        if ($iconPak === '0') { $iconPak = array('color' => 'black', 'size' => '55'); }
        elseif ($iconPak === '1') { $iconPak = array('color' => 'black', 'size' => '50'); }
        elseif ($iconPak === '2') { $iconPak = array('color' => 'black', 'size' => '45'); }
        elseif ($iconPak === '3') { $iconPak = array('color' => 'black', 'size' => '40'); }
        elseif ($iconPak === '4') { $iconPak = array('color' => 'black', 'size' => '35'); }
        // Icons Blau
        elseif ($iconPak === '5') { $iconPak = array('color' => 'blue', 'size' => '55'); }
        elseif ($iconPak === '6') { $iconPak = array('color' => 'blue', 'size' => '50'); }
        elseif ($iconPak === '7') { $iconPak = array('color' => 'blue', 'size' => '45'); }
        elseif ($iconPak === '8') { $iconPak = array('color' => 'blue', 'size' => '40'); }
        elseif ($iconPak === '9') { $iconPak = array('color' => 'blue', 'size' => '35'); }
        // Icons Orange
        elseif ($iconPak === '10') { $iconPak = array('color' => 'orange', 'size' => '55'); }
        elseif ($iconPak === '11') { $iconPak = array('color' => 'orange', 'size' => '50'); }
        elseif ($iconPak === '12') { $iconPak = array('color' => 'orange', 'size' => '45'); }
        elseif ($iconPak === '13') { $iconPak = array('color' => 'orange', 'size' => '40'); }
        elseif ($iconPak === '14') { $iconPak = array('color' => 'orange', 'size' => '35'); }
        // Icons Grün
        elseif ($iconPak === '15') { $iconPak = array('color' => 'green', 'size' => '55'); }
        elseif ($iconPak === '16') { $iconPak = array('color' => 'green', 'size' => '50'); }
        elseif ($iconPak === '17') { $iconPak = array('color' => 'green', 'size' => '45'); }
        elseif ($iconPak === '18') { $iconPak = array('color' => 'green', 'size' => '40'); }
        elseif ($iconPak === '19') { $iconPak = array('color' => 'green', 'size' => '35'); }
        // Icons Gelb
        elseif ($iconPak === '20') { $iconPak = array('color' => 'yellow', 'size' => '55'); }
        elseif ($iconPak === '21') { $iconPak = array('color' => 'yellow', 'size' => '50'); }
        elseif ($iconPak === '22') { $iconPak = array('color' => 'yellow', 'size' => '45'); }
        elseif ($iconPak === '23') { $iconPak = array('color' => 'yellow', 'size' => '40'); }
        elseif ($iconPak === '24') { $iconPak = array('color' => 'yellow', 'size' => '35'); }
        // default
        else { $iconPak = array('color' => 'green', 'size' => '45'); }

        return $iconPak;
    }
}
/**
 * Funktion zusätzliche Kontaktinformationen
 * Prüft die zusätzlichen Kontaktinformationen, falls vorhanden
 * Key => Übersetzung
 */
if (!function_exists('kps_contact_informations'))
{
    function kps_contact_informations($string = '') {

        $array = kps_unserialize($string);

        if (is_array($array) AND !empty($array))
        {
            $data = '';

            foreach ($array AS $key => $value)
            {
                if( $key == 'authorTelephone')
                {
                    $data .= esc_html__('Telephone', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorMobile')
                {
                    $data .= esc_html__('Mobile Phone', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorSignal')
                {
                    $data .= esc_html__('Signal-Messenger', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorViper')
                {
                    $data .= esc_html__('Viper-Messenger', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorTelegram')
                {
                    $data .= esc_html__('Telegram-Messenger', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorThreema')
                {
                    $data .= esc_html__('Threema-Messenger', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorWhatsapp')
                {
                    $data .= esc_html__('Whatsapp-Messenger', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorHoccer')
                {
                    $data .= esc_html__('Hoccer-Messenger', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorWire')
                {
                    $data .= esc_html__('Wire-Messenger', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorSkype')
                {
                    $data .= esc_html__('Skype-Messenger', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorFacebookMessenger')
                {
                    $data .= esc_html__('Facebook-Messenger', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorWebsite')
                {
                    $data .= esc_html__('Website', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorFacebook')
                {
                    $data .= esc_html__('Facebook', 'kps') . ": " . $value . " \r\n";
                }
                elseif( $key == 'authorInstagram')
                {
                    $data .= esc_html__('Instagram', 'kps') . ": " . $value . " \r\n";
                }
                else
                {
                    $data .= ' \r\n';
                }
            }

            return $data;
        }
        return false;
    }
}

/**
 * Funktion Min-Max-Default Range
 * $int     -> Variable
 * $min     -> Minimum
 * $max     -> Maximum
 * $default -> Defaultwert
 */
if (!function_exists('kps_min_max_default_range'))
{
    function kps_min_max_default_range($int = '', $min = '', $max = '', $default = '' ) {
        if ($int >= $min && $int <= $max)
        {
            return $int;
        }
        else
        {
            return $default;
        }
    }
}

/**
 * Funktion Permalink-Id
 * Hole alle ID's, wo der Shortcode eingesetzt wird
 */
if (!function_exists('kps_PermalinksWithShortCode'))
{
    function kps_PermalinksWithShortCode() {
        ob_start();
        $permaLinkId = array();

    	$theQuery = new WP_Query(  array(
                                        'post_type'           => 'any',             // Status des Post
                                        'ignore_sticky_posts' => true,              // Ignore the procedure
                                        's'                   => 'kps-shortcode',   // Shortcode
                                    )
        );

    	if ($theQuery->have_posts())
        {
    		// Alle Id's in das Array schreiben
            while ($theQuery->have_posts())
            {
                $theQuery->the_post();
    			$permaLinkId[] = get_the_ID();
            }
    		wp_reset_postdata(); // Reset
    	}

    	return $permaLinkId;
        return ob_get_clean();
    }
}

/**
 * Funktion Array Withelist
 * Nur diese Arrays sind erlaubt
 */
if (!function_exists('kps_array_whitelist_assoc'))
{
    function kps_array_whitelist_assoc($array1 = '', $array2 = '')
    {
        if (is_array($array1) && is_array($array2))
        {
            if (func_num_args() > 2)
            {
                $args = func_get_args();
                array_shift($args);
                $array2 = call_user_func_array('array_merge', $args);
            }

            /*
             * Leerzeichen entfernen im Value des Array's
             * Wird nur auf erste Dimension des Array's' angewendet!
             */
            $multiArray = array_filter($array1,'is_array');
            if(count($multiArray) == 0)
            {
                $array1 = array_map('trim', $array1);
            }
            return array_intersect_key($array1, array_flip($array2));
        }
        return false;
    }
}

/**
 * Funktion Sanitize Textarea
 */
if (!function_exists('kps_sanitize_textarea'))
{
    function kps_sanitize_textarea($textarea = '')
    {
        $textarea = str_replace("&nbsp;", "", $textarea); // Leerzeichen entfernen
        $textarea = str_replace("&#160;", "", $textarea); // Leerzeichen entfernen
        $textarea = str_replace("&#x00A0;", "", $textarea); // Leerzeichen entfernen
        $textarea = strval($textarea); // Variable in String konvertieren
        $textarea = htmlspecialchars_decode($textarea, ENT_COMPAT); // Konvertiert Anführungszeichen doppelt
        $textarea = strip_tags($textarea); // Entfernt HTML und PHP Tags
        $textarea = stripslashes($textarea); // Entfernt Maskierungszeichen aus einem String.
        $textarea = str_replace('\\', '&#92;', $textarea); // Ersetzt alle Vorkommen des Suchstrings durch einen anderen String
        $textarea = str_replace('"', '&quot;', $textarea); // Ersetzt alle Vorkommen des Suchstrings durch einen anderen String
        $textarea = str_replace("'", '&#39;', $textarea); // Ersetzt alle Vorkommen des Suchstrings durch einen anderen String
        $textarea = trim($textarea); // Entfernt Whitespaces (oder andere Zeichen) am Anfang und Ende eines Strings
        $textarea = sanitize_textarea_field($textarea);
        return $textarea;
    }
}

/**
 * Funktion Sanitize Field
 */
if (!function_exists('kps_sanitize_field'))
{
    function kps_sanitize_field($string = '')
    {
        $string = str_replace("&nbsp;", "", $string); // Leerzeichen entfernen
        $string = str_replace("&#160;", "", $string); // Leerzeichen entfernen
        $string = str_replace("&#x00A0;", "", $string); // Leerzeichen entfernen
        $string = strval($string); // Variable in String konvertieren
        $string = htmlspecialchars_decode($string, ENT_COMPAT); // Konvertiert Anführungszeichen doppelt
        $string = strip_tags($string); // Entfernt HTML und PHP Tags
        $string = stripslashes($string); // Entfernt Maskierungszeichen aus einem String.
        $string = str_replace('\\', '&#92;', $string); // Ersetzt alle Vorkommen des Suchstrings durch einen anderen String
        $string = str_replace('"', '&quot;', $string); // Ersetzt alle Vorkommen des Suchstrings durch einen anderen String
        $string = str_replace("'", '&#39;', $string); // Ersetzt alle Vorkommen des Suchstrings durch einen anderen String
        $string = trim($string); // Entfernt Whitespaces (oder andere Zeichen) am Anfang und Ende eines Strings
        $string = sanitize_text_field($string);
        return $string;
    }
}

/**
 * Funktion Unserialize
 */
if (!function_exists('kps_unserialize'))
{
    function kps_unserialize($string = '')
    {
        if (is_string($string) && !empty($string) && is_serialized($string))
        {
            return maybe_unserialize($string);
        }
        return false;
    }
}

/**
 * Funktion Ersten Zeichen einen Strings
 */
if (!function_exists('kps_getFirstChars'))
{
    function kps_getFirstChars($string, $length) {
        if (is_string($string))
        {
            return mb_substr($string, 0, $length);
        }
        return false;
    }
}

/**
 * Funktion Letzten Zeichen eines Strings
 */
if (!function_exists('kps_getLastChars'))
{
    function kps_getLastChars($string, $length) {
        if (is_string($string))
        {
            return mb_substr($string, -$length, $length);
        }
        return false;
    }
}