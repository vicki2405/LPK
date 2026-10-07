<template>
    <Head :title="`第${chapter.chapter_number}課: ${chapter.title} - Masayume`" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-16">

            <!-- ===== TOP NAV BAR ===== -->
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link :href="route('siswa.lms.index')"
                        class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <span class="text-[11px] font-black text-japan-red uppercase tracking-wider font-jp block">
                            第{{ chapter.chapter_number }}課 — {{ chapter.course?.title || 'LMS Masayume' }}
                        </span>
                        <h1 class="text-lg sm:text-xl font-black text-slate-900 leading-tight">{{ chapter.title }}</h1>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs font-bold text-slate-500 hidden sm:inline">Pindah Bab:</span>
                    <select
                        :value="chapter.id"
                        @change="changeChapter($event.target.value)"
                        class="text-xs font-bold rounded-xl border-slate-200 focus:border-japan-red focus:ring-japan-red py-2 px-3 bg-slate-50"
                    >
                        <option v-for="ch in otherChapters" :key="ch.id" :value="ch.id">
                            第{{ ch.chapter_number }}課: {{ ch.title }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- ===== TARGET INDIKATOR CAPAIAN PEMBELAJARAN BAB ===== -->
            <div v-if="indicators.length > 0" 
                class="bg-gradient-to-r from-rose-50/80 via-red-50/40 to-amber-50/50 border border-rose-200/80 rounded-3xl p-5 sm:p-6 shadow-sm space-y-3">
                <div class="flex items-center justify-between border-b border-rose-100 pb-2.5">
                    <h2 class="text-xs sm:text-sm font-black text-rose-950 uppercase tracking-wide flex items-center gap-2 font-jp">
                        <span>🎯</span>
                        <span>Target Capaian Pembelajaran Bab {{ chapter.chapter_number }} (学習到達目標)</span>
                    </h2>
                    <span class="text-[11px] font-bold text-rose-700 bg-rose-100/70 px-2.5 py-0.5 rounded-full font-mono">
                        {{ indicators.length }} Target
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 pt-1">
                    <div v-for="ind in indicators" :key="ind.id"
                        class="p-3 rounded-2xl bg-white/80 border border-rose-100 flex items-start gap-2.5 shadow-2xs">
                        <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 font-black text-xs flex items-center justify-center shrink-0">✓</span>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 block leading-tight font-jp">{{ ind.name }}</span>
                            <span v-if="ind.description" class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ ind.description }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== CHAPTER DESCRIPTION / POLA TATA BAHASA ===== -->
            <div v-if="chapter.description" class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-3xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <BookOpen class="w-5 h-5 text-blue-600" />
                    <h2 class="text-sm font-black text-blue-900 uppercase tracking-wide">📖 Penjelasan Tata Bahasa & Pola Kalimat (文法・Bunpou)</h2>
                </div>
                <div class="text-sm text-slate-800 leading-relaxed font-medium whitespace-pre-line">
                    {{ chapter.description }}
                </div>
            </div>

            <!-- ===== LESSONS CONTENT ===== -->
            <div v-if="chapter.lessons && chapter.lessons.length > 0" class="space-y-4">
                <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                    <FileText class="w-5 h-5 text-blue-600" />
                    Materi Pelajaran Bab {{ chapter.chapter_number }}
                </h2>

                <div v-for="lesson in chapter.lessons" :key="lesson.id"
                    class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                    <!-- Lesson Header -->
                    <div class="px-6 pt-5 pb-4 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl flex items-center justify-center text-white text-xs font-black shrink-0"
                            :class="{
                                'bg-blue-500': lesson.content_type === 'text_grammar' || lesson.content_type === 'article',
                                'bg-rose-500': lesson.content_type === 'video',
                                'bg-amber-500': lesson.content_type === 'audio',
                                'bg-emerald-500': lesson.content_type === 'pdf_handout' || lesson.content_type === 'pdf',
                                'bg-violet-500': lesson.content_type === 'culture' || lesson.content_type === 'image',
                            }">
                            {{ lesson.content_type === 'video' ? '▶' : lesson.content_type === 'audio' ? '🎧' : (lesson.content_type === 'pdf_handout' || lesson.content_type === 'pdf') ? '📄' : (lesson.content_type === 'culture' || lesson.content_type === 'image') ? '🏯' : '📝' }}
                        </span>
                        <h3 class="text-sm font-black text-slate-900">{{ lesson.title }}</h3>
                        <span v-if="lesson.duration_minutes" class="ml-auto text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                            {{ lesson.duration_minutes }} mnt
                        </span>
                    </div>

                    <!-- Text Grammar / Article Content -->
                    <div v-if="lesson.content_body" class="px-6 py-5">
                        <div class="prose max-w-none text-slate-700 text-sm leading-relaxed font-medium whitespace-pre-line bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                            {{ lesson.content_body }}
                        </div>
                    </div>

                    <!-- Video Embed -->
                    <div v-if="lesson.video_url" class="px-6 py-5">
                        <div class="aspect-video rounded-2xl overflow-hidden bg-slate-900">
                            <iframe
                                :src="getEmbedUrl(lesson.video_url)"
                                class="w-full h-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>

                    <!-- Audio Player (Whenever Audio File Exists) -->
                    <div v-if="lesson.audio_file" class="px-6 py-4 bg-amber-50/40 border-t border-slate-100">
                        <div class="flex items-center gap-2 mb-2">
                            <Volume2 class="w-4 h-4 text-amber-600" />
                            <span class="text-xs font-bold text-slate-800">Audio Pelafalan / Penjelasan:</span>
                        </div>
                        <audio controls class="w-full rounded-xl h-10">
                            <source :src="resolveMediaUrl(lesson.audio_file)" />
                            Browser Anda tidak mendukung pemutar audio.
                        </audio>
                    </div>

                    <!-- PDF Link (Whenever PDF File Exists or content_type is pdf/pdf_handout) -->
                    <div v-if="lesson.pdf_file" class="px-6 py-4 bg-emerald-50/30 border-t border-slate-100">
                        <a :href="resolveMediaUrl(lesson.pdf_file)" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200 hover:bg-emerald-100 transition-colors">
                            📄 Buka / Unduh PDF Materi Pelajaran
                        </a>
                    </div>

                    <!-- Image (Whenever Image File Exists) -->
                    <div v-if="lesson.image_file" class="px-6 py-4 border-t border-slate-100">
                        <img :src="resolveMediaUrl(lesson.image_file)" :alt="lesson.title"
                            class="rounded-2xl max-w-full border border-slate-200 shadow-sm" />
                    </div>
                </div>
            </div>

            <!-- ===== DEDICATED KOTOBA FLASHCARD BANNER ===== -->
            <div class="bg-gradient-to-br from-amber-500 via-amber-600 to-orange-600 rounded-3xl p-6 sm:p-7 text-white shadow-lg shadow-amber-500/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 overflow-hidden relative">
                <!-- Background decorative kanji -->
                <span class="absolute -right-4 -bottom-6 text-7xl sm:text-8xl font-black font-jp text-white/10 select-none pointer-events-none">
                    単語
                </span>

                <div class="space-y-2 relative z-10 max-w-xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm border border-white/20 text-xs font-black uppercase tracking-wider">
                        <Layers class="w-3.5 h-3.5" />
                        <span>Kotoba Gym & Flashcards</span>
                        <span v-if="chapter.vocabularies_count" class="bg-white text-amber-700 px-2 py-0.5 rounded-full font-bold text-[10px]">
                            {{ chapter.vocabularies_count }} Kosakata
                        </span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black leading-snug">
                        Latih Hafalan Kosakata Bab {{ chapter.chapter_number }}
                    </h3>
                    <p class="text-xs sm:text-sm text-amber-100 font-medium leading-relaxed">
                        Manajemen kosakata dipusatkan di modul <strong>Flashcards Kotoba</strong> agar fokus belajar lebih optimal, lengkap dengan audio pelafalan, furigana/kanji, dan kartu interaktif.
                    </p>
                </div>

                <div class="relative z-10 shrink-0 w-full md:w-auto">
                    <Link
                        :href="route('siswa.flashcards.index', { chapter_id: chapter.id })"
                        class="w-full md:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-2xl bg-white text-amber-950 font-black text-xs sm:text-sm shadow-md hover:bg-amber-50 hover:shadow-lg transition-all group"
                    >
                        <span>Buka Flashcards Bab Ini</span>
                        <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </Link>
                </div>
            </div>

            <!-- Empty lesson state if no lessons exist -->
            <div v-if="!chapter.lessons || chapter.lessons.length === 0" class="bg-white border border-slate-200 rounded-3xl p-10 text-center shadow-sm">
                <BookOpen class="w-10 h-10 text-slate-200 mx-auto mb-3" />
                <p class="text-sm font-bold text-slate-600">Belum ada materi pelajaran untuk bab ini.</p>
                <p class="text-xs text-slate-400 mt-1">Materi tata bahasa atau video akan ditambahkan oleh Sensei segera.</p>
            </div>

            <!-- ===== PREV / NEXT NAVIGATION ===== -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <Link v-if="prevChapter" :href="route('siswa.lms.show', prevChapter.id)"
                        class="flex items-center gap-3 p-4 bg-white border border-slate-200 hover:border-blue-300 hover:bg-blue-50/40 rounded-2xl transition-all group shadow-2xs">
                        <ChevronLeft class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition-colors shrink-0" />
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">← Bab Sebelumnya</span>
                            <span class="text-xs font-black text-slate-800 group-hover:text-blue-700 transition-colors line-clamp-1">
                                第{{ prevChapter.chapter_number }}課: {{ prevChapter.title }}
                            </span>
                        </div>
                    </Link>
                    <div v-else class="p-4 rounded-2xl border border-dashed border-slate-200 text-center text-xs text-slate-400 flex items-center justify-center gap-2 h-full">
                        <span>Ini Bab Pertama</span>
                    </div>
                </div>

                <div>
                    <Link v-if="nextChapter" :href="route('siswa.lms.show', nextChapter.id)"
                        class="flex items-center justify-end gap-3 p-4 bg-white border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/40 rounded-2xl transition-all group shadow-2xs text-right">
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Bab Berikutnya →</span>
                            <span class="text-xs font-black text-slate-800 group-hover:text-emerald-700 transition-colors line-clamp-1">
                                第{{ nextChapter.chapter_number }}課: {{ nextChapter.title }}
                            </span>
                        </div>
                        <ChevronRight class="w-5 h-5 text-slate-400 group-hover:text-emerald-600 transition-colors shrink-0" />
                    </Link>
                    <div v-else class="p-4 rounded-2xl border border-dashed border-slate-200 text-center text-xs text-slate-400 flex items-center justify-center gap-2 h-full">
                        <span>Ini Bab Terakhir</span>
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
import { BookOpen, Layers, ArrowLeft, ArrowRight, Volume2, FileText, ChevronLeft, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
    chapter: Object,
    otherChapters: Array,
    prevChapter: Object,
    nextChapter: Object,
});

const indicators = computed(() => {
    return props.chapter?.learning_indicators || props.chapter?.learningIndicators || [];
});

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
    // Convert YouTube watch URL to embed URL
    const ytMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
    if (ytMatch) return `https://www.youtube.com/embed/${ytMatch[1]}`;
    return url;
};
</script>
