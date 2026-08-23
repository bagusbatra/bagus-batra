import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  X,
  ExternalLink,
  Github,
  CheckCircle2,
  Layers,
  Sparkles,
  Calendar,
  User,
  Zap,
  Play,
  Maximize2
} from 'lucide-react';
import { Project, Language } from '../types';

interface ProjectModalProps {
  project: Project | null;
  lang: Language;
  onClose: () => void;
}

export const ProjectModal: React.FC<ProjectModalProps> = ({ project, lang, onClose }) => {
  const [activeTab, setActiveTab] = useState<'overview' | 'architecture' | 'preview'>('overview');

  if (!project) return null;

  return (
    <AnimatePresence>
      <div
        id="project-modal-backdrop"
        className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
        onClick={onClose}
      >
        <motion.div
          id="project-modal-dialog"
          initial={{ opacity: 0, scale: 0.95, y: 20 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          exit={{ opacity: 0, scale: 0.95, y: 20 }}
          transition={{ duration: 0.25, ease: 'easeOut' }}
          className="relative w-full max-w-4xl bg-white/95 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/80 overflow-hidden my-8"
          onClick={(e) => e.stopPropagation()}
        >
          {/* Top Bar with Project Banner */}
          <div className="relative h-64 sm:h-80 bg-slate-900 overflow-hidden">
            <img
              src={project.image}
              alt={project.title}
              className="w-full h-full object-cover opacity-60 mix-blend-luminosity scale-105"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />

            {/* Close Button */}
            <button
              id="close-project-modal-btn"
              onClick={onClose}
              className="absolute top-4 right-4 p-2.5 rounded-full bg-white/20 hover:bg-white/40 text-white backdrop-blur-md transition-all cursor-pointer border border-white/30"
              aria-label="Tutup modal"
            >
              <X className="w-5 h-5" />
            </button>

            {/* Banner Text Overlay */}
            <div className="absolute bottom-6 left-6 right-6 space-y-2">
              <div className="flex flex-wrap items-center gap-2">
                <span className="px-3 py-1 bg-indigo-500/90 backdrop-blur-md text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-xs border border-indigo-400/40">
                  {project.category}
                </span>
                {project.featured && (
                  <span className="px-3 py-1 bg-amber-400/95 backdrop-blur-md text-amber-950 text-xs font-bold rounded-lg flex items-center gap-1 shadow-2xs">
                    <Sparkles className="w-3.5 h-3.5" />
                    Featured
                  </span>
                )}
              </div>
              <h2 className="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                {project.title}
              </h2>
              <p className="text-sm text-slate-300 max-w-2xl">
                {project.tagline}
              </p>
            </div>
          </div>

          {/* Navigation Sub-Tabs */}
          <div className="flex border-b border-slate-200/80 bg-white/60 backdrop-blur-md px-6 pt-3 gap-2">
            {[
              { id: 'overview', labelId: 'Ikhtisar & Solusi', labelEn: 'Overview & Highlights' },
              { id: 'architecture', labelId: 'Arsitektur Stack', labelEn: 'Tech Architecture' },
              { id: 'preview', labelId: 'Simulasi Interaktif', labelEn: 'Live Preview' },
            ].map((tab) => (
              <button
                key={tab.id}
                id={`modal-tab-${tab.id}`}
                onClick={() => setActiveTab(tab.id as any)}
                className={`px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors cursor-pointer ${
                  activeTab === tab.id
                    ? 'bg-white text-indigo-600 border-t-2 border-indigo-600 shadow-2xs'
                    : 'text-slate-500 hover:text-slate-900'
                }`}
              >
                {lang === 'id' ? tab.labelId : tab.labelEn}
              </button>
            ))}
          </div>

          {/* Modal Content Body */}
          <div className="p-6 sm:p-8 space-y-6 max-h-[60vh] overflow-y-auto">
            {activeTab === 'overview' && (
              <div className="space-y-6">
                {/* Meta Grid */}
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-white/70 backdrop-blur-md rounded-2xl border border-white/90 text-xs shadow-2xs">
                  <div>
                    <span className="text-slate-400 block font-semibold">Role</span>
                    <span className="font-bold text-slate-800">{project.role}</span>
                  </div>
                  <div>
                    <span className="text-slate-400 block font-semibold">Timeline</span>
                    <span className="font-bold text-slate-800">{project.timeline}</span>
                  </div>
                  <div>
                    <span className="text-slate-400 block font-semibold">Client / Scope</span>
                    <span className="font-bold text-slate-800">{project.client || 'Open Project'}</span>
                  </div>
                  <div>
                    <span className="text-slate-400 block font-semibold">Category</span>
                    <span className="font-bold text-indigo-600">{project.category}</span>
                  </div>
                </div>

                {/* Metrics Highlight */}
                <div>
                  <h4 className="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                    {lang === 'id' ? 'Hasil & Dampak Terukur' : 'Key Engineering Metrics'}
                  </h4>
                  <div className="grid grid-cols-3 gap-3">
                    {project.metrics.map((m, idx) => (
                      <div key={idx} className="p-3.5 bg-indigo-50/80 backdrop-blur-md rounded-xl border border-indigo-100/80 text-center shadow-2xs">
                        <div className="text-xl sm:text-2xl font-extrabold text-indigo-700">
                          {m.value}
                        </div>
                        <div className="text-[11px] font-semibold text-slate-600 mt-0.5">
                          {m.label}
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                {/* Long Description */}
                <div className="space-y-2">
                  <h4 className="text-sm font-bold text-slate-900">
                    {lang === 'id' ? 'Tantangan & Solusi Rekayasa' : 'Engineering Challenge & Solution'}
                  </h4>
                  <p className="text-sm text-slate-600 leading-relaxed">
                    {project.longDescription}
                  </p>
                </div>

                {/* Key Highlights */}
                <div className="space-y-2.5">
                  <h4 className="text-sm font-bold text-slate-900">
                    {lang === 'id' ? 'Sorotan Fitur & Inovasi' : 'Key Technical Highlights'}
                  </h4>
                  <div className="space-y-2">
                    {project.highlights.map((h, i) => (
                      <div key={i} className="flex items-start gap-2.5 text-xs sm:text-sm text-slate-700">
                        <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                        <span>{h}</span>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'architecture' && (
              <div className="space-y-6">
                <div className="space-y-4">
                  <div>
                    <h4 className="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                      Frontend & Client Stack
                    </h4>
                    <div className="flex flex-wrap gap-2">
                      {project.techStack.frontend.map((t, idx) => (
                        <span key={idx} className="px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-xs font-bold">
                          {t}
                        </span>
                      ))}
                    </div>
                  </div>

                  {project.techStack.backend && (
                    <div>
                      <h4 className="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                        Backend & API Services
                      </h4>
                      <div className="flex flex-wrap gap-2">
                        {project.techStack.backend.map((t, idx) => (
                          <span key={idx} className="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold">
                            {t}
                          </span>
                        ))}
                      </div>
                    </div>
                  )}

                  {project.techStack.database && (
                    <div>
                      <h4 className="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                        Database & Caching Layer
                      </h4>
                      <div className="flex flex-wrap gap-2">
                        {project.techStack.database.map((t, idx) => (
                          <span key={idx} className="px-3 py-1.5 bg-purple-50 text-purple-700 border border-purple-200 rounded-lg text-xs font-bold">
                            {t}
                          </span>
                        ))}
                      </div>
                    </div>
                  )}

                  {project.techStack.cloudAndDevOps && (
                    <div>
                      <h4 className="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                        Cloud, CI/CD & Deployment
                      </h4>
                      <div className="flex flex-wrap gap-2">
                        {project.techStack.cloudAndDevOps.map((t, idx) => (
                          <span key={idx} className="px-3 py-1.5 bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-bold">
                            {t}
                          </span>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
              </div>
            )}

            {activeTab === 'preview' && (
              <div className="space-y-4">
                <div className="p-5 bg-slate-900 text-slate-200 rounded-2xl border border-slate-800 space-y-4">
                  <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div className="flex items-center gap-2">
                      <span className="w-3 h-3 rounded-full bg-rose-500" />
                      <span className="w-3 h-3 rounded-full bg-amber-500" />
                      <span className="w-3 h-3 rounded-full bg-emerald-500" />
                      <span className="text-xs font-mono text-slate-400 ml-2">preview.applet.local:3000/{project.id}</span>
                    </div>
                    <span className="text-[11px] font-mono bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded-sm">200 OK</span>
                  </div>

                  <div className="p-6 bg-slate-950 rounded-xl border border-slate-800/80 text-center space-y-3">
                    <div className="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center mx-auto shadow-lg shadow-indigo-500/30">
                      <Zap className="w-6 h-6" />
                    </div>
                    <h5 className="font-bold text-white text-base">
                      {project.title} — Live Sandbox Active
                    </h5>
                    <p className="text-xs text-slate-400 max-w-md mx-auto">
                      {lang === 'id'
                        ? 'Simulasi antarmuka berkinerja tinggi dengan visualisasi real-time dan transisi sub-frame.'
                        : 'Simulated production build with live data streaming and instant responsiveness.'}
                    </p>
                    {project.demoUrl && (
                      <a
                        href={project.demoUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition-colors"
                      >
                        <span>{lang === 'id' ? 'Buka Live Web App' : 'Launch Full Web App'}</span>
                        <ExternalLink className="w-3.5 h-3.5" />
                      </a>
                    )}
                  </div>
                </div>
              </div>
            )}
          </div>

          {/* Footer Action Strip */}
          <div className="p-5 sm:p-6 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <div className="flex items-center gap-2">
              {project.tags.slice(0, 3).map((tag, idx) => (
                <span key={idx} className="text-xs font-semibold text-slate-600 bg-white px-2.5 py-1 rounded-md border border-slate-200">
                  {tag}
                </span>
              ))}
            </div>

            <div className="flex items-center gap-2.5">
              {project.githubUrl && (
                <a
                  id="modal-project-github-btn"
                  href={project.githubUrl}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-slate-100 text-slate-800 text-xs font-bold rounded-xl border border-slate-200 transition-colors"
                >
                  <Github className="w-4 h-4" />
                  <span>GitHub</span>
                </a>
              )}
              {project.demoUrl && (
                <a
                  id="modal-project-demo-btn"
                  href={project.demoUrl}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors"
                >
                  <span>{lang === 'id' ? 'Kunjungi Live Demo' : 'Live Preview'}</span>
                  <ExternalLink className="w-4 h-4" />
                </a>
              )}
            </div>
          </div>
        </motion.div>
      </div>
    </AnimatePresence>
  );
};
