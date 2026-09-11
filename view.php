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
 * View page for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use mod_personalityfinder\form\response_form;
use mod_personalityfinder\local\config;
use mod_personalityfinder\local\results;

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('personalityfinder', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$personalityfinder = $DB->get_record('personalityfinder', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/personalityfinder:view', $context);

$PAGE->set_url('/mod/personalityfinder/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($personalityfinder->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$event = \mod_personalityfinder\event\course_module_viewed::create([
    'objectid' => $personalityfinder->id,
    'context' => $context,
]);
$event->add_record_snapshot('personalityfinder', $personalityfinder);
$event->trigger();
$PAGE->requires->js_call_amd('mod_personalityfinder/response_slider', 'init');
$PAGE->requires->js_call_amd('mod_personalityfinder/attribute_applicability', 'init');
$PAGE->requires->js_call_amd('mod_personalityfinder/description_toggle', 'init', [
    $cm->id,
    get_string('showdescription', 'mod_personalityfinder'),
    get_string('hidedescription', 'mod_personalityfinder'),
]);

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$canmanage = has_capability('mod/personalityfinder:manage', $context);

try {
    $config = config::decode($personalityfinder->configjson ?? '');
    $configerror = '';
} catch (moodle_exception $exception) {
    $config = config::default_instrument();
    $configerror = $exception->getMessage();
}

$latestrecords = $DB->get_records('personalityfinder_responses', [
    'personalityfinderid' => $personalityfinder->id,
    'userid' => $USER->id,
], 'timemodified DESC, id DESC', '*', 0, 1);
$latest = $latestrecords ? reset($latestrecords) : false;
$formlocked = $latest && empty($personalityfinder->allowresubmit);

if (optional_param('resetresponse', 0, PARAM_BOOL)) {
    require_sesskey();
    if ($formlocked) {
        redirect(new moodle_url('/mod/personalityfinder/view.php', ['id' => $cm->id, 'result' => 1]),
            get_string('resubmissionnotallowed', 'mod_personalityfinder'), null, \core\output\notification::NOTIFY_WARNING);
    }
    $DB->delete_records('personalityfinder_responses', [
        'personalityfinderid' => $personalityfinder->id,
        'userid' => $USER->id,
    ]);
    redirect(new moodle_url('/mod/personalityfinder/view.php', ['id' => $cm->id]),
        get_string('previousresponsecleared', 'mod_personalityfinder'), null, \core\output\notification::NOTIFY_WARNING);
}

response_form::prepare_slider_submission($config);
$mform = new response_form(null, ['config' => $config, 'cmid' => $cm->id]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', ['id' => $course->id]));
}

if ($data = $mform->get_data()) {
    if ($formlocked) {
        redirect(new moodle_url('/mod/personalityfinder/view.php', ['id' => $cm->id, 'result' => 1]),
            get_string('resubmissionnotallowed', 'mod_personalityfinder'));
    }
    $resultdata = results::calculate($config, $data);
    $existingrecords = $DB->get_records('personalityfinder_responses', [
        'personalityfinderid' => $personalityfinder->id,
        'userid' => $USER->id,
    ], 'timemodified DESC, id DESC');
    $record = $existingrecords ? reset($existingrecords) : false;
    $now = time();
    $saverecord = (object)[
        'personalityfinderid' => $personalityfinder->id,
        'userid' => $USER->id,
        'responsesjson' => json_encode((array)$data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'resultsjson' => json_encode($resultdata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'timemodified' => $now,
    ];
    if ($record) {
        $saverecord->id = $record->id;
        $DB->update_record('personalityfinder_responses', $saverecord);
        // Keep one current reflection per user/activity. Older duplicates can confuse
        // result display when an activity has been tested many times during development.
        foreach ($existingrecords as $oldrecord) {
            if ((int)$oldrecord->id !== (int)$record->id) {
                $DB->delete_records('personalityfinder_responses', ['id' => $oldrecord->id]);
            }
        }
    } else {
        $saverecord->timecreated = $now;
        $DB->insert_record('personalityfinder_responses', $saverecord);
    }
    redirect(new moodle_url('/mod/personalityfinder/view.php', ['id' => $cm->id, 'result' => 1]), get_string('responsesaved', 'mod_personalityfinder'));
}

$showresult = optional_param('result', 0, PARAM_BOOL) || $latest;
$resulttemplate = [];
if ($latest) {
    $decoded = [];
    if (!empty($latest->resultsjson)) {
        $decoded = json_decode($latest->resultsjson, true);
    }
    if (!is_array($decoded) || empty($decoded)) {
        $rawresponses = !empty($latest->responsesjson) ? json_decode($latest->responsesjson, true) : [];
        if (is_array($rawresponses) && !empty($rawresponses)) {
            $decoded = results::calculate($config, (object)$rawresponses);
            $latest->resultsjson = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $latest->timemodified = time();
            $DB->update_record('personalityfinder_responses', $latest);
        }
    }
    if (is_array($decoded) && !empty($decoded)) {
        $resulttemplate = results::for_template($decoded);
    }
}


$templatecontext = [
    'name' => format_string($personalityfinder->name),
    'cmid' => $cm->id,
    // Moodle 4.x already prints the theme-rendered activity description above
    // the activity content. We do not render the intro again in this template.
    'hasintro' => false,
    'intro' => '',
    'canmanage' => $canmanage,
    'builderurl' => (new moodle_url('/mod/personalityfinder/builder.php', ['id' => $cm->id]))->out(false),
    'pdfurl' => (new moodle_url('/mod/personalityfinder/pdf.php', ['id' => $cm->id]))->out(false),
    'reseturl' => (new moodle_url('/mod/personalityfinder/view.php', [
        'id' => $cm->id,
        'resetresponse' => 1,
        'sesskey' => sesskey(),
    ]))->out(false),
    'configerror' => $configerror,
    'instrument' => [
        'title' => format_string($config['instrument']['title'] ?? $personalityfinder->name),
        'description' => '',
        // The Moodle/theme-rendered activity intro is the single respondent-facing
        // description at the top of view.php. Do not also show the JSON instrument
        // description here, because many live instruments keep the same text in
        // both places during import/export testing.
        'showdescription' => false,
        'disclaimer' => format_text($config['instrument']['disclaimer'] ?? '', FORMAT_PLAIN),
    ],
    'hasresult' => !empty($resulttemplate),
    'formlocked' => $formlocked,
    'canredo' => !empty($latest) && !$formlocked,
    'result' => $resulttemplate,
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_personalityfinder/view', $templatecontext);
if ($configerror === '' && !$formlocked && empty($latest)) {
    $mform->display();
}
echo $OUTPUT->footer();
