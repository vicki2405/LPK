<template>
    <Head :title="isJapanese ? `問題 #${question.id} の編集 - 問題バンク` : `Edit Soal #${question.id} - Bank Soal Masayume`" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-12">
            <!-- Top Navigation & Header -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <Link :href="route('sensei.questions.index')" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 mb-2.5 transition-colors cursor-pointer">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ isJapanese ? '← 問題バンク一覧へ戻る' : '← Kembali ke Daftar Bank Soal' }}</span>
                    </Link>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <HelpCircle class="w-6 h-6 text-blue-600" />
                        <span>{{ isJapanese ? `問題 #${question.id} の編集` : `Edit Butir Soal #${question.id}` }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ isJapanese ? '問題文、音声・画像メディア、または正解の選択肢を更新します。' : 'Perbarui teks pertanyaan, media audio/gambar, atau pilihan kunci jawaban.' }}
                    </p>
                </div>
            </div>

            <!-- Full-Page Question Editor Form -->
            <form @submit.prevent="submitForm" class="space-y-6" enctype="multipart/form-data">
                <!-- 1. Metadata Soal -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>{{ isJapanese ? '1. 問題属性・分野設定' : '1. Informasi & Kategori Soal' }}</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                {{ isJapanese ? '習熟レベル *' : 'Level Kompetensi *' }}
                            </label>
                            <select v-model="form.level" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                                <option v-for="lv in (levels || [{code: 'N5', name: 'JLPT N5'}, {code: 'N4', name: 'JLPT N4'}, {code: 'N3', name: 'JLPT N3'}, {code: 'N2', name: 'JLPT N2'}, {code: 'JFT_A2', name: 'JFT-Basic A2'}])" :key="lv.code" :value="lv.code">
                                    {{ lv.name || lv.code }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 font-jp">
                                🎯 {{ isJapanese ? '学習到達目標・評価指標 *' : 'Indikator Capaian Pembelajaran *' }}
                            </label>
                            <select v-model="form.category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none font-jp">
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.code ? `[${cat.code}] ` : '' }}{{ cat.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                {{ isJapanese ? '関連する課' : 'Bab Materi Terkait' }}
                            </label>
                            <select v-model="form.chapter_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                                <option :value="null">{{ isJapanese ? '共通 / 課指定なし' : 'Umum / Tanpa Bab' }}</option>
                                <option v-for="ch in chapters" :key="ch.id" :value="ch.id">
                                    {{ isJapanese ? `第${ch.chapter_number}課: ${ch.title}` : `Bab ${ch.chapter_number}: ${ch.title}` }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                {{ isJapanese ? '配点ポイント *' : 'Bobot Poin Soal *' }}
                            </label>
                            <input 
                                type="number" 
                                v-model="form.points" 
                                required 
                                min="1" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- 2. Multimedia Pendukung Soal (Audio & Gambar) -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>{{ isJapanese ? '2. メディアファイル（任意）' : '2. Media Pendukung Soal (Opsional)' }}</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 mb-1.5">
                                <Volume2 class="w-3.5 h-3.5 text-rose-600" />
                                <span>{{ isJapanese ? 'リスニング音声 (MP3/WAV)' : 'File Audio Listening (MP3/WAV)' }}</span>
                            </label>
                            <input 
                                type="file" 
                                accept="audio/*"
                                @change="e => form.audio_file = e.target.files[0]"
                                class="w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer"
                            />
                            <div v-if="question.audio_url" class="mt-2 flex items-center gap-2">
                                <audio controls :src="question.audio_url" class="h-8 max-w-[240px]"></audio>
                                <span class="text-[11px] text-emerald-600 font-semibold">{{ isJapanese ? '✓ 音声設定済み' : '✓ Audio terpasang' }}</span>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 mb-1.5">
                                <ImageIcon class="w-3.5 h-3.5 text-blue-600" />
                                <span>{{ isJapanese ? '問題用画像・イラスト' : 'Gambar / Bagan Soal' }}</span>
                            </label>
                            <input 
                                type="file" 
                                accept="image/*"
                                @change="e => form.image_file = e.target.files[0]"
                                class="w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                            />
                            <div v-if="question.image_url" class="mt-2">
                                <img :src="question.image_url" class="h-20 rounded-lg object-cover border" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Teks Soal (Textarea Luas dengan Dukungan Huruf Jepang & Furigana) -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <span>{{ isJapanese ? '3. 問題文 *' : '3. Teks Pertanyaan Soal *' }}</span>
                    </h3>

                    <div>
                        <JapaneseInput
                            v-model="form.question_text"
                            :label="isJapanese ? '問題文 (ルビ・漢字入力可能)' : 'Teks Pertanyaan (Gunakan tombol sisipkan untuk memasukkan Huruf Jepang atau Furigana)'"
                            :required="true"
                            :isTextarea="true"
                            :rows="5"
                            :showFurigana="true"
                            :placeholder="isJapanese ? '問題文を入力してください... 例: 毎朝 何時に 起きますか。' : 'Tuliskan pertanyaan soal di sini... Contoh: 毎朝 何時に 起きますか。'"
                        />
                    </div>

                    <!-- Live Preview Teks Soal -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-[11px] font-bold text-slate-400 block mb-1">
                            {{ isJapanese ? '生徒の試験画面プレビュー:' : 'Preview Tampilan Soal di Layar Ujian Siswa:' }}
                        </span>
                        <div class="text-base font-bold font-jp text-slate-900" v-html="form.question_text || (isJapanese ? '<span class=\'text-slate-400 font-normal text-xs\'>問題文がありません...</span>' : '<span class=\'text-slate-400 font-normal text-xs\'>Belum ada teks pertanyaan...</span>')"></div>
                    </div>
                </div>

                <!-- 4. Pilihan Jawaban (A, B, C, D) & Kunci Jawaban -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-900">
                            {{ isJapanese ? '4. 選択肢と正解 *' : '4. Pilihan Jawaban & Kunci Jawaban *' }}
                        </h3>
                        <span class="text-xs font-semibold text-slate-500">
                            {{ isJapanese ? '正解の選択肢のラジオボタンを選択してください' : 'Pilih tombol radio pada jawaban yang BENAR' }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div 
                            v-for="(opt, idx) in form.options" 
                            :key="idx"
                            class="p-4 rounded-2xl border transition-all flex items-center gap-3"
                            :class="opt.is_correct ? 'bg-emerald-50/60 border-emerald-300 ring-1 ring-emerald-400/40' : 'bg-slate-50/70 border-slate-200'"
                        >
                            <!-- Radio Button Kunci Jawaban -->
                            <label class="flex items-center gap-2 cursor-pointer shrink-0">
                                <input 
                                    type="radio" 
                                    :name="'correct_answer'"
                                    :checked="opt.is_correct"
                                    @change="setCorrectOption(idx)"
                                    class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer"
                                />
                                <span class="font-black text-xs px-2.5 py-1 rounded-lg" :class="opt.is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700'">
                                    {{ String.fromCharCode(65 + idx) }}
                                </span>
                            </label>

                            <!-- Input Teks Pilihan Jawaban -->
                            <div class="flex-1">
                                <input 
                                    type="text" 
                                    v-model="opt.option_text" 
                                    required 
                                    @input="e => handleOptionInput(idx, e)"
                                    :placeholder="isJapanese ? `選択肢 ${String.fromCharCode(65 + idx)}...` : `Ketik teks pilihan ${String.fromCharCode(65 + idx)}... (bisa ketik romaji)`"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-jp focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white"
                                />
                            </div>

                            <!-- Badge Kunci Jawaban -->
                            <div class="shrink-0 w-24 text-right">
                                <span v-if="opt.is_correct" class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                                    {{ isJapanese ? '✓ 正解' : '✓ KUNCI' }}
                                </span>
                                <span v-else class="text-[11px] text-slate-400">
                                    {{ isJapanese ? 'ダミー' : 'Pengecoh' }}
                                </span>
                            </div>

                            <!-- Tombol Hapus Opsi (Jika > 2 opsi) -->
                            <button 
                                v-if="form.options.length > 2" 
                                type="button" 
                                @click="removeOption(idx)"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer shrink-0"
                                :title="isJapanese ? 'この選択肢を削除' : 'Hapus Pilihan Ini'">
                                <X class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Tambah Opsi (Maks. 5 opsi) -->
                    <div class="flex items-center justify-between pt-2">
                        <button 
                            v-if="form.options.length < 5" 
                            type="button" 
                            @click="addOption"
                            class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1.5 py-1.5 px-3 rounded-xl border border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50/50 transition-all cursor-pointer">
                            <Plus class="w-3.5 h-3.5" />
                            <span>{{ isJapanese ? `選択肢 ${String.fromCharCode(65 + form.options.length)} を追加` : `+ Tambah Pilihan (${String.fromCharCode(65 + form.options.length)})` }}</span>
                        </button>
                        <span class="text-[11px] text-slate-400 font-medium ml-auto">
                            {{ form.options.length }}/5 {{ isJapanese ? '選択肢 (最小2, 最大5)' : 'Pilihan (Min. 2, Maks. 5)' }}
                        </span>
                    </div>
                </div>


                <!-- Submit Bar -->
                <div class="flex items-center justify-between pt-4">
                    <Link :href="route('sensei.questions.index')" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                        {{ isJapanese ? 'キャンセル' : 'Batal' }}
                    </Link>

                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <Save class="w-4 h-4" />
                        <span>{{ form.processing ? (isJapanese ? '保存中...' : 'Menyimpan...') : (isJapanese ? '問題の更新を保存' : 'Perbarui Butir Soal') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import JapaneseInput from '@/Components/JapaneseInput.vue';
import { romajiToHiragana } from '@/Utils/japaneseConverter';
import { notifySuccess } from '@/Utils/alert';
import { useLang } from '@/Composables/useLang';
import { 
    HelpCircle, 
    ArrowLeft, 
    Volume2, 
    Image as ImageIcon, 
    Plus,
    X,
    Save 
} from 'lucide-vue-next';

const { isJapanese } = useLang();

const props = defineProps({
    question: Object,
    categories: Array,
    chapters: Array,
    levels: Array,
});

const form = useForm({
    category_id: props.question.question_category_id || props.question.category_id || props.categories?.[0]?.id || 1,
    chapter_id: props.question.chapter_id || null,
    level: props.question.level || props.question.level_code || 'N4',
    question_type: props.question.audio_url ? 'listening' : 'single_choice',
    question_text: props.question.question_text || '',
    points: props.question.score_points ? Math.round(Number(props.question.score_points)) : 2,
    audio_file: null,
    image_file: null,
    explanation: props.question.explanation || '',
    options: props.question.options?.map(opt => ({
        id: opt.id,
        option_text: opt.option_text,
        is_correct: Boolean(opt.is_correct),
        order_number: opt.order_number,
    })) || [
        { option_text: '', is_correct: true, order_number: 1 },
        { option_text: '', is_correct: false, order_number: 2 },
        { option_text: '', is_correct: false, order_number: 3 },
        { option_text: '', is_correct: false, order_number: 4 },
    ],
});

const setCorrectOption = (selectedIdx) => {
    form.options.forEach((opt, idx) => {
        opt.is_correct = (idx === selectedIdx);
    });
};

const addOption = () => {
    if (form.options.length < 5) {
        form.options.push({
            option_text: '',
            is_correct: false,
            order_number: form.options.length + 1,
        });
    }
};

const removeOption = (idx) => {
    if (form.options.length > 2) {
        const wasCorrect = form.options[idx].is_correct;
        form.options.splice(idx, 1);
        if (wasCorrect && form.options.length > 0) {
            form.options[0].is_correct = true;
        }
    }
};

const handleOptionInput = (idx, e) => {
    form.options[idx].option_text = romajiToHiragana(e.target.value);
};

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('sensei.questions.update', props.question.id), {
        onSuccess: () => {
            notifySuccess(isJapanese.value ? '更新完了' : 'Berhasil', isJapanese.value ? '問題が正常に更新されました。' : 'Butir soal berhasil diperbarui.');
        },
    });
};
</script>
