import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  BookOpen,
  Search,
  Clock,
  Calendar,
  Eye,
  Heart,
  Bookmark,
  ArrowRight,
  Sparkles,
  Tag,
  Share2
} from 'lucide-react';
import { BLOG_POSTS } from '../data/portfolioData';
import { BlogPost, Language } from '../types';
import { ArticleModal } from './ArticleModal';

interface BlogSectionProps {
  lang: Language;
}

export const BlogSection: React.FC<BlogSectionProps> = ({ lang }) => {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('All');
  const [selectedPost, setSelectedPost] = useState<BlogPost | null>(null);
  const [bookmarkedIds, setBookmarkedIds] = useState<string[]>([]);
  const [activeTag, setActiveTag] = useState<string | null>(null);

  useEffect(() => {
    const savedBookmarks = localStorage.getItem('blog_bookmarks');
    if (savedBookmarks) {
      try {
        setBookmarkedIds(JSON.parse(savedBookmarks));
      } catch (e) {
        setBookmarkedIds([]);
      }
    }
  }, []);

  const handleToggleBookmark = (postId: string) => {
    let updated: string[];
    if (bookmarkedIds.includes(postId)) {
      updated = bookmarkedIds.filter((id) => id !== postId);
    } else {
      updated = [...bookmarkedIds, postId];
    }
    setBookmarkedIds(updated);
    localStorage.setItem('blog_bookmarks', JSON.stringify(updated));
  };

  const categories = [
    { id: 'All', labelId: 'Semua Artikel', labelEn: 'All Articles' },
    { id: 'Frontend Architecture', labelId: 'Frontend Architecture', labelEn: 'Frontend Architecture' },
    { id: 'Performance', labelId: 'Web Performance', labelEn: 'Web Performance' },
    { id: 'Clean Code', labelId: 'UI & Clean Code', labelEn: 'UI & Clean Code' },
    { id: 'Full-Stack', labelId: 'Full-Stack & TS', labelEn: 'Full-Stack & TS' },
  ];

  // All unique tags
  const allTags = Array.from(new Set(BLOG_POSTS.flatMap((p) => p.tags)));

  const filteredPosts = BLOG_POSTS.filter((post) => {
    const matchesSearch =
      post.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
      post.summary.toLowerCase().includes(searchQuery.toLowerCase()) ||
      post.tags.some((t) => t.toLowerCase().includes(searchQuery.toLowerCase()));

    const matchesCategory = selectedCategory === 'All' || post.category === selectedCategory;
    const matchesTag = activeTag === null || post.tags.includes(activeTag);

    return matchesSearch && matchesCategory && matchesTag;
  });

  return (
    <section id="blog" className="py-20 sm:py-24 bg-transparent border-b border-white/60">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-12">
        {/* Header Strip */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: false, amount: 0.15 }}
          transition={{ duration: 0.6 }}
          className="flex flex-col md:flex-row md:items-end justify-between gap-6"
        >
          <div className="max-w-2xl space-y-3">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-50/80 backdrop-blur-md text-indigo-700 border border-indigo-100 text-xs font-bold uppercase tracking-wider">
              <BookOpen className="w-3.5 h-3.5" />
              <span>{lang === 'id' ? 'Blog & Tulisan Teknis' : 'Engineering Blog & Insights'}</span>
            </div>
            <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
              {lang === 'id'
                ? 'Wawasan Rekayasa Web, Performa & Best Practices'
                : 'Architectural Insights, Performance & Web Deep Dives'}
            </h2>
            <p className="text-slate-600 text-sm sm:text-base leading-relaxed">
              {lang === 'id'
                ? 'Catatan teknis, eksplorasi framework modern, dan studi kasus nyata yang saya tulis secara berkala untuk komunitas pengembang.'
                : 'Deep-dive tutorials, performance breakdown case studies, and modern frontend development patterns.'}
            </p>
          </div>

          {/* Search Bar */}
          <div className="w-full md:w-80">
            <div className="relative">
              <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
              <input
                id="blog-search-input"
                type="text"
                placeholder={lang === 'id' ? 'Cari artikel, tag, topik...' : 'Search articles, tags, topics...'}
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-10 pr-4 py-2.5 bg-white/60 backdrop-blur-md rounded-2xl border border-white/80 text-xs text-slate-800 placeholder-slate-400 focus:bg-white/90 focus:outline-indigo-500 shadow-2xs transition-all"
              />
              {searchQuery && (
                <button
                  onClick={() => setSearchQuery('')}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-bold"
                >
                  Clear
                </button>
              )}
            </div>
          </div>
        </motion.div>

        {/* Category Filter Pills & Active Tag Filter */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
          <div className="flex items-center gap-1 p-1.5 bg-white/50 backdrop-blur-md rounded-2xl border border-white/80 shadow-2xs overflow-x-auto max-w-full pb-1.5 sm:pb-1.5 scrollbar-none">
            {categories.map((cat) => {
              const isActive = selectedCategory === cat.id;
              return (
                <button
                  key={cat.id}
                  id={`blog-category-${cat.id.toLowerCase().replace(/[^a-z0-9]/g, '-')}`}
                  onClick={() => setSelectedCategory(cat.id)}
                  className={`relative px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors cursor-pointer shrink-0 ${
                    isActive
                      ? 'text-indigo-600 font-bold'
                      : 'text-slate-600 hover:text-slate-900 hover:bg-white/40'
                  }`}
                >
                  {isActive && (
                    <motion.div
                      layoutId="activeBlogCategory"
                      className="absolute inset-0 bg-white rounded-xl shadow-2xs border border-white/90 -z-10"
                      transition={{ type: 'spring', stiffness: 350, damping: 28 }}
                    />
                  )}
                  {lang === 'id' ? cat.labelId : cat.labelEn}
                </button>
              );
            })}
          </div>

          {/* Tags Chips */}
          <div className="flex flex-wrap items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
            <span className="text-xs font-semibold text-slate-400 mr-1 flex items-center gap-1 shrink-0">
              <Tag className="w-3 h-3" />
              Tags:
            </span>
            {allTags.slice(0, 5).map((tag) => (
              <button
                key={tag}
                onClick={() => setActiveTag(activeTag === tag ? null : tag)}
                className={`px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-colors cursor-pointer shrink-0 ${
                  activeTag === tag
                    ? 'bg-indigo-600 text-white'
                    : 'bg-white/60 backdrop-blur-xs text-slate-600 hover:bg-white/90 border border-white/60 shadow-2xs'
                }`}
              >
                #{tag}
              </button>
            ))}
            {activeTag && (
              <button
                onClick={() => setActiveTag(null)}
                className="text-[11px] font-bold text-rose-500 hover:underline ml-1 shrink-0 cursor-pointer"
              >
                Reset Tag
              </button>
            )}
          </div>
        </div>

        {/* Articles Responsive Grid */}
        {filteredPosts.length === 0 ? (
          <div className="p-12 text-center bg-white/50 backdrop-blur-md rounded-3xl border border-dashed border-white/90 text-slate-500 shadow-2xs">
            <BookOpen className="w-8 h-8 text-slate-400 mx-auto mb-2" />
            <p className="font-bold text-sm">
              {lang === 'id' ? 'Tidak ada artikel yang cocok dengan pencarian.' : 'No articles match your query.'}
            </p>
            <button
              onClick={() => {
                setSearchQuery('');
                setSelectedCategory('All');
                setActiveTag(null);
              }}
              className="mt-3 text-xs font-bold text-indigo-600 hover:underline cursor-pointer"
            >
              Reset semua filter
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
            {filteredPosts.map((post, pIdx) => {
              const isBookmarked = bookmarkedIds.includes(post.id);
              return (
                <motion.article
                  key={post.id}
                  layout
                  initial={{ opacity: 0, y: 30, scale: 0.97 }}
                  whileInView={{ opacity: 1, y: 0, scale: 1 }}
                  viewport={{ once: false, amount: 0.1 }}
                  transition={{ duration: 0.45, delay: (pIdx % 2) * 0.1 }}
                  whileHover={{ y: -6, transition: { duration: 0.2 } }}
                  id={`blog-card-${post.id}`}
                  className="group bg-white/60 backdrop-blur-lg rounded-3xl border border-white/80 shadow-2xs hover:shadow-xl hover:border-indigo-300/80 transition-all duration-300 flex flex-col justify-between overflow-hidden"
                >
                  <div className="space-y-4">
                    {/* Cover Image & Category Pill */}
                    <div
                      className="relative h-48 sm:h-56 overflow-hidden bg-slate-100 cursor-pointer"
                      onClick={() => setSelectedPost(post)}
                    >
                      <img
                        src={post.coverImage}
                        alt={post.title}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent" />

                      <div className="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between">
                        <span className="px-2.5 py-1 bg-white/90 backdrop-blur-md text-indigo-700 text-[11px] font-bold rounded-lg shadow-2xs border border-white/60">
                          {post.category}
                        </span>

                        <button
                          onClick={(e) => {
                            e.stopPropagation();
                            handleToggleBookmark(post.id);
                          }}
                          className={`p-2 rounded-full backdrop-blur-md transition-all cursor-pointer ${
                            isBookmarked
                              ? 'bg-indigo-600 text-white shadow-xs'
                              : 'bg-white/80 text-slate-700 hover:bg-white border border-white/80'
                          }`}
                          title={isBookmarked ? 'Hapus dari bookmark' : 'Simpan bookmark'}
                        >
                          <Bookmark className={`w-3.5 h-3.5 ${isBookmarked ? 'fill-white' : ''}`} />
                        </button>
                      </div>

                      {/* Meta on Cover */}
                      <div className="absolute bottom-3 left-3.5 right-3.5 flex items-center gap-3 text-[11px] text-white/90 font-medium">
                        <span className="flex items-center gap-1">
                          <Clock className="w-3 h-3 text-indigo-300" />
                          {post.readTime}
                        </span>
                        <span>•</span>
                        <span className="flex items-center gap-1">
                          <Calendar className="w-3 h-3 text-indigo-300" />
                          {post.publishedAt}
                        </span>
                      </div>
                    </div>

                    {/* Article Content */}
                    <div className="p-5 sm:p-6 pt-2 space-y-3">
                      <h3
                        onClick={() => setSelectedPost(post)}
                        className="text-lg sm:text-xl font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors leading-snug cursor-pointer"
                      >
                        {post.title}
                      </h3>

                      <p className="text-xs sm:text-sm text-slate-600 line-clamp-2 leading-relaxed">
                        {post.summary}
                      </p>

                      {/* Tags */}
                      <div className="flex flex-wrap gap-1.5 pt-1">
                        {post.tags.map((t, idx) => (
                          <span
                            key={idx}
                            onClick={() => setActiveTag(t)}
                            className="px-2.5 py-1 bg-white/70 backdrop-blur-xs text-slate-600 rounded-md text-[11px] font-semibold border border-white/60 shadow-2xs hover:bg-white/90 cursor-pointer"
                          >
                            #{t}
                          </span>
                        ))}
                      </div>
                    </div>
                  </div>

                  {/* Card Footer Strip */}
                  <div className="px-5 sm:px-6 pb-5 sm:pb-6 pt-3 border-t border-slate-200/50 flex items-center justify-between">
                    <button
                      onClick={() => setSelectedPost(post)}
                      className="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 cursor-pointer py-1"
                    >
                      <span>{lang === 'id' ? 'Baca Selengkapnya' : 'Read Article'}</span>
                      <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                    </button>

                    <div className="flex items-center gap-3 text-xs text-slate-400 font-medium">
                      <span className="flex items-center gap-1">
                        <Heart className="w-3.5 h-3.5 text-rose-500" />
                        {post.likes}
                      </span>
                      <span className="flex items-center gap-1">
                        <Eye className="w-3.5 h-3.5 text-slate-400" />
                        {post.views}
                      </span>
                    </div>
                  </div>
                </motion.article>
              );
            })}
          </div>
        )}
      </div>

      {/* Reader Modal */}
      <ArticleModal
        post={selectedPost}
        lang={lang}
        onClose={() => setSelectedPost(null)}
        isBookmarked={selectedPost ? bookmarkedIds.includes(selectedPost.id) : false}
        onToggleBookmark={handleToggleBookmark}
      />
    </section>
  );
};
