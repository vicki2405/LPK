<template>
    <Head :title="`Kelola Butir Soal: ${package.title} — Sensei Portal`" />

    <AuthenticatedLayout>
        <!-- ================= MODE 1: DAFTAR BUTIR SOAL PAKET ================= -->
        <div v-if="!isEditorMode" class="space-y-6 animate-fade-in pb-20">

            <!-- ================= HEADER SECTION ================= -->
            <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-indigo-600 via-blue-500 to-cyan-400 rounded-l-3xl"></div>

                <div class="pl-2 space-y-2">
                    <Link 
                        :href="route('sensei.questions.index')"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors cursor-pointer"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        <span>← Kembali ke Katalog Paket Soal</span>
                    </Link>

                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-black border border-indigo-200/70">
                                📚 Mapel: {{ package.subject?.name || 'Umum' }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-mono font-bold">
                                KODE: {{ package.code }}
                            </span>
                            <span 
                                class="px-2.5 py-0.5 rounded-lg text-xs font-bold border"
                                :class="package.display_mode === 'game' 
                                    ? 'bg-purple-50 text-purple-700 border-purple-200' 
                                    : 'bg-slate-50 text-slate-700 border-slate-200'"
                            >
                                {{ package.display_mode === 'game' ? '🎮 Mode Game' : '🏛️ Mode Formal' }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                            {{ package.title }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Level: <strong>{{ package.level || 'N4' }}</strong> · Durasi: <strong>{{ package.duration_minutes }} Menit</strong> · KKM: <strong>{{ package.passing_score }}</strong>
                        </p>
                    </div>
                </div>

                <!-- Header Actions: Desktop View (sm:flex) -->
                <div class="hidden sm:flex items-center gap-2 shrink-0 flex-wrap">
                    <!-- Toggle Publish -->
                    <button 
                        @click="togglePublish"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-2xl text-xs font-black transition-all cursor-pointer border shadow-xs"
                        :class="package.is_published 
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100' 
                            : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200'"
                    >
                        <span class="w-2.5 h-2.5 rounded-full" :class="package.is_published ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                        <span>{{ package.is_published ? '🟢 Aktif' : '⚪ Arsip' }}</span>
                    </button>

                    <!-- Preview CBT -->
                    <Link 
                        :href="route('sensei.questions.preview-package', package.id)"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 hover:text-indigo-600 border border-slate-300 text-xs font-bold shadow-xs transition-colors cursor-pointer"
                        title="Simulasi tampilan ujian CBT"
                    >
                        <Eye class="w-4 h-4 text-indigo-600" />
                        <span>Preview</span>
                    </Link>

                    <!-- Template Word (.docx) -->
                    <a 
                        :href="route('sensei.questions.template.download')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-2xl bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-700 border border-slate-300 text-xs font-bold shadow-xs transition-colors cursor-pointer"
                        title="Unduh Template Microsoft Word (.docx) resmi LPK"
                    >
                        <FileDown class="w-4 h-4 text-blue-600" />
                        <span>Template Word</span>
                    </a>

                    <!-- Export Word (.docx) -->
                    <a 
                        :href="route('sensei.questions.export-docx', package.id)"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-2xl bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-300 text-xs font-bold shadow-xs transition-colors cursor-pointer"
                        title="Ekspor seluruh butir soal ke dokumen Word (.docx) siap cetak"
                    >
                        <Download class="w-4 h-4 text-emerald-600" />
                        <span>Export Word</span>
                    </a>

                    <!-- Import Word (.docx) -->
                    <button 
                        @click="showImportWordModal = true"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-md shadow-blue-500/20 transition-all cursor-pointer"
                        title="Upload file Word (.docx) untuk memasukkan soal sekaligus"
                    >
                        <UploadCloud class="w-4 h-4" />
                        <span>Import Word</span>
                    </button>

                    <!-- Quick Text Import -->
                    <button 
                        @click="showQuickTextModal = true"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-2xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold shadow-xs transition-all cursor-pointer"
                        title="Salin dan tempel (copy-paste) teks soal dari dokumen"
                    >
                        <ClipboardList class="w-4 h-4 text-amber-600" />
                        <span>Quick Text</span>
                    </button>

                    <!-- Tambah Soal Manual -->
                    <button 
                        @click="openAddItemModal"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-500/25 transition-all cursor-pointer"
                    >
                        <PlusCircle class="w-4 h-4" />
                        <span>+ Soal</span>
                    </button>
                </div>

                <!-- Header Actions: Mobile View (flex sm:hidden) -->
                <div class="flex sm:hidden items-center gap-2 shrink-0 w-full justify-between pt-2 border-t border-slate-100 relative">
                    <div class="flex items-center gap-2">
                        <!-- Tambah Soal Manual -->
                        <button 
                            @click="openAddItemModal"
                            type="button"
                            class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-indigo-600 text-white text-xs font-black shadow-md shadow-indigo-500/25"
                        >
                            <PlusCircle class="w-4 h-4" />
                            <span>+ Soal</span>
                        </button>

                        <!-- Import Word -->
                        <button 
                            @click="showImportWordModal = true"
                            type="button"
                            class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-blue-600 text-white text-xs font-black shadow-xs"
                        >
                            <UploadCloud class="w-4 h-4" />
                            <span>Import</span>
                        </button>
                    </div>

                    <!-- Dropdown Opsi Lainnya -->
                    <div class="relative">
                        <button 
                            @click="showMobileActionsDropdown = !showMobileActionsDropdown"
                            type="button"
                            class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-200 cursor-pointer"
                        >
                            <MoreHorizontal class="w-4 h-4" />
                            <span>Opsi ▾</span>
                        </button>

                        <!-- Dropdown Menu Mobile -->
                        <div 
                            v-if="showMobileActionsDropdown" 
                            class="absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-200 p-1.5 z-50 space-y-1 animate-fade-in"
                        >
                            <button 
                                @click="togglePublish(); showMobileActionsDropdown = false" 
                                type="button" 
                                class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold flex items-center gap-2 hover:bg-slate-50 cursor-pointer"
                                :class="package.is_published ? 'text-emerald-700' : 'text-slate-600'"
                            >
                                <span class="w-2 h-2 rounded-full" :class="package.is_published ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                <span>Status: {{ package.is_published ? '🟢 Aktif' : '⚪ Arsip' }}</span>
                            </button>

                            <Link 
                                :href="route('sensei.questions.preview-package', package.id)" 
                                target="_blank" 
                                @click="showMobileActionsDropdown = false"
                                class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 flex items-center gap-2"
                            >
                                <Eye class="w-3.5 h-3.5 text-indigo-600" />
                                <span>Preview CBT</span>
                            </Link>

                            <a 
                                :href="route('sensei.questions.template.download')" 
                                @click="showMobileActionsDropdown = false"
                                class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 flex items-center gap-2"
                            >
                                <FileDown class="w-3.5 h-3.5 text-blue-600" />
                                <span>Template Word (.docx)</span>
                            </a>

                            <a 
                                :href="route('sensei.questions.export-docx', package.id)" 
                                @click="showMobileActionsDropdown = false"
                                class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 flex items-center gap-2"
                            >
                                <Download class="w-3.5 h-3.5 text-emerald-600" />
                                <span>Export Word (.docx)</span>
                            </a>

                            <button 
                                @click="showQuickTextModal = true; showMobileActionsDropdown = false" 
                                type="button" 
                                class="w-full text-left px-3 py-2 rounded-xl text-xs font-bold text-amber-800 hover:bg-amber-50 flex items-center gap-2 cursor-pointer"
                            >
                                <ClipboardList class="w-3.5 h-3.5 text-amber-600" />
                                <span>Quick Text Import</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= DAFTAR BUTIR SOAL ================= -->
            <!-- ================= DAFTAR BUTIR SOAL ================= -->
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <h2 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                        <HelpCircle class="w-5 h-5 text-indigo-600" />
                        <span>Daftar Butir Soal di Paket Ini</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold font-mono">
                            {{ package.questions?.length || 0 }} Butir Soal
                        </span>
                    </h2>

                    <button 
                        v-if="package.questions && package.questions.length > 0"
                        @click="openAddItemModal"
                        type="button"
                        class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer flex items-center gap-1 self-start sm:self-auto"
                    >
                        <PlusCircle class="w-3.5 h-3.5" />
                        <span>Tambah Butir Soal</span>
                    </button>
                </div>

                <!-- Filter Bar & Search (Hanya tampil jika ada soal di paket) -->
                <div v-if="package.questions && package.questions.length > 0" class="bg-white border border-slate-200/90 rounded-2xl p-3 sm:p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <!-- Section Tabs -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none text-xs">
                        <button
                            v-for="tab in sectionFilterTabs"
                            :key="tab.id"
                            type="button"
                            @click="selectedSectionFilter = tab.id"
                            class="px-3 py-1.5 rounded-xl font-bold transition-all shrink-0 cursor-pointer flex items-center gap-1.5"
                            :class="selectedSectionFilter === tab.id 
                                ? 'bg-indigo-600 text-white shadow-xs' 
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        >
                            <span>{{ tab.label }}</span>
                            <span 
                                class="px-1.5 py-0.2 rounded-md text-[10px] font-mono"
                                :class="selectedSectionFilter === tab.id ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'"
                            >
                                {{ tab.count }}
                            </span>
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full md:w-72 shrink-0">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                        <input
                            v-model="questionSearchQuery"
                            type="text"
                            placeholder="Cari teks soal / opsi..."
                            class="w-full pl-9 pr-8 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 placeholder-slate-400 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all"
                        />
                        <button 
                            v-if="questionSearchQuery"
                            type="button"
                            @click="questionSearchQuery = ''"
                            class="text-slate-400 hover:text-slate-600 absolute right-2.5 top-1/2 -translate-y-1/2 cursor-pointer p-0.5"
                        >
                            <X class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>

                <!-- List Soal Cards -->
                <div v-if="filteredQuestions.length > 0" class="space-y-4">
                    <div 
                        v-for="q in filteredQuestions" 
                        :key="q.id"
                        class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-5 sm:p-7 space-y-4 transition-all hover:border-slate-300"
                    >
                        <!-- Soal Header Row -->
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-xs font-mono shadow-xs">
                                    {{ q.originalIndex + 1 }}
                                </span>
                                <span class="text-xs font-bold text-slate-500 font-mono">
                                    Bobot: {{ Number(q.score_points) }} Poin
                                </span>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold border"
                                    :class="{
                                        'bg-purple-50 text-purple-700 border-purple-200': q.section_type === 'moji_goi',
                                        'bg-blue-50 text-blue-700 border-blue-200': q.section_type === 'bunpou',
                                        'bg-amber-50 text-amber-700 border-amber-200': q.section_type === 'dokkai',
                                        'bg-rose-50 text-rose-700 border-rose-200': q.section_type === 'choukai' || q.audio_url,
                                    }">
                                    {{ q.section_type === 'moji_goi' ? '📝 Moji-Goi' : (q.section_type === 'dokkai' ? '📖 Dokkai' : (q.section_type === 'choukai' || q.audio_url ? '🎧 Choukai' : '🔤 Bunpou')) }}
                                </span>
                                <span v-if="q.category" class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200 max-w-[200px] truncate" :title="q.category.name">
                                    🎯 {{ q.category.code ? q.category.code + ': ' : '' }}{{ q.category.name }}
                                </span>
                                <span v-if="q.audio_url" class="px-2 py-0.5 rounded-lg bg-rose-50 text-rose-600 text-[10px] font-bold border border-rose-200 flex items-center gap-1">
                                    🎧 Audio
                                </span>
                                <span v-if="q.image_url" class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-bold border border-blue-200 flex items-center gap-1">
                                    🖼️ Gambar
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-1.5">
                                <button 
                                    @click="openEditItemModal(q, q.originalIndex)"
                                    type="button"
                                    class="p-2 rounded-xl bg-slate-50 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 transition-colors cursor-pointer"
                                    title="Edit Soal"
                                >
                                    <FileEdit class="w-4 h-4" />
                                </button>
                                <button 
                                    @click="confirmDeleteItem(q)"
                                    type="button"
                                    class="p-2 rounded-xl bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition-colors cursor-pointer"
                                    title="Hapus Soal"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Petunjuk / Wacana Bacaan jika ada -->
                        <div v-if="q.instruction" class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/80 text-xs font-jp text-amber-950 font-medium">
                            <span class="font-bold font-sans text-amber-800 text-[10px] uppercase block mb-0.5">Petunjuk:</span>
                            {{ q.instruction }}
                        </div>

                        <div v-if="q.reading_passage" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-jp leading-relaxed text-slate-800">
                            <span class="font-bold font-sans text-slate-500 text-[10px] uppercase block mb-1">📖 Wacana / Teks Bacaan:</span>
                            <div v-html="q.reading_passage"></div>
                        </div>

                        <!-- Gambar / Audio jika ada -->
                        <div v-if="q.image_url" class="max-w-md rounded-2xl overflow-hidden border border-slate-200">
                            <img :src="q.image_url" alt="Ilustrasi Soal" class="w-full h-auto object-cover max-h-60" />
                        </div>

                        <div v-if="q.audio_url" class="p-3 rounded-2xl bg-slate-50 border border-slate-200 max-w-md">
                            <audio controls :src="q.audio_url" class="w-full h-8"></audio>
                        </div>

                        <!-- Teks Pertanyaan (dengan Furigana Ruby) -->
                        <div class="text-sm sm:text-base font-extrabold font-jp leading-[2.2] text-slate-900 select-text">
                            <div v-html="q.question_text"></div>
                        </div>

                        <!-- Pilihan Ganda -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                            <div 
                                v-for="(opt, idx) in q.options" 
                                :key="opt.id"
                                class="p-3 rounded-2xl border flex items-center gap-3 transition-colors"
                                :class="opt.is_correct 
                                    ? 'bg-emerald-50/80 border-emerald-300 text-emerald-950 font-bold shadow-2xs' 
                                    : 'bg-white border-slate-200 text-slate-700'"
                            >
                                <span 
                                    class="w-7 h-7 rounded-xl flex items-center justify-center font-black text-xs font-mono shrink-0"
                                    :class="opt.is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 border border-slate-200'"
                                >
                                    {{ formatOptionKey(opt.option_key, idx) }}
                                </span>
                                <span class="flex-1 font-jp text-xs sm:text-sm font-semibold" v-html="opt.option_text"></span>
                                <span v-if="opt.is_correct" class="px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-black shrink-0">
                                    ✓ KUNCI
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- State: Pencarian / Filter Tidak Ditemukan -->
                <div v-else-if="package.questions && package.questions.length > 0" class="bg-white rounded-3xl border border-dashed border-slate-300 p-8 sm:p-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <Search class="w-6 h-6" />
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-slate-800">Tidak ada butir soal yang sesuai kriteria</h4>
                        <p class="text-xs text-slate-500">Coba ubah kata kunci pencarian atau pilih tab seksi soal yang lain.</p>
                    </div>
                    <button
                        type="button"
                        @click="resetQuestionFilters"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer transition-colors"
                    >
                        Reset Pencarian & Filter
                    </button>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-4 shadow-xs">
                    <div class="w-16 h-16 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto text-2xl">
                        📝
                    </div>
                    <div class="max-w-md mx-auto">
                        <h3 class="text-lg font-black text-slate-900">
                            Paket Soal Ini Masih Kosong
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Mulai tambahkan butir soal nomor 1 dengan menekan tombol di bawah ini. Tulis teks soal dan tandai kunci jawaban yang benar.
                        </p>
                    </div>
                    <button 
                        @click="openAddItemModal"
                        type="button"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-500/25 transition-all cursor-pointer"
                    >
                        <PlusCircle class="w-4 h-4" />
                        <span>+ Tulis Butir Soal</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ================= MODE 2: FULL WORKSPACE STUDIO EDITOR BUTIR SOAL ================= -->
        <div v-else class="space-y-6 animate-fade-in pb-20">
                <!-- Top Sticky Action Header -->
                <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-4 z-20">
                    <div class="flex items-center gap-3">
                        <button 
                            @click="closeEditor"
                            type="button"
                            class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors cursor-pointer"
                            title="Kembali ke Daftar Soal"
                        >
                            <ArrowLeft class="w-5 h-5" />
                        </button>
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-black">
                                    📝 Studio Penulisan Butir Soal (Layar Penuh)
                                </span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                {{ editingItem ? `Edit Butir Soal #${editingItemIndex + 1}` : `Tambah Butir Soal Baru` }}
                            </h2>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 self-end sm:self-auto">
                        <button 
                            @click="closeEditor"
                            type="button"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            @click="submitItemForm"
                            type="button"
                            :disabled="isSubmittingItem"
                            class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-500/25 transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
                        >
                            <Save class="w-4 h-4" />
                            <span>{{ isSubmittingItem ? 'Menyimpan...' : (editingItem ? 'Simpan Perubahan Soal' : 'Simpan Butir Soal') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Form Workspace -->
                <form @submit.prevent="submitItemForm" class="space-y-6">
                    <!-- Baris 1: Seksi Soal CBT & Indikator Capaian & Bobot Skor -->
                    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    Seksi Soal CBT *
                                </label>
                                <select 
                                    v-model="itemForm.section_type"
                                    required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                >
                                    <option value="moji_goi">📝 Moji & Goi (Huruf & Kosakata)</option>
                                    <option value="bunpou">🔤 Bunpou (Tata Bahasa)</option>
                                    <option value="dokkai">📖 Dokkai (Pemahaman Bacaan)</option>
                                    <option value="choukai">🎧 Choukai (Mendengarkan / Audio)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    🎯 Indikator Capaian Pembelajaran
                                </label>
                                <select 
                                    v-model="itemForm.question_category_id"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                >
                                    <option value="">-- Umum / Tanpa Indikator Khusus --</option>
                                    <optgroup 
                                        v-for="(group, groupLabel) in groupedIndicators" 
                                        :key="groupLabel" 
                                        :label="groupLabel"
                                    >
                                        <option 
                                            v-for="ind in group" 
                                            :key="ind.id" 
                                            :value="ind.id"
                                        >
                                            {{ ind.code ? ind.code + ': ' : '' }}{{ ind.name }}
                                        </option>
                                    </optgroup>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    ⭐ Bobot Skor / Poin
                                </label>
                                <input 
                                    v-model.number="itemForm.score_points"
                                    type="number" 
                                    step="0.5" 
                                    min="0.1" 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold font-mono text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Baris 2: SATU FORM UTAMA UNTUK SEMUA TEKS SOAL -->
                    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                        <div>
                            <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                                <div>
                                    <h3 class="text-sm sm:text-base font-black text-slate-900 flex items-center gap-2">
                                        <span>Teks Pertanyaan Soal & Wacana Bacaan *</span>
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Tulis pertanyaan, instruksi, atau wacana cerita panjang (Dokkai) langsung di sini.
                                    </p>
                                </div>
                            </div>

                            <JapaneseInput 
                                v-model="itemForm.question_text"
                                :is-textarea="true"
                                :rows="6"
                                placeholder="Tuliskan pertanyaan soal atau paragraf wacana di sini..."
                                insert-button-text="Sisipkan ke Teks Soal"
                                :required="true"
                            />
                        </div>

                        <!-- Live Real-Time Preview -->
                        <div v-if="itemForm.question_text" class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">
                                👁️ Pratinjau Tampilan Siswa di Layar CBT:
                            </span>
                            <div class="prose max-w-none font-jp text-sm sm:text-base font-bold text-slate-900 leading-[2.2] whitespace-pre-line" v-html="itemForm.question_text"></div>
                        </div>
                    </div>

                    <!-- Baris 3: Media Pendukung -->
                    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-sm">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <span>📎 Media Pendukung (Opsional)</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">🖼️ Gambar Ilustrasi</label>
                                <input 
                                    @change="onImageChange"
                                    type="file" 
                                    accept="image/*"
                                    class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 cursor-pointer"
                                />
                            </div>

                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">🎧 Audio MP3 Listening (Choukai)</label>
                                <input 
                                    @change="onAudioChange"
                                    type="file" 
                                    accept="audio/*"
                                    class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 cursor-pointer"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Baris 4: Pilihan Jawaban Dinamis (2 - 5 Pilihan) -->
                    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                        <div class="flex items-center justify-between flex-wrap gap-2 pb-2 border-b border-slate-100">
                            <div>
                                <h3 class="text-sm sm:text-base font-black text-slate-900">
                                    Pilihan Jawaban ({{ itemForm.options.length }} Pilihan: {{ optionKeys.slice(0, itemForm.options.length).join(', ') }}) *
                                </h3>
                                <p class="text-xs text-slate-400">Dapat disesuaikan 2 hingga 5 pilihan. Klik tombol huruf untuk menetapkan kunci jawaban.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200">
                                    Kunci Terpilih: Pilihan {{ optionKeys[correctOptionIndex] }}
                                </span>
                                <button
                                    v-if="itemForm.options.length < 5"
                                    type="button"
                                    @click="addOption"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs border border-indigo-200 transition-colors cursor-pointer"
                                >
                                    <Plus class="w-3.5 h-3.5" />
                                    <span>Tambah Opsi ({{ optionKeys[itemForm.options.length] }})</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <div 
                                v-for="(opt, idx) in itemForm.options" 
                                :key="idx"
                                class="p-3.5 rounded-2xl border-2 transition-all flex items-center gap-2.5"
                                :class="correctOptionIndex === idx ? 'border-emerald-500 bg-emerald-50/50 shadow-sm' : 'border-slate-200 bg-white'"
                            >
                                <button 
                                    @click="correctOptionIndex = idx"
                                    type="button"
                                    class="w-10 h-10 rounded-xl font-black text-sm font-mono flex items-center justify-center shrink-0 cursor-pointer shadow-xs transition-all"
                                    :class="correctOptionIndex === idx ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    :title="`Klik untuk jadikan ${optionKeys[idx]} sebagai kunci jawaban`"
                                >
                                    {{ optionKeys[idx] }}
                                </button>
                                <input 
                                    v-model="opt.option_text"
                                    type="text" 
                                    required
                                    :placeholder="`Teks pilihan ${optionKeys[idx]}...`"
                                    class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm font-jp font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                />
                                <button 
                                    type="button"
                                    @click="openOptionJapaneseModal(idx)"
                                    class="px-2.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-rose-50 text-slate-700 border border-slate-200 transition-colors shrink-0 cursor-pointer"
                                    title="Keyboard Huruf Jepang"
                                >
                                    🇯🇵
                                </button>
                                <button 
                                    v-if="itemForm.options.length > 2"
                                    type="button"
                                    @click="removeOption(idx)"
                                    class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors shrink-0 cursor-pointer"
                                    :title="`Hapus opsi ${optionKeys[idx]}`"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Sticky Actions -->
                    <div class="flex items-center justify-between gap-4 pt-4 border-t border-slate-200">
                        <button 
                            @click="closeEditor"
                            type="button"
                            class="px-5 py-3 rounded-2xl border border-slate-300 text-slate-600 hover:bg-slate-100 text-xs sm:text-sm font-bold cursor-pointer"
                        >
                            ← Batal
                        </button>
                        <button 
                            type="submit"
                            :disabled="isSubmittingItem"
                            class="px-8 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-black shadow-lg shadow-indigo-500/25 cursor-pointer disabled:opacity-50"
                        >
                            <Save class="w-4 h-4" />
                            <span>{{ isSubmittingItem ? 'Menyimpan...' : (editingItem ? 'Simpan Perubahan Soal' : 'Simpan Butir Soal') }}</span>
                        </button>
                    </div>
                </form>
            </div>

        <!-- ================= MODAL PINTAR HURUF JEPANG UNTUK PILIHAN JAWABAN ================= -->
        <div v-if="activeOptionIndex !== null" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs animate-fade-in">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-7 space-y-5">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-base">
                            🇯🇵
                        </div>
                        <div>
                            <h4 class="text-sm sm:text-base font-bold text-slate-900">
                                Huruf Jepang Pilihan {{ optionKeys[activeOptionIndex] }}
                            </h4>
                            <p class="text-[11px] text-slate-500">
                                Ketik Romaji keyboard biasa atau buat Kanji + Furigana
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="activeOptionIndex = null" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        ✕
                    </button>
                </div>

                <!-- Tab Pemilihan 3 Huruf Jepang -->
                <div class="grid grid-cols-3 gap-2 bg-slate-100 p-1 rounded-2xl">
                    <button
                        type="button"
                        @click="optionActiveScript = 'hiragana'"
                        class="py-2 px-3 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                        :class="optionActiveScript === 'hiragana' ? 'bg-white text-rose-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    >
                        <span>🎌</span>
                        <span>1. Hiragana</span>
                    </button>
                    <button
                        type="button"
                        @click="optionActiveScript = 'katakana'"
                        class="py-2 px-3 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                        :class="optionActiveScript === 'katakana' ? 'bg-white text-amber-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    >
                        <span>🈁</span>
                        <span>2. Katakana</span>
                    </button>
                    <button
                        type="button"
                        @click="optionActiveScript = 'kanji'"
                        class="py-2 px-3 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5"
                        :class="optionActiveScript === 'kanji' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    >
                        <span>🎴</span>
                        <span>3. Kanji</span>
                    </button>
                </div>

                <!-- 1. Hiragana -->
                <div v-if="optionActiveScript === 'hiragana'" class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Ketik Romaji (Keyboard Biasa / HP):
                        </label>
                        <input
                            type="text"
                            v-model="optHiraganaRomaji"
                            @input="handleOptHiraganaInput"
                            placeholder="Contoh: nihon / ringo / gakusei"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-sans focus:ring-2 focus:ring-rose-500 focus:outline-none"
                            autofocus
                        />
                    </div>
                    <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-100">
                        <span class="text-[11px] font-bold text-rose-700 block mb-1">Hasil Huruf Hiragana:</span>
                        <div class="text-base sm:text-lg font-bold font-jp text-slate-900 min-h-[28px]">
                            {{ optPreviewHiragana || '...' }}
                        </div>
                    </div>
                </div>

                <!-- 2. Katakana -->
                <div v-if="optionActiveScript === 'katakana'" class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Ketik Romaji (Keyboard Biasa / HP):
                        </label>
                        <input
                            type="text"
                            v-model="optKatakanaRomaji"
                            @input="handleOptKatakanaInput"
                            placeholder="Contoh: terebi / kouhii / basu"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-sans focus:ring-2 focus:ring-amber-500 focus:outline-none"
                            autofocus
                        />
                    </div>
                    <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100">
                        <span class="text-[11px] font-bold text-amber-700 block mb-1">Hasil Huruf Katakana:</span>
                        <div class="text-base sm:text-lg font-bold font-jp text-slate-900 min-h-[28px]">
                            {{ optPreviewKatakana || '...' }}
                        </div>
                    </div>
                </div>

                <!-- 3. Kanji -->
                <div v-if="optionActiveScript === 'kanji'" class="space-y-3.5">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Huruf Kanji</label>
                            <input
                                type="text"
                                v-model="optKanjiText"
                                placeholder="Contoh: 日本"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-jp focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Cara Baca Romaji</label>
                            <input
                                type="text"
                                v-model="optKanjiReadingRomaji"
                                placeholder="nihon -> にほん"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-jp focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-100 text-center">
                        <span class="text-[10px] font-bold text-indigo-600 block mb-1">Tampilan Layar:</span>
                        <div class="text-lg font-bold font-jp text-slate-900 py-1" v-html="optPreviewKanjiRuby"></div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        @click="activeOptionIndex = null"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="confirmInsertToOption"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-md transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <span>📥</span>
                        <span>Sisipkan ke Pilihan {{ optionKeys[activeOptionIndex] }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ================= MODAL 1: IMPORT WORD (.DOCX) ================= -->
        <div 
            v-if="showImportWordModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-fade-in"
        >
            <div class="bg-white rounded-3xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden p-6 sm:p-7 space-y-5">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <UploadCloud class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900">Import Soal dari MS Word</h3>
                            <p class="text-xs text-slate-500">Mendukung teks, kanji, furigana, dan gambar inline</p>
                        </div>
                    </div>
                    <button 
                        @click="showImportWordModal = false"
                        type="button" 
                        class="p-1.5 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-600 cursor-pointer"
                    >
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Info Box Template -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-2">
                    <div class="flex items-center justify-between font-bold text-slate-800">
                        <span>💡 Belum punya format dokumennya?</span>
                        <a 
                            :href="route('sensei.questions.template.download')"
                            class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 underline font-black"
                        >
                            <FileDown class="w-3.5 h-3.5" />
                            <span>Unduh Template Word</span>
                        </a>
                    </div>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Pastikan butir soal disusun dalam tabel kartu sesuai template. Gambar ilustrasi dapat langsung di-paste ke dalam sel "SOAL".
                    </p>
                </div>

                <!-- Dropzone File Upload -->
                <div 
                    @dragover.prevent="isDraggingDocx = true"
                    @dragleave.prevent="isDraggingDocx = false"
                    @drop.prevent="handleDocxDrop"
                    @click="$refs.docxFileInput.click()"
                    class="border-2 border-dashed rounded-3xl p-6 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-3"
                    :class="isDraggingDocx 
                        ? 'border-blue-500 bg-blue-50/50' 
                        : (docxFile ? 'border-emerald-400 bg-emerald-50/30' : 'border-slate-300 hover:border-blue-400 bg-slate-50/50 hover:bg-blue-50/20')"
                >
                    <input 
                        ref="docxFileInput"
                        type="file" 
                        accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                        class="hidden" 
                        @change="handleDocxChange"
                    />

                    <div v-if="!docxFile" class="space-y-1">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100/80 text-blue-600 flex items-center justify-center mx-auto mb-2">
                            <FileText class="w-6 h-6" />
                        </div>
                        <p class="text-xs font-bold text-slate-800">
                            Tarik & lepas file <span class="text-blue-600">.docx</span> ke sini, atau klik untuk memilih
                        </p>
                        <p class="text-[10px] text-slate-400 font-mono">Maksimal ukuran file: 25 MB</p>
                    </div>

                    <div v-else class="flex items-center gap-3 text-left w-full px-2">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                            <CheckCircle2 class="w-5 h-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-black text-slate-900 truncate">{{ docxFileName }}</p>
                            <p class="text-[10px] text-slate-500 font-mono">{{ (docxFile.size / 1024).toFixed(1) }} KB · Siap diimpor</p>
                        </div>
                        <button 
                            @click.stop="docxFile = null; docxFileName = ''"
                            type="button" 
                            class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50"
                        >
                            <X class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <button 
                        @click="showImportWordModal = false"
                        type="button" 
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 cursor-pointer"
                        :disabled="isUploadingDocx"
                    >
                        Batal
                    </button>

                    <button 
                        @click="submitDocxImport"
                        type="button" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-md shadow-blue-500/25 transition-all cursor-pointer disabled:opacity-50"
                        :disabled="!docxFile || isUploadingDocx"
                    >
                        <Loader2 v-if="isUploadingDocx" class="w-4 h-4 animate-spin" />
                        <UploadCloud v-else class="w-4 h-4" />
                        <span>{{ isUploadingDocx ? 'Memproses Soal...' : 'Mulai Impor Soal' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ================= MODAL 2: QUICK TEXT IMPORT (AIKEN) ================= -->
        <div 
            v-if="showQuickTextModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-fade-in"
        >
            <div class="bg-white rounded-3xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden p-6 sm:p-7 space-y-4">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center">
                            <ClipboardList class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900">Quick Text Importer</h3>
                            <p class="text-xs text-slate-500">Salin & tempel (copy-paste) soal teks langsung tanpa upload file</p>
                        </div>
                    </div>
                    <button 
                        @click="showQuickTextModal = false"
                        type="button" 
                        class="p-1.5 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-600 cursor-pointer"
                    >
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Petunjuk Format -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                    <p class="font-bold text-slate-800">Format Penulisan (Pisahkan antar nomor soal dengan 1 baris kosong):</p>
                    <pre class="text-[11px] font-mono bg-white p-2.5 rounded-xl border border-slate-200 text-slate-700 leading-relaxed overflow-x-auto">1. わたしは まいにち (____) を べんきょうします。
A. にほんご
B. にぼんご
C. えいご
D. ちゅうごくご
KUNCI: A
PEMBAHASAN: にほんご artinya bahasa Jepang.</pre>
                </div>

                <!-- Textarea -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Tempelkan Teks Soal di Sini:
                    </label>
                    <textarea 
                        v-model="quickTextContent"
                        rows="8"
                        placeholder="Tempelkan soal-soal Anda di sini..."
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-amber-500 focus:outline-none"
                    ></textarea>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <button 
                        @click="showQuickTextModal = false"
                        type="button" 
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 cursor-pointer"
                        :disabled="isSubmittingText"
                    >
                        Batal
                    </button>

                    <button 
                        @click="submitQuickText"
                        type="button" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-black shadow-md shadow-amber-500/25 transition-all cursor-pointer disabled:opacity-50"
                        :disabled="!quickTextContent.trim() || isSubmittingText"
                    >
                        <Loader2 v-if="isSubmittingText" class="w-4 h-4 animate-spin" />
                        <ClipboardList v-else class="w-4 h-4" />
                        <span>{{ isSubmittingText ? 'Memproses Teks...' : 'Impor Teks Soal' }}</span>
                    </button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import JapaneseInput from '@/Components/JapaneseInput.vue';
import { romajiToHiragana, hiraganaToKatakana, createFuriganaRuby } from '@/Utils/japaneseConverter';
import { confirmDialog, notifySuccess, notifyError } from '@/Utils/alert';
import { 
    ArrowLeft, 
    PlusCircle, 
    Eye, 
    HelpCircle, 
    FileEdit, 
    Trash2,
    Save,
    FileDown,
    UploadCloud,
    Download,
    ClipboardList,
    X,
    CheckCircle2,
    AlertCircle,
    FileText,
    Loader2,
    Search,
    Plus,
    Minus,
    MoreHorizontal
} from 'lucide-vue-next';

const props = defineProps({
    package: Object,
    subjects: Array,
    batches: Array,
    learningIndicators: Array,
});

const showMobileActionsDropdown = ref(false);

const formatOptionKey = (key, idx) => {
    if (!key && key !== 0) return String.fromCharCode(65 + (idx || 0));
    const normalized = String(key).trim().toUpperCase();
    const map = { '1': 'A', '2': 'B', '3': 'C', '4': 'D', '5': 'E' };
    return map[normalized] || normalized;
};

// Toggle Publish
const togglePublish = () => {
    const action = props.package.is_published ? 'menonaktifkan' : 'mengaktifkan';
    confirmDialog(`Yakin ingin ${action} paket ujian ini?`, `Status paket "${props.package.title}" akan diperbarui.`).then((res) => {
        if (res.isConfirmed) {
            router.post(route('sensei.questions.toggle-publish', props.package.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    notifySuccess('Berhasil', `Paket soal berhasil diperbarui!`);
                },
            });
        }
    });
};

// ================= MODAL IMPORT STATE =================
const showImportWordModal = ref(false);
const showQuickTextModal = ref(false);
const docxFile = ref(null);
const docxFileName = ref('');
const isUploadingDocx = ref(false);
const isDraggingDocx = ref(false);
const quickTextContent = ref('');
const isSubmittingText = ref(false);

const handleDocxDrop = (e) => {
    isDraggingDocx.value = false;
    const file = e.dataTransfer?.files?.[0];
    if (file) {
        processSelectedDocx(file);
    }
};

const handleDocxChange = (e) => {
    const file = e.target.files?.[0];
    if (file) {
        processSelectedDocx(file);
    }
};

const processSelectedDocx = (file) => {
    if (!file.name.toLowerCase().endsWith('.docx')) {
        notifyError('Format Salah', 'Harap pilih file dokumen Microsoft Word dengan format .docx');
        return;
    }
    if (file.size > 25 * 1024 * 1024) {
        notifyError('File Terlalu Besar', 'Ukuran file maksimal 25 MB');
        return;
    }
    docxFile.value = file;
    docxFileName.value = file.name;
};

const submitDocxImport = () => {
    if (!docxFile.value) {
        notifyError('Perhatian', 'Pilih file Word (.docx) terlebih dahulu.');
        return;
    }
    isUploadingDocx.value = true;
    const formData = new FormData();
    formData.append('file', docxFile.value);

    router.post(route('sensei.questions.import-docx', props.package.id), formData, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            showImportWordModal.value = false;
            docxFile.value = null;
            docxFileName.value = '';
            notifySuccess('Import Berhasil', 'Soal dari Word berhasil dimasukkan ke dalam paket!');
        },
        onError: (errors) => {
            notifyError('Gagal Import', errors.error || errors.file || 'Terjadi kesalahan saat memproses dokumen Word.');
        },
        onFinish: () => {
            isUploadingDocx.value = false;
        },
    });
};

const submitQuickText = () => {
    if (!quickTextContent.value || quickTextContent.value.trim().length < 10) {
        notifyError('Perhatian', 'Teks soal masih kosong atau terlalu pendek.');
        return;
    }
    isSubmittingText.value = true;
    router.post(route('sensei.questions.import-text', props.package.id), {
        raw_text: quickTextContent.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showQuickTextModal.value = false;
            quickTextContent.value = '';
            notifySuccess('Import Berhasil', 'Soal dari teks berhasil dimasukkan!');
        },
        onError: (errors) => {
            notifyError('Gagal Import', errors.error || errors.raw_text || 'Format teks tidak dapat dibaca.');
        },
        onFinish: () => {
            isSubmittingText.value = false;
        },
    });
};

// ================= FILTER & SEARCH BUTIR SOAL =================
const optionKeys = ['A', 'B', 'C', 'D', 'E'];
const questionSearchQuery = ref('');
const selectedSectionFilter = ref('all');

const resetQuestionFilters = () => {
    questionSearchQuery.value = '';
    selectedSectionFilter.value = 'all';
};

const sectionFilterTabs = computed(() => {
    const list = props.package.questions || [];
    const counts = {
        all: list.length,
        moji_goi: 0,
        bunpou: 0,
        dokkai: 0,
        choukai: 0,
    };
    list.forEach(q => {
        const s = q.section_type || (q.audio_url ? 'choukai' : 'bunpou');
        if (counts[s] !== undefined) counts[s]++;
    });
    return [
        { id: 'all', label: 'Semua', count: counts.all },
        { id: 'moji_goi', label: 'Moji-Goi', count: counts.moji_goi },
        { id: 'bunpou', label: 'Bunpou', count: counts.bunpou },
        { id: 'dokkai', label: 'Dokkai', count: counts.dokkai },
        { id: 'choukai', label: 'Choukai', count: counts.choukai },
    ];
});

const filteredQuestions = computed(() => {
    const list = (props.package.questions || []).map((q, originalIndex) => ({
        ...q,
        originalIndex,
    }));
    return list.filter(q => {
        // Section Filter
        const sec = q.section_type || (q.audio_url ? 'choukai' : 'bunpou');
        if (selectedSectionFilter.value !== 'all' && sec !== selectedSectionFilter.value) {
            return false;
        }
        // Search Filter
        if (questionSearchQuery.value.trim()) {
            const query = questionSearchQuery.value.toLowerCase().trim();
            const textMatch = (q.question_text || '').toLowerCase().includes(query);
            const passageMatch = (q.reading_passage || '').toLowerCase().includes(query);
            const instructionMatch = (q.instruction || '').toLowerCase().includes(query);
            const explanationMatch = (q.explanation || '').toLowerCase().includes(query);
            const categoryMatch = (q.category?.name || '').toLowerCase().includes(query);
            const optionsMatch = (q.options || []).some(o => (o.option_text || '').toLowerCase().includes(query));
            return textMatch || passageMatch || instructionMatch || explanationMatch || categoryMatch || optionsMatch;
        }
        return true;
    });
});

const groupedIndicators = computed(() => {
    const groups = {};
    const sectionLabels = {
        moji_goi: '📝 Moji & Goi',
        bunpou: '🔤 Bunpou',
        dokkai: '📖 Dokkai',
        choukai: '🎧 Choukai',
        general: '🌐 Umum / Lainnya',
    };
    (props.learningIndicators || []).forEach(ind => {
        const sec = ind.section_type || 'general';
        const label = sectionLabels[sec] || sec.toUpperCase();
        if (!groups[label]) groups[label] = [];
        groups[label].push(ind);
    });
    return groups;
});

// ================= WORKSPACE EDITOR BUTIR SOAL =================
const isEditorMode = ref(false);
const editingItem = ref(null);
const editingItemIndex = ref(-1);
const isSubmittingItem = ref(false);
const correctOptionIndex = ref(0);

const itemForm = ref({
    section_type: 'bunpou',
    question_category_id: '',
    instruction: '',
    reading_passage: '',
    question_text: '',
    score_points: 1.0,
    explanation: '',
    image_file: null,
    audio_file: null,
    options: [
        { option_text: '' },
        { option_text: '' },
        { option_text: '' },
        { option_text: '' },
    ],
});

const addOption = () => {
    if (itemForm.value.options.length < 5) {
        itemForm.value.options.push({ option_text: '' });
    }
};

const removeOption = (idx) => {
    if (itemForm.value.options.length > 2) {
        itemForm.value.options.splice(idx, 1);
        if (correctOptionIndex.value >= itemForm.value.options.length) {
            correctOptionIndex.value = 0;
        }
    }
};

const onImageChange = (e) => {
    itemForm.value.image_file = e.target.files[0] || null;
};

const onAudioChange = (e) => {
    itemForm.value.audio_file = e.target.files[0] || null;
    if (e.target.files[0]) {
        itemForm.value.section_type = 'choukai';
    }
};

// Helper Huruf Jepang untuk Pilihan Jawaban
const activeOptionIndex = ref(null);
const optionActiveScript = ref('hiragana');
const optHiraganaRomaji = ref('');
const optPreviewHiragana = ref('');
const optKatakanaRomaji = ref('');
const optPreviewKatakana = ref('');
const optKanjiText = ref('');
const optKanjiReadingRomaji = ref('');

const optPreviewKanjiRuby = computed(() => {
    if (!optKanjiText.value) return '...';
    if (!optKanjiReadingRomaji.value) return optKanjiText.value;
    const readingHira = romajiToHiragana(optKanjiReadingRomaji.value);
    return createFuriganaRuby(optKanjiText.value, readingHira);
});

const openOptionJapaneseModal = (idx) => {
    activeOptionIndex.value = idx;
    optionActiveScript.value = 'hiragana';
    optHiraganaRomaji.value = '';
    optPreviewHiragana.value = '';
    optKatakanaRomaji.value = '';
    optPreviewKatakana.value = '';
    optKanjiText.value = '';
    optKanjiReadingRomaji.value = '';
};

const handleOptHiraganaInput = (e) => {
    optPreviewHiragana.value = romajiToHiragana(e.target.value);
};

const handleOptKatakanaInput = (e) => {
    optPreviewKatakana.value = hiraganaToKatakana(romajiToHiragana(e.target.value));
};

const confirmInsertToOption = () => {
    if (activeOptionIndex.value === null) return;
    let textToInsert = '';
    if (optionActiveScript.value === 'hiragana') {
        textToInsert = optPreviewHiragana.value;
    } else if (optionActiveScript.value === 'katakana') {
        textToInsert = optPreviewKatakana.value;
    } else if (optionActiveScript.value === 'kanji') {
        textToInsert = optPreviewKanjiRuby.value;
    }
    if (textToInsert && textToInsert !== '...') {
        itemForm.value.options[activeOptionIndex.value].option_text += (itemForm.value.options[activeOptionIndex.value].option_text ? ' ' : '') + textToInsert;
    }
    activeOptionIndex.value = null;
};

const openAddItemModal = () => {
    editingItem.value = null;
    editingItemIndex.value = -1;
    correctOptionIndex.value = 0;
    itemForm.value = {
        section_type: 'bunpou',
        question_category_id: '',
        instruction: '',
        reading_passage: '',
        question_text: '',
        score_points: 1.0,
        explanation: '',
        image_file: null,
        audio_file: null,
        options: [
            { option_text: '' },
            { option_text: '' },
            { option_text: '' },
            { option_text: '' },
        ],
    };
    isEditorMode.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const openEditItemModal = (item, index = 0) => {
    editingItem.value = item;
    editingItemIndex.value = index;
    
    // Find correct index
    const cIdx = item.options?.findIndex(o => o.is_correct);
    correctOptionIndex.value = cIdx >= 0 ? cIdx : 0;

    const opts = (item.options && item.options.length >= 2)
        ? item.options.map(o => ({ id: o.id, option_text: o.option_text }))
        : [
            { option_text: '' },
            { option_text: '' },
            { option_text: '' },
            { option_text: '' },
        ];

    itemForm.value = {
        section_type: item.section_type || (item.audio_url ? 'choukai' : 'bunpou'),
        question_category_id: item.question_category_id || '',
        instruction: item.instruction || '',
        reading_passage: item.reading_passage || '',
        question_text: item.question_text || '',
        score_points: Number(item.score_points) || 1.0,
        explanation: item.explanation || '',
        image_file: null,
        audio_file: null,
        options: opts,
    };
    isEditorMode.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const closeEditor = () => {
    isEditorMode.value = false;
    editingItem.value = null;
    editingItemIndex.value = -1;
};

const submitItemForm = () => {
    // Client-side validation
    if (!itemForm.value.question_text || !itemForm.value.question_text.trim()) {
        notifyError('Validasi Gagal', 'Teks pertanyaan wajib diisi.');
        return;
    }

    if (!itemForm.value.options || itemForm.value.options.length < 2) {
        notifyError('Validasi Gagal', 'Minimal harus ada 2 pilihan jawaban.');
        return;
    }

    if (itemForm.value.options.some(o => !o.option_text || !o.option_text.trim())) {
        notifyError('Validasi Gagal', 'Semua pilihan jawaban yang aktif harus diisi teks.');
        return;
    }

    isSubmittingItem.value = true;

    // Build payload with multipart support
    const formData = new FormData();
    formData.append('question_text', itemForm.value.question_text);
    formData.append('section_type', itemForm.value.section_type || 'bunpou');
    if (itemForm.value.question_category_id) formData.append('question_category_id', itemForm.value.question_category_id);
    if (itemForm.value.instruction) formData.append('instruction', itemForm.value.instruction);
    if (itemForm.value.reading_passage) formData.append('reading_passage', itemForm.value.reading_passage);
    formData.append('score_points', itemForm.value.score_points);
    if (itemForm.value.explanation) formData.append('explanation', itemForm.value.explanation);
    if (itemForm.value.image_file) formData.append('image_file', itemForm.value.image_file);
    if (itemForm.value.audio_file) formData.append('audio_file', itemForm.value.audio_file);

    itemForm.value.options.forEach((opt, idx) => {
        formData.append(`options[${idx}][option_text]`, opt.option_text);
        formData.append(`options[${idx}][is_correct]`, idx === correctOptionIndex.value ? '1' : '0');
        if (opt.id) formData.append(`options[${idx}][id]`, opt.id);
    });

    if (editingItem.value) {
        formData.append('_method', 'PUT');
        router.post(route('sensei.questions.update-item', [props.package.id, editingItem.value.id]), formData, {
            onSuccess: () => {
                isEditorMode.value = false;
                notifySuccess('Berhasil', 'Butir soal berhasil diperbarui!');
            },
            onError: (errors) => {
                const firstMsg = Object.values(errors)[0] || 'Terjadi kesalahan saat memperbarui soal.';
                notifyError('Gagal Menyimpan', firstMsg);
            },
            onFinish: () => isSubmittingItem.value = false,
        });
    } else {
        router.post(route('sensei.questions.store-item', props.package.id), formData, {
            onSuccess: () => {
                isEditorMode.value = false;
                notifySuccess('Berhasil', 'Butir soal baru berhasil ditambahkan ke paket!');
            },
            onError: (errors) => {
                const firstMsg = Object.values(errors)[0] || 'Terjadi kesalahan saat menambahkan butir soal.';
                notifyError('Gagal Menyimpan', firstMsg);
            },
            onFinish: () => isSubmittingItem.value = false,
        });
    }
};

const confirmDeleteItem = (item) => {
    confirmDialog('Hapus Butir Soal Ini?', 'Butir soal ini akan dihapus dari paket ujian secara permanen.').then((res) => {
        if (res.isConfirmed) {
            router.delete(route('sensei.questions.destroy-item', [props.package.id, item.id]), {
                preserveScroll: true,
                onSuccess: () => {
                    notifySuccess('Terhapus', 'Butir soal berhasil dihapus.');
                },
                onError: () => {
                    notifyError('Gagal Menghapus', 'Butir soal tidak dapat dihapus atau sudah terhapus. Memuat ulang data...');
                    router.reload();
                },
            });
        }
    });
};
</script>
