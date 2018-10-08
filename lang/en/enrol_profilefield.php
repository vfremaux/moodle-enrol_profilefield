<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Adds new instance of enrol_paypal to specified course
 * or edits current instance.
 *
 * @package    enrol_profilefield
 * @category   enrol
 * @copyright  2010 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Privacy.
$string['privacy:metadata'] = "The Profile Field Enrol do not store any data belonging to users";

// Capabilities.
$string['profilefield:config'] = 'Can configure profile field enrolment';
$string['profilefield:enrol'] = 'Can enrol through profile field enrolment';
$string['profilefield:manage'] = 'Can add profile field enrolment';
$string['profilefield:unenrol'] = 'Can unenrol from a field enrolment assignation';
$string['profilefield:unenrolself'] = 'Can unenrol self from a field enrolment assignation';

$string['assignrole'] = 'Assign role';
$string['auto_desc'] = 'This group has been automatically created by the ProfileField Enrol plugin. It will be deleted if you remove the ProfileField Enrol plugin from the course.';
$string['badprofile'] = 'You may be disapointed, but your profile information forbids you enrolling in this course. However, if you have a good reason to be here, please contact an administrator who will alter your profile consequently.';
$string['course'] = 'Course : $a';
$string['enrol/profilefield:unenrolself'] = 'Can unenrol self from course';
$string['auto'] = 'Automatic';
$string['auto_help'] = 'If enabled, the user will be enroled in course when loging in without necessarily having to visit the course.';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can be enrolled until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolme'] = 'Enrol me in the course';
$string['enrolmentconfirmation'] = 'Be welcome. Your profile information allows you to enroll in this course. Proceed ? ';
$string['enrolname'] = 'Profile Field Enrolment';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid (in seconds). If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user is enrolled. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can be enrolled from this date onward only.';
$string['emptyfield'] = 'No {$a}';
$string['groupon'] = 'Group By';
$string['g_none'] = 'No grouping, or choose...';
$string['g_auth'] = 'Auth Method';
$string['g_dept'] = 'Department';
$string['g_inst'] = 'Institution';
$string['g_lang'] = 'Language';
$string['groupon_help'] = 'This enrol plugin can automatically add users to a group when they are enrolled based upon one of these user fields.';
$string['grouppassword'] = 'Password to enter a group, if it is known.';
$string['newcourseenrol'] = 'A new participant has enrolled in course {$a}';
$string['nonexistantprofilefielderror'] = 'This field is not defined in user profile extensions';
$string['notificationtext'] = 'Notification template';
$string['notificationtext_help'] = 'The content of the mail can be written here, using &lt;%%USERNAME%%&gt;, &lt;%%COURSE%%&gt;, &lt;%%URL%%&gt; and &lt;%%TEACHER%%&gt; placeholders. Note that any multilanguage span tag will be processed based on the actual language of the recipient.';
$string['notifymanagers'] = 'Notify managers?';
$string['passwordinvalid'] = 'Password is invalid';
$string['pluginname'] = 'Profile Field Enrolment';
$string['pluginname_desc'] = 'This method allows direct enrolment in course if user has a profile field set to the expected value';
$string['profilefield'] = 'User profile field';
$string['profilefield_desc'] = 'A pointer to a custom user field';
$string['profilevalue'] = 'Expected value';
$string['profilevalue_desc'] = '';
$string['status'] = 'Allow using profile to enrol';
$string['unenrolself'] = 'Unenroll from course "{$a}"?';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';

$string['defaultnotification'] = '
Dear <%%TEACHER%%>,

the new user <%%USERNAME%%> has enrolled himself (profile agreed) in your course <%%COURSE%%>.

You can check his profile <a href="<%%URL%%>">here</a> after loggin in.
';
