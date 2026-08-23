import React from 'react';
import { motion } from 'motion/react';
import {
  Briefcase,
  Calendar,
  MapPin,
  CheckCircle2,
  ExternalLink,
  GraduationCap,
  Sparkles
} from 'lucide-react';
import { EXPERIENCES_DATA } from '../data/portfolioData';
import { Language } from '../types';

interface ExperienceTimelineProps {
  lang: Language;
}

export const ExperienceTimeline: React.FC<ExperienceTimelineProps> = ({ lang }) => {
  return (
    <section id="experience" className="py-20 sm:py-24 bg-transparent border-b border-white/60">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-14">
        {/* Section Header */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: false, amount: 0.15 }}
          transition={{ duration: 0.6 }}
          className="max-w-3xl space-y-3"
        >
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-50/80 backdrop-blur-md text-indigo-700 border border-indigo-100 text-xs font-bold uppercase tracking-wider">
            <Briefcase className="w-3.5 h-3.5" />
            <span>{lang === 'id' ? 'Karier & Pengalaman' : 'Career & Experience'}</span>
          </div>
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
            {lang === 'id'
              ? 'Jejak Rekam Profesional & Kepemimpinan Teknis'
              : 'Professional Journey & Technical Leadership'}
          </h2>
          <p className="text-slate-600 text-sm sm:text-base leading-relaxed">
            {lang === 'id'
              ? 'Perjalanan saya dalam mengembangkan ekosistem web berskala besar, memimpin tim engineering, dan menyelesaikan tantangan arsitektur kompleks.'
              : 'Over 6 years of architecting scalable web applications, mentoring development teams, and shipping production-grade software.'}
          </p>
        </motion.div>

        {/* Timeline List */}
        <div className="relative border-l-2 border-indigo-200/80 ml-3 sm:ml-8 space-y-8 sm:space-y-12 pl-5 sm:pl-10">
          {EXPERIENCES_DATA.map((exp, idx) => (
            <motion.div
              key={exp.id}
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: false, amount: 0.2 }}
              transition={{ duration: 0.5, delay: idx * 0.1 }}
              id={`experience-item-${exp.id}`}
              className="relative group"
            >
              {/* Timeline Indicator Dot */}
              <div className="absolute -left-[31px] sm:-left-[51px] top-2 w-5 sm:w-6 h-5 sm:h-6 rounded-full bg-white/95 backdrop-blur-md border-3 sm:border-4 border-indigo-600 shadow-sm group-hover:scale-125 group-hover:border-indigo-500 transition-transform duration-300" />

              <motion.div
                whileHover={{ y: -4, transition: { duration: 0.2 } }}
                className="bg-white/60 backdrop-blur-lg rounded-3xl p-5 sm:p-8 border border-white/80 shadow-2xs hover:shadow-xl hover:border-indigo-300/80 transition-all duration-300 space-y-4"
              >
                {/* Role Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                  <div>
                    <span className="text-xs font-bold font-mono text-indigo-600 tracking-wide uppercase">
                      {exp.period}
                    </span>
                    <h3 className="text-lg sm:text-xl font-extrabold text-slate-900 mt-0.5">
                      {exp.role}
                    </h3>
                    <div className="flex flex-wrap items-center gap-2 sm:gap-3 text-xs text-slate-500 mt-1">
                      <span className="font-bold text-slate-800">{exp.company}</span>
                      <span>•</span>
                      <span className="flex items-center gap-1">
                        <MapPin className="w-3 h-3 text-slate-400" />
                        {exp.location}
                      </span>
                      <span>•</span>
                      <span className="px-2 py-0.5 bg-white/70 border border-white/80 text-slate-700 rounded-md font-semibold text-[11px] shadow-2xs">
                        {exp.type}
                      </span>
                    </div>
                  </div>
                </div>

                {/* Description */}
                <p className="text-xs sm:text-sm text-slate-600 leading-relaxed">
                  {exp.description}
                </p>

                {/* Key Achievements */}
                <div className="space-y-2 pt-1">
                  <h4 className="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">
                    {lang === 'id' ? 'Pencapaian Kunci:' : 'Key Achievements:'}
                  </h4>
                  <div className="space-y-2">
                    {exp.achievements.map((ach, aIdx) => (
                      <div key={aIdx} className="flex items-start gap-2.5 text-xs sm:text-sm text-slate-700">
                        <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                        <span>{ach}</span>
                      </div>
                    ))}
                  </div>
                </div>

                {/* Tech Stack Pills */}
                <div className="pt-2 flex flex-wrap gap-1.5">
                  {exp.skills.map((skill, sIdx) => (
                    <span
                      key={sIdx}
                      className="px-2.5 py-1 bg-white/70 backdrop-blur-xs text-slate-700 rounded-md text-[11px] font-semibold border border-white/60 shadow-2xs"
                    >
                      {skill}
                    </span>
                  ))}
                </div>
              </motion.div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};
