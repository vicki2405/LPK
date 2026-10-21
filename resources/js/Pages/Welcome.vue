<template>
    <Head :title="isJapanese ? `${settings.lpk_name || '正夢'} 技能実習・特定技能送出機関 - 公式サイト` : `${settings.lpk_name || 'LPK Masayume'} - Pelatihan Kerja & Penyaluran Karir ke Jepang Resmi`" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans selection:bg-japan-red selection:text-white relative scroll-smooth">

        <!-- ================= FIXED / STICKY HEADER WRAPPER ================= -->
        <div class="fixed top-0 inset-x-0 z-50 transition-all duration-300">
            <!-- Topbar -->
            <div class="bg-slate-950 text-slate-300 text-xs py-1.5 px-4 sm:px-8 border-b border-slate-800/80">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-1.5">
                    <div class="flex items-center gap-3 text-[11px] sm:text-xs">
                        <span class="flex items-center gap-1.5 font-bold text-slate-200">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            {{ isJapanese ? '厚生労働省・公認送出機関' : (settings.lpk_legal_info || '🏛️ Izin Resmi Kemenaker RI & Terakreditasi SO') }}
                        </span>
                        <span class="hidden md:inline text-slate-700">|</span>
                        <span class="hidden md:inline text-slate-400">
                            {{ isJapanese ? '特定技能・技能実習生育成' : (settings.lpk_topbar_subtitle || 'Program Tokutei Ginou (SSW) & Magang Kerja Jepang') }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4 text-xs font-bold">
                        <a :href="`https://wa.me/${cleanWaNumber(settings.contact_whatsapp)}`" target="_blank" class="hover:text-amber-400 transition-colors flex items-center gap-1.5 text-slate-300">
                            <span>💬 {{ isJapanese ? '無料相談・受付:' : 'Konsultasi:' }} <strong class="text-white font-mono">{{ settings.contact_phone || '0812-3456-7890' }}</strong></span>
                        </a>
                        <!-- Language Switcher -->
                        <div class="flex items-center bg-slate-900 rounded-full p-0.5 border border-slate-800">
                            <button
                                type="button"
                                @click="setLang('id')"
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold transition-all cursor-pointer"
                                :class="isIndonesian ? 'bg-japan-red text-white shadow-xs' : 'text-slate-400 hover:text-white'"
                            >
                                🇮🇩 ID
                            </button>
                            <button
                                type="button"
                                @click="setLang('ja')"
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold transition-all cursor-pointer font-jp"
                                :class="isJapanese ? 'bg-japan-red text-white shadow-xs' : 'text-slate-400 hover:text-white'"
                            >
                                🇯🇵 日本語
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Navigation Navbar -->
            <nav class="bg-white/90 backdrop-blur-xl border-b border-slate-200/80 shadow-xs">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 sm:h-20 flex items-center justify-between">
                    <!-- Brand Logo -->
                    <Link :href="route('home')" class="flex items-center gap-3 group">
                        <MasayumeLogo class="transition-transform duration-300 group-hover:scale-105" />
                    </Link>

                    <!-- Desktop Nav Links with Hover Underline Animation -->
                    <div class="hidden lg:flex items-center gap-6 text-xs font-black uppercase tracking-wider text-slate-700">
                        <a href="#tentang" class="relative py-1 hover:text-japan-red transition-colors nav-link">{{ isJapanese ? '当機関について' : 'Tentang LPK' }}</a>
                        <a href="#program" class="relative py-1 hover:text-japan-red transition-colors nav-link">{{ isJapanese ? 'プログラム' : 'Program' }}</a>
                        <a href="#sektor" class="relative py-1 hover:text-japan-red transition-colors nav-link">{{ isJapanese ? '職種一覧' : 'Sektor Kerja' }}</a>
                        <a href="#alur" class="relative py-1 hover:text-japan-red transition-colors nav-link">{{ isJapanese ? '送出フロー' : 'Alur Penyaluran' }}</a>
                        <a href="#testimoni" class="relative py-1 hover:text-japan-red transition-colors nav-link">{{ isJapanese ? '実習生の声' : 'Testimoni' }}</a>
                        <a href="#galeri" class="relative py-1 hover:text-japan-red transition-colors nav-link">{{ isJapanese ? '活動写真' : 'Galeri Foto' }}</a>
                        <a href="#teknologi" class="relative py-1 hover:text-japan-red transition-colors nav-link">{{ isJapanese ? 'LMS・CBT' : 'Sistem Digital' }}</a>
                        <a href="#kontak" class="relative py-1 hover:text-japan-red transition-colors nav-link">{{ isJapanese ? 'お問い合わせ' : 'Kontak' }}</a>
                    </div>

                    <!-- CTA Login Portal Button & Mobile Hamburger -->
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('login')"
                            class="hidden sm:inline-flex px-5 py-2.5 rounded-2xl bg-gradient-to-r from-red-600 via-japan-red to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-xs font-black shadow-md shadow-japan-red/30 hover:shadow-lg hover:shadow-japan-red/40 transition-all duration-300 transform hover:-translate-y-0.5 items-center gap-2 group"
                        >
                            <span class="group-hover:rotate-12 transition-transform duration-300">🔑</span>
                            <span>{{ isJapanese ? 'ポータルログイン' : 'Masuk Portal' }}</span>
                        </Link>

                        <button
                            type="button"
                            @click="showMobileMenu = !showMobileMenu"
                            class="lg:hidden p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 transition-colors cursor-pointer"
                            aria-label="Toggle menu"
                        >
                            <Menu v-if="!showMobileMenu" class="w-5 h-5" />
                            <X v-else class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation Dropdown -->
                <div v-if="showMobileMenu" class="lg:hidden bg-white border-b border-slate-200 px-6 py-4 space-y-3 animate-fade-in text-xs font-black uppercase tracking-wider text-slate-700 shadow-xl">
                    <a href="#tentang" @click="showMobileMenu = false" class="block py-2 hover:text-japan-red transition-colors">{{ isJapanese ? '当機関について' : 'Tentang LPK' }}</a>
                    <a href="#program" @click="showMobileMenu = false" class="block py-2 hover:text-japan-red transition-colors">{{ isJapanese ? 'プログラム' : 'Program Pelatihan' }}</a>
                    <a href="#sektor" @click="showMobileMenu = false" class="block py-2 hover:text-japan-red transition-colors">{{ isJapanese ? '職種一覧' : 'Sektor Kerja' }}</a>
                    <a href="#alur" @click="showMobileMenu = false" class="block py-2 hover:text-japan-red transition-colors">{{ isJapanese ? '送出フロー' : 'Alur 7 Tahap' }}</a>
                    <a href="#testimoni" @click="showMobileMenu = false" class="block py-2 hover:text-japan-red transition-colors">{{ isJapanese ? '実習生の声' : 'Testimoni' }}</a>
                    <a href="#galeri" @click="showMobileMenu = false" class="block py-2 hover:text-japan-red transition-colors">{{ isJapanese ? '活動写真' : 'Galeri Kegiatan' }}</a>
                    <a href="#teknologi" @click="showMobileMenu = false" class="block py-2 hover:text-japan-red transition-colors">{{ isJapanese ? 'LMS・CBTシステム' : 'Sistem Digital' }}</a>
                    <a href="#kontak" @click="showMobileMenu = false" class="block py-2 hover:text-japan-red transition-colors">{{ isJapanese ? 'お問い合わせ' : 'Kontak' }}</a>
                    <div class="pt-2 border-t border-slate-100">
                        <Link
                            :href="route('login')"
                            class="w-full py-3 rounded-xl bg-japan-red text-white text-xs font-black text-center block shadow-md"
                        >
                            {{ isJapanese ? '🔑 ポータルログイン (LMS/CBT)' : '🔑 Masuk Portal (LMS & CBT)' }}
                        </Link>
                    </div>
                </div>
            </nav>
        </div>

        <!-- ================= ULTRA-MODERN HERO SECTION ================= -->
        <header class="relative bg-gradient-to-b from-slate-900 via-slate-950 to-slate-900 text-white pt-36 pb-16 sm:pt-44 sm:pb-24 overflow-hidden">
            
            <!-- Ambient Background Mesh Glow -->
            <div class="absolute -top-24 -right-24 w-96 sm:w-[550px] h-96 sm:h-[550px] bg-red-600/20 rounded-full blur-3xl pointer-events-none animate-pulse-slow"></div>
            <div class="absolute -bottom-24 -left-24 w-80 sm:w-[450px] h-80 sm:h-[450px] bg-rose-600/15 rounded-full blur-3xl pointer-events-none animate-pulse-slow"></div>
            
            <!-- Japanese Calligraphy Watermark -->
            <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 text-white/[0.03] text-[130px] sm:text-[240px] font-black font-jp pointer-events-none select-none tracking-widest leading-none">
                正夢
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                    
                    <!-- Left Hero Content -->
                    <div class="lg:col-span-7 space-y-6 text-center lg:text-left animate-slide-up">
                        
                        <!-- Badge Pill with Shimmer Effect -->
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-rose-300 text-[11px] sm:text-xs font-bold shadow-xs hover:border-red-400 transition-colors">
                            <span class="text-base animate-bounce-subtle">🎌</span>
                            <span>{{ isJapanese ? '日本就労・特定技能／技能実習生育成アカデミー' : (settings.lpk_hero_badge || 'LEMBAGA PELATIHAN KERJA JEPANG RESMI & TERPERCAYA') }}</span>
                        </div>

                        <!-- Headline with Animated Rotating Sector Badge -->
                        <h1 class="text-3xl sm:text-5xl lg:text-5xl font-black tracking-tight leading-[1.12]">
                            {{ isJapanese ? '確かな技術と日本語力で、' : 'Wujudkan Karir Impian ke' }}
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 via-rose-400 to-amber-300 block sm:inline">
                                {{ isJapanese ? '日本の未来へ羽ばたく。' : ' Jepang Bersama LPK Masayume' }}
                            </span>
                        </h1>

                        <!-- Dynamic Typewriter Sector Pill -->
                        <div class="flex items-center justify-center lg:justify-start gap-2 text-xs font-bold text-slate-300">
                            <span class="text-slate-400">{{ isJapanese ? '募集分野・職種:' : 'Peluang Karir Bidang:' }}</span>
                            <span class="px-3 py-1 rounded-xl bg-japan-red/20 text-amber-300 border border-japan-red/40 font-mono tracking-wide transition-all duration-300 inline-flex items-center gap-1.5">
                                <span :class="{ 'font-jp': isJapanese }">{{ currentRotatingSector }}</span>
                                <span class="w-1.5 h-3.5 bg-amber-400 animate-pulse"></span>
                            </span>
                        </div>

                        <!-- Description -->
                        <p class="text-xs sm:text-base text-slate-300 leading-relaxed font-medium max-w-2xl mx-auto lg:mx-0" :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese 
                                ? '正夢（マサユメ）は、JLPT N4／JFT-Basic A2対策、高度な介護・製造・外食スキル、および日本の労働倫理を徹底指導する公認送出機関です。' 
                                : (settings.lpk_hero_description || 'Membuka jalan karir profesional di Jepang melalui Program Tokutei Ginou (SSW) dan Magang Kerja. Didukung kurikulum terpadu JLPT N4, pelatih Sensei berpengalaman, dan sistem ujian CBT Digital mutakhir.') }}
                        </p>

                        <!-- CTA Group -->
                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                            <a
                                :href="`https://wa.me/${cleanWaNumber(settings.contact_whatsapp)}?text=Halo%20${encodeURIComponent(settings.lpk_name || 'LPK Masayume')},%20saya%20ingin%20konsultasi%20pendaftaran%20program%20ke%20Jepang.`"
                                target="_blank"
                                class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-japan-red via-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-xs font-black uppercase tracking-wider shadow-lg shadow-japan-red/40 hover:shadow-xl hover:shadow-japan-red/50 transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-3 cursor-pointer group"
                            >
                                <span class="group-hover:scale-125 transition-transform duration-300">💬</span>
                                <span :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '無料相談・お問い合わせ' : 'Konsultasi Pendaftaran Gratis' }}</span>
                            </a>
                            <Link
                                :href="route('login')"
                                class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white/10 hover:bg-white/20 text-white border border-white/20 hover:border-white/40 text-xs font-black uppercase tracking-wider backdrop-blur-md transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-2 group"
                            >
                                <span class="group-hover:rotate-45 transition-transform duration-300">🚀</span>
                                <span :class="{ 'font-jp': isJapanese }">{{ isJapanese ? 'LMS・CBTポータルへ' : 'Akses Portal CBT & LMS' }}</span>
                            </Link>
                        </div>

                        <!-- Social Proof & Trust Badges -->
                        <div class="pt-6 border-t border-white/10 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-slate-300">
                            <!-- Avatar Stack + Rating -->
                            <div class="flex items-center gap-3 bg-white/5 border border-white/10 px-3.5 py-2 rounded-2xl backdrop-blur-xs hover:bg-white/10 transition-colors">
                                <div class="flex -space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] flex items-center justify-center border-2 border-slate-900 font-mono">D</div>
                                    <div class="w-7 h-7 rounded-full bg-rose-500 text-white font-black text-[10px] flex items-center justify-center border-2 border-slate-900 font-mono">R</div>
                                    <div class="w-7 h-7 rounded-full bg-blue-500 text-white font-black text-[10px] flex items-center justify-center border-2 border-slate-900 font-mono">B</div>
                                    <div class="w-7 h-7 rounded-full bg-emerald-500 text-slate-950 font-black text-[10px] flex items-center justify-center border-2 border-slate-900 font-mono">+</div>
                                </div>
                                <div class="text-left">
                                    <div class="flex text-amber-400 text-[10px]">★★★★★</div>
                                    <span class="text-[11px] font-bold text-white">{{ isJapanese ? '4.9/5 (350名以上の修了生)' : '4.9/5 (350+ Alumni Jepang)' }}</span>
                                </div>
                            </div>

                            <span class="flex items-center gap-1.5 font-bold">
                                <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                                <span>{{ isJapanese ? 'インドネシア労働省・公認送出機関' : '100% Legal & Kemenaker RI' }}</span>
                            </span>
                            <span class="flex items-center gap-1.5 font-bold">
                                <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                                <span>{{ isJapanese ? '受入企業マッチング支援' : 'Garansi Matching Perusahaan' }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Right Hero: Layered 3D Visual Showcase with Floating Badges -->
                    <div class="lg:col-span-5 relative animate-fade-in">
                        <div class="relative mx-auto max-w-md lg:max-w-none">
                            
                            <!-- Ambient Backlight Glow -->
                            <div class="absolute -inset-2 bg-gradient-to-r from-red-600 via-rose-600 to-amber-500 rounded-3xl blur-xl opacity-50 animate-pulse-slow"></div>

                            <!-- Main Visual Showcase Container with Auto-Slider -->
                            <div 
                                class="relative bg-slate-900/90 backdrop-blur-xl rounded-3xl border border-white/20 overflow-hidden shadow-2xl p-2.5 sm:p-3.5 transition-transform duration-500 hover:scale-[1.01] group/hero"
                                @mouseenter="pauseHeroSlider"
                                @mouseleave="resumeHeroSlider"
                            >
                                
                                <!-- Primary Hero Image Slider (Local high-res Japan asset with fixed dimensions) -->
                                <div class="relative w-full h-64 sm:h-72 aspect-[4/3] sm:aspect-[16/11] rounded-2xl overflow-hidden bg-gradient-to-br from-slate-900 via-slate-950 to-red-950 border border-white/10">
                                    <transition name="fade" mode="out-in">
                                        <img
                                            :key="activeHeroSlide.id || currentHeroSlideIndex"
                                            :src="activeHeroSlide.image_path || '/images/hero-japan.jpg'"
                                            :alt="activeHeroSlide.title"
                                            class="w-full h-full object-cover object-center transition-all duration-700"
                                            loading="eager"
                                            @error="$event.target.style.display='none'"
                                        />
                                    </transition>
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent"></div>

                                    <!-- Top Left Floating Badge -->
                                    <div class="absolute top-3 left-3 bg-slate-950/85 backdrop-blur-md border border-white/25 px-3 py-1.5 rounded-xl shadow-lg flex items-center gap-2 animate-float">
                                        <span class="text-amber-400 text-sm">🌟</span>
                                        <div>
                                            <span class="text-[9px] text-slate-400 uppercase font-black block" :class="{ 'font-jp': isJapanese }">
                                                {{ isJapanese ? (activeHeroSlide.badge_top_label_jp || '合格率') : (activeHeroSlide.badge_top_label || 'Tingkat Kelulusan') }}
                                            </span>
                                            <span class="text-xs font-black text-white font-mono">
                                                {{ activeHeroSlide.badge_top || '98% JLPT N4 / JFT' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Prev / Next Slider Arrows (visible on card hover) -->
                                    <button
                                        type="button"
                                        @click.stop="prevHeroSlide"
                                        class="absolute left-2.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-slate-950/70 hover:bg-japan-red text-white text-sm font-bold flex items-center justify-center border border-white/20 backdrop-blur-md transition-all opacity-0 group-hover/hero:opacity-100 cursor-pointer shadow-lg z-20 hover:scale-110 active:scale-95"
                                        title="Slide Sebelumnya"
                                    >
                                        ‹
                                    </button>
                                    <button
                                        type="button"
                                        @click.stop="nextHeroSlide"
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-slate-950/70 hover:bg-japan-red text-white text-sm font-bold flex items-center justify-center border border-white/20 backdrop-blur-md transition-all opacity-0 group-hover/hero:opacity-100 cursor-pointer shadow-lg z-20 hover:scale-110 active:scale-95"
                                        title="Slide Selanjutnya"
                                    >
                                        ›
                                    </button>

                                    <!-- Bottom Info Overlay inside Image -->
                                    <div class="absolute bottom-3 inset-x-3 flex items-center justify-between bg-slate-950/85 backdrop-blur-md border border-white/20 p-2.5 rounded-xl z-10">
                                        <div class="flex items-center gap-2 truncate mr-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping shrink-0"></span>
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 -ml-4.5 shrink-0"></span>
                                            <span class="text-[11px] font-black text-white truncate" :class="{ 'font-jp': isJapanese }">
                                                {{ isJapanese ? (activeHeroSlide.title_jp || activeHeroSlide.title) : activeHeroSlide.title }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shrink-0" :class="{ 'font-jp': isJapanese }">
                                            {{ isJapanese ? (activeHeroSlide.status_label_jp || '募集状況: 受付中') : (activeHeroSlide.status_label || 'Status: Dibuka') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Floating Glass Cards: Salary & Penempatan -->
                                <div class="mt-3 grid grid-cols-2 gap-2.5 text-xs">
                                    <div class="bg-white/10 hover:bg-white/15 backdrop-blur-md border border-white/15 p-3 rounded-2xl transition-all duration-300 hover:-translate-y-0.5">
                                        <span class="text-[10px] text-slate-400 block font-bold" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '💰 初任給・給与水準:' : '💰 Standar Gaji Jepang:' }}</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono">{{ activeHeroSlide.salary_jpy || '180k - 250k JPY' }}</span>
                                        <span class="text-[10px] text-slate-300 block mt-0.5" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '（手取り換算 約20〜28万円）' : (activeHeroSlide.salary_idr || '± Rp 20 - 28 Juta/bln') }}</span>
                                    </div>

                                    <div class="bg-white/10 hover:bg-white/15 backdrop-blur-md border border-white/15 p-3 rounded-2xl flex flex-col justify-between transition-all duration-300 hover:-translate-y-0.5">
                                        <span class="text-[10px] text-slate-400 block font-bold" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '🛫 主な配属地域:' : '🛫 Penempatan Resmi:' }}</span>
                                        <span class="text-xs font-black text-white line-clamp-1" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? (activeHeroSlide.placement_location_jp || activeHeroSlide.placement_location) : activeHeroSlide.placement_location }}</span>
                                        <span class="text-[10px] text-amber-300 font-bold mt-0.5 line-clamp-1" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? (activeHeroSlide.facilities_jp || activeHeroSlide.facilities) : activeHeroSlide.facilities }}</span>
                                    </div>
                                </div>

                                <!-- Card CTA Direct Action -->
                                <a
                                    :href="activeHeroSlide.cta_url || `https://wa.me/${cleanWaNumber(settings.contact_whatsapp)}?text=Halo%20Admin%20${encodeURIComponent(settings.lpk_name || 'Masayume')},%20saya%20tertarik%20dengan%20${encodeURIComponent(activeHeroSlide.title)}.`"
                                    target="_blank"
                                    class="mt-3 w-full py-3 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-black text-xs uppercase tracking-wider text-center block shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:-translate-y-0.5"
                                    :class="{ 'font-jp': isJapanese }"
                                >
                                    {{ isJapanese ? (activeHeroSlide.cta_text_jp || '新期生募集に申し込む →') : (activeHeroSlide.cta_text || 'Daftar Angkatan Baru Sekarang →') }}
                                </a>

                                <!-- Slider Dot Indicators -->
                                <div class="mt-2.5 flex items-center justify-center gap-1.5">
                                    <button
                                        v-for="(s, idx) in availableHeroSlides"
                                        :key="idx"
                                        type="button"
                                        @click="goToHeroSlide(idx)"
                                        class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                                        :class="idx === currentHeroSlideIndex ? 'w-6 bg-japan-red' : 'w-2 bg-white/30 hover:bg-white/60'"
                                        :title="`Slide ${idx + 1}`"
                                    ></button>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </header>

        <!-- ================= INFINITE RUNNING MARQUEE TICKER (PITA TEKS BERJALAN) ================= -->
        <div class="bg-gradient-to-r from-red-700 via-japan-red to-rose-800 text-white py-3 border-y border-red-500/30 overflow-hidden shadow-inner relative z-20">
            <div class="marquee-track flex items-center whitespace-nowrap text-xs font-black tracking-widest uppercase">
                <div class="marquee-content flex items-center gap-8 px-4" :class="{ 'font-jp': isJapanese }">
                    <template v-if="isJapanese">
                        <span class="flex items-center gap-2">🎌 <strong>正夢（マサユメ）</strong> 日本就労アカデミー</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">💼 特定技能（SSW）公認プログラム</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏭 技能実習制度（3〜5年研修）</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏆 JLPT N4・JFT-Basic 合格率98%達成</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🗾 東京・大阪・愛知・全国受入企業提携</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏛️ インドネシア労働省認可 送出機関 (SO)</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">💻 JLPT本番準拠・独自CBT試験システム完備</span>
                        <span class="text-amber-300">✦</span>
                    </template>
                    <template v-else>
                        <span class="flex items-center gap-2">🎌 <strong>LPK MASAYUME</strong> TRAINING CENTER</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">💼 PROGRAM TOKUTEI GINOU (SSW) RESMI</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏭 PROGRAM MAGANG KERJA 3-5 TAHUN</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏆 SERTIFIKASI JLPT N4 &amp; JFT-BASIC A2</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🗾 PENEMPATAN TOKYO, OSAKA, AICHI, NAGOYA</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏛️ IZIN RESMI KEMENAKER RI &amp; AKREDITASI SO</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">💻 SIMULASI CBT STANDAR UJIAN RESMI</span>
                        <span class="text-amber-300">✦</span>
                    </template>
                </div>
                <!-- Duplicate for seamless infinite loop -->
                <div class="marquee-content flex items-center gap-8 px-4" aria-hidden="true" :class="{ 'font-jp': isJapanese }">
                    <template v-if="isJapanese">
                        <span class="flex items-center gap-2">🎌 <strong>正夢（マサユメ）</strong> 日本就労アカデミー</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">💼 特定技能（SSW）公認プログラム</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏭 技能実習制度（3〜5年研修）</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏆 JLPT N4・JFT-Basic 合格率98%達成</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🗾 東京・大阪・愛知・全国受入企業提携</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏛️ インドネシア労働省認可 送出機関 (SO)</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">💻 JLPT本番準拠・独自CBT試験システム完備</span>
                        <span class="text-amber-300">✦</span>
                    </template>
                    <template v-else>
                        <span class="flex items-center gap-2">🎌 <strong>LPK MASAYUME</strong> TRAINING CENTER</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">💼 PROGRAM TOKUTEI GINOU (SSW) RESMI</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏭 PROGRAM MAGANG KERJA 3-5 TAHUN</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏆 SERTIFIKASI JLPT N4 &amp; JFT-BASIC A2</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🗾 PENEMPATAN TOKYO, OSAKA, AICHI, NAGOYA</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">🏛️ IZIN RESMI KEMENAKER RI &amp; AKREDITASI SO</span>
                        <span class="text-amber-300">✦</span>
                        <span class="flex items-center gap-2">💻 SIMULASI CBT STANDAR UJIAN RESMI</span>
                        <span class="text-amber-300">✦</span>
                    </template>
                </div>
            </div>
        </div>

        <!-- ================= STATS COUNTER BAR WITH ANIMATED COUNTING ================= -->
        <section id="stats-section" class="bg-slate-950 text-white py-12 px-4 sm:px-8 border-b border-slate-800">
            <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 text-center">
                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-1 hover:border-amber-400/50 hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-1">
                    <span class="text-3xl sm:text-4xl font-black text-amber-400 font-mono">
                        {{ animatedStats.passing }}%
                    </span>
                    <p class="text-xs text-slate-300 font-bold uppercase tracking-wider">{{ isJapanese ? 'N4合格率' : (settings.stat_passing_label || 'Tingkat Kelulusan Ujian N4') }}</p>
                </div>
                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-1 hover:border-emerald-400/50 hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-1">
                    <span class="text-3xl sm:text-4xl font-black text-emerald-400 font-mono">
                        {{ animatedStats.alumni }}+
                    </span>
                    <p class="text-xs text-slate-300 font-bold uppercase tracking-wider">{{ isJapanese ? '渡日実習生・特定技能生' : (settings.stat_alumni_label || 'Siswa Berangkat ke Jepang') }}</p>
                </div>
                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-1 hover:border-blue-400/50 hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-1">
                    <span class="text-3xl sm:text-4xl font-black text-blue-400 font-mono">
                        {{ animatedStats.partners }}+
                    </span>
                    <p class="text-xs text-slate-300 font-bold uppercase tracking-wider">{{ isJapanese ? '提携受入企業・組合' : (settings.stat_partner_label || 'Mitra Perusahaan Jepang') }}</p>
                </div>
                <div class="p-5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs space-y-1 hover:border-rose-400/50 hover:bg-white/10 transition-all duration-300 transform hover:-translate-y-1">
                    <span class="text-3xl sm:text-4xl font-black text-rose-400 font-mono">
                        {{ animatedStats.legal }}%
                    </span>
                    <p class="text-xs text-slate-300 font-bold uppercase tracking-wider">{{ isJapanese ? '公式認可・合法手続' : (settings.stat_legal_label || 'Izin Resmi Kemenaker') }}</p>
                </div>
            </div>
        </section>

        <!-- ================= ABOUT LPK SECTION ================= -->
        <section id="tentang" class="py-24 bg-white scroll-mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-red-50 text-japan-red text-xs font-black uppercase tracking-wider border border-red-100 shadow-2xs">
                            {{ isJapanese ? '正夢について' : 'Profil Lembaga' }}
                        </div>
                        <h2 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight leading-tight" :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese ? '日本の産業界に貢献する、品格と高スキルを兼ね備えた人材の育成' : (settings.about_title || 'Mencetak Tenaga Kerja Profesional Berkarakter & Berkualitas untuk Industri Jepang') }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium" :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese ? '正夢（マサユメ）は、日本語教育、日本特有のビジネスマナー、労働規律（挨拶・報連相・5S）、および専門スキルの徹底指導を行う公認送出機関です。' : (settings.about_p1 || 'LPK Masayume (株式会社 正夢) adalah Lembaga Pelatihan Kerja terkemuka yang berfokus pada pembekalan bahasa, budaya, kedisiplinan (Hensachi & Aisatsu), serta keterampilan teknis kerja di Jepang.') }}
                        </p>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium" :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese ? '伝統的な対面集中授業と最先端の独自LMS＆CBTデジタル学習プラットフォームを融合し、入校から配属まで確実な進捗管理と高い合格実績を実現しています。' : (settings.about_p2 || 'Kami menggabungkan metode pembelajaran tradisional yang intensif dengan platform digital modern (LMS & CBT Standar Jepang) sehingga seluruh siswa dipantau progres kemampuan bahasanya secara presisi dari nol hingga siap berangkat.') }}
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="p-5 rounded-3xl bg-slate-50 border border-slate-200 hover:border-japan-red/40 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                                <h4 class="text-base font-black text-slate-900 mb-1" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '日本人専任講師・N2有資格者' : (settings.about_feature_1_title || 'Sensei Native & N2') }}</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '日本の大学卒・公認指導資格を持つ経験豊かな講師陣が直接指導。' : (settings.about_feature_1_desc || 'Pengajar berpengalaman lulusan universitas Jepang & bersertifikat resmi.') }}</p>
                            </div>
                            <div class="p-5 rounded-3xl bg-slate-50 border border-slate-200 hover:border-japan-red/40 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                                <h4 class="text-base font-black text-slate-900 mb-1" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '専用学生寮・学習設備完備' : (settings.about_feature_2_title || 'Asrama & Fasilitas') }}</h4>
                                <p class="text-[11px] text-slate-500 leading-relaxed" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '専用言語ラボおよび自習用CBT模擬試験室を完備した快適な研修環境。' : (settings.about_feature_2_desc || 'Lingkungan belajar kondusif dengan laboratorium bahasa & lab CBT mandiri.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-600 via-rose-700 to-slate-950 text-white rounded-3xl p-8 sm:p-10 shadow-2xl relative overflow-hidden transform hover:scale-[1.01] transition-transform duration-500">
                        <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none animate-pulse-slow"></div>

                        <span class="text-xs font-black uppercase tracking-widest text-rose-200 block mb-2 font-jp">{{ isJapanese ? '正夢の理念' : `PHILOSOPHY ${settings.lpk_name || 'MASAYUME'}` }}</span>
                        <h3 class="text-2xl font-black tracking-tight mb-4" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '「夢を現実に、日本の未来へ」' : `"${settings.philosophy_title || 'Mimpi Nyata Menuju Masa Depan'}"` }}</h3>
                        <p class="text-xs sm:text-sm text-rose-100 leading-relaxed font-medium mb-8" :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese ? '「正夢（まさゆめ）」という社名には、日本で働き活躍するという夢を確かな現実に変えるという強い決意が込められています。安全・合法的で誇りある就労を一貫して支援します。' : (settings.philosophy_desc || 'Dalam bahasa Jepang, 正夢 (Masayume) berarti "Mimpi yang Menjadi Kenyataan". Kami berkomitmen mendampingi setiap siswa agar mimpi bekerja dan sukses di Jepang terwujud secara aman, legal, dan bermartabat.') }}
                        </p>

                        <div class="space-y-3.5 border-t border-white/20 pt-6 text-xs text-rose-100" :class="{ 'font-jp': isJapanese }">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-white/20 text-white flex items-center justify-center font-bold font-mono">1</span>
                                <span>{{ isJapanese ? '規律ある労働倫理の徹底（5S: 整理・整頓・清掃・清潔・躾）' : 'Etika Kerja Disiplin (5S: Seiri, Seiton, Seiso, Seiketsu, Shitsuke)' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-white/20 text-white flex items-center justify-center font-bold font-mono">2</span>
                                <span>{{ isJapanese ? '円滑なコミュニケーション（報連相: 報告・連絡・相談）の励行' : 'Komunikasi Aktif (Hourensou: Houkoku, Renraku, Soudan)' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-white/20 text-white flex items-center justify-center font-bold font-mono">3</span>
                                <span>{{ isJapanese ? '日本の職場環境に適応する強い精神力と誠実性の涵養' : 'Integritas & Mental Tangguh Menghadapi Lingkungan Kerja Jepang' }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ================= PROGRAMS SECTION ================= -->
        <section id="program" class="py-24 bg-slate-100/70 border-y border-slate-200 scroll-mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-3xl mx-auto space-y-3">
                    <span class="text-xs font-black text-japan-red uppercase tracking-wider font-mono">{{ isJapanese ? '就労コース・育成体系' : 'PILIHAN JALUR KARIR' }}</span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '日本就労・研修プログラム一覧' : 'Program Pelatihan & Pengiriman ke Jepang' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '資格や目標、希望業種に応じた最適なプログラムを提供いたします。' : 'Pilih program yang sesuai dengan kualifikasi, minat keahlian, dan tujuan karir jangka panjang Anda.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="prog in programs" :key="prog.id" class="bg-white rounded-3xl p-7 border-2 border-slate-200 hover:border-japan-red transition-all duration-300 shadow-sm hover:shadow-xl flex flex-col justify-between group transform hover:-translate-y-1.5">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-red-50 text-japan-red flex items-center justify-center text-3xl mb-5 border border-red-100 group-hover:scale-110 group-hover:bg-japan-red group-hover:text-white transition-all duration-300">
                                {{ prog.icon || '💼' }}
                            </div>
                            <span class="px-3 py-1 rounded-lg bg-red-50 text-japan-red text-[11px] font-black uppercase tracking-wider border border-red-100" :class="{ 'font-jp': isJapanese }">
                                {{ isJapanese ? '公認プログラム' : (prog.badge_label || 'Program Resmi') }}
                            </span>
                            <h3 class="text-lg font-black text-slate-950 mt-3 mb-2 group-hover:text-japan-red transition-colors" :class="{ 'font-jp': isJapanese }">
                                {{ isJapanese ? (prog.name_jp || prog.name) : prog.name }}
                            </h3>
                            <p class="text-xs text-slate-600 leading-relaxed font-medium mb-6" :class="{ 'font-jp': isJapanese }">
                                {{ isJapanese ? (prog.description_jp || '日本受入企業への公式配属に向けた集中語学・技術研修プログラム。') : (prog.description || 'Program pembekalan intensif dengan penempatan kerja resmi di perusahaan Jepang.') }}
                            </p>

                            <div v-if="prog.salary_range" class="p-3.5 bg-slate-50 rounded-2xl mb-4 text-xs font-bold text-slate-800 border border-slate-100">
                                <span :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '💰 初任給・給与水準:' : '💰 Kisaran Gaji / Saku:' }}</span> <span class="font-black text-japan-red font-mono">{{ prog.salary_range }}</span>
                            </div>
                        </div>

                        <a
                            :href="`https://wa.me/${cleanWaNumber(settings.contact_whatsapp)}?text=Halo%20Admin,%20saya%20tertarik%20konsultasi%20Program%20${encodeURIComponent(prog.name)}.`"
                            target="_blank"
                            class="mt-6 w-full py-3.5 rounded-2xl bg-slate-950 group-hover:bg-japan-red text-white text-xs font-black text-center transition-all duration-300 shadow-xs cursor-pointer block transform active:scale-95"
                            :class="{ 'font-jp': isJapanese }"
                        >
                            {{ isJapanese ? `${prog.name_jp || prog.name} について相談 →` : `Konsultasi ${prog.name} →` }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= JOB SECTORS SECTION ================= -->
        <section id="sektor" class="py-24 bg-white scroll-mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-3xl mx-auto space-y-3">
                    <span class="text-xs font-black text-japan-red uppercase tracking-wider font-mono">{{ isJapanese ? '職種・受入分野' : 'PELUANG KERJA' }}</span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '日本国内で需要の高い主要就労職種' : 'Sektor Pekerjaan Populer di Jepang' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '日本の産業界における深刻な人手不足に対応し、充実した待遇と手厚い法的保護のもとで就労できます。' : 'Kebutuhan tenaga kerja di Jepang terbuka luas di berbagai bidang industri dengan gaji dan perlindungan hukum terjamin.' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div v-for="sec in jobSectors" :key="sec.id" class="p-5 rounded-3xl bg-slate-50 border border-slate-200 text-center hover:border-japan-red/40 hover:bg-red-50/20 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1.5 group cursor-default">
                        <span class="text-3xl sm:text-4xl block mb-2 group-hover:scale-125 transition-transform duration-300">{{ sec.icon || '🏢' }}</span>
                        <h4 class="text-xs font-black text-slate-900 line-clamp-1 group-hover:text-japan-red transition-colors" :class="{ 'font-jp': isJapanese }">
                            {{ isJapanese ? (sec.name_jp || sec.name) : sec.name }}
                        </h4>
                        <p class="text-[10px] text-slate-500 mt-1" :class="isJapanese ? 'font-sans' : 'font-jp'">
                            {{ isJapanese ? sec.name : (sec.name_jp || '-') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= 7-STEP PIPELINE SECTION ================= -->
        <section id="alur" class="py-24 bg-slate-950 text-white relative overflow-hidden scroll-mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 relative z-10">
                <div class="text-center max-w-3xl mx-auto space-y-3">
                    <span class="text-xs font-black text-amber-400 uppercase tracking-widest font-mono">{{ isJapanese ? '明確かつ安心のステップ' : 'TRANSPARAN & TERARAH' }}</span>
                    <h2 class="text-2xl sm:text-4xl font-black text-white tracking-tight" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? 'インドネシアから日本配属までの7段階プロセス' : 'Alur 7 Tahapan Penyaluran Siswa ke Jepang' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '入学受付から日本語学習、面接内定、ビザ申請、渡日後の受入まで一貫して支援。' : 'Proses bimbingan bertahap dari tahap pendaftaran awal hingga penjemputan di bandara Jepang.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="stage in pipelineStages" :key="stage.id" class="bg-slate-900/90 border border-slate-800 hover:border-japan-red/50 hover:bg-slate-900 p-6 rounded-3xl space-y-3 relative transition-all duration-300 transform hover:-translate-y-1" :class="stage.order_step === 7 ? 'sm:col-span-2 lg:col-span-2 bg-gradient-to-r from-slate-900 via-slate-900 to-rose-950 border-japan-red/40' : ''">
                        <span class="w-8 h-8 rounded-full bg-japan-red text-white flex items-center justify-center font-black text-xs font-mono shadow-md shadow-japan-red/30">
                            0{{ stage.order_step }}
                        </span>
                        <h3 class="text-sm font-black text-white" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? (stage.name_jp || stage.name) : stage.name }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? (stage.description_jp || getPipelineStageDescJp(stage.order_step)) : (stage.description || 'Bimbingan intensif persiapan penyaluran resmi.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= ALUMNI TESTIMONIALS SECTION ================= -->
        <section id="testimoni" v-if="testimonials && testimonials.length > 0" class="py-24 bg-slate-50 border-b border-slate-200 scroll-mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-3xl mx-auto space-y-3">
                    <span class="text-xs font-black text-japan-red uppercase tracking-wider font-mono">{{ isJapanese ? '修了生の実績・体験談' : 'KISAH SUKSES ALUMNI' }}</span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '日本各地で活躍する実習生・特定技能生の声' : 'Apa Kata Siswa yang Sudah Berada di Jepang?' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '正夢で学び、現在日本の企業で夢を叶えている実習生のリアルなメッセージ。' : 'Pengalaman nyata dari siswa-siswi yang telah berhasil mewujudkan mimpi berkarir di Jepang.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="testi in testimonials" :key="testi.id" class="bg-white p-7 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-4 transform hover:-translate-y-1.5">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-red-50 text-japan-red border border-red-100">
                                    📍 {{ testi.japan_location }}
                                </span>
                                <span class="text-xs text-amber-500 font-bold">★★★★★</span>
                            </div>

                            <p class="text-xs text-slate-700 leading-relaxed font-medium italic">
                                "{{ testi.testimony_text }}"
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-japan-red/10 text-japan-red font-black flex items-center justify-center text-sm font-mono">
                                {{ testi.name.charAt(0) }}
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-slate-900">{{ testi.name }}</h4>
                                <span class="text-[11px] text-slate-500 block">{{ testi.work_sector }} ({{ testi.program_type || 'Tokutei Ginou' }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= GALERI DOKUMENTASI KEGIATAN LPK ================= -->
        <section id="galeri" v-if="galleries && galleries.length > 0" class="py-24 bg-white scroll-mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-3xl mx-auto space-y-3">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-red-50 text-japan-red text-xs font-black uppercase tracking-wider border border-red-100" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '📸 実習・活動風景' : '📸 DOKUMENTASI LPK' }}
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '研修生の日々の活動・授業風景ギャラリー' : 'Galeri Kegiatan & Pelatihan Siswa' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '日本語授業、CBT模擬試験、企業面接会、壮行会および渡日当日のリアルな様子。' : 'Momen nyata pembelajaran kelas bahasa, simulasi ujian CBT, wawancara kerja, hingga keberangkatan ke Jepang.' }}
                    </p>

                    <!-- Category Filter Buttons with Active Animation -->
                    <div class="flex flex-wrap items-center justify-center gap-2 pt-4">
                        <button
                            v-for="cat in galleryCategories"
                            :key="cat.id"
                            type="button"
                            @click="activeCategory = cat.id"
                            class="px-4 py-2 rounded-full text-xs font-bold transition-all duration-300 cursor-pointer transform active:scale-95"
                            :class="[
                                activeCategory === cat.id
                                    ? 'bg-slate-950 text-white shadow-md scale-105'
                                    : 'bg-slate-100 text-slate-700 hover:bg-slate-200',
                                { 'font-jp': isJapanese }
                            ]"
                        >
                            <span>{{ isJapanese ? cat.label_jp : cat.label }}</span>
                        </button>
                    </div>
                </div>

                <!-- Gallery Photo Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="gal in filteredGalleries"
                        :key="gal.id"
                        @click="openLightbox(gal)"
                        class="bg-slate-50 rounded-3xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 cursor-pointer group flex flex-col justify-between"
                    >
                        <div class="relative aspect-4/3 overflow-hidden bg-slate-200">
                            <img
                                :src="gal.image_path"
                                :alt="gal.title"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                                loading="lazy"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-5">
                                <span class="text-white text-xs font-bold flex items-center gap-1.5" :class="{ 'font-jp': isJapanese }">
                                    🔍 {{ isJapanese ? 'クリックして拡大表示' : 'Klik untuk memperbesar foto' }}
                                </span>
                            </div>

                            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-950/80 text-white backdrop-blur-md border border-white/20" :class="{ 'font-jp': isJapanese }">
                                {{ getCategoryLabel(gal.category) }}
                            </span>
                        </div>

                        <div class="p-5 space-y-2">
                            <span v-if="gal.activity_date" class="text-[10px] font-mono text-slate-400 block">
                                📅 {{ formatDate(gal.activity_date) }}
                            </span>
                            <h3 class="text-sm font-black text-slate-900 line-clamp-2 group-hover:text-japan-red transition-colors" :class="{ 'font-jp': isJapanese }">
                                {{ gal.title }}
                            </h3>
                            <p v-if="gal.description" class="text-xs text-slate-500 line-clamp-2 leading-relaxed" :class="{ 'font-jp': isJapanese }">
                                {{ gal.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= LIGHTBOX PREVIEW MODAL ================= -->
        <div v-if="lightboxImage" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4 animate-fade-in" @click.self="lightboxImage = null">
            <div class="max-w-4xl w-full bg-slate-950 rounded-3xl overflow-hidden border border-slate-800 shadow-2xl relative animate-scale-up">
                <button
                    @click="lightboxImage = null"
                    class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/20 hover:bg-white text-slate-900 hover:text-black flex items-center justify-center font-bold text-lg backdrop-blur-md transition-colors cursor-pointer"
                >
                    ✕
                </button>

                <div class="max-h-[70vh] bg-black flex items-center justify-center overflow-hidden">
                    <img :src="lightboxImage.image_path" :alt="lightboxImage.title" class="max-h-[70vh] w-auto object-contain mx-auto" />
                </div>

                <div class="p-6 bg-slate-900 text-white space-y-2">
                    <div class="flex items-center justify-between gap-4">
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-japan-red text-white uppercase tracking-wider" :class="{ 'font-jp': isJapanese }">
                            {{ getCategoryLabel(lightboxImage.category) }}
                        </span>
                        <span v-if="lightboxImage.activity_date" class="text-xs text-slate-400 font-mono">
                            📅 {{ formatDate(lightboxImage.activity_date) }}
                        </span>
                    </div>
                    <h3 class="text-base font-black text-white" :class="{ 'font-jp': isJapanese }">{{ lightboxImage.title }}</h3>
                    <p v-if="lightboxImage.description" class="text-xs text-slate-300 leading-relaxed font-medium" :class="{ 'font-jp': isJapanese }">
                        {{ lightboxImage.description }}
                    </p>
                </div>
            </div>
        </div>

        <!-- ================= DIGITAL LMS & CBT HIGHLIGHT ================= -->
        <section id="teknologi" class="py-24 bg-slate-100/70 border-t border-slate-200 scroll-mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-red-950 text-white rounded-3xl p-8 sm:p-12 shadow-2xl relative overflow-hidden border border-white/10">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-7 space-y-5">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/20 text-red-300 text-xs font-bold border border-red-500/30" :class="{ 'font-jp': isJapanese }">
                                {{ isJapanese ? '💻 最先端の教育テクノロジー' : '💻 TEKNOLOGI PENDIDIKAN MODERN' }}
                            </div>
                            <h2 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight" :class="{ 'font-jp': isJapanese }">
                                {{ isJapanese ? '独自のLMS＆CBTシステムによる高効率な自学自習・試験対策' : 'Belajar & Simulasi Ujian Mandiri dengan Sistem CBT & LMS Masayume' }}
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed" :class="{ 'font-jp': isJapanese }">
                                {{ isJapanese ? '全生徒に専用デジタルポータルアカウントを発行。対話型学習モジュール、3D単語カード、公式聴解音声、不正防止機能付CBT模擬試験を完備。' : 'Seluruh siswa LPK Masayume dibekali akun portal digital eksklusif yang memuat modul belajar interaktif, kartu kosakata 3D, materi audio listening resmi, dan simulasi ujian CBT N4 berstandar anti-cheat.' }}
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 hover:bg-white/15 transition-colors">
                                    <h4 class="font-bold text-sm text-white mb-1" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '⏱️ 全画面CBT試験エンジン' : '⏱️ Engine CBT Fullscreen' }}</h4>
                                    <p class="text-[11px] text-slate-300 leading-relaxed" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? 'タブ切り替え即時検知機能とゼロレイテンシーの快適な操作性。' : 'Navigasi instan tanpa jeda dengan deteksi pelanggaran pindah tab otomatis.' }}</p>
                                </div>
                                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 hover:bg-white/15 transition-colors">
                                    <h4 class="font-bold text-sm text-white mb-1" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '🃏 3D単語フラッシュカード' : '🃏 Flashcard 3D Kotoba' }}</h4>
                                    <p class="text-[11px] text-slate-300 leading-relaxed" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '第1課〜第50課の必須語彙を網羅し、高精度な日本語音声読み上げに対応。' : 'Hafalan kosakata Bab 1-50 dengan pelafalan suara Text-to-Speech Jepang.' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-5 text-center bg-white/5 border border-white/10 p-6 sm:p-8 rounded-3xl backdrop-blur-md">
                            <h3 class="text-base font-black text-white mb-2" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '生徒・指導員アカウントをお持ちですか？' : 'Sudah Memiliki Akun Siswa / Sensei?' }}</h3>
                            <p class="text-xs text-slate-300 mb-6" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '教材の閲覧やCBT試験の受験は、内部ポータルからログインしてください。' : 'Silakan masuk ke portal internal untuk mengakses materi dan ujian CBT Anda.' }}</p>
                            <Link
                                :href="route('login')"
                                class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-japan-red to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-japan-red/40 transition-all duration-300 flex items-center justify-center gap-2 transform hover:-translate-y-0.5"
                                :class="{ 'font-jp': isJapanese }"
                            >
                                <span>{{ isJapanese ? '内部ポータルへログイン →' : 'Masuk ke Portal Internal →' }}</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= CONTACT & LOCATION ================= -->
        <section id="kontak" class="py-24 bg-white scroll-mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-3xl mx-auto space-y-3">
                    <span class="text-xs font-black text-japan-red uppercase tracking-wider font-mono">{{ isJapanese ? 'お問い合わせ・受入相談' : 'KONSULTASI & PENDAFTARAN' }}</span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '正夢（マサユメ）へのお問い合わせ' : `Hubungi Tim Konsultan ${settings.lpk_name || 'LPK Masayume'}` }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600" :class="{ 'font-jp': isJapanese }">
                        {{ isJapanese ? '特定技能・技能実習生の受入、提携に関するご相談はお気軽にお問い合わせください。' : 'Konsultasikan rencana karir Anda ke Jepang secara gratis bersama konsultan resmi kami.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 text-japan-red flex items-center justify-center text-xl mx-auto">
                            📍
                        </div>
                        <h4 class="text-sm font-black text-slate-900" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? 'インドネシア本部・研修センター' : 'Kantor Pusat LPK' }}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            {{ settings.contact_address || 'Gedung LPK Masayume, Jl. Pelatihan Karir Jepang No. 88, Jawa Barat, Indonesia' }}
                        </p>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mx-auto">
                            💬
                        </div>
                        <h4 class="text-sm font-black text-slate-900" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '電話・WhatsApp窓口' : 'WhatsApp & Telepon' }}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            {{ settings.contact_phone || '+62 812-3456-7890' }} ({{ isJapanese ? '月〜土: 08:00 - 17:00 (WIB)' : (settings.contact_hours || 'Senin - Sabtu: 08.00 - 17.00 WIB') }})
                        </p>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mx-auto">
                            ✉️
                        </div>
                        <h4 class="text-sm font-black text-slate-900" :class="{ 'font-jp': isJapanese }">{{ isJapanese ? '公式電子メール' : 'Email Resmi' }}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            {{ settings.contact_email || 'info@masayume-lpk.co.id' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= OFFICIAL FOOTER ================= -->
        <footer class="bg-slate-950 text-slate-400 py-12 px-4 sm:px-8 border-t border-slate-800 pb-28 md:pb-12">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 text-xs">
                <div class="flex items-center gap-3">
                    <MasayumeLogo />
                </div>

                <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 font-bold text-slate-400" :class="{ 'font-jp': isJapanese }">
                    <a href="#tentang" class="hover:text-white transition-colors">{{ isJapanese ? '当機関について' : 'Tentang Kami' }}</a>
                    <a href="#program" class="hover:text-white transition-colors">{{ isJapanese ? 'プログラム' : 'Program' }}</a>
                    <a href="#sektor" class="hover:text-white transition-colors">{{ isJapanese ? '職種一覧' : 'Sektor Kerja' }}</a>
                    <a href="#galeri" class="hover:text-white transition-colors">{{ isJapanese ? '活動写真' : 'Galeri' }}</a>
                    <a href="#alur" class="hover:text-white transition-colors">{{ isJapanese ? '送出フロー' : 'Alur Penyaluran' }}</a>
                    <Link :href="route('login')" class="text-japan-red hover:underline font-black">{{ isJapanese ? 'ポータルログイン' : 'Masuk Portal' }}</Link>
                </div>

                <p class="text-slate-400 text-center md:text-right" :class="{ 'font-jp': isJapanese }">
                    {{ isJapanese ? '© 2026 LPK Masayume (株式会社 正夢). 無断転載を禁じます。' : (settings.footer_copyright || '© 2026 LPK Masayume (正夢). Hak Cipta Dilindungi Undang-Undang.') }}
                </p>
            </div>
        </footer>

        <!-- ================= FLOATING MOBILE STICKY ACTION BAR ================= -->
        <div class="md:hidden fixed bottom-0 inset-x-0 bg-slate-950/95 backdrop-blur-xl border-t border-slate-800 p-3 z-50 shadow-2xl flex items-center gap-2">
            <a
                :href="`https://wa.me/${cleanWaNumber(settings.contact_whatsapp)}?text=Halo%20${encodeURIComponent(settings.lpk_name || 'LPK Masayume')},%20saya%20ingin%20konsultasi%20pendaftaran%20program%20ke%20Jepang.`"
                target="_blank"
                class="flex-1 py-3 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs text-center flex items-center justify-center gap-1.5 shadow-sm active:scale-95 transition-transform cursor-pointer"
            >
                <span :class="{ 'font-jp': isJapanese }">💬 {{ isJapanese ? '無料相談' : 'WhatsApp' }}</span>
            </a>
            <Link
                :href="route('login')"
                class="flex-1 py-3 px-3 rounded-xl bg-japan-red hover:bg-red-700 text-white font-black text-xs text-center flex items-center justify-center gap-1.5 shadow-sm active:scale-95 transition-transform"
            >
                <span :class="{ 'font-jp': isJapanese }">🔑 {{ isJapanese ? 'ポータル' : 'Masuk Portal' }}</span>
            </Link>
        </div>

        <!-- ================= FLOATING SCROLL TO TOP BUTTON ================= -->
        <button
            v-show="showScrollTop"
            @click="scrollToTop"
            class="hidden md:flex fixed bottom-8 right-8 z-40 w-12 h-12 rounded-full bg-slate-900/90 hover:bg-japan-red text-white backdrop-blur-md border border-white/20 shadow-xl items-center justify-center transition-all duration-300 transform hover:scale-110 cursor-pointer"
            aria-label="Scroll to top"
        >
            ↑
        </button>

    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MasayumeLogo from '@/Components/MasayumeLogo.vue';
import { useLang } from '@/Composables/useLang';
import { 
    CheckCircle2, 
    Award, 
    ShieldCheck, 
    Building2,
    Menu,
    X
} from 'lucide-vue-next';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
    programs: Array,
    jobSectors: Array,
    pipelineStages: Array,
    testimonials: Array,
    galleries: {
        type: Array,
        default: () => [],
    },
    batches: Array,
    stats: Object,
    canLogin: Boolean,
    heroSlides: {
        type: Array,
        default: () => [],
    },
});

const { isJapanese, isIndonesian, setLang } = useLang();
const showMobileMenu = ref(false);
const showScrollTop = ref(false);

const cleanWaNumber = (num) => {
    if (!num) return '6281234567890';
    return String(num).replace(/[^0-9]/g, '');
};

// ================= HERO CARD SLIDER STATE =================
const defaultHeroSlides = [
    {
        id: 1,
        title: 'Gelombang Angkatan Baru - Tokutei Ginou Kaigo',
        title_jp: '新期生募集 - 特定技能 介護職プログラム',
        badge_top_label: 'TINGKAT KELULUSAN',
        badge_top: '98% JLPT N4 / JFT',
        status_label: 'Status: Dibuka',
        status_label_jp: '募集状況: 受付中',
        image_path: '/images/hero-japan.jpg',
        salary_jpy: '180k - 250k JPY',
        salary_idr: '± Rp 20 - 28 Juta/bln',
        placement_location: 'Tokyo, Osaka, Kanagawa',
        placement_location_jp: '東京・大阪・神奈川',
        facilities: 'Asrama & BPJS Jepang',
        facilities_jp: '社員寮・社会保険完備',
        cta_text: 'Daftar Angkatan Baru Sekarang →',
        cta_text_jp: '新期生募集に申し込む →',
    },
    {
        id: 2,
        title: 'Program Manufaktur & Mesin Industri Otomotif',
        title_jp: '特定技能 素形材・産業機械・自動車製造',
        badge_top_label: 'GAJI & OVERTIME TINGGI',
        badge_top: '200k - 270k JPY',
        status_label: 'Status: Dibuka',
        status_label_jp: '募集状況: 受付中',
        image_path: '/images/hero-manufacturing.jpg',
        salary_jpy: '200k - 270k JPY',
        salary_idr: '± Rp 22 - 30 Juta/bln',
        placement_location: 'Aichi, Shizuoka, Mie',
        placement_location_jp: '愛知・静岡・三重',
        facilities: 'Mess Pabrik & Uang Makan',
        facilities_jp: '工場社員寮・食事手当支給',
        cta_text: 'Konsultasi Manufaktur →',
        cta_text_jp: '製造分野について相談 →',
    },
    {
        id: 3,
        title: 'Program Perhotelan & Pengolahan Makanan',
        title_jp: '宿泊業・外食業・飲食料品製造プログラム',
        badge_top_label: 'BEBAS BIAYA ASRAMA',
        badge_top: 'Benefit Lengkap',
        status_label: 'Status: Dibuka',
        status_label_jp: '募集状況: 受付中',
        image_path: '/images/hero-hospitality.jpg',
        salary_jpy: '185k - 245k JPY',
        salary_idr: '± Rp 20 - 27 Juta/bln',
        placement_location: 'Kyoto, Hokkaido, Fukuoka',
        placement_location_jp: '京都・北海道・福岡',
        facilities: 'Tempat Tinggal & Seragam',
        facilities_jp: '社宅完備・制服支給',
        cta_text: 'Daftar Perhotelan & Resto →',
        cta_text_jp: '外食・宿泊分野に申し込む →',
    },
];

const availableHeroSlides = computed(() => {
    if (props.heroSlides && props.heroSlides.length > 0) {
        return props.heroSlides;
    }
    return defaultHeroSlides;
});

const currentHeroSlideIndex = ref(0);
const activeHeroSlide = computed(() => {
    const list = availableHeroSlides.value;
    return list[currentHeroSlideIndex.value % list.length] || defaultHeroSlides[0];
});

let heroSliderInterval = null;
const isHeroSliderPaused = ref(false);

const startHeroSlider = () => {
    if (heroSliderInterval) clearInterval(heroSliderInterval);
    heroSliderInterval = setInterval(() => {
        if (!isHeroSliderPaused.value && availableHeroSlides.value.length > 1) {
            currentHeroSlideIndex.value = (currentHeroSlideIndex.value + 1) % availableHeroSlides.value.length;
        }
    }, 4500);
};

const pauseHeroSlider = () => {
    isHeroSliderPaused.value = true;
};

const resumeHeroSlider = () => {
    isHeroSliderPaused.value = false;
};

const nextHeroSlide = () => {
    const total = availableHeroSlides.value.length;
    currentHeroSlideIndex.value = (currentHeroSlideIndex.value + 1) % total;
};

const prevHeroSlide = () => {
    const total = availableHeroSlides.value.length;
    currentHeroSlideIndex.value = (currentHeroSlideIndex.value - 1 + total) % total;
};

const goToHeroSlide = (idx) => {
    currentHeroSlideIndex.value = idx;
};

// Rotating Sector Title in Hero (Bilingual)
const sectorsListId = [
    '🏥 Caregiver (Kaigo)',
    '🍱 Pengolahan Makanan',
    '🍣 Restoran & Hospitality',
    '⚙️ Manufaktur & Mesin',
    '🌾 Pertanian Jepang',
    '🏨 Perhotelan (Hotel Staff)',
];
const sectorsListJa = [
    '🏥 介護職（ケアギバー）',
    '🍱 飲食料品製造業',
    '🍣 外食業・フードサービス',
    '⚙️ 素形材・産業機械製造',
    '🌾 農業・畜産業',
    '🏨 宿泊業・ホテルスタッフ',
];
const currentRotatingIndex = ref(0);
const currentRotatingSector = computed(() => {
    const list = isJapanese.value ? sectorsListJa : sectorsListId;
    return list[currentRotatingIndex.value % list.length];
});

let sectorInterval = null;

// Animated Number Counter State
const animatedStats = reactive({
    passing: 0,
    alumni: 0,
    partners: 0,
    legal: 0,
});

const targetPassing = computed(() => {
    const raw = props.settings.stat_passing_rate ?? 98;
    return parseInt(String(raw).replace(/[^0-9]/g, ''), 10) || 98;
});

const targetAlumni = computed(() => {
    const raw = props.settings.stat_alumni_count ?? 350;
    return parseInt(String(raw).replace(/[^0-9]/g, ''), 10) || 350;
});

const targetPartners = computed(() => {
    const raw = props.settings.stat_partner_count ?? 85;
    return parseInt(String(raw).replace(/[^0-9]/g, ''), 10) || 85;
});

const targetLegal = computed(() => {
    const raw = props.settings.stat_legal_count ?? 100;
    return parseInt(String(raw).replace(/[^0-9]/g, ''), 10) || 100;
});

let counterStarted = false;
const startCounting = () => {
    if (counterStarted) return;
    counterStarted = true;

    const duration = 2000;
    const steps = 50;
    const intervalTime = duration / steps;
    let step = 0;

    const timer = setInterval(() => {
        step++;
        const progress = step / steps;
        animatedStats.passing = Math.floor(targetPassing.value * progress);
        animatedStats.alumni = Math.floor(targetAlumni.value * progress);
        animatedStats.partners = Math.floor(targetPartners.value * progress);
        animatedStats.legal = Math.floor(targetLegal.value * progress);

        if (step >= steps) {
            animatedStats.passing = targetPassing.value;
            animatedStats.alumni = targetAlumni.value;
            animatedStats.partners = targetPartners.value;
            animatedStats.legal = targetLegal.value;
            clearInterval(timer);
        }
    }, intervalTime);
};

// Gallery Category Filter & Lightbox
const activeCategory = ref('all');
const lightboxImage = ref(null);

const galleryCategories = [
    { id: 'all', label: '🌟 Semua Dokumentasi', label_jp: '🌟 すべての活動写真' },
    { id: 'pelatihan', label: '📖 Pelatihan Kelas & Lab', label_jp: '📖 日本語授業・研修' },
    { id: 'cbt', label: '💻 Ujian CBT N4', label_jp: '💻 CBT模擬試験' },
    { id: 'mensetsu', label: '🤝 Wawancara (Mensetsu)', label_jp: '🤝 企業面接会' },
    { id: 'keberangkatan', label: '🛫 Keberangkatan Bandara', label_jp: '🛫 出国・空港出発' },
    { id: 'asrama', label: '🏠 Asrama & Kedisiplinan', label_jp: '🏠 寮生活・マナー研修' },
];

const filteredGalleries = computed(() => {
    if (!props.galleries) return [];
    if (activeCategory.value === 'all') return props.galleries;
    return props.galleries.filter((item) => item.category === activeCategory.value);
});

const getCategoryLabel = (cat) => {
    const found = galleryCategories.find((c) => c.id === cat);
    if (!found) return cat;
    const text = isJapanese.value ? found.label_jp : found.label;
    return text.replace(/^[^\s]+\s/, '');
};

const openLightbox = (gal) => {
    lightboxImage.value = gal;
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return isJapanese.value
        ? date.toLocaleDateString('ja-JP', { year: 'numeric', month: 'long', day: 'numeric' })
        : date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
};

// 7-Stage Pipeline Japanese Descriptions Helper
const getPipelineStageDescJp = (step) => {
    const map = {
        1: '集中日本語学習、日本特有のビジネスマナー、労働規律および挨拶の習得。',
        2: 'JLPT N4／JFT-Basic A2 試験合格および特定技能評価試験対策。',
        3: '日本受入企業・監理団体との公式マッチング面接会と内定承諾。',
        4: '渡日直前の総合健康診断（MCU Fit）受診と徹底した体調管理。',
        5: '出入国在留管理庁への在留資格認定証明書（COE）申請と審査交付。',
        6: '在インドネシア日本国大使館での公式就労ビザ発給申請および航空券手配。',
        7: '日本到着、空港出迎え、受入企業への同行配属および初期生活支援。',
    };
    return map[step] || '日本配属に向けた一貫支援プログラム。';
};

// Scroll Handler
const handleScroll = () => {
    showScrollTop.value = window.scrollY > 400;

    // Trigger counter when scroll near stats
    const statsElem = document.getElementById('stats-section');
    if (statsElem) {
        const rect = statsElem.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom >= 0) {
            startCounting();
        }
    }
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    startCounting();
    startHeroSlider();

    // Rotate sector names every 2.5s
    sectorInterval = setInterval(() => {
        currentRotatingIndex.value = (currentRotatingIndex.value + 1) % sectorsListId.length;
    }, 2500);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    if (sectorInterval) clearInterval(sectorInterval);
    if (heroSliderInterval) clearInterval(heroSliderInterval);
});
</script>

<style scoped>
/* Hero Slide Fade Transition */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Infinite Running Marquee Animation */
@keyframes marquee {
    0% {
        transform: translateX(0%);
    }
    100% {
        transform: translateX(-50%);
    }
}

.marquee-track {
    display: flex;
    width: max-content;
    animation: marquee 25s linear infinite;
}

.marquee-track:hover {
    animation-play-state: paused;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-8px);
    }
}

@keyframes pulseSlow {
    0%, 100% {
        opacity: 0.25;
        transform: scale(1);
    }
    50% {
        opacity: 0.45;
        transform: scale(1.08);
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes scaleUp {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-float {
    animation: float 4s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulseSlow 6s ease-in-out infinite;
}

.animate-slide-up {
    animation: slideUp 0.6s ease-out forwards;
}

.animate-scale-up {
    animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Nav Link Hover Underline Animation */
.nav-link::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: #E60012;
    transition: width 0.3s ease;
    border-radius: 9999px;
}

.nav-link:hover::after {
    width: 100%;
}
</style>
