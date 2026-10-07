<template>
    <Head :title="isJapanese ? '単語の編集 (Edit Kosakata) - 正夢' : 'Edit Kosakata - Kotoba Hub'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-16 max-w-7xl mx-auto">
            <!-- ================= TOP NAVIGATION & BREADCRUMBS ================= -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-400 font-jp">
                        <Link :href="route('sensei.vocabularies.index')" class="hover:text-japan-red transition-colors flex items-center gap-1">
                            <ArrowLeft class="w-3.5 h-3.5" />
                            <span>{{ isJapanese ? '語彙一覧に戻る' : 'Kembali ke Daftar Kosakata' }}</span>
                        </Link>
                        <span>/</span>
                        <span class="text-slate-700">{{ isJapanese ? '単語の編集' : 'Edit Kosakata' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-blue-500 text-white flex items-center justify-center font-jp text-sm font-bold shadow-xs">
                            改
                        </span>
                        <span>{{ isJapanese ? '単語データの編集' : 'Edit Data Kosakata' }}</span>
                        <span class="text-sm font-bold text-rose-600 bg-rose-50 px-2.5 py-0.5 rounded-lg border border-rose-200/60 font-jp">
                            {{ vocabulary.kanji || vocabulary.hiragana }}
                        </span>
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <Link 
                        :href="route('sensei.vocabularies.index')"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer inline-flex items-center gap-1.5"
                    >
                        <span>{{ isJapanese ? 'キャンセル' : 'Batal' }}</span>
                    </Link>
                    <button 
                        type="submit" 
                        form="vocabEditForm"
                        :disabled="form.processing"
                        class="px-6 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/25 hover:shadow-japan-red/40 active:scale-[0.98] transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <Save class="w-4 h-4" />
                        <span>{{ isJapanese ? '変更を保存する' : 'Simpan Perubahan' }}</span>
                    </button>
                </div>
            </div>

            <!-- ================= TWO-COLUMN WORKSPACE ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- LEFT COLUMN: FORM WORKSPACE (lg:col-span-7) -->
                <div class="lg:col-span-7 space-y-6">
                    <form id="vocabEditForm" @submit.prevent="submitForm" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-7">
                        
                        <!-- ================= SECTION 1: KLASIFIKASI KATA ================= -->
                        <div class="space-y-3 pb-2 border-b border-slate-100">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                                <Tag class="w-3.5 h-3.5 text-rose-500" />
                                <span>{{ isJapanese ? '1. レベル・分類と品詞' : '1. Level Belajar & Klasifikasi Kata' }}</span>
                            </h2>

                            <!-- Baris Kompak 3 Kolom: Level, Kategori, Jenis Kata -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                <!-- Level Belajar -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        {{ isJapanese ? 'JLPT / SSW レベル *' : 'Level Belajar *' }}
                                    </label>
                                    <select 
                                        v-model="form.level" 
                                        required
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none transition-all cursor-pointer shadow-2xs"
                                    >
                                        <option v-for="(label, code) in (levels || defaultLevels)" :key="code" :value="code">
                                            {{ label }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.level" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.level }}</p>
                                </div>

                                <!-- Kategori / Topik Kosakata -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        {{ isJapanese ? 'カテゴリ / テーマ *' : 'Kategori / Topik *' }}
                                    </label>
                                    <input 
                                        type="text" 
                                        v-model="form.category" 
                                        list="categoryList"
                                        required 
                                        placeholder="Pilih atau ketik kategori..."
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none transition-all shadow-2xs"
                                    />
                                    <datalist id="categoryList">
                                        <option v-for="cat in categories" :key="cat" :value="cat" />
                                    </datalist>
                                    <p v-if="form.errors.category" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.category }}</p>
                                </div>

                                <!-- Jenis Kata (Part of Speech) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1 font-jp">
                                        {{ isJapanese ? '品詞分類 *' : 'Jenis Kata *' }}
                                    </label>
                                    <select 
                                        v-model="form.word_type" 
                                        required
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none transition-all cursor-pointer shadow-2xs font-jp"
                                    >
                                        <option v-for="wt in (wordTypes || defaultWordTypes)" :key="wt" :value="wt">
                                            {{ wt }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.word_type" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.word_type }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- ================= SECTION 2: ARTI BAHASA INDONESIA (INPUT UTAMA & AUTO-TRANSLATE) ================= -->
                        <div class="space-y-4 pt-2 bg-gradient-to-br from-rose-50/60 via-red-50/30 to-amber-50/30 p-5 rounded-2xl border border-rose-200/80">
                            <div class="flex items-center justify-between pb-2 border-b border-rose-200/60">
                                <h2 class="text-xs font-bold uppercase tracking-wider text-rose-900 flex items-center gap-2">
                                    <Sparkles class="w-4 h-4 text-rose-600 animate-spin-slow" />
                                    <span>{{ isJapanese ? '2. インドネシア語の意味（自動日本語変換）' : '2. Arti Bahasa Indonesia (Input Utama)' }}</span>
                                </h2>
                                <span v-if="isTranslating" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-rose-600 animate-pulse">
                                    <Loader2 class="w-3.5 h-3.5 animate-spin" />
                                    <span>Mencari padanan Jepang...</span>
                                </span>
                                <span v-else-if="translateSuccess" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                    <CheckCircle2 class="w-3.5 h-3.5" />
                                    <span>Padanan otomatis terisi!</span>
                                </span>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-900 mb-1.5 flex items-center justify-between">
                                    <span>{{ isJapanese ? 'インドネシア語の意味 *' : 'Arti Bahasa Indonesia *' }}</span>
                                    <span class="text-[10px] text-slate-500 font-normal">Ketik arti di sini, Huruf Jepang otomatis terisi di bawah</span>
                                </label>
                                
                                <div class="relative flex items-center gap-2">
                                    <input 
                                        type="text" 
                                        v-model="form.meaning_id" 
                                        @input="handleMeaningInput"
                                        required 
                                        placeholder="Ketik arti kata di sini, contoh: Guru, Makan, Sekolah, Perusahaan, Rumah Sakit..." 
                                        class="w-full px-4 py-3 rounded-xl border border-rose-200 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none bg-white shadow-2xs transition-all"
                                    />
                                    <button 
                                        type="button" 
                                        @click="triggerAutoTranslate"
                                        :disabled="isTranslating || !form.meaning_id"
                                        title="Klik untuk menerjemahkan ke Jepang ulang"
                                        class="px-3.5 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shrink-0 flex items-center gap-1.5 transition-all disabled:opacity-50 cursor-pointer shadow-xs"
                                    >
                                        <Sparkles class="w-3.5 h-3.5" />
                                        <span class="hidden sm:inline">Terjemahkan</span>
                                    </button>
                                </div>
                                <p v-if="form.errors.meaning_id" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.meaning_id }}</p>

                                <!-- Suggestion Chips -->
                                <div v-if="suggestions.length > 0" class="mt-3 pt-2.5 border-t border-rose-100 space-y-1.5">
                                    <span class="text-[10px] font-bold text-slate-500 block">💡 Pilihan Padanan Kata (Klik untuk terapkan):</span>
                                    <div class="flex flex-wrap gap-2">
                                        <button 
                                            type="button"
                                            v-for="(sug, idx) in suggestions" 
                                            :key="idx"
                                            @click="applySuggestion(sug)"
                                            class="px-3 py-1.5 rounded-xl text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs group hover:scale-[1.02]"
                                            :class="form.hiragana === sug.hiragana ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-slate-800 border-rose-200 hover:border-rose-400'"
                                        >
                                            <span v-if="sug.kanji" class="font-jp font-black text-sm">{{ sug.kanji }}</span>
                                            <span class="font-jp text-[11px] opacity-80">({{ sug.hiragana }})</span>
                                            <span class="text-[10px] font-mono opacity-70">[{{ sug.romaji }}]</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ================= SECTION 3: PENULISAN HURUF JEPANG & CARA BACA ================= -->
                        <div class="space-y-4 pt-2">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 font-jp">
                                    <Languages class="w-3.5 h-3.5 text-rose-500" />
                                    <span>{{ isJapanese ? '3. 日本語表記・読み方・ローマ字（編集可能）' : '3. Huruf & Pelafalan Jepang (Hasil Otomatis & Bisa Diedit)' }}</span>
                                </h2>
                                <span class="text-[10px] text-slate-400 font-medium hidden sm:inline">Sensei bebas mengoreksi atau mengubah isi di bawah</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Huruf Kanji (Opsional) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1 font-jp flex items-center justify-between">
                                        <span>{{ isJapanese ? '漢字表記（任意）' : 'Huruf Kanji (Opsional)' }}</span>
                                        <span class="text-[10px] text-slate-400">Bisa dikosongkan</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        v-model="form.kanji" 
                                        :placeholder="isJapanese ? '例：先生 / 日本 / 食べる' : 'Contoh: 先生 / 日本 / 食べる'" 
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-jp font-bold text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none bg-slate-50/50 focus:bg-white transition-all"
                                    />
                                    <p v-if="form.errors.kanji" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.kanji }}</p>
                                </div>

                                <!-- Hiragana / Cara Baca -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1 font-jp flex items-center justify-between">
                                        <span>{{ isJapanese ? 'ひらがな・読み *' : 'Hiragana / Cara Baca *' }}</span>
                                        <span class="text-[10px] text-rose-500 font-bold flex items-center gap-0.5">
                                            <Sparkles class="w-3 h-3" /> Auto-Konversi
                                        </span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <input 
                                            type="text" 
                                            v-model="form.hiragana" 
                                            @input="handleHiraganaTyping"
                                            required 
                                            :placeholder="isJapanese ? 'sensei -> せんせい' : 'Ketik romaji: sensei -> せんせい'" 
                                            class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200 text-sm font-jp font-bold text-rose-600 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none bg-slate-50/50 focus:bg-white transition-all"
                                        />
                                        <button 
                                            v-if="form.hiragana || form.kanji"
                                            type="button" 
                                            @click="playNativeJapaneseAudio(form.hiragana || form.kanji)"
                                            class="absolute right-2 p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-colors"
                                            :class="{ 'text-amber-600 animate-pulse bg-amber-50': isPlayingAudio }"
                                            title="Dengarkan Pelafalan Asli Jepang (Tokyo Accent 🔊)"
                                        >
                                            <Volume2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                    <p v-if="form.errors.hiragana" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.hiragana }}</p>

                                    <!-- Quick Audio Actions below Hiragana -->
                                    <div v-if="form.hiragana || form.kanji" class="flex flex-wrap items-center justify-between gap-2 mt-2">
                                        <button 
                                            type="button" 
                                            @click="playNativeJapaneseAudio(form.hiragana || form.kanji)"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all cursor-pointer"
                                            :class="isPlayingAudio ? 'bg-amber-100 text-amber-900 border border-amber-300 animate-pulse' : 'bg-slate-100 text-slate-700 hover:bg-amber-50 hover:text-amber-800 border border-slate-200'"
                                        >
                                            <Volume2 class="w-3.5 h-3.5 text-amber-600" />
                                            <span>{{ isPlayingAudio ? 'Sedang Memutar Audio...' : 'Putar Suara Asli Jepang 🔊' }}</span>
                                        </button>

                                        <button 
                                            type="button" 
                                            @click="saveNativeAudioForCard"
                                            :disabled="isSavingNativeAudio"
                                            class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-lg transition-all cursor-pointer disabled:opacity-50"
                                            title="Simpan audio pelafalan ini langsung ke kartu agar siswa bisa mendengarkannya di HP"
                                        >
                                            <Download v-if="!isSavingNativeAudio" class="w-3.5 h-3.5 text-emerald-600" />
                                            <Loader2 v-else class="w-3.5 h-3.5 animate-spin text-emerald-600" />
                                            <span>{{ form.generated_audio_url ? '✓ Audio Asli Terpasang' : '📥 Simpan sebagai Audio Kartu' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Romaji Pelafalan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1 font-jp flex items-center justify-between">
                                    <span>{{ isJapanese ? 'ローマ字（任意）' : 'Romaji Pelafalan (Opsional)' }}</span>
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.romaji" 
                                    :placeholder="isJapanese ? '例：sensei / nihon / taberu' : 'Contoh: sensei / nihon / taberu'" 
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-mono text-slate-700 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none bg-slate-50/50 focus:bg-white transition-all"
                                />
                                <p v-if="form.errors.romaji" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.romaji }}</p>
                            </div>
                        </div>

                        <!-- ================= SECTION 4: CONTOH KALIMAT (REIBUN) ================= -->
                        <div class="space-y-4 pt-2">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 pb-2 border-b border-slate-100 font-jp">
                                <BookOpen class="w-3.5 h-3.5 text-rose-500" />
                                <span>{{ isJapanese ? '4. 例文・実践フレーズ (Reibun)' : '4. Contoh Kalimat (Reibun)' }}</span>
                            </h2>

                            <div class="space-y-3 p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1 font-jp">
                                        {{ isJapanese ? '例文（日本語）' : 'Kalimat Bahasa Jepang' }}
                                    </label>
                                    <input 
                                        type="text" 
                                        v-model="form.example_sentence_jp" 
                                        :placeholder="isJapanese ? '例：田中さんは日本語の先生です。' : 'Kalimat Jepang, misal: 田中さんは日本語の先生です。'" 
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-jp focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none transition-all"
                                    />
                                    <p v-if="form.errors.example_sentence_jp" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.example_sentence_jp }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1 font-jp">
                                        {{ isJapanese ? 'インドネシア語訳' : 'Terjemahan Kalimat Indonesia' }}
                                    </label>
                                    <input 
                                        type="text" 
                                        v-model="form.example_sentence_id" 
                                        :placeholder="isJapanese ? '例：Sdr. Tanaka adalah guru bahasa Jepang.' : 'Arti Indonesia, misal: Sdr. Tanaka adalah guru bahasa Jepang.'" 
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none transition-all"
                                    />
                                    <p v-if="form.errors.example_sentence_id" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.example_sentence_id }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- ================= SECTION 5: MEDIA VISUAL & AUDIO ================= -->
                        <div class="space-y-4 pt-2">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 pb-2 border-b border-slate-100 font-jp">
                                <Mic class="w-3.5 h-3.5 text-rose-500" />
                                <span>{{ isJapanese ? '5. 画像と音声メディア (Media Flashcard)' : '5. Media Visual & Audio Pelafalan' }}</span>
                            </h2>

                            <!-- Gambar Ilustrasi Kosakata (Opsional) -->
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-700">
                                    {{ isJapanese ? '単語のイラスト・画像（任意）' : 'Gambar Ilustrasi Kosakata (Opsional)' }}
                                </label>
                                <div class="flex items-center gap-4">
                                    <div v-if="previewImageUrl" class="relative w-20 h-20 rounded-2xl overflow-hidden border border-slate-200 shrink-0">
                                        <img :src="previewImageUrl" alt="Preview Gambar" class="w-full h-full object-cover" />
                                        <button 
                                            type="button" 
                                            @click="removeImage"
                                            class="absolute top-1 right-1 p-1 bg-rose-600 text-white rounded-full hover:bg-rose-700 transition-colors shadow-xs"
                                        >
                                            <X class="w-3 h-3" />
                                        </button>
                                    </div>
                                    <label class="flex-1 border-2 border-dashed border-slate-200 hover:border-rose-300 rounded-2xl p-4 text-center cursor-pointer transition-colors bg-slate-50/50 hover:bg-rose-50/20">
                                        <input type="file" accept="image/*" @change="handleImageUpload" class="hidden" />
                                        <div class="flex items-center justify-center gap-2 text-xs font-bold text-slate-600">
                                            <ImageIcon class="w-4 h-4 text-rose-500" />
                                            <span>{{ previewImageUrl ? 'Ganti Foto Ilustrasi' : 'Unggah Foto Ilustrasi (PNG, JPG)' }}</span>
                                        </div>
                                    </label>
                                </div>
                                <p v-if="form.errors.image_file" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.image_file }}</p>
                            </div>

                            <!-- Audio Penutur Asli Jepang Terpasang (Generated Audio Status) -->
                            <div v-if="form.generated_audio_url && !form.audio_file" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shrink-0">
                                        <Volume2 class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <span class="text-xs font-black text-emerald-950 block">Audio Penutur Asli Jepang Siap Disimpan</span>
                                        <span class="text-[10px] text-emerald-700 block">Audio pelafalan native berformat MP3 tersimpan aman di server LPK Anda.</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button 
                                        type="button" 
                                        @click="playAudio(form.generated_audio_url)"
                                        class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-1.5 cursor-pointer shadow-xs"
                                    >
                                        <Play class="w-3 h-3 fill-current" />
                                        <span>Putar</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="form.generated_audio_url = ''; previewAudioUrl = null"
                                        class="px-2.5 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-700 text-xs font-bold cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>

                            <!-- Atau Unggah File Audio MP3 Sendiri -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    {{ isJapanese ? 'またはカスタム音声ファイルをアップロード' : 'Atau Unggah Rekaman File Audio Sendiri (Opsional)' }}
                                </label>
                                <div class="relative border-2 border-dashed rounded-3xl p-5 transition-all text-center group cursor-pointer"
                                    :class="activeAudioUrl ? 'border-emerald-300 bg-emerald-50/40 hover:bg-emerald-50/70' : 'border-slate-200 hover:border-rose-300 bg-slate-50/50 hover:bg-rose-50/30'">
                                    
                                    <input 
                                        type="file" 
                                        accept="audio/*" 
                                        @change="handleAudioUpload" 
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10"
                                    />

                                    <div v-if="!activeAudioUrl" class="flex flex-col items-center justify-center space-y-2">
                                        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                                            <UploadCloud class="w-5 h-5" />
                                        </div>
                                        <div class="space-y-0.5">
                                            <span class="text-xs font-bold text-slate-800 block">Klik atau geser file audio pelafalan ke sini</span>
                                            <span class="text-[10px] text-slate-400 block">Format MP3, WAV, OGG, atau M4A (Maks 10MB)</span>
                                        </div>
                                    </div>

                                    <div v-else class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                        <div class="flex items-center gap-3 text-left">
                                            <div class="w-9 h-9 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                                <Volume2 class="w-4 h-4" />
                                            </div>
                                            <div>
                                                <span class="text-xs font-bold text-slate-800 block line-clamp-1">
                                                    {{ form.audio_file ? form.audio_file.name : (form.generated_audio_url ? 'Audio Asli Jepang (Server LPK)' : (vocabulary.audio_file ? 'Audio tersimpan di database' : 'File audio')) }}
                                                </span>
                                                <span class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
                                                    <Check class="w-3 h-3" /> Audio siap diputar. Klik untuk ganti file.
                                                </span>
                                            </div>
                                        </div>
                                        <button 
                                            type="button" 
                                            @click.stop="playAudio(activeAudioUrl)"
                                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-2 z-20 cursor-pointer shadow-xs transition-transform active:scale-95 shrink-0"
                                        >
                                            <Play class="w-3.5 h-3.5 fill-current" />
                                            <span>Putar Suara 🔊</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p v-if="form.errors.audio_file" class="text-xs text-rose-500 font-bold mt-1">{{ form.errors.audio_file }}</p>
                        </div>

                        <!-- Form Submit Bar -->
                        <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                            <Link 
                                :href="route('sensei.vocabularies.index')"
                                class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer"
                            >
                                {{ isJapanese ? 'キャンセル' : 'Batal' }}
                            </Link>

                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="px-7 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/25 hover:shadow-japan-red/40 active:scale-[0.98] transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
                            >
                                <Save class="w-4 h-4" />
                                <span>{{ isJapanese ? '変更を保存する' : 'Simpan Perubahan' }}</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- RIGHT COLUMN: INTERACTIVE STICKY LIVE PREVIEW (lg:col-span-5) -->
                <div class="lg:col-span-5 lg:sticky lg:top-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5 font-jp">
                            <Eye class="w-4 h-4 text-rose-500" />
                            <span>{{ isJapanese ? 'リアルタイム プレビュー' : 'Live Card Preview (Tampilan Siswa)' }}</span>
                        </span>
                        <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-600 font-bold border border-rose-200/50">
                            Kotoba Flashcard
                        </span>
                    </div>

                    <!-- The Dark Japan Tech Flashcard -->
                    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 text-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-700/60 relative overflow-hidden flex flex-col justify-between min-h-[400px]">
                        <!-- Background Ambient Glow -->
                        <div class="absolute -right-10 -top-10 w-44 h-44 bg-japan-red/25 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -left-10 -bottom-10 w-44 h-44 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

                        <!-- Card Top Tags -->
                        <div class="flex items-center justify-between gap-2 z-10">
                            <div class="flex items-center gap-1.5">
                                <span class="px-2.5 py-0.5 rounded-lg bg-japan-red text-white text-[10px] font-black uppercase">
                                    {{ form.level || 'N5' }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-lg bg-white/10 backdrop-blur-xs text-[10px] font-bold text-slate-300 border border-white/10">
                                    {{ form.category || 'Kategori Kosakata' }}
                                </span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border border-rose-400/40 bg-japan-red/20 text-rose-300 font-jp">
                                {{ form.word_type || 'Jenis Kata' }}
                            </span>
                        </div>

                        <!-- Optional Card Image -->
                        <div v-if="previewImageUrl" class="my-3 z-10 flex justify-center">
                            <img :src="previewImageUrl" alt="Ilustrasi" class="max-h-32 rounded-xl object-contain border border-white/10 shadow-md" />
                        </div>

                        <!-- Main Word Display -->
                        <div class="my-auto py-5 text-center space-y-2 z-10">
                            <div v-if="form.kanji" class="text-4xl sm:text-5xl font-black font-jp tracking-wide text-white drop-shadow-md">
                                {{ form.kanji }}
                            </div>
                            <div class="text-xl sm:text-2xl font-bold font-jp" :class="form.kanji ? 'text-rose-400' : 'text-4xl text-white font-black'">
                                {{ form.hiragana || (isJapanese ? '（ひらがな）' : '（cara baca）') }}
                            </div>
                            <div v-if="form.romaji" class="text-xs text-slate-400 font-mono tracking-wider">
                                {{ form.romaji }}
                            </div>
                            <div class="pt-4 mt-3 border-t border-white/10 text-base sm:text-lg font-bold text-slate-100">
                                {{ form.meaning_id || (isJapanese ? '（インドネシア語の意味）' : '（arti dalam bahasa Indonesia）') }}
                            </div>
                        </div>

                        <!-- Reibun / Example Sentence Preview -->
                        <div v-if="form.example_sentence_jp || form.example_sentence_id" class="bg-white/5 backdrop-blur-xs p-3.5 rounded-2xl border border-white/10 text-xs space-y-1.5 z-10 mb-2">
                            <p v-if="form.example_sentence_jp" class="font-jp text-slate-200 font-medium leading-relaxed">
                                {{ form.example_sentence_jp }}
                            </p>
                            <p v-if="form.example_sentence_id" class="text-[11px] text-slate-400 italic">
                                {{ form.example_sentence_id }}
                            </p>
                        </div>

                        <!-- Audio Indicator & Test Button -->
                        <div class="flex items-center justify-between pt-3 mt-1 border-t border-white/10 text-xs text-slate-400 z-10">
                            <div class="flex items-center gap-2">
                                <Volume2 class="w-4 h-4" :class="activeAudioUrl ? 'text-emerald-400 animate-pulse' : 'text-amber-400'" />
                                <span class="text-[11px]">{{ activeAudioUrl ? 'Audio Asli Siap' : 'Audio Native Stream (Tokyo)' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button 
                                    v-if="activeAudioUrl" 
                                    type="button" 
                                    @click="playAudio(activeAudioUrl)"
                                    class="px-3 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 font-bold border border-emerald-500/30 flex items-center gap-1.5 cursor-pointer transition-all active:scale-95 text-xs"
                                >
                                    <Play class="w-3.5 h-3.5 fill-current" />
                                    <span>Tes Audio</span>
                                </button>
                                <button 
                                    v-else-if="form.hiragana || form.kanji"
                                    type="button" 
                                    @click="playNativeJapaneseAudio(form.hiragana || form.kanji)"
                                    class="px-3 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 font-bold border border-amber-500/30 flex items-center gap-1.5 cursor-pointer transition-all active:scale-95 text-xs"
                                    :class="{ 'animate-pulse': isPlayingAudio }"
                                >
                                    <Volume2 class="w-3.5 h-3.5" />
                                    <span>{{ isPlayingAudio ? 'Memutar...' : 'Tes Suara Jepang 🔊' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Helpful Guide Card -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs text-slate-600 space-y-2">
                        <div class="font-bold text-slate-800 flex items-center gap-1.5">
                            <span>💡</span>
                            <span>Alur Cerdas Edit Kosakata:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-500">
                            <li>Ubah Arti Bahasa Indonesia, sistem dapat mencarikan padanan alternatif.</li>
                            <li>Gunakan tombol "Terjemahkan" untuk memperbarui huruf Kanji/Hiragana sesuai arti baru.</li>
                            <li>Sensei bebas mengedit Kanji, Hiragana, atau Romaji secara manual.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { romajiToHiragana } from '@/Utils/japaneseConverter';
import { notifySuccess } from '@/Utils/alert';
import { useLang } from '@/Composables/useLang';
import { 
    ArrowLeft,
    Save, 
    Sparkles, 
    Volume2, 
    UploadCloud, 
    Play, 
    Eye, 
    Tag, 
    Languages, 
    BookOpen, 
    Mic, 
    Check,
    CheckCircle2,
    Loader2,
    Image as ImageIcon,
    X,
    Download
} from 'lucide-vue-next';

const { isJapanese } = useLang();

const props = defineProps({
    vocabulary: Object,
    categories: Array,
    wordTypes: Array,
    chapters: Array,
    levels: Object,
});

const defaultLevels = {
    'N5': 'N5 (Tingkat Dasar / Minna I)',
    'N4': 'N4 (Standar Kerja / Minna II)',
    'N3': 'N3 (Menengah / Intermediate)',
    'SSW_KAIGO': 'SSW Kaigo (Perawat Lansia)',
    'SSW_FOOD': 'SSW Pengolahan Makanan & Restoran',
    'SSW_AGRICULTURE': 'SSW Pertanian & Peternakan',
    'GENERAL': 'Umum & Etika Kerja (Aisatsu/5S)',
};

const defaultWordTypes = [
    'Kata Benda',
    'Kata Kerja',
    'Kata Sifat-i',
    'Kata Sifat-na',
    'Kata Keterangan',
    'Kata Sambung',
    'Ungkapan / Salam',
];

const previewAudioUrl = ref(null);
const previewImageUrl = ref(props.vocabulary.image_file || null);
let currentAudioInstance = null;
let activeAudioEl = null;
const isPlayingAudio = ref(false);
const isSavingNativeAudio = ref(false);

const activeAudioUrl = computed(() => {
    return previewAudioUrl.value || form.generated_audio_url || props.vocabulary.audio_file || null;
});

// Translation state
const isTranslating = ref(false);
const translateSuccess = ref(false);
const suggestions = ref([]);
let translateTimer = null;

const form = useForm({
    level: props.vocabulary.level || 'N5',
    category: props.vocabulary.category || 'Kehidupan Sehari-hari',
    word_type: props.vocabulary.word_type || 'Kata Benda',
    chapter_id: props.vocabulary.chapter_id || null,
    meaning_id: props.vocabulary.meaning_id || '',
    kanji: props.vocabulary.kanji || '',
    hiragana: props.vocabulary.hiragana || '',
    romaji: props.vocabulary.romaji || '',
    example_sentence_jp: props.vocabulary.example_sentence_jp || '',
    example_sentence_id: props.vocabulary.example_sentence_id || '',
    audio_file: null,
    generated_audio_url: '',
    image_file: null,
});

/**
 * Handle user typing in Indonesian meaning
 */
const handleMeaningInput = () => {
    translateSuccess.value = false;
    if (translateTimer) clearTimeout(translateTimer);
    
    if (!form.meaning_id || form.meaning_id.trim().length < 2) {
        suggestions.value = [];
        return;
    }

    translateTimer = setTimeout(() => {
        triggerAutoTranslate();
    }, 600);
};

/**
 * Call backend auto-translate endpoint
 */
const triggerAutoTranslate = async () => {
    if (!form.meaning_id || form.meaning_id.trim().length < 2) return;

    isTranslating.value = true;
    try {
        const response = await axios.post(route('sensei.vocabularies.auto-translate'), {
            meaning: form.meaning_id.trim(),
        });

        const data = response.data;
        if (data && data.found) {
            form.kanji = data.kanji || '';
            form.hiragana = data.hiragana || '';
            form.romaji = data.romaji || '';
            if (data.word_type) {
                form.word_type = data.word_type;
            }
            suggestions.value = data.suggestions || [];
            translateSuccess.value = true;
        } else {
            suggestions.value = [];
            translateSuccess.value = false;
        }
    } catch (e) {
        console.error('Auto-translate error:', e);
    } finally {
        isTranslating.value = false;
    }
};

/**
 * Apply selected suggestion chip
 */
const applySuggestion = (sug) => {
    form.kanji = sug.kanji || '';
    form.hiragana = sug.hiragana || '';
    form.romaji = sug.romaji || '';
    if (sug.word_type) {
        form.word_type = sug.word_type;
    }
    translateSuccess.value = true;
};

const handleHiraganaTyping = (e) => {
    form.hiragana = romajiToHiragana(e.target.value);
};

const handleImageUpload = (e) => {
    if (e.target.files && e.target.files[0]) {
        const file = e.target.files[0];
        form.image_file = file;
        if (previewImageUrl.value && previewImageUrl.value.startsWith('blob:')) {
            URL.revokeObjectURL(previewImageUrl.value);
        }
        previewImageUrl.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    form.image_file = null;
    if (previewImageUrl.value && previewImageUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(previewImageUrl.value);
    }
    previewImageUrl.value = null;
};

const handleAudioUpload = (e) => {
    if (e.target.files && e.target.files[0]) {
        const file = e.target.files[0];
        form.audio_file = file;
        form.generated_audio_url = '';
        if (previewAudioUrl.value) {
            URL.revokeObjectURL(previewAudioUrl.value);
        }
        previewAudioUrl.value = URL.createObjectURL(file);
    }
};

const playAudio = (url) => {
    if (!url) return;
    if (currentAudioInstance) {
        currentAudioInstance.pause();
    }
    if (activeAudioEl) {
        activeAudioEl.pause();
        activeAudioEl = null;
        isPlayingAudio.value = false;
    }
    currentAudioInstance = new Audio(url);
    currentAudioInstance.play().catch(() => {});
};

/**
 * Play authentic Native Japanese MP3 Audio via server stream.
 * NEVER uses client-side robot or Thai voice!
 */
const playNativeJapaneseAudio = (text) => {
    if (!text) return;
    if (activeAudioEl) {
        activeAudioEl.pause();
        activeAudioEl = null;
    }
    if (currentAudioInstance) {
        currentAudioInstance.pause();
    }

    isPlayingAudio.value = true;
    const url = route('sensei.vocabularies.pronunciation-audio', { text: text.trim() });
    activeAudioEl = new Audio(url);

    activeAudioEl.onended = () => {
        isPlayingAudio.value = false;
    };

    activeAudioEl.onerror = () => {
        isPlayingAudio.value = false;
        // Fallback ONLY to real Japanese voices if installed in browser
        if ('speechSynthesis' in window) {
            const voices = window.speechSynthesis.getVoices();
            const jaVoice = voices.find(v => v.lang && v.lang.toLowerCase().startsWith('ja'));
            if (jaVoice) {
                const u = new SpeechSynthesisUtterance(text);
                u.voice = jaVoice;
                u.lang = 'ja-JP';
                window.speechSynthesis.speak(u);
            }
        }
    };

    activeAudioEl.play().catch(e => {
        console.warn('Native audio play error:', e);
        isPlayingAudio.value = false;
    });
};

/**
 * Permanently save native Japanese pronunciation MP3 into server public disk
 */
const saveNativeAudioForCard = async () => {
    const text = form.hiragana || form.kanji;
    if (!text) return;

    isSavingNativeAudio.value = true;
    try {
        const res = await axios.post(route('sensei.vocabularies.generate-native-audio'), { text: text.trim() });
        if (res.data && res.data.audio_url) {
            form.generated_audio_url = res.data.audio_url;
            previewAudioUrl.value = res.data.audio_url;
            notifySuccess('Audio Native Tersimpan', 'Suara penutur asli Jepang berhasil disimpan ke server LPK untuk kartu ini!');
        }
    } catch (e) {
        console.error('Save native audio error:', e);
    } finally {
        isSavingNativeAudio.value = false;
    }
};

const submitForm = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('sensei.vocabularies.update', props.vocabulary.id), {
        onSuccess: () => {
            if (previewAudioUrl.value) {
                URL.revokeObjectURL(previewAudioUrl.value);
            }
            if (previewImageUrl.value && previewImageUrl.value.startsWith('blob:')) {
                URL.revokeObjectURL(previewImageUrl.value);
            }
            notifySuccess(
                isJapanese.value ? '更新完了' : 'Berhasil Diperbarui', 
                isJapanese.value ? '単語データを正常に更新しました。' : 'Data kosakata berhasil diperbarui.'
            );
        },
    });
};
</script>
