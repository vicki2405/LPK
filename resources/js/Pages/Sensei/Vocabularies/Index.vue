<template>
    <Head :title="isJapanese ? '単語・語彙管理 - Sensei Hub' : 'Kelola Kosakata (Kotoba) - Sensei Hub'" />

    <AuthenticatedLayout>
        <div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- ================= TOP PAGE HEADER ================= -->
            <div class="relative overflow-hidden bg-gradient-to-r from-rose-50 via-white to-amber-50/40 p-6 sm:p-8 rounded-3xl border border-rose-100/80 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div class="relative z-1 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-xs font-mono">
                            <Layers class="w-3.5 h-3.5" />
                            <span>{{ isJapanese ? '語彙マスター' : 'Kotoba Hub' }}</span>
                        </span>
                        <span v-if="selectedTopic" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                            📁 {{ selectedTopic.title }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2 font-jp">
                        <span>{{ selectedTopic ? (isJapanese ? 'トピック別単語一覧' : `Topik: ${selectedTopic.title}`) : (isJapanese ? '単語トピック一覧（テーブル）' : 'Kelompok Topik Kosakata') }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-2xl leading-relaxed">
                        {{ selectedTopic 
                            ? `Daftar kata terstruktur di dalam topik "${selectedTopic.title}". Tambah, edit arti, furigana, dan generate audio native.`
                            : 'Kelola perbendaharaan kata terstruktur dalam model tabel ringkas: Level Belajar → Kelompok Topik → Kosakata.' 
                        }}
                    </p>
                </div>

                <!-- SINGLE ACTION BUTTON IN HEADER (NO DUPLICATE BUTTONS) -->
                <div class="relative z-1 flex items-center gap-2.5 self-stretch sm:self-auto shrink-0">
                    <!-- If Inside a Topic Detail: Button to Add Word to this Topic -->
                    <Link 
                        v-if="selectedTopic"
                        :href="route('sensei.vocabularies.create', { topic_id: selectedTopic.id, level: selectedTopic.level })"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/25 hover:shadow-japan-red/40 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer group"
                    >
                        <PlusCircle class="w-4 h-4 group-hover:rotate-90 transition-transform duration-200" />
                        <span>{{ isJapanese ? '+ このトピックに単語追加' : '+ Tambah Kosakata ke Topik Ini' }}</span>
                    </Link>

                    <!-- If in Topic Overview: Button to Create New Topic -->
                    <button 
                        v-else
                        type="button"
                        @click="openCreateTopicModal"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/25 hover:shadow-japan-red/40 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer group"
                    >
                        <PlusCircle class="w-4 h-4 group-hover:rotate-90 transition-transform duration-200" />
                        <span>{{ isJapanese ? '+ 新規トピック作成' : '+ Buat Topik Baru' }}</span>
                    </button>
                </div>
            </div>

            <!-- ================= STATS SUMMARY CARDS (SHOWN ON OVERVIEW) ================= -->
            <div v-if="!selectedTopic" class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
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
                            {{ isJapanese ? 'トピックグループ数' : 'Wadah Topik' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-blue-600 mt-0.5 block font-mono">
                            {{ stats.total_topics ?? topics.length }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <FolderTree class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>

                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider">
                            {{ isJapanese ? '音声付き単語' : 'Dengan Audio Native' }}
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

            <!-- ================= VIEW 1: TABEL KELOMPOK TOPIK (MODEL TABEL) ================= -->
            <div v-if="!selectedTopic" class="space-y-4">
                <!-- Bar Filter & Pencarian Topik -->
                <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        <FolderTree class="w-4 h-4 text-rose-600 shrink-0" />
                        <span class="text-xs font-bold text-slate-700 font-jp">
                            {{ selectedLevel === 'all' ? 'Semua Topik Terdaftar' : `Topik Level ${selectedLevel}` }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-mono font-bold">
                            {{ displayedTopics.length }} Topik
                        </span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <!-- Search Box Topik -->
                        <div class="relative w-full sm:w-64">
                            <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                            <input 
                                type="text"
                                v-model="topicSearchQuery"
                                placeholder="Cari nama topik..."
                                class="w-full pl-8.5 pr-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none"
                            />
                        </div>

                        <!-- Level Selector -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-slate-400 uppercase font-mono hidden sm:inline">Level:</span>
                            <select 
                                v-model="selectedLevel"
                                @change="onLevelChange"
                                class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none cursor-pointer"
                            >
                                <option value="all">Semua Level</option>
                                <option v-for="(label, code) in levels" :key="code" :value="code">
                                    {{ code }} - {{ label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- TABEL UTAMA KELOMPOK TOPIK -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="py-3 px-4 w-12 text-center">#</th>
                                    <th class="py-3 px-4 w-24">Level</th>
                                    <th class="py-3 px-4 min-w-[200px]">Nama Topik & Deskripsi</th>
                                    <th class="py-3 px-4 min-w-[260px]">Cuplikan Kosakata</th>
                                    <th class="py-3 px-4 w-28 text-center">Jumlah Kata</th>
                                    <th class="py-3 px-4 w-48 text-right">Aksi Manajemen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr 
                                    v-for="(topic, idx) in displayedTopics" 
                                    :key="topic.id"
                                    class="hover:bg-rose-50/30 transition-colors group"
                                >
                                    <!-- Index Number -->
                                    <td class="py-3 px-4 text-center font-mono font-bold text-slate-400">
                                        {{ idx + 1 }}
                                    </td>

                                    <!-- Level Badge -->
                                    <td class="py-3 px-4">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase font-mono tracking-wider inline-block" :class="getLevelBadgeClass(topic.level)">
                                            {{ topic.level }}
                                        </span>
                                    </td>

                                    <!-- Topic Title & Description -->
                                    <td class="py-3 px-4">
                                        <div class="flex items-start gap-2">
                                            <Folder class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                                            <div>
                                                <span class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-japan-red transition-colors block">
                                                    {{ topic.title }}
                                                </span>
                                                <span class="text-[11px] text-slate-500 line-clamp-1 block mt-0.5">
                                                    {{ topic.description || 'Tidak ada catatan tambahan.' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Sample Words Preview Chips -->
                                    <td class="py-3 px-4">
                                        <div v-if="topic.vocabularies && topic.vocabularies.length > 0" class="flex flex-wrap items-center gap-1.5">
                                            <span 
                                                v-for="v in topic.vocabularies.slice(0, 4)" 
                                                :key="v.id"
                                                class="px-2 py-0.5 bg-slate-50 border border-slate-200/80 rounded-md text-[11px] font-jp font-semibold text-slate-700"
                                            >
                                                {{ v.kanji || v.hiragana }}
                                            </span>
                                            <span v-if="topic.vocabularies.length > 4" class="text-[10px] text-slate-400 font-bold font-mono">
                                                +{{ topic.vocabularies.length - 4 }}
                                            </span>
                                        </div>
                                        <span v-else class="text-[11px] text-slate-400 italic">
                                            Belum ada kata
                                        </span>
                                    </td>

                                    <!-- Word Count Badge -->
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold font-mono inline-block">
                                            {{ topic.vocabularies_count ?? (topic.vocabularies?.length ?? 0) }} Kata
                                        </span>
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Open Topic Detail -->
                                            <button 
                                                type="button"
                                                @click="openTopicDetail(topic)"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-japan-red text-white text-[11px] font-bold transition-all inline-flex items-center gap-1 cursor-pointer shadow-2xs"
                                                title="Buka daftar kosakata di topik ini"
                                            >
                                                <Eye class="w-3.5 h-3.5" />
                                                <span>Buka</span>
                                            </button>

                                            <!-- Add Word to this Topic -->
                                            <Link 
                                                :href="route('sensei.vocabularies.create', { topic_id: topic.id, level: topic.level })"
                                                class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition-colors"
                                                title="Tambah kata ke topik ini"
                                            >
                                                <PlusCircle class="w-3.5 h-3.5" />
                                            </Link>

                                            <!-- Edit Topic -->
                                            <button 
                                                type="button"
                                                @click="openEditTopicModal(topic)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                                                title="Edit nama/deskripsi topik"
                                            >
                                                <Edit3 class="w-3.5 h-3.5" />
                                            </button>

                                            <!-- Delete Topic -->
                                            <button 
                                                type="button"
                                                @click="deleteTopic(topic)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                title="Hapus topik ini"
                                            >
                                                <Trash2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="displayedTopics.length === 0">
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <FolderTree class="w-8 h-8 mx-auto text-slate-300 mb-2" />
                                        <span class="text-xs font-bold block text-slate-600">Tidak ada topik yang cocok dengan pencarian / level ini</span>
                                        <span class="text-[11px] block mt-1">Gunakan tombol "+ Buat Topik Baru" untuk menambahkan kelompok materi.</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================= VIEW 2: DETAIL TOPIK TERTENTU (TABEL KOSAKATA) ================= -->
            <div v-else class="space-y-4">
                <!-- Navigation Bar: Back to Topics & Active Topic Info -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <button 
                            type="button"
                            @click="closeTopicDetail"
                            class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-xs"
                        >
                            <RotateCcw class="w-3.5 h-3.5 text-slate-600" />
                            <span>← Kembali ke Semua Topik</span>
                        </button>
                        <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase font-mono tracking-wider" :class="getLevelBadgeClass(selectedTopic.level)">
                                {{ selectedTopic.level }}
                            </span>
                            <span class="text-xs sm:text-sm font-black text-slate-900 font-jp">
                                {{ selectedTopic.title }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold font-mono">
                                {{ vocabularies.total ?? vocabularies.data?.length ?? 0 }} Kata
                            </span>
                        </div>
                    </div>

                    <!-- Search Filter inside Topic -->
                    <div class="relative w-full sm:w-64">
                        <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                        <input 
                            type="text"
                            v-model="searchQuery"
                            @input="onSearchInput"
                            placeholder="Cari kata di topik ini..."
                            class="w-full pl-8.5 pr-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none"
                        />
                    </div>
                </div>

                <!-- Empty State Inside Topic -->
                <div v-if="!vocabularies.data || vocabularies.data.length === 0" class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs space-y-4 max-w-lg mx-auto">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto text-2xl">
                        📝
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Belum Ada Kata di Topik Ini</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Topik "{{ selectedTopic.title }}" masih kosong. Tambahkan kosakata sekarang untuk topik ini.
                        </p>
                    </div>
                    <Link 
                        :href="route('sensei.vocabularies.create', { topic_id: selectedTopic.id, level: selectedTopic.level })"
                        class="px-5 py-2.5 rounded-xl bg-japan-red text-white text-xs font-bold shadow-md shadow-japan-red/25 hover:bg-red-700 transition-all cursor-pointer inline-flex items-center gap-2"
                    >
                        <PlusCircle class="w-4 h-4" />
                        <span>Tambah Kata Pertama</span>
                    </Link>
                </div>

                <!-- Table View Mode for Words Inside Topic -->
                <div v-else class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="py-3 px-4 w-12 text-center">#</th>
                                    <th class="py-3 px-4 min-w-[160px]">Kosakata (Kanji/Hiragana)</th>
                                    <th class="py-3 px-4 min-w-[120px]">Romaji</th>
                                    <th class="py-3 px-4 min-w-[200px]">Arti Bahasa Indonesia</th>
                                    <th class="py-3 px-4 w-32">Jenis Kata</th>
                                    <th class="py-3 px-4 w-16 text-center">Audio</th>
                                    <th class="py-3 px-4 w-28 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr 
                                    v-for="(v, index) in vocabularies.data" 
                                    :key="v.id"
                                    class="hover:bg-rose-50/20 transition-colors"
                                >
                                    <!-- Index -->
                                    <td class="py-3 px-4 text-center font-mono font-bold text-slate-400">
                                        {{ index + 1 }}
                                    </td>

                                    <!-- Word Kanji & Hiragana -->
                                    <td class="py-3 px-4">
                                        <div class="font-jp">
                                            <span v-if="v.kanji" class="text-sm font-black text-slate-900 block">{{ v.kanji }}</span>
                                            <span class="text-xs text-rose-600 font-bold block">{{ v.hiragana }}</span>
                                        </div>
                                    </td>

                                    <!-- Romaji -->
                                    <td class="py-3 px-4 font-mono font-medium text-slate-600">
                                        {{ v.romaji || '-' }}
                                    </td>

                                    <!-- Meaning -->
                                    <td class="py-3 px-4 text-slate-800 font-semibold max-w-xs">
                                        {{ v.meaning_id }}
                                    </td>

                                    <!-- Word Type -->
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-mono text-[10px]">
                                            {{ v.word_type || 'Umum' }}
                                        </span>
                                    </td>

                                    <!-- Audio Control -->
                                    <td class="py-3 px-4 text-center">
                                        <button 
                                            type="button"
                                            @click="playAudio(v)"
                                            class="p-2 rounded-xl transition-all cursor-pointer inline-flex items-center justify-center"
                                            :class="playingAudioId === v.id ? 'bg-emerald-500 text-white animate-pulse' : (v.audio_file ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-slate-100 text-slate-400 hover:bg-slate-200')"
                                            :title="v.audio_file ? 'Dengarkan Audio Native' : 'Putar Audio Native TTS'"
                                        >
                                            <Volume2 class="w-3.5 h-3.5" />
                                        </button>
                                    </td>

                                    <!-- Actions (Edit & Delete) -->
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link 
                                                :href="route('sensei.vocabularies.edit', v.id)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                                title="Edit Kosakata"
                                            >
                                                <Edit3 class="w-3.5 h-3.5" />
                                            </Link>
                                            <button 
                                                type="button"
                                                @click="deleteVocabulary(v)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                title="Hapus Kosakata"
                                            >
                                                <Trash2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination for Words inside Topic -->
                <div v-if="vocabularies.links && vocabularies.links.length > 3" class="flex justify-center items-center gap-1.5 pt-4">
                    <template v-for="(link, i) in vocabularies.links" :key="i">
                        <Link 
                            v-if="link.url"
                            :href="link.url"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                            :class="link.active ? 'bg-slate-900 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'"
                            v-html="link.label"
                        />
                        <span 
                            v-else 
                            class="px-2.5 py-1 text-xs text-slate-400"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>

            <!-- ================= MODAL TOPIC (TAMBAH / EDIT TOPIK) ================= -->
            <div v-if="showTopicModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
                <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95 duration-150">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                                <FolderTree class="w-4 h-4" />
                            </span>
                            <h3 class="text-sm font-extrabold text-slate-900 font-jp">
                                {{ editingTopicId ? (isJapanese ? 'トピックの編集' : 'Edit Topik Kosakata') : (isJapanese ? '新規トピック作成' : 'Buat Topik Baru') }}
                            </h3>
                        </div>
                        <button 
                            type="button" 
                            @click="closeTopicModal"
                            class="text-slate-400 hover:text-slate-600 text-lg leading-none cursor-pointer"
                        >
                            &times;
                        </button>
                    </div>

                    <form @submit.prevent="submitTopic" class="space-y-4">
                        <!-- Level Selector -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Level Bahasa Jepang *
                            </label>
                            <select 
                                v-model="topicForm.level" 
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none"
                            >
                                <option v-for="(label, code) in levels" :key="code" :value="code">
                                    {{ code }} - {{ label }}
                                </option>
                            </select>
                        </div>

                        <!-- Nama / Judul Topik -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Topik / Kelompok Kosakata *
                            </label>
                            <input 
                                type="text" 
                                v-model="topicForm.title" 
                                required
                                placeholder="Contoh: Perkenalan Diri, Benda di Rumah, dll."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-rose-500 focus:outline-none"
                            />
                        </div>

                        <!-- Deskripsi Topik -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Deskripsi Topik (Opsional)
                            </label>
                            <textarea 
                                v-model="topicForm.description" 
                                rows="3"
                                placeholder="Tuliskan keterangan singkat mengenai cakupan kata di topik ini..."
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-rose-500 focus:outline-none"
                            ></textarea>
                        </div>

                        <!-- Modal Action Bar -->
                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                            <button 
                                type="button" 
                                @click="closeTopicModal"
                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                :disabled="topicForm.processing"
                                class="px-5 py-2 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/25 transition-all cursor-pointer disabled:opacity-50"
                            >
                                {{ topicForm.processing ? 'Menyimpan...' : (editingTopicId ? 'Simpan Perubahan' : 'Buat Topik') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { confirmDialog, notifySuccess } from '@/Utils/alert';
import { useLang } from '@/Composables/useLang';
import { 
    Layers, 
    FolderTree,
    Folder,
    Volume2, 
    Sparkles, 
    PlusCircle, 
    Search, 
    RotateCcw, 
    Eye,
    Edit3,
    Trash2
} from 'lucide-vue-next';

const { isJapanese } = useLang();

const props = defineProps({
    vocabularies: Object,
    topics: {
        type: Array,
        default: () => [],
    },
    chapters: Array,
    courses: Array,
    levels: {
        type: Object,
        default: () => ({
            'N5': 'JLPT N5 (Tingkat Dasar)',
            'N4': 'JLPT N4 (Standar Kerja)',
            'N3': 'JLPT N3 (Tingkat Menengah)'
        }),
    },
    categories: Array,
    wordTypes: Array,
    stats: Object,
    filters: Object,
});

// View States
const selectedLevel = ref(props.filters?.level || 'all');
const selectedTopicId = ref(props.filters?.topic_id || '');
const searchQuery = ref(props.filters?.search || '');
const topicSearchQuery = ref('');

// Selected Topic Object if active
const selectedTopic = computed(() => {
    if (!selectedTopicId.value || !props.topics) return null;
    return props.topics.find(t => String(t.id) === String(selectedTopicId.value)) || null;
});

// Topics filtered by selected level
const filteredTopics = computed(() => {
    if (!props.topics) return [];
    if (selectedLevel.value === 'all') return props.topics;
    return props.topics.filter(t => t.level === selectedLevel.value);
});

// Displayed topics with client-side instant search for high capacity (100+ topics)
const displayedTopics = computed(() => {
    let list = filteredTopics.value;
    if (topicSearchQuery.value && topicSearchQuery.value.trim()) {
        const q = topicSearchQuery.value.toLowerCase().trim();
        list = list.filter(t => 
            t.title.toLowerCase().includes(q) || 
            (t.description && t.description.toLowerCase().includes(q))
        );
    }
    return list;
});

const onLevelChange = () => {
    selectedTopicId.value = '';
    applyFilter();
};

const openTopicDetail = (topic) => {
    selectedTopicId.value = topic.id;
    selectedLevel.value = topic.level;
    searchQuery.value = '';
    applyFilter();
};

const closeTopicDetail = () => {
    selectedTopicId.value = '';
    searchQuery.value = '';
    applyFilter();
};

let searchDebounceTimer = null;
const onSearchInput = () => {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => {
        applyFilter();
    }, 350);
};

const applyFilter = () => {
    router.get(route('sensei.vocabularies.index'), {
        level: selectedLevel.value !== 'all' ? selectedLevel.value : undefined,
        topic_id: selectedTopicId.value || undefined,
        search: searchQuery.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Topic Modal State & Form
const showTopicModal = ref(false);
const editingTopicId = ref(null);

const topicForm = useForm({
    level: 'N5',
    title: '',
    description: '',
});

const openCreateTopicModal = () => {
    editingTopicId.value = null;
    topicForm.reset();
    topicForm.level = selectedLevel.value !== 'all' ? selectedLevel.value : 'N5';
    showTopicModal.value = true;
};

const openEditTopicModal = (topic) => {
    editingTopicId.value = topic.id;
    topicForm.level = topic.level;
    topicForm.title = topic.title;
    topicForm.description = topic.description || '';
    showTopicModal.value = true;
};

const closeTopicModal = () => {
    showTopicModal.value = false;
    editingTopicId.value = null;
};

const submitTopic = () => {
    if (editingTopicId.value) {
        topicForm.put(route('sensei.vocabularies.topics.update', editingTopicId.value), {
            onSuccess: () => {
                notifySuccess('Berhasil', 'Topik kosakata berhasil diperbarui');
                closeTopicModal();
            },
        });
    } else {
        topicForm.post(route('sensei.vocabularies.topics.store'), {
            onSuccess: () => {
                notifySuccess('Berhasil', 'Topik kosakata baru berhasil dibuat');
                closeTopicModal();
            },
        });
    }
};

const deleteTopic = async (topic) => {
    const confirmed = await confirmDialog(
        'Hapus Topik Ini?',
        `Topik "${topic.title}" akan dihapus. Kosakata yang ada di dalamnya tidak akan terhapus dari sistem.`
    );
    if (!confirmed) return;

    router.delete(route('sensei.vocabularies.topics.destroy', topic.id), {
        onSuccess: () => {
            notifySuccess('Berhasil', 'Topik kosakata berhasil dihapus');
            if (String(selectedTopicId.value) === String(topic.id)) {
                selectedTopicId.value = '';
            }
        },
    });
};

// Audio Native Player
const playingAudioId = ref(null);
let activeAudioElement = null;

const playAudio = (v) => {
    if (activeAudioElement) {
        activeAudioElement.pause();
        activeAudioElement = null;
    }

    if (playingAudioId.value === v.id) {
        playingAudioId.value = null;
        return;
    }

    const audioUrl = v.audio_file 
        ? `/storage/${v.audio_file}`
        : route('sensei.vocabularies.stream-audio', { text: v.kanji || v.hiragana });

    activeAudioElement = new Audio(audioUrl);
    playingAudioId.value = v.id;

    activeAudioElement.onended = () => {
        playingAudioId.value = null;
    };
    activeAudioElement.onerror = () => {
        playingAudioId.value = null;
        // Fallback Web Speech
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(v.kanji || v.hiragana);
            utterance.lang = 'ja-JP';
            window.speechSynthesis.speak(utterance);
        }
    };

    activeAudioElement.play().catch(() => {
        playingAudioId.value = null;
    });
};

// Delete Single Vocabulary
const deleteVocabulary = async (v) => {
    const confirmed = await confirmDialog(
        'Hapus Kosakata Ini?',
        `Kosakata "${v.kanji || v.hiragana} (${v.meaning_id})" akan dihapus permanen.`
    );
    if (!confirmed) return;

    router.delete(route('sensei.vocabularies.destroy', v.id), {
        preserveScroll: true,
        onSuccess: () => {
            notifySuccess('Berhasil', 'Kosakata berhasil dihapus');
        },
    });
};

// Helper badge class
const getLevelBadgeClass = (level) => {
    switch (level) {
        case 'N5': return 'bg-emerald-100 text-emerald-800';
        case 'N4': return 'bg-blue-100 text-blue-800';
        case 'N3': return 'bg-amber-100 text-amber-800';
        case 'N2': return 'bg-purple-100 text-purple-800';
        case 'N1': return 'bg-rose-100 text-rose-800';
        default: return 'bg-slate-100 text-slate-800';
    }
};
</script>
