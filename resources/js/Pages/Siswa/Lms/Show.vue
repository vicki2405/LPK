<template>
    <Head :title="`第${chapter.chapter_number}課: ${chapter.title} - Ruang Belajar Siswa`" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-20">

            <!-- ================= 1. TOP MINIMALIST HEADER ================= -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <Link 
                        :href="route('siswa.lms.index')"
                        class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors shrink-0"
                        title="Kembali ke Daftar Bab"
                    >
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[11px] font-black text-rose-600 font-mono">第 {{ chapter.chapter_number }} 課</span>
                            <span class="text-xs text-slate-300">•</span>
                            <span class="text-xs font-bold text-slate-500">Bab {{ chapter.chapter_number }}</span>
                            <template v-if="indicators.length > 0">
                                <span class="text-xs text-slate-300">•</span>
                                <span 
                                    v-for="ind in indicators" 
                                    :key="ind.id"
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200/70 text-[11px] font-bold"
                                    :title="ind.description || ind.name"
                                >
                                    🎯 {{ ind.name }}
                                </span>
                            </template>
                        </div>
                        <h1 class="text-base sm:text-lg font-black text-slate-900 leading-tight font-jp mt-0.5">
                            {{ chapter.title }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto">
                    <span class="text-xs font-bold text-slate-500 hidden sm:inline">Pindah Bab:</span>
                    <div class="relative">
                        <select
                            :value="chapter.id"
                            @change="changeChapter($event.target.value)"
                            class="text-xs font-bold rounded-xl border-slate-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 py-2 pl-3 pr-8 bg-slate-50 text-slate-800 cursor-pointer appearance-none"
                        >
                            <option v-for="ch in otherChapters" :key="ch.id" :value="ch.id">
                                第{{ ch.chapter_number }}課: {{ ch.title }}
                            </option>
                        </select>
                        <ChevronDown class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    </div>
                </div>
            </div>

            <!-- ================= 4. BUNPOU & TATA BAHASA SHOWCASE ================= -->
            <section 
                v-if="chapter.description" 
                id="section-bunpou"
                class="bg-white rounded-3xl p-6 sm:p-8 border border-blue-200/90 shadow-sm space-y-4 relative overflow-hidden"
            >
                <div class="flex items-center justify-between border-b border-blue-100 pb-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold">
                            <BookOpen class="w-5 h-5" />
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-blue-600 uppercase tracking-wider font-jp block">
                                文法解説 • Rangkuman Tata Bahasa
                            </span>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 font-jp tracking-tight">
                                Catatan Pokok & Pola Kalimat Bab {{ chapter.chapter_number }}
                            </h2>
                        </div>
                    </div>

                    <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold font-jp hidden sm:inline">
                        Minna no Nihongo
                    </span>
                </div>

                <!-- Textbook Styled Content Box -->
                <div class="bg-gradient-to-br from-slate-50/80 to-blue-50/30 p-5 sm:p-6 rounded-2xl border border-slate-200/80">
                    <div class="text-slate-800 leading-relaxed font-normal whitespace-pre-line tracking-normal font-sans text-xs sm:text-sm">
                        {{ chapter.description }}
                    </div>
                </div>
            </section>

            <!-- ================= 5. LESSONS STUDIO (MATERI PELAJARAN) ================= -->
            <section 
                v-if="chapter.lessons && chapter.lessons.length > 0" 
                id="section-lessons"
                class="space-y-4"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-6 rounded-full bg-blue-600 inline-block"></span>
                        <h2 class="text-base sm:text-lg font-black text-slate-950 tracking-tight font-jp">
                            Modul Materi Pelajaran (Materi Ke-1 s/d {{ chapter.lessons.length }})
                        </h2>
                    </div>
                    <span class="text-xs font-bold text-slate-400 font-mono">
                        {{ chapter.lessons.length }} Modul Tersedia
                    </span>
                </div>

                <div 
                    v-for="(lesson, index) in chapter.lessons" 
                    :key="lesson.id"
                    class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden transition-all hover:border-slate-300"
                >
                    <!-- Lesson Header Banner -->
                    <div class="px-6 py-4.5 bg-slate-50/80 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span 
                                class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-black shrink-0 shadow-xs"
                                :class="getLessonBadgeBg(lesson.content_type)"
                            >
                                {{ getLessonIconText(lesson.content_type) }}
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-white border border-slate-200 text-slate-500">
                                        Modul #{{ index + 1 }}
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 font-jp uppercase">
                                        {{ getLessonTypeLabel(lesson.content_type) }}
                                    </span>
                                </div>
                                <h3 class="text-sm sm:text-base font-black text-slate-900 font-jp mt-0.5">
                                    {{ lesson.title }}
                                </h3>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 ml-auto">
                            <span v-if="lesson.duration_minutes" class="text-xs font-mono font-bold text-slate-500 bg-white px-2.5 py-1 rounded-xl border border-slate-200 flex items-center gap-1.5">
                                <Clock class="w-3.5 h-3.5 text-slate-400" />
                                <span>{{ lesson.duration_minutes }} mnt</span>
                            </span>
                        </div>
                    </div>

                    <!-- 1. Text Grammar / Article Content -->
                    <div v-if="lesson.content_body" class="p-6 sm:p-7">
                        <div class="bg-slate-50/60 p-5 sm:p-6 rounded-2xl border border-slate-200/80 text-slate-800 leading-relaxed whitespace-pre-line font-medium text-xs sm:text-sm">
                            {{ lesson.content_body }}
                        </div>
                    </div>

                    <!-- 2. Video Player Embed -->
                    <div v-if="lesson.video_url" class="p-6 sm:p-7 space-y-3">
                        <div class="aspect-video rounded-2xl overflow-hidden bg-slate-950 shadow-md border border-slate-800">
                            <iframe
                                :src="getEmbedUrl(lesson.video_url)"
                                class="w-full h-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <Video class="w-4 h-4 text-rose-500" />
                            <span>Putar video materi penjelasan untuk menyimak contoh percakapan dan intonasi penutur asli.</span>
                        </div>
                    </div>

                    <!-- 3. Audio Player (Choukai Listening) -->
                    <div v-if="lesson.audio_file" class="px-6 py-5 bg-gradient-to-r from-amber-50/60 to-orange-50/30 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Headphones class="w-4 h-4 text-amber-600" />
                                <span class="text-xs font-black text-slate-800 font-jp">Audio Pelafalan & Latihan Menyimak (聴解・Choukai):</span>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200 font-mono">
                                Standard Tokyo Accent
                            </span>
                        </div>
                        <div class="bg-white p-3 rounded-2xl border border-amber-200 shadow-2xs">
                            <audio controls class="w-full h-10 outline-none">
                                <source :src="resolveMediaUrl(lesson.audio_file)" />
                                Browser Anda tidak mendukung pemutar audio.
                            </audio>
                        </div>
                    </div>

                    <!-- 4. PDF Handout Link -->
                    <div v-if="lesson.pdf_file" class="px-6 py-5 bg-emerald-50/30 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                                📄
                            </div>
                            <div>
                                <span class="text-xs font-black text-slate-900 block font-jp">Lembar Kerja & Handout Bab {{ chapter.chapter_number }}</span>
                                <span class="text-[11px] text-slate-500">Unduh dokumen PDF materi untuk dicetak atau dibaca secara offline.</span>
                            </div>
                        </div>

                        <a 
                            :href="resolveMediaUrl(lesson.pdf_file)" 
                            target="_blank"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all inline-flex items-center gap-2 cursor-pointer"
                        >
                            <Download class="w-4 h-4" />
                            <span>Buka / Unduh Dokumen PDF</span>
                        </a>
                    </div>

                    <!-- 5. Image Attachment -->
                    <div v-if="lesson.image_file" class="px-6 py-5 border-t border-slate-100">
                        <img 
                            :src="resolveMediaUrl(lesson.image_file)" 
                            :alt="lesson.title"
                            class="rounded-2xl max-w-full border border-slate-200 shadow-sm max-h-[500px] object-contain mx-auto" 
                        />
                    </div>
                </div>
            </section>

            <!-- Empty lesson state if no lessons exist -->
            <div v-if="(!chapter.lessons || chapter.lessons.length === 0) && !chapter.description" class="bg-white border border-slate-200 rounded-3xl p-12 text-center shadow-sm space-y-2">
                <BookOpen class="w-12 h-12 text-slate-300 mx-auto mb-2" />
                <h3 class="text-sm font-bold text-slate-700">Belum ada materi pelajaran untuk bab ini.</h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto">
                    Materi tata bahasa, video tutorial, dan lembar kerja akan segera diperbarui oleh Sensei pengampu kelas Anda.
                </p>
            </div>



            <!-- ================= 7. PREV / NEXT CHAPTER NAVIGATION ================= -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <Link 
                        v-if="prevChapter" 
                        :href="route('siswa.lms.show', prevChapter.id)"
                        class="flex items-center gap-3.5 p-4 sm:p-5 bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50/30 rounded-2xl transition-all group shadow-xs cursor-pointer"
                    >
                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center shrink-0 transition-all">
                            <ChevronLeft class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block font-mono">← Bab Sebelumnya</span>
                            <span class="text-xs sm:text-sm font-black text-slate-800 group-hover:text-blue-700 transition-colors line-clamp-1 font-jp">
                                第{{ prevChapter.chapter_number }}課: {{ prevChapter.title }}
                            </span>
                        </div>
                    </Link>
                    <div v-else class="p-4 sm:p-5 rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 text-center text-xs text-slate-400 flex items-center justify-center gap-2 h-full font-jp">
                        <span>⛩️ Ini adalah Bab Pertama dari Kursus Ini</span>
                    </div>
                </div>

                <div>
                    <Link 
                        v-if="nextChapter" 
                        :href="route('siswa.lms.show', nextChapter.id)"
                        class="flex items-center justify-end gap-3.5 p-4 sm:p-5 bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/30 rounded-2xl transition-all group shadow-xs text-right cursor-pointer"
                    >
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block font-mono">Bab Berikutnya →</span>
                            <span class="text-xs sm:text-sm font-black text-slate-800 group-hover:text-emerald-700 transition-colors line-clamp-1 font-jp">
                                第{{ nextChapter.chapter_number }}課: {{ nextChapter.title }}
                            </span>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center shrink-0 transition-all">
                            <ChevronRight class="w-5 h-5" />
                        </div>
                    </Link>
                    <div v-else class="p-4 sm:p-5 rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 text-center text-xs text-slate-400 flex items-center justify-center gap-2 h-full font-jp">
                        <span>🎉 Selamat! Ini adalah Bab Terakhir dari Kursus Ini</span>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    BookOpen, 
    Layers, 
    ArrowLeft, 
    ArrowRight, 
    Volume2, 
    FileText, 
    ChevronLeft, 
    ChevronRight,
    ChevronDown,
    Clock,
    Sparkles,
    Download,
    Headphones,
    Video
} from 'lucide-vue-next';

const props = defineProps({
    chapter: Object,
    otherChapters: Array,
    prevChapter: Object,
    nextChapter: Object,
});

// Target Indicators Badges
const indicators = computed(() => {
    return props.chapter?.learning_indicators || props.chapter?.learningIndicators || [];
});

// Helpers for Lesson Display
const getLessonBadgeBg = (type) => {
    switch (type) {
        case 'video':
            return 'bg-rose-500';
        case 'audio':
            return 'bg-amber-500';
        case 'pdf_handout':
        case 'pdf':
            return 'bg-emerald-500';
        case 'culture':
        case 'image':
            return 'bg-purple-500';
        default:
            return 'bg-blue-600';
    }
};

const getLessonIconText = (type) => {
    switch (type) {
        case 'video':
            return '▶';
        case 'audio':
            return '🎧';
        case 'pdf_handout':
        case 'pdf':
            return '📄';
        case 'culture':
        case 'image':
            return '🏯';
        default:
            return '📝';
    }
};

const getLessonTypeLabel = (type) => {
    switch (type) {
        case 'video':
            return 'Video Pembelajaran';
        case 'audio':
            return 'Audio Choukai';
        case 'pdf_handout':
        case 'pdf':
            return 'Handout PDF';
        case 'culture':
            return 'Wawasan Budaya';
        case 'image':
            return 'Ilustrasi Visual';
        default:
            return 'Tata Bahasa & Teks';
    }
};

const resolveMediaUrl = (path) => {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    if (path.startsWith('/storage/')) return path;
    if (path.startsWith('storage/')) return `/${path}`;
    return `/storage/${path}`;
};

const changeChapter = (chapterId) => {
    router.get(route('siswa.lms.show', chapterId));
};

const getEmbedUrl = (url) => {
    if (!url) return '';
    const ytMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
    if (ytMatch) return `https://www.youtube.com/embed/${ytMatch[1]}`;
    return url;
};
</script>
