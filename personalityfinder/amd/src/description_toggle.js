// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Toggles the activity description and remembers the user's preference.
 *
 * @module     mod_personalityfinder/description_toggle
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    return {
        init: function(cmid, showText, hideText) {
            document.querySelectorAll('.personalityfinder-redo-link').forEach(function(link) {
                link.addEventListener('click', function(event) {
                    if (!window.confirm(link.getAttribute('data-confirm-message'))) {
                        event.preventDefault();
                    }
                });
            });

            var root = document.querySelector('.path-mod-personalityfinder #intro.activity-description');
            if (!root) {
                return;
            }

            root.setAttribute('data-personalityfinder-description', cmid);

            var storageKey = 'mod_personalityfinder_description_visible_' + cmid;
            var showLabel = showText;
            var hideLabel = hideText;

            var button = document.querySelector('.personalityfinder-description-toggle[data-cmid="' + cmid + '"]');
            if (!button) {
                button = document.createElement('button');
                button.type = 'button';
                button.className = 'btn btn-secondary btn-sm personalityfinder-description-toggle';
                button.setAttribute('data-cmid', cmid);
                root.insertAdjacentElement('afterend', button);
            }

            var setVisible = function(visible) {
                root.hidden = !visible;
                button.setAttribute('aria-expanded', visible ? 'true' : 'false');
                button.textContent = visible ? hideLabel : showLabel;
                try {
                    window.localStorage.setItem(storageKey, visible ? '1' : '0');
                } catch (e) {
                    // Ignore private browsing or locked storage.
                }
            };

            try {
                var saved = window.localStorage.getItem(storageKey);
                setVisible(saved !== '0');
            } catch (e) {
                setVisible(true);
            }

            button.addEventListener('click', function() {
                setVisible(root.hidden);
            });
        }
    };
});
