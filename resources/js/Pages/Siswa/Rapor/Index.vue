<template>
    <Head :title="isJapanese ? '成績表・習熟度 — マイポータル' : 'Rapor & Capaian Belajar — Siswa'" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-24 md:pb-16">

            <!-- ================= HEADER SECTION: PROFIL & TARGET KELULUSAN ================= -->
            <div class="bg-gradient-to-br from-white via-slate-50 to-emerald-50/25 border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-xs relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-emerald-600 via-teal-500 to-cyan-400 rounded-l-3xl"></div>

                <div class="pl-2 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center font-black text-lg shadow-md shadow-emerald-600/20 shrink-0">
                            {{ student.name.charAt(0) }}
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-black border border-emerald-200 font-jp mb-1">
                                <Award class="w-3.5 h-3.5" />
                                <span>{{ isJapanese ? '公式成績表・学習カルテ' : 'Buku Rapor & Capaian Akademik' }}</span>
                            </div>
                            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-snug">
                                {{ student.name }}
                            </h1>
                            <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-2 flex-wrap font-medium">
                                <span class="font-mono text-slate-600 font-bold">{{ student.nik || `SISWA-${student.id}` }}</span>
                                <span>•</span>
                                <span class="text-slate-700 font-jp">{{ student.batches?.[0]?.name || (isJapanese ? '未所属' : 'Belum Ada Angkatan') }}</span>
                                <span>•</span>
                                <span class="font-bold text-rose-600 font-mono">Target: {{ student.target_language_level || 'JLPT N4' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="self-start sm:self-auto shrink-0 font-jp">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>{{ isJapanese ? '実習生カルテ有効' : 'Trainee Aktif' }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ================= EMPTY STATE IF NO CBT SESSIONS ================= -->
            <div v-if="!student.exam_sessions || student.exam_sessions.length === 0" class="bg-white p-8 sm:p-12 rounded-3xl border border-slate-200/90 text-center shadow-xs">
                <Award class="w-12 h-12 text-slate-300 mx-auto mb-3" />
                <h3 class="text-base font-black text-slate-900 font-jp">
                    {{ isJapanese ? 'CBT受験履歴がありません' : 'Belum Ada Rekap Ujian CBT' }}
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto leading-relaxed font-jp">
                    {{ isJapanese 
                        ? 'まだCBT試験を受験していません。メニューの「CBT模擬試験」から試験を受験すると、評価指標別の到達度と成績表がここに集計されます。' 
                        : 'Anda belum pernah menyelesaikan ujian CBT. Silakan ikuti simulasi ujian dari menu CBT agar grafik capaian kompetensi dan rapor terisi otomatis.' 
                    }}
                </p>
            </div>

            <!-- ================= RAPOR CONTENT WHEN SESSIONS EXIST ================= -->
            <template v-else>
                <!-- 1. KARTU METRIK UTAMA (RESPONSIVE GRID) -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <!-- Rata-rata Skor -->
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '平均スコア' : 'Rata-rata Skor' }}
                        </span>
                        <div class="my-2">
                            <span class="text-2xl sm:text-3xl font-black text-slate-950 font-mono">
                                {{ averageScore }}
                            </span>
                            <span class="text-xs text-slate-400 font-mono"> pts</span>
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-emerald-600 font-jp">
                            {{ isJapanese ? '最高得点: ' : 'Skor Tertinggi: ' }}{{ highestScore }} pts
                        </span>
                    </div>

                    <!-- Akurasi Benar -->
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '全セッション正答率' : 'Akurasi Kumulatif' }}
                        </span>
                        <div class="my-2 flex items-center justify-between">
                            <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-mono">
                                {{ overallAccuracy }}%
                            </span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                🟢
                            </span>
                        </div>
                        <span class="text-[10px] sm:text-[11px] text-slate-500 font-jp">
                            {{ totalCorrectCount }}/{{ totalAnsweredCount }} {{ isJapanese ? '正解' : 'Soal Terjawab Benar' }}
                        </span>
                    </div>

                    <!-- Ketuntasan Indikator -->
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '目標習得率' : 'Ketuntasan Indikator' }}
                        </span>
                        <div class="my-2 flex items-center justify-between">
                            <span class="text-2xl sm:text-3xl font-black text-blue-600 font-mono">
                                {{ indicatorStats.masteredCount }}/{{ indicatorStats.list.length }}
                            </span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                🎯
                            </span>
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-jp" :class="indicatorStats.remedialCount > 0 ? 'text-rose-600 font-bold' : 'text-slate-500'">
                            {{ indicatorStats.remedialCount }} {{ isJapanese ? '要指導' : 'Perlu Remedial' }}
                        </span>
                    </div>

                    <!-- Total Selesai Ujian -->
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '受験回数' : 'Ujian Diselesaikan' }}
                        </span>
                        <div class="my-2 flex items-center justify-between">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">
                                {{ student.exam_sessions.length }}
                            </span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700">
                                ⏱️
                            </span>
                        </div>
                        <span class="text-[10px] sm:text-[11px] text-emerald-700 font-bold font-jp">
                            {{ passedExamsCount }} {{ isJapanese ? '回合格' : 'Kali Lulus Passing Grade' }}
                        </span>
                    </div>
                </div>

                <!-- 2. PILIH SESI UJIAN UNTUK ANALISIS MENDALAM -->
                <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <span class="text-xs font-bold text-slate-600 uppercase tracking-wider font-jp shrink-0">
                        {{ isJapanese ? '分析する試験セッション:' : 'Pilih Sesi Ujian untuk Dianalisis:' }}
                    </span>

                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:pb-0 w-full sm:w-auto">
                        <button
                            v-for="session in student.exam_sessions"
                            :key="session.id"
                            @click="selectedSessionId = session.id"
                            type="button"
                            class="px-3.5 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 cursor-pointer border font-jp flex items-center gap-2"
                            :class="activeSession?.id === session.id
                                ? 'bg-slate-900 text-white border-slate-900 shadow-xs'
                                : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'"
                        >
                            <span class="truncate max-w-[140px] sm:max-w-[200px]">{{ session.exam?.title || 'Ujian CBT' }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded font-mono font-bold"
                                :class="session.is_passed ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300'">
                                {{ session.total_score || session.score || 0 }} pts
                            </span>
                        </button>
                    </div>
                </div>

                <!-- 3. CAPAIAN INDIKATOR KOMPETENSI (STANDAR PENILAIAN SENSEI) -->
                <div class="bg-white p-5 sm:p-7 rounded-3xl border border-slate-200/90 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold mb-1.5 font-jp">
                                <Target class="w-3.5 h-3.5" />
                                <span>{{ isJapanese ? '到達目標別達成度（指導員評価基準）' : 'Capaian Kompetensi Berdasarkan Standar Sensei' }}</span>
                            </div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 font-jp">
                                {{ isJapanese ? '学習到達度の可視化' : 'Penguasaan Indikator & Materi Ujian' }}
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5 font-jp leading-relaxed">
                                {{ isJapanese 
                                    ? '80%以上で習得完了（緑）、60〜79%は普通（黄）、60%未満は要復習（赤）です。' 
                                    : 'Persentase keberhasilan per indikator. Nilai ≥80% menandakan materi telah dikuasai dengan tuntas.' 
                                }}
                            </p>
                        </div>

                        <!-- Legend Status -->
                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap text-xs font-jp shrink-0">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px]">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                ≥80% Tuntas
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 font-bold text-[11px]">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                60-79% Cukup
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-bold text-[11px]">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                &lt;60% Remedial
                            </span>
                        </div>
                    </div>

                    <!-- Progress Bars List -->
                    <div class="space-y-3 sm:space-y-3.5">
                        <div 
                            v-for="item in indicatorStats.list" 
                            :key="item.indicator_id"
                            class="p-3.5 sm:p-4 rounded-2xl border transition-all"
                            :class="item.percentage >= 80 
                                ? 'bg-slate-50/60 border-slate-200/80 hover:border-emerald-300' 
                                : item.percentage >= 60 
                                    ? 'bg-amber-50/20 border-amber-200/60 hover:border-amber-300' 
                                    : 'bg-rose-50/20 border-rose-200/60 hover:border-rose-300'"
                        >
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 sm:gap-2 mb-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 text-xs font-bold">
                                        🎯
                                    </span>
                                    <h3 class="text-xs sm:text-sm font-black text-slate-900 font-jp truncate">
                                        {{ item.name }}
                                    </h3>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-2 sm:gap-3 shrink-0 pt-1 sm:pt-0 border-t border-slate-200/40 sm:border-0">
                                    <span class="text-[11px] sm:text-xs text-slate-500 font-jp">
                                        {{ item.correct_count }}/{{ item.total_questions }} {{ isJapanese ? '正解' : 'Soal Benar' }}
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm sm:text-base font-black font-mono"
                                            :class="item.percentage >= 80 ? 'text-emerald-700' : item.percentage >= 60 ? 'text-amber-700' : 'text-rose-700'">
                                            {{ item.percentage }}%
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black uppercase font-jp"
                                            :class="item.percentage >= 80 ? 'bg-emerald-100 text-emerald-800' : item.percentage >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800'">
                                            {{ item.percentage >= 80 ? 'Tuntas' : item.percentage >= 60 ? 'Cukup' : 'Remedial' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Animated Visual Progress Bar -->
                            <div class="w-full bg-slate-200/70 rounded-full h-2.5 sm:h-3 overflow-hidden p-0.5">
                                <div 
                                    class="h-full rounded-full transition-all duration-700 shadow-2xs"
                                    :class="item.percentage >= 80 ? 'bg-emerald-500' : item.percentage >= 60 ? 'bg-amber-500' : 'bg-rose-500'"
                                    :style="{ width: `${item.percentage}%` }"
                                ></div>
                            </div>
                        </div>

                        <div v-if="indicatorStats.list.length === 0" class="py-6 text-center text-slate-400 text-xs font-jp">
                            {{ isJapanese ? '評価指標データが集計されていません。' : 'Belum ada data indikator capaian untuk sesi ujian ini.' }}
                        </div>
                    </div>
                </div>

                <!-- 4. PETA PENCAPAIAN BUTIR SOAL & PEMBAHASAN -->
                <div class="bg-white p-5 sm:p-7 rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 font-jp flex items-center gap-2">
                                <span>📋</span>
                                <span>{{ isJapanese ? '出題問題の正誤確認' : 'Peta Butir Soal & Pembahasan Mandiri' }}</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5 font-jp">
                                {{ isJapanese ? '番号をタップすると、正解・解説・設問詳細を確認できます。' : 'Tap nomor soal untuk mempelajari jawaban yang benar beserta pembahasannya.' }}
                            </p>
                        </div>

                        <!-- Filter Mode Tabs -->
                        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-2xl text-xs font-bold font-jp self-start sm:self-auto">
                            <button 
                                type="button" 
                                @click="filterMode = 'all'"
                                class="px-2.5 py-1.5 rounded-xl transition-all cursor-pointer"
                                :class="filterMode === 'all' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            >
                                Semua ({{ totalAnsweredCount }})
                            </button>
                            <button 
                                type="button" 
                                @click="filterMode = 'correct'"
                                class="px-2.5 py-1.5 rounded-xl transition-all cursor-pointer text-emerald-700"
                                :class="filterMode === 'correct' ? 'bg-white shadow-xs font-black' : 'text-slate-500 hover:text-emerald-700'"
                            >
                                🟢 Benar ({{ totalCorrectCount }})
                            </button>
                            <button 
                                type="button" 
                                @click="filterMode = 'wrong'"
                                class="px-2.5 py-1.5 rounded-xl transition-all cursor-pointer text-rose-700"
                                :class="filterMode === 'wrong' ? 'bg-white shadow-xs font-black' : 'text-slate-500 hover:text-rose-700'"
                            >
                                🔴 Salah ({{ totalWrongCount }})
                            </button>
                        </div>
                    </div>

                    <!-- Number Grid Matrix (Mobile Touch Friendly) -->
                    <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 gap-2">
                        <button
                            v-for="(ans, idx) in filteredAnswers"
                            :key="ans.id"
                            type="button"
                            @click="selectedAnswer = ans; selectedQuestionIndex = idx + 1"
                            class="p-2 sm:p-2.5 rounded-2xl border-2 text-center transition-all cursor-pointer flex flex-col items-center justify-center gap-0.5 group active:scale-95"
                            :class="ans.is_correct 
                                ? 'border-emerald-200 bg-emerald-50/70 text-emerald-800 hover:bg-emerald-100' 
                                : 'border-rose-200 bg-rose-50/70 text-rose-800 hover:bg-rose-100'"
                        >
                            <span class="text-xs font-mono font-black">
                                #{{ idx + 1 }}
                            </span>
                            <span class="text-[10px] font-bold font-mono">
                                {{ ans.is_correct ? '✓' : '✕' }}
                            </span>
                        </button>
                    </div>

                    <!-- Selected Question Detail Drawer -->
                    <div v-if="selectedAnswer" class="mt-4 p-4 sm:p-6 rounded-3xl bg-slate-900 text-white space-y-4 animate-fade-in shadow-xl">
                        <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2.5 py-1 rounded-xl bg-white text-slate-900 font-mono font-black text-xs">
                                    Soal #{{ selectedQuestionIndex }}
                                </span>
                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold"
                                    :class="selectedAnswer.is_correct ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white'">
                                    {{ selectedAnswer.is_correct ? '✓ Terjawab Benar' : '✕ Terjawab Salah' }}
                                </span>
                                <span v-if="selectedAnswer.question?.category" class="px-2.5 py-1 rounded-xl bg-slate-800 text-slate-300 text-xs font-jp">
                                    🎯 {{ selectedAnswer.question.category.name }}
                                </span>
                            </div>

                            <button @click="selectedAnswer = null" class="text-slate-400 hover:text-white p-1 cursor-pointer">
                                ✕
                            </button>
                        </div>

                        <!-- Teks Soal -->
                        <div class="space-y-1.5">
                            <p class="text-[11px] text-slate-400 uppercase tracking-wider font-jp">
                                Teks Soal:
                            </p>
                            <div class="text-xs sm:text-sm font-semibold leading-relaxed font-jp bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700" v-html="selectedAnswer.question?.question_text">
                            </div>
                        </div>

                        <!-- Jawaban Siswa vs Kunci Resmi -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                            <div class="p-3 rounded-xl border font-jp"
                                :class="selectedAnswer.is_correct ? 'bg-emerald-950/40 border-emerald-600 text-emerald-200' : 'bg-rose-950/40 border-rose-600 text-rose-200'">
                                <span class="text-[10px] font-bold block opacity-75">Jawaban Anda:</span>
                                <p class="text-xs font-bold mt-1">
                                    {{ selectedAnswer.selectedOption?.option_text || 'Tidak Menjawab' }}
                                </p>
                            </div>

                            <div class="p-3 rounded-xl bg-emerald-950/40 border border-emerald-600 text-emerald-200 font-jp">
                                <span class="text-[10px] font-bold block opacity-75">Kunci Jawaban Resmi:</span>
                                <p class="text-xs font-bold mt-1">
                                    {{ selectedAnswer.question?.options?.find(o => o.is_correct)?.option_text || '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Pembahasan / Solusi -->
                        <div v-if="selectedAnswer.question?.explanation" class="p-3.5 rounded-xl bg-slate-800 text-xs text-slate-300 font-jp border border-slate-700">
                            <span class="font-bold text-amber-400 block mb-1">💡 Pembahasan Soal:</span>
                            <p>{{ selectedAnswer.question.explanation }}</p>
                        </div>
                    </div>
                </div>

                <!-- 5. DAFTAR RIWAYAT CBT LENGKAP (RESPONSIVE TABLE DESKTOP + CARDS MOBILE) -->
                <div class="bg-white p-5 sm:p-7 rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2 font-jp">
                        <Clock class="w-4 h-4 text-emerald-600" />
                        <span>Riwayat Sesi Ujian CBT yang Pernah Dikerjakan</span>
                    </h2>

                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 font-jp">
                            <thead class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="py-3 px-4">Nama Paket CBT</th>
                                    <th class="py-3 px-4">Waktu Ujian</th>
                                    <th class="py-3 px-4">Skor Diperoleh</th>
                                    <th class="py-3 px-4">Passing Grade</th>
                                    <th class="py-3 px-4">Status Kelulusan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="ses in student.exam_sessions" :key="ses.id" 
                                    class="hover:bg-slate-50/60 transition-colors"
                                    :class="activeSession?.id === ses.id ? 'bg-emerald-50/20' : ''">
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        {{ ses.exam?.title || 'Simulasi Ujian CBT' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 font-mono">
                                        {{ ses.submitted_at ? new Date(ses.submitted_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-' }}
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                        {{ ses.total_score || ses.score || 0 }} / {{ ses.exam?.max_score || 180 }} pts
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-500">
                                        Min. {{ ses.exam?.passing_score || 90 }} pts
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full font-bold text-[11px]"
                                            :class="ses.is_passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                                            {{ ses.is_passed ? '🎉 Lulus Passing Grade' : 'Belum Lulus' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card Feed View (md:hidden) -->
                    <div class="block md:hidden divide-y divide-slate-100">
                        <div v-for="ses in student.exam_sessions" :key="'mob-' + ses.id" class="py-3 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-bold text-xs text-slate-900 leading-snug">
                                    {{ ses.exam?.title || 'Simulasi Ujian CBT' }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-full font-black text-[10px] shrink-0"
                                    :class="ses.is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'">
                                    {{ ses.is_passed ? '🎉 LULUS' : 'BELUM LULUS' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs pt-0.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-slate-500 text-[11px]">Skor:</span>
                                    <span class="font-black font-mono text-slate-950">{{ ses.total_score || ses.score || 0 }}</span>
                                    <span class="text-slate-400 text-[10px]">/ {{ ses.exam?.max_score || 180 }} pts</span>
                                </div>

                                <span class="text-[10px] text-slate-400 font-mono">
                                    {{ ses.submitted_at ? new Date(ses.submitted_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' }) : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { 
    Award, 
    Target, 
    Clock
} from 'lucide-vue-next';

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});

const { isJapanese } = useLang();

// Active Session Selector State
const selectedSessionId = ref(props.student.exam_sessions?.[0]?.id || null);

const activeSession = computed(() => {
    if (!props.student.exam_sessions || props.student.exam_sessions.length === 0) return null;
    return props.student.exam_sessions.find(s => s.id === selectedSessionId.value) || props.student.exam_sessions[0];
});

// Aggregate Statistics across all sessions
const passedExamsCount = computed(() => {
    return (props.student.exam_sessions || []).filter(s => s.is_passed).length;
});

const averageScore = computed(() => {
    const list = props.student.exam_sessions || [];
    if (list.length === 0) return 0;
    const total = list.reduce((acc, curr) => acc + Number(curr.total_score || curr.score || 0), 0);
    return Math.round(total / list.length);
});

const highestScore = computed(() => {
    const list = props.student.exam_sessions || [];
    if (list.length === 0) return 0;
    return Math.max(...list.map(s => Number(s.total_score || s.score || 0)));
});

// Answers list for active selected session
const answersList = computed(() => {
    return activeSession.value?.answers || [];
});

const totalAnsweredCount = computed(() => answersList.value.length);
const totalCorrectCount = computed(() => answersList.value.filter(a => a.is_correct).length);
const totalWrongCount = computed(() => answersList.value.filter(a => !a.is_correct).length);

const overallAccuracy = computed(() => {
    if (totalAnsweredCount.value === 0) return 0;
    return Math.round((totalCorrectCount.value / totalAnsweredCount.value) * 100);
});

// Indicator Mastery Stats based on active session
const indicatorStats = computed(() => {
    const map = {};

    answersList.value.forEach(ans => {
        const cat = ans.question?.category;
        const indId = cat?.id || 0;
        const indName = cat?.name || (isJapanese.value ? '未分類・総合問題' : 'Umum / Pola Standar');
        const indCode = cat?.code || '';
        const indLevel = cat?.level || ans.question?.level || 'N4';

        if (!map[indId]) {
            map[indId] = {
                indicator_id: indId,
                name: indName,
                code: indCode,
                level: indLevel,
                total_questions: 0,
                correct_count: 0,
                wrong_count: 0,
            };
        }

        map[indId].total_questions++;
        if (ans.is_correct) {
            map[indId].correct_count++;
        } else {
            map[indId].wrong_count++;
        }
    });

    const list = Object.values(map).map(item => {
        const pct = item.total_questions > 0 
            ? Math.round((item.correct_count / item.total_questions) * 100) 
            : 0;
        return {
            ...item,
            percentage: pct,
        };
    }).sort((a, b) => b.percentage - a.percentage); // Sort highest first for student encouragement

    const masteredCount = list.filter(i => i.percentage >= 80).length;
    const remedialCount = list.filter(i => i.percentage < 60).length;

    return {
        list,
        masteredCount,
        remedialCount,
    };
});

// Question Matrix Filter & Modal State
const filterMode = ref('all');
const selectedAnswer = ref(null);
const selectedQuestionIndex = ref(null);

const filteredAnswers = computed(() => {
    if (filterMode.value === 'correct') {
        return answersList.value.filter(a => a.is_correct);
    }
    if (filterMode.value === 'wrong') {
        return answersList.value.filter(a => !a.is_correct);
    }
    return answersList.value;
});
</script>
