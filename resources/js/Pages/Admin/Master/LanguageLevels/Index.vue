<template>
    <AuthenticatedLayout>
        <Head :title="isJapanese ? '言語レベル設定' : 'Master Level Bahasa'" />

        <div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-red-50 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-red-50 text-japan-red border border-red-100 rounded-full text-xs font-black tracking-widest uppercase mb-3 font-jp">
                        <Award class="w-3.5 h-3.5" />
                        <span>{{ isJapanese ? 'マスターデータ管理' : 'Master Data' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3 font-jp">
                        <span>{{ isJapanese ? '日本語・語学レベル設定' : 'Tingkat Kemampuan Bahasa' }}</span>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-mono font-bold">
                            {{ languageLevels.length }} Level
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        {{ isJapanese 
                            ? 'システム全体の日本語基準レベル（JLPT N5〜N1、JFTなど）を管理します。Kanji、Kotoba、実習生データに反映されます。' 
                            : 'Kelola acuan tingkat bahasa Jepang (JLPT N5-N1, JFT-Basic, dll) untuk standarisasi modul Kartu Kanji, Kosakata, dan kualifikasi Siswa.' 
                        }}
                    </p>
                </div>

                <div class="relative z-10 flex items-center gap-3">
                    <button 
                        type="button" 
                        @click="openCreateModal"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl bg-japan-red hover:bg-red-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-japan-red/20 active:scale-[0.98] transition-all cursor-pointer font-jp"
                    >
                        <PlusCircle class="w-4 h-4" />
                        <span>{{ isJapanese ? 'レベルを追加' : 'Tambah Level Baru' }}</span>
                    </button>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '有効なレベル' : 'Level Aktif' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-slate-900 mt-0.5 block font-mono">
                            {{ activeCount }} / {{ languageLevels.length }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <CheckCircle2 class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '登録漢字総数' : 'Total Huruf Kanji Terkait' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-blue-600 mt-0.5 block font-mono">
                            {{ totalKanjisLinked }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <Languages class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '実習生ターゲット' : 'Target Siswa' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-amber-600 mt-0.5 block font-mono">
                            {{ totalStudentsLinked }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                        <Users class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 font-jp">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6 w-16 text-center">Urutan</th>
                                <th class="py-3.5 px-4 text-center">Kode</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? 'レベル名称' : 'Nama Tingkat / Level' }}</th>
                                <th class="py-3.5 px-4 text-center">Terkait Data</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '状態' : 'Status' }}</th>
                                <th class="py-3.5 px-4 text-center sm:pr-6">{{ isJapanese ? '操作' : 'Aksi' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="lvl in languageLevels" :key="lvl.id" class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 sm:px-6 font-bold text-slate-400 text-center font-mono">
                                    #{{ lvl.sort_order }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span 
                                        class="px-2.5 py-1 rounded-full text-xs font-black tracking-wide border font-mono inline-block shadow-2xs"
                                        :class="getLevelBadgeClass(lvl.code)"
                                    >
                                        {{ lvl.code }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-sm text-slate-900 flex items-center gap-2">
                                        <span>{{ lvl.name }}</span>
                                        <span v-if="lvl.name_jp" class="text-xs font-normal text-slate-400 font-jp">
                                            ({{ lvl.name_jp }})
                                        </span>
                                    </div>
                                    <p v-if="lvl.description" class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">
                                        {{ lvl.description }}
                                    </p>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-slate-50 border border-slate-200/60 rounded-xl text-[11px] font-mono text-slate-600">
                                        <span :title="`${lvl.kanjis_count || 0} Huruf Kanji`">漢: <b>{{ lvl.kanjis_count || 0 }}</b></span>
                                        <span class="text-slate-300">|</span>
                                        <span :title="`${lvl.vocabularies_count || 0} Kosakata Kotoba`">語: <b>{{ lvl.vocabularies_count || 0 }}</b></span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold inline-block font-jp"
                                        :class="lvl.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'">
                                        {{ lvl.is_active ? (isJapanese ? '有効' : 'Aktif') : (isJapanese ? '無効' : 'Nonaktif') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center sm:pr-6">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button 
                                            type="button"
                                            @click="openEditModal(lvl)" 
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors cursor-pointer"
                                            :title="isJapanese ? '編集' : 'Edit Level'"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button 
                                            type="button"
                                            @click="deleteLevel(lvl)" 
                                            class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors cursor-pointer"
                                            :title="isJapanese ? '削除' : 'Hapus Level'"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="languageLevels.length === 0">
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    {{ isJapanese ? 'レベルデータが登録されていません。' : 'Belum ada data level bahasa. Silakan tambahkan level baru.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= MODAL CREATE / EDIT LANGUAGE LEVEL ================= -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs animate-fade-in" @click.self="showModal = false">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-7">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <h3 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2 font-jp">
                        <Award class="w-5 h-5 text-japan-red" />
                        <span>{{ isEditing ? (isJapanese ? '語学レベルの編集' : 'Edit Tingkat / Level Bahasa') : (isJapanese ? '新規語学レベルの追加' : 'Tambah Level Bahasa Baru') }}</span>
                    </h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Info Kode Level saat Edit Mode (Read-only) -->
                    <div v-if="isEditing" class="p-3 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block font-jp">Kode Level Sistem</span>
                            <span class="text-xs font-mono font-black text-slate-800">{{ form.code }}</span>
                        </div>
                        <span class="text-[11px] text-slate-400 font-jp">Kode sistem tersinkronisasi</span>
                    </div>

                    <!-- Input Nama Level (Full Width saat Tambah Baru) -->
                    <div>
                        <label class="block text-[11px] font-black text-slate-700 mb-1 uppercase tracking-wider font-jp">
                            Nama Level *
                        </label>
                        <input 
                            type="text" 
                            v-model="form.name" 
                            placeholder="Contoh: JLPT N5 (Tingkat Dasar)" 
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border text-xs font-bold text-slate-900 focus:ring-2 focus:ring-japan-red focus:border-japan-red focus:outline-none"
                            :class="form.errors.name ? 'border-rose-400 bg-rose-50/40' : 'border-slate-300'"
                        />
                        <span v-if="form.errors.name" class="text-[10px] text-rose-600 font-bold mt-1 block">
                            {{ form.errors.name }}
                        </span>
                        <p v-if="!isEditing" class="text-[11px] text-slate-400 mt-1 font-jp">
                            * Kode sistem akan dibuat otomatis berdasarkan nama level (contoh: N5, N4, dsb).
                        </p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-slate-700 mb-1 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '表示順序 (#)' : 'Urutan Tampilan (#)' }}
                        </label>
                        <input 
                            type="number" 
                            v-model.number="form.sort_order" 
                            min="0" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-japan-red focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-slate-700 mb-1 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '説明・対象' : 'Deskripsi & Target Standar' }}
                        </label>
                        <textarea 
                            v-model="form.description" 
                            rows="2"
                            placeholder="Deskripsi kemampuan, materi dasar, atau acuan kerja..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-japan-red focus:outline-none resize-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input 
                            type="checkbox" 
                            id="activeCheckLvl" 
                            v-model="form.is_active"
                            class="w-4 h-4 rounded text-japan-red focus:ring-japan-red border-slate-300 cursor-pointer"
                        />
                        <label for="activeCheckLvl" class="text-xs text-slate-700 font-semibold cursor-pointer select-none">
                            {{ isJapanese ? 'このレベルを全システムで有効化する' : 'Aktifkan level ini untuk seluruh sistem (Kanji, Siswa, Kotoba, CBT)' }}
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button 
                            type="button" 
                            @click="showModal = false" 
                            class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                        >
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button 
                            type="submit" 
                            :disabled="form.processing" 
                            class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/20 active:scale-[0.98] transition-all cursor-pointer disabled:opacity-50"
                        >
                            {{ form.processing ? (isJapanese ? '保存中...' : 'Menyimpan...') : (isJapanese ? '保存する' : 'Simpan Level') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { confirmDialog, notifySuccess } from '@/Utils/alert';
import { Award, PlusCircle, Edit, Trash2, X, CheckCircle2, Languages, Users } from 'lucide-vue-next';

const props = defineProps({
    languageLevels: {
        type: Array,
        default: () => [],
    },
});

const { isJapanese } = useLang();

const showModal = ref(false);
const isEditing = ref(false);
const editingLevelId = ref(null);

const form = useForm({
    code: '',
    name: '',
    name_jp: '',
    description: '',
    sort_order: 1,
    is_active: true,
});

const activeCount = computed(() => {
    return props.languageLevels.filter(lvl => lvl.is_active).length;
});

const totalKanjisLinked = computed(() => {
    return props.languageLevels.reduce((sum, lvl) => sum + (lvl.kanjis_count || 0), 0);
});

const totalStudentsLinked = computed(() => {
    return props.languageLevels.reduce((sum, lvl) => sum + (lvl.students_count || 0), 0);
});

const getLevelBadgeClass = (code) => {
    switch (code) {
        case 'N5':
            return 'bg-emerald-50 text-emerald-700 border-emerald-300';
        case 'N4':
            return 'bg-blue-50 text-blue-700 border-blue-300';
        case 'N3':
            return 'bg-indigo-50 text-indigo-700 border-indigo-300';
        case 'N2':
            return 'bg-purple-50 text-purple-700 border-purple-300';
        case 'N1':
            return 'bg-rose-50 text-rose-700 border-rose-300';
        case 'JFT_A2':
            return 'bg-amber-50 text-amber-700 border-amber-300';
        default:
            return 'bg-slate-100 text-slate-700 border-slate-300';
    }
};

const openCreateModal = () => {
    isEditing.value = false;
    editingLevelId.value = null;
    form.reset();
    form.clearErrors();
    form.code = '';
    form.name = '';
    form.name_jp = '';
    form.description = '';
    form.sort_order = (props.languageLevels.length || 0) + 1;
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (lvl) => {
    isEditing.value = true;
    editingLevelId.value = lvl.id;
    form.clearErrors();
    form.code = lvl.code;
    form.name = lvl.name;
    form.name_jp = lvl.name_jp || '';
    form.description = lvl.description || '';
    form.sort_order = lvl.sort_order ?? 1;
    form.is_active = Boolean(lvl.is_active);
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.master.language-levels.update', editingLevelId.value), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                notifySuccess('Level bahasa berhasil diperbarui!');
            },
        });
    } else {
        form.post(route('admin.master.language-levels.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                notifySuccess('Level bahasa baru berhasil ditambahkan!');
            },
        });
    }
};

const deleteLevel = async (lvl) => {
    const confirmed = await confirmDialog({
        title: isJapanese.value ? 'レベルの削除確認' : 'Hapus Level Bahasa?',
        text: isJapanese.value 
            ? `本当に「${lvl.name}」を削除しますか？` 
            : `Apakah Anda yakin ingin menghapus level "${lvl.name} (${lvl.code})"?`,
        confirmButtonText: isJapanese.value ? 'はい、削除します' : 'Ya, Hapus',
        confirmButtonColor: '#e11d48',
    });

    if (confirmed) {
        form.delete(route('admin.master.language-levels.destroy', lvl.id), {
            preserveScroll: true,
            onSuccess: () => {
                notifySuccess('Level berhasil dihapus!');
            },
        });
    }
};
</script>