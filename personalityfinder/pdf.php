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
 * Download a respondent reflection PDF for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/pdflib.php');

use mod_personalityfinder\local\reflection_pdf;

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('personalityfinder', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$personalityfinder = $DB->get_record('personalityfinder', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/personalityfinder:view', $context);

$latestrecords = $DB->get_records('personalityfinder_responses', [
    'personalityfinderid' => $personalityfinder->id,
    'userid' => $USER->id,
], 'timemodified DESC, id DESC', '*', 0, 1);
$latest = $latestrecords ? reset($latestrecords) : false;

if (!$latest) {
    redirect(new moodle_url('/mod/personalityfinder/view.php', ['id' => $cm->id]),
        get_string('noresultstodownload', 'mod_personalityfinder'), null, \core\output\notification::NOTIFY_WARNING);
}

$results = !empty($latest->resultsjson) ? json_decode($latest->resultsjson, true) : [];
if (!is_array($results) || empty($results)) {
    $rawresponses = !empty($latest->responsesjson) ? json_decode($latest->responsesjson, true) : [];
    if (is_array($rawresponses) && !empty($rawresponses)) {
        try {
            $config = \mod_personalityfinder\local\config::decode($personalityfinder->configjson ?? '');
            $results = \mod_personalityfinder\local\results::calculate($config, (object)$rawresponses);
            $latest->resultsjson = json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $latest->timemodified = time();
            $DB->update_record('personalityfinder_responses', $latest);
        } catch (\Throwable $e) {
            $results = [];
        }
    }
}

if (!is_array($results) || empty($results)) {
    redirect(new moodle_url('/mod/personalityfinder/view.php', ['id' => $cm->id]),
        get_string('noresultstodownload', 'mod_personalityfinder'), null, \core\output\notification::NOTIFY_WARNING);
}

reflection_pdf::download($personalityfinder, $course, $cm, $latest, $results);
