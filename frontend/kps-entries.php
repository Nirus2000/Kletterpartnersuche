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
 * Formular zusammensetzen
 */
function kps_frontend_entries($kps_paged = '1')
{
    global $wpdb;

    // Template unterstützt HTML5
    $html5 = current_theme_supports('html5');

    // Hole Legenden Einstellungen
    $legendSettings     = kps_unserialize(get_option('kps_legend', false));
    $legendIconPak      = ($legendSettings['kpsLegendIconPak'] === '0') ? '25' : '25_t';
    $legendActivated    = ($legendSettings['kpslegendActivated'] === 'true') ? true : false;

    // Hole die derzeitige Seite
    $getPage = (isset($kps_paged) && is_numeric(absint($kps_paged)) && $kps_paged != 0) ? floor(absint($kps_paged)) : 1;

    // Hole Maximalanzahl der Einträge pro Seite
    $maxEntriesPerPage = get_option('kps_frontendPagination', false);

    // Paginagtion erstellen
    $totalEntriesCount  = $wpdb->get_results("SELECT * FROM " . KPS_TABLE_ENTRIES . " WHERE isLockedByAdmin = 1 AND isLocked = 1 AND lockedAutoReport = 0 AND deleteDateTime < NOW() ", object);
    $countTotalEntries  = $wpdb->num_rows; // Anzahl der Einträge
    $totalPages         = ceil($countTotalEntries / $maxEntriesPerPage); // Aufrunden
    $lastPage           = $totalPages - 1; // letzte Seite
    $previosPage        = $getPage - 1; // Verhergehende Seite
    $nextPage           = $getPage + 1; // Nächste Seite
    $limits             = (int)($getPage - 1) * $maxEntriesPerPage; // Limit für Query
    $pageUrl            = get_post_permalink(); // Hole die derzeitige Post-ID von Wordpress

    // Alle Einträge pro Seite aus Datenbank holen
    $results = $wpdb->get_results("SELECT id FROM " . KPS_TABLE_ENTRIES . " WHERE isLockedByAdmin = 1 AND isLocked = 1 AND lockedAutoReport = 0 AND deleteDateTime < NOW() ORDER BY unlockDateTime DESC LIMIT {$limits}, {$maxEntriesPerPage}", object);

    // Blätterfunktion
    if ($countTotalEntries > 0)
    {
        $firstEntry     = ($getPage - 1) * $maxEntriesPerPage + 1;
        $totalPerPage   = $countTotalEntries - (($getPage - 1) * $maxEntriesPerPage);
        if ($totalPerPage > $maxEntriesPerPage)
        {
            $totalPerPage = $maxEntriesPerPage;
        }
        $lastEntry = $firstEntry + $totalPerPage - 1;

        // Blätterfunktion im Frontend
        $pagination = kps_pagination_frontend($totalPages, $getPage, $lastPage, $previosPage, $nextPage, $pageUrl);
    }

    // HTML5
    if ($html5)
    {
        $output = '<header>';
        $output .= '<nav>';

        // Paginagtion
        if ($countTotalEntries > 0)
        {
            $output .= '<div class="page-navigation">' . $pagination . '</div>';
        }
    }
    else
    {
        // Paginagtion
        if ($countTotalEntries > 0)
        {
            $output .= '<div class="page-navigation">' . $pagination . '</div>';
        }
    }

    // HTML5
    if ($html5)
    {
        $output .= '</nav>';
        $output .= '</header>';
    }
    $output .= '<div id="kps-entries">';

    // HTML5
    if ($html5)
    {
        $output .= '<main>';
    }

    // Prüfe, ob ein Custom-Template im Themenordner vorhanden ist.
    if (locate_template(array(
        'kps-entries.php'
    ) , true, true) == '')
    {
        // Lade Default Template
        $output .= '<!-- KPS-Default Template -->';
        require_once ( KPS_DIR . '/frontend/kps-entries.php');
    }
    else
    {
        $output .= '<!-- KPS-Custom Template -->';
    }

    if (is_array($results) && count($results) > 0)
    {
        // Foreach-Schleife für Ausgabe des Templates
        foreach ($results as $entry)
        {
            if ($html5)
            {
                $output .= '<article>';
            }

            $entries = kps_template($entry);

            // Ausgabe aller Einträge
            $output .= $entries;

            if ($html5)
            {
                $output .= '</article>';
            }
        }
    }
    else
    {
        // HTML5
        if ($html5)
        {
            $output .= '<article>';
        }

        $output .= '<div style="text-align: center;">' . esc_html(__('No entries available!', 'kps')) . '</div>';

        // HTML5
        if ($html5)
        {
            $output .= '</article>';
        }

    }

    // HTML5
    if ($html5)
    {
        $output .= '</main>';
    }

    $output .= '</div>';

    // HTML5
    if ($html5)
    {
        $output .= '<header>';
        $output .= '<nav>';
    }

    // Paginagtion
    if ($countTotalEntries > 0)
    {
        $output .= '<div class="page-navigation">' . $pagination . '</div>';
    }

    if ($legendActivated === true && count($results) > 0)
    {
        $output .=  '
                    <div class="kps-divTable">
                    	<div class="kps-divTableBody">
                    		<div class="kps-divTableRow">
                    			<div class="kps-divTableCell kps-legend"><img class="kps-legend" src="' . KPS_RELATIV . '/frontend/gfx/hall_' . $legendIconPak . '.png"" alt="' . esc_html(__('Hall', 'kps')) . '" title="' . esc_html(__('Hall', 'kps')) . '" />&#160;' . esc_html(__('Hall', 'kps')) .'</div>
                    			<div class="kps-divTableCell kps-legend"><img class="kps-legend" src="' . KPS_RELATIV . '/frontend/gfx/nature_' . $legendIconPak . '.png"" alt="' . esc_html(__('Climbing', 'kps')) . '" title="' . esc_html(__('Climbing', 'kps')) . '" />&#160;' . esc_html(__('Climbing', 'kps')) .'</div>
                    			<div class="kps-divTableCell kps-legend"><img class="kps-legend" src="' . KPS_RELATIV . '/frontend/gfx/trekking_' . $legendIconPak . '.png"" alt="' . esc_html(__('Walking/Trekking', 'kps')) . '" title="' . esc_html(__('Walking/Trekking', 'kps')) . '" />&#160;' . esc_html(__('Walking/Trekking', 'kps')) .'</div>
                    			<div class="kps-divTableCell kps-legend"><img class="kps-legend" src="' . KPS_RELATIV . '/frontend/gfx/travel_' . $legendIconPak . '.png"" alt="' . esc_html(__('Travels', 'kps')) . '" title="' . esc_html(__('Travels', 'kps')) . '" />&#160;' . esc_html(__('Travels', 'kps')) .'</div>
                                <div class="kps-divTableCell kps-legend"><img class="kps-legend" src="' . KPS_RELATIV . '/frontend/gfx/onetime_' . $legendIconPak . '.png"" alt="' . esc_html(__('Unique', 'kps')) . '" title="' . esc_html(__('Unique', 'kps')) . '" />&#160;' . esc_html(__('Unique', 'kps')) .'</div>
                    		</div>
                    		<div class="kps-divTableRow">
                    			<div class="kps-divTableCell kps-legend"><img class="kps-legend" src="' . KPS_RELATIV . '/frontend/gfx/moretime_' . $legendIconPak . '.png"" alt="' . esc_html(__('Regularly', 'kps')) . '" title="' . esc_html(__('Regularly', 'kps')) . '" />&#160;' . esc_html(__('Regularly', 'kps')) .'</div>
                    			<div class="kps-divTableCell kps-legend"><img class="kps-legend" src="' . KPS_RELATIV . '/frontend/gfx/goalone_' . $legendIconPak . '.png"" alt="' . esc_html(__('Single person', 'kps')) . '" title="' . esc_html(__('Single person', 'kps')) . '" />&#160;' . esc_html(__('Single person', 'kps')) .'</div>
                    			<div class="kps-divTableCell kps-legend"><img class="kps-legend" src="' . KPS_RELATIV . '/frontend/gfx/family_' . $legendIconPak . '.png"" alt="' . esc_html(__('Family', 'kps')) . '" title="' . esc_html(__('Family', 'kps')) . '" />&#160;' . esc_html(__('Family', 'kps')) .'</div>
                                <div class="kps-divTableCell kps-legend"><img class="kps-legend" src="' . KPS_RELATIV . '/frontend/gfx/comeclub_' . $legendIconPak . '.png"" alt="' . esc_html(__('Club/Group', 'kps')) . '" title="' . esc_html(__('Club/Group', 'kps')) . '" />&#160;' . esc_html(__('Club/Group', 'kps')) .'</div>
                                <div class="kps-divTableCell kps-legend"></div>
                    		</div>
                    	</div>
                    </div>
                    ';
    }

    // HTML5
    if ($html5)
    {
        $output .= '</nav>';
        $output .= '</header>';
    }
    return $output;
}
