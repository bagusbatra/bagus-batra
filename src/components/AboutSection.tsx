import React, { useState } from 'react';
import { motion } from 'motion/react';
import {
  Code2,
  Layers,
  Palette,
  Sparkles,
  Server,
  Database,
  Cloud,
  GitBranch,
  Layout,
  Zap,
  CheckCircle,
  FileCode,
  Cpu,
  ShieldCheck,
  Flame,
  Award
} from 'lucide-react';
import { SKILLS_DATA, PERSONAL_INFO } from '../data/portfolioData';
import { Language, SkillItem } from '../types';

interface AboutSectionProps {
  lang: Language;
}

export const AboutSection: React.FC<AboutSectionProps> = ({ lang }) => {
  const [activeCategory, setActiveCategory] = useState<'all' | 'frontend' | 'backend' | 'devops' | 'tools'>('all');
  const [hoveredSkill, setHoveredSkill] = useState<string | null>(null);

  const filteredSkills = activeCategory === 'all'
    ? SKILLS_DATA
    : SKILLS_DATA.filter((s) => s.category === activeCategory);

  const getSkillIcon = (iconName: string) => {
    switch (iconName) {
      case 'Code2':
        return <Code2 className="w-5 h-5" />;
      case 'FileCode':
        return <FileCode className="w-5 h-5" />;
      case 'Palette':
        return <Palette className="w-5 h-5" />;
      case 'Sparkles':
        return <Sparkles className="w-5 h-5" />;
      case 'Layers':
        return <Layers className="w-5 h-5" />;
      case 'Server':
        return <Server className="w-5 h-5" />;
      case 'Database':
        return <Database className="w-5 h-5" />;
      case 'Cpu':
        return <Cpu className="w-5 h-5" />;
      case 'Cloud':
        return <Cloud className="w-5 h-5" />;
      case 'GitBranch':
        return <GitBranch className="w-5 h-5" />;
      case 'Layout':
        return <Layout className="w-5 h-5" />;
      case 'Zap':
        return <Zap className="w-5 h-5" />;
      default:
        return <Code2 className="w-5 h-5" />;
    }
  };

  const principles = [
    {
      titleId: 'Performance-First',
      titleEn: 'Performance-First',
      descId: 'Zero layout shift, bundle splitting, dan sub-second response time adalah standar tanpa kompromi.',
      descEn: 'Zero layout shift, smart code-splitting, and sub-second page loads are foundational standards.',
      icon: <Zap className="w-5 h-5 text-amber-500" />,
      bg: 'bg-amber-50 border-amber-200/80'
    },
    {
      titleId: 'Aksesibilitas & WAI-ARIA',
      titleEn: 'Accessibility by Default',
      descId: 'Antarmuka web harus dapat diakses dengan nyaman oleh semua pengguna, termasuk keyboard & screen reader.',
      descEn: 'Interfaces that are naturally navigable via keyboard and assistive screen readers.',
      icon: <ShieldCheck className="w-5 h-5 text-emerald-500" />,
      bg: 'bg-emerald-50 border-emerald-200/80'
    },
    {
      titleId: 'Arsitektur Modular & Bersih',
      titleEn: 'Modular Clean Architecture',
      descId: 'Struktur kode rapi, type-safe, dan terdokumentasi dengan baik agar mudah diskalakan bersama tim.',
      descEn: 'Type-safe, maintainable patterns designed for scalable team development.',
      icon: <Layers className="w-5 h-5 text-indigo-500" />,
      bg: 'bg-indigo-50 border-indigo-200/80'
    },
    {
      titleId: 'Micro-Interactions yang Alami',
      titleEn: 'Natural Micro-Interactions',
      descId: 'Sentuhan animasi berbasis fisika nyata yang membuat aplikasi web terasa hidup dan tidak kaku.',
      descEn: 'Delightful physics-based transitions that give digital products tactile elegance.',
      icon: <Sparkles className="w-5 h-5 text-purple-500" />,
      bg: 'bg-purple-50 border-purple-200/80'
    }
  ];

  return (
    <section id="about" className="py-20 sm:py-24 bg-transparent border-b border-white/60">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16 sm:space-y-20">
        {/* Section Header */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: false, amount: 0.2 }}
          transition={{ duration: 0.6 }}
          className="max-w-3xl space-y-3"
        >
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-50/80 backdrop-blur-md text-indigo-700 border border-indigo-100 text-xs font-bold uppercase tracking-wider">
            <Award className="w-3.5 h-3.5" />
            <span>{lang === 'id' ? 'Tentang & Prinsip Rekayasa' : 'About & Engineering Values'}</span>
          </div>
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
            {lang === 'id'
              ? 'Menghubungkan Desain Presisi dengan Kode Berkinerja Tinggi'
              : 'Bridging Pixel Precision with High-Performance Architecture'}
          </h2>
          <p className="text-slate-600 text-base sm:text-lg leading-relaxed">
            {lang === 'id' ? PERSONAL_INFO.bioId : PERSONAL_INFO.bioEn}
          </p>
        </motion.div>

        {/* 4 Core Principles Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
          {principles.map((p, idx) => (
            <motion.div
              key={idx}
              initial={{ opacity: 0, y: 30, scale: 0.96 }}
              whileInView={{ opacity: 1, y: 0, scale: 1 }}
              viewport={{ once: false, amount: 0.15 }}
              transition={{ duration: 0.5, delay: idx * 0.08 }}
              whileHover={{ y: -5, transition: { duration: 0.2 } }}
              className={`p-5 sm:p-6 rounded-3xl border backdrop-blur-lg ${p.bg} transition-all duration-300 shadow-2xs hover:shadow-lg`}
            >
              <div className="w-11 h-11 rounded-2xl bg-white/90 shadow-2xs flex items-center justify-center mb-4 border border-white/80">
                {p.icon}
              </div>
              <h3 className="font-bold text-slate-900 text-base mb-2">
                {lang === 'id' ? p.titleId : p.titleEn}
              </h3>
              <p className="text-xs text-slate-600 leading-relaxed">
                {lang === 'id' ? p.descId : p.descEn}
              </p>
            </motion.div>
          ))}
        </div>

        {/* Tech Stack & Skills Matrix */}
        <div id="skills" className="pt-4 sm:pt-8 space-y-6 sm:space-y-8">
          <motion.div
            initial={{ opacity: 0, y: 25 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: false, amount: 0.2 }}
            transition={{ duration: 0.6 }}
            className="flex flex-col md:flex-row md:items-end justify-between gap-4"
          >
            <div className="max-w-2xl">
              <h3 className="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {lang === 'id' ? 'Tech Stack & Matriks Keahlian' : 'Tech Stack & Skills Matrix'}
              </h3>
              <p className="text-xs sm:text-sm text-slate-500 mt-1">
                {lang === 'id'
                  ? 'Teknologi modern yang saya gunakan sehari-hari dalam membangun produk digital kelas dunia.'
                  : 'Modern technologies and tooling leveraged in production-grade software.'}
              </p>
            </div>

            {/* Filter Pills */}
            <div className="flex items-center gap-1 p-1.5 bg-white/50 backdrop-blur-md rounded-2xl border border-white/80 shadow-2xs overflow-x-auto max-w-full pb-1.5 sm:pb-1.5 scrollbar-none">
              {(
                [
                  { id: 'all', labelId: 'Semua', labelEn: 'All' },
                  { id: 'frontend', labelId: 'Frontend', labelEn: 'Frontend' },
                  { id: 'backend', labelId: 'Backend', labelEn: 'Backend' },
                  { id: 'devops', labelId: 'DevOps & Cloud', labelEn: 'DevOps' },
                  { id: 'tools', labelId: 'Tools & UI', labelEn: 'Tools' },
                ] as const
              ).map((cat) => {
                const isActive = activeCategory === cat.id;
                return (
                  <button
                    key={cat.id}
                    id={`skill-filter-${cat.id}`}
                    onClick={() => setActiveCategory(cat.id)}
                    className={`relative px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors cursor-pointer shrink-0 ${
                      isActive
                        ? 'text-indigo-600 font-bold'
                        : 'text-slate-600 hover:text-slate-900 hover:bg-white/40'
                    }`}
                  >
                    {isActive && (
                      <motion.div
                        layoutId="activeSkillTab"
                        className="absolute inset-0 bg-white shadow-2xs border border-white/90 rounded-xl -z-10"
                        transition={{ type: 'spring', stiffness: 350, damping: 28 }}
                      />
                    )}
                    {lang === 'id' ? cat.labelId : cat.labelEn}
                  </button>
                );
              })}
            </div>
          </motion.div>

          {/* Skill Grid */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-4.5">
            {filteredSkills.map((skill, idx) => {
              const isHovered = hoveredSkill === skill.name;
              return (
                <motion.div
                  key={skill.name}
                  layout
                  initial={{ opacity: 0, y: 25, scale: 0.97 }}
                  whileInView={{ opacity: 1, y: 0, scale: 1 }}
                  viewport={{ once: false, amount: 0.1 }}
                  transition={{ duration: 0.45, delay: (idx % 6) * 0.05 }}
                  id={`skill-card-${skill.name.toLowerCase().replace(/[^a-z0-9]/g, '-')}`}
                  onMouseEnter={() => setHoveredSkill(skill.name)}
                  onMouseLeave={() => setHoveredSkill(null)}
                  whileHover={{ y: -4, transition: { duration: 0.2 } }}
                  className="p-5 sm:p-5.5 bg-white/60 backdrop-blur-lg rounded-3xl border border-white/80 hover:border-indigo-300/80 shadow-2xs hover:shadow-xl transition-all duration-300 group relative overflow-hidden"
                >
                  <div className="flex items-start justify-between gap-3 mb-3">
                    <div className="w-10 h-10 rounded-xl bg-white/80 border border-white flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors shrink-0 shadow-2xs">
                      {getSkillIcon(skill.iconName)}
                    </div>
                    <div className="text-right">
                      <span className="text-xs font-mono font-bold text-slate-400 group-hover:text-indigo-600 transition-colors">
                        {skill.level}%
                      </span>
                      <p className="text-[11px] text-slate-400">{skill.experience}</p>
                    </div>
                  </div>

                  <h4 className="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                    {skill.name}
                  </h4>
                  <p className="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                    {skill.highlightText}
                  </p>

                  {/* Visual Progress Bar */}
                  <div className="w-full bg-slate-200/50 h-1.5 rounded-full overflow-hidden mt-3.5">
                    <motion.div
                      initial={{ width: 0 }}
                      whileInView={{ width: `${skill.level}%` }}
                      viewport={{ once: false, amount: 0.2 }}
                      transition={{ duration: 0.8, delay: 0.1, ease: 'easeOut' }}
                      className="h-full bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full"
                    />
                  </div>
                </motion.div>
              );
            })}
          </div>
        </div>
      </div>
    </section>
  );
};
