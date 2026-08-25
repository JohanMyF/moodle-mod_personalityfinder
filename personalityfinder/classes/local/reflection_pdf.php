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
 * PDF helper for PersonalityFinder reflection summaries.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reflection_pdf {
    /**
     * Downloads the reflection PDF for the current user.
     *
     * @param object $personalityfinder Activity instance.
     * @param object $course Course record.
     * @param object $cm Course module record.
     * @param object $response Response record.
     * @param array $results Calculated results.
     */
    public static function download(object $personalityfinder, object $course, object $cm, object $response, array $results): void {
        global $USER;

        $templatedata = results::for_template($results);
        $matrix = $templatedata['matrix'] ?? [];

        $pdf = new \pdf();
        $pdf->SetCreator('Moodle PersonalityFinder');
        $pdf->SetAuthor(fullname($USER));
        $pdf->SetTitle(format_string($personalityfinder->name) . ' - ' . get_string('reflectionsummarypdf', 'mod_personalityfinder'));
        $pdf->SetSubject(format_string($course->fullname));
        $pdf->SetMargins(14, 16, 14);
        $pdf->SetAutoPageBreak(true, 16);
        $pdf->AddPage();

        $html = self::build_html($personalityfinder, $course, $response, $templatedata, $matrix);
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = clean_filename(format_string($personalityfinder->name) . '-' . get_string('reflectionfilename', 'mod_personalityfinder') . '.pdf');
        $pdf->Output($filename, 'D');
        exit;
    }

    /**
     * Builds safe HTML for TCPDF.
     *
     * @param object $personalityfinder Activity instance.
     * @param object $course Course record.
     * @param object $response Response record.
     * @param array $data Template-style results.
     * @param array $matrix Matrix data.
     * @return string HTML.
     */
    protected static function build_html(object $personalityfinder, object $course, object $response, array $data, array $matrix): string {
        global $USER;

        $html = '';
        $html .= '<h1>' . s(format_string($personalityfinder->name)) . '</h1>';
        $html .= '<p class="muted"><strong>' . s(get_string('reflectionsummarypdf', 'mod_personalityfinder')) . '</strong></p>';
        $html .= '<p class="small">' . s(get_string('generatedfor', 'mod_personalityfinder')) . ': ' . s(fullname($USER)) . '<br />';
        $html .= s(get_string('course')) . ': ' . s(format_string($course->fullname)) . '<br />';
        $html .= s(get_string('generatedon', 'mod_personalityfinder')) . ': ' . userdate(time()) . '<br />';
        $html .= s(get_string('submittedon', 'mod_personalityfinder')) . ': ' . userdate((int)$response->timemodified) . '</p>';
        $html .= '<div class="note"><p>' . s(get_string('reflectionpdfdisclaimer', 'mod_personalityfinder')) . '</p></div>';

        $html .= self::attributes_html($data);
        // Keep the attribute reflection together and begin the scored reflection sections on a new page.
        $html .= '<br pagebreak="true" />';
        $html .= self::dimensions_html(get_string('focusdimensionresults', 'mod_personalityfinder'), $data['focusdimensions'] ?? []);
        $html .= self::matrix_html($matrix);
        $html .= self::dimensions_html(get_string('generaldimensionresults', 'mod_personalityfinder'), $data['generaldimensions'] ?? []);
        $html .= self::reflection_cloud_html($data['reflectioncloud'] ?? []);

        // TCPDF receives this HTML directly, so use element-level styles rather than a stylesheet block.
        $styles = [
            '<h1>' => '<h1 style="color:#0f3760;font-size:20pt;">',
            '<h2>' => '<h2 style="color:#0f3760;font-size:14pt;border-bottom:1px solid #d7dde3;">',
            '<h3>' => '<h3 style="color:#0f3760;font-size:11pt;">',
            'class="muted"' => 'style="color:#4b5563;"',
            'class="note"' => 'style="border:1px solid #b6d4fe;background-color:#f8fbff;padding:8px;"',
            'class="primary"' => 'style="border:1px solid #b6d4fe;background-color:#eef5fb;padding:8px;"',
            'class="small"' => 'style="font-size:8.5pt;"',
            'class="cloud"' => 'style="text-align:center;border:1px solid #d7dde3;background-color:#f8fbff;padding:12px;"',
            'class="selected"' => 'style="background-color:#fff3cd;"',
            'class="qtable"' => 'style="font-size:9.5pt;line-height:1.35;"',
        ];
        return str_replace(array_keys($styles), array_values($styles), $html);
    }

    /**
     * Builds attributes section.
     *
     * @param array $data Template data.
     * @return string HTML.
     */
    protected static function attributes_html(array $data): string {
        $html = '<h2>' . s(get_string('selectedattributes', 'mod_personalityfinder')) . '</h2>';
        $cards = $data['attributecards'] ?? [];
        if (empty($cards)) {
            $html .= '<p class="muted">' . s(get_string('noattributesselected', 'mod_personalityfinder')) . '</p>';
            return $html;
        }
        $html .= '<table class="qtable" cellpadding="5" cellspacing="0" width="100%">';
        foreach ($cards as $item) {
            $badges = [];
            if (!empty($item['others'])) {
                $badges[] = get_string('attributeothersbadge', 'mod_personalityfinder');
            }
            $html .= '<tr><td><strong>' . s((string)($item['label'] ?? '')) . '</strong>';
            if (!empty($item['description'])) {
                $html .= '<br /><span class="muted">' . s((string)$item['description']) . '</span>';
            }
            if (!empty($badges)) {
                $html .= '<br /><span class="small">' . s(implode(' | ', $badges)) . '</span>';
            }
            $html .= '</td></tr>';
        }
        $html .= '</table>';
        return $html;
    }

    /**
     * Builds a list of label items.
     *
     * @param array $items Items.
     * @return string HTML.
     */
    protected static function list_html(array $items): string {
        if (empty($items)) {
            return '<p class="muted">' . s(get_string('noattributesselected', 'mod_personalityfinder')) . '</p>';
        }
        $html = '<ul>';
        foreach ($items as $item) {
            $html .= '<li>' . s((string)($item['label'] ?? '')) . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * Builds a dimension score section.
     *
     * @param string $title Section title.
     * @param array $dimensions Dimensions.
     * @return string HTML.
     */
    protected static function dimensions_html(string $title, array $dimensions): string {
        $html = '<h2>' . s($title) . '</h2>';
        foreach ($dimensions as $dimension) {
            $html .= '<p><strong>' . s((string)($dimension['left_label'] ?? '')) . ' &harr; ' . s((string)($dimension['right_label'] ?? '')) . '</strong>';
            if (!empty($dimension['scoretext'])) {
                $html .= ' <span class="small">(' . s((string)$dimension['scoretext']) . ')</span>';
            }
            if (!empty($dimension['calculated'])) {
                $html .= ' <span class="small">' . s(get_string('calculateddimension', 'mod_personalityfinder')) . '</span>';
            }
            $html .= '</p>';
            if (!empty($dimension['description'])) {
                $html .= '<p class="muted">' . s((string)$dimension['description']) . '</p>';
            }
        }
        return $html;
    }


    /**
     * Builds a deterministic reflection cloud for PDF output.
     *
     * @param array $words Cloud words.
     * @return string HTML.
     */
    protected static function reflection_cloud_html(array $words): string {
        if (empty($words)) {
            return '';
        }
        $html = '<h2>' . s(get_string('reflectioncloudheading', 'mod_personalityfinder')) . '</h2>';
        $html .= '<p class="muted">' . s(get_string('reflectioncloudintro', 'mod_personalityfinder')) . '</p>';
        $html .= '<div class="cloud">';
        foreach ($words as $word) {
            $fontsize = max(12, min(38, (int)($word['fontsize'] ?? 18)));
            $colour = (($word['strength'] ?? 0) < 0.45) ? '#6b7280' : '#0f3760';
            $html .= '<span style="display:inline-block;padding:5px 9px;color:' . $colour . ';font-size:' . $fontsize . 'px;">' . s((string)($word['label'] ?? '')) . '</span> ';
        }
        $html .= '</div>';
        $html .= '<p style="border:1px solid #b6d4fe;background-color:#f8fbff;padding:8px;font-size:8.5pt;">' . s(get_string('reflectioncloudnote', 'mod_personalityfinder')) . '</p>';
        return $html;
    }

    /**
     * Builds matrix section.
     *
     * @param array $matrix Matrix data.
     * @return string HTML.
     */
    protected static function matrix_html(array $matrix): string {
        if (empty($matrix)) {
            return '';
        }
        $html = '<h2>' . s(get_string('matrixresult', 'mod_personalityfinder')) . '</h2>';
        $html .= '<p class="muted">' . s(get_string('matrixresultintro', 'mod_personalityfinder')) . '</p>';
        $html .= '<p><strong>' . s(get_string('scores', 'mod_personalityfinder')) . ':</strong><br />';
        $html .= s((string)($matrix['horizontal_left_label'] ?? '')) . ' &harr; ' . s((string)($matrix['horizontal_right_label'] ?? '')) . ' = ' . s((string)($matrix['horizontal_score_text'] ?? '')) . '<br />';
        $html .= s((string)($matrix['vertical_bottom_label'] ?? '')) . ' &harr; ' . s((string)($matrix['vertical_top_label'] ?? '')) . ' = ' . s((string)($matrix['vertical_score_text'] ?? '')) . '</p>';

        if (!empty($matrix['label'])) {
            $html .= '<div class="primary"><h3>' . s(get_string('primarystyle', 'mod_personalityfinder')) . ': ' . s((string)$matrix['label']) . '</h3>';
            if (!empty($matrix['summary'])) {
                $html .= '<p>' . s((string)$matrix['summary']) . '</p>';
            }
            if (!empty($matrix['assignments'])) {
                $html .= '<p><strong>' . s(get_string('possibleassignments', 'mod_personalityfinder')) . ':</strong> ' . nl2br(s((string)$matrix['assignments'])) . '</p>';
            }
            if (!empty($matrix['prompt'])) {
                $html .= '<p class="muted">' . nl2br(s((string)$matrix['prompt'])) . '</p>';
            }
            $html .= '</div>';
        }

        $quadrants = $matrix['quadrants'] ?? [];
        if (!empty($quadrants)) {
            $byposition = [];
            foreach ($quadrants as $quadrant) {
                $byposition[(string)($quadrant['position'] ?? '')] = $quadrant;
            }
            $html .= '<h3>' . s(get_string('quadrantreflectionoverview', 'mod_personalityfinder')) . '</h3>';
            $html .= '<table class="qtable" cellpadding="4" cellspacing="0" width="100%"><tr>';
            $html .= self::quadrant_cell($byposition['top-left'] ?? []);
            $html .= self::quadrant_cell($byposition['top-right'] ?? []);
            $html .= '</tr><tr>';
            $html .= self::quadrant_cell($byposition['bottom-left'] ?? []);
            $html .= self::quadrant_cell($byposition['bottom-right'] ?? []);
            $html .= '</tr></table>';
        }

        $html .= '<p style="border:1px solid #b6d4fe;background-color:#f8fbff;padding:8px;font-size:8.5pt;">' . s(get_string('matrixresultdisclaimer', 'mod_personalityfinder')) . '</p>';
        return $html;
    }

    /**
     * Builds a matrix quadrant table cell.
     *
     * @param array $quadrant Quadrant data.
     * @return string HTML.
     */
    protected static function quadrant_cell(array $quadrant): string {
        $class = !empty($quadrant['selected']) ? ' class="selected"' : '';
        $html = '<td width="50%"' . $class . '>';
        if (!empty($quadrant['roman'])) {
            $html .= '<p class="small"><strong>' . s((string)$quadrant['roman']) . '</strong></p>';
        }
        if (!empty($quadrant['heading'])) {
            $html .= '<p class="small">' . s((string)$quadrant['heading']) . '</p>';
        }
        if (!empty($quadrant['label'])) {
            $html .= '<h3>' . s((string)$quadrant['label']) . '</h3>';
        }
        if (!empty($quadrant['summary'])) {
            $html .= '<p>' . s((string)$quadrant['summary']) . '</p>';
        }
        if (!empty($quadrant['prompt'])) {
            $html .= '<p class="muted">' . nl2br(s((string)$quadrant['prompt'])) . '</p>';
        }
        $html .= '</td>';
        return $html;
    }
}
