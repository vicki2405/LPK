<template>
    <Head title="Kotoba Flashcards & Quiz Trainer - Masayume" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-16">
            <!-- ================= HERO HEADER BANNER ================= -->
            <div v-if="!isPracticeMode" class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white p-5 sm:p-7 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-6">
                <!-- Japanese Motif Watermark -->
                <div class="absolute -right-4 -bottom-6 font-jp text-8xl sm:text-9xl font-black text-white select-none pointer-events-none opacity-5">
                    単語
                </div>

                <div class="relative z-10 space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30 font-jp">
                        <Layers class="w-3.5 h-3.5 text-amber-400" />
                        <span>Gym Hafalan Kosakata (Kotoba Memory Trainer)</span>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold tracking-tight">
                            Kotoba Flashcards & Quiz
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl leading-relaxed">
                            Modul hafalan kosakata bertingkat: Kuasai per topik, uji pemahaman lewat kuis kilat, dan perdalam dengan kamus audio Tokyo.
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
                            ? 'bg-amber-400 text-slate-950 shadow-md font-black' 
                            : 'text-slate-300 hover:text-white hover:bg-white/10'"
                    >
                        {{ key === 'ALL' ? 'Semua' : key }}
                    </button>
                </div>
            </div>

            <!-- ================= STATE 1: TOPIC MODULE HUB (LOBBY TOPIK) ================= -->
            <div v-if="!isPracticeMode" class="space-y-6">
                <!-- Section Header & Quick Search -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/90 shadow-xs">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">
                                Pilih Modul Topik Belajar
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500">
                            Tersedia <span class="font-bold text-slate-800">{{ topics.length }} topik</span> pada level <span class="font-bold text-amber-600">{{ selectedLevel }}</span> (Total {{ activeVocabList.length }} Kosakata).
                        </p>
                    </div>

                    <!-- Search Topic Input -->
                    <div class="relative w-full sm:w-72">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                        <input 
                            type="text" 
                            v-model="searchTopicQuery" 
                            placeholder="Cari judul modul topik..." 
                            class="w-full pl-9 pr-3.5 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all"
                        />
                    </div>
                </div>

                <!-- Grid Modul Topik -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- SPECIAL CARD: Full Deck (Semua Kosakata Level Ini) -->
                    <div 
                        class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white rounded-3xl p-5 border border-slate-800 shadow-md hover:shadow-xl transition-all flex flex-col justify-between group relative overflow-hidden"
                    >
                        <div class="absolute -right-4 -bottom-4 font-jp text-7xl font-black text-white/5 pointer-events-none select-none">
                            全
                        </div>
                        <div class="space-y-3 relative z-10">
                            <div class="flex items-center justify-between">
                                <span class="w-10 h-10 rounded-2xl bg-amber-400/20 text-amber-300 border border-amber-400/30 flex items-center justify-center text-lg font-bold">
                                    📚
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-400/20 text-amber-300 text-[11px] font-mono font-bold">
                                    {{ activeVocabList.length }} Kosakata
                                </span>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-white group-hover:text-amber-300 transition-colors">
                                    Semua Kosakata (Full Deck)
                                </h3>
                                <p class="text-xs text-slate-400 mt-1 line-clamp-2">
                                    Latihan seluruh kumpulan kosakata pada level {{ selectedLevel }} tanpa batasan topik.
                                </p>
                            </div>
                        </div>

                        <div class="pt-5 flex items-center gap-2 relative z-10">
                            <button 
                                type="button"
                                @click="startTraining(null, 'flashcard')"
                                class="flex-1 py-2.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs font-black transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-md"
                            >
                                <Play class="w-3.5 h-3.5 fill-current" />
                                <span>Mulai Flashcard</span>
                            </button>
                            <button 
                                type="button"
                                @click="startTraining(null, 'quiz')"
                                class="p-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center border border-white/10"
                                title="Langsung Mulai Kuis Kilat"
                            >
                                <Zap class="w-4 h-4 text-amber-300" />
                            </button>
                        </div>
                    </div>

                    <!-- TOPIC CARDS -->
                    <div 
                        v-for="top in filteredTopics" 
                        :key="top.id"
                        class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:border-amber-400/60 transition-all flex flex-col justify-between group"
                    >
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center text-lg font-bold">
                                    {{ top.icon || '📁' }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-mono font-bold">
                                        {{ top.level || selectedLevel }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100/70 text-amber-900 text-[11px] font-mono font-bold">
                                        {{ top.vocabularies_count ?? 0 }} Kata
                                    </span>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 font-jp group-hover:text-amber-600 transition-colors">
                                    {{ top.title }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                    {{ top.description || 'Kumpulan kosakata esensial untuk menguasai topik percakapan dan ujian JLPT.' }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-5 flex items-center gap-2">
                            <button 
                                type="button"
                                @click="startTraining(top.id, 'flashcard')"
                                class="flex-1 py-2.5 rounded-2xl bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-xs"
                            >
                                <Play class="w-3.5 h-3.5 fill-current" />
                                <span>Pelajari Modul</span>
                            </button>
                            <button 
                                type="button"
                                @click="startTraining(top.id, 'quiz')"
                                class="p-2.5 rounded-2xl bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold transition-all cursor-pointer flex items-center justify-center border border-amber-200"
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
                    <h3 class="text-sm font-bold text-slate-700">Tidak ada topik yang sesuai dengan pencarian</h3>
                    <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci lain atau ubah filter level di atas.</p>
                </div>
            </div>

            <!-- ================= STATE 2: FOCUSED PRACTICE ARENA (ARENA BELAJAR) ================= -->
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
                                    {{ currentActiveTopic ? currentActiveTopic.title : 'Semua Kosakata (Full Deck)' }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-mono font-bold shrink-0">
                                    {{ activeVocabList.length }} Kata
                                </span>
                            </div>
                            <span class="text-[11px] text-slate-400 block truncate">
                                Level {{ selectedLevel }} • Sesi Hafalan Aktif
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
                                ? 'bg-amber-500 text-slate-950 shadow-xs font-black' 
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
                            <span>Daftar Kata</span>
                        </button>
                    </div>
                </div>

                <!-- Empty State if No Vocabs in this Topic -->
                <div v-if="activeVocabList.length === 0" class="bg-white rounded-3xl p-12 text-center border border-dashed border-slate-200">
                    <p class="text-sm font-bold text-slate-700">Belum ada kosakata di dalam topik ini.</p>
                    <button 
                        type="button"
                        @click="exitPracticeMode" 
                        class="mt-4 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold"
                    >
                        Kembali ke Pilihan Topik
                    </button>
                </div>

                <!-- SUB-VIEW 1: FLASHCARD 3D FLIP MODE -->
                <div v-else-if="activeMode === 'flashcard'" class="max-w-2xl mx-auto space-y-5">
                    <!-- Progress Bar & Indicator -->
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 px-1">
                        <span>Kartu {{ currentCardIndex + 1 }} dari {{ activeVocabList.length }}</span>
                        <div class="w-32 sm:w-48 h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                            <div 
                                class="h-full bg-amber-500 transition-all duration-300" 
                                :style="{ width: `${((currentCardIndex + 1) / activeVocabList.length) * 100}%` }"
                            ></div>
                        </div>
                    </div>

                    <!-- 3D Flip Card Container -->
                    <div class="perspective-1000">
                        <div 
                            @click="isFlipped = !isFlipped" 
                            class="relative w-full min-h-[300px] sm:min-h-[340px] rounded-3xl cursor-pointer transition-transform duration-500 transform-style-3d shadow-xl hover:shadow-2xl border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-slate-100 flex items-center justify-center p-8 text-center select-none"
                            :class="{ 'rotate-y-180 bg-gradient-to-br from-amber-50/80 to-rose-50/80': isFlipped }"
                        >
                            <!-- FRONT SIDE: Kanji / Kata Utama ONLY -->
                            <div v-show="!isFlipped" class="flex flex-col items-center justify-center space-y-4">
                                <div class="flex items-center gap-1.5 flex-wrap justify-center">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-900 text-white text-[10px] font-black uppercase font-mono">
                                        {{ currentVocab?.level || selectedLevel }}
                                    </span>
                                    <span class="text-[10px] font-bold text-japan-red uppercase tracking-wider bg-rose-50 px-2.5 py-0.5 rounded-lg border border-rose-100">
                                        {{ currentVocab?.word_type || 'Kata Benda' }}
                                    </span>
                                </div>

                                <h2 class="text-5xl sm:text-6xl font-black text-slate-950 font-jp tracking-wide py-4">
                                    {{ currentVocab?.kanji || currentVocab?.hiragana }}
                                </h2>
                                
                                <div class="flex items-center gap-1.5 text-xs text-slate-400 font-bold pt-2">
                                    <span>👆 Klik kartu untuk melihat cara baca & arti</span>
                                </div>
                            </div>

                            <!-- BACK SIDE: Cara Baca (Hiragana & Romaji), Arti, dan Reibun -->
                            <div v-show="isFlipped" class="flex flex-col items-center justify-center space-y-3.5 transform rotate-y-180 w-full max-w-md">
                                <div class="flex items-center gap-2">
                                    <span v-if="currentVocab?.kanji" class="px-2.5 py-0.5 rounded-lg bg-amber-100 text-amber-900 text-[11px] font-black font-jp">
                                        漢字: {{ currentVocab?.kanji }}
                                    </span>
                                    <span class="text-[10px] font-bold text-japan-red uppercase tracking-wider bg-rose-50 px-2.5 py-0.5 rounded-lg border border-rose-100">
                                        {{ currentVocab?.word_type || 'Kata Benda' }}
                                    </span>
                                </div>

                                <!-- Cara Baca Hiragana & Romaji -->
                                <div class="space-y-0.5 py-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">
                                        Cara Baca (読み方)
                                    </span>
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="text-2xl sm:text-3xl font-black text-amber-600 font-jp tracking-wide">
                                            {{ currentVocab?.hiragana }}
                                        </span>
                                        <button 
                                            type="button"
                                            @click.stop="speakJapanese(currentVocab?.hiragana || currentVocab?.kanji, currentVocab?.audio_file)" 
                                            class="p-1.5 rounded-full bg-amber-100 hover:bg-amber-200 text-amber-800 transition-all shadow-xs cursor-pointer"
                                            :class="{ 'animate-pulse ring-2 ring-amber-300': isPlayingAudio && currentlyPlayingText === (currentVocab?.hiragana || currentVocab?.kanji) }"
                                            title="Dengarkan Pelafalan Asli Jepang (Tokyo 🔊)"
                                        >
                                            <Volume2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                    <div v-if="currentVocab?.romaji" class="text-xs sm:text-sm font-bold text-slate-500 font-mono">
                                        {{ currentVocab?.romaji }}
                                    </div>
                                </div>

                                <!-- Arti Bahasa Indonesia -->
                                <div class="bg-white/90 p-3 rounded-2xl border border-amber-200 w-full shadow-2xs">
                                    <span class="text-[10px] font-black text-amber-700 uppercase tracking-wider block mb-0.5">
                                        Arti Bahasa Indonesia
                                    </span>
                                    <h2 class="text-lg sm:text-xl font-black text-slate-900 uppercase">
                                        {{ currentVocab?.meaning_id }}
                                    </h2>
                                </div>

                                <!-- Contoh Kalimat (例文 / Reibun) -->
                                <div v-if="currentVocab?.example_sentence_jp" class="bg-white/95 backdrop-blur-xs p-3 rounded-2xl border border-amber-200 text-xs text-slate-800 font-jp text-left shadow-2xs w-full">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-japan-red font-black text-[11px]">例文 (Reibun):</span>
                                        <button 
                                            type="button"
                                            @click.stop="speakJapanese(currentVocab?.example_sentence_jp)" 
                                            class="p-1 rounded-lg text-slate-500 hover:text-slate-800 transition-colors cursor-pointer"
                                            :class="{ 'text-amber-600 animate-pulse': isPlayingAudio && currentlyPlayingText === currentVocab?.example_sentence_jp }"
                                            title="Dengarkan Contoh Kalimat (Tokyo 🔊)"
                                        >
                                            <Volume2 class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                    <p class="font-bold leading-relaxed">{{ currentVocab?.example_sentence_jp }}</p>
                                    <p v-if="currentVocab?.example_sentence_id" class="text-slate-500 text-[11px] font-sans mt-0.5">{{ currentVocab?.example_sentence_id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Flashcard Controls: Previous, Mastered Flags, Next -->
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
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xl space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-amber-500 text-slate-950 text-xs font-black">
                                    SOAL {{ quizIndex + 1 }}/{{ activeVocabList.length }}
                                </span>
                                <span class="text-xs text-slate-500 font-bold">Pilih arti bahasa Indonesia yang tepat</span>
                            </div>
                            <button 
                                type="button"
                                @click="activeMode = 'flashcard'" 
                                class="text-xs font-bold text-slate-400 hover:text-slate-700 cursor-pointer"
                            >
                                ✕ Keluar Kuis
                            </button>
                        </div>

                        <div class="text-center py-6 space-y-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                                {{ quizCurrentVocab?.word_type || 'Kata Benda' }}
                            </span>
                            <div class="space-y-1">
                                <div class="flex items-center justify-center gap-2">
                                    <h2 class="text-3xl font-black text-slate-900 font-jp">
                                        {{ quizCurrentVocab?.kanji || quizCurrentVocab?.hiragana }}
                                    </h2>
                                    <button 
                                        type="button"
                                        @click="speakJapanese(quizCurrentVocab?.hiragana || quizCurrentVocab?.kanji, quizCurrentVocab?.audio_file)" 
                                        class="p-1.5 rounded-full transition-all shadow-2xs cursor-pointer"
                                        :class="isPlayingAudio && currentlyPlayingText === (quizCurrentVocab?.hiragana || quizCurrentVocab?.kanji) 
                                            ? 'bg-amber-100 text-amber-700 animate-pulse ring-2 ring-amber-300' 
                                            : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'"
                                        title="Dengarkan Pelafalan Asli Jepang (Tokyo 🔊)"
                                    >
                                        <Volume2 class="w-4 h-4" />
                                    </button>
                                </div>
                                <p v-if="quizCurrentVocab?.kanji" class="text-sm font-bold text-rose-600 font-jp">
                                    {{ quizCurrentVocab?.hiragana }}
                                </p>
                                <p v-if="quizCurrentVocab?.romaji" class="text-xs font-mono text-slate-400">
                                    {{ quizCurrentVocab?.romaji }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <button 
                                v-for="(opt, idx) in quizOptions" 
                                :key="idx"
                                type="button"
                                @click="answerQuiz(opt)"
                                class="p-4 rounded-2xl border text-xs font-bold text-left transition-all cursor-pointer flex items-center gap-3"
                                :class="quizSelectedAnswer === opt 
                                    ? (opt === quizCurrentVocab?.meaning_id ? 'bg-emerald-500 text-white border-emerald-600 shadow-md' : 'bg-rose-500 text-white border-rose-600 shadow-md')
                                    : 'bg-white border-slate-200 text-slate-800 hover:border-slate-300 hover:bg-slate-50/70'"
                            >
                                <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-mono shrink-0">
                                    {{ ['A', 'B', 'C', 'D'][idx] }}
                                </span>
                                <span class="text-sm">{{ opt }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- SUB-VIEW 3: TABLE / VOCABULARY LIST -->
                <div v-else class="space-y-4">
                    <!-- Search inside Active Vocabs -->
                    <div class="flex items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-slate-200 shadow-xs">
                        <div class="relative flex-1 max-w-md">
                            <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                            <input 
                                type="text" 
                                v-model="searchListQuery" 
                                placeholder="Cari kanji / hiragana / arti..." 
                                class="w-full pl-9 pr-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none"
                            />
                        </div>
                        <span class="text-xs font-bold text-slate-500 whitespace-nowrap">
                            {{ filteredVocabList.length }} Kosakata
                        </span>
                    </div>

                    <!-- Table Container -->
                    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-700">
                                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    <tr>
                                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                                        <th class="py-3.5 px-4">Kanji & Hiragana</th>
                                        <th class="py-3.5 px-4">Arti Indonesia</th>
                                        <th class="py-3.5 px-4">Jenis Kata</th>
                                        <th class="py-3.5 px-4">Contoh Kalimat (Reibun)</th>
                                        <th class="py-3.5 px-4 text-center">Suara</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="(v, idx) in filteredVocabList" :key="v.id" class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-4 text-center font-bold text-slate-400 font-mono">
                                            {{ idx + 1 }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-baseline gap-2">
                                                <span v-if="v.kanji" class="text-base font-black text-slate-900 font-jp">{{ v.kanji }}</span>
                                                <span class="text-sm font-bold text-rose-600 font-jp" :class="{ 'text-base': !v.kanji }">{{ v.hiragana }}</span>
                                            </div>
                                            <span v-if="v.romaji" class="text-[11px] font-mono text-slate-400 block mt-0.5">{{ v.romaji }}</span>
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-slate-900 text-sm">
                                            {{ v.meaning_id }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ v.word_type || 'Kata' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 max-w-sm">
                                            <div v-if="v.example_sentence_jp" class="space-y-0.5 text-slate-600">
                                                <p class="font-jp text-xs font-semibold text-slate-800">{{ v.example_sentence_jp }}</p>
                                                <p v-if="v.example_sentence_id" class="text-[11px] text-slate-500 italic">{{ v.example_sentence_id }}</p>
                                            </div>
                                            <span v-else class="text-slate-300">-</span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <button 
                                                type="button" 
                                                @click="speakJapanese(v.hiragana || v.kanji, v.audio_file)"
                                                class="p-2 rounded-xl transition-all cursor-pointer inline-flex items-center justify-center shadow-2xs"
                                                :class="isPlayingAudio && currentlyPlayingText === (v.hiragana || v.kanji) 
                                                    ? 'bg-amber-100 text-amber-700 animate-pulse ring-2 ring-amber-300' 
                                                    : 'bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-600'"
                                                title="Dengarkan Pelafalan Asli Jepang (Tokyo 🔊)"
                                            >
                                                <Volume2 class="w-4 h-4" />
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredVocabList.length === 0">
                                        <td colspan="6" class="py-12 text-center text-slate-400">
                                            Tidak ada kosakata yang cocok dengan pencarian.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile List -->
                        <div class="block md:hidden divide-y divide-slate-100">
                            <div v-for="v in filteredVocabList" :key="'mob-' + v.id" class="p-4 space-y-2.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="space-y-0.5">
                                        <div class="flex items-baseline gap-2">
                                            <span v-if="v.kanji" class="text-lg font-black text-slate-950 font-jp">{{ v.kanji }}</span>
                                            <span class="text-sm font-bold text-rose-600 font-jp" :class="{ 'text-lg': !v.kanji }">{{ v.hiragana }}</span>
                                        </div>
                                        <span v-if="v.romaji" class="text-xs font-mono text-slate-400 block">{{ v.romaji }}</span>
                                    </div>
                                    <button 
                                        type="button" 
                                        @click="speakJapanese(v.hiragana || v.kanji, v.audio_file)"
                                        class="p-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all cursor-pointer"
                                    >
                                        <Volume2 class="w-4 h-4" />
                                    </button>
                                </div>
                                <div class="bg-amber-50/60 rounded-xl px-3 py-2 border border-amber-100 text-xs font-bold text-slate-900">
                                    {{ v.meaning_id }}
                                </div>
                            </div>
                        </div>
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
import { notifySuccess } from '@/Utils/alert';
import { 
    Layers, 
    Folder, 
    ChevronLeft, 
    ChevronRight, 
    Volume2, 
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
    chapters: Array,
    categories: Array,
    selectedLevel: {
        type: String,
        default: 'N5',
    },
    selectedCategory: String,
    selectedChapterId: [String, Number],
    vocabularies: {
        type: Array,
        default: () => [],
    },
});

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
    router.get(route('siswa.flashcards.index'), {
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
    currentCardIndex.value = 0;
    isFlipped.value = false;
    
    if (topicId) {
        router.get(route('siswa.flashcards.index'), {
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
        router.get(route('siswa.flashcards.index'), {
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
    router.get(route('siswa.flashcards.index'), {
        level: props.selectedLevel,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            isPracticeMode.value = false;
        }
    });
};

// Flashcard 3D State
const currentCardIndex = ref(0);
const isFlipped = ref(false);

const activeVocabList = computed(() => {
    return props.vocabularies || [];
});

const filteredVocabList = computed(() => {
    if (!searchListQuery.value.trim()) return activeVocabList.value;
    const q = searchListQuery.value.toLowerCase().trim();
    return activeVocabList.value.filter(v => 
        (v.hiragana && v.hiragana.toLowerCase().includes(q)) ||
        (v.kanji && v.kanji.toLowerCase().includes(q)) ||
        (v.romaji && v.romaji.toLowerCase().includes(q)) ||
        (v.meaning_id && v.meaning_id.toLowerCase().includes(q))
    );
});

const currentVocab = computed(() => {
    return activeVocabList.value[currentCardIndex.value] || activeVocabList.value[0];
});

const nextCard = () => {
    isFlipped.value = false;
    if (currentCardIndex.value < activeVocabList.value.length - 1) {
        currentCardIndex.value++;
    } else {
        currentCardIndex.value = 0;
    }
};

const prevCard = () => {
    isFlipped.value = false;
    if (currentCardIndex.value > 0) {
        currentCardIndex.value--;
    } else {
        currentCardIndex.value = activeVocabList.value.length - 1;
    }
};

const markMastered = (status) => {
    if (status && currentVocab.value) {
        notifySuccess('Hafalan Tercatat!', `Kosakata 「${currentVocab.value.hiragana}」 ditandai sudah hafal.`);
    }
    nextCard();
};

// Audio Pronunciation
const isPlayingAudio = ref(false);
const currentlyPlayingText = ref('');
let activeAudioEl = null;

const speakJapanese = (text, audioFile = null) => {
    if (activeAudioEl) {
        activeAudioEl.pause();
        activeAudioEl = null;
    }

    if (audioFile) {
        isPlayingAudio.value = true;
        currentlyPlayingText.value = text || '';
        activeAudioEl = new Audio(audioFile);
        activeAudioEl.onended = () => {
            isPlayingAudio.value = false;
            currentlyPlayingText.value = '';
        };
        activeAudioEl.onerror = () => {
            isPlayingAudio.value = false;
            currentlyPlayingText.value = '';
        };
        activeAudioEl.play().catch(() => {
            isPlayingAudio.value = false;
            currentlyPlayingText.value = '';
        });
        return;
    }

    if (!text || !text.trim()) return;

    isPlayingAudio.value = true;
    currentlyPlayingText.value = text.trim();

    const url = route('siswa.flashcards.pronunciation-audio', { text: text.trim() });
    activeAudioEl = new Audio(url);

    activeAudioEl.onended = () => {
        isPlayingAudio.value = false;
        currentlyPlayingText.value = '';
    };

    activeAudioEl.onerror = () => {
        isPlayingAudio.value = false;
        currentlyPlayingText.value = '';
        if ('speechSynthesis' in window) {
            const voices = window.speechSynthesis.getVoices();
            const jaVoice = voices.find(v => v.lang && v.lang.toLowerCase().startsWith('ja'));
            if (jaVoice) {
                const u = new SpeechSynthesisUtterance(text);
                u.voice = jaVoice;
                u.lang = 'ja-JP';
                window.speechSynthesis.speak(u);
            }
        }
    };

    activeAudioEl.play().catch(() => {
        isPlayingAudio.value = false;
        currentlyPlayingText.value = '';
    });
};

// Quiz Mode State & Logic
const quizIndex = ref(0);
const quizSelectedAnswer = ref(null);

const quizCurrentVocab = computed(() => {
    return activeVocabList.value[quizIndex.value] || activeVocabList.value[0];
});

const quizOptions = computed(() => {
    if (!quizCurrentVocab.value) return [];
    const correct = quizCurrentVocab.value.meaning_id;
    const others = activeVocabList.value
        .map(v => v.meaning_id)
        .filter(m => m !== correct);
    
    return [correct, ...others.slice(0, 3)].sort(() => 0.5 - Math.random());
});

const startQuizMode = () => {
    activeMode.value = 'quiz';
    quizIndex.value = 0;
    quizSelectedAnswer.value = null;
};

const answerQuiz = (selected) => {
    quizSelectedAnswer.value = selected;
    if (selected === quizCurrentVocab.value?.meaning_id) {
        notifySuccess('Jawaban Benar! 🎉', `Arti dari 「${quizCurrentVocab.value.hiragana}」 adalah ${selected}.`);
    }

    setTimeout(() => {
        quizSelectedAnswer.value = null;
        if (quizIndex.value < activeVocabList.value.length - 1) {
            quizIndex.value++;
        } else {
            quizIndex.value = 0;
            notifySuccess('Kuis Selesai', 'Anda telah menyelesaikan seluruh butir kuis kosakata ini!');
            activeMode.value = 'flashcard';
        }
    }, 900);
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
