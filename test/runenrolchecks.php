<?php

define('MOODLE_TEST', 1);

require('../../../config.php');
require_login();
$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

$users = get_users();
foreach($users as $u) {
    $userlist[$u->id] = fullname($u);
}

echo '<form name="selectuser">';
echo '<select name="uid">';
foreach($userlist as $uid => $uname) {
    echo '<option value="'.$uid.'">'.$uname.'</option>';
}
echo '</select>';
echo '<input type="submit" name="go" value="test">';
echo '</form>';

$uid = optional_param('uid', $USER->id, PARAM_INT);
$user = $DB->get_record('user', array('id' => $uid));

echo "testing with ".fullname($user);

enrol_check_plugins($user);

