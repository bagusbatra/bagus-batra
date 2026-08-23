import React from 'react';
import { motion } from 'motion/react';
import {
  Code2,
  Heart,
  ArrowUp,
  Github,
  Linkedin,
  Twitter,
  Mail,
  Sparkles
} from 'lucide-react';
import { PERSONAL_INFO } from '../data/portfolioData';
import { Language } from '../types';

interface FooterProps {
  lang: Language;
}

export const Footer: React.FC<FooterProps> = ({ lang }) => {
  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <footer id="main-footer" className="bg-slate-900/90 backdrop-blur-xl text-slate-400 py-16 border-t border-slate-800/80 relative">
      <motion.div
        initial={{ opacity: 0, y: 25 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: false, amount: 0.15 }}
        transition={{ duration: 0.6 }}
        className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12"
      >
        <div className="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
          {/* Brand Col */}
          <div className="md:col-span-5 space-y-4">
            <div className="flex items-center gap-2.5">
              <div className="w-9 h-9 rounded-xl bg-indigo-600/90 backdrop-blur-md flex items-center justify-center text-white shadow-sm shadow-indigo-500/30 border border-indigo-400/30">
                <Code2 className="w-5 h-5" />
              </div>
              <div className="font-bold text-white text-lg tracking-tight">
                <span>Bagus</span>
                <span className="text-indigo-400">.dev</span>
              </div>
            </div>
            <p className="text-xs text-slate-400 max-w-sm leading-relaxed">
              {lang === 'id'
                ? 'Senior Web Developer & Technical Writer berfokus pada arsitektur frontend skala enterprise, optimasi performa web, dan antarmuka elegan.'
                : 'Senior Web Developer crafting performant, accessible, and elegant web solutions for global products.'}
            </p>
            <div className="text-xs text-slate-500 font-mono">
              Jakarta / Bali, ID • {new Date().getFullYear()}
            </div>
          </div>

          {/* Quick Nav Links */}
          <div className="md:col-span-4 grid grid-cols-2 gap-4 text-xs">
            <div className="space-y-2.5">
              <div className="font-bold text-slate-200 uppercase tracking-wider text-[11px]">
                Navigasi
              </div>
              <ul className="space-y-2">
                <li><a href="#hero" className="hover:text-indigo-400 transition-colors">Beranda</a></li>
                <li><a href="#about" className="hover:text-indigo-400 transition-colors">Tentang & Prinsip</a></li>
                <li><a href="#skills" className="hover:text-indigo-400 transition-colors">Tech Stack</a></li>
                <li><a href="#projects" className="hover:text-indigo-400 transition-colors">Showcase Proyek</a></li>
              </ul>
            </div>
            <div className="space-y-2.5">
              <div className="font-bold text-slate-200 uppercase tracking-wider text-[11px]">
                Eksplorasi
              </div>
              <ul className="space-y-2">
                <li><a href="#playground" className="hover:text-indigo-400 transition-colors">Interactive UI Lab</a></li>
                <li><a href="#experience" className="hover:text-indigo-400 transition-colors">Pengalaman</a></li>
                <li><a href="#blog" className="hover:text-indigo-400 transition-colors">Artikel & Blog</a></li>
                <li><a href="#contact" className="hover:text-indigo-400 transition-colors">Kontak & Brief</a></li>
              </ul>
            </div>
          </div>

          {/* Back to top & Tech badge */}
          <div className="md:col-span-3 flex flex-col items-start md:items-end justify-between space-y-4">
            <button
              id="back-to-top-btn"
              onClick={scrollToTop}
              className="flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/15 backdrop-blur-md text-slate-200 text-xs font-bold rounded-xl border border-white/10 transition-all cursor-pointer shadow-sm hover:scale-105"
            >
              <ArrowUp className="w-3.5 h-3.5" />
              <span>{lang === 'id' ? 'Kembali ke Atas' : 'Back to Top'}</span>
            </button>

            <div className="text-[11px] text-slate-500 md:text-right">
              Built with React 19, TypeScript, Tailwind CSS & Motion.
            </div>
          </div>
        </div>

        {/* Bottom copyright line */}
        <div className="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
          <div>
            © {new Date().getFullYear()} Bagus Batra. All rights reserved.
          </div>
          <div className="flex items-center gap-1">
            <span>Crafted with</span>
            <Heart className="w-3.5 h-3.5 text-rose-500 fill-rose-500" />
            <span>and obsessive attention to detail.</span>
          </div>
        </div>
      </motion.div>
    </footer>
  );
};
