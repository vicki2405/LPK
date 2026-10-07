<template>
    <Head :title="isJapanese ? '指導員・講師管理 - 正夢' : 'Manajemen Data Sensei - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Top Header Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-japan-red text-xs font-semibold mb-2 border border-red-200/60">
                        <Users class="w-3.5 h-3.5" />
                        <span>{{ isJapanese ? '指導員・講師陣' : 'Tenaga Pengajar LPK' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <Users class="w-6 h-6 text-japan-red" />
                        <span>{{ isJapanese ? '指導員データ管理' : 'Data Sensei & Instruktur' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ isJapanese ? '日本語指導員のアカウント登録、担当クラス、および権限管理' : 'Kelola akun Sensei, kontak, pengampu kelas, dan reset password instruktur.' }}
                    </p>
                </div>

                <button 
                    @click="openCreateModal"
                    class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/20 transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto"
                >
                    <UserPlus class="w-4 h-4" />
                    <span>{{ isJapanese ? '+ 指導員を新規登録' : '+ Tambah Sensei Baru' }}</span>
                </button>
            </div>

            <!-- Search Bar -->
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input 
                        type="text" 
                        v-model="searchQuery" 
                        @keyup.enter="applyFilter"
                        :placeholder="isJapanese ? '氏名・Emailで検索...' : 'Cari nama atau email sensei...'"
                        class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-japan-red focus:outline-none"
                    />
                </div>
            </div>

            <!-- Senseis Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6">{{ isJapanese ? '指導員情報' : 'Profil Sensei' }}</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? '連絡先' : 'Kontak' }}</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? '担当クラス数' : 'Kelas Diampu' }}</th>
                                <th class="py-3.5 px-4 text-right sm:pr-6">{{ isJapanese ? '操作' : 'Aksi' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="sensei in senseis.data" :key="sensei.id" class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-red-600 to-rose-700 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                            {{ sensei.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900">{{ sensei.name }}</p>
                                            <p v-if="sensei.katakana_name" class="text-[11px] text-japan-red font-jp font-semibold">
                                                {{ sensei.katakana_name }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="text-slate-800 font-medium block">{{ sensei.email }}</span>
                                    <span v-if="sensei.phone" class="text-slate-400 text-[11px]">{{ sensei.phone }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs">
                                        {{ sensei.batches_count || 0 }} {{ isJapanese ? 'クラス' : 'Kelas' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right sm:pr-6">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button 
                                            @click="openEditModal(sensei)" 
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                            :title="isJapanese ? '編集' : 'Edit Sensei'"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button 
                                            @click="resetPassword(sensei)" 
                                            class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors"
                                            :title="isJapanese ? 'パスワード初期化' : 'Reset Password'"
                                        >
                                            <KeyRound class="w-4 h-4" />
                                        </button>
                                        <button 
                                            @click="deleteSensei(sensei)" 
                                            class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors"
                                            :title="isJapanese ? '削除' : 'Hapus Sensei'"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="senseis.data.length === 0">
                                <td colspan="4" class="py-10 text-center text-slate-400">
                                    {{ isJapanese ? '指導員データが見つかりませんでした。' : 'Belum ada data sensei yang terdaftar.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= MODAL CREATE / EDIT SENSEI ================= -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                        <Users class="w-5 h-5 text-japan-red" />
                        <span>{{ isEditing ? (isJapanese ? '指導員情報の編集' : 'Edit Data Sensei') : (isJapanese ? '指導員の新規登録' : 'Tambah Sensei Baru') }}</span>
                    </h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? '指導員氏名' : 'Nama Lengkap Sensei' }} *
                        </label>
                        <input 
                            type="text" 
                            v-model="form.name" 
                            required 
                            placeholder="Contoh: Tanaka Sensei / Ahmad" 
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
                                placeholder="sensei@gmail.com" 
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                            />
                        </div>
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

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? '担当クラス・期生' : 'Kelas / Angkatan yang Diampu' }}
                        </label>
                        <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto p-2.5 rounded-xl border border-slate-200 bg-slate-50/50">
                            <label v-for="b in batches" :key="b.id" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white text-xs cursor-pointer">
                                <input type="checkbox" :value="b.id" v-model="form.batch_ids" class="rounded border-slate-300 text-japan-red focus:ring-japan-red" />
                                <span class="font-bold text-slate-800">{{ b.name }}</span>
                            </label>
                            <span v-if="!batches || batches.length === 0" class="text-xs text-slate-400 col-span-2 text-center py-2">
                                {{ isJapanese ? '登録済みの期生がありません' : 'Belum ada data angkatan aktif.' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer disabled:opacity-50">
                            {{ form.processing ? (isJapanese ? '保存中...' : 'Menyimpan...') : (isJapanese ? '保存する' : 'Simpan Sensei') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { confirmDialog, notifySuccess } from '@/Utils/alert';
import { Users, UserPlus, Search, Edit, KeyRound, Trash2, X } from 'lucide-vue-next';

const props = defineProps({
    senseis: Object,
    batches: Array,
    filters: Object,
});

const { isJapanese } = useLang();

const searchQuery = ref(props.filters?.search || '');

const applyFilter = () => {
    router.get(route('admin.senseis.index'), { search: searchQuery.value }, { preserveState: true, replace: true });
};

const showModal = ref(false);
const isEditing = ref(false);
const editingSenseiId = ref(null);

const form = useForm({
    name: '',
    katakana_name: '',
    email: '',
    phone: '',
    password: '',
    batch_ids: [],
});

const openCreateModal = () => {
    isEditing.value = false;
    editingSenseiId.value = null;
    form.reset();
    form.batch_ids = [];
    showModal.value = true;
};

const openEditModal = (sensei) => {
    isEditing.value = true;
    editingSenseiId.value = sensei.id;
    form.name = sensei.name;
    form.katakana_name = sensei.katakana_name || '';
    form.email = sensei.email;
    form.phone = sensei.phone || '';
    form.batch_ids = sensei.batches ? sensei.batches.map(b => b.id) : [];
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.senseis.update', editingSenseiId.value), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '更新完了' : 'Berhasil', isJapanese.value ? '指導員情報を更新しました。' : 'Data Sensei berhasil diperbarui.');
            },
        });
    } else {
        form.post(route('admin.senseis.store'), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '登録完了' : 'Berhasil', isJapanese.value ? '指導員を登録しました。' : 'Sensei baru berhasil ditambahkan.');
            },
        });
    }
};

const resetPassword = (sensei) => {
    confirmDialog(
        isJapanese.value ? `パスワードを初期化しますか？` : `Reset Password ${sensei.name}?`,
        isJapanese.value ? 'パスワードは「password」に初期化されます。' : 'Kata sandi Sensei akan direset kembali menjadi "password".'
    ).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.senseis.reset-password', sensei.id), {}, {
                onSuccess: () => {
                    notifySuccess(isJapanese.value ? '初期化完了' : 'Sandi Direset', isJapanese.value ? 'パスワードをリセットしました。' : 'Kata sandi berhasil direset ke "password".');
                },
            });
        }
    });
};

const deleteSensei = (sensei) => {
    confirmDialog(
        isJapanese.value ? `指導員「${sensei.name}」を削除しますか？` : `Hapus ${sensei.name}?`,
        isJapanese.value ? 'この操作は取り消せません。' : 'Akun Sensei ini akan dihapus dari sistem.'
    ).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('admin.senseis.destroy', sensei.id), {
                onSuccess: () => {
                    notifySuccess(isJapanese.value ? '削除完了' : 'Terhapus', isJapanese.value ? '指導員を削除しました。' : 'Data Sensei berhasil dihapus.');
                },
            });
        }
    });
};
</script>
