import Alpine from 'alpinejs';
import { initRevealOnScroll } from './reveal';

/* ------------------------------------------------------------------ *
 * Language store — persisted globally so every section (Blade + Alpine)
 * stays in sync without a page reload.
 * ------------------------------------------------------------------ */
Alpine.store('lang', {
    current: localStorage.getItem('portfolio_lang') || 'id',
    toggle() {
        this.current = this.current === 'id' ? 'en' : 'id';
        localStorage.setItem('portfolio_lang', this.current);
    },
});

/* ------------------------------------------------------------------ *
 * UI store — global modal / drawer / bookmark state shared across the
 * whole page (CV modal, article reader modal). Project case-study used to
 * live here too (activeProject/activeProjectTab/openProject/closeProject)
 * but that modal was retired in Iterasi 11 (Fase 2) in favour of a real
 * page (/projects/{project_key}) — removed together with
 * project-modal.blade.php so no dead store state/JS console errors remain.
 * ------------------------------------------------------------------ */
Alpine.store('ui', {
    cvOpen: false,
    activeArticle: null,
    bookmarks: JSON.parse(localStorage.getItem('blog_bookmarks') || '[]'),

    lockScroll() {
        document.documentElement.style.overflow = 'hidden';
    },
    unlockScroll() {
        document.documentElement.style.overflow = '';
    },

    openCv() {
        this.cvOpen = true;
        this.lockScroll();
    },
    closeCv() {
        this.cvOpen = false;
        this.unlockScroll();
    },

    openArticle(post) {
        this.activeArticle = post;
        this.lockScroll();
    },
    closeArticle() {
        this.activeArticle = null;
        this.unlockScroll();
    },

    isBookmarked(id) {
        return this.bookmarks.includes(id);
    },
    toggleBookmark(id) {
        if (this.bookmarks.includes(id)) {
            this.bookmarks = this.bookmarks.filter((b) => b !== id);
        } else {
            this.bookmarks = [...this.bookmarks, id];
        }
        localStorage.setItem('blog_bookmarks', JSON.stringify(this.bookmarks));
    },
});

/* ------------------------------------------------------------------ *
 * Root app component — scroll progress bar, scroll-spy, mobile drawer,
 * floating back-to-top / quick-contact widget, reveal-on-scroll.
 * ------------------------------------------------------------------ */
Alpine.data('appRoot', () => ({
    isScrolled: false,
    activeSection: 'hero',
    scrollProgress: 0,
    showFloatingWidget: false,
    mobileMenuOpen: false,
    sectionIds: ['hero', 'about', 'skills', 'projects', 'experience', 'blog', 'testimonials', 'contact'],

    init() {
        this.onScroll();
        window.addEventListener('scroll', () => this.onScroll(), { passive: true });
        this.initReveal();
    },

    onScroll() {
        const scrollY = window.scrollY || window.pageYOffset;
        this.isScrolled = scrollY > 20;
        this.showFloatingWidget = scrollY > 400;

        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        this.scrollProgress = docHeight > 0 ? Math.min(1, Math.max(0, scrollY / docHeight)) : 0;

        const scrollPosition = scrollY + 120;
        for (const id of this.sectionIds) {
            const el = document.getElementById(id);
            if (el) {
                const top = el.offsetTop;
                const height = el.offsetHeight;
                if (scrollPosition >= top && scrollPosition < top + height) {
                    this.activeSection = id;
                    break;
                }
            }
        }
    },

    scrollTo(id) {
        this.mobileMenuOpen = false;
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ behavior: 'smooth' });
    },

    scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    initReveal() {
        initRevealOnScroll();
    },
}));

/* ------------------------------------------------------------------ *
 * About section — skills matrix category filter.
 * ------------------------------------------------------------------ */
Alpine.data('aboutSection', () => ({
    category: 'all',
}));

/* ------------------------------------------------------------------ *
 * Projects section — category filter + case-study modal trigger.
 * ------------------------------------------------------------------ */
Alpine.data('projectsSection', () => ({
    category: 'All',
}));

/* ------------------------------------------------------------------ *
 * Blog section — search, category filter, tag chips, bookmarks.
 * ------------------------------------------------------------------ */
Alpine.data('blogSection', (posts) => ({
    query: '',
    category: 'All',
    tag: null,
    posts,

    matches(post) {
        const q = this.query.toLowerCase();
        const matchesSearch =
            q === '' ||
            post.title.toLowerCase().includes(q) ||
            post.summary.toLowerCase().includes(q) ||
            post.tags.some((t) => t.toLowerCase().includes(q));
        const matchesCategory = this.category === 'All' || post.category === this.category;
        const matchesTag = this.tag === null || post.tags.includes(this.tag);
        return matchesSearch && matchesCategory && matchesTag;
    },

    get allTags() {
        return [...new Set(this.posts.flatMap((p) => p.tags))].slice(0, 5);
    },

    get hasResults() {
        return this.posts.some((p) => this.matches(p));
    },

    resetFilters() {
        this.query = '';
        this.category = 'All';
        this.tag = null;
    },

    toggleTag(t) {
        this.tag = this.tag === t ? null : t;
    },
}));

/* ------------------------------------------------------------------ *
 * Article reader modal — per-post likes, bookmarks, comments, share,
 * copy-code — all scoped to the currently open post (from $store.ui).
 * ------------------------------------------------------------------ */
Alpine.data('articleModal', () => ({
    likesByPost: {},
    likedByPost: {},
    commentsByPost: {},
    copiedCodeId: null,
    copiedLink: false,
    newCommentName: '',
    newCommentText: '',

    ensure(post) {
        if (!post) return;
        if (this.likesByPost[post.id] === undefined) {
            this.likesByPost[post.id] = post.likes;
            this.likedByPost[post.id] = false;
        }
        if (this.commentsByPost[post.id] === undefined) {
            const saved = localStorage.getItem(`comments_${post.id}`);
            if (saved) {
                try {
                    this.commentsByPost[post.id] = JSON.parse(saved);
                } catch (e) {
                    this.commentsByPost[post.id] = [];
                }
            } else {
                this.commentsByPost[post.id] = [
                    {
                        id: 'c1',
                        author: 'Dimas Wijaya',
                        avatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=120&auto=format&fit=crop&q=80',
                        date: '2 jam lalu',
                        text: 'Penjelasan arsitekturnya sangat gamblang dan solutif! Terutama bagian pemisahan server vs client components di Next.js 15.',
                    },
                    {
                        id: 'c2',
                        author: 'Nadia Salsabila',
                        avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=120&auto=format&fit=crop&q=80',
                        date: 'Kemarin',
                        text: 'Tips Web Vitals-nya langsung saya terapkan di project kantor dan FCP turun signifikan. Terima kasih mas Bagus!',
                    },
                ];
            }
        }
    },

    likes(post) {
        this.ensure(post);
        return this.likesByPost[post.id];
    },
    hasLiked(post) {
        this.ensure(post);
        return this.likedByPost[post.id];
    },
    toggleLike(post) {
        this.ensure(post);
        if (this.likedByPost[post.id]) {
            this.likesByPost[post.id] -= 1;
            this.likedByPost[post.id] = false;
        } else {
            this.likesByPost[post.id] += 1;
            this.likedByPost[post.id] = true;
        }
    },
    comments(post) {
        this.ensure(post);
        return this.commentsByPost[post.id];
    },
    addComment(post) {
        if (!this.newCommentName.trim() || !this.newCommentText.trim()) return;
        this.ensure(post);
        const comment = {
            id: Date.now().toString(),
            author: this.newCommentName.trim(),
            avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80',
            date: Alpine.store('lang').current === 'id' ? 'Baru saja' : 'Just now',
            text: this.newCommentText.trim(),
        };
        this.commentsByPost[post.id] = [comment, ...this.commentsByPost[post.id]];
        localStorage.setItem(`comments_${post.id}`, JSON.stringify(this.commentsByPost[post.id]));
        this.newCommentName = '';
        this.newCommentText = '';
    },

    copyCode(code, id) {
        navigator.clipboard.writeText(code);
        this.copiedCodeId = id;
        setTimeout(() => (this.copiedCodeId = null), 2000);
    },

    share(platform, post) {
        const url = window.location.href;
        const text = `${post.title} — oleh Bagus Batra`;
        if (platform === 'twitter') {
            window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank');
        } else if (platform === 'linkedin') {
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`, '_blank');
        } else if (platform === 'whatsapp') {
            window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(`${text} ${url}`)}`, '_blank');
        } else if (platform === 'copy') {
            navigator.clipboard.writeText(url);
            this.copiedLink = true;
            setTimeout(() => (this.copiedLink = false), 2000);
        }
    },
}));

/* ------------------------------------------------------------------ *
 * Contact section — project-type pill selector + email copy feedback.
 * The actual message form is a real POST (see routes/web.php) handled
 * server-side; this only manages small client-side UI affordances.
 * ------------------------------------------------------------------ */
Alpine.data('contactSection', (initialProjectType) => ({
    projectType: initialProjectType,
    copiedEmail: false,
    calendarOpen: false,
    selectedSlot: null,
    callBooked: false,

    copyEmail(email) {
        navigator.clipboard.writeText(email);
        this.copiedEmail = true;
        setTimeout(() => (this.copiedEmail = false), 2400);
    },

    bookCall() {
        if (!this.selectedSlot) return;
        this.callBooked = true;
        setTimeout(() => {
            this.calendarOpen = false;
            this.callBooked = false;
            this.selectedSlot = null;
        }, 2500);
    },
}));

/* ------------------------------------------------------------------ *
 * Social bar — copy-email affordance reused in Hero / Contact.
 * ------------------------------------------------------------------ */
Alpine.data('socialBar', () => ({
    copiedEmail: false,
    copyEmail(email) {
        navigator.clipboard.writeText(email);
        this.copiedEmail = true;
        setTimeout(() => (this.copiedEmail = false), 2400);
    },
}));

export default Alpine;
