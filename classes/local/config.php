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

namespace mod_personalityfinder\local;

/**
 * JSON configuration helper for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config {
    /** @var string Expected JSON schema identifier. */
    public const SCHEMA = 'mod_personalityfinder';

    /**
     * Returns the default example instrument as an array.
     *
     * @return array
     */
    public static function default_instrument(): array {
        return [
            'schema' => self::SCHEMA,
            'schema_version' => '1.0',
            'instrument' => [
                'title' => get_string('defaultinstrument', 'mod_personalityfinder'),
                'description' => get_string('activityintro', 'mod_personalityfinder'),
                'disclaimer' => get_string('disclaimer', 'mod_personalityfinder'),
            ],
            'settings' => [
                'attribute_mode' => 'self_and_others_one_pass',
                'show_matrix' => true,
                'allow_focus_adjustment_in_general_dimensions' => true,
                'store_calculated_and_adjusted_values' => true,
                'general_dimension_scale_points' => 8,
                'focus_scale_points' => 4,
            ],
            'attribute_prompt' => get_string('defaultattributeprompt', 'mod_personalityfinder'),
            'two_dimensions_prompt' => get_string('defaulttwodimensionsprompt', 'mod_personalityfinder'),
            'general_dimensions_prompt' => get_string('defaultgeneraldimensionsprompt', 'mod_personalityfinder'),
            'attributes' => [
                ['id' => 'courageous', 'label' => 'Courageous', 'description' => 'Willing to act with bravery when something important is at stake.', 'sortorder' => 10, 'enabled' => true],
                ['id' => 'steadfast', 'label' => 'Steadfast', 'description' => 'Reliable, loyal, and consistent over time.', 'sortorder' => 20, 'enabled' => true],
                ['id' => 'ordered', 'label' => 'Ordered', 'description' => 'Prefers clarity, structure, and organised processes.', 'sortorder' => 30, 'enabled' => true],
                ['id' => 'bigpicture', 'label' => 'Big-picture', 'description' => 'Naturally notices larger patterns, possibilities, and direction.', 'sortorder' => 40, 'enabled' => true],
                ['id' => 'talkative', 'label' => 'Talkative', 'description' => 'Often expresses thoughts and energy through conversation.', 'sortorder' => 50, 'enabled' => true],
            ],
            'focus_dimensions' => [
                [
                    'id' => 'task_people',
                    'left_label' => 'Task-oriented',
                    'right_label' => 'People-oriented',
                    'description' => 'Reflects whether the respondent tends to focus first on completion or relational experience.',
                    'scale_points' => 4,
                    'items' => [
                        ['id' => 'tp1', 'left_statement' => 'I notice first what needs to be done.', 'right_statement' => 'I notice first how people are feeling.', 'reverse_scored' => false, 'sortorder' => 10],
                        ['id' => 'tp2', 'left_statement' => 'I feel satisfied when a task is completed well.', 'right_statement' => 'I feel satisfied when people feel included and encouraged.', 'reverse_scored' => false, 'sortorder' => 20],
                        ['id' => 'tp3', 'left_statement' => 'I usually ask, “What needs to happen next?”', 'right_statement' => 'I usually ask, “Who needs support or encouragement?”', 'reverse_scored' => false, 'sortorder' => 30],
                    ],
                ],
                [
                    'id' => 'structured_unstructured',
                    'left_label' => 'Unstructured',
                    'right_label' => 'Structured',
                    'description' => 'Reflects whether the respondent tends to prefer flexible response or clear structure.',
                    'scale_points' => 4,
                    'items' => [
                        ['id' => 'su1', 'left_statement' => 'I prefer to begin and adjust as I go.', 'right_statement' => 'I prefer to plan before I begin.', 'reverse_scored' => false, 'sortorder' => 10],
                        ['id' => 'su2', 'left_statement' => 'I feel more comfortable when there is room to improvise.', 'right_statement' => 'I feel more comfortable when expectations are clear.', 'reverse_scored' => false, 'sortorder' => 20],
                        ['id' => 'su3', 'left_statement' => 'I usually ask, “What is possible?”', 'right_statement' => 'I usually ask, “What is the plan?”', 'reverse_scored' => false, 'sortorder' => 30],
                    ],
                ],
            ],
            'general_dimensions' => [
                ['id' => 'introvert_extrovert', 'left_label' => 'Introvert', 'right_label' => 'Extrovert', 'scale_points' => 8, 'description' => 'Reflects where the respondent tends to draw energy.', 'sortorder' => 10],
                ['id' => 'planned_spontaneous', 'left_label' => 'Planned / Controlled', 'right_label' => 'Spontaneous / Flexible', 'scale_points' => 8, 'description' => 'Reflects how the respondent relates to planning, order, and flexibility.', 'sortorder' => 20],
                ['id' => 'task_people', 'source' => 'focus_dimension', 'source_dimension_id' => 'task_people', 'left_label' => 'Task-oriented', 'right_label' => 'People-oriented', 'scale_points' => 8, 'description' => 'Pre-populated from the earlier focus dimension.', 'allow_adjustment' => true, 'sortorder' => 30],
                ['id' => 'structured_unstructured', 'source' => 'focus_dimension', 'source_dimension_id' => 'structured_unstructured', 'left_label' => 'Unstructured', 'right_label' => 'Structured', 'scale_points' => 8, 'description' => 'Pre-populated from the earlier focus dimension.', 'allow_adjustment' => true, 'sortorder' => 40],
            ],
            'matrix' => [
                'enabled' => true,
                'vertical_dimension_id' => 'structured_unstructured',
                'horizontal_dimension_id' => 'task_people',
                'centre_zone' => 0.15,
                'quadrants' => [
                    ['id' => 'organiser_implementer', 'vertical_pole' => 'right', 'horizontal_pole' => 'left', 'label' => 'Organiser / Implementer', 'summary' => 'You may naturally bring order, clarity, responsibility, and follow-through.', 'prompt' => 'How can you use your love of order and completion to serve people well?'],
                    ['id' => 'shepherd_facilitator', 'vertical_pole' => 'right', 'horizontal_pole' => 'right', 'label' => 'Shepherd / Facilitator', 'summary' => 'You may naturally bring care, consistency, relational responsibility, and thoughtful facilitation.', 'prompt' => 'How can you care for people faithfully while also keeping healthy boundaries and helping the group move forward?'],
                    ['id' => 'starter_problem_solver', 'vertical_pole' => 'left', 'horizontal_pole' => 'left', 'label' => 'Starter / Problem-solver', 'summary' => 'You may naturally bring energy, initiative, adaptability, and practical problem-solving.', 'prompt' => 'How can you turn your energy for action into something that others can understand, join, and continue?'],
                    ['id' => 'connector_encourager', 'vertical_pole' => 'left', 'horizontal_pole' => 'right', 'label' => 'Connector / Encourager', 'summary' => 'You may naturally bring warmth, flexibility, encouragement, and relational energy.', 'prompt' => 'How can you use your relational warmth in ways that are sustainable and dependable?'],
                ],
            ],
            'reflection_prompts' => [
                'Which part of your result feels most accurate?',
                'Which part surprised you?',
                'Where have others seen these qualities in you?',
                'Which settings tend to energise you?',
                'Which settings tend to drain you?',
                'What kind of team helps you flourish?',
            ],
        ];
    }

    /**
     * Returns the default instrument JSON, pretty printed.
     *
     * @return string
     */
    public static function default_json(): string {
        return json_encode(self::default_instrument(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Decodes and validates a JSON instrument.
     *
     * @param string|null $json Raw JSON.
     * @return array Decoded configuration.
     * @throws \moodle_exception When the JSON is invalid.
     */
    public static function decode(?string $json): array {
        $json = trim((string)$json);
        if ($json === '') {
            return self::default_instrument();
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', json_last_error_msg());
        }
        if (!is_array($data)) {
            throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'The top-level JSON value must be an object.');
        }
        self::validate($data);
        return $data;
    }

    /**
     * Returns pretty-printed JSON after validation.
     *
     * @param string|null $json Raw JSON.
     * @return string
     */
    public static function normalise_json(?string $json): string {
        return json_encode(self::decode($json), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Validates the core shape of a configuration.
     *
     * @param array $data Configuration data.
     * @throws \moodle_exception When invalid.
     */
    public static function validate(array $data): void {
        if (($data['schema'] ?? '') !== self::SCHEMA) {
            throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'The schema must be mod_personalityfinder.');
        }
        if (empty($data['instrument']) || !is_array($data['instrument'])) {
            throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'The instrument object is required.');
        }
        if (!isset($data['attributes']) || !is_array($data['attributes'])) {
            throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'The attributes array is required.');
        }
        if (!isset($data['focus_dimensions']) || !is_array($data['focus_dimensions']) || count($data['focus_dimensions']) !== 2) {
            throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'Exactly two focus dimensions are required.');
        }
        if (!isset($data['general_dimensions']) || !is_array($data['general_dimensions'])) {
            throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'The general_dimensions array is required.');
        }
        self::require_unique_ids($data['attributes'], 'attributes');
        self::require_unique_ids($data['focus_dimensions'], 'focus_dimensions');
        self::require_unique_ids($data['general_dimensions'], 'general_dimensions');

        foreach ($data['focus_dimensions'] as $dimension) {
            if (empty($dimension['id']) || empty($dimension['left_label']) || empty($dimension['right_label'])) {
                throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'Each focus dimension requires id, left_label, and right_label.');
            }
            if (empty($dimension['items']) || !is_array($dimension['items'])) {
                throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'Each focus dimension requires at least one item.');
            }
            self::require_scale_points($dimension['scale_points'] ?? 4);
            self::require_unique_ids($dimension['items'], 'focus dimension items');
        }

        foreach ($data['general_dimensions'] as $dimension) {
            if (empty($dimension['id']) || empty($dimension['left_label']) || empty($dimension['right_label'])) {
                throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'Each general dimension requires id, left_label, and right_label.');
            }
            self::require_scale_points($dimension['scale_points'] ?? ($data['settings']['general_dimension_scale_points'] ?? 8));
        }
    }

    /**
     * Checks for duplicate IDs.
     *
     * @param array $items Items with IDs.
     * @param string $label Human-readable label.
     * @throws \moodle_exception When invalid.
     */
    private static function require_unique_ids(array $items, string $label): void {
        $seen = [];
        foreach ($items as $item) {
            if (!is_array($item) || empty($item['id'])) {
                throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'Every item in ' . $label . ' requires an id.');
            }
            $id = (string)$item['id'];
            if (isset($seen[$id])) {
                throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'Duplicate id in ' . $label . ': ' . $id);
            }
            $seen[$id] = true;
        }
    }

    /**
     * Checks scale point setting.
     *
     * @param mixed $points Scale points.
     * @throws \moodle_exception When invalid.
     */
    private static function require_scale_points($points): void {
        $points = (int)$points;
        if ($points < 2 || $points > 8) {
            throw new \moodle_exception('configjsoninvalid', 'mod_personalityfinder', '', 'Scale points must be between 2 and 8.');
        }
    }

    /**
     * Builds a lightweight summary for templates.
     *
     * @param array $config Configuration array.
     * @return array
     */
    public static function summary_for_template(array $config): array {
        $instrument = $config['instrument'] ?? [];
        $matrix = $config['matrix'] ?? [];
        return [
            'title' => $instrument['title'] ?? get_string('unknown', 'mod_personalityfinder'),
            'description' => $instrument['description'] ?? '',
            'disclaimer' => $instrument['disclaimer'] ?? get_string('disclaimer', 'mod_personalityfinder'),
            'attributescount' => count($config['attributes'] ?? []),
            'focusdimensions' => array_values(array_map([self::class, 'dimension_for_template'], $config['focus_dimensions'] ?? [])),
            'generaldimensions' => array_values(array_map([self::class, 'dimension_for_template'], $config['general_dimensions'] ?? [])),
            'matrixenabled' => !empty($matrix['enabled']),
            'quadrants' => array_values(array_map([self::class, 'quadrant_for_template'], $matrix['quadrants'] ?? [])),
            'promptscount' => count($config['reflection_prompts'] ?? []),
        ];
    }

    /**
     * Prepares a dimension summary for templates.
     *
     * @param array $dimension Dimension data.
     * @return array
     */
    private static function dimension_for_template(array $dimension): array {
        return [
            'id' => $dimension['id'] ?? '',
            'left_label' => $dimension['left_label'] ?? '',
            'right_label' => $dimension['right_label'] ?? '',
            'description' => $dimension['description'] ?? '',
            'scale_points' => $dimension['scale_points'] ?? '',
            'itemcount' => isset($dimension['items']) && is_array($dimension['items']) ? count($dimension['items']) : 0,
        ];
    }

    /**
     * Prepares a quadrant summary for templates.
     *
     * @param array $quadrant Quadrant data.
     * @return array
     */
    private static function quadrant_for_template(array $quadrant): array {
        return [
            'label' => $quadrant['label'] ?? '',
            'summary' => $quadrant['summary'] ?? '',
            'prompt' => $quadrant['prompt'] ?? '',
        ];
    }
}
