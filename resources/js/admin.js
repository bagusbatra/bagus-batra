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

/**
 * Iterasi 20 (Fase 4) — reorder drag-drop untuk tab "Urutan & Isi Section"
 * (7 section top-level). HTML5 Drag and Drop API native (draggable="true" +
 * dragstart/dragover/drop di markup Blade) dikombinasikan dgn state Alpine
 * di sini — TIDAK memakai library JS baru (bukan SortableJS dkk), sesuai
 * docs/RENCANA-KUSTOMISASI-TAMPILAN.md bagian 3.
 *
 * Beda dgn sectionToggle() di atas: komponen ini TIDAK melakukan fetch/AJAX
 * sendiri. Drag-drop murni mengubah urutan array `items` di client (live,
 * tanpa request), lalu admin klik SATU tombol submit "Simpan sebagai Draft"
 * yang men-submit form biasa (PUT ke admin.appearance.sections.update) —
 * hidden input `order[]` dirender via x-for mengikuti urutan `items` saat
 * ini, jadi urutan baru otomatis terkirim tanpa JS tambahan utk serialisasi.
 * Keputusan ini didokumentasikan di docs/LOG-ITERASI.md Iterasi 20: form
 * POST biasa dipilih ketimbang fetch/AJAX supaya konsisten dgn SEMUA tab
 * appearance lain (branding/animasi — full-page redirect + refresh banner
 * status Draft/Live), tanpa perlu duplikasi logic refresh banner via JS.
 */
Alpine.data('sectionReorder', (initialItems) => ({
    items: initialItems,
    dragIndex: null,

    dragStart(index) {
        this.dragIndex = index;
    },

    dragOverItem(index) {
        if (this.dragIndex === null || this.dragIndex === index) return;

        const moved = this.items.splice(this.dragIndex, 1)[0];
        this.items.splice(index, 0, moved);
        this.dragIndex = index;
    },

    dragEnd() {
        this.dragIndex = null;
    },
}));

window.Alpine = Alpine;

Alpine.start();
