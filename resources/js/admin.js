/* ------------------------------------------------------------------ *
 * Admin entry point (Iterasi 13 — Fase 3: pemisahan bundle publik vs
 * admin). Mandiri: Alpine core + reveal-on-scroll (admin layout &
 * halaman login memakai x-data="revealOnScroll", lihat reveal.js) +
 * helper CRUD admin di bawah ini. TIDAK mengimpor ./portfolio — dicek
 * (grep) tidak ada view admin manapun yang memakai $store.lang,
 * $store.ui, atau appRoot()/aboutSection()/projectsSection()/dst, jadi
 * seluruh logic publik itu tidak perlu ikut terkirim ke halaman admin.
 * ------------------------------------------------------------------ */
import Alpine from 'alpinejs';
import './reveal';

/**
 * Pill switch on the "Pengaturan Section" page — auto-saves via PATCH
 * to admin.section-settings.toggle as soon as it's clicked (no separate
 * "Simpan" button; see docs/LOG-ITERASI.md Iterasi 1 for the rationale).
 * Optimistic UI: flips immediately, rolls back + shows an error pill on
 * failure, reconciles with the server's response on success.
 */
Alpine.data('sectionToggle', (id, initialActive) => ({
    id,
    active: initialActive,
    loading: false,
    feedback: null,
    feedbackTimer: null,

    async toggle() {
        if (this.loading) return;

        const previous = this.active;
        this.active = !this.active;
        this.loading = true;

        try {
            const res = await fetch(`/admin/section-settings/${this.id}/toggle`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
            });

            if (!res.ok) throw new Error('Request failed');

            const data = await res.json();
            this.active = data.is_active;
            this.showFeedback('ok');
        } catch (e) {
            this.active = previous;
            this.showFeedback('error');
        } finally {
            this.loading = false;
        }
    },

    showFeedback(type) {
        clearTimeout(this.feedbackTimer);
        this.feedback = type;
        this.feedbackTimer = setTimeout(() => { this.feedback = null; }, 2000);
    },
}));

window.Alpine = Alpine;

Alpine.start();
