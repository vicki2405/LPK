<template>
    <Head :title="isJapanese ? `${student.name} - 成績・到達度詳細` : `Rapor Siswa - ${student.name}`" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-12">
            <!-- Top Header Action & Student Card -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
                <div>
                    <Link :href="route('sensei.grades.index')" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 mb-3 transition-colors cursor-pointer">
                        <ArrowLeft class="w-4 h-4" />
                        <span>{{ isJapanese ? '← 成績一覧に戻る' : '← Kembali ke Daftar Rapor' }}</span>
                    </Link>
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-600 to-red-700 text-white flex items-center justify-center font-black text-lg shadow-md shadow-rose-500/20">
                            {{ student.name.charAt(0) }}
                        </div>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                                <span>{{ student.name }}</span>
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-mono">
                                    {{ student.nik || `SISWA-${student.id}` }}
                                </span>
                            </h1>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                                <span>{{ student.email }}</span>
                                <span>·</span>
                                <span class="font-bold text-slate-700 font-jp">{{ student.batches?.[0]?.name || (isJapanese ? '未所属' : 'Belum Ada Angkatan') }}</span>
                                <span>·</span>
                                <span class="font-bold text-rose-600 font-mono">{{ isJapanese ? '目標' : 'Target' }}: {{ student.target_language_level || 'JLPT N4' }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start md:self-auto font-jp">
                    <span class="px-4 py-2 rounded-2xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ isJapanese ? '● 在籍・受講中' : '● Status: Trainee Aktif' }}
                    </span>
                </div>
            </div>

            <!-- Empty State if No Exam Session -->
            <div v-if="!student.exam_sessions || student.exam_sessions.length === 0" class="bg-white p-12 rounded-3xl border border-slate-200/80 text-center">
                <Target class="w-12 h-12 text-slate-300 mx-auto mb-3" />
                <h3 class="text-base font-bold text-slate-800 font-jp">
                    {{ isJapanese ? 'CBT受験履歴がありません' : 'Belum Ada Riwayat Ujian CBT' }}
                </h3>
                <p class="text-xs text-slate-500 mt-1 font-jp">
                    {{ isJapanese 
                        ? 'この実習生はまだCBT試験を受験していません。試験が完了すると、到達目標別の達成度グラフがここに表示されます。' 
                        : 'Siswa ini belum pernah mengerjakan simulasi CBT. Grafik capaian indikator akan otomatis muncul begitu siswa menyelesaikan ujian.' 
                    }}
                </p>
            </div>

            <template v-else>
                <!-- Sesi Ujian Selector (Jika Mengerjakan Lebih dari 1 Paket Ujian) -->
                <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2 font-jp">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            {{ isJapanese ? '分析対象の試験セッション:' : 'Pilih Sesi Ujian untuk Dianalisis:' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                        <button
                            v-for="session in student.exam_sessions"
                            :key="session.id"
                            @click="selectedSessionId = session.id"
                            type="button"
                            class="px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 cursor-pointer border font-jp flex items-center gap-2"
                            :class="activeSession?.id === session.id
                                ? 'bg-slate-900 text-white border-slate-900 shadow-sm'
                                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                        >
                            <span>{{ session.exam?.title || 'Ujian CBT' }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded font-mono font-bold"
                                :class="session.is_passed ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300'">
                                {{ session.total_score || session.score || 0 }} pts
                            </span>
                        </button>
                    </div>
                </div>

                <!-- ================= ANALYTICS SCORE & OVERVIEW CARDS ================= -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    <!-- Skor Total -->
                    <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '獲得スコア / 満点' : 'Skor Diperoleh' }}
                        </span>
                        <div class="my-2">
                            <div class="flex items-baseline gap-1.5 sm:gap-2">
                                <span class="text-2xl sm:text-3xl font-black text-slate-950 font-mono">
                                    {{ activeSession?.total_score || activeSession?.score || 0 }}
                                </span>
                                <span class="text-[11px] sm:text-xs font-bold text-slate-400 font-mono">
                                    / {{ activeSession?.exam?.max_score || 180 }} pts
                                </span>
                            </div>
                            <p class="text-[10px] sm:text-[11px] font-bold font-jp mt-1 truncate"
                                :class="activeSession?.is_passed ? 'text-emerald-600' : 'text-rose-600'">
                                {{ activeSession?.is_passed ? (isJapanese ? '● 合格達成' : '● Lulus') : (isJapanese ? '● 不合格' : '● Belum Lulus') }}
                            </p>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 sm:h-2 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                :class="activeSession?.is_passed ? 'bg-emerald-500' : 'bg-rose-500'"
                                :style="{ width: `${Math.min(100, Math.round(((activeSession?.total_score || activeSession?.score || 0) / (activeSession?.exam?.max_score || 180)) * 100))}%` }">
                            </div>
                        </div>
                    </div>

                    <!-- Akurasi Jawaban Benar -->
                    <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '正答率 (精度)' : 'Akurasi Benar' }}
                        </span>
                        <div class="my-2 flex items-center justify-between">
                            <div>
                                <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-mono">
                                    {{ overallAccuracy }}%
                                </span>
                                <p class="text-[10px] sm:text-[11px] text-slate-500 font-jp mt-0.5">
                                    {{ totalCorrectCount }}/{{ totalAnsweredCount }} {{ isJapanese ? '正解' : 'Benar' }}
                                </p>
                            </div>
                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs sm:text-sm">
                                🟢
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-jp">
                            {{ totalWrongCount }} {{ isJapanese ? '問不正解' : 'Soal Salah' }}
                        </span>
                    </div>

                    <!-- Tingkat Ketuntasan Indikator -->
                    <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '到達目標・達成率' : 'Ketuntasan Indikator' }}
                        </span>
                        <div class="my-2 flex items-center justify-between">
                            <div>
                                <span class="text-2xl sm:text-3xl font-black text-blue-600 font-mono">
                                    {{ indicatorStats.masteredCount }}/{{ indicatorStats.list.length }}
                                </span>
                                <p class="text-[10px] sm:text-[11px] text-slate-500 font-jp mt-0.5">
                                    {{ isJapanese ? '目標習得' : 'Indikator Tuntas' }}
                                </p>
                            </div>
                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs sm:text-sm">
                                🎯
                            </div>
                        </div>
                        <span class="text-[10px] font-jp truncate" :class="indicatorStats.remedialCount > 0 ? 'text-rose-600 font-bold' : 'text-slate-400'">
                            {{ indicatorStats.remedialCount }} {{ isJapanese ? '要指導' : 'Perlu Remedial' }}
                        </span>
                    </div>

                    <!-- Waktu Ujian & Integritas Layar -->
                    <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider font-jp">
                            {{ isJapanese ? '不正検知・受験日' : 'Integritas Ujian' }}
                        </span>
                        <div class="my-2">
                            <span class="text-xs sm:text-sm font-bold text-slate-900 block truncate">
                                {{ activeSession?.submitted_at ? new Date(activeSession.submitted_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '-' }}
                            </span>
                            <div class="mt-1.5">
                                <span v-if="activeSession?.is_disqualified" class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200 font-jp">
                                    ⛔ {{ isJapanese ? '失格' : 'Diskualifikasi' }}
                                </span>
                                <span v-else-if="activeSession?.violation_count > 0" class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 font-jp">
                                    ⚠️ {{ activeSession.violation_count }}x {{ isJapanese ? '離脱' : 'Pindah Tab' }}
                                </span>
                                <span v-else class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 font-jp">
                                    ✓ {{ isJapanese ? '誠実受験' : 'Tertib' }}
                                </span>
                            </div>
                        </div>
                        <span class="text-[9px] text-slate-400 font-mono">
                            ID: #{{ activeSession?.id }}
                        </span>
                    </div>
                </div>

                <!-- ================= 1. GRAFIK CAPAIAN PER INDIKATOR PEMBELAJARAN ================= -->
                <div class="bg-white p-4 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5 sm:space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold mb-1.5 font-jp">
                                <Target class="w-3.5 h-3.5" />
                                <span>{{ isJapanese ? '学習到達目標（評価指標）別達成度グラフ' : 'Grafik Capaian per Indikator Pembelajaran' }}</span>
                            </div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 font-jp">
                                {{ isJapanese ? '出題問題の到達度分析（正答率 & 習得状況）' : 'Analisis Penguasaan Indikator Soal CBT' }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5 font-jp">
                                {{ isJapanese 
                                    ? 'この試験で出題された各評価指標に対する実習生の正答率を可視化しています。80%以上で習得、60%未満は個別指導が必要です。' 
                                    : 'Persentase keberhasilan siswa berdasarkan indikator resmi yang telah diatur. Nilai < 60% menandakan kelemahan yang wajib dibimbing remedial.' 
                                }}
                            </p>
                        </div>

                        <!-- Legend Status -->
                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap text-xs font-jp shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px] sm:text-[11px]">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                {{ isJapanese ? '≥ 80% 習得' : '≥ 80% Tuntas' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 font-bold text-[10px] sm:text-[11px]">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                {{ isJapanese ? '60〜79% 普通' : '60-79% Cukup' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-bold text-[10px] sm:text-[11px]">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                {{ isJapanese ? '< 60% 要指導' : '< 60% Belum Tuntas' }}
                            </span>
                        </div>
                    </div>

                    <!-- Progress Bars per Indikator -->
                    <div class="space-y-3 sm:space-y-4">
                        <div 
                            v-for="item in indicatorStats.list" 
                            :key="item.indicator_id"
                            class="p-3.5 sm:p-4 rounded-2xl border transition-all"
                            :class="item.percentage >= 80 
                                ? 'bg-slate-50/50 border-slate-200/80 hover:border-emerald-300' 
                                : item.percentage >= 60 
                                    ? 'bg-amber-50/30 border-amber-200/60 hover:border-amber-300' 
                                    : 'bg-rose-50/30 border-rose-200/60 hover:border-rose-300'"
                        >
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 sm:gap-2 mb-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 text-xs">
                                        🎯
                                    </span>
                                    <h4 class="text-xs sm:text-sm font-black text-slate-900 font-jp truncate">
                                        {{ item.name }}
                                    </h4>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-2 sm:gap-3 shrink-0 pt-1 sm:pt-0 border-t border-slate-200/40 sm:border-0">
                                    <span class="text-[11px] sm:text-xs text-slate-500 font-jp">
                                        {{ item.correct_count }}/{{ item.total_questions }} {{ isJapanese ? '正解' : 'Soal' }}
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm sm:text-base font-black font-mono"
                                            :class="item.percentage >= 80 ? 'text-emerald-700' : item.percentage >= 60 ? 'text-amber-700' : 'text-rose-700'">
                                            {{ item.percentage }}%
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black uppercase font-jp"
                                            :class="item.percentage >= 80 ? 'bg-emerald-100 text-emerald-800' : item.percentage >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800'">
                                            {{ item.percentage >= 80 ? (isJapanese ? '習得' : 'Tuntas') : item.percentage >= 60 ? (isJapanese ? '普通' : 'Cukup') : (isJapanese ? '要指導' : 'Remedial') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Animated Visual Progress Bar -->
                            <div class="w-full bg-slate-200/80 rounded-full h-2.5 sm:h-3 overflow-hidden p-0.5">
                                <div 
                                    class="h-full rounded-full transition-all duration-700 shadow-xs"
                                    :class="item.percentage >= 80 ? 'bg-emerald-500' : item.percentage >= 60 ? 'bg-amber-500' : 'bg-rose-500'"
                                    :style="{ width: `${item.percentage}%` }"
                                ></div>
                            </div>
                        </div>

                        <div v-if="indicatorStats.list.length === 0" class="py-8 text-center text-slate-400 text-xs font-jp">
                            {{ isJapanese ? 'この試験の出題問題には評価指標が設定されていません。' : 'Soal-soal pada paket ujian ini belum ditautkan ke Indikator Capaian.' }}
                        </div>
                    </div>
                </div>

                <!-- ================= 2. PETA PENCAPAIAN BUTIR SOAL (ITEM ANALYSIS MATRIX) ================= -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 font-jp flex items-center gap-2">
                                <span>📋</span>
                                <span>{{ isJapanese ? '全出題問題の正誤マップ（クリックで設問詳細・解説）' : 'Peta Pencapaian Butir Soal (Item Matrix)' }}</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5 font-jp">
                                {{ isJapanese ? '各設問番号をクリックすると、実習生の選択肢、正解、および配点詳細を確認できます。' : 'Klik nomor soal untuk melihat teks soal, jawaban siswa, kunci jawaban resmi, dan indikator capaiannya.' }}
                            </p>
                        </div>

                        <!-- Filter Buttons for Matrix -->
                        <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-2xl text-xs font-bold font-jp">
                            <button 
                                type="button" 
                                @click="filterMode = 'all'"
                                class="px-3 py-1.5 rounded-xl transition-all cursor-pointer"
                                :class="filterMode === 'all' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            >
                                {{ isJapanese ? '全問' : 'Semua' }} ({{ totalAnsweredCount }})
                            </button>
                            <button 
                                type="button" 
                                @click="filterMode = 'correct'"
                                class="px-3 py-1.5 rounded-xl transition-all cursor-pointer text-emerald-700"
                                :class="filterMode === 'correct' ? 'bg-white shadow-xs font-black' : 'text-slate-500 hover:text-emerald-700'"
                            >
                                🟢 {{ isJapanese ? '正解' : 'Benar' }} ({{ totalCorrectCount }})
                            </button>
                            <button 
                                type="button" 
                                @click="filterMode = 'wrong'"
                                class="px-3 py-1.5 rounded-xl transition-all cursor-pointer text-rose-700"
                                :class="filterMode === 'wrong' ? 'bg-white shadow-xs font-black' : 'text-slate-500 hover:text-rose-700'"
                            >
                                🔴 {{ isJapanese ? '不正解' : 'Salah' }} ({{ totalWrongCount }})
                            </button>
                        </div>
                    </div>

                    <!-- Number Grid Matrix -->
                    <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 gap-2.5">
                        <button
                            v-for="(ans, idx) in filteredAnswers"
                            :key="ans.id"
                            type="button"
                            @click="selectedAnswer = ans; selectedQuestionIndex = idx + 1"
                            class="p-2.5 rounded-2xl border-2 text-center transition-all cursor-pointer flex flex-col items-center justify-center gap-0.5 group hover:scale-105"
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
                            <span class="text-[9px] font-bold text-slate-500 truncate max-w-[50px] font-jp">
                                {{ ans.question?.category?.code || ans.question?.category?.name || '-' }}
                            </span>
                        </button>
                    </div>

                    <!-- Selected Question Detail Drawer / Box -->
                    <div v-if="selectedAnswer" class="mt-4 p-5 sm:p-6 rounded-3xl bg-slate-900 text-white space-y-4 animate-fade-in shadow-xl">
                        <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2.5 py-1 rounded-xl bg-white text-slate-900 font-mono font-black text-xs">
                                    {{ isJapanese ? '第' + selectedQuestionIndex + '問' : 'Soal #' + selectedQuestionIndex }}
                                </span>
                                <span class="px-2.5 py-1 rounded-xl text-xs font-bold"
                                    :class="selectedAnswer.is_correct ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white'">
                                    {{ selectedAnswer.is_correct ? (isJapanese ? '✓ 正解 (得点獲得)' : '✓ Terjawab Benar') : (isJapanese ? '✕ 不正解 (0点)' : '✕ Terjawab Salah') }}
                                </span>
                                <span v-if="selectedAnswer.question?.category" class="px-2.5 py-1 rounded-xl bg-slate-800 text-slate-300 text-xs font-jp">
                                    🎯 {{ isJapanese ? '到達目標: ' : 'Indikator: ' }} {{ selectedAnswer.question.category.name }}
                                </span>
                            </div>

                            <button @click="selectedAnswer = null" class="text-slate-400 hover:text-white p-1 cursor-pointer">
                                ✕
                            </button>
                        </div>

                        <!-- Question Text -->
                        <div class="space-y-2">
                            <p class="text-xs text-slate-400 uppercase tracking-wider font-jp">
                                {{ isJapanese ? '設問文:' : 'Teks Soal:' }}
                            </p>
                            <div class="text-sm font-semibold leading-relaxed font-jp bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700" v-html="selectedAnswer.question?.question_text">
                            </div>
                        </div>

                        <!-- Comparison: Siswa vs Kunci Jawaban -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <div class="p-3 rounded-xl border font-jp"
                                :class="selectedAnswer.is_correct ? 'bg-emerald-950/40 border-emerald-600 text-emerald-200' : 'bg-rose-950/40 border-rose-600 text-rose-200'">
                                <span class="text-[10px] font-bold block opacity-75">
                                    {{ isJapanese ? '実習生の回答:' : 'Jawaban yang Dipilih Siswa:' }}
                                </span>
                                <p class="text-xs font-bold mt-1">
                                    {{ selectedAnswer.selectedOption?.option_text || (isJapanese ? '未回答' : 'Tidak Menjawab') }}
                                </p>
                            </div>

                            <div class="p-3 rounded-xl bg-emerald-950/40 border border-emerald-600 text-emerald-200 font-jp">
                                <span class="text-[10px] font-bold block opacity-75">
                                    {{ isJapanese ? '正解の選択肢:' : 'Kunci Jawaban Resmi:' }}
                                </span>
                                <p class="text-xs font-bold mt-1">
                                    {{ selectedAnswer.question?.options?.find(o => o.is_correct)?.option_text || '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Penjelasan / Kaisetsu jika ada -->
                        <div v-if="selectedAnswer.question?.explanation" class="p-3.5 rounded-xl bg-slate-800 text-xs text-slate-300 font-jp border border-slate-700">
                            <span class="font-bold text-amber-400 block mb-1">💡 {{ isJapanese ? '解説・指導アドバイス:' : 'Pembahasan & Solusi:' }}</span>
                            <p>{{ selectedAnswer.question.explanation }}</p>
                        </div>
                    </div>
                </div>

                <!-- ================= 3. RIWAYAT LENGKAP CBT EXAM SESSIONS TABLE ================= -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2 font-jp">
                        <Clock class="w-5 h-5 text-japan-red" />
                        <span>{{ isJapanese ? '全受験セッション履歴' : 'Daftar Seluruh Riwayat Ujian CBT' }}</span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700 font-jp">
                            <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="py-3 px-4">{{ isJapanese ? '試験名' : 'Nama Paket CBT' }}</th>
                                    <th class="py-3 px-4">{{ isJapanese ? '受験日時' : 'Waktu Pengerjaan' }}</th>
                                    <th class="py-3 px-4">{{ isJapanese ? 'スコア' : 'Skor Diperoleh' }}</th>
                                    <th class="py-3 px-4">{{ isJapanese ? '合否判定' : 'Status Kelulusan' }}</th>
                                    <th class="py-3 px-4 text-right">{{ isJapanese ? '操作' : 'Aksi' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="session in student.exam_sessions" :key="session.id" 
                                    class="hover:bg-slate-50/60 transition-colors"
                                    :class="activeSession?.id === session.id ? 'bg-blue-50/30' : ''">
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        {{ session.exam?.title }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 font-mono">
                                        {{ session.submitted_at ? new Date(session.submitted_at).toLocaleDateString('id-ID') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                        {{ session.total_score || session.score || 0 }} / {{ session.exam?.max_score || 180 }} pts
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full font-bold text-[11px]"
                                            :class="session.is_passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                                            {{ session.is_passed ? (isJapanese ? '合格 (Lulus)' : 'Lulus Passing Grade') : (isJapanese ? '不合格' : 'Belum Lulus') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <button 
                                            @click="deleteSession(session)"
                                            type="button" 
                                            class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 transition-colors cursor-pointer"
                                            :title="isJapanese ? 'この受験記録を削除' : 'Hapus riwayat sesi ujian ini'"
                                        >
                                            <Trash2 class="w-3.5 h-3.5" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useLang } from '@/Composables/useLang';
import { notifySuccess, confirmDialog } from '@/Utils/alert';
import { 
    Clock, 
    ArrowLeft, 
    Trash2, 
    Target, 
    Award, 
    CheckCircle2 
} from 'lucide-vue-next';

const props = defineProps({
    student: Object,
});

const { isJapanese } = useLang();

// Sesi Ujian Aktif
const selectedSessionId = ref(props.student?.exam_sessions?.[0]?.id || null);

const activeSession = computed(() => {
    if (!props.student?.exam_sessions || props.student.exam_sessions.length === 0) return null;
    if (!selectedSessionId.value) return props.student.exam_sessions[0];
    return props.student.exam_sessions.find(s => s.id === selectedSessionId.value) || props.student.exam_sessions[0];
});

// Hitung Statistik Butir Soal
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

// Hitung Statistik Capaian Berdasarkan Indikator Nyata (Learning Indicators)
const indicatorStats = computed(() => {
    const map = {};

    answersList.value.forEach(ans => {
        const cat = ans.question?.category;
        const indId = cat?.id || 0;
        const indName = cat?.name || (isJapanese.value ? '未分類・総合問題' : 'Umum / Tanpa Indikator');
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
    }).sort((a, b) => a.percentage - b.percentage); // Urutkan dari yang paling lemah agar Sensei langsung lihat kelemahan siswa

    const masteredCount = list.filter(i => i.percentage >= 80).length;
    const remedialCount = list.filter(i => i.percentage < 60).length;

    return {
        list,
        masteredCount,
        remedialCount,
    };
});

// Peta Butir Soal Filter & Modal State
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

const deleteSession = (session) => {
    confirmDialog(
        isJapanese.value ? 'この試験結果を削除しますか？' : 'Hapus riwayat ujian ini?',
        isJapanese.value ? `「${session.exam?.title}」の受験データが完全に削除されます。` : `Data hasil ujian ${session.exam?.title} akan dihapus permanen dari rapor siswa.`
    ).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('sensei.grades.destroy-session', session.id), {
                preserveScroll: true,
                onSuccess: () => {
                    notifySuccess(
                        isJapanese.value ? '削除完了' : 'Terhapus', 
                        isJapanese.value ? '受験履歴を削除しました。' : 'Riwayat ujian berhasil dihapus dari rapor.'
                    );
                },
            });
        }
    });
};
</script>
