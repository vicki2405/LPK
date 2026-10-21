<template>
    <Head :title="isJapanese ? '指導教員ダッシュボード - 正夢' : 'Dashboard Sensei - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-8 animate-fade-in pb-12">
            <!-- ================= SENSEI HERO BANNER ================= -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white p-6 sm:p-8 shadow-xl border border-slate-800">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 backdrop-blur-md text-amber-300 text-xs font-bold mb-3 border border-amber-500/30"
                            :class="{ 'font-jp': isJapanese }">
                            <span>{{ isJapanese ? '👨‍🏫 指導教員スタジオ' : '👨‍🏫 Portal Instruktur Sensei' }}</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight flex items-center gap-3"
                            :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese 
                                ? ($page.props.auth.user.name.includes('先生') ? `${$page.props.auth.user.name}、こんにちは！🌸` : `${$page.props.auth.user.name} 先生、こんにちは！🌸`) 
                                : ($page.props.auth.user.name.toLowerCase().includes('sensei') ? `Selamat Datang, ${$page.props.auth.user.name}! 🌸` : `Selamat Datang, ${$page.props.auth.user.name} Sensei! 🌸`) 
                            }}
                        </h1>
                        <p class="text-slate-200 text-xs sm:text-sm mt-1.5 max-w-2xl leading-relaxed font-medium"
                            :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese 
                                ? '課別教材の作成、ルビ付き問題バンク、JLPT N4模擬試験管理、および実習生の学習進捗・成績確認を一元管理します。' 
                                : 'Pusat kendali pembuatan materi bab, penyusunan bank soal Furigana, manajemen ujian simulasi CBT N4, dan pemantauan nilai ketuntasan siswa.' 
                            }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                        <Link :href="route('sensei.questions.index')" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 hover:shadow-blue-500/35 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer group">
                            <PlusCircle class="w-4 h-4 group-hover:rotate-90 transition-transform duration-200" />
                            <span>{{ isJapanese ? '+ 新規問題作成' : '+ Buat Paket Soal' }}</span>
                        </Link>
                        <Link :href="route('sensei.exams.index')" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/30 hover:shadow-japan-red/45 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer group">
                            <Clock class="w-4 h-4" />
                            <span>{{ isJapanese ? '+ CBT試験を作成' : '+ Buat Paket CBT' }}</span>
                        </Link>
                        <Link :href="route('sensei.lms.index')" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold backdrop-blur-md border border-white/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <BookPlus class="w-4 h-4" />
                            <span>{{ isJapanese ? '+ 課の教材を作成' : '+ Kelola Materi Bab' }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Ambient Glow -->
                <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <!-- ================= ANTI-CHEAT CBT WARNING ALERT ================= -->
            <div v-if="violationSessions && violationSessions.length > 0" 
                class="p-5 rounded-3xl bg-gradient-to-r from-rose-950 via-slate-900 to-rose-950 text-white border border-rose-600/50 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4 animate-pulse">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-rose-600/30 border border-rose-500/50 text-rose-300 flex items-center justify-center shrink-0">
                        <ShieldAlert class="w-6 h-6 text-rose-400" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black uppercase">
                                {{ isJapanese ? '不正警告検知' : 'Peringatan Kecurangan Terdeteksi' }}
                            </span>
                            <span class="text-xs text-rose-300 font-bold font-mono">
                                {{ violationSessions.length }} {{ isJapanese ? '名が違反' : 'Peserta Ujian' }}
                            </span>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-white mt-1">
                            {{ isJapanese 
                                ? 'CBT試験中にタブ切り替えやAI検索等の不正行為が検知されました。' 
                                : 'Terdeteksi siswa yang berpindah tab browser / mencari jawaban AI saat ujian CBT.' 
                            }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-rose-200">
                            <span v-for="vs in violationSessions" :key="vs.id" class="px-2 py-0.5 rounded-md bg-white/10 text-rose-100 text-[11px] font-semibold">
                                👤 {{ vs.user?.name }}: {{ vs.is_disqualified ? '⛔ Didiskualifikasi' : `⚠️ ${vs.violation_count}x Keluar Tab` }}
                            </span>
                        </div>
                    </div>
                </div>

                <Link :href="route('sensei.exams.index')" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-md shadow-rose-600/30 flex items-center gap-2 shrink-0 self-start sm:self-auto cursor-pointer">
                    <ShieldAlert class="w-4 h-4" />
                    <span>{{ isJapanese ? '監視パネルを開く' : 'Buka Panel Pengawas' }}</span>
                </Link>
            </div>

            <!-- ================= SENSEI 4 STATS (CLICKABLE) ================= -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- 1. LMS Materi Bab -->
                <Link :href="route('sensei.lms.index')" 
                    class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-md hover:border-blue-300 hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-200 block group cursor-pointer">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider group-hover:text-blue-600 transition-colors">
                            {{ isJapanese ? '課別教材 (LMS)' : 'LMS Materi Bab' }}
                        </span>
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center group-hover:scale-110 transition-transform font-bold shadow-2xs">
                            <BookOpen class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-950 font-mono">{{ stats.total_chapters ?? 0 }}</span>
                        <span class="text-xs text-slate-800 font-bold">
                            {{ isJapanese ? '課 登録済' : 'Bab Materi' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-medium mt-1.5 flex items-center justify-between">
                        <span>{{ isJapanese ? 'みんなの日本語・特定技能' : 'Minna no Nihongo & SSW' }}</span>
                        <ArrowRight class="w-3.5 h-3.5 text-slate-300 group-hover:text-blue-600 group-hover:translate-x-1 transition-all" />
                    </p>
                </Link>

                <!-- 2. Bank Soal N4 -->
                <Link :href="route('sensei.questions.index')" 
                    class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-md hover:border-amber-300 hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-200 block group cursor-pointer">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider group-hover:text-amber-600 transition-colors">
                            {{ isJapanese ? '問題バンク' : 'Bank Soal' }}
                        </span>
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center group-hover:scale-110 transition-transform font-bold shadow-2xs">
                            <HelpCircle class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-950 font-mono">{{ stats.total_questions ?? 0 }}</span>
                        <span class="text-xs text-slate-800 font-bold">
                            {{ isJapanese ? '問 登録済' : 'Butir Soal' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-medium mt-1.5 flex items-center justify-between">
                        <span>{{ isJapanese ? 'ルビ表記＆聴解音声対応' : 'Furigana & Audio Choukai' }}</span>
                        <ArrowRight class="w-3.5 h-3.5 text-slate-300 group-hover:text-amber-600 group-hover:translate-x-1 transition-all" />
                    </p>
                </Link>

                <!-- 3. CBT Ujian Aktif -->
                <Link :href="route('sensei.exams.index')" 
                    class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-md hover:border-red-300 hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-200 block group cursor-pointer">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider group-hover:text-japan-red transition-colors">
                            {{ isJapanese ? 'CBT試験' : 'Ujian & CBT' }}
                        </span>
                        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-japan-red flex items-center justify-center group-hover:scale-110 transition-transform font-bold shadow-2xs">
                            <Clock class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-japan-red font-mono">{{ stats.active_exams ?? 0 }}</span>
                        <span class="text-xs text-rose-900 bg-rose-100 px-2 py-0.5 rounded font-black">
                            {{ isJapanese ? '配信中' : 'Paket Aktif' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-medium mt-1.5 flex items-center justify-between">
                        <span>{{ isJapanese ? 'JLPT N4 / JFT公式模擬' : 'Simulasi N4 & JFT-Basic' }}</span>
                        <ArrowRight class="w-3.5 h-3.5 text-slate-300 group-hover:text-japan-red group-hover:translate-x-1 transition-all" />
                    </p>
                </Link>

                <!-- 4. Rapor & Siswa Binaan -->
                <Link :href="route('sensei.grades.index')" 
                    class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-md hover:border-emerald-300 hover:-translate-y-0.5 active:scale-[0.99] transition-all duration-200 block group cursor-pointer">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider group-hover:text-emerald-600 transition-colors">
                            {{ isJapanese ? '成績・習熟度' : 'Rapor & Siswa' }}
                        </span>
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center group-hover:scale-110 transition-transform font-bold shadow-2xs">
                            <Award class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-black text-slate-950 font-mono">{{ stats.total_students_guided ?? 0 }}</span>
                        <span class="text-xs text-emerald-800 font-bold">
                            {{ isJapanese ? '名 受講中' : 'Siswa Trainee' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 font-medium mt-1.5 flex items-center justify-between">
                        <span class="truncate">{{ batches && batches.length > 0 ? (batches.length === 1 ? batches[0].name : `${batches.length} ${isJapanese ? 'クラス' : 'Kelas Aktif'}`) : (isJapanese ? '担当クラス一覧' : 'Daftar Kelas Binaan') }}</span>
                        <ArrowRight class="w-3.5 h-3.5 text-slate-300 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all shrink-0" />
                    </p>
                </Link>
            </div>

            <!-- ================= CBT CONTROLLER & WEAKNESS HEATMAP ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Live CBT Exam Controller -->
                <div class="lg:col-span-2 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-black text-slate-950 flex items-center gap-2" :class="{ 'font-jp': isJapanese }">
                                <Clock class="w-5 h-5 text-japan-red" />
                                {{ isJapanese ? '実施中のCBT試験一覧' : 'Paket Ujian CBT Sedang Berjalan' }}
                            </h3>
                            <p class="text-xs text-slate-700 font-medium mt-0.5">
                                {{ isJapanese ? '実習生の受験進捗と結果の確認・公開管理' : 'Pantau status pengerjaan siswa dan rilis hasil ujian' }}
                            </p>
                        </div>
                        <Link :href="route('sensei.exams.index')" class="text-xs font-bold text-japan-red px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 transition-colors">
                            {{ isJapanese ? '+ 新規試験作成' : '+ Buat CBT Baru' }}
                        </Link>
                    </div>

                    <div v-if="activeExams && activeExams.length > 0" class="space-y-4">
                        <div v-for="exam in activeExams" :key="exam.id" 
                            class="p-5 rounded-2xl border border-slate-200 bg-slate-50/70 hover:border-japan-red/40 transition-all">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-black px-2 py-0.5 rounded bg-japan-red text-white">
                                            {{ exam.level }}
                                        </span>
                                        <h4 class="font-bold text-sm text-slate-950">{{ exam.title }}</h4>
                                    </div>
                                    <p class="text-xs text-slate-700 font-semibold mt-1.5 flex items-center gap-4">
                                        <span>⏱️ {{ isJapanese ? '制限時間' : 'Durasi' }}: {{ exam.duration_minutes }} {{ isJapanese ? '分' : 'Menit' }}</span>
                                        <span>🎯 {{ isJapanese ? '合格基準点' : 'Passing Grade' }}: {{ exam.passing_score }} / {{ exam.max_score }}</span>
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <Link :href="route('sensei.grades.index')" class="px-3.5 py-2 rounded-xl bg-white text-xs font-bold text-slate-800 border border-slate-300 hover:bg-slate-50 hover:border-slate-400 active:scale-[0.98] transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer">
                                        <Eye class="w-3.5 h-3.5 text-slate-500" />
                                        <span>{{ isJapanese ? '成績確認' : 'Lihat Nilai' }}</span>
                                    </Link>
                                    <Link :href="route('sensei.exams.index')" class="px-3.5 py-2 rounded-xl bg-slate-950 text-xs font-bold text-white hover:bg-slate-800 active:scale-[0.98] transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer">
                                        <Settings2 class="w-3.5 h-3.5 text-slate-300" />
                                        <span>{{ isJapanese ? '試験管理' : 'Kelola CBT' }}</span>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clean Empty State if no active exams -->
                    <div v-else class="py-12 text-center text-slate-500 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <Clock class="w-10 h-10 text-slate-300 mx-auto mb-2" />
                        <p class="font-bold text-xs text-slate-800">
                            {{ isJapanese ? '配信中のCBT試験パッケージはまだありません。' : 'Belum ada paket ujian CBT yang aktif ditayangkan.' }}
                        </p>
                        <p class="text-[11px] text-slate-500 mt-0.5 mb-3">
                            {{ isJapanese ? '下のボタンをクリックして、CBT模擬試験を作成・公開してください。' : 'Klik tombol di bawah untuk membuat dan menerbitkan jadwal ujian simulasi CBT.' }}
                        </p>
                        <Link :href="route('sensei.exams.index')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-japan-red text-white text-xs font-bold shadow-sm hover:bg-red-700 transition-colors">
                            <PlusCircle class="w-3.5 h-3.5" />
                            <span>{{ isJapanese ? '+ 新規CBT試験を作成' : '+ Buat Paket CBT Baru' }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Weakness Heatmap (Real Database CBT Analytics) -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-black text-slate-950 flex items-center gap-2" :class="{ 'font-jp': isJapanese }">
                                    🧠 {{ isJapanese ? '苦手分野ヒートマップ' : 'Peta Kelemahan Siswa' }}
                                </h3>
                                <p class="text-[11px] text-slate-700 font-medium">
                                    {{ isJapanese ? '実習生のCBT誤答率分析（DB集計）' : 'Analisis tingkat kesalahan soal CBT siswa secara real-time' }}
                                </p>
                            </div>
                        </div>

                        <!-- Real Database Heatmap List -->
                        <div v-if="weaknessHeatmap && weaknessHeatmap.length > 0" class="space-y-4 mt-6">
                            <div v-for="(item, idx) in weaknessHeatmap" :key="idx" class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-slate-900">{{ item.topic }}</span>
                                    <span :class="item.status === 'critical' ? 'text-rose-700 font-black' : item.status === 'warning' ? 'text-amber-800 font-black' : 'text-emerald-800 font-black'">
                                        {{ item.wrong_percentage }}% {{ isJapanese ? '誤答' : 'Salah' }} ({{ item.wrong_count }}/{{ item.total_answered }})
                                    </span>
                                </div>
                                <div class="h-2.5 w-full bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                        :style="{ width: item.wrong_percentage + '%' }"
                                        :class="item.status === 'critical' ? 'bg-rose-600' : item.status === 'warning' ? 'bg-amber-500' : 'bg-emerald-500'">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Clean Empty State if no student has completed CBT yet -->
                        <div v-else class="py-10 text-center text-slate-500 bg-slate-50/60 rounded-2xl border border-dashed border-slate-200 mt-4 px-4">
                            <HelpCircle class="w-9 h-9 text-slate-300 mx-auto mb-2" />
                            <p class="font-bold text-xs text-slate-800">
                                {{ isJapanese ? 'CBT受験データがまだありません' : 'Belum Ada Data Ujian CBT Siswa' }}
                            </p>
                            <p class="text-[11px] text-slate-500 mt-1 max-w-xs mx-auto leading-relaxed">
                                {{ isJapanese ? '実習生がCBT試験を受験・提出すると、カテゴリー別の誤答率が自動でリアルタイム集計されます。' : 'Tingkat kelemahan materi akan otomatis dihitung secara real-time dari database setelah siswa mengerjakan dan mengumpulkan ujian CBT.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Dynamic Advice Box (Generated from real data) -->
                    <div v-if="dynamicAdvice" class="mt-6 p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-950 leading-relaxed font-jp font-medium">
                        💡 <strong>{{ isJapanese ? '指導アドバイス:' : 'Saran Sensei:' }}</strong> 
                        {{ dynamicAdvice }}
                    </div>
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
    Clock, 
    BookOpen, 
    Award, 
    PlusCircle, 
    BookPlus, 
    HelpCircle,
    ShieldAlert,
    Eye,
    Settings2,
    ArrowRight
} from 'lucide-vue-next';

defineProps({
    stats: Object,
    batches: Array,
    activeExams: Array,
    weaknessHeatmap: Array,
    dynamicAdvice: String,
    violationSessions: Array,
});

const { isJapanese } = useLang();
</script>
