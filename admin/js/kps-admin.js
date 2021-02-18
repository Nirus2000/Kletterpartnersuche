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
 * Check/Uncheck Toogle Einträge
 * Dropdown Toggle Einträge
 */
jQuery(document)
    .ready(function ($) {
        "use strict";
        /* Toggle Checkboxes */
        jQuery("#kpsShowEntries input[name='kpsCheckAllTop']")
            .change(function () {
                kpsToggleCheckbox($("input[name='kpsCheckAllTop']").is(":checked"));
	   });

	   jQuery("#kpsShowEntries input[name='kpsCheckAllBottom']")
            .change(function () {
                kpsToggleCheckbox($("input[name='kpsCheckAllBottom']").is(":checked"));
	   });

        function kpsToggleCheckbox(checkAll_checked) {
        	jQuery("input[name^='kpsCheck']")
                .attr("checked", checkAll_checked);
        }
        /* Toggle Dropdowns */
        jQuery("#kpsActionTop")
            .change(function(){
                var ind = $(this).find('option:selected').index();
                $('#kpsActionBottom option').eq(ind).prop('selected', true);
        });

        jQuery("#kpsActionBottom")
            .change(function(){
                var ind = $(this).find('option:selected').index();
                $('#kpsActionTop option').eq(ind).prop('selected', true);
        });
    });

/*
 * Auto-Size Textarea
 */
jQuery(document)
    jQuery
        .each(jQuery('textarea[data-autoresize]'), function() {
            "use strict";
            var offset = this.offsetHeight - this.clientHeight;

            var resizeTextarea = function(el) {
                jQuery(el).css('height', 'auto').css('height', el.scrollHeight + offset);
            };
            jQuery(this).on('keyup input', function() { resizeTextarea(this); }).removeAttr('data-autoresize');
    });


/*
 * Metabox Post-Container Toggeln ( hide / show)
 */
jQuery(document).ready(function($) {
	jQuery('.kps .postbox button.handlediv').click( function() {
		jQuery(this).closest('.postbox').toggleClass('closed');
	});
});

jQuery(document).ready(function($) {
	jQuery('.kps .postbox h2').click( function() {
		jQuery(this).closest('.postbox').toggleClass('closed');
	});
});


/*
 * Toggeln ( hide / show)
 * Toggeln ( form elements)
 */
jQuery(document)
    .ready(function ($) {
        "use strict";
        // Tabs
        jQuery('.kps_nav_tab_wrapper a' )
            .on('click', function() {
        		jQuery('form.kps_options' ).removeClass( 'active' );
        		jQuery('.kps_nav_tab_wrapper a' ).removeClass( 'nav-tab-active' );

            	var rel = jQuery( this ).attr('rel');
            	jQuery('.' + rel ).addClass( 'active' );
            	jQuery(this).addClass( 'nav-tab-active' );

            	return false;
            });

        // Reset Statisik
        jQuery("input#kpsResetStatisticsConfirmed")
            .prop("checked", false); // Checkbox ist nicht aktiv (Initialisierung)

        // Deinstallation
        jQuery("input#kpsUninstallConfirmed")
            .prop("checked", false); // Checkbox ist nicht aktiv (Initialisierung)


        jQuery("input#kpsUninstallConfirmed")
            .change(function () {
                var checked = jQuery("input#kpsUninstallConfirmed")
                    .prop('checked');
                if (checked === true) {
                    jQuery("#kpsUninstall")
                        .addClass('button-primary');
                    jQuery("#kpsUninstall")
                        .removeAttr('disabled');
                } else {
                    jQuery("#kpsUninstall")
                        .removeClass('button-primary');
                    jQuery("#kpsUninstall")
                        .attr('disabled', true);
                }
            });

        jQuery("input#kpsResetStatisticsConfirmed")
            .change(function () {
                var checked = jQuery("input#kpsResetStatisticsConfirmed")
                    .prop('checked');
                if (checked === true) {
                    jQuery("#kpsResetStatistics")
                        .addClass('button-primary');
                    jQuery("#kpsResetStatistics")
                        .removeAttr('disabled');
                } else {
                    jQuery("#kpsResetStatistics")
                        .removeClass('button-primary');
                    jQuery("#kpsResetStatistics")
                        .attr('disabled', true);
                }
            });
    });