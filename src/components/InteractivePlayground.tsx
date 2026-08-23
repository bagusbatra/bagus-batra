import React, { useState } from 'react';
import { motion } from 'motion/react';
import {
  Sparkles,
  Sliders,
  Play,
  RotateCcw,
  Copy,
  Check,
  Zap,
  Code2,
  Palette,
  Activity,
  Layers,
  MousePointer
} from 'lucide-react';
import confetti from 'canvas-confetti';
import { Language } from '../types';

interface InteractivePlaygroundProps {
  lang: Language;
}

export const InteractivePlayground: React.FC<InteractivePlaygroundProps> = ({ lang }) => {
  const [activeTab, setActiveTab] = useState<'spring' | 'theme' | 'optimistic'>('spring');

  // Spring Demo State
  const [stiffness, setStiffness] = useState(300);
  const [damping, setDamping] = useState(20);
  const [scaleFactor, setScaleFactor] = useState(1.1);
  const [clickCount, setClickCount] = useState(0);

  // Theme Token State
  const [accentColor, setAccentColor] = useState<'indigo' | 'emerald' | 'rose' | 'amber' | 'cyan'>('indigo');
  const [borderRadius, setBorderRadius] = useState<number>(16);
  const [glassmorphism, setGlassmorphism] = useState<boolean>(true);

  // Optimistic UI Demo State
  const [latencyMs, setLatencyMs] = useState<number>(600);
  const [todos, setTodos] = useState<{ id: number; text: string; completed: boolean; isOptimistic?: boolean }[]>([
    { id: 1, text: 'Optimasi Web Vitals LCP < 1.2s', completed: true },
    { id: 2, text: 'Migrasi ke React 19 Server Actions', completed: false },
    { id: 3, text: 'Setup Vitest automated coverage', completed: false },
  ]);
  const [newTodoText, setNewTodoText] = useState('');
  const [isSimulating, setIsSimulating] = useState(false);
  const [copiedCode, setCopiedCode] = useState(false);

  const handleTriggerConfetti = () => {
    setClickCount((prev) => prev + 1);
    confetti({
      particleCount: 30,
      spread: 60,
      origin: { y: 0.7 },
      colors: ['#6366f1', '#10b981', '#f59e0b']
    });
  };

  const handleAddOptimisticTodo = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newTodoText.trim()) return;

    const tempId = Date.now();
    const newEntry = {
      id: tempId,
      text: newTodoText.trim(),
      completed: false,
      isOptimistic: true,
    };

    // Instant optimistic update
    setTodos((prev) => [...prev, newEntry]);
    setNewTodoText('');
    setIsSimulating(true);

    // Simulate backend response after latency
    setTimeout(() => {
      setTodos((prev) =>
        prev.map((t) => (t.id === tempId ? { ...t, isOptimistic: false } : t))
      );
      setIsSimulating(false);
    }, latencyMs);
  };

  const toggleTodo = (id: number) => {
    setTodos((prev) =>
      prev.map((t) => (t.id === id ? { ...t, completed: !t.completed } : t))
    );
  };

  const getAccentColorClasses = () => {
    switch (accentColor) {
      case 'emerald':
        return {
          bg: 'bg-emerald-600',
          text: 'text-emerald-600',
          border: 'border-emerald-200',
          lightBg: 'bg-emerald-50',
          ring: 'focus:ring-emerald-500',
        };
      case 'rose':
        return {
          bg: 'bg-rose-600',
          text: 'text-rose-600',
          border: 'border-rose-200',
          lightBg: 'bg-rose-50',
          ring: 'focus:ring-rose-500',
        };
      case 'amber':
        return {
          bg: 'bg-amber-600',
          text: 'text-amber-600',
          border: 'border-amber-200',
          lightBg: 'bg-amber-50',
          ring: 'focus:ring-amber-500',
        };
      case 'cyan':
        return {
          bg: 'bg-cyan-600',
          text: 'text-cyan-600',
          border: 'border-cyan-200',
          lightBg: 'bg-cyan-50',
          ring: 'focus:ring-cyan-500',
        };
      default:
        return {
          bg: 'bg-indigo-600',
          text: 'text-indigo-600',
          border: 'border-indigo-200',
          lightBg: 'bg-indigo-50',
          ring: 'focus:ring-indigo-500',
        };
    }
  };

  const currentAccent = getAccentColorClasses();

  const handleCopySnippet = (snippet: string) => {
    navigator.clipboard.writeText(snippet);
    setCopiedCode(true);
    setTimeout(() => setCopiedCode(false), 2000);
  };

  return (
    <section id="playground" className="py-20 sm:py-24 bg-transparent border-b border-white/60">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-12">
        {/* Section Header */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: false, amount: 0.15 }}
          transition={{ duration: 0.6 }}
          className="max-w-3xl space-y-3"
        >
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-purple-50/80 backdrop-blur-md text-purple-700 border border-purple-100 text-xs font-bold uppercase tracking-wider">
            <Sparkles className="w-3.5 h-3.5" />
            <span>{lang === 'id' ? 'Eksplorasi Interaktif' : 'Interactive UI Lab'}</span>
          </div>
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
            {lang === 'id'
              ? 'UI Craftsmanship & Live Component Sandbox'
              : 'UI Craftsmanship & Live Micro-Interaction Lab'}
          </h2>
          <p className="text-slate-600 text-sm sm:text-base leading-relaxed">
            {lang === 'id'
              ? 'Cobalah secara langsung bagaimana sentuhan fisika pegas (spring physics), token warna dinamis, dan optimistic UI menghidupkan pengalaman pengguna tanpa jeda.'
              : 'Experiment live with natural spring physics, dynamic design tokens, and instant optimistic state updates.'}
          </p>
        </motion.div>

        {/* Experiment Tab Switcher */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: false, amount: 0.15 }}
          transition={{ duration: 0.5, delay: 0.1 }}
          className="flex items-center gap-1 p-1.5 bg-white/50 backdrop-blur-md rounded-2xl border border-white/80 max-w-xl shadow-2xs overflow-x-auto scrollbar-none"
        >
          {[
            { id: 'spring', labelId: 'Spring Physics', labelEn: 'Spring Physics', icon: <Activity className="w-4 h-4" /> },
            { id: 'theme', labelId: 'Design Tokens', labelEn: 'Design Tokens', icon: <Palette className="w-4 h-4" /> },
            { id: 'optimistic', labelId: 'Optimistic UI', labelEn: 'Optimistic UI', icon: <Zap className="w-4 h-4" /> },
          ].map((tab) => {
            const isActive = activeTab === tab.id;
            return (
              <button
                key={tab.id}
                id={`tab-${tab.id}-lab`}
                onClick={() => setActiveTab(tab.id as any)}
                className={`relative flex-1 flex items-center justify-center gap-2 py-2.5 px-3 sm:px-4 rounded-xl text-xs font-bold transition-colors cursor-pointer shrink-0 ${
                  isActive ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-slate-900'
                }`}
              >
                {isActive && (
                  <motion.div
                    layoutId="activePlaygroundTab"
                    className="absolute inset-0 bg-white rounded-xl shadow-2xs border border-white/90 -z-10"
                    transition={{ type: 'spring', stiffness: 350, damping: 28 }}
                  />
                )}
                {tab.icon}
                <span>{lang === 'id' ? tab.labelId : tab.labelEn}</span>
              </button>
            );
          })}
        </motion.div>

        {/* Playground Canvas Container */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
          {/* Controls Column */}
          <motion.div
            initial={{ opacity: 0, x: -25 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: false, amount: 0.15 }}
            transition={{ duration: 0.6 }}
            className="lg:col-span-5 bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-7 border border-white/80 shadow-2xs space-y-6"
          >
            {activeTab === 'spring' && (
              <div className="space-y-5">
                <div className="flex items-center justify-between pb-3 border-b border-slate-200/60">
                  <h4 className="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <Sliders className="w-4 h-4 text-indigo-600" />
                    <span>Spring Parameters</span>
                  </h4>
                  <button
                    onClick={() => {
                      setStiffness(300);
                      setDamping(20);
                      setScaleFactor(1.1);
                    }}
                    className="text-xs text-slate-500 hover:text-indigo-600 flex items-center gap-1 cursor-pointer"
                  >
                    <RotateCcw className="w-3 h-3" />
                    Reset
                  </button>
                </div>

                <div className="space-y-4 text-xs font-medium text-slate-700">
                  <div>
                    <div className="flex justify-between mb-1.5">
                      <span>Stiffness (Kekakuan):</span>
                      <span className="font-mono font-bold text-indigo-600">{stiffness}</span>
                    </div>
                    <input
                      type="range"
                      min="100"
                      max="600"
                      step="10"
                      value={stiffness}
                      onChange={(e) => setStiffness(Number(e.target.value))}
                      className="w-full accent-indigo-600 cursor-pointer h-2 bg-slate-200 rounded-lg"
                    />
                  </div>

                  <div>
                    <div className="flex justify-between mb-1.5">
                      <span>Damping (Peredaman):</span>
                      <span className="font-mono font-bold text-indigo-600">{damping}</span>
                    </div>
                    <input
                      type="range"
                      min="5"
                      max="40"
                      step="1"
                      value={damping}
                      onChange={(e) => setDamping(Number(e.target.value))}
                      className="w-full accent-indigo-600 cursor-pointer h-2 bg-slate-200 rounded-lg"
                    />
                  </div>

                  <div>
                    <div className="flex justify-between mb-1.5">
                      <span>Hover Scale Factor:</span>
                      <span className="font-mono font-bold text-indigo-600">{scaleFactor.toFixed(2)}x</span>
                    </div>
                    <input
                      type="range"
                      min="1.0"
                      max="1.3"
                      step="0.02"
                      value={scaleFactor}
                      onChange={(e) => setScaleFactor(Number(e.target.value))}
                      className="w-full accent-indigo-600 cursor-pointer h-2 bg-slate-200 rounded-lg"
                    />
                  </div>
                </div>

                <div className="p-3.5 bg-indigo-50/70 backdrop-blur-md rounded-2xl text-xs text-indigo-900 leading-relaxed border border-indigo-100">
                  💡 <strong>Insight:</strong> Spring physics memberikan sensasi organik alami tanpa kurva statis yang kaku.
                </div>
              </div>
            )}

            {activeTab === 'theme' && (
              <div className="space-y-5">
                <div className="flex items-center justify-between pb-3 border-b border-slate-200/60">
                  <h4 className="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <Palette className="w-4 h-4 text-indigo-600" />
                    <span>Design Token Controls</span>
                  </h4>
                </div>

                <div className="space-y-4 text-xs font-medium text-slate-700">
                  <div>
                    <span className="block mb-2 font-bold text-slate-800">Accent Palette:</span>
                    <div className="flex items-center gap-2.5 flex-wrap">
                      {[
                        { id: 'indigo', label: 'Indigo', bg: 'bg-indigo-600' },
                        { id: 'emerald', label: 'Emerald', bg: 'bg-emerald-600' },
                        { id: 'rose', label: 'Rose', bg: 'bg-rose-600' },
                        { id: 'amber', label: 'Amber', bg: 'bg-amber-600' },
                        { id: 'cyan', label: 'Cyan', bg: 'bg-cyan-600' },
                      ].map((c) => (
                        <button
                          key={c.id}
                          onClick={() => setAccentColor(c.id as any)}
                          className={`w-8 h-8 rounded-full ${c.bg} transition-all cursor-pointer flex items-center justify-center text-white shadow-xs ${
                            accentColor === c.id ? 'ring-2 ring-offset-2 ring-slate-800 scale-110' : 'opacity-80 hover:opacity-100'
                          }`}
                        >
                          {accentColor === c.id && <Check className="w-4 h-4" />}
                        </button>
                      ))}
                    </div>
                  </div>

                  <div>
                    <div className="flex justify-between mb-1.5">
                      <span>Border Radius:</span>
                      <span className="font-mono font-bold text-slate-900">{borderRadius}px</span>
                    </div>
                    <input
                      type="range"
                      min="4"
                      max="32"
                      step="2"
                      value={borderRadius}
                      onChange={(e) => setBorderRadius(Number(e.target.value))}
                      className="w-full accent-indigo-600 cursor-pointer h-2 bg-slate-200 rounded-lg"
                    />
                  </div>

                  <div className="flex items-center justify-between pt-2">
                    <span className="font-bold text-slate-800">Backdrop Blur (Glassmorphism):</span>
                    <button
                      onClick={() => setGlassmorphism(!glassmorphism)}
                      className={`w-11 h-6 rounded-full transition-colors cursor-pointer relative p-0.5 ${
                        glassmorphism ? 'bg-indigo-600' : 'bg-slate-300'
                      }`}
                    >
                      <div
                        className={`w-5 h-5 rounded-full bg-white transition-transform ${
                          glassmorphism ? 'translate-x-5' : 'translate-x-0'
                        }`}
                      />
                    </button>
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'optimistic' && (
              <div className="space-y-5">
                <div className="flex items-center justify-between pb-3 border-b border-slate-200/60">
                  <h4 className="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <Zap className="w-4 h-4 text-amber-500" />
                    <span>Network Latency Simulation</span>
                  </h4>
                </div>

                <div className="space-y-4 text-xs font-medium text-slate-700">
                  <div>
                    <div className="flex justify-between mb-1.5">
                      <span>Simulated Latency:</span>
                      <span className="font-mono font-bold text-amber-600">{latencyMs} ms</span>
                    </div>
                    <input
                      type="range"
                      min="100"
                      max="2000"
                      step="100"
                      value={latencyMs}
                      onChange={(e) => setLatencyMs(Number(e.target.value))}
                      className="w-full accent-amber-500 cursor-pointer h-2 bg-slate-200 rounded-lg"
                    />
                  </div>

                  <form onSubmit={handleAddOptimisticTodo} className="space-y-2 pt-2">
                    <label className="block text-xs font-bold text-slate-800">
                      {lang === 'id' ? 'Tambahkan Tugas (Optimistic):' : 'Add Task (Optimistic):'}
                    </label>
                    <div className="flex gap-2">
                      <input
                        type="text"
                        placeholder={lang === 'id' ? 'Ketik item baru...' : 'Type new task...'}
                        value={newTodoText}
                        onChange={(e) => setNewTodoText(e.target.value)}
                        className="flex-1 px-3.5 py-2.5 bg-white/90 rounded-xl border border-white/90 text-xs focus:outline-indigo-500 shadow-2xs"
                      />
                      <button
                        type="submit"
                        disabled={isSimulating}
                        className="px-4 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer disabled:opacity-50 shadow-xs"
                      >
                        Add
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            )}
          </motion.div>

          {/* Interactive Preview Canvas */}
          <motion.div
            initial={{ opacity: 0, x: 25 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: false, amount: 0.15 }}
            transition={{ duration: 0.6 }}
            className="lg:col-span-7 bg-slate-900/90 backdrop-blur-xl rounded-3xl p-5 sm:p-8 text-slate-100 shadow-xl border border-white/10 space-y-6"
          >
            <div className="flex items-center justify-between pb-4 border-b border-slate-800 text-xs font-mono text-slate-400">
              <div className="flex items-center gap-2">
                <span className="w-3 h-3 rounded-full bg-rose-500" />
                <span className="w-3 h-3 rounded-full bg-amber-500" />
                <span className="w-3 h-3 rounded-full bg-emerald-500" />
                <span className="ml-2 font-bold text-slate-300">Live Stage & Component Preview</span>
              </div>
              <span className="text-indigo-400">interactive: true</span>
            </div>

            {/* Interactive Demo View */}
            {activeTab === 'spring' && (
              <div className="py-8 sm:py-10 flex flex-col items-center justify-center space-y-6 text-center">
                <p className="text-xs text-slate-400 max-w-sm">
                  {lang === 'id'
                    ? 'Arahkan kursor & klik tombol di bawah untuk merasakan respons pegas dinamis:'
                    : 'Hover and click the button below to feel the natural kinetic bounce:'}
                </p>

                <motion.button
                  id="interactive-spring-btn"
                  onClick={handleTriggerConfetti}
                  whileHover={{ scale: scaleFactor }}
                  whileTap={{ scale: 0.95 }}
                  transition={{ type: 'spring', stiffness, damping }}
                  className="px-6 sm:px-8 py-3.5 sm:py-4 bg-gradient-to-r from-indigo-500 to-blue-600 text-white font-extrabold text-sm sm:text-base rounded-2xl shadow-lg shadow-indigo-500/30 flex items-center gap-3 cursor-pointer select-none"
                >
                  <Sparkles className="w-5 h-5 text-amber-300" />
                  <span>{lang === 'id' ? 'Klik Saya! (Confetti + Spring)' : 'Tap Me! (Confetti + Spring)'}</span>
                </motion.button>

                <div className="text-xs font-mono text-slate-400 bg-slate-950/80 px-4 py-2 rounded-xl border border-slate-800">
                  Total Interaksi: <span className="text-amber-400 font-bold">{clickCount}</span> clicks
                </div>
              </div>
            )}

            {activeTab === 'theme' && (
              <div className="py-4 space-y-5">
                <div
                  style={{ borderRadius: `${borderRadius}px` }}
                  className={`p-5 sm:p-6 border transition-all duration-300 ${
                    glassmorphism
                      ? 'bg-slate-800/60 backdrop-blur-lg border-slate-700/80 shadow-lg'
                      : 'bg-slate-800 border-slate-700'
                  }`}
                >
                  <div className="flex items-center justify-between mb-3">
                    <div className="flex items-center gap-2">
                      <div className={`w-3 h-3 rounded-full ${currentAccent.bg}`} />
                      <h5 className="font-bold text-white text-sm">Theme Tokenized Card</h5>
                    </div>
                    <span className={`text-[10px] font-mono font-bold uppercase px-2 py-0.5 rounded-md ${currentAccent.lightBg} ${currentAccent.text}`}>
                      {accentColor}
                    </span>
                  </div>
                  <p className="text-xs text-slate-300 leading-relaxed mb-4">
                    Komponen ini secara dinamis mengadopsi token radius ({borderRadius}px) dan palet aksen aktif tanpa CSS duplication.
                  </p>
                  <button
                    style={{ borderRadius: `${Math.max(6, borderRadius - 6)}px` }}
                    className={`px-4 py-2 ${currentAccent.bg} text-white font-bold text-xs shadow-xs hover:opacity-90 transition-opacity cursor-pointer`}
                  >
                    Action Button
                  </button>
                </div>
              </div>
            )}

            {activeTab === 'optimistic' && (
              <div className="py-2 space-y-4">
                <div className="flex items-center justify-between text-xs text-slate-400">
                  <span>Daftar Tugas (Klik untuk toggle):</span>
                  {isSimulating && (
                    <span className="text-amber-400 flex items-center gap-1 animate-pulse font-mono text-[11px]">
                      <Activity className="w-3 h-3" />
                      Syncing ({latencyMs}ms)...
                    </span>
                  )}
                </div>

                <div className="space-y-2">
                  {todos.map((t) => (
                    <div
                      key={t.id}
                      onClick={() => toggleTodo(t.id)}
                      className={`p-3 rounded-xl border flex items-center justify-between text-xs cursor-pointer transition-all ${
                        t.completed
                          ? 'bg-slate-950/60 border-slate-800 text-slate-500 line-through'
                          : 'bg-slate-800/80 backdrop-blur-md border-slate-700 text-slate-200'
                      }`}
                    >
                      <div className="flex items-center gap-2.5">
                        <div
                          className={`w-4 h-4 rounded-md border flex items-center justify-center ${
                            t.completed
                              ? 'bg-emerald-500 border-emerald-500 text-white'
                              : 'border-slate-500'
                          }`}
                        >
                          {t.completed && <Check className="w-3 h-3" />}
                        </div>
                        <span className="break-all">{t.text}</span>
                      </div>
                      {t.isOptimistic && (
                        <span className="text-[10px] font-mono text-amber-400 bg-amber-950 px-2 py-0.5 rounded-sm shrink-0">
                          Optimistic
                        </span>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Generated Code Snippet Footer */}
            <div className="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
              <span className="font-mono text-slate-400 text-[11px] break-all">
                {activeTab === 'spring' && `transition: { type: 'spring', stiffness: ${stiffness}, damping: ${damping} }`}
                {activeTab === 'theme' && `className: "rounded-[${borderRadius}px] ${currentAccent.bg}"`}
                {activeTab === 'optimistic' && `setTodos(prev => [...prev, optimisticItem])`}
              </span>
              <button
                onClick={() =>
                  handleCopySnippet(
                    activeTab === 'spring'
                      ? `transition={{ type: 'spring', stiffness: ${stiffness}, damping: ${damping} }}`
                      : activeTab === 'theme'
                      ? `className="rounded-[${borderRadius}px] ${currentAccent.bg}"`
                      : `setTodos(prev => [...prev, optimisticItem]);`
                  )
                }
                className="flex items-center gap-1 text-slate-400 hover:text-white transition-colors cursor-pointer shrink-0"
                title="Salin snippet kode"
              >
                {copiedCode ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5" />}
                <span className="text-[11px] font-bold">{copiedCode ? 'Tersalin' : 'Copy'}</span>
              </button>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};
