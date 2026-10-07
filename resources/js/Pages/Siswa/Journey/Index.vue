<template>
    <Head title="Progres Penyaluran & Visa Kerja Jepang - Masayume" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-12">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold mb-3 border border-emerald-500/30">
                        <CheckCircle2 class="w-3.5 h-3.5" />
                        <span>Roadmap Penyaluran Kerja ke Jepang (Tokutei Ginou / Magang)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Progres Penyaluran, COE & Visa Jepang
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-2 max-w-xl leading-relaxed">
                        Pantau setiap tahapan perjalanan Anda mulai dari pelatihan bahasa, ujian sertifikasi, matching wawancara perusahaan Jepang, hingga penerbitan visa dan tiket pesawat.
                    </p>
                </div>

                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 text-left shrink-0">
                    <span class="text-[10px] text-slate-300 uppercase tracking-wider block font-bold">Angkatan & Kelas</span>
                    <span class="text-sm font-black text-white block mt-0.5">{{ batch?.name || 'Reguler N4 Camp' }}</span>
                    <span class="text-[11px] text-emerald-300 font-bold block mt-1">Sektor: {{ user.target_job_sector || 'Kaigo / Pengolahan Makanan' }}</span>
                </div>
            </div>

            <!-- 7 STAGES PIPELINE TRACKER -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <CheckCircle2 class="w-5 h-5 text-emerald-600" />
                    7 Milestone Tahapan Penyaluran Trainee
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div 
                        v-for="(stage, idx) in pipelineStages" 
                        :key="stage.id"
                        class="p-5 rounded-2xl border transition-all flex flex-col justify-between"
                        :class="idx <= 1 
                            ? 'bg-emerald-50/60 border-emerald-200 shadow-2xs' 
                            : (idx === 2 ? 'bg-amber-50/60 border-amber-300 shadow-xs' : 'bg-slate-50 border-slate-200 opacity-70')"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black"
                                    :class="idx <= 1 ? 'bg-emerald-600 text-white' : (idx === 2 ? 'bg-amber-500 text-white animate-pulse' : 'bg-slate-300 text-slate-700')">
                                    {{ idx + 1 }}
                                </span>
                                <span v-if="idx <= 1" class="text-[10px] font-black text-emerald-700 uppercase bg-emerald-100 px-2 py-0.5 rounded">
                                    ✓ Selesai
                                </span>
                                <span v-else-if="idx === 2" class="text-[10px] font-black text-amber-700 uppercase bg-amber-100 px-2 py-0.5 rounded">
                                    ● Berjalan
                                </span>
                                <span v-else class="text-[10px] font-bold text-slate-400 uppercase">
                                    Menunggu
                                </span>
                            </div>

                            <h3 class="text-sm font-black text-slate-900">{{ stage.name }}</h3>
                            <p v-if="stage.name_jp" class="text-xs text-slate-500 font-jp font-semibold mt-0.5">{{ stage.name_jp }}</p>
                            <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                                {{ stage.description || 'Tahapan evaluasi dan persiapan dokumen keberangkatan.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DOCUMENT & VISA STATUS CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                <!-- MCU Status Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-400 uppercase">Status Kesehatan</span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase"
                                :class="user.mcu_status === 'fit' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
                                {{ user.mcu_status || 'Dalam Proses' }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Medical Check Up (MCU)</h3>
                        <p class="text-xs text-slate-500 mt-1">Pemeriksaan kesehatan standar Kumiai & RS rujukan resmi Jepang.</p>
                    </div>
                </div>

                <!-- COE Status Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-400 uppercase">Imigrasi Jepang</span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase"
                                :class="user.coe_status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700'">
                                {{ user.coe_status || 'Menunggu Pengajuan' }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Certificate of Eligibility (COE)</h3>
                        <p class="text-xs text-slate-500 mt-1">Surat izin tinggal resmi yang diterbitkan oleh Imigrasi Jepang (出入国在留管理庁).</p>
                    </div>
                </div>

                <!-- Visa Status Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-400 uppercase">Kedutaan Besar</span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase"
                                :class="user.visa_status === 'issued' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700'">
                                {{ user.visa_status || 'Menunggu COE' }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Visa Kerja & Tiket Terbang</h3>
                        <p class="text-xs text-slate-500 mt-1">Penerbitan visa Tokutei Ginou / Ginou Jisshuusei oleh Kedutaan Jepang di Indonesia.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CheckCircle2 } from 'lucide-vue-next';

const props = defineProps({
    user: Object,
    batch: Object,
    pipelineStages: Array,
});
</script>
