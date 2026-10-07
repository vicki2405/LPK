<template>
    <Head :title="`Bab ${chapter.chapter_number}: ${chapter.title} - Editor Materi`" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-12">
            <!-- ================= TOP NAVIGATION & HEADER ================= -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <Link :href="route('sensei.lms.index')" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 mb-2.5 transition-colors cursor-pointer">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ isJapanese ? '← 課一覧に戻る' : '← Kembali ke Daftar Bab' }}</span>
                    </Link>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="text-xs font-black px-3 py-1 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 font-jp">
                            第{{ chapter.chapter_number }}課 · Bab {{ chapter.chapter_number }}
                        </span>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                            {{ chapter.title }}
                        </h1>
                    </div>
                    <p v-if="chapter.description" class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ chapter.description }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ isJapanese ? '● 公開中' : '● Status: Aktif Tayang' }}
                    </span>
                </div>
            </div>

            <!-- ================= INDIKATOR CAPAIAN PEMBELAJARAN BAB ================= -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 font-jp">
                        <span class="text-base">🎯</span>
                        <span>{{ isJapanese ? '第' + chapter.chapter_number + '課の学習到達目標・評価指標' : 'Indikator Capaian Pembelajaran Bab ' + chapter.chapter_number }}</span>
                    </h3>
                    <span class="text-[11px] font-bold text-slate-400 font-jp">
                        {{ (chapter.learning_indicators?.length || 0) }} {{ isJapanese ? '指標設定済み' : 'Indikator Aktif' }}
                    </span>
                </div>

                <div v-if="chapter.learning_indicators && chapter.learning_indicators.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                    <div v-for="ind in chapter.learning_indicators" :key="ind.id"
                        class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 group hover:bg-rose-50/40 hover:border-rose-200 transition-all">
                        <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 text-xs font-black">
                            ✓
                        </div>
                        <div class="space-y-0.5 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-[10px] font-bold px-1.5 py-0.5 rounded bg-white border border-slate-200 text-slate-700">
                                    {{ ind.code || `IND-${ind.id}` }}
                                </span>
                                <span class="text-xs font-bold text-slate-900 font-jp truncate">
                                    {{ ind.name }}
                                </span>
                            </div>
                            <p v-if="ind.description" class="text-[11px] text-slate-500 line-clamp-2">
                                {{ ind.description }}
                            </p>
                        </div>
                    </div>
                </div>

                <div v-else class="py-4 text-center text-slate-400 text-xs flex items-center justify-center gap-2 font-jp">
                    <span>ℹ️</span>
                    <span>{{ isJapanese ? 'この課に特化した到達目標はまだ登録されていません。総合目標が適用されます。' : 'Bab ini belum memiliki indikator spesifik terikat. Menggunakan indikator umum LPK.' }}</span>
                </div>
            </div>

            <!-- ================= MATERI TATA BAHASA & LESSONS STUDIO ================= -->
            <div class="space-y-6">
                <!-- 1. Form Penulisan Materi Langsung di Halaman -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                📝
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-900">
                                {{ isEditingLesson ? 'Edit Materi Pembelajaran' : 'Tulis Materi Pembelajaran Baru' }}
                            </h3>
                        </div>

                        <button 
                            v-if="isEditingLesson" 
                            type="button" 
                            @click="resetLessonForm" 
                            class="text-xs font-bold text-slate-500 hover:text-slate-800 underline cursor-pointer"
                        >
                            Batal Edit / Tulis Baru
                        </button>
                    </div>

                    <form @submit.prevent="submitLessonForm" class="space-y-5" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Judul Materi / Poin Tata Bahasa *
                                </label>
                                <input 
                                    type="text" 
                                    v-model="lessonForm.title" 
                                    required 
                                    placeholder="Contoh: Pola 1: ~んです / ~んですが... (Menyatakan Alasan)" 
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Tipe Konten *
                                </label>
                                <select v-model="lessonForm.content_type" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
                                    <option value="text_grammar">Tata Bahasa & Penjelasan (Bunpou)</option>
                                    <option value="video">Video Pembelajaran</option>
                                    <option value="pdf_handout">Handout / Modul Bacaan</option>
                                    <option value="culture">Budaya & Dunia Kerja Jepang</option>
                                </select>
                            </div>
                        </div>

                        <!-- MULTIMEDIA INPUTS: Gambar, Voice Audio, Video Link -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200/80">
                            <!-- 1. Upload Gambar -->
                            <div>
                                <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 mb-1.5">
                                    <Image class="w-3.5 h-3.5 text-blue-600" />
                                    <span>Gambar Ilustrasi (Opsional)</span>
                                </label>
                                <input 
                                    type="file" 
                                    accept="image/*"
                                    @change="e => lessonForm.image_file = e.target.files[0]"
                                    class="w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                                />
                                <span v-if="existingLessonImage" class="text-[11px] text-emerald-600 font-semibold mt-1 block">
                                    ✓ Gambar saat ini terpasang
                                </span>
                            </div>

                            <!-- 2. Upload Voice Audio (MP3/WAV) -->
                            <div>
                                <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 mb-1.5">
                                    <Volume2 class="w-3.5 h-3.5 text-rose-600" />
                                    <span>Voice Audio (Opsional)</span>
                                </label>
                                <input 
                                    type="file" 
                                    accept="audio/*"
                                    @change="e => lessonForm.audio_file = e.target.files[0]"
                                    class="w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer"
                                />
                                <span v-if="existingLessonAudio" class="text-[11px] text-emerald-600 font-semibold mt-1 block">
                                    ✓ Voice audio saat ini terpasang
                                </span>
                            </div>

                            <!-- 3. Link Video YouTube -->
                            <div>
                                <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 mb-1.5">
                                    <Video class="w-3.5 h-3.5 text-red-600" />
                                    <span>Link Video YouTube (Opsional)</span>
                                </label>
                                <input 
                                    type="text" 
                                    v-model="lessonForm.video_url" 
                                    placeholder="https://www.youtube.com/..." 
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none"
                                />
                            </div>
                        </div>

                        <!-- BODY KONTEN MATERI LENGKAP DENGAN JAPANESE INPUT HELPER -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    Isi Materi & Pola Kalimat (Mendukung Teks Jepang & Furigana) *
                                </label>
                                <span class="text-[11px] text-slate-400">
                                    Gunakan tag &lt;ruby&gt;漢字&lt;rt&gt;かんじ&lt;/rt&gt;&lt;/ruby&gt; untuk furigana
                                </span>
                            </div>

                            <JapaneseInput 
                                v-model="lessonForm.content_body"
                                :is-textarea="true"
                                :rows="8"
                                placeholder="Tuliskan rumus pola kalimat, penjelasan makna, contoh percakapan kaiwa, dan catatan budaya di sini..."
                            />
                        </div>

                        <!-- LIVE PREVIEW MATERI -->
                        <div v-if="lessonForm.content_body" class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider mb-2">
                                👁️ Preview Tampilan Siswa:
                            </span>
                            <div class="prose max-w-none text-slate-800 text-xs sm:text-sm font-jp leading-relaxed" v-html="lessonForm.content_body"></div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <button 
                                type="submit" 
                                :disabled="lessonForm.processing"
                                class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
                            >
                                <Save class="w-4 h-4" />
                                <span>{{ isEditingLesson ? 'Simpan Perubahan Materi' : 'Terbitkan Materi Pembelajaran' }}</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 2. List Daftar Materi Bab yang Telah Diterbitkan -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <FileText class="w-5 h-5 text-blue-600" />
                            <span>Daftar Poin Materi Bab {{ chapter.chapter_number }} ({{ chapter.lessons?.length || 0 }} Materi)</span>
                        </h3>
                    </div>

                    <div v-if="chapter.lessons && chapter.lessons.length > 0" class="space-y-4">
                        <div 
                            v-for="(lesson, idx) in chapter.lessons" 
                            :key="lesson.id"
                            class="p-5 rounded-2xl border border-slate-200/80 bg-white hover:border-blue-300 hover:shadow-xs transition-all space-y-3"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ idx + 1 }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm text-slate-900">{{ lesson.title }}</h4>
                                        <span class="text-[11px] text-slate-400 uppercase font-semibold">Tipe: {{ lesson.content_type }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button 
                                        @click="populateLessonForEdit(lesson)"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                                        title="Edit Materi"
                                    >
                                        <Edit class="w-4 h-4" />
                                    </button>
                                    <button 
                                        @click="deleteLesson(lesson)"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                        title="Hapus Materi"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- Media preview badges -->
                            <div class="flex flex-wrap items-center gap-2 text-[11px]">
                                <span v-if="lesson.image_file" class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-medium">🖼️ Gambar Ada</span>
                                <span v-if="lesson.audio_file" class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 font-medium">🔊 Voice Audio Ada</span>
                                <span v-if="lesson.video_url" class="px-2 py-0.5 rounded-md bg-red-50 text-red-700 font-medium">🎬 Video YouTube</span>
                            </div>

                            <!-- Content body preview -->
                            <div class="bg-slate-50/70 p-4 rounded-xl text-xs text-slate-700 font-jp leading-relaxed max-h-32 overflow-hidden relative">
                                <div v-html="lesson.content_body"></div>
                                <div class="absolute inset-x-0 bottom-0 h-8 bg-gradient-to-t from-slate-50 to-transparent pointer-events-none"></div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-12 text-center text-slate-400 space-y-2">
                        <FileText class="w-10 h-10 text-slate-300 mx-auto" />
                        <p class="font-bold text-sm text-slate-600">Belum ada materi tata bahasa yang ditulis untuk bab ini.</p>
                        <p class="text-xs text-slate-400">Gunakan formulir editor di atas untuk menyusun poin tata bahasa baru.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import JapaneseInput from '@/Components/JapaneseInput.vue';
import { useLang } from '@/Composables/useLang';
import { notifySuccess, confirmDialog } from '@/Utils/alert';
import { 
    ArrowLeft, 
    FileText, 
    Layers, 
    Edit, 
    Trash2, 
    Save,
    Image,
    Volume2,
    Video
} from 'lucide-vue-next';

const props = defineProps({
    chapter: Object,
});

const { isJapanese } = useLang();

// ================= LESSON FORM STATE =================
const isEditingLesson = ref(false);
const editingLessonId = ref(null);
const existingLessonImage = ref(null);
const existingLessonAudio = ref(null);

const lessonForm = useForm({
    type: 'lesson',
    chapter_id: props.chapter.id,
    title: '',
    content_type: 'text_grammar',
    content_body: '',
    video_url: '',
    image_file: null,
    audio_file: null,
    duration_minutes: 45,
    is_published: true,
});

const resetLessonForm = () => {
    isEditingLesson.value = false;
    editingLessonId.value = null;
    existingLessonImage.value = null;
    existingLessonAudio.value = null;
    lessonForm.reset();
    lessonForm.chapter_id = props.chapter.id;
    lessonForm.content_type = 'text_grammar';
    lessonForm.is_published = true;
    lessonForm.image_file = null;
    lessonForm.audio_file = null;
};

const populateLessonForEdit = (lesson) => {
    isEditingLesson.value = true;
    editingLessonId.value = lesson.id;
    lessonForm.chapter_id = lesson.chapter_id;
    lessonForm.title = lesson.title;
    lessonForm.content_type = lesson.content_type;
    lessonForm.content_body = lesson.content_body;
    lessonForm.video_url = lesson.video_url || '';
    lessonForm.duration_minutes = lesson.duration_minutes || 45;
    lessonForm.is_published = Boolean(lesson.is_published);
    lessonForm.image_file = null;
    lessonForm.audio_file = null;
    existingLessonImage.value = lesson.image_file || null;
    existingLessonAudio.value = lesson.audio_file || null;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const submitLessonForm = () => {
    if (isEditingLesson.value) {
        lessonForm.transform((data) => ({
            ...data,
            _method: 'put',
        })).post(route('sensei.lms.update', editingLessonId.value), {
            onSuccess: () => {
                resetLessonForm();
                notifySuccess('Berhasil', 'Materi pembelajaran berhasil diperbarui.');
            },
        });
    } else {
        lessonForm.post(route('sensei.lms.store'), {
            onSuccess: () => {
                resetLessonForm();
                notifySuccess('Berhasil', 'Materi pembelajaran baru berhasil disimpan dan diterbitkan.');
            },
        });
    }
};

const deleteLesson = (lesson) => {
    confirmDialog(
        `Hapus Materi "${lesson.title}"?`,
        'Materi ini akan dihapus dari bab pembelajaran.'
    ).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('sensei.lms.destroy', lesson.id), {
                data: { type: 'lesson' },
                onSuccess: () => {
                    notifySuccess('Terhapus', 'Materi pembelajaran berhasil dihapus.');
                },
            });
        }
    });
};
</script>
