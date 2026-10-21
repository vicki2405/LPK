<template>
    <Head :title="isJapanese ? '漢字の編集 (Edit Kanji) - 正夢' : 'Edit Huruf Kanji - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-16 max-w-4xl mx-auto">
            <!-- ================= TOP NAVIGATION ================= -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-400 font-jp">
                        <Link :href="route('sensei.kanjis.index')" class="hover:text-indigo-600 transition-colors flex items-center gap-1">
                            <ArrowLeft class="w-3.5 h-3.5" />
                            <span>{{ isJapanese ? '漢字一覧に戻る' : 'Kembali ke Bank Kanji' }}</span>
                        </Link>
                        <span>/</span>
                        <span class="text-slate-700">{{ isJapanese ? '編集' : 'Edit Kanji' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-jp text-base font-bold shadow-xs">
                            {{ kanji.kanji || '漢' }}
                        </span>
                        <span>{{ isJapanese ? '漢字情報の編集' : 'Edit Huruf Kanji' }}</span>
                    </h1>
                </div>

                <div class="flex items-center gap-2.5">
                    <Link 
                        :href="route('sensei.kanjis.index')"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer inline-flex items-center gap-1.5"
                    >
                        <span>{{ isJapanese ? 'キャンセル' : 'Batal' }}</span>
                    </Link>
                    <button 
                        type="submit" 
                        form="kanjiEditForm"
                        :disabled="form.processing"
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 active:scale-[0.98] transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <Save class="w-4 h-4" />
                        <span>{{ form.processing ? 'Menyimpan...' : (isJapanese ? '変更を保存' : 'Perbarui Kanji') }}</span>
                    </button>
                </div>
            </div>

            <!-- ================= FORM CONTAINER ================= -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8 space-y-6">
                <!-- Live Preview Card -->
                <div class="p-4 rounded-2xl bg-gradient-to-r from-indigo-50/60 via-slate-50 to-indigo-50/60 border border-indigo-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-white border border-indigo-200/80 shadow-2xs flex items-center justify-center font-jp text-3xl font-black text-slate-900 shrink-0">
                            {{ form.kanji || '漢' }}
                        </div>
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-indigo-700 font-jp">
                                    {{ form.hiragana || 'Cara baca hiragana' }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black border font-mono bg-indigo-50 text-indigo-700 border-indigo-200">
                                    {{ form.level || 'N5' }}
                                </span>
                            </div>
                            <div class="text-xs font-black text-slate-800">
                                {{ form.meaning_id || 'Arti dalam bahasa Indonesia' }}
                            </div>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider bg-white px-2.5 py-1 rounded-lg border border-indigo-200 font-jp shadow-2xs">
                        Preview Kartu
                    </span>
                </div>

                <form id="kanjiEditForm" @submit.prevent="submitForm" class="space-y-5">
                    <!-- Row 1: Karakter Kanji & Level -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1.5 uppercase tracking-wider font-jp">
                                {{ isJapanese ? '漢字 *' : 'Huruf Kanji *' }}
                            </label>
                            <input 
                                type="text" 
                                v-model="form.kanji"
                                placeholder="Contoh: 日"
                                required
                                class="w-full px-4 py-3 rounded-2xl border text-base font-black font-jp text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-all"
                                :class="form.errors.kanji ? 'border-rose-400 bg-rose-50/40' : 'border-slate-300 bg-slate-50/40 focus:bg-white'"
                            />
                            <p v-if="form.errors.kanji" class="text-xs text-rose-600 font-bold mt-1.5">{{ form.errors.kanji }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1.5 uppercase tracking-wider font-jp">
                                {{ isJapanese ? 'レベル *' : 'Level Bahasa *' }}
                            </label>
                            <select 
                                v-model="form.level"
                                required
                                class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-xs font-bold text-slate-900 bg-slate-50/40 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-mono transition-all"
                            >
                                <option 
                                    v-for="lvl in activeLanguageLevels" 
                                    :key="lvl.code" 
                                    :value="lvl.code"
                                >
                                    {{ lvl.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.level" class="text-xs text-rose-600 font-bold mt-1.5">{{ form.errors.level }}</p>
                        </div>
                    </div>

                    <!-- Wadah Topik Kanji -->
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1.5 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '漢字トピック *' : 'Wadah Topik Kanji' }}
                        </label>
                        <select 
                            v-if="availableTopics.length > 0"
                            v-model="selectedTopicId"
                            @change="handleTopicSelect"
                            class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-xs font-bold text-slate-900 bg-slate-50/40 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all cursor-pointer"
                        >
                            <option value="">-- Tanpa Topik Khusus (Bank Kanji Umum) --</option>
                            <option v-for="top in availableTopics" :key="top.id" :value="top.id">
                                [{{ top.level }}] {{ top.title }}
                            </option>
                        </select>
                        <div v-else class="text-xs text-slate-400 py-1 font-medium">
                            Belum ada topik khusus untuk level ini. Kanji akan dimasukkan ke bank umum level {{ form.level }}.
                        </div>
                    </div>

                    <!-- Row 2: Cara Baca Hiragana & Romaji -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1.5 uppercase tracking-wider font-jp">
                                {{ isJapanese ? 'ひらがなの読み方 *' : 'Cara Baca Hiragana *' }}
                            </label>
                            <input 
                                type="text" 
                                v-model="form.hiragana"
                                placeholder="Contoh: ひ / にち"
                                required
                                class="w-full px-4 py-3 rounded-2xl border text-xs font-bold font-jp text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-all"
                                :class="form.errors.hiragana ? 'border-rose-400 bg-rose-50/40' : 'border-slate-300 bg-slate-50/40 focus:bg-white'"
                            />
                            <p v-if="form.errors.hiragana" class="text-xs text-rose-600 font-bold mt-1.5">{{ form.errors.hiragana }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1.5 uppercase tracking-wider font-jp">
                                Romaji (Opsional)
                            </label>
                            <input 
                                type="text" 
                                v-model="form.romaji"
                                placeholder="Contoh: hi / nichi"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-xs font-mono text-slate-900 bg-slate-50/40 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Row 3: Onyomi & Kunyomi (Opsional) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1.5 uppercase tracking-wider font-jp flex items-center justify-between">
                                <span>{{ isJapanese ? '音読み (任意)' : 'Onyomi (音読み)' }}</span>
                                <span class="text-[10px] text-slate-400 font-normal font-sans">Opsional</span>
                            </label>
                            <input 
                                type="text" 
                                v-model="form.onyomi"
                                placeholder="Contoh: ニチ, ジツ"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-xs font-bold font-jp text-slate-900 bg-slate-50/40 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1.5 uppercase tracking-wider font-jp flex items-center justify-between">
                                <span>{{ isJapanese ? '訓読み (任意)' : 'Kunyomi (訓読み)' }}</span>
                                <span class="text-[10px] text-slate-400 font-normal font-sans">Opsional</span>
                            </label>
                            <input 
                                type="text" 
                                v-model="form.kunyomi"
                                placeholder="Contoh: ひ, -び, -か"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-xs font-bold font-jp text-slate-900 bg-slate-50/40 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Row 4: Arti Bahasa Indonesia -->
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1.5 uppercase tracking-wider font-jp">
                            {{ isJapanese ? 'インドネシア語の意味 *' : 'Arti Bahasa Indonesia *' }}
                        </label>
                        <input 
                            type="text" 
                            v-model="form.meaning_id"
                            placeholder="Contoh: Matahari / Hari"
                            required
                            class="w-full px-4 py-3 rounded-2xl border text-xs font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-all"
                            :class="form.errors.meaning_id ? 'border-rose-400 bg-rose-50/40' : 'border-slate-300 bg-slate-50/40 focus:bg-white'"
                        />
                        <p v-if="form.errors.meaning_id" class="text-xs text-rose-600 font-bold mt-1.5">{{ form.errors.meaning_id }}</p>
                    </div>

                    <!-- Row 4: Jumlah Goresan & Catatan -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-700 mb-1.5 uppercase tracking-wider font-jp">
                                Jumlah Coretan (画数)
                            </label>
                            <input 
                                type="number" 
                                v-model.number="form.stroke_count"
                                min="1"
                                max="60"
                                placeholder="Contoh: 4"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-xs font-mono font-bold text-slate-900 bg-slate-50/40 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-black text-slate-700 mb-1.5 uppercase tracking-wider font-jp">
                                {{ isJapanese ? '備考・熟語例 (任意)' : 'Catatan / Contoh Kata (Opsional)' }}
                            </label>
                            <input 
                                type="text" 
                                v-model="form.notes"
                                placeholder="Contoh: 日本 (Nihon / Jepang), 日曜日 (Minggu)"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-xs text-slate-900 bg-slate-50/40 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-jp transition-all"
                            />
                        </div>
                    </div>

                    <!-- Bottom Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <Link 
                            :href="route('sensei.kanjis.index')"
                            class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors"
                        >
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </Link>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 active:scale-[0.98] transition-all cursor-pointer disabled:opacity-50 flex items-center gap-2"
                        >
                            <Save class="w-4 h-4" />
                            <span>{{ form.processing ? 'Menyimpan...' : (isJapanese ? '変更を保存' : 'Perbarui Huruf Kanji') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { ArrowLeft, Save } from 'lucide-vue-next';

const props = defineProps({
    kanji: {
        type: Object,
        required: true,
    },
    languageLevels: {
        type: Array,
        default: () => [],
    },
    topics: {
        type: Array,
        default: () => [],
    },
    selectedTopicIds: {
        type: Array,
        default: () => [],
    },
});

const { isJapanese } = useLang();

const activeLanguageLevels = computed(() => {
    if (props.languageLevels && props.languageLevels.length > 0) {
        return props.languageLevels;
    }
    return [
        { code: 'N5', name: 'JLPT N5 (Tingkat Dasar)' },
        { code: 'N4', name: 'JLPT N4 & JFT-Basic A2 (Standar Kerja)' },
        { code: 'N3', name: 'JLPT N3 (Tingkat Menengah)' },
    ];
});

const initialTopicId = (props.selectedTopicIds && props.selectedTopicIds.length > 0) ? props.selectedTopicIds[0] : '';
const selectedTopicId = ref(initialTopicId);

const availableTopics = computed(() => {
    if (!props.topics) return [];
    return props.topics.filter(t => t.level === form.level);
});

const handleTopicSelect = () => {
    if (selectedTopicId.value) {
        form.topic_ids = [Number(selectedTopicId.value)];
    } else {
        form.topic_ids = [];
    }
};

const form = useForm({
    kanji: props.kanji.kanji || '',
    hiragana: props.kanji.hiragana || '',
    romaji: props.kanji.romaji || '',
    onyomi: props.kanji.onyomi || '',
    kunyomi: props.kanji.kunyomi || '',
    meaning_id: props.kanji.meaning_id || '',
    level: props.kanji.level || 'N5',
    topic_ids: props.selectedTopicIds || [],
    stroke_count: props.kanji.stroke_count || null,
    notes: props.kanji.notes || '',
});

watch(() => form.level, () => {
    const valid = availableTopics.value;
    if (valid.length > 0) {
        const exists = valid.find(t => t.id === Number(selectedTopicId.value));
        if (!exists) {
            selectedTopicId.value = valid[0].id;
            handleTopicSelect();
        }
    } else {
        selectedTopicId.value = '';
        form.topic_ids = [];
    }
});

const submitForm = () => {
    form.put(route('sensei.kanjis.update', props.kanji.id));
};
</script>
