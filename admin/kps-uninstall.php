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
 * Hauptfunktion
 */
function kps_uninstall()
{
    $verification = false; // Iniziierung

    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('administrator'))
    {
        die(esc_html(__('Access denied!', 'kps')));
    }

    // Javascript einladen
    kps_admin_enqueue();

    // Metabox erstellen
    add_meta_box('kps_admin_uninstalling', esc_html(__('Uninstallation', 'kps')) , 'kps_admin_uninstalling', 'kps_uninstall', 'normal');

    // Post-Variabeln festlegen die akzeptiert werden
    $postList = array(
        'kpsUninstallConfirmed',
        'kpsUninstallToken'
    );
    $postVars = kps_array_whitelist_assoc($_POST, $postList);

    if (isset($postVars['kpsUninstallConfirmed']))
    {
        // Token verifizieren
        $verification = wp_verify_nonce($postVars['kpsUninstallToken'], 'kpsUninstall');

        // Uninstallprozess starten
        if ($verification == true)
        {
            kps_uninstallproceed(); // Deinstallationsprozess starten

?>

                            <div class="wrap kps">
                                <div>
                                    <h3><?php echo esc_html(__('Climbing-Partner-Search', 'kps')); ?> - <?php echo esc_html(__('Uninstallation', 'kps')); ?></h3>
                                </div>
                                <script>
                                     setTimeout(
                                        function( ) {
                                            window.location.href = '<?php echo admin_url('/index.php'); ?>'
                                        }, 10000 );
                                </script>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p>...<?php echo esc_html(__('all entries have been deleted', 'kps')); ?>.</p>
                                                <p>...<?php echo esc_html(__('Plugin has been disabled.', 'kps')); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="hr kps"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><?php echo esc_html(__('Uninstall completed. All database entries and settings have been removed.', 'kps')); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="hr kps"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p><?php echo esc_html(__('You will be redirected in 10 seconds', 'kps')); ?>...</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="hr kps"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php echo esc_html(__('You will now be automatically redirected to the main page of the dashboard.', 'kps')); ?>
                                               <a href="<?php echo admin_url('/index.php'); ?>"> <?php echo esc_html(__('Dashboard', 'kps')); ?>
                                               </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php
        }
    }
    else
    {
?>
       <div class="wrap">
            <div>
                <h3><?php echo esc_html(__('Climbing-Partner-Search', 'kps')); ?> - <?php echo esc_html(__('Uninstallation', 'kps')); ?>
               </h3>
            </div>
            <div id="dashboard-widgets-wrap" class="kps">
                <div id="dashboard-widgets" class="metabox-holder">
                    <div class="postbox-container"><?php do_meta_boxes('kps_uninstall', 'normal', ''); ?></div>
                </div>
            </div>
        </div>
        <?php
    }
}

/**
 * Übersicht Deinstallation
 */
function kps_admin_uninstalling()
{
    // Token erstellen
    $token = wp_create_nonce('kpsUninstall');

    echo '
            <form class="form kps" action="" method="post">
                <table class="table_list">
                    <tbody>
                        <tr>
                            <td colspan="2">' . esc_html(__('With the uninstall you delete all entries in the database, as well as the attitudes!', 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td colspan="2">' . esc_html(__('This can not be undone. The process is final.', 'kps')) . '</td>
                        </tr>
                        <tr>
                            <td colspan="2">&#160;</td>
                        </tr>
                        <tr>
                            <td>' . esc_html(__('Acknowledge!', 'kps')) . '</td>
                            <td>
                                <input type="checkbox" name="kpsUninstallConfirmed" id="kpsUninstallConfirmed" />
                                <label for="kpsUninstallConfirmed">' . esc_html(__('Yes, I am absolutely sure. Run!', 'kps')) . '</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">&#160;</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <input type="hidden" id="kpsUninstallToken" name="kpsUninstallToken" value="' . $token . '" />
                                <input class="button" type="submit" name="kpsUninstall" id="kpsUninstall" disabled value="' . esc_html(__('Yes, uninstall!', 'kps')) . '" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        ';
}

/**
 * Übersicht Deinstallation
 */
function kps_uninstallproceed()
{
    // Zugriffsrechte prüfen
    if (function_exists('current_user_can') && !current_user_can('administrator'))
    {
        die(esc_html(__('Access denied!', 'kps')));
    }

    global $wpdb;

    // Löschen der Plugin-Datenbanktabellen
    $wpdb->query("DROP TABLE " . KPS_TABLE_ENTRIES);

    // Warte 1 Sekunde für Delay
    sleep(1);

    $wpdb->query("DROP TABLE " . KPS_TABLE_REQUIREMENT);

    // Warte 1 Sekunde für Delay
    sleep(1);

    // Löschen der Plugin Einstellungen in der Wordpress Options-Tabelle
    $wpdb->query("DELETE FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'kps_%' ");

    // Warte 1 Sekunde für Delay
    sleep(1);

    // Deaktiviere Plugin
    deactivate_plugins(KPS_FOLDER . '/kps.php');
}

