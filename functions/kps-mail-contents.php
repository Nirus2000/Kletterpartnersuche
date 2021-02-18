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
 * Email Vorlage Freischaltung Eintrag (Administrator)
 */
function kps_mailcontent_adminUnlock($mailContentExist = false) {

    if ($mailContentExist === false)
    {
        $unlockSubject = esc_html__('Entry unlocked', 'kps');
        $unlockContent = esc_html__('Your entry has just been unlocked!

The deletion time for this entry was set to %erasedatetime%.

Your entry:
*******************
Entry written on: %setdate%
Entry released on: %unlockdatetime%
Entry will be deleted on: %erasedatetime%

%entrycontent%

Your contact details:
*******************
Name: %authorname%
Email: %authoremail%
%authorcontactdata%

Many Thanks!
Your team
%blogname%.
%blogurl%
%blogemail%', 'kps');

        return array('Subject' => $unlockSubject, 'Content' => $unlockContent);  // Rückgabe als Array
    }
    else
    {
        $unlockSubject = esc_attr($mailContentExist['kpsUnlockSubject']);
        $unlockContent = esc_attr($mailContentExist['kpsUnlockContent']);

        return array('Subject' => $unlockSubject, 'Content' => $unlockContent); // Rückgabe als Array
    }
}

/**
 * Email Vorlage Aktivierung Eintrag
 */
function kps_mailcontent_activation($mailContentExist = false) {

    if ($mailContentExist === false)
    {
        $writeSubject = esc_html__('Activation', 'kps');
        $writeContent = esc_html__('You have just posted a new entry on %blogname%.

To be able to publish it, you have to confirm it via the link
below and enter your email address. If you do not release this
post, it will be deleted automatically on %erasedatetime% from
our database.

Activate entry:
*******************
%linkactivation%

Delete entry:
*******************
Password: %erasepassword%
%linkdelete%

Your entry:
*******************
Entry written on: %setdate%
Entry released on: %unlockdatetime%
Entry will be deleted on: %erasedatetime%

%entrycontent%

Your contact details:
*******************
Name: %authorname%
Email: %authoremail%
%authorcontactdata%

Many Thanks!
Your team
%blogname%
%blogurl%
%blogemail%', 'kps');

        return array('Subject' => $writeSubject, 'Content' => $writeContent);  // Rückgabe als Array
    }
    else
    {
        $writeSubject = esc_attr($mailContentExist['kpsActivationSubject']);
        $writeContent = esc_attr($mailContentExist['kpsActivationContent']);

        return array('Subject' => $writeSubject, 'Content' => $writeContent); // Rückgabe als Array
    }
}

/**
 * Email Vorlage Anforderung
 */
function kps_mailcontent_requirement($mailContentExist = false) {

    if ($mailContentExist === false)
    {
        $requirementSubject = esc_html__('Requirement', 'kps');
        $requirementContent = esc_html__('You have requested the contact details for the following entry.

Entry:
*******************
Entry written on: %setdate%
Entry released on: %unlockdatetime%
Entry will be deleted on: %erasedatetime%

%entrycontent%

The contact details are:
************************
Name: %authorname%
Email: %authoremail%
%authorcontactdata%

Have fun. Bergheil!
Your team
%blogname%
%blogurl%
%blogemail%', 'kps');

        return array('Subject' => $requirementSubject, 'Content' => $requirementContent);  // Rückgabe als Array
    }
    else
    {
        $requirementSubject = esc_attr($mailContentExist['kpsContactDataSubject']);
        $requirementContent = esc_attr($mailContentExist['kpsContactDataContent']);

        return array('Subject' => $requirementSubject, 'Content' => $requirementContent); // Rückgabe als Array
    }
}

/**
 * Email Vorlage Verifizierung
 */
function kps_mailcontent_verify($mailContentExist = false) {

    if ($mailContentExist === false)
    {
        $verifySubject = esc_html__('Verification', 'kps');
        $verifyContent = esc_html__('You want the contact details for the following entry.

To retrieve the contact information, click on the link and enter the password.
The request key is valid for 24 hours!

Request link:
*******************
Password: %regpassword%
%linkreg%

Entry:
*******************
%entrycontent%

Many Thanks!
Your team
%blogname%
%blogurl%
%blogemail%', 'kps');

        return array('Subject' => $verifySubject, 'Content' => $verifyContent);  // Rückgabe als Array
    }
    else
    {
        $verifySubject = esc_attr($mailContentExist['kpsVerifictionSubject']);
        $verifyContent = esc_attr($mailContentExist['kpsVerifictionContent']);

        return array('Subject' => $verifySubject, 'Content' => $verifyContent); // Rückgabe als Array
    }
}