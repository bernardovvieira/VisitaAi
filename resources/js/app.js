import './bootstrap';
import './theme';
import './offline';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const OPTIONAL_LABEL_REGEX = /\s*\((?:opcional|optional)\)\.?/giu;

function normalizeFormLabels() {
	const labels = document.querySelectorAll('label');

	labels.forEach((label) => {
		// Remove explicit "(opcional)" markers from label text nodes.
		label.childNodes.forEach((node) => {
			if (node.nodeType !== Node.TEXT_NODE) {
				return;
			}
			const original = node.textContent || '';
			const updated = original.replace(OPTIONAL_LABEL_REGEX, '').replace(/\s{2,}/g, ' ');
			if (updated !== original) {
				node.textContent = updated.trimEnd();
			}
		});

		const fieldId = label.getAttribute('for');
		const field = fieldId ? document.getElementById(fieldId) : label.querySelector('input, select, textarea');
		if (!field || !('required' in field) || !field.required) {
			return;
		}

		const alreadyMarked = label.textContent?.includes('*') || !!label.querySelector('[data-required-indicator="true"]');
		if (alreadyMarked) {
			return;
		}

		const star = document.createElement('span');
		star.className = 'text-red-500';
		star.setAttribute('data-required-indicator', 'true');
		star.textContent = ' *';
		label.appendChild(star);
	});
}

document.addEventListener('DOMContentLoaded', normalizeFormLabels);
