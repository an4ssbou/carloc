import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

if (document.querySelector('[data-gps-tracking]')) {
    import('./gps-tracking');
}
