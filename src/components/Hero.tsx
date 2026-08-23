import React from 'react';
import { motion } from 'motion/react';
import {
  Sparkles,
  ArrowRight,
  FileDown,
  Terminal,
  CheckCircle2,
  Cpu,
  Layers,
  Zap,
  Globe,
  MapPin,
  TrendingUp,
  MessageSquare
} from 'lucide-react';
import { PERSONAL_INFO } from '../data/portfolioData';
import { Language } from '../types';
import { SocialBar } from './SocialBar';

interface HeroProps {
  lang: Language;
  onOpenContact: () => void;
  onDownloadCV: () => void;
}

export const Hero: React.FC<HeroProps> = ({ lang, onOpenContact, onDownloadCV }) => {
  const scrollToProjects = () => {
    const el = document.getElementById('projects');
    if (el) el.scrollIntoView({ behavior: 'smooth' });
  };

  return (
    <section
      id="hero"
      className="relative pt-28 pb-20 md:pt-36 md:pb-28 overflow-hidden bg-gradient-to-b from-slate-50/60 via-white to-slate-50/40"
    >
      {/* Subtle background ambient mesh */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-[600px] pointer-events-none overflow-hidden opacity-60">
        <div className="absolute -top-32 left-1/4 w-96 h-96 bg-indigo-200/40 rounded-full blur-3xl" />
        <div className="absolute top-10 right-1/4 w-80 h-80 bg-blue-200/30 rounded-full blur-3xl" />
        <div className="absolute top-48 left-1/2 w-72 h-72 bg-amber-200/20 rounded-full blur-3xl" />
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
          {/* Left Column: Heading & Value Proposition */}
          <div className="lg:col-span-7 space-y-7">
            {/* Status Beacon */}
            <motion.div
              initial={{ opacity: 0, y: 20, scale: 0.95 }}
              whileInView={{ opacity: 1, y: 0, scale: 1 }}
              viewport={{ once: false, amount: 0.2 }}
              transition={{ duration: 0.5 }}
              className="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/70 backdrop-blur-md border border-white/90 shadow-2xs text-xs font-semibold text-slate-800"
            >
              <span className="relative flex h-2.5 w-2.5">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75" />
                <span className="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500" />
              </span>
              <span>
                {lang === 'id'
                  ? 'Tersedia untuk Proyek Baru & Konsultasi'
                  : 'Available for New Projects & Contracts'}
              </span>
            </motion.div>

            {/* Main Headline */}
            <motion.div
              initial={{ opacity: 0, y: 25 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: false, amount: 0.2 }}
              transition={{ duration: 0.6, delay: 0.05 }}
              className="space-y-3"
            >
              <h1 className="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.15] sm:leading-[1.12]">
                {lang === 'id' ? (
                  <>
                    Merancang Web <span className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-500">Cepat, Elegan</span> & Intuitif.
                  </>
                ) : (
                  <>
                    Engineering <span className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-500">Fast, Elegant</span> & Scalable Web Apps.
                  </>
                )}
              </h1>
              <p className="text-base sm:text-lg lg:text-xl text-slate-600 font-normal leading-relaxed max-w-2xl">
                {lang === 'id' ? PERSONAL_INFO.taglineId : PERSONAL_INFO.taglineEn}
              </p>
            </motion.div>

            {/* Quick Experience Pills */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: false, amount: 0.2 }}
              transition={{ duration: 0.6, delay: 0.1 }}
              className="flex flex-wrap items-center gap-2 text-xs font-medium text-slate-600"
            >
              <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/60 backdrop-blur-md text-slate-700 border border-white/80 shadow-2xs">
                <MapPin className="w-3.5 h-3.5 text-indigo-500" />
                {PERSONAL_INFO.location}
              </span>
              <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/60 backdrop-blur-md text-slate-700 border border-white/80 shadow-2xs">
                <Zap className="w-3.5 h-3.5 text-amber-500" />
                {lang === 'id' ? 'Spesialis React 19 & Next.js' : 'React 19 & Next.js Specialist'}
              </span>
              <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/60 backdrop-blur-md text-slate-700 border border-white/80 shadow-2xs">
                <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500" />
                Lighthouse 95+ Score
              </span>
            </motion.div>

            {/* CTA Buttons */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: false, amount: 0.2 }}
              transition={{ duration: 0.6, delay: 0.15 }}
              className="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3 pt-2"
            >
              <motion.button
                whileHover={{ scale: 1.03, y: -2 }}
                whileTap={{ scale: 0.97 }}
                id="hero-explore-projects-btn"
                onClick={scrollToProjects}
                className="group flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-bold text-sm shadow-md hover:shadow-indigo-500/25 transition-all duration-300 cursor-pointer"
              >
                <span>{lang === 'id' ? 'Jelajahi Showcase Proyek' : 'Explore Featured Projects'}</span>
                <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
              </motion.button>

              <motion.button
                whileHover={{ scale: 1.03, y: -2 }}
                whileTap={{ scale: 0.97 }}
                id="hero-contact-btn"
                onClick={onOpenContact}
                className="flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-white/80 hover:bg-white text-slate-800 font-bold text-sm border border-white/90 shadow-2xs hover:border-indigo-200 backdrop-blur-md transition-all cursor-pointer"
              >
                <MessageSquare className="w-4 h-4 text-indigo-600" />
                <span>{lang === 'id' ? 'Diskusikan Proyek' : 'Let’s Talk'}</span>
              </motion.button>

              <motion.button
                whileHover={{ scale: 1.03, y: -2 }}
                whileTap={{ scale: 0.97 }}
                id="hero-cv-btn"
                onClick={onDownloadCV}
                className="flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl bg-indigo-50/80 hover:bg-indigo-100 text-indigo-700 font-bold text-sm border border-indigo-200/80 backdrop-blur-md transition-colors cursor-pointer"
                title="Download CV format PDF"
              >
                <FileDown className="w-4 h-4" />
                <span>{lang === 'id' ? 'Resume / CV' : 'Download CV'}</span>
              </motion.button>
            </motion.div>

            {/* Social Media Links Bar */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: false, amount: 0.2 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="pt-3 border-t border-slate-200/60"
            >
              <p className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2.5">
                {lang === 'id' ? 'Koneksi & Media Sosial' : 'Connect on Social Platforms'}
              </p>
              <SocialBar lang={lang} variant="horizontal" />
            </motion.div>
          </div>

          {/* Right Column: Interactive Developer Profile & Code Card */}
          <motion.div
            initial={{ opacity: 0, scale: 0.92, y: 30 }}
            whileInView={{ opacity: 1, scale: 1, y: 0 }}
            viewport={{ once: false, amount: 0.2 }}
            transition={{ duration: 0.7, delay: 0.15 }}
            className="lg:col-span-5 relative"
          >
            {/* Supporting Floating Tech Badges */}
            <motion.div
              animate={{ y: [0, -8, 0] }}
              transition={{ duration: 4, repeat: Infinity, ease: 'easeInOut' }}
              className="hidden sm:flex absolute -top-4 -left-4 z-20 items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/90 backdrop-blur-md border border-white/90 shadow-md text-xs font-bold text-indigo-700"
            >
              <Sparkles className="w-3.5 h-3.5 text-amber-500" />
              <span>React 19 Ready</span>
            </motion.div>

            <motion.div
              animate={{ y: [0, 8, 0] }}
              transition={{ duration: 5, repeat: Infinity, ease: 'easeInOut', delay: 1 }}
              className="hidden sm:flex absolute -bottom-3 -right-3 z-20 items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900/90 text-white backdrop-blur-md border border-slate-800 shadow-md text-xs font-bold font-mono"
            >
              <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
              <span>TypeScript 5.8</span>
            </motion.div>

            <div className="relative mx-auto max-w-md lg:max-w-none">
              {/* Outer Frosted Glass Card */}
              <div className="p-1.5 rounded-3xl bg-white/50 backdrop-blur-xl border border-white/80 shadow-lg shadow-slate-200/50">
                <div className="bg-white/70 backdrop-blur-lg rounded-[22px] p-5 sm:p-7 space-y-5 sm:space-y-6 border border-white/90">
                  {/* Top Bar with Developer Info */}
                  <div className="flex items-center justify-between pb-4 border-b border-slate-200/50">
                    <div className="flex items-center gap-3.5">
                      <div className="relative">
                        <img
                          src={PERSONAL_INFO.avatar}
                          alt={PERSONAL_INFO.name}
                          className="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl object-cover border-2 border-white shadow-sm"
                        />
                        <div className="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white" />
                      </div>
                      <div>
                        <h3 className="font-extrabold text-slate-900 text-base">
                          {PERSONAL_INFO.name}
                        </h3>
                        <p className="text-xs text-indigo-600 font-semibold">
                          {lang === 'id' ? PERSONAL_INFO.titleId : PERSONAL_INFO.titleEn}
                        </p>
                      </div>
                    </div>
                    <div className="px-2.5 py-1 rounded-lg bg-white/70 backdrop-blur-md border border-white/80 text-[11px] font-mono text-slate-600 font-bold">
                      v2026.1
                    </div>
                  </div>

                  {/* Micro Terminal Snippet */}
                  <div className="bg-slate-900/90 backdrop-blur-md text-slate-200 rounded-2xl p-3.5 sm:p-4 font-mono text-xs shadow-inner space-y-2 border border-slate-800 overflow-x-auto">
                    <div className="flex items-center justify-between pb-2 border-b border-slate-800 text-[11px] text-slate-400 min-w-[240px]">
                      <div className="flex items-center gap-1.5">
                        <div className="w-2.5 h-2.5 rounded-full bg-rose-500/80" />
                        <div className="w-2.5 h-2.5 rounded-full bg-amber-500/80" />
                        <div className="w-2.5 h-2.5 rounded-full bg-emerald-500/80" />
                      </div>
                      <span>developer.config.ts</span>
                    </div>
                    <div className="space-y-1 text-slate-300 text-[11px] leading-relaxed pt-1 min-w-[240px]">
                      <p className="text-indigo-400">const <span className="text-white font-semibold">developer</span> = &#123;</p>
                      <p className="pl-4">name: <span className="text-emerald-400">'{PERSONAL_INFO.name}'</span>,</p>
                      <p className="pl-4">stack: [<span className="text-amber-300">'React 19'</span>, <span className="text-amber-300">'Next.js'</span>, <span className="text-amber-300">'TypeScript'</span>],</p>
                      <p className="pl-4">focus: <span className="text-emerald-400">'High-Performance UX'</span>,</p>
                      <p className="pl-4">coffeeLevel: <span className="text-purple-300">100</span>,</p>
                      <p className="text-indigo-400">&#125;;</p>
                    </div>
                  </div>

                  {/* Key Highlights Grid */}
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div className="p-3 sm:p-3.5 bg-white/60 backdrop-blur-md rounded-xl border border-white/80 shadow-2xs">
                      <div className="flex items-center gap-2 text-indigo-600 mb-1">
                        <Cpu className="w-4 h-4" />
                        <span className="text-xs font-bold text-slate-700">Code Quality</span>
                      </div>
                      <p className="text-xs text-slate-500">
                        {lang === 'id' ? 'TypeScript ketat & modular' : 'Strict TS & zero-lint errors'}
                      </p>
                    </div>

                    <div className="p-3 sm:p-3.5 bg-white/60 backdrop-blur-md rounded-xl border border-white/80 shadow-2xs">
                      <div className="flex items-center gap-2 text-emerald-600 mb-1">
                        <TrendingUp className="w-4 h-4" />
                        <span className="text-xs font-bold text-slate-700">Web Vitals</span>
                      </div>
                      <p className="text-xs text-slate-500">
                        {lang === 'id' ? 'Sub-second page load' : 'Sub-second LCP & 0 CLS'}
                      </p>
                    </div>
                  </div>

                  {/* Philosophy Quote */}
                  <p className="text-xs text-slate-600 italic bg-white/50 backdrop-blur-md p-3 rounded-xl border border-white/70">
                    "{lang === 'id' ? 'Kode yang hebat adalah kode yang mudah dipahami manusia, bukan hanya dimengerti mesin.' : 'Clean code is code that speaks clearly to humans, not just machines.'}"
                  </p>
                </div>
              </div>
            </div>
          </motion.div>
        </div>

        {/* Bottom Metrics Bar */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: false, amount: 0.2 }}
          transition={{ duration: 0.7, delay: 0.2 }}
          className="mt-14 sm:mt-16 pt-8 border-t border-slate-200/60 grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6"
        >
          <motion.div
            whileHover={{ y: -3 }}
            className="p-4 sm:p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 shadow-2xs hover:bg-white/80 transition-all"
          >
            <div className="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
              {PERSONAL_INFO.yearsOfExp}
            </div>
            <div className="text-xs font-semibold text-slate-500 mt-0.5">
              {lang === 'id' ? 'Tahun Pengalaman' : 'Years Experience'}
            </div>
          </motion.div>

          <motion.div
            whileHover={{ y: -3 }}
            className="p-4 sm:p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 shadow-2xs hover:bg-white/80 transition-all"
          >
            <div className="text-2xl sm:text-3xl font-extrabold text-indigo-600 tracking-tight">
              {PERSONAL_INFO.completedProjects}
            </div>
            <div className="text-xs font-semibold text-slate-500 mt-0.5">
              {lang === 'id' ? 'Proyek Web Selesai' : 'Completed Projects'}
            </div>
          </motion.div>

          <motion.div
            whileHover={{ y: -3 }}
            className="p-4 sm:p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 shadow-2xs hover:bg-white/80 transition-all"
          >
            <div className="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight">
              {PERSONAL_INFO.clientSatisfaction}
            </div>
            <div className="text-xs font-semibold text-slate-500 mt-0.5">
              {lang === 'id' ? 'Tingkat Kepuasan Klien' : 'Client Satisfaction'}
            </div>
          </motion.div>

          <motion.div
            whileHover={{ y: -3 }}
            className="p-4 sm:p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 shadow-2xs hover:bg-white/80 transition-all"
          >
            <div className="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
              {PERSONAL_INFO.openSourceContributions}
            </div>
            <div className="text-xs font-semibold text-slate-500 mt-0.5">
              {lang === 'id' ? 'Kontribusi Open Source' : 'OSS Contributions'}
            </div>
          </motion.div>
        </motion.div>
      </div>
    </section>
  );
};
