import './bootstrap';

// Registrazione del service worker (rende il sito installabile come app / PWA).
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch((error) => {
            console.warn('Service worker non registrato:', error);
        });
    });
}

// Menu mobile (apertura/chiusura).
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-menu]');
    if (toggle && menu) {
        toggle.addEventListener('click', () => menu.classList.toggle('hidden'));
    }

    // Comparsa "morbida" degli elementi quando entrano nello schermo.
    // Gli elementi con [data-reveal] partono visibili; qui li prepariamo e li
    // riveliamo. Se il browser non supporta l'osservatore, restano visibili.
    const items = document.querySelectorAll('[data-reveal]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (items.length && 'IntersectionObserver' in window && !reduceMotion) {
        items.forEach((el, i) => {
            el.classList.add('reveal');
            el.style.transitionDelay = `${Math.min(i % 4, 3) * 90}ms`;
        });
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        items.forEach((el) => observer.observe(el));
    }
});
