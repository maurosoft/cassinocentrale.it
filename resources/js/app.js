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

    // Effetto parallax leggero sulle immagini con [data-parallax].
    const parallaxEls = document.querySelectorAll('[data-parallax]');
    if (parallaxEls.length && !reduceMotion) {
        let ticking = false;
        const updateParallax = () => {
            const y = window.scrollY;
            parallaxEls.forEach((el) => {
                el.style.transform = `translate3d(0, ${(y * 0.18).toFixed(1)}px, 0)`;
            });
            ticking = false;
        };
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }, { passive: true });
        updateParallax();
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

    // Galleria a schermo intero (lightbox) per le foto delle camere.
    const triggers = Array.from(document.querySelectorAll('[data-lightbox]'));
    if (triggers.length) {
        const groups = {};
        triggers.forEach((el) => {
            const g = el.dataset.lightbox || 'default';
            (groups[g] = groups[g] || []).push(el.dataset.src);
        });

        const overlay = document.createElement('div');
        overlay.className = 'lightbox';
        overlay.hidden = true;
        overlay.innerHTML =
            '<button class="lightbox__close" type="button" aria-label="Chiudi">&times;</button>'
            + '<button class="lightbox__nav lightbox__prev" type="button" aria-label="Precedente">&#8249;</button>'
            + '<img class="lightbox__img" alt="">'
            + '<button class="lightbox__nav lightbox__next" type="button" aria-label="Successiva">&#8250;</button>'
            + '<span class="lightbox__counter"></span>';
        document.body.appendChild(overlay);

        const imgEl = overlay.querySelector('.lightbox__img');
        const counter = overlay.querySelector('.lightbox__counter');
        let list = [];
        let i = 0;

        const render = () => {
            imgEl.src = list[i];
            counter.textContent = list.length > 1 ? `${i + 1} / ${list.length}` : '';
        };
        const openAt = (arr, index) => {
            list = arr;
            i = Math.max(0, index);
            render();
            overlay.hidden = false;
            document.body.style.overflow = 'hidden';
        };
        const close = () => { overlay.hidden = true; document.body.style.overflow = ''; };
        const prev = () => { i = (i - 1 + list.length) % list.length; render(); };
        const next = () => { i = (i + 1) % list.length; render(); };

        triggers.forEach((el) => el.addEventListener('click', () => {
            const arr = groups[el.dataset.lightbox || 'default'];
            openAt(arr, arr.indexOf(el.dataset.src));
        }));
        overlay.querySelector('.lightbox__close').addEventListener('click', close);
        overlay.querySelector('.lightbox__prev').addEventListener('click', (e) => { e.stopPropagation(); prev(); });
        overlay.querySelector('.lightbox__next').addEventListener('click', (e) => { e.stopPropagation(); next(); });
        overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });
        document.addEventListener('keydown', (e) => {
            if (overlay.hidden) return;
            if (e.key === 'Escape') close();
            else if (e.key === 'ArrowLeft') prev();
            else if (e.key === 'ArrowRight') next();
        });
    }
});
