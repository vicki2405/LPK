<template>
    <Head :title="isJapanese ? 'ログイン - 正夢' : 'Masuk Portal - LPK Masayume'" />

    <div class="min-h-screen bg-[#070b14] text-slate-100 flex flex-col justify-between items-center p-4 sm:p-6 relative overflow-hidden selection:bg-japan-red selection:text-white font-sans">
        
        <!-- ================= ANIMATED BACKGROUND EFFECTS ================= -->
        <!-- Subtle Tech Grid Background -->
        <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:28px_28px] opacity-40 pointer-events-none"></div>

        <!-- Ambient Glowing Aura Orbs -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-red-600/15 rounded-full blur-[140px] pointer-events-none animate-pulse-slow"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-rose-600/15 rounded-full blur-[140px] pointer-events-none animate-pulse-slow delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] bg-indigo-950/20 rounded-full blur-[160px] pointer-events-none"></div>

        <!-- Floating Sakura Petals (CSS Particle Animation) -->
        <div class="sakura-container pointer-events-none absolute inset-0 overflow-hidden">
            <span v-for="n in 12" :key="n" :class="`sakura-petal petal-${n}`"></span>
        </div>

        <!-- ================= TOP BAR: HOME BUTTON & LANGUAGE SWITCHER ================= -->
        <header class="w-full max-w-md flex items-center justify-between z-20 pt-2 sm:pt-4">
            <!-- Back to Home Link -->
            <Link
                :href="route('home')"
                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-900/80 backdrop-blur-xl border border-slate-800/90 text-slate-300 hover:text-white hover:bg-slate-800/80 transition-all duration-300 shadow-lg shadow-black/40 hover:scale-105 group"
                :title="isJapanese ? 'ホームページへ戻る' : 'Kembali ke Beranda Utama'"
            >
                <ArrowLeft class="w-3.5 h-3.5 text-slate-400 group-hover:text-white group-hover:-translate-x-0.5 transition-transform" />
                <span :class="{ 'font-jp': isJapanese }">
                    {{ isJapanese ? 'ホーム' : 'Beranda' }}
                </span>
            </Link>

            <!-- Language Switcher -->
            <div class="flex items-center bg-slate-900/80 backdrop-blur-xl border border-slate-800/90 p-1 rounded-full shadow-lg shadow-black/40">
                <button
                    type="button"
                    @click="setLang('id')"
                    class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all duration-300 cursor-pointer"
                    :class="isIndonesian 
                        ? 'bg-japan-red text-white shadow-md shadow-japan-red/40 scale-105' 
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/50'"
                >
                    <span class="text-sm">🇮🇩</span>
                    <span>ID</span>
                </button>
                <button
                    type="button"
                    @click="setLang('ja')"
                    class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all duration-300 cursor-pointer font-jp"
                    :class="isJapanese 
                        ? 'bg-japan-red text-white shadow-md shadow-japan-red/40 scale-105' 
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/50'"
                >
                    <span class="text-sm">🇯🇵</span>
                    <span>日本語</span>
                </button>
            </div>
        </header>

        <!-- ================= MAIN LOGIN CARD ================= -->
        <main class="w-full max-w-md my-auto z-10 animate-fade-in-up">
            <div class="relative group">
                <!-- Glowing Card Border Backdrop -->
                <div class="absolute -inset-0.5 bg-gradient-to-r from-red-600 via-rose-500 to-amber-500 rounded-[2rem] blur opacity-30 group-hover:opacity-60 transition duration-1000 group-hover:duration-200 animate-tilt"></div>

                <div class="relative bg-slate-900/90 backdrop-blur-2xl rounded-[1.9rem] border border-slate-800/90 shadow-2xl p-7 sm:p-9">
                    
                    <!-- Brand & Header -->
                    <div class="flex flex-col items-center text-center mb-7">
                        <!-- Logo in Crisp Card Container -->
                        <div class="bg-white px-5 py-2.5 rounded-2xl shadow-lg shadow-black/30 border border-slate-200/40 mb-4 transform hover:scale-105 transition-transform duration-300 flex items-center justify-center">
                            <MasayumeLogo />
                        </div>

                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-japan-red/15 text-rose-300 text-[11px] font-semibold border border-japan-red/30 mb-2">
                            <Sparkles class="w-3.5 h-3.5 text-amber-400 animate-spin-slow" />
                            <span>{{ isJapanese ? '公式学習・CBTシステム' : 'Sistem LMS & CBT Resmi' }}</span>
                        </div>

                        <h1 class="text-2xl font-black text-white tracking-tight" :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese ? 'ポータルへログイン' : 'Selamat Datang' }}
                        </h1>
                        <p class="text-xs text-slate-400 mt-1 max-w-xs leading-relaxed" :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese ? '正夢トレーニングセンター学習管理システム' : 'Masuk untuk mengakses materi & simulasi ujian CBT' }}
                        </p>
                    </div>

                    <!-- Status Flash Message -->
                    <div v-if="status" class="mb-5 p-3.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs font-medium text-center flex items-center justify-center gap-2">
                        <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0" />
                        <span>{{ status }}</span>
                    </div>

                    <!-- Login Form -->
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-300 mb-1.5" :class="{ 'font-jp': isJapanese }">
                                {{ isJapanese ? 'メールアドレス' : 'Alamat Email' }}
                            </label>
                            <div class="relative group/input">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 group-focus-within/input:text-japan-red transition-colors">
                                    <Mail class="w-4 h-4" />
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    :placeholder="isJapanese ? '例: user@email.com' : 'nama@email.com'"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-800 text-white placeholder-slate-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-japan-red/40 focus:border-japan-red transition-all duration-200"
                                    :class="{ 'border-rose-500 ring-1 ring-rose-500': form.errors.email }"
                                />
                            </div>
                            <p v-if="form.errors.email" class="mt-1.5 text-xs text-rose-400 font-medium">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <div class="mb-1.5">
                                <label for="password" class="block text-xs font-bold text-slate-300" :class="{ 'font-jp': isJapanese }">
                                    {{ isJapanese ? 'パスワード' : 'Kata Sandi' }}
                                </label>
                            </div>
                            <div class="relative group/input">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 group-focus-within/input:text-japan-red transition-colors">
                                    <Lock class="w-4 h-4" />
                                </div>
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    placeholder="••••••••"
                                    class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-slate-950/80 border border-slate-800 text-white placeholder-slate-500 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-japan-red/40 focus:border-japan-red transition-all duration-200"
                                    :class="{ 'border-rose-500 ring-1 ring-rose-500': form.errors.password }"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors cursor-pointer"
                                >
                                    <EyeOff v-if="showPassword" class="w-4 h-4" />
                                    <Eye v-else class="w-4 h-4" />
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="mt-1.5 text-xs text-rose-400 font-medium">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    v-model="form.remember"
                                    class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-japan-red focus:ring-japan-red focus:ring-offset-slate-900"
                                />
                                <span class="text-xs text-slate-400 font-medium" :class="{ 'font-jp': isJapanese }">
                                    {{ isJapanese ? '次回から自動ログイン' : 'Ingat saya di perangkat ini' }}
                                </span>
                            </label>
                        </div>

                        <!-- Submit Button with Shimmer Sweep Effect -->
                        <button
                            type="submit"
                            :disabled="isAuthenticating || form.processing"
                            class="relative overflow-hidden w-full mt-2 py-3 rounded-xl bg-gradient-to-r from-japan-red via-red-600 to-rose-700 hover:from-red-700 hover:to-rose-800 text-white text-xs sm:text-sm font-bold shadow-lg shadow-japan-red/30 hover:shadow-japan-red/50 transition-all duration-300 transform active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75 group/btn"
                        >
                            <!-- Shimmer light sweep bar -->
                            <div class="absolute inset-0 -translate-x-full group-hover/btn:animate-shimmer bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

                            <span v-if="isAuthenticating || form.processing" class="flex items-center gap-2" :class="{ 'font-jp': isJapanese }">
                                <Loader2 class="w-4 h-4 animate-spin text-white" />
                                <span>{{ isJapanese ? 'ログイン中...' : 'Memproses Masuk...' }}</span>
                            </span>
                            <span v-else class="flex items-center gap-2" :class="{ 'font-jp': isJapanese }">
                                <span>{{ isJapanese ? 'ログインする' : 'Masuk ke Portal' }}</span>
                                <ArrowRight class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </main>

        <!-- ================= FOOTER ================= -->
        <footer class="w-full text-center pb-3 text-xs text-slate-500 z-10">
            <p>© 2026 LPK Masayume Training Center · Japan Tech LMS & CBT Portal</p>
        </footer>

        <!-- ================= MODEL C: JAPANESE MODERN SLIDING SHUTTER TRANSITION ================= -->
        <Transition
            enter-active-class="transition-transform duration-500 cubic-bezier(0.16, 1, 0.3, 1)"
            enter-from-class="-translate-y-full"
            enter-to-class="translate-y-0"
            leave-active-class="transition-all duration-400 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-105"
        >
            <div 
                v-if="isAuthenticating" 
                class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-[#070b14] select-none overflow-hidden"
            >
                <!-- Subtle Japanese Shoji Geometric Grid & Ambient Aura Lighting -->
                <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:28px_28px] opacity-35 pointer-events-none"></div>
                <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.015)_1px,transparent_1px)] [background-size:56px_56px] pointer-events-none"></div>
                <div class="absolute -top-32 -left-32 w-96 h-96 bg-red-600/15 rounded-full blur-[140px] pointer-events-none animate-pulse-slow"></div>
                <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-rose-600/15 rounded-full blur-[140px] pointer-events-none animate-pulse-slow"></div>

                <!-- Floating Sakura Petals Drift Across Shutter -->
                <div class="sakura-container pointer-events-none absolute inset-0 overflow-hidden">
                    <span v-for="n in 12" :key="n" :class="`sakura-petal petal-${n}`"></span>
                </div>

                <!-- Central Japanese Emblem & Mon Crest -->
                <div class="relative z-10 flex flex-col items-center justify-center text-center max-w-sm px-6 animate-fade-in-up">
                    
                    <!-- Zen Ring & Masayume Crest -->
                    <div class="relative flex items-center justify-center mb-7">
                        <!-- Outer Zen Enso Circle with Gold & Crimson gradient -->
                        <div class="absolute -inset-4 rounded-full border-2 border-red-500/40 border-t-red-500 border-r-amber-400 animate-spin-slow pointer-events-none"></div>
                        <!-- Breathing Red Core Glow -->
                        <div class="absolute -inset-6 rounded-full bg-red-600/25 blur-2xl animate-pulse pointer-events-none"></div>
                        
                        <!-- Masayume Logo in Crisp White Card Container -->
                        <div class="relative z-10 bg-white px-6 py-3 rounded-2xl shadow-2xl shadow-red-950/60 border border-slate-200/60 flex items-center justify-center">
                            <MasayumeLogo />
                        </div>
                    </div>

                    <!-- Minimalist Japanese Typography -->
                    <div class="space-y-1.5 mb-6">
                        <h3 class="text-xl font-bold text-white tracking-wide flex items-center justify-center gap-2" :class="{ 'font-jp': isJapanese }">
                            <span>{{ shutterTitle }}</span>
                        </h3>
                        <p class="text-xs text-rose-300/80 font-mono tracking-wider" :class="{ 'font-jp': isJapanese }">
                            {{ shutterSubtitle }}
                        </p>
                    </div>

                    <!-- Japanese Precision Laser Progress Line -->
                    <div class="w-56 h-1.5 bg-slate-800/90 rounded-full overflow-hidden relative shadow-inner p-0.5 border border-white/5">
                        <div 
                            class="h-full bg-gradient-to-r from-japan-red via-rose-500 to-amber-400 rounded-full transition-all duration-300 ease-out shadow-[0_0_12px_rgba(239,68,68,0.8)]"
                            :style="{ width: `${progressPercent}%` }"
                        >
                            <div class="absolute inset-0 bg-white/40 animate-shimmer"></div>
                        </div>
                    </div>

                    <!-- Bottom Subtext -->
                    <div class="mt-6 flex items-center gap-2 text-[11px] text-slate-500 font-mono tracking-widest">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>LPK MASAYUME · PORTAL CBT & LMS</span>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import MasayumeLogo from '@/Components/MasayumeLogo.vue';
import { useLang } from '@/Composables/useLang';
import { 
    Mail, 
    Lock, 
    Eye, 
    EyeOff, 
    ArrowRight,
    ArrowLeft,
    Sparkles,
    CheckCircle2,
    Loader2
} from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const { isJapanese, isIndonesian, setLang } = useLang();

const showPassword = ref(false);
const isAuthenticating = ref(false);
const progressPercent = ref(0);
const isSuccessReady = ref(false);
let progressTimer = null;
let timeoutSafety = null;

const shutterTitle = computed(() => {
    if (isSuccessReady.value) {
        return isJapanese.value ? 'ようこそ、ダッシュボードへ' : 'Selamat Datang di Portal Masayume';
    }
    if (progressPercent.value < 55) {
        return isJapanese.value ? '認証情報を確認中...' : 'Memverifikasi Kredensial...';
    }
    return isJapanese.value ? '学びの場を整えています...' : 'Mempersiapkan Ruang Belajar...';
});

const shutterSubtitle = computed(() => {
    if (isSuccessReady.value) {
        return '正夢トレーニングセンター · MEMBUKA DASHBOARD';
    }
    if (progressPercent.value < 55) {
        return 'ユーザー照合中 · AUTHENTICATING';
    }
    return '学習環境とセッションを起動中 · PREPARING WORKSPACE';
});

const cleanupTimers = () => {
    if (progressTimer) {
        clearInterval(progressTimer);
        progressTimer = null;
    }
    if (timeoutSafety) {
        clearTimeout(timeoutSafety);
        timeoutSafety = null;
    }
};

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    cleanupTimers();
    isAuthenticating.value = true;
    isSuccessReady.value = false;
    progressPercent.value = 25;

    // Smooth progress timer
    progressTimer = setInterval(() => {
        if (progressPercent.value < 65) {
            progressPercent.value += 10;
        } else if (progressPercent.value < 90) {
            progressPercent.value += 4;
        }
    }, 100);

    timeoutSafety = setTimeout(() => {
        if (isAuthenticating.value && !form.processing) {
            cleanupTimers();
            isAuthenticating.value = false;
            progressPercent.value = 0;
        }
    }, 12000);

    // Delay form submission slightly (650ms) so user visibly experiences the Japanese Shutter transition
    setTimeout(() => {
        form.post(route('login'), {
            onSuccess: () => {
                cleanupTimers();
                progressPercent.value = 100;
                isSuccessReady.value = true;
            },
            onError: () => {
                cleanupTimers();
                isAuthenticating.value = false;
                progressPercent.value = 0;
                isSuccessReady.value = false;
            },
            onFinish: () => {
                form.reset('password');
                if (form.hasErrors) {
                    cleanupTimers();
                    isAuthenticating.value = false;
                    progressPercent.value = 0;
                    isSuccessReady.value = false;
                }
            },
        });
    }, 650);
};
</script>

<style scoped>
/* Keyframe Animations */
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(200%); }
}

@keyframes pulseSlow {
    0%, 100% { opacity: 0.2; transform: scale(1); }
    50% { opacity: 0.35; transform: scale(1.08); }
}

@keyframes spinSlow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes spinReverse {
    from { transform: rotate(360deg); }
    to { transform: rotate(0deg); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-shimmer {
    animation: shimmer 1.6s infinite ease-in-out;
}

.animate-pulse-slow {
    animation: pulseSlow 8s infinite ease-in-out;
}

.animate-spin-slow {
    animation: spinSlow 12s infinite linear;
}

.animate-spin-reverse {
    animation: spinReverse 9s infinite linear;
}

.animate-fade-in-up {
    animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Falling Sakura Blossoms CSS Animation */
.sakura-petal {
    position: absolute;
    top: -20px;
    width: 14px;
    height: 10px;
    background: radial-gradient(circle, #fda4af 0%, #f43f5e 100%);
    border-radius: 12px 1px;
    opacity: 0.25;
    animation: fall linear infinite;
    filter: drop-shadow(0 0 4px rgba(244, 63, 94, 0.3));
}

@keyframes fall {
    0% {
        top: -10%;
        transform: translateX(0) rotate(0deg) scale(0.8);
        opacity: 0;
    }
    10% {
        opacity: 0.35;
    }
    90% {
        opacity: 0.2;
    }
    100% {
        top: 110%;
        transform: translateX(120px) rotate(480deg) scale(1.1);
        opacity: 0;
    }
}

.petal-1  { left: 8%;  animation-duration: 9s;  animation-delay: 0s; }
.petal-2  { left: 18%; animation-duration: 12s; animation-delay: 2s; width: 16px; height: 12px; }
.petal-3  { left: 28%; animation-duration: 10s; animation-delay: 4s; }
.petal-4  { left: 40%; animation-duration: 14s; animation-delay: 1s; }
.petal-5  { left: 52%; animation-duration: 11s; animation-delay: 3s; width: 15px; height: 11px; }
.petal-6  { left: 62%; animation-duration: 13s; animation-delay: 5s; }
.petal-7  { left: 74%; animation-duration: 9.5s; animation-delay: 2.5s; }
.petal-8  { left: 85%; animation-duration: 11.5s; animation-delay: 0.5s; width: 14px; height: 10px; }
.petal-9  { left: 92%; animation-duration: 15s; animation-delay: 4.5s; }
.petal-10 { left: 4%;  animation-duration: 10.5s; animation-delay: 3.5s; }
.petal-11 { left: 48%; animation-duration: 12.5s; animation-delay: 6s; }
.petal-12 { left: 80%; animation-duration: 13.5s; animation-delay: 1.5s; }
</style>
