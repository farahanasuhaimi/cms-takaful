import './bootstrap';

import Alpine from 'alpinejs';
import { quotationBuilder } from './quotation-builder';

window.Alpine = Alpine;
window.quotationBuilder = quotationBuilder;

Alpine.start();
