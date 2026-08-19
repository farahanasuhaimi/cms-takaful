import './bootstrap';

import Alpine from 'alpinejs';
import { quotationBuilder } from './quotation-builder';

window.Alpine = Alpine;
window.quotationBuilder = quotationBuilder;

document.addEventListener('alpine:init', () => {
    // PDPA privacy mode — blurs client names, commission & payment figures
    // sitewide so screenshots for social media don't leak personal data.
    Alpine.store('privacy', {
        enabled: localStorage.getItem('pdpa_privacy_mode') === 'true',
        toggle() {
            this.enabled = !this.enabled;
            localStorage.setItem('pdpa_privacy_mode', this.enabled);
        },
    });
});

Alpine.start();
