import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
  Mail,
  MessageSquare,
  Send,
  Calendar,
  Clock,
  Sparkles,
  CheckCircle2,
  Copy,
  Check,
  MapPin,
  Phone,
  ArrowRight,
  ShieldCheck,
  ExternalLink
} from 'lucide-react';
import confetti from 'canvas-confetti';
import { PERSONAL_INFO, SOCIAL_LINKS } from '../data/portfolioData';
import { Language } from '../types';
import { SocialBar } from './SocialBar';

interface ContactSectionProps {
  lang: Language;
}

export const ContactSection: React.FC<ContactSectionProps> = ({ lang }) => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    projectType: 'Full-Stack Web App',
    budget: '$3k - $8k (IDR 45M - 120M)',
    timeline: '1 - 2 Bulan',
    message: '',
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [copiedEmail, setCopiedEmail] = useState(false);
  const [calendarModalOpen, setCalendarModalOpen] = useState(false);
  const [selectedSlot, setSelectedSlot] = useState<string | null>(null);
  const [callBooked, setCallBooked] = useState(false);

  const handleCopyEmail = () => {
    navigator.clipboard.writeText(PERSONAL_INFO.email);
    setCopiedEmail(true);
    confetti({
      particleCount: 30,
      spread: 50,
      origin: { y: 0.8 },
      colors: ['#6366f1', '#3b82f6']
    });
    setTimeout(() => setCopiedEmail(false), 2400);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.name || !formData.email || !formData.message) return;

    setIsSubmitting(true);
    // Simulate realistic network request
    setTimeout(() => {
      setIsSubmitting(false);
      setSubmitted(true);
      confetti({
        particleCount: 80,
        spread: 80,
        origin: { y: 0.6 },
        colors: ['#6366f1', '#10b981', '#f59e0b', '#3b82f6']
      });
    }, 900);
  };

  const handleBookCall = (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedSlot) return;
    setCallBooked(true);
    confetti({
      particleCount: 50,
      spread: 70,
      origin: { y: 0.6 }
    });
    setTimeout(() => {
      setCalendarModalOpen(false);
      setCallBooked(false);
      setSelectedSlot(null);
    }, 2500);
  };

  return (
    <section id="contact" className="py-20 sm:py-24 bg-transparent relative overflow-hidden">
      {/* Background radial highlight */}
      <div className="absolute top-1/2 right-0 w-96 h-96 bg-indigo-100/40 rounded-full blur-3xl pointer-events-none" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 sm:space-y-16 relative z-10">
        {/* Header */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: false, amount: 0.15 }}
          transition={{ duration: 0.6 }}
          className="max-w-3xl space-y-3"
        >
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-50/80 backdrop-blur-md text-indigo-700 border border-indigo-100 text-xs font-bold uppercase tracking-wider">
            <Mail className="w-3.5 h-3.5" />
            <span>{lang === 'id' ? 'Mulai Kolaborasi' : 'Get In Touch'}</span>
          </div>
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
            {lang === 'id'
              ? 'Punya Ide Proyek Hebat? Mari Kita Wujudkan Bersama.'
              : 'Have a Project in Mind? Let’s Build Something Exceptional.'}
          </h2>
          <p className="text-slate-600 text-sm sm:text-base leading-relaxed">
            {lang === 'id'
              ? 'Tersedia untuk proyek freelance terpilih, kontrak konsultasi arsitektur frontend, maupun peran full-time strategis. Respon dalam < 24 jam.'
              : 'Open for selected contract builds, architectural consultations, and full-time remote engineering roles.'}
          </p>
        </motion.div>

        {/* Main Grid: Info + Form */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
          {/* Left Column: Direct Contacts & Socials */}
          <motion.div
            initial={{ opacity: 0, x: -30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: false, amount: 0.15 }}
            transition={{ duration: 0.6 }}
            className="lg:col-span-5 space-y-6"
          >
            <div className="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-8 border border-white/80 shadow-2xs space-y-6">
              <h3 className="text-lg font-bold text-slate-900">
                {lang === 'id' ? 'Kontak Langsung & Konsultasi' : 'Direct Contacts & Channels'}
              </h3>

              <div className="space-y-3 sm:space-y-4">
                {/* Email Direct Card */}
                <div className="p-3.5 sm:p-4 bg-white/70 backdrop-blur-md rounded-2xl border border-white/90 shadow-2xs flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-indigo-50/80 text-indigo-600 flex items-center justify-center border border-indigo-100/50 shrink-0">
                      <Mail className="w-5 h-5" />
                    </div>
                    <div className="overflow-hidden">
                      <div className="text-[11px] font-bold text-slate-400 uppercase">Email Resmi</div>
                      <a href={`mailto:${PERSONAL_INFO.email}`} className="text-xs font-mono font-bold text-slate-800 hover:text-indigo-600 truncate block">
                        {PERSONAL_INFO.email}
                      </a>
                    </div>
                  </div>
                  <button
                    onClick={handleCopyEmail}
                    className="p-2 text-slate-500 hover:text-indigo-600 hover:bg-white/90 rounded-lg transition-colors cursor-pointer border border-transparent hover:border-white/80 shrink-0"
                    title="Salin Email"
                  >
                    {copiedEmail ? <Check className="w-4 h-4 text-emerald-600" /> : <Copy className="w-4 h-4" />}
                  </button>
                </div>

                {/* WhatsApp Direct Card */}
                <a
                  href="https://wa.me/6281234567890?text=Halo%20Bagus,%20saya%20tertarik%20untuk%20bekerjasama"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="p-3.5 sm:p-4 bg-white/70 backdrop-blur-md rounded-2xl border border-white/90 shadow-2xs hover:border-emerald-300 transition-colors flex items-center justify-between group"
                >
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-emerald-50/80 text-emerald-600 flex items-center justify-center border border-emerald-100/50 shrink-0">
                      <Phone className="w-5 h-5" />
                    </div>
                    <div>
                      <div className="text-[11px] font-bold text-slate-400 uppercase">WhatsApp Chat</div>
                      <div className="text-xs font-mono font-bold text-slate-800 group-hover:text-emerald-600">
                        +62 812-3456-7890
                      </div>
                    </div>
                  </div>
                  <ExternalLink className="w-4 h-4 text-slate-400 group-hover:text-emerald-600" />
                </a>

                {/* Location Card */}
                <div className="p-3.5 sm:p-4 bg-white/70 backdrop-blur-md rounded-2xl border border-white/90 shadow-2xs flex items-center gap-3">
                  <div className="w-10 h-10 rounded-xl bg-blue-50/80 text-blue-600 flex items-center justify-center border border-blue-100/50 shrink-0">
                    <MapPin className="w-5 h-5" />
                  </div>
                  <div>
                    <div className="text-[11px] font-bold text-slate-400 uppercase">Lokasi Kerja</div>
                    <div className="text-xs font-semibold text-slate-800">
                      {PERSONAL_INFO.location}
                    </div>
                  </div>
                </div>
              </div>

              {/* Book 15-min Discovery Call Card */}
              <div className="p-5 sm:p-5.5 bg-slate-900/90 backdrop-blur-md text-white rounded-3xl shadow-lg border border-white/10 space-y-3">
                <div className="flex items-center gap-2 text-indigo-300 text-xs font-bold uppercase tracking-wider">
                  <Calendar className="w-4 h-4" />
                  <span>1-on-1 Discovery Call</span>
                </div>
                <h4 className="font-bold text-sm">
                  {lang === 'id' ? 'Jadwalkan Konsultasi 15 Menit' : 'Book a 15-Min Quick Intro Call'}
                </h4>
                <p className="text-xs text-slate-300 leading-relaxed">
                  {lang === 'id'
                    ? 'Diskusikan kebutuhan arsitektur produk, timeline, atau tanya jawab teknis tanpa komitmen.'
                    : 'Quick synchronous sync to discuss technical feasibility, timelines, and budget.'}
                </p>
                <button
                  id="open-calendar-call-btn"
                  onClick={() => setCalendarModalOpen(true)}
                  className="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer flex items-center justify-center gap-1.5 shadow-md shadow-indigo-600/30"
                >
                  <Calendar className="w-3.5 h-3.5" />
                  <span>{lang === 'id' ? 'Pilih Jadwal Diskusi' : 'Select Time Slot'}</span>
                </button>
              </div>

              {/* Security & Reliability Badge */}
              <div className="flex items-center gap-2 text-xs text-slate-500">
                <ShieldCheck className="w-4 h-4 text-emerald-600 shrink-0" />
                <span>NDA signed upon request. Zero spam guarantee.</span>
              </div>
            </div>
          </motion.div>

          {/* Right Column: Interactive Inquiry Form */}
          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: false, amount: 0.15 }}
            transition={{ duration: 0.6 }}
            className="lg:col-span-7"
          >
            <div className="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-9 border border-white/80 shadow-xl shadow-slate-200/50 space-y-6">
              <div className="space-y-1">
                <h3 className="text-xl font-extrabold text-slate-900">
                  {lang === 'id' ? 'Kirim Pesan atau Brief Proyek' : 'Send Message or Project Brief'}
                </h3>
                <p className="text-xs text-slate-500">
                  {lang === 'id' ? 'Isi formulir di bawah untuk mendapatkan estimasi penawaran dan solusi teknis.' : 'Fill out the form below to receive project estimates and architectural feedback.'}
                </p>
              </div>

              {submitted ? (
                <motion.div
                  initial={{ opacity: 0, scale: 0.95 }}
                  animate={{ opacity: 1, scale: 1 }}
                  className="p-6 sm:p-8 text-center bg-emerald-50/80 backdrop-blur-md rounded-2xl border border-emerald-200 space-y-4"
                >
                  <div className="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center mx-auto shadow-md shadow-emerald-600/30">
                    <CheckCircle2 className="w-6 h-6" />
                  </div>
                  <div className="space-y-1">
                    <h4 className="text-lg font-bold text-emerald-950">
                      {lang === 'id' ? 'Pesan Berhasil Terkirim!' : 'Message Received Successfully!'}
                    </h4>
                    <p className="text-xs text-emerald-800 max-w-md mx-auto">
                      {lang === 'id'
                        ? `Terima kasih ${formData.name}. Saya akan meninjau rincian proyek Anda dan membalas melalui ${formData.email} dalam kurun waktu kurang dari 24 jam.`
                        : `Thank you ${formData.name}. I will review your requirements and respond back to ${formData.email} within 24 hours.`}
                    </p>
                  </div>
                  <button
                    onClick={() => {
                      setSubmitted(false);
                      setFormData({
                        name: '',
                        email: '',
                        projectType: 'Full-Stack Web App',
                        budget: '$3k - $8k (IDR 45M - 120M)',
                        timeline: '1 - 2 Bulan',
                        message: '',
                      });
                    }}
                    className="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer shadow-xs"
                  >
                    {lang === 'id' ? 'Kirim Pesan Lain' : 'Send Another Message'}
                  </button>
                </motion.div>
              ) : (
                <form onSubmit={handleSubmit} className="space-y-5">
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-1.5">
                      <label className="block text-xs font-bold text-slate-800">
                        {lang === 'id' ? 'Nama Lengkap *' : 'Full Name *'}
                      </label>
                      <input
                        type="text"
                        required
                        placeholder="e.g. Raditya Pratama"
                        value={formData.name}
                        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                        className="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors"
                      />
                    </div>

                    <div className="space-y-1.5">
                      <label className="block text-xs font-bold text-slate-800">
                        {lang === 'id' ? 'Alamat Email *' : 'Email Address *'}
                      </label>
                      <input
                        type="email"
                        required
                        placeholder="e.g. name@company.com"
                        value={formData.email}
                        onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                        className="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors"
                      />
                    </div>
                  </div>

                  {/* Project Type Selector */}
                  <div className="space-y-1.5">
                    <label className="block text-xs font-bold text-slate-800">
                      {lang === 'id' ? 'Kategori Kebutuhan Proyek' : 'Project Scope & Type'}
                    </label>
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
                      {[
                        'Full-Stack App',
                        'Frontend System',
                        'Design System',
                        'Consulting / Audit',
                      ].map((t) => (
                        <button
                          key={t}
                          type="button"
                          onClick={() => setFormData({ ...formData, projectType: t })}
                          className={`p-2.5 rounded-xl text-xs font-semibold border text-center transition-all cursor-pointer ${
                            formData.projectType === t
                              ? 'bg-indigo-50/90 backdrop-blur-md border-indigo-400 text-indigo-700 font-bold shadow-2xs'
                              : 'bg-white/60 backdrop-blur-xs border-white/80 text-slate-600 hover:bg-white/90'
                          }`}
                        >
                          {t}
                        </button>
                      ))}
                    </div>
                  </div>

                  {/* Budget Selector */}
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="space-y-1.5">
                      <label className="block text-xs font-bold text-slate-800">
                        {lang === 'id' ? 'Estimasi Budget' : 'Estimated Budget'}
                      </label>
                      <select
                        value={formData.budget}
                        onChange={(e) => setFormData({ ...formData, budget: e.target.value })}
                        className="w-full px-3.5 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs"
                      >
                        <option>&lt; $3k (IDR 20M - 45M)</option>
                        <option>$3k - $8k (IDR 45M - 120M)</option>
                        <option>$8k - $15k (IDR 120M - 230M)</option>
                        <option>&gt; $15k (Enterprise Scale)</option>
                      </select>
                    </div>

                    <div className="space-y-1.5">
                      <label className="block text-xs font-bold text-slate-800">
                        {lang === 'id' ? 'Target Waktu (Timeline)' : 'Desired Timeline'}
                      </label>
                      <select
                        value={formData.timeline}
                        onChange={(e) => setFormData({ ...formData, timeline: e.target.value })}
                        className="w-full px-3.5 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs"
                      >
                        <option>Segera (&lt; 1 Bulan)</option>
                        <option>1 - 2 Bulan</option>
                        <option>3 - 6 Bulan</option>
                        <option>Jangka Panjang / Retainer</option>
                      </select>
                    </div>
                  </div>

                  {/* Message Body */}
                  <div className="space-y-1.5">
                    <label className="block text-xs font-bold text-slate-800">
                      {lang === 'id' ? 'Deskripsi Proyek & Tujuan Utama *' : 'Project Details & Goals *'}
                    </label>
                    <textarea
                      required
                      rows={4}
                      placeholder={
                        lang === 'id'
                          ? 'Ceritakan tentang produk yang ingin dibangun, target pengguna, fitur utama, atau kendala performa yang dialami saat ini...'
                          : 'Tell me about the product you want to build, user goals, key milestones...'
                      }
                      value={formData.message}
                      onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                      className="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs text-slate-800 focus:bg-white focus:outline-indigo-500 resize-none transition-colors shadow-2xs"
                    />
                  </div>

                  {/* Submit Button */}
                  <button
                    id="contact-form-submit-btn"
                    type="submit"
                    disabled={isSubmitting}
                    className="w-full py-3.5 bg-slate-900 hover:bg-indigo-600 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md hover:shadow-indigo-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                  >
                    {isSubmitting ? (
                      <span>{lang === 'id' ? 'Mengirim pesan...' : 'Sending message...'}</span>
                    ) : (
                      <>
                        <Send className="w-4 h-4" />
                        <span>{lang === 'id' ? 'Kirim Brief & Mulai Diskusi' : 'Send Brief & Start Discussion'}</span>
                      </>
                    )}
                  </button>
                </form>
              )}
            </div>
          </motion.div>
        </div>

        {/* Social Media Integration Grid Section */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: false, amount: 0.15 }}
          transition={{ duration: 0.6 }}
          className="pt-8 border-t border-slate-200/80 space-y-6"
        >
          <div className="text-center max-w-2xl mx-auto space-y-1.5">
            <h3 className="text-xl font-extrabold text-slate-900">
              {lang === 'id' ? 'Temukan & Terhubung di Platform Lain' : 'Connect Across All Digital Channels'}
            </h3>
            <p className="text-xs text-slate-500">
              {lang === 'id'
                ? 'Ikuti kabar terbaru seputar artikel teknologi, live code streaming, dan update repositori.'
                : 'Follow tech blogs, GitHub open source repos, and engineering updates.'}
            </p>
          </div>

          <SocialBar lang={lang} variant="cards" />
        </motion.div>
      </div>

      {/* Discovery Call Booking Modal */}
      <AnimatePresence>
        {calendarModalOpen && (
          <div
            className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            onClick={() => setCalendarModalOpen(false)}
          >
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6"
              onClick={(e) => e.stopPropagation()}
            >
              <div className="flex items-center justify-between pb-3 border-b border-slate-100">
                <div className="flex items-center gap-2 text-indigo-600">
                  <Calendar className="w-5 h-5" />
                  <h4 className="font-bold text-slate-900 text-base">Schedule 15-min Call</h4>
                </div>
                <button
                  onClick={() => setCalendarModalOpen(false)}
                  className="text-slate-400 hover:text-slate-700 text-xs font-bold"
                >
                  ✕
                </button>
              </div>

              {callBooked ? (
                <div className="text-center py-6 space-y-2">
                  <div className="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                    <Check className="w-6 h-6" />
                  </div>
                  <h5 className="font-bold text-slate-900 text-sm">Jadwal Berhasil Dikonfirmasi!</h5>
                  <p className="text-xs text-slate-500">
                    Undangan Google Meet telah dikirim ke kalender Anda untuk sesi <strong>{selectedSlot}</strong>.
                  </p>
                </div>
              ) : (
                <form onSubmit={handleBookCall} className="space-y-4">
                  <p className="text-xs text-slate-600">
                    Pilih slot waktu yang cocok untuk diskusi teknis via Google Meet:
                  </p>

                  <div className="space-y-2">
                    {[
                      'Senin, 10:00 - 10:15 WIB (GMT+7)',
                      'Selasa, 14:30 - 14:45 WIB (GMT+7)',
                      'Rabu, 16:00 - 16:15 WIB (GMT+7)',
                      'Kamis, 11:00 - 11:15 WIB (GMT+7)',
                    ].map((slot) => (
                      <div
                        key={slot}
                        onClick={() => setSelectedSlot(slot)}
                        className={`p-3 rounded-xl border text-xs font-medium cursor-pointer transition-all ${
                          selectedSlot === slot
                            ? 'bg-indigo-50 border-indigo-600 text-indigo-900 font-bold'
                            : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'
                        }`}
                      >
                        {slot}
                      </div>
                    ))}
                  </div>

                  <button
                    type="submit"
                    disabled={!selectedSlot}
                    className="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-colors disabled:opacity-50 cursor-pointer"
                  >
                    Konfirmasi Slot Pertemuan
                  </button>
                </form>
              )}
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </section>
  );
};
