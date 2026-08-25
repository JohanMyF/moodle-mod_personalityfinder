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
 * Builder page for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

use mod_personalityfinder\form\builder_form;
use mod_personalityfinder\local\config;

/**
 * Builds a safe machine id from a label.
 *
 * @param string $label Human label.
 * @param string $fallback Fallback id.
 * @return string
 */
function personalityfinder_builder_slug(string $label, string $fallback): string {
    $slug = strtolower(trim($label));
    $slug = preg_replace('/[^a-z0-9_]+/', '_', $slug);
    $slug = trim($slug, '_');
    if ($slug === '') {
        $slug = $fallback;
    }
    return clean_param($slug, PARAM_ALPHANUMEXT);
}

/**
 * Rebuilds the attributes array from the early visual builder fields.
 *
 * The rest of the instrument still comes from the JSON editor so we keep this
 * first visual-builder step conservative and low-risk.
 *
 * @param stdClass $data Submitted form data.
 * @return string Normalised JSON.
 */
function personalityfinder_builder_json_from_form(stdClass $data): string {
    $config = config::decode($data->configjson ?? '');
    $count = isset($data->attributescount) ? (int)$data->attributescount : 0;
    $attributes = [];
    $usedids = [];

    for ($index = 0; $index < $count; $index++) {
        $labelfield = 'attr_label_' . $index;
        $descriptionfield = 'attr_description_' . $index;
        $enabledfield = 'attr_enabled_' . $index;
        $idfield = 'attr_id_' . $index;

        $label = trim((string)($data->{$labelfield} ?? ''));
        if ($label === '') {
            continue;
        }

        $id = personalityfinder_builder_slug((string)($data->{$idfield} ?? ''), 'attribute_' . ($index + 1));
        if ($id === '' || isset($usedids[$id])) {
            $id = personalityfinder_builder_slug($label, 'attribute_' . ($index + 1));
        }
        $baseid = $id;
        $suffix = 2;
        while (isset($usedids[$id])) {
            $id = $baseid . '_' . $suffix;
            $suffix++;
        }
        $usedids[$id] = true;

        $attributes[] = [
            'id' => $id,
            'label' => $label,
            'description' => trim((string)($data->{$descriptionfield} ?? '')),
            'sortorder' => (count($attributes) + 1) * 10,
            'enabled' => !empty($data->{$enabledfield}),
        ];
    }

    $config['attribute_prompt'] = trim((string)($data->attribute_prompt ?? ''));
    $config['attributes'] = $attributes;
    $config['two_dimensions_prompt'] = trim((string)($data->two_dimensions_prompt ?? ''));
    $config['focus_dimensions'] = personalityfinder_builder_focus_dimensions_from_form($data, $config);
    $config['matrix'] = personalityfinder_builder_matrix_from_form($data, $config);
    $config['general_dimensions_prompt'] = trim((string)($data->general_dimensions_prompt ?? ''));
    if (!isset($config['settings']) || !is_array($config['settings'])) {
        $config['settings'] = [];
    }
    $config['settings']['focus_scale_points'] = max(2, min(8, (int)($data->focus_scale_points ?? 4)));
    $config['settings']['general_dimension_scale_points'] = max(2, min(8, (int)($data->general_scale_points ?? ($config['settings']['general_dimension_scale_points'] ?? 8))));
    $config['general_dimensions'] = personalityfinder_builder_general_dimensions_from_form($data, $config);
    personalityfinder_builder_sync_focus_general_dimensions($config);

    config::validate($config);
    return json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/**
 * Rebuilds the two focus dimensions from submitted builder fields.
 *
 * @param stdClass $data Submitted form data.
 * @param array $config Existing configuration.
 * @return array Focus dimensions.
 */
function personalityfinder_builder_focus_dimensions_from_form(stdClass $data, array $config): array {
    $dimensions = [];
    $existing = array_values($config['focus_dimensions'] ?? []);
    $scale = max(2, min(8, (int)($data->focus_scale_points ?? 4)));

    for ($dimensionindex = 0; $dimensionindex < 2; $dimensionindex++) {
        $leftfield = 'focus_left_label_' . $dimensionindex;
        $rightfield = 'focus_right_label_' . $dimensionindex;
        $descriptionfield = 'focus_description_' . $dimensionindex;
        $enabledfield = 'focus_enabled_' . $dimensionindex;
        $idfield = 'focus_id_' . $dimensionindex;
        $left = trim((string)($data->{$leftfield} ?? ''));
        $right = trim((string)($data->{$rightfield} ?? ''));
        if ($left === '') {
            $left = get_string('exampleleft', 'mod_personalityfinder');
        }
        if ($right === '') {
            $right = get_string('exampleright', 'mod_personalityfinder');
        }

        $fallbackid = 'dimension_' . ($dimensionindex + 1);
        $id = personalityfinder_builder_slug((string)($data->{$idfield} ?? ''), $fallbackid);
        if ($id === '') {
            $id = personalityfinder_builder_slug($left . '_' . $right, $fallbackid);
        }

        $itemcountfield = 'focus_items_count_' . $dimensionindex;
        $itemcount = isset($data->{$itemcountfield}) ? (int)$data->{$itemcountfield} : 0;
        $items = [];
        $useditemids = [];
        for ($itemindex = 0; $itemindex < $itemcount; $itemindex++) {
            $leftitemfield = 'focus_item_left_' . $dimensionindex . '_' . $itemindex;
            $rightitemfield = 'focus_item_right_' . $dimensionindex . '_' . $itemindex;
            $itemidfield = 'focus_item_id_' . $dimensionindex . '_' . $itemindex;
            $leftstatement = trim((string)($data->{$leftitemfield} ?? ''));
            $rightstatement = trim((string)($data->{$rightitemfield} ?? ''));
            if ($leftstatement === '' && $rightstatement === '') {
                continue;
            }
            $itemid = personalityfinder_builder_slug((string)($data->{$itemidfield} ?? ''), 'item_' . ($itemindex + 1));
            if ($itemid === '' || isset($useditemids[$itemid])) {
                $itemid = personalityfinder_builder_slug($leftstatement ?: $rightstatement, 'item_' . ($itemindex + 1));
            }
            $baseid = $itemid;
            $suffix = 2;
            while (isset($useditemids[$itemid])) {
                $itemid = $baseid . '_' . $suffix;
                $suffix++;
            }
            $useditemids[$itemid] = true;
            $items[] = [
                'id' => $itemid,
                'left_statement' => $leftstatement,
                'right_statement' => $rightstatement,
                'reverse_scored' => false,
                'sortorder' => (count($items) + 1) * 10,
            ];
        }

        if (empty($items)) {
            $items[] = [
                'id' => 'item_1',
                'left_statement' => get_string('defaultleftstatement', 'mod_personalityfinder'),
                'right_statement' => get_string('defaultrightstatement', 'mod_personalityfinder'),
                'reverse_scored' => false,
                'sortorder' => 10,
            ];
        }

        $dimensions[] = [
            'id' => $id,
            'left_label' => $left,
            'right_label' => $right,
            'description' => trim((string)($data->{$descriptionfield} ?? ($existing[$dimensionindex]['description'] ?? ''))),
            'enabled' => !empty($data->{$enabledfield}),
            'scale_points' => $scale,
            'items' => $items,
        ];
    }

    return $dimensions;
}

/**
 * Rebuilds the matrix interpretation configuration from submitted builder fields.
 *
 * @param stdClass $data Submitted form data.
 * @param array $config Existing configuration.
 * @return array Matrix configuration.
 */
function personalityfinder_builder_matrix_from_form(stdClass $data, array $config): array {
    $existing = $config['matrix'] ?? [];
    $focus = array_values($config['focus_dimensions'] ?? []);
    $horizontalid = $focus[0]['id'] ?? ($existing['horizontal_dimension_id'] ?? 'dimension_1');
    $verticalid = $focus[1]['id'] ?? ($existing['vertical_dimension_id'] ?? 'dimension_2');
    $count = isset($data->matrix_quadrants_count) ? (int)$data->matrix_quadrants_count : 4;
    $count = max(4, min(4, $count));
    $quadrants = [];
    $usedids = [];

    for ($index = 0; $index < $count; $index++) {
        $idfield = 'matrix_quadrant_id_' . $index;
        $labelfield = 'matrix_quadrant_label_' . $index;
        $summaryfield = 'matrix_quadrant_summary_' . $index;
        $assignmentsfield = 'matrix_quadrant_assignments_' . $index;
        $promptfield = 'matrix_quadrant_prompt_' . $index;
        $verticalfield = 'matrix_quadrant_vertical_' . $index;
        $horizontalfield = 'matrix_quadrant_horizontal_' . $index;

        $label = trim((string)($data->{$labelfield} ?? ''));
        if ($label === '') {
            $label = get_string('quadrantnumber', 'mod_personalityfinder', $index + 1);
        }
        $id = personalityfinder_builder_slug((string)($data->{$idfield} ?? ''), 'quadrant_' . ($index + 1));
        if ($id === '' || isset($usedids[$id])) {
            $id = personalityfinder_builder_slug($label, 'quadrant_' . ($index + 1));
        }
        $baseid = $id;
        $suffix = 2;
        while (isset($usedids[$id])) {
            $id = $baseid . '_' . $suffix;
            $suffix++;
        }
        $usedids[$id] = true;

        $assignmentsraw = trim((string)($data->{$assignmentsfield} ?? ''));
        $assignments = [];
        if ($assignmentsraw !== '') {
            foreach (preg_split('/\R/', $assignmentsraw) as $line) {
                $line = trim($line);
                if ($line !== '') {
                    $assignments[] = $line;
                }
            }
        }

        $quadrants[] = [
            'id' => $id,
            'vertical_pole' => clean_param((string)($data->{$verticalfield} ?? ($index < 2 ? 'right' : 'left')), PARAM_ALPHA),
            'horizontal_pole' => clean_param((string)($data->{$horizontalfield} ?? ($index % 2 === 0 ? 'left' : 'right')), PARAM_ALPHA),
            'label' => $label,
            'summary' => trim((string)($data->{$summaryfield} ?? '')),
            'assignments' => $assignments,
            'prompt' => trim((string)($data->{$promptfield} ?? '')),
        ];
    }

    return [
        'enabled' => !empty($data->matrix_enabled),
        'vertical_dimension_id' => $verticalid,
        'horizontal_dimension_id' => $horizontalid,
        'centre_zone' => (float)($existing['centre_zone'] ?? 0.15),
        'quadrants' => $quadrants,
    ];
}


/**
 * Rebuilds editable general dimensions from submitted builder fields.
 *
 * Focus-dimension mirror rows are added separately by the sync function.
 *
 * @param stdClass $data Submitted form data.
 * @param array $config Existing configuration.
 * @return array Editable general dimensions only.
 */
function personalityfinder_builder_general_dimensions_from_form(stdClass $data, array $config): array {
    $count = isset($data->generaldimensionscount) ? (int)$data->generaldimensionscount : 0;
    $scale = max(2, min(8, (int)($data->general_scale_points ?? ($config['settings']['general_dimension_scale_points'] ?? 8))));
    $dimensions = [];
    $usedids = [];

    for ($index = 0; $index < $count; $index++) {
        $leftfield = 'general_left_label_' . $index;
        $rightfield = 'general_right_label_' . $index;
        $descriptionfield = 'general_description_' . $index;
        $enabledfield = 'general_enabled_' . $index;
        $idfield = 'general_id_' . $index;

        $left = trim((string)($data->{$leftfield} ?? ''));
        $right = trim((string)($data->{$rightfield} ?? ''));
        if ($left === '' && $right === '') {
            continue;
        }
        if ($left === '') {
            $left = get_string('exampleleft', 'mod_personalityfinder');
        }
        if ($right === '') {
            $right = get_string('exampleright', 'mod_personalityfinder');
        }

        $id = personalityfinder_builder_slug((string)($data->{$idfield} ?? ''), 'general_dimension_' . ($index + 1));
        if ($id === '' || isset($usedids[$id])) {
            $id = personalityfinder_builder_slug($left . '_' . $right, 'general_dimension_' . ($index + 1));
        }
        $baseid = $id;
        $suffix = 2;
        while (isset($usedids[$id])) {
            $id = $baseid . '_' . $suffix;
            $suffix++;
        }
        $usedids[$id] = true;

        $dimensions[] = [
            'id' => $id,
            'left_label' => $left,
            'right_label' => $right,
            'scale_points' => $scale,
            'description' => trim((string)($data->{$descriptionfield} ?? '')),
            'enabled' => !empty($data->{$enabledfield}),
            'sortorder' => (count($dimensions) + 3) * 10,
        ];
    }

    return $dimensions;
}

/**
 * Keeps the general-dimension mirror entries aligned with the two focus dimensions.
 *
 * @param array $config Instrument configuration, passed by reference.
 */
function personalityfinder_builder_sync_focus_general_dimensions(array &$config): void {
    if (empty($config['general_dimensions']) || !is_array($config['general_dimensions'])) {
        $config['general_dimensions'] = [];
    }
    $additional = array_values(array_filter($config['general_dimensions'], static function($dimension): bool {
        return empty($dimension['source']) || $dimension['source'] !== 'focus_dimension';
    }));
    $generalscale = max(2, min(8, (int)($config['settings']['general_dimension_scale_points'] ?? 8)));
    $general = [];
    $usedids = [];
    foreach (array_values($config['focus_dimensions'] ?? []) as $index => $dimension) {
        $id = clean_param($dimension['id'] ?? ('focus_dimension_' . ($index + 1)), PARAM_ALPHANUMEXT);
        if ($id === '' || isset($usedids[$id])) {
            $id = 'focus_dimension_' . ($index + 1);
        }
        $usedids[$id] = true;
        $general[] = [
            'id' => $id,
            'source' => 'focus_dimension',
            'source_dimension_id' => $dimension['id'] ?? $id,
            'left_label' => $dimension['left_label'] ?? get_string('exampleleft', 'mod_personalityfinder'),
            'right_label' => $dimension['right_label'] ?? get_string('exampleright', 'mod_personalityfinder'),
            'scale_points' => $generalscale,
            'description' => $dimension['description'] ?? '',
            'allow_adjustment' => false,
            'sortorder' => (count($general) + 1) * 10,
        ];
    }

    foreach ($additional as $dimension) {
        $id = clean_param($dimension['id'] ?? 'general_dimension_' . (count($general) + 1), PARAM_ALPHANUMEXT);
        if ($id === '' || isset($usedids[$id])) {
            $baseid = personalityfinder_builder_slug(($dimension['left_label'] ?? '') . '_' . ($dimension['right_label'] ?? ''), 'general_dimension_' . (count($general) + 1));
            $id = $baseid;
            $suffix = 2;
            while (isset($usedids[$id])) {
                $id = $baseid . '_' . $suffix;
                $suffix++;
            }
        }
        $usedids[$id] = true;
        $dimension['id'] = $id;
        $dimension['scale_points'] = $generalscale;
        $dimension['sortorder'] = (count($general) + 1) * 10;
        $general[] = $dimension;
    }
    $config['general_dimensions'] = $general;
}


/**
 * Updates attributes directly from the stored JSON for simple link actions.
 *
 * @param string $json Current configuration JSON.
 * @param string $action Action name: add or delete.
 * @param int $index Attribute index for delete.
 * @return array Tuple of [json, focusattr].
 */
function personalityfinder_builder_json_link_action(string $json, string $action, int $index = -1): array {
    $config = config::decode($json ?: config::default_json());
    $attributes = array_values($config['attributes'] ?? []);

    if ($action === 'delete' && $index >= 0 && isset($attributes[$index])) {
        array_splice($attributes, $index, 1);
        $openattr = count($attributes) > 0 ? max(0, min($index, count($attributes) - 1)) : -1;
    } else if ($action === 'add') {
        $usedids = [];
        foreach ($attributes as $attribute) {
            if (!empty($attribute['id'])) {
                $usedids[$attribute['id']] = true;
            }
        }
        $newid = 'attribute_' . (count($attributes) + 1);
        $baseid = $newid;
        $suffix = 2;
        while (isset($usedids[$newid])) {
            $newid = $baseid . '_' . $suffix;
            $suffix++;
        }
        $attributes[] = [
            'id' => $newid,
            'label' => get_string('newattribute', 'mod_personalityfinder'),
            'description' => '',
            'sortorder' => (count($attributes) + 1) * 10,
            'enabled' => true,
        ];
        $openattr = count($attributes) - 1;
    } else {
        $openattr = -1;
    }

    foreach ($attributes as $sortindex => &$attribute) {
        $attribute['sortorder'] = ($sortindex + 1) * 10;
    }
    unset($attribute);

    $config['attributes'] = $attributes;
    config::validate($config);
    return [json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $openattr];
}


/**
 * Updates editable general dimensions directly from the stored JSON for simple link actions.
 *
 * @param string $json Current configuration JSON.
 * @param string $action Action name: add or delete.
 * @param int $index General dimension index among editable dimensions for delete.
 * @return array Tuple of [json, focusindex].
 */
function personalityfinder_builder_general_dimension_link_action(string $json, string $action, int $index = -1): array {
    $config = config::decode($json ?: config::default_json());
    $additional = array_values(array_filter($config['general_dimensions'] ?? [], static function($dimension): bool {
        return empty($dimension['source']) || $dimension['source'] !== 'focus_dimension';
    }));
    $target = -1;

    if ($action === 'delete' && $index >= 0 && isset($additional[$index])) {
        array_splice($additional, $index, 1);
        $target = count($additional) > 0 ? max(0, min($index, count($additional) - 1)) : -1;
    } else if ($action === 'add') {
        $usedids = [];
        foreach ($additional as $dimension) {
            if (!empty($dimension['id'])) {
                $usedids[$dimension['id']] = true;
            }
        }
        $newid = 'general_dimension_' . (count($additional) + 1);
        $baseid = $newid;
        $suffix = 2;
        while (isset($usedids[$newid])) {
            $newid = $baseid . '_' . $suffix;
            $suffix++;
        }
        $scale = max(2, min(8, (int)($config['settings']['general_dimension_scale_points'] ?? 8)));
        $additional[] = [
            'id' => $newid,
            'left_label' => get_string('newgeneralleftlabel', 'mod_personalityfinder'),
            'right_label' => get_string('newgeneralrightlabel', 'mod_personalityfinder'),
            'description' => '',
            'scale_points' => $scale,
            'enabled' => true,
            'sortorder' => (count($additional) + 3) * 10,
        ];
        $target = count($additional) - 1;
    }

    $config['general_dimensions'] = $additional;
    personalityfinder_builder_sync_focus_general_dimensions($config);
    config::validate($config);
    return [json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $target];
}

/**
 * Updates semantic differentials directly from the stored JSON for simple link actions.
 *
 * @param string $json Current configuration JSON.
 * @param string $action Action name: add or delete.
 * @param int $dimensionindex Focus dimension index.
 * @param int $itemindex Item index for delete.
 * @return array Tuple of [json, targetitemindex].
 */
function personalityfinder_builder_focus_item_link_action(string $json, string $action, int $dimensionindex, int $itemindex = -1): array {
    $config = config::decode($json ?: config::default_json());
    $dimensions = array_values($config['focus_dimensions'] ?? []);
    if (!isset($dimensions[$dimensionindex])) {
        return [$json, -1];
    }
    $items = array_values($dimensions[$dimensionindex]['items'] ?? []);
    $targetitem = -1;

    if ($action === 'delete' && $itemindex >= 0 && isset($items[$itemindex])) {
        array_splice($items, $itemindex, 1);
        $targetitem = count($items) > 0 ? max(0, min($itemindex, count($items) - 1)) : -1;
    } else if ($action === 'add') {
        $usedids = [];
        foreach ($items as $item) {
            if (!empty($item['id'])) {
                $usedids[$item['id']] = true;
            }
        }
        $newid = 'item_' . (count($items) + 1);
        $baseid = $newid;
        $suffix = 2;
        while (isset($usedids[$newid])) {
            $newid = $baseid . '_' . $suffix;
            $suffix++;
        }
        $items[] = [
            'id' => $newid,
            'left_statement' => get_string('newleftstatement', 'mod_personalityfinder'),
            'right_statement' => get_string('newrightstatement', 'mod_personalityfinder'),
            'reverse_scored' => false,
            'sortorder' => (count($items) + 1) * 10,
        ];
        $targetitem = count($items) - 1;
    }

    if (empty($items)) {
        $items[] = [
            'id' => 'item_1',
            'left_statement' => get_string('defaultleftstatement', 'mod_personalityfinder'),
            'right_statement' => get_string('defaultrightstatement', 'mod_personalityfinder'),
            'reverse_scored' => false,
            'sortorder' => 10,
        ];
    }

    foreach ($items as $sortindex => &$item) {
        $item['sortorder'] = ($sortindex + 1) * 10;
    }
    unset($item);

    $dimensions[$dimensionindex]['items'] = $items;
    $config['focus_dimensions'] = $dimensions;
    config::validate($config);
    return [json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $targetitem];
}


$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('personalityfinder', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$personalityfinder = $DB->get_record('personalityfinder', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/personalityfinder:manage', $context);

$PAGE->set_url('/mod/personalityfinder/builder.php', ['id' => $cm->id]);
$PAGE->set_title(get_string('builderheading', 'mod_personalityfinder'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->add_body_class('personalityfinder-builder-page');

$activeaccordion = optional_param('activeaccordion', '', PARAM_ALPHAEXT);
$openattr = optional_param('openattr', -1, PARAM_INT);
$addattr = optional_param('addattr', 0, PARAM_BOOL);
$deleteattr = optional_param('deleteattr', -1, PARAM_INT);
$addfocusitem = optional_param('addfocusitem', -1, PARAM_INT);
$deletefocusdim = optional_param('deletefocusdim', -1, PARAM_INT);
$deletefocusitem = optional_param('deletefocusitem', -1, PARAM_INT);
$addgeneraldim = optional_param('addgeneraldim', 0, PARAM_BOOL);
$deletegeneraldim = optional_param('deletegeneraldim', -1, PARAM_INT);
$exportjson = optional_param('exportjson', 0, PARAM_BOOL);

if ($exportjson) {
    require_sesskey();
    $filename = clean_filename('personalityfinder-' . $personalityfinder->name . '.json');
    $json = $personalityfinder->configjson ?: config::default_json();
    \core\session\manager::write_close();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $json;
    die;
}

if ($addattr || $deleteattr >= 0) {
    require_sesskey();
    if ($addattr) {
        [$personalityfinder->configjson, $openattr] = personalityfinder_builder_json_link_action($personalityfinder->configjson, 'add');
    } else {
        [$personalityfinder->configjson, $openattr] = personalityfinder_builder_json_link_action($personalityfinder->configjson, 'delete', $deleteattr);
    }
    $personalityfinder->timemodified = time();
    $DB->update_record('personalityfinder', $personalityfinder);
    $redirecturl = new moodle_url('/mod/personalityfinder/builder.php', [
        'id' => $cm->id,
        'activeaccordion' => 'attributes',
    ]);
    if ($addattr && $openattr >= 0) {
        $redirecturl->param('openattr', $openattr);
    }
    if ($openattr >= 0) {
        $redirecturl->set_anchor('personalityfinder-attribute-' . $openattr);
    } else {
        $redirecturl->set_anchor('personalityfinder-attributes-add-row');
    }
    redirect($redirecturl, get_string('configjsonsaved', 'mod_personalityfinder'), null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($addgeneraldim || $deletegeneraldim >= 0) {
    require_sesskey();
    if ($addgeneraldim) {
        [$personalityfinder->configjson, $targetgeneral] = personalityfinder_builder_general_dimension_link_action($personalityfinder->configjson, 'add');
    } else {
        [$personalityfinder->configjson, $targetgeneral] = personalityfinder_builder_general_dimension_link_action($personalityfinder->configjson, 'delete', $deletegeneraldim);
    }
    $personalityfinder->timemodified = time();
    $DB->update_record('personalityfinder', $personalityfinder);
    $redirecturl = new moodle_url('/mod/personalityfinder/builder.php', [
        'id' => $cm->id,
        'activeaccordion' => 'generaldimensions',
    ]);
    if ($targetgeneral >= 0) {
        $redirecturl->set_anchor('personalityfinder-general-dimension-' . $targetgeneral);
    } else {
        $redirecturl->set_anchor('personalityfinder-general-dimensions-add-row');
    }
    redirect($redirecturl, get_string('configjsonsaved', 'mod_personalityfinder'), null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($addfocusitem >= 0 || ($deletefocusdim >= 0 && $deletefocusitem >= 0)) {
    require_sesskey();
    if ($addfocusitem >= 0) {
        [$personalityfinder->configjson, $targetitem] = personalityfinder_builder_focus_item_link_action($personalityfinder->configjson, 'add', $addfocusitem);
        $focusanchor = $addfocusitem;
    } else {
        [$personalityfinder->configjson, $targetitem] = personalityfinder_builder_focus_item_link_action($personalityfinder->configjson, 'delete', $deletefocusdim, $deletefocusitem);
        $focusanchor = $deletefocusdim;
    }
    $personalityfinder->timemodified = time();
    $DB->update_record('personalityfinder', $personalityfinder);
    $redirecturl = new moodle_url('/mod/personalityfinder/builder.php', [
        'id' => $cm->id,
        'activeaccordion' => 'twodimensions',
    ]);
    if (isset($targetitem) && $targetitem >= 0) {
        $redirecturl->set_anchor('personalityfinder-focus-' . max(0, min(1, $focusanchor)) . '-item-' . $targetitem);
    } else {
        $redirecturl->set_anchor('personalityfinder-focus-dimension-' . max(0, min(1, $focusanchor)));
    }
    redirect($redirecturl, get_string('configjsonsaved', 'mod_personalityfinder'), null, \core\output\notification::NOTIFY_SUCCESS);
}

$form = new builder_form(null, [
    'cmid' => $cm->id,
    'configjson' => $personalityfinder->configjson ?: config::default_json(),
    'openattr' => $openattr,
    'activeaccordion' => $activeaccordion,
]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/personalityfinder/view.php', ['id' => $cm->id]));
}

if ($data = $form->get_data()) {
    if (!empty($data->loadexample)) {
        $form = new builder_form(null, [
            'cmid' => $cm->id,
            'configjson' => config::default_json(),
            'openattr' => -1,
            'activeaccordion' => '',
        ]);
    } else if (!empty($data->importjsonbutton)) {
        $uploadedjson = $form->get_file_content('importjsonfile');
        if ($uploadedjson === false || trim((string)$uploadedjson) === '') {
            redirect(new moodle_url('/mod/personalityfinder/builder.php', [
                'id' => $cm->id,
                'activeaccordion' => 'json',
            ], 'personalityfinder-import-json'), get_string('importjsonmissing', 'mod_personalityfinder'), null, \core\output\notification::NOTIFY_ERROR);
        }
        try {
            $personalityfinder->configjson = config::normalise_json($uploadedjson);
        } catch (\moodle_exception $exception) {
            redirect(new moodle_url('/mod/personalityfinder/builder.php', [
                'id' => $cm->id,
                'activeaccordion' => 'json',
            ], 'personalityfinder-import-json'), get_string('configjsoninvalid', 'mod_personalityfinder', $exception->getMessage()), null, \core\output\notification::NOTIFY_ERROR);
        }
        $personalityfinder->timemodified = time();
        $DB->update_record('personalityfinder', $personalityfinder);
        redirect(new moodle_url('/mod/personalityfinder/builder.php', [
            'id' => $cm->id,
            'activeaccordion' => 'json',
        ], 'personalityfinder-import-json'), get_string('importjsonsaved', 'mod_personalityfinder'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else if (!empty($data->validatejsonbutton)) {
        config::normalise_json($data->configjson ?? '');
        redirect(new moodle_url('/mod/personalityfinder/builder.php', [
            'id' => $cm->id,
            'activeaccordion' => 'json',
        ], 'personalityfinder-config-json'), get_string('validatejsonsuccess', 'mod_personalityfinder'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        $personalityfinder->configjson = personalityfinder_builder_json_from_form($data);
        $personalityfinder->timemodified = time();
        $DB->update_record('personalityfinder', $personalityfinder);

        $anchor = 'personalityfinder-builder-actions';
        $redirectactiveaccordion = '';
        $count = isset($data->attributescount) ? (int)$data->attributescount : 0;
        for ($index = 0; $index < $count; $index++) {
            $savebutton = 'saveattribute_' . $index;
            if (!empty($data->{$savebutton})) {
                $anchor = 'personalityfinder-attribute-' . $index;
                $redirectactiveaccordion = 'attributes';
                break;
            }
        }
        for ($dimensionindex = 0; $dimensionindex < 2; $dimensionindex++) {
            $savebutton = 'savefocusdimension_' . $dimensionindex;
            if (!empty($data->{$savebutton})) {
                $anchor = 'personalityfinder-focus-dimension-' . $dimensionindex;
                $redirectactiveaccordion = 'twodimensions';
                break;
            }
        }
        if (!empty($data->savematrix)) {
            $anchor = 'personalityfinder-matrix-interpretation';
            $redirectactiveaccordion = 'twodimensions';
        }
        $generalcount = isset($data->generaldimensionscount) ? (int)$data->generaldimensionscount : 0;
        for ($index = 0; $index < $generalcount; $index++) {
            $savebutton = 'savegeneraldimension_' . $index;
            if (!empty($data->{$savebutton})) {
                $anchor = 'personalityfinder-general-dimension-' . $index;
                $redirectactiveaccordion = 'generaldimensions';
                break;
            }
        }
        if (!empty($data->savegeneraldimensions)) {
            $anchor = 'personalityfinder-general-dimensions-builder';
            $redirectactiveaccordion = 'generaldimensions';
        }

        $redirectparams = ['id' => $cm->id];
        if ($redirectactiveaccordion !== '') {
            $redirectparams['activeaccordion'] = $redirectactiveaccordion;
        }
        $redirecturl = new moodle_url('/mod/personalityfinder/builder.php', $redirectparams);
        $redirecturl->set_anchor($anchor);
        redirect($redirecturl, get_string('configjsonsaved', 'mod_personalityfinder'), null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($personalityfinder->name));
echo html_writer::div(get_string('builderintro', 'mod_personalityfinder'), 'personalityfinder-builder-intro');
echo html_writer::link(new moodle_url('/mod/personalityfinder/view.php', ['id' => $cm->id]), get_string('viewactivity', 'mod_personalityfinder'), ['class' => 'btn btn-secondary mb-3']);
$form->display();
echo $OUTPUT->footer();
