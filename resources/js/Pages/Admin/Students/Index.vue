<template>
    <Head :title="isJapanese ? '実習生データ管理 - 正夢' : 'Manajemen Data Siswa - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Top Header Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-japan-red text-xs font-semibold mb-2 border border-red-200/60">
                        <Users class="w-3.5 h-3.5" />
                        <span>{{ isJapanese ? '実習生・研修生' : 'Peserta Pelatihan LPK' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <GraduationCap class="w-6 h-6 text-japan-red" />
                        <span>{{ isJapanese ? '実習生データ管理' : 'Data Siswa Trainee' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ isJapanese ? '実習生の入校期生、特定技能分野、送り出し進捗、および健康診断状況の総合管理' : 'Kelola data siswa, angkatan, program penyaluran, sektor kerja, target level, dan status penyaluran.' }}
                    </p>
                </div>

                <button 
                    @click="openCreateModal"
                    class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/20 transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto"
                >
                    <UserPlus class="w-4 h-4" />
                    <span>{{ isJapanese ? '+ 実習生を新規登録' : '+ Tambah Siswa Baru' }}</span>
                </button>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    <!-- Search Bar -->
                    <div class="relative col-span-1 sm:col-span-2 md:col-span-2 lg:col-span-1">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                        <input 
                            type="text" 
                            v-model="searchQuery" 
                            @keyup.enter="applyFilter"
                            :placeholder="isJapanese ? '氏名・NIK・Email...' : 'Cari nama, NIK, email...'"
                            class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-japan-red focus:outline-none"
                        />
                    </div>

                    <!-- Filter Batch -->
                    <div>
                        <select 
                            v-model="selectedBatch" 
                            @change="applyFilter"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-japan-red focus:outline-none"
                        >
                            <option value="">{{ isJapanese ? 'すべての期生' : 'Semua Angkatan' }}</option>
                            <option v-for="b in batches" :key="b.id" :value="b.id">{{ b.name }}</option>
                        </select>
                    </div>

                    <!-- Filter Program -->
                    <div>
                        <select 
                            v-model="selectedProgram" 
                            @change="applyFilter"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-japan-red focus:outline-none"
                        >
                            <option value="">{{ isJapanese ? 'すべてのプログラム' : 'Semua Program' }}</option>
                            <option v-for="prog in programs" :key="prog.id" :value="prog.name">
                                {{ isJapanese ? (prog.name_jp || prog.name) : prog.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Filter Job Sector -->
                    <div>
                        <select 
                            v-model="selectedSector" 
                            @change="applyFilter"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-japan-red focus:outline-none"
                        >
                            <option value="">{{ isJapanese ? 'すべての業種' : 'Semua Sektor' }}</option>
                            <option v-for="sec in jobSectors" :key="sec.id" :value="sec.name">
                                {{ isJapanese ? (sec.name_jp || sec.name) : sec.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Filter Pipeline Stage -->
                    <div>
                        <select 
                            v-model="selectedStage" 
                            @change="applyFilter"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-japan-red focus:outline-none"
                        >
                            <option value="">{{ isJapanese ? 'すべての進捗' : 'Semua Tahapan' }}</option>
                            <option v-for="st in pipelineStages" :key="st.id" :value="st.code">
                                {{ st.order_step }}. {{ isJapanese ? (st.name_jp || st.name) : st.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button 
                        @click="resetFilter" 
                        class="text-xs font-semibold text-slate-500 hover:text-japan-red transition-colors flex items-center gap-1.5 cursor-pointer"
                    >
                        <RotateCcw class="w-3.5 h-3.5" />
                        <span>{{ isJapanese ? '条件をリセット' : 'Reset Filter' }}</span>
                    </button>
                </div>
            </div>

            <!-- Students Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6 w-14 text-center">No</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '実習生情報' : 'Nama Siswa' }}</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '所属期生・年度' : 'Angkatan & Tahun' }}</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '状態' : 'Status' }}</th>
                                <th class="py-3.5 px-4 text-center sm:pr-6">{{ isJapanese ? '操作' : 'Aksi' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(student, index) in students.data" :key="student.id" class="hover:bg-slate-50/60 transition-colors">
                                <!-- Nomor Urut -->
                                <td class="py-3.5 px-4 sm:px-6 text-center font-bold text-slate-400">
                                    {{ (students.from || 1) + index }}
                                </td>

                                <!-- Student Info -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-red-600 to-rose-700 text-white flex items-center justify-center font-bold text-xs shadow-sm shrink-0">
                                            {{ student.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900">{{ student.name }}</p>
                                            <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5">
                                                <span>{{ student.email }}</span>
                                                <span v-if="student.nik" class="text-slate-300">•</span>
                                                <span v-if="student.nik" class="font-mono">NIK: {{ student.nik }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Batch & Year -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="inline-block px-2.5 py-1 rounded-lg bg-red-50 text-japan-red border border-red-100 font-bold text-xs">
                                            {{ student.batches?.[0]?.name || (isJapanese ? '未所属' : 'Belum Ada Angkatan') }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium text-xs">
                                            {{ getBatchYear(student) }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Status Aktif -->
                                <td class="py-3.5 px-4 text-center">
                                    <span v-if="student.batches?.[0]?.status === 'graduated'" class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>{{ isJapanese ? '修了' : 'Alumni / Selesai' }}</span>
                                    </span>
                                    <span v-else class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>{{ isJapanese ? '有効 (在籍)' : 'Aktif' }}</span>
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="py-3.5 px-4 text-center sm:pr-6">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button 
                                            @click="openEditModal(student)" 
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                            :title="isJapanese ? '編集' : 'Edit Data Siswa'"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button 
                                            @click="resetPassword(student)" 
                                            class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors"
                                            :title="isJapanese ? 'パスワード初期化' : 'Reset Password'"
                                        >
                                            <KeyRound class="w-4 h-4" />
                                        </button>
                                        <button 
                                            @click="deleteStudent(student)" 
                                            class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors"
                                            :title="isJapanese ? '削除' : 'Hapus Siswa'"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="students.data.length === 0">
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <Users class="w-8 h-8 text-slate-300 mb-2" />
                                        <p class="text-sm font-semibold">{{ isJapanese ? '実習生データが見つかりませんでした。' : 'Belum ada data siswa.' }}</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="students.links && students.links.length > 3" class="p-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        {{ isJapanese ? `全 ${students.total} 件中 ${students.from || 0} - ${students.to || 0} 件を表示` : `Menampilkan ${students.from || 0} - ${students.to || 0} dari ${students.total} siswa` }}
                    </span>
                    <div class="flex items-center gap-1">
                        <button 
                            v-for="(link, i) in students.links" 
                            :key="i"
                            @click="router.visit(link.url)"
                            :disabled="!link.url || link.active"
                            v-html="link.label"
                            class="px-3 py-1 text-xs rounded-lg border transition-colors"
                            :class="link.active ? 'bg-japan-red text-white border-japan-red font-bold' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 disabled:opacity-40'"
                        ></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= MODAL CREATE / EDIT STUDENT ================= -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                        <GraduationCap class="w-5 h-5 text-japan-red" />
                        <span>{{ isEditing ? (isJapanese ? '実習生データの編集' : 'Edit Data Siswa Trainee') : (isJapanese ? '新規実習生の登録' : 'Pendaftaran Siswa Trainee Baru') }}</span>
                    </h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? '氏名' : 'Nama Lengkap' }} *
                        </label>
                        <input 
                            type="text" 
                            v-model="form.name" 
                            required 
                            placeholder="Contoh: Budi Santoso" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                        />
                    </div>

                    <!-- Email & Password -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Email *
                            </label>
                            <input 
                                type="email" 
                                v-model="form.email" 
                                required 
                                placeholder="budi@example.com" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                            />
                        </div>
                        <div v-if="!isEditing">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '初期パスワード' : 'Kata Sandi Awal' }}
                            </label>
                            <input 
                                type="password" 
                                v-model="form.password" 
                                placeholder="Default: password" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                            />
                        </div>
                        <div v-else>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '電話番号' : 'Nomor WhatsApp / HP' }}
                            </label>
                            <input 
                                type="text" 
                                v-model="form.phone" 
                                placeholder="08123456789" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- Master Data Dropdowns: Batch & Program -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '所属期生' : 'Pilih Angkatan / Batch' }} *
                            </label>
                            <select v-model="form.batch_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none">
                                <option v-for="b in batches" :key="b.id" :value="b.id">
                                    {{ b.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? 'プログラム区分' : 'Pilih Program Penyaluran' }} *
                            </label>
                            <select v-model="form.program_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none">
                                <option v-for="prog in programs" :key="prog.id" :value="prog.name">
                                    {{ isJapanese ? (prog.name_jp || prog.name) : prog.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Master Data Dropdowns: Job Sector & Target Level -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '就労分野・業種' : 'Pilih Sektor Kerja Jepang' }} *
                            </label>
                            <select v-model="form.target_job_sector" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none">
                                <option v-for="sec in jobSectors" :key="sec.id" :value="sec.name">
                                    {{ isJapanese ? (sec.name_jp || sec.name) : sec.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '目標レベル' : 'Pilih Target Level Bahasa' }} *
                            </label>
                            <select v-model="form.target_language_level" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none">
                                <option v-for="lvl in standardLanguageLevels" :key="lvl.code" :value="lvl.name">
                                    {{ isJapanese ? (lvl.name_jp || lvl.name) : lvl.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Pipeline & Medical Status -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '送り出し進捗' : 'Tahapan Penyaluran' }} *
                            </label>
                            <select v-model="form.pipeline_stage" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none">
                                <option v-for="st in pipelineStages" :key="st.id" :value="st.code">
                                    {{ st.order_step }}. {{ isJapanese ? (st.name_jp || st.name) : st.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '健康診断状況' : 'Pemeriksaan Medis' }} *
                            </label>
                            <select v-model="form.mcu_status" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none">
                                <option value="pending">{{ isJapanese ? '未受診' : 'Belum Tes Medis' }}</option>
                                <option value="fit">{{ isJapanese ? '合格' : 'Lolos Medis' }}</option>
                                <option value="unfit">{{ isJapanese ? '不合格' : 'Tidak Lolos' }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- NIK & Passport -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '身分証明番号' : 'Nomor KTP (NIK)' }}
                            </label>
                            <input type="text" v-model="form.nik" placeholder="3201..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-japan-red focus:outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '旅券番号' : 'Nomor Paspor' }}
                            </label>
                            <input type="text" v-model="form.passport_number" placeholder="C1234567" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-japan-red focus:outline-none uppercase" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer disabled:opacity-50">
                            {{ form.processing ? (isJapanese ? '保存中...' : 'Menyimpan...') : (isJapanese ? '保存する' : 'Simpan Siswa') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { notifySuccess, confirmDialog } from '@/Utils/alert';
import { 
    Users, 
    GraduationCap, 
    Briefcase, 
    PlaneTakeoff, 
    UserPlus, 
    Search, 
    RotateCcw, 
    Edit, 
    Trash2, 
    KeyRound, 
    X 
} from 'lucide-vue-next';

const props = defineProps({
    students: Object,
    batches: Array,
    programs: Array,
    jobSectors: Array,
    pipelineStages: Array,
    languageLevels: Array,
    filters: Object,
});

const standardLanguageLevels = computed(() => {
    if (props.languageLevels && props.languageLevels.length > 0) {
        return props.languageLevels;
    }
    return [
        { code: 'N5', name: 'JLPT N5 (Tingkat Dasar)', name_jp: 'JLPT N5 (入門・基礎)' },
        { code: 'N4', name: 'JLPT N4 & JFT-Basic A2 (Standar Kerja)', name_jp: 'JLPT N4 / JFT A2 (就労基準)' },
        { code: 'N3', name: 'JLPT N3 (Tingkat Menengah)', name_jp: 'JLPT N3 (中級実用)' },
        { code: 'N2', name: 'JLPT N2 (Tingkat Mahir / Karir)', name_jp: 'JLPT N2 (上級・ビジネス)' },
    ];
});

const { isJapanese } = useLang();

const searchQuery = ref(props.filters?.search || '');
const selectedBatch = ref(props.filters?.batch_id || '');
const selectedProgram = ref(props.filters?.program_type || '');
const selectedSector = ref(props.filters?.target_job_sector || '');
const selectedStage = ref(props.filters?.pipeline_stage || '');

const getStageName = (code) => {
    const stage = props.pipelineStages?.find(s => s.code === code);
    if (!stage) return code;
    return isJapanese.value ? (stage.name_jp || stage.name) : stage.name;
};

const getBatchYear = (student) => {
    if (student.batches?.[0]?.start_date) {
        return String(student.batches[0].start_date).substring(0, 4);
    }
    if (student.created_at) {
        return String(student.created_at).substring(0, 4);
    }
    return new Date().getFullYear();
};

const applyFilter = () => {
    router.get(route('admin.students.index'), {
        search: searchQuery.value,
        batch_id: selectedBatch.value,
        program_type: selectedProgram.value,
        target_job_sector: selectedSector.value,
        pipeline_stage: selectedStage.value,
    }, { preserveState: true, replace: true });
};

const resetFilter = () => {
    searchQuery.value = '';
    selectedBatch.value = '';
    selectedProgram.value = '';
    selectedSector.value = '';
    selectedStage.value = '';
    applyFilter();
};

const showModal = ref(false);
const isEditing = ref(false);
const editingStudentId = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    nik: '',
    phone: '',
    gender: 'L',
    birth_place: '',
    birth_date: '',
    batch_id: null,
    program_type: '',
    target_job_sector: '',
    target_language_level: '',
    pipeline_stage: 'pelatihan',
    mcu_status: 'pending',
    coe_status: 'pending',
    visa_status: 'pending',
    passport_number: '',
});

const openCreateModal = () => {
    isEditing.value = false;
    editingStudentId.value = null;
    form.reset();
    form.batch_id = props.batches?.[0]?.id || null;
    form.program_type = props.programs?.[0]?.name || 'Tokutei Ginou (SSW)';
    form.target_job_sector = props.jobSectors?.[0]?.name || 'Pengolahan Makanan & Minuman';
    form.target_language_level = standardLanguageLevels.value[1]?.name || standardLanguageLevels.value[0]?.name || '';
    form.pipeline_stage = props.pipelineStages?.[0]?.code || 'pelatihan';
    showModal.value = true;
};

const openEditModal = (student) => {
    isEditing.value = true;
    editingStudentId.value = student.id;
    form.name = student.name;
    form.email = student.email;
    form.nik = student.nik || '';
    form.phone = student.phone || '';
    form.gender = student.gender || 'L';
    form.birth_place = student.birth_place || '';
    form.birth_date = student.birth_date ? student.birth_date.substring(0, 10) : '';
    form.batch_id = student.batches?.[0]?.id || props.batches?.[0]?.id || null;
    form.program_type = student.program_type || props.programs?.[0]?.name || '';
    form.target_job_sector = student.target_job_sector || props.jobSectors?.[0]?.name || '';
    form.target_language_level = student.target_language_level || standardLanguageLevels.value[1]?.name || standardLanguageLevels.value[0]?.name || '';
    form.pipeline_stage = student.pipeline_stage || 'pelatihan';
    form.mcu_status = student.mcu_status || 'pending';
    form.coe_status = student.coe_status || 'pending';
    form.visa_status = student.visa_status || 'pending';
    form.passport_number = student.passport_number || '';
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.students.update', editingStudentId.value), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '更新完了' : 'Berhasil', isJapanese.value ? '実習生データを更新しました。' : 'Data siswa berhasil diperbarui.');
            },
        });
    } else {
        form.post(route('admin.students.store'), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '登録完了' : 'Berhasil', isJapanese.value ? '実習生を新規登録しました。' : 'Siswa baru berhasil didaftarkan.');
            },
        });
    }
};

const resetPassword = (student) => {
    confirmDialog(
        isJapanese.value ? `実習生「${student.name}」のパスワードを初期化しますか？` : `Reset Password ${student.name}?`,
        isJapanese.value ? '初期パスワードは「password」に設定されます。' : 'Kata sandi akun siswa ini akan diatur ulang menjadi default (password).'
    ).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.students.reset-password', student.id), {}, {
                onSuccess: () => {
                    notifySuccess(isJapanese.value ? '初期化完了' : 'Berhasil', isJapanese.value ? 'パスワードを初期化しました。' : 'Kata sandi berhasil direset ke "password".');
                },
            });
        }
    });
};

const deleteStudent = (student) => {
    confirmDialog(
        isJapanese.value ? `実習生「${student.name}」を削除しますか？` : `Hapus ${student.name}?`,
        isJapanese.value ? 'この操作は取り消せません。' : 'Data siswa dan riwayat ujian CBT akan dihapus dari sistem.'
    ).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('admin.students.destroy', student.id), {
                onSuccess: () => {
                    notifySuccess(isJapanese.value ? '削除完了' : 'Terhapus', isJapanese.value ? '実習生データを削除しました。' : 'Data siswa berhasil dihapus.');
                },
            });
        }
    });
};
</script>
