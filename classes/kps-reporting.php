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
 * Report-Class
 */
class kps_reporting
{
    private     $_isFound,              // Eintrag gefunden
                $_isReported,           // Email wurde schon versendet
                $_lockedAutoReport,     // Auto-Sperre
                $_reportCount,          // Reporting
                $_isReport,             // Report in DB
                $_reportSettings,       // Report Einstellungen
                $_reportCounter,        // Report-Counter
                $_reportReason,         // Grund
                $_emailCopyCC,          // Emaileinstellungen
                $_authorContent,        // Content
                $_setDateTime,          // Eintrag Timestamp
                $_unlockDateTime,       // Freigabe Timestamp
                $_deleteDateTime,       // Löschen Timestamp
                $_sendReport,           // Email versenden
                $_sendReportAuto,       // Email versenden Auto-Spere
                $_sendReportSiteAdmin,  // Email an BlogAdmin
                $_isSend,               // Email versendet
                $_isSendReportAuto,     // Email versendet
                $_isSendReportAdmin,    // Email versendet an BlogAdmin
                $_sendReportComplet,    // Email versenden -> Gesamtmeldungen erreicht
                $_newReportCount;       // neugebildeter Report


    /**
     * Konstrukteur
     */
    public function __construct($id = 0, $reportChoise = NULL)
    {
        $this->_reportCount         = (array)'';
        $this->_isReported          = false;
        $this->_isFound             = false;
        $this->_isReport            = false;
        $this->_newReportCount      = false;
        $this->_isSend              = false;
        $this->_isSendReportAuto    = false;
        $this->_isSendReportAdmin   = false;
        $this->_reportSettings      = kps_unserialize(get_option('kps_report', false));
        $this->_reportCounter       = (int)0;
        $this->_reportReason        = (string)'';
        $this->_lockedAutoReport    = false;
        $this->_emailCopyCC         = (array)kps_unserialize(get_option('kps_mailFromCC', false));
        $this->_authorContent       = (string)'';
        $this->_setDateTime         = (string)'';
        $this->_unlockDateTime      = (string)'';
        $this->_deleteDateTime      = (string)'';
        $this->_sendReport          = false;
        $this->_sendReportAuto      = false;
        $this->_sendReportSiteAdmin = false;
        $this->_sendReportComplet   = false;

        // Hole Eintrag
        $this->get_entry($id);

        // Wenn True dann Update DB
        if ($this->_isFound === true)
        {
            // Hole Reporting und Gesamtzahl alle Reports
            $this->get_reporting($this->_reportCount);

            // Reporting neu bilden und auswerten
            $this->get_report($this->_reportCount, $reportChoise, $this->_reportSettings);
        }

        // Report-Count update in Datenbank zum Eintrag
        if ($this->_isFound === true && $this->_newReportCount === true)
        {
            // Neuen Report-Count setzen in Datenbank
            $this->set_reportCount($id, $this->_reportCount);
        }

        // Admin informiert
        if ($this->_isReported === false && $this->_sendReport === true)
        {
            $this->sendEmail($id);

            // Report versendet und Datenbank updaten
            $this->set_sendReport($id);
        }

        // Report versendet und Datenbank updaten
        if( $this->_isReported === false && $this->_isSend === true)
        {
            $this->set_sendReport($id);
        }

        // Gesamtmeldungen erreicht
        if ($this->_sendReportComplet === true && $this->_lockedAutoReport === false)
        {
            $this->sendEmail($id);
        }

        // Auto-Sperre ausgelöst
        if ($this->_lockedAutoReport === false && $this->_sendReportAuto === true)
        {
            $this->sendEmail($id);
        }

        // Autosperre gesetzen
        if ($this->_sendReportAuto === true)
        {
            $this->set_sendReportAdmin($id);
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
     * Datensatz aus Datenbank laden
     */
    public function get_entry($id = 0)
    {
        // Hole Eintrag aus Datenbank
        $data = new kps_entry_read($id);

        if (!empty($data))
        {
            $this->_authorContent       = $data->show_authorContent();
            $this->_lockedAutoReport    = $data->show_lockedAutoReport();
            $this->_reportCount         = $data->show_reportCount();
            $this->_isReported          = $data->show_isReported();
            $this->_setDateTime         = $data->show_setDateTime();
            $this->_unlockDateTime      = $data->show_unlockDateTime();
            $this->_deleteDateTime      = $data->show_deleteDateTime();
            $this->_isFound             = $data->show_isFound();
            return true; // Rückgabe des Wertes
        }
        return false; // Rückgabe des Wertes
    }

    /**
     * Reporting + Zusammenzählen aller Reports
     */
    public function sendEmail($id)
    {
        // Email vorbereiten
        $reportSubject = esc_html__('Entry reported', 'kps') . ': ' . get_bloginfo('name');;

        if ($this->_sendReportAuto === true)
        {
            $reportContent = esc_html__('An entry was blocked by the automatic lock!', 'kps');
        }
        elseif ($this->_sendReportComplet === true)
        {
            $reportContent = esc_html__('The total amount of messages for the entry has been reached!', 'kps');
        }
        else
        {
            $reportContent = esc_html__('An entry was reported!', 'kps');
        }

        $reportContent .= '

' . esc_html__('Reason', 'kps') . ': ' . $this->_reportReason . '
' . esc_html__('Timestamp', 'kps') . ': ' . date_i18n(get_option('date_format'), current_time('timestamp')) .' @ '. date_i18n(get_option('time_format'), current_time('timestamp')) . '

' . esc_html__('Edit-Link', 'kps') . ':
**************************
' . KPS_ADMIN_URL . '/entries.php&edit_id=' . $id . '

' . esc_html__('System data', 'kps') . '
**************************
ID: ' . $id . '
'. esc_html__('Created on', 'kps') . ': ' . $this->_setDateTime . '
'. esc_html__('Released on', 'kps') . ': ' . $this->_unlockDateTime . '
'. esc_html__('Delete Time', 'kps') . ': ' . $this->_deleteDateTime . '

'. esc_html__('Spam/Advertising', 'kps') . ': ' . $this->_reportCount['spam'] . '
'. esc_html__('Inappropriate/Violence', 'kps') . ': ' . $this->_reportCount['unreasonable'] . '
'. esc_html__('Double entry', 'kps') . ': ' . $this->_reportCount['double'] . '
'. esc_html__('Personality rights', 'kps') . ': ' . $this->_reportCount['privacy'] . '
'. esc_html__('Others', 'kps') . ': ' . $this->_reportCount['others'] . '

' . esc_html__('Entry', 'kps') . '
**************************
' . $this->_authorContent . '

' . get_bloginfo('name') . '
' . get_bloginfo('url') . '
' . get_option('kps_mailFrom', false);

        // Email versenden
        $headers = 'From: ' . get_bloginfo('name'). ' <' .  esc_attr(get_option('kps_MailFrom', false)) . '>';
        $this->_isSend = wp_mail(esc_attr($this->_emailCopyCC['kpsEmailReport']), $reportSubject, $reportContent, $headers);

        // Email versenden Auto-Sperre
        if ($this->_lockedAutoReport === false && $this->_sendReportAuto === true)
        {
            $this->_isSendReportAuto = wp_mail(esc_attr($this->_emailCopyCC['kpsEmailReport']), $reportSubject, $reportContent, $headers);

            if(($this->_emailCopyCC['kpsEmailReport'] != get_option('kps_mailFrom', false)) && $this->_sendReportAuto === true)
            {
                $this->_isSendReportAdmin = wp_mail(esc_attr(get_option('kps_mailFrom', false)), $reportSubject, $reportContent, $headers);
            }
        }

        return $this->_isSendReportAuto; // Rückgabe des Wertes
    }

    /**
     * Reporting
     */
    public function get_reporting($reporting = '')
    {
        $this->_reportCount = kps_unserialize($reporting);

        return true; // Rückgabe des Wertes
    }

    /**
     * Report setzen
     */
    public function get_report($dbReport = NULL, $newReport = NULL, $reportSettings = NULL)
    {
            // Auswahl escapen
            switch ($newReport)
            {
                case '0':
                    $dbReport['spam']           = $dbReport['spam'] + 1;
                    $reportReason               = esc_html__('Spam/Advertising', 'kps');
                    $sendReport                 = ($dbReport['spam'] >= $reportSettings['kpsReportSpam']) ? true : false;
                    $sendReportAuto             = ($dbReport['spam'] >= $reportSettings['kpsAutoReportSpam']) ? true : false;
                break;
                case '1':
                    $dbReport['unreasonable']   = $dbReport['unreasonable'] + 1;
                    $reportReason               = esc_html__('Inappropriate/Violence', 'kps');
                    $sendReport                 = ($dbReport['unreasonable'] >= $reportSettings['kpsReportUnreasonable']) ? true : false;
                    $sendReportAuto             = ($dbReport['unreasonable'] >= $reportSettings['kpsAutoReportUnreasonable']) ? true : false;
                break;
                case '2':
                    $dbReport['double']         = $dbReport['double'] + 1;
                    $reportReason               = esc_html__('Double entry', 'kps');
                    $sendReport                 = ($dbReport['double'] >= $reportSettings['kpsReportDouble']) ? true : false;
                    $sendReportAuto             = ($dbReport['double'] >= $reportSettings['kpsAutoReportDouble']) ? true : false;
                break;
                case '3':
                    $dbReport['privacy']        = $dbReport['privacy'] + 1;
                    $reportReason               = esc_html__('Personality rights', 'kps');
                    $sendReport                 = ($dbReport['privacy'] >= $reportSettings['kpsReportPrivacy']) ? true : false;
                    $sendReportAuto             = ($dbReport['privacy'] >= $reportSettings['kpsAutoReportPrivacy']) ? true : false;
                break;
                case '4':
                    $dbReport['others']         = $dbReport['others'] + 1;
                    $reportReason               = esc_html__('Others', 'kps');
                    $sendReport                 = ($dbReport['others'] >= $reportSettings['kpsReportOthers']) ? true : false;
                    $sendReportAuto             = ($dbReport['others'] >= $reportSettings['kpsAutoReportOthers']) ? true : false;
                break;
                default:
                    $dbReport                   = NULL;
                    $reportReason               = '';
                    $sendReport                 = false;
                    $sendReportAuto             = false;
            }

            // Alle Meldungen zusammenzählen
            $this->_reportCounter = $dbReport['spam'] + $dbReport['unreasonable'] + $dbReport['double'] + $dbReport['privacy'] + $dbReport['others'];

            if (!is_null($dbReport)
                && is_array($dbReport)
                && !empty($dbReport))
            {
                // Prüfen, ob Gesamtmeldungen erreicht sind
                if($this->_reportCounter == $reportSettings['kpsAdminSendReportAfter'])
                {
                    $this->_sendReportComplet = true;
                }

                // Report-Array bilden
                $report = array(
                                'spam'          => $dbReport['spam'],
                                'unreasonable'  => $dbReport['unreasonable'],
                                'double'        => $dbReport['double'],
                                'privacy'       => $dbReport['privacy'],
                                'others'        => $dbReport['others']
                                );

                $this->_reportCount     = $report;
                $this->_reportReason    = $reportReason;
                $this->_newReportCount  = true;
                $this->_sendReport      = $sendReport;
                $this->_sendReportAuto  = $sendReportAuto;
                return true; // Rückgabe des Wertes
            }
            else
            {
                $this->_newReportCount = false;
                return false; // Rückgabe des Wertes
            }
        return false; // Rückgabe des Wertes
    }

    /**
     * Reported updaten
     */
    public function set_reportCount($id = 0, $report = NULL)
    {
        global $wpdb;

        // Report-Array serialisieren
        $report = serialize($report);

        // Report Counter updaten
        if (is_serialized($report) == true)
        {
            $this->_isReport = $wpdb->update(KPS_TABLE_ENTRIES, array('reportCount' => $report
                                                                ),
                                                                array('id' => $id
                                                                ),
                                                                array('%s'
                                                                )
            );

            return $this->_isReport; // Rückgabe des Wertes
        }

        return false; // Rückgabe des Wertes
    }

    /**
     * Report update
     */
    public function set_sendReport($id = 0)
    {
        global $wpdb;

        $this->_isReported = $wpdb->update(KPS_TABLE_ENTRIES,   array('isReported' => 1
                                                                ),
                                                                array('id' => $id
                                                                ),
                                                                array('%d')
        );

        return $this->_isReported; // Rückgabe des Wertes
    }

    /**
     * Report update Auto-Sperre
     */
    public function set_sendReportAdmin($id = 0)
    {
        global $wpdb;

        $this->_isSendReportAuto = $wpdb->update(KPS_TABLE_ENTRIES, array('lockedAutoReport' => 1
                                                                    ),
                                                                    array('id' => $id
                                                                    ),
                                                                    array('%d')
        );

        return $this->_isSendReportAuto; // Rückgabe des Wertes
    }

    /**
     * Ausgaben
     */
    public function show_isReport()
    {
        return $this->_isReport; // Rückgabe des Wertes

    }

    public function show_isFound()
    {
        return $this->_isFound; // Rückgabe des Wertes

    }
}