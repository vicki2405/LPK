<template>
    <Head :title="isJapanese ? 'ロール・権限マトリクス - 正夢' : 'Role & Hak Akses Permission - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Top Header Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-50 text-japan-red text-xs font-semibold mb-2 border border-rose-200/60">
                        <ShieldCheck class="w-3.5 h-3.5" />
                        <span>{{ isJapanese ? 'セキュリティ・アクセス制御' : 'Otorisasi & Keamanan' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <ShieldCheck class="w-6 h-6 text-japan-red" />
                        <span>{{ isJapanese ? '権限ロール設定 (Role & Permissions)' : 'Pengaturan Peran & Hak Akses' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ isJapanese ? '各ユーザー役割（管理者、指導員、実習生）に対する機能アクセスマトリクスの管理' : 'Atur matriks izin hak akses modul untuk peran Administrator, Sensei, Siswa, dan peran tambahan lainnya.' }}
                    </p>
                </div>

                <button 
                    @click="openCreateModal"
                    class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/20 transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto"
                >
                    <PlusCircle class="w-4 h-4" />
                    <span>{{ isJapanese ? '+ 新規ロール追加' : '+ Tambah Peran Baru' }}</span>
                </button>
            </div>

            <!-- Roles Grid & Permissions Matrix -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Role Selector Column -->
                <div class="space-y-3">
                    <div v-for="role in roles" :key="role.id" 
                        @click="selectRole(role)"
                        class="p-5 rounded-2xl border transition-all cursor-pointer flex items-center justify-between"
                        :class="selectedRole?.id === role.id 
                            ? 'bg-slate-900 text-white border-slate-900 shadow-md' 
                            : 'bg-white text-slate-800 border-slate-200/80 hover:border-slate-300'">
                        <div>
                            <span class="text-xs font-mono uppercase tracking-wider block opacity-70">
                                {{ role.name }}
                            </span>
                            <h3 class="text-base font-bold capitalize mt-0.5">{{ formatRoleName(role.name) }}</h3>
                            <p class="text-xs mt-1" :class="selectedRole?.id === role.id ? 'text-slate-300' : 'text-slate-500'">
                                {{ role.users_count || 0 }} {{ isJapanese ? '名のユーザー' : 'Pengguna terdaftar' }}
                            </p>
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                            :class="selectedRole?.id === role.id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">
                            {{ role.permissions?.length || 0 }} {{ isJapanese ? '権限' : 'Izin' }}
                        </span>
                    </div>
                </div>

                <!-- Permissions Matrix Config Column -->
                <div class="lg:col-span-2 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
                    <div v-if="selectedRole" class="space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                    <ShieldCheck class="w-5 h-5 text-japan-red" />
                                    <span>{{ isJapanese ? '権限設定:' : 'Hak Akses untuk:' }} <strong class="text-japan-red uppercase">{{ selectedRole.name }}</strong></span>
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ isJapanese ? 'チェックを入れた機能のみがこのロールのユーザーに許可されます。' : 'Centang izin fitur yang diperbolehkan untuk peran ini.' }}
                                </p>
                            </div>

                            <button 
                                @click="savePermissions"
                                :disabled="form.processing"
                                class="px-5 py-2 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer disabled:opacity-50"
                            >
                                {{ form.processing ? (isJapanese ? '保存中...' : 'Menyimpan...') : (isJapanese ? '変更を保存' : 'Simpan Hak Akses') }}
                            </button>
                        </div>

                        <!-- Permission Checkboxes Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label v-for="perm in permissions" :key="perm.id" 
                                class="flex items-start gap-3 p-3.5 rounded-2xl border border-slate-100 hover:bg-slate-50/80 transition-colors cursor-pointer"
                                :class="{ 'bg-red-50/40 border-red-200': form.permissions.includes(perm.name) }">
                                <input 
                                    type="checkbox" 
                                    :value="perm.name" 
                                    v-model="form.permissions"
                                    class="w-4 h-4 mt-0.5 rounded text-japan-red focus:ring-japan-red border-slate-300"
                                />
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">
                                        {{ formatPermName(perm.name) }}
                                    </span>
                                    <span class="text-[11px] font-mono text-slate-400">
                                        {{ perm.name }}
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= MODAL CREATE NEW ROLE ================= -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-6 sm:p-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <PlusCircle class="w-5 h-5 text-japan-red" />
                        <span>{{ isJapanese ? '新規ロールの作成' : 'Tambah Peran (Role) Baru' }}</span>
                    </h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitCreateRole" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? 'ロール名 (英字小文字)' : 'Nama Peran (Kode Huruf Kecil)' }} *
                        </label>
                        <input 
                            type="text" 
                            v-model="createRoleForm.name" 
                            required 
                            placeholder="staff_tu" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none lowercase"
                        />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button type="submit" :disabled="createRoleForm.processing" class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer disabled:opacity-50">
                            {{ createRoleForm.processing ? (isJapanese ? '作成中...' : 'Menyimpan...') : (isJapanese ? '作成する' : 'Buat Peran') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { notifySuccess } from '@/Utils/alert';
import { ShieldCheck, PlusCircle, X } from 'lucide-vue-next';

const props = defineProps({
    roles: Array,
    permissions: Array,
});

const { isJapanese } = useLang();

const selectedRole = ref(props.roles?.[0] || null);

const form = useForm({
    permissions: [],
});

const createRoleForm = useForm({
    name: '',
});

const showModal = ref(false);

const selectRole = (role) => {
    selectedRole.value = role;
    form.permissions = role.permissions ? role.permissions.map(p => p.name) : [];
};

onMounted(() => {
    if (selectedRole.value) {
        selectRole(selectedRole.value);
    }
});

const formatRoleName = (roleName) => {
    if (isJapanese.value) {
        if (roleName === 'admin') return '管理者';
        if (roleName === 'sensei') return '指導員';
        if (roleName === 'siswa') return '実習生';
        return roleName;
    }
    if (roleName === 'admin') return 'Administrator LPK';
    if (roleName === 'sensei') return 'Sensei / Instruktur';
    if (roleName === 'siswa') return 'Siswa Trainee';
    return roleName;
};

const formatPermName = (name) => {
    const dict = {
        manage_users: isJapanese.value ? 'ユーザー管理' : 'Kelola Akun Pengguna',
        manage_roles: isJapanese.value ? '権限ロール管理' : 'Kelola Peran & Hak Akses',
        manage_students: isJapanese.value ? '実習生データ管理' : 'Kelola Data Siswa',
        manage_batches: isJapanese.value ? 'クラス・期生管理' : 'Kelola Angkatan & Kelas',
        manage_pipeline: isJapanese.value ? '送り出し進捗管理' : 'Kelola Alur Penyaluran',
        manage_cbt_exams: isJapanese.value ? 'CBT試験管理' : 'Kelola Paket Ujian CBT',
        manage_questions: isJapanese.value ? '問題バンク管理' : 'Kelola Bank Soal',
        manage_courses: isJapanese.value ? 'カリキュラム管理' : 'Kelola Materi & Bab',
        grade_exams: isJapanese.value ? '採点・成績管理' : 'Koreksi & Nilai Ujian',
        take_exams: isJapanese.value ? 'CBT受験権限' : 'Akses Mengerjakan Ujian CBT',
        view_learning_materials: isJapanese.value ? '学習教材閲覧' : 'Akses Materi Belajar',
        manage_master_data: isJapanese.value ? 'マスタデータ管理' : 'Kelola Master Data',
        view_analytics: isJapanese.value ? '統計・分析閲覧' : 'Akses Laporan & Statistik',
    };
    return dict[name] || name;
};

const savePermissions = () => {
    if (!selectedRole.value) return;

    form.put(route('admin.roles.update', selectedRole.value.id), {
        onSuccess: () => {
            notifySuccess(isJapanese.value ? '更新完了' : 'Berhasil', isJapanese.value ? '権限設定を更新しました。' : `Hak akses untuk peran ${selectedRole.value.name} berhasil diperbarui.`);
        },
    });
};

const openCreateModal = () => {
    createRoleForm.reset();
    showModal.value = true;
};

const submitCreateRole = () => {
    createRoleForm.post(route('admin.roles.store'), {
        onSuccess: () => {
            showModal.value = false;
            notifySuccess(isJapanese.value ? '作成完了' : 'Berhasil', isJapanese.value ? '新しいロールを作成しました。' : 'Peran baru berhasil dibuat.');
        },
    });
};
</script>
