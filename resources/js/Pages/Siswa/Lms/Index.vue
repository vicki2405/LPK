<template>
    <Head title="Materi Kurikulum LMS (Bab 1 - 50) - Masayume" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-12">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden">
                <div class="relative z-10 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold mb-3 border border-blue-500/30">
                        <BookOpen class="w-3.5 h-3.5" />
                        <span>Kurikulum Resmi Minna no Nihongo (N5 - N4)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Materi Pelajaran & Tata Bahasa (Bab 1 - 50)
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
                        Pilih bab pelajaran untuk mempelajari pola kalimat (*Bunpou*), daftar kosakata resmi (*Kotoba*), dan contoh percakapan kerja di Jepang.
                    </p>
                </div>
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <!-- Search & Filter Controls -->
            <div class="bg-white p-4 sm:p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="relative w-full sm:w-80">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Cari nomor bab atau judul materi..."
                        class="w-full pl-10 pr-4 py-2 rounded-xl text-xs border border-slate-200 focus:border-japan-red focus:ring-japan-red"
                    />
                </div>
                <span class="text-xs font-bold text-slate-500 shrink-0">
                    Menampilkan {{ filteredChapters.length }} Bab Tersedia
                </span>
            </div>

            <!-- Chapter Grid Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div 
                    v-for="ch in filteredChapters" 
                    :key="ch.id"
                    class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group hover:border-japan-red/50"
                >
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-xl bg-red-50 text-japan-red text-xs font-black font-jp border border-red-100">
                                第{{ ch.chapter_number }}課 (Bab {{ ch.chapter_number }})
                            </span>
                            <span class="text-[11px] font-bold text-slate-400">
                                📚 {{ ch.vocabularies_count || 0 }} Kosakata
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 group-hover:text-japan-red transition-colors mb-2">
                            {{ ch.title }}
                        </h3>

                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-3 mb-6 font-medium">
                            {{ ch.description || 'Pola pembentukan kalimat, percakapan kerja, dan daftar kosakata resmi.' }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            Aktif
                        </span>
                        <Link 
                            :href="route('siswa.lms.show', ch.id)" 
                            class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-japan-red text-white text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm"
                        >
                            <span>Buka Materi Bab</span>
                            <ChevronRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredChapters.length === 0" class="p-12 text-center bg-white rounded-3xl border border-slate-200">
                <BookOpen class="w-12 h-12 text-slate-300 mx-auto mb-3" />
                <h4 class="text-sm font-bold text-slate-800">Tidak Ada Bab yang Cocok</h4>
                <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { BookOpen, Search, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
    courses: Array,
});

const searchQuery = ref('');

const allChapters = computed(() => {
    const list = [];
    (props.courses || []).forEach(c => {
        if (c.chapters) {
            c.chapters.forEach(ch => list.push(ch));
        }
    });
    return list;
});

const filteredChapters = computed(() => {
    if (!searchQuery.value) return allChapters.value;
    const q = searchQuery.value.toLowerCase();
    return allChapters.value.filter(ch => 
        ch.title?.toLowerCase().includes(q) || 
        ch.chapter_number?.toString().includes(q) ||
        ch.description?.toLowerCase().includes(q)
    );
});
</script>
