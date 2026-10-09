<template>
    <Head :title="isJapanese ? 'LMS教材・課別管理 - 正夢' : 'LMS Materi & Kurikulum - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-16">
            <!-- ================= HEADER SECTION ================= -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold mb-2 border border-blue-200/60 font-jp">
                        <BookOpen class="w-3.5 h-3.5" />
                        <span>{{ isJapanese ? '教材・カリキュラム管理' : 'Modul Pembelajaran LPK' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <BookOpen class="w-6 h-6 text-blue-600" />
                        <span>{{ isJapanese ? 'LMS教材・課別カリキュラム' : 'LMS Materi & Kurikulum Bab' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ isJapanese ? '課別（1〜50課）の学習項目・文法解説・実習生向け教材の管理' : 'Kelola urutan bab pembelajaran, materi pokok tata bahasa (Bunpou), dan modul kelas.' }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button 
                        @click="openCreateChapterModal"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 active:scale-[0.98] transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto"
                    >
                        <PlusCircle class="w-4 h-4" />
                        <span>{{ isJapanese ? '+ 新規課を追加' : '+ Tambah Bab Baru' }}</span>
                    </button>
                </div>
            </div>

            <!-- ================= FILTER & SEARCH TOOLBAR ================= -->
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/90 shadow-xs space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                    
                    <!-- Search Input (lg:col-span-5) -->
                    <div class="relative lg:col-span-5">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                        <input 
                            type="text" 
                            v-model="searchQuery" 
                            @keyup.enter="applyFilters"
                            :placeholder="isJapanese ? '課番号やタイトルで検索 (Enter)...' : 'Cari nomor bab atau judul materi (Enter)...'"
                            class="w-full pl-10 pr-9 py-2.5 rounded-2xl bg-slate-50/70 border border-slate-200 text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all placeholder:text-slate-400"
                        />
                        <button 
                            v-if="searchQuery" 
                            @click="searchQuery = ''; applyFilters()" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer p-0.5"
                        >
                            <X class="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <!-- Filter Status Publikasi (lg:col-span-3) -->
                    <div class="lg:col-span-3">
                        <select 
                            v-model="selectedStatus" 
                            @change="applyFilters"
                            class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all cursor-pointer"
                        >
                            <option value="">{{ isJapanese ? 'すべてのステータス (Semua Status)' : 'Semua Status Tampilan' }}</option>
                            <option value="published">🟢 {{ isJapanese ? '公開中 (Aktif / Publik)' : 'Aktif (Dapat Diakses Siswa)' }}</option>
                            <option value="draft">⚪ {{ isJapanese ? '下書き (Draft)' : 'Draft (Disembunyikan)' }}</option>
                        </select>
                    </div>

                    <!-- Short / Tampilan Per Halaman (10, 20, 30, 50) (lg:col-span-2) -->
                    <div class="lg:col-span-2">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[11px] font-bold text-slate-500 shrink-0 hidden xl:inline">Tampil:</span>
                            <select 
                                v-model="perPage" 
                                @change="applyFilters"
                                class="w-full px-3 py-2.5 rounded-2xl border border-slate-200 text-xs font-black text-slate-800 bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all cursor-pointer font-mono"
                                title="Jumlah bab per halaman"
                            >
                                <option :value="10">10 / halaman</option>
                                <option :value="20">20 / halaman</option>
                                <option :value="30">30 / halaman</option>
                                <option :value="50">50 / halaman</option>
                            </select>
                        </div>
                    </div>

                    <!-- Urutan & Reset (lg:col-span-2) -->
                    <div class="lg:col-span-2 flex items-center justify-end gap-2">
                        <!-- Urutan Bab -->
                        <button 
                            type="button"
                            @click="toggleSort"
                            class="px-3 py-2.5 rounded-2xl border text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer flex-1 sm:flex-initial justify-center"
                            :class="selectedSort === 'desc' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                            :title="selectedSort === 'desc' ? 'Urutan: Bab Terbesar -> Terkecil' : 'Urutan: Bab 1 -> Terakhir'"
                        >
                            <ArrowUpDown class="w-3.5 h-3.5 text-blue-600" />
                            <span>{{ selectedSort === 'desc' ? 'Bab 50→1' : 'Bab 1→50' }}</span>
                        </button>

                        <!-- Reset Filter -->
                        <button 
                            v-if="hasActiveFilter"
                            type="button" 
                            @click="resetFilters" 
                            class="p-2.5 rounded-2xl text-xs font-bold text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors border border-slate-200 cursor-pointer shrink-0"
                            title="Reset Semua Filter"
                        >
                            <RotateCcw class="w-4 h-4" />
                        </button>
                    </div>

                </div>
            </div>

            <!-- ================= TABLE VIEW ================= -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-black text-slate-500 uppercase tracking-wider font-jp">
                                <th class="py-3.5 px-4 sm:px-6 w-28 text-center">{{ isJapanese ? '課番号' : 'No. Bab' }}</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? '学習タイトル・概要' : 'Judul Materi Pokok & Capaian Belajar' }}</th>
                                <th class="py-3.5 px-4 w-44 text-center">{{ isJapanese ? '収録教材数' : 'Materi Pembelajaran' }}</th>
                                <th class="py-3.5 px-4 w-32 text-center">{{ isJapanese ? '公開状態' : 'Status' }}</th>
                                <th class="py-3.5 px-4 sm:px-6 w-52 text-right">{{ isJapanese ? '操作' : 'Aksi' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            <tr 
                                v-for="chapter in chapterList" 
                                :key="chapter.id" 
                                class="hover:bg-blue-50/40 transition-colors group"
                            >
                                <!-- No. Bab -->
                                <td class="py-3.5 px-4 sm:px-6 text-center">
                                    <div class="inline-flex flex-col items-center justify-center px-3 py-1.5 rounded-2xl bg-blue-50 text-blue-800 border border-blue-200/70 shadow-2xs group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-all">
                                        <span class="text-xs font-black tracking-tight font-mono">Bab {{ chapter.chapter_number }}</span>
                                        <span class="text-[10px] font-jp opacity-80 font-bold">第{{ chapter.chapter_number }}課</span>
                                    </div>
                                </td>

                                <!-- Judul Bab & Deskripsi -->
                                <td class="py-3.5 px-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <Link 
                                                :href="route('sensei.lms.show', chapter.id)"
                                                class="text-sm font-black text-slate-900 group-hover:text-blue-600 transition-colors tracking-tight line-clamp-1 cursor-pointer"
                                            >
                                                {{ chapter.title }}
                                            </Link>
                                        </div>
                                        <div v-if="chapter.learning_indicators && chapter.learning_indicators.length > 0" class="flex flex-wrap gap-1 mt-0.5">
                                            <span 
                                                v-for="ind in chapter.learning_indicators" 
                                                :key="ind.id"
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/70 text-[10px] font-bold"
                                            >
                                                <Target class="w-3 h-3 text-rose-600 shrink-0" />
                                                <span class="truncate max-w-[280px]">{{ ind.name }}</span>
                                            </span>
                                        </div>
                                        <p v-if="chapter.description" class="text-xs text-slate-500 line-clamp-1 font-normal leading-relaxed">
                                            {{ chapter.description }}
                                        </p>
                                        <span v-else-if="!chapter.learning_indicators || chapter.learning_indicators.length === 0" class="text-[11px] text-slate-400 italic">
                                            Belum ada catatan deskripsi.
                                        </span>
                                    </div>
                                </td>

                                <!-- Jumlah Materi Pembelajaran -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100/90 text-slate-800 font-bold border border-slate-200/60 font-jp">
                                        <FileText class="w-3.5 h-3.5 text-blue-600" />
                                        <span class="font-mono text-xs font-black">{{ chapter.lessons_count ?? chapter.lessons?.length ?? 0 }}</span>
                                        <span class="text-[11px] text-slate-600 font-medium">Materi</span>
                                    </div>
                                </td>

                                <!-- Status Publikasi -->
                                <td class="py-3.5 px-4 text-center">
                                    <span 
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold border"
                                        :class="chapter.is_published 
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
                                            : 'bg-slate-100 text-slate-500 border-slate-200'"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full" :class="chapter.is_published ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                        <span>{{ chapter.is_published ? (isJapanese ? '公開中' : 'Aktif') : 'Draft' }}</span>
                                    </span>
                                </td>

                                <!-- Aksi Cepat -->
                                <td class="py-3.5 px-4 sm:px-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Tombol Kelola Materi -->
                                        <Link 
                                            :href="route('sensei.lms.show', chapter.id)"
                                            class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-xs font-bold inline-flex items-center gap-1.5 shadow-xs shadow-blue-500/20 transition-all cursor-pointer shrink-0"
                                            title="Buka Halaman Editor Materi Bab"
                                        >
                                            <FileText class="w-3.5 h-3.5" />
                                            <span>Kelola Materi</span>
                                        </Link>

                                        <!-- Edit Info Modal Button -->
                                        <button 
                                            type="button"
                                            @click="openEditChapterModal(chapter)"
                                            class="p-2 rounded-xl text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer border border-transparent hover:border-blue-100"
                                            title="Edit Info Bab"
                                        >
                                            <Edit class="w-3.5 h-3.5" />
                                        </button>

                                        <!-- Hapus Bab Button -->
                                        <button 
                                            type="button"
                                            @click="deleteChapter(chapter)"
                                            class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer border border-transparent hover:border-rose-100"
                                            title="Hapus Bab"
                                        >
                                            <Trash2 class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty Row State -->
                            <tr v-if="chapterList.length === 0">
                                <td colspan="5" class="py-14 text-center">
                                    <BookOpen class="w-12 h-12 text-slate-300 mx-auto mb-3" />
                                    <h3 class="text-sm font-bold text-slate-800">
                                        {{ isJapanese ? '該当する課データがありません' : 'Tidak Ada Bab Ditemukan' }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                        {{ searchQuery || selectedStatus ? 'Coba ubah kata kunci pencarian atau reset filter di atas.' : 'Belum ada bab materi pada kurikulum ini. Silakan klik tombol "+ Tambah Bab Baru" di atas.' }}
                                    </p>
                                    <button 
                                        v-if="hasActiveFilter"
                                        type="button"
                                        @click="resetFilters"
                                        class="mt-3 px-4 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition-colors cursor-pointer inline-flex items-center gap-1.5"
                                    >
                                        <RotateCcw class="w-3.5 h-3.5" />
                                        <span>Reset Filter</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ================= TABLE PAGINATION FOOTER ================= -->
                <div v-if="isPaginated" class="p-4 sm:p-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/50">
                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan <span class="font-bold text-slate-800 font-mono">{{ chapters.from || 1 }}</span> - 
                        <span class="font-bold text-slate-800 font-mono">{{ chapters.to || chapterList.length }}</span> dari 
                        <span class="font-bold text-slate-800 font-mono">{{ chapters.total || chapterList.length }}</span> bab materi
                    </div>

                    <!-- Links Pagination Buttons -->
                    <div v-if="chapters.links && chapters.links.length > 3" class="flex items-center gap-1 flex-wrap">
                        <Link 
                            v-for="(link, i) in chapters.links" 
                            :key="i"
                            :href="link.url || '#'"
                            class="px-3 py-1.5 rounded-xl font-bold text-xs transition-all"
                            :class="[
                                link.active ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100',
                                !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : 'cursor-pointer'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= MODAL TAMBAH / EDIT INFO BAB ================= -->
        <div v-if="showChapterModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="relative bg-white rounded-[2rem] border border-slate-100 shadow-2xl max-w-lg w-full p-6 sm:p-7 max-h-[90vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-4 mb-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg shadow-2xs">
                            <BookOpen class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                                <span>{{ isEditingChapter ? (isJapanese ? '課情報の編集' : 'Edit Informasi Bab') : (isJapanese ? '新規課の追加' : 'Tambah Bab Baru') }}</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ isJapanese ? 'カリキュラムの課番号と学習タイトルを設定します' : 'Atur kurikulum, nomor bab, dan materi pokok pembelajaran' }}
                            </p>
                        </div>
                    </div>
                    <button 
                        @click="showChapterModal = false" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors cursor-pointer"
                        title="Tutup"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>

                <form @submit.prevent="submitChapterForm" class="space-y-4">
                    <!-- 1. Kurikulum (Full Width) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <span>{{ isJapanese ? '対象カリキュラム *' : 'Kurikulum Pembelajaran *' }}</span>
                        </label>
                        <div class="relative">
                            <select 
                                v-model="chapterForm.course_id" 
                                required 
                                class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-200 bg-slate-50/70 text-xs sm:text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all appearance-none cursor-pointer"
                            >
                                <option v-for="c in courses" :key="c.id" :value="c.id">
                                    📚 {{ c.title }}
                                </option>
                            </select>
                            <ChevronDown class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                        </div>
                    </div>

                    <!-- 2. Grid: Nomor Bab & Status Publikasi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Nomor Bab -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                {{ isJapanese ? '課番号 *' : 'Nomor Bab *' }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-black text-blue-600">
                                    Bab
                                </div>
                                <input 
                                    type="number" 
                                    v-model="chapterForm.chapter_number" 
                                    required 
                                    min="1" 
                                    class="w-full pl-12 pr-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/70 text-xs sm:text-sm font-black text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all font-mono"
                                />
                            </div>
                            <span class="text-[10px] text-slate-400 font-jp font-semibold mt-1 block">
                                第 {{ chapterForm.chapter_number || 1 }} 課
                            </span>
                        </div>

                        <!-- Status Publikasi -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                {{ isJapanese ? '公開ステータス' : 'Status Tampilan' }}
                            </label>
                            <div class="grid grid-cols-2 gap-1.5 bg-slate-100/80 p-1 rounded-xl border border-slate-200/60">
                                <button
                                    type="button"
                                    @click="chapterForm.is_published = true"
                                    class="py-1.5 px-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer"
                                    :class="chapterForm.is_published ? 'bg-white text-emerald-700 shadow-2xs border border-slate-200/50' : 'text-slate-500 hover:text-slate-800'"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>{{ isJapanese ? '公開' : 'Aktif' }}</span>
                                </button>
                                <button
                                    type="button"
                                    @click="chapterForm.is_published = false"
                                    class="py-1.5 px-2 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer"
                                    :class="!chapterForm.is_published ? 'bg-white text-slate-800 shadow-2xs border border-slate-200/50' : 'text-slate-500 hover:text-slate-800'"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    <span>Draft</span>
                                </button>
                            </div>
                            <span class="text-[10px] text-slate-400 mt-1 block">
                                {{ chapterForm.is_published ? '🟢 Siswa dapat mengakses bab ini' : '⚪ Disembunyikan dari portal siswa' }}
                            </span>
                        </div>
                    </div>

                    <!-- 3. Judul Bab -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            {{ isJapanese ? '課のタイトル（学習目標・項目） *' : 'Judul Bab (Materi Pokok) *' }}
                        </label>
                        <input 
                            type="text" 
                            v-model="chapterForm.title" 
                            required 
                            :placeholder="isJapanese ? '例: 自己紹介と挨拶 (Perkenalan Diri & Salam)' : 'Misal: Perkenalan Diri & Salam (自己紹介)'" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/70 text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all placeholder:text-slate-400"
                        />
                    </div>

                    <!-- 4. Capaian Pembelajaran (Dropdown dari Admin Master Learning Indicators) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                <Target class="w-3.5 h-3.5 text-rose-600" />
                                <span>{{ isJapanese ? '学習到達目標・評価指標（マスター連動）' : 'Capaian Pembelajaran (Indikator Belajar)' }}</span>
                            </label>
                            <span class="text-[10px] text-slate-400 font-medium">Opsional</span>
                        </div>
                        <div class="relative">
                            <select 
                                v-model="chapterForm.learning_indicator_id"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/70 text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all cursor-pointer appearance-none pr-9"
                            >
                                <option value="">{{ isJapanese ? '-- 目標指標を選択（未設定） --' : '-- Pilih Capaian Pembelajaran dari Master Admin --' }}</option>
                                <option 
                                    v-for="ind in learningIndicators" 
                                    :key="ind.id" 
                                    :value="ind.id"
                                >
                                    {{ ind.code ? `[${ind.code}] ` : '' }}{{ ind.name }} (Level: {{ ind.level || 'Umum' }})
                                </option>
                            </select>
                            <ChevronDown class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">
                            {{ isJapanese ? '※ 管理者ポータル（/admin/master/learning-indicators）の指標と自動連動します。' : 'Otomatis terhubung dengan master indikator kurikulum admin untuk evaluasi & CBT.' }}
                        </p>
                    </div>

                    <!-- 5. Catatan / Ringkasan Tambahan Bab (Opsional) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                {{ isJapanese ? '補足メモ・概要（任意）' : 'Catatan / Ringkasan Tambahan Bab' }}
                            </label>
                            <span class="text-[10px] text-slate-400 font-medium">Opsional</span>
                        </div>
                        <textarea 
                            v-model="chapterForm.description" 
                            rows="2"
                            :placeholder="isJapanese ? 'この課の補足事項や学習メモ...' : 'Penjelasan singkat catatan topik tata bahasa atau materi pokok bab ini...'" 
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50/70 text-xs text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 focus:outline-none transition-all resize-none placeholder:text-slate-400"
                        ></textarea>
                    </div>

                    <!-- Modal Actions Footer -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button 
                            type="button" 
                            @click="showChapterModal = false" 
                            class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold transition-all cursor-pointer"
                        >
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button 
                            type="submit" 
                            :disabled="chapterForm.processing" 
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-xs font-bold shadow-md shadow-blue-500/25 transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
                        >
                            <Save class="w-4 h-4" />
                            <span>{{ chapterForm.processing ? (isJapanese ? '保存中...' : 'Menyimpan...') : (isEditingChapter ? (isJapanese ? '更新を保存' : 'Simpan Perubahan') : (isJapanese ? '課を登録' : 'Simpan Bab')) }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { notifySuccess, confirmDialog } from '@/Utils/alert';
import { 
    BookOpen, 
    PlusCircle, 
    Edit, 
    Trash2, 
    X,
    FileText,
    ChevronDown,
    Save,
    Search,
    RotateCcw,
    ArrowUpDown,
    Target
} from 'lucide-vue-next';

const props = defineProps({
    courses: Array,
    selectedCourseId: Number,
    chapters: [Object, Array],
    filters: Object,
    learningIndicators: {
        type: Array,
        default: () => [],
    },
});

const { isJapanese } = useLang();

// Extract array of chapters whether paginated object or plain array
const chapterList = computed(() => {
    if (Array.isArray(props.chapters)) {
        return props.chapters;
    }
    return props.chapters?.data || [];
});

const isPaginated = computed(() => {
    return Boolean(props.chapters && typeof props.chapters === 'object' && 'total' in props.chapters);
});

// Filter & Sort reactive states
const searchQuery = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || '');
const selectedSort = ref(props.filters?.sort || 'asc');
const perPage = ref(Number(props.filters?.per_page) || 20);

const hasActiveFilter = computed(() => {
    return Boolean(
        searchQuery.value || 
        selectedStatus.value || 
        (selectedSort.value && selectedSort.value !== 'asc') || 
        perPage.value !== 20
    );
});

const applyFilters = () => {
    router.get(route('sensei.lms.index'), {
        course_id: props.selectedCourseId,
        search: searchQuery.value || undefined,
        status: selectedStatus.value || undefined,
        sort: selectedSort.value !== 'asc' ? selectedSort.value : undefined,
        per_page: perPage.value,
    }, { 
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const resetFilters = () => {
    searchQuery.value = '';
    selectedStatus.value = '';
    selectedSort.value = 'asc';
    perPage.value = 20;
    router.get(route('sensei.lms.index'), {
        course_id: props.selectedCourseId,
    }, { 
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const toggleSort = () => {
    selectedSort.value = selectedSort.value === 'asc' ? 'desc' : 'asc';
    applyFilters();
};

const changeCourse = (courseId) => {
    router.get(route('sensei.lms.index'), { 
        course_id: courseId,
        search: searchQuery.value || undefined,
        status: selectedStatus.value || undefined,
        sort: selectedSort.value !== 'asc' ? selectedSort.value : undefined,
        per_page: perPage.value,
    }, { 
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

// Chapter Modal State
const showChapterModal = ref(false);
const isEditingChapter = ref(false);
const editingChapterId = ref(null);

const chapterForm = useForm({
    type: 'chapter',
    course_id: props.selectedCourseId || 1,
    chapter_number: 1,
    title: '',
    learning_indicator_id: '',
    description: '',
    is_published: true,
});

const openCreateChapterModal = () => {
    isEditingChapter.value = false;
    editingChapterId.value = null;
    chapterForm.reset();
    chapterForm.course_id = props.selectedCourseId || (props.courses?.[0]?.id || 1);
    
    // Auto increment chapter_number based on total
    const totalCount = isPaginated.value ? (props.chapters.total || 0) : chapterList.value.length;
    chapterForm.chapter_number = totalCount + 1;
    chapterForm.learning_indicator_id = '';
    chapterForm.description = '';
    chapterForm.is_published = true;
    showChapterModal.value = true;
};

const openEditChapterModal = (ch) => {
    isEditingChapter.value = true;
    editingChapterId.value = ch.id;
    chapterForm.course_id = ch.course_id;
    chapterForm.chapter_number = ch.chapter_number;
    chapterForm.title = ch.title;
    const linkedInd = ch.learning_indicators?.[0]?.id || 
                      (props.learningIndicators?.find(i => i.chapter_id === ch.id)?.id || '');
    chapterForm.learning_indicator_id = linkedInd;
    chapterForm.description = ch.description || '';
    chapterForm.is_published = Boolean(ch.is_published);
    showChapterModal.value = true;
};

const submitChapterForm = () => {
    if (isEditingChapter.value) {
        chapterForm.put(route('sensei.lms.update', editingChapterId.value), {
            onSuccess: () => {
                showChapterModal.value = false;
                notifySuccess('Berhasil', 'Informasi Bab berhasil diperbarui.');
            },
        });
    } else {
        chapterForm.post(route('sensei.lms.store'), {
            onSuccess: () => {
                showChapterModal.value = false;
                notifySuccess('Berhasil', 'Bab materi baru berhasil ditambahkan.');
            },
        });
    }
};

const deleteChapter = (ch) => {
    confirmDialog(
        `Hapus Bab ${ch.chapter_number}?`,
        'Seluruh materi di dalam bab ini akan ikut terhapus.'
    ).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('sensei.lms.destroy', ch.id), {
                data: { type: 'chapter' },
                onSuccess: () => {
                    notifySuccess('Terhapus', 'Bab materi berhasil dihapus.');
                },
            });
        }
    });
};
</script>
