<template>
    <Head :title="isJapanese ? '進捗ステップマスタ - 正夢' : 'Master Tahap Penyaluran - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Top Header Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-800 text-xs font-semibold mb-2 border border-blue-200/60">
                        <PlaneTakeoff class="w-3.5 h-3.5 text-blue-600" />
                        <span>{{ isJapanese ? 'マスタ設定' : 'Pengaturan Master Data' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <ListOrdered class="w-6 h-6 text-japan-red" />
                        <span>{{ isJapanese ? '進捗ステップマスタ' : 'Master Tahapan Penyaluran Siswa' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ isJapanese ? '実習生が日本へ出国するまでの選考・面接・書類手続きステップの管理' : 'Atur alur proses keberangkatan siswa dari tahap awal hingga tiba di Jepang.' }}
                    </p>
                </div>

                <button 
                    @click="openCreateModal"
                    class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/20 transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto"
                >
                    <PlusCircle class="w-4 h-4" />
                    <span>{{ isJapanese ? '+ 新規ステップ追加' : '+ Tambah Tahap Baru' }}</span>
                </button>
            </div>

            <!-- Pipeline Stages Stepper View -->
            <div class="space-y-3">
                <div v-for="stage in stages" :key="stage.id" 
                    class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:border-slate-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-extrabold text-sm shadow-sm shrink-0">
                            {{ stage.order_step }}
                        </div>

                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900">
                                    {{ isJapanese ? (stage.name_jp || stage.name) : stage.name }}
                                </h3>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <span v-if="stage.description">{{ stage.description }}</span>
                                <span v-else class="text-slate-400">Tahap ke-{{ stage.order_step }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Stage Status & Actions -->
                    <div class="flex items-center gap-3 self-end sm:self-auto">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                            :class="stage.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500'">
                            {{ stage.is_active ? (isJapanese ? '有効' : 'Aktif') : (isJapanese ? '無効' : 'Nonaktif') }}
                        </span>

                        <div class="flex items-center gap-1">
                            <button 
                                @click="openEditModal(stage)" 
                                class="p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                :title="isJapanese ? '編集' : 'Edit'"
                            >
                                <Edit class="w-4 h-4" />
                            </button>
                            <button 
                                @click="deleteStage(stage)" 
                                class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 transition-colors"
                                :title="isJapanese ? '削除' : 'Hapus'"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= MODAL CREATE / EDIT STAGE ================= -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                        <ListOrdered class="w-5 h-5 text-japan-red" />
                        <span>{{ isEditing ? (isJapanese ? 'ステップ情報の編集' : 'Edit Tahapan Penyaluran') : (isJapanese ? '新規ステップの追加' : 'Tambah Tahapan Baru') }}</span>
                    </h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? 'ステップ名' : 'Nama Tahapan' }} *
                        </label>
                        <input 
                            type="text" 
                            v-model="form.name" 
                            required 
                            placeholder="Pelatihan Pra-Pemberangkatan" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? 'ステップ番号' : 'Nomor Urutan' }} *
                        </label>
                        <input 
                            type="number" 
                            v-model="form.order_step" 
                            required 
                            min="1" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            {{ isJapanese ? '説明・詳細' : 'Keterangan' }}
                        </label>
                        <textarea 
                            v-model="form.description" 
                            rows="2" 
                            placeholder="..." 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-japan-red focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" v-model="form.is_active" id="activeCheckStage" class="w-4 h-4 rounded text-japan-red focus:ring-japan-red" />
                        <label for="activeCheckStage" class="text-xs text-slate-700 font-semibold cursor-pointer">
                            {{ isJapanese ? '有効にする' : 'Status Aktif' }}
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer disabled:opacity-50">
                            {{ form.processing ? (isJapanese ? '保存中...' : 'Menyimpan...') : (isJapanese ? '保存する' : 'Simpan Tahap') }}
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
import { ListOrdered, PlusCircle, PlaneTakeoff, Edit, Trash2, X } from 'lucide-vue-next';

defineProps({
    stages: Array,
});

const { isJapanese } = useLang();

const showModal = ref(false);
const isEditing = ref(false);
const editingStageId = ref(null);

const form = useForm({
    name: '',
    name_jp: '',
    order_step: 1,
    badge_color: 'blue',
    icon: 'FileText',
    description: '',
    is_active: true,
});

const openCreateModal = () => {
    isEditing.value = false;
    editingStageId.value = null;
    form.reset();
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (stage) => {
    isEditing.value = true;
    editingStageId.value = stage.id;
    form.name = stage.name;
    form.name_jp = stage.name_jp || '';
    form.order_step = stage.order_step;
    form.badge_color = stage.badge_color;
    form.icon = stage.icon || 'FileText';
    form.description = stage.description || '';
    form.is_active = stage.is_active;
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.master.pipeline-stages.update', editingStageId.value), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '更新完了' : 'Berhasil', isJapanese.value ? '進捗ステップを更新しました。' : 'Tahapan penyaluran berhasil diperbarui.');
            },
        });
    } else {
        form.post(route('admin.master.pipeline-stages.store'), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '登録完了' : 'Berhasil', isJapanese.value ? '新しい進捗ステップを追加しました。' : 'Tahapan penyaluran baru berhasil ditambahkan.');
            },
        });
    }
};

const deleteStage = (stage) => {
    confirmDialog(
        isJapanese.value ? `ステップ「${stage.name_jp || stage.name}」を削除しますか？` : `Hapus ${stage.name}?`,
        isJapanese.value ? 'この操作は取り消せません。' : 'Tahapan alur penyaluran ini akan dihapus dari sistem.'
    ).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('admin.master.pipeline-stages.destroy', stage.id), {
                onSuccess: () => {
                    notifySuccess(isJapanese.value ? '削除完了' : 'Terhapus', isJapanese.value ? 'ステップを削除しました。' : 'Tahapan penyaluran berhasil dihapus.');
                },
            });
        }
    });
};
</script>
