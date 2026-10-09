<template>
    <Head :title="isJapanese ? '漢字フラッシュカード & クイズ - 正夢' : 'Kanji Flashcards & Quiz Trainer - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-16">
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

                    <!-- Right: Mode Switcher (Clean & Distinct) -->
                    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-2xl w-full md:w-auto justify-between md:justify-start">
                        <button 
                            type="button"
                            @click="activeMode = 'flashcard'"
                            class="flex-1 md:flex-initial px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                            :class="activeMode === 'flashcard' 
                                ? 'bg-white text-slate-950 shadow-xs font-black' 
                                : 'text-slate-600 hover:text-slate-900'"
                        >
                            <span>🃏</span>
                            <span>Flashcard</span>
                        </button>
                        <button 
                            type="button"
                            @click="startQuizMode"
                            class="flex-1 md:flex-initial px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                            :class="activeMode === 'quiz' 
                                ? 'bg-indigo-600 text-white shadow-xs font-black' 
                                : 'text-slate-600 hover:text-slate-900'"
                        >
                            <span>⚡</span>
                            <span>Kuis Kilat</span>
                        </button>
                        <button 
                            type="button"
                            @click="activeMode = 'list'"
                            class="flex-1 md:flex-initial px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                            :class="activeMode === 'list' 
                                ? 'bg-slate-900 text-white shadow-xs font-black' 
                                : 'text-slate-600 hover:text-slate-900'"
                        >
                            <span>📖</span>
                            <span>Kamus Kanji</span>
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
                                    <span v-if="currentKanji?.stroke_count" class="px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold font-jp">
                                        {{ currentKanji.stroke_count }} Goresan (画)
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
                                    <span v-if="currentKanji?.stroke_count" class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-[10px] font-mono font-bold">
                                        {{ currentKanji.stroke_count }} 画
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

                    <!-- Controls: Previous, Mastered Flags, Next -->
                    <div class="flex items-center justify-between gap-2 sm:gap-4 pt-1">
                        <button 
                            type="button"
                            @click="prevCard" 
                            class="px-4 py-2.5 sm:py-3 rounded-2xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 shadow-xs flex items-center gap-1.5 cursor-pointer shrink-0"
                        >
                            <ChevronLeft class="w-4 h-4" />
                            <span class="hidden sm:inline">Sebelumnya</span>
                        </button>

                        <div class="flex items-center gap-2">
                            <button 
                                type="button"
                                @click="markMastered(false)" 
                                class="px-3 sm:px-4 py-2.5 sm:py-3 rounded-2xl bg-rose-50 text-rose-700 font-bold text-xs hover:bg-rose-100 border border-rose-200 transition-all cursor-pointer flex items-center gap-1.5"
                            >
                                <XCircle class="w-4 h-4 text-rose-500" />
                                <span class="hidden sm:inline">Belum Hafal</span>
                            </button>
                            <button 
                                type="button"
                                @click="markMastered(true)" 
                                class="px-3 sm:px-4 py-2.5 sm:py-3 rounded-2xl bg-emerald-50 text-emerald-700 font-bold text-xs hover:bg-emerald-100 border border-emerald-200 transition-all cursor-pointer flex items-center gap-1.5"
                            >
                                <CheckCircle2 class="w-4 h-4 text-emerald-500" />
                                <span class="hidden sm:inline">Sudah Hafal</span>
                            </button>
                        </div>

                        <button 
                            type="button"
                            @click="nextCard" 
                            class="px-4 py-2.5 sm:py-3 rounded-2xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 shadow-md flex items-center gap-1.5 cursor-pointer shrink-0"
                        >
                            <span class="hidden sm:inline">Berikutnya</span>
                            <ChevronRight class="w-4 h-4" />
                        </button>
                    </div>

                    <!-- Keyboard Shortcut Hint -->
                    <p class="text-center text-[11px] text-slate-400 font-medium">
                        Tips Keyboard: <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-300 text-slate-600 font-mono text-[10px]">Spasi</kbd> untuk balik kartu • <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-300 text-slate-600 font-mono text-[10px]">◀</kbd> <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-300 text-slate-600 font-mono text-[10px]">▶</kbd> untuk navigasi
                    </p>
                </div>

                <!-- SUB-VIEW 2: SPEED QUIZ MODE -->
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

                <!-- SUB-VIEW 3: KANJI GRID LIST -->
                <div v-else class="space-y-4">
                    <!-- Search Bar in Active Kanji Pool -->
                    <div class="flex items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                        <div class="relative flex-1 max-w-md">
                            <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                            <input 
                                type="text" 
                                v-model="searchListQuery" 
                                placeholder="Cari kanji / onyomi / kunyomi / arti..." 
                                class="w-full pl-9 pr-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none"
                            />
                        </div>
                        <span class="text-xs font-bold text-slate-500 whitespace-nowrap">
                            {{ filteredKanjiList.length }} Kanji
                        </span>
                    </div>

                    <!-- Visual Kanji Cards Grid -->
                    <div v-if="filteredKanjiList.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 sm:gap-4">
                        <div 
                            v-for="k in filteredKanjiList" 
                            :key="k.id"
                            class="bg-white rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all p-4 flex flex-col justify-between space-y-2.5 group relative overflow-hidden"
                        >
                            <div class="flex items-center justify-between gap-1 text-[10px]">
                                <span class="px-2 py-0.5 rounded-full font-black border font-mono" :class="getLevelBadgeClass(k.level)">
                                    {{ k.level }}
                                </span>
                                <span v-if="k.stroke_count" class="font-mono text-slate-400 font-bold font-jp">
                                    {{ k.stroke_count }}画
                                </span>
                            </div>

                            <div class="text-center py-1 space-y-1">
                                <div class="text-4xl sm:text-5xl font-black text-slate-900 font-jp tracking-tight group-hover:scale-105 transition-transform">
                                    {{ k.kanji }}
                                </div>
                                <div class="text-xs font-bold text-indigo-600 font-jp truncate">
                                    {{ k.hiragana }}
                                </div>
                                <div class="text-xs font-black text-slate-800 line-clamp-2 pt-1.5 border-t border-slate-100">
                                    {{ k.meaning_id }}
                                </div>
                            </div>

                            <div v-if="k.notes" class="bg-slate-50 p-2 rounded-xl text-[10px] text-slate-600 font-jp line-clamp-2">
                                {{ k.notes }}
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-16 text-center text-slate-500 bg-white rounded-3xl border border-dashed border-slate-200">
                        <Languages class="w-10 h-10 text-slate-300 mx-auto mb-2" />
                        <p class="font-bold text-sm text-slate-700">Tidak ada huruf kanji yang cocok dengan pencarian.</p>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
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
    CheckCircle2,
    XCircle
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

// Mode: 'flashcard' | 'quiz' | 'list'
const activeMode = ref('flashcard');

// Practice Mode state (true = in focused arena, false = in topic lobby)
const isPracticeMode = ref(Boolean(props.selectedTopicId));

const searchTopicQuery = ref('');
const searchListQuery = ref('');

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

const filteredKanjiList = computed(() => {
    let list = [...activeKanjiList.value];
    if (searchListQuery.value.trim()) {
        const q = searchListQuery.value.toLowerCase().trim();
        list = list.filter(k => 
            (k.kanji && k.kanji.toLowerCase().includes(q)) ||
            (k.hiragana && k.hiragana.toLowerCase().includes(q)) ||
            (k.meaning_id && k.meaning_id.toLowerCase().includes(q)) ||
            (k.onyomi && k.onyomi.toLowerCase().includes(q)) ||
            (k.kunyomi && k.kunyomi.toLowerCase().includes(q)) ||
            (k.romaji && k.romaji.toLowerCase().includes(q)) ||
            (k.notes && k.notes.toLowerCase().includes(q))
        );
    }
    return list;
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

const markMastered = () => {
    nextCard();
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
    if (!isPracticeMode.value || activeMode.value !== 'flashcard') return;

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
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
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
