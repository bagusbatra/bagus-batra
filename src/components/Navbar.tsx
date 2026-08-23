import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  Menu,
  X,
  Sparkles,
  ArrowUpRight,
  Github,
  Linkedin,
  Twitter,
  Mail,
  FileDown,
  Globe,
  Code2
} from 'lucide-react';
import { Language } from '../types';
import { PERSONAL_INFO } from '../data/portfolioData';

interface NavbarProps {
  lang: Language;
  onToggleLang: () => void;
  onOpenContact: () => void;
  onDownloadCV: () => void;
}

export const Navbar: React.FC<NavbarProps> = ({
  lang,
  onToggleLang,
  onOpenContact,
  onDownloadCV,
}) => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [activeSection, setActiveSection] = useState('hero');
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      if (window.scrollY > 20) {
        setIsScrolled(true);
      } else {
        setIsScrolled(false);
      }

      // Determine active section
      const sections = ['hero', 'about', 'skills', 'projects', 'playground', 'experience', 'blog', 'contact'];
      const scrollPosition = window.scrollY + 120;

      for (const sectionId of sections) {
        const el = document.getElementById(sectionId);
        if (el) {
          const top = el.offsetTop;
          const height = el.offsetHeight;
          if (scrollPosition >= top && scrollPosition < top + height) {
            setActiveSection(sectionId);
            break;
          }
        }
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navLinks = [
    { id: 'hero', labelId: 'Beranda', labelEn: 'Home' },
    { id: 'about', labelId: 'Tentang', labelEn: 'About' },
    { id: 'skills', labelId: 'Keahlian', labelEn: 'Skills' },
    { id: 'projects', labelId: 'Proyek', labelEn: 'Projects' },
    { id: 'playground', labelId: 'UI Lab', labelEn: 'UI Lab' },
    { id: 'experience', labelId: 'Pengalaman', labelEn: 'Experience' },
    { id: 'blog', labelId: 'Artikel Blog', labelEn: 'Blog' },
    { id: 'contact', labelId: 'Kontak', labelEn: 'Contact' },
  ];

  const scrollTo = (id: string) => {
    setMobileMenuOpen(false);
    const element = document.getElementById(id);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <header
      id="main-navbar-header"
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        isScrolled
          ? 'py-3 bg-white/65 backdrop-blur-xl shadow-xs border-b border-white/80'
          : 'py-4 bg-white/40 backdrop-blur-md border-b border-white/50'
      }`}
    >
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between">
          {/* Logo / Brand Name */}
          <button
            id="brand-logo-btn"
            onClick={() => scrollTo('hero')}
            className="flex items-center gap-2.5 group text-left cursor-pointer focus:outline-none"
          >
            <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center text-white shadow-sm shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-200">
              <Code2 className="w-5 h-5" />
            </div>
            <div>
              <div className="flex items-center gap-1.5 font-bold text-slate-900 text-lg tracking-tight group-hover:text-indigo-600 transition-colors">
                <span>Bagus</span>
                <span className="text-indigo-600">.dev</span>
              </div>
              <p className="text-[11px] text-slate-700 hidden sm:block font-medium">
                {lang === 'id' ? 'Web Developer & Architect' : 'Web Developer & Architect'}
              </p>
            </div>
          </button>

          {/* Desktop Nav Links */}
          <nav
            id="desktop-navigation"
            aria-label="Navigasi Utama"
            className="hidden md:flex items-center gap-1 bg-white/50 p-1.5 rounded-full border border-white/80 shadow-2xs backdrop-blur-md relative"
          >
            {navLinks.map((link) => {
              const isActive = activeSection === link.id;
              return (
                <button
                  key={link.id}
                  id={`nav-link-${link.id}`}
                  onClick={() => scrollTo(link.id)}
                  className={`relative px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors duration-200 cursor-pointer ${
                    isActive
                      ? 'text-indigo-600 font-bold'
                      : 'text-slate-600 hover:text-slate-900 hover:bg-white/40'
                  }`}
                >
                  {isActive && (
                    <motion.div
                      layoutId="activeNavIndicator"
                      className="absolute inset-0 bg-white/95 rounded-full shadow-2xs border border-white/90 -z-10"
                      transition={{ type: 'spring', stiffness: 380, damping: 30 }}
                    />
                  )}
                  {lang === 'id' ? link.labelId : link.labelEn}
                </button>
              );
            })}
          </nav>

          {/* Action Buttons & Language Switcher */}
          <div className="hidden lg:flex items-center gap-2.5">
            {/* Language Toggle */}
            <motion.button
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              id="lang-toggle-btn"
              onClick={onToggleLang}
              className="flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:text-indigo-600 hover:bg-white/80 rounded-lg transition-colors border border-white/80 bg-white/50 backdrop-blur-md shadow-2xs cursor-pointer"
              title={lang === 'id' ? 'Ganti ke Bahasa Inggris' : 'Switch to Indonesian'}
            >
              <Globe className="w-3.5 h-3.5 text-slate-500" />
              <span className="uppercase">{lang}</span>
            </motion.button>

            {/* Quick CV Button */}
            <motion.button
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              id="nav-cv-download-btn"
              onClick={onDownloadCV}
              className="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:text-indigo-600 hover:bg-white/80 rounded-lg transition-colors border border-white/80 bg-white/50 backdrop-blur-md shadow-2xs cursor-pointer"
            >
              <FileDown className="w-3.5 h-3.5" />
              <span>CV</span>
            </motion.button>

            {/* Hire Me / Contact CTA */}
            <motion.button
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              id="nav-hire-me-btn"
              onClick={onOpenContact}
              className="flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold text-white bg-slate-900 hover:bg-indigo-600 rounded-lg transition-all duration-200 shadow-xs hover:shadow-indigo-500/20 cursor-pointer"
            >
              <Sparkles className="w-3.5 h-3.5 text-indigo-300" />
              <span>{lang === 'id' ? 'Rekrut Saya' : 'Hire Me'}</span>
            </motion.button>
          </div>

          {/* Mobile Menu & Quick Lang Toggle */}
          <div className="flex items-center gap-2 md:hidden">
            <motion.button
              whileTap={{ scale: 0.9 }}
              id="mobile-lang-btn"
              onClick={onToggleLang}
              className="p-2 text-xs font-bold text-slate-700 bg-white/70 backdrop-blur-md rounded-lg border border-white/80 shadow-2xs cursor-pointer"
            >
              <span className="uppercase">{lang}</span>
            </motion.button>

            <motion.button
              whileTap={{ scale: 0.9 }}
              id="mobile-menu-toggle-btn"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="p-2.5 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-white/80 bg-white/60 backdrop-blur-md border border-white/80 shadow-2xs focus:outline-none cursor-pointer"
              aria-label="Toggle menu"
            >
              {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </motion.button>
          </div>
        </div>
      </div>

      {/* Mobile Drawer Menu */}
      <AnimatePresence>
        {mobileMenuOpen && (
          <motion.div
            id="mobile-nav-drawer"
            initial={{ opacity: 0, height: 0, y: -10 }}
            animate={{ opacity: 1, height: 'auto', y: 0 }}
            exit={{ opacity: 0, height: 0, y: -10 }}
            transition={{ duration: 0.25, ease: 'easeOut' }}
            className="md:hidden bg-white/90 backdrop-blur-2xl border-b border-white/80 px-4 py-5 shadow-xl max-h-[80vh] overflow-y-auto"
          >
            <div className="flex flex-col space-y-1">
              {navLinks.map((link, idx) => {
                const isActive = activeSection === link.id;
                return (
                  <motion.button
                    key={link.id}
                    initial={{ opacity: 0, x: -12 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ delay: idx * 0.04 }}
                    id={`mobile-link-${link.id}`}
                    onClick={() => scrollTo(link.id)}
                    className={`flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors cursor-pointer ${
                      isActive
                        ? 'bg-white text-indigo-600 font-bold border border-indigo-100 shadow-2xs'
                        : 'text-slate-700 hover:bg-white/60'
                    }`}
                  >
                    <span>{lang === 'id' ? link.labelId : link.labelEn}</span>
                    {isActive && <div className="w-2 h-2 rounded-full bg-indigo-600 shadow-xs shadow-indigo-500/50" />}
                  </motion.button>
                );
              })}
            </div>

            <div className="pt-4 mt-3 border-t border-slate-200/60 flex flex-col gap-2.5">
              <button
                id="mobile-cv-btn"
                onClick={() => {
                  setMobileMenuOpen(false);
                  onDownloadCV();
                }}
                className="w-full flex items-center justify-center gap-2 py-3 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl shadow-2xs cursor-pointer"
              >
                <FileDown className="w-4 h-4 text-indigo-600" />
                <span>{lang === 'id' ? 'Unduh Resume / CV (PDF)' : 'Download CV (PDF)'}</span>
              </button>
              <button
                id="mobile-contact-cta-btn"
                onClick={() => {
                  setMobileMenuOpen(false);
                  onOpenContact();
                }}
                className="w-full flex items-center justify-center gap-2 py-3 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-600/20 cursor-pointer"
              >
                <Sparkles className="w-4 h-4 text-indigo-200" />
                <span>{lang === 'id' ? 'Mulai Diskusi Proyek' : 'Start Project Discussion'}</span>
              </button>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </header>
  );
};
