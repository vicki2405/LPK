<template>
    <Head :title="isJapanese ? '期生マスタ - 正夢' : 'Master Angkatan / Batch - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Top Header Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-800 text-xs font-semibold mb-2 border border-indigo-200/60">
                        <GraduationCap class="w-3.5 h-3.5 text-indigo-600" />
                        <span>{{ isJapanese ? 'マスタ設定' : 'Pengaturan Master Data' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <GraduationCap class="w-6 h-6 text-japan-red" />
                        <span>{{ isJapanese ? '期生マスタ (Angkatan)' : 'Master Angkatan & Batch' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ isJapanese ? '実習生の入校期生、研修期間、およびステータスの管理' : 'Kelola data kelompok angkatan/batch penerimaan siswa LPK, kode batch, serta periode pelatihan.' }}
                    </p>
                </div>

                <button 
                    @click="openCreateModal"
                    class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/20 transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto"
                >
                    <PlusCircle class="w-4 h-4" />
                    <span>{{ isJapanese ? '+ 新規期生追加' : '+ Tambah Angkatan Baru' }}</span>
                </button>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6 w-16 text-center">No</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '期生名' : 'Nama Angkatan / Batch' }}</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '研修期間' : 'Periode Pelatihan' }}</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '実習生数' : 'Jumlah Siswa' }}</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '状態' : 'Status' }}</th>
                                <th class="py-3.5 px-4 text-center sm:pr-6">{{ isJapanese ? '操作' : 'Aksi' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(batch, index) in batches" :key="batch.id" class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 sm:px-6 text-center font-bold text-slate-400">{{ index + 1 }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-bold text-sm text-slate-900 block">{{ batch.name }}</span>
                                    <span v-if="batch.notes" class="text-[11px] text-slate-400 block">{{ batch.notes }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div v-if="batch.start_date || batch.end_date" class="text-xs font-semibold text-slate-800 inline-flex items-center justify-center gap-1.5">
                                        <span>{{ formatDate(batch.start_date) }}</span>
                                        <span class="text-slate-400 font-normal text-[11px]">s/d</span>
                                        <span>{{ formatDate(batch.end_date) }}</span>
                                    </div>
                                    <span v-else class="text-slate-400 text-xs">-</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs inline-block">
                                        {{ batch.siswas_count || 0 }} {{ isJapanese ? '名' : 'Siswa' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold inline-block"
                                        :class="batch.status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600'">
                                        {{ batch.status === 'active' ? (isJapanese ? '進行中' : 'Aktif') : (isJapanese ? '修了' : 'Selesai / Alumni') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center sm:pr-6">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button 
                                            @click="openEditModal(batch)" 
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                            :title="isJapanese ? '編集' : 'Edit Angkatan'"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button 
                                            @click="deleteBatch(batch)" 
                                            class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors"
                                            :title="isJapanese ? '削除' : 'Hapus Angkatan'"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="batches.length === 0">
                                <td colspan="6" class="py-10 text-center text-slate-400">
                                    {{ isJapanese ? '期生データがありません。' : 'Belum ada data master angkatan. Silakan tambahkan angkatan baru.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= MODAL CREATE / EDIT BATCH ================= -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                        <GraduationCap class="w-5 h-5 text-japan-red" />
                        <span>{{ isEditing ? (isJapanese ? '期生情報の編集' : 'Edit Master Angkatan') : (isJapanese ? '新規期生の追加' : 'Tambah Master Angkatan Baru') }}</span>
                    </h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? '期生名' : 'Nama Angkatan / Batch' }} *
                        </label>
                        <input 
                            type="text" 
                            v-model="form.name" 
                            required 
                            placeholder="Contoh: Angkatan 16" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? '状態' : 'Status Angkatan' }} *
                        </label>
                        <select v-model="form.status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none">
                            <option value="active">{{ isJapanese ? '進行中' : 'Aktif' }}</option>
                            <option value="graduated">{{ isJapanese ? '修了' : 'Selesai / Alumni' }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '開始日' : 'Tanggal Mulai' }}
                            </label>
                            <input type="date" v-model="form.start_date" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-japan-red focus:outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                {{ isJapanese ? '修了予定日' : 'Target Selesai' }}
                            </label>
                            <input type="date" v-model="form.end_date" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-japan-red focus:outline-none" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? '説明・備考' : 'Keterangan Tambahan' }}
                        </label>
                        <textarea 
                            v-model="form.notes" 
                            rows="2" 
                            placeholder="Catatan mengenai angkatan ini..." 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-japan-red focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer disabled:opacity-50">
                            {{ form.processing ? (isJapanese ? '保存中...' : 'Menyimpan...') : (isJapanese ? '保存する' : 'Simpan Angkatan') }}
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
import { notifySuccess, confirmDialog } from '@/Utils/alert';
import { 
    GraduationCap, 
    PlusCircle, 
    Edit, 
    Trash2, 
    X
} from 'lucide-vue-next';

defineProps({
    batches: Array,
});

const { isJapanese } = useLang();

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    try {
        const cleanStr = String(dateStr).substring(0, 10);
        const parts = cleanStr.split('-');
        if (parts.length === 3) {
            const year = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1;
            const day = parseInt(parts[2], 10);
            const d = new Date(year, month, day);

            if (isJapanese.value) {
                return new Intl.DateTimeFormat('ja-JP', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                }).format(d);
            }

            return new Intl.DateTimeFormat('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            }).format(d);
        }
        return cleanStr;
    } catch (e) {
        return dateStr ? String(dateStr).substring(0, 10) : '-';
    }
};

const showModal = ref(false);
const isEditing = ref(false);
const editingBatchId = ref(null);

const form = useForm({
    name: '',
    start_date: '',
    end_date: '',
    status: 'active',
    notes: '',
});

const openCreateModal = () => {
    isEditing.value = false;
    editingBatchId.value = null;
    form.reset();
    form.status = 'active';
    showModal.value = true;
};

const openEditModal = (batch) => {
    isEditing.value = true;
    editingBatchId.value = batch.id;
    form.name = batch.name;
    form.start_date = batch.start_date ? batch.start_date.substring(0, 10) : '';
    form.end_date = batch.end_date ? batch.end_date.substring(0, 10) : '';
    form.status = batch.status || 'active';
    form.notes = batch.notes || '';
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.master.batches.update', editingBatchId.value), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '更新完了' : 'Berhasil', isJapanese.value ? '期生情報を更新しました。' : 'Data master angkatan berhasil diperbarui.');
            },
        });
    } else {
        form.post(route('admin.master.batches.store'), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '登録完了' : 'Berhasil', isJapanese.value ? '新しい期生を追加しました。' : 'Master angkatan baru berhasil ditambahkan.');
            },
        });
    }
};

const deleteBatch = (batch) => {
    confirmDialog(
        isJapanese.value ? `期生「${batch.name}」を削除しますか？` : `Hapus ${batch.name}?`,
        isJapanese.value ? 'この操作は取り消せません。' : 'Data angkatan ini akan dihapus dari sistem.'
    ).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('admin.master.batches.destroy', batch.id), {
                onSuccess: () => {
                    notifySuccess(isJapanese.value ? '削除完了' : 'Terhapus', isJapanese.value ? '期生を削除しました。' : 'Data angkatan berhasil dihapus.');
                },
            });
        }
    });
};
</script>
