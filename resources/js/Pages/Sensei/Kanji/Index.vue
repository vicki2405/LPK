<template>
    <Head :title="isJapanese ? '漢字マスター (Kanji Hub) - 正夢' : 'Bank Huruf & Kartu Kanji - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-5 sm:space-y-6 animate-fade-in pb-16">
            <!-- ================= TOP HEADER & HERO SECTION ================= -->
            <div class="relative overflow-hidden bg-white p-5 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-5">
                <!-- Japanese Watermark Background -->
                <div class="absolute -right-6 -bottom-6 font-jp text-8xl font-black text-slate-100 select-none pointer-events-none opacity-60">
                    漢字
                </div>

                <div class="relative z-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold mb-2.5 border border-indigo-200/60 font-jp">
                        <Languages class="w-3.5 h-3.5 text-indigo-600" />
                        <span>{{ isJapanese ? '漢字カード・文字データベース' : 'Bank Huruf & Kartu Kanji (Kanji Cards Hub)' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-jp text-base font-bold shadow-xs">
                            漢
                        </span>
                        <span>{{ isJapanese ? '漢字マスター' : 'Kanji (Bank Huruf & Kartu)' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                        {{ isJapanese 
                            ? '漢字、ひらがなの読み方、インドネシア語の意味をカードおよびテーブル形式で登録・管理できます。' 
                            : 'Kelola perbendaharaan huruf kanji, cara baca hiragana, serta arti bahasa Indonesia dalam format tabel dan kartu belajar interaktif.' 
                        }}
                    </p>
                </div>

                <div class="relative z-1 flex items-center gap-2.5 self-stretch sm:self-auto">
                    <Link 
                        :href="route('sensei.kanjis.create')"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer group"
                    >
                        <PlusCircle class="w-4 h-4 group-hover:rotate-90 transition-transform duration-200" />
                        <span>{{ isJapanese ? '+ 新規漢字登録' : '+ Tambah Kanji' }}</span>
                    </Link>
                </div>
            </div>

            <!-- ================= STATS SUMMARY CARDS ================= -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider font-jp">
                            {{ isJapanese ? '登録漢字総数' : 'Total Kanji' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-slate-900 mt-0.5 block font-mono">
                            {{ stats.total ?? 0 }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <Languages class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>

                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider font-jp">
                            {{ isJapanese ? 'N5レベル漢字' : 'Kanji N5' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-emerald-600 mt-0.5 block font-mono">
                            {{ stats.total_n5 ?? 0 }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <Sparkles class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>

                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider font-jp">
                            {{ isJapanese ? 'N4レベル漢字' : 'Kanji N4' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-blue-600 mt-0.5 block font-mono">
                            {{ stats.total_n4 ?? 0 }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <GraduationCap class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>

                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider font-jp">
                            {{ isJapanese ? 'マスター基準レベル' : 'Standar Level Admin' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-amber-600 mt-0.5 block font-mono">
                            {{ activeLanguageLevels.length }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                        <Award class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>
            </div>

            <!-- ================= FLASH MESSAGES ================= -->
            <div v-if="$page.props.flash?.success" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-xs">
                <CheckCircle2 class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                <p class="text-xs text-emerald-800 font-semibold">{{ $page.props.flash.success }}</p>
            </div>

            <!-- ================= TOOLBAR FILTER & PENCARIAN ================= -->
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <!-- Search Box -->
                <div class="relative flex-1">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input 
                        type="text" 
                        v-model="searchQuery" 
                        @keyup.enter="applyFilter"
                        :placeholder="isJapanese ? '漢字・ひらがな・インドネシア語の意味で検索 (Enter)...' : 'Cari huruf kanji, hiragana, atau arti Indonesia (Tekan Enter)...'"
                        class="w-full pl-10 pr-9 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all placeholder:text-slate-400 font-jp"
                    />
                    <button 
                        v-if="searchQuery" 
                        type="button"
                        @click="searchQuery = ''; applyFilter()" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer"
                    >
                        ✕
                    </button>
                </div>

                <!-- Right Action Bar: Level Pills, Reset Filter & View Switcher -->
                <div class="flex items-center justify-between md:justify-end gap-2 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
                    <!-- Level Filter Pills -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button 
                            type="button"
                            @click="selectedLevel = 'all'; applyFilter()"
                            class="px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer"
                            :class="selectedLevel === 'all' 
                                ? 'bg-slate-900 text-white shadow-xs' 
                                : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-900 border border-slate-200/70'"
                        >
                            {{ isJapanese ? 'すべてのレベル' : 'Semua Level' }}
                        </button>
                        <button 
                            type="button"
                            v-for="lvl in activeLanguageLevels"
                            :key="lvl.code"
                            @click="selectedLevel = lvl.code; applyFilter()"
                            class="px-3 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer font-mono"
                            :class="selectedLevel === lvl.code 
                                ? 'bg-indigo-600 text-white shadow-xs' 
                                : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-900 border border-slate-200/70'"
                        >
                            {{ lvl.code }}
                        </button>
                    </div>

                    <!-- Reset Filter Button -->
                    <button 
                        v-if="hasActiveFilter"
                        type="button" 
                        @click="resetFilter" 
                        class="px-3 py-2 rounded-xl text-xs font-bold text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors flex items-center gap-1.5 cursor-pointer shrink-0"
                        title="Reset Filter"
                    >
                        <RotateCcw class="w-3.5 h-3.5" />
                        <span class="hidden sm:inline">Reset</span>
                    </button>

                    <!-- View Switcher (Table vs Grid) -->
                    <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 shrink-0">
                        <button 
                            type="button" 
                            @click="viewMode = 'table'"
                            class="p-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                            :class="viewMode === 'table' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                            title="Tampilan Tabel Data"
                        >
                            <List class="w-4 h-4" />
                        </button>
                        <button 
                            type="button" 
                            @click="viewMode = 'grid'"
                            class="p-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                            :class="viewMode === 'grid' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                            title="Tampilan Kartu Belajar"
                        >
                            <LayoutGrid class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- ================= DATA LISTING ================= -->
            <div v-if="kanjis.data && kanjis.data.length > 0" class="space-y-6">
                <!-- 1. TABLE VIEW MODE -->
                <div v-if="viewMode === 'table'" class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 font-jp">
                                <tr>
                                    <th class="py-3.5 px-4 sm:px-6 w-20 text-center">Kanji</th>
                                    <th class="py-3.5 px-4">Cara Baca (Hiragana / Romaji)</th>
                                    <th class="py-3.5 px-4">Arti Indonesia</th>
                                    <th class="py-3.5 px-4 text-center">Level</th>
                                    <th class="py-3.5 px-4 text-center">Goresan</th>
                                    <th class="py-3.5 px-4 text-center sm:pr-6">{{ isJapanese ? '操作' : 'Aksi' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="k in kanjis.data" :key="k.id" class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-4 sm:px-6 text-center">
                                        <span class="font-jp text-3xl font-black text-slate-900 inline-block group-hover:scale-110 transition-transform select-all">
                                            {{ k.kanji }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-sm text-indigo-700 font-jp">
                                            {{ k.hiragana }}
                                        </div>
                                        <div v-if="k.romaji" class="text-[11px] text-slate-400 font-mono mt-0.5">
                                            {{ k.romaji }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800 text-xs sm:text-sm">
                                            {{ k.meaning_id }}
                                        </div>
                                        <div v-if="k.notes" class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 font-jp">
                                            {{ k.notes }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span 
                                            class="px-2.5 py-0.5 rounded-full text-xs font-black tracking-wide border font-mono inline-block shadow-2xs"
                                            :class="getLevelBadgeClass(k.level)"
                                        >
                                            {{ k.level }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-500">
                                        {{ k.stroke_count ? `${k.stroke_count} 画` : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center sm:pr-6">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <Link 
                                                :href="route('sensei.kanjis.edit', k.id)"
                                                class="p-1.5 rounded-xl text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors cursor-pointer"
                                                :title="isJapanese ? '漢字を編集' : 'Edit Kanji'"
                                            >
                                                <Edit class="w-4 h-4" />
                                            </Link>
                                            <button 
                                                type="button"
                                                @click="confirmDeleteKanji(k)"
                                                class="p-1.5 rounded-xl text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-colors cursor-pointer"
                                                :title="isJapanese ? '漢字を削除' : 'Hapus Kanji'"
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. CARD GRID VIEW MODE -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div 
                        v-for="k in kanjis.data" 
                        :key="k.id"
                        class="bg-white rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all p-5 flex flex-col justify-between space-y-4 group relative overflow-hidden"
                    >
                        <!-- Top Card Header: Level & Stroke Count -->
                        <div class="flex items-center justify-between gap-2">
                            <span 
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wide border font-mono shadow-2xs"
                                :class="getLevelBadgeClass(k.level)"
                            >
                                {{ k.level }}
                            </span>
                            <span v-if="k.stroke_count" class="text-[10px] font-mono text-slate-400 font-bold font-jp">
                                {{ k.stroke_count }} 画
                            </span>
                        </div>

                        <!-- Card Body: Huruf Kanji Besar, Hiragana & Arti -->
                        <div class="text-center py-2 space-y-1.5">
                            <div class="text-4xl sm:text-5xl font-black text-slate-900 font-jp tracking-tight group-hover:scale-105 transition-transform duration-200 select-all">
                                {{ k.kanji }}
                            </div>

                            <div class="text-sm sm:text-base font-bold text-indigo-600 font-jp tracking-wide">
                                {{ k.hiragana }}
                            </div>

                            <div v-if="k.romaji" class="text-[11px] font-mono text-slate-400">
                                {{ k.romaji }}
                            </div>

                            <div class="text-xs sm:text-sm font-black text-slate-800 pt-2.5 border-t border-slate-100 leading-snug">
                                {{ k.meaning_id }}
                            </div>
                        </div>

                        <!-- Catatan / Contoh Kata (Jika Ada) -->
                        <div v-if="k.notes" class="bg-slate-50 p-2.5 rounded-xl text-[11px] text-slate-600 font-jp line-clamp-2 leading-relaxed">
                            {{ k.notes }}
                        </div>

                        <!-- Card Footer: Aksi Edit & Hapus -->
                        <div class="flex items-center justify-end pt-2 border-t border-slate-100 gap-1.5">
                            <Link 
                                :href="route('sensei.kanjis.edit', k.id)"
                                class="p-1.5 rounded-xl text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors cursor-pointer"
                                :title="isJapanese ? '漢字を編集' : 'Edit Kanji'"
                            >
                                <Edit class="w-4 h-4" />
                            </Link>
                            <button 
                                type="button"
                                @click="confirmDeleteKanji(k)"
                                class="p-1.5 rounded-xl text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-colors cursor-pointer"
                                :title="isJapanese ? '漢字を削除' : 'Hapus Kanji'"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination Footer -->
                <div v-if="kanjis.links && kanjis.links.length > 3" class="p-4 bg-white rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <span class="text-slate-500 font-jp">
                        {{ isJapanese ? `全 ${kanjis.total} 件中 ${kanjis.from || 0}〜${kanjis.to || 0} 件を表示` : `Menampilkan ${kanjis.from || 0} - ${kanjis.to || 0} dari total ${kanjis.total} kanji` }}
                    </span>
                    <div class="flex items-center gap-1 flex-wrap justify-center">
                        <Link 
                            v-for="(link, i) in kanjis.links" 
                            :key="i"
                            :href="link.url || '#'"
                            class="px-3 py-1.5 rounded-xl font-bold transition-all text-xs"
                            :class="[
                                link.active ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100',
                                !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : 'cursor-pointer'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto">
                    <Languages class="w-6 h-6" />
                </div>
                <h3 class="text-base font-bold text-slate-800 font-jp">
                    {{ isJapanese ? '該当する漢字が見つかりません' : 'Tidak ada huruf kanji yang cocok' }}
                </h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    {{ isJapanese ? '検索条件を変更するか、新しい漢字を追加してください。' : 'Silakan gunakan kata kunci pencarian lain atau klik tombol "+ Tambah Kanji" untuk menambah kanji baru.' }}
                </p>
                <div class="pt-2">
                    <Link 
                        :href="route('sensei.kanjis.create')"
                        class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition-all cursor-pointer inline-block"
                    >
                        {{ isJapanese ? '+ 漢字を新規登録' : '+ Tambah Kanji Baru' }}
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { confirmDialog, notifySuccess } from '@/Utils/alert';
import { 
    Languages, 
    PlusCircle, 
    Edit, 
    Trash2, 
    Search, 
    Sparkles, 
    GraduationCap, 
    CheckCircle2, 
    List, 
    LayoutGrid, 
    RotateCcw,
    Award
} from 'lucide-vue-next';

const props = defineProps({
    kanjis: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    languageLevels: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({ search: '', level: 'all' }),
    },
    stats: {
        type: Object,
        default: () => ({ total: 0, total_n5: 0, total_n4: 0 }),
    },
});

const { isJapanese } = useLang();

// View Mode state: table vs grid
const viewMode = ref('table');

// Filter & Search states
const searchQuery = ref(props.filters.search || '');
const selectedLevel = ref(props.filters.level || 'all');

const activeLanguageLevels = computed(() => {
    if (props.languageLevels && props.languageLevels.length > 0) {
        return props.languageLevels;
    }
    // Fallback standard levels
    return [
        { code: 'N5', name: 'JLPT N5 (Tingkat Dasar)' },
        { code: 'N4', name: 'JLPT N4 & JFT-Basic A2 (Standar Kerja)' },
        { code: 'N3', name: 'JLPT N3 (Tingkat Menengah)' },
        { code: 'N2', name: 'JLPT N2 (Tingkat Mahir / Karir)' },
        { code: 'N1', name: 'JLPT N1 (Tingkat Fasih / Native)' },
    ];
});

const hasActiveFilter = computed(() => {
    return Boolean(searchQuery.value) || (selectedLevel.value !== 'all');
});

const applyFilter = () => {
    router.get(route('sensei.kanjis.index'), {
        search: searchQuery.value,
        level: selectedLevel.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const resetFilter = () => {
    searchQuery.value = '';
    selectedLevel.value = 'all';
    applyFilter();
};

const getLevelBadgeClass = (level) => {
    switch (level) {
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

const deleteForm = useForm({});

const confirmDeleteKanji = async (k) => {
    const confirmed = await confirmDialog({
        title: isJapanese.value ? '漢字の削除確認' : 'Hapus Huruf Kanji?',
        text: isJapanese.value 
            ? `本当に「${k.kanji}」(${k.meaning_id}) を削除しますか？` 
            : `Apakah Anda yakin ingin menghapus huruf kanji "${k.kanji}" (${k.meaning_id})?`,
        confirmButtonText: isJapanese.value ? 'はい、削除します' : 'Ya, Hapus',
        confirmButtonColor: '#e11d48',
    });

    if (confirmed) {
        deleteForm.delete(route('sensei.kanjis.destroy', k.id), {
            preserveScroll: true,
            onSuccess: () => {
                notifySuccess(`Kanji [${k.kanji}] berhasil dihapus!`);
            },
        });
    }
};
</script>
