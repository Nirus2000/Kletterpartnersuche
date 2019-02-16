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
 * Funktion Wordpress
 * Data-Erasure
 */
function kps_PrivacyErasre($erasers) {
  $erasers['Climbing-Partner-Search'] =    array(
                                            'eraser_friendly_name' => esc_html(__('Climbing-Partner-Search')),
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
        'messages'       => esc_html(__( 'No entries or requirements found in the Climbing-Partner-Search!')),
        'done'           => true
        );
    }

    // Einträge löschen
    if (is_array($resultEntries) && count($resultEntries) > 0)
    {
        foreach ($resultEntries as $entries)
        {
            $wpdb->query("DELETE FROM " . KPS_TABLE_ENTRIES . " WHERE authorEmail = '" . $authorEmail . "'");
            $messages[] = esc_html(__('Climbing-Partner-Search')) . ':&#160;' . esc_html(__('Entry deleted')) . '&#160;ID:&#160;' . $entries->id;
        }
    }
    else
    {
        $messages[] = esc_html(__('Climbing-Partner-Search')) . ':&#160;' . esc_html(__('No entries found!'));
    }

    // Löschen der Anfragen die nicht versendet wurden
    if (is_array($resultVerifications) && count($resultVerifications) > 0)
    {
        foreach ($resultVerifications as $verifications)
        {
            if ($verifications->sendData === '0')
            {
                $wpdb->query("DELETE FROM " . KPS_TABLE_REQUIREMENT . " WHERE userEmail = '" . $authorEmail . "'");
                $messages[] = esc_html(__('Climbing-Partner-Search')) . ':&#160;' . esc_html(__('Request deleted')) . '&#160;ID:&#160;' . $verifications->id;
            }
            else
            {
                // Löschen nicht möglich
                $messages[] = esc_html(__('Climbing-Partner-Search')) . ':&#160;' . esc_html(__('Delete not possible! You have obtained contact information of another author.'));
            }
        }
    }
    else
    {
        $message[] = esc_html(__('Climbing-Partner-Search')) . ':&#160;' . esc_html(__('No requirements found under this entry!'));
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
                                        'exporter_friendly_name'    => esc_html(__('Climbing-Partner-Search')),
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

    $entryInformationsToExport = array( 'authorId'          => esc_html(__('User-Id', 'kps')),
                                        'authorName'        => esc_html(__('Name', 'kps')),
                                        'authorEmail'       => esc_html(__('Email', 'kps')),
                                        'formOptions'       => esc_html(__('Additional contact options', 'kps')),
                                        'authorIp'          => esc_html(__('IP-Adress', 'kps')),
                                        'authorHost'        => esc_html(__('Host', 'kps')),
                                        'setDateTime'       => esc_html(__('Created', 'kps')),
                                        'unlockDateTime'    => esc_html(__('Released', 'kps')),
                                        'deleteDateTime'    => esc_html(__('Delete Time', 'kps')),
                                        'authorSearchfor'   => esc_html(__('I am looking for', 'kps')),
                                        'authorRule'        => esc_html(__('Kind of search', 'kps')),
                                        'yourRule'          => esc_html(__('I am', 'kps')),
                                        'content'           => esc_html(__('Entry', 'kps')),
                                        'url'               => esc_html(__('URL', 'kps')),
                                        'verifications'     => esc_html(__('Contact details shipped', 'kps'))
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
                        $setAuthorContactInfo = kps_unserialize($entry->show_authorContactData());

                        // Übersetzung der zusätzlichen Kontaktinformationen
                        if (!empty($setAuthorContactInfo) AND $setAuthorContactInfo != "" AND is_array($setAuthorContactInfo))
                        {
                            foreach ($setAuthorContactInfo AS $key => $value)
                            {
                                if( $key == 'authorTelephone')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Telephone', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorMobile')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Mobile Phone', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorSignal')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Signal-Messenger', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorViper')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Viper-Messenger', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorTelegram')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Telegram-Messenger', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorWhatsapp')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Whatsapp-Messenger', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorHoccer')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Hoccer-Messenger', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorWire')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Wire-Messenger', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorSkype')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Skype-Messenger', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorFacebookMessenger')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Facebook-Messenger', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorWebsite')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Website', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorFacebook')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Facebook', 'kps')) . ": " . $value . "<br />";
                                }
                                elseif( $key == 'authorInstagram')
                                {
                                    $setAuthorContactInfoData .= esc_html(__('Instagram', 'kps')) . ": " . $value . "<br />";
                                }
                                else
                                {
                                    $setAuthorContactInfoData .= ' ';
                                }
                            }
                        }
                        $value = $setAuthorContactInfoData;
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
                            $permalinkId = get_PermalinksWithShortCode();

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
                                $value = esc_html(__('This entry is not visible!', 'kps'));
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
                                $value = esc_html(__('No personal-data sent!', 'kps'));
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
            	'group_label' => esc_html(__('Climbing-Partner-Search entries', 'kps')),
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