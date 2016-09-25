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
define('MOODLE_TEST', 1);

require('../../../config.php');
require_login();
$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

$users = get_users();
foreach ($users as $u) {
    $userlist[$u->id] = fullname($u);
}

echo '<form name="selectuser">';
echo '<select name="uid">';
foreach ($userlist as $uid => $uname) {
    echo '<option value="'.$uid.'">'.$uname.'</option>';
}
echo '</select>';
echo '<input type="submit" name="go" value="test">';
echo '</form>';

$uid = optional_param('uid', $USER->id, PARAM_INT);
$user = $DB->get_record('user', array('id' => $uid));

echo "testing with ".fullname($user);

enrol_check_plugins($user);

