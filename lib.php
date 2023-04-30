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
 * Profile field enrolment plugin.
 *
 * This plugin provides automatic enrol when a user enters the course with
 * a convenient profile value.
 *
 * @package    enrol_profilefield
 * @category   enrol
 * @author     Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright  2012 Valery Fremaux
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!function_exists('debug_trace')) {
    @include_once($CFG->dirroot.'/local/advancedperfs/debugtools.php');
    if (!function_exists('debug_trace')) {
        function debug_trace($msg, $tracelevel = 0, $label = '', $backtracelevel = 1) {
            // Fake this function if not existing in the target moodle environment.
            assert(1);
        }
        define('TRACE_ERRORS', 1); // Errors should be always traced when trace is on.
        define('TRACE_NOTICE', 3); // Notices are important notices in normal execution.
        define('TRACE_DEBUG', 5); // Debug are debug time notices that should be burried in debug_fine level when debug is ok.
        define('TRACE_DATA', 8); // Data level is when requiring to see data structures content.
        define('TRACE_DEBUG_FINE', 10); // Debug fine are control points we want to keep when code is refactored and debug needs to be reactivated.
    }
}

define('SINGLE_FIELD', 0);
define('MULTIPLE_FIELDS', 1);

class enrol_profilefield_plugin extends enrol_plugin {

    public function allow_enrol(stdClass $instance) {
        // Users with enrol cap may enrol other users manually.
        return true;
    }

    public function allow_unenrol(stdClass $instance) {
        // Users with unenrol cap may unenrol other users manually.
        return true;
    }

    public function allow_manage(stdClass $instance) {
        // Users with manage cap may tweak period and status.
        return true;
    }

    public function show_enrolme_link(stdClass $instance) {
        return ($instance->status == ENROL_INSTANCE_ENABLED);
    }

    public function get_info_icons(array $instances) {
        return array(new pix_icon('icon', get_string('pluginname', 'enrol_profilefield'), 'enrol_profilefield'));
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param object $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/profilefield:config', $context);
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/profilefield:config', $context)) {
            return null;
        }

        // Multiple instances supported.
        return new moodle_url('/enrol/profilefield/edit.php', array('courseid' => $courseid));
    }

    /**
     * Returns enrolment instance manage link.
     *
     * By defaults looks for manage.php file and tests for manage capability.
     *
     * @param navigation_node $instancesnode
     * @param stdClass $instance
     * @return moodle_url;
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'profilefield') {
             throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/profilefield:config', $context)) {
            $params = array('id' => $instance->id, 'courseid' => $instance->courseid, 'type' => $instance->enrol);
            $managelink = new moodle_url('/enrol/profilefield/edit.php', $params);
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'profilefield') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();

        if (has_capability('enrol/profilefield:config', $context)) {
            $params = array('courseid' => $instance->courseid, 'id' => $instance->id);
            $editlink = new moodle_url('/enrol/profilefield/edit.php', $params);
            $icon = new pix_icon('t/edit', get_string('edit'), 'core', array('class' => 'iconsmall'));
            $icons[] = $OUTPUT->action_icon($editlink, $icon);
        }

        return $icons;
    }

    /**
     * Gets an array of the user enrolment actions. These are provided
     * in the enrolled user list, in the enrolment method column.
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol($instance) && has_capability('enrol/profilefield:unenrol', $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $attrs = array('class' => 'unenrollink', 'rel' => $ue->id);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url, $attrs);
        }
        if ($this->allow_manage($instance) && has_capability('enrol/profilefield:manage', $context)) {
            $url = new moodle_url('/enrol/profilefield/editenrolment.php', $params);
            $attrs = array('class' => 'editenrollink', 'rel' => $ue->id);
            $actions[] = new user_enrolment_action(new pix_icon('t/edit', ''), get_string('edit'), $url, $attrs);
        }
        return $actions;
    }

    /**
     * Creates course enrol form, checks if form submitted
     * and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     * @return string html text, usually a form in a text box
     */
    public function enrol_page_hook(stdClass $instance) {
        global $CFG, $OUTPUT, $USER, $DB;

        if (isguestuser()) {
            // Can not enrol guest!!
            return null;
        }

        if ($DB->record_exists('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
            // TODO: maybe we should tell them they are already enrolled, but can not access the course.
            return null;
        }

        if ($instance->enrolstartdate != 0 and $instance->enrolstartdate > time()) {
            // TODO: inform that we can not enrol yet.
            return null;
        }

        if ($instance->enrolenddate != 0 and $instance->enrolenddate < time()) {
            // TODO: inform that enrolment is not possible any more.
            return null;
        }

        if (!$this->check_user_profile_conditions($instance)) {
            $output = $OUTPUT->heading(get_string('pluginname', 'enrol_profilefield'), 2);
            $output .= $OUTPUT->notification(get_string('badprofile', 'enrol_profilefield'));
            $output .= $OUTPUT->continue_button($CFG->wwwroot);
            return $OUTPUT->box($output);
        }

        require_once($CFG->dirroot.'/enrol/profilefield/enrol_form.php');
        require_once($CFG->dirroot.'/group/lib.php');

        $form = new enrol_profilefield_enrol_form(null, $instance);
        $instanceid = optional_param('instance', 0, PARAM_INT);

        if ($instance->id == $instanceid) {
            if ($data = $form->get_data()) {
                $enrol = enrol_get_plugin('profilefield');
                $timestart = time();
                if ($instance->enrolperiod) {
                    $timeend = $timestart + $instance->enrolperiod;
                } else {
                    $timeend = 0;
                }

                $this->enrol_user($instance, $USER->id, $instance->roleid, $timestart, $timeend);

                // Autocreate group if required.
                $this->process_group($instance, $USER);

                // In addition check also the password for autogrouping by the user.
                if (!empty($data->enrolpassword)) {
                    // It must be a group enrolment, let's assign group too.
                    if ($groups = $DB->get_records('groups', array('courseid' => $instance->courseid), 'id', 'id, enrolmentkey')) {
                        foreach ($groups as $group) {
                            if (empty($group->enrolmentkey)) {
                                continue;
                            }
                            if ($group->enrolmentkey === $data->enrolpassword) {
                                groups_add_member($group->id, $USER->id);
                                break;
                            }
                        }
                    }
                }

                // Send notification to teachers.
                if ($instance->customint1) {
                    $this->notify_owners($instance, $USER);
                }
            }
        }

        ob_start();
        echo $OUTPUT->notification(get_string('enrolmentconfirmation', 'enrol_profilefield'));
        $form->display();
        $output = ob_get_clean();

        return $OUTPUT->box($output);
    }

    /**
     * checks all user profile conditions to get in.
     * @param enrolobject $instance
     * @param object $user
     */
    public function check_user_profile_conditions(stdClass $instance, $user = null) {
        global $USER, $DB, $OUTPUT, $CFG;

        $config = get_config('enrol_profilefield');

        if (is_null($user)) {
            $user = $USER;
        }

        if ($config->multiplefields == SINGLE_FIELD) {
            $profilefield = $instance->customchar1;
            $profilevalue = (!empty($instance->customtext2)) ? $instance->customtext2 : '';

            if (preg_match('/^profile_field_(.*)$/', $profilefield, $matches)) {
                // Case of user custom fields.

                if (!$pfield = $DB->get_record('user_info_field', array('shortname' => $matches[1]))) {
                    return false;
                }

                $params = array('userid' => $user->id, 'fieldid' => $pfield->id);
                $uservalue = $DB->get_field('user_info_data', 'data', $params);
                if (strpos($profilevalue, 'REGEXP:') === 0) {
                    $profilevalue = trim(preg_replace('/^REGEXP:/', '', $profilevalue));
                    if (preg_match('/'.$profilevalue.'/', $uservalue)) {
                        return true;
                    } else {
                        debug_trace("Not matching because Regexp /{$profilevalue}/ failed");
                    }
                } else {
                    if ($uservalue == $profilevalue) {
                        return true;
                    } else {
                        debug_trace("Not matching because values not equal to {$profilevalue}");
                    }
                }
            } else {
                // We guess it is a standard user attribute.
                if (isset($user->$profilefield)) {
                    $uservalue = $user->$profilefield;
                    if (strpos($profilevalue, 'REGEXP:') === 0) {
                        $profilevalue = trim(preg_replace('/^REGEXP:/', '', $profilevalue));
                        if (preg_match('/'.$profilevalue.'/', $uservalue)) {
                            return true;
                        } else {
                            debug_trace("Not matching because Regexp /{$profilevalue}/ in '$uservalue' failed");
                        }
                    } else {
                        if ($uservalue == $profilevalue) {
                            return true;
                        } else {
                            debug_trace("Not matching because values not equal to {$profilevalue}");
                        }
                    }
                }
            }
        } else {
            // customchar1 holds fieldnames and operators expressions
            // customtext2 holds fieldexpected values, in order

            if (empty($instance->customchar1)) {
                return false;
            }

            // We start with a rough setup.
            $parts = explode(' ', $instance->customchar1);
            $profilevalues = explode(',', $instance->customtext2);

            // We expect : profile_fields or user attribute names miwed with AND or OR operator.
            $expectsfieldname = true;
            $datas = array();
            $operators = array();
            $fields = array();
            foreach ($parts as $part) {
                if ($expectsfieldname) {
                    if (preg_match('/^profile_field_/', trim($part))) {
                        $shortname = str_replace('profile_field_', '', $part);
                        $pfield = $DB->get_record('user_info_field', array('shortname' => $shortname));
                        $fields[] = $part;
                        $params = array('userid' => $user->id, 'fieldid' => $pfield->id);
                        $datas[] = $DB->get_field('user_info_data', 'data', $params);
                    } else {
                        $attribute = $part;
                        $fields[] = $part;
                        $datas[] = $user->$attribute;
                    }
                    $expectsfieldname = false;
                } else {
                    if (!in_array($part, array('AND', 'OR'))) {
                        throw new moodle_exception('Operator expected must be AND or OR');
                    }
                    if ($part == 'AND') {
                        $operators[] = '&&';
                    } else {
                        $operators[] = '||';
                    }
                    $expectsfieldname = true;
                }
            }

            if (count($profilevalues) != count($fields) && ($CFG->debug == DEBUG_DEVELOPER)) {
                $msg = 'Profile field enrol: Fields and expected values count do not match in instance '.$instance->id;
                $msg .= ' and course '.$instance->cid;
                echo $OUTPUT->notification($msg, 'notifyerror');
            }

            $expression = '$result = ';
            foreach ($fields as $field) {
                $data = trim(array_shift($datas));
                $data = str_replace('"', '&quot;', $data);
                $expression .= " \"{$data}\" ";
                $value = trim(array_shift($profilevalues));
                $value = str_replace('"', '&quot;', $value);
                $expression .= " == \"{$value}\" ";
                $operator = array_shift($operators);
                if ($operator) {
                    $expression .= " {$operator} ";
                }
            }

            if (($CFG->debug == DEBUG_DEVELOPER) && function_exists('debug_trace')) {
                debug_trace("Enroling by profie user {$user->id} ");
                debug_trace($expression);
            }

            eval($expression. ";");
            return $result;

        }

        return false;
    }

    /**
     * This is important especially for external enrol plugins,
     * this function is called for all enabled enrol plugins
     * right after every user login.
     *
     * @param object $user user record
     *
     * @return void
     */
    public function sync_user_enrolments($user) {
        global $DB;

        // Get records of all the active AutoEnrol instances which are set to enrol at login.
        $params = array('enrol' => 'profilefield', 'status' => 0, 'customint2' => 1);
        $instances = $DB->get_records('enrol', $params, null, '*');

        // Now get a record of all of the users enrolments.
        $userenrolments = $DB->get_records('user_enrolments', array('userid' => $user->id), null, '*');

        // Run through all of the autoenrolment instances and check that the user has been enrolled.
        foreach ($instances as $instance) {
            $found = false;
            foreach ($userenrolments as $userenrol) {
                if ($userenrol->enrolid == $instance->id) {
                    $found = true;
                }
            }

            if (defined('MOODLE_TEST')) {
                echo json_encode($instance).'<br/>';
                echo "Checking ".fullname($user).'<br/>';
                echo "Allowed : ".$this->check_user_profile_conditions($instance, $user);
            }

            // Check if user passes the profile check.
            if (!$found && $this->check_user_profile_conditions($instance, $user)) {
                $this->enrol_user($instance, $user->id, $instance->roleid, time(), 0);
                $this->process_group($instance, $user);

                // Send notification to teachers.
                if ($instance->customint1) {
                    $this->notify_owners($instance, $user);
                }
            }
        }
    }

    /**
     * Shall we process group and create one from the user's profile values ?
     * @param object $instance
     * @param object $user
     */
    private function process_group(stdClass $instance, $user) {
        global $CFG, $DB;

        if ($instance->customchar3 != '') {
            require_once($CFG->dirroot . '/group/lib.php');

            if (!strpos($instance->customchar3, 'g') === false) {
                // Pure numeric is an 1,2,3,4 option.
                $types = array($user->auth, $user->department, $user->institution, $user->lang);
                $groupname = $types[$instance->customchar3];

                if (!strlen($name)) {
                    $filtertype = array(get_string('g_none', 'enrol_autoenrol'),
                        get_string('g_auth', 'enrol_profilefield'),
                        get_string('g_dept', 'enrol_profilefield'),
                        get_string('g_inst', 'enrol_profilefield'),
                        get_string('g_lang', 'enrol_profilefield'));

                    $groupname = get_string('emptyfield', 'enrol_profilefield', $filtertype[$instance->customchar3]);
                }

                $group = $this->get_group($instance, $groupname);
            } else {
                // Course Group is using pattern g<groupid> - <groupname> @see edit_form.php.
                $groupid = str_replace('g', '', $instance->customchar3);
                $group = $DB->get_record('groups', ['id' => $groupid]);
                if (!$group) {
                    // Group has been removed ?
                    return;
                }
            }
            return groups_add_member($group, $user->id);
        }
    }

    /**
     * Get or autocreate a group for routing profile enroled users.
     * @param stdClass $instance
     * @param $name
     * @param moodle_database $DB
     * @return int|mixed id of the group
     * @throws coding_exception
     * @throws moodle_exception
     */
    private function get_group(stdClass $instance, $name) {
        global $DB;

        $idnumber = "profilefield|$instance->id|$name";

        $autogrouping = $DB->get_record('groupings', array('name' => '_auto_', 'courseid' => $instance->courseid));
        if (!$autogrouping) {
            $autogrouping = new stdclass();
            $autogrouping->courseid = $instance->courseid;
            $autogrouping->name = '_auto_';
            $autogrouping->id = groups_create_grouping($autogrouping);
        }

        $group = $DB->get_record('groups', array('idnumber' => $idnumber, 'courseid' => $instance->courseid));

        if (!$group) {
            $group = new stdclass();
            $group->courseid = $instance->courseid;
            $group->name = $name;
            $group->idnumber = $idnumber;
            $group->description = get_string('auto_desc', 'enrol_profilefield');
            $group->id = groups_create_group($group);

            groups_assign_grouping($autogrouping->id, $group->id);
        }

        return $group->id;
    }

    public function notify_owners(&$instance, &$appliant) {
        global $DB;

        $course = $DB->get_record('course', array('id' => $instance->courseid), '*', MUST_EXIST);

        $a = new stdClass();
        $a->profileurl = new moodle_url('/user/view.php', array('id' => $appliant->id, 'course' => $course->id));

        if (trim($instance->customtext1) !== '') {
            $message = $instance->customtext1;
            $message = str_replace('<%%USERNAME%%>', fullname($appliant), $message);
            $message = str_replace('<%%COURSE%%>', format_string($course->fullname), $message);
            $message = str_replace('<%%SHORTNAME%%>', $course->shortname, $message);
            $message = str_replace('<%%URL%%>', $a->profileurl, $message);

            $subject = get_string('newcourseenrol', 'enrol_profilefield', format_string($course->fullname));
        }

        $context = context_course::instance($course->id);

        $fields = 'u.id, '.get_all_user_name_fields(true, 'u').', emailstop';
        if ($managers = get_users_by_capability($context, 'enrol/profilefield:manage', $fields)) {

            foreach ($managers as $m) {
                $message = str_replace('<%%TEACHER%%>', fullname($m), $message);
                email_to_user($m, $appliant, $subject, $message);
            }
        }
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = \context_course::instance($instance->courseid);
        return has_capability('enrol/profilefield:manage', $context);
    }

    /**
     * Provides a mapping of script attributes to internal
     * storage attributes
     * @return 
     */
    public function script_attributes($map = true) {
        $scriptmap = array(
            'name' => 'name',
            'status' => 'status',
            'role' => 'roleid',
            'period' => 'period',
            'start' => 'enrolstartdate',
            'end' => 'enrolenddate',
            'profilefield' => 'customchar1',
            'profilevalue' => 'customtext2',
            'notifymanagers' => 'customint1',
            'auto' => 'customint2',
            'autogroup' => 'customchar3',
            'overridegrouppassword' => 'customint4',
            'maxenrolled' => 'customint5',
            'notificationtext' => 'customtext1',
        );

        $scriptdesc = array(
            'name' => array(
                'output' => 'name',
                'required' => 0,
             ),
            'status' => array(
                'output' => 'status',
                'required' => 0,
                'default' => 0
             ),
            'role' => array(
                'output' => 'roleid',
                'required' => 0,
                'default' => 'hardcoded'
             ),
            'period' => array(
                'output' => 'period',
                'required' => 0,
                'default' => 0
            ),
            'start' => array(
                'output' => 'enrolstartdate',
                'required' => 0,
            ),
            'end' => array(
                'output' => 'enrolenddate',
                'required' => 0,
                'default' => 0,
            ),
            'profilefield' => array(
                'output' => 'customchar1',
                'required' => 1,
            ),
            'profilevalue' => array(
                'output' => 'customtext2',
                'required' => 1
            ),
            'notifymanagers' => array(
                'output' => 'customint1',
                'required' => 0,
                'default' => 0,
            ),
            'auto' => array(
                'output' => 'customint2',
                'required' => 0,
                'default' => 0,
            ),
            'autogroup' => array(
                'output' => 'customchar3',
                'required' => 0,
                'default '=> 0,
            ),
            'overridegrouppassword' => array(
                'output' => 'customint4',
                'required' => 0,
                'default' => 1,
            ),
            'maxenrolled' => array(
                'output' => 'customint5',
                'required' => 0,
                'default' => 0,
            ),
            'notificationtext' => array(
                'output' => 'customtext1',
                'required' => 0,
                'default' => '',
            ),
        );

        if ($map) {
            return $scriptmap;
        } else {
            return $scriptdesc;
        }
    }

    /**
     * Checks incoming attributes and give report
     * @return 
     */
    public function script_check($context, &$handler) {
        global $USER, $DB;

        $profilefieldname = trim($context->params->profilefield);

        if (empty($profilefieldname)) {
            $handler->error('Enrol ProfileField Script input check : Empty user profile attribute ');
            return;
        }

        if (preg_match('/^profile_field_/', $profilefieldname)) {
            $fieldname = str_replace('profile_field_', '', $fieldname);
            if (!$DB->get_record('user_info_field', array('shortname' => $fieldname))) {
                $handler->error('Enrol ProfileField Script input check : Unkown user profile attribute '.$fieldname);
            }
        } else {
            /*
             * this is just a control of existance in the standard profile.
             * Real $USER data is not engaged.
             */
            if (!isset($USER->$profilefieldname)) {
                $handler->error('Enrol ProfileField Script input check : Unkown user attribute '.$fieldname);
            }
        }
    }
}

/**
 * Indicates API features that the enrol plugin supports.
 *
 * @param string $feature
 * @return mixed True if yes (some features may use other values)
 */
function enrol_profilefield_supports($feature) {
    switch ($feature) {
        case ENROL_RESTORE_TYPE:
            return ENROL_RESTORE_EXACT;

        default:
            return null;
    }
}
