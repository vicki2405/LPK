<template>
    <Head :title="isJapanese ? '管理者ダッシュボード - 正夢' : 'Dashboard Admin - LPK Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-8 animate-fade-in pb-12">
            <!-- ================= HERO BANNER ================= -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white p-6 sm:p-8 shadow-xl border border-slate-800">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-amber-300 text-xs font-bold mb-3 border border-white/20"
                            :class="{ 'font-jp': isJapanese }">
                            <span>{{ isJapanese ? '👑 LPK運営管理本部' : '👑 Portal Manajemen LPK' }}</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight"
                            :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese ? '管理者様、お疲れ様です！' : 'Selamat Datang, Admin LPK Masayume!' }}
                        </h1>
                        <p class="text-slate-200 text-xs sm:text-sm mt-1.5 max-w-2xl leading-relaxed font-medium"
                            :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese 
                                ? '全クラスの運営状況、N4合格率、および日本就労送り出しパイプラインをリアルタイムで統括・管理します。' 
                                : 'Pantau seluruh kegiatan operasional, progres kelulusan N4 siswa, dan tahapan penyaluran tenaga kerja ke Jepang secara real-time.' 
                            }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <Link :href="route('admin.students.index')" class="px-4 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-lg shadow-japan-red/30 transition-all flex items-center gap-2 group cursor-pointer">
                            <UserPlus class="w-4 h-4 group-hover:scale-110 transition-transform" />
                            <span>{{ isJapanese ? '+ 実習生を新規登録' : '+ Daftarkan Siswa' }}</span>
                        </Link>
                        <Link :href="route('admin.master.batches.index')" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold backdrop-blur-md border border-white/20 transition-all flex items-center gap-2 cursor-pointer">
                            <PlusCircle class="w-4 h-4" />
                            <span>{{ isJapanese ? '+ クラスを開設' : '+ Buka Batch Baru' }}</span>
                        </Link>
                        <Link :href="route('admin.database.index')" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold backdrop-blur-md border border-white/20 transition-all flex items-center gap-2 cursor-pointer" title="Kelola Database">
                            <Database class="w-4 h-4 text-emerald-400" />
                            <span>{{ isJapanese ? '💾 データベース' : '💾 Database' }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Ambient Glow Behind -->
                <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <!-- ================= METRIC CARDS (BENTO GRID) ================= -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Total Siswa -->
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider">
                            {{ isJapanese ? '在籍実習生' : 'Siswa Aktif' }}
                        </span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center group-hover:scale-110 transition-transform font-bold">
                            <Users class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-950">{{ stats?.total_siswa || 0 }}</span>
                        <span class="text-xs text-slate-800 font-bold">
                            {{ isJapanese ? '名' : 'Siswa' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-700 font-medium mt-1">
                        {{ isJapanese ? `${stats?.total_batches || 0} クラスに配属中` : `Tersebar di ${stats?.total_batches || 0} kelas pelatihan` }}
                    </p>
                </div>

                <!-- Kelas / Batches -->
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider">
                            {{ isJapanese ? '開設クラス数' : 'Kelas Aktif' }}
                        </span>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center group-hover:scale-110 transition-transform font-bold">
                            <GraduationCap class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-950">{{ stats?.total_batches || 0 }}</span>
                        <span class="text-xs text-slate-800 font-bold">
                            {{ isJapanese ? 'クラス' : 'Angkatan' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-700 font-medium mt-1">
                        {{ isJapanese ? `指導員 ${stats?.total_sensei || 0} 名在籍` : `Diampu oleh ${stats?.total_sensei || 0} instruktur` }}
                    </p>
                </div>

                <!-- Tingkat Kelulusan N4 -->
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider">
                            {{ isJapanese ? 'N4合格率' : 'Tingkat Lulus N4' }}
                        </span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center group-hover:scale-110 transition-transform font-bold">
                            <Award class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-emerald-700">{{ stats?.passing_rate || 0 }}%</span>
                        <span class="text-xs text-emerald-950 bg-emerald-100 px-2 py-0.5 rounded font-black">
                            {{ isJapanese ? '高水準' : 'Tinggi' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-700 font-medium mt-1">
                        {{ isJapanese ? 'JLPT N4 / JFT A2 合格基準' : 'Standar Kelulusan N4' }}
                    </p>
                </div>

                <!-- Siap Terbang -->
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider">
                            {{ isJapanese ? '出国準備完了' : 'Siap Berangkat' }}
                        </span>
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-japan-red flex items-center justify-center group-hover:scale-110 transition-transform font-bold">
                            <PlaneTakeoff class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-japan-red">{{ stats?.ready_to_fly || 0 }}</span>
                        <span class="text-xs text-slate-800 font-bold">
                            {{ isJapanese ? '名' : 'Siswa' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-700 font-medium mt-1">
                        {{ isJapanese ? '就労ビザ取得完了' : 'Visa & Tiket Siap' }}
                    </p>
                </div>
            </div>

            <!-- ================= TRAINEE PIPELINE FUNNEL TRACKER ================= -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-black text-slate-950 flex items-center gap-2" :class="{ 'font-jp': isJapanese }">
                            <PlaneTakeoff class="w-5 h-5 text-japan-red" />
                            {{ isJapanese ? '日本就労送り出しパイプライン' : 'Alur Tahapan Penyaluran Siswa' }}
                        </h2>
                        <p class="text-xs text-slate-700 font-medium mt-0.5">
                            {{ isJapanese ? '日本語教育から面接、在留資格、就労ビザ取得、出国までの各段階' : 'Pantauan tahapan siswa dari pelatihan awal hingga keberangkatan' }}
                        </p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 bg-slate-100 text-slate-800 rounded-full border border-slate-200">
                        {{ isJapanese ? 'リアルタイム更新' : 'Pembaruan Otomatis' }}
                    </span>
                </div>

                <!-- Funnel Steps -->
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center relative">
                        <span class="text-[10px] font-black text-slate-700 uppercase tracking-wider">
                            {{ isJapanese ? '1. 語学教育' : '1. Pelatihan' }}
                        </span>
                        <p class="text-2xl font-black text-slate-950 mt-1">{{ pipeline?.pelatihan || 0 }}</p>
                        <span class="text-[10px] text-slate-700 font-bold">{{ isJapanese ? '受講中' : 'Siswa di Kelas' }}</span>
                    </div>
                    <div class="bg-blue-50/70 p-4 rounded-2xl border border-blue-200 text-center relative">
                        <span class="text-[10px] font-black text-blue-800 uppercase tracking-wider">
                            {{ isJapanese ? '2. N4合格' : '2. Lulus N4' }}
                        </span>
                        <p class="text-2xl font-black text-blue-900 mt-1">{{ pipeline?.lulus_n4 || 0 }}</p>
                        <span class="text-[10px] text-blue-800 font-bold">{{ isJapanese ? '証明書取得' : 'Sertifikat' }}</span>
                    </div>
                    <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-200 text-center relative">
                        <span class="text-[10px] font-black text-amber-900 uppercase tracking-wider">
                            {{ isJapanese ? '3. 面接内定' : '3. Matching' }}
                        </span>
                        <p class="text-2xl font-black text-amber-950 mt-1">{{ pipeline?.matching_mensetsu || 0 }}</p>
                        <span class="text-[10px] text-amber-900 font-bold">{{ isJapanese ? '企業面接通過' : 'Wawancara' }}</span>
                    </div>
                    <div class="bg-purple-50/70 p-4 rounded-2xl border border-purple-200 text-center relative">
                        <span class="text-[10px] font-black text-purple-900 uppercase tracking-wider">
                            {{ isJapanese ? '4. 健康診断' : '4. Medis' }}
                        </span>
                        <p class="text-2xl font-black text-purple-950 mt-1">{{ pipeline?.proses_mcu || 0 }}</p>
                        <span class="text-[10px] text-purple-900 font-bold">{{ isJapanese ? '健康診断合格' : 'Lolos Medis' }}</span>
                    </div>
                    <div class="bg-indigo-50/70 p-4 rounded-2xl border border-indigo-200 text-center relative">
                        <span class="text-[10px] font-black text-indigo-900 uppercase tracking-wider">
                            {{ isJapanese ? '5. 資格申請' : '5. Berkas Imigrasi' }}
                        </span>
                        <p class="text-2xl font-black text-indigo-950 mt-1">{{ pipeline?.proses_coe || 0 }}</p>
                        <span class="text-[10px] text-indigo-900 font-bold">{{ isJapanese ? '審査中' : 'Proses Berkas' }}</span>
                    </div>
                    <div class="bg-emerald-50/70 p-4 rounded-2xl border border-emerald-200 text-center relative">
                        <span class="text-[10px] font-black text-emerald-900 uppercase tracking-wider">
                            {{ isJapanese ? '6. ビザ発給' : '6. Visa Kerja' }}
                        </span>
                        <p class="text-2xl font-black text-emerald-950 mt-1">{{ pipeline?.visa_ready || 0 }}</p>
                        <span class="text-[10px] text-emerald-900 font-bold">{{ isJapanese ? 'ビザ取得完了' : 'Visa Terbit' }}</span>
                    </div>
                    <div class="bg-rose-50 p-4 rounded-2xl border border-rose-200 text-center relative ring-2 ring-japan-red/30">
                        <span class="text-[10px] font-black text-japan-red uppercase tracking-wider">
                            {{ isJapanese ? '7. 出国完了' : '7. Berangkat' }}
                        </span>
                        <p class="text-2xl font-black text-japan-red mt-1">{{ pipeline?.terbang || 0 }}</p>
                        <span class="text-[10px] text-rose-900 font-bold">{{ isJapanese ? '現地就労開始' : 'Tiba di Tujuan' }}</span>
                    </div>
                </div>
            </div>

            <!-- ================= BATCHES MONITORING ================= -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-black text-slate-950 flex items-center gap-2" :class="{ 'font-jp': isJapanese }">
                        <GraduationCap class="w-5 h-5 text-slate-900" />
                        {{ isJapanese ? '開設クラス・期生の進行状況' : 'Monitoring Kelas & Angkatan Aktif' }}
                    </h3>
                    <Link :href="route('admin.master.batches.index')" class="text-xs font-bold text-japan-red hover:underline">
                        {{ isJapanese ? 'クラス一覧・管理 →' : 'Kelola Semua Angkatan →' }}
                    </Link>
                </div>

                <div v-if="batches && batches.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="batch in batches" :key="batch.id" 
                        class="p-4 rounded-2xl border border-slate-200 hover:border-slate-400 bg-slate-50/70 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-black text-japan-red px-2 py-0.5 rounded bg-red-50 border border-red-100">
                                    {{ batch.code }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-50 text-amber-900 border border-amber-200">
                                    {{ batch.job_sector || (isJapanese ? '全分野' : 'Multi-Sektor') }}
                                </span>
                            </div>
                            <h4 class="font-black text-sm text-slate-950">{{ batch.name }}</h4>
                            <p class="text-xs text-slate-700 font-semibold mt-1">
                                {{ isJapanese ? '目標' : 'Target' }}: <span class="font-black text-slate-950">{{ batch.target_level }}</span>
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-3 mt-3 border-t border-slate-200 text-xs">
                            <span class="text-slate-700 font-medium">{{ isJapanese ? '受講生' : 'Peserta' }}: <strong class="text-slate-950 font-black">{{ batch.siswas_count || 0 }} {{ isJapanese ? '名' : 'Siswa' }}</strong></span>
                            <span class="text-emerald-900 font-black bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                {{ isJapanese ? '進行中' : 'Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div v-else class="py-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <p class="text-xs font-bold text-slate-600">{{ isJapanese ? '登録されているクラスがありません。' : 'Belum ada kelas atau angkatan yang aktif.' }}</p>
                    <Link :href="route('admin.master.batches.index')" class="inline-block mt-2 text-xs font-bold text-japan-red hover:underline">
                        {{ isJapanese ? '+ 新しいクラスを作成する' : '+ Tambah Angkatan Baru' }}
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { 
    Users, GraduationCap, Award, PlaneTakeoff, UserPlus, 
    PlusCircle, Database
} from 'lucide-vue-next';

defineProps({
    stats: Object,
    pipeline: Object,
    batches: Array,
    recentUsers: Array,
});

const { isJapanese } = useLang();
</script>
