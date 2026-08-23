@php
    $blogCategories = [
        ['id' => 'All', 'id_label' => 'Semua Artikel', 'en_label' => 'All Articles'],
        ['id' => 'Frontend Architecture', 'id_label' => 'Frontend Architecture', 'en_label' => 'Frontend Architecture'],
        ['id' => 'Performance', 'id_label' => 'Web Performance', 'en_label' => 'Web Performance'],
        ['id' => 'Clean Code', 'id_label' => 'UI & Clean Code', 'en_label' => 'UI & Clean Code'],
        ['id' => 'Full-Stack', 'id_label' => 'Full-Stack & TS', 'en_label' => 'Full-Stack & TS'],
    ];
@endphp

<section id="blog" x-data="blogSection(@js($blogPosts->map->toJs()->values()))" class="py-20 sm:py-24 bg-transparent border-b border-white/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-12">
        {{-- Header Strip --}}
        <div data-reveal class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-2xl space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-50/80 backdrop-blur-md text-indigo-700 border border-indigo-100 text-xs font-bold uppercase tracking-wider">
                    <x-icon name="book-open" class="w-3.5 h-3.5" />
                    <span x-show="$store.lang.current === 'id'">Blog &amp; Tulisan Teknis</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Engineering Blog &amp; Insights</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    <span x-show="$store.lang.current === 'id'">Wawasan Rekayasa Web, Performa &amp; Best Practices</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Architectural Insights, Performance &amp; Web Deep Dives</span>
                </h2>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    <span x-show="$store.lang.current === 'id'">Catatan teknis, eksplorasi framework modern, dan studi kasus nyata yang saya tulis secara berkala untuk komunitas pengembang.</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Deep-dive tutorials, performance breakdown case studies, and modern frontend development patterns.</span>
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="w-full md:w-80">
                <div class="relative">
                    <x-icon name="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input
                        id="blog-search-input"
                        type="text"
                        x-model="query"
                        :placeholder="$store.lang.current === 'id' ? 'Cari artikel, tag, topik...' : 'Search articles, tags, topics...'"
                        class="w-full pl-10 pr-4 py-2.5 bg-white/60 backdrop-blur-md rounded-2xl border border-white/80 text-xs text-slate-800 placeholder-slate-400 focus:bg-white/90 focus:outline-indigo-500 shadow-2xs transition-all"
                    />
                    <button x-show="query" @click="query = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-bold">Clear</button>
                </div>
            </div>
        </div>

        {{-- Category Filter Pills & Active Tag Filter --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
            <div class="flex items-center gap-1 p-1.5 bg-white/50 backdrop-blur-md rounded-2xl border border-white/80 shadow-2xs overflow-x-auto max-w-full pb-1.5 sm:pb-1.5 scrollbar-none">
                @foreach ($blogCategories as $cat)
                    <button
                        id="blog-category-{{ \Illuminate\Support\Str::slug($cat['id']) }}"
                        @click="category = '{{ $cat['id'] }}'"
                        class="relative px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors cursor-pointer shrink-0"
                        :class="category === '{{ $cat['id'] }}' ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/40'"
                    >
                        <span x-show="category === '{{ $cat['id'] }}'" class="absolute inset-0 bg-white rounded-xl shadow-2xs border border-white/90 -z-10"></span>
                        <span x-show="$store.lang.current === 'id'">{{ $cat['id_label'] }}</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>{{ $cat['en_label'] }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Tags Chips --}}
            <div class="flex flex-wrap items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                <span class="text-xs font-semibold text-slate-400 mr-1 flex items-center gap-1 shrink-0">
                    <x-icon name="tag" class="w-3 h-3" />
                    Tags:
                </span>
                <template x-for="t in allTags" :key="t">
                    <button
                        @click="toggleTag(t)"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-colors cursor-pointer shrink-0"
                        :class="tag === t ? 'bg-indigo-600 text-white' : 'bg-white/60 backdrop-blur-xs text-slate-600 hover:bg-white/90 border border-white/60 shadow-2xs'"
                        x-text="'#' + t"
                    ></button>
                </template>
                <button x-show="tag" @click="tag = null" class="text-[11px] font-bold text-rose-500 hover:underline ml-1 shrink-0 cursor-pointer">Reset Tag</button>
            </div>
        </div>

        {{-- Articles Responsive Grid --}}
        <div x-show="!hasResults" class="p-12 text-center bg-white/50 backdrop-blur-md rounded-3xl border border-dashed border-white/90 text-slate-500 shadow-2xs">
            <x-icon name="book-open" class="w-8 h-8 text-slate-400 mx-auto mb-2" />
            <p class="font-bold text-sm">
                <span x-show="$store.lang.current === 'id'">Tidak ada artikel yang cocok dengan pencarian.</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>No articles match your query.</span>
            </p>
            <button @click="resetFilters()" class="mt-3 text-xs font-bold text-indigo-600 hover:underline cursor-pointer">Reset semua filter</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
            @foreach ($blogPosts as $post)
                <article
                    data-reveal
                    x-data="{ post: @js($post->toJs()) }"
                    x-show="matches(post)"
                    x-transition
                    id="blog-card-{{ $post->post_key }}"
                    class="group bg-white/60 backdrop-blur-lg rounded-3xl border border-white/80 shadow-2xs hover:shadow-xl hover:border-indigo-300/80 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between overflow-hidden"
                >
                    <div class="space-y-4">
                        {{-- Cover Image & Category Pill --}}
                        <div class="relative h-48 sm:h-56 overflow-hidden bg-slate-100 cursor-pointer" @click="$store.ui.openArticle(post)">
                            <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" width="800" height="450" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>

                            <div class="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between">
                                <span class="px-2.5 py-1 bg-white/90 backdrop-blur-md text-indigo-700 text-[11px] font-bold rounded-lg shadow-2xs border border-white/60">{{ $post->category }}</span>
                                <button
                                    @click.stop="$store.ui.toggleBookmark('{{ $post->post_key }}')"
                                    class="p-2 rounded-full backdrop-blur-md transition-all cursor-pointer"
                                    :class="$store.ui.isBookmarked('{{ $post->post_key }}') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white/80 text-slate-700 hover:bg-white border border-white/80'"
                                    :title="$store.ui.isBookmarked('{{ $post->post_key }}') ? 'Hapus dari bookmark' : 'Simpan bookmark'"
                                >
                                    <x-icon name="bookmark" class="w-3.5 h-3.5" />
                                </button>
                            </div>

                            <div class="absolute bottom-3 left-3.5 right-3.5 flex items-center gap-3 text-[11px] text-white/90 font-medium">
                                <span class="flex items-center gap-1">
                                    <x-icon name="clock" class="w-3 h-3 text-indigo-300" />
                                    {{ $post->read_time }}
                                </span>
                                <span>&bull;</span>
                                <span class="flex items-center gap-1">
                                    <x-icon name="calendar" class="w-3 h-3 text-indigo-300" />
                                    {{ $post->published_at }}
                                </span>
                            </div>
                        </div>

                        {{-- Article Content --}}
                        <div class="p-5 sm:p-6 pt-2 space-y-3">
                            <h3 @click="$store.ui.openArticle(post)" class="text-lg sm:text-xl font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors leading-snug cursor-pointer">{{ $post->title }}</h3>
                            <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 leading-relaxed">{{ $post->summary }}</p>

                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @foreach ($post->tags as $t)
                                    <span @click="tag = '{{ $t }}'" class="px-2.5 py-1 bg-white/70 backdrop-blur-xs text-slate-600 rounded-md text-[11px] font-semibold border border-white/60 shadow-2xs hover:bg-white/90 cursor-pointer">#{{ $t }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Card Footer Strip --}}
                    <div class="px-5 sm:px-6 pb-5 sm:pb-6 pt-3 border-t border-slate-200/50 flex items-center justify-between">
                        <button @click="$store.ui.openArticle(post)" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 cursor-pointer py-1">
                            <span x-show="$store.lang.current === 'id'">Baca Selengkapnya</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Read Article</span>
                            <x-icon name="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                        </button>

                        <div class="flex items-center gap-3 text-xs text-slate-400 font-medium">
                            <span class="flex items-center gap-1">
                                <x-icon name="heart" class="w-3.5 h-3.5 text-rose-500" />
                                {{ $post->likes }}
                            </span>
                            <span class="flex items-center gap-1">
                                <x-icon name="eye" class="w-3.5 h-3.5 text-slate-400" />
                                {{ $post->views }}
                            </span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
