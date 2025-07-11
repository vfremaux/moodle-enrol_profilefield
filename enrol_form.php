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
 * @package    enrol_profilefield
 * @category   enrol
 * @author     Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright  2010 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_profilefield_enrol_form extends moodleform {
    protected $instance;
    protected $toomany = false;

    /**
     * Overriding this function to get unique form id for multiple self enrolments
     *
     * @return string form identifier
     */
    protected function get_form_identifier() {
        $formid = $this->_customdata->id.'_'.get_class($this);
        return $formid;
    }

    public function definition() {
        global $DB, $COURSE;

        $mform = $this->_form;
        $instance = $this->_customdata;
        $this->instance = $instance;
        $plugin = enrol_get_plugin('self');

        $heading = $plugin->get_instance_name($instance);
        $mform->addElement('header', 'profilefieldheader', $heading);

        // Customint5 : enrolment limit.
        if ($instance->customint5 > 0) {
            // Max enrol limit specified.
            $count = $DB->count_records('user_enrolments', array('enrolid' => $instance->id));
            if ($count >= $instance->customint5) {

                // Bad luck, no more self enrolments here.
                $this->toomany = true;
                $mform->addElement('static', 'notice', '', get_string('maxenrolledreached', 'enrol_profilefield'));
                return;
            }
        }

        // If No autogrouping and group passwords not overriden collect an eventual password to present with the group request.
        // customint3 : Autogrouping
        // customint4 : Overridegrouppasswords
        if ($instance->customint3 == 0 && empty($instance->customint4)) {

            $sql = "
                SELECT
                    g.id,
                    g.name,
                    g.enrolmentkey
                FROM
                    {groups} g
                LEFT JOIN
                    {groupings_groups} gg
                ON
                    g.id = gg.groupid
                JOIN
                    {groupings} gr
                ON
                    gg.groupingid = gr.id
                WHERE
                    gr.id IS NULL OR gr.name != '_auto_' AND
                    g.courseid = :courseid
            ";

            $groupchoice = $DB->get_records_sql($sql, array('courseid' => $COURSE->id), 'g.id, g.name', 'name');
            if (!empty($groupchoice)) {

                $needspass = false;
                $groupopts = array();
                foreach ($groupchoice as $groupopt) {
                    if ($groupdef->enrolmentkey) {
                        $needspass = true;
                    }
                    $groupopts[$groupopt->id] = $groupopt->name;
                }

                $groupoptions = array('' => get_string('none')) + $groupchoice;
                $mform->addElement('select', 'group', get_string('group'), $groupchoice);

                $params = array('id' => $instance->id.'_enrolpassword');
                $mform->addElement('passwordunmask', 'enrolpassword', get_string('grouppassword', 'enrol_profilefield'), $params);
            }
        }

        $this->add_action_buttons(false, get_string('enrolme', 'enrol_profilefield'));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $instance->courseid);

        $mform->addElement('hidden', 'instance');
        $mform->setType('instance', PARAM_INT);
        $mform->setDefault('instance', $instance->id);
    }

    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        $instance = $this->instance;

        if ($this->toomany) {
            $errors['notice'] = get_string('error');
            return $errors;
        }

        if (!empty($data['enrolpassword'])) {
            $groups = $DB->get_records('groups', array('courseid' => $instance->courseid), 'id ASC', 'id, enrolmentkey');
            $found = false;
            foreach ($groups as $group) {
                if (empty($group->enrolmentkey)) {
                    continue;
                }
                if ($group->enrolmentkey === $data['enrolpassword']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                // We can not hint because there are probably multiple passwords.
                $errors['enrolpassword'] = get_string('passwordinvalid', 'enrol_profilefield');
            }
        }

        return $errors;
    }
}