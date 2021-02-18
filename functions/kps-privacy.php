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
 * Funktion Wordpress
 * Data-Erasure
 */
function kps_PrivacyErasre($erasers) {
  $erasers['Climbing-Partner-Search'] =    array(
                                            'eraser_friendly_name' => esc_html__('Climbing-Partner-Search', 'kps'),
                                            'callback'             => 'kps_PrivacyErasure',
  );
  return $erasers;
}
add_filter('wp_privacy_personal_data_erasers', 'kps_PrivacyErasre', 10);

/**
 * Funktion Data-Erasure
 * Löschen der Einträge
 * Löschen der Anfragen für Einträge die nicht versendet wurde
 * für die Einhaltung der DSGVO
 */
function kps_PrivacyErasure($authorEmail) {

    global $wpdb;

    $number = 500; // Limit us to avoid timing out

    // Keine Email-Adresse vorhanden
    if (empty($authorEmail))
    {
        return array(   'items_removed'  => false,
                        'items_retained' => false,
                        'messages'       => array(),
                        'done'           => true
        );
    }

    // Datenabfrage zu Einträgen
    $resultEntries = $wpdb->get_results("SELECT * FROM " . KPS_TABLE_ENTRIES . " WHERE authorEmail = '" . $authorEmail . "'", object);

    // Datenabfrage zu Verifizierungen
    $resultVerifications = $wpdb->get_results("SELECT * FROM " . KPS_TABLE_REQUIREMENT . " WHERE userEmail = '" . $authorEmail . "'", object);

    if ((!is_array($resultEntries) OR empty($resultEntries))
        && (!is_array($resultVerifications) OR empty($resultVerifications)))
    {
        return array(
        'items_removed'  => true,
        'items_retained' => true,
        'messages'       => esc_html__('No entries or requirements found in the Climbing-Partner-Search!', 'kps'),
        'done'           => true
        );
    }

    // Einträge löschen
    if (is_array($resultEntries) && count($resultEntries) > 0)
    {
        foreach ($resultEntries as $entries)
        {
            $wpdb->query("DELETE FROM " . KPS_TABLE_ENTRIES . " WHERE authorEmail = '" . $authorEmail . "'");
            $messages[] = esc_html__('Climbing-Partner-Search', 'kps') . ':&#160;' . esc_html__('Entry deleted', 'kps') . '&#160;ID:&#160;' . $entries->id;
        }
    }
    else
    {
        $messages[] = esc_html__('Climbing-Partner-Search', 'kps') . ':&#160;' . esc_html__('No entries found!', 'kps');
    }

    // Löschen der Anfragen die nicht versendet wurden
    if (is_array($resultVerifications) && count($resultVerifications) > 0)
    {
        foreach ($resultVerifications as $verifications)
        {
            if ($verifications->sendData === '0')
            {
                $wpdb->query("DELETE FROM " . KPS_TABLE_REQUIREMENT . " WHERE userEmail = '" . $authorEmail . "'");
                $messages[] = esc_html__('Climbing-Partner-Search', 'kps') . ':&#160;' . esc_html__('Request deleted', 'kps') . '&#160;ID:&#160;' . $verifications->id;
            }
            else
            {
                // Löschen nicht möglich
                $messages[] = esc_html__('Climbing-Partner-Search', 'kps') . ':&#160;' . esc_html__('Delete not possible! You have obtained contact information of another author.', 'kps');
            }
        }
    }
    else
    {
        $message[] = esc_html__('Climbing-Partner-Search', 'kps') . ':&#160;' . esc_html__('No requirements found under this entry!', 'kps');
    }

    $done = count($entries) + count($resultVerifications) < $number;

    return array(   'items_removed' => true,
                    'items_retained' => true,
                    'messages' => $messages,
                    'done' => $done,
    );
}

/**
 * Funktion Wordpress
 * Data-Exporter
 */
function register_kps_Data_exporter($exporters) {
    $exporters['Climbing-Partner-Search'] =  array(
                                        'exporter_friendly_name'    => esc_html__('Climbing-Partner-Search', 'kps'),
                                        'callback'                  => 'kps_PrivacyExporter',
    );
    return $exporters;
}
add_filter('wp_privacy_personal_data_exporters', 'register_kps_Data_exporter', 10);

/**
 * Funktion Data-Exporter
 */
function kps_PrivacyExporter($authorEmail) {

    global $wpdb;

    $number         = 500;      // Limit us to avoid timing out
    $dataToExport   = array();  // Export-Array

	// Keine Email-Adresse vorhanden
    if (empty($authorEmail))
    {
        return array(   'data' => array(),
                        'done' => true,
        );
    }

    $entryInformationsToExport = array( 'authorId'          => esc_html__('User-Id', 'kps'),
                                        'authorName'        => esc_html__('Name', 'kps'),
                                        'authorEmail'       => esc_html__('Email', 'kps'),
                                        'formOptions'       => esc_html__('Additional contact options', 'kps'),
                                        'authorIp'          => esc_html__('IP-Adress', 'kps'),
                                        'authorHost'        => esc_html__('Host', 'kps'),
                                        'setDateTime'       => esc_html__('Created', 'kps'),
                                        'unlockDateTime'    => esc_html__('Released', 'kps'),
                                        'deleteDateTime'    => esc_html__('Delete Time', 'kps'),
                                        'authorSearchfor'   => esc_html__('I am looking for', 'kps'),
                                        'authorRule'        => esc_html__('Kind of search', 'kps'),
                                        'yourRule'          => esc_html__('I am', 'kps'),
                                        'content'           => esc_html__('Entry', 'kps'),
                                        'url'               => esc_html__('URL', 'kps'),
                                        'verifications'     => esc_html__('Contact details shipped', 'kps')
	);

    // Datenabfrage zu Einträgen
    $resultEntries = $wpdb->get_results("SELECT * FROM " . KPS_TABLE_ENTRIES . " WHERE authorEmail =  '" . $authorEmail . "'", object);

    if (is_array($resultEntries) && count($resultEntries) > 0)
    {
        foreach ($resultEntries as $entries)
        {
            $entryDataToExport   = array();

            // Klasse instanzieren
            $entry = new kps_entry_read($entries->id);
            $entryId = $entries->id;

            foreach ($entryInformationsToExport as $key => $name)
            {
                $value = '';

                // Alle Daten zusammenstellen
                switch ( $key ) {
                	case 'authorId':
                		$value = $entry->show_authorId();
               		break;
                	case 'authorName':
                		$value = $entry->show_authorName_raw();
               		break;
                	case 'authorEmail':
                		$value = $entry->show_authorEmail_raw();
               		break;
                	case 'formOptions':
                        // zusätzliche Kontaktdaten
                        $authorContactData = kps_contact_informations($entry->show_authorContactData());
                        $value = $authorContactData;
               		break;
                	case 'authorIp':
                		$value = $entry->show_authorIp();
               		break;
                	case 'authorHost':
                		$value = $entry->show_authorHost();
               		break;
                	case 'setDateTime':
                		$value = $entry->show_setDateTime();
               		break;
                	case 'unlockDateTime':
                		$value = $entry->show_unlockDateTime();
               		break;
                	case 'deleteDateTime':
                		$value = $entry->show_deleteDateTime();
               		break;
                	case 'authorSearchfor':
                		$value = $entry->show_authorSearchfor_raw();
               		break;
                	case 'authorRule':
                		$value = $entry->show_authorRule_raw();
               		break;
                	case 'yourRule':
                		$value = $entry->show_yourRule_raw();
               		break;
                	case 'content':
                		$value = $entry->show_authorContent();
               		break;
                	case 'url':
                        // Daten für Export-Array zusammenstellen
                        $permalinkId = kps_PermalinksWithShortCode();

                        // Links zum Eintrag, wenn sichtbar
                        if ($entry->show_isLocked() === true
                            && $entry->show_isLockedByAdmin() === true
                            && $entry->show_lockedAutoReport() === false)
                        {
                            foreach ($permalinkId as $url)
                            {
                                $value  .= '<a href="' . esc_url(get_post_permalink($url)) . '" target="_blank">' .esc_url(get_post_permalink($url)) . '</a><br />';
                            }
                        }
                        else
                        {
                            $value = esc_html__('This entry is not visible!', 'kps');
                        }
               		break;
                	case 'verifications':
                        $resultVerifications = $wpdb->get_results("SELECT * FROM " . KPS_TABLE_REQUIREMENT . " WHERE entryId =  '" . $entries->id . "' AND sendData = 1", object);
                        if (is_array($resultVerifications) && count($resultVerifications) > 0)
                        {
                            foreach ($resultVerifications as $verifications)
                            {
                                $value  .= $verifications->userEmail . '<font size="4"><span>&#160;&#10140;&#160;</span></font>' . date_i18n(get_option('date_format'), $verifications->sendTimestamp) . ', ' . date_i18n(get_option('time_format'), $verifications->sendTimestamp) . '<br />';
                            }
                        }
                        else
                        {
                            $value = esc_html__('No personal-data sent!', 'kps');
                        }
               		break;
                }

                // Daten für Export-Array zusammenstellen
                $entryDataToExport[] = array(   'name'  => $name,
                                                'value' => $value
                );
 			}

            // Export-Array
            $dataToExport[] = array(
            	'group_id'    => 'kps',
            	'group_label' => esc_html__('Climbing-Partner-Search entries', 'kps'),
            	'item_id'     => "kps-entry-{$entryId}",
            	'data'        => $entryDataToExport,
            );
        }
    }
    else
    {
        return array(   'data' => array(),
                        'done' => true,
		);
	}

    // Tell core if we have more comments to work on still
	$done = false;
	if ( count($results) + count($resultVerifications)  < $number )
    {
		$done = true;
	}

	return array(
		'data' => $dataToExport,
		'done' => $done,
	);
}