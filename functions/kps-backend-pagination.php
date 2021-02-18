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
 * Funktion Pagination
 * Blätterfunktion im Backend
 */
function kps_pagination_backend($totalPages, $getPage, $lastPage, $previosPage, $nextPage, $pageUrl)
{
    if (!$totalPages)
    {
        $totalPages = 1;
    }
    if (!$getPage)
    {
        $getPage = 1;
    }

    $adjacents = 3;
    $output = '<span>';

    if ($totalPages > 1)
    {
        if ($getPage > 1)
        {
            $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=1><span>&laquo;</span></a> ';
            $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=' . $previosPage . '><span>&lsaquo;</span></a> ';
        }
        if ($totalPages < 7 + ($adjacents * 2))
        {
            for ($counter = 1;$counter <= $totalPages;$counter++)
            {
                if ($counter == $getPage)
                {
                    $output .= '<span class="page-numbers current">' . $counter . '</span> ';
                }
                else
                {
                    $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=' . $counter . '>' . $counter . '</a> ';
                }
            }
        }
        elseif ($totalPages > 5 + ($adjacents * 2))
        {
            if ($getPage < 1 + ($adjacents * 2))
            {
                for ($counter = 1;$counter < 4 + ($adjacents * 2);$counter++)
                {
                    if ($counter == $getPage)
                    {
                        $output .= '<span class="page-numbers current">' . $counter . '</span>';
                    }
                    else
                    {
                        $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=' . $counter . '>' . $counter . '</a> ';
                    }
                }
                $output .= '<span class="page-numbers dots">...</span>';
                $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=' . $lastPage . '>' . $lastPage . '</a> ';
                $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=' . $totalPages . '>' . $totalPages . '</a>';
            }
            elseif ($totalPages - ($adjacents * 2) > $getPage && $getPage > ($adjacents * 2))
            {
                $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=1\">1</a> ';
                $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=2\">2</a>';
                $output .= '<span class="page-numbers dots">...</span>';
                for ($counter = $getPage - $adjacents;$counter <= $getPage + $adjacents;$counter++)
                {
                    if ($counter == $getPage)
                    {
                        $output .= '<span class="page-numbers current">' . $counter . '</span> ';
                        $output .= '<span>' . esc_html__('of', 'kps') . '<span> ' . $totalPages . '</span> ';
                    }
                    else
                    {
                        $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=' . $counter . '>' . $counter . '</a> ';
                    }
                }
                $output .= '<span class="page-numbers dots">...</span>';
                $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=' . $lastPage . '>' . $lastPage . '</a> ';
                $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=' . $totalPages . '>' . $totalPages . '</a>';
            }
            else
            {
                $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=1\">1</a> ';
                $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=2\">2</a>';
                $output .= '<span class="page-numbers dots">...</span>';
                for ($counter = $totalPages - (2 + ($adjacents * 2));$counter <= $totalPages;$counter++)
                {
                    if ($counter == $getPage)
                    {
                        $output .= '<span class="page-numbers current">' . $counter . '</span>';
                    }
                    else
                    {
                        $output .= '<a class="page-numbers" href=' . $pageUrl . '&amp;paged=' . $counter . '>' . $counter . '</a> ';
                    }
                }
            }
        }
        if ($getPage < $counter - 1)
        {
            $output .= ' <a class="page-numbers" href=' . $pageUrl . '&amp;paged=' . $nextPage . '><span>&rsaquo;</span></a>';
            $output .= ' <a class="page-numbers" href=' . $pageUrl . '&amp;paged=' . $totalPages . '><span>&raquo;</span></a>';
        }
    }
    $output .= '</span>';
    return $output;
}

