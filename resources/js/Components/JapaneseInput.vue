<template>
    <div class="space-y-2 w-full">
        <!-- Header Bar: Label & Smart Japanese Inserter Button -->
        <div class="flex flex-wrap items-center justify-between gap-2">
            <label v-if="label" class="block text-xs font-bold text-slate-700">
                {{ label }} <span v-if="required" class="text-rose-500">*</span>
            </label>

            <!-- SMART BUTTON: SISIPKAN HURUF JEPANG -->
            <div class="flex items-center gap-1.5 select-none">
                <button
                    type="button"
                    @click="openSmartInserterModal"
                    class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-rose-600 to-indigo-600 hover:from-rose-700 hover:to-indigo-700 text-white text-xs font-bold shadow-sm hover:shadow transition-all flex items-center gap-1.5 cursor-pointer"
                >
                    <span>🇯🇵</span>
                    <span>+ Sisipkan Huruf Jepang (Hiragana / Katakana / Kanji)</span>
                </button>

                <!-- Quick Symbols Insert -->
                <div class="hidden sm:flex items-center gap-1 bg-slate-100 border border-slate-200 rounded-xl p-1">
                    <button
                        type="button"
                        v-for="sym in ['「」', '、', '。', 'ー', '〜']"
                        :key="sym"
                        @click="insertSymbol(sym)"
                        class="px-2 py-0.5 rounded-lg text-xs font-jp font-bold text-slate-700 hover:bg-white hover:text-black transition-colors cursor-pointer shadow-2xs"
                        :title="`Sisipkan simbol ${sym}`"
                    >
                        {{ sym }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Textarea / Input Box (Ketik Bahasa Indonesia Bebas & Normal) -->
        <div class="relative">
            <textarea
                v-if="isTextarea"
                ref="inputRef"
                :rows="rows || 4"
                :value="modelValue"
                @input="handleTextInput"
                :placeholder="placeholder || 'Ketik di sini... Klik tombol [+ Sisipkan Huruf Jepang] jika ingin memasukkan kalimat Jepang di tengah-tengah teks.'"
                :required="required"
                class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none transition-all font-jp leading-relaxed resize-y min-h-[130px]"
                :class="inputClass"
            ></textarea>

            <input
                v-else
                ref="inputRef"
                type="text"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :placeholder="placeholder || 'Ketik di sini...'"
                :required="required"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none transition-all font-jp"
                :class="inputClass"
            />
        </div>

        <!-- ================= MODAL PINTAR SISIPKAN 3 HURUF JEPANG ================= -->
        <div v-if="showModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs animate-fade-in">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-7 space-y-5">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-base">
                            🇯🇵
                        </div>
                        <div>
                            <h4 class="text-sm sm:text-base font-bold text-slate-900">
                                Sisipkan Huruf / Kalimat Jepang
                            </h4>
                            <p class="text-[11px] text-slate-500">
                                Otomatis masuk tepat di posisi kursor kalimat materi Anda
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        ✕
                    </button>
                </div>

                <!-- Tab Pemilihan 3 Huruf Jepang (Hiragana, Katakana, Kanji) -->
                <div class="grid grid-cols-3 gap-2 bg-slate-100 p-1 rounded-2xl">
                    <button
                        type="button"
                        @click="activeScript = 'hiragana'"
                        class="py-2 px-3 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                        :class="activeScript === 'hiragana' 
                            ? 'bg-white text-rose-600 shadow-xs' 
                            : 'text-slate-600 hover:text-slate-900'"
                    >
                        <span>🎌</span>
                        <span>1. Hiragana</span>
                    </button>

                    <button
                        type="button"
                        @click="activeScript = 'katakana'"
                        class="py-2 px-3 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                        :class="activeScript === 'katakana' 
                            ? 'bg-white text-amber-600 shadow-xs' 
                            : 'text-slate-600 hover:text-slate-900'"
                    >
                        <span>🈁</span>
                        <span>2. Katakana</span>
                    </button>

                    <button
                        type="button"
                        @click="activeScript = 'kanji'"
                        class="py-2 px-3 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                        :class="activeScript === 'kanji' 
                            ? 'bg-white text-indigo-600 shadow-xs' 
                            : 'text-slate-600 hover:text-slate-900'"
                    >
                        <span>🎴</span>
                        <span>3. Kanji (Furigana)</span>
                    </button>
                </div>

                <!-- ================= 1. FORM HIRAGANA ================= -->
                <div v-if="activeScript === 'hiragana'" class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Ketik Romaji (Keyboard Biasa / HP):
                        </label>
                        <input
                            type="text"
                            v-model="inputHiraganaRomaji"
                            @input="handleHiraganaInput"
                            placeholder="Contoh: arigatou gozaimasu / watashi wa gakusei desu"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-sans focus:ring-2 focus:ring-rose-500 focus:outline-none"
                            autofocus
                        />
                    </div>

                    <!-- Hasil Live Hiragana -->
                    <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-100">
                        <span class="text-[11px] font-bold text-rose-700 block mb-1">Hasil Huruf Hiragana:</span>
                        <div class="text-base sm:text-lg font-bold font-jp text-slate-900 min-h-[28px] select-all">
                            {{ previewHiragana || '...' }}
                        </div>
                    </div>
                </div>

                <!-- ================= 2. FORM KATAKANA ================= -->
                <div v-if="activeScript === 'katakana'" class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Ketik Romaji (Keyboard Biasa / HP):
                        </label>
                        <input
                            type="text"
                            v-model="inputKatakanaRomaji"
                            @input="handleKatakanaInput"
                            placeholder="Contoh: terebi / kouhii / indonesia"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-sans focus:ring-2 focus:ring-amber-500 focus:outline-none"
                            autofocus
                        />
                    </div>

                    <!-- Hasil Live Katakana -->
                    <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100">
                        <span class="text-[11px] font-bold text-amber-700 block mb-1">Hasil Huruf Katakana:</span>
                        <div class="text-base sm:text-lg font-bold font-jp text-slate-900 min-h-[28px] select-all">
                            {{ previewKatakana || '...' }}
                        </div>
                    </div>
                </div>

                <!-- ================= 3. FORM KANJI (FURIGANA) ================= -->
                <div v-if="activeScript === 'kanji'" class="space-y-3.5">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Huruf Kanji *
                            </label>
                            <input
                                type="text"
                                v-model="kanjiText"
                                placeholder="Contoh: 先生 atau 日本語"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-jp focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Cara Baca (Ketik Romaji) *
                            </label>
                            <input
                                type="text"
                                v-model="kanjiFuriganaRomaji"
                                @input="handleFuriganaReadingInput"
                                placeholder="sensei -> せんせい"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-jp focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- Preview Furigana Ruby -->
                    <div class="p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-100 text-center">
                        <span class="text-[10px] font-bold text-indigo-600 block mb-1">Hasil Tampilan di Layar Siswa:</span>
                        <div class="text-lg sm:text-xl font-bold font-jp text-slate-900 py-1" v-html="previewKanjiRubyHtml"></div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 cursor-pointer"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        @click="confirmInsert"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <span>📥</span>
                        <span>{{ insertButtonText || 'Sisipkan ke Teks' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick, watch, onMounted } from 'vue';
import { romajiToHiragana, hiraganaToKatakana, createFuriganaRuby } from '@/Utils/japaneseConverter';

const props = defineProps({
    modelValue: [String, Number],
    label: String,
    placeholder: String,
    required: Boolean,
    isTextarea: Boolean,
    rows: Number,
    showFurigana: {
        type: Boolean,
        default: true,
    },
    inputClass: String,
    insertButtonText: String,
});

const emit = defineEmits(['update:modelValue']);

const inputRef = ref(null);
const showModal = ref(false);
const activeScript = ref('hiragana'); // 'hiragana' | 'katakana' | 'kanji'

const autoResize = (el) => {
    if (!el || !props.isTextarea) return;
    el.style.height = 'auto';
    el.style.height = `${Math.max(el.scrollHeight, 130)}px`;
};

const handleTextInput = (e) => {
    emit('update:modelValue', e.target.value);
    autoResize(e.target);
};

watch(() => props.modelValue, () => {
    nextTick(() => {
        if (inputRef.value && props.isTextarea) {
            autoResize(inputRef.value);
        }
    });
});

onMounted(() => {
    nextTick(() => {
        if (inputRef.value && props.isTextarea) {
            autoResize(inputRef.value);
        }
    });
});

// Cursor tracking
let savedCursorStart = 0;
let savedCursorEnd = 0;

// Tab 1: Hiragana State
const inputHiraganaRomaji = ref('');
const previewHiragana = ref('');

const handleHiraganaInput = (e) => {
    previewHiragana.value = romajiToHiragana(e.target.value);
};

// Tab 2: Katakana State
const inputKatakanaRomaji = ref('');
const previewKatakana = ref('');

const handleKatakanaInput = (e) => {
    previewKatakana.value = hiraganaToKatakana(romajiToHiragana(e.target.value));
};

// Tab 3: Kanji + Furigana State
const kanjiText = ref('');
const kanjiFuriganaRomaji = ref('');
const kanjiFuriganaReading = ref('');

const handleFuriganaReadingInput = (e) => {
    kanjiFuriganaReading.value = romajiToHiragana(e.target.value);
};

const previewKanjiRubyHtml = computed(() => {
    if (!kanjiText.value && !kanjiFuriganaReading.value) {
        return '<span class="text-slate-400 font-normal text-xs">Belum ada input</span>';
    }
    return createFuriganaRuby(kanjiText.value || '日本語', kanjiFuriganaReading.value || 'にほんご');
});

// Open Modal & Save Cursor Position
const openSmartInserterModal = () => {
    const el = inputRef.value;
    if (el) {
        savedCursorStart = el.selectionStart || (props.modelValue ? props.modelValue.length : 0);
        savedCursorEnd = el.selectionEnd || (props.modelValue ? props.modelValue.length : 0);
    } else {
        savedCursorStart = props.modelValue ? props.modelValue.length : 0;
        savedCursorEnd = savedCursorStart;
    }

    // Reset inputs
    inputHiraganaRomaji.value = '';
    previewHiragana.value = '';
    inputKatakanaRomaji.value = '';
    previewKatakana.value = '';
    kanjiText.value = '';
    kanjiFuriganaRomaji.value = '';
    kanjiFuriganaReading.value = '';

    showModal.value = true;
};

// Confirm & Insert Text into Cursor Position
const confirmInsert = () => {
    let textToInsert = '';

    if (activeScript.value === 'hiragana') {
        textToInsert = previewHiragana.value || romajiToHiragana(inputHiraganaRomaji.value);
    } else if (activeScript.value === 'katakana') {
        textToInsert = previewKatakana.value || hiraganaToKatakana(romajiToHiragana(inputKatakanaRomaji.value));
    } else if (activeScript.value === 'kanji') {
        if (kanjiFuriganaReading.value) {
            textToInsert = createFuriganaRuby(kanjiText.value, kanjiFuriganaReading.value);
        } else {
            textToInsert = kanjiText.value;
        }
    }

    if (!textToInsert) {
        showModal.value = false;
        return;
    }

    const currentFullText = props.modelValue || '';
    const updatedText = currentFullText.substring(0, savedCursorStart) + textToInsert + currentFullText.substring(savedCursorEnd);

    emit('update:modelValue', updatedText);
    showModal.value = false;

    nextTick(() => {
        const el = inputRef.value;
        if (el) {
            el.focus();
            const newPos = savedCursorStart + textToInsert.length;
            el.setSelectionRange(newPos, newPos);
        }
    });
};

// Quick Symbol Insertion at Cursor Position
const insertSymbol = (sym) => {
    const el = inputRef.value;
    const currentFullText = props.modelValue || '';
    const start = el ? (el.selectionStart || currentFullText.length) : currentFullText.length;
    const end = el ? (el.selectionEnd || currentFullText.length) : currentFullText.length;

    let insertText = sym;
    let newCursorPos = start + sym.length;

    if (sym === '「」') {
        insertText = '「」';
        newCursorPos = start + 1; // Cursor in the middle
    }

    const updatedText = currentFullText.substring(0, start) + insertText + currentFullText.substring(end);
    emit('update:modelValue', updatedText);

    nextTick(() => {
        if (el) {
            el.focus();
            el.setSelectionRange(newCursorPos, newCursorPos);
        }
    });
};
</script>
