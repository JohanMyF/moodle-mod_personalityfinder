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

use mod_personalityfinder\form\response_form;

/**
 * Result calculation helper for PersonalityFinder respondent submissions.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class results {
    /**
     * Calculates results from submitted data.
     *
     * @param array $config Instrument configuration.
     * @param object $data Submitted form data.
     * @return array Results.
     */
    public static function calculate(array $config, object $data): array {
        $raw = (array)$data;
        $results = [
            'attributes' => self::calculate_attributes($config, $raw),
            'focus_dimensions' => self::calculate_focus_dimensions($config, $raw),
            'general_dimensions' => [],
            'matrix' => [],
        ];
        $results['general_dimensions'] = self::calculate_general_dimensions($config, $raw, $results['focus_dimensions']);
        $results['matrix'] = self::calculate_matrix($config, $results['focus_dimensions']);
        return $results;
    }

    /**
     * Calculates attribute selections.
     *
     * @param array $config Instrument configuration.
     * @param array $raw Submitted raw data.
     * @return array Attribute results.
     */
    protected static function calculate_attributes(array $config, array $raw): array {
        $self = [];
        $others = [];
        $cards = [];
        $notapplicable = [];
        $attributes = array_values($config['attributes'] ?? []);
        $attributesafes = response_form::unique_safe_names($attributes, 'attribute');
        foreach ($attributes as $attributeindex => $attribute) {
            if (empty($attribute['enabled'])) {
                continue;
            }
            $id = $attributesafes[$attributeindex] ?? response_form::safe_name((string)($attribute['id'] ?? $attribute['label'] ?? 'attribute'));
            $label = (string)($attribute['label'] ?? $id);
            $description = (string)($attribute['description'] ?? '');
            $isna = !empty($raw['attr_na_' . $id]);
            $isself = !$isna && !empty($raw['attr_self_' . $id]);
            $isothers = !$isna && !empty($raw['attr_others_' . $id]);

            $item = [
                'id' => $id,
                'label' => $label,
                'description' => $description,
                'self' => $isself,
                'others' => $isothers,
                'shared' => $isself && $isothers,
                'selfonly' => $isself && !$isothers,
                'othersonly' => !$isself && $isothers,
                'notapplicable' => $isna,
            ];
            if ($isself) {
                $self[] = $item;
            }
            if ($isothers) {
                $others[] = $item;
            }
            // Report cards are shown only for attributes the respondent selected
            // for themselves. The optional 'others may see this' reflection is
            // displayed as supporting context on that selected attribute card.
            if ($isself) {
                $cards[] = $item;
            }
            if ($isna) {
                $notapplicable[] = $item;
            }
        }
        return ['self' => $self, 'others' => $others, 'cards' => $cards, 'notapplicable' => $notapplicable];
    }

    /**
     * Calculates focus dimension scores.
     *
     * @param array $config Instrument configuration.
     * @param array $raw Submitted raw data.
     * @return array Focus dimension results keyed by dimension ID.
     */
    protected static function calculate_focus_dimensions(array $config, array $raw): array {
        $results = [];
        $dimensions = array_values($config['focus_dimensions'] ?? []);
        $dimensionsafes = response_form::unique_safe_names($dimensions, 'dimension');
        foreach ($dimensions as $dimensionindex => $dimension) {
            $dimensionid = (string)($dimension['id'] ?? 'dimension');
            $safeid = $dimensionsafes[$dimensionindex] ?? response_form::safe_name($dimensionid);
            $scalepoints = max(2, min(8, (int)($dimension['scale_points'] ?? ($config['settings']['focus_scale_points'] ?? 4))));
            $values = [];
            $items = array_values($dimension['items'] ?? []);
            $itemsafes = response_form::unique_safe_names($items, 'item');
            foreach ($items as $itemindex => $item) {
                $itemid = $itemsafes[$itemindex] ?? response_form::safe_name((string)($item['id'] ?? 'item'));
                $field = 'focus_' . $safeid . '_' . $itemid;
                if (!empty($raw[$field])) {
                    $value = max(1, min($scalepoints, (int)$raw[$field]));
                    if (!empty($item['reverse_scored'])) {
                        $value = ($scalepoints + 1) - $value;
                    }
                    $values[] = $value;
                }
            }
            $average = $values ? array_sum($values) / count($values) : null;
            $normalised = $average === null ? null : self::normalise($average, $scalepoints);
            $results[$dimensionid] = [
                'id' => $dimensionid,
                'left_label' => (string)($dimension['left_label'] ?? ''),
                'right_label' => (string)($dimension['right_label'] ?? ''),
                'description' => (string)($dimension['description'] ?? ''),
                'scale_points' => $scalepoints,
                'average' => $average,
                'normalised' => $normalised,
                'score' => $average === null ? null : round($average, 2),
                'values' => $values,
            ];
        }
        return $results;
    }

    /**
     * Calculates general dimension results, including focus-derived dimensions.
     *
     * @param array $config Instrument configuration.
     * @param array $raw Submitted raw data.
     * @param array $focusresults Focus results.
     * @return array General dimension results.
     */
    protected static function calculate_general_dimensions(array $config, array $raw, array $focusresults): array {
        $results = [];
        $defaultscale = max(2, min(8, (int)($config['settings']['general_dimension_scale_points'] ?? 8)));
        $general = array_values($config['general_dimensions'] ?? []);
        $generalsafes = response_form::unique_safe_names($general, 'general');
        foreach ($general as $dimensionindex => $dimension) {
            $dimensionid = (string)($dimension['id'] ?? 'dimension');
            $scalepoints = max(2, min(8, (int)($dimension['scale_points'] ?? $defaultscale)));
            $source = (string)($dimension['source'] ?? 'respondent');
            $score = null;
            $normalised = null;
            if ($source === 'focus_dimension') {
                $sourceid = (string)($dimension['source_dimension_id'] ?? $dimensionid);
                if (isset($focusresults[$sourceid]) && $focusresults[$sourceid]['normalised'] !== null) {
                    $normalised = $focusresults[$sourceid]['normalised'];
                    $score = self::convert_from_normalised($normalised, $scalepoints);
                }
            } else {
                $field = 'general_' . ($generalsafes[$dimensionindex] ?? response_form::safe_name($dimensionid));
                if (!empty($raw[$field])) {
                    $score = max(1, min($scalepoints, (int)$raw[$field]));
                    $normalised = self::normalise($score, $scalepoints);
                }
            }
            $results[] = [
                'id' => $dimensionid,
                'left_label' => (string)($dimension['left_label'] ?? ''),
                'right_label' => (string)($dimension['right_label'] ?? ''),
                'description' => (string)($dimension['description'] ?? ''),
                'scale_points' => $scalepoints,
                'source' => $source,
                'score' => $score,
                'normalised' => $normalised,
            ];
        }
        return $results;
    }

    /**
     * Calculates matrix result from the two focus dimensions.
     *
     * @param array $config Instrument configuration.
     * @param array $focusresults Focus results.
     * @return array Matrix result.
     */
    protected static function calculate_matrix(array $config, array $focusresults): array {
        $matrix = $config['matrix'] ?? [];
        if (empty($matrix['enabled'])) {
            return [];
        }
        $horizontalid = (string)($matrix['horizontal_dimension_id'] ?? ($config['focus_dimensions'][0]['id'] ?? ''));
        $verticalid = (string)($matrix['vertical_dimension_id'] ?? ($config['focus_dimensions'][1]['id'] ?? ''));
        $horizontalresult = $focusresults[$horizontalid] ?? null;
        $verticalresult = $focusresults[$verticalid] ?? null;
        $horizontal = $horizontalresult['normalised'] ?? null;
        $vertical = $verticalresult['normalised'] ?? null;
        if ($horizontal === null || $vertical === null || !$horizontalresult || !$verticalresult) {
            return [];
        }

        $horizontalpole = $horizontal < 0.5 ? 'left' : 'right';
        $verticalpole = $vertical < 0.5 ? 'left' : 'right';
        $hleft = (string)($horizontalresult['left_label'] ?? '');
        $hright = (string)($horizontalresult['right_label'] ?? '');
        $vbottom = (string)($verticalresult['left_label'] ?? '');
        $vtop = (string)($verticalresult['right_label'] ?? '');
        $selected = [];
        $quadrants = [];

        // The matrix uses the normal mathematical orientation:
        // the horizontal axis increases from left to right, and the vertical
        // axis increases from bottom to top. Therefore the right pole of the
        // vertical dimension is displayed at the top.
        $positions = [
            'right_left' => 'top-left',
            'right_right' => 'top-right',
            'left_left' => 'bottom-left',
            'left_right' => 'bottom-right',
        ];
        $romans = [
            'top-right' => 'I',
            'top-left' => 'II',
            'bottom-left' => 'III',
            'bottom-right' => 'IV',
        ];

        foreach (($matrix['quadrants'] ?? []) as $quadrant) {
            $hpole = (string)($quadrant['horizontal_pole'] ?? '');
            $vpole = (string)($quadrant['vertical_pole'] ?? '');
            $key = $vpole . '_' . $hpole;
            $heading = ($hpole === 'right' ? $hright : $hleft) . ' | ' . ($vpole === 'right' ? $vtop : $vbottom);
            $position = $positions[$key] ?? '';
            $item = [
                'position' => $position,
                'roman' => $romans[$position] ?? '',
                'horizontal_pole' => $hpole,
                'vertical_pole' => $vpole,
                'heading' => $heading,
                'label' => (string)($quadrant['label'] ?? ''),
                'summary' => (string)($quadrant['summary'] ?? ''),
                'assignments' => self::text_from_value($quadrant['assignments'] ?? ''),
                'prompt' => self::text_from_value($quadrant['prompt'] ?? ($quadrant['reflection_prompt'] ?? '')),
                'selected' => $hpole === $horizontalpole && $vpole === $verticalpole,
            ];
            if ($item['selected']) {
                $selected = $item;
            }
            $quadrants[] = $item;
        }

        $hscale = (int)($horizontalresult['scale_points'] ?? 4);
        $vscale = (int)($verticalresult['scale_points'] ?? 4);
        $hscore = $horizontalresult['score'] ?? null;
        $vscore = $verticalresult['score'] ?? null;
        $hpercent = round(max(0.0, min(1.0, $horizontal)) * 100, 1);
        $vpercent = round(max(0.0, min(1.0, $vertical)) * 100, 1);
        $vtoppercent = round(100 - $vpercent, 1);

        return [
            'horizontal_dimension_id' => $horizontalid,
            'vertical_dimension_id' => $verticalid,
            'horizontal_pole' => $horizontalpole,
            'vertical_pole' => $verticalpole,
            'horizontal_left_label' => $hleft,
            'horizontal_right_label' => $hright,
            'vertical_top_label' => $vtop,
            'vertical_bottom_label' => $vbottom,
            'horizontal_score_text' => $hscore === null ? '' : get_string('scoreoutof', 'mod_personalityfinder',
                ['score' => $hscore, 'max' => $hscale]),
            'vertical_score_text' => $vscore === null ? '' : get_string('scoreoutof', 'mod_personalityfinder',
                ['score' => $vscore, 'max' => $vscale]),
            'horizontal_ticks' => self::ticks($hscale),
            'vertical_ticks' => self::ticks($vscale, true),
            'marker_left' => $hpercent,
            'marker_top' => $vtoppercent,
            'markerstyle' => '--pf-marker-left: ' . $hpercent . '%; --pf-marker-top: ' . $vtoppercent . '%;',
            'quadrants' => $quadrants,
            'selected' => $selected,
            'label' => (string)($selected['label'] ?? ''),
            'summary' => (string)($selected['summary'] ?? ''),
            'assignments' => (string)($selected['assignments'] ?? ''),
            'prompt' => (string)($selected['prompt'] ?? ''),
        ];
    }


    /**
     * Builds scale ticks for template display.
     *
     * @param int $scale Scale points.
     * @param bool $reverse Whether to display from high to low.
     * @return array Tick items.
     */
    protected static function ticks(int $scale, bool $reverse = false): array {
        $scale = max(2, min(8, $scale));
        $values = range(1, $scale);
        if ($reverse) {
            $values = array_reverse($values);
        }
        return array_map(static fn($value) => ['value' => $value], $values);
    }

    /**
     * Safely converts scalar or list values from JSON into display text.
     *
     * @param mixed $value Source value.
     * @return string Display text.
     */
    protected static function text_from_value($value): string {
        if (is_array($value)) {
            $parts = [];
            foreach ($value as $item) {
                if (is_array($item)) {
                    $item = implode(' ', array_map('strval', $item));
                }
                $item = trim((string)$item);
                if ($item !== '') {
                    $parts[] = $item;
                }
            }
            return implode("
", $parts);
        }
        return trim((string)$value);
    }

    /**
     * Converts a score to a 0..1 normalised value.
     *
     * @param float $score Score.
     * @param int $scale Scale points.
     * @return float Normalised score.
     */
    protected static function normalise(float $score, int $scale): float {
        if ($scale <= 1) {
            return 0.0;
        }
        return max(0.0, min(1.0, ($score - 1) / ($scale - 1)));
    }

    /**
     * Converts a 0..1 value to a scale score.
     *
     * @param float $normalised Normalised value.
     * @param int $scale Scale points.
     * @return int Converted score.
     */
    protected static function convert_from_normalised(float $normalised, int $scale): int {
        return (int)round(max(0.0, min(1.0, $normalised)) * ($scale - 1) + 1);
    }

    /**
     * Converts results to template data.
     *
     * @param array $results Results.
     * @return array Template data.
     */
    public static function for_template(array $results): array {
        $self = array_map([self::class, 'attribute_template'], $results['attributes']['self'] ?? []);
        $others = array_map([self::class, 'attribute_template'], $results['attributes']['others'] ?? []);
        $cards = array_map([self::class, 'attribute_template'], $results['attributes']['cards'] ?? []);
        // Do not rebuild cards from the 'others' list. If the respondent did
        // not select the attribute for themselves, it is intentionally absent
        // from the card report.
        $focus = [];
        foreach (($results['focus_dimensions'] ?? []) as $dimension) {
            $focus[] = self::dimension_template($dimension, false);
        }
        $general = [];
        foreach (($results['general_dimensions'] ?? []) as $dimension) {
            $general[] = self::dimension_template($dimension, ($dimension['source'] ?? '') === 'focus_dimension');
        }
        $cloud = self::reflection_cloud($results['general_dimensions'] ?? []);
        $matrix = $results['matrix'] ?? [];
        return [
            'selfattributes' => $self,
            'hasselfattributes' => !empty($self),
            'otherattributes' => $others,
            'hasotherattributes' => !empty($others),
            'attributecards' => $cards,
            'hasattributecards' => !empty($cards),
            'focusdimensions' => $focus,
            'generaldimensions' => $general,
            'hasreflectioncloud' => !empty($cloud),
            'reflectioncloud' => $cloud,
            'hasmatrix' => !empty($matrix),
            'matrix' => $matrix,
        ];
    }


    /**
     * Builds deterministic word-map data from general personality dimensions.
     *
     * Each bipolar dimension contributes both pole labels. The word closest to
     * the respondent's calculated or selected position is made visually stronger.
     * This is a reflection aid, not a diagnostic word cloud.
     *
     * @param array $dimensions General dimension results.
     * @return array Cloud word template data.
     */
    protected static function reflection_cloud(array $dimensions): array {
        $words = [];
        $palette = ['blue', 'purple', 'red', 'green', 'orange'];
        $index = 0;
        foreach ($dimensions as $dimension) {
            $score = $dimension['score'] ?? null;
            $scale = max(2, min(8, (int)($dimension['scale_points'] ?? 8)));
            $normalised = $dimension['normalised'] ?? null;
            if ($score === null || $normalised === null) {
                continue;
            }

            $leftlabel = trim((string)($dimension['left_label'] ?? ''));
            $rightlabel = trim((string)($dimension['right_label'] ?? ''));
            if ($leftlabel !== '') {
                $words[] = self::cloud_word($leftlabel, 1.0 - (float)$normalised, $palette[$index % count($palette)]);
            }
            if ($rightlabel !== '') {
                $words[] = self::cloud_word($rightlabel, (float)$normalised, $palette[($index + 2) % count($palette)]);
            }
            $index++;
        }

        // Show stronger descriptors first while keeping a stable order for ties.
        usort($words, static function(array $a, array $b): int {
            if ($a['strength'] === $b['strength']) {
                return $a['order'] <=> $b['order'];
            }
            return $a['strength'] < $b['strength'] ? 1 : -1;
        });

        $counter = 0;
        foreach ($words as &$word) {
            $word['order'] = ++$counter;
        }
        unset($word);
        return $words;
    }

    /**
     * Creates a single deterministic reflection-cloud word.
     *
     * @param string $label Word or phrase.
     * @param float $strength 0..1 strength.
     * @param string $palette Palette class suffix.
     * @return array Template data.
     */
    protected static function cloud_word(string $label, float $strength, string $palette): array {
        static $order = 0;
        $order++;
        $strength = max(0.0, min(1.0, $strength));
        $minfont = 18;
        $maxfont = 52;
        $fontsize = (int)round($minfont + (($maxfont - $minfont) * $strength));
        $opacity = round(0.35 + (0.65 * $strength), 2);
        if ($strength >= 0.78) {
            $band = 'verystrong';
        } else if ($strength >= 0.63) {
            $band = 'strong';
        } else if ($strength >= 0.48) {
            $band = 'moderate';
        } else if ($strength >= 0.35) {
            $band = 'subtle';
        } else {
            $band = 'weak';
        }
        return [
            'label' => $label,
            'strength' => $strength,
            'order' => $order,
            'fontsize' => $fontsize,
            'opacity' => $opacity,
            'style' => '--pf-cloud-size: ' . $fontsize . 'px; --pf-cloud-opacity: ' . $opacity . ';',
            'pdfstyle' => 'font-size:' . $fontsize . 'px;',
            'band' => $band,
            'palette' => $palette,
            'class' => 'personalityfinder-cloud-word-' . $band . ' personalityfinder-cloud-word-' . $palette,
        ];
    }

    /**
     * Prepares attribute template data.
     *
     * @param mixed $attribute Attribute result item or legacy label.
     * @return array Template data.
     */
    protected static function attribute_template($attribute): array {
        if (!is_array($attribute)) {
            return [
                'label' => (string)$attribute,
                'description' => '',
                'self' => false,
                'others' => false,
                'shared' => false,
                'selfonly' => false,
                'othersonly' => false,
            ];
        }
        $self = !empty($attribute['self']);
        $others = !empty($attribute['others']);
        return [
            'label' => (string)($attribute['label'] ?? ''),
            'description' => (string)($attribute['description'] ?? ''),
            'self' => $self,
            'others' => $others,
            'shared' => $self && $others,
            'selfonly' => $self && !$others,
            'othersonly' => !$self && $others,
        ];
    }

    /**
     * Prepares dimension template data.
     *
     * @param array $dimension Dimension result.
     * @param bool $calculated Whether calculated from focus dimensions.
     * @return array Template data.
     */
    protected static function dimension_template(array $dimension, bool $calculated): array {
        $score = $dimension['score'];
        $max = (int)$dimension['scale_points'];
        return [
            'left_label' => $dimension['left_label'],
            'right_label' => $dimension['right_label'],
            'description' => $dimension['description'],
            'score' => $score,
            'max' => $max,
            'scoretext' => $score === null ? '' : get_string('scoreoutof', 'mod_personalityfinder', ['score' => $score, 'max' => $max]),
            'calculated' => $calculated,
        ];
    }
}
