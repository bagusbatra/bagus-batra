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
import { Editor } from '@tiptap/core';
import { StarterKit } from '@tiptap/starter-kit';

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

/**
 * Iterasi 27 (Fase 5) — rich text editor (Tiptap) untuk field `body` tiap
 * "Bagian Artikel" (section) di form Blog (admin/blog/form.blade.php).
 * Dipilih di atas Quill (lihat docs/RENCANA-PENYEMPURNAAN-ADMIN.md bagian
 * 3): Tiptap headless, TIDAK bawa CSS/toolbar sendiri, jadi toolbar
 * dibangun sendiri pakai <x-icon> & kelas Tailwind yang sama gayanya
 * dengan tombol admin lain — menyatu secara visual, bukan widget pihak
 * ketiga yang ditempel.
 *
 * SATU instance Editor per section (form Blog punya repeater dinamis,
 * section bisa ditambah/dihapus via Alpine x-for) — komponen ini dipasang
 * di `x-data="richTextEditor(section)"` DI DALAM x-for section, jadi tiap
 * iterasi otomatis dapat instance-nya sendiri, dan Alpine otomatis
 * memanggil destroy() (magic teardown hook bawaan Alpine) saat baris
 * section-nya dihapus dari array — tidak perlu cleanup manual di listener
 * tombol hapus.
 *
 * `section` (objek row dari array reaktif `sections` milik x-data parent
 * form) dioper LANGSUNG (referensi objek JS, bukan disalin) — onUpdate()
 * menulis balik ke `section.body` supaya perubahan otomatis "terlihat"
 * oleh x-data parent (array `sections` yang sama persis dipakai utk
 * generate `sections[${index}][body]` saat submit), tanpa perlu emit
 * event/props eksplisit lintas komponen.
 *
 * `tick` — Tiptap mengelola state internalnya sendiri lewat ProseMirror
 * (BUKAN lewat Alpine reactive proxy), jadi Alpine TIDAK tahu kapan harus
 * re-evaluate binding `:class="isActive('bold') ? ... : ..."` di tombol
 * toolbar kalau hanya membaca editor langsung. `tick` dibaca (bukan
 * dipakai nilainya) di dalam isActive() semata-mata supaya Alpine's
 * dependency-tracking mencatatnya sbg dependency dari ekspresi itu — tiap
 * `tick++` di onTransaction (dipanggil Tiptap di SETIAP perubahan state:
 * ketik, pindah selection, toggle mark, dst) memaksa Alpine re-evaluate
 * SEMUA binding yg membaca isActive(), sehingga status "aktif" tombol
 * toolbar (mis. Bold ter-highlight saat kursor di teks tebal) selalu
 * sinkron real-time.
 *
 * BUG NYATA ditemukan & diperbaiki saat verifikasi browser sungguhan
 * (Iterasi 27): instance Editor SEMPAT disimpan sbg `this.editor` (properti
 * biasa di objek yang dikembalikan `Alpine.data()`) — Alpine ikut membuat
 * properti itu REAKTIF (deep-proxy, sama seperti semua properti lain di
 * `x-data`), yang berarti instance ProseMirror internal Tiptap (state/view)
 * ikut terbungkus Proxy Alpine. ProseMirror melakukan pengecekan identitas
 * referensi ketat antara `transaction.startState` & `view.state` saat
 * dispatch — dibungkus Proxy merusak pengecekan itu, menghasilkan
 * `RangeError: Applying a mismatched transaction` SETIAP kali toolbar
 * diklik (mis. toggle Bold), terlihat jelas di console browser sungguhan
 * walau tidak muncul di pengujian curl (curl tidak menjalankan JS sama
 * sekali). **Solusi**: instance Editor disimpan di variabel closure
 * `editor` DI LUAR objek yang di-return (bukan `this.editor`) — Alpine
 * HANYA membuat properti pada objek yang di-return jadi reaktif, variabel
 * closure biasa tidak pernah disentuh sistem reaktivitasnya sama sekali.
 */
Alpine.data('richTextEditor', (section) => {
    let editor = null;

    return {
        tick: 0,

        init() {
            editor = new Editor({
                element: this.$refs.editorEl,
                extensions: [
                    StarterKit.configure({
                        // Iterasi 27: HANYA formatting level-paragraf (lihat
                        // docs/RENCANA-PENYEMPURNAAN-ADMIN.md bagian 3) —
                        // heading/codeBlock/strike/horizontalRule SENGAJA
                        // dimatikan. heading & codeBlock krn field terpisah
                        // (`heading`, `code_language`/`code_filename`/`code_code`)
                        // sudah ada di section yang sama, membiarkan Tiptap juga
                        // punya heading/code-block akan jadi 2 cara berbeda
                        // membuat hal yang sama (membingungkan admin). strike &
                        // horizontalRule di luar scope literal permintaan.
                        heading: false,
                        codeBlock: false,
                        strike: false,
                        horizontalRule: false,
                        link: {
                            openOnClick: false,
                            HTMLAttributes: { rel: 'noopener noreferrer nofollow', target: '_blank' },
                        },
                    }),
                ],
                content: section.body || '',
                editorProps: {
                    attributes: {
                        // .rich-content — kelas format kustom (resources/css/app.css,
                        // Iterasi 27), BUKAN @tailwindcss/typography (plugin
                        // "prose" TIDAK diinstal di project ini) — kebutuhan
                        // formatting di sini kecil & spesifik (p/strong/em/u/
                        // list/blockquote/code/a saja, tanpa heading/gambar/tabel),
                        // custom CSS terarah lebih proporsional drpd menambah
                        // dependency ke-2 di iterasi yang sama (Tiptap sendiri
                        // sudah 1 pengecualian sadar dari "hindari dependency
                        // baru"). Kelas SAMA dipakai di admin (di sini) & publik
                        // (portfolio/partials/article-modal.blade.php) supaya
                        // WYSIWYG-nya benar-benar wysiwyg — apa yang admin lihat
                        // saat mengetik = gaya yang sama persis dgn yang visitor
                        // lihat nanti.
                        class: 'rich-content focus:outline-none min-h-28 px-3.5 py-2.5',
                    },
                },
                onUpdate: ({ editor: ed }) => {
                    section.body = ed.getHTML();
                },
                onTransaction: () => {
                    this.tick++;
                },
            });
        },

        destroy() {
            editor?.destroy();
        },

        isActive(name, attrs) {
            void this.tick;

            return editor ? editor.isActive(name, attrs) : false;
        },

        run(fn) {
            if (!editor) return;
            fn(editor.chain().focus());
        },

        setLink() {
            if (!editor) return;

            const current = editor.getAttributes('link').href ?? '';
            const url = window.prompt('URL tautan (kosongkan untuk hapus tautan):', current);

            if (url === null) return;

            if (url.trim() === '') {
                editor.chain().focus().unsetLink().run();
                return;
            }

            editor.chain().focus().extendMarkRange('link').setLink({ href: url.trim() }).run();
        },
    };
});

window.Alpine = Alpine;

Alpine.start();
