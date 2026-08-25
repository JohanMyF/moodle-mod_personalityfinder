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
 * Restore structure step for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines restore structure for PersonalityFinder.
 */
class restore_personalityfinder_activity_structure_step extends restore_activity_structure_step {
    /**
     * Defines restore paths.
     *
     * @return array
     */
    protected function define_structure() {
        return [new restore_path_element('personalityfinder', '/activity/personalityfinder')];
    }

    /**
     * Processes a restored PersonalityFinder record.
     *
     * @param array $data Restored data.
     */
    protected function process_personalityfinder($data) {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();
        $oldid = $data->id;
        unset($data->id);

        $newitemid = $DB->insert_record('personalityfinder', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Runs after restore.
     */
    protected function after_execute() {
        $this->add_related_files('mod_personalityfinder', 'intro', null);
    }
}
