import Alpine from 'alpinejs';

/* ------------------------------------------------------------------ *
 * Reveal-on-scroll — shared IntersectionObserver logic for [data-reveal]
 * elements. Extracted out of `appRoot` (resources/js/portfolio.js) so it
 * can be reused by the admin panel layout without dragging along
 * public-only concerns (scroll-spy, mobile drawer, floating widget).
 * ------------------------------------------------------------------ */
export function initRevealOnScroll() {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12 }
    );

    document.querySelectorAll('[data-reveal]').forEach((el) => observer.observe(el));
}

/* ------------------------------------------------------------------ *
 * Standalone Alpine component for layouts that only need the reveal
 * animation (e.g. `x-data="revealOnScroll"` on the admin layout root).
 * The public page instead wires this in via `appRoot.initReveal()`.
 * ------------------------------------------------------------------ */
Alpine.data('revealOnScroll', () => ({
    init() {
        initRevealOnScroll();
    },
}));

export default Alpine;
