<template>
    <Head :title="isJapanese ? '漢字フラッシュカード & クイズ - 正夢' : 'Kanji Flashcards & Quiz Trainer - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-24 md:pb-16">
            <!-- ================= HERO HEADER BANNER ================= -->
            <div v-if="!isPracticeMode" class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white p-5 sm:p-7 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-6">
                <!-- Japanese Kanji Watermark -->
                <div class="absolute -right-4 -bottom-6 font-jp text-8xl sm:text-9xl font-black text-white select-none pointer-events-none opacity-5">
                    漢字
                </div>

                <div class="relative z-10 space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold border border-indigo-500/30 font-jp">
                        <Languages class="w-3.5 h-3.5 text-indigo-400" />
                        <span>{{ isJapanese ? '漢字記憶ジム・インタラクティブ' : 'Gym Hafalan Huruf Kanji (Kanji Memory Gym)' }}</span>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold tracking-tight">
                            Kanji Flashcards & Quiz
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl leading-relaxed">
                            {{ isJapanese 
                                ? 'トピック別モジュール、3Dカード反転、スピード4択クイズ、漢字グリッドで効率的に漢字を習得します。' 
                                : 'Modul hafalan kanji bertingkat: Kuasai per topik tematik, latih kecepatan lewat kuis kilat, dan telaah makna onyomi/kunyomi.' 
                            }}
                        </p>
                    </div>
                </div>

                <!-- Level Selector Pill Tabs -->
                <div class="relative z-10 flex flex-wrap items-center gap-1.5 bg-white/10 backdrop-blur-md p-1.5 rounded-2xl border border-white/20">
                    <button 
                        v-for="(lbl, key) in levels" 
                        :key="key"
                        type="button"
                        @click="changeLevel(key)"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer font-mono"
                        :class="selectedLevel === key 
                            ? 'bg-indigo-500 text-white shadow-md font-black' 
                            : 'text-slate-300 hover:text-white hover:bg-white/10'"
                    >
                        {{ key === 'ALL' ? 'Semua' : key }}
                    </button>
                </div>
            </div>

            <!-- ================= STATE 1: KANJI TOPIC MODULE HUB (LOBBY TOPIK) ================= -->
            <div v-if="!isPracticeMode" class="space-y-6">
                <!-- Section Header & Quick Search -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/90 shadow-xs">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></span>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">
                                Pilih Modul Topik Huruf Kanji
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500">
                            Tersedia <span class="font-bold text-slate-800">{{ topics.length }} modul topik</span> pada level <span class="font-bold text-indigo-600">{{ selectedLevel }}</span> (Total {{ activeKanjiList.length }} Kanji).
                        </p>
                    </div>

                    <!-- Search Topic Input -->
                    <div class="relative w-full sm:w-72">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                        <input 
                            type="text" 
                            v-model="searchTopicQuery" 
                            placeholder="Cari judul modul kanji..." 
                            class="w-full pl-9 pr-3.5 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                        />
                    </div>
                </div>

                <!-- Grid Modul Topik Kanji -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- SPECIAL CARD: Full Deck (Semua Kanji Level Ini) -->
                    <div 
                        class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white rounded-3xl p-5 border border-slate-800 shadow-md hover:shadow-xl transition-all flex flex-col justify-between group relative overflow-hidden"
                    >
                        <div class="absolute -right-4 -bottom-4 font-jp text-7xl font-black text-white/5 pointer-events-none select-none">
                            漢
                        </div>
                        <div class="space-y-3 relative z-10">
                            <div class="flex items-center justify-between">
                                <span class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 flex items-center justify-center text-lg font-bold">
                                    漢
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 text-[11px] font-mono font-bold">
                                    {{ activeKanjiList.length }} Huruf Kanji
                                </span>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-white group-hover:text-indigo-300 transition-colors">
                                    Semua Kanji (Full Deck)
                                </h3>
                                <p class="text-xs text-slate-400 mt-1 line-clamp-2">
                                    Latihan seluruh kumpulan huruf kanji pada level {{ selectedLevel }} tanpa batasan kelompok topik.
                                </p>
                            </div>
                        </div>

                        <div class="pt-5 flex items-center gap-2 relative z-10">
                            <button 
                                type="button"
                                @click="startTraining(null, 'flashcard')"
                                class="flex-1 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-md shadow-indigo-600/30"
                            >
                                <Play class="w-3.5 h-3.5 fill-current" />
                                <span>Mulai Flashcard</span>
                            </button>
                            <button 
                                type="button"
                                @click="startTraining(null, 'quiz')"
                                class="p-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center border border-white/10"
                                title="Langsung Mulai Kuis Kilat Kanji"
                            >
                                <Zap class="w-4 h-4 text-indigo-300" />
                            </button>
                        </div>
                    </div>

                    <!-- TOPIC CARDS -->
                    <div 
                        v-for="top in filteredTopics" 
                        :key="top.id"
                        class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:border-indigo-400/60 transition-all flex flex-col justify-between group"
                    >
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 border border-indigo-200/60 flex items-center justify-center text-lg font-bold font-jp">
                                    {{ top.icon || '漢' }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-mono font-bold">
                                        {{ top.level || selectedLevel }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-100/70 text-indigo-900 text-[11px] font-mono font-bold">
                                        {{ top.kanjis_count ?? 0 }} Kanji
                                    </span>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 font-jp group-hover:text-indigo-600 transition-colors">
                                    {{ top.title }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                    {{ top.description || 'Kumpulan huruf kanji esensial dan cara baca onyomi/kunyomi sesuai topik.' }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-5 flex items-center gap-2">
                            <button 
                                type="button"
                                @click="startTraining(top.id, 'flashcard')"
                                class="flex-1 py-2.5 rounded-2xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-xs"
                            >
                                <Play class="w-3.5 h-3.5 fill-current" />
                                <span>Pelajari Modul</span>
                            </button>
                            <button 
                                type="button"
                                @click="startTraining(top.id, 'quiz')"
                                class="p-2.5 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition-all cursor-pointer flex items-center justify-center border border-indigo-200"
                                title="Mulai Kuis Topik Ini"
                            >
                                <Zap class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State for Topic Search -->
                <div v-if="filteredTopics.length === 0" class="bg-white rounded-3xl p-12 text-center border border-dashed border-slate-200">
                    <Folder class="w-12 h-12 text-slate-300 mx-auto mb-3" />
                    <h3 class="text-sm font-bold text-slate-700">Tidak ada modul kanji yang sesuai dengan pencarian</h3>
                    <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci lain atau pilih level di atas.</p>
                </div>
            </div>

            <!-- ================= STATE 2: FOCUSED KANJI ARENA (ARENA BELAJAR) ================= -->
            <div v-else class="space-y-6">
                <!-- Focused Arena Sticky Navigation Bar -->
                <div class="bg-white p-3.5 sm:p-4 rounded-3xl border border-slate-200/90 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <!-- Left: Back Button & Topic Indicator -->
                    <div class="flex items-center gap-3">
                        <button 
                            type="button"
                            @click="exitPracticeMode"
                            class="px-3 py-2 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all cursor-pointer flex items-center gap-1.5 shrink-0"
                        >
                            <ArrowLeft class="w-4 h-4" />
                            <span class="hidden sm:inline">Ganti Modul</span>
                        </button>
                        <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>
                        <div class="truncate">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black text-slate-900 font-jp truncate">
                                    {{ currentActiveTopic ? currentActiveTopic.title : 'Semua Kanji (Full Deck)' }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-mono font-bold shrink-0">
                                    {{ activeKanjiList.length }} Kanji
                                </span>
                            </div>
                            <span class="text-[11px] text-slate-400 block truncate">
                                Level {{ selectedLevel }} • Sesi Hafalan Kanji Aktif
                            </span>
                        </div>
                    </div>

                    <!-- Right: Mode Switcher (Clean, Non-Redundant & Distinct) -->
                    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-2xl w-full md:w-auto justify-between md:justify-start">
                        <button 
                            type="button"
                            @click="activeMode = 'flashcard'"
                            class="flex-1 md:flex-initial px-3 sm:px-4 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                            :class="activeMode === 'flashcard' 
                                ? 'bg-white text-slate-950 shadow-xs font-black' 
                                : 'text-slate-600 hover:text-slate-900'"
                        >
                            <span>🃏</span>
                            <span>Flashcard</span>
                        </button>
                        <button 
                            type="button"
                            @click="activeMode = 'canvas'"
                            class="flex-1 md:flex-initial px-3 sm:px-4 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                            :class="activeMode === 'canvas' 
                                ? 'bg-white text-slate-950 shadow-xs font-black' 
                                : 'text-slate-600 hover:text-slate-900'"
                        >
                            <span>✍️</span>
                            <span>Tulis Kanji</span>
                        </button>
                        <button 
                            type="button"
                            @click="startQuizMode"
                            class="flex-1 md:flex-initial px-3 sm:px-4 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                            :class="activeMode === 'quiz' 
                                ? 'bg-indigo-600 text-white shadow-xs font-black' 
                                : 'text-slate-600 hover:text-slate-900'"
                        >
                            <span>⚡</span>
                            <span>Kuis Kilat</span>
                        </button>
                    </div>
                </div>

                <!-- Empty State if No Kanji in this Topic -->
                <div v-if="activeKanjiList.length === 0" class="bg-white rounded-3xl p-12 text-center border border-dashed border-slate-200">
                    <p class="text-sm font-bold text-slate-700">Belum ada huruf kanji di dalam modul ini.</p>
                    <button 
                        type="button"
                        @click="exitPracticeMode" 
                        class="mt-4 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold"
                    >
                        Kembali ke Pilihan Topik
                    </button>
                </div>

                <!-- SUB-VIEW 1: KANJI FLASHCARD 3D FLIP MODE -->
                <div v-else-if="activeMode === 'flashcard'" class="max-w-2xl mx-auto space-y-5">
                    <!-- Progress Bar & Indicator -->
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 px-1">
                        <span>Kanji {{ currentIndex + 1 }} dari {{ activeKanjiList.length }}</span>
                        <div class="flex items-center gap-3">
                            <button 
                                type="button" 
                                @click="shuffleCards"
                                class="text-slate-500 hover:text-indigo-600 flex items-center gap-1 cursor-pointer transition-colors"
                                title="Acak urutan kartu"
                            >
                                <Shuffle class="w-3.5 h-3.5" />
                                <span class="text-[11px]">Acak</span>
                            </button>
                            <div class="w-28 sm:w-40 h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                                <div 
                                    class="h-full bg-indigo-600 transition-all duration-300" 
                                    :style="{ width: `${((currentIndex + 1) / activeKanjiList.length) * 100}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- 3D Flip Card Container -->
                    <div class="perspective-1000">
                        <div 
                            @click="isFlipped = !isFlipped" 
                            class="relative w-full min-h-[320px] sm:min-h-[360px] rounded-3xl cursor-pointer transition-transform duration-500 transform-style-3d shadow-xl hover:shadow-2xl border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-slate-100 flex items-center justify-center p-8 text-center select-none"
                            :class="{ 'rotate-y-180 bg-gradient-to-br from-indigo-50/80 to-purple-50/80': isFlipped }"
                        >
                            <!-- FRONT SIDE: Huge Kanji Character ONLY -->
                            <div v-show="!isFlipped" class="flex flex-col items-center justify-center space-y-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-900 text-white text-[10px] font-black uppercase font-mono">
                                        {{ currentKanji?.level || selectedLevel }}
                                    </span>
                                </div>

                                <h2 class="text-8xl sm:text-9xl font-black text-slate-950 font-jp tracking-tight py-4">
                                    {{ currentKanji?.kanji }}
                                </h2>
                                
                                <div class="flex items-center gap-1.5 text-xs text-slate-400 font-bold pt-2">
                                    <span>👆 Klik kartu untuk melihat cara baca & arti</span>
                                </div>
                            </div>

                            <!-- BACK SIDE: Hiragana, Romaji, Meaning & Onyomi/Kunyomi -->
                            <div v-show="isFlipped" class="flex flex-col items-center justify-center space-y-3.5 transform rotate-y-180 w-full max-w-md">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-indigo-100 text-indigo-800 text-[11px] font-black font-jp">
                                        漢字: {{ currentKanji?.kanji }}
                                    </span>
                                </div>

                                <!-- Cara Baca Hiragana & Romaji -->
                                <div class="space-y-0.5 py-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">
                                        Cara Baca (読み方)
                                    </span>
                                    <div class="text-2xl sm:text-3xl font-black text-indigo-600 font-jp tracking-wide">
                                        {{ currentKanji?.hiragana || '-' }}
                                    </div>
                                    <div v-if="currentKanji?.romaji" class="text-xs sm:text-sm font-bold text-slate-500 font-mono">
                                        {{ currentKanji?.romaji }}
                                    </div>
                                </div>

                                <!-- Arti Bahasa Indonesia -->
                                <div class="bg-white/90 p-3 rounded-2xl border border-indigo-100 w-full shadow-2xs">
                                    <span class="text-[10px] font-black text-indigo-700 uppercase tracking-wider block mb-0.5">
                                        Arti Bahasa Indonesia
                                    </span>
                                    <h2 class="text-lg sm:text-xl font-black text-slate-900 uppercase">
                                        {{ currentKanji?.meaning_id }}
                                    </h2>
                                </div>

                                <!-- Onyomi / Kunyomi Readings Block -->
                                <div class="grid grid-cols-2 gap-2 w-full text-left bg-white/95 p-3 rounded-2xl border border-indigo-100 shadow-2xs">
                                    <div>
                                        <span class="text-[10px] font-black text-rose-600 uppercase tracking-wider block">音読み (Onyomi):</span>
                                        <p class="font-jp font-bold text-xs text-slate-900 mt-0.5">{{ currentKanji?.onyomi || '-' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-black text-emerald-600 uppercase tracking-wider block">訓読み (Kunyomi):</span>
                                        <p class="font-jp font-bold text-xs text-slate-900 mt-0.5">{{ currentKanji?.kunyomi || '-' }}</p>
                                    </div>
                                </div>

                                <!-- Notes or Examples if any -->
                                <div v-if="currentKanji?.notes" class="text-xs text-slate-600 font-jp bg-slate-50/90 p-2.5 rounded-xl border border-slate-200 text-left w-full">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Catatan:</span>
                                    <p class="leading-relaxed">{{ currentKanji.notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Controls: Clean & Non-Redundant -->
                    <div class="flex items-center justify-between gap-3 pt-2">
                        <button 
                            type="button"
                            @click="prevCard" 
                            class="px-4 py-2.5 sm:py-3 rounded-2xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 shadow-xs flex items-center gap-1.5 cursor-pointer shrink-0"
                        >
                            <ChevronLeft class="w-4 h-4" />
                            <span class="hidden sm:inline">Sebelumnya</span>
                        </button>

                        <button 
                            type="button" 
                            @click="shuffleCards"
                            class="px-4 py-2.5 sm:py-3 rounded-2xl bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 font-bold text-xs transition-colors flex items-center gap-1.5 cursor-pointer"
                            title="Acak urutan kartu"
                        >
                            <Shuffle class="w-3.5 h-3.5 text-slate-500" />
                            <span>Acak Urutan</span>
                        </button>

                        <button 
                            type="button"
                            @click="nextCard" 
                            class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-2xl bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
                        >
                            <span>Berikutnya</span>
                            <ChevronRight class="w-4 h-4" />
                        </button>
                    </div>

                    <!-- Keyboard Shortcut Hint -->
                    <p class="text-center text-[11px] text-slate-400 font-medium">
                        Tips Keyboard: <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-300 text-slate-600 font-mono text-[10px]">Spasi</kbd> untuk balik kartu • <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-300 text-slate-600 font-mono text-[10px]">◀</kbd> <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-300 text-slate-600 font-mono text-[10px]">▶</kbd> untuk navigasi
                    </p>
                </div>

                <!-- SUB-VIEW 2: GUIDED KANJI TRACING ARENA (LEGA & FOKUS PADA MENULIS) -->
                <div v-else-if="activeMode === 'canvas'" class="max-w-md mx-auto space-y-3">
                    <!-- Ultra-Compact 1-Line Info & Toolbar (Hemat Ruang Vertikal) -->
                    <div class="flex items-center justify-between text-xs px-1 gap-2">
                        <!-- Kanji Indicator, Reading & Meaning In One Line -->
                        <div class="flex items-center gap-1.5 min-w-0 text-slate-600">
                            <span class="font-bold text-slate-400 text-xs shrink-0">
                                {{ currentIndex + 1 }}/{{ activeKanjiList.length }}
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="font-jp font-black text-indigo-600 text-sm truncate">
                                {{ currentKanji?.hiragana || '-' }}
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="font-bold text-slate-800 text-xs uppercase truncate">
                                {{ currentKanji?.meaning_id || '-' }}
                            </span>
                        </div>

                        <!-- Right: Action Icons (Contoh & Outline) -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            <!-- Tombol Putar Contoh Animasi -->
                            <button 
                                type="button"
                                @click="playStrokeDemo"
                                :disabled="isStrokeAnimating"
                                class="px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer bg-white border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 shadow-2xs flex items-center gap-1 disabled:opacity-50"
                                title="Lihat animasi urutan penulisan resmi"
                            >
                                <Play class="w-3.5 h-3.5 fill-current text-indigo-600" />
                                <span class="text-[11px]">Contoh</span>
                            </button>

                            <!-- Toggle Outline Cetakan -->
                            <button 
                                type="button"
                                @click="toggleGuidedOutline"
                                class="p-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer border shadow-2xs"
                                :class="showGuidedOutline 
                                    ? 'bg-indigo-50 border-indigo-200 text-indigo-700' 
                                    : 'bg-white border-slate-200 text-slate-500 hover:text-slate-700'"
                                :title="showGuidedOutline ? 'Cetakan Aktif (Klik untuk sembunyikan)' : 'Tulis Mandiri (Klik untuk tampilkan)'"
                            >
                                <Eye v-if="showGuidedOutline" class="w-4 h-4 text-indigo-600" />
                                <EyeOff v-else class="w-4 h-4 text-slate-400" />
                            </button>
                        </div>
                    </div>

                    <!-- Sleek Full-Width Dark Genkouyoushi Box (Lega & Nyaman di HP) -->
                    <div class="relative w-full aspect-square max-w-[340px] sm:max-w-[380px] mx-auto rounded-3xl bg-slate-950 border-2 border-slate-800 shadow-2xl overflow-hidden select-none touch-none flex items-center justify-center">
                        <!-- Traditional Dotted Crosshair Grid (Subtle Dark Slate) -->
                        <div class="absolute inset-0 pointer-events-none">
                            <div class="absolute top-1/2 left-0 right-0 h-px border-b border-dashed border-slate-800/80 -translate-y-1/2"></div>
                            <div class="absolute left-1/2 top-0 bottom-0 w-px border-r border-dashed border-slate-800/80 -translate-x-1/2"></div>
                            <svg class="absolute inset-0 w-full h-full text-slate-800/40" preserveAspectRatio="none">
                                <line x1="0" y1="0" x2="100%" y2="100%" stroke="currentColor" stroke-dasharray="4,4" stroke-width="1" />
                                <line x1="100%" y1="0" x2="0" y2="100%" stroke="currentColor" stroke-dasharray="4,4" stroke-width="1" />
                            </svg>
                        </div>

                        <!-- Target Container for HanziWriter SVG -->
                        <div 
                            ref="guidedWriterTargetRef"
                            class="relative z-10 flex items-center justify-center cursor-crosshair select-none"
                        ></div>

                        <!-- Success Pill on Complete -->
                        <div v-if="isGuidedCompleted" class="absolute top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-emerald-500/90 text-white text-[11px] font-bold shadow-lg flex items-center gap-1.5 animate-fade-in z-20 pointer-events-none">
                            <span>✓</span>
                            <span>Bagus! Bentuk Tepat</span>
                        </div>

                        <!-- Loading State Spinner if downloading stroke data -->
                        <div v-if="isStrokeLoading" class="absolute inset-0 flex items-center justify-center bg-slate-950/80 z-20">
                            <div class="text-center space-y-2">
                                <div class="w-7 h-7 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                                <span class="text-xs text-slate-400 font-bold block">Memuat cetakan huruf...</span>
                            </div>
                        </div>

                        <!-- Fallback message if character stroke data is unavailable -->
                        <div v-if="strokeLoadError" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center z-20 bg-slate-950">
                            <span class="text-6xl font-black font-jp text-white mb-2">{{ currentKanji?.kanji }}</span>
                            <p class="text-xs text-slate-400">Cetakan vektor huruf ini tidak tersedia secara otomatis.</p>
                        </div>
                    </div>

                    <!-- Ergonomic Controls: 3 Buttons Thumb-Friendly -->
                    <div class="flex items-center justify-between gap-2.5 pt-1">
                        <!-- Tombol Sebelumnya -->
                        <button 
                            type="button"
                            @click="prevKanjiGuided" 
                            class="flex-1 py-2.5 sm:py-3 rounded-2xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 shadow-xs flex items-center justify-center gap-1 cursor-pointer"
                        >
                            <ChevronLeft class="w-4 h-4" />
                            <span class="hidden sm:inline">Sebelumnya</span>
                        </button>

                        <!-- Tombol Ulangi Menulis Huruf Ini -->
                        <button 
                            type="button" 
                            @click="restartGuidedWriting"
                            class="flex-1 py-2.5 sm:py-3 rounded-2xl bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                            title="Ulangi menulis kanji ini dari goresan pertama"
                        >
                            <RotateCcw class="w-3.5 h-3.5 text-slate-500" />
                            <span>Ulangi</span>
                        </button>

                        <!-- Tombol Berikutnya -->
                        <button 
                            type="button"
                            @click="nextKanjiGuided" 
                            class="flex-1 py-2.5 sm:py-3 rounded-2xl bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1 cursor-pointer"
                        >
                            <span>Berikutnya</span>
                            <ChevronRight class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- SUB-VIEW 3: SPEED QUIZ MODE -->
                <div v-else-if="activeMode === 'quiz'" class="max-w-2xl mx-auto">
                    <div v-if="!quizFinished && currentQuizQuestion" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xl space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-600 text-white text-xs font-black">
                                    SOAL {{ quizIndex + 1 }}/{{ quizQuestions.length }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-black text-xs border border-amber-200">
                                    🔥 Streak: {{ quizStreak }}
                                </span>
                            </div>
                            <button 
                                type="button"
                                @click="activeMode = 'flashcard'" 
                                class="text-xs font-bold text-slate-400 hover:text-slate-700 cursor-pointer"
                            >
                                ✕ Keluar Kuis
                            </button>
                        </div>

                        <!-- Question Prompt & Huge Kanji -->
                        <div class="text-center py-4 space-y-2 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">
                                Apa arti dari karakter kanji ini?
                            </span>
                            <h2 class="text-7xl sm:text-8xl font-black text-slate-900 font-jp tracking-wide py-2">
                                {{ currentQuizQuestion.kanji.kanji }}
                            </h2>
                            <p class="text-sm font-bold text-indigo-600 font-jp">
                                {{ currentQuizQuestion.kanji.hiragana }}
                            </p>
                        </div>

                        <!-- Multiple Choice Answer Buttons -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button 
                                type="button"
                                v-for="(option, idx) in currentQuizQuestion.options" 
                                :key="idx"
                                @click="selectQuizAnswer(option)"
                                :disabled="quizAnswerSelected !== null"
                                class="p-4 rounded-2xl border text-left text-xs font-bold transition-all cursor-pointer flex items-center justify-between gap-2 shadow-2xs"
                                :class="getQuizOptionClass(option)"
                            >
                                <span class="text-sm font-black">{{ option }}</span>
                                <span v-if="quizAnswerSelected !== null && option === currentQuizQuestion.kanji.meaning_id" class="text-emerald-600 font-black">
                                    ✓
                                </span>
                                <span v-else-if="quizAnswerSelected === option && option !== currentQuizQuestion.kanji.meaning_id" class="text-rose-600 font-black">
                                    ✗
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Quiz Finished Result Screen -->
                    <div v-else-if="quizFinished" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-lg text-center space-y-5 animate-fade-in">
                        <div class="w-16 h-16 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto text-2xl shadow-inner">
                            🏆
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900">Kuis Selesai!</h3>
                            <p class="text-xs text-slate-500 mt-1">Latihan kilat kanji berhasil diselesaikan.</p>
                        </div>

                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 max-w-xs mx-auto space-y-2">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Skor Akhir</span>
                            <div class="text-4xl font-black text-indigo-600 font-mono">
                                {{ quizScore }} <span class="text-slate-400 text-lg">/ {{ quizQuestions.length * 10 }}</span>
                            </div>
                            <p class="text-xs font-bold text-slate-700">
                                Benar: {{ Math.round(quizScore / 10) }} dari {{ quizQuestions.length }} Kanji
                            </p>
                        </div>

                        <div class="flex items-center justify-center gap-3 pt-2">
                            <button 
                                type="button"
                                @click="startQuizMode"
                                class="px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/25 transition-all cursor-pointer flex items-center gap-2"
                            >
                                <span>🔄 Mainkan Lagi</span>
                            </button>
                            <button 
                                type="button"
                                @click="activeMode = 'flashcard'"
                                class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all cursor-pointer"
                            >
                                <span>Kembali ke Flashcard</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import HanziWriter from 'hanzi-writer';
import { 
    Languages, 
    Folder, 
    ChevronLeft, 
    ChevronRight, 
    Shuffle, 
    Search, 
    Play, 
    Zap, 
    ArrowLeft,
    RotateCcw,
    Eye,
    EyeOff
} from 'lucide-vue-next';

const props = defineProps({
    levels: {
        type: Object,
        default: () => ({}),
    },
    topics: {
        type: Array,
        default: () => [],
    },
    selectedTopicId: [Number, String],
    selectedLevel: {
        type: String,
        default: 'ALL',
    },
    kanjis: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Object,
        default: () => ({ total: 0, total_n5: 0, total_n4: 0 }),
    },
});

const { isJapanese } = useLang();

// Mode: 'flashcard' | 'canvas' | 'quiz'
const activeMode = ref('flashcard');

// Practice Mode state (true = in focused arena, false = in topic lobby)
const isPracticeMode = ref(Boolean(props.selectedTopicId));

const searchTopicQuery = ref('');

// Filter topics reactive
const filteredTopics = computed(() => {
    if (!searchTopicQuery.value.trim()) return props.topics;
    const q = searchTopicQuery.value.toLowerCase().trim();
    return props.topics.filter(t => 
        (t.title && t.title.toLowerCase().includes(q)) ||
        (t.description && t.description.toLowerCase().includes(q))
    );
});

// Active topic object
const currentActiveTopic = computed(() => {
    if (!props.selectedTopicId) return null;
    return props.topics.find(t => t.id == props.selectedTopicId) || null;
});

// Change Level
const changeLevel = (lvl) => {
    router.get(route('siswa.kanjis.index'), {
        level: lvl,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
    isPracticeMode.value = false;
};

// Start Training for a Topic or Full Deck
const startTraining = (topicId, mode = 'flashcard') => {
    activeMode.value = mode;
    currentIndex.value = 0;
    isFlipped.value = false;
    
    if (topicId) {
        router.get(route('siswa.kanjis.index'), {
            level: props.selectedLevel,
            topic_id: topicId,
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                isPracticeMode.value = true;
                if (mode === 'quiz') startQuizMode();
            }
        });
    } else {
        // Full Deck
        router.get(route('siswa.kanjis.index'), {
            level: props.selectedLevel,
            topic_id: 'all',
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                isPracticeMode.value = true;
                if (mode === 'quiz') startQuizMode();
            }
        });
    }
};

// Exit Practice Mode back to Topic Lobby
const exitPracticeMode = () => {
    router.get(route('siswa.kanjis.index'), {
        level: props.selectedLevel,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            isPracticeMode.value = false;
        }
    });
};

// Active Kanji Pool
const activeKanjiList = computed(() => {
    return props.kanjis || [];
});

// Flashcard 3D State
const currentIndex = ref(0);
const isFlipped = ref(false);

const currentKanji = computed(() => {
    if (activeKanjiList.value.length === 0) return null;
    return activeKanjiList.value[currentIndex.value] || activeKanjiList.value[0];
});

const nextCard = () => {
    if (activeKanjiList.value.length === 0) return;
    isFlipped.value = false;
    if (currentIndex.value < activeKanjiList.value.length - 1) {
        currentIndex.value++;
    } else {
        currentIndex.value = 0;
    }
};

const prevCard = () => {
    if (activeKanjiList.value.length === 0) return;
    isFlipped.value = false;
    if (currentIndex.value > 0) {
        currentIndex.value--;
    } else {
        currentIndex.value = activeKanjiList.value.length - 1;
    }
};

const shuffleCards = () => {
    if (activeKanjiList.value.length <= 1) return;
    isFlipped.value = false;
    currentIndex.value = Math.floor(Math.random() * activeKanjiList.value.length);
};

// ================= GUIDED KANJI TRACING (HANZI WRITER) STATE & LOGIC =================
const guidedWriterTargetRef = ref(null);
let writerInstance = null;
const isStrokeLoading = ref(false);
const strokeLoadError = ref(false);
const isStrokeAnimating = ref(false);
const isGuidedCompleted = ref(false);
const showGuidedOutline = ref(true);

const getWriterSize = () => {
    if (typeof window === 'undefined') return 340;
    if (window.innerWidth < 640) {
        return Math.min(window.innerWidth - 32, 340);
    }
    return 340;
};

const initGuidedWriter = () => {
    if (!guidedWriterTargetRef.value || !currentKanji.value?.kanji) return;
    
    // Clear previous SVG content in target container
    guidedWriterTargetRef.value.innerHTML = '';
    
    isStrokeLoading.value = true;
    strokeLoadError.value = false;
    isGuidedCompleted.value = false;
    isStrokeAnimating.value = false;
    
    const size = getWriterSize();
    const char = currentKanji.value.kanji.charAt(0);
    
    try {
        const createWriter = HanziWriter.create || HanziWriter;
        writerInstance = createWriter(guidedWriterTargetRef.value, char, {
            width: size,
            height: size,
            padding: 15,
            showOutline: showGuidedOutline.value,
            strokeAnimationSpeed: 1.2,
            delayBetweenStrokes: 120,
            strokeColor: '#f8fafc',      // Bright clean white fill (matching user's screenshot)
            outlineColor: '#334155',     // Dark slate stencil outline (matching user's screenshot)
            drawingColor: '#e11d48',     // Rose/red trace stroke
            drawingWidth: 16,
            showCharacter: false,
            showHintAfterMisses: 1,      // Show red guidance dot/line after 1 miss
            highlightColor: '#e11d48',   // Red guidance dot
            onLoadCharDataError: () => {
                isStrokeLoading.value = false;
                strokeLoadError.value = true;
            },
            onLoadCharDataSuccess: () => {
                isStrokeLoading.value = false;
                strokeLoadError.value = false;
                startGuidedQuiz();
            }
        });
    } catch (e) {
        console.error('Failed to init HanziWriter:', e);
        isStrokeLoading.value = false;
        strokeLoadError.value = true;
    }
};

const startGuidedQuiz = () => {
    if (!writerInstance) return;
    isGuidedCompleted.value = false;
    writerInstance.quiz({
        showOutline: showGuidedOutline.value,
        showCharacter: false,
        showHintAfterMisses: 1,
        onComplete: () => {
            isGuidedCompleted.value = true;
        }
    });
};

const playStrokeDemo = () => {
    if (!writerInstance || isStrokeAnimating.value) return;
    isStrokeAnimating.value = true;
    writerInstance.animateCharacter({
        onComplete: () => {
            isStrokeAnimating.value = false;
            startGuidedQuiz();
        }
    });
};

const restartGuidedWriting = () => {
    if (!writerInstance) return;
    isGuidedCompleted.value = false;
    startGuidedQuiz();
};

const toggleGuidedOutline = () => {
    showGuidedOutline.value = !showGuidedOutline.value;
    if (writerInstance) {
        if (showGuidedOutline.value) {
            writerInstance.showOutline();
        } else {
            writerInstance.hideOutline();
        }
    }
};

const prevKanjiGuided = () => {
    prevCard();
};

const nextKanjiGuided = () => {
    nextCard();
};

// Automatically re-initialize guided writer when currentKanji changes
watch(() => currentIndex.value, () => {
    if (activeMode.value === 'canvas') {
        nextTick(() => {
            initGuidedWriter();
        });
    }
});

// Re-init guided writer when student switches to canvas mode
watch(() => activeMode.value, (newMode) => {
    if (newMode === 'canvas') {
        nextTick(() => {
            initGuidedWriter();
        });
    }
});

const handleResize = () => {
    if (activeMode.value === 'canvas') {
        initGuidedWriter();
    }
};

// Speed Quiz State & Logic
const quizQuestions = ref([]);
const quizIndex = ref(0);
const quizScore = ref(0);
const quizStreak = ref(0);
const quizAnswerSelected = ref(null);
const quizFinished = ref(false);

const currentQuizQuestion = computed(() => {
    if (quizQuestions.value.length === 0) return null;
    return quizQuestions.value[quizIndex.value] || null;
});

const startQuizMode = () => {
    if (activeKanjiList.value.length < 4) {
        activeMode.value = 'flashcard';
        return;
    }
    activeMode.value = 'quiz';
    quizFinished.value = false;
    quizScore.value = 0;
    quizStreak.value = 0;
    quizIndex.value = 0;
    quizAnswerSelected.value = null;

    const shuffledPool = [...activeKanjiList.value].sort(() => 0.5 - Math.random());
    const selectedPool = shuffledPool.slice(0, Math.min(10, shuffledPool.length));

    quizQuestions.value = selectedPool.map(target => {
        const distractors = activeKanjiList.value
            .filter(k => k.id !== target.id)
            .sort(() => 0.5 - Math.random())
            .slice(0, 3)
            .map(k => k.meaning_id);

        const options = [...distractors, target.meaning_id].sort(() => 0.5 - Math.random());

        return {
            kanji: target,
            options: options,
        };
    });
};

const selectQuizAnswer = (option) => {
    if (quizAnswerSelected.value !== null) return;
    quizAnswerSelected.value = option;

    const isCorrect = option === currentQuizQuestion.value.kanji.meaning_id;
    if (isCorrect) {
        quizScore.value += 10;
        quizStreak.value += 1;
    } else {
        quizStreak.value = 0;
    }

    setTimeout(() => {
        if (quizIndex.value < quizQuestions.value.length - 1) {
            quizIndex.value++;
            quizAnswerSelected.value = null;
        } else {
            quizFinished.value = true;
        }
    }, 900);
};

const getQuizOptionClass = (option) => {
    if (quizAnswerSelected.value === null) {
        return 'bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300 text-slate-800 border-slate-200';
    }

    const correctMeaning = currentQuizQuestion.value?.kanji?.meaning_id;
    if (option === correctMeaning) {
        return 'bg-emerald-50 text-emerald-800 border-emerald-400 ring-2 ring-emerald-300 scale-[1.01]';
    }

    if (quizAnswerSelected.value === option && option !== correctMeaning) {
        return 'bg-rose-50 text-rose-800 border-rose-400 ring-2 ring-rose-300';
    }

    return 'bg-slate-50 text-slate-400 border-slate-200 opacity-60';
};

const getLevelBadgeClass = (level) => {
    switch (level) {
        case 'N5':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'N4':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'N3':
            return 'bg-purple-50 text-purple-700 border-purple-200';
        default:
            return 'bg-slate-100 text-slate-700 border-slate-200';
    }
};

// Keyboard Shortcuts
const handleKeyDown = (e) => {
    if (!isPracticeMode.value) return;

    if (activeMode.value === 'flashcard') {
        if (e.code === 'Space') {
            e.preventDefault();
            isFlipped.value = !isFlipped.value;
        } else if (e.code === 'ArrowRight') {
            e.preventDefault();
            nextCard();
        } else if (e.code === 'ArrowLeft') {
            e.preventDefault();
            prevCard();
        }
    } else if (activeMode.value === 'canvas') {
        if (e.code === 'ArrowRight') {
            e.preventDefault();
            nextKanjiGuided();
        } else if (e.code === 'ArrowLeft') {
            e.preventDefault();
            prevKanjiGuided();
        }
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
    window.removeEventListener('resize', handleResize);
});
</script>

<style scoped>
.perspective-1000 {
    perspective: 1000px;
}
.transform-style-3d {
    transform-style: preserve-3d;
}
.rotate-y-180 {
    transform: rotateY(180deg);
}
</style>
