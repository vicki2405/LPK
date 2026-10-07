<template>
    <Head title="Kotoba Flashcards & Quiz Trainer - Masayume" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-12">
            <!-- Header Banner (Compact on Mobile, Full on Desktop) -->
            <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white p-3.5 sm:p-7 rounded-2xl sm:rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-6">
                <div>
                    <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold mb-2 border border-amber-500/30">
                        <Layers class="w-3.5 h-3.5" />
                        <span>Gym Hafalan Kosakata (Kotoba Memory Trainer)</span>
                    </div>
                    <div class="flex items-center justify-between sm:block">
                        <h1 class="text-base sm:text-2xl md:text-3xl font-extrabold tracking-tight">
                            Kotoba Flashcards & Quiz
                        </h1>
                        <span class="sm:hidden px-2 py-0.5 rounded-full bg-amber-400/20 text-amber-300 text-[10px] font-bold">
                            {{ activeVocabList.length }} Kotoba
                        </span>
                    </div>
                    <p class="hidden sm:block text-xs sm:text-sm text-slate-300 mt-1 max-w-xl leading-relaxed">
                        Pusat latihan hafalan kosakata per bab lengkap: Kartu 3D flip, simulasi kuis kilat pilihan ganda, dan kamus tabel kosakata ber-audio.
                    </p>
                </div>

                <!-- Mode Toggle Buttons (3 MODES - COMPACT ON MOBILE) -->
                <div class="flex items-center gap-1 bg-white/10 backdrop-blur-md p-1 rounded-xl border border-white/20 w-full sm:w-auto justify-between sm:justify-start">
                    <button 
                        @click="activeMode = 'flashcard'"
                        class="flex-1 sm:flex-initial px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-bold transition-all cursor-pointer text-center"
                        :class="activeMode === 'flashcard' ? 'bg-white text-slate-900 shadow-md font-black' : 'text-slate-300 hover:text-white'"
                    >
                        🃏 Flashcard
                    </button>
                    <button 
                        @click="startQuizMode"
                        class="flex-1 sm:flex-initial px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-bold transition-all cursor-pointer text-center"
                        :class="activeMode === 'quiz' ? 'bg-japan-red text-white shadow-md font-black' : 'text-slate-300 hover:text-white'"
                    >
                        ⚡ Kuis Kilat
                    </button>
                    <button 
                        @click="activeMode = 'list'"
                        class="flex-1 sm:flex-initial px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-bold transition-all cursor-pointer text-center"
                        :class="activeMode === 'list' ? 'bg-blue-600 text-white shadow-md font-black' : 'text-slate-300 hover:text-white'"
                    >
                        📖 Kamus
                    </button>
                </div>
            </div>

            <!-- Multi-Filter & Search Bar (Smart Mobile Responsive) -->
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <!-- MOBILE VIEW: COMPACT SMART FILTER STRIP (HANYA ~44px, TIDAK MENUTUPI MATERI) -->
                <div class="sm:hidden p-2.5 space-y-2.5">
                    <div class="flex items-center gap-2">
                        <!-- Level Select Compact -->
                        <div class="flex-1 relative">
                            <select 
                                :value="selectedLevel" 
                                @change="applyFilter('level', $event.target.value)"
                                class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-japan-red focus:ring-japan-red py-2 pl-3 pr-7 bg-slate-50 font-jp truncate"
                            >
                                <option v-for="(lbl, key) in levels" :key="key" :value="key">
                                    🏷️ {{ lbl }}
                                </option>
                            </select>
                        </div>

                        <!-- Toggle Filter Bab & Topik Drawer Button -->
                        <button 
                            type="button"
                            @click="showMobileFilters = !showMobileFilters"
                            class="px-3 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shrink-0 border"
                            :class="showMobileFilters || activeFilterCount > 0 
                                ? 'bg-japan-red text-white border-japan-red shadow-xs' 
                                : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200'"
                        >
                            <SlidersHorizontal class="w-3.5 h-3.5" />
                            <span>Filter</span>
                            <span v-if="activeFilterCount > 0" class="w-4 h-4 rounded-full bg-white text-japan-red text-[10px] font-black flex items-center justify-center">
                                {{ activeFilterCount }}
                            </span>
                        </button>
                    </div>

                    <!-- Collapsible Filter Drawer on Mobile -->
                    <div v-show="showMobileFilters" class="pt-2 border-t border-slate-100 space-y-2.5 animate-fade-in">
                        <div class="grid grid-cols-1 gap-2">
                            <!-- Category Filter -->
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">Topik / Kategori:</label>
                                <select 
                                    :value="selectedCategory || ''" 
                                    @change="applyFilter('category', $event.target.value)"
                                    class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-japan-red focus:ring-japan-red py-2 px-3 bg-slate-50 font-jp"
                                >
                                    <option value="">Semua Kategori / Topik</option>
                                    <option v-for="cat in categories" :key="cat" :value="cat">
                                        {{ cat }}
                                    </option>
                                </select>
                            </div>

                            <!-- Chapter Filter -->
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">Bab LMS (Opsional):</label>
                                <select 
                                    :value="selectedChapterId || ''" 
                                    @change="applyFilter('chapter_id', $event.target.value)"
                                    class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-japan-red focus:ring-japan-red py-2 px-3 bg-slate-50 font-jp"
                                >
                                    <option value="">Semua Bab / Mandiri</option>
                                    <option value="none">⚡ Kosakata Mandiri</option>
                                    <option v-for="ch in chapters" :key="ch.id" :value="ch.id">
                                        第{{ ch.chapter_number }}課: {{ ch.title }}
                                    </option>
                                </select>
                            </div>

                            <!-- Search Input Mobile -->
                            <div class="relative">
                                <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                                <input 
                                    type="text" 
                                    v-model="searchListQuery" 
                                    placeholder="Cari kanji / hiragana / arti..." 
                                    class="w-full pl-8 pr-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none"
                                />
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-[11px] font-bold text-slate-500">
                                {{ filteredVocabList.length }} Kosakata Cocok
                            </span>
                            <button 
                                type="button" 
                                @click="showMobileFilters = false" 
                                class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-bold"
                            >
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>

                <!-- DESKTOP VIEW: FULL 3-COLUMN FILTER & SEARCH -->
                <div class="hidden sm:block p-5 space-y-3">
                    <div class="grid grid-cols-3 gap-3">
                        <!-- Level Filter -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">Pilih Level:</label>
                            <select 
                                :value="selectedLevel" 
                                @change="applyFilter('level', $event.target.value)"
                                class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-japan-red focus:ring-japan-red py-2 px-3 bg-slate-50 font-jp"
                            >
                                <option v-for="(lbl, key) in levels" :key="key" :value="key">
                                    {{ lbl }}
                                </option>
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">Topik / Kategori:</label>
                            <select 
                                :value="selectedCategory || ''" 
                                @change="applyFilter('category', $event.target.value)"
                                class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-japan-red focus:ring-japan-red py-2 px-3 bg-slate-50 font-jp"
                            >
                                <option value="">Semua Kategori / Topik</option>
                                <option v-for="cat in categories" :key="cat" :value="cat">
                                    {{ cat }}
                                </option>
                            </select>
                        </div>

                        <!-- Chapter Filter (Optional) -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">Bab LMS (Opsional):</label>
                            <select 
                                :value="selectedChapterId || ''" 
                                @change="applyFilter('chapter_id', $event.target.value)"
                                class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-japan-red focus:ring-japan-red py-2 px-3 bg-slate-50 font-jp"
                            >
                                <option value="">Semua Bab / Mandiri</option>
                                <option value="none">⚡ Kosakata Mandiri</option>
                                <option v-for="ch in chapters" :key="ch.id" :value="ch.id">
                                    第{{ ch.chapter_number }}課: {{ ch.title }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-2 border-t border-slate-100">
                        <div class="relative flex-1 max-w-md">
                            <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                            <input 
                                type="text" 
                                v-model="searchListQuery" 
                                placeholder="Cari kanji / hiragana / arti di daftar ini..." 
                                class="w-full pl-8 pr-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:outline-none"
                            />
                        </div>
                        <span class="text-xs font-bold text-slate-500 whitespace-nowrap">
                            {{ filteredVocabList.length }} Kosakata Terdaftar
                        </span>
                    </div>
                </div>
            </div>

            <!-- ================= MODE 1: FLASHCARD 3D FLIP MODE ================= -->
            <div v-if="activeMode === 'flashcard'" class="max-w-2xl mx-auto space-y-6">
                <!-- 3D Flip Card -->
                <div class="perspective-1000">
                    <div 
                        @click="isFlipped = !isFlipped" 
                        class="relative w-full h-72 sm:h-80 rounded-3xl cursor-pointer transition-transform duration-500 transform-style-3d shadow-xl hover:shadow-2xl border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-slate-100 flex items-center justify-center p-8 text-center select-none"
                        :class="{ 'rotate-y-180 bg-gradient-to-br from-amber-50/80 to-rose-50/80': isFlipped }"
                    >
                        <!-- FRONT SIDE: Kanji / Furigana -->
                        <div v-show="!isFlipped" class="flex flex-col items-center justify-center space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black text-japan-red uppercase tracking-wider bg-rose-50 px-3 py-1 rounded-full border border-rose-100">
                                    {{ currentVocab?.word_type || 'Kata Benda' }}
                                </span>
                                <button 
                                    @click.stop="speakJapanese(currentVocab?.hiragana || currentVocab?.kanji, currentVocab?.audio_file)" 
                                    class="p-1.5 rounded-full transition-all shadow-2xs cursor-pointer"
                                    :class="isPlayingAudio && currentlyPlayingText === (currentVocab?.hiragana || currentVocab?.kanji) 
                                        ? 'bg-amber-100 text-amber-700 animate-pulse ring-2 ring-amber-300' 
                                        : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                                    title="Dengarkan Pelafalan Asli Jepang (Tokyo 🔊)"
                                >
                                    <Volume2 class="w-4 h-4" />
                                </button>
                            </div>
                            <h2 class="text-4xl sm:text-5xl font-black text-slate-950 font-jp tracking-wide">
                                {{ currentVocab?.kanji || currentVocab?.hiragana }}
                            </h2>
                            <p class="text-base font-bold text-slate-800 font-jp">
                                {{ currentVocab?.hiragana }} <span v-if="currentVocab?.romaji" class="text-slate-500 font-normal">({{ currentVocab?.romaji }})</span>
                            </p>
                            <div class="flex items-center gap-1.5 text-xs text-slate-400 font-bold pt-2">
                                <span>👆 Klik untuk membalik arti</span>
                            </div>
                        </div>

                        <!-- BACK SIDE: Meaning & Sentence -->
                        <div v-show="isFlipped" class="flex flex-col items-center justify-center space-y-4 transform rotate-y-180">
                            <span class="text-xs font-black text-amber-900 uppercase tracking-wider bg-amber-100 px-3 py-1 rounded-full">
                                Arti Bahasa Indonesia
                            </span>
                            <h2 class="text-3xl font-black text-slate-950 uppercase">
                                {{ currentVocab?.meaning_id }}
                            </h2>
                            <div v-if="currentVocab?.example_sentence_jp" class="bg-white/90 backdrop-blur-xs p-4 rounded-2xl border border-amber-200 text-xs text-slate-800 font-jp text-left shadow-xs max-w-md">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-japan-red font-black">例文 (Reibun):</span>
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
                                <p class="font-bold">{{ currentVocab?.example_sentence_jp }}</p>
                                <p v-if="currentVocab?.example_sentence_id" class="text-slate-500 text-[11px] font-sans mt-0.5">{{ currentVocab?.example_sentence_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Controls -->
                <div class="flex items-center justify-between gap-1.5 sm:gap-4">
                    <button 
                        @click="prevCard" 
                        class="px-3 sm:px-5 py-2.5 sm:py-3 rounded-2xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 shadow-xs flex items-center gap-1.5 cursor-pointer shrink-0"
                        title="Kartu Sebelumnya"
                    >
                        <ChevronLeft class="w-4 h-4" />
                        <span class="hidden sm:inline">Sebelumnya</span>
                    </button>

                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <button 
                            @click="markMastered(false)" 
                            class="px-2.5 sm:px-4 py-2.5 sm:py-3 rounded-2xl bg-rose-50 text-rose-700 font-bold text-xs hover:bg-rose-100 border border-rose-200 transition-all cursor-pointer flex items-center gap-1"
                        >
                            <span>❌</span>
                            <span class="hidden xs:inline">Belum</span>
                            <span class="hidden sm:inline">Hafal</span>
                        </button>
                        <button 
                            @click="markMastered(true)" 
                            class="px-2.5 sm:px-4 py-2.5 sm:py-3 rounded-2xl bg-emerald-50 text-emerald-700 font-bold text-xs hover:bg-emerald-100 border border-emerald-200 transition-all cursor-pointer flex items-center gap-1"
                        >
                            <span>✅</span>
                            <span class="hidden xs:inline">Sudah</span>
                            <span class="hidden sm:inline">Hafal</span>
                        </button>
                    </div>

                    <button 
                        @click="nextCard" 
                        class="px-3 sm:px-5 py-2.5 sm:py-3 rounded-2xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 shadow-md flex items-center gap-1.5 cursor-pointer shrink-0"
                        title="Kartu Berikutnya"
                    >
                        <span class="hidden sm:inline">Berikutnya</span>
                        <ChevronRight class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- ================= MODE 2: SPEED QUIZ MODE ================= -->
            <div v-else-if="activeMode === 'quiz'" class="max-w-2xl mx-auto">
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xl space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-japan-red text-white text-xs font-black">
                                KUIS {{ quizIndex + 1 }}/{{ activeVocabList.length }}
                            </span>
                            <span class="text-xs text-slate-500 font-bold">Pilih arti bahasa Indonesia yang tepat</span>
                        </div>
                        <button @click="activeMode = 'flashcard'" class="text-xs font-bold text-slate-400 hover:text-slate-700">
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

            <!-- ================= MODE 3: FULL VOCABULARY LIST / KAMUS TABLE ================= -->
            <div v-else class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <!-- Desktop Table View (hidden on mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6 w-12 text-center">No</th>
                                <th class="py-3.5 px-4">{{ 'Kanji & Hiragana' }}</th>
                                <th class="py-3.5 px-4">Arti Indonesia</th>
                                <th class="py-3.5 px-4">Jenis Kata</th>
                                <th class="py-3.5 px-4">Contoh Kalimat (Reibun)</th>
                                <th class="py-3.5 px-4 text-center">Suara</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(v, idx) in filteredVocabList" :key="v.id" class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 sm:px-6 text-center font-bold text-slate-400 font-mono">
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
                                        {{ v.word_type }}
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
                                    Tidak ada kosakata yang cocok dengan pencarian di bab ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card Feed View (block md:hidden) -->
                <div class="block md:hidden divide-y divide-slate-100">
                    <div v-for="(v, idx) in filteredVocabList" :key="'mob-' + v.id" class="p-4 space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="space-y-0.5">
                                <div class="flex items-baseline gap-2">
                                    <span v-if="v.kanji" class="text-lg font-black text-slate-950 font-jp">{{ v.kanji }}</span>
                                    <span class="text-sm font-bold text-japan-red font-jp" :class="{ 'text-lg': !v.kanji }">{{ v.hiragana }}</span>
                                </div>
                                <span v-if="v.romaji" class="text-xs font-mono text-slate-400 block">{{ v.romaji }}</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ v.word_type || 'Kata' }}
                                </span>
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
                            </div>
                        </div>

                        <!-- Arti Bahasa Indonesia -->
                        <div class="bg-amber-50/50 rounded-xl px-3 py-2 border border-amber-100">
                            <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Arti:</span>
                            <span class="text-xs font-black text-slate-900">{{ v.meaning_id }}</span>
                        </div>

                        <!-- Contoh Kalimat -->
                        <div v-if="v.example_sentence_jp" class="space-y-0.5 text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <p class="font-jp font-semibold text-slate-800 leading-snug">{{ v.example_sentence_jp }}</p>
                            <p v-if="v.example_sentence_id" class="text-[11px] text-slate-500 italic mt-0.5">{{ v.example_sentence_id }}</p>
                        </div>
                    </div>

                    <div v-if="filteredVocabList.length === 0" class="p-8 text-center text-slate-400 text-xs">
                        Tidak ada kosakata yang cocok dengan pencarian di bab ini.
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { notifySuccess } from '@/Utils/alert';
import { 
    Layers, 
    ChevronLeft, 
    ChevronRight, 
    Volume2, 
    Search,
    SlidersHorizontal
} from 'lucide-vue-next';

const props = defineProps({
    levels: Object,
    chapters: Array,
    categories: Array,
    selectedLevel: String,
    selectedCategory: String,
    selectedChapterId: [String, Number],
    vocabularies: Array,
});

const activeMode = ref('flashcard');
const searchListQuery = ref('');
const showMobileFilters = ref(false);

const activeFilterCount = computed(() => {
    let count = 0;
    if (props.selectedCategory) count++;
    if (props.selectedChapterId) count++;
    if (searchListQuery.value) count++;
    return count;
});

const currentCardIndex = ref(0);
const isFlipped = ref(false);

const quizIndex = ref(0);
const quizSelectedAnswer = ref(null);

const activeVocabList = computed(() => {
    return props.vocabularies || [];
});

const filteredVocabList = computed(() => {
    if (!searchListQuery.value) return activeVocabList.value;
    const q = searchListQuery.value.toLowerCase();
    return activeVocabList.value.filter(v => 
        (v.hiragana && v.hiragana.toLowerCase().includes(q)) ||
        (v.kanji && v.kanji.toLowerCase().includes(q)) ||
        (v.romaji && v.romaji.toLowerCase().includes(q)) ||
        (v.meaning_id && v.meaning_id.toLowerCase().includes(q)) ||
        (v.category && v.category.toLowerCase().includes(q))
    );
});

const currentVocab = computed(() => {
    return activeVocabList.value[currentCardIndex.value] || activeVocabList.value[0];
});

const quizCurrentVocab = computed(() => {
    return activeVocabList.value[quizIndex.value] || activeVocabList.value[0];
});

const quizOptions = computed(() => {
    if (!quizCurrentVocab.value) return [];
    const correct = quizCurrentVocab.value.meaning_id;
    const others = activeVocabList.value
        .map(v => v.meaning_id)
        .filter(m => m !== correct);
    
    const shuffled = [correct, ...others.slice(0, 3)].sort(() => 0.5 - Math.random());
    return shuffled;
});

const applyFilter = (key, value) => {
    const params = {
        level: props.selectedLevel || 'ALL',
        category: props.selectedCategory || undefined,
        chapter_id: props.selectedChapterId || undefined,
    };
    params[key] = value || undefined;

    router.get(route('siswa.flashcards.index'), params, { preserveState: true });
    currentCardIndex.value = 0;
    isFlipped.value = false;
};

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
    if (status) {
        notifySuccess('Hafalan Tercatat!', `Kosakata 「${currentVocab.value.hiragana}」 ditandai sudah hafal.`);
    }
    nextCard();
};

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
        // Fallback ONLY to real Japanese voices if installed
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

    activeAudioEl.play().catch(e => {
        console.warn('Native audio play error:', e);
        isPlayingAudio.value = false;
        currentlyPlayingText.value = '';
    });
};

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
            notifySuccess('Kuis Selesai', 'Anda telah menyelesaikan seluruh butir kuis kosakata pada bab ini!');
            activeMode.value = 'flashcard';
        }
    }, 900);
};
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
