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
 * Backup structure step for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines backup structure for PersonalityFinder.
 */
class backup_personalityfinder_activity_structure_step extends backup_activity_structure_step {
    /**
     * Defines the backup structure.
     *
     * @return backup_nested_element
     */
    protected function define_structure() {
        $personalityfinder = new backup_nested_element('personalityfinder', ['id'], [
            'course', 'name', 'intro', 'introformat', 'configjson', 'allowresubmit', 'timecreated', 'timemodified'
        ]);

        $personalityfinder->set_source_table('personalityfinder', ['id' => backup::VAR_ACTIVITYID]);
        $personalityfinder->annotate_files('mod_personalityfinder', 'intro', null);

        return $this->prepare_activity_structure($personalityfinder);
    }
}
