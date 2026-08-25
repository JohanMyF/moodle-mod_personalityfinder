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

use html_writer;
use mod_personalityfinder\local\config;

/**
 * JSON and visual builder form for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class builder_form extends \moodleform {
    /**
     * Defines the form.
     */
    public function definition(): void {
        $mform = $this->_form;
        $customdata = $this->_customdata;
        $json = $customdata['configjson'] ?? config::default_json();
        $activeaccordion = $customdata['activeaccordion'] ?? '';

        try {
            $instrument = config::decode($json);
        } catch (\moodle_exception $exception) {
            $instrument = config::default_instrument();
        }

        $mform->addElement('hidden', 'id', $customdata['cmid']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('header', 'jsonheader', get_string('jsoneditorheading', 'mod_personalityfinder'));
        $mform->setExpanded('jsonheader', $activeaccordion === 'json');
        $mform->addElement('static', 'jsonintro', '', get_string('jsoneditorintro', 'mod_personalityfinder'));

        $mform->addElement('filepicker', 'importjsonfile', get_string('importjsonfile', 'mod_personalityfinder'), null, [
            'accepted_types' => ['.json', 'application/json', 'text/plain'],
            'maxbytes' => 1048576,
        ]);
        $mform->addHelpButton('importjsonfile', 'importjsonfile', 'mod_personalityfinder');
        $importbuttons = [];
        $importbuttons[] = $mform->createElement('submit', 'importjsonbutton', get_string('importjsonbutton', 'mod_personalityfinder'));
        $mform->addGroup($importbuttons, 'importjsonbuttonar', '', [' '], false);

        $mform->addElement('static', 'jsonadvancednote', '', get_string('jsonadvancednote', 'mod_personalityfinder'));
        $mform->addElement('textarea', 'configjson', get_string('configjson', 'mod_personalityfinder'), [
            'rows' => 22,
            'cols' => 110,
            'class' => 'personalityfinder-json-textarea',
        ]);
        $mform->setType('configjson', PARAM_RAW);
        $mform->addHelpButton('configjson', 'configjson', 'mod_personalityfinder');
        $mform->setDefault('configjson', $json);

        $exporturl = new \moodle_url('/mod/personalityfinder/builder.php', [
            'id' => $customdata['cmid'],
            'exportjson' => 1,
            'sesskey' => sesskey(),
        ]);
        $jsonbuttons = [];
        $jsonbuttons[] = $mform->createElement('submit', 'savejsonbutton', get_string('savejsonbutton', 'mod_personalityfinder'), [
            'class' => 'btn btn-primary personalityfinder-builder-panel-save-button',
        ]);
        $jsonbuttons[] = $mform->createElement('submit', 'validatejsonbutton', get_string('validatejsonbutton', 'mod_personalityfinder'));
        $jsonbuttons[] = $mform->createElement('static', 'exportjsonbutton', '', html_writer::link(
            $exporturl,
            get_string('exportjsonbutton', 'mod_personalityfinder'),
            ['class' => 'btn btn-secondary personalityfinder-export-json-button']
        ));
        $mform->addGroup($jsonbuttons, 'jsonbuttonar', '', [' '], false);

        $mform->addElement('header', 'visualbuilderheader', get_string('visualbuilderheading', 'mod_personalityfinder'));
        $mform->setExpanded('visualbuilderheader', $activeaccordion === 'attributes');
        $mform->addElement('static', 'visualbuilderintro', '', get_string('visualbuilderintro', 'mod_personalityfinder'));
        $this->add_attributes_builder($instrument);

        $mform->addElement('header', 'twodimensionsheader', get_string('twodimensionsheading', 'mod_personalityfinder'));
        $mform->setExpanded('twodimensionsheader', $activeaccordion === 'twodimensions');
        $mform->addElement('static', 'twodimensionsintro', '', get_string('twodimensionsintro', 'mod_personalityfinder'));
        $this->add_two_dimensions_builder($instrument);

        $mform->addElement('header', 'generaldimensionsheader', get_string('generalpersonalitydimensionsheading', 'mod_personalityfinder'));
        $mform->setExpanded('generaldimensionsheader', $activeaccordion === 'generaldimensions');
        $mform->addElement('static', 'generaldimensionsintrostatic', '', get_string('generalpersonalitydimensionsintro', 'mod_personalityfinder'));
        $this->add_general_dimensions_builder($instrument);

        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('saveinstrument', 'mod_personalityfinder'), [
            'class' => 'btn btn-primary personalityfinder-builder-settings-save-button',
        ]);
        $buttonarray[] = $mform->createElement('submit', 'loadexample', get_string('configjsonreset', 'mod_personalityfinder'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addElement('html', html_writer::start_tag('div', ['id' => 'personalityfinder-builder-actions']));
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->addElement('html', html_writer::end_tag('div'));
    }

    /**
     * Adds the visual attribute builder.
     *
     * @param array $instrument Decoded instrument configuration.
     */
    private function add_attributes_builder(array $instrument): void {
        $mform = $this->_form;
        $attributes = array_values($instrument['attributes'] ?? []);
        $mform->addElement('html', html_writer::start_tag('div', ['class' => 'personalityfinder-builder-status']));
        $mform->addElement('html', html_writer::tag('h3', get_string('attributesbuilderheading', 'mod_personalityfinder'), [
            'class' => 'personalityfinder-builder-subsection-heading',
        ]));
        $mform->addElement('html', html_writer::tag('p', get_string('attributesbuilderintro', 'mod_personalityfinder'), [
            'class' => 'personalityfinder-builder-helptext',
        ]));

        $prompt = $instrument['attribute_prompt'] ?? get_string('defaultattributeprompt', 'mod_personalityfinder');
        $mform->addElement('textarea', 'attribute_prompt', get_string('attributeprompt', 'mod_personalityfinder'), [
            'rows' => 3,
            'cols' => 90,
        ]);
        $mform->setType('attribute_prompt', PARAM_TEXT);
        $mform->setDefault('attribute_prompt', $prompt);
        $mform->addHelpButton('attribute_prompt', 'attributeprompt', 'mod_personalityfinder');

        $mform->addElement('hidden', 'attributescount', count($attributes));
        $mform->setType('attributescount', PARAM_INT);

        $openindex = ($this->_customdata['activeaccordion'] ?? '') === 'attributes' && isset($this->_customdata['openattr'])
            ? (int)$this->_customdata['openattr'] : -1;
        foreach ($attributes as $index => $attribute) {
            $this->add_attribute_row($index, $attribute, $openindex);
        }

        if (empty($attributes)) {
            $mform->addElement('html', html_writer::div(get_string('noattributes', 'mod_personalityfinder'), 'alert alert-info'));
        }

        $addurl = new \moodle_url('/mod/personalityfinder/builder.php', [
            'id' => $this->_customdata['cmid'],
            'addattr' => 1,
            'activeaccordion' => 'attributes',
            'sesskey' => sesskey(),
        ]);
        $addbutton = html_writer::link($addurl, '+ ' . get_string('addattribute', 'mod_personalityfinder'), [
            'class' => 'personalityfinder-builder-add-row-button',
        ]);
        $mform->addElement('html', html_writer::div($addbutton, 'personalityfinder-builder-add-row', [
            'id' => 'personalityfinder-attributes-add-row',
        ]));

        $mform->addElement('html', html_writer::end_tag('div'));
    }

    /**
     * Adds a single attribute editor row.
     *
     * @param int $index Attribute index.
     * @param array $attribute Attribute data.
     * @param int $openindex Open attribute index.
     */
    private function add_attribute_row(int $index, array $attribute, int $openindex = 0): void {
        $mform = $this->_form;
        $id = clean_param($attribute['id'] ?? 'attribute_' . ($index + 1), PARAM_ALPHANUMEXT);
        $label = format_string($attribute['label'] ?? get_string('attribute', 'mod_personalityfinder') . ' ' . ($index + 1));
        $summary = s($label);

        $detailsattrs = [
            'class' => 'personalityfinder-builder-mini-details',
            'id' => 'personalityfinder-attribute-' . $index,
        ];
        if ($openindex >= 0 && $index === $openindex) {
            $detailsattrs['open'] = 'open';
        }
        $mform->addElement('html', html_writer::start_tag('details', $detailsattrs));
        $deleteurl = new \moodle_url('/mod/personalityfinder/builder.php', [
            'id' => $this->_customdata['cmid'],
            'deleteattr' => $index,
            'activeaccordion' => 'attributes',
            'sesskey' => sesskey(),
        ]);
        $deletebutton = html_writer::link($deleteurl, get_string('deleteattribute', 'mod_personalityfinder'), [
            'class' => 'personalityfinder-builder-bar-button personalityfinder-builder-delete-bar-button',
            'data-action' => 'delete-attribute',
            'onclick' => 'return confirm(' . json_encode(get_string('deleteattributeconfirm', 'mod_personalityfinder')) . ');',
        ]);
        $summaryhtml = html_writer::span($summary, 'personalityfinder-builder-summary-text') . $deletebutton;
        $mform->addElement('html', html_writer::tag('summary', $summaryhtml, [
            'class' => 'personalityfinder-builder-mini-summary',
        ]));
        $mform->addElement('html', html_writer::start_tag('div', ['class' => 'personalityfinder-builder-row personalityfinder-builder-compact-row']));

        $mform->addElement('hidden', 'attr_id_' . $index, $id);
        $mform->setType('attr_id_' . $index, PARAM_ALPHANUMEXT);

        $mform->addElement('text', 'attr_label_' . $index, get_string('attributelabel', 'mod_personalityfinder'), ['size' => 64]);
        $mform->setType('attr_label_' . $index, PARAM_TEXT);
        $mform->setDefault('attr_label_' . $index, $attribute['label'] ?? '');

        $mform->addElement('textarea', 'attr_description_' . $index, get_string('attributedescription', 'mod_personalityfinder'), [
            'rows' => 3,
            'cols' => 70,
        ]);
        $mform->setType('attr_description_' . $index, PARAM_TEXT);
        $mform->setDefault('attr_description_' . $index, $attribute['description'] ?? '');

        $mform->addElement('advcheckbox', 'attr_enabled_' . $index, get_string('attributeenabled', 'mod_personalityfinder'));
        $mform->setDefault('attr_enabled_' . $index, !array_key_exists('enabled', $attribute) || !empty($attribute['enabled']) ? 1 : 0);

        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'saveattribute_' . $index, get_string('saveattribute', 'mod_personalityfinder'), [
            'class' => 'btn btn-primary personalityfinder-builder-panel-save-button',
        ]);
        $mform->addGroup($buttonarray, 'attr_actions_' . $index, '', [' '], false);

        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('details'));
    }

    /**
     * Adds the two focus dimension builder section.
     *
     * @param array $instrument Decoded instrument configuration.
     */
    private function add_two_dimensions_builder(array $instrument): void {
        $mform = $this->_form;
        $dimensions = array_values($instrument['focus_dimensions'] ?? []);
        $dimensions = array_pad(array_slice($dimensions, 0, 2), 2, []);
        $scale = $this->get_focus_scale_points($instrument, $dimensions);

        $mform->addElement('html', html_writer::start_tag('div', [
            'class' => 'personalityfinder-builder-status personalityfinder-two-dimensions-builder',
        ]));

        $prompt = $instrument['two_dimensions_prompt'] ?? get_string('defaulttwodimensionsprompt', 'mod_personalityfinder');
        $mform->addElement('textarea', 'two_dimensions_prompt', get_string('twodimensionsprompt', 'mod_personalityfinder'), [
            'rows' => 3,
            'cols' => 90,
        ]);
        $mform->setType('two_dimensions_prompt', PARAM_TEXT);
        $mform->setDefault('two_dimensions_prompt', $prompt);
        $mform->addHelpButton('two_dimensions_prompt', 'twodimensionsprompt', 'mod_personalityfinder');

        $mform->addElement('html', html_writer::start_tag('div', ['class' => 'personalityfinder-scale-settings-card']));
        $scaleoptions = [];
        for ($i = 2; $i <= 8; $i++) {
            $scaleoptions[$i] = $i;
        }
        $mform->addElement('select', 'focus_scale_points', get_string('focusscalepoints', 'mod_personalityfinder'), $scaleoptions);
        $mform->setType('focus_scale_points', PARAM_INT);
        $mform->setDefault('focus_scale_points', $scale);
        $mform->addHelpButton('focus_scale_points', 'focusscalepoints', 'mod_personalityfinder');
        $mform->addElement('html', html_writer::div(get_string('focusscalepreviewnote', 'mod_personalityfinder'), 'personalityfinder-scale-settings-note'));
        $mform->addElement('html', html_writer::div($this->scale_preview_html($scale), 'personalityfinder-scale-preview', [
            'aria-label' => get_string('scalepreview', 'mod_personalityfinder'),
        ]));
        $mform->addElement('html', html_writer::end_tag('div'));

        $mform->addElement('hidden', 'focusdimensionscount', 2);
        $mform->setType('focusdimensionscount', PARAM_INT);

        foreach ($dimensions as $dimensionindex => $dimension) {
            $this->add_focus_dimension_card($dimensionindex, $dimension, $scale);
        }

        $this->add_matrix_interpretation_builder($instrument, $dimensions);

        $mform->addElement('html', html_writer::end_tag('div'));
    }

    /**
     * Gets the shared focus scale points.
     *
     * @param array $instrument Instrument data.
     * @param array $dimensions Dimension data.
     * @return int
     */
    private function get_focus_scale_points(array $instrument, array $dimensions): int {
        $scale = (int)($instrument['settings']['focus_scale_points'] ?? 0);
        if ($scale < 2 && !empty($dimensions[0]['scale_points'])) {
            $scale = (int)$dimensions[0]['scale_points'];
        }
        if ($scale < 2) {
            $scale = 4;
        }
        return max(2, min(8, $scale));
    }

    /**
     * Adds a focus dimension card.
     *
     * @param int $dimensionindex Dimension index.
     * @param array $dimension Dimension data.
     * @param int $scale Scale points.
     */
    private function add_focus_dimension_card(int $dimensionindex, array $dimension, int $scale): void {
        $mform = $this->_form;
        $number = $dimensionindex + 1;
        $items = array_values($dimension['items'] ?? []);
        $leftlabel = $dimension['left_label'] ?? ($dimensionindex === 0 ? 'People-oriented' : 'Structured');
        $rightlabel = $dimension['right_label'] ?? ($dimensionindex === 0 ? 'Task-oriented' : 'Unstructured');
        $heading = trim($leftlabel . ' ↔ ' . $rightlabel);
        if ($heading === '↔') {
            $heading = get_string('dimensionnumber', 'mod_personalityfinder', $number);
        }

        $mform->addElement('html', html_writer::start_tag('div', [
            'class' => 'personalityfinder-focus-dimension-card',
            'id' => 'personalityfinder-focus-dimension-' . $dimensionindex,
        ]));

        $enabled = !array_key_exists('enabled', $dimension) || !empty($dimension['enabled']);
        $enabledhtml = html_writer::tag('span', get_string('attributeenabled', 'mod_personalityfinder'), [
            'class' => 'personalityfinder-focus-enabled-label',
        ]);
        $mform->addElement('html', html_writer::div(
            html_writer::span('⋮⋮', 'personalityfinder-builder-drag-handle') .
            html_writer::span(s(get_string('dimensionnumber', 'mod_personalityfinder', $number)), 'personalityfinder-focus-card-title') .
            html_writer::span(s($heading), 'personalityfinder-focus-card-subtitle') .
            html_writer::span($enabledhtml, 'personalityfinder-focus-card-status'),
            'personalityfinder-focus-card-heading'
        ));

        $mform->addElement('hidden', 'focus_id_' . $dimensionindex, clean_param($dimension['id'] ?? 'dimension_' . $number, PARAM_ALPHANUMEXT));
        $mform->setType('focus_id_' . $dimensionindex, PARAM_ALPHANUMEXT);

        $mform->addElement('html', html_writer::start_tag('div', ['class' => 'personalityfinder-focus-grid']));
        $mform->addElement('text', 'focus_left_label_' . $dimensionindex, get_string('leftpoleextreme', 'mod_personalityfinder'), ['size' => 45]);
        $mform->setType('focus_left_label_' . $dimensionindex, PARAM_TEXT);
        $mform->setDefault('focus_left_label_' . $dimensionindex, $leftlabel);
        $mform->addElement('text', 'focus_right_label_' . $dimensionindex, get_string('rightpoleextreme', 'mod_personalityfinder'), ['size' => 45]);
        $mform->setType('focus_right_label_' . $dimensionindex, PARAM_TEXT);
        $mform->setDefault('focus_right_label_' . $dimensionindex, $rightlabel);
        $mform->addElement('html', html_writer::end_tag('div'));

        $mform->addElement('textarea', 'focus_description_' . $dimensionindex, get_string('dimensiondescription', 'mod_personalityfinder'), [
            'rows' => 2,
            'cols' => 90,
        ]);
        $mform->setType('focus_description_' . $dimensionindex, PARAM_TEXT);
        $mform->setDefault('focus_description_' . $dimensionindex, $dimension['description'] ?? '');

        $mform->addElement('advcheckbox', 'focus_enabled_' . $dimensionindex, get_string('attributeenabled', 'mod_personalityfinder'));
        $mform->setDefault('focus_enabled_' . $dimensionindex, $enabled ? 1 : 0);

        $mform->addElement('hidden', 'focus_items_count_' . $dimensionindex, count($items));
        $mform->setType('focus_items_count_' . $dimensionindex, PARAM_INT);

        $mform->addElement('html', html_writer::tag('h4', get_string('semanticdifferentialsfor', 'mod_personalityfinder', $number), [
            'class' => 'personalityfinder-builder-subsection-heading personalityfinder-semantic-heading',
        ]));
        $mform->addElement('html', html_writer::start_tag('div', ['class' => 'personalityfinder-semantic-table']));
        $mform->addElement('html', html_writer::div(
            html_writer::span(get_string('leftstatement', 'mod_personalityfinder')) .
            html_writer::span(get_string('scalepreview', 'mod_personalityfinder')) .
            html_writer::span(get_string('rightstatement', 'mod_personalityfinder')) .
            html_writer::span(get_string('actions', 'mod_personalityfinder')),
            'personalityfinder-semantic-table-header'
        ));

        foreach ($items as $itemindex => $item) {
            $this->add_semantic_differential_row($dimensionindex, $itemindex, $item, $scale);
        }

        if (empty($items)) {
            $mform->addElement('html', html_writer::div(get_string('nosemanticdifferentials', 'mod_personalityfinder'), 'alert alert-info'));
        }

        $addurl = new \moodle_url('/mod/personalityfinder/builder.php', [
            'id' => $this->_customdata['cmid'],
            'addfocusitem' => $dimensionindex,
            'activeaccordion' => 'twodimensions',
            'sesskey' => sesskey(),
        ]);
        $mform->addElement('html', html_writer::div(html_writer::link(
            $addurl,
            '+ ' . get_string('addsemanticdifferential', 'mod_personalityfinder'),
            ['class' => 'personalityfinder-builder-small-add-button']
        ), 'personalityfinder-semantic-add-row'));
        $mform->addElement('html', html_writer::end_tag('div'));

        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'savefocusdimension_' . $dimensionindex, get_string('savedimension', 'mod_personalityfinder'), [
            'class' => 'btn btn-primary personalityfinder-builder-panel-save-button',
        ]);
        $mform->addGroup($buttonarray, 'focus_actions_' . $dimensionindex, '', [' '], false);

        $mform->addElement('html', html_writer::end_tag('div'));
    }

    /**
     * Adds a semantic differential item row.
     *
     * @param int $dimensionindex Dimension index.
     * @param int $itemindex Item index.
     * @param array $item Item data.
     * @param int $scale Scale points.
     */
    private function add_semantic_differential_row(int $dimensionindex, int $itemindex, array $item, int $scale): void {
        $mform = $this->_form;
        $rowid = 'personalityfinder-focus-' . $dimensionindex . '-item-' . $itemindex;
        $deleteurl = new \moodle_url('/mod/personalityfinder/builder.php', [
            'id' => $this->_customdata['cmid'],
            'deletefocusdim' => $dimensionindex,
            'deletefocusitem' => $itemindex,
            'activeaccordion' => 'twodimensions',
            'sesskey' => sesskey(),
        ]);
        $deletebutton = html_writer::link($deleteurl, get_string('deleteattribute', 'mod_personalityfinder'), [
            'class' => 'personalityfinder-builder-delete-icon-button',
            'onclick' => 'return confirm(' . json_encode(get_string('deletesemanticconfirm', 'mod_personalityfinder')) . ');',
        ]);

        $mform->addElement('html', html_writer::start_tag('div', [
            'class' => 'personalityfinder-semantic-row',
            'id' => $rowid,
        ]));
        $mform->addElement('hidden', 'focus_item_id_' . $dimensionindex . '_' . $itemindex,
            clean_param($item['id'] ?? 'item_' . ($itemindex + 1), PARAM_ALPHANUMEXT));
        $mform->setType('focus_item_id_' . $dimensionindex . '_' . $itemindex, PARAM_ALPHANUMEXT);

        $mform->addElement('text', 'focus_item_left_' . $dimensionindex . '_' . $itemindex, '', ['size' => 44]);
        $mform->setType('focus_item_left_' . $dimensionindex . '_' . $itemindex, PARAM_TEXT);
        $mform->setDefault('focus_item_left_' . $dimensionindex . '_' . $itemindex, $item['left_statement'] ?? '');

        $mform->addElement('html', html_writer::div($this->scale_preview_html($scale), 'personalityfinder-semantic-scale-preview'));

        $mform->addElement('text', 'focus_item_right_' . $dimensionindex . '_' . $itemindex, '', ['size' => 44]);
        $mform->setType('focus_item_right_' . $dimensionindex . '_' . $itemindex, PARAM_TEXT);
        $mform->setDefault('focus_item_right_' . $dimensionindex . '_' . $itemindex, $item['right_statement'] ?? '');

        $mform->addElement('html', html_writer::div($deletebutton, 'personalityfinder-semantic-actions'));
        $mform->addElement('html', html_writer::end_tag('div'));
    }

    /**
     * Adds the matrix interpretation editor for the two focus dimensions.
     *
     * @param array $instrument Decoded instrument configuration.
     * @param array $dimensions Two focus dimensions.
     */
    private function add_matrix_interpretation_builder(array $instrument, array $dimensions): void {
        $mform = $this->_form;
        $matrix = $instrument['matrix'] ?? [];
        $quadrants = array_values($matrix['quadrants'] ?? []);
        $quadrants = $this->normalise_quadrants_for_builder($quadrants);

        // Dimension 1 is the horizontal axis. Dimension 2 is the vertical axis.
        // On the vertical axis, the right pole is displayed at the top.
        $horizontal = $dimensions[0] ?? [];
        $vertical = $dimensions[1] ?? [];
        $hleft = $horizontal['left_label'] ?? get_string('leftpoleextreme', 'mod_personalityfinder');
        $hright = $horizontal['right_label'] ?? get_string('rightpoleextreme', 'mod_personalityfinder');
        $vbottom = $vertical['left_label'] ?? get_string('leftpoleextreme', 'mod_personalityfinder');
        $vtop = $vertical['right_label'] ?? get_string('rightpoleextreme', 'mod_personalityfinder');

        $mform->addElement('html', html_writer::start_tag('details', [
            'class' => 'personalityfinder-builder-mini-details personalityfinder-matrix-builder-details',
            'id' => 'personalityfinder-matrix-interpretation',
        ]));
        $mform->addElement('html', html_writer::tag('summary',
            html_writer::span(get_string('matrixinterpretationheading', 'mod_personalityfinder'), 'personalityfinder-builder-summary-text'),
            ['class' => 'personalityfinder-builder-mini-summary']
        ));
        $mform->addElement('html', html_writer::start_tag('div', [
            'class' => 'personalityfinder-builder-row personalityfinder-matrix-builder-row',
        ]));

        $mform->addElement('static', 'matrix_intro_static', '', get_string('matrixinterpretationintro', 'mod_personalityfinder'));
        $mform->addElement('advcheckbox', 'matrix_enabled', get_string('matrixenabled', 'mod_personalityfinder'));
        $mform->setDefault('matrix_enabled', !array_key_exists('enabled', $matrix) || !empty($matrix['enabled']) ? 1 : 0);

        $mform->addElement('hidden', 'matrix_quadrants_count', 4);
        $mform->setType('matrix_quadrants_count', PARAM_INT);

        $mform->addElement('html', html_writer::start_tag('div', [
            'class' => 'personalityfinder-matrix-visual-editor',
            'aria-label' => get_string('matrixinterpretationheading', 'mod_personalityfinder'),
        ]));
        $mform->addElement('html', html_writer::start_tag('div', [
            'class' => 'personalityfinder-matrix-visual-quadrants',
        ]));

        $this->add_matrix_quadrant_editor(0, $quadrants[0], $vtop, $hleft, 'top-left');
        $this->add_matrix_quadrant_editor(1, $quadrants[1], $vtop, $hright, 'top-right');
        $this->add_matrix_quadrant_editor(2, $quadrants[2], $vbottom, $hleft, 'bottom-left');
        $this->add_matrix_quadrant_editor(3, $quadrants[3], $vbottom, $hright, 'bottom-right');

        $mform->addElement('html', html_writer::end_tag('div'));

        $mform->addElement('html', html_writer::start_tag('div', [
            'class' => 'personalityfinder-matrix-axis-overlay',
            'aria-hidden' => 'true',
        ]));
        $mform->addElement('html', html_writer::div(
            html_writer::span(s($hleft), 'personalityfinder-matrix-horizontal-left') .
            html_writer::span(s($hright), 'personalityfinder-matrix-horizontal-right'),
            'personalityfinder-matrix-horizontal-axis'
        ));
        $mform->addElement('html', html_writer::div(
            html_writer::span(s($vtop), 'personalityfinder-matrix-vertical-top') .
            html_writer::span(s($vbottom), 'personalityfinder-matrix-vertical-bottom'),
            'personalityfinder-matrix-vertical-axis'
        ));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('div'));

        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'savematrix', get_string('savematrix', 'mod_personalityfinder'), [
            'class' => 'btn btn-primary personalityfinder-builder-panel-save-button',
        ]);
        $mform->addGroup($buttonarray, 'matrix_actions', '', [' '], false);

        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('details'));
    }

    /**
     * Normalises the matrix quadrants for display in the builder.
     *
     * Quadrant order is fixed for the builder visual:
     * 0 = horizontal left + vertical top.
     * 1 = horizontal right + vertical top.
     * 2 = horizontal left + vertical bottom.
     * 3 = horizontal right + vertical bottom.
     *
     * @param array $quadrants Existing quadrants.
     * @return array Four quadrants.
     */
    private function normalise_quadrants_for_builder(array $quadrants): array {
        $defaults = [
            ['vertical_pole' => 'right', 'horizontal_pole' => 'left'],
            ['vertical_pole' => 'right', 'horizontal_pole' => 'right'],
            ['vertical_pole' => 'left', 'horizontal_pole' => 'left'],
            ['vertical_pole' => 'left', 'horizontal_pole' => 'right'],
        ];
        for ($i = 0; $i < 4; $i++) {
            if (empty($quadrants[$i]) || !is_array($quadrants[$i])) {
                $quadrants[$i] = [];
            }
            $quadrants[$i] = array_merge($defaults[$i], $quadrants[$i]);
            $quadrants[$i]['vertical_pole'] = $defaults[$i]['vertical_pole'];
            $quadrants[$i]['horizontal_pole'] = $defaults[$i]['horizontal_pole'];
            if (empty($quadrants[$i]['id'])) {
                $quadrants[$i]['id'] = 'quadrant_' . ($i + 1);
            }
        }
        return array_slice($quadrants, 0, 4);
    }

    /**
     * Adds one quadrant editor cell.
     *
     * @param int $index Quadrant index.
     * @param array $quadrant Quadrant data.
     * @param string $verticalpole Vertical pole label for this quadrant.
     * @param string $horizontalpole Horizontal pole label for this quadrant.
     * @param string $position CSS position class suffix.
     */
    private function add_matrix_quadrant_editor(
        int $index,
        array $quadrant,
        string $verticalpole,
        string $horizontalpole,
        string $position
    ): void {
        $mform = $this->_form;
        $title = $quadrant['label'] ?? get_string('quadrantnumber', 'mod_personalityfinder', $index + 1);
        $assignments = $quadrant['assignments'] ?? [];
        if (is_array($assignments)) {
            $assignments = implode("\n", $assignments);
        }
        $polelabel = $horizontalpole . ' | ' . $verticalpole;

        $mform->addElement('html', html_writer::start_tag('div', [
            'class' => 'personalityfinder-matrix-quadrant-details personalityfinder-matrix-quadrant-' . $position,
            'id' => 'personalityfinder-matrix-quadrant-' . $index,
        ]));
        $mform->addElement('html', html_writer::div(
            html_writer::span(s($polelabel), 'personalityfinder-builder-summary-text') .
            html_writer::span(s($title), 'personalityfinder-matrix-pole-pill'),
            'personalityfinder-matrix-quadrant-summary'
        ));
        $mform->addElement('html', html_writer::start_tag('div', ['class' => 'personalityfinder-matrix-quadrant-body']));

        $mform->addElement('hidden', 'matrix_quadrant_id_' . $index, clean_param($quadrant['id'] ?? 'quadrant_' . ($index + 1), PARAM_ALPHANUMEXT));
        $mform->setType('matrix_quadrant_id_' . $index, PARAM_ALPHANUMEXT);
        $mform->addElement('hidden', 'matrix_quadrant_vertical_' . $index, $quadrant['vertical_pole'] ?? ($index < 2 ? 'right' : 'left'));
        $mform->setType('matrix_quadrant_vertical_' . $index, PARAM_ALPHA);
        $mform->addElement('hidden', 'matrix_quadrant_horizontal_' . $index, $quadrant['horizontal_pole'] ?? ($index % 2 === 0 ? 'left' : 'right'));
        $mform->setType('matrix_quadrant_horizontal_' . $index, PARAM_ALPHA);

        $mform->addElement('html', html_writer::div(s($polelabel), 'personalityfinder-matrix-pole-heading'));

        $mform->addElement('text', 'matrix_quadrant_label_' . $index, get_string('quadrantlabel', 'mod_personalityfinder'), ['size' => 48]);
        $mform->setType('matrix_quadrant_label_' . $index, PARAM_TEXT);
        $mform->setDefault('matrix_quadrant_label_' . $index, $title);

        $mform->addElement('textarea', 'matrix_quadrant_summary_' . $index, get_string('quadrantsummary', 'mod_personalityfinder'), [
            'rows' => 3,
            'cols' => 60,
        ]);
        $mform->setType('matrix_quadrant_summary_' . $index, PARAM_TEXT);
        $mform->setDefault('matrix_quadrant_summary_' . $index, $quadrant['summary'] ?? '');

        $mform->addElement('textarea', 'matrix_quadrant_assignments_' . $index, get_string('quadrantassignments', 'mod_personalityfinder'), [
            'rows' => 3,
            'cols' => 60,
        ]);
        $mform->setType('matrix_quadrant_assignments_' . $index, PARAM_TEXT);
        $mform->setDefault('matrix_quadrant_assignments_' . $index, $assignments);
        $mform->addHelpButton('matrix_quadrant_assignments_' . $index, 'quadrantassignments', 'mod_personalityfinder');

        $mform->addElement('textarea', 'matrix_quadrant_prompt_' . $index, get_string('quadrantprompt', 'mod_personalityfinder'), [
            'rows' => 3,
            'cols' => 60,
        ]);
        $mform->setType('matrix_quadrant_prompt_' . $index, PARAM_TEXT);
        $mform->setDefault('matrix_quadrant_prompt_' . $index, $quadrant['prompt'] ?? '');

        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('div'));
    }

    /**
     * Adds the general personality dimensions builder.
     *
     * @param array $instrument Decoded instrument configuration.
     */
    private function add_general_dimensions_builder(array $instrument): void {
        $mform = $this->_form;
        $focus = array_values($instrument['focus_dimensions'] ?? []);
        $general = array_values($instrument['general_dimensions'] ?? []);
        $additional = array_values(array_filter($general, static function($dimension): bool {
            return empty($dimension['source']) || $dimension['source'] !== 'focus_dimension';
        }));
        $scale = max(2, min(8, (int)($instrument['settings']['general_dimension_scale_points'] ?? 8)));

        $mform->addElement('html', html_writer::start_tag('div', [
            'class' => 'personalityfinder-builder-status personalityfinder-general-dimensions-builder',
            'id' => 'personalityfinder-general-dimensions-builder',
        ]));

        $prompt = $instrument['general_dimensions_prompt'] ?? get_string('defaultgeneraldimensionsprompt', 'mod_personalityfinder');
        $mform->addElement('textarea', 'general_dimensions_prompt', get_string('generaldimensionsprompt', 'mod_personalityfinder'), [
            'rows' => 3,
            'cols' => 90,
        ]);
        $mform->setType('general_dimensions_prompt', PARAM_TEXT);
        $mform->setDefault('general_dimensions_prompt', $prompt);
        $mform->addHelpButton('general_dimensions_prompt', 'generaldimensionsprompt', 'mod_personalityfinder');

        $scaleoptions = [];
        for ($i = 2; $i <= 8; $i++) {
            $scaleoptions[$i] = $i;
        }
        $mform->addElement('select', 'general_scale_points', get_string('generalscalepoints', 'mod_personalityfinder'), $scaleoptions);
        $mform->setType('general_scale_points', PARAM_INT);
        $mform->setDefault('general_scale_points', $scale);
        $mform->addHelpButton('general_scale_points', 'generalscalepoints', 'mod_personalityfinder');
        $mform->addElement('html', html_writer::div($this->scale_preview_html($scale), 'personalityfinder-scale-preview personalityfinder-general-scale-preview'));

        $mform->addElement('html', html_writer::tag('h3', get_string('generaldimensionsbuilderheading', 'mod_personalityfinder'), [
            'class' => 'personalityfinder-builder-subsection-heading',
        ]));
        $mform->addElement('html', html_writer::tag('p', get_string('generaldimensionsbuilderintro', 'mod_personalityfinder'), [
            'class' => 'personalityfinder-builder-helptext',
        ]));

        foreach (array_slice(array_pad($focus, 2, []), 0, 2) as $index => $dimension) {
            $this->add_calculated_general_dimension_row($index, $dimension, $scale);
        }

        $mform->addElement('hidden', 'generaldimensionscount', count($additional));
        $mform->setType('generaldimensionscount', PARAM_INT);
        foreach ($additional as $index => $dimension) {
            $this->add_general_dimension_row($index, $dimension, $scale);
        }

        $addurl = new \moodle_url('/mod/personalityfinder/builder.php', [
            'id' => $this->_customdata['cmid'],
            'addgeneraldim' => 1,
            'activeaccordion' => 'generaldimensions',
            'sesskey' => sesskey(),
        ]);
        $mform->addElement('html', html_writer::div(html_writer::link(
            $addurl,
            '+ ' . get_string('addgeneraldimension', 'mod_personalityfinder'),
            ['class' => 'personalityfinder-builder-add-row-button']
        ), 'personalityfinder-builder-add-row', ['id' => 'personalityfinder-general-dimensions-add-row']));

        $mform->addElement('html', html_writer::div(get_string('calculateddimensionsnote', 'mod_personalityfinder'), 'alert alert-info personalityfinder-general-calculated-note'));

        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'savegeneraldimensions', get_string('savegeneraldimensions', 'mod_personalityfinder'), [
            'class' => 'btn btn-primary personalityfinder-builder-panel-save-button',
        ]);
        $mform->addGroup($buttonarray, 'general_dimensions_actions', '', [' '], false);

        $mform->addElement('html', html_writer::end_tag('div'));
    }

    /**
     * Adds a locked/calculated general dimension row from a focus dimension.
     *
     * @param int $index Dimension index.
     * @param array $dimension Focus dimension.
     * @param int $scale General scale points.
     */
    private function add_calculated_general_dimension_row(int $index, array $dimension, int $scale): void {
        $mform = $this->_form;
        $left = $dimension['left_label'] ?? get_string('leftpoleextreme', 'mod_personalityfinder');
        $right = $dimension['right_label'] ?? get_string('rightpoleextreme', 'mod_personalityfinder');
        $description = $dimension['description'] ?? '';
        $heading = $left . ' ↔ ' . $right;

        $mform->addElement('html', html_writer::start_tag('details', [
            'class' => 'personalityfinder-builder-mini-details personalityfinder-general-dimension-details personalityfinder-general-calculated-dimension',
            'id' => 'personalityfinder-general-calculated-' . $index,
            'open' => 'open',
        ]));
        $mform->addElement('html', html_writer::tag('summary',
            html_writer::span('🔒', 'personalityfinder-general-lock') .
            html_writer::span(s($heading), 'personalityfinder-builder-summary-text') .
            html_writer::span(get_string('calculateddimension', 'mod_personalityfinder'), 'personalityfinder-matrix-pole-pill'),
            ['class' => 'personalityfinder-builder-mini-summary']
        ));
        $mform->addElement('html', html_writer::start_tag('div', ['class' => 'personalityfinder-builder-row personalityfinder-builder-compact-row']));
        $mform->addElement('html', html_writer::tag('p', s($description), ['class' => 'personalityfinder-builder-helptext']));
        $mform->addElement('html', html_writer::div($this->general_dimension_scale_line($left, $right, $scale), 'personalityfinder-general-scale-line'));
        $mform->addElement('html', html_writer::div(get_string('calculateddimensionexplain', 'mod_personalityfinder'), 'personalityfinder-muted'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('details'));
    }

    /**
     * Adds an editable general dimension row.
     *
     * @param int $index Dimension index among additional dimensions.
     * @param array $dimension Dimension data.
     * @param int $scale General scale points.
     */
    private function add_general_dimension_row(int $index, array $dimension, int $scale): void {
        $mform = $this->_form;
        $left = $dimension['left_label'] ?? get_string('exampleleft', 'mod_personalityfinder');
        $right = $dimension['right_label'] ?? get_string('exampleright', 'mod_personalityfinder');
        $heading = $left . ' ↔ ' . $right;
        $id = clean_param($dimension['id'] ?? 'general_dimension_' . ($index + 1), PARAM_ALPHANUMEXT);

        $deleteurl = new \moodle_url('/mod/personalityfinder/builder.php', [
            'id' => $this->_customdata['cmid'],
            'deletegeneraldim' => $index,
            'activeaccordion' => 'generaldimensions',
            'sesskey' => sesskey(),
        ]);
        $deletebutton = html_writer::link($deleteurl, get_string('deleteattribute', 'mod_personalityfinder'), [
            'class' => 'personalityfinder-builder-bar-button personalityfinder-builder-delete-bar-button',
            'onclick' => 'return confirm(' . json_encode(get_string('deletegeneraldimensionconfirm', 'mod_personalityfinder')) . ');',
        ]);

        $mform->addElement('html', html_writer::start_tag('details', [
            'class' => 'personalityfinder-builder-mini-details personalityfinder-general-dimension-details',
            'id' => 'personalityfinder-general-dimension-' . $index,
        ]));
        $mform->addElement('html', html_writer::tag('summary',
            html_writer::span(s($heading), 'personalityfinder-builder-summary-text') . $deletebutton,
            ['class' => 'personalityfinder-builder-mini-summary']
        ));
        $mform->addElement('html', html_writer::start_tag('div', ['class' => 'personalityfinder-builder-row personalityfinder-builder-compact-row']));

        $mform->addElement('hidden', 'general_id_' . $index, $id);
        $mform->setType('general_id_' . $index, PARAM_ALPHANUMEXT);
        $mform->addElement('html', html_writer::start_tag('div', ['class' => 'personalityfinder-focus-grid']));
        $mform->addElement('text', 'general_left_label_' . $index, get_string('leftpoleextreme', 'mod_personalityfinder'), ['size' => 45]);
        $mform->setType('general_left_label_' . $index, PARAM_TEXT);
        $mform->setDefault('general_left_label_' . $index, $left);
        $mform->addElement('text', 'general_right_label_' . $index, get_string('rightpoleextreme', 'mod_personalityfinder'), ['size' => 45]);
        $mform->setType('general_right_label_' . $index, PARAM_TEXT);
        $mform->setDefault('general_right_label_' . $index, $right);
        $mform->addElement('html', html_writer::end_tag('div'));

        $mform->addElement('textarea', 'general_description_' . $index, get_string('dimensiondescription', 'mod_personalityfinder'), [
            'rows' => 2,
            'cols' => 90,
        ]);
        $mform->setType('general_description_' . $index, PARAM_TEXT);
        $mform->setDefault('general_description_' . $index, $dimension['description'] ?? '');
        $mform->addElement('advcheckbox', 'general_enabled_' . $index, get_string('attributeenabled', 'mod_personalityfinder'));
        $mform->setDefault('general_enabled_' . $index, !array_key_exists('enabled', $dimension) || !empty($dimension['enabled']) ? 1 : 0);
        $mform->addElement('html', html_writer::div($this->general_dimension_scale_line($left, $right, $scale), 'personalityfinder-general-scale-line'));

        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'savegeneraldimension_' . $index, get_string('savegeneraldimension', 'mod_personalityfinder'), [
            'class' => 'btn btn-primary personalityfinder-builder-panel-save-button',
        ]);
        $mform->addGroup($buttonarray, 'general_actions_' . $index, '', [' '], false);

        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('details'));
    }

    /**
     * Builds a simple general dimension scale preview line.
     *
     * @param string $left Left label.
     * @param string $right Right label.
     * @param int $scale Scale points.
     * @return string
     */
    private function general_dimension_scale_line(string $left, string $right, int $scale): string {
        return html_writer::div(s($left), 'personalityfinder-general-scale-anchor') .
            html_writer::div($this->scale_preview_html($scale), 'personalityfinder-general-scale-dots') .
            html_writer::div(s($right), 'personalityfinder-general-scale-anchor personalityfinder-general-scale-anchor-right');
    }

    /**
     * Builds static scale preview HTML.
     *
     * @param int $scale Scale points.
     * @return string
     */
    private function scale_preview_html(int $scale): string {
        $scale = max(2, min(8, $scale));
        $html = '';
        for ($i = 1; $i <= $scale; $i++) {
            $html .= html_writer::span('', 'personalityfinder-scale-dot', [
                'title' => get_string('scaleanchorposition', 'mod_personalityfinder', $i),
            ]);
        }
        return $html;
    }

    /**
     * Validates JSON before saving.
     *
     * @param array $data Submitted data.
     * @param array $files Submitted files.
     * @return array Errors.
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        if (!empty($data['loadexample'])) {
            return $errors;
        }
        try {
            config::normalise_json($data['configjson'] ?? '');
        } catch (\moodle_exception $exception) {
            $errors['configjson'] = $exception->getMessage();
        }
        return $errors;
    }
}
