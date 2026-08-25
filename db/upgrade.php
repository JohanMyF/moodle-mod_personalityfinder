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
 * Upgrade steps for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Runs upgrade steps for PersonalityFinder.
 *
 * @param int $oldversion The previously installed plugin version.
 * @return bool
 */
function xmldb_personalityfinder_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026061101) {
        $table = new xmldb_table('personalityfinder_responses');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('personalityfinderid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('responsesjson', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('resultsjson', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('personalityfinder_fk', XMLDB_KEY_FOREIGN, ['personalityfinderid'], 'personalityfinder', ['id']);
        $table->add_index('personalityfinder_user', XMLDB_INDEX_UNIQUE, ['personalityfinderid', 'userid']);
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2026061101, 'personalityfinder');
    }


    if ($oldversion < 2026061102) {
        $table = new xmldb_table('personalityfinder');
        $field = new xmldb_field('allowresubmit', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'configjson');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2026061102, 'personalityfinder');
    }

    return true;
}
