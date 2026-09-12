import './bootstrap';
import flatpickr from 'flatpickr';
import { Italian } from 'flatpickr/dist/l10n/it.js';
import 'flatpickr/dist/flatpickr.min.css';

// Registrazione del service worker (rende il sito installabile come app / PWA).
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch((error) => {
            console.warn('Service worker non registrato:', error);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Menu mobile (apertura/chiusura).
    const toggle = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-menu]');
    if (toggle && menu) {
        toggle.addEventListener('click', () => menu.classList.toggle('hidden'));
    }

    // Comparsa "morbida" degli elementi quando entrano nello schermo.
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

    // Calendario di prenotazione (scelta arrivo/partenza) con giorni pieni disattivati.
    const rangeEl = document.querySelector('[data-date-range]');
    if (rangeEl) {
        const inHidden = document.querySelector('#booking-checkin');
        const outHidden = document.querySelector('#booking-checkout');
        let disabled = [];
        try {
            disabled = JSON.parse(rangeEl.dataset.disabled || '[]');
        } catch (e) {
            disabled = [];
        }

        const startVal = rangeEl.dataset.start;
        const endVal = rangeEl.dataset.end;

        flatpickr(rangeEl, {
            mode: 'range',
            minDate: 'today',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'j F Y',
            altInputClass: rangeEl.className,
            locale: Italian,
            disable: disabled,
            defaultDate: (startVal && endVal) ? [startVal, endVal] : null,
            onChange(selectedDates) {
                if (selectedDates.length === 2 && inHidden && outHidden) {
                    const fmt = (d) => {
                        const m = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        return `${d.getFullYear()}-${m}-${day}`;
                    };
                    inHidden.value = fmt(selectedDates[0]);
                    outHidden.value = fmt(selectedDates[1]);
                }
            },
        });
    }
});
