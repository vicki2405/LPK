<template>
    <div 
        v-if="show" 
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/85 backdrop-blur-xl transition-all duration-300 select-none animate-fade-in"
    >
        <!-- Elegant Spacious Card (max-w-xl to max-w-2xl) -->
        <div class="relative max-w-xl sm:max-w-2xl w-full bg-slate-900/95 border border-slate-700/80 rounded-3xl p-5 sm:p-7 text-white shadow-[0_25px_60px_rgba(0,0,0,0.85)] overflow-hidden flex flex-col gap-4">
            <!-- Top Subtle Ambient Glow -->
            <div class="absolute -top-20 left-1/2 -translate-x-1/2 w-80 h-32 bg-amber-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Header -->
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-800 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500/25 to-yellow-500/10 text-amber-400 border border-amber-500/40 flex items-center justify-center text-lg font-bold shadow-md shadow-amber-500/10 shrink-0">
                        🏆
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm sm:text-base font-black tracking-tight text-white uppercase font-sans">
                                Papan Peringkat CBT
                            </h3>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[9px] font-mono font-bold uppercase tracking-wider">
                                Live Database
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">
                            Skor tertinggi berdasarkan catatan hasil ujian riil
                        </p>
                    </div>
                </div>

                <!-- Your Rank Pill -->
                <div class="px-3.5 py-1.5 rounded-2xl bg-cyan-950/70 border border-cyan-500/40 text-cyan-300 text-xs font-mono font-black flex items-center gap-1.5 shadow-sm">
                    <span>Posisi #{{ currentUserRank }}</span>
                    <span v-if="rankDiff > 0" class="text-emerald-400 font-bold">▲{{ rankDiff }}</span>
                </div>
            </div>

            <!-- Hero Score Spotlight (Your Performance Summary) -->
            <div class="relative z-10 p-4 rounded-2xl bg-gradient-to-r from-purple-950/50 via-slate-800/60 to-indigo-950/50 border border-purple-500/30 flex flex-wrap items-center justify-between gap-3 shadow-inner">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 text-white font-black text-sm flex items-center justify-center shadow-md shadow-cyan-500/30 shrink-0">
                        {{ (currentUserName || 'T').charAt(0) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-sm text-white">{{ currentUserName || 'Anda' }}</span>
                            <span class="px-1.5 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-[10px] font-bold">
                                Kamu
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-300 mt-0.5">
                            <span v-if="userStreak > 0" class="inline-flex items-center gap-1 text-amber-300 font-bold">
                                <span>🔥</span>
                                <span>{{ userStreak }}x Combo</span>
                            </span>
                            <span v-else class="text-slate-400 font-medium">Fokus &amp; Konsisten</span>
                        </div>
                    </div>
                </div>

                <!-- Score Big Counter -->
                <div class="text-right">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block font-mono">Total Skor Terkumpul</span>
                    <div class="flex items-baseline justify-end gap-1">
                        <span class="font-mono font-black text-xl sm:text-2xl text-amber-400 drop-shadow-[0_0_12px_rgba(251,191,36,0.4)]">
                            {{ Number(userScore || 0).toLocaleString() }}
                        </span>
                        <span class="font-mono text-xs text-slate-400 font-bold">pts</span>
                    </div>
                </div>
            </div>

            <!-- Top 5 Leaderboard Standings Table -->
            <div class="relative z-10 space-y-2">
                <div class="flex items-center justify-between px-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <span>Daftar Peserta Teratas</span>
                    <span>Perolehan Nilai</span>
                </div>

                <!-- Empty State if no records in DB yet -->
                <div v-if="sortedLeaderboard.length === 0" class="py-6 px-4 rounded-2xl bg-slate-800/40 border border-slate-800 text-center text-slate-400 text-xs">
                    <p class="font-semibold text-slate-300">Belum ada peserta lain yang tercatat di database.</p>
                    <p class="text-[11px] text-slate-500 mt-1">Anda saat ini menjadi pemegang rekor pertama untuk paket ujian ini!</p>
                </div>

                <!-- Leaderboard Rows -->
                <div 
                    v-for="(p, idx) in sortedLeaderboard.slice(0, 5)" 
                    :key="p.id"
                    class="px-4 py-3 sm:py-3.5 rounded-2xl border transition-all flex items-center justify-between gap-3 text-xs sm:text-sm"
                    :class="p.isCurrentUser 
                        ? 'bg-cyan-950/40 border-cyan-500/70 text-white shadow-md shadow-cyan-950/40 scale-[1.01]' 
                        : idx === 0 
                            ? 'bg-gradient-to-r from-amber-500/10 via-slate-800/40 to-transparent border-amber-500/40 text-slate-200' 
                            : 'bg-slate-800/40 border-slate-800/80 text-slate-300'"
                >
                    <!-- Rank Badge & Participant Name -->
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <!-- Rank Icon / Badge -->
                        <div class="w-8 h-8 rounded-xl font-mono font-black text-xs sm:text-sm flex items-center justify-center shrink-0 shadow-xs"
                            :class="[
                                idx === 0 ? 'bg-gradient-to-tr from-amber-500 to-yellow-300 text-slate-950 font-black shadow-amber-500/30' : '',
                                idx === 1 ? 'bg-gradient-to-tr from-slate-200 to-slate-400 text-slate-950 font-black' : '',
                                idx === 2 ? 'bg-gradient-to-tr from-amber-700 to-amber-600 text-white font-black' : '',
                                idx > 2 ? 'bg-slate-800 text-slate-400 border border-slate-700' : '',
                            ]">
                            {{ idx === 0 ? '🥇' : idx === 1 ? '🥈' : idx === 2 ? '🥉' : idx + 1 }}
                        </div>

                        <!-- Name & Pill -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 truncate">
                                <span class="font-bold truncate text-xs sm:text-sm" :class="p.isCurrentUser ? 'text-cyan-300 font-black' : 'text-slate-200'">
                                    {{ p.name }}
                                </span>
                                <span v-if="p.isCurrentUser" class="text-[9px] font-black px-1.5 py-0.2 rounded bg-cyan-500/25 text-cyan-300 border border-cyan-500/40 shrink-0">
                                    Kamu
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Score Points with Mono Font -->
                    <div class="text-right shrink-0 flex items-center gap-1.5">
                        <span class="font-mono font-black text-xs sm:text-sm" :class="p.isCurrentUser ? 'text-amber-400' : 'text-slate-200'">
                            {{ Number(p.score).toLocaleString() }}
                        </span>
                        <span class="text-[10px] text-slate-500 font-mono font-semibold">pts</span>
                    </div>
                </div>
            </div>

            <!-- Single Minimal Action Button with Auto-Advance Progress -->
            <div class="relative z-10 pt-2">
                <!-- Auto-timer progress bar -->
                <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden mb-3">
                    <div class="h-full bg-gradient-to-r from-cyan-400 via-amber-400 to-emerald-400 rounded-full transition-all duration-100 ease-linear"
                        :style="{ width: `${progressPercent}%` }">
                    </div>
                </div>

                <button 
                    @click="handleProceed"
                    type="button"
                    class="w-full py-3 px-6 rounded-2xl bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 hover:from-slate-700 hover:to-slate-600 text-white text-xs sm:text-sm font-black transition-all flex items-center justify-center gap-2 cursor-pointer border border-slate-600 shadow-md active:scale-[0.99]"
                >
                    <span>Lanjut Soal Berikutnya</span>
                    <span class="text-cyan-400 text-base">→</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch, onUnmounted } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    leaderboard: {
        type: Array,
        default: () => [],
    },
    currentUserRank: {
        type: Number,
        default: 1,
    },
    rankDiff: {
        type: Number,
        default: 0,
    },
    userScore: {
        type: Number,
        default: 0,
    },
    userStreak: {
        type: Number,
        default: 0,
    },
    currentUserName: {
        type: String,
        default: 'Peserta CBT',
    },
    autoAdvanceMs: {
        type: Number,
        default: 2300,
    },
});

const emit = defineEmits(['proceed']);

const sortedLeaderboard = computed(() => {
    return [...props.leaderboard].sort((a, b) => b.score - a.score);
});

const progressPercent = ref(100);
let timerInterval = null;
let autoAdvanceTimeout = null;

const handleProceed = () => {
    clearInterval(timerInterval);
    clearTimeout(autoAdvanceTimeout);
    emit('proceed');
};

watch(() => props.show, (newVal) => {
    if (newVal) {
        progressPercent.value = 100;
        const stepTime = 50;
        const totalSteps = props.autoAdvanceMs / stepTime;
        const decrement = 100 / totalSteps;

        clearInterval(timerInterval);
        clearTimeout(autoAdvanceTimeout);

        timerInterval = setInterval(() => {
            progressPercent.value = Math.max(0, progressPercent.value - decrement);
        }, stepTime);

        autoAdvanceTimeout = setTimeout(() => {
            handleProceed();
        }, props.autoAdvanceMs);
    } else {
        clearInterval(timerInterval);
        clearTimeout(autoAdvanceTimeout);
    }
});

onUnmounted(() => {
    clearInterval(timerInterval);
    clearTimeout(autoAdvanceTimeout);
});
</script>
