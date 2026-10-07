<template>
    <Head :title="isJapanese ? '問題バンク設定 - 指導員ポータル' : 'Pengaturan Bank Soal - Sensei Portal'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-12">

            <!-- HEADER -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-50 text-violet-700 text-xs font-semibold mb-2 border border-violet-200/60">
                    <Settings class="w-3.5 h-3.5" />
                    <span>{{ isJapanese ? '問題バンク構成管理' : 'Konfigurasi Bank Soal' }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <Settings class="w-6 h-6 text-violet-600" />
                    <span>{{ isJapanese ? '問題バンク設定' : 'Pengaturan Bank Soal' }}</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    {{ isJapanese 
                        ? '機関のカリキュラムに合わせて、習熟レベルと問題分野を管理します。これらの設定は問題作成時に選択肢として反映されます。' 
                        : 'Atur Level Kompetensi dan Kategori / Indikator Soal sesuai kebutuhan LPK Anda. Data ini akan muncul sebagai pilihan saat Sensei membuat soal baru.' 
                    }}
                </p>
            </div>

            <!-- FLASH MESSAGES -->
            <div v-if="$page.props.flash?.success"
                class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2 shadow-sm">
                <CheckCircle2 class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>{{ $page.props.flash.success }}</span>
            </div>
            <div v-if="$page.props.errors?.error"
                class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center gap-2 shadow-sm">
                <AlertCircle class="w-5 h-5 text-rose-600 shrink-0" />
                <span>{{ $page.props.errors.error }}</span>
            </div>

            <!-- GRID: Level (kiri) | Kategori (kanan) -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">

                <!-- ===================== PANEL LEVEL KOMPETENSI ===================== -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-100 bg-blue-50/40 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-black text-slate-800 flex items-center gap-2">
                                <Target class="w-5 h-5 text-blue-600" />
                                {{ isJapanese ? '習熟レベル管理' : 'Level Kompetensi' }}
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ isJapanese ? `${levels.length} 件のレベル登録済み` : `${levels.length} level terdaftar` }}
                            </p>
                        </div>
                        <button @click="openAddLevelModal"
                            class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center gap-1.5 cursor-pointer transition-all shadow-sm shadow-blue-400/20 active:scale-95">
                            <PlusCircle class="w-4 h-4" /> {{ isJapanese ? 'レベルを追加' : 'Tambah Level' }}
                        </button>
                    </div>

                    <!-- Daftar Level -->
                    <div class="divide-y divide-slate-100 overflow-y-auto max-h-[600px]">
                        <div v-if="levels.length === 0" class="p-10 text-center text-slate-400 text-sm">
                            {{ isJapanese ? 'レベルが登録されていません。最初のレベルを追加してください。' : 'Belum ada level. Tambahkan level pertama Anda!' }}
                        </div>

                        <div v-for="lv in levels" :key="lv.id"
                            class="px-5 py-4 hover:bg-slate-50/60 transition-colors">
                            <div class="flex items-center gap-3">
                                <!-- Badge Kode/Level -->
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black min-w-[54px] text-center"
                                    :class="lv.is_active ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-500'">
                                    {{ lv.code }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-bold text-slate-800">{{ lv.name }}</p>
                                        <!-- Usage Badge -->
                                        <span v-if="lv.questions_count > 0"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200/80"
                                            :title="isJapanese ? `${lv.questions_count} 件の問題で使用中` : `Digunakan oleh ${lv.questions_count} butir soal`">
                                            {{ lv.questions_count }} {{ isJapanese ? '問' : 'Soal Terkait' }}
                                        </span>
                                        <span v-else class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-400">
                                            0 {{ isJapanese ? '問' : 'Soal' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ lv.description || '—' }}</p>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <!-- Toggle Active -->
                                    <button @click="toggleLevelActive(lv)"
                                        class="p-1.5 rounded-lg transition-colors cursor-pointer"
                                        :class="lv.is_active ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:bg-slate-100'"
                                        :title="lv.is_active ? (isJapanese ? '有効 — クリックして無効化' : 'Aktif — Klik untuk nonaktifkan') : (isJapanese ? '無効 — クリックして有効化' : 'Nonaktif — Klik untuk aktifkan')">
                                        <CheckCircle2 class="w-4 h-4" />
                                    </button>
                                    <button @click="openEditLevelModal(lv)"
                                        class="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 transition-colors cursor-pointer"
                                        :title="isJapanese ? '編集' : 'Edit Level'">
                                        <Edit class="w-3.5 h-3.5" />
                                    </button>
                                    <button @click="deleteLevel(lv)"
                                        class="p-1.5 rounded-lg transition-colors cursor-pointer"
                                        :class="lv.questions_count > 0 ? 'text-slate-300 hover:text-rose-500 hover:bg-rose-50' : 'text-rose-500 hover:bg-rose-50'"
                                        :title="lv.questions_count > 0 ? (isJapanese ? `${lv.questions_count}件の問題で使用中のため削除不可` : `Masih digunakan oleh ${lv.questions_count} butir soal`) : (isJapanese ? '削除' : 'Hapus Level')">
                                        <Trash2 class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===================== PANEL KATEGORI SOAL ===================== -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-100 bg-rose-50/40 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-black text-slate-800 flex items-center gap-2">
                                <Tag class="w-5 h-5 text-rose-600" />
                                {{ isJapanese ? '問題分野・セクション管理' : 'Kategori / Indikator Soal' }}
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ isJapanese ? `${categories.length} 件の分野登録済み` : `${categories.length} kategori terdaftar` }}
                            </p>
                        </div>
                        <button @click="openAddCategoryModal"
                            class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold flex items-center gap-1.5 cursor-pointer transition-all shadow-sm shadow-rose-400/20 active:scale-95">
                            <PlusCircle class="w-4 h-4" /> {{ isJapanese ? '分野を追加' : 'Tambah Kategori' }}
                        </button>
                    </div>

                    <!-- Filter & Search Kategori -->
                    <div class="p-4 border-b border-slate-100 bg-slate-50/40 space-y-3">
                        <div class="flex items-center gap-2">
                            <input v-model="categorySearch" type="text"
                                :placeholder="isJapanese ? '分野名で検索...' : 'Cari nama kategori / indikator...'"
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none" />
                        </div>
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                            <button @click="selectedSectionTab = 'all'"
                                class="px-3 py-1.5 rounded-lg font-bold shrink-0 transition-colors cursor-pointer"
                                :class="selectedSectionTab === 'all' ? 'bg-rose-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'">
                                {{ isJapanese ? 'すべて' : 'Semua' }} ({{ categories.length }})
                            </button>
                            <button v-for="sec in sectionTabs" :key="sec.key"
                                @click="selectedSectionTab = sec.key"
                                class="px-2.5 py-1.5 rounded-lg font-bold shrink-0 transition-colors flex items-center gap-1 cursor-pointer"
                                :class="selectedSectionTab === sec.key ? 'bg-rose-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'">
                                <span>{{ sec.icon }}</span>
                                <span>{{ sec.label }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Daftar Kategori -->
                    <div class="divide-y divide-slate-100 overflow-y-auto max-h-[600px]">
                        <div v-if="filteredCategories.length === 0" class="p-10 text-center text-slate-400 text-sm">
                            {{ isJapanese ? '該当する分野・カテゴリが見つかりません。' : 'Tidak ada kategori yang cocok.' }}
                        </div>

                        <div v-for="cat in filteredCategories" :key="cat.id"
                            class="px-5 py-4 hover:bg-slate-50/60 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="text-xl shrink-0">{{ sectionIcon(cat.section_type) }}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-bold text-slate-800">{{ cat.name }}</p>
                                        <!-- Level Badge -->
                                        <span v-if="cat.level_code || cat.level" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                                            {{ cat.level_code || cat.level }}
                                        </span>
                                        <!-- Usage Badge -->
                                        <span v-if="cat.questions_count > 0"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200/80"
                                            :title="isJapanese ? `${cat.questions_count} 件の問題で使用中` : `Digunakan oleh ${cat.questions_count} butir soal`">
                                            {{ cat.questions_count }} {{ isJapanese ? '問' : 'Soal Terkait' }}
                                        </span>
                                        <span v-else class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-400">
                                            0 {{ isJapanese ? '問' : 'Soal' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-mono">{{ cat.section_type }}</span>
                                        {{ cat.description ? ' · ' + cat.description : '' }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button @click="openEditCategoryModal(cat)"
                                        class="p-1.5 rounded-lg text-blue-500 hover:bg-blue-50 transition-colors cursor-pointer"
                                        :title="isJapanese ? '編集' : 'Edit Kategori'">
                                        <Edit class="w-3.5 h-3.5" />
                                    </button>
                                    <button @click="deleteCategory(cat)"
                                        class="p-1.5 rounded-lg transition-colors cursor-pointer"
                                        :class="cat.questions_count > 0 ? 'text-amber-600 hover:bg-amber-50' : 'text-rose-500 hover:bg-rose-50'"
                                        :title="cat.questions_count > 0 ? (isJapanese ? '関連問題を解除して削除' : 'Lepas relasi soal & hapus kategori') : (isJapanese ? '削除' : 'Hapus Kategori')">
                                        <Trash2 class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INFO CARD -->
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 flex gap-3">
                <Info class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
                <div>
                    <p class="text-sm font-bold text-blue-800">
                        {{ isJapanese ? '重要なお知らせ' : 'Informasi & Petunjuk Penggunaan' }}
                    </p>
                    <p class="text-xs text-blue-700 mt-1 leading-relaxed">
                        {{ isJapanese 
                            ? 'すでに問題データで使用されているレベルや分野には使用中のバッジが表示されます。レベルを完全に削除したい場合は、対象の問題を別のレベルに変更するか削除してください。分野（カテゴリ）については、削除時に問題の紐付けのみを安全に解除することが可能です。' 
                            : 'Level atau Kategori yang sedang digunakan oleh butir soal ditandai dengan badge "Soal Terkait". Untuk Level Kompetensi, data yang masih memiliki soal tidak dapat dihapus demi keamanan integritas data ujian. Untuk Kategori Soal, Anda dapat menghapusnya dengan opsi melepaskan relasi butir soal secara aman tanpa menghapus soal itu sendiri.' 
                        }}
                    </p>
                </div>
            </div>

        </div>

        <!-- ================= MODAL POP-UP LEVEL KOMPETENSI (2026 UI/UX) ================= -->
        <div v-if="showLevelModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-md animate-fade-in" 
            @click.self="closeLevelModal">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl max-w-lg w-full p-6 sm:p-8 space-y-5 animate-scale-up">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                            <Target class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900">
                                {{ isEditingLevel ? (isJapanese ? 'レベルの編集' : 'Edit Level Kompetensi') : (isJapanese ? '新規レベルの追加' : 'Tambah Level Kompetensi') }}
                            </h3>
                            <p class="text-xs text-slate-500">
                                {{ isJapanese ? 'カリキュラムの習熟レベルを設定します' : 'Atur tingkat kompetensi kurikulum LPK' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="closeLevelModal" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                        <X class="w-4 h-4" />
                    </button>
                </div>

                <form @submit.prevent="submitLevelForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            {{ isJapanese ? 'レベル名称 *' : 'Nama Level *' }}
                        </label>
                        <input 
                            v-model="levelForm.name" 
                            type="text" 
                            required
                            :placeholder="isJapanese ? '例: N5, N4, Dasar, Lanjutan' : 'Contoh: N5, N4, Tingkat Dasar, atau Pra-Pemberangkatan'"
                            class="w-full px-3.5 py-2.5 rounded-xl border text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none transition-all"
                            :class="levelForm.errors.name ? 'border-rose-400 bg-rose-50/40' : 'border-slate-300'" 
                        />
                        <span v-if="levelForm.errors.name" class="text-[11px] text-rose-600 font-semibold block mt-1">
                            {{ levelForm.errors.name }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            {{ isJapanese ? '表示順序' : 'Urutan Tampil' }}
                        </label>
                        <input 
                            v-model.number="levelForm.order_index" 
                            type="number" 
                            min="0"
                            placeholder="1, 2, 3..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none transition-all" 
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            {{ isJapanese ? '説明・概要' : 'Deskripsi' }}
                        </label>
                        <textarea 
                            v-model="levelForm.description" 
                            rows="3"
                            :placeholder="isJapanese ? 'このレベルの概要・基準...' : 'Keterangan ringkas mengenai level kompetensi ini...'"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none transition-all"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button 
                            type="button" 
                            @click="closeLevelModal"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer"
                        >
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button 
                            type="submit" 
                            :disabled="levelForm.processing"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold cursor-pointer transition-all disabled:opacity-50 flex items-center gap-2 shadow-md shadow-blue-500/20 active:scale-95"
                        >
                            <span v-if="levelForm.processing" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span>{{ isEditingLevel ? (isJapanese ? '更新保存' : 'Simpan Perubahan') : (isJapanese ? 'レベルを保存' : 'Simpan Level') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL POP-UP KATEGORI / INDIKATOR SOAL (2026 UI/UX) ================= -->
        <div v-if="showCategoryModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-md animate-fade-in" 
            @click.self="closeCategoryModal">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xl max-w-lg w-full p-6 sm:p-8 space-y-5 animate-scale-up">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100">
                            <Tag class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900">
                                {{ isEditingCategory ? (isJapanese ? '分野の編集' : 'Edit Kategori / Indikator') : (isJapanese ? '新規分野の追加' : 'Tambah Kategori / Indikator') }}
                            </h3>
                            <p class="text-xs text-slate-500">
                                {{ isJapanese ? '問題のセクションと対象分野を設定します' : 'Atur kategori pembagian soal dan indikator capaian' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="closeCategoryModal" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                        <X class="w-4 h-4" />
                    </button>
                </div>

                <form @submit.prevent="submitCategoryForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            {{ isJapanese ? '分野名称 *' : 'Nama Kategori / Indikator *' }}
                        </label>
                        <input 
                            v-model="categoryForm.name" 
                            type="text" 
                            required
                            :placeholder="isJapanese ? '例: 文字・語彙, 敬語, カンジ読解' : 'Contoh: Moji & Goi, Keigo, Tata Bahasa Dasar'"
                            class="w-full px-3.5 py-2.5 rounded-xl border text-xs sm:text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none transition-all"
                            :class="categoryForm.errors.name ? 'border-rose-400 bg-rose-50/40' : 'border-slate-300'" 
                        />
                        <span v-if="categoryForm.errors.name" class="text-[11px] text-rose-600 font-semibold block mt-1">
                            {{ categoryForm.errors.name }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                {{ isJapanese ? 'セクション区分 *' : 'Tipe Seksi (Section Type) *' }}
                            </label>
                            <select 
                                v-model="categoryForm.section_type" 
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none transition-all bg-white"
                            >
                                <option value="moji_goi">📖 {{ isJapanese ? '文字・語彙' : 'Huruf & Kosakata' }}</option>
                                <option value="bunpou">🧩 {{ isJapanese ? '文法' : 'Tata Bahasa' }}</option>
                                <option value="dokkai">📄 {{ isJapanese ? '読解' : 'Membaca (Dokkai)' }}</option>
                                <option value="choukai">🎧 {{ isJapanese ? '聴解' : 'Mendengarkan (Choukai)' }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                {{ isJapanese ? '対象習熟レベル' : 'Level Target' }}
                            </label>
                            <select 
                                v-model="categoryForm.level_code"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none transition-all bg-white"
                            >
                                <option value="">{{ isJapanese ? '共通 / 指定なし' : '— Semua Level —' }}</option>
                                <option v-for="lv in levels" :key="lv.id" :value="lv.code">
                                    {{ lv.code }} ({{ lv.name }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            {{ isJapanese ? '説明・備考' : 'Deskripsi' }}
                        </label>
                        <textarea 
                            v-model="categoryForm.description" 
                            rows="3"
                            :placeholder="isJapanese ? 'この分野の出題内容...' : 'Keterangan ringkas mengenai kategori soal ini...'"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none transition-all"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button 
                            type="button" 
                            @click="closeCategoryModal"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer"
                        >
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button 
                            type="submit" 
                            :disabled="categoryForm.processing"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold cursor-pointer transition-all disabled:opacity-50 flex items-center gap-2 shadow-md shadow-rose-500/20 active:scale-95"
                        >
                            <span v-if="categoryForm.processing" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span>{{ isEditingCategory ? (isJapanese ? '更新保存' : 'Simpan Perubahan') : (isJapanese ? '分野を保存' : 'Simpan Kategori') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { confirmDialog } from '@/Utils/alert';
import { useLang } from '@/Composables/useLang';
import {
    Settings, Target, Tag, PlusCircle, Edit, Trash2,
    CheckCircle2, AlertCircle, Info, X
} from 'lucide-vue-next';

const { isJapanese } = useLang();

const props = defineProps({
    levels:     Array,
    categories: Array,
});

// ===== LEVEL STATE =====
const showLevelModal   = ref(false);
const isEditingLevel   = ref(false);
const selectedLevelId  = ref(null);

const levelForm = useForm({
    name: '',
    description: '',
    order_index: 99,
    is_active: true,
});

const openAddLevelModal = () => {
    isEditingLevel.value = false;
    selectedLevelId.value = null;
    levelForm.reset();
    levelForm.clearErrors();
    levelForm.order_index = (props.levels?.length || 0) + 1;
    levelForm.is_active = true;
    showLevelModal.value = true;
};

const openEditLevelModal = (lv) => {
    isEditingLevel.value = true;
    selectedLevelId.value = lv.id;
    levelForm.clearErrors();
    levelForm.name = lv.name;
    levelForm.description = lv.description || '';
    levelForm.order_index = lv.order_index;
    levelForm.is_active = lv.is_active;
    showLevelModal.value = true;
};

const closeLevelModal = () => {
    showLevelModal.value = false;
    levelForm.reset();
    levelForm.clearErrors();
};

const submitLevelForm = () => {
    if (isEditingLevel.value) {
        levelForm.put(route('sensei.settings.levels.update', selectedLevelId.value), {
            preserveScroll: true,
            onSuccess: () => closeLevelModal(),
        });
    } else {
        levelForm.post(route('sensei.settings.levels.store'), {
            preserveScroll: true,
            onSuccess: () => closeLevelModal(),
        });
    }
};

const toggleLevelActive = (lv) => {
    router.put(route('sensei.settings.levels.update', lv.id), {
        name: lv.name,
        description: lv.description,
        order_index: lv.order_index,
        is_active: !lv.is_active,
    }, { preserveScroll: true });
};

const deleteLevel = (lv) => {
    if (lv.questions_count > 0) {
        const title = isJapanese.value ? 'レベルを削除できません' : 'Level Tidak Dapat Dihapus';
        const text = isJapanese.value 
            ? `このレベルは現在 ${lv.questions_count} 件の問題で使用されています。問題データを変更または削除してから再度お試しください。`
            : `Level "${lv.name}" (${lv.code}) masih digunakan oleh ${lv.questions_count} butir soal di database. Silakan pindahkan atau hapus soal terkait terlebih dahulu.`;
        confirmDialog(title, text, 'warning');
        return;
    }

    const title = isJapanese.value ? `レベル「${lv.name}」を削除しますか？` : `Hapus Level "${lv.name}"?`;
    const text = isJapanese.value ? 'このレベルは完全に削除されます。' : 'Level ini belum memiliki soal terkait dan akan dihapus permanen.';
    confirmDialog(title, text).then(r => {
        if (r.isConfirmed) {
            router.delete(route('sensei.settings.levels.destroy', lv.id), { preserveScroll: true });
        }
    });
};

// ===== CATEGORY STATE =====
const showCategoryModal   = ref(false);
const isEditingCategory   = ref(false);
const selectedCategoryId  = ref(null);
const categorySearch      = ref('');
const selectedSectionTab  = ref('all');

const sectionTabs = [
    { key: 'moji_goi', label: 'Moji-Goi', icon: '📖' },
    { key: 'bunpou', label: 'Bunpou', icon: '🧩' },
    { key: 'dokkai', label: 'Dokkai', icon: '📄' },
    { key: 'choukai', label: 'Choukai', icon: '🎧' },
];

const filteredCategories = computed(() => {
    return (props.categories || []).filter(cat => {
        const matchTab = selectedSectionTab.value === 'all' || cat.section_type === selectedSectionTab.value;
        const q = categorySearch.value.trim().toLowerCase();
        if (!q) return matchTab;
        const matchSearch = (cat.name && cat.name.toLowerCase().includes(q)) ||
                            (cat.description && cat.description.toLowerCase().includes(q));
        return matchTab && matchSearch;
    });
});

const categoryForm = useForm({
    name: '',
    section_type: 'moji_goi',
    level_code: '',
    description: '',
});

const openAddCategoryModal = () => {
    isEditingCategory.value = false;
    selectedCategoryId.value = null;
    categoryForm.reset();
    categoryForm.clearErrors();
    categoryForm.section_type = 'moji_goi';
    categoryForm.level_code = '';
    showCategoryModal.value = true;
};

const openEditCategoryModal = (cat) => {
    isEditingCategory.value = true;
    selectedCategoryId.value = cat.id;
    categoryForm.clearErrors();
    categoryForm.name = cat.name;
    categoryForm.section_type = cat.section_type;
    categoryForm.level_code = cat.level_code || '';
    categoryForm.description = cat.description || '';
    showCategoryModal.value = true;
};

const closeCategoryModal = () => {
    showCategoryModal.value = false;
    categoryForm.reset();
    categoryForm.clearErrors();
};

const submitCategoryForm = () => {
    if (isEditingCategory.value) {
        categoryForm.put(route('sensei.settings.categories.update', selectedCategoryId.value), {
            preserveScroll: true,
            onSuccess: () => closeCategoryModal(),
        });
    } else {
        categoryForm.post(route('sensei.settings.categories.store'), {
            preserveScroll: true,
            onSuccess: () => closeCategoryModal(),
        });
    }
};

const deleteCategory = (cat) => {
    if (cat.questions_count > 0) {
        const title = isJapanese.value ? `分野「${cat.name}」を削除しますか？` : `Hapus Kategori "${cat.name}"?`;
        const text = isJapanese.value
            ? `この分野は ${cat.questions_count} 件の問題で使用されています。削除すると問題との紐付けが安全に解除（未分類に設定）されます。続行しますか？`
            : `Kategori ini masih digunakan oleh ${cat.questions_count} butir soal. Jika dihapus, relasi pada butir soal tersebut akan dilepaskan (menjadi tanpa kategori) tanpa menghapus fisik soal. Lanjutkan?`;
        
        confirmDialog(title, text, 'warning').then(r => {
            if (r.isConfirmed) {
                router.delete(route('sensei.settings.categories.destroy', cat.id), {
                    data: { force_detach: true },
                    preserveScroll: true,
                });
            }
        });
        return;
    }

    const title = isJapanese.value ? `分野「${cat.name}」を削除しますか？` : `Hapus Kategori "${cat.name}"?`;
    const text = isJapanese.value ? 'この分野は完全に削除されます。' : 'Kategori ini belum memiliki soal terkait dan akan dihapus permanen.';
    confirmDialog(title, text).then(r => {
        if (r.isConfirmed) {
            router.delete(route('sensei.settings.categories.destroy', cat.id), { preserveScroll: true });
        }
    });
};

// ===== HELPERS =====
const sectionIcon = (type) => ({ moji_goi: '📖', bunpou: '🧩', dokkai: '📄', choukai: '🎧' }[type] || '📝');
</script>
