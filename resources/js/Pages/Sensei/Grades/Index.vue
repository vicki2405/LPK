<template>
    <Head :title="isJapanese ? '成績・習熟度一覧 - 正夢' : 'Rapor & Kemahiran Siswa - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in">
            <!-- Top Header Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-semibold mb-2 border border-emerald-200/60">
                        <Award class="w-3.5 h-3.5 text-emerald-600" />
                        <span>{{ isJapanese ? '成績・習熟度評価' : 'Buku Rapor & Evaluasi LPK' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <Award class="w-6 h-6 text-emerald-600" />
                        <span>{{ isJapanese ? '実習生・成績＆習熟度管理' : 'Rapor & Kemahiran Siswa' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ isJapanese ? '実習生ごとのCBT試験スコア、合格率、および特定技能・JLPT N4到達度の総合管理' : 'Pantau rekap nilai CBT, riwayat pengerjaan simulasi, dan tingkat kesiapan kelulusan N4 / JFT-Basic A2 masing-masing siswa.' }}
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-80">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input 
                        type="text" 
                        v-model="searchQuery" 
                        @keyup.enter="applyFilter"
                        :placeholder="isJapanese ? '氏名・NIKで検索...' : 'Cari nama atau NIK siswa...'"
                        class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                    />
                </div>

                <div class="w-full sm:w-64">
                    <select v-model="selectedBatch" @change="applyFilter" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                        <option value="">{{ isJapanese ? 'すべての期生' : 'Semua Angkatan / Kelas' }}</option>
                        <option v-for="b in batches" :key="b.id" :value="b.id">
                            {{ b.name }} ({{ b.code }})
                        </option>
                    </select>
                </div>
            </div>

            <!-- Student Grade Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 font-jp">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6 text-left">{{ isJapanese ? '実習生情報' : 'Nama Siswa' }}</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '所属期生' : 'Angkatan' }}</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? 'CBT受験回数' : 'Ujian Diikuti' }}</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '最新スコア' : 'Skor Terakhir' }}</th>
                                <th class="py-3.5 px-4 text-center">{{ isJapanese ? '習熟度・合否判定' : 'Tingkat Kemahiran' }}</th>
                                <th class="py-3.5 px-4 sm:px-6 text-right">{{ isJapanese ? 'アクション' : 'Aksi' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            <tr v-for="st in students.data" :key="st.id" class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                                            {{ st.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-slate-900">{{ st.name }}</p>
                                            <span class="text-[11px] text-slate-400">{{ st.email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-red-50 text-japan-red font-bold text-xs border border-red-100">
                                        {{ st.batches?.[0]?.name || (isJapanese ? '未所属' : 'Belum Ada Kelas') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-bold text-slate-800 font-mono">{{ st.exam_sessions_count || 0 }} {{ isJapanese ? '回' : 'Sesi' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div v-if="st.exam_sessions && st.exam_sessions.length > 0">
                                        <span class="font-bold text-sm text-slate-900 font-mono">
                                            {{ st.exam_sessions[0]?.total_score ?? st.exam_sessions[0]?.score ?? 0 }} / {{ st.exam_sessions[0]?.exam?.max_score || 180 }}
                                        </span>
                                    </div>
                                    <span v-else class="text-slate-400 font-mono">-</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span v-if="st.exam_sessions && st.exam_sessions.length > 0"
                                        class="px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1 shadow-2xs"
                                        :class="st.exam_sessions[0]?.is_passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                                        {{ st.exam_sessions[0]?.is_passed ? (isJapanese ? '合格 (Lolos N4)' : 'Lulus Passing Grade') : (isJapanese ? '不合格 (再受験)' : 'Belum Lulus') }}
                                    </span>
                                    <span v-else class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500">
                                        {{ isJapanese ? '未受験' : 'Belum Ujian' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 sm:px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link 
                                            :href="route('sensei.grades.show', st.id)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 text-xs font-bold active:scale-[0.98] transition-all shadow-2xs border border-emerald-200/60"
                                        >
                                            <Eye class="w-3.5 h-3.5" />
                                            <span>{{ isJapanese ? '成績詳細' : 'Buka Rapor' }}</span>
                                        </Link>

                                        <button 
                                            v-if="st.exam_sessions_count > 0"
                                            @click="resetStudent(st)"
                                            type="button"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 text-xs font-bold active:scale-[0.98] transition-all shadow-2xs border border-rose-200/60 cursor-pointer"
                                            title="Reset semua riwayat ujian siswa"
                                        >
                                            <Trash2 class="w-3.5 h-3.5" />
                                            <span class="hidden sm:inline">{{ isJapanese ? '履歴リセット' : 'Reset Nilai' }}</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="students.data.length === 0">
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    {{ isJapanese ? '実習生データが見つかりませんでした。' : 'Belum ada data nilai siswa.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="students.links && students.links.length > 3" class="flex items-center justify-center gap-1.5 pt-4">
                <Link
                    v-for="(link, i) in students.links"
                    :key="i"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all"
                    :class="[
                        link.active ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50',
                        !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : 'active:scale-[0.95]'
                    ]"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { notifySuccess, confirmDialog } from '@/Utils/alert';
import { 
    Award, 
    Search,
    Eye,
    Trash2
} from 'lucide-vue-next';

const props = defineProps({
    students: Object,
    batches: Array,
    recentSessions: Array,
    filters: Object,
});

const { isJapanese } = useLang();

const searchQuery = ref(props.filters?.search || '');
const selectedBatch = ref(props.filters?.batch_id || '');

const applyFilter = () => {
    router.get(route('sensei.grades.index'), {
        search: searchQuery.value,
        batch_id: selectedBatch.value,
    }, { preserveState: true, replace: true });
};

const resetStudent = (student) => {
    confirmDialog(
        isJapanese.value ? `「${student.name}」の全試験履歴をリセットしますか？` : `Reset semua nilai ujian ${student.name}?`,
        isJapanese.value ? 'この操作により、実習生の全CBT受験データとスコアが削除されます。' : 'Seluruh riwayat sesi ujian CBT dan skor siswa ini akan dihapus dari sistem.'
    ).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('sensei.grades.reset-student', student.id), {
                preserveScroll: true,
                onSuccess: () => {
                    notifySuccess(
                        isJapanese.value ? 'リセット完了' : 'Berhasil', 
                        isJapanese.value ? '受験履歴をリセットしました。' : `Riwayat ujian ${student.name} berhasil dibersihkan.`
                    );
                },
            });
        }
    });
};
</script>
