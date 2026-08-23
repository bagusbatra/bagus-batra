import React, { useState } from 'react';
import { motion } from 'motion/react';
import {
  Github,
  Linkedin,
  Twitter,
  Youtube,
  Instagram,
  Mail,
  Copy,
  Check,
  ExternalLink,
  MessageCircle
} from 'lucide-react';
import confetti from 'canvas-confetti';
import { SOCIAL_LINKS, PERSONAL_INFO } from '../data/portfolioData';
import { Language } from '../types';

interface SocialBarProps {
  lang: Language;
  variant?: 'floating' | 'horizontal' | 'compact' | 'cards';
}

export const SocialBar: React.FC<SocialBarProps> = ({ lang, variant = 'horizontal' }) => {
  const [copiedEmail, setCopiedEmail] = useState(false);

  const handleCopyEmail = (e: React.MouseEvent) => {
    e.preventDefault();
    navigator.clipboard.writeText(PERSONAL_INFO.email);
    setCopiedEmail(true);
    confetti({
      particleCount: 40,
      spread: 55,
      origin: { y: 0.8 },
      colors: ['#6366f1', '#3b82f6', '#10b981']
    });
    setTimeout(() => setCopiedEmail(false), 2400);
  };

  const getIcon = (iconName: string) => {
    switch (iconName) {
      case 'Github':
        return <Github className="w-4 h-4" />;
      case 'Linkedin':
        return <Linkedin className="w-4 h-4" />;
      case 'Twitter':
        return <Twitter className="w-4 h-4" />;
      case 'Youtube':
        return <Youtube className="w-4 h-4" />;
      case 'Instagram':
        return <Instagram className="w-4 h-4" />;
      case 'Mail':
        return <Mail className="w-4 h-4" />;
      default:
        return <ExternalLink className="w-4 h-4" />;
    }
  };

  if (variant === 'cards') {
    return (
      <div id="social-integration-grid" className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {SOCIAL_LINKS.map((link) => (
          <a
            key={link.platform}
            id={`social-card-${link.platform.toLowerCase()}`}
            href={link.url}
            target="_blank"
            rel="noopener noreferrer"
            className="group p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 hover:border-indigo-300 shadow-2xs hover:shadow-md transition-all duration-300 flex items-start gap-3.5 relative overflow-hidden"
          >
            <div className="w-11 h-11 rounded-xl bg-white/80 border border-white flex items-center justify-center text-slate-700 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-colors duration-200 shrink-0 shadow-2xs">
              {getIcon(link.icon)}
            </div>
            <div className="flex-1 min-w-0">
              <div className="flex items-center justify-between">
                <h4 className="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                  {link.name}
                </h4>
                <ExternalLink className="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
              </div>
              <p className="text-xs font-mono text-indigo-600 font-medium truncate mt-0.5">
                {link.username}
              </p>
              <p className="text-xs text-slate-500 mt-1 line-clamp-1">
                {link.description}
              </p>
            </div>
          </a>
        ))}

        {/* Quick WhatsApp Action Card */}
        <a
          id="social-card-whatsapp"
          href="https://wa.me/6281234567890?text=Halo%20Bagus,%20saya%20tertarik%20untuk%20bekerjasama%20proyek%20web"
          target="_blank"
          rel="noopener noreferrer"
          className="group p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 hover:border-emerald-300 shadow-2xs hover:shadow-md transition-all duration-300 flex items-start gap-3.5"
        >
          <div className="w-11 h-11 rounded-xl bg-emerald-50/80 backdrop-blur-md border border-emerald-100/60 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-200 shrink-0 shadow-2xs">
            <MessageCircle className="w-5 h-5" />
          </div>
          <div className="flex-1 min-w-0">
            <div className="flex items-center justify-between">
              <h4 className="text-sm font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">
                WhatsApp Direct
              </h4>
              <ExternalLink className="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-600 transition-transform" />
            </div>
            <p className="text-xs font-mono text-emerald-600 font-medium truncate mt-0.5">
              +62 812-3456-7890
            </p>
            <p className="text-xs text-slate-500 mt-1">
              {lang === 'id' ? 'Chat instan untuk diskusi cepat' : 'Instant chat for fast inquiries'}
            </p>
          </div>
        </a>

        {/* Copy Email Card */}
        <button
          id="social-card-copy-email"
          onClick={handleCopyEmail}
          className="group p-4.5 bg-gradient-to-br from-indigo-50/70 to-blue-50/70 backdrop-blur-lg rounded-2xl border border-indigo-100/90 hover:border-indigo-300 shadow-2xs hover:shadow-md transition-all duration-300 flex items-start gap-3.5 text-left cursor-pointer"
        >
          <div className="w-11 h-11 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-indigo-500/20">
            {copiedEmail ? <Check className="w-5 h-5" /> : <Copy className="w-5 h-5" />}
          </div>
          <div className="flex-1 min-w-0">
            <div className="flex items-center justify-between">
              <h4 className="text-sm font-bold text-indigo-950">
                {copiedEmail ? (lang === 'id' ? 'Tersalin ke Clipboard!' : 'Copied to Clipboard!') : (lang === 'id' ? 'Salin Alamat Email' : 'Copy Email Address')}
              </h4>
              <span className="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 bg-indigo-100/80 backdrop-blur-xs text-indigo-700 rounded-md border border-indigo-200/50">
                {copiedEmail ? 'Copied' : 'Click'}
              </span>
            </div>
            <p className="text-xs font-mono text-indigo-700 font-medium truncate mt-0.5">
              {PERSONAL_INFO.email}
            </p>
            <p className="text-xs text-indigo-900/60 mt-1">
              {lang === 'id' ? 'Klik untuk menyalin alamat email langsung' : 'Click to copy direct email address'}
            </p>
          </div>
        </button>
      </div>
    );
  }

  return (
    <div id="social-links-bar" className="flex flex-wrap items-center gap-2">
      {SOCIAL_LINKS.map((link) => (
        <a
          key={link.platform}
          id={`social-link-${link.platform.toLowerCase()}`}
          href={link.url}
          target="_blank"
          rel="noopener noreferrer"
          className="flex items-center gap-2 px-3 py-2 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-slate-700 hover:text-indigo-600 hover:border-indigo-200 hover:bg-white transition-all text-xs font-semibold shadow-2xs"
          title={`${link.name}: ${link.username}`}
        >
          {getIcon(link.icon)}
          <span className="hidden sm:inline">{link.name}</span>
        </a>
      ))}

      {/* Copy Email Pill Button */}
      <button
        id="quick-copy-email-btn"
        onClick={handleCopyEmail}
        className="flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50/80 backdrop-blur-md border border-indigo-200/80 text-indigo-700 hover:bg-indigo-100/90 rounded-xl transition-all text-xs font-semibold cursor-pointer shadow-2xs"
        title="Klik untuk menyalin email"
      >
        {copiedEmail ? (
          <>
            <Check className="w-3.5 h-3.5 text-emerald-600" />
            <span className="text-emerald-700">{lang === 'id' ? 'Email Tersalin!' : 'Copied!'}</span>
          </>
        ) : (
          <>
            <Copy className="w-3.5 h-3.5" />
            <span>{lang === 'id' ? 'Salin Email' : 'Copy Email'}</span>
          </>
        )}
      </button>
    </div>
  );
};
