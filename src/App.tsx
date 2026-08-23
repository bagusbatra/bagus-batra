/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import React, { useState, useEffect } from 'react';
import { motion, useScroll, useSpring, AnimatePresence } from 'motion/react';
import { ArrowUp, Sparkles, MessageSquare } from 'lucide-react';
import { Navbar } from './components/Navbar';
import { Hero } from './components/Hero';
import { AboutSection } from './components/AboutSection';
import { ProjectsSection } from './components/ProjectsSection';
import { InteractivePlayground } from './components/InteractivePlayground';
import { ExperienceTimeline } from './components/ExperienceTimeline';
import { BlogSection } from './components/BlogSection';
import { TestimonialsSection } from './components/TestimonialsSection';
import { ContactSection } from './components/ContactSection';
import { Footer } from './components/Footer';
import { CVModal } from './components/CVModal';
import { Language } from './types';

export default function App() {
  const [lang, setLang] = useState<Language>('id');
  const [isCVModalOpen, setIsCVModalOpen] = useState(false);
  const [showFloatingTop, setShowFloatingTop] = useState(false);

  // Scroll Progress Bar Spring
  const { scrollYProgress, scrollY } = useScroll();
  const scaleX = useSpring(scrollYProgress, {
    stiffness: 100,
    damping: 30,
    restDelta: 0.001,
  });

  useEffect(() => {
    return scrollY.on('change', (latest) => {
      if (latest > 400) {
        setShowFloatingTop(true);
      } else {
        setShowFloatingTop(false);
      }
    });
  }, [scrollY]);

  const toggleLanguage = () => {
    setLang((prev) => (prev === 'id' ? 'en' : 'id'));
  };

  const handleOpenContact = () => {
    const contactEl = document.getElementById('contact');
    if (contactEl) {
      contactEl.scrollIntoView({ behavior: 'smooth' });
    }
  };

  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  return (
    <div className="min-h-screen bg-slate-50/80 text-slate-800 flex flex-col selection:bg-indigo-500/15 selection:text-indigo-900 font-sans relative overflow-x-hidden">
      {/* Top Scroll Progress Indicator */}
      <motion.div
        id="scroll-progress-bar"
        className="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-600 via-purple-500 to-blue-500 origin-left z-50 shadow-sm shadow-indigo-500/30"
        style={{ scaleX }}
      />

      {/* Ambient background animated light gradients for living frosted glass diffusion */}
      <div className="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <motion.div
          animate={{
            x: [0, 40, -30, 0],
            y: [0, -50, 30, 0],
            scale: [1, 1.15, 0.95, 1],
          }}
          transition={{
            duration: 18,
            repeat: Infinity,
            ease: 'easeInOut',
          }}
          className="absolute -top-40 -right-40 w-[650px] h-[650px] bg-gradient-to-br from-indigo-200/40 via-purple-100/30 to-transparent rounded-full blur-3xl"
        />
        <motion.div
          animate={{
            x: [0, -50, 30, 0],
            y: [0, 40, -40, 0],
            scale: [1, 1.1, 0.9, 1],
          }}
          transition={{
            duration: 22,
            repeat: Infinity,
            ease: 'easeInOut',
            delay: 2,
          }}
          className="absolute top-[35%] -left-40 w-[600px] h-[600px] bg-gradient-to-tr from-blue-200/35 via-cyan-100/25 to-transparent rounded-full blur-3xl"
        />
        <motion.div
          animate={{
            x: [0, 35, -45, 0],
            y: [0, -35, 45, 0],
            scale: [1, 1.2, 0.95, 1],
          }}
          transition={{
            duration: 20,
            repeat: Infinity,
            ease: 'easeInOut',
            delay: 4,
          }}
          className="absolute top-[70%] -right-32 w-[550px] h-[550px] bg-gradient-to-tl from-indigo-100/40 via-violet-100/30 to-transparent rounded-full blur-3xl"
        />
      </div>

      {/* Sticky Glass Navbar */}
      <Navbar
        lang={lang}
        onToggleLang={toggleLanguage}
        onOpenContact={handleOpenContact}
        onDownloadCV={() => setIsCVModalOpen(true)}
      />

      {/* Main Content Sections */}
      <main className="flex-1 relative z-10">
        <Hero
          lang={lang}
          onOpenContact={handleOpenContact}
          onDownloadCV={() => setIsCVModalOpen(true)}
        />

        <AboutSection lang={lang} />

        <ProjectsSection lang={lang} />

        <InteractivePlayground lang={lang} />

        <ExperienceTimeline lang={lang} />

        <BlogSection lang={lang} />

        <TestimonialsSection lang={lang} />

        <ContactSection lang={lang} />
      </main>

      {/* Floating Quick Action Widget (Back to Top & Quick Contact) */}
      <AnimatePresence>
        {showFloatingTop && (
          <motion.div
            initial={{ opacity: 0, scale: 0.8, y: 20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.8, y: 20 }}
            transition={{ type: 'spring', stiffness: 300, damping: 25 }}
            className="fixed bottom-6 right-6 z-40 flex flex-col items-center gap-2.5"
          >
            <motion.button
              whileHover={{ scale: 1.08 }}
              whileTap={{ scale: 0.92 }}
              onClick={handleOpenContact}
              className="p-3.5 rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 transition-colors cursor-pointer border border-indigo-400/40 backdrop-blur-md flex items-center justify-center group"
              title={lang === 'id' ? 'Kirim Pesan / Kontak' : 'Contact Me'}
            >
              <MessageSquare className="w-5 h-5 group-hover:scale-110 transition-transform" />
            </motion.button>

            <motion.button
              id="floating-back-to-top"
              whileHover={{ scale: 1.08 }}
              whileTap={{ scale: 0.92 }}
              onClick={scrollToTop}
              className="p-3.5 rounded-2xl bg-white/80 hover:bg-white text-slate-700 shadow-lg shadow-slate-300/40 border border-white/90 backdrop-blur-md transition-colors cursor-pointer flex items-center justify-center group"
              title={lang === 'id' ? 'Kembali ke atas' : 'Back to top'}
            >
              <ArrowUp className="w-5 h-5 text-indigo-600 group-hover:-translate-y-0.5 transition-transform" />
            </motion.button>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Footer */}
      <Footer lang={lang} />

      {/* CV Modal */}
      <CVModal
        isOpen={isCVModalOpen}
        lang={lang}
        onClose={() => setIsCVModalOpen(false)}
      />
    </div>
  );
}

