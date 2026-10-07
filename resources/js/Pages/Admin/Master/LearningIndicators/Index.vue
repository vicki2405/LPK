<template>
    <Head :title="isJapanese ? '学習到達目標・評価指標管理 - 管理者ポータル' : 'Master Indikator Capaian Pembelajaran - Admin LPK'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-12">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-semibold mb-2 border border-rose-200/60 font-jp">
                        <Target class="w-3.5 h-3.5" />
                        <span>{{ isJapanese ? 'カリキュラム評価指標' : 'Master Data Kurikulum & Ujian' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5 font-jp">
                        <Target class="w-6 h-6 text-rose-600" />
                        <span>{{ isJapanese ? '学習到達目標・評価指標管理' : 'Master Indikator Capaian Pembelajaran' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        {{ isJapanese 
                            ? '実習生の習得度を測定するための学習到達目標および評価指標（CBT bank soal）を管理します。' 
                            : 'Kelola daftar indikator capaian pembelajaran yang digunakan sebagai tolok ukur evaluasi dan kategori butir soal ujian.' 
                        }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button 
                        type="button" 
                        @click="openCreateModal"
                        class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/25 transition-all flex items-center gap-2 cursor-pointer font-jp"
                    >
                        <PlusCircle class="w-4 h-4" />
                        <span>{{ isJapanese ? '＋ 新規指標を登録' : '+ Tambah Indikator Baru' }}</span>
                    </button>
                </div>
            </div>

            <!-- Stats Overview Card -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '登録指標総数' : 'Total Indikator Capaian' }}
                        </p>
                        <p class="text-2xl font-black text-slate-900 mt-1">{{ stats.total || 0 }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                        <Target class="w-6 h-6" />
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? 'ステータス' : 'Status Sistem' }}
                        </p>
                        <p class="text-sm font-bold text-emerald-600 mt-1 flex items-center gap-1.5 font-jp">
                            <CheckCircle2 class="w-4 h-4" />
                            <span>{{ isJapanese ? '稼働中・利用可能' : 'Aktif untuk CBT & Evaluasi' }}</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <CheckCircle2 class="w-6 h-6" />
                    </div>
                </div>
            </div>

            <!-- Search Toolbar -->
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="relative flex-1 max-w-md">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input 
                        type="text" 
                        v-model="searchQuery" 
                        @keyup.enter="applyFilter"
                        :placeholder="isJapanese ? '指標名または説明で検索...' : 'Cari nama indikator atau deskripsi...'" 
                        class="w-full pl-9 pr-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <button 
                        v-if="searchQuery"
                        type="button" 
                        @click="resetFilter" 
                        class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors flex items-center gap-1.5 cursor-pointer shrink-0"
                    >
                        <RotateCcw class="w-3.5 h-3.5" />
                        <span>Reset</span>
                    </button>
                    <button 
                        type="button" 
                        @click="applyFilter"
                        class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all cursor-pointer"
                    >
                        <span>Cari</span>
                    </button>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 font-jp">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6 w-14 text-center">{{ isJapanese ? '番号' : 'No.' }}</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? '学習到達目標・評価指標名' : 'Nama Indikator Capaian Pembelajaran' }}</th>
                                <th class="py-3.5 px-4 w-2/5">{{ isJapanese ? '説明・実務基準' : 'Deskripsi / Keterangan' }}</th>
                                <th class="py-3.5 px-4 sm:pr-6 text-right w-24">{{ isJapanese ? '操作' : 'Aksi' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(ind, index) in indicators.data" :key="ind.id" class="hover:bg-slate-50/70 transition-colors group">
                                <td class="py-4 px-4 sm:px-6 text-center font-bold text-slate-400 font-mono">
                                    {{ (indicators.current_page - 1) * indicators.per_page + index + 1 }}
                                </td>

                                <td class="py-4 px-4">
                                    <p class="font-bold text-slate-950 font-jp text-sm leading-snug">
                                        {{ ind.name }}
                                    </p>
                                </td>

                                <td class="py-4 px-4">
                                    <p v-if="ind.description" class="text-xs text-slate-600 leading-relaxed">
                                        {{ ind.description }}
                                    </p>
                                    <span v-else class="text-slate-300 font-mono text-xs">—</span>
                                </td>

                                <td class="py-4 px-4 sm:pr-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5 font-jp">
                                        <button 
                                            type="button" 
                                            @click="openEditModal(ind)"
                                            class="p-2 rounded-xl bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-600 transition-colors cursor-pointer"
                                            :title="isJapanese ? '指標を編集' : 'Edit Indikator'"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="deleteIndicator(ind)"
                                            class="p-2 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 transition-colors cursor-pointer"
                                            :title="isJapanese ? '指標を削除' : 'Hapus Indikator'"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="indicators.data.length === 0">
                                <td colspan="4" class="py-12 text-center text-slate-400 space-y-2">
                                    <Target class="w-10 h-10 text-slate-300 mx-auto" />
                                    <p class="font-bold text-sm text-slate-600 font-jp">
                                        {{ isJapanese ? '該当する評価指標が見つかりません。' : 'Belum ada indikator capaian yang terdaftar.' }}
                                    </p>
                                    <p class="text-xs text-slate-400 font-jp">
                                        {{ isJapanese ? '「新規指標を登録」ボタンから目標を設定してください。' : 'Klik tombol "+ Tambah Indikator Baru" di atas untuk menambahkan indikator.' }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Responsive Cards -->
                <div class="block md:hidden divide-y divide-slate-100">
                    <div v-for="ind in indicators.data" :key="'mob-' + ind.id" class="p-4 space-y-3">
                        <div class="space-y-1">
                            <p class="font-bold text-slate-950 font-jp text-sm leading-snug">
                                {{ ind.name }}
                            </p>
                            <p v-if="ind.description" class="text-xs text-slate-500 leading-relaxed">
                                {{ ind.description }}
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-1.5 pt-2 border-t border-slate-50 text-xs font-jp">
                            <button 
                                type="button" 
                                @click="openEditModal(ind)"
                                class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold hover:bg-blue-50 hover:text-blue-600 flex items-center gap-1 cursor-pointer"
                            >
                                <Edit class="w-3.5 h-3.5" />
                                <span>Edit</span>
                            </button>
                            <button 
                                type="button" 
                                @click="deleteIndicator(ind)"
                                class="p-1 rounded-lg bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600 cursor-pointer"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>

                    <div v-if="indicators.data.length === 0" class="py-12 text-center text-slate-400 space-y-2 p-4">
                        <Target class="w-10 h-10 text-slate-300 mx-auto" />
                        <p class="font-bold text-sm text-slate-600 font-jp">
                            {{ isJapanese ? '該当する評価指標が見つかりません。' : 'Belum ada indikator capaian.' }}
                        </p>
                    </div>
                </div>

                <!-- Pagination Footer -->
                <div v-if="indicators.links && indicators.links.length > 3" class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <span class="text-slate-500 font-jp">
                        {{ isJapanese 
                            ? `全 ${indicators.total} 件中 ${indicators.from || 0} - ${indicators.to || 0} 件を表示` 
                            : `Menampilkan ${indicators.from || 0} - ${indicators.to || 0} dari total ${indicators.total} indikator` 
                        }}
                    </span>

                    <div class="flex items-center gap-1">
                        <template v-for="(link, i) in indicators.links" :key="i">
                            <Link 
                                v-if="link.url"
                                :href="link.url"
                                preserve-scroll
                                preserve-state
                                class="px-3 py-1.5 rounded-lg border font-medium transition-colors"
                                :class="link.active ? 'bg-slate-900 border-slate-900 text-white font-bold' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50'"
                                v-html="link.label"
                            />
                            <span 
                                v-else 
                                class="px-3 py-1.5 rounded-lg border border-slate-100 text-slate-300 select-none"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>

            <!-- Modal Form Tambah / Edit Indikator -->
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs animate-fade-in">
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl max-w-lg w-full p-6 space-y-5">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                                🎯
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 font-jp">
                                {{ isEditing ? (isJapanese ? '評価指標の編集' : 'Edit Indikator Capaian') : (isJapanese ? '新規評価指標の登録' : 'Tambah Indikator Capaian Baru') }}
                            </h3>
                        </div>
                        <button @click="showModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <!-- Modal Form -->
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <!-- Nama Indikator Capaian -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 font-jp">
                                {{ isJapanese ? '学習到達目標・評価指標名 *' : 'Nama Indikator Capaian Pembelajaran *' }}
                            </label>
                            <input 
                                type="text" 
                                v-model="form.name" 
                                required 
                                autofocus
                                :placeholder="isJapanese ? '例：基本助詞の正しい用法' : 'Contoh: Pemahaman Partikel & Struktur Kalimat Kerja'" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-rose-500 focus:outline-none transition-all"
                                :class="{ 'border-rose-500 ring-1 ring-rose-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600 font-bold flex items-center gap-1">
                                <span>⚠️</span> <span>{{ form.errors.name }}</span>
                            </p>
                        </div>

                        <!-- Deskripsi / Keterangan (Opsional) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 font-jp">
                                {{ isJapanese ? '説明・実務基準（任意）' : 'Deskripsi / Keterangan (Opsional)' }}
                            </label>
                            <textarea 
                                v-model="form.description" 
                                rows="3"
                                :placeholder="isJapanese ? '指標に関する補足説明や基準を記載...' : 'Tambahkan catatan atau penjelasan tambahan jika diperlukan...'"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm leading-relaxed focus:ring-2 focus:ring-rose-500 focus:outline-none"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600 font-bold">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 font-jp">
                            <button 
                                type="button" 
                                @click="showModal = false"
                                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer"
                            >
                                {{ isJapanese ? 'キャンセル' : 'Batal' }}
                            </button>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="px-6 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/25 transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
                            >
                                <Loader2 v-if="form.processing" class="w-4 h-4 animate-spin" />
                                <Save v-else class="w-4 h-4" />
                                <span>{{ isJapanese ? (isEditing ? '更新を保存' : '指標を登録') : (isEditing ? 'Simpan Perubahan' : 'Simpan Indikator') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { notifySuccess, confirmDialog } from '@/Utils/alert';
import { 
    Target, 
    PlusCircle, 
    Search, 
    CheckCircle2, 
    RotateCcw, 
    Edit, 
    Trash2, 
    Save, 
    Loader2 
} from 'lucide-vue-next';

const { isJapanese } = useLang();

const props = defineProps({
    indicators: Object,
    stats: Object,
    filters: Object,
});

// Search State
const searchQuery = ref(props.filters?.search || '');

const applyFilter = () => {
    router.get(route('admin.master.learning-indicators.index'), {
        search: searchQuery.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilter = () => {
    searchQuery.value = '';
    router.get(route('admin.master.learning-indicators.index'));
};

// Modal & Form State
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    description: '',
});

const openCreateModal = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (ind) => {
    isEditing.value = true;
    editingId.value = ind.id;
    form.name = ind.name;
    form.description = ind.description || '';
    form.clearErrors();
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.master.learning-indicators.update', editingId.value), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(
                    isJapanese.value ? '更新完了' : 'Berhasil', 
                    isJapanese.value ? '評価指標が更新されました。' : 'Indikator capaian berhasil diperbarui.'
                );
            },
        });
    } else {
        form.post(route('admin.master.learning-indicators.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(
                    isJapanese.value ? '登録完了' : 'Berhasil', 
                    isJapanese.value ? '評価指標が正常に登録されました。' : 'Indikator capaian baru berhasil ditambahkan.'
                );
            },
        });
    }
};

const deleteIndicator = (ind) => {
    confirmDialog(
        isJapanese.value ? '評価指標を削除しますか？' : 'Hapus Indikator Ini?',
        isJapanese.value ? `「${ind.name}」の指標データを削除します。` : `Data indikator "${ind.name}" akan dihapus dari sistem.`
    ).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.master.learning-indicators.destroy', ind.id), {
                preserveScroll: true,
                onSuccess: () => {
                    notifySuccess(
                        isJapanese.value ? '削除完了' : 'Berhasil', 
                        isJapanese.value ? '評価指標が削除されました。' : 'Indikator capaian berhasil dihapus.'
                    );
                },
            });
        }
    });
};
</script>
