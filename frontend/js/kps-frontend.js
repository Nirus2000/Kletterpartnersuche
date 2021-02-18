/*
 * JavaScript für KPS
 *
 * @author 		Alexander Ott
 * @copyright 	2018-2021
 * @email 		kps@nirus-online.de
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

/*
 * Formular Toggeln per Buttom (show)
 */
jQuery(document)
    .ready(function ($) {
        "use strict";
        jQuery("#kps-write-button").click(function () {
                $("#kps-write-button").slideUp(800);
                $("#kps-new-entry").slideDown(800);
                return false;
            });
    });

/*
 * Formular Toggeln per Cross (hide)
 */
jQuery(document)
    .ready(function($) {
        "use strict";
        jQuery( "button.kps-closeFrom" ).click(function() {
    		$("#kps-write-button").slideDown(800);
    		$("#kps-new-entry").slideUp(800);
    		return false;
    	});
    });

/*
 * Spam-Sperre
 */
jQuery(window)
    .ready(function($) {
        "user strict"
        setInterval('blockspam()', 1000);
});

function blockspam() {
    var kps_spamtimer1  = kps_spam.kps_spamtimer1;
    var kps_spamtimer2  = kps_spam.kps_spamtimer2;

    var timer1  = new Number( jQuery( '#' + kps_spamtimer1 ).val() );
    var timer2  = new Number( jQuery( '#' + kps_spamtimer2 ).val() );

    // Timer runter/raufzählen und §_POST-Variable updaten
    var timer1  = timer1 - 1
    var timer2  = timer2 + 1

    jQuery( '#' + kps_spamtimer1 ).val( timer1 );
    jQuery( '#' + kps_spamtimer2 ).val( timer2 );
}