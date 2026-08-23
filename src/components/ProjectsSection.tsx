import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  ExternalLink,
  Github,
  Sparkles,
  ArrowRight,
  Eye,
  Layers,
  Zap,
  Star,
  CheckCircle2,
  FolderGit2
} from 'lucide-react';
import { PROJECTS_DATA } from '../data/portfolioData';
import { Project, Language } from '../types';
import { ProjectModal } from './ProjectModal';

interface ProjectsSectionProps {
  lang: Language;
}

export const ProjectsSection: React.FC<ProjectsSectionProps> = ({ lang }) => {
  const [selectedCategory, setSelectedCategory] = useState<string>('All');
  const [activeProjectModal, setActiveProjectModal] = useState<Project | null>(null);

  const categories = [
    { id: 'All', labelId: 'Semua Proyek', labelEn: 'All Projects' },
    { id: 'Full-Stack', labelId: 'Full-Stack', labelEn: 'Full-Stack' },
    { id: 'Frontend', labelId: 'Frontend Apps', labelEn: 'Frontend Apps' },
    { id: 'UI/UX & Systems', labelId: 'Design Systems', labelEn: 'Design Systems' },
    { id: 'Open Source', labelId: 'Open Source', labelEn: 'Open Source' },
    { id: 'AI & Tools', labelId: 'AI & Tools', labelEn: 'AI & Tools' },
  ];

  const filteredProjects = selectedCategory === 'All'
    ? PROJECTS_DATA
    : PROJECTS_DATA.filter((p) => p.category === selectedCategory);

  return (
    <section id="projects" className="py-20 sm:py-24 bg-transparent border-b border-white/60">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-12">
        {/* Section Title */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: false, amount: 0.15 }}
          transition={{ duration: 0.6 }}
          className="flex flex-col md:flex-row md:items-end justify-between gap-6"
        >
          <div className="max-w-2xl space-y-3">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-50/80 backdrop-blur-md text-indigo-700 border border-indigo-100 text-xs font-bold uppercase tracking-wider">
              <FolderGit2 className="w-3.5 h-3.5" />
              <span>{lang === 'id' ? 'Karya Pilihan' : 'Featured Work'}</span>
            </div>
            <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
              {lang === 'id'
                ? 'Studi Kasus Proyek Web & Arsitektur Sistem'
                : 'Selected Web Projects & Systems Architecture'}
            </h2>
            <p className="text-slate-600 text-sm sm:text-base leading-relaxed">
              {lang === 'id'
                ? 'Koleksi produk digital nyata yang saya rancang dari tahap konsep hingga deployment dengan fokus kecepatan & pengalaman pengguna.'
                : 'Production applications built with a focus on runtime performance, accessibility, and high conversion.'}
            </p>
          </div>

          {/* Category Filter Tabs */}
          <div className="flex items-center gap-1 p-1.5 bg-white/50 backdrop-blur-md rounded-2xl border border-white/80 shadow-2xs overflow-x-auto max-w-full pb-1.5 sm:pb-1.5 scrollbar-none">
            {categories.map((cat) => {
              const isActive = selectedCategory === cat.id;
              return (
                <button
                  key={cat.id}
                  id={`project-cat-${cat.id.toLowerCase().replace(/[^a-z0-9]/g, '-')}`}
                  onClick={() => setSelectedCategory(cat.id)}
                  className={`relative px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors cursor-pointer shrink-0 ${
                    isActive
                      ? 'text-white'
                      : 'text-slate-600 hover:text-slate-900 hover:bg-white/40'
                  }`}
                >
                  {isActive && (
                    <motion.div
                      layoutId="activeProjectCategory"
                      className="absolute inset-0 bg-indigo-600 rounded-xl shadow-xs -z-10"
                      transition={{ type: 'spring', stiffness: 350, damping: 28 }}
                    />
                  )}
                  {lang === 'id' ? cat.labelId : cat.labelEn}
                </button>
              );
            })}
          </div>
        </motion.div>

        {/* Projects Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
          <AnimatePresence mode="popLayout">
            {filteredProjects.map((project, idx) => (
              <motion.article
                key={project.id}
                layout
                initial={{ opacity: 0, y: 30, scale: 0.96 }}
                whileInView={{ opacity: 1, y: 0, scale: 1 }}
                viewport={{ once: false, amount: 0.1 }}
                exit={{ opacity: 0, scale: 0.92 }}
                transition={{ duration: 0.45, delay: (idx % 3) * 0.08 }}
                whileHover={{ y: -6, transition: { duration: 0.2 } }}
                id={`project-card-${project.id}`}
                className="group bg-white/60 backdrop-blur-lg rounded-3xl border border-white/80 shadow-2xs hover:shadow-xl hover:border-indigo-300/80 transition-all duration-300 flex flex-col overflow-hidden"
              >
                {/* Project Image Banner */}
                <div className="relative h-48 sm:h-52 overflow-hidden bg-slate-100">
                  <img
                    src={project.image}
                    alt={project.title}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent" />

                  {/* Badge Row */}
                  <div className="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between">
                    <span className="px-2.5 py-1 bg-white/85 backdrop-blur-md text-slate-800 text-[11px] font-bold rounded-lg shadow-2xs border border-white/60">
                      {project.category}
                    </span>
                    {project.featured && (
                      <span className="px-2.5 py-1 bg-amber-400/95 text-amber-950 text-[11px] font-bold rounded-lg shadow-2xs flex items-center gap-1">
                        <Sparkles className="w-3 h-3" />
                        Featured
                      </span>
                    )}
                  </div>

                  {/* Quick Metric Badge in Corner */}
                  <div className="absolute bottom-3 left-3 right-3 flex items-center gap-2">
                    {project.metrics.slice(0, 2).map((m, mIdx) => (
                      <span
                        key={mIdx}
                        className="px-2.5 py-1 bg-slate-900/80 backdrop-blur-md text-white text-[11px] font-mono font-medium rounded-lg border border-white/10"
                      >
                        <strong className="text-indigo-300 font-bold">{m.value}</strong> {m.label}
                      </span>
                    ))}
                  </div>
                </div>

                {/* Card Content Body */}
                <div className="p-5 sm:p-6 flex-1 flex flex-col justify-between space-y-4">
                  <div className="space-y-2">
                    <h3 className="text-lg font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors">
                      {project.title}
                    </h3>
                    <p className="text-xs text-slate-600 leading-relaxed line-clamp-2">
                      {project.description}
                    </p>
                  </div>

                  {/* Tags */}
                  <div className="flex flex-wrap gap-1.5 pt-1">
                    {project.tags.slice(0, 4).map((tag, tIdx) => (
                      <span
                        key={tIdx}
                        className="px-2.5 py-1 bg-white/70 backdrop-blur-xs text-slate-600 rounded-md text-[11px] font-semibold border border-white/60 shadow-2xs"
                      >
                        {tag}
                      </span>
                    ))}
                  </div>

                  {/* Card Actions */}
                  <div className="pt-3 border-t border-slate-200/50 flex items-center justify-between">
                    <button
                      id={`view-study-${project.id}`}
                      onClick={() => setActiveProjectModal(project)}
                      className="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 cursor-pointer py-1"
                    >
                      <span>{lang === 'id' ? 'Detail Case Study' : 'Case Study'}</span>
                      <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                    </button>

                    <div className="flex items-center gap-1.5">
                      {project.githubUrl && (
                        <a
                          id={`card-github-${project.id}`}
                          href={project.githubUrl}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="p-2 text-slate-500 hover:text-slate-900 hover:bg-white/80 rounded-lg transition-colors border border-transparent hover:border-white/80 shadow-2xs"
                          title="Lihat Repositori GitHub"
                        >
                          <Github className="w-4 h-4" />
                        </a>
                      )}
                      {project.demoUrl && (
                        <a
                          id={`card-demo-${project.id}`}
                          href={project.demoUrl}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/80 rounded-lg transition-colors border border-transparent hover:border-indigo-100 shadow-2xs"
                          title="Buka Live Demo"
                        >
                          <ExternalLink className="w-4 h-4" />
                        </a>
                      )}
                    </div>
                  </div>
                </div>
              </motion.article>
            ))}
          </AnimatePresence>
        </div>
      </div>

      {/* Case Study Modal */}
      <ProjectModal
        project={activeProjectModal}
        lang={lang}
        onClose={() => setActiveProjectModal(null)}
      />
    </section>
  );
};
