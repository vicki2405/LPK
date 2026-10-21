<template>
    <Head :title="isJapanese ? '新規問題パッケージ作成 - 問題バンク' : 'Buat Paket Soal Baru - Bank Soal Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-12">
            <!-- Top Navigation & Header -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <Link :href="route('sensei.questions.index')" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 mb-2.5 transition-colors cursor-pointer">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ isJapanese ? '← 問題バンクへ戻る' : '← Kembali ke Bank Soal' }}</span>
                    </Link>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <BookOpenCheck class="w-6 h-6 text-blue-600" />
                        <span>{{ isJapanese ? '新規問題パッケージ作成' : 'Buat Paket Soal Baru' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ isJapanese 
                            ? 'まず問題パッケージの属性を設定し、その後1回のセッションで問題を作成します。' 
                            : 'Tentukan identitas paket soal terlebih dahulu, lalu isi butir-butir soalnya dalam satu sesi kerja.' 
                        }}
                    </p>
                </div>

                <!-- Step Indicator -->
                <div class="flex items-center gap-2 shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all"
                            :class="step === 1 ? 'bg-blue-600 text-white shadow-md shadow-blue-400/30' : 'bg-emerald-500 text-white'">
                            {{ step === 1 ? '1' : '✓' }}
                        </div>
                        <span class="text-xs font-bold" :class="step === 1 ? 'text-blue-700' : 'text-emerald-700'">
                            {{ isJapanese ? 'パッケージ属性' : 'Identitas Paket' }}
                        </span>
                    </div>
                    <div class="w-8 h-0.5 bg-slate-200 rounded"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all"
                            :class="step === 2 ? 'bg-blue-600 text-white shadow-md shadow-blue-400/30' : 'bg-slate-200 text-slate-500'">
                            2
                        </div>
                        <span class="text-xs font-bold" :class="step === 2 ? 'text-blue-700' : 'text-slate-400'">
                            {{ isJapanese ? '問題作成' : 'Tulis Soal' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- ============= LANGKAH 1: PILIH IDENTITAS PAKET ============= -->
            <div v-if="step === 1" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-xs font-black">1</span>
                        {{ isJapanese ? '問題パッケージ属性の設定' : 'Tentukan Identitas Paket Soal' }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 ml-9">
                        {{ isJapanese 
                            ? 'レベル、関連する課、および問題分野を選択します。このセッションで作成するすべての問題がこのパッケージにまとめられます。' 
                            : 'Pilih Level, Bab Materi, dan Kategori soal. Semua butir soal yang kamu buat dalam sesi ini akan tergabung dalam paket ini.' 
                        }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Level -->
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-2 uppercase tracking-wider">
                            🎯 {{ isJapanese ? '習熟レベル *' : 'Level Kompetensi *' }}
                        </label>
                        <div class="space-y-2">
                            <button v-for="lv in levelOptions" :key="lv.code"
                                type="button"
                                @click="packet.level = lv.code"
                                class="w-full px-4 py-3 rounded-2xl border-2 text-left transition-all cursor-pointer flex items-center gap-3"
                                :class="packet.level === lv.code
                                    ? 'border-blue-600 bg-blue-50 text-blue-800'
                                    : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                                <span class="font-black text-sm w-14 shrink-0 px-2 py-0.5 rounded-lg text-center"
                                    :class="packet.level === lv.code ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600'">
                                    {{ lv.code }}
                                </span>
                                <span class="text-xs font-semibold">{{ lv.name }}</span>
                            </button>
                            <div v-if="levelOptions.length === 0" class="text-xs text-slate-400 text-center py-4">
                                {{ isJapanese ? 'レベルが登録されていません。' : 'Belum ada level. Tambahkan di' }}
                                <a :href="route('sensei.settings.questions')" class="text-blue-600 font-bold underline">
                                    {{ isJapanese ? '問題バンク設定' : 'Pengaturan Bank Soal' }}
                                </a>。
                            </div>
                        </div>
                    </div>

                    <!-- Bab / Chapter -->
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-2 uppercase tracking-wider">
                            📖 {{ isJapanese ? '関連する課' : 'Bab Materi Terkait' }}
                        </label>
                        <p class="text-[11px] text-slate-400 mb-2">
                            {{ isJapanese ? '任意 — 共通問題の場合は空欄' : 'Opsional — kosongkan jika soal ini bersifat umum / lintas bab.' }}
                        </p>
                        <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            <button
                                type="button"
                                @click="packet.chapter_id = null"
                                class="w-full px-3.5 py-2.5 rounded-xl border-2 text-left transition-all cursor-pointer flex items-center gap-2"
                                :class="packet.chapter_id === null
                                    ? 'border-slate-500 bg-slate-50 text-slate-800 font-bold'
                                    : 'border-slate-200 hover:border-slate-300 text-slate-500'">
                                <span class="text-xs">🗂️ {{ isJapanese ? '共通 / 課指定なし' : 'Umum / Tanpa Bab' }}</span>
                            </button>
                            <button v-for="ch in chapters" :key="ch.id"
                                type="button"
                                @click="packet.chapter_id = ch.id"
                                class="w-full px-3.5 py-2.5 rounded-xl border-2 text-left transition-all cursor-pointer"
                                :class="packet.chapter_id === ch.id
                                    ? 'border-blue-600 bg-blue-50 text-blue-800 font-bold'
                                    : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                                <div class="flex items-start gap-2">
                                    <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-slate-200 text-slate-600 shrink-0">
                                        第{{ ch.chapter_number }}課
                                    </span>
                                    <span class="text-xs font-semibold leading-tight">{{ ch.title }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-0.5 block ml-7">{{ ch.course?.title }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Indikator Capaian Pembelajaran -->
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-2 uppercase tracking-wider font-jp">
                            🎯 {{ isJapanese ? '学習到達目標・評価指標 *' : 'Indikator Capaian Pembelajaran *' }}
                        </label>
                        <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            <button v-for="cat in categories" :key="cat.id"
                                type="button"
                                @click="packet.category_id = cat.id; packet.category_name = cat.name"
                                class="w-full px-4 py-3 rounded-2xl border-2 text-left transition-all cursor-pointer"
                                :class="packet.category_id === cat.id
                                    ? 'border-rose-500 bg-rose-50 text-rose-800'
                                    : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                                <div class="flex items-center gap-2">
                                    <span v-if="cat.code" class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-white border border-slate-200 text-slate-700 font-bold">
                                        {{ cat.code }}
                                    </span>
                                    <span class="text-xs font-bold font-jp">{{ cat.name }}</span>
                                </div>
                                <span v-if="cat.description" class="text-[11px] text-slate-500 block mt-0.5">{{ cat.description }}</span>
                            </button>
                            <div v-if="categories.length === 0" class="text-xs text-slate-400 text-center py-4 font-jp">
                                {{ isJapanese ? '指標が未登録です。管理者メニューから追加してください。' : 'Belum ada indikator. Tambahkan di menu Admin > Indikator Capaian.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Identitas -->
                <div class="mt-6 p-5 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50/50" v-if="packet.level && packet.category_id">
                    <p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">
                        {{ isJapanese ? '作成予定のパッケージプレビュー:' : 'Preview Paket yang Akan Dibuat:' }}
                    </p>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-black">{{ packet.level }}</span>
                        <span class="text-slate-400">›</span>
                        <span class="px-3 py-1 rounded-full bg-slate-200 text-slate-700 text-xs font-semibold">
                            {{ packet.chapter_id ? chapters.find(c => c.id === packet.chapter_id)?.title : (isJapanese ? '共通（課指定なし）' : 'Umum (Tanpa Bab)') }}
                        </span>
                        <span class="text-slate-400">›</span>
                        <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-bold">{{ packet.category_name }}</span>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="button"
                        :disabled="!packet.level || !packet.category_id"
                        @click="step = 2"
                        class="px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-bold shadow-md shadow-blue-500/20 transition-all flex items-center gap-2 cursor-pointer">
                        {{ isJapanese ? '次へ: 問題文を入力' : 'Lanjut: Tulis Butir Soal' }} <ChevronRight class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- ============= LANGKAH 2: TULIS BUTIR SOAL ============= -->
            <div v-if="step === 2" class="space-y-5">
                <!-- Header Paket Dikunci -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-5 sm:p-6 rounded-3xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="text-blue-200 text-[11px] font-bold uppercase tracking-wider mb-1.5">
                            {{ isJapanese ? '現在のパッケージ (固定)' : 'Paket Soal Aktif (Dikunci)' }}
                        </p>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-3 py-1 rounded-full bg-white/20 text-white text-xs font-black">{{ packet.level }}</span>
                            <span class="text-blue-300">›</span>
                            <span class="px-3 py-1 rounded-full bg-white/20 text-white text-xs font-semibold">
                                {{ packet.chapter_id ? chapters.find(c => c.id === packet.chapter_id)?.title : (isJapanese ? '共通（課指定なし）' : 'Umum (Tanpa Bab)') }}
                            </span>
                            <span class="text-blue-300">›</span>
                            <span class="px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold">{{ packet.category_name }}</span>
                        </div>
                    </div>
                    <button type="button" @click="step = 1"
                        class="text-xs font-bold text-blue-200 hover:text-white border border-blue-400/50 hover:border-white/50 px-3 py-1.5 rounded-xl transition-colors cursor-pointer shrink-0">
                        {{ isJapanese ? '← パッケージ変更' : '← Ganti Paket' }}
                    </button>
                </div>

                <!-- Daftar Butir Soal -->
                <div v-for="(q, qIdx) in questions" :key="qIdx"
                    class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <!-- Soal Header -->
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-sm font-black text-slate-800 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-blue-600 text-white text-xs font-black flex items-center justify-center">
                                {{ qIdx + 1 }}
                            </span>
                            {{ isJapanese ? `問題 #${qIdx + 1}` : `Butir Soal #${qIdx + 1}` }}
                        </h3>
                        <button v-if="questions.length > 1" type="button" @click="removeQuestion(qIdx)"
                            class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors cursor-pointer">
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="p-6 sm:p-8 space-y-5">
                        <!-- Petunjuk Soal -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">
                                {{ isJapanese ? '問題の指示文 (Instruction) — 任意' : 'Petunjuk Soal (Instruction) — Opsional' }}
                            </label>
                            <input type="text" v-model="q.instruction"
                                placeholder="例: 下線の 言葉は どう 書きますか。"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-jp focus:ring-2 focus:ring-blue-600 focus:outline-none" />
                        </div>

                        <!-- Media (Audio / Gambar) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <div>
                                <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 mb-1.5">
                                    <Volume2 class="w-3.5 h-3.5 text-rose-600" /> 
                                    {{ isJapanese ? 'リスニング音声ファイル (任意)' : 'File Audio Listening (Opsional)' }}
                                </label>
                                <input type="file" accept="audio/*"
                                    @change="e => q.audio_file = e.target.files[0]"
                                    class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer" />
                            </div>
                            <div>
                                <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 mb-1.5">
                                    <ImageIcon class="w-3.5 h-3.5 text-blue-600" /> 
                                    {{ isJapanese ? '問題用イラスト画像 (任意)' : 'Gambar Ilustrasi Soal (Opsional)' }}
                                </label>
                                <input type="file" accept="image/*"
                                    @change="e => q.image_file = e.target.files[0]"
                                    class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                            </div>
                        </div>

                        <!-- Teks Soal -->
                        <div>
                            <JapaneseInput
                                v-model="q.question_text"
                                :label="isJapanese ? '問題文 *' : 'Teks Pertanyaan Soal *'"
                                :required="true"
                                :isTextarea="true"
                                :rows="4"
                                :showFurigana="true"
                                :placeholder="isJapanese ? '問題文を入力してください... 例: 毎朝 何時に 起きますか。' : 'Ketik pertanyaan soal... Contoh: 毎朝 何時に 起きますか。'"
                            />
                        </div>

                        <!-- Live Preview -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800" v-if="q.question_text">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                                {{ isJapanese ? '生徒の試験画面プレビュー:' : 'Preview di Layar Ujian Siswa:' }}
                            </p>
                            <div class="text-base font-bold font-jp text-white leading-loose" v-html="q.question_text"></div>
                        </div>

                        <!-- Pilihan Jawaban (A, B, C, D) -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-xs font-bold text-slate-700">
                                    {{ isJapanese ? '選択肢と正解 *' : 'Pilihan Jawaban & Kunci *' }}
                                </label>
                                <span class="text-[11px] text-slate-400">
                                    {{ isJapanese ? 'ラジオボタンをクリックして正解を選択' : 'Klik radio = pilih jawaban BENAR' }}
                                </span>
                            </div>
                            <div class="space-y-2.5">
                                <div v-for="(opt, oIdx) in q.options" :key="oIdx"
                                    class="p-3.5 rounded-2xl border-2 transition-all flex items-center gap-3"
                                    :class="opt.is_correct ? 'bg-emerald-50/70 border-emerald-400' : 'border-slate-200 bg-slate-50/50'">
                                    <label class="flex items-center gap-2 cursor-pointer shrink-0">
                                        <input type="radio" :name="`correct_${qIdx}`"
                                            :checked="opt.is_correct"
                                            @change="setCorrectOption(qIdx, oIdx)"
                                            class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[11px] font-black shrink-0 transition-colors"
                                            :class="opt.is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600'">
                                            {{ String.fromCharCode(65 + oIdx) }}
                                        </span>
                                    </label>
                                    <input type="text" v-model="opt.option_text" required
                                        @input="e => handleOptionInput(qIdx, oIdx, e)"
                                        :placeholder="isJapanese ? `選択肢 ${String.fromCharCode(65 + oIdx)}...` : `Pilihan ${String.fromCharCode(65 + oIdx)}...`"
                                        class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs font-jp focus:ring-2 focus:ring-blue-600 focus:outline-none bg-white" />
                                    <span v-if="opt.is_correct" class="text-[10px] font-bold text-emerald-700 shrink-0">
                                        {{ isJapanese ? '✓ 正解' : '✓ KUNCI' }}
                                    </span>
                                    <!-- Tombol Hapus Opsi (Jika > 2 opsi) -->
                                    <button 
                                        v-if="q.options.length > 2" 
                                        type="button" 
                                        @click="removeOption(qIdx, oIdx)"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer shrink-0"
                                        :title="isJapanese ? 'この選択肢を削除' : 'Hapus Pilihan Ini'">
                                        <X class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                            <!-- Tombol Tambah Opsi (Maks. 5 opsi) -->
                            <div class="flex items-center justify-between pt-2">
                                <button 
                                    v-if="q.options.length < 5" 
                                    type="button" 
                                    @click="addOption(qIdx)"
                                    class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1.5 py-1.5 px-3 rounded-xl border border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50/50 transition-all cursor-pointer">
                                    <Plus class="w-3.5 h-3.5" />
                                    <span>{{ isJapanese ? `選択肢 ${String.fromCharCode(65 + q.options.length)} を追加` : `+ Tambah Pilihan (${String.fromCharCode(65 + q.options.length)})` }}</span>
                                </button>
                                <span class="text-[11px] text-slate-400 font-medium ml-auto">
                                    {{ q.options.length }}/5 {{ isJapanese ? '選択肢 (最小2, 最大5)' : 'Pilihan (Min. 2, Maks. 5)' }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Tombol Tambah Soal Baru -->
                <button type="button" @click="addQuestion"
                    class="w-full py-4 rounded-3xl border-2 border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50/50 text-blue-600 font-bold text-sm transition-all cursor-pointer flex items-center justify-center gap-2">
                    <PlusCircle class="w-5 h-5" />
                    {{ isJapanese ? '+ このパッケージに問題を追加' : '+ Tambah Butir Soal Lagi dalam Paket Ini' }}
                </button>

                <!-- Submit Bar -->
                <div class="flex items-center justify-between pt-2 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div>
                        <p class="text-xs font-bold text-slate-700">
                            {{ isJapanese ? `${questions.length} 問の問題を保存準備完了` : `${questions.length} Butir Soal Siap Disimpan` }}
                        </p>
                        <p class="text-[11px] text-slate-400">
                            {{ isJapanese ? 'パッケージ:' : 'Paket:' }} {{ packet.level }} › {{ packet.category_name }}
                        </p>
                    </div>
                    <button
                        type="button"
                        :disabled="isSubmitting || !questions.every(q => q.question_text)"
                        @click="submitAll"
                        class="px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-bold shadow-md shadow-blue-500/20 transition-all flex items-center gap-2 cursor-pointer">
                        <Save class="w-4 h-4" />
                        {{ isSubmitting 
                            ? (isJapanese ? '保存中...' : 'Menyimpan...') 
                            : (isJapanese ? `${questions.length} 問を問題バンクに保存` : `Simpan ${questions.length} Soal ke Bank Soal`) 
                        }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import JapaneseInput from '@/Components/JapaneseInput.vue';
import { romajiToHiragana } from '@/Utils/japaneseConverter';
import { notifySuccess } from '@/Utils/alert';
import { useLang } from '@/Composables/useLang';
import {
    ArrowLeft,
    ChevronRight,
    BookOpenCheck,
    Volume2,
    Image as ImageIcon,
    Save,
    PlusCircle,
    Plus,
    X,
    Trash2
} from 'lucide-vue-next';

const { isJapanese } = useLang();

const props = defineProps({
    categories: Array,
    chapters: Array,
    levels: Array,
    initial: Object,
});

// ========== STATE ==========
const step = ref(1);
const isSubmitting = ref(false);

// Gunakan levels dari DB (dinamis per LPK)
const levelOptions = computed(() => props.levels || []);

const packet = reactive({
    level: props.initial?.level || props.levels?.[0]?.code || 'N4',
    chapter_id: props.initial?.chapter_id ? Number(props.initial.chapter_id) : null,
    category_id: props.initial?.category_id ? Number(props.initial.category_id) : (props.categories?.[0]?.id || null),
    category_name: props.categories?.find(c => c.id == props.initial?.category_id)?.name || props.categories?.[0]?.name || '',
});

const createEmptyQuestion = () => ({
    instruction: '',
    question_text: '',
    audio_file: null,
    image_file: null,
    explanation: '',
    options: [
        { option_text: '', is_correct: true },
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
    ],
});

const questions = reactive([createEmptyQuestion()]);

// ========== METHODS ==========
const addQuestion = () => {
    questions.push(createEmptyQuestion());
    setTimeout(() => {
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }, 100);
};

const removeQuestion = (idx) => {
    questions.splice(idx, 1);
};

const setCorrectOption = (qIdx, selectedOIdx) => {
    questions[qIdx].options.forEach((opt, oIdx) => {
        opt.is_correct = (oIdx === selectedOIdx);
    });
};

const addOption = (qIdx) => {
    if (questions[qIdx].options.length < 5) {
        questions[qIdx].options.push({ option_text: '', is_correct: false });
    }
};

const removeOption = (qIdx, oIdx) => {
    if (questions[qIdx].options.length > 2) {
        const wasCorrect = questions[qIdx].options[oIdx].is_correct;
        questions[qIdx].options.splice(oIdx, 1);
        if (wasCorrect && questions[qIdx].options.length > 0) {
            questions[qIdx].options[0].is_correct = true;
        }
    }
};

const handleOptionInput = (qIdx, oIdx, e) => {
    questions[qIdx].options[oIdx].option_text = romajiToHiragana(e.target.value);
};

// Submit semua soal dalam 1 kali request atomik (Bulk Store)
const submitAll = () => {
    isSubmitting.value = true;
    const formData = new FormData();
    formData.append('category_id', packet.category_id);
    if (packet.chapter_id) {
        formData.append('chapter_id', packet.chapter_id);
    }
    formData.append('level', packet.level);

    questions.forEach((q, qIdx) => {
        formData.append(`questions[${qIdx}][question_text]`, q.question_text);
        formData.append(`questions[${qIdx}][instruction]`, q.instruction || '');
        formData.append(`questions[${qIdx}][explanation]`, q.explanation || '');
        formData.append(`questions[${qIdx}][points]`, 2);

        if (q.audio_file) formData.append(`questions[${qIdx}][audio_file]`, q.audio_file);
        if (q.image_file) formData.append(`questions[${qIdx}][image_file]`, q.image_file);

        q.options.forEach((opt, oIdx) => {
            formData.append(`questions[${qIdx}][options][${oIdx}][option_text]`, opt.option_text);
            formData.append(`questions[${qIdx}][options][${oIdx}][is_correct]`, opt.is_correct ? '1' : '0');
        });
    });

    router.post(route('sensei.questions.store'), formData, {
        onSuccess: () => {
            isSubmitting.value = false;
            notifySuccess(
                isJapanese.value ? '保存完了' : 'Berhasil Disimpan!',
                isJapanese.value 
                    ? `${questions.length} 問が問題バンクに一括登録されました。`
                    : `${questions.length} butir soal berhasil disimpan sekaligus ke Bank Soal!`
            );
        },
        onError: (err) => {
            isSubmitting.value = false;
            console.error('Gagal menyimpan paket soal:', err);
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};
</script>
