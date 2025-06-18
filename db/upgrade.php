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
 * This file keeps track of upgrades to the paypal enrolment plugin
 *
 * @package    enrol_profilefield
 * @copyright  2010 Eugene Venter
 * @author     Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_profilefield_upgrade($oldversion) {
    global $CFG;

    if ($oldversion < 2019021200) {
        convert_profile_value();

        upgrade_plugin_savepoint(true, 2019021200, 'enrol', 'profilefield');
    }

    if ($oldversion < 2020050800) {
        convert_profile3_value();

        upgrade_plugin_savepoint(true, 2020050800, 'enrol', 'profilefield');
    }

    return true;
}

function convert_profile_value() {
    global $DB;

    mtrace('Copying customchar2 to customtext2');
    $params = array('enrol' => 'profilefied');
    $enrols = $DB->get_records('enrol', $params);

    if (!empty($enrols)) {
        foreach ($enrols as $e) {
            $e->customtext2 = $e->customchar2;
            $DB->update_record('enrol', $e);
        }
    }
    mtrace('Done.');
}

function convert_profile3_value() {
    global $DB;

    mtrace('Copying customint3 to customchar3');
    $params = array('enrol' => 'profilefied');
    $enrols = $DB->get_records('enrol', $params);

    if (!empty($enrols)) {
        foreach ($enrols as $e) {
            $e->customchar3 = $e->customint3;
            $DB->update_record('enrol', $e);
        }
    }
    mtrace('Done.');
}