<template>
    <Head :title="isJapanese ? '漢字マスター管理 - Sensei Hub' : 'Kelola Kanji - Sensei Hub'" />

    <AuthenticatedLayout>
        <div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- ================= TOP PAGE HEADER ================= -->
            <div class="relative overflow-hidden bg-gradient-to-r from-indigo-50 via-white to-purple-50/40 p-6 sm:p-8 rounded-3xl border border-indigo-100/80 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div class="relative z-1 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-indigo-600 text-white shadow-xs font-mono">
                            <Languages class="w-3.5 h-3.5" />
                            <span>{{ isJapanese ? '漢字バンク' : 'Kanji Master Hub' }}</span>
                        </span>
                        <span v-if="selectedTopic" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800">
                            📁 {{ selectedTopic.title }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2 font-jp">
                        <span>{{ selectedTopic ? (isJapanese ? 'トピック別漢字一覧' : `Topik Kanji: ${selectedTopic.title}`) : (isJapanese ? '漢字トピック一覧（テーブル）' : 'Kelompok Topik Kanji') }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-2xl leading-relaxed">
                        {{ selectedTopic 
                            ? `Daftar huruf kanji di dalam topik "${selectedTopic.title}". Kelola Onyomi, Kunyomi, goresan, dan arti kata.`
                            : 'Kelola pembelajaran kanji terstruktur dalam model tabel ringkas: Level Belajar → Kelompok Topik Kanji → Karakter Huruf.' 
                        }}
                    </p>
                </div>

                <!-- SINGLE ACTION BUTTON IN HEADER (NO DUPLICATE BUTTONS) -->
                <div class="relative z-1 flex items-center gap-2.5 self-stretch sm:self-auto shrink-0">
                    <!-- If Inside a Topic Detail: Button to Add Kanji to this Topic -->
                    <Link 
                        v-if="selectedTopic"
                        :href="route('sensei.kanjis.create', { topic_id: selectedTopic.id, level: selectedTopic.level })"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/25 hover:shadow-japan-red/40 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer group"
                    >
                        <PlusCircle class="w-4 h-4 group-hover:rotate-90 transition-transform duration-200" />
                        <span>{{ isJapanese ? '+ このトピックに漢字追加' : '+ Tambah Kanji ke Topik Ini' }}</span>
                    </Link>

                    <!-- If in Topic Overview: Button to Create New Topic -->
                    <button 
                        v-else
                        type="button"
                        @click="openCreateTopicModal"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/25 hover:shadow-japan-red/40 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer group"
                    >
                        <PlusCircle class="w-4 h-4 group-hover:rotate-90 transition-transform duration-200" />
                        <span>{{ isJapanese ? '+ 新規トピック作成' : '+ Buat Topik Kanji' }}</span>
                    </button>
                </div>
            </div>

            <!-- ================= STATS SUMMARY CARDS (SHOWN ON OVERVIEW) ================= -->
            <div v-if="!selectedTopic" class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex items-center justify-between">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider">
                            {{ isJapanese ? '登録漢字総数' : 'Total Huruf Kanji' }}
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
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider">
                            {{ isJapanese ? 'トピックグループ数' : 'Wadah Topik Kanji' }}
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
                            {{ isJapanese ? 'N5基礎漢字' : 'Kanji Level N5' }}
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
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 block uppercase tracking-wider">
                            {{ isJapanese ? 'N4実務漢字' : 'Kanji Level N4' }}
                        </span>
                        <span class="text-lg sm:text-2xl font-black text-purple-600 mt-0.5 block font-mono">
                            {{ stats.total_n4 ?? 0 }}
                        </span>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                        <Bookmark class="w-4 h-4 sm:w-5 sm:h-5" />
                    </div>
                </div>
            </div>

            <!-- ================= VIEW 1: TABEL KELOMPOK TOPIK KANJI (MODEL TABEL) ================= -->
            <div v-if="!selectedTopic" class="space-y-4">
                <!-- Bar Filter & Pencarian Topik Kanji -->
                <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        <FolderTree class="w-4 h-4 text-indigo-600 shrink-0" />
                        <span class="text-xs font-bold text-slate-700 font-jp">
                            {{ selectedLevel === 'all' ? 'Semua Topik Kanji Terdaftar' : `Topik Kanji Level ${selectedLevel}` }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-mono font-bold">
                            {{ displayedTopics.length }} Topik
                        </span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <!-- Search Box Topik Kanji -->
                        <div class="relative w-full sm:w-64">
                            <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                            <input 
                                type="text"
                                v-model="topicSearchQuery"
                                placeholder="Cari nama topik kanji..."
                                class="w-full pl-8.5 pr-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>

                        <!-- Level Selector -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-slate-400 uppercase font-mono hidden sm:inline">Level:</span>
                            <select 
                                v-model="selectedLevel"
                                @change="onLevelChange"
                                class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer"
                            >
                                <option value="all">Semua Level</option>
                                <option v-for="lvl in languageLevels" :key="lvl.code" :value="lvl.code">
                                    {{ lvl.code }} - {{ lvl.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- TABEL UTAMA KELOMPOK TOPIK KANJI -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="py-3 px-4 w-12 text-center">#</th>
                                    <th class="py-3 px-4 w-24">Level</th>
                                    <th class="py-3 px-4 min-w-[200px]">Nama Topik & Deskripsi</th>
                                    <th class="py-3 px-4 min-w-[260px]">Karakter Kanji di Topik</th>
                                    <th class="py-3 px-4 w-28 text-center">Jumlah Kanji</th>
                                    <th class="py-3 px-4 w-48 text-right">Aksi Manajemen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr 
                                    v-for="(topic, idx) in displayedTopics" 
                                    :key="topic.id"
                                    class="hover:bg-indigo-50/30 transition-colors group"
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
                                            <Folder class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" />
                                            <div>
                                                <span class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors block">
                                                    {{ topic.title }}
                                                </span>
                                                <span class="text-[11px] text-slate-500 line-clamp-1 block mt-0.5">
                                                    {{ topic.description || 'Tidak ada catatan tambahan.' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Sample Kanji Characters Preview Chips -->
                                    <td class="py-3 px-4">
                                        <div v-if="topic.kanjis && topic.kanjis.length > 0" class="flex flex-wrap items-center gap-1.5">
                                            <span 
                                                v-for="k in topic.kanjis.slice(0, 8)" 
                                                :key="k.id"
                                                class="w-7 h-7 flex items-center justify-center bg-indigo-50 border border-indigo-100 rounded-lg text-sm font-jp font-black text-indigo-950 shadow-2xs"
                                            >
                                                {{ k.kanji }}
                                            </span>
                                            <span v-if="topic.kanjis.length > 8" class="text-[10px] text-slate-400 font-bold font-mono pl-1">
                                                +{{ topic.kanjis.length - 8 }}
                                            </span>
                                        </div>
                                        <span v-else class="text-[11px] text-slate-400 italic">
                                            Belum ada kanji
                                        </span>
                                    </td>

                                    <!-- Kanji Count Badge -->
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold font-mono inline-block">
                                            {{ topic.kanjis_count ?? (topic.kanjis?.length ?? 0) }} Kanji
                                        </span>
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Open Topic Detail -->
                                            <button 
                                                type="button"
                                                @click="openTopicDetail(topic)"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-[11px] font-bold transition-all inline-flex items-center gap-1 cursor-pointer shadow-2xs"
                                                title="Buka daftar kanji di topik ini"
                                            >
                                                <Eye class="w-3.5 h-3.5" />
                                                <span>Buka</span>
                                            </button>

                                            <!-- Add Kanji to this Topic -->
                                            <Link 
                                                :href="route('sensei.kanjis.create', { topic_id: topic.id, level: topic.level })"
                                                class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition-colors"
                                                title="Tambah kanji ke topik ini"
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
                                        <span class="text-xs font-bold block text-slate-600">Tidak ada topik kanji yang cocok dengan pencarian / level ini</span>
                                        <span class="text-[11px] block mt-1">Gunakan tombol "+ Buat Topik Kanji" untuk menambahkan kelompok materi.</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================= VIEW 2: DETAIL TOPIK KANJI (TABEL KANJI) ================= -->
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
                                {{ kanjis.total ?? kanjis.data?.length ?? 0 }} Kanji
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
                            placeholder="Cari kanji / onyomi / arti..."
                            class="w-full pl-8.5 pr-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>
                </div>

                <!-- Empty State Inside Topic -->
                <div v-if="!kanjis.data || kanjis.data.length === 0" class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs space-y-4 max-w-lg mx-auto">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mx-auto text-2xl">
                        字
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Belum Ada Kanji di Topik Ini</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Topik "{{ selectedTopic.title }}" belum memiliki kanji terdaftar.
                        </p>
                    </div>
                    <Link 
                        :href="route('sensei.kanjis.create', { topic_id: selectedTopic.id, level: selectedTopic.level })"
                        class="px-5 py-2.5 rounded-xl bg-japan-red text-white text-xs font-bold shadow-md shadow-japan-red/25 hover:bg-red-700 transition-all cursor-pointer inline-flex items-center gap-2"
                    >
                        <PlusCircle class="w-4 h-4" />
                        <span>Tambah Kanji Pertama</span>
                    </Link>
                </div>

                <!-- Table View Mode for Kanji Inside Topic -->
                <div v-else class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="py-3 px-4 w-12 text-center">#</th>
                                    <th class="py-3 px-4 w-20 text-center">Huruf Kanji</th>
                                    <th class="py-3 px-4 min-w-[150px]">Onyomi (音読み)</th>
                                    <th class="py-3 px-4 min-w-[150px]">Kunyomi (訓読み)</th>
                                    <th class="py-3 px-4 min-w-[200px]">Arti Bahasa Indonesia</th>
                                    <th class="py-3 px-4 w-24 text-center">Goresan</th>
                                    <th class="py-3 px-4 w-24 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr 
                                    v-for="(k, index) in kanjis.data" 
                                    :key="k.id"
                                    class="hover:bg-indigo-50/20 transition-colors"
                                >
                                    <!-- Index -->
                                    <td class="py-3 px-4 text-center font-mono font-bold text-slate-400">
                                        {{ index + 1 }}
                                    </td>

                                    <!-- Large Kanji Character -->
                                    <td class="py-3 px-4 text-center">
                                        <div class="w-10 h-10 mx-auto rounded-xl bg-indigo-50/70 border border-indigo-100 flex items-center justify-center font-jp text-2xl font-black text-slate-900 shadow-2xs">
                                            {{ k.kanji }}
                                        </div>
                                    </td>

                                    <!-- Onyomi -->
                                    <td class="py-3 px-4 font-jp">
                                        <span class="text-xs font-semibold text-indigo-700 block">{{ k.onyomi || '-' }}</span>
                                    </td>

                                    <!-- Kunyomi -->
                                    <td class="py-3 px-4 font-jp">
                                        <span class="text-xs font-semibold text-rose-700 block">{{ k.kunyomi || '-' }}</span>
                                    </td>

                                    <!-- Meaning -->
                                    <td class="py-3 px-4 text-slate-800 font-semibold max-w-xs">
                                        {{ k.meaning_id || k.meaning }}
                                    </td>

                                    <!-- Stroke Count -->
                                    <td class="py-3 px-4 text-center font-mono font-medium text-slate-600">
                                        {{ k.stroke_count ? `${k.stroke_count} 画` : '-' }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Link 
                                                :href="route('sensei.kanjis.edit', k.id)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                                title="Edit Kanji"
                                            >
                                                <Edit3 class="w-3.5 h-3.5" />
                                            </Link>
                                            <button 
                                                type="button"
                                                @click="deleteKanji(k)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                title="Hapus Kanji"
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

                <!-- Pagination for Kanji -->
                <div v-if="kanjis.links && kanjis.links.length > 3" class="flex justify-center items-center gap-1.5 pt-4">
                    <template v-for="(link, i) in kanjis.links" :key="i">
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

            <!-- ================= MODAL TOPIC KANJI ================= -->
            <div v-if="showTopicModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
                <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95 duration-150">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                                <FolderTree class="w-4 h-4" />
                            </span>
                            <h3 class="text-sm font-extrabold text-slate-900 font-jp">
                                {{ editingTopicId ? (isJapanese ? '漢字トピックの編集' : 'Edit Topik Kanji') : (isJapanese ? '新規漢字トピック作成' : 'Buat Topik Kanji Baru') }}
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
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            >
                                <option v-for="lvl in languageLevels" :key="lvl.code" :value="lvl.code">
                                    {{ lvl.code }} - {{ lvl.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Nama / Judul Topik -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Topik Kanji *
                            </label>
                            <input 
                                type="text" 
                                v-model="topicForm.title" 
                                required
                                placeholder="Contoh: Hari & Elemen Alam, Angka Dasar, dll."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
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
                                placeholder="Tuliskan keterangan mengenai cakupan kanji di topik ini..."
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
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
    Languages, 
    FolderTree,
    Folder,
    Sparkles, 
    Bookmark,
    PlusCircle, 
    Search, 
    RotateCcw, 
    Eye,
    Edit3,
    Trash2
} from 'lucide-vue-next';

const { isJapanese } = useLang();

const props = defineProps({
    kanjis: Object,
    topics: {
        type: Array,
        default: () => [],
    },
    languageLevels: {
        type: Array,
        default: () => [],
    },
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
    router.get(route('sensei.kanjis.index'), {
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
        topicForm.put(route('sensei.kanjis.topics.update', editingTopicId.value), {
            onSuccess: () => {
                notifySuccess('Berhasil', 'Topik kanji berhasil diperbarui');
                closeTopicModal();
            },
        });
    } else {
        topicForm.post(route('sensei.kanjis.topics.store'), {
            onSuccess: () => {
                notifySuccess('Berhasil', 'Topik kanji baru berhasil dibuat');
                closeTopicModal();
            },
        });
    }
};

const deleteTopic = async (topic) => {
    const confirmed = await confirmDialog(
        'Hapus Topik Kanji Ini?',
        `Topik "${topic.title}" akan dihapus. Karakter kanji di dalamnya tidak akan terhapus dari sistem.`
    );
    if (!confirmed) return;

    router.delete(route('sensei.kanjis.topics.destroy', topic.id), {
        onSuccess: () => {
            notifySuccess('Berhasil', 'Topik kanji berhasil dihapus');
            if (String(selectedTopicId.value) === String(topic.id)) {
                selectedTopicId.value = '';
            }
        },
    });
};

// Delete Single Kanji
const deleteKanji = async (k) => {
    const confirmed = await confirmDialog(
        'Hapus Huruf Kanji Ini?',
        `Kanji "${k.kanji} (${k.meaning_id || k.meaning})" akan dihapus permanen.`
    );
    if (!confirmed) return;

    router.delete(route('sensei.kanjis.destroy', k.id), {
        preserveScroll: true,
        onSuccess: () => {
            notifySuccess('Berhasil', 'Huruf kanji berhasil dihapus');
        },
    });
};

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
