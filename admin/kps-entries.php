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
function kps_entries($id = '')
{
    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('moderate_comments'))
    {
        wp_die(esc_html__('Access denied!', 'kps'));
    }

    // Javascript einladen
    kps_admin_enqueue();

    global $wpdb;

    $verification   = false;
    $entryRelease   = false;
    $pageNum        = 0;
    $count          = 0;
    $entryEditId    = 0;
    $entryReleaseId = 0;
    $reports        = '';
    $reportcounter  = '';

    // URL-Request für Schnellnavigation oder ob Eintrag direkt freigegeben werden soll (aus Email an Administrator)
    if (is_numeric(absint($_REQUEST['show'])))
    {
        $entryRelease   = true;
        $entryReleaseId = absint($_REQUEST['show']);
    }
    else
    {
        $show = (isset($_REQUEST['show']) && in_array(strtolower($_REQUEST['show']) , array(
            'all',
            'isopen',
            'islockedbyadmin',
            'isunlockbyadmin',
            'islocked',
            'isunlocked',
            'autoreport',
            'isreported',
            'expired'
        ))) ? $_REQUEST['show'] : 'all';
    }

    // Hole die derzeitige Seite
    $getPage = (isset($_REQUEST['paged']) && is_numeric(absint($_REQUEST['paged'])) && $_REQUEST['paged'] != 0) ? floor(absint($_REQUEST['paged'])) : 1;

    // URL-Request prüfen, ob Eintrag direkt bearbeitet werden soll (aus Frontent)
    $entryEdit      = (isset($_REQUEST['edit_id']) && is_numeric(absint($_REQUEST['edit_id']))) ? true : false;
    $entryEditId    = ($entryEdit === true) ? absint($_REQUEST['edit_id']) : 0;

    // Hole Usereinstellungen
    $userSettings = kps_unserialize(get_option('kps_userSettings', false));

    // Hole Ausgabeeinstellungen
    $outputSettings = kps_unserialize(get_option('kps_output', false));

    // Hole Email-Vorlagen Einstellungen
    $adminUnlockMailSettings = kps_unserialize(get_option('kps_adminUnlockMailSettings', false));

    // Hole Maximalanzahl der Einträge pro Seite
    $maxEntriesPerPage = get_option('kps_backendPagination', false);

    // Hole Löschzeitpunkt für nicht freigegebne Einträge
    $deleteNoEntryTime = get_option('kps_deleteNoEntryTime', false);

    // Hole Löschzeitpunkt für freigegebne Einträge
    $deleteEntryTime = get_option('kps_deleteEntryTime', false);

    // Token erstellen
    $token = wp_create_nonce('kpsActionToken');

    if (isset($_POST['kpsAction']))
    {
        // Post-Variabeln festlegen die akzeptiert werden
        $postList = array(
            'kpsAction',
            'kpsCheck',
            'kpsCheckAllTop',
            'kpsCheckAllBottom',
            'kpsActionToken'
        );
        $postVars = kps_array_whitelist_assoc($_POST, $postList);

        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsActionToken'], 'kpsActionToken');

    }

    // Eintrag freigeben aus Adminstrator Email Link
    if ($entryRelease === true AND $entryReleaseId > 0)
    {
        // Eintrag aus Datenbank holen
        $entry = new kps_entry_read($entryReleaseId);

        if ($entry->_isFound === true)
        {
            if ($entry->show_isLockedByAdmin() !== true)
            {
                if ($entry->show_isLocked() === false || $entry->show_isLockedByAdmin() === true)
                {
                    $wpdb->update(KPS_TABLE_ENTRIES, array(
                        'isLockedByAdmin' => 1
                    ) , array(
                        'id' => $entryReleaseId
                    ) , array(
                        '%d'
                    ));
                }
                else
                {
                    $wpdb->update(KPS_TABLE_ENTRIES, array(
                        'isLockedByAdmin'   => 1,
                        'unlockDateTime'    => time(),
                        'deleteDateTime'    => time() + $deleteEntryTime,
                    ) , array(
                        'id' => $entryReleaseId
                    ) , array(
                        '%d',
                        '%d'
                    ));

                    /* Wenn, Autor noch nicht freigegeben, keine Email
                     * ansonsten Benachtigung, das Eintrag freigegeben wurde
                    */
                    if ($userSettings['kpsUserRequireAdminUnlock'] === 'true')
                    {
                        $adminUnlockMail = kps_mailcontent_adminUnlock($adminUnlockMailSettings);

                        // zusätzliche Kontaktdaten
                        $authorContactData = kps_contact_informations($entry->show_authorContactData());

                        // Ersetze Shorttags
                        $postShorttags = array(
                            '%blogname%'            => get_bloginfo('name', 'raw'),
                            '%blogurl%'             => get_bloginfo('url', 'raw'),
                            '%blogemail%'           => get_option('kps_MailFrom', false),
                            '%authorname%'          => $entry->show_authorName_raw(),
                            '%authoremail%'         => $entry->show_authorEmail_raw(),
                            '%entrycontent%'        => $entry->show_authorContent(),
                            '%authorcontactdata%'   => $authorContactData,
                            '%setdate%'             => $entry->show_emailSetDateTime(),
                            '%unlockdatetime%'      => $entry->show_emailUnlockDateTime(),
                            '%erasedatetime%'       => $entry->show_emailDeleteDateTime()
                        );

                        $adminUnlockMail['Content'] = str_replace(array_keys($postShorttags), $postShorttags, $adminUnlockMail['Content']);

                        // Email versenden
                        $headers = 'From: ' . get_bloginfo('name'). ' <' .  esc_attr(get_option('kps_MailFrom', false)) . '>';
                        wp_mail(esc_attr($entry->show_authorEmail_raw()), $adminUnlockMail['Subject'], $adminUnlockMail['Content'], $headers);
                    }
                }
                echo '
                <div class="notice notice-success is-dismissible">
                	<p>
                        <strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Release (Admin)', 'kps') . '</strong>
                    </p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
            else
            {
                echo '
                <div class="notice notice-info is-dismissible">
                	<p>
                        <strong>' . esc_html__('Note! Entry has already been activated!', 'kps') . '</strong>
                    </p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
        }
        else
        {
            echo '
            <div class="notice notice-error is-dismissible">
            	<p><strong>' . esc_html__('Error!', 'kps') . '&#160;' . esc_html__('No entries found!', 'kps') . '</strong></p>
            	<button type="button" class="notice-dismiss">
            		<span class="screen-reader-text">Dismiss this notice.</span>
            	</button>
            </div>
            ';
        }
    }

    // Eintrag bearbeiten (Multi-Action)
    if ($verification == true
        && isset($postVars['kpsAction'])
        && !empty($postVars['kpsAction']))
    {
        if (is_array($postVars)
            && count($postVars['kpsCheck']) > 0)
        {
            foreach ($postVars['kpsCheck'] AS $key => $id)
            {
                if (is_numeric(absint($id)))
                {
                    $postVars['kpsCheck'][$key] = $id;

                    // Eintrag löschen
                    if ($postVars['kpsAction'] == "delete" && !empty($postVars['kpsCheck'][$key]))
                    {
                        $wpdb->delete(KPS_TABLE_ENTRIES, array(
                            'id' => $postVars['kpsCheck'][$key]
                        ) , array(
                            '%d'
                        ));
                    }

                    // Reset Meldungen (Admin)
                    if ($postVars['kpsAction'] == "reset" && !empty($postVars['kpsCheck'][$key]))
                    {
                        // Report Counter resetten
                        $reportcounter = serialize(array(
                                                    'spam'          => 0,
                                                    'unreasonable'  => 0,
                                                    'double'        => 0,
                                                    'privacy'       => 0,
                                                    'others'        => 0)
                                        );

                        $wpdb->update(KPS_TABLE_ENTRIES, array(
                            'lockedAutoReport'  => 0,
                            'reportCount'       => $reportcounter,
                            'isReported'        => 0
                        ) , array(
                            'id' => $postVars['kpsCheck'][$key]
                        ) , array(
                            '%d',
                            '%s',
                            '%d'
                        ));

                    }

                    // Eintrag sperren (User)
                    if ($postVars['kpsAction'] == "lock" && !empty($postVars['kpsCheck'][$key]))
                    {
                        $wpdb->update(KPS_TABLE_ENTRIES, array(
                            'isLocked'          => 0,
                            'isLockedByAdmin'   => 0,
                            'unlockDateTime'    => 0,
                            'deleteDateTime'    => time() + $deleteNoEntryTime
                        ) , array(
                            'id' => $postVars['kpsCheck'][$key]
                        ) , array(
                            '%d',
                            '%d',
                            '%d'
                        ));
                    }

                    // Eintrag freischalten (Administrator)
                    if ($postVars['kpsAction'] == "unlockbyadmin" && !empty($postVars['kpsCheck'][$key]))
                    {
                        // Eintrag aus Datenbank holen
                        $entry = new kps_entry_read($postVars['kpsCheck'][$key]);

                        if ($entry->show_isLocked() === false || $entry->show_isLockedByAdmin() === true)
                        {
                            $wpdb->update(KPS_TABLE_ENTRIES, array(
                                'isLockedByAdmin' => 1
                            ) , array(
                                'id' => $postVars['kpsCheck'][$key]
                            ) , array(
                                '%d'
                            ));
                        }
                        else
                        {
                            $wpdb->update(KPS_TABLE_ENTRIES, array(
                                'isLockedByAdmin'   => 1,
                                'unlockDateTime'    => time(),
                                'deleteDateTime'    => time() + $deleteEntryTime,
                            ) , array(
                                'id' => $postVars['kpsCheck'][$key]
                            ) , array(
                                '%d',
                                '%d'
                            ));

                            /* Wenn, Autor noch nicht freigegeben, keine Email
                             * ansonsten Benachtigung, das Eintrag freigegeben wurde
                            */
                            if ($userSettings['kpsUserRequireAdminUnlock'] === 'true')
                            {
                                $adminUnlockMail = kps_mailcontent_adminUnlock($adminUnlockMailSettings);

                                // zusätzliche Kontaktdaten
                                $authorContactData = kps_contact_informations($entry->show_authorContactData());

                                // Ersetze Shorttags
                                $postShorttags = array(
                                    '%blogname%'            => get_bloginfo('name', 'raw'),
                                    '%blogurl%'             => get_bloginfo('url', 'raw'),
                                    '%blogemail%'           => get_option('kps_MailFrom', false),
                                    '%authorname%'          => $entry->show_authorName_raw(),
                                    '%authoremail%'         => $entry->show_authorEmail_raw(),
                                    '%entrycontent%'        => $entry->show_authorContent(),
                                    '%authorcontactdata%'   => $authorContactData,
                                    '%setdate%'             => $entry->show_emailSetDateTime(),
                                    '%unlockdatetime%'      => $entry->show_emailUnlockDateTime(),
                                    '%erasedatetime%'       => $entry->show_emailDeleteDateTime()
                                );

                                $adminUnlockMail['Content'] = str_replace(array_keys($postShorttags), $postShorttags, $adminUnlockMail['Content']);

                                // Email versenden
                                $headers = 'From: ' . get_bloginfo('name'). ' <' .  esc_attr(get_option('kps_MailFrom', false)) . '>';
                                wp_mail(esc_attr($entry->show_authorEmail_raw()), $adminUnlockMail['Subject'], $adminUnlockMail['Content'], $headers);
                            }
                        }
                    }

                    // Eintrag sperren (Administrator)
                    if ($postVars['kpsAction'] == "lockbyadmin" && !empty($postVars['kpsCheck'][$key]))
                    {
                        $wpdb->update(KPS_TABLE_ENTRIES, array(
                            'isLockedByAdmin' => 0,
                            'unlockDateTime' => 0,
                            'deleteDateTime' => time() + $deleteNoEntryTime
                        ) , array(
                            'id' => $postVars['kpsCheck'][$key]
                        ) , array(
                            '%d',
                            '%d',
                            '%d'
                        ));
                    }
                }
            }
            if ($postVars['kpsAction'] == "lockbyadmin")
            {
                echo '
                <div class="notice notice-success is-dismissible">
                	<p>
                        <strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Lock (Admin)', 'kps') . '</strong>
                    </p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
            if ($postVars['kpsAction'] == "unlockbyadmin")
            {
                echo '
                <div class="notice notice-success is-dismissible">
                	<p>
                        <strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Release (Admin)', 'kps') . '</strong>
                    </p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
            if ($postVars['kpsAction'] == "lock")
            {
                echo '
                <div class="notice notice-success is-dismissible">
                	<p>
                        <strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Lock (Author)', 'kps') . '</strong>
                    </p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
            if ($postVars['kpsAction'] == "reset")
            {
                echo '
                <div class="notice notice-success is-dismissible">
                	<p>
                        <strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Reset Report (Admin)', 'kps') . '</strong>
                    </p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
            if ($postVars['kpsAction'] == "delete")
            {
                echo '
                <div class="notice notice-success is-dismissible">
                	<p>
                        <strong>' . esc_html__('Saved', 'kps') . ':&#160;' . esc_html__('Delete', 'kps') . '</strong>
                    </p>
                	<button type="button" class="notice-dismiss">
                		<span class="screen-reader-text">Dismiss this notice.</span>
                	</button>
                </div>
                ';
            }
        }
    }

    // Alle gesperrten Einträge (Administrator)
    if ($show == 'islockedbyadmin')
    {
        $query = "WHERE isLockedByAdmin = 0";
    }
    // Alle freigeschalteten Einträge (Administrator)
    elseif ($show == 'isunlockbyadmin')
    {
        $query = "WHERE isLockedByAdmin = 1";
    }
    // Alle gesperrten Einträge (User)
    elseif ($show == 'islocked')
    {
        $query = "WHERE isLocked = 0";
    }
    // Alle gesperrten Einträge durch User
    elseif ($show == 'isunlocked')
    {
        $query = "WHERE isLocked = 1";
    }
    // Alle abgelaufenen Einträge
    elseif ($show == 'expired')
    {
        $query = "WHERE deleteDateTime < " . time() . "";
    }
    // Alle öffenen Einträge
    elseif ($show == 'isopen')
    {
        $query = "WHERE isLocked = 0 OR isLockedByAdmin = 0";
    }
    // Einträge mit Meldungen
    elseif ($show == 'autoReport')
    {
        $query = "WHERE lockedAutoReport = 1";
    }
    // Auto-Sperre
    elseif ($show == 'isreported')
    {
        $query = "WHERE isReported = 1";
    }
    // Eintrag bearbeiten (aus Frontend)
    elseif ($entryEdit === true)
    {
        $query = "WHERE id = {$entryEditId}";
    }
    // Eintrag freigeben (aus Administrator Email Link)
    elseif ($entryRelease === true)
    {
        $query = "WHERE id = {$entryReleaseId}";
    }
    // Alle Einträge
    else
    {
        $query = "";
    }

    // Zählen
    $countAction = $wpdb->get_row("SELECT
                                    COUNT(*) AS allEntries,
                                    IFNULL(SUM(CASE WHEN isReported = 1 THEN 1 ELSE 0 END), 0) AS isReported,
                                    IFNULL(SUM(CASE WHEN lockedAutoReport  = 1 THEN 1 ELSE 0 END), 0) AS lockedAutoReport,
                                    IFNULL(SUM(CASE WHEN isLocked = 0 THEN 1 ELSE 0 END), 0) AS isLocked,
                                    IFNULL(SUM(CASE WHEN isLocked = 1 THEN 1 ELSE 0 END), 0) AS isUnLocked,
                                    IFNULL(SUM(CASE WHEN isLockedByAdmin = 0 THEN 1 ELSE 0 END), 0) AS isLockedByAdmin,
                                    IFNULL(SUM(CASE WHEN isLockedByAdmin = 1 THEN 1 ELSE 0 END), 0) AS isUnLockedByAdmin,
                                    IFNULL(SUM(CASE WHEN isLocked = 0 OR isLockedByAdmin = 0 THEN 1 ELSE 0 END), 0) AS isLockedBoth,
                                    IFNULL(SUM(CASE WHEN deleteDateTime < " . time() . " THEN 1 ELSE 0 END), 0) AS deleteDateTime
                                FROM " . KPS_TABLE_ENTRIES, OBJECT);

    // Paginagtion erstellen
    $totalEntriesCount = $wpdb->get_results("SELECT * FROM " . KPS_TABLE_ENTRIES . " " . $query . "", object);
    $countTotalEntries = $wpdb->num_rows; // Anzahl der Einträge

    // Update Pagigation, wenn keine Einträge nach der Aktualisieurng der Datenbank vorhanden sind
    if ($countTotalEntries === 0)
    {
        $query = '';
        $countTotalEntries = $countAction->allEntries; // Alle Einträge

    }

    // Berechnung der augegebenen Einträge pro Seite
    $totalPages = ceil($countTotalEntries / $maxEntriesPerPage); // Aufrunden
    $lastPage = $totalPages - 1; // letzte Seite
    $previosPage = $getPage - 1; // Verhergehende Seite
    $nextPage = $getPage + 1; // Nächste Seite
    $limits = (int)($getPage - 1) * $maxEntriesPerPage; // Limit für Query
    $pageUrl = "admin.php?page=" . KPS_FOLDER . "/entries.php";

    // Alle Einträge pro Seite aus Datenbank holen
    $results = $wpdb->get_results("SELECT id FROM " . KPS_TABLE_ENTRIES . " " . $query . " ORDER BY ID DESC LIMIT {$limits}, {$maxEntriesPerPage}", object);

    // Blätterfunktion
    if ($countTotalEntries > 0)
    {
        $firstEntry = ($getPage - 1) * $maxEntriesPerPage + 1;
        $totalPerPage = $countTotalEntries - (($getPage - 1) * $maxEntriesPerPage);
        if ($totalPerPage > $maxEntriesPerPage)
        {
            $totalPerPage = $maxEntriesPerPage;
        }
        $lastEntry = $firstEntry + $totalPerPage - 1;

        $pagination =   '
                        <div class="tablenav-pages">
                            <span class="displaying-num">' . $firstEntry . ' &#45; ' . $lastEntry . ' ' . esc_html__('of', 'kps') . ' ' . $countTotalEntries . '</span>
                            ' . kps_pagination_backend($totalPages, $getPage, $lastPage, $previosPage, $nextPage, $pageUrl) . '
                        </div>
                        ';
    }
?>
    <div id="kps">
        <form class="form" id="kpsShowEntries" name="kpsShowEntries" action="" method="post">
           <div>
                <h3><?php echo esc_html__('Climbing-Partner-Search', 'kps'); ?> - <?php echo esc_html__('Entries', 'kps'); ?></h3>
            </div>
            <div class=" kps_container">
                <div>
                    <ul class="subsubsub">
                        <li>
                            <?php
                            if ($countAction->allEntries > 0)
                            {
                                ?>
                                <a href='admin.php?page=<?php echo KPS_FOLDER; ?>/entries.php&amp;show=all'
                                <?php
                                if ($show == 'all')
                                {
                                    echo ' class="current"';
                                }
                                ?>
                                >
                                <?php echo esc_html__('All', 'kps'); ?><span>&#160;(<?php echo $countAction->allEntries; ?>)</a>&#160;&#124;</span>
                                <?php
                            }
                            else
                            {
                                echo esc_html__('All', 'kps') . "<span>&#160;&#124;</span>";
                            }
                            ?>
                        </li>
                        <li>
                            <?php
                            if ($countAction->isLockedBoth > 0)
                            {
                                ?>
                                <a href='admin.php?page=<?php echo KPS_FOLDER;?>/entries.php&amp;show=isopen'
                                <?php
                                if ($show == 'isopen')
                                {
                                    echo ' class="current"';
                                }
                                ?>
                                >
                                <?php
                                    echo esc_html(_n('open entry', 'open entries', $countAction->isLockedBoth, 'kps'));
                                ?> <span>(<?php echo $countAction->isLockedBoth; ?>)</a>&#160;&#124;</span>
                                <?php
                            }
                            else
                            {
                                echo esc_html__('open entries', 'kps') . "<span>&#160;&#124;&#160;</span>";
                            }
                            ?>
                        </li>
                        <li>
                            <?php
                            if ($countAction->isLockedByAdmin > 0)
                            {
                                ?>
                                <a href='admin.php?page=<?php echo KPS_FOLDER; ?>/entries.php&amp;show=islockedbyadmin'
                                <?php
                                if ($show == 'islockedbyadmin')
                                {
                                    echo ' class="current"';
                                }
                                ?>
                                >
                                <?php
                                    echo esc_html(_n('locked by Admin', 'locked by Admin', $countAction->isLockedByAdmin, 'kps'));
                                ?> <span>(<?php echo $countAction->isLockedByAdmin; ?>)</a>&#160;&#124;</span>
                                <?php
                            }
                            else
                            {
                                echo esc_html__('locked by Admin', 'kps') . "<span>&#160;&#124;&#160;</span>";
                            }
                            ?>
                        </li>
                        <li>
                            <?php
                            if ($countAction->isUnLockedByAdmin > 0)
                            {
                                ?>
                                <a href='admin.php?page=<?php echo KPS_FOLDER; ?>/entries.php&amp;show=isunlockbyadmin'
                                <?php
                                if ($show == 'isunlockbyadmin')
                                {
                                    echo ' class="current"';
                                }
                                ?>
                                >
                                <?php
                                    echo esc_html(_n('released by Admin', 'released by Admin', $countAction->isUnLockedByAdmin, 'kps'));
                                ?> <span>(<?php echo $countAction->isUnLockedByAdmin; ?>)</a>&#160;&#124;</span>
                                <?php
                            }
                            else
                            {
                                echo esc_html__('released by Admin', 'kps') . "<span>&#160;&#124;&#160;</span>";
                            }
                            ?>
                        </li>
                        <li>
                            <?php
                            if ($countAction->isLocked > 0)
                            {
                                ?>
                                <a href='admin.php?page=<?php echo KPS_FOLDER; ?>/entries.php&amp;show=islocked'
                                <?php
                                if ($show == 'islock')
                                {
                                    echo ' class="current"';
                                }
                                ?>
                                >
                                <?php
                                    echo esc_html(_n('locked by Author', 'locked by Author', $countAction->isLocked, 'kps'));
                                ?> <span>(<?php echo $countAction->isLocked; ?>)</a>&#160;&#124;</span>
                                <?php
                            }
                            else
                            {
                                echo esc_html__('locked by Author', 'kps') . "<span>&#160;&#124;&#160;</span>";
                            }
                            ?>
                        </li>
                        <li>
                            <?php
                            if ($countAction->isUnLocked > 0)
                            {
                                ?>
                                <a href='admin.php?page=<?php echo KPS_FOLDER; ?>/entries.php&amp;show=isunlocked'
                                <?php
                                if ($show == 'isunlock')
                                {
                                    echo ' class="current"';
                                }
                                ?>
                                >
                                <?php
                                    echo esc_html(_n('released by Author', 'released by Author', $countAction->isUnLocked, 'kps'));
                                ?> <span>(<?php echo $countAction->isUnLocked; ?>)</a>&#160;&#124;</span>
                                <?php
                            }
                            else
                            {
                                echo esc_html__('released by Author', 'kps') . "<span>&#160;&#124;&#160;</span>";
                            }
                            ?>
                        </li>
                        <li>
                            <?php
                            if ($countAction->isReported > 0)
                            {
                                ?>
                                <a href='admin.php?page=<?php echo KPS_FOLDER; ?>/entries.php&amp;show=isreported'
                                <?php
                                if ($show == 'isreported')
                                {
                                    echo ' class="current"';
                                }
                                ?>
                                >
                                <?php
                                    echo esc_html(_n('entry with report', 'entries with report', $countAction->isReported, 'kps'));
                                ?> <span>(<?php echo $countAction->isReported; ?>)</a>&#160;&#124;</span>
                                <?php
                            }
                            else
                            {
                                echo esc_html__('entry with report', 'kps') . "<span>&#160;&#124;&#160;</span>";
                            }
                            ?>
                        </li>
                        <li>
                            <?php
                            if ($countAction->lockedAutoReport > 0)
                            {
                                ?>
                                <a href='admin.php?page=<?php echo KPS_FOLDER; ?>/entries.php&amp;show=autoReport'
                                <?php
                                if ($show == 'autoReport')
                                {
                                    echo ' class="current"';
                                }
                                ?>
                                >
                                <?php
                                    echo esc_html__('Auto-Lock', 'kps');
                                ?> <span>(<?php echo $countAction->lockedAutoReport; ?>)</a>&#160;&#124;</span>
                                <?php
                            }
                            else
                            {
                                echo esc_html__('Auto-Lock', 'kps') . "<span>&#160;&#124;&#160;</span>";
                            }
                            ?>
                        </li>
                        <li>
                            <?php
                            if ($countAction->deleteDateTime > 0)
                            {
                                ?>
                                <a href='admin.php?page=
                                <?php
                                    echo KPS_FOLDER;
                                ?>/entries.php&amp;show=expired'
                                <?php
                                if ($show == 'expired')
                                {
                                    echo ' class="current"';
                                }
                                ?>
                                >
                                <?php
                                    echo esc_html__('expired', 'kps');
                                ?> <span>(<?php echo $countAction->deleteDateTime; ?>)</span></a>
                                <?php
                            }
                            else
                            {
                                echo esc_html__('expired', 'kps');
                            }
                            ?>
                      </li>
                    </ul>
                </div>
                <div class="tablenav">
                    <div class="alignleft actions">
                        <label for="kpsAction"></label>
                        <select name="kpsAction" id="kpsActionTop">
                            <option label=" "></option>
                            <option value="lock"><?php echo esc_html__('Lock (Author)', 'kps'); ?></option>
                            <option value="unlockbyadmin"><?php echo esc_html__('Approval (Admin)', 'kps'); ?></option>
                            <option value="reset"><?php echo esc_html__('Reset Report (Admin)', 'kps'); ?></option>>
                            <option value="lockbyadmin"><?php echo esc_html__('Lock (Admin)', 'kps'); ?></option>
                            <option value="delete"><?php echo esc_html__('Delete', 'kps'); ?></option>
                        </select>
                        <input type="hidden" id="kpsActionToken" name="kpsActionToken" value="<?php echo $token; ?>" />
                        <input value="<?php echo esc_html__('Execute', 'kps'); ?>" name="kpsAdminDoIt" id="kpsAdminDoIt" class="button action" type="submit" />
                    </div>
                    <?php echo $pagination; ?>
                </div>
                <div>
                    <table class="table_list">
                        <thead>
                            <tr>
                                <th class="th_list_top" scope="col">
                                    <label for="kpsCheckAllTop"></label>
                                    <input name="kpsCheckAllTop" id="kpsCheckAllTop" type="checkbox"/>
                                </th>
                                <th class="th_list_top" scope="col"><?php echo esc_html__('ID', 'kps'); ?></th>
                                <th class="th_list_top" scope="col"><?php echo esc_html__('Author', 'kps'); ?></th>
                                <th class="th_list_top" scope="col"><?php echo esc_html__('Entry', 'kps'); ?></th>
                                <th class="th_list_top" scope="col"><?php echo esc_html__('Created', 'kps'); ?></th>
                                <th class="th_list_top" scope="col"><?php echo esc_html__('Released', 'kps'); ?></th>
                                <th class="th_list_top" scope="col"><?php echo esc_html__('Delete Time', 'kps'); ?></th>
                                <th class="th_list_top" scope="col"><?php echo esc_html__('Reports', 'kps'); ?></th>
                                <th class="th_list_top" scope="col"><?php echo esc_html__('Release (Admin)', 'kps'); ?></th>
                                <th class="th_list_top" scope="col"><?php echo esc_html__('Release (Author)', 'kps'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
        // Foreach-Schleife für die Auflistung aller Einträge
        if (is_array($results) && count($results) > 0)
        {
            foreach ($results AS $resultItem)
            {
                // Klasse instanzieren
                $read = new kps_entry_read($resultItem->id, $userSettings);
        ?>
                                      <tr class="tr_list">
                                            <td class="td_list" style="text-align: center;">
                                                <label for="kpsCheck[]"></label>
                                                <input name="kpsCheck[]" id="kpsCheck<?php echo $resultItem->id; ?>" type="checkbox" value="<?php echo $resultItem->id; ?>" />
                                            </td>
                                            <td class="td_list" style="text-align: center;"><?php echo $resultItem->id; ?></td>
                                            <td class="td_list" style="vertical-align: top;">
                                                <?php
                                                    echo $read->show_authorName() . ' (' . $read->show_authorId(). ')<br />' . $read->show_authorEmail();
                                                    echo '
                                                        <div style="text-align: left;"><b>' . esc_html__('IP-Adress', 'kps') . ':</b>&#160;' . nl2br($read->show_authorIp()) . '</div>
                                                        <div style="text-align: left;"><b>' . esc_html__('Host', 'kps') . ':</b>&#160;' . nl2br($read->show_authorHost()) . '</div>
                                                        <div style="text-align: left;"><b>' . esc_html__('Activation-Key', 'kps') . ':</b>&#160;' . kps_getFirstChars($read->show_activationcode(), 5) . '[...]' . kps_getLastChars($read->show_activationcode(), 5) . '</div>
                                                        <div style="text-align: left;"><b>' . esc_html__('Delete-Key', 'kps') . ':</b>&#160;' . kps_getFirstChars($read->show_deletecode(), 5) . '[...]' . kps_getLastChars($read->show_deletecode(), 5) . '</div>
                                                    ';
                                                ?>
                                            </td>
                                            <td class="td_list" style="width: 30%; vertical-align: top;">
                                                <?php
                                                echo '
                                                    <div style="text-align: left;">' . $read->show_authorSearchfor() . '&#160;' . $read->show_authorRule() . '&#160;' . $read->show_yourRule() . '<div>
                                                    <div class="kps-br"></div>
                                                    <div style="text-align: left;">' . nl2br($read->show_authorContent()) . '</div>
                                                ';
                                                ?>
                                              </td>
                                            <td class="td_list" style="text-align: center; vertical-align: top;"><?php echo $read->show_setDateTime(); ?></td>
                                            <td class="td_list" style="text-align: center; vertical-align: top;"><?php echo $read->show_unlockDateTime(); ?></td>
                                            <td class="td_list" style="text-align: center; vertical-align: top;"><?php echo $read->show_deleteDateTime(); ?></td>
                                            <td class="td_list" style="text-align: center; vertical-align: top;">
                                                <?php
                                                    $reports = kps_unserialize($read->show_reportCount());
                                                    echo '
                                                        <div style="text-align: left;"><b>' . esc_html__('Spam/Advertising', 'kps') . ':</b>&#160;' . $reports['spam'] . '</div>
                                                        <div style="text-align: left;"><b>' . esc_html__('Inappropriate/Violence', 'kps') . ':</b>&#160;' . $reports['unreasonable'] . '</div>
                                                        <div style="text-align: left;"><b>' . esc_html__('Double entry', 'kps') . ':</b>&#160;' . $reports['double'] . '</div>
                                                        <div style="text-align: left;"><b>' . esc_html__('Personality rights', 'kps') . ':</b>&#160;' . $reports['privacy'] . '</div>
                                                        <div style="text-align: left;"><b>' . esc_html__('Others', 'kps') . ':</b>&#160;' . $reports['others'] . '</div>
                                                    ';
                                                ?>
                                            </td>
                                            <td class="td_list" style="text-align: center;"><span>
                                                <?php
                                                    echo ($read->show_isLockedByAdmin() === true) ? '<span class="dashicons dashicons-yes"></span>' : '<span class="dashicons dashicons-no-alt"></span>';
                                                    echo ($read->show_isReported() === true) ? '<span class="dashicons dashicons-warning"></span>' : '';
                                                    echo ($read->show_lockedAutoReport() === true) ? '<span class="dashicons dashicons-admin-network"></span>' : '';
                                                ?>
                                            </span></td>
                                            <td class="td_list" style="text-align: center;"><span>
                                                <?php
                                                    echo ($read->show_isLocked() === true) ? '<span class="dashicons dashicons-yes"></span>' : '<span class="dashicons dashicons-no-alt"></span>';
                                                ?>
                                            </span></td>
                                        </tr>
                                    <?php
            }
        }
        else
        {
        ?>
                              <tr>
                                    <td colspan="10" style="text-align: center;"><strong><?php echo esc_html__('No entries available!', 'kps'); ?></strong></td>
                                </tr>
                                <?php
        }

        ?>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="th_list_bottom" scope="col">
                                    <label for="kpsCheckAllBottom"></label>
                                    <input name="kpsCheckAllBottom" id="kpsCheckAllBottom" type="checkbox"/>
                                </th>
                                <th class="th_list_bottom" scope="col"><?php echo esc_html__('ID', 'kps'); ?></th>
                                <th class="th_list_bottom" scope="col"><?php echo esc_html__('Author', 'kps'); ?></th>
                                <th class="th_list_bottom" scope="col"><?php echo esc_html__('Entry', 'kps'); ?></th>
                                <th class="th_list_bottom" scope="col"><?php echo esc_html__('Created', 'kps'); ?></th>
                                <th class="th_list_bottom" scope="col"><?php echo esc_html__('Released', 'kps'); ?></th>
                                <th class="th_list_bottom" scope="col"><?php echo esc_html__('Delete Time', 'kps'); ?></th>
                                <th class="th_list_bottom" scope="col"><?php echo esc_html__('Reports', 'kps'); ?></th>
                                <th class="th_list_bottom" scope="col"><?php echo esc_html__('Release (Admin)', 'kps'); ?></th>
                                <th class="th_list_bottom" scope="col"><?php echo esc_html__('Release (Author)', 'kps'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="tablenav">
                    <div class="alignleft actions">
                        <label for="kpsAction"></label>
                        <select name="kpsAction" id="kpsActionBottom">
                            <option label=" "></option>
                            <option value="lock"><?php echo esc_html__('Lock (Author)', 'kps'); ?></option>
                            <option value="unlockbyadmin"><?php echo esc_html__('Approval (Admin)', 'kps'); ?></option>
                            <option value="reset"><?php echo esc_html__('Reset Reports (Admin)', 'kps'); ?></option>
                            <option value="lockbyadmin"><?php echo esc_html__('Lock (Admin)', 'kps'); ?></option>
                            <option value="delete"><?php echo esc_html__('Delete', 'kps'); ?></option>
                        </select>
                        <input type="hidden" id="kpsActionToken" name="kpsActionToken" value="<?php echo $token; ?>" />
                        <input value="<?php echo __('Execute', 'kps'); ?>" name="kpsAdminDoIt" id="kpsAdminDoIt" class="button action" type="submit" />
                    </div>
                    <?php echo $pagination; ?>
                </div>
            </form>
            <div class="kps-br"></div>
            <div class="kps-form-info" style="text-align: center;">
                <span class="dashicons dashicons-yes"></span><?php echo esc_html__('Released', 'kps'); ?>&#160;&#124;
                <span class="dashicons dashicons-no-alt"></span><?php echo esc_html__('Blocked', 'kps'); ?>&#160;&#124;
                <span class="dashicons dashicons-warning"></span><?php echo esc_html__('Reports', 'kps'); ?>&#160;&#124;
                <span class="dashicons dashicons-admin-network"></span><?php echo esc_html__('Auto-Lock', 'kps'); ?>
            </div>
        </div>
    </div>
<?php
}