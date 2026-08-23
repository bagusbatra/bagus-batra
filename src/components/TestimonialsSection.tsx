import React from 'react';
import { motion } from 'motion/react';
import { Star, Quote, MessageCircle, CheckCircle2 } from 'lucide-react';
import { TESTIMONIALS_DATA } from '../data/portfolioData';
import { Language } from '../types';

interface TestimonialsSectionProps {
  lang: Language;
}

export const TestimonialsSection: React.FC<TestimonialsSectionProps> = ({ lang }) => {
  return (
    <section id="testimonials" className="py-20 sm:py-24 bg-transparent border-b border-white/60">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-12">
        {/* Header */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: false, amount: 0.15 }}
          transition={{ duration: 0.6 }}
          className="max-w-3xl space-y-3"
        >
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-amber-50/80 backdrop-blur-md text-amber-800 border border-amber-200 text-xs font-bold uppercase tracking-wider">
            <Star className="w-3.5 h-3.5 fill-amber-500 text-amber-500" />
            <span>{lang === 'id' ? 'Rekomendasi & Testimoni' : 'Testimonials & Endorsements'}</span>
          </div>
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
            {lang === 'id'
              ? 'Apa Kata Rekan Kerja, CTO & Klien Kolaborator'
              : 'What Engineering Leaders & Clients Say'}
          </h2>
          <p className="text-slate-600 text-sm sm:text-base leading-relaxed">
            {lang === 'id'
              ? 'Testimoni nyata dari para pemimpin teknologi dan founder yang telah berkolaborasi dalam proyek-proyek penting.'
              : 'Endorsements from engineering leaders, product managers, and founders on delivered solutions.'}
          </p>
        </motion.div>

        {/* Testimonial Cards Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {TESTIMONIALS_DATA.map((item, idx) => (
            <motion.div
              key={item.id}
              initial={{ opacity: 0, y: 30, scale: 0.96 }}
              whileInView={{ opacity: 1, y: 0, scale: 1 }}
              viewport={{ once: false, amount: 0.15 }}
              transition={{ duration: 0.5, delay: idx * 0.1 }}
              whileHover={{ y: -6, transition: { duration: 0.2 } }}
              id={`testimonial-card-${item.id}`}
              className="bg-white/60 backdrop-blur-lg rounded-3xl p-6 sm:p-7 border border-white/80 shadow-2xs hover:shadow-xl hover:border-indigo-300/80 transition-all duration-300 flex flex-col justify-between space-y-6 relative"
            >
              <div className="space-y-4">
                {/* Rating Stars & Project tag */}
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-1">
                    {[...Array(item.rating)].map((_, i) => (
                      <Star key={i} className="w-4 h-4 fill-amber-400 text-amber-400" />
                    ))}
                  </div>
                  <span className="text-[11px] font-semibold text-indigo-700 bg-indigo-50/80 backdrop-blur-md px-2.5 py-0.5 rounded-md border border-indigo-100/80">
                    {item.projectTag}
                  </span>
                </div>

                <p className="text-xs sm:text-sm text-slate-600 leading-relaxed italic">
                  "{item.content}"
                </p>
              </div>

              {/* Author Info */}
              <div className="flex items-center gap-3 pt-4 border-t border-slate-200/50">
                <img
                  src={item.avatar}
                  alt={item.name}
                  className="w-11 h-11 rounded-full object-cover border-2 border-white shadow-2xs"
                />
                <div>
                  <h4 className="text-sm font-bold text-slate-900">{item.name}</h4>
                  <p className="text-xs text-slate-500">{item.role}</p>
                  <p className="text-[11px] font-semibold text-slate-400">{item.company}</p>
                </div>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};
