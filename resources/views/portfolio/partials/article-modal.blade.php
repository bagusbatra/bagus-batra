<div
    id="article-modal-backdrop"
    x-data="articleModal()"
    x-show="$store.ui.activeArticle"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="$store.ui.closeArticle()"
    @keydown.escape.window="$store.ui.closeArticle()"
    class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
    style="display: none;"
>
    <template x-if="$store.ui.activeArticle">
        <article
            id="article-reader-container"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 scale-96 translate-y-5"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            @click.stop
            class="relative w-full max-w-4xl bg-white/95 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/80 overflow-hidden my-6 max-h-[92vh] flex flex-col"
        >
            {{-- Header Bar --}}
            <div class="px-6 py-4 border-b border-slate-200/80 bg-white/75 backdrop-blur-md flex items-center justify-between sticky top-0 z-20">
                <button @click="$store.ui.closeArticle()" class="flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-indigo-600 cursor-pointer">
                    <x-icon name="arrow-left" class="w-4 h-4" />
                    <span x-show="$store.lang.current === 'id'">Kembali ke Blog</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Back to Blog</span>
                </button>

                <div class="flex items-center gap-2">
                    <button
                        @click="toggleLike($store.ui.activeArticle)"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer"
                        :class="hasLiked($store.ui.activeArticle) ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-slate-100 text-slate-700 hover:bg-rose-50 hover:text-rose-600'"
                    >
                        <x-icon name="heart" class="w-3.5 h-3.5" />
                        <span x-text="likes($store.ui.activeArticle)"></span>
                    </button>

                    <button
                        @click="$store.ui.toggleBookmark($store.ui.activeArticle.id)"
                        class="p-2 rounded-full text-xs font-bold transition-all cursor-pointer"
                        :class="$store.ui.isBookmarked($store.ui.activeArticle.id) ? 'bg-indigo-50 text-indigo-600 border border-indigo-200' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50 hover:text-indigo-600'"
                    >
                        <x-icon name="bookmark" class="w-3.5 h-3.5" />
                    </button>

                    <button @click="share('copy', $store.ui.activeArticle)" class="flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors cursor-pointer">
                        <x-icon name="check" class="w-3.5 h-3.5 text-emerald-600" x-show="copiedLink" />
                        <x-icon name="copy" class="w-3.5 h-3.5" x-show="!copiedLink" />
                        <template x-if="copiedLink">
                            <span>
                                <span x-show="$store.lang.current === 'id'">Link Tersalin!</span>
                                <span x-show="$store.lang.current === 'en'" x-cloak>Copied!</span>
                            </span>
                        </template>
                        <template x-if="!copiedLink">
                            <span>
                                <span x-show="$store.lang.current === 'id'">Salin Link</span>
                                <span x-show="$store.lang.current === 'en'" x-cloak>Copy Link</span>
                            </span>
                        </template>
                    </button>

                    <button @click="$store.ui.closeArticle()" class="p-1.5 rounded-full text-slate-400 hover:text-slate-800 hover:bg-slate-100 cursor-pointer ml-1" aria-label="Tutup">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
            </div>

            {{-- Reader Scrollable Content --}}
            <div class="overflow-y-auto flex-1 p-6 sm:p-10 space-y-8">
                {{-- Post Metadata & Heading --}}
                <div class="space-y-4 max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold uppercase tracking-wider" x-text="$store.ui.activeArticle.category"></span>
                        <span class="flex items-center gap-1 text-xs text-slate-500 font-medium">
                            <x-icon name="clock" class="w-3.5 h-3.5" />
                            <span x-text="$store.ui.activeArticle.readTime"></span>
                        </span>
                        <span class="flex items-center gap-1 text-xs text-slate-500 font-medium">
                            <x-icon name="calendar" class="w-3.5 h-3.5" />
                            <span x-text="$store.ui.activeArticle.publishedAt"></span>
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight" x-text="$store.ui.activeArticle.title"></h1>

                    <div class="flex items-center gap-3 pt-2">
                        <img :src="$store.ui.activeArticle.author.avatar" :alt="$store.ui.activeArticle.author.name" width="40" height="40" loading="lazy" decoding="async" class="w-10 h-10 rounded-full object-cover border border-slate-200" />
                        <div>
                            <div class="text-sm font-bold text-slate-900" x-text="$store.ui.activeArticle.author.name"></div>
                            <div class="text-xs text-slate-500" x-text="$store.ui.activeArticle.author.role"></div>
                        </div>
                    </div>
                </div>

                {{-- Article Cover Image --}}
                <div class="rounded-2xl overflow-hidden max-h-96 border border-slate-200 shadow-xs">
                    <img :src="$store.ui.activeArticle.coverImage" :alt="$store.ui.activeArticle.title" width="800" height="450" loading="lazy" decoding="async" class="w-full h-full object-cover" />
                </div>

                {{-- Post Summary Lead --}}
                <div class="p-5 bg-slate-50 rounded-2xl border-l-4 border-indigo-600 text-slate-700 text-sm sm:text-base font-medium leading-relaxed" x-text="$store.ui.activeArticle.summary"></div>

                {{-- Article Content Sections --}}
                <div class="space-y-8 text-slate-700 leading-relaxed text-sm sm:text-base">
                    <template x-for="(sec, idx) in $store.ui.activeArticle.sections" :key="idx">
                        <div class="space-y-3.5">
                            <h3 x-show="sec.heading" class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight pt-2" x-text="sec.heading"></h3>
                            <p class="text-slate-600 leading-relaxed" x-text="sec.body"></p>

                            {{-- Code Snippet Block --}}
                            <div x-show="sec.codeSnippet" class="rounded-2xl overflow-hidden bg-slate-900 border border-slate-800 shadow-md">
                                <div class="px-4 py-2.5 bg-slate-950 flex items-center justify-between border-b border-slate-800 text-xs font-mono text-slate-400">
                                    <span x-text="sec.codeSnippet ? (sec.codeSnippet.filename || ('code.' + sec.codeSnippet.language)) : ''"></span>
                                    <button @click="copyCode(sec.codeSnippet.code, 'snippet-' + idx)" class="flex items-center gap-1 text-slate-400 hover:text-white transition-colors cursor-pointer">
                                        <x-icon name="check" class="w-3.5 h-3.5 text-emerald-400" x-show="copiedCodeId === 'snippet-' + idx" />
                                        <x-icon name="copy" class="w-3.5 h-3.5" x-show="copiedCodeId !== 'snippet-' + idx" />
                                        <span class="text-[11px] font-bold" x-text="copiedCodeId === 'snippet-' + idx ? 'Copied' : 'Copy'"></span>
                                    </button>
                                </div>
                                <pre class="p-4 text-xs font-mono text-slate-200 overflow-x-auto leading-relaxed"><code x-text="sec.codeSnippet ? sec.codeSnippet.code : ''"></code></pre>
                            </div>

                            {{-- Pro Tip Box --}}
                            <div x-show="sec.tip" class="p-4 rounded-xl bg-amber-50/80 border border-amber-200/80 text-amber-900 text-xs sm:text-sm font-medium flex items-start gap-2.5">
                                <x-icon name="sparkles" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                                <span x-text="sec.tip"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Social Share Bar --}}
                <div class="pt-8 border-t border-slate-200 space-y-3">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <span x-show="$store.lang.current === 'id'">Bagikan Artikel Ini:</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Share This Article:</span>
                    </h4>
                    <div class="flex flex-wrap items-center gap-2">
                        <button @click="share('twitter', $store.ui.activeArticle)" class="flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-900 hover:text-white rounded-xl text-xs font-semibold text-slate-700 transition-all cursor-pointer">
                            <x-icon name="twitter" class="w-4 h-4" />
                            <span>X (Twitter)</span>
                        </button>
                        <button @click="share('linkedin', $store.ui.activeArticle)" class="flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-[#0A66C2] hover:text-white rounded-xl text-xs font-semibold text-slate-700 transition-all cursor-pointer">
                            <x-icon name="linkedin" class="w-4 h-4" />
                            <span>LinkedIn</span>
                        </button>
                        <button @click="share('whatsapp', $store.ui.activeArticle)" class="flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-emerald-600 hover:text-white rounded-xl text-xs font-semibold text-slate-700 transition-all cursor-pointer">
                            <x-icon name="message-square" class="w-4 h-4" />
                            <span>WhatsApp</span>
                        </button>
                    </div>
                </div>

                {{-- Comments Section --}}
                <div class="pt-8 border-t border-slate-200 space-y-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <x-icon name="message-square" class="w-5 h-5 text-indigo-600" />
                            <span x-show="$store.lang.current === 'id'">Diskusi &amp; Tanggapan</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Discussion &amp; Comments</span>
                            <span class="text-xs px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full" x-text="comments($store.ui.activeArticle).length"></span>
                        </h4>
                    </div>

                    {{-- Add Comment Form --}}
                    <form @submit.prevent="addComment($store.ui.activeArticle)" class="p-4.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <input
                                type="text"
                                required
                                x-model="newCommentName"
                                :placeholder="$store.lang.current === 'id' ? 'Nama Anda...' : 'Your Name...'"
                                class="px-3.5 py-2 bg-white rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-indigo-500"
                            />
                        </div>
                        <textarea
                            required
                            rows="3"
                            x-model="newCommentText"
                            :placeholder="$store.lang.current === 'id' ? 'Tuliskan pandangan atau pertanyaan Anda seputar topik ini...' : 'Write your thoughts or questions...'"
                            class="w-full px-3.5 py-2 bg-white rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-indigo-500 resize-none"
                        ></textarea>
                        <button type="submit" class="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-xs">
                            <x-icon name="send" class="w-3.5 h-3.5" />
                            <span x-show="$store.lang.current === 'id'">Kirim Komentar</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Post Comment</span>
                        </button>
                    </form>

                    {{-- Comments List --}}
                    <div class="space-y-3">
                        <template x-for="c in comments($store.ui.activeArticle)" :key="c.id">
                            <div class="p-4 bg-white rounded-2xl border border-slate-200/80 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <img :src="c.avatar" :alt="c.author" width="28" height="28" loading="lazy" decoding="async" class="w-7 h-7 rounded-full object-cover" />
                                        <span class="text-xs font-bold text-slate-900" x-text="c.author"></span>
                                    </div>
                                    <span class="text-[11px] text-slate-400" x-text="c.date"></span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed pl-9" x-text="c.text"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </article>
    </template>
</div>
