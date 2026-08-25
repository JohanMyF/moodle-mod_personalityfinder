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

namespace mod_personalityfinder\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Respondent form for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class response_form extends \moodleform {
    /** @var array Instrument configuration. */
    protected array $instrumentconfig;

    /**
     * Defines the form.
     */
    protected function definition(): void {
        $mform = $this->_form;
        $this->instrumentconfig = $this->_customdata['config'] ?? [];
        $cmid = $this->_customdata['cmid'] ?? 0;

        $mform->addElement('hidden', 'id', $cmid);
        $mform->setType('id', PARAM_INT);

        $this->add_attributes_section($mform);
        $this->add_focus_dimensions_section($mform);
        $this->add_general_dimensions_section($mform);

        $this->add_action_buttons(false, get_string('submitresponses', 'mod_personalityfinder'));
    }

    /**
     * Adds the attributes section.
     *
     * @param \MoodleQuickForm $mform Form object.
     */
    protected function add_attributes_section(\MoodleQuickForm $mform): void {
        $attributes = $this->instrumentconfig['attributes'] ?? [];
        $prompt = $this->instrumentconfig['attribute_prompt'] ?? get_string('defaultattributeprompt', 'mod_personalityfinder');

        $mform->addElement('header', 'attributeshdr', get_string('attributes', 'mod_personalityfinder'));
        $mform->addElement('html', \html_writer::div(format_text($prompt, FORMAT_PLAIN), 'personalityfinder-prompt'));

        $attributesafes = self::unique_safe_names($attributes, 'attribute');
        foreach (array_values($attributes) as $attributeindex => $attribute) {
            if (empty($attribute['enabled'])) {
                continue;
            }
            $id = $attributesafes[$attributeindex] ?? self::safe_name($attribute['id'] ?? $attribute['label'] ?? uniqid('attr'));
            $label = format_string($attribute['label'] ?? $id);
            $description = trim((string)($attribute['description'] ?? ''));
            $card = \html_writer::start_div('personalityfinder-respondent-card');
            $card .= \html_writer::tag('h4', $label);
            if ($description !== '') {
                $card .= \html_writer::div(format_text($description, FORMAT_PLAIN), 'personalityfinder-muted');
            }
            $card .= \html_writer::end_div();
            $mform->addElement('html', $card);

            $group = [];
            $group[] = $mform->createElement('advcheckbox', 'attr_self_' . $id, '', get_string('attributemyself', 'mod_personalityfinder'));
            $group[] = $mform->createElement('advcheckbox', 'attr_others_' . $id, '', get_string('attributeothers', 'mod_personalityfinder'));
            $group[] = $mform->createElement('advcheckbox', 'attr_na_' . $id, '', get_string('attributenotapplicable', 'mod_personalityfinder'));
            $mform->addGroup($group, 'attr_group_' . $id, '', [' '], false);
        }
    }

    /**
     * Adds the focus dimensions section.
     *
     * @param \MoodleQuickForm $mform Form object.
     */
    protected function add_focus_dimensions_section(\MoodleQuickForm $mform): void {
        $dimensions = $this->instrumentconfig['focus_dimensions'] ?? [];
        $prompt = $this->instrumentconfig['two_dimensions_prompt'] ?? get_string('defaulttwodimensionsprompt', 'mod_personalityfinder');

        $mform->addElement('header', 'twodimensionshdr', get_string('twodimensionsheading', 'mod_personalityfinder'));
        $mform->addElement('html', \html_writer::div(format_text($prompt, FORMAT_PLAIN), 'personalityfinder-prompt'));

        $dimensionsafes = self::unique_safe_names($dimensions, 'dimension');
        foreach (array_values($dimensions) as $dimensionindex => $dimension) {
            $dimensionid = $dimensionsafes[$dimensionindex] ?? self::safe_name($dimension['id'] ?? uniqid('dim'));
            $leftlabel = format_string($dimension['left_label'] ?? get_string('exampleleft', 'mod_personalityfinder'));
            $rightlabel = format_string($dimension['right_label'] ?? get_string('exampleright', 'mod_personalityfinder'));
            $description = trim((string)($dimension['description'] ?? ''));
            $scalepoints = max(2, min(8, (int)($dimension['scale_points'] ?? ($this->instrumentconfig['settings']['focus_scale_points'] ?? 4))));

            $html = \html_writer::start_div('personalityfinder-rating-card');
            $html .= \html_writer::tag('h3', $leftlabel . ' ↔ ' . $rightlabel);
            if ($description !== '') {
                $html .= \html_writer::div(format_text($description, FORMAT_PLAIN), 'personalityfinder-muted');
            }
            $html .= \html_writer::end_div();
            $mform->addElement('html', $html);

            $items = array_values($dimension['items'] ?? []);
            $itemsafes = self::unique_safe_names($items, 'item');
            foreach ($items as $itemindex => $item) {
                $itemid = $itemsafes[$itemindex] ?? self::safe_name($item['id'] ?? uniqid('item'));
                $fieldname = 'focus_' . $dimensionid . '_' . $itemid;
                $leftstatement = format_string($item['left_statement'] ?? $leftlabel);
                $rightstatement = format_string($item['right_statement'] ?? $rightlabel);
                $this->add_scale_group($mform, $fieldname, $leftstatement, $rightstatement, $scalepoints, $leftlabel, $rightlabel);
            }
        }
    }

    /**
     * Adds the general dimensions section.
     *
     * @param \MoodleQuickForm $mform Form object.
     */
    protected function add_general_dimensions_section(\MoodleQuickForm $mform): void {
        $general = $this->instrumentconfig['general_dimensions'] ?? [];
        $prompt = $this->instrumentconfig['general_dimensions_prompt'] ?? get_string('defaultgeneraldimensionsprompt', 'mod_personalityfinder');
        $defaultscale = max(2, min(8, (int)($this->instrumentconfig['settings']['general_dimension_scale_points'] ?? 8)));

        $mform->addElement('header', 'generaldimensionshdr', get_string('generaldimensions', 'mod_personalityfinder'));
        $mform->addElement('html', \html_writer::div(format_text($prompt, FORMAT_PLAIN), 'personalityfinder-prompt'));

        $generalsafes = self::unique_safe_names($general, 'general');
        foreach (array_values($general) as $dimensionindex => $dimension) {
            if (($dimension['source'] ?? '') === 'focus_dimension') {
                continue;
            }
            $dimensionid = $generalsafes[$dimensionindex] ?? self::safe_name($dimension['id'] ?? uniqid('gen'));
            $leftlabel = format_string($dimension['left_label'] ?? get_string('exampleleft', 'mod_personalityfinder'));
            $rightlabel = format_string($dimension['right_label'] ?? get_string('exampleright', 'mod_personalityfinder'));
            $description = trim((string)($dimension['description'] ?? ''));
            $scalepoints = max(2, min(8, (int)($dimension['scale_points'] ?? $defaultscale)));

            $html = \html_writer::start_div('personalityfinder-rating-card');
            $html .= \html_writer::tag('h3', $leftlabel . ' ↔ ' . $rightlabel);
            if ($description !== '') {
                $html .= \html_writer::div(format_text($description, FORMAT_PLAIN), 'personalityfinder-muted');
            }
            $html .= \html_writer::end_div();
            $mform->addElement('html', $html);

            $this->add_scale_group($mform, 'general_' . $dimensionid, $leftlabel, $rightlabel, $scalepoints, $leftlabel, $rightlabel);
        }
    }

    /**
     * Adds a semantic differential rating group.
     *
     * @param \MoodleQuickForm $mform Form object.
     * @param string $fieldname Field name.
     * @param string $left Left label.
     * @param string $right Right label.
     * @param int $scalepoints Number of points.
     */
    protected function add_scale_group(\MoodleQuickForm $mform, string $fieldname, string $left, string $right,
            int $scalepoints, string $leftpole = '', string $rightpole = ''): void {
        $leftpole = $leftpole ?: $left;
        $rightpole = $rightpole ?: $right;
        $currentvalue = optional_param($fieldname, 0, PARAM_INT);
        $displayvalue = $currentvalue > 0 ? $currentvalue : (int)ceil($scalepoints / 2);

        // Store the selected value in a Moodle form element so validation, get_data(),
        // privacy export and response saving continue to use normal Moodle form plumbing.
        // The visible range control below writes to this hidden field via AMD JavaScript.
        $mform->addElement('hidden', $fieldname, $currentvalue > 0 ? $currentvalue : '');
        $mform->setType($fieldname, PARAM_INT);

        $tickhtml = '';
        for ($point = 1; $point <= $scalepoints; $point++) {
            $tickhtml .= \html_writer::span('', 'personalityfinder-slider-tick', [
                'aria-hidden' => 'true',
            ]);
        }

        $rangeattrs = [
            'type' => 'range',
            'class' => 'personalityfinder-stepped-range' . ($currentvalue > 0 ? ' is-selected' : ''),
            'min' => 1,
            'max' => $scalepoints,
            'step' => 1,
            'value' => $displayvalue,
            'data-target' => $fieldname,
            'aria-label' => $leftpole . ' to ' . $rightpole,
        ];

        $control = \html_writer::start_div('personalityfinder-scale-control', [
            'data-scale-points' => $scalepoints,
        ]);
        $control .= \html_writer::div(
            \html_writer::span($leftpole) . \html_writer::span($rightpole),
            'personalityfinder-scale-pole-labels'
        );
        $control .= \html_writer::start_div('personalityfinder-range-shell');
        $control .= \html_writer::empty_tag('input', $rangeattrs);
        $control .= \html_writer::div($tickhtml, 'personalityfinder-slider-ticks', [
            'aria-hidden' => 'true',
        ]);
        $control .= \html_writer::end_div();
        $control .= \html_writer::div(
            \html_writer::span(get_string('leanleft', 'mod_personalityfinder')) .
            \html_writer::span(get_string('leanright', 'mod_personalityfinder')),
            'personalityfinder-scale-lean-labels'
        );
        $control .= \html_writer::end_div();

        $mform->addElement('html', \html_writer::start_div('personalityfinder-respondent-scale personalityfinder-semantic-slider'));
        $mform->addElement('html', \html_writer::div($left, 'personalityfinder-scale-anchor personalityfinder-scale-anchor-left'));
        $mform->addElement('html', $control);
        $mform->addElement('html', \html_writer::div($right, 'personalityfinder-scale-anchor personalityfinder-scale-anchor-right'));
        $mform->addElement('html', \html_writer::end_div());
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
        $config = $this->instrumentconfig;

        $dimensions = array_values($config['focus_dimensions'] ?? []);
        $dimensionsafes = self::unique_safe_names($dimensions, 'dimension');
        foreach ($dimensions as $dimensionindex => $dimension) {
            $dimensionid = $dimensionsafes[$dimensionindex] ?? self::safe_name($dimension['id'] ?? '');
            $items = array_values($dimension['items'] ?? []);
            $itemsafes = self::unique_safe_names($items, 'item');
            foreach ($items as $itemindex => $item) {
                $itemid = $itemsafes[$itemindex] ?? self::safe_name($item['id'] ?? '');
                $field = 'focus_' . $dimensionid . '_' . $itemid;
                if (empty($data[$field])) {
                    $errors[$field] = get_string('required');
                }
            }
        }

        $general = array_values($config['general_dimensions'] ?? []);
        $generalsafes = self::unique_safe_names($general, 'general');
        foreach ($general as $dimensionindex => $dimension) {
            if (($dimension['source'] ?? '') === 'focus_dimension') {
                continue;
            }
            $field = 'general_' . ($generalsafes[$dimensionindex] ?? self::safe_name($dimension['id'] ?? ''));
            if (empty($data[$field])) {
                $errors[$field] = get_string('required');
            }
        }
        return $errors;
    }


    /**
     * Builds stable, unique form-name fragments for a list of configured items.
     *
     * Moodle form field names must be ASCII and unique. Some imported instruments
     * may contain repeated, empty or non-ASCII IDs, which previously caused several
     * controls to write to the same submitted field. That made sliders and
     * attribute selectors appear to behave like radio buttons and could prevent
     * the report from being generated after a redo.
     *
     * @param array $items Configured items.
     * @param string $prefix Fallback prefix.
     * @return array Unique safe names keyed by item index.
     */
    public static function unique_safe_names(array $items, string $prefix): array {
        $names = [];
        $used = [];
        foreach (array_values($items) as $index => $item) {
            $raw = '';
            if (is_array($item)) {
                $raw = (string)($item['id'] ?? $item['label'] ?? $item['left_label'] ?? $item['name'] ?? '');
            }
            $base = self::safe_name($raw);
            if ($base === 'item') {
                $base = self::safe_name($prefix . '_' . ($index + 1));
            }
            $candidate = $base;
            if (isset($used[$candidate])) {
                $candidate = $base . '_' . substr(sha1($raw . '_' . $index), 0, 8);
            }
            $counter = 2;
            while (isset($used[$candidate])) {
                $candidate = $base . '_' . $counter;
                $counter++;
            }
            $used[$candidate] = true;
            $names[$index] = $candidate;
        }
        return $names;
    }

    /**
     * Safely converts an ID to a form field fragment.
     *
     * @param string $name Raw name.
     * @return string Safe name.
     */
    public static function safe_name(string $name): string {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        if ($converted !== false && $converted !== '') {
            $name = $converted;
        }
        $name = strtolower(preg_replace('/[^a-zA-Z0-9_]+/', '_', $name));
        return trim($name, '_') ?: 'item';
    }
}
