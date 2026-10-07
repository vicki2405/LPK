<template>
    <Head :title="isJapanese ? '就労分野マスタ - 正夢' : 'Master Bidang Kerja - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Top Header Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold mb-2 border border-amber-200/60">
                        <Briefcase class="w-3.5 h-3.5 text-amber-600" />
                        <span>{{ isJapanese ? 'マスタ設定' : 'Pengaturan Master Data' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <Briefcase class="w-6 h-6 text-japan-red" />
                        <span>{{ isJapanese ? '就労分野マスタ' : 'Master Sektor & Bidang Kerja' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ isJapanese ? '特定技能・技能実習の対象業種一覧の管理' : 'Kelola daftar bidang pekerjaan untuk siswa dan kelas pelatihan.' }}
                    </p>
                </div>

                <button 
                    @click="openCreateModal"
                    class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/20 transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto"
                >
                    <PlusCircle class="w-4 h-4" />
                    <span>{{ isJapanese ? '+ 新規業種追加' : '+ Tambah Sektor Baru' }}</span>
                </button>
            </div>

            <!-- Job Sectors Table Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6 w-16 text-center">No</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '業種名' : 'Nama Sektor Pekerjaan' }}</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '状態' : 'Status' }}</th>
                                <th class="py-3.5 px-4 text-center sm:pr-6">{{ isJapanese ? '操作' : 'Aksi' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(sector, index) in sectors" :key="sector.id" class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 sm:px-6 font-bold text-slate-400 text-center">{{ index + 1 }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-bold text-sm text-slate-900 block">
                                        {{ isJapanese ? (sector.name_jp || sector.name) : sector.name }}
                                    </span>
                                    <span v-if="sector.description" class="text-[11px] text-slate-400 block">{{ sector.description }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold inline-block"
                                        :class="sector.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500'">
                                        {{ sector.is_active ? (isJapanese ? '有効' : 'Aktif') : (isJapanese ? 'Nonaktif' : 'Nonaktif') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center sm:pr-6">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button 
                                            @click="openEditModal(sector)" 
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                            :title="isJapanese ? '編集' : 'Edit'"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button 
                                            @click="deleteSector(sector)" 
                                            class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors"
                                            :title="isJapanese ? '削除' : 'Hapus'"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="sectors.length === 0">
                                <td colspan="4" class="py-10 text-center text-slate-400">
                                    {{ isJapanese ? '業種データがありません。' : 'Belum ada data sektor kerja.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= MODAL CREATE / EDIT SECTOR ================= -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                        <Briefcase class="w-5 h-5 text-japan-red" />
                        <span>{{ isEditing ? (isJapanese ? '業種情報の編集' : 'Edit Sektor Kerja') : (isJapanese ? '新規業種の追加' : 'Tambah Sektor Baru') }}</span>
                    </h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? '業種名' : 'Nama Sektor Kerja' }} *
                        </label>
                        <input 
                            type="text" 
                            v-model="form.name" 
                            required 
                            placeholder="Pengolahan Makanan & Minuman" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? '表示順' : 'Nomor Urutan' }}
                        </label>
                        <input 
                            type="number" 
                            v-model="form.sort_order" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? '説明・備考' : 'Keterangan' }}
                        </label>
                        <textarea 
                            v-model="form.description" 
                            rows="2" 
                            placeholder="..." 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-japan-red focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" v-model="form.is_active" id="activeCheck" class="w-4 h-4 rounded text-japan-red focus:ring-japan-red" />
                        <label for="activeCheck" class="text-xs text-slate-700 font-semibold cursor-pointer">
                            {{ isJapanese ? '有効にする' : 'Status Aktif' }}
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer disabled:opacity-50">
                            {{ form.processing ? (isJapanese ? '保存中...' : 'Menyimpan...') : (isJapanese ? '保存する' : 'Simpan Sektor') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { confirmDialog, notifySuccess } from '@/Utils/alert';
import { Briefcase, PlusCircle, Edit, Trash2, X } from 'lucide-vue-next';

defineProps({
    sectors: Array,
});

const { isJapanese } = useLang();

const showModal = ref(false);
const isEditing = ref(false);
const editingSectorId = ref(null);

const form = useForm({
    name: '',
    name_jp: '',
    description: '',
    sort_order: 1,
    is_active: true,
});

const openCreateModal = () => {
    isEditing.value = false;
    editingSectorId.value = null;
    form.reset();
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (sector) => {
    isEditing.value = true;
    editingSectorId.value = sector.id;
    form.name = sector.name;
    form.name_jp = sector.name_jp || '';
    form.description = sector.description || '';
    form.sort_order = sector.sort_order;
    form.is_active = sector.is_active;
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.master.job-sectors.update', editingSectorId.value), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '更新完了' : 'Berhasil', isJapanese.value ? '業種マスタを更新しました。' : 'Sektor bidang kerja berhasil diperbarui.');
            },
        });
    } else {
        form.post(route('admin.master.job-sectors.store'), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '登録完了' : 'Berhasil', isJapanese.value ? '新しい業種を追加しました。' : 'Sektor bidang kerja baru berhasil ditambahkan.');
            },
        });
    }
};

const deleteSector = (sector) => {
    confirmDialog(
        isJapanese.value ? `業種「${sector.name_jp || sector.name}」を削除しますか？` : `Hapus ${sector.name}?`,
        isJapanese.value ? 'この操作は取り消せません。' : 'Sektor bidang kerja ini akan dihapus dari sistem.'
    ).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('admin.master.job-sectors.destroy', sector.id), {
                onSuccess: () => {
                    notifySuccess(isJapanese.value ? '削除完了' : 'Terhapus', isJapanese.value ? '業種を削除しました。' : 'Sektor bidang kerja berhasil dihapus.');
                },
            });
        }
    });
};
</script>
