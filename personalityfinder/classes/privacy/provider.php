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

namespace mod_personalityfinder\privacy;

use core_privacy\local\metadata\collection;

/**
 * Privacy provider for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider {
    /**
     * Describes personal data stored by this plugin.
     *
     * @param collection $collection Metadata collection.
     * @return collection Updated collection.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'personalityfinder_responses',
            [
                'personalityfinderid' => 'privacy:metadata:personalityfinder_responses:personalityfinderid',
                'userid' => 'privacy:metadata:personalityfinder_responses:userid',
                'responsesjson' => 'privacy:metadata:personalityfinder_responses:responsesjson',
                'resultsjson' => 'privacy:metadata:personalityfinder_responses:resultsjson',
                'timecreated' => 'privacy:metadata:personalityfinder_responses:timecreated',
                'timemodified' => 'privacy:metadata:personalityfinder_responses:timemodified',
            ],
            'privacy:metadata:personalityfinder_responses'
        );
        return $collection;
    }
}
