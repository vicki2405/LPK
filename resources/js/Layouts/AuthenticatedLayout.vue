<template>
    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans flex flex-col md:flex-row antialiased selection:bg-japan-red selection:text-white">
        <!-- ================= DESKTOP / TABLET SIDEBAR ================= -->
        <aside 
            class="hidden md:flex flex-col bg-white/95 backdrop-blur-md border-r border-slate-200/80 fixed inset-y-0 left-0 z-30 shadow-xs transition-all duration-300 ease-in-out select-none"
            :class="isSidebarCollapsed ? 'md:w-20' : 'md:w-64 lg:w-72'"
        >
            <!-- Brand Header -->
            <div 
                class="h-20 border-b border-slate-100 flex items-center transition-all duration-300 overflow-hidden"
                :class="isSidebarCollapsed ? 'px-3 justify-center' : 'px-4 justify-between'"
            >
                <Link :href="dashboardRoute" prefetch cache-for="30s" class="group flex items-center overflow-hidden">
                    <MasayumeLogo :iconOnly="isSidebarCollapsed" />
                </Link>

                <!-- Tombol Slider Collapse (Header) -->
                <button 
                    v-if="!isSidebarCollapsed"
                    type="button" 
                    @click="toggleSidebar" 
                    class="p-2 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors cursor-pointer shrink-0"
                    :title="isJapanese ? 'サイドバーを閉じる' : 'Tutup / Ciutkan Sidebar'"
                >
                    <PanelLeftClose class="w-5 h-5" />
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-5 space-y-1.5 overflow-y-auto overflow-x-hidden scrollbar-thin scrollbar-thumb-slate-200">
                <!-- Shared: Dashboard -->
                <Link 
                    :href="dashboardRoute" 
                    prefetch 
                    cache-for="30s"
                    class="group relative flex items-center gap-3 rounded-2xl text-sm font-bold transition-all duration-200"
                    :class="[
                        isSidebarCollapsed ? 'justify-center p-3' : 'px-3.5 py-3',
                        isDashboardActive 
                            ? 'bg-japan-red text-white shadow-md shadow-japan-red/25' 
                            : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100/90'
                    ]"
                >
                    <LayoutDashboard class="w-5 h-5 shrink-0" />
                    <span v-if="!isSidebarCollapsed" class="truncate font-jp">
                        {{ isJapanese ? 'ダッシュボード' : 'Dashboard Utama' }}
                    </span>

                    <!-- Floating Tooltip saat Collapsed -->
                    <div 
                        v-if="isSidebarCollapsed" 
                        class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-150 z-50 shadow-xl font-jp"
                    >
                        {{ isJapanese ? 'ダッシュボード' : 'Dashboard Utama' }}
                    </div>
                </Link>

                <!-- ================= ADMIN NAVIGATION ================= -->
                <template v-if="userRole === 'admin'">
                    <!-- 1. PENGATURAN MASTER DATA -->
                    <div v-if="!isSidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-black uppercase tracking-wider text-slate-400 font-jp">
                        {{ isJapanese ? 'マスタ設定・プログラム' : 'Pengaturan Master Data' }}
                    </div>
                    <div v-else class="w-6 h-px bg-slate-200 mx-auto my-3"></div>

                    <!-- Angkatan / Batch -->
                    <Link :href="route('admin.master.batches.index')" prefetch cache-for="1m"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2',
                            route().current('admin.master.batches.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <GraduationCap class="w-4 h-4 shrink-0" :class="route().current('admin.master.batches.*') ? 'text-japan-red' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate font-jp">{{ isJapanese ? '期生マスタ' : 'Master Angkatan / Batch' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '期生マスタ' : 'Master Angkatan / Batch' }}
                        </div>
                    </Link>

                    <!-- Program Penyaluran -->
                    <Link :href="route('admin.master.programs.index')" prefetch cache-for="1m"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2',
                            route().current('admin.master.programs.*') ? 'bg-indigo-50 text-indigo-700 font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <FolderKanban class="w-4 h-4 shrink-0" :class="route().current('admin.master.programs.*') ? 'text-indigo-600' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate font-jp">{{ isJapanese ? 'プログラム区分マスタ' : 'Master Program Penyaluran' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? 'プログラム区分マスタ' : 'Master Program Penyaluran' }}
                        </div>
                    </Link>

                    <!-- Bidang Kerja -->
                    <Link :href="route('admin.master.job-sectors.index')" prefetch cache-for="1m"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2',
                            route().current('admin.master.job-sectors.*') ? 'bg-amber-50 text-amber-800 font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <Briefcase class="w-4 h-4 shrink-0" :class="route().current('admin.master.job-sectors.*') ? 'text-amber-600' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate font-jp">{{ isJapanese ? '就労分野マスタ' : 'Master Bidang Kerja' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '就労分野マスタ' : 'Master Bidang Kerja' }}
                        </div>
                    </Link>

                    <!-- Tahap Penyaluran -->
                    <Link :href="route('admin.master.pipeline-stages.index')" prefetch cache-for="1m"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2',
                            route().current('admin.master.pipeline-stages.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <ListOrdered class="w-4 h-4 shrink-0" :class="route().current('admin.master.pipeline-stages.*') ? 'text-blue-600' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate font-jp">{{ isJapanese ? '進捗ステップマスタ' : 'Master Tahap Penyaluran' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '進捗ステップマスタ' : 'Master Tahap Penyaluran' }}
                        </div>
                    </Link>

                    <!-- Indikator Capaian Pembelajaran -->
                    <Link :href="route('admin.master.learning-indicators.index')" prefetch cache-for="1m"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2',
                            route().current('admin.master.learning-indicators.*') ? 'bg-rose-50 text-rose-800 font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <Target class="w-4 h-4 shrink-0" :class="route().current('admin.master.learning-indicators.*') ? 'text-rose-600' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate font-jp">{{ isJapanese ? '学習到達目標・評価指標' : 'Indikator Capaian Pembelajaran' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '学習到達目標・評価指標' : 'Indikator Capaian Pembelajaran' }}
                        </div>
                    </Link>

                    <!-- 2. MANAJEMEN SISWA & SENSEI -->
                    <div v-if="!isSidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-black uppercase tracking-wider text-slate-400 font-jp">
                        {{ isJapanese ? '実習生・指導員管理' : 'Manajemen Siswa & Sensei' }}
                    </div>
                    <div v-else class="w-6 h-px bg-slate-200 mx-auto my-3"></div>

                    <!-- Data Siswa -->
                    <Link :href="route('admin.students.index')" prefetch cache-for="30s"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('admin.students.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <Users class="w-4 h-4 shrink-0" :class="route().current('admin.students.*') ? 'text-japan-red' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate font-jp">{{ isJapanese ? '実習生データ管理' : 'Data Siswa Trainee' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '実習生データ管理' : 'Data Siswa Trainee' }}
                        </div>
                    </Link>

                    <!-- Data Sensei -->
                    <Link :href="route('admin.senseis.index')" prefetch cache-for="30s"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('admin.senseis.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <Users class="w-4 h-4 shrink-0" :class="route().current('admin.senseis.*') ? 'text-japan-red' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate font-jp">{{ isJapanese ? '指導員データ管理' : 'Data Sensei / Instruktur' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '指導員データ管理' : 'Data Sensei / Instruktur' }}
                        </div>
                    </Link>

                    <!-- 3. AKUN & HAK AKSES -->
                    <div v-if="!isSidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-black uppercase tracking-wider text-slate-400 font-jp">
                        {{ isJapanese ? 'アカウント・権限管理' : 'Akun & Hak Akses' }}
                    </div>
                    <div v-else class="w-6 h-px bg-slate-200 mx-auto my-3"></div>

                    <!-- Pengguna -->
                    <Link :href="route('admin.users.index')" prefetch cache-for="30s"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('admin.users.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <Users class="w-4 h-4 shrink-0" :class="route().current('admin.users.*') ? 'text-japan-red' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate font-jp">{{ isJapanese ? '管理者・スタッフアカウント' : 'Akun Administrator & Staf' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '管理者・スタッフアカウント' : 'Akun Administrator & Staf' }}
                        </div>
                    </Link>

                    <!-- Role -->
                    <Link :href="route('admin.roles.index')" prefetch cache-for="1m"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('admin.roles.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <ShieldCheck class="w-4 h-4 shrink-0" :class="route().current('admin.roles.*') ? 'text-japan-red' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate font-jp">{{ isJapanese ? '権限ロール設定' : 'Role & Hak Akses' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '権限ロール設定' : 'Role & Hak Akses' }}
                        </div>
                    </Link>

                    <!-- 4. PENGATURAN WEBSITE & CMS (SUBMENU DIROMBAK BERSIH & MODERN) -->
                    <div v-if="!isSidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-black uppercase tracking-wider text-slate-400 font-jp">
                        {{ isJapanese ? '公式サイト設定・CMS' : 'Website LPK & CMS' }}
                    </div>
                    <div v-else class="w-6 h-px bg-slate-200 mx-auto my-3"></div>

                    <div class="group relative">
                        <!-- Tombol Induk CMS Website -->
                        <button
                            type="button"
                            @click="isSidebarCollapsed ? (isSidebarCollapsed = false, showCmsMenu = true) : (showCmsMenu = !showCmsMenu)"
                            class="w-full flex items-center rounded-xl text-xs font-bold transition-all duration-200 cursor-pointer font-jp"
                            :class="[
                                isSidebarCollapsed ? 'justify-center p-2.5' : 'justify-between px-3.5 py-2.5',
                                isCmsMenuActive ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                            ]"
                        >
                            <span class="flex items-center gap-3">
                                <Globe class="w-4 h-4 shrink-0" :class="isCmsMenuActive ? 'text-japan-red' : 'text-slate-500'" />
                                <span v-if="!isSidebarCollapsed" class="truncate">{{ isJapanese ? '公式サイト・CMS' : 'Website LPK & CMS' }}</span>
                            </span>
                            <ChevronDown 
                                v-if="!isSidebarCollapsed"
                                class="w-4 h-4 transition-transform duration-200 shrink-0"
                                :class="[showCmsMenu ? 'rotate-180' : '', isCmsMenuActive ? 'text-japan-red' : 'text-slate-400']"
                            />
                        </button>

                        <!-- Sub-Menu CMS Saat Sidebar Terbuka (Modern Nested Card, Tanpa Emoji Teks) -->
                        <div 
                            v-if="showCmsMenu && !isSidebarCollapsed" 
                            class="mt-1.5 ml-2 mr-1 p-1.5 bg-slate-50/90 rounded-2xl border border-slate-200/60 space-y-0.5 animate-fade-in"
                        >
                            <!-- Sub 1: Pengaturan Umum Website -->
                            <Link :href="route('admin.website-settings.index', { tab: 'hero' })" prefetch cache-for="1m"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150"
                                :class="isCmsTabActive('hero') ? 'bg-white text-japan-red font-bold shadow-xs border border-slate-100' : 'text-slate-600 hover:text-slate-950 hover:bg-white/70'">
                                <Settings class="w-3.5 h-3.5 shrink-0" :class="isCmsTabActive('hero') ? 'text-japan-red' : 'text-slate-400'" />
                                <span>{{ isJapanese ? '基本情報・ヘッダー' : 'Pengaturan Umum' }}</span>
                            </Link>

                            <!-- Sub 2: Testimoni Alumni -->
                            <Link :href="route('admin.website-settings.index', { tab: 'testimonials' })" prefetch cache-for="1m"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150"
                                :class="isCmsTabActive('testimonials') ? 'bg-white text-amber-700 font-bold shadow-xs border border-slate-100' : 'text-slate-600 hover:text-slate-950 hover:bg-white/70'">
                                <MessageSquareQuote class="w-3.5 h-3.5 shrink-0" :class="isCmsTabActive('testimonials') ? 'text-amber-600' : 'text-slate-400'" />
                                <span>{{ isJapanese ? '修了生の声・体験談' : 'Testimoni Alumni' }}</span>
                            </Link>

                            <!-- Sub 3: Galeri Foto Kegiatan -->
                            <Link :href="route('admin.website-settings.index', { tab: 'galleries' })" prefetch cache-for="1m"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150"
                                :class="isCmsTabActive('galleries') ? 'bg-white text-indigo-700 font-bold shadow-xs border border-slate-100' : 'text-slate-600 hover:text-slate-950 hover:bg-white/70'">
                                <Camera class="w-3.5 h-3.5 shrink-0" :class="isCmsTabActive('galleries') ? 'text-indigo-600' : 'text-slate-400'" />
                                <span>{{ isJapanese ? '活動写真ギャラリー' : 'Galeri Kegiatan' }}</span>
                            </Link>

                            <!-- Sub 4: Slider Banner Hero -->
                            <Link :href="route('admin.website-settings.index', { tab: 'hero_slides' })" prefetch cache-for="1m"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150"
                                :class="isCmsTabActive('hero_slides') ? 'bg-white text-emerald-700 font-bold shadow-xs border border-slate-100' : 'text-slate-600 hover:text-slate-950 hover:bg-white/70'">
                                <SlidersHorizontal class="w-3.5 h-3.5 shrink-0" :class="isCmsTabActive('hero_slides') ? 'text-emerald-600' : 'text-slate-400'" />
                                <span>{{ isJapanese ? 'スライダーバナー' : 'Slide Banner Hero' }}</span>
                            </Link>
                        </div>

                        <!-- Flyout Menu CMS saat Sidebar Collapsed (Mini Mode) -->
                        <div 
                            v-if="isSidebarCollapsed" 
                            class="pointer-events-none group-hover:pointer-events-auto absolute left-full top-0 ml-3 w-52 bg-white rounded-2xl border border-slate-200/90 shadow-2xl p-2 space-y-1 opacity-0 group-hover:opacity-100 transition-all duration-200 z-50 font-jp"
                        >
                            <p class="px-2.5 py-1 text-[10px] font-black text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">
                                {{ isJapanese ? '公式サイト・CMS' : 'Website LPK & CMS' }}
                            </p>
                            <Link :href="route('admin.website-settings.index', { tab: 'hero' })" prefetch class="flex items-center gap-2 px-2.5 py-2 rounded-xl text-xs font-bold hover:bg-red-50 hover:text-japan-red text-slate-700 transition-colors">
                                <Settings class="w-3.5 h-3.5 text-slate-400" />
                                <span>{{ isJapanese ? '基本情報設定' : 'Pengaturan Umum' }}</span>
                            </Link>
                            <Link :href="route('admin.website-settings.index', { tab: 'testimonials' })" prefetch class="flex items-center gap-2 px-2.5 py-2 rounded-xl text-xs font-bold hover:bg-amber-50 hover:text-amber-700 text-slate-700 transition-colors">
                                <MessageSquareQuote class="w-3.5 h-3.5 text-slate-400" />
                                <span>{{ isJapanese ? '修了生の声' : 'Testimoni Alumni' }}</span>
                            </Link>
                            <Link :href="route('admin.website-settings.index', { tab: 'galleries' })" prefetch class="flex items-center gap-2 px-2.5 py-2 rounded-xl text-xs font-bold hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 transition-colors">
                                <Camera class="w-3.5 h-3.5 text-slate-400" />
                                <span>{{ isJapanese ? '活動写真' : 'Galeri Kegiatan' }}</span>
                            </Link>
                            <Link :href="route('admin.website-settings.index', { tab: 'hero_slides' })" prefetch class="flex items-center gap-2 px-2.5 py-2 rounded-xl text-xs font-bold hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 transition-colors">
                                <SlidersHorizontal class="w-3.5 h-3.5 text-slate-400" />
                                <span>{{ isJapanese ? 'スライダー' : 'Slide Banner Hero' }}</span>
                            </Link>
                        </div>
                    </div>
                </template>

                <!-- ================= SENSEI NAVIGATION ================= -->
                <template v-if="userRole === 'sensei'">
                    <div v-if="!isSidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-black uppercase tracking-wider text-slate-400 font-jp">
                        {{ isJapanese ? '指導員ポータル' : 'Menu Utama Sensei' }}
                    </div>
                    <div v-else class="w-6 h-px bg-slate-200 mx-auto my-3"></div>

                    <!-- 1. LMS (Materi & Kurikulum) -->
                    <Link :href="route('sensei.lms.index')" prefetch cache-for="1m"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors font-jp"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('sensei.lms.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <BookOpen class="w-4 h-4 shrink-0" :class="route().current('sensei.lms.*') ? 'text-blue-600' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate">{{ isJapanese ? '課別教材管理' : 'LMS (Materi & Kurikulum)' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '課別教材管理' : 'LMS (Materi & Kurikulum)' }}
                        </div>
                    </Link>

                    <!-- 2. Kotoba (Kosakata) -->
                    <Link :href="route('sensei.vocabularies.index')" prefetch cache-for="1m"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors font-jp"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('sensei.vocabularies.*') ? 'bg-rose-50 text-rose-700 font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <Layers class="w-4 h-4 shrink-0" :class="route().current('sensei.vocabularies.*') ? 'text-rose-600' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate">{{ isJapanese ? '単語・語彙管理' : 'Kotoba (Kosakata)' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '単語・語彙管理' : 'Kotoba (Kosakata)' }}
                        </div>
                    </Link>

                    <!-- 3. SOAL CBT — Parent Menu (Modern Nested, Tanpa Emoji Teks) -->
                    <div class="group relative">
                        <button
                            type="button"
                            @click="isSidebarCollapsed ? (isSidebarCollapsed = false, showSoalMenu = true) : (showSoalMenu = !showSoalMenu)"
                            class="w-full flex items-center rounded-xl text-xs font-bold transition-all duration-200 cursor-pointer font-jp"
                            :class="[
                                isSidebarCollapsed ? 'justify-center p-2.5' : 'justify-between px-3.5 py-2.5',
                                isSoalMenuActive ? 'bg-amber-50 text-amber-700 font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                            ]"
                        >
                            <span class="flex items-center gap-3">
                                <HelpCircle class="w-4 h-4 shrink-0" :class="isSoalMenuActive ? 'text-amber-500' : 'text-slate-500'" />
                                <span v-if="!isSidebarCollapsed" class="truncate">{{ isJapanese ? 'CBT問題バンク' : 'Soal CBT' }}</span>
                            </span>
                            <ChevronDown 
                                v-if="!isSidebarCollapsed"
                                class="w-4 h-4 transition-transform duration-200 shrink-0"
                                :class="[showSoalMenu ? 'rotate-180' : '', isSoalMenuActive ? 'text-amber-500' : 'text-slate-400']"
                            />
                        </button>

                        <!-- Sub-Menu SOAL CBT Expanded -->
                        <div 
                            v-if="showSoalMenu && !isSidebarCollapsed" 
                            class="mt-1.5 ml-2 mr-1 p-1.5 bg-slate-50/90 rounded-2xl border border-slate-200/60 space-y-0.5 animate-fade-in"
                        >
                            <!-- Sub 1: Pengaturan Bank Soal -->
                            <Link :href="route('sensei.settings.questions')" prefetch cache-for="1m"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all font-jp"
                                :class="route().current('sensei.settings.*') ? 'bg-white text-violet-700 font-bold shadow-xs border border-slate-100' : 'text-slate-600 hover:text-slate-950 hover:bg-white/70'">
                                <Settings class="w-3.5 h-3.5 shrink-0" :class="route().current('sensei.settings.*') ? 'text-violet-600' : 'text-slate-400'" />
                                <span>{{ isJapanese ? '難易度・分野設定' : 'Pengaturan Bank Soal' }}</span>
                            </Link>

                            <!-- Sub 2: Bank Soal -->
                            <Link :href="route('sensei.questions.index')" prefetch cache-for="1m"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all font-jp"
                                :class="route().current('sensei.questions.*') ? 'bg-white text-amber-700 font-bold shadow-xs border border-slate-100' : 'text-slate-600 hover:text-slate-950 hover:bg-white/70'">
                                <HelpCircle class="w-3.5 h-3.5 shrink-0" :class="route().current('sensei.questions.*') ? 'text-amber-500' : 'text-slate-400'" />
                                <span>{{ isJapanese ? '問題一覧・登録' : 'Bank Soal (Paket Soal)' }}</span>
                            </Link>
                        </div>

                        <!-- Flyout Menu SOAL CBT Collapsed -->
                        <div 
                            v-if="isSidebarCollapsed" 
                            class="pointer-events-none group-hover:pointer-events-auto absolute left-full top-0 ml-3 w-48 bg-white rounded-2xl border border-slate-200/90 shadow-2xl p-2 space-y-1 opacity-0 group-hover:opacity-100 transition-all duration-200 z-50 font-jp"
                        >
                            <p class="px-2.5 py-1 text-[10px] font-black text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">
                                {{ isJapanese ? 'CBT問題バンク' : 'Soal CBT' }}
                            </p>
                            <Link :href="route('sensei.settings.questions')" prefetch class="flex items-center gap-2 px-2.5 py-2 rounded-xl text-xs font-bold hover:bg-violet-50 hover:text-violet-700 text-slate-700 transition-colors">
                                <Settings class="w-3.5 h-3.5 text-slate-400" />
                                <span>{{ isJapanese ? '難易度・分野設定' : 'Pengaturan Bank Soal' }}</span>
                            </Link>
                            <Link :href="route('sensei.questions.index')" prefetch class="flex items-center gap-2 px-2.5 py-2 rounded-xl text-xs font-bold hover:bg-amber-50 hover:text-amber-700 text-slate-700 transition-colors">
                                <HelpCircle class="w-3.5 h-3.5 text-slate-400" />
                                <span>{{ isJapanese ? '問題一覧・登録' : 'Bank Soal (Paket)' }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- 4. CBT (Ujian & Jadwal) -->
                    <Link :href="route('sensei.exams.index')" prefetch cache-for="30s"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors font-jp"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('sensei.exams.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <Clock class="w-4 h-4 shrink-0" :class="route().current('sensei.exams.*') ? 'text-japan-red' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate">{{ isJapanese ? 'CBT試験・日程管理' : 'CBT (Ujian & Jadwal)' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? 'CBT試験・日程管理' : 'CBT (Ujian & Jadwal)' }}
                        </div>
                    </Link>

                    <!-- 5. Rapor & Kemahiran Siswa -->
                    <Link :href="route('sensei.grades.index')" prefetch cache-for="30s"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors font-jp"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('sensei.grades.*') ? 'bg-emerald-50 text-emerald-700 font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <Award class="w-4 h-4 shrink-0" :class="route().current('sensei.grades.*') ? 'text-emerald-600' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate">{{ isJapanese ? '成績・習熟度評価' : 'Rapor & Kemahiran Siswa' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '成績・習熟度評価' : 'Rapor & Kemahiran Siswa' }}
                        </div>
                    </Link>
                </template>

                <!-- ================= SISWA NAVIGATION ================= -->
                <template v-if="userRole === 'siswa'">
                    <div v-if="!isSidebarCollapsed" class="pt-4 pb-1 px-3 text-[11px] font-black uppercase tracking-wider text-slate-400 font-jp">
                        {{ isJapanese ? '実習生学習ルーム' : 'Ruang Belajar Siswa' }}
                    </div>
                    <div v-else class="w-6 h-px bg-slate-200 mx-auto my-3"></div>

                    <!-- 1. LMS Materi Bab (1-50) -->
                    <Link :href="route('siswa.lms.index')" prefetch cache-for="1m"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('siswa.lms.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <BookOpen class="w-4 h-4 shrink-0" :class="route().current('siswa.lms.*') ? 'text-blue-600' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate">{{ isJapanese ? '課別テキスト（1〜50課）' : 'Materi Bab (1-50)' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '課別テキスト（1〜50課）' : 'Materi Bab (1-50)' }}
                        </div>
                    </Link>

                    <!-- 2. Kotoba Flashcards & Quiz -->
                    <Link :href="route('siswa.flashcards.index')" prefetch cache-for="1m"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('siswa.flashcards.*') ? 'bg-amber-50 text-amber-700 font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <Layers class="w-4 h-4 shrink-0" :class="route().current('siswa.flashcards.*') ? 'text-amber-500' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate">{{ isJapanese ? '単語フラッシュカード' : 'Kotoba Flashcards' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? '単語フラッシュカード' : 'Kotoba Flashcards' }}
                        </div>
                    </Link>

                    <!-- 3. Simulasi CBT N4 -->
                    <Link :href="route('siswa.exams.index')" prefetch cache-for="30s"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('siswa.exams.*') || route().current('siswa.cbt.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <Clock class="w-4 h-4 shrink-0" :class="route().current('siswa.exams.*') || route().current('siswa.cbt.*') ? 'text-japan-red' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate">{{ isJapanese ? 'N4 CBT模擬試験' : 'Simulasi CBT N4' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? 'N4 CBT模擬試験' : 'Simulasi CBT N4' }}
                        </div>
                    </Link>

                    <!-- 4. Progres Penyaluran & Visa -->
                    <Link :href="route('siswa.journey.index')" prefetch cache-for="1m"
                        class="group relative flex items-center gap-3 rounded-xl text-xs font-bold transition-colors"
                        :class="[
                            isSidebarCollapsed ? 'justify-center p-2.5' : 'px-3.5 py-2.5',
                            route().current('siswa.journey.*') ? 'bg-emerald-50 text-emerald-700 font-black' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950'
                        ]">
                        <CheckCircle2 class="w-4 h-4 shrink-0" :class="route().current('siswa.journey.*') ? 'text-emerald-600' : 'text-slate-500'" />
                        <span v-if="!isSidebarCollapsed" class="truncate">{{ isJapanese ? 'ビザ・出国進捗' : 'Progres Visa & Terbang' }}</span>
                        <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                            {{ isJapanese ? 'ビザ・出国進捗' : 'Progres Visa & Terbang' }}
                        </div>
                    </Link>
                </template>
            </nav>

            <!-- Bottom Toggle Footer (Slider Button) -->
            <div class="p-3 border-t border-slate-100 bg-white/50">
                <button 
                    type="button" 
                    @click="toggleSidebar" 
                    class="w-full flex items-center justify-center gap-2 p-2.5 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all cursor-pointer group relative"
                    :title="isSidebarCollapsed ? 'Buka Sidebar' : 'Tutup Sidebar'"
                >
                    <PanelLeftOpen v-if="isSidebarCollapsed" class="w-5 h-5 text-japan-red transition-transform group-hover:scale-110" />
                    <template v-else>
                        <PanelLeftClose class="w-4 h-4 text-slate-400 group-hover:text-slate-700" />
                        <span class="text-xs font-semibold text-slate-600 group-hover:text-slate-900 font-jp">
                            {{ isJapanese ? 'サイドバーを閉じる' : 'Tutup Sidebar' }}
                        </span>
                    </template>
                    <div v-if="isSidebarCollapsed" class="pointer-events-none absolute left-full ml-3 px-3 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all z-50 shadow-xl font-jp">
                        {{ isJapanese ? 'サイドバーを展開' : 'Buka Sidebar' }}
                    </div>
                </button>
            </div>
        </aside>

        <!-- ================= MOBILE SIDEBAR DRAWER (SLIDE-OVER) ================= -->
        <div v-if="showMobileDrawer" class="fixed inset-0 z-50 md:hidden flex animate-fade-in">
            <!-- Backdrop Overlay -->
            <div @click="showMobileDrawer = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

            <!-- Drawer Content -->
            <div class="relative w-72 max-w-[85vw] bg-white h-full flex flex-col z-10 shadow-2xl border-r border-slate-200">
                <!-- Brand Header in Drawer -->
                <div class="h-16 px-4 flex items-center justify-between border-b border-slate-100">
                    <Link :href="dashboardRoute" prefetch cache-for="30s" @click="showMobileDrawer = false" class="group">
                        <MasayumeLogo />
                    </Link>
                    <button @click="showMobileDrawer = false" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Drawer Navigation Links -->
                <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                    <!-- Shared: Dashboard -->
                    <Link :href="dashboardRoute" prefetch cache-for="30s"
                        @click="showMobileDrawer = false"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200"
                        :class="isDashboardActive
                            ? 'bg-japan-red text-white shadow-md shadow-japan-red/25' 
                            : 'text-slate-800 hover:text-black hover:bg-slate-100'">
                        <LayoutDashboard class="w-4 h-4" />
                        <span>{{ isJapanese ? 'ダッシュボード' : 'Dashboard Utama' }}</span>
                    </Link>

                    <!-- ================= ADMIN NAVIGATION MOBILE DRAWER ================= -->
                    <template v-if="userRole === 'admin'">
                        <div class="pt-3 pb-1 px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 font-jp">
                            {{ isJapanese ? 'マスタ設定・プログラム' : 'Pengaturan Master Data' }}
                        </div>
                        <Link :href="route('admin.master.batches.index')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            :class="route().current('admin.master.batches.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <GraduationCap class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '期生マスタ' : 'Master Angkatan / Batch' }}</span>
                        </Link>
                        <Link :href="route('admin.master.programs.index')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            :class="route().current('admin.master.programs.*') ? 'bg-indigo-50 text-indigo-700 font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <FolderKanban class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? 'プログラム区分マスタ' : 'Master Program Penyaluran' }}</span>
                        </Link>
                        <Link :href="route('admin.master.job-sectors.index')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            :class="route().current('admin.master.job-sectors.*') ? 'bg-amber-50 text-amber-800 font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <Briefcase class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '就労分野マスタ' : 'Master Bidang Kerja' }}</span>
                        </Link>
                        <Link :href="route('admin.master.pipeline-stages.index')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            :class="route().current('admin.master.pipeline-stages.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <ListOrdered class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '進捗ステップマスタ' : 'Master Tahap Penyaluran' }}</span>
                        </Link>
                        <Link :href="route('admin.master.learning-indicators.index')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors font-jp"
                            :class="route().current('admin.master.learning-indicators.*') ? 'bg-rose-50 text-rose-800 font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <Target class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '学習到達目標・評価指標' : 'Indikator Capaian Pembelajaran' }}</span>
                        </Link>

                        <div class="pt-3 pb-1 px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 font-jp">
                            {{ isJapanese ? '実習生・指導員管理' : 'Manajemen Siswa & Sensei' }}
                        </div>
                        <Link :href="route('admin.students.index')" prefetch cache-for="30s" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            :class="route().current('admin.students.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <Users class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '実習生データ管理' : 'Data Siswa Trainee' }}</span>
                        </Link>
                        <Link :href="route('admin.senseis.index')" prefetch cache-for="30s" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            :class="route().current('admin.senseis.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <Users class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '指導員データ管理' : 'Data Sensei / Instruktur' }}</span>
                        </Link>
                        <Link :href="route('admin.users.index')" prefetch cache-for="30s" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            :class="route().current('admin.users.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <Users class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '管理者・スタッフアカウント' : 'Akun Administrator & Staf' }}</span>
                        </Link>

                        <!-- Website LPK & CMS Mobile (Clean Nested Submenu) -->
                        <div>
                            <button
                                type="button"
                                @click="showCmsMenu = !showCmsMenu"
                                class="w-full flex items-center justify-between gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors cursor-pointer font-jp"
                                :class="isCmsMenuActive ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100'"
                            >
                                <span class="flex items-center gap-3">
                                    <Globe class="w-4 h-4" :class="isCmsMenuActive ? 'text-japan-red' : 'text-slate-500'" />
                                    <span>{{ isJapanese ? '公式サイト・CMS' : 'Website LPK & CMS' }}</span>
                                </span>
                                <ChevronDown class="w-3.5 h-3.5 transition-transform duration-200 shrink-0"
                                    :class="[showCmsMenu ? 'rotate-180' : '', isCmsMenuActive ? 'text-japan-red' : 'text-slate-400']"
                                />
                            </button>
                            <div v-if="showCmsMenu" class="mt-1 ml-2 mr-1 p-1 bg-slate-50 rounded-xl border border-slate-100 space-y-0.5">
                                <Link :href="route('admin.website-settings.index', { tab: 'hero' })" prefetch cache-for="1m" @click="showMobileDrawer = false"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                    :class="isCmsTabActive('hero') ? 'bg-white text-japan-red font-bold shadow-xs' : 'text-slate-600 hover:text-slate-950'">
                                    <Settings class="w-3.5 h-3.5 text-slate-400" />
                                    <span>{{ isJapanese ? '基本情報設定' : 'Pengaturan Umum' }}</span>
                                </Link>
                                <Link :href="route('admin.website-settings.index', { tab: 'testimonials' })" prefetch cache-for="1m" @click="showMobileDrawer = false"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                    :class="isCmsTabActive('testimonials') ? 'bg-white text-amber-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-950'">
                                    <MessageSquareQuote class="w-3.5 h-3.5 text-slate-400" />
                                    <span>{{ isJapanese ? '修了生の声' : 'Testimoni Alumni' }}</span>
                                </Link>
                                <Link :href="route('admin.website-settings.index', { tab: 'galleries' })" prefetch cache-for="1m" @click="showMobileDrawer = false"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                    :class="isCmsTabActive('galleries') ? 'bg-white text-indigo-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-950'">
                                    <Camera class="w-3.5 h-3.5 text-slate-400" />
                                    <span>{{ isJapanese ? '活動写真' : 'Galeri Kegiatan' }}</span>
                                </Link>
                                <Link :href="route('admin.website-settings.index', { tab: 'hero_slides' })" prefetch cache-for="1m" @click="showMobileDrawer = false"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                    :class="isCmsTabActive('hero_slides') ? 'bg-white text-emerald-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-950'">
                                    <SlidersHorizontal class="w-3.5 h-3.5 text-slate-400" />
                                    <span>{{ isJapanese ? 'スライダー' : 'Slide Banner Hero' }}</span>
                                </Link>
                            </div>
                        </div>
                    </template>

                    <!-- ================= SENSEI NAVIGATION MOBILE DRAWER ================= -->
                    <template v-if="userRole === 'sensei'">
                        <div class="pt-3 pb-1 px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 font-jp">
                            {{ isJapanese ? '指導員ポータル' : 'Menu Utama Sensei' }}
                        </div>
                        <Link :href="route('sensei.lms.index')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors font-jp"
                            :class="route().current('sensei.lms.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <BookOpen class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '課別教材管理' : 'LMS (Materi & Kurikulum)' }}</span>
                        </Link>
                        <Link :href="route('sensei.vocabularies.index')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors font-jp"
                            :class="route().current('sensei.vocabularies.*') ? 'bg-rose-50 text-rose-700 font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <Layers class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '単語・語彙管理' : 'Kotoba (Kosakata)' }}</span>
                        </Link>

                        <!-- SOAL CBT Mobile Submenu -->
                        <div>
                            <button
                                type="button"
                                @click="showSoalMenu = !showSoalMenu"
                                class="w-full flex items-center justify-between gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors cursor-pointer font-jp"
                                :class="isSoalMenuActive ? 'bg-amber-50 text-amber-700 font-black' : 'text-slate-700 hover:bg-slate-100'"
                            >
                                <span class="flex items-center gap-3">
                                    <HelpCircle class="w-4 h-4" :class="isSoalMenuActive ? 'text-amber-500' : 'text-slate-500'" />
                                    <span>{{ isJapanese ? 'CBT問題バンク' : 'Soal CBT' }}</span>
                                </span>
                                <ChevronDown class="w-3.5 h-3.5 transition-transform duration-200 shrink-0"
                                    :class="[showSoalMenu ? 'rotate-180' : '', isSoalMenuActive ? 'text-amber-500' : 'text-slate-400']"
                                />
                            </button>
                            <div v-if="showSoalMenu" class="mt-1 ml-2 mr-1 p-1 bg-slate-50 rounded-xl border border-slate-100 space-y-0.5">
                                <Link :href="route('sensei.settings.questions')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors font-jp"
                                    :class="route().current('sensei.settings.*') ? 'bg-white text-violet-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-950'">
                                    <Settings class="w-3.5 h-3.5 text-slate-400" />
                                    <span>{{ isJapanese ? '難易度・分野設定' : 'Pengaturan Bank Soal' }}</span>
                                </Link>
                                <Link :href="route('sensei.questions.index')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors font-jp"
                                    :class="route().current('sensei.questions.*') ? 'bg-white text-amber-700 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-950'">
                                    <HelpCircle class="w-3.5 h-3.5 text-slate-400" />
                                    <span>{{ isJapanese ? '問題一覧・登録' : 'Bank Soal (Paket)' }}</span>
                                </Link>
                            </div>
                        </div>

                        <Link :href="route('sensei.exams.index')" prefetch cache-for="30s" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors font-jp"
                            :class="route().current('sensei.exams.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <Clock class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? 'CBT試験・日程管理' : 'CBT (Ujian & Jadwal)' }}</span>
                        </Link>
                        <Link :href="route('sensei.grades.index')" prefetch cache-for="30s" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors font-jp"
                            :class="route().current('sensei.grades.*') ? 'bg-emerald-50 text-emerald-700 font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <Award class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '成績・習熟度評価' : 'Rapor & Kemahiran Siswa' }}</span>
                        </Link>
                    </template>

                    <!-- ================= SISWA NAVIGATION MOBILE DRAWER ================= -->
                    <template v-if="userRole === 'siswa'">
                        <div class="pt-3 pb-1 px-3 text-[10px] font-black uppercase tracking-wider text-slate-400 font-jp">
                            {{ isJapanese ? '実習生学習ルーム' : 'Ruang Belajar Siswa' }}
                        </div>
                        <Link :href="route('siswa.lms.index')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            :class="route().current('siswa.lms.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <BookOpen class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '課別テキスト（1〜50課）' : 'Materi Bab (1-50)' }}</span>
                        </Link>
                        <Link :href="route('siswa.flashcards.index')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            :class="route().current('siswa.flashcards.*') ? 'bg-amber-50 text-amber-700 font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <Layers class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? '単語フラッシュカード' : 'Kotoba Flashcards' }}</span>
                        </Link>
                        <Link :href="route('siswa.exams.index')" prefetch cache-for="30s" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            :class="route().current('siswa.exams.*') || route().current('siswa.cbt.*') ? 'bg-red-50 text-japan-red font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <Clock class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? 'N4 CBT模擬試験' : 'Simulasi CBT N4' }}</span>
                        </Link>
                        <Link :href="route('siswa.journey.index')" prefetch cache-for="1m" @click="showMobileDrawer = false"
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                            :class="route().current('siswa.journey.*') ? 'bg-emerald-50 text-emerald-700 font-black' : 'text-slate-700 hover:bg-slate-100'">
                            <CheckCircle2 class="w-4 h-4 text-slate-500" />
                            <span>{{ isJapanese ? 'ビザ・出国進捗' : 'Progres Visa & Terbang' }}</span>
                        </Link>
                    </template>
                </nav>
            </div>
        </div>

        <!-- ================= MAIN CONTENT AREA ================= -->
        <div 
            class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
            :class="isSidebarCollapsed ? 'md:pl-20' : 'md:pl-64 lg:pl-72'"
        >
            <!-- Top Navbar -->
            <header class="h-16 sm:h-20 bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-20 px-3 sm:px-6 lg:px-8 flex items-center justify-between">
                <!-- Mobile Hamburger + Brand Header -->
                <div class="flex items-center gap-1.5 md:hidden shrink-0">
                    <button 
                        type="button" 
                        @click="showMobileDrawer = true" 
                        class="p-1.5 rounded-xl text-slate-700 hover:text-black hover:bg-slate-100 transition-colors cursor-pointer"
                        title="Buka Menu"
                    >
                        <Menu class="w-5 h-5" />
                    </button>
                    <Link :href="dashboardRoute" prefetch cache-for="30s" class="flex items-center scale-90 sm:scale-100 origin-left">
                        <MasayumeLogo />
                    </Link>
                </div>

                <!-- Desktop / Tablet Slider Toggle + Breadcrumb -->
                <div class="hidden md:flex items-center gap-3 text-sm text-slate-900 font-jp font-bold">
                    <button 
                        type="button" 
                        @click="toggleSidebar" 
                        class="p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all cursor-pointer"
                        :title="isSidebarCollapsed ? 'Buka Sidebar' : 'Tutup Sidebar'"
                    >
                        <PanelLeftOpen v-if="isSidebarCollapsed" class="w-5 h-5 text-japan-red" />
                        <PanelLeftClose v-else class="w-5 h-5 text-slate-600" />
                    </button>

                    <div class="flex items-center gap-2 text-xs sm:text-sm">
                        <span class="font-black text-slate-950 capitalize">
                            {{ userRole === 'admin' ? (isJapanese ? '管理者ポータル' : 'Admin Portal') : userRole === 'sensei' ? (isJapanese ? '指導教員ポータル' : 'Sensei Portal') : (isJapanese ? '実習生ポータル' : 'Siswa Portal') }}
                        </span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-700 font-semibold truncate max-w-[220px] lg:max-w-md">{{ currentBreadcrumbTitle }}</span>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-1.5 sm:gap-3">
                    <!-- Language Switcher Toggle in Navbar -->
                    <div class="flex items-center bg-slate-100 border border-slate-200/80 p-0.5 rounded-full shadow-inner scale-90 sm:scale-100">
                        <button
                            type="button"
                            @click="setLang('id')"
                            class="flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer"
                            :class="isIndonesian 
                                ? 'bg-japan-red text-white shadow-sm' 
                                : 'text-slate-700 hover:text-slate-950 font-bold'"
                        >
                            <span>🇮🇩</span>
                            <span class="hidden xs:inline">ID</span>
                        </button>
                        <button
                            type="button"
                            @click="setLang('ja')"
                            class="flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer font-jp"
                            :class="isJapanese 
                                ? 'bg-japan-red text-white shadow-sm' 
                                : 'text-slate-700 hover:text-slate-950 font-bold'"
                        >
                            <span>🇯🇵</span>
                            <span class="hidden xs:inline">JP</span>
                        </button>
                    </div>

                    <!-- Notification Bell -->
                    <button class="relative p-1.5 sm:p-2 rounded-xl text-slate-700 hover:text-black hover:bg-slate-100 transition-colors cursor-pointer">
                        <Bell class="w-4 h-4 sm:w-5 sm:h-5" />
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-japan-red rounded-full ring-2 ring-white animate-pulse"></span>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center gap-1.5 sm:gap-2.5 p-1 sm:p-1.5 sm:pr-3 rounded-full hover:bg-slate-100 transition-colors border border-slate-200 cursor-pointer">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-slate-800 to-slate-950 text-white flex items-center justify-center font-bold text-xs shadow-sm overflow-hidden border border-slate-300/60">
                                        <img v-if="$page.props.auth.user.avatar_url" :src="$page.props.auth.user.avatar_url" alt="Avatar" class="w-full h-full object-cover" />
                                        <span v-else>{{ $page.props.auth.user.name.charAt(0) }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-slate-900 hidden sm:inline-block max-w-[100px] truncate">
                                        {{ $page.props.auth.user.name }}
                                    </span>
                                    <ChevronDown class="w-3.5 h-3.5 text-slate-700" />
                                </button>
                            </template>

                            <template #content>
                                <div class="px-4 py-2 border-b border-slate-100 text-xs">
                                    <p class="font-bold text-slate-950 truncate">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-slate-600 truncate font-mono text-[11px]">{{ $page.props.auth.user.email }}</p>
                                </div>

                                <DropdownLink :href="route('profile.edit')" class="flex items-center gap-2 text-xs font-semibold py-2">
                                    <User class="w-4 h-4 text-slate-500" />
                                    <span>{{ isJapanese ? 'プロファイル設定' : 'Profil Pengguna' }}</span>
                                </DropdownLink>

                                <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center gap-2 text-xs font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 py-2">
                                    <LogOut class="w-4 h-4" />
                                    <span>{{ isJapanese ? 'ログアウト' : 'Keluar Sistem' }}</span>
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- Page Main Content Slot -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                <!-- Flash Messages Feedback Banner -->
                <div v-if="$page.props.flash?.success" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-xs animate-fade-in">
                    <CheckCircle2 class="w-5 h-5 text-emerald-600 shrink-0" />
                    <span>{{ $page.props.flash.success }}</span>
                </div>

                <div v-if="$page.props.flash?.error" class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-xs animate-fade-in">
                    <AlertCircle class="w-5 h-5 text-rose-600 shrink-0" />
                    <span>{{ $page.props.flash.error }}</span>
                </div>

                <slot />
            </main>
        </div>

        <!-- ================= MOBILE BOTTOM NAVIGATION ================= -->
        <div class="md:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200 z-30 px-3 py-2 flex items-center justify-around shadow-lg">
            <Link :href="dashboardRoute" prefetch cache-for="30s"
                class="flex flex-col items-center gap-1 text-[10px] font-bold"
                :class="isDashboardActive ? 'text-japan-red' : 'text-slate-600'">
                <LayoutDashboard class="w-4 h-4" />
                <span>Dashboard</span>
            </Link>

            <!-- Admin Menus Mobile -->
            <template v-if="userRole === 'admin'">
                <Link :href="route('admin.students.index')" prefetch cache-for="30s"
                    class="flex flex-col items-center gap-1 text-[10px] font-bold"
                    :class="route().current('admin.students.*') ? 'text-japan-red' : 'text-slate-600'">
                    <Users class="w-4 h-4" />
                    <span>Siswa</span>
                </Link>
                <Link :href="route('admin.master.batches.index')" prefetch cache-for="1m"
                    class="flex flex-col items-center gap-1 text-[10px] font-bold"
                    :class="route().current('admin.master.batches.*') ? 'text-japan-red' : 'text-slate-600'">
                    <GraduationCap class="w-4 h-4" />
                    <span>Angkatan</span>
                </Link>
                <Link :href="route('admin.website-settings.index')" prefetch cache-for="1m"
                    class="flex flex-col items-center gap-1 text-[10px] font-bold"
                    :class="route().current('admin.website-settings.*') ? 'text-japan-red' : 'text-slate-600'">
                    <Globe class="w-4 h-4" />
                    <span>CMS</span>
                </Link>
            </template>

            <!-- Sensei Menus Mobile -->
            <template v-if="userRole === 'sensei'">
                <Link :href="route('sensei.lms.index')" prefetch cache-for="1m"
                    class="flex flex-col items-center gap-1 text-[10px] font-bold"
                    :class="route().current('sensei.lms.*') ? 'text-blue-700' : 'text-slate-600'">
                    <BookOpen class="w-4 h-4" />
                    <span>LMS</span>
                </Link>
                <Link :href="route('sensei.questions.index')" prefetch cache-for="1m"
                    class="flex flex-col items-center gap-1 text-[10px] font-bold"
                    :class="isSoalMenuActive ? 'text-amber-700' : 'text-slate-600'">
                    <HelpCircle class="w-4 h-4" />
                    <span>Soal</span>
                </Link>
                <Link :href="route('sensei.exams.index')" prefetch cache-for="30s"
                    class="flex flex-col items-center gap-1 text-[10px] font-bold"
                    :class="route().current('sensei.exams.*') ? 'text-japan-red' : 'text-slate-600'">
                    <Clock class="w-4 h-4" />
                    <span>CBT</span>
                </Link>
            </template>

            <!-- Siswa Menus Mobile -->
            <template v-if="userRole === 'siswa'">
                <Link :href="route('siswa.lms.index')" prefetch cache-for="1m"
                    class="flex flex-col items-center gap-1 text-[10px] font-bold"
                    :class="route().current('siswa.lms.*') ? 'text-blue-700 font-black' : 'text-slate-600'">
                    <BookOpen class="w-4 h-4" />
                    <span>Materi</span>
                </Link>
                <Link :href="route('siswa.flashcards.index')" prefetch cache-for="1m"
                    class="flex flex-col items-center gap-1 text-[10px] font-bold"
                    :class="route().current('siswa.flashcards.*') ? 'text-amber-700 font-black' : 'text-slate-600'">
                    <Layers class="w-4 h-4" />
                    <span>Kotoba</span>
                </Link>
                <Link :href="route('siswa.exams.index')" prefetch cache-for="30s"
                    class="flex flex-col items-center gap-1 text-[10px] font-bold"
                    :class="route().current('siswa.exams.*') || route().current('siswa.cbt.*') ? 'text-japan-red font-black' : 'text-slate-600'">
                    <Clock class="w-4 h-4" />
                    <span>CBT</span>
                </Link>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import MasayumeLogo from '@/Components/MasayumeLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { useLang } from '@/Composables/useLang';
import { 
    LayoutDashboard, 
    Users, 
    GraduationCap, 
    BookOpen, 
    Clock, 
    Award, 
    CheckCircle2, 
    AlertCircle, 
    Bell, 
    ChevronDown, 
    User, 
    LogOut,
    Briefcase,
    ListOrdered,
    FolderKanban,
    ShieldCheck,
    Layers,
    HelpCircle,
    Menu,
    X,
    Settings,
    Globe,
    Target,
    MessageSquareQuote,
    Camera,
    SlidersHorizontal,
    PanelLeftClose,
    PanelLeftOpen
} from 'lucide-vue-next';

const page = usePage();
const { isJapanese, isIndonesian, setLang } = useLang();
const showMobileDrawer = ref(false);

// Sidebar Slider Collapse/Expand state
const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
    try {
        localStorage.setItem('sidebar_collapsed', isSidebarCollapsed.value ? 'true' : 'false');
    } catch (e) {
        // LocalStorage fallback
    }
};

onMounted(() => {
    try {
        const saved = localStorage.getItem('sidebar_collapsed');
        if (saved !== null) {
            isSidebarCollapsed.value = saved === 'true';
        } else if (window.innerWidth < 1200 && window.innerWidth >= 768) {
            // Auto-collapse pada tablet / laptop sedang agar ruang kerja lega
            isSidebarCollapsed.value = true;
        }
    } catch (e) {
        // Fallback
    }
});

// SOAL CBT collapsible menu state — auto-expand when on related routes
const showSoalMenu = ref(
    route().current('sensei.questions.*') || route().current('sensei.settings.*')
);
const isSoalMenuActive = computed(
    () => route().current('sensei.questions.*') || route().current('sensei.settings.*')
);

// CMS Website collapsible menu state — auto-expand when on related routes
const showCmsMenu = ref(route().current('admin.website-settings.*'));
const isCmsMenuActive = computed(() => route().current('admin.website-settings.*'));

const isCmsTabActive = (tab) => {
    if (!route().current('admin.website-settings.*')) return false;
    if (tab === 'hero') {
        return !page.url.includes('tab=') || page.url.includes('tab=hero');
    }
    return page.url.includes(`tab=${tab}`);
};

const userRole = computed(() => {
    const role = page.props.auth?.user?.role;
    if (role) return role;
    const roles = page.props.auth?.user?.roles || [];
    if (roles.includes('admin')) return 'admin';
    if (roles.includes('sensei')) return 'sensei';
    if (roles.includes('siswa')) return 'siswa';
    return 'siswa';
});

const dashboardRoute = computed(() => {
    if (userRole.value === 'admin') return route('admin.dashboard');
    if (userRole.value === 'sensei') return route('sensei.dashboard');
    return route('siswa.dashboard');
});

const isDashboardActive = computed(() => {
    return route().current('dashboard') || 
           route().current('*.dashboard') || 
           route().current('admin.dashboard') || 
           route().current('sensei.dashboard') || 
           route().current('siswa.dashboard');
});

const currentBreadcrumbTitle = computed(() => {
    // Sensei Routes
    if (route().current('sensei.vocabularies.*')) return isJapanese.value ? '単語・語彙管理' : 'Manajemen Kosakata (Kotoba)';
    if (route().current('sensei.lms.*')) return isJapanese.value ? '課別教材管理' : 'LMS Materi & Kurikulum';
    if (route().current('sensei.questions.*')) return isJapanese.value ? '問題一覧・登録' : 'Bank Soal CBT';
    if (route().current('sensei.settings.*')) return isJapanese.value ? '難易度・分野設定' : 'Pengaturan Bank Soal';
    if (route().current('sensei.exams.*')) return isJapanese.value ? 'CBT試験・日程管理' : 'CBT Ujian & Jadwal';
    if (route().current('sensei.grades.*')) return isJapanese.value ? '成績・習熟度評価' : 'Rapor & Kemahiran Siswa';
    
    // Admin Routes
    if (route().current('admin.master.learning-indicators.*')) return isJapanese.value ? '学習到達目標・評価指標' : 'Indikator Capaian Pembelajaran';
    if (route().current('admin.students.*')) return isJapanese.value ? '実習生・生徒管理' : 'Data Siswa Trainee';
    if (route().current('admin.senseis.*')) return isJapanese.value ? '指導員・Sensei管理' : 'Data Instruktur Sensei';
    if (route().current('admin.master.batches.*')) return isJapanese.value ? '期生・クラス管理' : 'Angkatan & Kelas Batch';
    if (route().current('admin.website-settings.*')) {
        if (page.url.includes('tab=testimonials')) return isJapanese.value ? '修了生の声・体験談管理' : 'Testimoni Alumni Siswa';
        if (page.url.includes('tab=galleries')) return isJapanese.value ? '活動写真ギャラリー管理' : 'Galeri Kegiatan LPK';
        if (page.url.includes('tab=hero_slides')) return isJapanese.value ? 'スライダー・バナー管理' : 'Slide Banner Hero';
        return isJapanese.value ? '公式サイト設定・CMS' : 'Pengaturan Website & CMS';
    }
    if (route().current('admin.roles.*')) return isJapanese.value ? '権限・ロール管理' : 'Hak Akses & Role';
    if (route().current('admin.users.*')) return isJapanese.value ? '管理者・スタッフアカウント管理' : 'Akun Administrator & Staf';

    // Siswa Routes
    if (route().current('siswa.lms.*')) return isJapanese.value ? '課別テキスト' : 'Ruang Belajar LMS';
    if (route().current('siswa.flashcards.*')) return isJapanese.value ? '単語フラッシュカード' : 'Gym Kosakata Kotoba';
    if (route().current('siswa.exams.*') || route().current('siswa.cbt.*')) return isJapanese.value ? 'CBT模擬試験' : 'Simulasi Ujian CBT';
    if (route().current('siswa.journey.*')) return isJapanese.value ? 'ビザ・出国進捗' : 'Progres Visa & Penyaluran';

    return isJapanese.value ? 'ダッシュボード' : 'Dashboard';
});
</script>
