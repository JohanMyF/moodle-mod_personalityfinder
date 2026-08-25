// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Stepped semantic differential sliders for PersonalityFinder.
 *
 * @module     mod_personalityfinder/response_slider
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const init = () => {
    const sliders = document.querySelectorAll('.path-mod-personalityfinder .personalityfinder-stepped-range');

    const cssEscape = (value) => {
        if (window.CSS && typeof window.CSS.escape === 'function') {
            return window.CSS.escape(value);
        }
        return value.replace(/(["\\\[\]#.:])/g, '\\$1');
    };

    sliders.forEach((slider) => {
        const targetName = slider.getAttribute('data-target');
        if (!targetName) {
            return;
        }
        const form = slider.closest('form');
        if (!form) {
            return;
        }
        const hidden = form.querySelector('input[type="hidden"][name="' + cssEscape(targetName) + '"]');
        if (!hidden) {
            return;
        }

        const sync = () => {
            hidden.value = slider.value;
            slider.classList.add('is-selected');
        };

        if (hidden.value) {
            slider.value = hidden.value;
            slider.classList.add('is-selected');
        }

        // input/change handle normal dragging. click/pointerup/keyup handle the important
        // case where the respondent deliberately accepts the default notch without moving
        // the slider; some browsers do not fire input/change when the value stays the same.
        slider.addEventListener('input', sync);
        slider.addEventListener('change', sync);
        slider.addEventListener('click', sync);
        slider.addEventListener('pointerup', sync);
        slider.addEventListener('keyup', sync);

        if (!form.dataset.personalityfinderSliderSubmitBound) {
            form.dataset.personalityfinderSliderSubmitBound = '1';
            form.addEventListener('submit', () => {
                form.querySelectorAll('.personalityfinder-stepped-range.is-selected').forEach((selectedSlider) => {
                    const selectedTarget = selectedSlider.getAttribute('data-target');
                    if (!selectedTarget) {
                        return;
                    }
                    const selectedHidden = form.querySelector('input[type="hidden"][name="' + cssEscape(selectedTarget) + '"]');
                    if (selectedHidden) {
                        selectedHidden.value = selectedSlider.value;
                    }
                });
            });
        }
    });
};
