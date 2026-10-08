<template>
    <Head :title="isJapanese ? '漢字フラッシュカード & クイズ - 正夢' : 'Kanji Flashcards & Quiz Trainer - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-16">
            <!-- ================= HEADER BANNER ================= -->
            <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white p-4 sm:p-7 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-6">
                <!-- Japanese Kanji Watermark -->
                <div class="absolute -right-4 -bottom-6 font-jp text-8xl sm:text-9xl font-black text-white select-none pointer-events-none opacity-5">
                    漢字
                </div>

                <div class="relative z-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold mb-2.5 border border-indigo-500/30 font-jp">
                        <Languages class="w-3.5 h-3.5 text-indigo-400" />
                        <span>{{ isJapanese ? '漢字記憶ジム・インタラクティブ' : 'Gym Hafalan Huruf Kanji (Kanji Memory Gym)' }}</span>
                    </div>
                    <div class="flex items-center justify-between sm:block">
                        <h1 class="text-lg sm:text-2xl md:text-3xl font-extrabold tracking-tight">
                            Kanji Flashcards & Quiz
                        </h1>
                        <span class="sm:hidden px-2.5 py-0.5 rounded-full bg-indigo-400/20 text-indigo-300 text-[10px] font-bold">
                            {{ activeKanjiList.length }} Kanji
                        </span>
                    </div>
                    <p class="hidden sm:block text-xs sm:text-sm text-slate-300 mt-1 max-w-xl leading-relaxed">
                        {{ isJapanese 
                            ? '3Dカード反転、スピード4択クイズ、漢字辞書リストで楽しく効率的に漢字をマスターします。' 
                            : 'Pusat latihan hafalan kanji interaktif: Kartu 3D Flip yang bisa dibalik, simulasi Kuis Kilat pilihan ganda, dan kamus huruf lengkap.' 
                        }}
                    </p>
                </div>

                <!-- 3 Mode Toggle Buttons (Bersih, Jelas, Tanpa Duplikat) -->
                <div class="relative z-1 flex items-center gap-1 bg-white/10 backdrop-blur-md p-1.5 rounded-2xl border border-white/20 w-full sm:w-auto justify-between sm:justify-start">
                    <button 
                        type="button"
                        @click="activeMode = 'flashcard'"
                        class="flex-1 sm:flex-initial px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer text-center flex items-center justify-center gap-1.5"
                        :class="activeMode === 'flashcard' ? 'bg-white text-slate-950 shadow-md font-black' : 'text-slate-300 hover:text-white'"
                    >
                        <span>🃏</span>
                        <span>Flashcard</span>
                    </button>
                    <button 
                        type="button"
                        @click="startQuizMode"
                        class="flex-1 sm:flex-initial px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer text-center flex items-center justify-center gap-1.5"
                        :class="activeMode === 'quiz' ? 'bg-indigo-600 text-white shadow-md font-black' : 'text-slate-300 hover:text-white'"
                    >
                        <span>⚡</span>
                        <span>Kuis Kilat</span>
                    </button>
                    <button 
                        type="button"
                        @click="activeMode = 'list'"
                        class="flex-1 sm:flex-initial px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer text-center flex items-center justify-center gap-1.5"
                        :class="activeMode === 'list' ? 'bg-blue-600 text-white shadow-md font-black' : 'text-slate-300 hover:text-white'"
                    >
                        <span>📖</span>
                        <span>Kamus</span>
                    </button>
                </div>
            </div>

            <!-- ================= FILTER & SEARCH BAR ================= -->
            <div class="bg-white p-3.5 sm:p-4 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <!-- Level Select -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 whitespace-nowrap">Filter Level:</span>
                    <select 
                        :value="selectedLevel" 
                        @change="changeLevel($event.target.value)"
                        class="text-xs font-bold rounded-xl border-slate-300 focus:border-indigo-600 focus:ring-indigo-600 py-1.5 px-3 bg-slate-50 font-mono"
                    >
                        <option v-for="(lbl, key) in levels" :key="key" :value="key">
                            {{ lbl }}
                        </option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative flex-1 sm:max-w-xs">
                    <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input 
                        type="text" 
                        v-model="searchQuery" 
                        :placeholder="isJapanese ? '漢字・読み・意味で絞り込み...' : 'Cari huruf kanji, hiragana, atau arti...'" 
                        class="w-full pl-8 pr-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all placeholder:text-slate-400 font-jp"
                    />
                </div>

                <!-- Counter Badge -->
                <div class="text-xs font-bold text-slate-600 hidden sm:block">
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-mono">
                        {{ activeKanjiList.length }} Kanji Tersedia
                    </span>
                </div>
            </div>

            <!-- ================= MODE 1: FLASHCARD 3D FLIP MODE ================= -->
            <div v-if="activeMode === 'flashcard'" class="max-w-2xl mx-auto space-y-6">
                <!-- Card Container with 3D Flip -->
                <div v-if="activeKanjiList.length > 0" class="space-y-4">
                    <!-- Progress Bar & Indicator -->
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 px-1">
                        <span>Kartu {{ currentIndex + 1 }} dari {{ activeKanjiList.length }}</span>
                        <span class="font-mono text-indigo-600">
                            {{ Math.round(((currentIndex + 1) / activeKanjiList.length) * 100) }}% Selesai
                        </span>
                    </div>

                    <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                        <div 
                            class="h-full bg-indigo-600 rounded-full transition-all duration-300"
                            :style="{ width: `${((currentIndex + 1) / activeKanjiList.length) * 100}%` }"
                        ></div>
                    </div>

                    <!-- 3D Flip Card -->
                    <div class="perspective-1000">
                        <div 
                            @click="isFlipped = !isFlipped" 
                            class="relative w-full min-h-[300px] sm:min-h-[340px] rounded-3xl cursor-pointer transition-transform duration-500 transform-style-3d shadow-xl hover:shadow-2xl border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-slate-100 flex items-center justify-center p-6 sm:p-8 text-center select-none"
                            :class="{ 'rotate-y-180 bg-gradient-to-br from-indigo-50/90 to-purple-50/90 border-indigo-200': isFlipped }"
                        >
                            <!-- SISI DEPAN: Karakter Kanji & Cara Baca -->
                            <div v-show="!isFlipped" class="flex flex-col items-center justify-center space-y-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black px-3 py-1 rounded-full border font-mono" :class="getLevelBadgeClass(currentKanji?.level)">
                                        {{ currentKanji?.level || 'N5' }}
                                    </span>
                                    <span v-if="currentKanji?.stroke_count" class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full font-jp">
                                        {{ currentKanji?.stroke_count }} 画
                                    </span>
                                </div>

                                <!-- Karakter Kanji Besar -->
                                <h2 class="text-6xl sm:text-7xl md:text-8xl font-black text-slate-950 font-jp tracking-wide select-all">
                                    {{ currentKanji?.kanji }}
                                </h2>

                                <!-- Hiragana -->
                                <p class="text-base sm:text-lg font-bold text-indigo-600 font-jp">
                                    {{ currentKanji?.hiragana }}
                                    <span v-if="currentKanji?.romaji" class="text-slate-400 font-mono text-xs ml-1 font-normal">
                                        ({{ currentKanji?.romaji }})
                                    </span>
                                </p>

                                <div class="flex items-center gap-1.5 text-xs text-slate-400 font-bold pt-2">
                                    <span>👆 Ketuk kartu untuk membalik arti</span>
                                </div>
                            </div>

                            <!-- SISI BELAKANG: Arti Indonesia & Contoh -->
                            <div v-show="isFlipped" class="flex flex-col items-center justify-center space-y-3.5 transform rotate-y-180 w-full max-w-md">
                                <span class="text-[11px] font-black text-indigo-900 uppercase tracking-wider bg-indigo-100 px-3 py-1 rounded-full">
                                    Arti Bahasa Indonesia
                                </span>

                                <h2 class="text-2xl sm:text-3xl font-black text-slate-950 uppercase leading-snug">
                                    {{ currentKanji?.meaning_id }}
                                </h2>

                                <!-- Onyomi / Kunyomi Details jika ada -->
                                <div v-if="currentKanji?.onyomi || currentKanji?.kunyomi" class="flex flex-wrap items-center justify-center gap-2 text-xs font-jp">
                                    <span v-if="currentKanji?.onyomi" class="px-2.5 py-1 rounded-lg bg-white/80 border border-indigo-100 text-slate-700 font-bold">
                                        音: {{ currentKanji.onyomi }}
                                    </span>
                                    <span v-if="currentKanji?.kunyomi" class="px-2.5 py-1 rounded-lg bg-white/80 border border-indigo-100 text-slate-700 font-bold">
                                        訓: {{ currentKanji.kunyomi }}
                                    </span>
                                </div>

                                <!-- Catatan / Contoh Kata Jukugo -->
                                <div v-if="currentKanji?.notes" class="bg-white/90 backdrop-blur-xs p-3.5 rounded-2xl border border-indigo-100 text-xs text-slate-800 font-jp text-left shadow-xs w-full">
                                    <span class="text-indigo-600 font-black block mb-0.5">用例・備考 (Contoh):</span>
                                    <p class="font-medium leading-relaxed">{{ currentKanji.notes }}</p>
                                </div>

                                <div class="text-[11px] text-slate-400 font-bold pt-1">
                                    <span>👆 Ketuk untuk kembali ke huruf kanji</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ergonomic Flashcard Navigation (Tanpa Duplikat) -->
                    <div class="flex items-center justify-between gap-2 pt-2">
                        <button 
                            type="button"
                            @click="prevCard" 
                            class="px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 shadow-xs flex items-center gap-1.5 cursor-pointer"
                            title="Kartu Sebelumnya (Panah Kiri)"
                        >
                            <ChevronLeft class="w-4 h-4" />
                            <span class="hidden sm:inline">Sebelumnya</span>
                        </button>

                        <div class="flex items-center gap-2">
                            <button 
                                type="button"
                                @click="shuffleCards"
                                class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer"
                                title="Acak Urutan Kartu (Shuffle)"
                            >
                                <Shuffle class="w-4 h-4" />
                            </button>

                            <button 
                                type="button"
                                @click="markMastered(false)" 
                                class="px-3 sm:px-4 py-2.5 rounded-2xl bg-rose-50 text-rose-700 font-bold text-xs hover:bg-rose-100 border border-rose-200 transition-all cursor-pointer flex items-center gap-1"
                            >
                                <span>❌</span>
                                <span class="hidden xs:inline">Belum Hafal</span>
                            </button>
                            <button 
                                type="button"
                                @click="markMastered(true)" 
                                class="px-3 sm:px-4 py-2.5 rounded-2xl bg-emerald-50 text-emerald-700 font-bold text-xs hover:bg-emerald-100 border border-emerald-200 transition-all cursor-pointer flex items-center gap-1"
                            >
                                <span>✅</span>
                                <span class="hidden xs:inline">Sudah Hafal</span>
                            </button>
                        </div>

                        <button 
                            type="button"
                            @click="nextCard" 
                            class="px-4 py-2.5 rounded-2xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 shadow-md flex items-center gap-1.5 cursor-pointer"
                            title="Kartu Berikutnya (Panah Kanan / Spasi)"
                        >
                            <span class="hidden sm:inline">Berikutnya</span>
                            <ChevronRight class="w-4 h-4" />
                        </button>
                    </div>

                    <!-- Keyboard Shortcut Hint -->
                    <div class="text-center text-[11px] text-slate-400 font-medium">
                        Keyboard: <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-600 font-mono">Spasi</kbd> membalik kartu • <kbd class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-600 font-mono">← / →</kbd> ganti kartu
                    </div>
                </div>

                <div v-else class="py-16 text-center text-slate-500 bg-white rounded-3xl border border-dashed border-slate-200">
                    <Languages class="w-10 h-10 text-slate-300 mx-auto mb-2" />
                    <p class="font-bold text-sm text-slate-700">Tidak ada kartu kanji yang cocok dengan filter.</p>
                    <p class="text-xs text-slate-400 mt-1">Ubah filter level atau kata kunci pencarian Anda.</p>
                </div>
            </div>

            <!-- ================= MODE 2: KANJI SPEED QUIZ (SERU & MENANTANG) ================= -->
            <div v-else-if="activeMode === 'quiz'" class="max-w-xl mx-auto space-y-6">
                <!-- Quiz Playing State -->
                <div v-if="!quizFinished && currentQuizQuestion" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-lg space-y-6">
                    <!-- Quiz Header & Streak -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <span class="text-xs font-black text-slate-400 font-mono">
                            Soal {{ quizIndex + 1 }} dari {{ quizQuestions.length }}
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 font-black text-xs border border-amber-200">
                                🔥 Streak: {{ quizStreak }}
                            </span>
                            <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 font-black text-xs border border-indigo-200">
                                Skor: {{ quizScore }}
                            </span>
                        </div>
                    </div>

                    <!-- Question Prompt & Huge Kanji -->
                    <div class="text-center py-4 space-y-2">
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

            <!-- ================= MODE 3: KAMUS / DAFTAR KANJI (GRID) ================= -->
            <div v-else-if="activeMode === 'list'" class="space-y-6">
                <div v-if="activeKanjiList.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 sm:gap-4">
                    <div 
                        v-for="k in activeKanjiList" 
                        :key="k.id"
                        class="bg-white rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all p-4 flex flex-col justify-between space-y-2.5 group relative overflow-hidden"
                    >
                        <!-- Top Header: Level & Stroke -->
                        <div class="flex items-center justify-between gap-1 text-[10px]">
                            <span class="px-2 py-0.5 rounded-full font-black border font-mono" :class="getLevelBadgeClass(k.level)">
                                {{ k.level }}
                            </span>
                            <span v-if="k.stroke_count" class="font-mono text-slate-400 font-bold font-jp">
                                {{ k.stroke_count }}画
                            </span>
                        </div>

                        <!-- Card Center -->
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

                        <!-- Notes jika ada -->
                        <div v-if="k.notes" class="bg-slate-50 p-2 rounded-xl text-[10px] text-slate-600 font-jp line-clamp-2">
                            {{ k.notes }}
                        </div>
                    </div>
                </div>

                <div v-else class="py-16 text-center text-slate-500 bg-white rounded-3xl border border-dashed border-slate-200">
                    <Languages class="w-10 h-10 text-slate-300 mx-auto mb-2" />
                    <p class="font-bold text-sm text-slate-700">Tidak ada huruf kanji yang cocok.</p>
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
import { Languages, Search, ChevronLeft, ChevronRight, Shuffle } from 'lucide-vue-next';

const props = defineProps({
    levels: {
        type: Object,
        default: () => ({}),
    },
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

// Active Mode: 'flashcard' | 'quiz' | 'list'
const activeMode = ref('flashcard');
const searchQuery = ref('');

// Filter Level
const changeLevel = (lvl) => {
    router.get(route('siswa.kanjis.index'), {
        level: lvl,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Filtered kanji list (reactive client-side search over active database pool)
const activeKanjiList = computed(() => {
    let list = [...props.kanjis];
    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase().trim();
        list = list.filter(k => 
            (k.kanji && k.kanji.toLowerCase().includes(q)) ||
            (k.hiragana && k.hiragana.toLowerCase().includes(q)) ||
            (k.meaning_id && k.meaning_id.toLowerCase().includes(q)) ||
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
        currentIndex.value = 0; // Loop kembali ke awal
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

const markMastered = (mastered) => {
    // Memberikan feedback dan lanjut ke kartu berikutnya
    nextCard();
};

// ================= SPEED QUIZ STATE & LOGIC =================
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
    if (props.kanjis.length < 4) {
        activeMode.value = 'flashcard';
        return;
    }
    activeMode.value = 'quiz';
    quizFinished.value = false;
    quizScore.value = 0;
    quizStreak.value = 0;
    quizIndex.value = 0;
    quizAnswerSelected.value = null;

    // Ambil maksimal 10 kanji acak dari pool DB
    const shuffledPool = [...props.kanjis].sort(() => 0.5 - Math.random());
    const selectedPool = shuffledPool.slice(0, Math.min(10, shuffledPool.length));

    // Bangun 4 opsi pilihan ganda untuk setiap pertanyaan
    quizQuestions.value = selectedPool.map(target => {
        const distractors = props.kanjis
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

    // Auto lanjut ke pertanyaan berikutnya setelah jeda 900ms
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

// Keyboard Handler for Flashcards
const handleKeyDown = (e) => {
    if (activeMode.value !== 'flashcard') return;

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
