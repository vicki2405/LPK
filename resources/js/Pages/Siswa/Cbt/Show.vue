<template>
    <Head :title="`CBT: ${exam.title} - Masayume`" />

    <div 
        class="min-h-screen font-sans flex flex-col select-none relative transition-colors duration-300 overflow-x-hidden"
        :class="cbtMode === 'game' ? 'bg-[#0E0B1F] text-slate-100' : 'bg-[#F8F9FB] text-slate-800'"
        @contextmenu.prevent
    >
        <!-- ================= TOP LIVE EXAM PROGRESS TRACK ================= -->
        <div class="h-1.5 w-full fixed top-0 left-0 z-50 overflow-hidden" :class="cbtMode === 'game' ? 'bg-slate-800' : 'bg-slate-200/60'">
            <div 
                class="h-full transition-all duration-500 shadow-xs"
                :class="cbtMode === 'game' 
                    ? 'bg-gradient-to-r from-amber-500 via-rose-500 to-cyan-400' 
                    : 'bg-gradient-to-r from-japan-red via-rose-500 to-amber-500'"
                :style="{ width: `${progressPercentage}%` }"
            ></div>
        </div>

        <!-- ================= TOP CBT HEADER & TIMER BAR ================= -->
        <header 
            class="h-16 sticky top-0 z-40 px-3 sm:px-6 flex items-center justify-between transition-colors duration-300"
            :class="cbtMode === 'game' 
                ? 'bg-slate-900/95 backdrop-blur-xl border-b border-slate-800 text-white shadow-lg shadow-black/40' 
                : 'bg-white/85 backdrop-blur-xl border-b border-slate-200/80 text-slate-900 shadow-[0_4px_20px_rgba(0,0,0,0.03)]'"
        >
            <!-- Exam Title & Info -->
            <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0 flex-1 mr-2">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl sm:rounded-2xl text-white flex items-center justify-center font-black text-xs shadow-md shrink-0"
                    :class="cbtMode === 'game' 
                        ? 'bg-gradient-to-br from-amber-400 to-orange-600 shadow-amber-500/20' 
                        : 'bg-gradient-to-br from-japan-red to-rose-600 shadow-japan-red/20'">
                    {{ cbtMode === 'game' ? '🎮' : 'CBT' }}
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-xs sm:text-sm font-black truncate leading-tight"
                        :class="cbtMode === 'game' ? 'text-white' : 'text-slate-900'">
                        {{ exam.title }}
                    </h1>
                    <div class="flex items-center gap-1.5 text-[10px] sm:text-[11px] font-medium truncate mt-0.5"
                        :class="cbtMode === 'game' ? 'text-slate-400' : 'text-slate-500'">
                        <button 
                            @click="showMobilePalette = true"
                            type="button"
                            class="lg:hidden inline-flex items-center gap-1 font-bold rounded px-1.5 py-0.5 -ml-1 transition-colors cursor-pointer"
                            :class="cbtMode === 'game' ? 'text-cyan-400 bg-cyan-950/40 border border-cyan-500/30' : 'text-slate-800 bg-slate-100 hover:bg-slate-200 border border-slate-200'"
                            title="Buka Palet Nomor Soal"
                        >
                            <span>{{ cbtMode === 'game' ? 'Stage' : 'Soal' }} {{ currentQuestionIndex + 1 }}/{{ questions.length }}</span>
                            <span class="text-[9px]">📋▾</span>
                        </button>
                        <span class="font-bold hidden lg:inline" :class="cbtMode === 'game' ? 'text-cyan-400' : 'text-slate-700'">
                            {{ cbtMode === 'game' ? 'Stage' : 'Soal' }} {{ currentQuestionIndex + 1 }}/{{ questions.length }}
                        </span>
                        <span>·</span>
                        <span class="font-bold font-mono" :class="cbtMode === 'game' ? 'text-emerald-400' : 'text-emerald-700'">
                            {{ answeredCount }} Terjawab
                        </span>
                        <span class="text-slate-500 hidden sm:inline">({{ progressPercentage }}%)</span>
                    </div>
                </div>
            </div>

            <!-- Mode Switcher, Countdown Timer & Actions -->
            <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                <!-- PREVIEW SENSEI BADGE -->
                <div v-if="session?.is_preview" class="hidden md:flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[11px] font-black animate-pulse">
                    <span>👁️ PREVIEW SENSEI</span>
                </div>

                <!-- SFX Sound Toggle Button (Game Mode) -->
                <button 
                    v-if="cbtMode === 'game'"
                    @click="toggleSfx"
                    type="button"
                    class="px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold border flex items-center gap-1.5 cursor-pointer transition-all shadow-xs"
                    :class="!isSfxMuted 
                        ? 'bg-amber-500/20 text-amber-300 border-amber-500/40 hover:bg-amber-500/30' 
                        : 'bg-slate-800 text-slate-400 border-slate-700'"
                    :title="isSfxMuted ? 'Nyalakan Suara SFX' : 'Matikan Suara SFX'"
                >
                    <Volume2 v-if="!isSfxMuted" class="w-3.5 h-3.5 text-amber-400" />
                    <VolumeX v-else class="w-3.5 h-3.5 text-slate-400" />
                    <span class="hidden sm:inline">{{ !isSfxMuted ? 'SFX ON' : 'Mute' }}</span>
                </button>

                <!-- Live Sync Status Badge (Desktop & Tablet) -->
                <div class="hidden md:flex items-center">
                    <div v-if="syncStatus === 'syncing'" class="px-2.5 py-1 rounded-full text-[11px] font-bold flex items-center gap-1.5 shadow-2xs"
                        :class="cbtMode === 'game' ? 'bg-amber-950/50 border border-amber-500/50 text-amber-300' : 'bg-amber-50 border border-amber-200/80 text-amber-800'">
                        <RefreshCw class="w-3 h-3 text-amber-400 animate-spin" />
                        <span>Menyimpan...</span>
                    </div>
                    <div v-else-if="syncStatus === 'offline'" class="px-2.5 py-1 rounded-full text-[11px] font-bold flex items-center gap-1.5 shadow-2xs animate-pulse"
                        :class="cbtMode === 'game' ? 'bg-rose-950/50 border border-rose-500/50 text-rose-300' : 'bg-rose-50 border border-rose-200/80 text-rose-800'">
                        <CloudOff class="w-3 h-3 text-rose-400" />
                        <span>Offline</span>
                    </div>
                    <div v-else class="px-2.5 py-1 rounded-full text-[11px] font-bold flex items-center gap-1.5 shadow-2xs"
                        :class="cbtMode === 'game' ? 'bg-emerald-950/50 border border-emerald-500/40 text-emerald-300' : 'bg-emerald-50 border border-emerald-200/80 text-emerald-800'">
                        <CheckCircle2 class="w-3 h-3 text-emerald-400" />
                        <span>Tersimpan</span>
                    </div>
                </div>

                <!-- Countdown Timer Pill -->
                <div class="px-2.5 sm:px-3.5 py-1 sm:py-1.5 rounded-full border flex items-center gap-1.5 font-mono text-xs sm:text-sm font-black shadow-xs transition-colors shrink-0"
                    :class="[
                        timerSeconds < 300 
                            ? 'bg-rose-500/20 border-rose-500 text-rose-400 animate-pulse' 
                            : cbtMode === 'game' 
                                ? 'bg-slate-800 border-cyan-500/50 text-cyan-300 shadow-[0_0_15px_rgba(6,182,212,0.2)]' 
                                : 'bg-gradient-to-r from-amber-50 to-orange-50 border-amber-300/80 text-amber-900'
                    ]">
                    <Clock class="w-3.5 h-3.5 shrink-0" :class="cbtMode === 'game' ? 'text-cyan-400' : 'text-amber-600'" />
                    <span>{{ formattedTimer }}</span>
                </div>

                <!-- Toggle Fullscreen Button (Desktop/Tablet Only) -->
                <button 
                    @click="requestFullscreen"
                    class="p-2 rounded-full transition-colors cursor-pointer hidden sm:flex items-center justify-center border shrink-0"
                    :class="cbtMode === 'game' ? 'bg-slate-800 hover:bg-slate-700 text-slate-300 border-slate-700' : 'bg-slate-100 hover:bg-slate-200 text-slate-600 border-slate-200/80'"
                    :title="isFullscreen ? 'Layar Penuh Aktif' : 'Masuk Layar Penuh'"
                >
                    <Maximize2 v-if="!isFullscreen" class="w-4 h-4" />
                    <Minimize2 v-else class="w-4 h-4 text-emerald-400" />
                </button>

                <!-- Finish / Submit Button -->
                <button 
                    @click="cbtMode === 'game' ? (isStageClearedModal = true) : confirmSubmit()"
                    class="flex px-3 sm:px-4 py-2 rounded-full text-white text-xs font-black transition-all shadow-md items-center gap-1.5 cursor-pointer shrink-0"
                    :class="cbtMode === 'game' 
                        ? 'bg-gradient-to-r from-amber-500 to-red-600 hover:from-amber-400 hover:to-red-500 shadow-amber-500/20' 
                        : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-emerald-600/20'"
                >
                    <CheckCircle class="w-4 h-4" />
                    <span>{{ cbtMode === 'game' ? 'Hasil Battle' : 'Kirim Ujian' }}</span>
                </button>
            </div>
        </header>

        <!-- ================= GAME ADVENTURE HUD BAR (WAYGROUND / QUIZIZZ STYLE) ================= -->
        <div v-if="cbtMode === 'game'" class="bg-gradient-to-r from-slate-900 via-[#161233] to-slate-900 border-b border-purple-900/40 px-3 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-2.5 text-xs shadow-lg animate-fade-in">
            <!-- Trainee Avatar, Rank & Live Game Score -->
            <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-slate-950 flex items-center justify-center font-black text-xs shadow-md shadow-amber-500/20 shrink-0">
                        {{ currentUser.name?.charAt(0) || 'T' }}
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-black text-white text-xs truncate max-w-[110px] sm:max-w-none">{{ currentUser.name }}</span>
                            <span class="px-1.5 py-0.2 rounded bg-blue-500/20 text-blue-400 border border-blue-500/30 text-[10px] font-bold font-mono">
                                Lv. {{ currentLevel }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Live Points Counter -->
                <div class="px-3 py-1 rounded-full bg-gradient-to-r from-purple-900/80 to-indigo-900/80 border border-purple-500/50 text-purple-200 font-mono font-black text-xs sm:text-sm flex items-center gap-1.5 shadow-md shadow-purple-950/40">
                    <span class="text-amber-400">🏆</span>
                    <span>{{ gameScore.toLocaleString() }} pts</span>
                </div>

                <!-- Live Rank Position Badge -->
                <div class="px-2.5 py-1 rounded-full bg-cyan-950/80 border border-cyan-500/40 text-cyan-300 font-mono font-black text-xs flex items-center gap-1 shadow-xs">
                    <span>Rank #{{ currentUserRank }}</span>
                    <span v-if="currentRankDiff > 0" class="text-emerald-400 font-bold">▲{{ currentRankDiff }}</span>
                </div>
            </div>

            <!-- Health Hearts & Flame Streak Multiplier -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <!-- HP Hearts -->
                <div class="flex items-center gap-1 bg-slate-800/90 px-2.5 py-1 rounded-full border border-slate-700">
                    <span class="text-[10px] font-bold text-slate-400 mr-1 uppercase">HP:</span>
                    <Heart 
                        v-for="h in 5" 
                        :key="h" 
                        class="w-3.5 h-3.5 transition-all"
                        :class="h <= remainingHearts ? 'text-rose-500 fill-rose-500 drop-shadow-[0_0_6px_rgba(244,63,94,0.7)]' : 'text-slate-600 fill-slate-700/50'"
                    />
                </div>

                <!-- Flame Streak Multiplier -->
                <div class="flex items-center gap-1.5 px-3 py-1 rounded-full border font-black text-xs shadow-md transition-all"
                    :class="gameStreak > 0 
                        ? 'bg-gradient-to-r from-amber-500 to-red-500 text-slate-950 border-amber-300 shadow-amber-500/30 animate-pulse' 
                        : 'bg-slate-800 text-slate-400 border-slate-700'">
                    <Flame class="w-3.5 h-3.5 text-amber-300 fill-amber-300 animate-bounce" />
                    <span>{{ gameStreak }}x STREAK! 🔥</span>
                </div>
            </div>

            <!-- Quest Stage Indicator -->
            <div class="hidden sm:flex items-center gap-2 text-[11px] font-mono text-slate-300">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Stage {{ currentQuestionIndex + 1 }} / {{ questions.length }}</span>
            </div>
        </div>

        <!-- ================= MAIN CBT WORKSPACE (SPLIT LAYOUT) ================= -->
        <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
            <!-- Question Content View Area -->
            <main class="flex-1 overflow-y-auto p-3.5 sm:p-8 max-w-4xl mx-auto w-full space-y-4 sm:space-y-6 pb-28 sm:pb-32">
                <!-- Hero Floating Question Card -->
                <div 
                    class="rounded-3xl sm:rounded-4xl p-4 sm:p-8 border space-y-4 sm:space-y-5 relative overflow-hidden transition-all duration-300"
                    :class="cbtMode === 'game' 
                        ? 'bg-slate-900/95 border-slate-700/80 shadow-[0_0_35px_rgba(0,0,0,0.6)]' 
                        : 'bg-white border-slate-200/80 shadow-[0_10px_35px_rgba(0,0,0,0.04)]'"
                >
                    <!-- Accent Line Top -->
                    <div class="absolute top-0 left-0 right-0 h-1.5"
                        :class="cbtMode === 'game' 
                            ? 'bg-gradient-to-r from-amber-400 via-rose-500 to-cyan-400 shadow-[0_0_10px_rgba(244,63,94,0.6)]' 
                            : 'bg-gradient-to-r from-japan-red via-rose-500 to-amber-400'"></div>

                    <!-- Question Header Row -->
                    <div class="space-y-2.5 border-b pb-3 sm:pb-4 transition-colors"
                        :class="cbtMode === 'game' ? 'border-slate-800' : 'border-slate-100'">
                        <!-- Top Meta Row: Q.Number, Points, and Flag Button -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-xl font-mono font-black text-xs sm:text-sm shadow-xs"
                                    :class="cbtMode === 'game' 
                                        ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-slate-950 shadow-amber-500/20' 
                                        : 'bg-gradient-to-r from-slate-900 to-slate-800 text-white'">
                                    {{ cbtMode === 'game' ? 'STAGE' : 'Q.' }}{{ currentQuestionIndex + 1 < 10 ? '0' : '' }}{{ currentQuestionIndex + 1 }}
                                </span>
                                <span class="px-2 py-0.5 rounded-lg font-mono text-[11px] font-bold"
                                    :class="cbtMode === 'game' ? 'bg-slate-800 text-amber-300 border border-slate-700' : 'bg-slate-100 text-slate-600'">
                                    +{{ Number(currentQuestion.score_points).toFixed(0) }} {{ cbtMode === 'game' ? 'EXP' : 'Poin' }}
                                </span>
                            </div>

                            <!-- Flag / Ragu-ragu Button -->
                            <button 
                                @click="toggleFlag"
                                type="button" 
                                class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full border text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shrink-0 shadow-2xs"
                                :class="isCurrentFlagged 
                                    ? 'bg-gradient-to-r from-amber-400 to-amber-500 text-slate-950 border-amber-400 font-black shadow-amber-400/20' 
                                    : cbtMode === 'game' 
                                        ? 'bg-slate-800 border-slate-700 text-slate-300 hover:bg-slate-700' 
                                        : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
                                <Bookmark class="w-3.5 h-3.5" :class="isCurrentFlagged ? 'text-slate-950 fill-slate-950' : 'text-slate-400'" />
                                <span>{{ isCurrentFlagged ? (cbtMode === 'game' ? 'Tersimpan (Ragu)' : 'Ragu-Ragu') : 'Tandai Ragu' }}</span>
                            </button>
                        </div>

                        <!-- Learning Indicator Tag Row (Full Width, Never Truncated) -->
                        <div v-if="currentQuestion.category_name" class="flex items-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold font-jp leading-snug"
                                :class="cbtMode === 'game' 
                                    ? 'bg-rose-950/60 text-rose-300 border border-rose-500/30' 
                                    : 'bg-rose-50 text-rose-800 border border-rose-200/70'">
                                <span class="shrink-0">{{ cbtMode === 'game' ? '⚔️' : '🎯' }}</span>
                                <span>{{ currentQuestion.category_name }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Audio Choukai Player Anti-Cheat (If Available) -->
                    <div v-if="currentQuestion.audio_url" class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white shadow-md border border-slate-700/80 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-japan-red text-white flex items-center justify-center shadow-sm shrink-0">
                                    <Volume2 class="w-4 h-4" />
                                </div>
                                <div>
                                    <span class="text-xs font-black text-white font-jp block">
                                        聴解リスニング (Audio Menyimak N4)
                                    </span>
                                    <span class="text-[10px] text-slate-300 font-mono">
                                        Limit: {{ maxAudioPlays }}x Pemutaran Standar Resmi
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Play Counter Badge -->
                            <div class="px-2.5 py-1 rounded-full text-[11px] font-bold font-mono flex items-center gap-1.5"
                                :class="isAudioLimitReached 
                                    ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' 
                                    : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40'">
                                <span>{{ isAudioLimitReached ? '⛔ Habis' : '● Tersedia' }}</span>
                                <span>({{ currentQuestionAudioPlays }}/{{ maxAudioPlays }}x)</span>
                            </div>
                        </div>

                        <!-- Audio Controls Bar (Anti-Scrubbing & Play Limitation) -->
                        <div class="bg-slate-950/60 rounded-xl p-3 flex items-center gap-3 border border-slate-800">
                            <!-- Play / Pause Button -->
                            <button 
                                type="button" 
                                @click="toggleAudioPlay"
                                :disabled="isAudioLimitReached && !isAudioPlaying"
                                class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white transition-all shrink-0 cursor-pointer shadow-md"
                                :class="isAudioLimitReached && !isAudioPlaying 
                                    ? 'bg-slate-700 text-slate-400 cursor-not-allowed' 
                                    : isAudioPlaying 
                                        ? 'bg-amber-500 hover:bg-amber-600' 
                                        : 'bg-japan-red hover:bg-red-700 hover:scale-105 active:scale-95'"
                            >
                                <Pause v-if="isAudioPlaying" class="w-5 h-5 fill-white" />
                                <Play v-else class="w-5 h-5 fill-white ml-0.5" />
                            </button>

                            <!-- Wave Visualizer & Progress Track (Non-Scrubbable) -->
                            <div class="flex-1 min-w-0 space-y-1.5">
                                <div class="flex items-center justify-between text-[11px] font-mono text-slate-400">
                                    <span>{{ formatDuration(audioCurrentTime) }}</span>
                                    <!-- Animated soundwave when playing -->
                                    <div v-if="isAudioPlaying" class="flex items-center gap-0.5 h-3">
                                        <span class="w-1 bg-japan-red rounded-full animate-pulse h-2"></span>
                                        <span class="w-1 bg-rose-400 rounded-full animate-bounce h-3.5"></span>
                                        <span class="w-1 bg-amber-400 rounded-full animate-pulse h-2.5"></span>
                                        <span class="w-1 bg-japan-red rounded-full animate-bounce h-3"></span>
                                    </div>
                                    <span v-else class="text-[10px] text-slate-500 uppercase tracking-wider">Anti-Scrubbing Protected</span>
                                    <span>{{ formatDuration(audioDuration) }}</span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-japan-red to-rose-500 rounded-full transition-all duration-200"
                                        :style="{ width: `${audioDuration > 0 ? (audioCurrentTime / audioDuration) * 100 : 0}%` }">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Native Audio Engine -->
                        <audio 
                            ref="audioPlayerRef" 
                            :src="currentQuestion.audio_url" 
                            @timeupdate="onAudioTimeUpdate" 
                            @loadedmetadata="onAudioLoadedMetadata" 
                            @ended="onAudioEnded"
                            class="hidden"
                            preload="metadata"
                        ></audio>
                    </div>

                    <!-- Question Image Illustration (If Available) -->
                    <div v-if="currentQuestion.image_url" class="p-4 sm:p-6 rounded-2xl border flex flex-col items-center justify-center shadow-xs"
                        :class="cbtMode === 'game' ? 'bg-slate-950/60 border-slate-800' : 'bg-slate-50 border-slate-200/80'">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-3 font-sans self-start">
                            🖼️ 図・イラスト (Gambar / Denah Soal):
                        </span>
                        <img :src="currentQuestion.image_url" alt="Ilustrasi Soal CBT" class="max-h-72 w-auto max-w-full rounded-2xl object-contain border border-slate-200 shadow-sm bg-white p-1" />
                    </div>

                    <!-- Passage Reading Text (Dokkai) -->
                    <div v-if="currentQuestion.passage_text" class="p-5 sm:p-6 rounded-2xl border text-sm leading-relaxed font-jp shadow-xs"
                        :class="cbtMode === 'game' ? 'bg-slate-950/70 border-slate-800 text-slate-100' : 'bg-slate-50/80 border-slate-200/80 text-slate-800'">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2 font-sans flex items-center gap-1.5">
                            <span>📖</span>
                            <span>文章読解 (Wacana / Teks Bacaan):</span>
                        </span>
                        <div class="prose max-w-none font-jp leading-loose" :class="cbtMode === 'game' ? 'prose-invert text-slate-100' : 'text-slate-800'" v-html="currentQuestion.passage_text"></div>
                    </div>

                    <!-- Official JLPT Instruction (Petunjuk Soal) -->
                    <div v-if="currentQuestion.instruction" class="p-4 rounded-2xl border text-xs font-jp leading-relaxed shadow-xs"
                        :class="cbtMode === 'game' 
                            ? 'bg-amber-950/30 border-amber-500/30 text-amber-200' 
                            : 'bg-gradient-to-r from-amber-50/80 to-orange-50/50 border-amber-200/80 text-amber-950'">
                        <span class="text-[10px] font-black uppercase tracking-wider mb-1 font-sans flex items-center gap-1"
                            :class="cbtMode === 'game' ? 'text-amber-400' : 'text-amber-800'">
                            <span>💡</span>
                            <span>設問の指示 (Petunjuk Pengerjaan):</span>
                        </span>
                        <p class="leading-relaxed font-medium">{{ currentQuestion.instruction }}</p>
                    </div>

                    <!-- Question Text (High-Contrast Furigana Ruby Typography) -->
                    <div 
                        class="p-5 sm:p-8 rounded-2xl border text-base sm:text-xl font-extrabold font-jp leading-[2.3] shadow-xs select-text transition-colors"
                        :class="cbtMode === 'game' 
                            ? 'bg-slate-950/80 border-slate-800 text-white shadow-inner' 
                            : 'bg-[#FCFDFE] border-slate-200/90 text-slate-900'"
                    >
                        <div class="cbt-question-text" v-html="currentQuestion.question_text"></div>
                    </div>

                    <!-- Multiple Choice Options (A, B, C, D) -->
                    <!-- A. FORMAL MODE: Standard clean list -->
                    <div v-if="cbtMode !== 'game'" class="space-y-3 pt-2">
                        <button 
                            v-for="(opt, idx) in currentQuestion.options" 
                            :key="opt.id"
                            @click="selectOption(opt.id)"
                            type="button"
                            class="w-full text-left p-4 sm:p-5 rounded-2xl sm:rounded-3xl border-2 transition-all duration-200 flex items-center gap-3.5 sm:gap-4 cursor-pointer min-h-[58px] relative overflow-hidden group"
                            :class="currentSelectedOptionId === opt.id 
                                ? 'border-japan-red bg-gradient-to-r from-red-50/90 via-rose-50/40 to-white text-slate-950 shadow-md shadow-red-500/10 -translate-y-0.5' 
                                : 'border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50/70 text-slate-800 hover:-translate-y-0.5 hover:shadow-xs'"
                        >
                            <!-- Left Accent Strip when Selected -->
                            <div v-if="currentSelectedOptionId === opt.id" class="absolute left-0 top-0 bottom-0 w-1.5 bg-japan-red rounded-l"></div>

                            <!-- Option Key Circle (A, B, C, D) -->
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl font-black text-xs sm:text-sm flex items-center justify-center shrink-0 transition-all font-mono"
                                :class="currentSelectedOptionId === opt.id 
                                    ? 'bg-japan-red text-white shadow-md shadow-japan-red/30 scale-105' 
                                    : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200 border border-slate-200/60'">
                                {{ formatOptionKey(opt.option_key, idx) }}
                            </div>

                            <!-- Option Text -->
                            <div class="flex-1 font-jp text-sm sm:text-base font-semibold leading-relaxed"
                                :class="currentSelectedOptionId === opt.id ? 'text-slate-950 font-bold' : 'text-slate-900'"
                                v-html="opt.option_text"></div>

                            <!-- Selected Option Indicator (Radio Dot) -->
                            <div v-if="currentSelectedOptionId === opt.id" class="w-6 h-6 rounded-full bg-japan-red text-white flex items-center justify-center shrink-0 shadow-xs">
                                <span class="w-2.5 h-2.5 rounded-full bg-white"></span>
                            </div>
                        </button>
                    </div>

                    <!-- B. GAME MODE: 4 BALOK 3D ARCADE TACTILE BUTTONS (QUIZIZZ / WAYGROUND STYLE) -->
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3">
                        <button 
                            v-for="(opt, idx) in currentQuestion.options" 
                            :key="opt.id"
                            @click="selectOption(opt.id)"
                            type="button"
                            :disabled="isGameEvaluating || Boolean(answeredQuestionResults[currentQuestion.id])"
                            class="w-full text-left p-4 sm:p-5 rounded-3xl transition-all duration-150 flex items-center gap-3.5 sm:gap-4 cursor-pointer relative overflow-hidden group select-none"
                            :class="[
                                // Selected & Correct
                                (answeredQuestionResults[currentQuestion.id]?.selectedOptionId === opt.id && answeredQuestionResults[currentQuestion.id]?.isCorrect) 
                                    ? 'bg-gradient-to-br from-emerald-500 via-emerald-600 to-green-700 text-white ring-4 ring-emerald-300 shadow-[0_8px_0_#065f46] scale-[1.02]' 
                                : // Selected & Wrong
                                (answeredQuestionResults[currentQuestion.id]?.selectedOptionId === opt.id && !answeredQuestionResults[currentQuestion.id]?.isCorrect)
                                    ? 'bg-gradient-to-br from-rose-600 via-rose-700 to-red-800 text-white ring-4 ring-rose-400 shadow-[0_8px_0_#881337] animate-screen-shake'
                                : // Revealed Correct Option when student guessed wrong
                                (revealedCorrectOptions[currentQuestion.id] === opt.id && answeredQuestionResults[currentQuestion.id] && !answeredQuestionResults[currentQuestion.id]?.isCorrect)
                                    ? 'bg-gradient-to-br from-emerald-600/90 to-teal-800 text-white ring-4 ring-emerald-400 shadow-[0_8px_0_#064e3b] animate-pulse'
                                : // Selected before evaluation completes
                                currentSelectedOptionId === opt.id 
                                    ? 'ring-4 ring-white/95 scale-[1.02] brightness-115' 
                                : // Default Quizizz 4 distinct colors
                                idx === 0 ? 'bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 text-white shadow-[0_8px_0_#312e81] hover:brightness-110 active:translate-y-2 active:shadow-[0_1px_0_#312e81]' : '',
                                idx === 1 ? 'bg-gradient-to-br from-emerald-500 via-teal-600 to-emerald-800 text-white shadow-[0_8px_0_#064e3b] hover:brightness-110 active:translate-y-2 active:shadow-[0_1px_0_#064e3b]' : '',
                                idx === 2 ? 'bg-gradient-to-br from-amber-500 via-orange-500 to-amber-700 text-white shadow-[0_8px_0_#7c2d12] hover:brightness-110 active:translate-y-2 active:shadow-[0_1px_0_#7c2d12]' : '',
                                idx >= 3 ? 'bg-gradient-to-br from-rose-600 via-pink-600 to-rose-800 text-white shadow-[0_8px_0_#881337] hover:brightness-110 active:translate-y-2 active:shadow-[0_1px_0_#881337]' : '',
                                // Dim others when answered
                                (answeredQuestionResults[currentQuestion.id] && answeredQuestionResults[currentQuestion.id]?.selectedOptionId !== opt.id && revealedCorrectOptions[currentQuestion.id] !== opt.id) ? 'opacity-40 grayscale-[30%]' : ''
                            ]"
                        >
                            <!-- Shape Badge (Circle, Diamond, Triangle, Square) -->
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-black/25 backdrop-blur-xs flex items-center justify-center font-black text-base shrink-0 border border-white/20 shadow-inner">
                                <span v-if="answeredQuestionResults[currentQuestion.id]?.selectedOptionId === opt.id && answeredQuestionResults[currentQuestion.id]?.isCorrect">💮</span>
                                <span v-else-if="answeredQuestionResults[currentQuestion.id]?.selectedOptionId === opt.id && !answeredQuestionResults[currentQuestion.id]?.isCorrect">✕</span>
                                <span v-else-if="revealedCorrectOptions[currentQuestion.id] === opt.id && !answeredQuestionResults[currentQuestion.id]?.isCorrect">✓</span>
                                <span v-else-if="idx === 0">●</span>
                                <span v-else-if="idx === 1">◆</span>
                                <span v-else-if="idx === 2">▲</span>
                                <span v-else>■</span>
                            </div>

                            <!-- Option Key & Text -->
                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-black uppercase tracking-wider text-white/70 font-mono">
                                    Pilihan {{ formatOptionKey(opt.option_key, idx) }}
                                </div>
                                <div class="font-jp text-sm sm:text-base font-black leading-snug text-white mt-0.5" v-html="opt.option_text"></div>
                            </div>

                            <!-- Active Selection Badges -->
                            <div v-if="answeredQuestionResults[currentQuestion.id]?.selectedOptionId === opt.id && answeredQuestionResults[currentQuestion.id]?.isCorrect" 
                                class="px-3 py-1 rounded-full bg-emerald-400 text-slate-950 flex items-center justify-center shrink-0 font-black text-[10px] sm:text-xs shadow-lg uppercase tracking-wider">
                                正解 · Benar!
                            </div>
                            <div v-else-if="answeredQuestionResults[currentQuestion.id]?.selectedOptionId === opt.id && !answeredQuestionResults[currentQuestion.id]?.isCorrect" 
                                class="px-3 py-1 rounded-full bg-rose-400 text-white flex items-center justify-center shrink-0 font-black text-[10px] sm:text-xs shadow-lg uppercase tracking-wider">
                                不正解 · Salah
                            </div>
                            <div v-else-if="revealedCorrectOptions[currentQuestion.id] === opt.id && !answeredQuestionResults[currentQuestion.id]?.isCorrect" 
                                class="px-3 py-1 rounded-full bg-emerald-300 text-slate-950 flex items-center justify-center shrink-0 font-black text-[10px] sm:text-xs shadow-lg uppercase tracking-wider">
                                Jawaban Benar
                            </div>
                            <div v-else-if="currentSelectedOptionId === opt.id" class="px-3 py-1 rounded-full bg-white text-slate-950 flex items-center justify-center shrink-0 font-black text-[10px] sm:text-xs shadow-lg uppercase tracking-wider">
                                Dipilih
                            </div>
                        </button>
                    </div>
                </div>
            </main>

            <!-- ================= DESKTOP PALETTE SIDEBAR ================= -->
            <aside 
                class="hidden lg:flex p-6 flex-col justify-between shrink-0 transition-colors duration-300"
                :class="cbtMode === 'game' 
                    ? 'w-80 bg-slate-900/95 border-l border-slate-800 text-white shadow-2xl' 
                    : 'w-76 bg-white border-l border-slate-200/80 text-slate-900 shadow-[0_4px_25px_rgba(0,0,0,0.02)]'"
            >
                <div>
                    <!-- Mini Progress Status -->
                    <div class="p-4 rounded-2xl border mb-5 transition-colors"
                        :class="cbtMode === 'game' ? 'bg-slate-800/80 border-slate-700' : 'bg-slate-50 border-slate-200/80'">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-black uppercase tracking-wider"
                                :class="cbtMode === 'game' ? 'text-cyan-400' : 'text-slate-700'">
                                {{ cbtMode === 'game' ? 'Quest Progress' : 'Capaian Pengerjaan' }}
                            </span>
                            <span class="text-xs font-black font-mono"
                                :class="cbtMode === 'game' ? 'text-emerald-400' : 'text-emerald-700'">
                                {{ progressPercentage }}%
                            </span>
                        </div>
                        <div class="w-full rounded-full h-2 overflow-hidden"
                            :class="cbtMode === 'game' ? 'bg-slate-700' : 'bg-slate-200'">
                            <div class="h-full rounded-full transition-all duration-500"
                                :class="cbtMode === 'game' ? 'bg-gradient-to-r from-amber-400 via-rose-500 to-cyan-400' : 'bg-gradient-to-r from-emerald-500 to-teal-500'"
                                :style="{ width: `${progressPercentage}%` }"></div>
                        </div>
                        <p class="text-[11px] mt-2 flex items-center justify-between"
                            :class="cbtMode === 'game' ? 'text-slate-400' : 'text-slate-500'">
                            <span>Selesai: <strong class="font-mono" :class="cbtMode === 'game' ? 'text-white' : 'text-slate-800'">{{ answeredCount }}</strong></span>
                            <span>Sisa: <strong class="font-mono" :class="cbtMode === 'game' ? 'text-white' : 'text-slate-800'">{{ questions.length - answeredCount }}</strong></span>
                        </p>
                    </div>

                    <!-- Legend -->
                    <div class="flex items-center justify-between text-[11px] font-bold mb-4 pb-3 border-b"
                        :class="cbtMode === 'game' ? 'text-slate-400 border-slate-800' : 'text-slate-500 border-slate-100'">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-xs"></span>
                            <span>{{ cbtMode === 'game' ? 'Lolos' : 'Dijawab' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-xs"></span>
                            <span>Ragu</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full border"
                                :class="cbtMode === 'game' ? 'bg-slate-800 border-slate-700' : 'bg-slate-200 border-slate-300'"></span>
                            <span>Belum</span>
                        </div>
                    </div>

                    <!-- Modern Keycaps Grid Numbers -->
                    <div class="grid grid-cols-5 gap-2 max-h-[calc(100vh-370px)] overflow-y-auto pr-1">
                        <button 
                            v-for="(q, idx) in questions" 
                            :key="q.id"
                            @click="currentQuestionIndex = idx"
                            type="button"
                            class="h-10 rounded-xl font-bold text-xs flex items-center justify-center transition-all cursor-pointer border relative"
                            :class="[
                                currentQuestionIndex === idx 
                                    ? (cbtMode === 'game' 
                                        ? 'ring-2 ring-amber-400 ring-offset-2 ring-offset-slate-900 scale-105 z-10 font-black shadow-lg shadow-amber-400/30 bg-gradient-to-br from-amber-400 to-yellow-500 text-slate-950' 
                                        : 'ring-2 ring-japan-red ring-offset-2 scale-105 z-10 font-black shadow-md')
                                    : '',
                                userAnswers[q.id]?.is_doubtful 
                                    ? 'bg-gradient-to-br from-amber-400 to-amber-500 text-slate-950 border-amber-400 font-black shadow-xs' 
                                    : userAnswers[q.id]?.question_option_id 
                                        ? (cbtMode === 'game' 
                                            ? 'bg-gradient-to-br from-emerald-600 to-teal-700 text-white border-emerald-500/80 shadow-[0_0_8px_rgba(16,185,129,0.3)]' 
                                            : 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white border-emerald-500 shadow-xs')
                                        : (cbtMode === 'game' 
                                            ? 'bg-slate-800/80 text-slate-400 border-slate-700 hover:bg-slate-700 hover:text-white' 
                                            : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100')
                            ]"
                        >
                            {{ idx + 1 }}
                        </button>
                    </div>
                </div>

                <!-- Anti-Cheat Status in Sidebar -->
                <div class="pt-4 border-t text-[11px] space-y-1.5"
                    :class="cbtMode === 'game' ? 'border-slate-800 text-slate-400' : 'border-slate-100 text-slate-500'">
                    <div class="flex items-center gap-1.5 font-bold"
                        :class="violationCount > 0 ? (cbtMode === 'game' ? 'text-amber-400' : 'text-amber-700') : (cbtMode === 'game' ? 'text-emerald-400' : 'text-emerald-700')">
                        <ShieldAlert v-if="violationCount > 0" class="w-4 h-4" />
                        <ShieldCheck v-else class="w-4 h-4" />
                        <span>{{ violationCount === 0 ? 'Integritas: Tertib (0 Pelanggaran)' : `${violationCount}x Peringatan Tab` }}</span>
                    </div>
                    <p class="text-[10px] leading-relaxed" :class="cbtMode === 'game' ? 'text-slate-500' : 'text-slate-400'">
                        Sistem live monitoring aktif. Dilarang berpindah tab.
                    </p>
                </div>
            </aside>
        </div>

        <!-- ================= FLOATING BOTTOM CAPSULE NAVIGATION BAR (FORMAL MODE ONLY) ================= -->
        <div v-if="cbtMode !== 'game'" class="fixed bottom-4 left-3 right-3 sm:left-1/2 sm:-translate-x-1/2 sm:w-full sm:max-w-xl z-30">
            <div 
                class="backdrop-blur-xl border p-2 rounded-full flex items-center justify-between gap-2 transition-colors duration-300"
                :class="cbtMode === 'game' 
                    ? 'bg-slate-900/95 border-slate-700 text-white shadow-[0_10px_35px_rgba(0,0,0,0.8)]' 
                    : 'bg-white/95 border-slate-200/90 text-slate-900 shadow-[0_10px_35px_rgba(0,0,0,0.12)]'"
            >
                <!-- Prev Button -->
                <button 
                    @click="prevQuestion" 
                    :disabled="currentQuestionIndex === 0"
                    class="px-4 sm:px-5 py-2.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
                    :class="cbtMode === 'game' ? 'bg-slate-800 hover:bg-slate-700 text-slate-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-800'"
                >
                    <ChevronLeft class="w-4 h-4" />
                    <span class="hidden sm:inline">Sebelumnya</span>
                </button>

                <!-- Center Question Info (Clickable on Mobile/Tablet to open Palette) -->
                <div class="flex items-center gap-2">
                    <button 
                        @click="showMobilePalette = true"
                        type="button"
                        class="text-xs font-black font-mono px-3 py-1 rounded-full border flex items-center gap-1.5 cursor-pointer transition-all hover:scale-105 shadow-2xs"
                        :class="cbtMode === 'game' ? 'bg-slate-800 text-amber-300 border-slate-700' : 'bg-slate-50 hover:bg-slate-100 text-slate-800 border-slate-200'"
                        title="Buka Palet Nomor Soal"
                    >
                        <span>{{ currentQuestionIndex + 1 }} / {{ questions.length }}</span>
                        <ChevronUp class="w-3.5 h-3.5 text-slate-400" />
                    </button>
                    <button 
                        @click="toggleFlag"
                        type="button" 
                        class="p-2 rounded-full border transition-all cursor-pointer"
                        :class="isCurrentFlagged 
                            ? 'bg-amber-400 text-slate-950 border-amber-400' 
                            : cbtMode === 'game' 
                                ? 'bg-slate-800 text-slate-400 border-slate-700 hover:bg-slate-700' 
                                : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200'"
                        :title="isCurrentFlagged ? 'Hapus Tanda Ragu' : 'Tandai Ragu-Ragu'"
                    >
                        <Bookmark class="w-3.5 h-3.5" :class="isCurrentFlagged ? 'fill-slate-950 text-slate-950' : ''" />
                    </button>
                </div>

                <!-- Next or Finish Button -->
                <button 
                    v-if="currentQuestionIndex < questions.length - 1"
                    @click="nextQuestion" 
                    class="px-5 sm:px-6 py-2.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-sm"
                    :class="cbtMode === 'game' ? 'bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-black' : 'bg-slate-900 hover:bg-slate-800 text-white'"
                >
                    <span>Berikutnya</span>
                    <ChevronRight class="w-4 h-4" />
                </button>

                <button 
                    v-else
                    @click="cbtMode === 'game' ? (isStageClearedModal = true) : confirmSubmit()"
                    class="px-5 sm:px-6 py-2.5 rounded-full text-white text-xs font-black transition-all shadow-md flex items-center gap-1.5 cursor-pointer"
                    :class="cbtMode === 'game' 
                        ? 'bg-gradient-to-r from-amber-500 to-red-600 hover:from-amber-400 hover:to-red-500 shadow-amber-500/30' 
                        : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-emerald-600/20'"
                >
                    <CheckCircle class="w-4 h-4" />
                    <span>{{ cbtMode === 'game' ? 'Hasil Battle' : 'Selesai & Kirim' }}</span>
                </button>
            </div>
        </div>

        <!-- ================= MOBILE PALETTE BOTTOM-SHEET DRAWER ================= -->
        <div v-if="showMobilePalette" class="fixed inset-0 z-50 flex flex-col justify-end lg:hidden animate-fade-in">
            <div class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs" @click="showMobilePalette = false"></div>

            <div class="relative bg-white rounded-t-3xl sm:rounded-t-4xl shadow-2xl p-5 border-t border-slate-200 max-h-[85vh] flex flex-col z-10 animate-slide-up">
                <!-- Mobile Drawer Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-xl bg-japan-red text-white flex items-center justify-center font-black text-xs">
                            📋
                        </div>
                        <h3 class="text-sm font-extrabold text-slate-900">Palet Nomor Soal CBT</h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-emerald-700 font-mono">
                            {{ answeredCount }}/{{ questions.length }} Terjawab
                        </span>
                        <button @click="showMobilePalette = false" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:text-slate-800 cursor-pointer">
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Mobile Drawer Legend -->
                <div class="flex items-center justify-center gap-4 text-[11px] font-bold text-slate-500 py-3 border-b border-slate-100">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span>Dijawab</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        <span>Ragu-ragu</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-200 border border-slate-300"></span>
                        <span>Belum</span>
                    </div>
                </div>

                <!-- Mobile Drawer Keycaps Grid (Thumb Friendly) -->
                <div class="grid grid-cols-5 gap-2.5 py-4 overflow-y-auto max-h-72">
                    <button 
                        v-for="(q, idx) in questions" 
                        :key="'mob-' + q.id"
                        @click="currentQuestionIndex = idx; showMobilePalette = false"
                        class="h-12 rounded-2xl font-bold text-xs flex items-center justify-center transition-all cursor-pointer border"
                        :class="[
                            currentQuestionIndex === idx ? 'ring-2 ring-japan-red ring-offset-2 scale-105 z-10 font-black shadow-md' : '',
                            userAnswers[q.id]?.is_doubtful 
                                ? 'bg-gradient-to-br from-amber-400 to-amber-500 text-slate-950 border-amber-400 font-black' 
                                : userAnswers[q.id]?.question_option_id 
                                    ? 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white border-emerald-500 shadow-xs' 
                                    : 'bg-slate-50 text-slate-700 border-slate-200'
                        ]"
                    >
                        {{ idx + 1 }}
                    </button>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <button 
                        @click="showMobilePalette = false"
                        class="w-full py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all cursor-pointer shadow-md"
                    >
                        Tutup Palet &amp; Lanjutkan Mengerjakan
                    </button>
                </div>
            </div>
        </div>

        <!-- ================= ANTI-CHEAT VIOLATION MODAL ALERT ================= -->
        <div v-if="showViolationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs animate-fade-in">
            <div class="bg-white border border-rose-300 rounded-3xl p-6 sm:p-8 max-w-md w-full text-center shadow-2xl text-slate-800">
                <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center mx-auto mb-4 animate-bounce">
                    <ShieldAlert class="w-8 h-8" />
                </div>
                <h3 class="text-lg font-black text-rose-700">
                    PERINGATAN PELANGGARAN ({{ violationCount }}/3)
                </h3>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed font-medium">
                    {{ violationMessage || 'Anda terdeteksi berpindah tab browser / meminimalkan aplikasi ujian.' }}
                </p>
                <div class="mt-4 p-3 rounded-xl bg-rose-50/60 border border-rose-200 text-[11px] text-rose-900 text-left font-mono">
                    ⚠️ Data pelanggaran beserta waktu detik ini telah otomatis dicatat ke <strong>Dashboard Pengawas Sensei</strong>.
                </div>
                <button 
                    @click="resumeAfterViolation"
                    class="w-full mt-5 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black transition-all cursor-pointer shadow-md"
                >
                    Saya Mengerti &amp; Kembali ke Ujian Fullscreen
                </button>
            </div>
        </div>

        <!-- ================= DISQUALIFIED MODAL ================= -->
        <div v-if="isDisqualified" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="bg-white border border-rose-500 rounded-3xl p-8 max-w-md w-full text-center shadow-2xl text-slate-800">
                <div class="w-16 h-16 rounded-full bg-rose-600 text-white flex items-center justify-center mx-auto mb-4">
                    <AlertTriangle class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-black text-rose-600">UJIAN DIDISKUALIFIKASI</h3>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Sesi ujian Anda telah dibatalkan oleh Pengawas Ujian Sensei karena terindikasi melakukan pelanggaran berulang.
                </p>
                <Link :href="route('siswa.dashboard')" class="inline-block mt-6 px-6 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                    Kembali ke Dashboard
                </Link>
            </div>
        </div>

        <!-- ================= GAME FEEDBACK OVERLAY (4 RANDOM VICTORY & 4 RANDOM DEFEAT MODELS) ================= -->
        <GameFeedbackModal 
            :show="gameFeedback.show" 
            :is-correct="gameFeedback.isCorrect" 
            :model-index="gameFeedback.modelIndex" 
            :streak="gameStreak" 
        />

        <!-- ================= LIVE LEADERBOARD OVERLAY (QUIZIZZ STYLE) ================= -->
        <GameLeaderboardOverlay 
            :show="showLeaderboard" 
            :leaderboard="liveLeaderboard" 
            :current-user-rank="currentUserRank" 
            :rank-diff="currentRankDiff" 
            :user-score="gameScore"
            :user-streak="gameStreak"
            :current-user-name="currentUser?.name"
            @proceed="proceedFromLeaderboard" 
        />

        <!-- ================= STAGE CLEARED / VICTORY SUMMARY MODAL (GAME MODE) ================= -->
        <div v-if="isStageClearedModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-xl animate-fade-in select-none">
            <div class="relative max-w-xl sm:max-w-2xl w-full bg-gradient-to-b from-[#1c1642] via-slate-900 to-slate-950 border-2 border-amber-400/50 rounded-3xl p-6 sm:p-9 text-center text-white shadow-[0_25px_60px_rgba(0,0,0,0.85)] overflow-hidden">
                <!-- Golden Ambient Aura -->
                <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-80 h-80 rounded-full bg-amber-400/20 blur-3xl pointer-events-none"></div>

                <!-- Trophy Icon / Badge -->
                <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 rounded-3xl bg-gradient-to-tr from-amber-500 via-yellow-400 to-amber-200 text-slate-950 flex items-center justify-center text-4xl sm:text-5xl shadow-[0_0_35px_rgba(251,191,36,0.6)] animate-bounce-subtle">
                    🏆
                </div>

                <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-black uppercase tracking-wider mb-2">
                    <span>⚔️</span>
                    <span>STAGE CLEARED · BATTLE CBT SELESAI</span>
                </div>

                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">HASIL PERMAINAN CBT</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">Seluruh butir soal telah berhasil diselesaikan secara langsung.</p>

                <!-- Stats Grid (Spacious & Clean) -->
                <div class="grid grid-cols-3 gap-3.5 my-6 p-4.5 rounded-2xl bg-white/5 border border-white/10">
                    <div class="p-3 rounded-2xl bg-slate-800/70 border border-slate-700/60 shadow-inner">
                        <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Total EXP</span>
                        <span class="text-xl sm:text-2xl font-black text-amber-400 font-mono mt-0.5 block">{{ gameScore.toLocaleString() }}</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-800/70 border border-slate-700/60 shadow-inner">
                        <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Benar</span>
                        <span class="text-xl sm:text-2xl font-black text-emerald-400 font-mono mt-0.5 block">{{ gameCorrectCount }} / {{ questions.length }}</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-800/70 border border-slate-700/60 shadow-inner">
                        <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Akurasi</span>
                        <span class="text-xl sm:text-2xl font-black text-cyan-400 font-mono mt-0.5 block">{{ gameAccuracy }}%</span>
                    </div>
                </div>

                <!-- Pass / Fail Badge -->
                <div class="mb-6 p-3.5 rounded-2xl border flex items-center justify-center gap-2 font-black text-xs sm:text-sm"
                    :class="isGamePassed ? 'bg-emerald-500/20 border-emerald-500/40 text-emerald-300' : 'bg-rose-500/20 border-rose-500/40 text-rose-300'">
                    <span>{{ isGamePassed ? '🎉 GOUKAKU (合格) · LULUS PASSING GRADE!' : '⚠️ FUGOUKAKU (不合格) · PERLU LATIHAN LAGI' }}</span>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button 
                        v-if="session?.is_preview" 
                        @click="resetPreviewGame" 
                        type="button" 
                        class="w-full sm:flex-1 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-black transition-all cursor-pointer border border-slate-700"
                    >
                        🔄 Ulangi Simulasi (Reset)
                    </button>
                    <button 
                        v-if="session?.is_preview" 
                        @click="closePreviewWindow" 
                        type="button" 
                        class="w-full sm:flex-1 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 text-xs font-black transition-all cursor-pointer shadow-lg shadow-emerald-500/20"
                    >
                        ✓ Selesai &amp; Tutup Preview
                    </button>
                    <button 
                        v-else 
                        @click="executeSubmit" 
                        type="button" 
                        class="w-full py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 text-xs font-black transition-all cursor-pointer shadow-lg shadow-emerald-500/20"
                    >
                        🚀 Kirim Lembar Ujian &amp; Lihat Rapor
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { confirmDialog } from '@/Utils/alert';
import { 
    Clock, 
    Maximize2, 
    Minimize2, 
    CheckCircle, 
    CheckCircle2, 
    RefreshCw, 
    CloudOff, 
    ChevronLeft, 
    ChevronRight, 
    Bookmark, 
    Volume2, 
    ShieldAlert, 
    ShieldCheck, 
    AlertTriangle, 
    Grid, 
    Play, 
    Pause,
    Gamepad2,
    Flame,
    Heart,
    Zap,
    Sparkles,
    Award,
    VolumeX,
    ChevronUp
} from 'lucide-vue-next';
import { sfx } from '@/Utils/sfx';
import GameFeedbackModal from '@/Components/Cbt/GameFeedbackModal.vue';
import GameLeaderboardOverlay from '@/Components/Cbt/GameLeaderboardOverlay.vue';

const props = defineProps({
    session: Object,
    exam: Object,
    questions: Array,
    existingAnswers: Object,
    initialLeaderboard: Array,
});

const page = usePage();
const currentUser = computed(() => page.props.auth?.user || { name: 'Trainee' });

// ================= CBT DISPLAY MODE =================
// Mode ujian 100% diputuskan oleh Sensei melalui database ('game' vs 'formal')
const cbtMode = computed(() => (props.exam?.display_mode === 'game' ? 'game' : 'formal'));

// Game Mode Interactive States (Wayground / Quizizz Style)
const gameScore = ref(0);
const gameStreak = ref(0);
const isSfxMuted = ref(false);

// Game Feedback Modal & Instant Evaluation States
const gameFeedback = ref({
    show: false,
    isCorrect: true,
    modelIndex: 0,
});
const isGameEvaluating = ref(false);
const gameWrongCount = ref(0);
const revealedCorrectOptions = ref({});
const answeredQuestionResults = ref({});
const isStageClearedModal = ref(false);

// Live Leaderboard (Zero Dummy, 100% Real Database Data)
const showLeaderboard = ref(false);
const currentRankDiff = ref(0);
const dbLeaderboard = ref(props.initialLeaderboard || []);

const liveLeaderboard = computed(() => {
    const userEntry = {
        id: 'me',
        name: currentUser.value?.name || 'Trainee',
        score: gameScore.value,
        isCurrentUser: true,
    };
    // Keep other real participants from the database
    const others = (dbLeaderboard.value || []).filter(p => !p.isCurrentUser && p.user_id !== currentUser.value?.id);
    return [userEntry, ...others].sort((a, b) => b.score - a.score);
});

const currentUserRank = computed(() => {
    const idx = liveLeaderboard.value.findIndex(p => p.isCurrentUser);
    return idx >= 0 ? idx + 1 : 1;
});

const proceedFromLeaderboard = () => {
    showLeaderboard.value = false;
    isGameEvaluating.value = false;

    if (currentQuestionIndex.value < props.questions.length - 1) {
        currentQuestionIndex.value++;
    } else {
        // Reached last question! Open Stage Cleared Modal!
        isStageClearedModal.value = true;
        sfx.playVictory();
    }
};

const toggleSfx = () => {
    isSfxMuted.value = sfx.toggleMute();
};

const formatOptionKey = (key, idx) => {
    if (!key && key !== 0) return String.fromCharCode(65 + (idx || 0));
    const normalized = String(key).trim().toUpperCase();
    const map = { '1': 'A', '2': 'B', '3': 'C', '4': 'D', '5': 'E' };
    return map[normalized] || normalized;
};

// Gamification Computed States
const currentLevel = computed(() => {
    return Math.min(50, Math.floor(answeredCount.value / 2) + 1);
});

const remainingHearts = computed(() => {
    if (cbtMode.value === 'game') {
        return Math.max(0, 5 - gameWrongCount.value);
    }
    return Math.max(1, 5 - violationCount.value);
});

const gameCorrectCount = computed(() => {
    return Object.values(answeredQuestionResults.value).filter(r => r.isCorrect).length;
});

const gameAccuracy = computed(() => {
    if (!props.questions || props.questions.length === 0) return 0;
    return Math.round((gameCorrectCount.value / props.questions.length) * 100);
});

const isGamePassed = computed(() => {
    const passingScore = props.exam?.passing_score || 90;
    const maxScore = props.exam?.max_score || 180;
    const calculatedScore = Math.round((gameCorrectCount.value / (props.questions?.length || 1)) * maxScore);
    return calculatedScore >= passingScore;
});

const resetPreviewGame = () => {
    answeredQuestionResults.value = {};
    revealedCorrectOptions.value = {};
    userAnswers.value = {};
    currentQuestionIndex.value = 0;
    gameScore.value = 0;
    gameStreak.value = 0;
    gameWrongCount.value = 0;
    showLeaderboard.value = false;
    currentRankDiff.value = 0;
    isStageClearedModal.value = false;
};

const closePreviewWindow = () => {
    window.close();
};

const currentStreak = computed(() => {
    return Math.min(10, Math.max(1, Math.floor(answeredCount.value / 3) + 1));
});

const currentQuestionIndex = ref(0);
const isFullscreen = ref(false);
const timerSeconds = ref(Math.max(0, Math.floor(Number(props.session?.remaining_seconds) || 3600)));
let timerInterval = null;
let periodicSyncInterval = null;
const showMobilePalette = ref(false);

// Choukai Audio Engine Anti-Cheat State
const audioPlayerRef = ref(null);
const isAudioPlaying = ref(false);
const audioCurrentTime = ref(0);
const audioDuration = ref(0);
const audioPlayHistory = ref({});

const currentQuestionAudioPlays = computed(() => {
    return audioPlayHistory.value[currentQuestion.value?.id] || 0;
});

const maxAudioPlays = computed(() => {
    return Number(currentQuestion.value?.audio_play_limit) || 1;
});

const isAudioLimitReached = computed(() => {
    return currentQuestionAudioPlays.value >= maxAudioPlays.value;
});

const formatDuration = (seconds) => {
    if (!seconds || isNaN(seconds) || seconds < 0) return '00:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
};

const toggleAudioPlay = () => {
    if (!audioPlayerRef.value) return;

    if (isAudioPlaying.value) {
        audioPlayerRef.value.pause();
        isAudioPlaying.value = false;
    } else {
        if (isAudioLimitReached.value) return;
        audioPlayerRef.value.play().then(() => {
            isAudioPlaying.value = true;
        }).catch((err) => {
            console.error('Audio play error:', err);
        });
    }
};

const onAudioTimeUpdate = () => {
    if (audioPlayerRef.value) {
        audioCurrentTime.value = audioPlayerRef.value.currentTime;
    }
};

const onAudioLoadedMetadata = () => {
    if (audioPlayerRef.value) {
        audioDuration.value = audioPlayerRef.value.duration;
        audioCurrentTime.value = 0;
    }
};

const onAudioEnded = () => {
    isAudioPlaying.value = false;
    const qId = currentQuestion.value?.id;
    if (qId) {
        audioPlayHistory.value[qId] = (audioPlayHistory.value[qId] || 0) + 1;
    }
};

// Reset audio when question changes
watch(currentQuestionIndex, () => {
    if (audioPlayerRef.value) {
        audioPlayerRef.value.pause();
    }
    isAudioPlaying.value = false;
    audioCurrentTime.value = 0;
    audioDuration.value = 0;
});

// Anti-Cheat State
const violationCount = ref(props.session?.violation_count || 0);
const isDisqualified = ref(Boolean(props.session?.is_disqualified));
const showViolationModal = ref(false);
const violationMessage = ref('');

// ================= OFFLINE BUFFER & LIVE SYNC ENGINE =================
const storageKey = `cbt_answers_session_${props.session.id}`;
const syncStatus = ref('synced'); // 'synced' | 'syncing' | 'offline'
const pendingSyncQueue = ref(new Map());
let syncDebounceTimer = null;

// Initialize userAnswers: merge server existingAnswers with local storage fallback
const initAnswers = () => {
    if (props.session?.is_preview) {
        try {
            localStorage.removeItem(storageKey);
        } catch (e) {}
        return {};
    }
    const initial = { ...props.existingAnswers };
    try {
        const savedLocal = localStorage.getItem(storageKey);
        if (savedLocal) {
            const parsed = JSON.parse(savedLocal);
            Object.assign(initial, parsed);
        }
    } catch (e) {
        console.warn('Gagal membaca local storage CBT', e);
    }
    return initial;
};

const userAnswers = ref(initAnswers());

// Simpan ke local storage seketika (0ms, proteksi crash browser/HP)
const persistToLocalStorage = () => {
    if (props.session?.is_preview) return;
    try {
        localStorage.setItem(storageKey, JSON.stringify(userAnswers.value));
    } catch (e) {}
};

// Kirim antrean jawaban ke server secara asinkron (Batch Support)
const flushSyncQueue = async () => {
    if (props.session?.is_preview) {
        pendingSyncQueue.value.clear();
        syncStatus.value = 'synced';
        return;
    }

    if (pendingSyncQueue.value.size === 0) {
        syncStatus.value = 'synced';
        return;
    }

    const payload = Array.from(pendingSyncQueue.value.values());
    syncStatus.value = 'syncing';

    try {
        await axios.post(route('siswa.cbt.save-answer', props.session.id), {
            answers: payload
        });

        // Hapus item yang sukses terkirim dari antrean
        payload.forEach(item => {
            const cur = pendingSyncQueue.value.get(item.question_id);
            if (cur && cur.question_option_id === item.question_option_id && cur.is_doubtful === item.is_doubtful) {
                pendingSyncQueue.value.delete(item.question_id);
            }
        });

        if (pendingSyncQueue.value.size === 0) {
            syncStatus.value = 'synced';
        } else {
            scheduleSync();
        }
    } catch (err) {
        syncStatus.value = 'offline';
    }
};

// Debounce trigger (500ms) agar mengoptimalkan throughput server saat 100 siswa mengklik
const scheduleSync = () => {
    syncStatus.value = 'syncing';
    if (syncDebounceTimer) clearTimeout(syncDebounceTimer);
    syncDebounceTimer = setTimeout(() => {
        flushSyncQueue();
    }, 500);
};

// Antrekan perubahan jawaban
const queueAnswerChange = (questionId, optionId, isDoubtful) => {
    pendingSyncQueue.value.set(questionId, {
        question_id: questionId,
        question_option_id: optionId,
        is_doubtful: Boolean(isDoubtful),
    });
    persistToLocalStorage();
    scheduleSync();
};

const currentQuestion = computed(() => {
    return props.questions[currentQuestionIndex.value] || props.questions[0] || {
        id: 0,
        number: 1,
        level: props.exam?.level || 'N4',
        score_points: 1,
        category_name: 'Tata Bahasa',
        question_text: 'Paket ujian ini belum memuat butir pertanyaan yang aktif.',
        options: [],
    };
});

const currentSelectedOptionId = computed(() => {
    return userAnswers.value[currentQuestion.value?.id]?.question_option_id || null;
});

const isCurrentFlagged = computed(() => {
    return userAnswers.value[currentQuestion.value?.id]?.is_doubtful || false;
});

const answeredCount = computed(() => {
    return Object.values(userAnswers.value).filter(a => a.question_option_id).length;
});

const progressPercentage = computed(() => {
    if (!props.questions || props.questions.length === 0) return 0;
    return Math.min(100, Math.round((answeredCount.value / props.questions.length) * 100));
});

const formattedTimer = computed(() => {
    const totalSec = Math.max(0, Math.floor(Number(timerSeconds.value) || 0));
    const hours = Math.floor(totalSec / 3600);
    const minutes = Math.floor((totalSec % 3600) / 60);
    const seconds = totalSec % 60;
    const mStr = minutes.toString().padStart(2, '0');
    const sStr = seconds.toString().padStart(2, '0');
    if (hours > 0) {
        return `${hours.toString().padStart(2, '0')}:${mStr}:${sStr}`;
    }
    return `${mStr}:${sStr}`;
});

// Fullscreen Logic
const requestFullscreen = () => {
    const elem = document.documentElement;
    if (!document.fullscreenElement) {
        if (elem.requestFullscreen) {
            elem.requestFullscreen().catch(() => {});
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        }
        isFullscreen.value = true;
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen().catch(() => {});
        }
        isFullscreen.value = false;
    }
};

// Answer Selection & Auto-Save
const selectOption = (optionId) => {
    const qId = currentQuestion.value.id;
    const isSame = userAnswers.value[qId]?.question_option_id === optionId;
    const newOptionId = isSame && cbtMode.value !== 'game' ? null : optionId;

    // FORMAL MODE: Standard clean selection
    if (cbtMode.value !== 'game') {
        userAnswers.value[qId] = {
            ...userAnswers.value[qId],
            question_option_id: newOptionId,
        };
        queueAnswerChange(qId, newOptionId, userAnswers.value[qId]?.is_doubtful || false);
        return;
    }

    // GAME MODE (Arcade Instant Evaluation & Randomized Animations):
    // 1. Lock if currently evaluating animation or question already finished
    if (isGameEvaluating.value) return;
    if (answeredQuestionResults.value[qId]) return;

    isGameEvaluating.value = true;
    sfx.playPop();

    // 2. Record selection locally
    userAnswers.value[qId] = {
        ...userAnswers.value[qId],
        question_option_id: optionId,
        is_doubtful: false,
    };
    persistToLocalStorage();

    const opt = currentQuestion.value.options?.find(o => o.id === optionId);
    const correctOpt = currentQuestion.value.options?.find(o => o.is_correct);

    // 3. Evaluate answer immediately
    if (props.session?.is_preview) {
        const isCorrect = Boolean(opt?.is_correct);
        const correctOptId = correctOpt?.id || null;
        triggerGameEvaluation(qId, optionId, isCorrect, correctOptId);
    } else {
        // Live student exam session: immediately send answer with mode: 'game'
        axios.post(route('siswa.cbt.save-answer', props.session.id), {
            answers: [{
                question_id: qId,
                question_option_id: optionId,
                is_doubtful: false,
            }],
            mode: 'game'
        }).then((res) => {
            const apiCorrect = res.data?.is_correct ?? (opt?.is_correct ?? false);
            const apiCorrectOptId = res.data?.correct_option_id ?? (correctOpt?.id ?? null);
            if (res.data?.live_leaderboard) {
                dbLeaderboard.value = res.data.live_leaderboard;
            }
            triggerGameEvaluation(qId, optionId, Boolean(apiCorrect), apiCorrectOptId);
        }).catch(() => {
            triggerGameEvaluation(qId, optionId, Boolean(opt?.is_correct), correctOpt?.id || null);
        });
    }
};

const triggerGameEvaluation = (qId, optionId, isCorrect, correctOptionId) => {
    if (correctOptionId) {
        revealedCorrectOptions.value[qId] = correctOptionId;
    }
    answeredQuestionResults.value[qId] = {
        isCorrect: isCorrect,
        selectedOptionId: optionId,
    };

    // Pick 1 of 4 models randomly (0, 1, 2, 3)
    const randModel = Math.floor(Math.random() * 4);

    const prevRank = currentUserRank.value;

    if (isCorrect) {
        gameStreak.value++;
        const expGained = 250 + (gameStreak.value * 50);
        gameScore.value += expGained;
        sfx.playVictory();
    } else {
        gameStreak.value = 0;
        gameWrongCount.value++;
        sfx.playExplosion();
    }

    const newRank = currentUserRank.value;
    currentRankDiff.value = Math.max(0, prevRank - newRank);

    gameFeedback.value = {
        show: true,
        isCorrect: isCorrect,
        modelIndex: randModel,
    };

    // Keep feedback animation visible for 1.1s, then transition into live leaderboard overlay
    setTimeout(() => {
        gameFeedback.value.show = false;
        showLeaderboard.value = true;
    }, 1100);
};

const toggleFlag = () => {
    const qId = currentQuestion.value.id;
    const currentFlag = Boolean(userAnswers.value[qId]?.is_doubtful);
    userAnswers.value[qId] = {
        ...userAnswers.value[qId],
        is_doubtful: !currentFlag,
    };

    queueAnswerChange(qId, userAnswers.value[qId]?.question_option_id || null, !currentFlag);
};

const nextQuestion = () => {
    if (currentQuestionIndex.value < props.questions.length - 1) {
        currentQuestionIndex.value++;
    }
};

const prevQuestion = () => {
    if (currentQuestionIndex.value > 0) {
        currentQuestionIndex.value--;
    }
};

// Anti-Cheat Violation Reporting
const reportViolation = (type, message) => {
    if (isDisqualified.value || props.session?.is_preview) return;

    violationCount.value++;
    violationMessage.value = message;
    showViolationModal.value = true;

    axios.post(route('siswa.cbt.log-violation', props.session.id), {
        type: type,
        message: message,
    }).then((res) => {
        if (res.data?.is_disqualified) {
            isDisqualified.value = true;
        }
    }).catch(() => {});
};

const handleVisibilityChange = () => {
    if (document.hidden) {
        reportViolation('tab_switch', 'Terdeteksi berpindah tab browser atau meminimalkan browser.');
    }
};

const handleWindowBlur = () => {
    reportViolation('window_blur', 'Layar ujian kehilangan fokus (membuka aplikasi lain/AI).');
};

const handleFullscreenChange = () => {
    isFullscreen.value = Boolean(document.fullscreenElement);
    if (!document.fullscreenElement && !showViolationModal.value && !isDisqualified.value) {
        reportViolation('tab_switch', 'Terdeteksi keluar dari mode Fullscreen ujian.');
    }
};

const resumeAfterViolation = () => {
    showViolationModal.value = false;
    requestFullscreen();
};

// Eksekusi pengiriman lembar ujian dengan jaminan data terkirim 100%
const executeSubmit = () => {
    if (props.session?.is_preview) {
        alert("Mode Preview Ujian Selesai!\n\nSeluruh alur dan komponen ujian berhasil disimulasikan. Anda dapat menutup tab ini.");
        window.close();
        return;
    }

    const pendingAnswers = Object.entries(userAnswers.value).map(([qId, ans]) => ({
        question_id: Number(qId),
        question_option_id: ans.question_option_id || null,
        is_doubtful: Boolean(ans.is_doubtful),
    }));

    router.post(route('siswa.cbt.submit', props.session.id), {
        pending_answers: pendingAnswers,
    }, {
        onSuccess: () => {
            try {
                localStorage.removeItem(storageKey);
            } catch (e) {}
        }
    });
};

const confirmSubmit = () => {
    const unanswered = props.questions.length - answeredCount.value;
    const warningText = unanswered > 0 
        ? `Masih ada ${unanswered} soal yang belum dijawab. Apakah Anda yakin ingin menyelesaikan ujian sekarang?`
        : 'Seluruh soal telah dijawab. Apakah Anda yakin ingin mengakhiri sesi ujian ini?';

    confirmDialog('Kirim Lembar Ujian?', warningText).then((result) => {
        if (result.isConfirmed) {
            executeSubmit();
        }
    });
};

// Event listener online / offline
const handleOnline = () => {
    if (pendingSyncQueue.value.size > 0) {
        flushSyncQueue();
    } else {
        syncStatus.value = 'synced';
    }
};

const handleOffline = () => {
    syncStatus.value = 'offline';
};

onMounted(() => {
    try {
        if (props.exam?.id) {
            localStorage.removeItem(`cbt_ui_mode_${props.exam.id}`);
        }
        localStorage.removeItem('cbt_ui_mode');
    } catch (e) {}

    requestFullscreen();

    timerInterval = setInterval(() => {
        if (timerSeconds.value > 0) {
            timerSeconds.value = Math.max(0, Math.floor(timerSeconds.value - 1));
        } else {
            clearInterval(timerInterval);
            executeSubmit();
        }
    }, 1000);

    // Auto-retry flush setiap 15 detik jika masih ada antrean yang belum tersinkron
    periodicSyncInterval = setInterval(() => {
        if (pendingSyncQueue.value.size > 0 && navigator.onLine) {
            flushSyncQueue();
        }
    }, 15000);

    document.addEventListener('visibilitychange', handleVisibilityChange);
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    window.addEventListener('blur', handleWindowBlur);
    window.addEventListener('online', handleOnline);
    window.addEventListener('offline', handleOffline);
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
    if (periodicSyncInterval) clearInterval(periodicSyncInterval);
    if (syncDebounceTimer) clearTimeout(syncDebounceTimer);
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    document.removeEventListener('fullscreenchange', handleFullscreenChange);
    window.removeEventListener('blur', handleWindowBlur);
    window.removeEventListener('online', handleOnline);
    window.removeEventListener('offline', handleOffline);
});
</script>

<style>
.cbt-question-text ruby {
    display: inline-flex;
    flex-direction: column-reverse;
    line-height: 1.25;
}
.cbt-question-text rt {
    font-size: 0.58em;
    color: #e11d48;
    text-align: center;
    user-select: none;
    font-weight: 700;
}

@keyframes screen-shake {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    15% { transform: translate(-10px, 8px) rotate(-1.5deg); }
    30% { transform: translate(10px, -8px) rotate(1.5deg); }
    45% { transform: translate(-8px, -5px) rotate(-1deg); }
    60% { transform: translate(8px, 5px) rotate(1deg); }
    75% { transform: translate(-5px, 3px) rotate(-0.5deg); }
    90% { transform: translate(3px, -3px) rotate(0.5deg); }
}

.animate-screen-shake {
    animation: screen-shake 0.45s cubic-bezier(.36,.07,.19,.97) both;
}

@keyframes bomb-pulse {
    0% { transform: scale(0.6) rotate(-10deg); opacity: 0; }
    50% { transform: scale(1.15) rotate(5deg); opacity: 1; }
    70% { transform: scale(0.95) rotate(-3deg); }
    100% { transform: scale(1) rotate(0deg); }
}

.animate-bomb-burst {
    animation: bomb-pulse 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
}

@keyframes victory-bounce {
    0% { transform: scale(0.5); opacity: 0; }
    60% { transform: scale(1.12); opacity: 1; }
    100% { transform: scale(1); }
}

.animate-victory-pop {
    animation: victory-bounce 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
}
</style>
