<?php
/**
 * @author 		Alexander Ott
 * @copyright 	2018-2021
 * @email 		webmaster@nirus-online.de
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
 * Widget-Klasse
 */
class Kletterpartner_Suche extends WP_Widget {

    /**
     * Konstrukteur
     */
    function __construct()
    {
		$widgetOptions = array(
    			             'classname'     => 'kps-widget',
    			             'description'   => esc_html__('Shows the last entries in the Climbing-Partner-Search.', 'kps'),
        );
        parent::__construct( 'Climbing-Partner-Search', esc_html__('Climbing-Partner-Search', 'kps').':', $widgetOptions );
        $this->alt_option_name = 'kps';
    }

    /**
     * Frontend Ausgabe
     *
     * @param array $args
	 * @param array $instance
     */
    function widget($args, $instance)
    {
        global $wpdb;

        //  Importiert Variablen eines Arrays in die aktuelle Symboltabelle
        extract($args);
        $defaultValues = array( 'title'         =>  esc_html__('Climbing-Partner-Search', 'kps'),
                                'showEntries'   =>  3,
                                'linktext'      =>  esc_html__('Read more...', 'kps')
        );

        $instance = wp_parse_args((array)$instance, $defaultValues);

        $title          = esc_html($instance['title']);
        $showEntries    = (int)$instance['showEntries'];
        $linkText       = esc_html($instance['linkText']);

        // Alle Einträge pro Seite aus Datenbank holen
        $results = $wpdb->get_results("SELECT * FROM " . KPS_TABLE_ENTRIES . "
                                        WHERE isLockedByAdmin = 1
                                            AND isLocked = 1
                                            AND lockedAutoReport = 0
                                            AND deleteDateTime < NOW()
                                        ORDER BY ID DESC LIMIT {$showEntries}", object);

        $output = $before_widget;

		if ($title !== FALSE) {
			$output .= $before_title . apply_filters('widget_title', $title) . $after_title;
		}

        // HTML5
        if ($html5)
        {
            $output .= '<div class="kps-widget">';
            $output .= '<article>';
        }
        else
        {
            $output .= '<div>';
        }

        if (is_array($results) && count($results) > 0)
        {
            // Foreach-Schleife für Ausgabe des Templates
            foreach ($results as $entry)
            {
                // Klasse instanzieren
                $entry = new kps_entry_read($entry->id);

                $output .= '
                            <div class="kps-widget-divTable">
                            	<div class="kps-widget-divTableBody">
                            		<div class="kps-widget-divTableRow">
                            			<div class="kps-widget-divTableCell kps-widget">' .
                                        $entry->show_authorName_raw() . '&#160;-&#160;' . $entry->show_unlockDateTimeWidget()
                                        . '</div>
                            		</div>
                            		<div class="kps-widget-divTableRow">
                            			<div class="kps-widget-divTableCell kps-widget">' .
                                        $entry->show_authorSearchforWidget() . '&#160;' .
                                        $entry->show_authorRuleWidget() . '&#160;' .
                                        $entry->show_yourRuleWidget()
                                        . '</div>
                                    </div>
                            	</div>
                            </div>
                            <div class="kps-widget-br"></div>
                            ';
            }

            // Link zum weiterlesen
            $output .= '<a href="' .  esc_url(get_post_permalink(kps_PermalinksWithShortCode()[0])) . '" class="kps-more" title="' . $linkText . '">' . $linkText . '</a>';
        }
        else
        {
            // Keine Einträge vorhanden
            $output .= esc_html__('No entries available!', 'kps');
        }

        // HTML5
        if ($html5)
        {
            $output .= '</article>';
        }
        $output .= '</div>';

        $output .= $after_widget;

        // Ausgabe generieren
        echo $output; // Rückgabe
    }

    /**
     * Update Widget
     *
	 * @param array $new_instance --> Neue Optionen
	 * @param array $old_instance --> Alte Optionen
	 *
	 * @return array
     */
    function update($new_instance, $old_instance)
    {
        $instance['title']          = strip_tags($new_instance['title']);
        $instance['showEntries']    = (int) strip_tags($new_instance['showEntries']);
        $instance['linkText']       = strip_tags($new_instance['linkText']);
        return $new_instance;
    }

    /**
     * Backend Ausgabe
     *
     * @param array $instance Widget Optionen
     */
    function form($instance)
    {
        $defaultValues = array( 'title'         =>  esc_html__('Climbing-Partner-Search', 'kps'),
                                'showEntries'   =>  3,
                                'linkText'      =>  esc_html__('Read more...', 'kps')
        );

        // Defaultwerte ersetzen, falls geändert
        $instance = wp_parse_args((array)$instance, $defaultValues);

        $title          = esc_html($instance['title']);
        $showEntries    = (int)$instance['showEntries'];
        $linkText       = esc_html($instance['linkText']);

        echo '
                    <label for="' . $this->get_field_name('title') . '">' . esc_html__('Title', 'kps') . ':</label><br />
                    <input id="' . $this->get_field_name('title') . '" value="' . $title . '" name="' . $this->get_field_name('title') . '" type="text" /><br />
                    <label for="' . $this->get_field_name('showEntries'). '">' . esc_html__('Number of entries', 'kps') . ':</label><br />
    				<select id="' . $this->get_field_name('showEntries'). '" name="' . $this->get_field_name('showEntries'). '">';

                    for ($i = 1; $i <= 25; $i++)
                    {
						echo '<option value="' . $i . '"';

                        if ($i === $showEntries)
                        {
                            echo ' selected="selected"';
                        }

        echo            '>' . $i . '</option>';
					}
        echo    '
    				</select><br />
                    <label for="' . $this->get_field_name('linkText') . '">' . esc_html__('Linktext', 'kps') . ':</label><br />
                    <input id="' . $this->get_field_name('linkText') . '" value="' . $linkText . '" name="' . $this->get_field_name('linkText') . '" type="text" /><br />
                ';
    }
}
add_action( 'widgets_init', 'wpdocs_register_widgets' );

/**
 * Register Widget
 */
function wpdocs_register_widgets() {
    register_widget( 'Kletterpartner_Suche' );
}