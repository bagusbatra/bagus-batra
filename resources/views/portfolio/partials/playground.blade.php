<section id="playground" x-data="playground()" class="py-20 sm:py-24 bg-transparent border-b border-white/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-12">
        {{-- Section Header --}}
        <div data-reveal class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-purple-50/80 backdrop-blur-md text-purple-700 border border-purple-100 text-xs font-bold uppercase tracking-wider">
                <x-icon name="sparkles" class="w-3.5 h-3.5" />
                <span x-show="$store.lang.current === 'id'">Eksplorasi Interaktif</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Interactive UI Lab</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                <span x-show="$store.lang.current === 'id'">UI Craftsmanship &amp; Live Component Sandbox</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>UI Craftsmanship &amp; Live Micro-Interaction Lab</span>
            </h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                <span x-show="$store.lang.current === 'id'">Cobalah secara langsung bagaimana sentuhan fisika pegas (spring physics), token warna dinamis, dan optimistic UI menghidupkan pengalaman pengguna tanpa jeda.</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Experiment live with natural spring physics, dynamic design tokens, and instant optimistic state updates.</span>
            </p>
        </div>

        {{-- Experiment Tab Switcher --}}
        <div data-reveal class="flex items-center gap-1 p-1.5 bg-white/50 backdrop-blur-md rounded-2xl border border-white/80 max-w-xl shadow-2xs overflow-x-auto scrollbar-none">
            <button @click="tab = 'spring'" id="tab-spring-lab" class="relative flex-1 flex items-center justify-center gap-2 py-2.5 px-3 sm:px-4 rounded-xl text-xs font-bold transition-colors cursor-pointer shrink-0" :class="tab === 'spring' ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-slate-900'">
                <span x-show="tab === 'spring'" class="absolute inset-0 bg-white rounded-xl shadow-2xs border border-white/90 -z-10"></span>
                <x-icon name="activity" class="w-4 h-4" />
                <span x-show="$store.lang.current === 'id'">Spring Physics</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Spring Physics</span>
            </button>
            <button @click="tab = 'theme'" id="tab-theme-lab" class="relative flex-1 flex items-center justify-center gap-2 py-2.5 px-3 sm:px-4 rounded-xl text-xs font-bold transition-colors cursor-pointer shrink-0" :class="tab === 'theme' ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-slate-900'">
                <span x-show="tab === 'theme'" class="absolute inset-0 bg-white rounded-xl shadow-2xs border border-white/90 -z-10"></span>
                <x-icon name="palette" class="w-4 h-4" />
                <span x-show="$store.lang.current === 'id'">Design Tokens</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Design Tokens</span>
            </button>
            <button @click="tab = 'optimistic'" id="tab-optimistic-lab" class="relative flex-1 flex items-center justify-center gap-2 py-2.5 px-3 sm:px-4 rounded-xl text-xs font-bold transition-colors cursor-pointer shrink-0" :class="tab === 'optimistic' ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-slate-900'">
                <span x-show="tab === 'optimistic'" class="absolute inset-0 bg-white rounded-xl shadow-2xs border border-white/90 -z-10"></span>
                <x-icon name="zap" class="w-4 h-4" />
                <span x-show="$store.lang.current === 'id'">Optimistic UI</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Optimistic UI</span>
            </button>
        </div>

        {{-- Playground Canvas Container --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">
            {{-- Controls Column --}}
            <div data-reveal class="lg:col-span-5 bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-7 border border-white/80 shadow-2xs space-y-6">
                {{-- Spring controls --}}
                <div x-show="tab === 'spring'" class="space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200/60">
                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <x-icon name="sliders" class="w-4 h-4 text-indigo-600" />
                            <span>Spring Parameters</span>
                        </h4>
                        <button @click="resetSpring()" class="text-xs text-slate-500 hover:text-indigo-600 flex items-center gap-1 cursor-pointer">
                            <x-icon name="rotate-ccw" class="w-3 h-3" />
                            Reset
                        </button>
                    </div>

                    <div class="space-y-4 text-xs font-medium text-slate-700">
                        <div>
                            <div class="flex justify-between mb-1.5">
                                <span>Stiffness (Kekakuan):</span>
                                <span class="font-mono font-bold text-indigo-600" x-text="stiffness"></span>
                            </div>
                            <input type="range" min="100" max="600" step="10" x-model.number="stiffness" class="w-full accent-indigo-600 cursor-pointer h-2 bg-slate-200 rounded-lg" />
                        </div>

                        <div>
                            <div class="flex justify-between mb-1.5">
                                <span>Damping (Peredaman):</span>
                                <span class="font-mono font-bold text-indigo-600" x-text="damping"></span>
                            </div>
                            <input type="range" min="5" max="40" step="1" x-model.number="damping" class="w-full accent-indigo-600 cursor-pointer h-2 bg-slate-200 rounded-lg" />
                        </div>

                        <div>
                            <div class="flex justify-between mb-1.5">
                                <span>Hover Scale Factor:</span>
                                <span class="font-mono font-bold text-indigo-600" x-text="scaleFactor.toFixed(2) + 'x'"></span>
                            </div>
                            <input type="range" min="1.0" max="1.3" step="0.02" x-model.number="scaleFactor" class="w-full accent-indigo-600 cursor-pointer h-2 bg-slate-200 rounded-lg" />
                        </div>
                    </div>

                    <div class="p-3.5 bg-indigo-50/70 backdrop-blur-md rounded-2xl text-xs text-indigo-900 leading-relaxed border border-indigo-100">
                        💡 <strong>Insight:</strong> Spring physics memberikan sensasi organik alami tanpa kurva statis yang kaku.
                    </div>
                </div>

                {{-- Theme controls --}}
                <div x-show="tab === 'theme'" class="space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200/60">
                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <x-icon name="palette" class="w-4 h-4 text-indigo-600" />
                            <span>Design Token Controls</span>
                        </h4>
                    </div>

                    <div class="space-y-4 text-xs font-medium text-slate-700">
                        <div>
                            <span class="block mb-2 font-bold text-slate-800">Accent Palette:</span>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <template x-for="c in ['indigo','emerald','rose','amber','cyan']" :key="c">
                                    <button
                                        @click="accent = c"
                                        class="w-8 h-8 rounded-full transition-all cursor-pointer flex items-center justify-center text-white shadow-xs"
                                        :class="[c === 'indigo' ? 'bg-indigo-600' : c === 'emerald' ? 'bg-emerald-600' : c === 'rose' ? 'bg-rose-600' : c === 'amber' ? 'bg-amber-600' : 'bg-cyan-600', accent === c ? 'ring-2 ring-offset-2 ring-slate-800 scale-110' : 'opacity-80 hover:opacity-100']"
                                    >
                                        <x-icon name="check" class="w-4 h-4" x-show="accent === c" />
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1.5">
                                <span>Border Radius:</span>
                                <span class="font-mono font-bold text-slate-900" x-text="radius + 'px'"></span>
                            </div>
                            <input type="range" min="4" max="32" step="2" x-model.number="radius" class="w-full accent-indigo-600 cursor-pointer h-2 bg-slate-200 rounded-lg" />
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="font-bold text-slate-800">Backdrop Blur (Glassmorphism):</span>
                            <button @click="glass = !glass" class="w-11 h-6 rounded-full transition-colors cursor-pointer relative p-0.5" :class="glass ? 'bg-indigo-600' : 'bg-slate-300'">
                                <div class="w-5 h-5 rounded-full bg-white transition-transform" :class="glass ? 'translate-x-5' : 'translate-x-0'"></div>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Optimistic UI controls --}}
                <div x-show="tab === 'optimistic'" class="space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200/60">
                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <x-icon name="zap" class="w-4 h-4 text-amber-500" />
                            <span>Network Latency Simulation</span>
                        </h4>
                    </div>

                    <div class="space-y-4 text-xs font-medium text-slate-700">
                        <div>
                            <div class="flex justify-between mb-1.5">
                                <span>Simulated Latency:</span>
                                <span class="font-mono font-bold text-amber-600" x-text="latency + ' ms'"></span>
                            </div>
                            <input type="range" min="100" max="2000" step="100" x-model.number="latency" class="w-full accent-amber-500 cursor-pointer h-2 bg-slate-200 rounded-lg" />
                        </div>

                        <form @submit.prevent="addOptimisticTodo()" class="space-y-2 pt-2">
                            <label class="block text-xs font-bold text-slate-800">
                                <span x-show="$store.lang.current === 'id'">Tambahkan Tugas (Optimistic):</span>
                                <span x-show="$store.lang.current === 'en'" x-cloak>Add Task (Optimistic):</span>
                            </label>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    x-model="newTodoText"
                                    :placeholder="$store.lang.current === 'id' ? 'Ketik item baru...' : 'Type new task...'"
                                    class="flex-1 px-3.5 py-2.5 bg-white/90 rounded-xl border border-white/90 text-xs focus:outline-indigo-500 shadow-2xs"
                                />
                                <button type="submit" :disabled="isSimulating" class="px-4 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer disabled:opacity-50 shadow-xs">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Interactive Preview Canvas --}}
            <div data-reveal class="lg:col-span-7 bg-slate-900/90 backdrop-blur-xl rounded-3xl p-5 sm:p-8 text-slate-100 shadow-xl border border-white/10 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 text-xs font-mono text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="ml-2 font-bold text-slate-300">Live Stage &amp; Component Preview</span>
                    </div>
                    <span class="text-indigo-400">interactive: true</span>
                </div>

                {{-- Spring demo --}}
                <div x-show="tab === 'spring'" class="py-8 sm:py-10 flex flex-col items-center justify-center space-y-6 text-center">
                    <p class="text-xs text-slate-400 max-w-sm">
                        <span x-show="$store.lang.current === 'id'">Arahkan kursor &amp; klik tombol di bawah untuk merasakan respons pegas dinamis:</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Hover and click the button below to feel the natural kinetic bounce:</span>
                    </p>

                    <button
                        id="interactive-spring-btn"
                        @click="triggerClick()"
                        @mouseenter="hovered = true"
                        @mouseleave="hovered = false"
                        @mousedown="pressed = true"
                        @mouseup="pressed = false"
                        :style="springStyle()"
                        class="px-6 sm:px-8 py-3.5 sm:py-4 bg-gradient-to-r from-indigo-500 to-blue-600 text-white font-extrabold text-sm sm:text-base rounded-2xl shadow-lg shadow-indigo-500/30 flex items-center gap-3 cursor-pointer select-none"
                    >
                        <x-icon name="sparkles" class="w-5 h-5 text-amber-300" />
                        <span x-show="$store.lang.current === 'id'">Klik Saya! (Confetti + Spring)</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Tap Me! (Confetti + Spring)</span>
                    </button>

                    <div class="text-xs font-mono text-slate-400 bg-slate-950/80 px-4 py-2 rounded-xl border border-slate-800">
                        Total Interaksi: <span class="text-amber-400 font-bold" x-text="clickCount"></span> clicks
                    </div>
                </div>

                {{-- Theme demo --}}
                <div x-show="tab === 'theme'" class="py-4 space-y-5">
                    <div
                        :style="'border-radius: ' + radius + 'px'"
                        class="p-5 sm:p-6 border transition-all duration-300"
                        :class="glass ? 'bg-slate-800/60 backdrop-blur-lg border-slate-700/80 shadow-lg' : 'bg-slate-800 border-slate-700'"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" :class="accentClasses().bg"></div>
                                <h5 class="font-bold text-white text-sm">Theme Tokenized Card</h5>
                            </div>
                            <span class="text-[10px] font-mono font-bold uppercase px-2 py-0.5 rounded-md" :class="[accentClasses().lightBg, accentClasses().text]" x-text="accent"></span>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed mb-4">
                            Komponen ini secara dinamis mengadopsi token radius (<span x-text="radius"></span>px) dan palet aksen aktif tanpa CSS duplication.
                        </p>
                        <button :style="'border-radius: ' + Math.max(6, radius - 6) + 'px'" class="px-4 py-2 text-white font-bold text-xs shadow-xs hover:opacity-90 transition-opacity cursor-pointer" :class="accentClasses().bg">
                            Action Button
                        </button>
                    </div>
                </div>

                {{-- Optimistic UI demo --}}
                <div x-show="tab === 'optimistic'" class="py-2 space-y-4">
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>Daftar Tugas (Klik untuk toggle):</span>
                        <span x-show="isSimulating" class="text-amber-400 flex items-center gap-1 animate-pulse font-mono text-[11px]">
                            <x-icon name="activity" class="w-3 h-3" />
                            Syncing (<span x-text="latency"></span>ms)...
                        </span>
                    </div>

                    <div class="space-y-2">
                        <template x-for="t in todos" :key="t.id">
                            <div @click="toggleTodo(t.id)" class="p-3 rounded-xl border flex items-center justify-between text-xs cursor-pointer transition-all" :class="t.completed ? 'bg-slate-950/60 border-slate-800 text-slate-500 line-through' : 'bg-slate-800/80 backdrop-blur-md border-slate-700 text-slate-200'">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-4 h-4 rounded-md border flex items-center justify-center" :class="t.completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-500'">
                                        <x-icon name="check" class="w-3 h-3" x-show="t.completed" />
                                    </div>
                                    <span class="break-all" x-text="t.text"></span>
                                </div>
                                <span x-show="t.isOptimistic" class="text-[10px] font-mono text-amber-400 bg-amber-950 px-2 py-0.5 rounded-sm shrink-0">Optimistic</span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Generated Code Snippet Footer --}}
                <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
                    <span class="font-mono text-slate-400 text-[11px] break-all" x-text="snippetLabel()"></span>
                    <button @click="copySnippet()" class="flex items-center gap-1 text-slate-400 hover:text-white transition-colors cursor-pointer shrink-0" title="Salin snippet kode">
                        <x-icon name="check" class="w-3.5 h-3.5 text-emerald-400" x-show="copiedCode" />
                        <x-icon name="copy" class="w-3.5 h-3.5" x-show="!copiedCode" />
                        <span class="text-[11px] font-bold" x-text="copiedCode ? 'Tersalin' : 'Copy'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
