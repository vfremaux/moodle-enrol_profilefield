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
 * Adds new instance of enrol_profilefield to specified course
 * or edits current instance.
 *
 * @package    enrol_profilefield
 * @category   enrol
 * @author     Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright  2013 Valery Fremaux  (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_profilefield_edit_form extends moodleform {

    public function definition() {
        global $DB;

        $config = get_config('enrol_profilefield');

        $mform = $this->_form;

        list($roles, $mode) = $this->_customdata;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_profilefield'));

        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'), array('size' => 32));
        $mform->setType('name', PARAM_TEXT);

        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'status', get_string('status', 'enrol_profilefield'), $options);
        $mform->setDefault('status', 1);

        // Customint2.
        $options = array(1  => get_string('yes'),
                         0 => get_string('no'));
        $mform->addElement('select', 'auto', get_string('auto', 'enrol_profilefield'), $options);
        $mform->setDefault('auto', 0);
        $mform->addHelpButton('auto', 'auto', 'enrol_profilefield');

        $userfields = array(
            'country' => get_string('country'),
            'lang' => get_string('language'),
            'institution' => get_string('institution'),
            'department' => get_string('department'),
            'city' => get_string('city'),
        );

        // Customchar1.
        if ($config->multiplefields == SINGLE_FIELD) {
            $userextrafields = $DB->get_records('user_info_field', array());
            if ($userextrafields) {
                foreach ($userextrafields as $uf) {
                    $userfields['profile_field_'.$uf->shortname] = $uf->name;
                }
            }
            $mform->addElement('select', 'profilefield', get_string('profilefield', 'enrol_profilefield'), $userfields);
        } else {
            $allfields = $DB->get_records('user_info_field', array(), 'name', 'shortname, name');
            foreach($allfields as $f) {
                $fieldnames[] = 'profile_field_'.$f->shortname;
            }
            $help = implode(', ', $fieldnames);
            $mform->addElement('static', 'profilefieldhelp', get_string('usableprofilefields', 'enrol_profilefield'), $help);

            $mform->addElement('text', 'profilefield', get_string('profilefields', 'enrol_profilefield'), array('size' => 80));
            $mform->addHelpButton('profilefield', 'profilefieldmultiple', 'enrol_profilefield');
            $mform->setType('profilefield', PARAM_TEXT);
        }

        // Customtext2.
        if ($config->multiplefields == SINGLE_FIELD) {
            $mform->addElement('text', 'profilevalue', get_string('profilevalue', 'enrol_profilefield'));
            $mform->setType('profilevalue', PARAM_TEXT);
        } else {
            $mform->addElement('text', 'profilevalue', get_string('profilevalues', 'enrol_profilefield'), array('size' => 80));
            $mform->addHelpButton('profilevalue', 'profilevaluemultiple', 'enrol_profilefield');
            $mform->setType('profilevalue', PARAM_TEXT);
        }

        $mform->addElement('select', 'roleid', get_string('assignrole', 'enrol_profilefield'), $roles);

        $mform->addElement('checkbox', 'notifymanagers', get_string('notifymanagers', 'enrol_profilefield'));

        // Customtext1.
        $params = array('cols' => 60, 'rows' => 10);
        $mform->addElement('textarea', 'notificationtext', get_string('notificationtext', 'enrol_profilefield'), $params);
        $mform->setType('notificationtext', PARAM_CLEANHTML);
        $mform->addHelpButton('notificationtext', 'notificationtext', 'enrol_profilefield');

        // Customint3.
        $fields = array(get_string('g_none', 'enrol_profilefield'),
                get_string('g_auth', 'enrol_profilefield'),
                get_string('g_dept', 'enrol_profilefield'),
                get_string('g_inst', 'enrol_profilefield'),
                get_string('g_lang', 'enrol_profilefield'));

        $mform->addElement('select', 'autogroup', get_string('groupon', 'enrol_profilefield'), $fields);
        $mform->setType('autogroup', PARAM_INT);
        $mform->addHelpButton('autogroup', 'groupon', 'enrol_profilefield');
        $mform->setDefault('autogroup', 0);

        // Customint 4
        $mform->addElement('checkbox', 'overridegrouppassword', get_string('overridegrouppassword', 'enrol_profilefield'));
        $mform->addHelpButton('overridegrouppassword', 'overridegrouppassword', 'enrol_profilefield');
        $mform->DisabledIf('overridegrouppassword', 'autogroup', 'eq', 0);

        // Customint 5
        $mform->addElement('text', 'maxenrolled', get_string('maxenrolled', 'enrol_profilefield'), array('size' => 4));
        $mform->setType('maxenrolled', PARAM_INT);

        $params = array('optional' => true, 'defaultunit' => 86400);
        $mform->addElement('duration', 'enrolperiod', get_string('enrolperiod', 'enrol_profilefield'), $params);
        $mform->addHelpButton('enrolperiod', 'enrolperiod', 'enrol_profilefield');

        $params = array('optional' => true);
        $mform->addElement('date_selector', 'enrolstartdate', get_string('enrolstartdate', 'enrol_profilefield'), $params);
        $mform->setDefault('enrolstartdate', 0);
        $mform->addHelpButton('enrolstartdate', 'enrolstartdate', 'enrol_profilefield');

        $params = array('optional' => true);
        $mform->addElement('date_selector', 'enrolenddate', get_string('enrolenddate', 'enrol_profilefield'), $params);
        $mform->setDefault('enrolenddate', 0);
        $mform->addHelpButton('enrolenddate', 'enrolenddate', 'enrol_profilefield');

        $this->add_action_buttons(true, (($mode == 'update') ? null : get_string('addinstance', 'enrol')));
    }

    public function validation($data, $files) {

        $errors = parent::validation($data, $files);

        if ($data['status'] == ENROL_INSTANCE_ENABLED) {
            if (!empty($data['enrolenddate']) and $data['enrolenddate'] < $data['enrolstartdate']) {
                $errors['enrolenddate'] = get_string('enrolenddaterror', 'enrol_profilefield');
            }
        }

        return $errors;
    }
}