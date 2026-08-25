// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Handles the attribute "not applicable" option in the respondent form.
 *
 * @module     mod_personalityfinder/attribute_applicability
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    /**
     * Updates one attribute checkbox group.
     *
     * @param {HTMLInputElement} notApplicable The not-applicable checkbox.
     */
    const updateGroup = function(notApplicable) {
        const suffix = notApplicable.name.replace(/^attr_na_/, '');
        const form = notApplicable.closest('form') || document;
        const self = form.querySelector('input[name="attr_self_' + suffix + '"]');
        const others = form.querySelector('input[name="attr_others_' + suffix + '"]');
        [self, others].forEach(function(box) {
            if (!box) {
                return;
            }
            if (notApplicable.checked) {
                box.checked = false;
                box.disabled = true;
                var label = box.closest('label');
                if (label) {
                    label.classList.add('personalityfinder-attribute-option-disabled');
                }
            } else {
                box.disabled = false;
                var label = box.closest('label');
                if (label) {
                    label.classList.remove('personalityfinder-attribute-option-disabled');
                }
            }
        });
    };

    /**
     * Initialises the behaviour.
     */
    const init = function() {
        document.querySelectorAll('input[name^="attr_na_"]').forEach(function(notApplicable) {
            updateGroup(notApplicable);
            notApplicable.addEventListener('change', function() {
                updateGroup(notApplicable);
            });
            const suffix = notApplicable.name.replace(/^attr_na_/, '');
            const form = notApplicable.closest('form') || document;
            ['attr_self_', 'attr_others_'].forEach(function(prefix) {
                const box = form.querySelector('input[name="' + prefix + suffix + '"]');
                if (!box) {
                    return;
                }
                box.addEventListener('change', function() {
                    if (box.checked && notApplicable.checked) {
                        notApplicable.checked = false;
                        updateGroup(notApplicable);
                    }
                });
            });
        });
    };

    return {
        init: init
    };
});
