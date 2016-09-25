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

require('../../config.php');
require_once('edit_form.php');

$courseid = required_param('courseid', PARAM_INT);
$instanceid = optional_param('id', 0, PARAM_INT); // Enrol instance id.

$course = $DB->get_record('course', array('id' => $courseid), '*');
$context = context_course::instance($course->id);

// Security.

require_login($course);
require_capability('enrol/profilefield:config', $context);

$url = new moodle_url('/enrol/profilefield/edit.php', array('courseid' => $course->id, 'id' => $instanceid));
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

$return = new moodle_url('/enrol/instances.php', array('id' => $course->id));
if (!enrol_is_enabled('profilefield')) {
    redirect($return);
}

$plugin = enrol_get_plugin('profilefield');

if ($instanceid) {
    $instance = $DB->get_record('enrol', array('id' => $instanceid), '*');
    $roles = get_default_enrol_roles($context, $instance->roleid);
    $mode = 'update';
} else {
    require_capability('moodle/course:enrolconfig', $context);

    // No instance yet, we have to add new instance.
    navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id' => $course->id)));
    $instance = new stdClass();
    $instance->id = null;
    $instance->courseid = $course->id;
    $instance->customchar1 = ''; // Profile field.
    $instance->customchar2 = ''; // Profile value.
    $instance->customint1 = 1; // Notifies teachers of entry by default.
    $instance->customint2 = 0; // Not automated.
    $instance->customint3 = 0; // Without grouping.
    $instance->customtext1 = format_text(get_string('defaultnotification', 'enrol_profilefield')); // Notification for teachers.

    $roles = get_default_enrol_roles($context, $plugin->get_config('roleid'));
    $mode = 'add';
}

$mform = new enrol_profilefield_edit_form(null, array($roles, $mode));

if ($mform->is_cancelled()) {
    redirect($return);
} else if ($data = $mform->get_data()) {

    if ($instance->id) {
        $reset = ($instance->status != $data->status);

        $instance->status         = $data->status;
        $instance->name           = $data->name;
        $instance->roleid         = $data->roleid;
        $instance->customint1     = 0 + @$data->notifymanagers; // Checkbox.
        $instance->customint2     = 0 + @$data->auto; // Checkbox.
        $instance->customint3     = 0 + @$data->autogroup; // Select.
        $instance->customtext1    = $data->notificationtext;
        $instance->customchar1    = $data->profilefield;
        $instance->customchar2    = $data->profilevalue;
        $instance->enrolperiod    = $data->enrolperiod;
        $instance->enrolstartdate = $data->enrolstartdate;
        $instance->enrolenddate   = $data->enrolenddate;
        $instance->timemodified   = time();

        $DB->update_record('enrol', $instance);

        if ($reset) {
            $context->mark_dirty();
        }

    } else {
        $fields = array('status' => $data->status,
                        'name' => $data->name,
                        'customint1' => $data->notifymanagers,
                        'customint2' => $data->auto,
                        'customint3' => $data->autogroup,
                        'customtext1' => $data->notificationtext,
                        'customchar1' => $data->profilefield,
                        'customchar2' => $data->profilevalue,
                        'roleid' => $data->roleid,
                        'enrolperiod' => $data->enrolperiod,
                        'enrolstartdate' => $data->enrolstartdate,
                        'enrolenddate' => $data->enrolenddate
        );
        $plugin->add_instance($course, $fields);
    }

    redirect($return);
}

$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'enrol_profilefield'));

echo $OUTPUT->header();
if ($instanceid) {
    $formdata = new StdClass();
    $formdata->id = $instance->id;
    $formdata->courseid = $courseid;
    $formdata->auto = $instance->customint2;
    $formdata->autogroup = $instance->customint3;
    $formdata->profilefield = $instance->customchar1;
    $formdata->profilevalue = $instance->customchar2;
    $formdata->notifymanagers = $instance->customint1;
    $formdata->name = $instance->name;
    $formdata->status = $instance->status;
    $formdata->roleid = $instance->roleid;
    $formdata->enrolperiod = $instance->enrolperiod;
    $formdata->enrolstartdate = $instance->enrolstartdate;
    $formdata->enrolenddate = $instance->enrolenddate;
    if (empty($instance->customtext1)) {
        $formdata->customtext1 = get_string('defaultnotification', 'enrol_profilefield');
    }
    $formdata->notificationtext = $instance->customtext1;

    $mform->set_data($formdata);
} else {
    $formdata = new StdClass();
    $formdata->id = $instance->id;
    $formdata->courseid = $courseid;
    $formdata->roleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
    $formdata->status = 1;
    $formdata->name = get_string('pluginname', 'enrol_profilefield');
    $mform->set_data($formdata);
}

echo $OUTPUT->heading(get_string('pluginname', 'enrol_profilefield'));
$mform->display();
echo $OUTPUT->footer();
