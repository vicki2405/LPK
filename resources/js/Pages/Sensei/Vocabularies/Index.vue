<template>
    <Head :title="isJapanese ? '単語管理 (Kotoba Manager) - 正夢' : 'Manajemen Kosakata (Kotoba Manager) - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-5 sm:space-y-6 animate-fade-in pb-12">
            <!-- ================= TOP HEADER & HERO SECTION ================= -->
            <div class="relative overflow-hidden bg-white p-5 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-5">
                <!-- Background Japanese Watermark -->
                <div class="absolute -right-6 -bottom-6 font-jp text-8xl font-black text-slate-100 select-none pointer-events-none opacity-60">
                    語彙
                </div>

                <div class="relative z-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold mb-2.5 border border-rose-200/60 font-jp">
                        <Layers class="w-3.5 h-3.5 text-rose-600" />
                        <span>{{ isJapanese ? '単独語彙データベース・単語帳' : 'Bank Kosakata Mandiri & Flashcard Manager' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-japan-red text-white flex items-center justify-center font-jp text-sm font-bold shadow-xs">
                            語
                        </span>
                        <span>{{ isJapanese ? '単語・語彙マネージャー' : 'Manajemen Kosakata (Kotoba Hub)' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                        {{ isJapanese 
                            ? 'Kotoba Hubで単語、漢字、読み方、意味、例文、ネイティブ発音音声を自由に作成・管理できます。' 
                            : 'Kelola seluruh perbendaharaan kosakata mandiri (Kotoba Hub), kanji, furigana otomatis, arti bahasa Indonesia, reibun (contoh kalimat), dan audio pelafalan native secara praktis dan terstruktur.' 
                        }}
                    </p>
                </div>

                <div class="relative z-1 flex items-center gap-2.5 self-stretch sm:self-auto">
                    <Link 
                        :href="route('sensei.vocabularies.create')"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/25 hover:shadow-japan-red/40 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer group"
                    >
                        <PlusCircle class="w-4 h-4 group-hover:rotate-90 transition-transform duration-200" />
                        <span>{{ isJapanese ? '+ 新規単語登録' : '+ Tambah Kosakata' }}</span>
                    </Link>
                </div>
            </div>

            <!-- ================= STATS SUMMARY CARDS ================= -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider">
                            {{ isJapanese ? '登録単語総数' : 'Total Kosakata' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-slate-900 mt-0.5 block font-mono">
                            {{ stats.total_vocabularies ?? 0 }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                        <Layers class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>

                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider">
                            {{ isJapanese ? '音声付き単語' : 'Dengan Audio' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-emerald-600 mt-0.5 block font-mono">
                            {{ stats.total_with_audio ?? 0 }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <Volume2 class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>

                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider">
                            {{ isJapanese ? '登録カテゴリ数' : 'Topik / Kategori' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-blue-600 mt-0.5 block font-mono">
                            {{ categories?.length ?? 0 }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <GraduationCap class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>

                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider">
                            {{ isJapanese ? '動詞の数' : 'Kata Kerja (動詞)' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-amber-600 mt-0.5 block font-mono">
                            {{ stats.total_verbs ?? 0 }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                        <Sparkles class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>
            </div>

            <!-- ================= FILTER & SEARCH TOOLBAR ================= -->
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-xs space-y-3">
                <!-- Row 1: Search & Mode Switcher -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                        <input 
                            type="text" 
                            v-model="searchQuery" 
                            @keyup.enter="applyFilter"
                            :placeholder="isJapanese ? '漢字・読み・意味・カテゴリで検索 (Enter)...' : 'Cari kanji, hiragana, arti, kategori (Tekan Enter)...'"
                            class="w-full pl-10 pr-9 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none transition-all placeholder:text-slate-400 font-jp"
                        />
                        <button 
                            v-if="searchQuery" 
                            @click="searchQuery = ''; applyFilter()" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- Right Buttons: Reset, Filter Toggle Mobile, View Switcher -->
                    <div class="flex items-center justify-between sm:justify-end gap-2">
                        <button 
                            type="button"
                            @click="showMobileFilters = !showMobileFilters"
                            class="sm:hidden px-3 py-2 rounded-xl text-xs font-bold border border-slate-200 flex items-center gap-1.5 transition-colors"
                            :class="showMobileFilters || hasActiveFilter ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-slate-50 text-slate-600'"
                        >
                            <SlidersHorizontal class="w-3.5 h-3.5" />
                            <span>Filter</span>
                            <span v-if="activeFilterCount > 0" class="w-4 h-4 rounded-full bg-japan-red text-white text-[10px] flex items-center justify-center font-bold">
                                {{ activeFilterCount }}
                            </span>
                        </button>

                        <button 
                            v-if="hasActiveFilter"
                            type="button" 
                            @click="resetFilter" 
                            class="px-3 py-2 rounded-xl text-xs font-bold text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors flex items-center gap-1.5 cursor-pointer"
                            title="Reset Filter"
                        >
                            <RotateCcw class="w-3.5 h-3.5" />
                            <span class="hidden sm:inline">Reset</span>
                        </button>

                        <!-- View Switcher -->
                        <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200">
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
                                title="Tampilan Kartu Flashcard"
                            >
                                <LayoutGrid class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Filter Selectors (Desktop always visible, Mobile toggleable) -->
                <div 
                    class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-2 sm:pt-1 border-t border-slate-100 sm:border-t-0"
                    :class="{ 'hidden sm:grid': !showMobileFilters }"
                >
                    <!-- Category / Topic Filter -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">
                            {{ isJapanese ? 'カテゴリ / テーマ' : 'Kategori / Topik' }}
                        </label>
                        <select 
                            v-model="selectedCategory" 
                            @change="applyFilter"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-medium"
                        >
                            <option value="">{{ isJapanese ? 'すべてのカテゴリ' : 'Semua Topik / Kategori' }}</option>
                            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                        </select>
                    </div>

                    <!-- Word Type Filter -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">
                            {{ isJapanese ? '品詞分類' : 'Jenis Kata (Part of Speech)' }}
                        </label>
                        <select 
                            v-model="selectedWordType" 
                            @change="applyFilter"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-jp font-medium"
                        >
                            <option value="">{{ isJapanese ? 'すべての品詞' : 'Semua Jenis Kata' }}</option>
                            <option v-for="wt in wordTypes" :key="wt" :value="wt">{{ formatWordType(wt) }}</option>
                        </select>
                    </div>

                    <!-- Audio Filter -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 mb-1 uppercase tracking-wider">
                            {{ isJapanese ? '音声ファイル' : 'Status File Audio' }}
                        </label>
                        <select 
                            v-model="selectedAudio" 
                            @change="applyFilter"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-medium"
                        >
                            <option value="">{{ isJapanese ? 'すべての状態' : 'Semua (Audio & Non-Audio)' }}</option>
                            <option value="yes">{{ isJapanese ? '🔊 音声ありのみ' : '🔊 Memiliki Audio' }}</option>
                            <option value="no">{{ isJapanese ? '✕ 音声なし' : '✕ Tanpa Audio' }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ================= VIEW MODE 1: DATA TABLE ================= -->
            <div v-if="viewMode === 'table'" class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 font-jp">
                            <tr>
                                <th class="py-3.5 px-4 sm:px-6 w-14 text-center">{{ isJapanese ? '番号' : 'No.' }}</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? '漢字・単語' : 'Kanji & Furigana' }}</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? '意味' : 'Arti Indonesia' }}</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? '品詞' : 'Jenis Kata' }}</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? 'カテゴリ / テーマ' : 'Kategori / Topik' }}</th>
                                <th class="py-3.5 px-4">{{ isJapanese ? '例文・訳' : 'Contoh Kalimat (Reibun)' }}</th>
                                <th class="py-3.5 px-3 text-center">{{ isJapanese ? '音声' : 'Audio' }}</th>
                                <th class="py-3.5 px-4 sm:pr-6 text-right">{{ isJapanese ? '操作' : 'Aksi' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(v, index) in vocabularies.data" :key="v.id" class="hover:bg-slate-50/70 transition-colors group">
                                <td class="py-3.5 px-4 sm:px-6 text-center font-bold text-slate-400 font-mono">
                                    {{ (vocabularies.current_page - 1) * vocabularies.per_page + index + 1 }}
                                </td>

                                <td class="py-3.5 px-4 min-w-[140px]">
                                    <div class="flex items-baseline gap-2">
                                        <span v-if="v.kanji" class="text-base font-black text-slate-950 font-jp">
                                            {{ v.kanji }}
                                        </span>
                                        <span class="text-sm font-bold text-rose-600 font-jp" :class="{ 'text-base': !v.kanji }">
                                            {{ v.hiragana }}
                                        </span>
                                    </div>
                                    <span v-if="v.romaji" class="text-[11px] font-mono text-slate-500 block mt-0.5">
                                        {{ v.romaji }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 font-bold text-slate-900 min-w-[150px]">
                                    {{ v.meaning_id }}
                                </td>

                                <td class="py-3.5 px-4 min-w-[130px]">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold border font-jp"
                                        :class="getWordTypeBadgeClass(v.word_type)">
                                        {{ formatWordType(v.word_type) }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 min-w-[130px]">
                                    <span class="inline-block px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-[11px] font-bold">
                                        {{ v.category || 'Umum' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 max-w-xs">
                                    <div v-if="v.example_sentence_jp" class="space-y-0.5 text-slate-600">
                                        <p class="font-jp text-[11px] font-semibold text-slate-800 line-clamp-1">
                                            {{ v.example_sentence_jp }}
                                        </p>
                                        <p v-if="v.example_sentence_id" class="text-[10px] text-slate-500 italic line-clamp-1">
                                            {{ v.example_sentence_id }}
                                        </p>
                                    </div>
                                    <span v-else class="text-slate-300 text-[11px]">-</span>
                                </td>

                                <td class="py-3.5 px-3 text-center">
                                    <button 
                                        v-if="v.audio_file"
                                        type="button" 
                                        @click="playAudio(v.audio_file, v.id)"
                                        class="p-2 rounded-xl transition-all cursor-pointer inline-flex items-center justify-center shadow-2xs"
                                        :class="currentlyPlayingId === v.id ? 'bg-emerald-600 text-white scale-110 ring-2 ring-emerald-300 animate-pulse' : 'bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-600'"
                                        :title="isJapanese ? '音声を再生' : 'Putar Pelafalan Suara'"
                                    >
                                        <Volume2 class="w-4 h-4" />
                                    </button>
                                    <span v-else class="text-slate-300 text-xs">-</span>
                                </td>

                                <td class="py-3.5 px-4 sm:pr-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Link 
                                            :href="route('sensei.vocabularies.edit', v.id)"
                                            class="p-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-600 transition-colors cursor-pointer inline-flex items-center justify-center"
                                            :title="isJapanese ? '単語を編集' : 'Edit Kosakata'"
                                        >
                                            <Edit class="w-3.5 h-3.5" />
                                        </Link>
                                        <button 
                                            type="button" 
                                            @click="deleteVocab(v)"
                                            class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 transition-colors cursor-pointer"
                                            :title="isJapanese ? '単語を削除' : 'Hapus Kosakata'"
                                        >
                                            <Trash2 class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="vocabularies.data.length === 0">
                                <td colspan="8" class="py-12 text-center text-slate-400 space-y-2">
                                    <Layers class="w-10 h-10 text-slate-300 mx-auto" />
                                    <p class="font-bold text-sm text-slate-600">{{ isJapanese ? '該当する単語データが見つかりません。' : 'Tidak ada kosakata yang cocok dengan filter.' }}</p>
                                    <p class="text-xs text-slate-400">{{ isJapanese ? '検索条件を変更するか、新しい単語を登録してください。' : 'Silakan ubah filter pencarian atau tambahkan kosakata baru.' }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div v-if="vocabularies.links && vocabularies.links.length > 3" class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <span class="text-slate-500 font-jp">
                        {{ isJapanese ? `全 ${vocabularies.total} 件中 ${vocabularies.from || 0}〜${vocabularies.to || 0} 件を表示` : `Menampilkan ${vocabularies.from || 0} - ${vocabularies.to || 0} dari ${vocabularies.total} kosakata` }}
                    </span>
                    <div class="flex items-center gap-1 flex-wrap justify-center">
                        <Link 
                            v-for="(link, i) in vocabularies.links" 
                            :key="i"
                            :href="link.url || '#'"
                            class="px-3 py-1.5 rounded-xl font-bold transition-all text-xs"
                            :class="[
                                link.active ? 'bg-japan-red text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100',
                                !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : 'cursor-pointer'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>

            <!-- ================= VIEW MODE 2: FLASHCARD GRID ================= -->
            <div v-else class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div 
                        v-for="v in vocabularies.data" 
                        :key="v.id"
                        class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between space-y-4 group relative overflow-hidden"
                    >
                        <!-- Top Card Header: Kategori & Jenis Kata -->
                        <div class="flex items-start justify-between gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-[10px]">
                                {{ v.category || 'Umum' }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border font-jp" :class="getWordTypeBadgeClass(v.word_type)">
                                {{ formatWordType(v.word_type) }}
                            </span>
                        </div>

                        <!-- Main Word Display -->
                        <div class="text-center py-2 space-y-1">
                            <div v-if="v.kanji" class="text-2xl sm:text-3xl font-black text-slate-900 font-jp tracking-tight">
                                {{ v.kanji }}
                            </div>
                            <div class="text-base sm:text-lg font-bold text-rose-600 font-jp">
                                {{ v.hiragana }}
                            </div>
                            <div v-if="v.romaji" class="text-xs text-slate-500 font-mono">
                                {{ v.romaji }}
                            </div>
                            <div class="text-xs sm:text-sm font-black text-slate-800 pt-2 border-t border-slate-100">
                                {{ v.meaning_id }}
                            </div>
                        </div>

                        <!-- Example Sentence -->
                        <div v-if="v.example_sentence_jp" class="bg-slate-50 p-2.5 rounded-xl text-[11px] space-y-0.5">
                            <p class="font-jp font-semibold text-slate-800 line-clamp-2">
                                {{ v.example_sentence_jp }}
                            </p>
                            <p v-if="v.example_sentence_id" class="text-[10px] text-slate-500 italic line-clamp-2">
                                {{ v.example_sentence_id }}
                            </p>
                        </div>

                        <!-- Card Bottom Actions -->
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <button 
                                v-if="v.audio_file"
                                type="button" 
                                @click="playAudio(v.audio_file, v.id)"
                                class="px-2.5 py-1.5 rounded-xl transition-all flex items-center gap-1.5 text-[11px] font-bold cursor-pointer"
                                :class="currentlyPlayingId === v.id ? 'bg-emerald-600 text-white scale-105 ring-2 ring-emerald-300 animate-pulse' : 'bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-600'"
                            >
                                <Volume2 class="w-3.5 h-3.5" />
                                <span>Audio</span>
                            </button>
                            <span v-else class="text-[10px] text-slate-300">No Audio</span>

                            <div class="flex items-center gap-1">
                                <Link 
                                    :href="route('sensei.vocabularies.edit', v.id)" 
                                    class="p-1.5 rounded-lg text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors cursor-pointer inline-flex items-center justify-center"
                                    :title="isJapanese ? '単語を編集' : 'Edit Kosakata'"
                                >
                                    <Edit class="w-3.5 h-3.5" />
                                </Link>
                                <button @click="deleteVocab(v)" class="p-1.5 rounded-lg text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-colors cursor-pointer">
                                    <Trash2 class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid Pagination -->
                <div v-if="vocabularies.links && vocabularies.links.length > 3" class="flex items-center justify-center gap-1 py-4 flex-wrap">
                    <Link 
                        v-for="(link, i) in vocabularies.links" 
                        :key="i"
                        :href="link.url || '#'"
                        class="px-3.5 py-2 rounded-xl font-bold text-xs transition-all"
                        :class="[
                            link.active ? 'bg-japan-red text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50',
                            !link.url ? 'opacity-40 cursor-not-allowed pointer-events-none' : 'cursor-pointer'
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { notifySuccess, confirmDialog } from '@/Utils/alert';
import { useLang } from '@/Composables/useLang';
import { 
    Layers, 
    PlusCircle, 
    Search, 
    Volume2, 
    Sparkles, 
    List, 
    LayoutGrid, 
    RotateCcw, 
    Edit, 
    Trash2, 
    GraduationCap,
    SlidersHorizontal
} from 'lucide-vue-next';

const { isJapanese } = useLang();

const props = defineProps({
    vocabularies: Object,
    chapters: Array,
    courses: Array,
    levels: Object,
    categories: Array,
    wordTypes: Array,
    stats: Object,
    filters: Object,
});

const viewMode = ref('table');
const showMobileFilters = ref(false);

// Filter State
const searchQuery = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || '');
const selectedWordType = ref(props.filters?.word_type || '');
const selectedAudio = ref(props.filters?.has_audio || '');

const hasActiveFilter = computed(() => {
    return Boolean(searchQuery.value || selectedCategory.value || selectedWordType.value || selectedAudio.value);
});

const activeFilterCount = computed(() => {
    let count = 0;
    if (selectedCategory.value) count++;
    if (selectedWordType.value) count++;
    if (selectedAudio.value) count++;
    return count;
});

const applyFilter = () => {
    router.get(route('sensei.vocabularies.index'), {
        search: searchQuery.value || undefined,
        category: selectedCategory.value || undefined,
        word_type: selectedWordType.value || undefined,
        has_audio: selectedAudio.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilter = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    selectedWordType.value = '';
    selectedAudio.value = '';
    router.get(route('sensei.vocabularies.index'));
};

// Badge helper
const getLevelBadgeClass = (level) => {
    if (level === 'N5') return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
    if (level === 'N4') return 'bg-blue-100 text-blue-800 border border-blue-300';
    if (level === 'N3') return 'bg-indigo-100 text-indigo-800 border border-indigo-300';
    if (level && level.startsWith('SSW')) return 'bg-amber-100 text-amber-800 border border-amber-300';
    return 'bg-slate-100 text-slate-700 border border-slate-300';
};

const getWordTypeBadgeClass = (type) => {
    if (!type) return 'bg-slate-100 text-slate-700 border-slate-200';
    if (type.includes('Kata Kerja')) return 'bg-amber-50 text-amber-800 border-amber-200';
    if (type.includes('Kata Benda')) return 'bg-blue-50 text-blue-800 border-blue-200';
    if (type.includes('Kata Sifat')) return 'bg-emerald-50 text-emerald-800 border-emerald-200';
    return 'bg-purple-50 text-purple-800 border-purple-200';
};

// Word Type Translator for authentic Japanese terminology
const formatWordType = (type) => {
    if (!type) return '-';
    if (!isJapanese.value) return type;
    const map = {
        'Kata Benda': '名詞',
        'Kata Kerja Golongan I': '動詞 (Iグループ)',
        'Kata Kerja Golongan II': '動詞 (IIグループ)',
        'Kata Kerja Golongan III': '動詞 (IIIグループ)',
        'Kata Sifat-i': 'い形容詞',
        'Kata Sifat-na': 'な形容詞',
        'Kata Keterangan': '副詞',
        'Kata Sambung': '接続詞',
        'Kata Posisi': '位置詞・名詞',
        'Ungkapan / Salam': '挨拶・慣用表現',
    };
    return map[type] || type;
};

// Audio Player with visual playing indicator
const currentlyPlayingId = ref(null);
let currentAudioInstance = null;

const playAudio = (url, id = null) => {
    if (!url) return;
    if (currentAudioInstance) {
        currentAudioInstance.pause();
    }
    currentlyPlayingId.value = id;
    currentAudioInstance = new Audio(url);
    currentAudioInstance.play().catch(() => {
        currentlyPlayingId.value = null;
    });
    currentAudioInstance.onended = () => {
        currentlyPlayingId.value = null;
    };
};

const deleteVocab = (v) => {
    confirmDialog(
        isJapanese.value ? `単語「${v.hiragana}」を削除しますか？` : `Hapus Kosakata 「${v.hiragana}」?`,
        isJapanese.value ? 'この操作は取り消せません。' : `Kosakata "${v.hiragana} (${v.meaning_id})" akan dihapus dari database.`
    ).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('sensei.vocabularies.destroy', v.id), {
                onSuccess: () => {
                    notifySuccess(
                        isJapanese.value ? '削除完了' : 'Terhapus', 
                        isJapanese.value ? '単語を削除しました。' : 'Kosakata berhasil dihapus.'
                    );
                },
            });
        }
    });
};
</script>
