<div
    id="project-modal-backdrop"
    x-show="$store.ui.activeProject"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="$store.ui.closeProject()"
    @keydown.escape.window="$store.ui.closeProject()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
    style="display: none;"
>
    <template x-if="$store.ui.activeProject">
        <div
            id="project-modal-dialog"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 scale-95 translate-y-5"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            @click.stop
            class="relative w-full max-w-4xl bg-white/95 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/80 overflow-hidden my-8"
        >
            {{-- Top Bar with Project Banner --}}
            <div class="relative h-64 sm:h-80 bg-slate-900 overflow-hidden">
                <img :src="$store.ui.activeProject.image" :alt="$store.ui.activeProject.title" class="w-full h-full object-cover opacity-60 mix-blend-luminosity scale-105" />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>

                <button id="close-project-modal-btn" @click="$store.ui.closeProject()" class="absolute top-4 right-4 p-2.5 rounded-full bg-white/20 hover:bg-white/40 text-white backdrop-blur-md transition-all cursor-pointer border border-white/30" aria-label="Tutup modal">
                    <x-icon name="x" class="w-5 h-5" />
                </button>

                <div class="absolute bottom-6 left-6 right-6 space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 bg-indigo-500/90 backdrop-blur-md text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-xs border border-indigo-400/40" x-text="$store.ui.activeProject.category"></span>
                        <span x-show="$store.ui.activeProject.featured" class="px-3 py-1 bg-amber-400/95 backdrop-blur-md text-amber-950 text-xs font-bold rounded-lg flex items-center gap-1 shadow-2xs">
                            <x-icon name="sparkles" class="w-3.5 h-3.5" />
                            Featured
                        </span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight" x-text="$store.ui.activeProject.title"></h2>
                    <p class="text-sm text-slate-300 max-w-2xl" x-text="$store.ui.activeProject.tagline"></p>
                </div>
            </div>

            {{-- Navigation Sub-Tabs --}}
            <div class="flex border-b border-slate-200/80 bg-white/60 backdrop-blur-md px-6 pt-3 gap-2">
                <button id="modal-tab-overview" @click="$store.ui.activeProjectTab = 'overview'" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors cursor-pointer" :class="$store.ui.activeProjectTab === 'overview' ? 'bg-white text-indigo-600 border-t-2 border-indigo-600 shadow-2xs' : 'text-slate-500 hover:text-slate-900'">
                    <span x-show="$store.lang.current === 'id'">Ikhtisar &amp; Solusi</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Overview &amp; Highlights</span>
                </button>
                <button id="modal-tab-architecture" @click="$store.ui.activeProjectTab = 'architecture'" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors cursor-pointer" :class="$store.ui.activeProjectTab === 'architecture' ? 'bg-white text-indigo-600 border-t-2 border-indigo-600 shadow-2xs' : 'text-slate-500 hover:text-slate-900'">
                    <span x-show="$store.lang.current === 'id'">Arsitektur Stack</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Tech Architecture</span>
                </button>
                <button id="modal-tab-preview" @click="$store.ui.activeProjectTab = 'preview'" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors cursor-pointer" :class="$store.ui.activeProjectTab === 'preview' ? 'bg-white text-indigo-600 border-t-2 border-indigo-600 shadow-2xs' : 'text-slate-500 hover:text-slate-900'">
                    <span x-show="$store.lang.current === 'id'">Simulasi Interaktif</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Live Preview</span>
                </button>
            </div>

            {{-- Modal Content Body --}}
            <div class="p-6 sm:p-8 space-y-6 max-h-[60vh] overflow-y-auto">
                {{-- Overview Tab --}}
                <div x-show="$store.ui.activeProjectTab === 'overview'" class="space-y-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-white/70 backdrop-blur-md rounded-2xl border border-white/90 text-xs shadow-2xs">
                        <div>
                            <span class="text-slate-400 block font-semibold">Role</span>
                            <span class="font-bold text-slate-800" x-text="$store.ui.activeProject.role"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold">Timeline</span>
                            <span class="font-bold text-slate-800" x-text="$store.ui.activeProject.timeline"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold">Client / Scope</span>
                            <span class="font-bold text-slate-800" x-text="$store.ui.activeProject.client || 'Open Project'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold">Category</span>
                            <span class="font-bold text-indigo-600" x-text="$store.ui.activeProject.category"></span>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                            <span x-show="$store.lang.current === 'id'">Hasil &amp; Dampak Terukur</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Key Engineering Metrics</span>
                        </h4>
                        <div class="grid grid-cols-3 gap-3">
                            <template x-for="(m, idx) in $store.ui.activeProject.metrics" :key="idx">
                                <div class="p-3.5 bg-indigo-50/80 backdrop-blur-md rounded-xl border border-indigo-100/80 text-center shadow-2xs">
                                    <div class="text-xl sm:text-2xl font-extrabold text-indigo-700" x-text="m.value"></div>
                                    <div class="text-[11px] font-semibold text-slate-600 mt-0.5" x-text="m.label"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-sm font-bold text-slate-900">
                            <span x-show="$store.lang.current === 'id'">Tantangan &amp; Solusi Rekayasa</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Engineering Challenge &amp; Solution</span>
                        </h4>
                        <p class="text-sm text-slate-600 leading-relaxed" x-text="$store.ui.activeProject.longDescription"></p>
                    </div>

                    <div class="space-y-2.5">
                        <h4 class="text-sm font-bold text-slate-900">
                            <span x-show="$store.lang.current === 'id'">Sorotan Fitur &amp; Inovasi</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Key Technical Highlights</span>
                        </h4>
                        <div class="space-y-2">
                            <template x-for="(h, idx) in $store.ui.activeProject.highlights" :key="idx">
                                <div class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-700">
                                    <x-icon name="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                                    <span x-text="h"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Architecture Tab --}}
                <div x-show="$store.ui.activeProjectTab === 'architecture'" class="space-y-6">
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Frontend &amp; Client Stack</h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(t, idx) in $store.ui.activeProject.techStack.frontend" :key="idx">
                                    <span class="px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-xs font-bold" x-text="t"></span>
                                </template>
                            </div>
                        </div>

                        <div x-show="$store.ui.activeProject.techStack.backend && $store.ui.activeProject.techStack.backend.length">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Backend &amp; API Services</h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(t, idx) in ($store.ui.activeProject.techStack.backend || [])" :key="idx">
                                    <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold" x-text="t"></span>
                                </template>
                            </div>
                        </div>

                        <div x-show="$store.ui.activeProject.techStack.database && $store.ui.activeProject.techStack.database.length">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Database &amp; Caching Layer</h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(t, idx) in ($store.ui.activeProject.techStack.database || [])" :key="idx">
                                    <span class="px-3 py-1.5 bg-purple-50 text-purple-700 border border-purple-200 rounded-lg text-xs font-bold" x-text="t"></span>
                                </template>
                            </div>
                        </div>

                        <div x-show="$store.ui.activeProject.techStack.cloudAndDevOps && $store.ui.activeProject.techStack.cloudAndDevOps.length">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Cloud, CI/CD &amp; Deployment</h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(t, idx) in ($store.ui.activeProject.techStack.cloudAndDevOps || [])" :key="idx">
                                    <span class="px-3 py-1.5 bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-bold" x-text="t"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Preview Tab --}}
                <div x-show="$store.ui.activeProjectTab === 'preview'" class="space-y-4">
                    <div class="p-5 bg-slate-900 text-slate-200 rounded-2xl border border-slate-800 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <span class="text-xs font-mono text-slate-400 ml-2" x-text="'preview.applet.local:3000/' + $store.ui.activeProject.id"></span>
                            </div>
                            <span class="text-[11px] font-mono bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded-sm">200 OK</span>
                        </div>

                        <div class="p-6 bg-slate-950 rounded-xl border border-slate-800/80 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center mx-auto shadow-lg shadow-indigo-500/30">
                                <x-icon name="zap" class="w-6 h-6" />
                            </div>
                            <h5 class="font-bold text-white text-base">
                                <span x-text="$store.ui.activeProject.title"></span> — Live Sandbox Active
                            </h5>
                            <p class="text-xs text-slate-400 max-w-md mx-auto">
                                <span x-show="$store.lang.current === 'id'">Simulasi antarmuka berkinerja tinggi dengan visualisasi real-time dan transisi sub-frame.</span>
                                <span x-show="$store.lang.current === 'en'" x-cloak>Simulated production build with live data streaming and instant responsiveness.</span>
                            </p>
                            <a x-show="$store.ui.activeProject.demoUrl" :href="$store.ui.activeProject.demoUrl" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition-colors">
                                <span x-show="$store.lang.current === 'id'">Buka Live Web App</span>
                                <span x-show="$store.lang.current === 'en'" x-cloak>Launch Full Web App</span>
                                <x-icon name="external-link" class="w-3.5 h-3.5" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Action Strip --}}
            <div class="p-5 sm:p-6 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <template x-for="(tag, idx) in ($store.ui.activeProject.tags || []).slice(0, 3)" :key="idx">
                        <span class="text-xs font-semibold text-slate-600 bg-white px-2.5 py-1 rounded-md border border-slate-200" x-text="tag"></span>
                    </template>
                </div>

                <div class="flex items-center gap-2.5">
                    <a x-show="$store.ui.activeProject.githubUrl" :href="$store.ui.activeProject.githubUrl" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-slate-100 text-slate-800 text-xs font-bold rounded-xl border border-slate-200 transition-colors">
                        <x-icon name="github" class="w-4 h-4" />
                        <span>GitHub</span>
                    </a>
                    <a x-show="$store.ui.activeProject.demoUrl" :href="$store.ui.activeProject.demoUrl" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                        <span x-show="$store.lang.current === 'id'">Kunjungi Live Demo</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Live Preview</span>
                        <x-icon name="external-link" class="w-4 h-4" />
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>
