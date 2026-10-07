<template>
    <Head :title="isJapanese ? 'ユーザーアカウント管理 - 正夢' : 'Manajemen Akun Pengguna - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Top Header Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-semibold mb-2 border border-slate-200">
                        <ShieldCheck class="w-3.5 h-3.5 text-slate-600" />
                        <span>{{ isJapanese ? 'システム権限管理' : 'Keamanan & Akses Pengguna' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <Users class="w-6 h-6 text-japan-red" />
                        <span>{{ isJapanese ? '管理者・スタッフアカウント管理' : 'Akun Administrator & Staf' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ isJapanese ? '管理者および管理スタッフのアカウント、権限ロール割り当て、パスワード初期化を管理します（生徒・指導員は専用メニューで管理）。' : 'Kelola akun administrator dan staf pengelola sistem, penetapan hak akses, dan pengaturan keamanan kata sandi.' }}
                    </p>
                </div>

                <button 
                    @click="openCreateModal"
                    class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/20 transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto"
                >
                    <UserPlus class="w-4 h-4" />
                    <span>{{ isJapanese ? '+ アカウント新規作成' : '+ Buat Akun Baru' }}</span>
                </button>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input 
                        type="text" 
                        v-model="searchQuery" 
                        @keyup.enter="applyFilter"
                        :placeholder="isJapanese ? '氏名またはメールで検索...' : 'Cari nama atau email...'"
                        class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-japan-red focus:outline-none"
                    />
                </div>

                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                    <select v-model="selectedRole" @change="applyFilter" class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-japan-red focus:outline-none">
                        <option value="">-- {{ isJapanese ? 'すべての権限ロール' : 'Semua Peran / Role' }} --</option>
                        <option v-for="r in roles" :key="r.id" :value="r.name">
                            {{ formatRole(r.name) }}
                        </option>
                    </select>

                    <button @click="resetFilter" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors text-xs font-semibold" :title="isJapanese ? 'リセット' : 'Reset Filter'">
                        <RotateCcw class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6">{{ isJapanese ? 'ユーザー情報' : 'Nama & Email' }}</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? '権限ロール' : 'Peran / Role' }}</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? '連絡先' : 'No. WhatsApp' }}</th>
                                <th class="py-3.5 px-4 text-right sm:pr-6">{{ isJapanese ? '操作' : 'Aksi' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900">
                                                {{ isJapanese ? (user.katakana_name || user.name) : user.name }}
                                            </p>
                                            <p class="text-[11px] text-slate-400">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span v-for="r in user.roles" :key="r.id" 
                                        class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider inline-block mr-1"
                                        :class="{
                                            'bg-rose-50 text-japan-red border border-rose-200': r.name === 'admin',
                                            'bg-indigo-50 text-indigo-700 border border-indigo-200': r.name === 'sensei',
                                            'bg-emerald-50 text-emerald-700 border border-emerald-200': r.name === 'siswa',
                                        }">
                                        {{ formatRole(r.name) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-600">
                                    {{ user.phone || '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-right sm:pr-6">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button 
                                            @click="openEditModal(user)" 
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                            :title="isJapanese ? '編集' : 'Edit Akun'"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button 
                                            @click="resetPassword(user)" 
                                            class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors"
                                            :title="isJapanese ? 'パスワード初期化' : 'Reset Password'"
                                        >
                                            <KeyRound class="w-4 h-4" />
                                        </button>
                                        <button 
                                            @click="deleteUser(user)" 
                                            class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors"
                                            :title="isJapanese ? '削除' : 'Hapus Akun'"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="4" class="py-10 text-center text-slate-400">
                                    {{ isJapanese ? 'ユーザーが見つかりませんでした。' : 'Tidak ada data pengguna.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.links && users.links.length > 3" class="p-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        Total {{ users.total }} {{ isJapanese ? '名' : 'Pengguna' }}
                    </span>
                    <div class="flex gap-1">
                        <template v-for="(link, idx) in users.links" :key="idx">
                            <Link 
                                v-if="link.url" 
                                :href="link.url" 
                                v-html="link.label"
                                class="px-3 py-1 text-xs rounded-lg font-medium transition-colors"
                                :class="link.active ? 'bg-japan-red text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= MODAL CREATE / EDIT USER ================= -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                        <Users class="w-5 h-5 text-japan-red" />
                        <span>{{ isEditing ? (isJapanese ? 'ユーザー情報の編集' : 'Edit Data Pengguna') : (isJapanese ? 'アカウント新規登録' : 'Buat Akun Baru') }}</span>
                    </h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? '氏名' : 'Nama Lengkap' }} *
                        </label>
                        <input 
                            type="text" 
                            v-model="form.name" 
                            required 
                            placeholder="Nama Pengguna" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Email *
                            </label>
                            <input 
                                type="email" 
                                v-model="form.email" 
                                required 
                                placeholder="user@gmail.com" 
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '権限ロール' : 'Peran / Role' }} *
                            </label>
                            <select v-model="form.role" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none uppercase font-bold">
                                <option v-for="r in roles" :key="r.id" :value="r.name">
                                    {{ formatRole(r.name) }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '電話番号' : 'No. WhatsApp' }}
                            </label>
                            <input 
                                type="text" 
                                v-model="form.phone" 
                                placeholder="0812..." 
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                            />
                        </div>
                        <div v-if="!isEditing">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '初期パスワード' : 'Password Awal' }}
                            </label>
                            <input 
                                type="text" 
                                v-model="form.password" 
                                placeholder="Default: password" 
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer disabled:opacity-50">
                            {{ form.processing ? (isJapanese ? '保存中...' : 'Menyimpan...') : (isJapanese ? '保存する' : 'Simpan Akun') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { confirmDialog, notifySuccess } from '@/Utils/alert';
import { Users, UserPlus, Search, Edit, KeyRound, Trash2, RotateCcw, ShieldCheck, X } from 'lucide-vue-next';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

const { isJapanese } = useLang();

const searchQuery = ref(props.filters?.search || '');
const selectedRole = ref(props.filters?.role || '');

const formatRole = (role) => {
    if (isJapanese.value) {
        if (role === 'admin') return '管理者';
        if (role === 'sensei') return '指導員';
        if (role === 'siswa') return '実習生';
        return role;
    }
    if (role === 'admin') return 'Administrator';
    if (role === 'sensei') return 'Sensei';
    if (role === 'siswa') return 'Siswa';
    return role;
};

const applyFilter = () => {
    router.get(route('admin.users.index'), {
        search: searchQuery.value,
        role: selectedRole.value,
    }, { preserveState: true, replace: true });
};

const resetFilter = () => {
    searchQuery.value = '';
    selectedRole.value = '';
    applyFilter();
};

const showModal = ref(false);
const isEditing = ref(false);
const editingUserId = ref(null);

const form = useForm({
    name: '',
    katakana_name: '',
    email: '',
    phone: '',
    role: props.roles?.[0]?.name || 'admin',
    password: '',
});

const openCreateModal = () => {
    isEditing.value = false;
    editingUserId.value = null;
    form.reset();
    form.role = props.roles?.[0]?.name || 'admin';
    showModal.value = true;
};

const openEditModal = (user) => {
    isEditing.value = true;
    editingUserId.value = user.id;
    form.name = user.name;
    form.katakana_name = user.katakana_name || '';
    form.email = user.email;
    form.phone = user.phone || '';
    form.role = user.roles?.[0]?.name || props.roles?.[0]?.name || 'admin';
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.users.update', editingUserId.value), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '更新完了' : 'Berhasil', isJapanese.value ? 'ユーザー情報を更新しました。' : 'Data pengguna berhasil diperbarui.');
            },
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '登録完了' : 'Berhasil', isJapanese.value ? '新しいユーザーアカウントを作成しました。' : 'Akun pengguna baru berhasil dibuat.');
            },
        });
    }
};

const resetPassword = (user) => {
    confirmDialog(
        isJapanese.value ? `パスワードを初期化しますか？` : `Reset Password ${user.name}?`,
        isJapanese.value ? 'パスワードは「password」に初期化されます。' : 'Kata sandi pengguna akan direset kembali menjadi "password".'
    ).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.users.reset-password', user.id), {}, {
                onSuccess: () => {
                    notifySuccess(isJapanese.value ? '初期化完了' : 'Sandi Direset', isJapanese.value ? 'パスワードをリセットしました。' : 'Kata sandi berhasil direset ke "password".');
                },
            });
        }
    });
};

const deleteUser = (user) => {
    confirmDialog(
        isJapanese.value ? `ユーザー「${user.name}」を削除しますか？` : `Hapus ${user.name}?`,
        isJapanese.value ? 'この操作は取り消せません。' : 'Akun pengguna ini akan dihapus dari sistem.'
    ).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('admin.users.destroy', user.id), {
                onSuccess: () => {
                    notifySuccess(isJapanese.value ? '削除完了' : 'Terhapus', isJapanese.value ? 'ユーザーを削除しました。' : 'Akun berhasil dihapus.');
                },
            });
        }
    });
};
</script>
