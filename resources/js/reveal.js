import Alpine from 'alpinejs';

/* ------------------------------------------------------------------ *
 * Reveal-on-scroll — shared IntersectionObserver logic for [data-reveal]
 * elements. Extracted out of `appRoot` (resources/js/portfolio.js) so it
 * can be reused by the admin panel layout without dragging along
 * public-only concerns (scroll-spy, mobile drawer, floating widget).
 * ------------------------------------------------------------------ */
export function initRevealOnScroll() {
    // Iterasi 18 (Fase 4) — bukti konsep end-to-end setting
    // `animations_enabled` (lihat App\Models\DisplaySetting,
    // App\Http\Middleware\HandleAppearancePreview). Nilai efektif
    // (mempertimbangkan draft/preview di sisi server) dikirim sebagai
    // `data-reveal-enabled` di <body> oleh resources/views/layouts/app.blade.php
    // — dibaca langsung dari DOM di sini (server-rendered), TIDAK query API
    // terpisah. Body admin layout tidak memiliki atribut ini sama sekali,
    // jadi `undefined !== '0'` tetap true (default aktif) — panel admin
    // sendiri tidak ikut terpengaruh setting tampilan situs publik.
    const enabled = document.body?.dataset.revealEnabled !== '0';

    if (!enabled) {
        // Animasi dimatikan: tampilkan langsung semua elemen [data-reveal]
        // tanpa observer, supaya konten tidak "nyangkut" tersembunyi
        // (CSS elemen ini default opacity-0 sebelum class .is-visible).
        document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
        return;
    }

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
