import React from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  X,
  Printer,
  Download,
  CheckCircle2,
  Mail,
  MapPin,
  Globe,
  Briefcase,
  GraduationCap,
  Sparkles,
  Code2
} from 'lucide-react';
import confetti from 'canvas-confetti';
import { PERSONAL_INFO, EXPERIENCES_DATA, SKILLS_DATA } from '../data/portfolioData';
import { Language } from '../types';

interface CVModalProps {
  isOpen: boolean;
  lang: Language;
  onClose: () => void;
}

export const CVModal: React.FC<CVModalProps> = ({ isOpen, lang, onClose }) => {
  if (!isOpen) return null;

  const handleDownload = () => {
    confetti({
      particleCount: 50,
      spread: 70,
      origin: { y: 0.6 }
    });
    window.print();
  };

  return (
    <AnimatePresence>
      <div
        id="cv-modal-backdrop"
        className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-900/70 backdrop-blur-xs overflow-y-auto"
        onClick={onClose}
      >
        <motion.div
          id="cv-modal-dialog"
          initial={{ opacity: 0, scale: 0.95, y: 20 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          exit={{ opacity: 0, scale: 0.95, y: 20 }}
          className="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-6 flex flex-col max-h-[90vh]"
          onClick={(e) => e.stopPropagation()}
        >
          {/* Action Header */}
          <div className="px-6 py-4 bg-slate-900 text-white flex items-center justify-between sticky top-0 z-10">
            <div className="flex items-center gap-2">
              <Code2 className="w-5 h-5 text-indigo-400" />
              <span className="font-bold text-sm">Curriculum Vitae — {PERSONAL_INFO.name}</span>
            </div>

            <div className="flex items-center gap-2">
              <button
                onClick={handleDownload}
                className="flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-xs"
              >
                <Printer className="w-3.5 h-3.5" />
                <span>{lang === 'id' ? 'Cetak / Unduh PDF' : 'Print / Download PDF'}</span>
              </button>

              <button
                onClick={onClose}
                className="p-1.5 text-slate-400 hover:text-white rounded-lg cursor-pointer"
                aria-label="Tutup"
              >
                <X className="w-5 h-5" />
              </button>
            </div>
          </div>

          {/* Printable CV Body */}
          <div className="p-8 sm:p-10 overflow-y-auto space-y-8 text-slate-800 font-sans">
            {/* Header Resume Strip */}
            <div className="border-b border-slate-200 pb-6 space-y-2">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                  <h1 className="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {PERSONAL_INFO.name}
                  </h1>
                  <p className="text-sm font-bold text-indigo-600">
                    {PERSONAL_INFO.titleId} • Senior Software Engineer
                  </p>
                </div>
                <div className="text-xs text-slate-500 space-y-1 sm:text-right">
                  <div className="flex items-center sm:justify-end gap-1">
                    <Mail className="w-3.5 h-3.5 text-slate-400" />
                    <span>{PERSONAL_INFO.email}</span>
                  </div>
                  <div className="flex items-center sm:justify-end gap-1">
                    <MapPin className="w-3.5 h-3.5 text-slate-400" />
                    <span>{PERSONAL_INFO.location}</span>
                  </div>
                </div>
              </div>
              <p className="text-xs text-slate-600 leading-relaxed pt-2">
                {PERSONAL_INFO.bioId}
              </p>
            </div>

            {/* Core Competencies / Skills */}
            <div className="space-y-3">
              <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Tech Stack & Keahlian Utama
              </h3>
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
                {SKILLS_DATA.slice(0, 8).map((s) => (
                  <div key={s.name} className="p-2.5 bg-slate-50 rounded-xl border border-slate-200/80 text-xs">
                    <div className="font-bold text-slate-800">{s.name}</div>
                    <div className="text-[10px] text-indigo-600 font-semibold">{s.experience} exp</div>
                  </div>
                ))}
              </div>
            </div>

            {/* Experience */}
            <div className="space-y-4">
              <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <Briefcase className="w-3.5 h-3.5 text-indigo-600" />
                Pengalaman Kerja Profesional
              </h3>
              <div className="space-y-5">
                {EXPERIENCES_DATA.map((exp) => (
                  <div key={exp.id} className="space-y-1.5">
                    <div className="flex justify-between items-start">
                      <div>
                        <h4 className="text-sm font-bold text-slate-900">{exp.role}</h4>
                        <p className="text-xs font-semibold text-slate-700">{exp.company} • {exp.location}</p>
                      </div>
                      <span className="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                        {exp.period}
                      </span>
                    </div>
                    <p className="text-xs text-slate-600 leading-relaxed">{exp.description}</p>
                    <ul className="space-y-1 pt-1">
                      {exp.achievements.map((ach, i) => (
                        <li key={i} className="text-[11px] text-slate-600 flex items-start gap-2">
                          <span className="w-1.5 h-1.5 rounded-full bg-indigo-600 mt-1 shrink-0" />
                          <span>{ach}</span>
                        </li>
                      ))}
                    </ul>
                  </div>
                ))}
              </div>
            </div>

            {/* Education */}
            <div className="space-y-2 pt-2 border-t border-slate-200">
              <h3 className="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                <GraduationCap className="w-3.5 h-3.5 text-indigo-600" />
                Pendidikan & Sertifikasi
              </h3>
              <div className="flex justify-between items-start text-xs">
                <div>
                  <div className="font-bold text-slate-900">S.Kom dalam Teknik Informatika / Ilmu Komputer</div>
                  <div className="text-slate-600">Universitas Indonesia (GPA: 3.84 / 4.00)</div>
                </div>
                <span className="font-mono text-slate-500">2015 — 2019</span>
              </div>
            </div>
          </div>
        </motion.div>
      </div>
    </AnimatePresence>
  );
};
