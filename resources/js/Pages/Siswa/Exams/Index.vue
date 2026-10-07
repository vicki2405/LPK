<template>
    <Head title="Portal Ujian CBT - Masayume LPK" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-12">

            <!-- ===== HEADER SECTION ===== -->
            <div class="bg-gradient-to-br from-white via-slate-50 to-red-50/30 border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-[0_10px_30px_rgba(0,0,0,0.03)] flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                <!-- Decorative red accent top-left -->
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-gradient-to-br from-red-100/60 to-rose-200/30 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-japan-red via-rose-500 to-amber-400 rounded-l-3xl"></div>

                <div class="pl-2 relative z-10">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-rose-50 text-japan-red text-xs font-bold mb-3 border border-rose-200/80 shadow-2xs">
                        <Clock class="w-3.5 h-3.5" />
                        <span>Engine CBT Standar Ujian JLPT N4 / JFT-Basic A2</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-950">
                        Portal Ujian CBT &amp; Rapor Nilai
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 max-w-xl leading-relaxed font-medium">
                        Kerjakan paket ujian resmi yang ditugaskan Sensei. Sistem otomatis menjalankan mode fullscreen anti-cheat dengan pencatatan rekam jejak real-time.
                    </p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-3 gap-2.5 sm:gap-3 shrink-0 pl-2 md:pl-0 relative z-10">
                    <div class="bg-white border border-amber-200/90 px-3.5 sm:px-4 py-3 rounded-2xl text-center shadow-xs">
                        <span class="text-[10px] text-amber-800 uppercase tracking-wider block font-bold">Rata-Rata</span>
                        <span class="text-xl sm:text-2xl font-black text-amber-600 font-mono">{{ stats.avg_score || 0 }}</span>
                        <span class="text-[10px] text-amber-700/70 font-semibold block">Poin</span>
                    </div>
                    <div class="bg-white border border-emerald-200/90 px-3.5 sm:px-4 py-3 rounded-2xl text-center shadow-xs">
                        <span class="text-[10px] text-emerald-800 uppercase tracking-wider block font-bold">Total Lulus</span>
                        <span class="text-xl sm:text-2xl font-black text-emerald-600 font-mono">{{ stats.total_passed || 0 }}</span>
                        <span class="text-[10px] text-emerald-700/70 font-semibold block">Kali</span>
                    </div>
                    <div class="bg-white border border-blue-200/90 px-3.5 sm:px-4 py-3 rounded-2xl text-center shadow-xs">
                        <span class="text-[10px] text-blue-800 uppercase tracking-wider block font-bold">Diikuti</span>
                        <span class="text-xl sm:text-2xl font-black text-blue-600 font-mono">{{ stats.total_taken || 0 }}</span>
                        <span class="text-[10px] text-blue-700/70 font-semibold block">Paket</span>
                    </div>
                </div>
            </div>

            <!-- ===== ANTI-CHEAT NOTICE ===== -->
            <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-50/90 to-orange-50/60 border border-amber-200/90 flex items-start gap-3.5 text-xs text-amber-950 leading-relaxed shadow-xs">
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                    <ShieldAlert class="w-4 h-4" />
                </div>
                <div>
                    <strong class="font-black text-amber-900">⚠️ PERATURAN RESMI CBT:</strong>
                    &nbsp;Saat ujian dimulai, browser otomatis masuk ke <strong>Mode Layar Penuh (Fullscreen)</strong>. 
                    Dilarang berpindah tab browser, membuka aplikasi lain, keluar dari fullscreen, atau meminimalkan HP. 
                    Seluruh aktivitas tercatat dan dilaporkan otomatis ke Sensei.
                </div>
            </div>

            <!-- ===== ACTIVE EXAM PACKAGES ===== -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base sm:text-lg font-black text-slate-950 flex items-center gap-2">
                        <Clock class="w-5 h-5 text-japan-red" />
                        Tugas Paket Ujian Aktif dari Sensei
                    </h2>
                    <span v-if="assignedExams && assignedExams.length > 0"
                        class="text-xs text-japan-red font-black bg-rose-50 px-3 py-1 rounded-full border border-rose-200 animate-pulse">
                        ● {{ assignedExams.length }} Ujian Siap Dikerjakan
                    </span>
                </div>

                <!-- Exam Cards Grid -->
                <div v-if="assignedExams && assignedExams.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        v-for="exam in assignedExams"
                        :key="exam.id"
                        class="bg-white border border-slate-200/90 hover:border-rose-300 hover:shadow-[0_12px_35px_rgba(225,29,72,0.12)] rounded-3xl p-6 flex flex-col justify-between transition-all duration-300 group relative overflow-hidden shadow-xs"
                    >
                        <!-- Top Accent Hover Bar -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-japan-red via-rose-500 to-amber-400 group-hover:h-1.5 transition-all"></div>

                        <div>
                            <!-- Exam Header: Subject & Duration -->
                            <div class="flex items-center justify-between mb-3 gap-2 flex-wrap">
                                <span class="px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-black border border-indigo-200/80 shadow-2xs">
                                    📚 {{ exam.subject?.name || 'Mata Pelajaran Umum' }}
                                </span>
                                <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5 bg-slate-50 px-3 py-1 rounded-xl border border-slate-200">
                                    <Clock class="w-3.5 h-3.5 text-slate-400" />
                                    {{ exam.duration_minutes }} Menit
                                </span>
                            </div>

                            <!-- Exam Title & Desc -->
                            <h3 class="text-lg font-black text-slate-950 tracking-tight mb-2 group-hover:text-japan-red transition-colors">
                                {{ exam.title }}
                            </h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-4 font-medium line-clamp-2">
                                {{ exam.description || 'Simulasi resmi kelulusan (Moji-Goi, Bunpou-Dokkai, & Choukai).' }}
                            </p>

                            <!-- Exam Specs Grid -->
                            <div class="grid grid-cols-3 gap-2 mb-4">
                                <div class="bg-slate-50 rounded-2xl p-2.5 text-center border border-slate-200/60">
                                    <span class="block text-[10px] text-slate-500 font-bold uppercase">Butir Soal</span>
                                    <span class="block text-base font-black text-slate-900 font-mono">{{ exam.questions?.length || exam.questions_count || 0 }}</span>
                                </div>
                                <div class="bg-rose-50/70 rounded-2xl p-2.5 text-center border border-rose-200/60">
                                    <span class="block text-[10px] text-japan-red font-bold uppercase">Passing</span>
                                    <span class="block text-base font-black text-japan-red font-mono">{{ exam.passing_score }}</span>
                                </div>
                                <div class="bg-slate-50 rounded-2xl p-2.5 text-center border border-slate-200/60">
                                    <span class="block text-[10px] text-slate-500 font-bold uppercase">Max Skor</span>
                                    <span class="block text-base font-black text-slate-900 font-mono">{{ exam.max_score }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer: Sensei + Start Button -->
                        <div class="flex items-center justify-between gap-2 flex-wrap pt-4 border-t border-slate-100">
                            <span class="text-xs text-slate-600 font-semibold flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-800 to-slate-900 text-white text-[11px] font-black flex items-center justify-center shadow-xs">
                                    {{ exam.creator?.name?.charAt(0) || 'S' }}
                                </span>
                                <span class="truncate max-w-[120px] xs:max-w-none">{{ exam.creator?.name || 'Sensei LPK' }}</span>
                            </span>
                            <button
                                @click="startExam(exam)"
                                class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-japan-red to-rose-600 hover:from-red-600 hover:to-rose-700 text-white text-xs font-black shadow-md shadow-japan-red/25 transition-all transform hover:scale-105 flex items-center gap-2 cursor-pointer shrink-0"
                            >
                                🚀 <span>Mulai Ujian</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="p-10 rounded-3xl bg-white border border-slate-200 text-center shadow-2xs">
                    <Clock class="w-12 h-12 text-slate-200 mx-auto mb-3" />
                    <h4 class="text-sm font-bold text-slate-800">Belum Ada Tugas Ujian CBT Aktif</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Ujian yang dijadwalkan dan diaktifkan oleh Sensei akan muncul di sini secara otomatis.</p>
                </div>
            </div>

            <!-- ===== GRADEBOOK TABLE ===== -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-black text-slate-950 flex items-center gap-2">
                        <Award class="w-5 h-5 text-emerald-600" />
                        Rekam Jejak &amp; Rapor Nilai CBT Anda
                    </h3>
                    <span class="text-xs font-bold text-slate-500 bg-slate-50 px-3 py-1 rounded-full border border-slate-200">
                        {{ recentSessions.length }} Kali Ujian
                    </span>
                </div>

                <!-- Desktop Table View (hidden on mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-black uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3 px-4">Nama Paket CBT</th>
                                <th class="py-3 px-4">Waktu Ujian</th>
                                <th class="py-3 px-4">Skor Diperoleh</th>
                                <th class="py-3 px-4">Integritas Ujian</th>
                                <th class="py-3 px-4">Hasil Kelulusan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            <tr v-for="ses in recentSessions" :key="ses.id" class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ ses.exam?.title || 'Simulasi Ujian CBT' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 font-mono">
                                    {{ new Date(ses.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-black text-sm text-slate-900 font-mono">{{ ses.total_score }}</span>
                                    <span class="text-slate-400 text-[10px]"> / {{ ses.exam?.max_score || 180 }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span v-if="ses.violation_count === 0"
                                        class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                        🟢 Tertib (0 Pelanggaran)
                                    </span>
                                    <span v-else
                                        class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                                        ⚠️ {{ ses.violation_count }}x Pindah Tab
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span v-if="ses.is_disqualified"
                                        class="px-2.5 py-1 rounded-full bg-red-100 text-red-800 font-black text-[10px] uppercase">
                                        ⛔ Didiskualifikasi
                                    </span>
                                    <span v-else-if="ses.is_passed"
                                        class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-black text-[10px] uppercase">
                                        🎉 LULUS (合格)
                                    </span>
                                    <span v-else-if="ses.status === 'in_progress'"
                                        class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 font-black text-[10px] uppercase animate-pulse">
                                        ⏳ Sedang Berlangsung
                                    </span>
                                    <span v-else
                                        class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-black text-[10px] uppercase">
                                        Belum Lulus
                                    </span>
                                </td>
                            </tr>

                            <tr v-if="recentSessions.length === 0">
                                <td colspan="5" class="py-10 text-center text-slate-400 text-xs">
                                    <Award class="w-8 h-8 text-slate-200 mx-auto mb-2" />
                                    Anda belum pernah mengikuti ujian CBT. Pilih paket ujian di atas untuk memulai.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card Feed View (md:hidden) -->
                <div class="block md:hidden divide-y divide-slate-100">
                    <div v-for="ses in recentSessions" :key="'mob-' + ses.id" class="py-3.5 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="font-bold text-sm text-slate-900 leading-snug">
                                {{ ses.exam?.title || 'Simulasi Ujian CBT' }}
                            </h4>
                            <span v-if="ses.is_passed"
                                class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-black text-[10px] uppercase shrink-0">
                                🎉 LULUS
                            </span>
                            <span v-else-if="ses.is_disqualified"
                                class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-black text-[10px] uppercase shrink-0">
                                ⛔ DISKUALIFIKASI
                            </span>
                            <span v-else
                                class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-black text-[10px] uppercase shrink-0">
                                BELUM LULUS
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-1">
                            <div class="flex items-center gap-1.5">
                                <span class="text-slate-500 text-[11px]">Skor:</span>
                                <span class="font-black font-mono text-sm text-slate-950">{{ ses.total_score }}</span>
                                <span class="text-slate-400 text-[10px]">/ {{ ses.exam?.max_score || 180 }} pts</span>
                            </div>

                            <span class="text-[10px] text-slate-400 font-mono">
                                {{ new Date(ses.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }) }}
                            </span>
                        </div>

                        <div class="pt-0.5">
                            <span v-if="ses.violation_count === 0"
                                class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200">
                                🟢 Tertib (0 Pelanggaran)
                            </span>
                            <span v-else
                                class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200">
                                ⚠️ {{ ses.violation_count }}x Pindah Tab
                            </span>
                        </div>
                    </div>

                    <div v-if="recentSessions.length === 0" class="py-8 text-center text-slate-400 text-xs">
                        <Award class="w-8 h-8 text-slate-200 mx-auto mb-2" />
                        Anda belum pernah mengikuti ujian CBT. Pilih paket ujian di atas untuk memulai.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { confirmDialog } from '@/Utils/alert';
import { Clock, Award, ShieldAlert } from 'lucide-vue-next';

const props = defineProps({
    batch: Object,
    assignedExams: Array,
    recentSessions: Array,
    stats: Object,
});

const startExam = (exam) => {
    confirmDialog(
        `Mulai Ujian CBT: ${exam.title}?`,
        `⏱️ Durasi: ${exam.duration_minutes} Menit.\n🖥️ Layar akan otomatis masuk ke MODE FULLSCREEN.\n⚠️ PERINGATAN: Dilarang berpindah tab browser, membuka AI, atau meminimalkan HP.`
    ).then((result) => {
        if (result.isConfirmed) {
            router.post(route('siswa.cbt.start', exam.id));
        }
    });
};
</script>
