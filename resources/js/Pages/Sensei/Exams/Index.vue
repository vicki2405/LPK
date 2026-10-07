<template>
    <Head :title="isJapanese ? 'CBT試験管理 - 正夢' : 'Manajemen CBT - Masayume'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-12">
            <!-- Top Header Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-japan-red text-xs font-bold mb-2 border border-red-200">
                        <Clock class="w-3.5 h-3.5" />
                        <span>{{ isJapanese ? 'CBT試験パッケージ' : 'Engine & Jadwal CBT' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-950 tracking-tight flex items-center gap-2.5">
                        <Clock class="w-6 h-6 text-japan-red" />
                        <span>{{ isJapanese ? 'CBT試験・スケジュール管理' : 'Ujian & Jadwal CBT' }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1">
                        {{ isJapanese 
                            ? 'JLPT N4およびJFT-Basic A2公式模擬試験パッケージの作成、開催スケジュール、出題問題の選択、および公開・非公開の切り替えを一元管理します。' 
                            : 'Atur paket ujian CBT, pilih butir soal dari bank soal, passing grade, durasi, dan kendali saklar ON / OFF untuk menampilkan ujian ke siswa.' 
                        }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button 
                        @click="openCreateModal"
                        class="px-5 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md shadow-japan-red/20 transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto"
                    >
                        <PlusCircle class="w-4 h-4" />
                        <span>{{ isJapanese ? '+ 新規CBT試験を作成' : '+ Buat Paket CBT Baru' }}</span>
                    </button>
                </div>
            </div>

            <!-- Minimalist Search & Filter Bar (Clean & Compact) -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white px-5 py-3.5 rounded-2xl border border-slate-200 shadow-2xs">
                <div class="relative w-full sm:w-72">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input 
                        type="text" 
                        v-model="searchQuery" 
                        :placeholder="isJapanese ? '試験名・コードで検索...' : 'Cari nama atau kode ujian...'" 
                        class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-japan-red/50 text-slate-800 placeholder-slate-400 font-medium"
                    />
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <!-- Filter Status -->
                    <select 
                        v-model="selectedStatusFilter" 
                        class="w-1/2 sm:w-auto text-xs px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 font-bold focus:outline-none focus:ring-2 focus:ring-japan-red/50 cursor-pointer bg-white"
                    >
                        <option value="all">{{ isJapanese ? '全ステータス' : 'Semua Status' }}</option>
                        <option value="published">ON ({{ isJapanese ? '公開中' : 'Aktif' }})</option>
                        <option value="draft">OFF ({{ isJapanese ? '非公開' : 'Draft' }})</option>
                    </select>

                    <!-- Filter Angkatan / Batch -->
                    <select 
                        v-model="selectedBatchFilter" 
                        class="w-1/2 sm:w-auto text-xs px-3 py-1.5 rounded-xl border border-slate-200 text-slate-700 font-bold focus:outline-none focus:ring-2 focus:ring-japan-red/50 cursor-pointer bg-white"
                    >
                        <option value="all">{{ isJapanese ? '全対象期生' : 'Semua Angkatan' }}</option>
                        <option v-for="b in batches" :key="b.id" :value="b.id">
                            {{ b.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Exams Grid -->
            <div v-if="filteredExams.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="exam in filteredExams" :key="exam.id"
                    class="bg-white p-6 rounded-3xl border shadow-sm hover:shadow-md transition-all flex flex-col justify-between group"
                    :class="exam.is_published ? 'border-emerald-200 ring-1 ring-emerald-400/20' : 'border-slate-200 opacity-90'">
                    <div>
                        <!-- Header Exam Card: Level, Mapel, Mode & ON/OFF Switch -->
                        <div class="flex items-center justify-between gap-1.5 flex-wrap mb-3">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-xs font-black px-2.5 py-0.5 rounded-lg bg-red-50 text-japan-red border border-red-100 uppercase">
                                    {{ exam.level }}
                                </span>

                                <!-- Badge Mata Pelajaran (Mapel) -->
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center gap-1">
                                    <BookOpen class="w-3 h-3" />
                                    <span>{{ exam.subject?.name || 'Bahasa Jepang' }}</span>
                                </span>

                                <!-- Badge Bank Soal Sumber -->
                                <span v-if="exam.question_bank" class="text-[11px] font-bold px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center gap-1" :title="'Master Bank: ' + exam.question_bank.title">
                                    <BookOpenCheck class="w-3 h-3 text-emerald-600" />
                                    <span class="max-w-[120px] truncate">{{ exam.question_bank.title }}</span>
                                </span>
                            </div>

                            <!-- SAKLAR INTERAKTIF ON / OFF (STATUS AKSES SISWA) -->
                            <button 
                                @click="togglePublish(exam)"
                                type="button"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer shadow-2xs"
                                :class="exam.is_published 
                                    ? 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-emerald-500/25' 
                                    : 'bg-slate-200 hover:bg-slate-300 text-slate-700'"
                                :title="exam.is_published ? (isJapanese ? 'クリックして非公開 (OFF) にする' : 'Klik untuk matikan (OFF) ujian ini') : (isJapanese ? 'クリックして公開 (ON) にする' : 'Klik untuk aktifkan (ON) ujian ini')"
                            >
                                <span class="w-2 h-2 rounded-full" :class="exam.is_published ? 'bg-white animate-pulse' : 'bg-slate-400'"></span>
                                <span>{{ exam.is_published ? (isJapanese ? 'ON (公開中)' : 'ON (Aktif)') : (isJapanese ? 'OFF (非公開)' : 'OFF (Draft)') }}</span>
                            </button>
                        </div>

                        <h3 class="text-base font-black text-slate-950 mb-1">
                            {{ exam.title }}
                        </h3>
                        <p class="text-[11px] font-mono text-slate-400 mb-2">
                            {{ exam.code }}
                        </p>
                        <p v-if="exam.description" class="text-xs text-slate-600 line-clamp-2 mb-4 font-medium">
                            {{ exam.description }}
                        </p>

                        <!-- Exam Specifications -->
                        <div class="space-y-2 text-xs text-slate-800 border-t border-slate-100 pt-3 font-semibold">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-600">{{ isJapanese ? '制限時間' : 'Durasi Waktu' }}:</span>
                                <span class="font-black text-slate-950">{{ exam.duration_minutes }} {{ isJapanese ? '分' : 'Menit' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-600">{{ isJapanese ? '合格基準点' : 'Passing Grade' }}:</span>
                                <span class="font-black text-japan-red">{{ exam.passing_score }} / {{ exam.max_score }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-600">{{ isJapanese ? '出題問題数' : 'Jumlah Soal' }}:</span>
                                <span class="font-black text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                    {{ exam.questions_count || exam.questions?.length || 0 }} {{ isJapanese ? '問' : 'Butir Soal' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-600">{{ isJapanese ? '対象期生' : 'Target Angkatan' }}:</span>
                                <span class="font-black text-indigo-900 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">
                                    {{ exam.batch?.name || (isJapanese ? '全期生対象' : 'Semua Angkatan') }}
                                </span>
                            </div>

                            <!-- SAKLAR TUNGGAL MODEL UJIAN (FORMAL JFT VS GAME) -->
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-slate-600">{{ isJapanese ? 'モード設定' : 'Model Ujian' }}:</span>
                                <button 
                                    @click="toggleExamMode(exam)"
                                    type="button"
                                    class="px-2.5 py-1 rounded-xl text-xs font-black flex items-center gap-1.5 transition-all cursor-pointer border shadow-2xs hover:scale-[1.02] active:scale-[0.98]"
                                    :class="exam.display_mode === 'game' 
                                        ? 'bg-amber-50 text-amber-900 border-amber-300 hover:bg-amber-100' 
                                        : 'bg-blue-50 text-blue-900 border-blue-200 hover:bg-blue-100'"
                                    :title="exam.display_mode === 'game' ? 'Klik untuk ganti ke Model Resmi JFT' : 'Klik untuk ganti ke Model Game Interaktif'"
                                >
                                    <Gamepad2 v-if="exam.display_mode === 'game'" class="w-3.5 h-3.5 text-amber-600" />
                                    <Building2 v-else class="w-3.5 h-3.5 text-blue-600" />
                                    <span>{{ exam.display_mode === 'game' ? '🎮 Model Game' : '🏛️ Model Resmi JFT' }}</span>
                                </button>
                            </div>
                            <!-- Jadwal Akses Jika Diset -->
                            <div v-if="exam.start_time || exam.end_time" class="flex items-center justify-between text-[11px] pt-1 text-slate-600 border-t border-slate-100">
                                <span class="text-slate-500 flex items-center gap-1">
                                    <Calendar class="w-3 h-3 text-slate-400" />
                                    <span>{{ isJapanese ? '実施期間' : 'Jadwal' }}:</span>
                                </span>
                                <span class="font-mono text-slate-800 font-bold">
                                    {{ formatScheduleDate(exam.start_time) }} - {{ formatScheduleDate(exam.end_time) }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Keterangan Siswa -->
                        <div class="mt-3 p-2.5 rounded-xl text-[11px] font-medium border"
                            :class="exam.is_published ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-600'">
                            <span v-if="exam.is_published" class="flex items-center gap-1.5">
                                <CheckCircle2 class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                <span>{{ isJapanese ? '実習生画面に表示中（受験可能）' : 'Tampil di portal siswa & siap dikerjakan' }}</span>
                            </span>
                            <span v-else class="flex items-center gap-1.5">
                                <EyeOff class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                <span>{{ isJapanese ? '非公開：実習生画面には表示されません' : 'Disembunyikan: Siswa belum dapat melihat ujian ini' }}</span>
                            </span>
                        </div>

                        <!-- Anti-Cheat Live Monitoring Button -->
                        <button 
                            @click="openMonitoringModal(exam)"
                            class="w-full mt-3 py-2.5 px-3.5 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-950 text-white text-xs font-bold hover:from-slate-800 hover:to-slate-900 transition-all flex items-center justify-between shadow-sm cursor-pointer group"
                        >
                            <span class="flex items-center gap-2">
                                <ShieldAlert class="w-4 h-4 text-amber-400 group-hover:scale-110 transition-transform" />
                                <span>{{ isJapanese ? '🛡️ 受験者監視・不正検知' : '🛡️ Pantau Peserta & Pelanggaran' }}</span>
                            </span>
                            
                            <span v-if="exam.sessions?.some(s => s.violation_count > 0 || s.is_disqualified)" 
                                class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black animate-pulse flex items-center gap-1">
                                <span>⚠️</span>
                                <span>{{ exam.sessions.filter(s => s.violation_count > 0 || s.is_disqualified).length }} Curang</span>
                            </span>
                            <span v-else class="text-[11px] text-slate-400 font-semibold">
                                {{ exam.sessions?.length || 0 }} {{ isJapanese ? '名受験' : 'Peserta' }}
                            </span>
                        </button>
                    </div>

                    <!-- Actions (Kelola Soal, Preview Ujian, Edit, Hapus) -->
                    <div class="flex items-center justify-between gap-2 pt-4 mt-4 border-t border-slate-100 flex-wrap">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <!-- Tombol Kelola / Tulis Soal -->
                            <a 
                                :href="exam.question_bank_id ? route('sensei.questions.manage-package', exam.question_bank_id) : route('sensei.questions.index')"
                                class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer border border-blue-200/70 shadow-2xs active:scale-[0.98]"
                                title="Buka lembar kerja penyusunan butir soal pada Master Bank Soal"
                            >
                                <FileText class="w-3.5 h-3.5" />
                                <span>{{ isJapanese ? '問題管理' : 'Kelola Soal' }}</span>
                                <span class="px-1.5 py-0.2 rounded-full bg-blue-200 group-hover:bg-white text-blue-900 text-[10px] font-black">
                                    {{ exam.questions_count || exam.questions?.length || 0 }}
                                </span>
                            </a>

                            <!-- Tombol Preview Ujian Siswa (Buka di Tab Baru) -->
                            <a 
                                :href="route('sensei.exams.preview', exam.id)"
                                target="_blank"
                                class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer border border-emerald-200/70 shadow-2xs active:scale-[0.98]"
                                title="Buka simulasi pengerjaan ujian siswa di tab baru"
                            >
                                <Eye class="w-3.5 h-3.5" />
                                <span>{{ isJapanese ? '試験' : 'Preview' }}</span>
                            </a>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <button 
                                @click="openEditModal(exam)"
                                class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer active:scale-[0.98]"
                                title="Ubah parameter & pengaturan paket ujian"
                            >
                                <Edit class="w-3.5 h-3.5" />
                                <span>{{ isJapanese ? '編集' : 'Edit' }}</span>
                            </button>
                            <button 
                                @click="deleteExam(exam)"
                                class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer active:scale-[0.98]"
                                title="Hapus paket ujian"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                                <span>{{ isJapanese ? '削除' : 'Hapus' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State (No search results or no exams) -->
            <div v-if="filteredExams.length === 0" class="bg-white p-12 rounded-3xl border border-slate-200 text-center">
                <Clock class="w-12 h-12 text-slate-300 mx-auto mb-3" />
                <h3 class="text-base font-black text-slate-900">
                    {{ exams.length === 0 
                        ? (isJapanese ? 'CBT試験データがありません' : 'Belum Ada Paket Ujian CBT') 
                        : (isJapanese ? '一致する試験が見つかりません' : 'Tidak Ada Ujian yang Cocok') 
                    }}
                </h3>
                <p class="text-xs text-slate-600 font-medium mt-1">
                    {{ exams.length === 0 
                        ? (isJapanese ? '「新規CBT試験を作成」ボタンから試験を作成してください。' : 'Silakan klik tombol di atas untuk membuat paket ujian CBT baru.') 
                        : (isJapanese ? '検索ワードやフィルターの条件を変更してください。' : 'Coba ubah kata kunci pencarian atau pilihan filter di atas.') 
                    }}
                </p>
                <button 
                    v-if="exams.length > 0" 
                    @click="searchQuery = ''; selectedStatusFilter = 'all'; selectedBatchFilter = 'all'" 
                    class="mt-3 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer"
                >
                    {{ isJapanese ? 'フィルターをリセット' : 'Reset Pencarian' }}
                </button>
            </div>
        </div>

        <!-- ================= MODAL CREATE / EDIT CBT EXAM ================= -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full p-6 sm:p-8 max-h-[92vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-950 flex items-center gap-2">
                            <Clock class="w-5 h-5 text-japan-red" />
                            <span>{{ isEditing ? (isJapanese ? 'CBT試験の編集' : 'Edit Paket Ujian CBT') : (isJapanese ? '新規CBT試験の作成' : 'Buat Paket Ujian CBT Baru') }}</span>
                        </h3>
                        <p v-if="isEditing" class="text-xs text-slate-500 font-mono mt-0.5">
                            Kode: {{ form.code }}
                        </p>
                    </div>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Sumber Paket Bank Soal Master -->
                    <div class="p-3.5 rounded-2xl border border-indigo-100 bg-indigo-50/50">
                        <label class="flex items-center justify-between text-xs font-black text-indigo-950 mb-1">
                            <span class="flex items-center gap-1.5">
                                <BookOpenCheck class="w-3.5 h-3.5 text-indigo-600" />
                                <span>{{ isJapanese ? '連動する問題バンクパッケージ' : 'Pilih Sumber Paket Bank Soal' }}</span>
                            </span>
                            <span class="text-[10px] text-indigo-600 font-bold bg-white px-2 py-0.5 rounded-md border border-indigo-200">Arsip Permanen</span>
                        </label>
                        <select 
                            v-model="form.question_bank_id" 
                            @change="onQuestionBankChange"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-indigo-200 text-xs sm:text-sm focus:ring-2 focus:ring-indigo-600 focus:outline-none font-bold bg-white cursor-pointer"
                        >
                            <option :value="null">-- Tanpa Bank Soal (Jadwal Ujian Baru Kosong) --</option>
                            <option v-for="qb in questionBanks" :key="qb.id" :value="qb.id">
                                [{{ qb.level }}] {{ qb.title }} ({{ qb.questions_count || 0 }} Butir Soal · {{ qb.subject?.name || 'Mapel' }})
                            </option>
                        </select>
                        <p class="text-[10px] text-slate-500 mt-1">
                            Memilih paket bank soal akan otomatis mengisikan mapel, durasi, KKM, dan butir soal ke jadwal ujian ini.
                        </p>
                    </div>

                    <!-- 2. Mata Pelajaran & Nama Ujian -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1">
                                Mata Pelajaran *
                            </label>
                            <select 
                                v-model="form.subject_id" 
                                required 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-japan-red focus:outline-none font-bold"
                            >
                                <option :value="null" disabled>-- Pilih Mapel --</option>
                                <option v-for="sub in subjects" :key="sub.id" :value="sub.id">
                                    {{ sub.name }} ({{ sub.code }})
                                </option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1">
                                Nama / Judul Ujian CBT *
                            </label>
                            <input 
                                type="text" 
                                v-model="form.title" 
                                required 
                                placeholder="Contoh: Tryout Resmi Kelulusan N4 Angkatan 12" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-japan-red focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- 3. Target Angkatan / Kelas Siswa -->
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1">
                            👥 Target Angkatan / Kelas Peserta
                        </label>
                        <select v-model="form.batch_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-japan-red focus:outline-none">
                            <option :value="null">-- Semua Siswa (Terbuka Umum) --</option>
                            <option v-for="b in batches" :key="b.id" :value="b.id">
                                {{ b.name }} ({{ b.code }})
                            </option>
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Pilih kelas yang wajib mengikuti ujian ini. Jika dibiarkan terbuka umum, seluruh siswa aktif dapat melihat dan mengerjakan ujian.</p>
                    </div>

                    <!-- 4. Durasi & Nilai KKM Kelulusan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1">
                                ⏱️ Durasi Pengerjaan (Menit) *
                            </label>
                            <input 
                                type="number" 
                                v-model.number="form.duration_minutes" 
                                required 
                                min="5" 
                                max="300"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold font-mono focus:ring-2 focus:ring-japan-red focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1">
                                🎯 Nilai Minimum Kelulusan (KKM) *
                            </label>
                            <input 
                                type="number" 
                                v-model.number="form.passing_score" 
                                required 
                                min="1" 
                                max="1000"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold font-mono focus:ring-2 focus:ring-japan-red focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- 5. Jadwal Akses Ujian (Opsional) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center gap-1">
                                <Calendar class="w-3.5 h-3.5 text-slate-500" />
                                <span>{{ isJapanese ? '開始日時（任意）' : 'Waktu Mulai Akses (Opsional)' }}</span>
                            </label>
                            <input 
                                type="datetime-local" 
                                v-model="form.start_time" 
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-japan-red focus:outline-none bg-white text-slate-800"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center gap-1">
                                <Calendar class="w-3.5 h-3.5 text-slate-500" />
                                <span>{{ isJapanese ? '終了日時（任意）' : 'Waktu Selesai Akses (Opsional)' }}</span>
                            </label>
                            <input 
                                type="datetime-local" 
                                v-model="form.end_time" 
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-japan-red focus:outline-none bg-white text-slate-800"
                            />
                        </div>
                        <p class="text-[10px] text-slate-400 col-span-1 sm:col-span-2">
                            Kosongkan jika ujian dapat diakses kapan saja tanpa batasan tanggal/jam otomatis.
                        </p>
                    </div>

                    <!-- 5. Pilihan Model Ujian CBT (Resmi JFT vs Game Interaktif) -->
                    <div class="space-y-2 p-3.5 rounded-2xl border border-slate-200 bg-slate-50/70">
                        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">
                            🎮 / 🏛️ {{ isJapanese ? '試験表示モード' : 'Pilihan Model Tampilan Ujian CBT' }} *
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Model 1: Resmi JFT / JLPT -->
                            <div 
                                @click="form.display_mode = 'formal'"
                                class="p-3 rounded-xl border-2 transition-all cursor-pointer flex items-start gap-3 bg-white"
                                :class="form.display_mode === 'formal' ? 'border-blue-600 bg-blue-50/40 shadow-xs' : 'border-slate-200 hover:border-slate-300 opacity-80'"
                            >
                                <input 
                                    type="radio" 
                                    name="display_mode" 
                                    value="formal" 
                                    v-model="form.display_mode" 
                                    class="mt-0.5 text-blue-600 focus:ring-blue-500" 
                                />
                                <div>
                                    <span class="text-xs font-black text-slate-900 block flex items-center gap-1.5">
                                        <Building2 class="w-3.5 h-3.5 text-blue-600" />
                                        <span>{{ isJapanese ? '公式JFT・JLPTモード' : 'Model Resmi JFT / JLPT' }}</span>
                                    </span>
                                    <span class="text-[10px] text-slate-500 leading-tight block mt-0.5">
                                        Layout split-screen standar ujian kelulusan resmi dengan pemutar audio listening ketat.
                                    </span>
                                </div>
                            </div>

                            <!-- Model 2: Game Interaktif -->
                            <div 
                                @click="form.display_mode = 'game'"
                                class="p-3 rounded-xl border-2 transition-all cursor-pointer flex items-start gap-3 bg-white"
                                :class="form.display_mode === 'game' ? 'border-amber-500 bg-amber-50/40 shadow-xs' : 'border-slate-200 hover:border-slate-300 opacity-80'"
                            >
                                <input 
                                    type="radio" 
                                    name="display_mode" 
                                    value="game" 
                                    v-model="form.display_mode" 
                                    class="mt-0.5 text-amber-600 focus:ring-amber-500" 
                                />
                                <div>
                                    <span class="text-xs font-black text-slate-900 block flex items-center gap-1.5">
                                        <Gamepad2 class="w-3.5 h-3.5 text-amber-600" />
                                        <span>{{ isJapanese ? 'ゲーム・RPGモード' : 'Model Game Interaktif' }}</span>
                                    </span>
                                    <span class="text-[10px] text-slate-500 leading-tight block mt-0.5">
                                        Visual interaktif RPG, animasi skor langsung, efek suara, dan atmosfer santai untuk latihan.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Ringkasan Soal dalam Paket (Jika Edit) -->
                    <div v-if="isEditing" class="p-4 rounded-2xl border border-blue-200 bg-blue-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                                📝
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-slate-900">
                                    Butir Soal Ujian Terpasang
                                </h4>
                                <p class="text-[11px] text-slate-600 font-medium mt-0.5">
                                    Saat ini terpasang: 
                                    <span class="font-bold text-blue-700 font-mono">
                                        {{ currentEditingExam?.questions_count || currentEditingExam?.questions?.length || 0 }} Butir Soal
                                    </span>.
                                </p>
                            </div>
                        </div>

                        <a 
                            :href="currentEditingExam?.question_bank_id ? route('sensei.questions.manage-package', currentEditingExam.question_bank_id) : route('sensei.questions.index')"
                            target="_blank"
                            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition-colors flex items-center justify-center gap-1.5 shrink-0 cursor-pointer"
                        >
                            <FileText class="w-3.5 h-3.5" />
                            <span>Kelola & Tulis Soal</span>
                            <ExternalLink class="w-3 h-3 ml-0.5" />
                        </a>
                    </div>

                    <!-- 6. Saklar Aktifkan Ujian (ON / OFF) -->
                    <div class="p-3.5 rounded-2xl border flex items-center justify-between transition-colors cursor-pointer"
                        :class="form.is_published ? 'bg-emerald-50/70 border-emerald-300' : 'bg-slate-50 border-slate-200'"
                        @click="form.is_published = !form.is_published"
                    >
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full shrink-0" :class="form.is_published ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                            <div>
                                <span class="text-xs text-slate-900 font-black block">
                                    {{ form.is_published ? 'Status Ujian: ON (Siswa Dapat Mengakses Ujian)' : 'Status Ujian: OFF (Disembunyikan / Draft)' }}
                                </span>
                                <span class="text-[10px] text-slate-500">
                                    Aktifkan saklar ini agar ujian muncul di jadwal CBT siswa yang ditugaskan.
                                </span>
                            </div>
                        </div>
                        <input type="checkbox" v-model="form.is_published" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 pointer-events-none" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                            {{ isJapanese ? 'キャンセル' : 'Batal' }}
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-6 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer disabled:opacity-50">
                            {{ form.processing ? (isJapanese ? '保存中...' : 'Menyimpan...') : (isJapanese ? 'CBT試験を保存' : 'Simpan Paket CBT') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL ANTI-CHEAT LIVE PROCTORING & MONITORING ================= -->
        <div v-if="showMonitoringModal" class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-6 bg-slate-950/70 backdrop-blur-md animate-fade-in">
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-2xl max-w-5xl w-full flex flex-col max-h-[95vh] sm:max-h-[92vh] overflow-hidden">
                <!-- Modal Top Header -->
                <div class="p-4 sm:p-6 border-b border-slate-100 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white flex items-start justify-between gap-3 shrink-0">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-amber-500/20 text-amber-300 text-[11px] sm:text-xs font-bold mb-1.5 sm:mb-2 border border-amber-500/30">
                            <ShieldAlert class="w-3.5 h-3.5" />
                            <span>{{ isJapanese ? 'リアルタイム不正監視システム' : 'Sistem Pengawas CBT Real-Time' }}</span>
                        </div>
                        <h2 class="text-base sm:text-xl font-black tracking-tight flex flex-wrap items-center gap-2">
                            <span>{{ selectedExamForMonitoring?.title }}</span>
                            <span class="text-[10px] sm:text-xs font-mono px-2 py-0.5 rounded bg-white/10 text-slate-300 border border-white/10">
                                {{ selectedExamForMonitoring?.code }}
                            </span>
                        </h2>
                        <p class="text-[11px] sm:text-xs text-slate-300 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span>⏱️ {{ selectedExamForMonitoring?.duration_minutes }} {{ isJapanese ? '分' : 'Menit' }}</span>
                            <span>🎯 Pass: {{ selectedExamForMonitoring?.passing_score }}/{{ selectedExamForMonitoring?.max_score }}</span>
                            <span>👥 {{ selectedExamForMonitoring?.batch?.name || (isJapanese ? '全期生対象' : 'Semua Siswa') }}</span>
                        </p>
                    </div>

                    <button @click="showMonitoringModal = false" class="text-slate-400 hover:text-white p-1.5 sm:p-2 rounded-xl bg-white/10 hover:bg-white/20 transition-colors cursor-pointer shrink-0">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- 4 Proctoring Metric Summary Cards -->
                <div class="p-3 sm:p-6 bg-slate-50 border-b border-slate-200/80 grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 shrink-0">
                    <!-- 1. Total Peserta -->
                    <div class="bg-white p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-slate-200 shadow-2xs">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider block">{{ isJapanese ? '総受験者' : 'Total Peserta' }}</span>
                        <span class="text-lg sm:text-2xl font-black text-slate-900">{{ monitoringSessions.length }}</span>
                        <span class="text-[10px] sm:text-[11px] text-slate-500 font-medium block">{{ isJapanese ? '名が受験' : 'Siswa Mengikuti' }}</span>
                    </div>

                    <!-- 2. Tertib / 0 Pelanggaran -->
                    <div class="bg-white p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-emerald-200 shadow-2xs">
                        <span class="text-[10px] sm:text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">🟢 {{ isJapanese ? '正常' : 'Tertib' }}</span>
                        <span class="text-lg sm:text-2xl font-black text-emerald-700">{{ honestSessionsCount }}</span>
                        <span class="text-[10px] sm:text-[11px] text-emerald-600 font-medium block">0x {{ isJapanese ? '違反なし' : 'Keluar Layar' }}</span>
                    </div>

                    <!-- 3. Peringatan (1-2x) -->
                    <div class="bg-white p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-amber-200 shadow-2xs">
                        <span class="text-[10px] sm:text-[11px] font-bold text-amber-700 uppercase tracking-wider block">🟡 {{ isJapanese ? '警告' : 'Peringatan' }}</span>
                        <span class="text-lg sm:text-2xl font-black text-amber-700">{{ warningSessionsCount }}</span>
                        <span class="text-[10px] sm:text-[11px] text-amber-600 font-medium block">1-2x {{ isJapanese ? '画面離脱' : 'Keluar Tab' }}</span>
                    </div>

                    <!-- 4. Pelanggaran Berat / Didiskualifikasi -->
                    <div class="bg-white p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-rose-200 shadow-2xs">
                        <span class="text-[10px] sm:text-[11px] font-bold text-rose-700 uppercase tracking-wider block">🔴 {{ isJapanese ? '失格' : 'Diskualifikasi' }}</span>
                        <span class="text-lg sm:text-2xl font-black text-rose-700">{{ criticalSessionsCount }}</span>
                        <span class="text-[10px] sm:text-[11px] text-rose-600 font-medium block">≥3x / {{ isJapanese ? '失格処分' : 'Didiskualifikasi' }}</span>
                    </div>
                </div>

                <!-- Filter & Search Controls in Monitoring -->
                <div class="px-4 sm:px-6 py-2.5 sm:py-3 bg-white border-b border-slate-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 sm:gap-3 shrink-0">
                    <div class="flex items-center gap-2 flex-1">
                        <Search class="w-4 h-4 text-slate-400 shrink-0" />
                        <input 
                            type="text" 
                            v-model="monitoringSearch" 
                            :placeholder="isJapanese ? '生徒名またはメールを検索...' : 'Cari nama atau email siswa...'" 
                            class="w-full text-xs px-3 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900"
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <select v-model="monitoringFilter" class="w-full sm:w-auto text-xs px-3 py-1.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-900 font-bold text-slate-700">
                            <option value="all">{{ isJapanese ? '全ステータス' : 'Semua Status' }} ({{ monitoringSessions.length }})</option>
                            <option value="violated">{{ isJapanese ? '不正警告者のみ' : 'Hanya Siswa Terindikasi Curang' }}</option>
                            <option value="honest">{{ isJapanese ? '正常受験者のみ' : 'Hanya Siswa Tertib' }}</option>
                            <option value="disqualified">{{ isJapanese ? '失格処分者のみ' : 'Hanya Didiskualifikasi' }}</option>
                        </select>
                    </div>
                </div>

                <!-- Examinee List Table (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-3 sm:p-6 space-y-3">
                    <div v-if="filteredMonitoringSessions.length > 0" class="space-y-3">
                        <div 
                            v-for="session in filteredMonitoringSessions" 
                            :key="session.id" 
                            class="p-3.5 sm:p-5 rounded-2xl border transition-all"
                            :class="session.is_disqualified 
                                ? 'bg-rose-50/50 border-rose-300' 
                                : session.violation_count > 0 
                                    ? 'bg-amber-50/40 border-amber-200' 
                                    : 'bg-white border-slate-200'"
                        >
                            <!-- Student Header & Integrity Row -->
                            <div class="flex flex-col gap-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl font-black text-xs sm:text-sm flex items-center justify-center text-white shrink-0"
                                            :class="session.is_disqualified ? 'bg-rose-600' : session.violation_count > 0 ? 'bg-amber-600' : 'bg-slate-900'">
                                            {{ session.user?.name?.charAt(0) || 'S' }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <h4 class="font-bold text-xs sm:text-sm text-slate-900 truncate">{{ session.user?.name }}</h4>
                                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                                    {{ session.user?.batches?.[0]?.name || 'Reguler' }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5">
                                                <span class="truncate">{{ session.user?.email }}</span>
                                                <span>•</span>
                                                <span>{{ isJapanese ? '得点' : 'Skor' }}: <strong>{{ session.total_score }}</strong></span>
                                                <span>•</span>
                                                <span class="capitalize font-semibold text-slate-700">
                                                    {{ session.status === 'in_progress' ? (isJapanese ? '受験中' : 'Sedang Ujian') : session.status === 'submitted' ? (isJapanese ? '提出済' : 'Selesai') : session.status }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Anti-Cheat Badge & Quick Actions -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-200/60">
                                    <div class="px-2.5 py-1 rounded-xl border text-xs font-black flex items-center gap-1.5 self-start"
                                        :class="session.is_disqualified 
                                            ? 'bg-rose-100 text-rose-800 border-rose-300' 
                                            : session.violation_count >= 3 
                                                ? 'bg-rose-100 text-rose-800 border-rose-300' 
                                                : session.violation_count > 0 
                                                    ? 'bg-amber-100 text-amber-900 border-amber-300' 
                                                    : 'bg-emerald-50 text-emerald-800 border-emerald-200'">
                                        <ShieldAlert v-if="session.violation_count > 0 || session.is_disqualified" class="w-3.5 h-3.5 text-rose-600 shrink-0" />
                                        <ShieldCheck v-else class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                        
                                        <span>
                                            {{ session.is_disqualified 
                                                ? (isJapanese ? '⛔ 失格処分' : '⛔ DIDISKUALIFIKASI') 
                                                : session.violation_count === 0 
                                                    ? (isJapanese ? '🟢 正常 (違反なし)' : '🟢 Tertib (0 Pelanggaran)') 
                                                    : (isJapanese ? `⚠️ ${session.violation_count}回 画面離脱警告` : `⚠️ ${session.violation_count}x Peringatan Keluar Tab`) 
                                            }}
                                        </span>
                                    </div>

                                    <!-- Action Buttons Group -->
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <button 
                                            type="button" 
                                            @click="toggleSessionLogs(session.id)"
                                            class="flex-1 sm:flex-none justify-center px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors flex items-center gap-1 cursor-pointer"
                                        >
                                            <span>{{ isJapanese ? 'ログ履歴' : 'Log Waktu' }} ({{ session.violation_logs?.length || 0 }})</span>
                                            <ChevronDown v-if="expandedSessionId !== session.id" class="w-3.5 h-3.5" />
                                            <ChevronUp v-else class="w-3.5 h-3.5" />
                                        </button>

                                        <button 
                                            v-if="!session.is_disqualified"
                                            type="button" 
                                            @click="disqualifyStudent(session)"
                                            class="flex-1 sm:flex-none justify-center px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1 cursor-pointer"
                                        >
                                            <UserX class="w-3.5 h-3.5" />
                                            <span>{{ isJapanese ? '失格処分' : 'Diskualifikasi' }}</span>
                                        </button>

                                        <button 
                                            v-else
                                            type="button" 
                                            @click="resetStudentViolations(session)"
                                            class="flex-1 sm:flex-none justify-center px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1 cursor-pointer"
                                        >
                                            <RotateCcw class="w-3.5 h-3.5" />
                                            <span>{{ isJapanese ? '処分解除' : 'Pulihkan / Reset' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Expanded Timeline of Cheating / Tab Switching Logs -->
                            <div v-if="expandedSessionId === session.id" class="mt-3 pt-3 border-t border-slate-200/80 space-y-2 animate-fade-in">
                                <span class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                                    <Activity class="w-4 h-4 text-slate-700 shrink-0" />
                                    <span>{{ isJapanese ? '違反ログタイムライン:' : 'Rincian Jejak Waktu Pelanggaran (Detik & Jam):' }}</span>
                                </span>

                                <div v-if="session.violation_logs && session.violation_logs.length > 0" class="space-y-2">
                                    <div 
                                        v-for="(log, lIdx) in session.violation_logs" 
                                        :key="lIdx"
                                        class="p-2.5 rounded-xl bg-white border border-slate-200 text-xs flex items-start justify-between gap-3 font-mono"
                                    >
                                        <div class="flex items-start gap-2">
                                            <span class="text-rose-600 font-bold">⚠️ [{{ log.time_formatted || log.timestamp?.substring(11, 19) }}]</span>
                                            <span class="text-slate-800 font-bold font-sans">{{ log.title || log.message }}</span>
                                        </div>
                                        <span v-if="log.duration_seconds" class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-sans">
                                            {{ isJapanese ? `離脱時間: ${log.duration_seconds}秒` : `Durasi: ${log.duration_seconds} detik` }}
                                        </span>
                                    </div>
                                </div>

                                <div v-else class="text-xs text-slate-500 italic py-2">
                                    {{ isJapanese ? '違反ログはありません。正常に受験しています。' : 'Tidak ada catatan pelanggaran. Siswa mengerjakan ujian secara tertib tanpa berpindah tab.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clean Empty State if no examinees -->
                    <div v-else class="py-16 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <ShieldAlert class="w-12 h-12 text-slate-300 mx-auto mb-2" />
                        <h4 class="text-sm font-bold text-slate-700">{{ isJapanese ? '受験者がまだいません' : 'Belum Ada Peserta yang Mengerjakan Ujian Ini' }}</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                            {{ isJapanese ? '実習生がCBT試験を開始すると、リアルタイム監視データがここに表示されます。' : 'Ketika siswa memulai ujian melalui antarmuka CBT, seluruh aktivitas layar, tab, dan status integritas akan tampil di sini secara real-time.' }}
                        </p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
                    <span class="text-xs text-slate-500 flex items-center gap-1.5">
                        💡 <em>{{ isJapanese ? '指導教員は失格処分や警告リセットを行う権限を持ちます。' : 'Sensei memiliki kendali penuh untuk mendiskualifikasi atau mereset peringatan ujian.' }}</em>
                    </span>

                    <button @click="showMonitoringModal = false" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold transition-colors cursor-pointer">
                        {{ isJapanese ? '閉じる' : 'Tutup Panel Pengawas' }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { notifySuccess, notifyError, confirmDialog } from '@/Utils/alert';
import { 
    Clock, 
    PlusCircle, 
    Edit, 
    Trash2, 
    X,
    ShieldAlert,
    ShieldCheck,
    Search,
    UserX,
    RotateCcw,
    ChevronDown,
    ChevronUp,
    Activity,
    CheckCircle2,
    EyeOff,
    Gamepad2,
    Building2,
    Eye,
    BookOpen,
    BookOpenCheck,
    FileText,
    ExternalLink,
    Calendar
} from 'lucide-vue-next';

const props = defineProps({
    exams: Array,
    subjects: Array,
    questionBanks: Array,
    batches: Array,
    levels: Array,
});

const { isJapanese } = useLang();

const showModal = ref(false);
const isEditing = ref(false);
const editingExamId = ref(null);

// Anti-Cheat Monitoring Modal State
const showMonitoringModal = ref(false);
const selectedExamForMonitoring = ref(null);
const expandedSessionId = ref(null);
const monitoringSearch = ref('');
const monitoringFilter = ref('all');

const monitoringSessions = computed(() => {
    return selectedExamForMonitoring.value?.sessions || [];
});

const honestSessionsCount = computed(() => {
    return monitoringSessions.value.filter(s => s.violation_count === 0 && !s.is_disqualified).length;
});

const warningSessionsCount = computed(() => {
    return monitoringSessions.value.filter(s => s.violation_count > 0 && s.violation_count < 3 && !s.is_disqualified).length;
});

const criticalSessionsCount = computed(() => {
    return monitoringSessions.value.filter(s => s.violation_count >= 3 || s.is_disqualified).length;
});

const filteredMonitoringSessions = computed(() => {
    let list = monitoringSessions.value;

    if (monitoringSearch.value) {
        const q = monitoringSearch.value.toLowerCase();
        list = list.filter(s => s.user?.name?.toLowerCase().includes(q) || s.user?.email?.toLowerCase().includes(q));
    }

    if (monitoringFilter.value === 'violated') {
        list = list.filter(s => s.violation_count > 0 || s.is_disqualified);
    } else if (monitoringFilter.value === 'honest') {
        list = list.filter(s => s.violation_count === 0 && !s.is_disqualified);
    } else if (monitoringFilter.value === 'disqualified') {
        list = list.filter(s => s.is_disqualified);
    }

    return list;
});

const openMonitoringModal = (exam) => {
    selectedExamForMonitoring.value = exam;
    expandedSessionId.value = null;
    monitoringSearch.value = '';
    monitoringFilter.value = 'all';
    showMonitoringModal.value = true;
};

const toggleSessionLogs = (sessionId) => {
    expandedSessionId.value = expandedSessionId.value === sessionId ? null : sessionId;
};

const disqualifyStudent = (session) => {
    confirmDialog(
        `Diskualifikasi ${session.user?.name}?`,
        'Siswa akan dinyatakan gagal/didiskualifikasi dari ujian ini karena terindikasi melakukan pelanggaran.'
    ).then((result) => {
        if (result.isConfirmed) {
            router.post(route('sensei.exams.disqualify-session', session.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    session.is_disqualified = true;
                    session.status = 'cancelled';
                    notifySuccess('Didiskualifikasi', `Peserta ${session.user?.name} telah didiskualifikasi.`);
                },
            });
        }
    });
};

const resetStudentViolations = (session) => {
    confirmDialog(
        `Pulihkan / Reset Peringatan ${session.user?.name}?`,
        'Jumlah pelanggaran siswa akan direset ke 0 dan status diskualifikasi dibatalkan.'
    ).then((result) => {
        if (result.isConfirmed) {
            router.post(route('sensei.exams.reset-violations', session.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    session.is_disqualified = false;
                    session.violation_count = 0;
                    notifySuccess('Dipulihkan', `Peringatan untuk ${session.user?.name} berhasil direset.`);
                },
            });
        }
    });
};

// Main Exam Grid Search & Filter State
const searchQuery = ref('');
const selectedStatusFilter = ref('all');
const selectedBatchFilter = ref('all');

const filteredExams = computed(() => {
    let list = props.exams || [];

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase().trim();
        list = list.filter(e => 
            e.title?.toLowerCase().includes(q) || 
            e.code?.toLowerCase().includes(q) ||
            e.subject?.name?.toLowerCase().includes(q)
        );
    }

    if (selectedStatusFilter.value === 'published') {
        list = list.filter(e => Boolean(e.is_published));
    } else if (selectedStatusFilter.value === 'draft') {
        list = list.filter(e => !e.is_published);
    }

    if (selectedBatchFilter.value !== 'all') {
        list = list.filter(e => e.batch_id === selectedBatchFilter.value);
    }

    return list;
});

const formatScheduleDate = (dateStr) => {
    if (!dateStr) return '-';
    try {
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch {
        return dateStr;
    }
};

const toDateTimeLocal = (dateStr) => {
    if (!dateStr) return '';
    try {
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return '';
        const pad = (n) => String(n).padStart(2, '0');
        const year = d.getFullYear();
        const month = pad(d.getMonth() + 1);
        const day = pad(d.getDate());
        const hours = pad(d.getHours());
        const minutes = pad(d.getMinutes());
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    } catch {
        return '';
    }
};

const form = useForm({
    title: '',
    code: '',
    question_bank_id: null,
    subject_id: null,
    level: 'N4',
    exam_type: 'jlpt_simulation',
    duration_minutes: 60,
    passing_score: 90,
    max_score: 180,
    batch_id: null,
    start_time: '',
    end_time: '',
    is_published: false,
    display_mode: 'formal',
    description: '',
});

const onQuestionBankChange = () => {
    if (!form.question_bank_id) return;
    const selectedBank = props.questionBanks?.find(b => b.id === form.question_bank_id);
    if (selectedBank) {
        form.subject_id = selectedBank.subject_id;
        form.level = selectedBank.level;
        form.duration_minutes = selectedBank.duration_minutes;
        form.passing_score = selectedBank.passing_score;
        form.max_score = selectedBank.max_score || 180;
        if (selectedBank.display_mode) {
            form.display_mode = selectedBank.display_mode;
        }
        if (!isEditing.value || !form.title) {
            form.title = selectedBank.title;
        }
    }
};

const openCreateModal = () => {
    isEditing.value = false;
    editingExamId.value = null;
    form.reset();
    form.question_bank_id = props.questionBanks?.[0]?.id || null;
    if (props.questionBanks?.[0]) {
        const b = props.questionBanks[0];
        form.title = b.title;
        form.subject_id = b.subject_id;
        form.level = b.level;
        form.duration_minutes = b.duration_minutes;
        form.passing_score = b.passing_score;
        form.max_score = b.max_score || 180;
        form.display_mode = b.display_mode || 'formal';
    } else {
        form.title = '';
        form.subject_id = props.subjects?.[0]?.id || null;
        form.level = props.levels?.[0]?.code || 'N4';
        form.duration_minutes = 60;
        form.passing_score = 90;
        form.max_score = 180;
        form.display_mode = 'formal';
    }
    form.code = 'CBT-N4-' + Math.random().toString(36).substring(2, 7).toUpperCase();
    form.exam_type = 'jlpt_simulation';
    form.batch_id = null;
    form.start_time = '';
    form.end_time = '';
    form.is_published = false;
    form.description = '';
    showModal.value = true;
};

const openEditModal = (exam) => {
    isEditing.value = true;
    editingExamId.value = exam.id;
    form.question_bank_id = exam.question_bank_id || null;
    form.title = exam.title;
    form.code = exam.code;
    form.subject_id = exam.subject_id || (exam.question_bank?.subject_id ?? props.subjects?.[0]?.id ?? null);
    form.level = exam.level;
    form.exam_type = exam.exam_type;
    form.duration_minutes = exam.duration_minutes;
    form.passing_score = exam.passing_score;
    form.max_score = exam.max_score;
    form.batch_id = exam.batch_id || null;
    form.start_time = toDateTimeLocal(exam.start_time);
    form.end_time = toDateTimeLocal(exam.end_time);
    form.is_published = Boolean(exam.is_published);
    form.display_mode = exam.display_mode || 'formal';
    form.description = exam.description || '';
    showModal.value = true;
};

const togglePublish = (exam) => {
    router.post(route('sensei.exams.toggle-publish', exam.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            notifySuccess(
                isJapanese.value ? 'ステータス更新' : 'Status Berhasil Diubah', 
                isJapanese.value 
                    ? `試験「${exam.title}」の公開状態を変更しました。` 
                    : `Status ujian "${exam.title}" berhasil diubah.`
            );
        },
    });
};

const toggleExamMode = (exam) => {
    router.post(route('sensei.exams.toggle-mode', exam.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            const nextMode = exam.display_mode === 'game' ? 'formal' : 'game';
            exam.display_mode = nextMode;
            notifySuccess(
                isJapanese.value ? 'モード切替' : 'Mode Berhasil Diubah', 
                isJapanese.value 
                    ? `試験「${exam.title}」の表示モードを${nextMode === 'game' ? 'ゲームモード' : '公式JFTモード'}に変更しました。` 
                    : `Gaya tampilan "${exam.title}" diubah ke ${nextMode === 'game' ? '🎮 Mode Game' : '🏛️ Mode Formal'}.`
            );
        },
    });
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('sensei.exams.update', editingExamId.value), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '更新完了' : 'Berhasil', isJapanese.value ? 'CBT試験情報を更新しました。' : 'Paket Ujian CBT berhasil diperbarui.');
            },
        });
    } else {
        form.post(route('sensei.exams.store'), {
            onSuccess: () => {
                showModal.value = false;
                notifySuccess(isJapanese.value ? '登録完了' : 'Berhasil', isJapanese.value ? '新しいCBT試験を作成しました。' : 'Paket Ujian CBT baru berhasil dibuat.');
            },
        });
    }
};

const deleteExam = (exam) => {
    confirmDialog(
        isJapanese.value ? `CBT試験「${exam.title}」を削除しますか？` : `Hapus CBT ${exam.title}?`,
        isJapanese.value ? 'この操作は取り消せません。' : 'Paket ujian CBT ini akan dihapus dari sistem.'
    ).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('sensei.exams.destroy', exam.id), {
                preserveScroll: true,
                onSuccess: () => {
                    notifySuccess(isJapanese.value ? '削除完了' : 'Terhapus', isJapanese.value ? 'CBT試験を削除しました。' : 'Paket Ujian CBT berhasil dihapus.');
                },
                onError: () => {
                    notifyError(isJapanese.value ? 'エラー' : 'Gagal Menghapus', isJapanese.value ? '試験の削除に失敗しました。ページを再読み込みします。' : 'Gagal menghapus paket ujian. Memuat ulang data...');
                    router.reload();
                },
            });
        }
    });
};
</script>
