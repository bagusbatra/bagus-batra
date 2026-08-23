import Alpine from 'alpinejs';
import './reveal';
import './portfolio';

/* ------------------------------------------------------------------ *
 * Public-site entry point (Iterasi 13 — Fase 3: pemisahan bundle publik
 * vs admin). Berisi Alpine core + reveal-on-scroll + seluruh logic
 * publik (lang store, scroll-spy navbar, floating widget, filter Blog,
 * article modal, playground demo, dst — lihat portfolio.js). TIDAK
 * mengimpor ./admin — halaman publik tidak pernah butuh helper CRUD
 * admin (sectionToggle dkk), jadi tidak perlu ikut terkirim ke browser.
 * ------------------------------------------------------------------ */

window.Alpine = Alpine;

Alpine.start();
