/* ------------------------------------------------------------------ *
 * Admin-only Alpine components. Only referenced from views under
 * resources/views/admin/**, but bundled in the shared app.js entry
 * (same Vite entry as the public site — harmless no-op elsewhere).
 * ------------------------------------------------------------------ */
import Alpine from 'alpinejs';

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
