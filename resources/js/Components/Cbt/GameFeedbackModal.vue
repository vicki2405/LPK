<template>
    <div 
        v-if="show" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-md transition-all duration-200 select-none pointer-events-none"
    >
        <!-- ======================= VICTORY / SEIKAI (BENAR) ======================= -->
        <div 
            v-if="isCorrect" 
            class="relative max-w-sm sm:max-w-md w-full rounded-3xl p-6 sm:p-8 text-center text-white shadow-2xl overflow-hidden border animate-victory-pop"
            :class="[
                modelIndex === 0 ? 'bg-gradient-to-b from-rose-950 via-slate-900 to-slate-950 border-rose-500/50 shadow-rose-900/40' : '',
                modelIndex === 1 ? 'bg-gradient-to-b from-amber-950 via-slate-900 to-slate-950 border-amber-500/50 shadow-amber-900/40' : '',
                modelIndex === 2 ? 'bg-gradient-to-b from-cyan-950 via-slate-900 to-slate-950 border-cyan-500/50 shadow-cyan-900/40' : '',
                modelIndex === 3 ? 'bg-gradient-to-b from-purple-950 via-slate-900 to-slate-950 border-purple-500/50 shadow-purple-900/40' : '',
            ]"
        >
            <!-- Ambient Glow -->
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 rounded-full blur-3xl opacity-60 pointer-events-none"
                :class="[
                    modelIndex === 0 ? 'bg-rose-500' : '',
                    modelIndex === 1 ? 'bg-amber-400' : '',
                    modelIndex === 2 ? 'bg-cyan-400' : '',
                    modelIndex === 3 ? 'bg-fuchsia-500' : '',
                ]"></div>

            <!-- MODEL 0: HANAMARU SAKURA STAMP (花丸 大正解) -->
            <div v-if="modelIndex === 0" class="relative z-10 flex flex-col items-center">
                <!-- Sakura floating background particles -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <span class="absolute top-2 left-6 text-xl animate-float-petal-1">🌸</span>
                    <span class="absolute top-8 right-8 text-lg animate-float-petal-2">🌸</span>
                    <span class="absolute bottom-4 left-10 text-base animate-float-petal-3">🌸</span>
                    <span class="absolute bottom-2 right-12 text-xl animate-float-petal-1">🌸</span>
                </div>

                <!-- Hanamaru Stamp SVG -->
                <div class="relative w-28 h-28 sm:w-32 sm:h-32 mb-4 animate-stamp-down">
                    <svg viewBox="0 0 100 100" class="w-full h-full text-rose-500 drop-shadow-[0_0_20px_rgba(244,63,94,0.6)]">
                        <!-- Dual Outer Petal Ring -->
                        <circle cx="50" cy="50" r="44" fill="none" stroke="currentColor" stroke-width="4" stroke-dasharray="14 3" class="animate-spin-slow" />
                        <circle cx="50" cy="50" r="38" fill="rgba(244,63,94,0.15)" stroke="currentColor" stroke-width="3" />
                        <!-- Inner Flower Center -->
                        <circle cx="50" cy="50" r="28" fill="rgba(244,63,94,0.3)" stroke="currentColor" stroke-width="2" />
                    </svg>
                    <!-- Kanji Center -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center font-jp font-black text-rose-300 tracking-tight leading-tight">
                        <span class="text-xs sm:text-sm">大変よく</span>
                        <span class="text-base sm:text-lg text-white font-extrabold">出来ました</span>
                    </div>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/40 text-xs font-black uppercase tracking-wider mb-2">
                    <span>💮</span>
                    <span>花丸 大正解 (SEIKAI!)</span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight">SUGOI! LUAR BIASA!</h3>
                <p class="text-xs text-rose-200 mt-1 font-medium">Jawaban Anda tepat dan sempurna! Pertahankan fokus!</p>
            </div>

            <!-- MODEL 1: RPG GOLDEN LEVEL-UP AURA (黄金オーラ・レベルアップ) -->
            <div v-else-if="modelIndex === 1" class="relative z-10 flex flex-col items-center">
                <!-- Level up light beams -->
                <div class="relative w-28 h-28 sm:w-32 sm:h-32 mb-4 flex items-center justify-center">
                    <!-- Rotating Starburst -->
                    <div class="absolute inset-0 rounded-full border-4 border-dashed border-amber-400/40 animate-spin-slow"></div>
                    <!-- Golden Star Icon with Pulse -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-tr from-amber-500 via-yellow-400 to-amber-200 text-slate-950 flex items-center justify-center text-4xl sm:text-5xl shadow-[0_0_35px_rgba(251,191,36,0.8)] animate-bounce-subtle">
                        ⭐
                    </div>
                    <!-- Sparks -->
                    <span class="absolute -top-1 -right-2 text-2xl animate-ping">✨</span>
                    <span class="absolute bottom-0 -left-2 text-2xl animate-pulse">⚡</span>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-black uppercase tracking-wider mb-2">
                    <span>🔥</span>
                    <span>LEVEL UP &amp; COMBO STRIKE</span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-amber-300 tracking-tight">PERFECT HIT!</h3>
                <p class="text-xs text-amber-100/90 mt-1 font-medium">Rentetan jawaban benar mengaktifkan bonus EXP ekstra!</p>
            </div>

            <!-- MODEL 2: SAMURAI LIGHTNING SLASH (雷光一閃・斬撃) -->
            <div v-else-if="modelIndex === 2" class="relative z-10 flex flex-col items-center">
                <!-- Lightning Blade Slash Visual -->
                <div class="relative w-28 h-28 sm:w-32 sm:h-32 mb-4 flex items-center justify-center overflow-hidden rounded-2xl bg-cyan-950/60 border border-cyan-500/40 shadow-[0_0_30px_rgba(6,182,212,0.4)]">
                    <!-- Diagonal Neon Blade Flash -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-48 h-1.5 bg-gradient-to-r from-transparent via-cyan-300 to-transparent rotate-45 animate-slash-flash"></div>
                    </div>
                    <!-- Center Katana & Lightning Emblem -->
                    <div class="relative z-10 text-4xl sm:text-5xl drop-shadow-[0_0_15px_rgba(6,182,212,0.8)]">
                        ⚡
                    </div>
                    <span class="absolute bottom-2 text-[10px] font-black font-jp text-cyan-300 uppercase tracking-widest">
                        一閃 · 命中
                    </span>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-xs font-black uppercase tracking-wider mb-2">
                    <span>🗡️</span>
                    <span>ICHIBAN! TEBASAN AKURAT</span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-cyan-300 tracking-tight">TEPAT &amp; TAJAM!</h3>
                <p class="text-xs text-cyan-100/90 mt-1 font-medium">Analisis kanji dan tata bahasa Anda sangat presisi!</p>
            </div>

            <!-- MODEL 3: MATSURI FIREWORKS HANABI (祭・花火大正解) -->
            <div v-else class="relative z-10 flex flex-col items-center">
                <!-- Confetti & Hanabi bursts -->
                <div class="relative w-28 h-28 sm:w-32 sm:h-32 mb-4 flex items-center justify-center">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-tr from-fuchsia-600 to-pink-500 text-white flex items-center justify-center text-4xl shadow-[0_0_35px_rgba(217,70,239,0.7)] animate-bounce-subtle">
                        🎆
                    </div>
                    <!-- Floating fireworks particles -->
                    <span class="absolute -top-2 left-0 text-xl animate-ping">✨</span>
                    <span class="absolute top-2 -right-3 text-2xl animate-pulse">🎉</span>
                    <span class="absolute -bottom-2 right-2 text-xl animate-bounce">🎊</span>
                    <span class="absolute bottom-0 -left-3 text-lg animate-ping">🌟</span>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40 text-xs font-black uppercase tracking-wider mb-2">
                    <span>🏮</span>
                    <span>HANABI DAISEIKAI (祝)</span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-fuchsia-300 tracking-tight">DAISEIKAI! BENAR!</h3>
                <p class="text-xs text-purple-100/90 mt-1 font-medium">Pilihan Anda 100% benar sesuai kunci jawaban resmi!</p>
            </div>

            <!-- Bottom Reward XP & Streak Badge -->
            <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-center gap-4 text-xs font-mono font-bold">
                <span class="text-emerald-400 flex items-center gap-1">
                    <span>⭐</span>
                    <span>+250 EXP</span>
                </span>
                <span v-if="streak > 1" class="text-amber-400 flex items-center gap-1">
                    <span>🔥</span>
                    <span>{{ streak }}x Combo!</span>
                </span>
            </div>
        </div>

        <!-- ======================= DEFEAT / MACHIGAI (SALAH) ======================= -->
        <div 
            v-else 
            class="relative max-w-sm sm:max-w-md w-full rounded-3xl p-6 sm:p-8 text-center text-white shadow-2xl overflow-hidden border animate-screen-shake"
            :class="[
                modelIndex === 0 ? 'bg-gradient-to-b from-rose-950 via-slate-900 to-slate-950 border-rose-600/60 shadow-rose-950/60' : '',
                modelIndex === 1 ? 'bg-gradient-to-b from-red-950 via-slate-900 to-slate-950 border-red-600/60 shadow-red-950/60' : '',
                modelIndex === 2 ? 'bg-gradient-to-b from-amber-950 via-slate-900 to-slate-950 border-amber-600/60 shadow-amber-950/60' : '',
                modelIndex === 3 ? 'bg-gradient-to-b from-slate-900 via-slate-950 to-slate-950 border-blue-500/40 shadow-blue-950/40' : '',
            ]"
        >
            <!-- Ambient Defeat Red Glow -->
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-48 h-48 rounded-full blur-3xl opacity-50 bg-rose-600 pointer-events-none"></div>

            <!-- MODEL 0: BATSU & RAIN CLOUD (残念・雨雲バツ) -->
            <div v-if="modelIndex === 0" class="relative z-10 flex flex-col items-center">
                <div class="relative w-28 h-28 sm:w-32 sm:h-32 mb-4 flex items-center justify-center">
                    <!-- Rain Cloud Top -->
                    <div class="absolute -top-2 text-3xl animate-bounce-subtle">
                        🌧️
                    </div>
                    <!-- Big Batsu SVG -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-rose-600/20 border-4 border-rose-500 flex items-center justify-center text-4xl sm:text-5xl font-black text-rose-500 drop-shadow-[0_0_20px_rgba(244,63,94,0.8)]">
                        ✕
                    </div>
                    <!-- Falling Rain Drops -->
                    <span class="absolute bottom-0 left-6 text-cyan-400 text-sm animate-rain-drop">💧</span>
                    <span class="absolute bottom-1 right-6 text-cyan-400 text-sm animate-rain-drop" style="animation-delay: 0.2s;">💧</span>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/40 text-xs font-black uppercase tracking-wider mb-2">
                    <span>☔</span>
                    <span>ZANNEN! 不正解 (SALAH)</span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-rose-400 tracking-tight">MASIH KURANG TEPAT!</h3>
                <p class="text-xs text-rose-200 mt-1 font-medium">Sayang sekali, opsi ini belum benar. Jangan menyerah!</p>
            </div>

            <!-- MODEL 1: SHIELD SHATTER (盾破壊・クリティカル) -->
            <div v-else-if="modelIndex === 1" class="relative z-10 flex flex-col items-center">
                <div class="relative w-28 h-28 sm:w-32 sm:h-32 mb-4 flex items-center justify-center">
                    <!-- Broken Shield / Shatter Visual -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-red-950/80 border-2 border-red-500 flex items-center justify-center text-4xl sm:text-5xl shadow-[0_0_25px_rgba(239,68,68,0.7)]">
                        🛡️
                    </div>
                    <!-- Crack Overlays -->
                    <span class="absolute inset-0 flex items-center justify-center text-3xl font-black text-red-400 drop-shadow-md">
                        💥
                    </span>
                    <span class="absolute -top-1 -right-2 text-xl animate-ping">⚡</span>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500/20 text-red-300 border border-red-500/40 text-xs font-black uppercase tracking-wider mb-2">
                    <span>💔</span>
                    <span>CRITICAL HIT · PERISAI PECAH</span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-red-400 tracking-tight">PERTAHANAN JEBOL!</h3>
                <p class="text-xs text-red-200 mt-1 font-medium">Serangan musuh menembus perisai Anda! Evaluasi kembali!</p>
            </div>

            <!-- MODEL 2: BONK & DIZZY SPIRAL (ピヨピヨ・目が回る) -->
            <div v-else-if="modelIndex === 2" class="relative z-10 flex flex-col items-center">
                <div class="relative w-28 h-28 sm:w-32 sm:h-32 mb-4 flex items-center justify-center">
                    <!-- Dizzy Head & Mallet -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-amber-950/70 border-2 border-amber-500 flex items-center justify-center text-4xl sm:text-5xl shadow-[0_0_25px_rgba(245,158,11,0.6)]">
                        😵
                    </div>
                    <!-- Circling Stars -->
                    <div class="absolute inset-0 rounded-full border-2 border-dashed border-amber-400/50 animate-spin-slow"></div>
                    <span class="absolute -top-2 left-6 text-base animate-bounce">💫</span>
                    <span class="absolute top-2 -right-1 text-base animate-spin">🌀</span>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-black uppercase tracking-wider mb-2">
                    <span>🔨</span>
                    <span>OUCH! TERPELESET (まだまだ)</span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-amber-400 tracking-tight">ADUH, BELUM TEPAT!</h3>
                <p class="text-xs text-amber-200 mt-1 font-medium">Tarik napas, perhatikan partikel dan kanji lebih teliti lagi!</p>
            </div>

            <!-- MODEL 3: ANIME SWEAT DROP & FALLING LEAF (どんまい・枯葉汗) -->
            <div v-else class="relative z-10 flex flex-col items-center">
                <div class="relative w-28 h-28 sm:w-32 sm:h-32 mb-4 flex items-center justify-center">
                    <!-- Anime Face & Big Blue Sweat -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-slate-800 border-2 border-blue-400/50 flex items-center justify-center text-4xl sm:text-5xl shadow-[0_0_25px_rgba(59,130,246,0.5)]">
                        😅
                    </div>
                    <!-- Sweat Drop Sliding Down -->
                    <span class="absolute top-1 right-2 text-2xl text-cyan-400 drop-shadow-md animate-bounce-subtle">
                        💧
                    </span>
                    <!-- Falling Autumn Leaves -->
                    <span class="absolute -bottom-1 -left-2 text-xl animate-float-leaf">🍂</span>
                    <span class="absolute top-6 -left-3 text-lg animate-float-leaf" style="animation-delay: 0.3s;">🍁</span>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/40 text-xs font-black uppercase tracking-wider mb-2">
                    <span>🍂</span>
                    <span>DON'MAI! TETAP SEMANGAT</span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-blue-300 tracking-tight">DON'MAI! BUKAN MASALAH!</h3>
                <p class="text-xs text-slate-300 mt-1 font-medium">Jadikan pelajaran untuk membalas di nomor selanjutnya!</p>
            </div>

            <!-- Bottom Note -->
            <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-center gap-2 text-xs font-mono font-bold text-rose-400">
                <span>⚠️ HP Berkurang 1</span>
                <span>·</span>
                <span class="text-slate-400">Combo Reset</span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    isCorrect: {
        type: Boolean,
        default: true,
    },
    modelIndex: {
        type: Number,
        default: 0,
    },
    streak: {
        type: Number,
        default: 0,
    },
});
</script>

<style scoped>
@keyframes stamp-down {
    0% { transform: scale(2.2) rotate(-15deg); opacity: 0; }
    50% { transform: scale(0.9) rotate(2deg); opacity: 1; }
    75% { transform: scale(1.05) rotate(-1deg); }
    100% { transform: scale(1) rotate(0deg); }
}

.animate-stamp-down {
    animation: stamp-down 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
}

@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin-slow {
    animation: spin-slow 8s linear infinite;
}

@keyframes bounce-subtle {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

.animate-bounce-subtle {
    animation: bounce-subtle 1.2s ease-in-out infinite;
}

@keyframes slash-flash {
    0% { transform: translateX(-100%) rotate(45deg); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateX(100%) rotate(45deg); opacity: 0; }
}

.animate-slash-flash {
    animation: slash-flash 0.5s ease-out infinite;
}

@keyframes float-petal-1 {
    0% { transform: translate(0, 0) rotate(0deg); opacity: 0.8; }
    50% { transform: translate(10px, -15px) rotate(45deg); opacity: 1; }
    100% { transform: translate(20px, -30px) rotate(90deg); opacity: 0; }
}

.animate-float-petal-1 {
    animation: float-petal-1 1.5s ease-out infinite;
}

@keyframes float-petal-2 {
    0% { transform: translate(0, 0) rotate(0deg); opacity: 0.8; }
    50% { transform: translate(-10px, -12px) rotate(-45deg); opacity: 1; }
    100% { transform: translate(-18px, -25px) rotate(-90deg); opacity: 0; }
}

.animate-float-petal-2 {
    animation: float-petal-2 1.3s ease-out infinite;
}

@keyframes float-leaf {
    0% { transform: translate(0, -10px) rotate(-10deg); opacity: 0.9; }
    50% { transform: translate(8px, 6px) rotate(15deg); opacity: 1; }
    100% { transform: translate(16px, 20px) rotate(40deg); opacity: 0; }
}

.animate-float-leaf {
    animation: float-leaf 1.4s ease-in-out infinite;
}

@keyframes rain-drop {
    0% { transform: translateY(-5px); opacity: 1; }
    100% { transform: translateY(15px); opacity: 0; }
}

.animate-rain-drop {
    animation: rain-drop 0.6s linear infinite;
}
</style>
