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
 * Hauptfunktion
 */
function get_kps($atts)
{
    $shortCodeValues = shortcode_atts(array('button-write'      => 'true',
                                            'show-form-only'    => 'false',
                                            'button-text'       => ''
	                                   ), $atts);

    // Aktivierungs-Code
    if (isset($_GET['kps_akey']) && !empty($_GET['kps_akey']) && is_string($_GET['kps_akey']) && strlen($_GET['kps_akey']) == 32)
    {
        $kps_akey = trim($_GET['kps_akey']);
    }
    else
    {
        $kps_akey = '0';
    }

    // Löschungs-Code
    if (isset($_GET['kps_dkey']) && !empty($_GET['kps_dkey']) && is_string($_GET['kps_dkey']) && strlen($_GET['kps_dkey']) == 32)
    {
        $kps_dkey = trim($_GET['kps_dkey']);
    }
    else
    {
        $kps_dkey = '0';
    }

    // Verifizierung
    if (isset($_GET['kps_data']) && !empty($_GET['kps_data']) && is_string($_GET['kps_data']))
    {
        $kps_data = $_GET['kps_data'];
    }
    else
    {
        $kps_data = '0';
    }

    // Anforderung
    if (isset($_GET['kps_require']) && !empty($_GET['kps_require']) && is_string($_GET['kps_require']))
    {
        $kps_require = $_GET['kps_require'];
    }
    else
    {
        $kps_require = '0';
    }

    // Pagination
    if (isset($_GET['kps_paged']) && !empty($_GET['kps_paged']) && is_string($_GET['kps_paged']))
    {
        $kps_paged = $_GET['kps_paged'];
    }
    else
    {
        $kps_paged = '1';
    }

    // Eintrag melden
    if (isset($_GET['kps_report']) && !empty($_GET['kps_report']) && is_string($_GET['kps_report']))
    {
        $kps_report = $_GET['kps_report'];
    }
    else
    {
        $kps_report = 0;
    }

    // Ausgabe Start
    $output = '<div id="kps">';

    if (isset($kps_akey) && $kps_akey > '0' )
    {
        // Aktivierungs-Code
        $output .= kps_activationcode($kps_akey);
    }
    elseif (isset($kps_dkey) && $kps_dkey > '0' )
    {
        // Lösch-Code
        $output .= kps_deletecode($kps_dkey);
    }
    elseif (isset($kps_data) && $kps_data > '0' )
    {
        // Verifizierngs-ID
        $output .= kps_verifyId($kps_data);
    }
    elseif (isset($kps_require) && $kps_require > '0' )
    {
        // Anforderungs-ID
        $output .= kps_requirement($kps_require);
    }
    elseif (isset($kps_report) && $kps_report > '0' )
    {
        // Meldungs-ID
        $output .= kps_report($kps_report);
    }
    else
    {
        if ($shortCodeValues['button-write'] === 'true')
        {
            // Hole Formular
            $output .= kps_frontend_form($shortCodeValues);
        }

        if ($shortCodeValues['show-form-only'] === 'false')
        {
            // Hole Einträge
            $output .= kps_frontend_entries($kps_paged);
        }
    }

    // Ausgabe Ende
    $output .= '</div>';

    return $output; // Rückgabe
}
add_shortcode('kps-shortcode', 'get_kps'); // Shortcode festlegen