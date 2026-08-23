import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  X,
  Clock,
  Calendar,
  Eye,
  Heart,
  Bookmark,
  Share2,
  Copy,
  Check,
  Twitter,
  Linkedin,
  MessageSquare,
  Send,
  Sparkles,
  ArrowLeft,
  BookOpen
} from 'lucide-react';
import confetti from 'canvas-confetti';
import { BlogPost, Language, CommentItem } from '../types';

interface ArticleModalProps {
  post: BlogPost | null;
  lang: Language;
  onClose: () => void;
  isBookmarked: boolean;
  onToggleBookmark: (postId: string) => void;
}

export const ArticleModal: React.FC<ArticleModalProps> = ({
  post,
  lang,
  onClose,
  isBookmarked,
  onToggleBookmark,
}) => {
  const [likes, setLikes] = useState(0);
  const [hasLiked, setHasLiked] = useState(false);
  const [copiedCodeId, setCopiedCodeId] = useState<string | null>(null);
  const [copiedLink, setCopiedLink] = useState(false);
  const [comments, setComments] = useState<CommentItem[]>([]);
  const [newCommentName, setNewCommentName] = useState('');
  const [newCommentText, setNewCommentText] = useState('');

  useEffect(() => {
    if (post) {
      setLikes(post.likes);
      setHasLiked(false);
      // Load saved comments for this post
      const saved = localStorage.getItem(`comments_${post.id}`);
      if (saved) {
        try {
          setComments(JSON.parse(saved));
        } catch (e) {
          setComments([]);
        }
      } else {
        // Initial sample comments
        setComments([
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
          }
        ]);
      }
    }
  }, [post]);

  if (!post) return null;

  const handleLike = () => {
    if (!hasLiked) {
      setLikes((prev) => prev + 1);
      setHasLiked(true);
      confetti({
        particleCount: 35,
        spread: 60,
        origin: { y: 0.8 },
        colors: ['#f43f5e', '#ec4899', '#6366f1']
      });
    } else {
      setLikes((prev) => prev - 1);
      setHasLiked(false);
    }
  };

  const handleCopyCode = (code: string, id: string) => {
    navigator.clipboard.writeText(code);
    setCopiedCodeId(id);
    setTimeout(() => setCopiedCodeId(null), 2000);
  };

  const handleShare = (platform: 'twitter' | 'linkedin' | 'whatsapp' | 'copy') => {
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
      setCopiedLink(true);
      setTimeout(() => setCopiedLink(false), 2000);
    }
  };

  const handleAddComment = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newCommentName.trim() || !newCommentText.trim()) return;

    const newComment: CommentItem = {
      id: Date.now().toString(),
      author: newCommentName.trim(),
      avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80',
      date: lang === 'id' ? 'Baru saja' : 'Just now',
      text: newCommentText.trim(),
    };

    const updated = [newComment, ...comments];
    setComments(updated);
    localStorage.setItem(`comments_${post.id}`, JSON.stringify(updated));
    setNewCommentText('');
    setNewCommentName('');
  };

  return (
    <AnimatePresence>
      <div
        id="article-modal-backdrop"
        className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
        onClick={onClose}
      >
        <motion.article
          id="article-reader-container"
          initial={{ opacity: 0, scale: 0.96, y: 20 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          exit={{ opacity: 0, scale: 0.96, y: 20 }}
          transition={{ duration: 0.25 }}
          className="relative w-full max-w-4xl bg-white/95 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/80 overflow-hidden my-6 max-h-[92vh] flex flex-col"
          onClick={(e) => e.stopPropagation()}
        >
          {/* Header Bar */}
          <div className="px-6 py-4 border-b border-slate-200/80 bg-white/75 backdrop-blur-md flex items-center justify-between sticky top-0 z-20">
            <button
              onClick={onClose}
              className="flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-indigo-600 cursor-pointer"
            >
              <ArrowLeft className="w-4 h-4" />
              <span>{lang === 'id' ? 'Kembali ke Blog' : 'Back to Blog'}</span>
            </button>

            {/* Quick Action Buttons */}
            <div className="flex items-center gap-2">
              <button
                onClick={handleLike}
                className={`flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer ${
                  hasLiked
                    ? 'bg-rose-50 text-rose-600 border border-rose-200'
                    : 'bg-slate-100 text-slate-700 hover:bg-rose-50 hover:text-rose-600'
                }`}
              >
                <Heart className={`w-3.5 h-3.5 ${hasLiked ? 'fill-rose-600 text-rose-600' : ''}`} />
                <span>{likes}</span>
              </button>

              <button
                onClick={() => onToggleBookmark(post.id)}
                className={`p-2 rounded-full text-xs font-bold transition-all cursor-pointer ${
                  isBookmarked
                    ? 'bg-indigo-50 text-indigo-600 border border-indigo-200'
                    : 'bg-slate-100 text-slate-700 hover:bg-indigo-50 hover:text-indigo-600'
                }`}
                title={isBookmarked ? 'Tersimpan di bookmark' : 'Simpan ke bookmark'}
              >
                <Bookmark className={`w-3.5 h-3.5 ${isBookmarked ? 'fill-indigo-600 text-indigo-600' : ''}`} />
              </button>

              <button
                onClick={() => handleShare('copy')}
                className="flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors cursor-pointer"
              >
                {copiedLink ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Copy className="w-3.5 h-3.5" />}
                <span>{copiedLink ? (lang === 'id' ? 'Link Tersalin!' : 'Copied!') : (lang === 'id' ? 'Salin Link' : 'Copy Link')}</span>
              </button>

              <button
                onClick={onClose}
                className="p-1.5 rounded-full text-slate-400 hover:text-slate-800 hover:bg-slate-100 cursor-pointer ml-1"
                aria-label="Tutup"
              >
                <X className="w-5 h-5" />
              </button>
            </div>
          </div>

          {/* Reader Scrollable Content */}
          <div className="overflow-y-auto flex-1 p-6 sm:p-10 space-y-8">
            {/* Post Metadata & Heading */}
            <div className="space-y-4 max-w-3xl">
              <div className="flex flex-wrap items-center gap-2">
                <span className="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold uppercase tracking-wider">
                  {post.category}
                </span>
                <span className="flex items-center gap-1 text-xs text-slate-500 font-medium">
                  <Clock className="w-3.5 h-3.5" />
                  {post.readTime}
                </span>
                <span className="flex items-center gap-1 text-xs text-slate-500 font-medium">
                  <Calendar className="w-3.5 h-3.5" />
                  {post.publishedAt}
                </span>
              </div>

              <h1 className="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                {post.title}
              </h1>

              {/* Author Strip */}
              <div className="flex items-center gap-3 pt-2">
                <img
                  src={post.author.avatar}
                  alt={post.author.name}
                  className="w-10 h-10 rounded-full object-cover border border-slate-200"
                />
                <div>
                  <div className="text-sm font-bold text-slate-900">{post.author.name}</div>
                  <div className="text-xs text-slate-500">{post.author.role}</div>
                </div>
              </div>
            </div>

            {/* Article Cover Image */}
            <div className="rounded-2xl overflow-hidden max-h-96 border border-slate-200 shadow-xs">
              <img
                src={post.coverImage}
                alt={post.title}
                className="w-full h-full object-cover"
              />
            </div>

            {/* Post Summary Lead */}
            <div className="p-5 bg-slate-50 rounded-2xl border-l-4 border-indigo-600 text-slate-700 text-sm sm:text-base font-medium leading-relaxed">
              {post.summary}
            </div>

            {/* Article Content Sections */}
            <div className="space-y-8 text-slate-700 leading-relaxed text-sm sm:text-base">
              {post.sections.map((sec, idx) => (
                <div key={idx} className="space-y-3.5">
                  {sec.heading && (
                    <h3 className="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight pt-2">
                      {sec.heading}
                    </h3>
                  )}
                  <p className="text-slate-600 leading-relaxed">
                    {sec.body}
                  </p>

                  {/* Code Snippet Block */}
                  {sec.codeSnippet && (
                    <div className="rounded-2xl overflow-hidden bg-slate-900 border border-slate-800 shadow-md">
                      <div className="px-4 py-2.5 bg-slate-950 flex items-center justify-between border-b border-slate-800 text-xs font-mono text-slate-400">
                        <span>{sec.codeSnippet.filename || `code.${sec.codeSnippet.language}`}</span>
                        <button
                          onClick={() => handleCopyCode(sec.codeSnippet!.code, `snippet-${idx}`)}
                          className="flex items-center gap-1 text-slate-400 hover:text-white transition-colors cursor-pointer"
                        >
                          {copiedCodeId === `snippet-${idx}` ? (
                            <Check className="w-3.5 h-3.5 text-emerald-400" />
                          ) : (
                            <Copy className="w-3.5 h-3.5" />
                          )}
                          <span className="text-[11px] font-bold">
                            {copiedCodeId === `snippet-${idx}` ? 'Copied' : 'Copy'}
                          </span>
                        </button>
                      </div>
                      <pre className="p-4 text-xs font-mono text-slate-200 overflow-x-auto leading-relaxed">
                        <code>{sec.codeSnippet.code}</code>
                      </pre>
                    </div>
                  )}

                  {/* Pro Tip Box */}
                  {sec.tip && (
                    <div className="p-4 rounded-xl bg-amber-50/80 border border-amber-200/80 text-amber-900 text-xs sm:text-sm font-medium flex items-start gap-2.5">
                      <Sparkles className="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                      <span>{sec.tip}</span>
                    </div>
                  )}
                </div>
              ))}
            </div>

            {/* Social Share Bar */}
            <div className="pt-8 border-t border-slate-200 space-y-3">
              <h4 className="text-xs font-bold text-slate-400 uppercase tracking-wider">
                {lang === 'id' ? 'Bagikan Artikel Ini:' : 'Share This Article:'}
              </h4>
              <div className="flex flex-wrap items-center gap-2">
                <button
                  onClick={() => handleShare('twitter')}
                  className="flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-900 hover:text-white rounded-xl text-xs font-semibold text-slate-700 transition-all cursor-pointer"
                >
                  <Twitter className="w-4 h-4" />
                  <span>X (Twitter)</span>
                </button>
                <button
                  onClick={() => handleShare('linkedin')}
                  className="flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-[#0A66C2] hover:text-white rounded-xl text-xs font-semibold text-slate-700 transition-all cursor-pointer"
                >
                  <Linkedin className="w-4 h-4" />
                  <span>LinkedIn</span>
                </button>
                <button
                  onClick={() => handleShare('whatsapp')}
                  className="flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-emerald-600 hover:text-white rounded-xl text-xs font-semibold text-slate-700 transition-all cursor-pointer"
                >
                  <MessageSquare className="w-4 h-4" />
                  <span>WhatsApp</span>
                </button>
              </div>
            </div>

            {/* Comments Section */}
            <div className="pt-8 border-t border-slate-200 space-y-6">
              <div className="flex items-center justify-between">
                <h4 className="text-lg font-bold text-slate-900 flex items-center gap-2">
                  <MessageSquare className="w-5 h-5 text-indigo-600" />
                  <span>{lang === 'id' ? 'Diskusi & Tanggapan' : 'Discussion & Comments'}</span>
                  <span className="text-xs px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full">
                    {comments.length}
                  </span>
                </h4>
              </div>

              {/* Add Comment Form */}
              <form onSubmit={handleAddComment} className="p-4.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <input
                    type="text"
                    required
                    placeholder={lang === 'id' ? 'Nama Anda...' : 'Your Name...'}
                    value={newCommentName}
                    onChange={(e) => setNewCommentName(e.target.value)}
                    className="px-3.5 py-2 bg-white rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-indigo-500"
                  />
                </div>
                <textarea
                  required
                  rows={3}
                  placeholder={lang === 'id' ? 'Tuliskan pandangan atau pertanyaan Anda seputar topik ini...' : 'Write your thoughts or questions...'}
                  value={newCommentText}
                  onChange={(e) => setNewCommentText(e.target.value)}
                  className="w-full px-3.5 py-2 bg-white rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-indigo-500 resize-none"
                />
                <button
                  type="submit"
                  className="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-xs"
                >
                  <Send className="w-3.5 h-3.5" />
                  <span>{lang === 'id' ? 'Kirim Komentar' : 'Post Comment'}</span>
                </button>
              </form>

              {/* Comments List */}
              <div className="space-y-3">
                {comments.map((c) => (
                  <div key={c.id} className="p-4 bg-white rounded-2xl border border-slate-200/80 space-y-1.5">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <img src={c.avatar} alt={c.author} className="w-7 h-7 rounded-full object-cover" />
                        <span className="text-xs font-bold text-slate-900">{c.author}</span>
                      </div>
                      <span className="text-[11px] text-slate-400">{c.date}</span>
                    </div>
                    <p className="text-xs text-slate-600 leading-relaxed pl-9">
                      {c.text}
                    </p>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </motion.article>
      </div>
    </AnimatePresence>
  );
};
