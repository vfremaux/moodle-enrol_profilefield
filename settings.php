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
 * Cohort enrolment plugin settings and presets.
 *
 * @package    enrol_profilefield
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/enrol/profilefield/lib.php');

if ($ADMIN->fulltree) {

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_profilefield_settings', '', get_string('pluginname_desc', 'enrol_profilefield')));

    $key = 'enrol_profilefield/multiplefields';
    $label = get_string('configmultiplefields', 'enrol_profilefield');
    $desc = get_string('configmultiplefields_desc', 'enrol_profilefield');
    $default = 0;
    $options = array(
        SINGLE_FIELD => get_string('singlefield', 'enrol_profilefield'),
        MULTIPLE_FIELDS => get_string('multiplefields', 'enrol_profilefield'),
    );
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $options));

}
