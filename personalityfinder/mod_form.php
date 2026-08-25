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
 * Activity settings form for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

use mod_personalityfinder\local\config;

/**
 * Moodle activity settings form.
 */
class mod_personalityfinder_mod_form extends moodleform_mod {
    /**
     * Defines the form.
     */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name', 'mod_personalityfinder'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements(get_string('intro', 'mod_personalityfinder'));

        $mform->addElement('advcheckbox', 'allowresubmit', get_string('allowresubmit', 'mod_personalityfinder'),
            get_string('allowresubmit_helptext', 'mod_personalityfinder'));
        $mform->setDefault('allowresubmit', 1);

        // The instrument definition is intentionally stored as JSON, but the main
        // Moodle settings form should remain simple. Teachers build the instrument
        // on builder.php after the activity instance has been created.
        $mform->addElement('hidden', 'configjson');
        $mform->setType('configjson', PARAM_RAW);
        $mform->setDefault('configjson', config::default_json());

        $mform->addElement('header', 'instrumentbuilderheader', get_string('instrumentbuilderheading', 'mod_personalityfinder'));
        $mform->addElement('static', 'instrumentbuilderintro', get_string('builder', 'mod_personalityfinder'),
            get_string('instrumentbuilderintro', 'mod_personalityfinder'));

        $mform->addElement('static', 'instrumentbuilderwarning', '', html_writer::div(
            get_string('instrumentbuilderwarning', 'mod_personalityfinder'),
            'alert alert-warning'
        ));

        $mform->addElement('static', 'instrumentbuilderbutton', '', $this->builder_button_html());

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Returns the builder button HTML.
     *
     * @return string HTML.
     */
    private function builder_button_html(): string {
        $cmid = $this->get_coursemodule_id();

        if (empty($cmid)) {
            return html_writer::div(
                get_string('instrumentbuildernotavailable', 'mod_personalityfinder'),
                'alert alert-info'
            );
        }

        $url = new moodle_url('/mod/personalityfinder/builder.php', ['id' => $cmid]);
        return html_writer::link($url, get_string('builderlink', 'mod_personalityfinder'), [
            'class' => 'btn btn-primary',
        ]);
    }

    /**
     * Gets the course module id when editing an existing activity.
     *
     * @return int Course module id or zero when the activity has not been created yet.
     */
    private function get_coursemodule_id(): int {
        if (!empty($this->current->coursemodule)) {
            return (int)$this->current->coursemodule;
        }

        if (!empty($this->_cm) && !empty($this->_cm->id)) {
            return (int)$this->_cm->id;
        }

        return 0;
    }

    /**
     * Validates the form.
     *
     * @param array $data Submitted data.
     * @param array $files Submitted files.
     * @return array Errors.
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if (empty($data['configjson'])) {
            return $errors;
        }

        try {
            config::normalise_json($data['configjson']);
        } catch (moodle_exception $exception) {
            $errors['configjson'] = $exception->getMessage();
        }

        return $errors;
    }
}
