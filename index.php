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
 * Course index page for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/tablelib.php');

$id = required_param('id', PARAM_INT);
$course = get_course($id);
require_login($course);

$PAGE->set_url('/mod/personalityfinder/index.php', ['id' => $course->id]);
$PAGE->set_title(get_string('modulenameplural', 'mod_personalityfinder'));
$PAGE->set_heading(format_string($course->fullname));

$modinfo = get_fast_modinfo($course);
$instances = [];
foreach ($modinfo->get_instances_of('personalityfinder') as $cm) {
    if (!$cm->uservisible) {
        continue;
    }
    $instances[] = [
        'name' => html_writer::link(new moodle_url('/mod/personalityfinder/view.php', ['id' => $cm->id]), format_string($cm->name)),
        'section' => $cm->get_section_info()->section,
    ];
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_personalityfinder'));

if (empty($instances)) {
    echo $OUTPUT->notification(get_string('thereareno', 'moodle', get_string('modulenameplural', 'mod_personalityfinder')), 'info');
} else {
    $table = new html_table();
    $table->head = [get_string('name'), get_string('section')];
    foreach ($instances as $instance) {
        $table->data[] = [$instance['name'], $instance['section']];
    }
    echo html_writer::table($table);
}

echo $OUTPUT->footer();
