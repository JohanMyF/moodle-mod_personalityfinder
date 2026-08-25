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
 * Restore task for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/personalityfinder/backup/moodle2/restore_personalityfinder_stepslib.php');

/**
 * Defines the restore task for PersonalityFinder.
 */
class restore_personalityfinder_activity_task extends restore_activity_task {
    /**
     * No plugin-specific settings yet.
     */
    protected function define_my_settings() {
    }

    /**
     * Defines restore steps.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_personalityfinder_activity_structure_step('personalityfinder_structure', 'personalityfinder.xml'));
    }

    /**
     * Defines decode rules.
     *
     * @return array
     */
    public static function define_decode_contents() {
        return [];
    }

    /**
     * Defines link decode rules.
     *
     * @return array
     */
    public static function define_decode_rules() {
        return [];
    }
}
