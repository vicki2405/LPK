<template>
    <Head :title="`Hasil Ujian: ${exam.title} - Masayume`" />

    <AuthenticatedLayout>
        <div class="max-w-3xl mx-auto space-y-6 animate-fade-in pb-12">
            <!-- Top Congratulations / Result Card -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden text-center p-8 sm:p-12 relative">
                <!-- Result Badge -->
                <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center shadow-lg"
                    :class="session.is_passed ? 'bg-emerald-500 text-white shadow-emerald-500/30' : 'bg-rose-500 text-white shadow-rose-500/30'">
                    <CheckCircle2 v-if="session.is_passed" class="w-10 h-10" />
                    <XCircle v-else class="w-10 h-10" />
                </div>

                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider mb-2 font-jp"
                    :class="session.is_passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                    {{ session.is_passed ? '合格 GOUKAKU (LULUS PASSING GRADE)' : '不合格 FUGOUKAKU (BELUM LULUS)' }}
                </span>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    {{ exam.title }}
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Diselesaikan pada: {{ new Date(session.submitted_at).toLocaleString('id-ID') }}
                </p>

                <!-- Total Score Number -->
                <div class="my-8 p-6 rounded-3xl bg-slate-50 border border-slate-200/80 max-w-sm mx-auto">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Skor Total Diperoleh</span>
                    <div class="text-4xl sm:text-5xl font-black text-slate-950 my-1">
                        {{ session.total_score }} <span class="text-base text-slate-400 font-bold">/ {{ exam.max_score }}</span>
                    </div>
                    <span class="text-xs font-bold"
                        :class="session.is_passed ? 'text-emerald-600' : 'text-rose-600'">
                        Passing Grade Resmi: {{ exam.passing_score }} Poin
                    </span>
                </div>

                <!-- 3 JLPT Score Category Breakdown -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-lg mx-auto text-left">
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">1. Moji & Goi</span>
                        <span class="text-lg font-black text-slate-900">{{ session.moji_goi_score || 0 }}</span>
                        <span class="text-[10px] text-slate-500 font-medium block">Kosakata / Kanji</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">2. Bunpou & Dokkai</span>
                        <span class="text-lg font-black text-slate-900">{{ session.bunpou_dokkai_score || 0 }}</span>
                        <span class="text-[10px] text-slate-500 font-medium block">Tata Bahasa & Bacaan</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">3. Choukai</span>
                        <span class="text-lg font-black text-slate-900">{{ session.choukai_score || 0 }}</span>
                        <span class="text-[10px] text-slate-500 font-medium block">Pendengaran (Audio)</span>
                    </div>
                </div>

                <!-- Answer Statistics -->
                <div class="mt-6 pt-6 border-t border-slate-100 flex items-center justify-center gap-6 text-xs text-slate-600 font-bold">
                    <span class="text-emerald-700">✓ {{ session.correct_answers_count }} Benar</span>
                    <span class="text-rose-700">✗ {{ session.wrong_answers_count }} Salah</span>
                    <span class="text-slate-500">○ {{ session.unanswered_count }} Kosong</span>
                </div>

                <!-- Return Button -->
                <div class="mt-8">
                    <Link 
                        :href="route('siswa.dashboard')" 
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-slate-900 hover:bg-black text-white text-xs font-bold transition-all shadow-md cursor-pointer"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        <span>Kembali ke Training Camp Dashboard</span>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CheckCircle2, XCircle, ArrowLeft } from 'lucide-vue-next';

defineProps({
    session: Object,
    exam: Object,
});
</script>
