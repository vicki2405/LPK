<template>
    <Head :title="isJapanese ? 'CBT問題バンク・科目別パケット — 指導員' : 'Bank Soal CBT per Mata Pelajaran — Sensei Portal'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-16">

            <!-- ================= HEADER SECTION ================= -->
            <div class="bg-gradient-to-br from-white via-slate-50 to-indigo-50/30 border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-indigo-600 via-blue-500 to-cyan-400 rounded-l-3xl"></div>

                <div class="pl-2">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-black mb-2 border border-indigo-200/80 shadow-2xs">
                        <BookOpenCheck class="w-3.5 h-3.5" />
                        <span>{{ isJapanese ? 'CBT学校モデル問題管理' : 'Bank Soal CBT Standar Mapel Sekolah' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>{{ isJapanese ? '科目別 CBT 問題パッケージ一覧' : 'Bank Soal CBT per Mata Pelajaran (Mapel)' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                        {{ isJapanese 
                            ? '科目（Mapel）ごとに整理されたCBT問題パッケージ。問題の執筆・管理と生徒への出題（有効化）を1つの表で迅速に行えます。' 
                            : 'Kelola paket soal per Mata Pelajaran dalam format tabel ringkas. Tulis butir soal dan aktifkan akses ujian siswa secara instan.' 
                        }}
                    </p>
                </div>

                <!-- Tombol Aksi Utama -->
                <div class="flex items-center gap-2.5 shrink-0 pl-2 md:pl-0 flex-wrap">
                    <a 
                        :href="route('sensei.questions.template.download')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 hover:text-blue-600 border border-slate-300 text-xs font-black shadow-xs transition-all cursor-pointer"
                        title="Unduh Template Microsoft Word (.docx) resmi LPK"
                    >
                        <FileDown class="w-4 h-4 text-blue-600" />
                        <span>{{ isJapanese ? 'Wordテンプレート' : 'Unduh Template Word' }}</span>
                    </a>

                    <button 
                        @click="openAddSubjectModal"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 hover:text-indigo-600 border border-slate-300 text-xs font-black shadow-xs transition-all cursor-pointer"
                    >
                        <FolderPlus class="w-4 h-4 text-indigo-600" />
                        <span>{{ isJapanese ? '+ 科目を追加' : '+ Tambah Mapel' }}</span>
                    </button>

                    <button 
                        @click="openAddPackageModal"
                        type="button"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-500/25 transition-all cursor-pointer"
                    >
                        <PlusCircle class="w-4 h-4" />
                        <span>{{ isJapanese ? '+ 新規パッケージ作成' : '+ Buat Paket Soal Baru' }}</span>
                    </button>
                </div>
            </div>

            <!-- ================= STATS SUMMARY CARDS ================= -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <FolderKanban class="w-5 h-5" />
                    </div>
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Mata Pelajaran</span>
                        <span class="text-xl sm:text-2xl font-black text-slate-900 font-mono">{{ stats.total_subjects }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <Layers class="w-5 h-5" />
                    </div>
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Paket Soal</span>
                        <span class="text-xl sm:text-2xl font-black text-slate-900 font-mono">{{ stats.total_packages }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <CheckCircle2 class="w-5 h-5" />
                    </div>
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Paket Aktif (Siswa)</span>
                        <span class="text-xl sm:text-2xl font-black text-emerald-600 font-mono">{{ stats.total_active }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <HelpCircle class="w-5 h-5" />
                    </div>
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Butir Soal Masuk</span>
                        <span class="text-xl sm:text-2xl font-black text-amber-600 font-mono">{{ stats.total_questions }}</span>
                    </div>
                </div>
            </div>

            <!-- ================= TOOLBAR: FILTER MAPEL, STATUS & PENCARIAN ================= -->
            <div class="bg-white rounded-3xl border border-slate-200/90 p-4 sm:p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3.5">
                <div class="flex items-center gap-2.5 flex-wrap flex-1">
                    <!-- Dropdown Filter Mapel -->
                    <div class="min-w-[200px]">
                        <select 
                            v-model="activeSubjectId"
                            @change="applyFilter"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 bg-slate-50/60 cursor-pointer"
                        >
                            <option value="">Semua Mata Pelajaran ({{ stats.total_packages }} Paket)</option>
                            <option v-for="sub in subjects" :key="sub.id" :value="sub.id">
                                📚 {{ sub.name }} ({{ sub.exams_count ?? 0 }} Paket)
                            </option>
                        </select>
                    </div>

                    <!-- Filter Status Ujian -->
                    <div class="min-w-[150px]">
                        <select 
                            v-model="activeStatus"
                            @change="applyFilter"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 bg-slate-50/60 cursor-pointer"
                        >
                            <option value="">Semua Status</option>
                            <option value="active">🟢 Hanya Aktif (Siswa)</option>
                            <option value="inactive">⚪ Hanya Nonaktif</option>
                        </select>
                    </div>

                    <!-- Tombol Reset jika ada filter -->
                    <button 
                        v-if="activeSubjectId || activeStatus || searchQuery"
                        @click="resetFilter"
                        type="button"
                        class="text-xs font-bold text-rose-500 hover:text-rose-700 px-3 py-2 rounded-xl hover:bg-rose-50 cursor-pointer transition-colors"
                    >
                        ✕ Reset Filter
                    </button>
                </div>

                <!-- Input Pencarian Cepat -->
                <div class="relative w-full md:w-72">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input 
                        type="text" 
                        v-model="searchQuery"
                        @input="debounceSearch"
                        placeholder="Cari judul paket atau kode..." 
                        class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500 bg-slate-50/60 placeholder-slate-400"
                    />
                    <button 
                        v-if="searchQuery"
                        @click="searchQuery = ''; applyFilter()"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 text-xs font-bold"
                    >
                        ✕
                    </button>
                </div>
            </div>

            <!-- ================= TABEL DATA PAKET SOAL CBT ================= -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 border-collapse">
                        <!-- Table Head -->
                        <thead class="bg-slate-50/80 text-slate-800 font-black uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-4 px-4 text-center w-12">No</th>
                                <th class="py-4 px-4 min-w-[170px]">Mata Pelajaran</th>
                                <th class="py-4 px-4 min-w-[240px]">Paket Soal &amp; Kode</th>
                                <th class="py-4 px-4 min-w-[130px]">Jadwal CBT</th>
                                <th class="py-4 px-4 text-center min-w-[110px]">Soal &amp; Durasi</th>
                                <th class="py-4 px-4 text-center min-w-[90px]">KKM</th>
                                <th class="py-4 px-4 text-center min-w-[150px]">Status Bank Soal</th>
                                <th class="py-4 px-4 text-right min-w-[180px]">Aksi</th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-slate-100 font-medium">
                            <tr 
                                v-for="(pkg, idx) in (packages.data || packages)" 
                                :key="pkg.id"
                                class="hover:bg-slate-50/70 transition-colors group"
                            >
                                <!-- No -->
                                <td class="py-4 px-4 text-center font-mono font-bold text-slate-400">
                                    {{ getRowNumber(idx) }}
                                </td>

                                <!-- Mata Pelajaran -->
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-indigo-50 text-indigo-700 text-[11px] font-black border border-indigo-200/70">
                                        📚 {{ pkg.subject?.name || 'Mata Pelajaran Umum' }}
                                    </span>
                                </td>

                                <!-- Paket Soal, Kode & Mode -->
                                <td class="py-4 px-4">
                                    <div class="space-y-1">
                                        <div class="font-black text-slate-900 text-sm group-hover:text-indigo-600 transition-colors">
                                            {{ pkg.title }}
                                        </div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-mono text-[10px] text-slate-400 font-bold bg-slate-100 px-2 py-0.5 rounded">
                                                {{ pkg.code }}
                                            </span>
                                            <span 
                                                class="px-2 py-0.5 rounded text-[10px] font-bold border"
                                                :class="pkg.display_mode === 'game' 
                                                    ? 'bg-purple-50 text-purple-700 border-purple-200' 
                                                    : 'bg-slate-50 text-slate-600 border-slate-200'"
                                            >
                                                {{ pkg.display_mode === 'game' ? '🎮 Game' : '🏛️ Formal' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Jadwal CBT Terhubung -->
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-xl"
                                        :class="pkg.exams_count > 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600'">
                                        <Clock class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                        <span>{{ pkg.exams_count > 0 ? `${pkg.exams_count} Jadwal Aktif` : 'Arsip Master' }}</span>
                                    </span>
                                </td>

                                <!-- Soal & Durasi -->
                                <td class="py-4 px-4 text-center">
                                    <div class="space-y-0.5">
                                        <span class="font-mono font-black text-slate-900 block text-xs">
                                            {{ pkg.questions_count || 0 }} Soal
                                        </span>
                                        <span class="text-[11px] text-slate-500 font-mono block">
                                            ⏱️ {{ pkg.duration_minutes }} mnt
                                        </span>
                                    </div>
                                </td>

                                <!-- KKM -->
                                <td class="py-4 px-4 text-center">
                                    <span class="font-mono font-black text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200 text-xs">
                                        Min. {{ pkg.passing_score }}
                                    </span>
                                </td>

                                <!-- Status Siswa (Saklar On/Off) -->
                                <td class="py-4 px-4 text-center">
                                    <button 
                                        @click="togglePublish(pkg)"
                                        type="button"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black transition-all cursor-pointer shadow-2xs border"
                                        :class="pkg.is_published 
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100' 
                                            : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200'"
                                        :title="pkg.is_published ? 'Bank Soal Aktif. Klik untuk nonaktifkan.' : 'Bank Soal Nonaktif. Klik untuk aktifkan.'"
                                    >
                                        <span class="w-2 h-2 rounded-full" :class="pkg.is_published ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                                        <span>{{ pkg.is_published ? '🟢 Aktif' : '⚪ Arsip' }}</span>
                                    </button>
                                </td>

                                <!-- Aksi Cepat -->
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Tombol Utama: Tulis Soal -->
                                        <Link 
                                            :href="route('sensei.questions.manage-package', pkg.id)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-xs transition-all cursor-pointer"
                                            title="Buka Lembar Tulis & Kelola Soal"
                                        >
                                            <FileEdit class="w-3.5 h-3.5" />
                                            <span>Tulis Soal</span>
                                        </Link>

                                        <!-- Preview CBT -->
                                        <Link 
                                            :href="route('sensei.questions.preview-package', pkg.id)"
                                            target="_blank"
                                            class="p-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-600 hover:text-indigo-600 border border-slate-200 transition-colors cursor-pointer"
                                            title="Live Preview CBT Langsung dari Bank Soal"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </Link>

                                        <!-- Export Word (.docx) -->
                                        <a 
                                            :href="route('sensei.questions.export-docx', pkg.id)"
                                            class="p-1.5 rounded-xl bg-white hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 border border-slate-200 transition-colors cursor-pointer"
                                            title="Ekspor Soal ke Dokumen Word (.docx) Siap Cetak"
                                        >
                                            <Download class="w-4 h-4" />
                                        </a>

                                        <!-- Edit Paket -->
                                        <button 
                                            @click="openEditPackageModal(pkg)"
                                            type="button"
                                            class="p-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-600 hover:text-indigo-600 border border-slate-200 transition-colors cursor-pointer"
                                            title="Edit Pengaturan Paket"
                                        >
                                            <Settings class="w-4 h-4" />
                                        </button>

                                        <!-- Hapus Paket -->
                                        <button 
                                            @click="confirmDeletePackage(pkg)"
                                            type="button"
                                            class="p-1.5 rounded-xl bg-white hover:bg-rose-50 text-slate-400 hover:text-rose-600 border border-slate-200 transition-colors cursor-pointer"
                                            title="Hapus Paket"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty Row State -->
                            <tr v-if="(!packages.data && packages.length === 0) || (packages.data && packages.data.length === 0)">
                                <td colspan="8" class="py-14 text-center text-slate-400 space-y-3">
                                    <div class="text-3xl">📦</div>
                                    <div class="text-xs font-bold text-slate-600">
                                        Tidak ada paket soal yang sesuai dengan filter atau pencarian Anda.
                                    </div>
                                    <button 
                                        @click="openAddPackageModal"
                                        type="button"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-black shadow-xs hover:bg-indigo-700 cursor-pointer"
                                    >
                                        <PlusCircle class="w-4 h-4" />
                                        <span>+ Buat Paket Soal Baru</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ================= PAGINATION BAR ================= -->
                <div 
                    v-if="packages.links && packages.links.length > 3" 
                    class="p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"
                >
                    <span class="text-slate-500 font-medium">
                        Menampilkan <strong>{{ packages.from || 0 }}</strong> sampai <strong>{{ packages.to || 0 }}</strong> dari <strong>{{ packages.total }}</strong> Paket Soal
                    </span>

                    <div class="flex items-center gap-1">
                        <template v-for="(link, lIdx) in packages.links" :key="lIdx">
                            <Link 
                                v-if="link.url" 
                                :href="link.url" 
                                v-html="link.label"
                                class="px-3 py-1.5 text-xs rounded-xl font-bold transition-all"
                                :class="link.active 
                                    ? 'bg-indigo-600 text-white shadow-xs' 
                                    : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                            />
                            <span 
                                v-else 
                                v-html="link.label"
                                class="px-2.5 py-1.5 text-xs text-slate-300 select-none"
                            ></span>
                        </template>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= MODAL TAMBAH / EDIT MATA PELAJARAN ================= -->
        <div v-if="showSubjectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs animate-fade-in">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 border border-slate-200 shadow-2xl space-y-5 text-slate-800">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xs">
                            📚
                        </div>
                        <h3 class="text-base font-black text-slate-900">
                            {{ editingSubject ? 'Edit Mata Pelajaran (Mapel)' : 'Tambah Mata Pelajaran Baru' }}
                        </h3>
                    </div>
                    <button @click="showSubjectModal = false" class="text-slate-400 hover:text-slate-700 p-1 cursor-pointer font-bold">
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitSubjectForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Mata Pelajaran *
                        </label>
                        <input 
                            v-model="subjectForm.name"
                            type="text" 
                            required
                            placeholder="Contoh: Tata Bahasa (Bunpou) / Kosakata (Moji-Goi)"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                            Kode Mapel (Unik) *
                        </label>
                        <input 
                            v-model="subjectForm.code"
                            type="text" 
                            required
                            placeholder="Contoh: BUNPOU_N4 / MOJI_GOI / KAIWA_01"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                        <p class="text-[10px] text-slate-400 mt-1">Kode unik huruf kapital untuk identifikasi sistem.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                            Keterangan / Deskripsi (Opsional)
                        </label>
                        <textarea 
                            v-model="subjectForm.description"
                            rows="2"
                            placeholder="Penjelasan singkat mengenai materi mata pelajaran ini..."
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button 
                            v-if="editingSubject"
                            @click="confirmDeleteSubject(editingSubject)"
                            type="button"
                            class="mr-auto text-xs font-bold text-rose-600 hover:text-rose-800 p-1 cursor-pointer"
                        >
                            Hapus Mapel
                        </button>

                        <button 
                            @click="showSubjectModal = false"
                            type="button"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            :disabled="isSubmittingSubject"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-500/20 cursor-pointer disabled:opacity-50"
                        >
                            {{ isSubmittingSubject ? 'Menyimpan...' : (editingSubject ? 'Simpan Perubahan' : 'Simpan Mapel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL BUAT / EDIT PAKET SOAL ================= -->
        <div v-if="showPackageModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs animate-fade-in">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 border border-slate-200 shadow-2xl space-y-5 text-slate-800 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xs">
                            📦
                        </div>
                        <h3 class="text-base font-black text-slate-900">
                            {{ editingPackage ? 'Edit Pengaturan Paket Soal' : 'Buat Paket Soal Baru' }}
                        </h3>
                    </div>
                    <button @click="showPackageModal = false" class="text-slate-400 hover:text-slate-700 p-1 cursor-pointer font-bold">
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitPackageForm" class="space-y-4">
                    <!-- Pilih Mapel -->
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                            Mata Pelajaran (Mapel) *
                        </label>
                        <select 
                            v-model="packageForm.subject_id"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        >
                            <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                            <option v-for="sub in subjects" :key="sub.id" :value="sub.id">
                                {{ sub.name }} ({{ sub.code }})
                            </option>
                        </select>
                    </div>

                    <!-- Judul Paket Soal -->
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                            Judul / Nama Paket Soal *
                        </label>
                        <input 
                            v-model="packageForm.title"
                            type="text" 
                            required
                            placeholder="Contoh: Paket 01 — Latihan Soal Pola Kalimat N4"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>

                    <!-- Durasi Menit & Passing Score -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                Durasi Pengerjaan (Menit) *
                            </label>
                            <input 
                                v-model.number="packageForm.duration_minutes"
                                type="number" 
                                required
                                min="5" 
                                max="300"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                Nilai Minimum Kelulusan (KKM) *
                            </label>
                            <input 
                                v-model.number="packageForm.passing_score"
                                type="number" 
                                required
                                min="0" 
                                max="1000"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button 
                            @click="showPackageModal = false"
                            type="button"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            :disabled="isSubmittingPackage"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-500/20 cursor-pointer disabled:opacity-50"
                        >
                            {{ isSubmittingPackage ? 'Menyimpan...' : (editingPackage ? 'Simpan Perubahan' : 'Buat Paket & Lanjut Tulis Soal →') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { confirmDialog, notifySuccess, notifyError } from '@/Utils/alert';
import { 
    BookOpenCheck, 
    PlusCircle, 
    FolderPlus, 
    FolderKanban, 
    Layers, 
    CheckCircle2, 
    HelpCircle, 
    Users, 
    Search, 
    Clock,
    FileEdit, 
    Eye, 
    Settings, 
    Trash2,
    FileDown,
    Download
} from 'lucide-vue-next';

const props = defineProps({
    subjects: Array,
    packages: Object, // Paginated object { data, total, per_page, current_page, links }
    batches: Array,
    filters: Object,
    stats: Object,
});

const { isJapanese } = useLang();

// Filter State
const activeSubjectId = ref(props.filters?.subject_id || '');
const activeStatus = ref(props.filters?.status || '');
const searchQuery = ref(props.filters?.search || '');
let searchTimer = null;

const applyFilter = () => {
    router.get(route('sensei.questions.index'), {
        subject_id: activeSubjectId.value || undefined,
        status: activeStatus.value || undefined,
        search: searchQuery.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilter = () => {
    activeSubjectId.value = '';
    activeStatus.value = '';
    searchQuery.value = '';
    applyFilter();
};

const debounceSearch = () => {
    if (searchTimer) clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        applyFilter();
    }, 350);
};

const getRowNumber = (idx) => {
    const currentPage = props.packages.current_page || 1;
    const perPage = props.packages.per_page || 15;
    return (currentPage - 1) * perPage + idx + 1;
};

// ================= TOGGLE PUBLISH (AKTIF/NONAKTIF UNTUK SISWA) =================
const togglePublish = (pkg) => {
    const action = pkg.is_published ? 'menonaktifkan' : 'mengaktifkan';
    const text = pkg.is_published 
        ? `Siswa tidak akan dapat mengakses paket "${pkg.title}" ini lagi.` 
        : `Siswa akan langsung dapat melihat dan mengerjakan paket "${pkg.title}" ini di jadwal CBT.`;

    confirmDialog(`Yakin ingin ${action} paket ujian ini?`, text).then((res) => {
        if (res.isConfirmed) {
            router.post(route('sensei.questions.toggle-publish', pkg.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    notifySuccess(
                        pkg.is_published ? 'Paket Dinonaktifkan' : 'Paket Berhasil Diaktifkan!',
                        `Status paket "${pkg.title}" telah diperbarui.`
                    );
                },
            });
        }
    });
};

// ================= MODAL MATA PELAJARAN (MAPEL) =================
const showSubjectModal = ref(false);
const editingSubject = ref(null);
const isSubmittingSubject = ref(false);
const subjectForm = ref({
    name: '',
    code: '',
    description: '',
});

const openAddSubjectModal = () => {
    editingSubject.value = null;
    subjectForm.value = {
        name: '',
        code: '',
        description: '',
    };
    showSubjectModal.value = true;
};

const openEditSubjectModal = (sub) => {
    editingSubject.value = sub;
    subjectForm.value = {
        name: sub.name,
        code: sub.code,
        description: sub.description || '',
    };
    showSubjectModal.value = true;
};

const submitSubjectForm = () => {
    isSubmittingSubject.value = true;
    if (editingSubject.value) {
        router.put(route('sensei.subjects.update', editingSubject.value.id), subjectForm.value, {
            onSuccess: () => {
                showSubjectModal.value = false;
                notifySuccess('Berhasil', 'Mata Pelajaran berhasil diperbarui!');
            },
            onFinish: () => isSubmittingSubject.value = false,
        });
    } else {
        router.post(route('sensei.subjects.store'), subjectForm.value, {
            onSuccess: () => {
                showSubjectModal.value = false;
                notifySuccess('Berhasil', 'Mata Pelajaran baru berhasil ditambahkan!');
            },
            onFinish: () => isSubmittingSubject.value = false,
        });
    }
};

const confirmDeleteSubject = (sub) => {
    confirmDialog(`Hapus Mata Pelajaran "${sub.name}"?`, 'Mapel hanya dapat dihapus jika tidak ada paket soal di dalamnya.').then((res) => {
        if (res.isConfirmed) {
            router.delete(route('sensei.subjects.destroy', sub.id), {
                onSuccess: () => {
                    showSubjectModal.value = false;
                    notifySuccess('Terhapus', 'Mata Pelajaran berhasil dihapus.');
                },
                onError: (err) => {
                    notifyError('Gagal Hapus', err.error || 'Mata Pelajaran masih memiliki paket soal aktif.');
                }
            });
        }
    });
};

// ================= MODAL BUAT / EDIT PAKET SOAL =================
const showPackageModal = ref(false);
const editingPackage = ref(null);
const isSubmittingPackage = ref(false);
const packageForm = ref({
    subject_id: '',
    title: '',
    duration_minutes: 60,
    passing_score: 90,
});

const openAddPackageModal = () => {
    editingPackage.value = null;
    packageForm.value = {
        subject_id: activeSubjectId.value || (props.subjects[0]?.id || ''),
        title: '',
        duration_minutes: 60,
        passing_score: 90,
    };
    showPackageModal.value = true;
};

const openEditPackageModal = (pkg) => {
    editingPackage.value = pkg;
    packageForm.value = {
        subject_id: pkg.subject_id,
        title: pkg.title,
        duration_minutes: pkg.duration_minutes,
        passing_score: pkg.passing_score,
    };
    showPackageModal.value = true;
};

const submitPackageForm = () => {
    isSubmittingPackage.value = true;
    if (editingPackage.value) {
        router.put(route('sensei.questions.update-package', editingPackage.value.id), packageForm.value, {
            onSuccess: () => {
                showPackageModal.value = false;
                notifySuccess('Berhasil', 'Pengaturan paket soal berhasil diperbarui!');
            },
            onFinish: () => isSubmittingPackage.value = false,
        });
    } else {
        router.post(route('sensei.questions.store-package'), packageForm.value, {
            onSuccess: () => {
                showPackageModal.value = false;
            },
            onFinish: () => isSubmittingPackage.value = false,
        });
    }
};

const confirmDeletePackage = (pkg) => {
    confirmDialog(`Hapus Paket Soal "${pkg.title}"?`, 'Seluruh butir soal dalam paket ini akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.').then((res) => {
        if (res.isConfirmed) {
            router.delete(route('sensei.questions.destroy-package', pkg.id), {
                onSuccess: () => {
                    notifySuccess('Terhapus', 'Paket soal berhasil dihapus.');
                },
            });
        }
    });
};
</script>
