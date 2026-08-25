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
 * Library functions for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_personalityfinder\local\config;

/**
 * Returns supported Moodle features.
 *
 * @param string $feature Feature constant.
 * @return mixed
 */
function personalityfinder_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
        case FEATURE_SHOW_DESCRIPTION:
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false;
        default:
            return null;
    }
}

/**
 * Adds a PersonalityFinder instance.
 *
 * @param stdClass $data Form data.
 * @param mod_personalityfinder_mod_form|null $mform Form object.
 * @return int New instance ID.
 */
function personalityfinder_add_instance($data, $mform = null) {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;
    $data->configjson = config::normalise_json($data->configjson ?? '');

    return $DB->insert_record('personalityfinder', $data);
}

/**
 * Updates a PersonalityFinder instance.
 *
 * @param stdClass $data Form data.
 * @param mod_personalityfinder_mod_form|null $mform Form object.
 * @return bool
 */
function personalityfinder_update_instance($data, $mform = null) {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();
    $data->configjson = config::normalise_json($data->configjson ?? '');

    return $DB->update_record('personalityfinder', $data);
}

/**
 * Deletes a PersonalityFinder instance.
 *
 * @param int $id Instance ID.
 * @return bool
 */
function personalityfinder_delete_instance($id) {
    global $DB;

    if (!$DB->record_exists('personalityfinder', ['id' => $id])) {
        return false;
    }

    $DB->delete_records('personalityfinder_responses', ['personalityfinderid' => $id]);
    $DB->delete_records('personalityfinder', ['id' => $id]);
    return true;
}

/**
 * Provides course module info for the course page.
 *
 * @param cm_info $cm Course module info.
 */
function personalityfinder_cm_info_view(cm_info $cm): void {
    global $DB;

    $instance = $DB->get_record('personalityfinder', ['id' => $cm->instance], 'id, name, intro, introformat', IGNORE_MISSING);
    if (!$instance) {
        return;
    }

    if ($cm->showdescription && trim((string)$instance->intro) !== '') {
        $cm->set_content(format_module_intro('personalityfinder', $instance, $cm->id, false));
    }
}

/**
 * Returns the activity icon URL if needed by Moodle.
 *
 * @return moodle_url
 */
function personalityfinder_get_icon(): moodle_url {
    return new moodle_url('/mod/personalityfinder/pix/icon.svg');
}
