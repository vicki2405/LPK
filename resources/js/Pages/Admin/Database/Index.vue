<template>
    <Head :title="isJapanese ? 'データベース - 正夢' : 'Database - LPK Masayume'" />

    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto space-y-6 animate-fade-in pb-16">
            <!-- ================= HEADER SECTION ================= -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 font-jp">
                        <Database class="w-6 h-6 text-slate-800" />
                        <span>Database</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1 font-medium font-jp">
                        {{ isJapanese 
                            ? 'システム全体のデータベース（.sql）のバックアップ保存および復元を管理します。' 
                            : 'Kelola pencadangan (backup) dan pemulihan (restore) basis data sistem LPK.' 
                        }}
                    </p>
                </div>

                <!-- Status Badge Ringkas -->
                <div class="flex items-center gap-2 self-start sm:self-auto font-mono text-xs">
                    <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-bold border border-slate-200/80">
                        {{ databaseStats.name }} · {{ databaseStats.total_tables }} Tabel · {{ databaseStats.size_formatted }}
                    </span>
                </div>
            </div>

            <!-- ================= FLASH MESSAGES ================= -->
            <div v-if="$page.props.flash?.success" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-xs">
                <CheckCircle2 class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                <p class="text-xs text-emerald-800 font-semibold">{{ $page.props.flash.success }}</p>
            </div>

            <div v-if="$page.props.flash?.error" class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3 shadow-xs">
                <AlertTriangle class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
                <p class="text-xs text-rose-800 font-semibold">{{ $page.props.flash.error }}</p>
            </div>

            <!-- ================= DUA PANEL BERDAMPINGAN: BACKUP & RESTORE ================= -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
                
                <!-- 1. PANEL BACKUP -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-7 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center font-bold">
                                <Download class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="text-base font-black text-slate-900 font-jp">
                                    {{ isJapanese ? 'バックアップ (エクスポート)' : 'Backup Database' }}
                                </h2>
                                <span class="text-[11px] text-slate-400 font-medium font-jp">
                                    {{ isJapanese ? '全データのSQLファイル保存' : 'Unduh cadangan data lengkap' }}
                                </span>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            {{ isJapanese 
                                ? '全テーブルの構造とデータ（アカウント、カリキュラム、CBT試験、成績など）を .sql ファイルとして書き出します。' 
                                : 'Mengekstrak seluruh struktur dan isi data (akun, kurikulum LMS, bank soal, jadwal ujian CBT, dan hasil nilai) ke dalam berkas .sql.' 
                            }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <a 
                            :href="route('admin.database.download')" 
                            class="w-full px-5 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer group"
                        >
                            <Download class="w-4 h-4 text-emerald-400 group-hover:-translate-y-0.5 transition-transform" />
                            <span>{{ isJapanese ? 'バックアップをダウンロード (.sql)' : 'Download Backup (.sql)' }}</span>
                        </a>
                    </div>
                </div>

                <!-- 2. PANEL RESTORE -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-7 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center font-bold">
                                <UploadCloud class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="text-base font-black text-slate-900 font-jp">
                                    {{ isJapanese ? '復元 (リストア)' : 'Restore Database' }}
                                </h2>
                                <span class="text-[11px] text-rose-600 font-bold font-jp">
                                    {{ isJapanese ? '⚠️ 既存データを上書き' : '⚠️ Menimpa data berjalan' }}
                                </span>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            {{ isJapanese 
                                ? '以前のバックアップファイル（.sql）を読み込み、データベースをその時点の状態へ復元します。' 
                                : 'Unggah berkas backup (.sql) untuk mengembalikan seluruh tabel dan data sistem ke kondisi saat file dibuat.' 
                            }}
                        </p>

                        <!-- Form Restore Sederhana -->
                        <form @submit.prevent="confirmAndSubmitRestore" id="restoreFormElement" class="space-y-3.5">
                            <!-- File Selector -->
                            <div>
                                <input 
                                    type="file" 
                                    ref="fileInputRef" 
                                    @change="handleFileChange" 
                                    accept=".sql,.txt" 
                                    class="hidden" 
                                />

                                <div 
                                    @click="triggerFileInput"
                                    class="border border-dashed rounded-2xl p-4 text-center transition-all cursor-pointer flex items-center justify-center gap-3 group"
                                    :class="selectedFileName ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-300 hover:border-slate-400 bg-slate-50/70'"
                                >
                                    <FileText v-if="selectedFileName" class="w-5 h-5 text-emerald-600 shrink-0" />
                                    <UploadCloud v-else class="w-5 h-5 text-slate-400 shrink-0 group-hover:scale-110 transition-transform" />

                                    <div class="text-left min-w-0">
                                        <span v-if="selectedFileName" class="text-xs font-black text-slate-900 block truncate">
                                            {{ selectedFileName }} ({{ selectedFileSize }})
                                        </span>
                                        <span v-else class="text-xs font-bold text-slate-700 block">
                                            Pilih berkas backup (.sql)
                                        </span>
                                        <span class="text-[10px] text-slate-400 block">Maksimal 100MB</span>
                                    </div>
                                </div>

                                <div v-if="restoreForm.errors.backup_file" class="text-[11px] text-rose-600 font-bold mt-1">
                                    {{ restoreForm.errors.backup_file }}
                                </div>
                            </div>

                            <!-- Input Konfirmasi Singkat -->
                            <div>
                                <input 
                                    type="text" 
                                    v-model="restoreForm.confirmation"
                                    placeholder="Ketik RESTORE untuk konfirmasi"
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 uppercase tracking-wider"
                                />
                                <div v-if="restoreForm.errors.confirmation" class="text-[11px] text-rose-600 font-bold mt-1">
                                    {{ restoreForm.errors.confirmation }}
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <button 
                            type="button"
                            @click="confirmAndSubmitRestore"
                            :disabled="!isRestoreReady || restoreForm.processing"
                            class="w-full px-5 py-3 rounded-2xl text-xs font-black transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm"
                            :class="isRestoreReady && !restoreForm.processing 
                                ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-rose-600/20' 
                                : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                        >
                            <RefreshCw v-if="restoreForm.processing" class="w-4 h-4 animate-spin" />
                            <ShieldAlert v-else class="w-4 h-4" />
                            <span>
                                {{ restoreForm.processing 
                                    ? (isJapanese ? '復元中...' : 'Memproses Restore...') 
                                    : (isJapanese ? 'データベースを復元' : 'Restore Database') 
                                }}
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import Swal from 'sweetalert2';
import { 
    Database, Download, UploadCloud, AlertTriangle, CheckCircle2, 
    RefreshCw, FileText, ShieldAlert 
} from 'lucide-vue-next';

defineProps({
    databaseStats: {
        type: Object,
        required: true,
    },
});

const { isJapanese } = useLang();

// State File Upload
const fileInputRef = ref(null);
const selectedFileName = ref('');
const selectedFileSize = ref('');

const restoreForm = useForm({
    backup_file: null,
    confirmation: '',
});

const triggerFileInput = () => {
    if (fileInputRef.value) {
        fileInputRef.value.click();
    }
};

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        restoreForm.backup_file = file;
        selectedFileName.value = file.name;
        selectedFileSize.value = formatFileSize(file.size);
    } else {
        restoreForm.backup_file = null;
        selectedFileName.value = '';
        selectedFileSize.value = '';
    }
};

const formatFileSize = (bytes) => {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const isRestoreReady = computed(() => {
    return restoreForm.backup_file !== null && restoreForm.confirmation.trim().toUpperCase() === 'RESTORE';
});

const confirmAndSubmitRestore = () => {
    if (!isRestoreReady.value) return;

    Swal.fire({
        title: isJapanese.value ? 'データベース復元の確認' : 'Konfirmasi Restore Database',
        html: `
            <div class="text-left text-xs text-slate-700 space-y-2">
                <p class="font-bold text-rose-600">
                    ⚠️ TINDAKAN INI AKAN MENIMPA DATA BERJALAN DENGAN BERKAS BACKUP!
                </p>
                <p>
                    Berkas: <strong class="font-mono text-slate-900">${selectedFileName.value}</strong>
                </p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: isJapanese.value ? 'はい、復元を実行します' : 'Ya, Lakukan Restore',
        cancelButtonText: isJapanese.value ? 'Batal' : 'Batal',
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
    }).then((result) => {
        if (result.isConfirmed) {
            restoreForm.post(route('admin.database.restore'), {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    restoreForm.reset();
                    selectedFileName.value = '';
                    selectedFileSize.value = '';
                    if (fileInputRef.value) fileInputRef.value.value = '';
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Database berhasil dipulihkan secara sempurna.',
                        icon: 'success',
                        confirmButtonColor: '#0F172A',
                    });
                },
                onError: (err) => {
                    Swal.fire({
                        title: 'Gagal!',
                        text: Object.values(err)[0] || 'Terjadi kesalahan saat memulihkan database.',
                        icon: 'error',
                        confirmButtonColor: '#DC2626',
                    });
                }
            });
        }
    });
};
</script>
