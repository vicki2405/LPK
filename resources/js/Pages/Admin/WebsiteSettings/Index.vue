<template>
    <Head title="Pengaturan Website LPK (CMS Landing Page) - Masayume" />

    <AuthenticatedLayout>
        <div class="space-y-6 animate-fade-in pb-16">

            <!-- ================= HEADER SECTION ================= -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-japan-red text-xs font-bold mb-2 border border-red-100">
                        <Globe class="w-3.5 h-3.5" />
                        <span>Content Management System (CMS)</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-950 tracking-tight flex items-center gap-2.5">
                        <Globe class="w-6 h-6 text-japan-red" />
                        <span>Pengaturan &amp; Konten Website LPK</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1 font-medium">
                        Kelola seluruh teks promosi, statistik, kontak, program, sektor kerja, dan testimoni pada website publik LPK.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        :href="route('home')"
                        target="_blank"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-all flex items-center gap-2 border border-slate-200 cursor-pointer"
                    >
                        <ExternalLink class="w-4 h-4 text-slate-600" />
                        <span>Lihat Website Depan</span>
                    </a>
                </div>
            </div>

            <!-- ================= WORKSPACE TABS NAVIGATION ================= -->
            <div class="bg-white p-2 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-1.5 overflow-x-auto pb-2 sm:pb-2">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    type="button"
                    @click="activeTab = tab.id"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer flex items-center gap-2"
                    :class="activeTab === tab.id
                        ? 'bg-japan-red text-white shadow-xs'
                        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                >
                    <span>{{ tab.icon }}</span>
                    <span>{{ tab.name }}</span>
                </button>
            </div>

            <!-- ================= TAB 1: IDENTITAS & HERO ================= -->
            <div v-if="activeTab === 'hero'" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                            <span>🎌</span> Identitas Lembaga &amp; Teks Hero Depan
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Teks utama yang tampil di bagian paling atas saat pengunjung membuka web.</p>
                    </div>
                </div>

                <form @submit.prevent="saveGeneralSettings" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Resmi LPK *</label>
                            <input type="text" v-model="generalForm.lpk_name" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-japan-red focus:border-japan-red" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Teks Badge Atas Hero</label>
                            <input type="text" v-model="generalForm.lpk_hero_badge" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-japan-red focus:border-japan-red" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Headline / Judul Utama Promosi *</label>
                        <input type="text" v-model="generalForm.lpk_tagline" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-japan-red focus:border-japan-red" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Promosi Hero *</label>
                        <textarea rows="3" v-model="generalForm.lpk_hero_description" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-japan-red focus:border-japan-red"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Teks Legalitas Top Bar</label>
                            <input type="text" v-model="generalForm.lpk_legal_info" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-japan-red focus:border-japan-red" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Sub-heading Top Bar</label>
                            <input type="text" v-model="generalForm.lpk_topbar_subtitle" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-japan-red focus:border-japan-red" />
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" :disabled="isSaving" class="px-6 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white font-black text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer">
                            <Save class="w-4 h-4" />
                            <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ================= TAB: SLIDER HERO CARD ================= -->
            <div v-if="activeTab === 'hero_slides'" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                            <span>🎠</span> Slider Hero Card (Lowongan &amp; Banner Berjalan)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Kelola slide kartu informasi lowongan dan banner hero di halaman utama. Mendukung multi-slide carousel otomatis.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="openCreateHeroSlideModal"
                        class="px-4 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white font-black text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer shrink-0"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Tambah Slide Baru</span>
                    </button>
                </div>

                <!-- Slides Grid -->
                <div v-if="heroSlides && heroSlides.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="slide in heroSlides"
                        :key="slide.id"
                        class="bg-slate-900 rounded-3xl overflow-hidden border border-slate-800 shadow-lg flex flex-col justify-between group transition-all duration-300 hover:border-japan-red/50 hover:shadow-2xl"
                    >
                        <!-- Slide Banner Image & Overlays -->
                        <div class="relative h-48 w-full overflow-hidden bg-slate-950">
                            <img
                                :src="slide.image_path"
                                :alt="slide.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent"></div>

                            <!-- Top Badges -->
                            <div class="absolute top-3 inset-x-3 flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-950/80 text-amber-400 border border-amber-400/30 backdrop-blur-md">
                                    ⭐ {{ slide.badge_top || '98% JLPT N4' }}
                                </span>
                                <span
                                    class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider backdrop-blur-md border"
                                    :class="slide.is_active ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-slate-800/80 text-slate-400 border-slate-700'"
                                >
                                    {{ slide.is_active ? '● Aktif' : '○ Non-Aktif' }}
                                </span>
                            </div>

                            <!-- Bottom Title inside Image -->
                            <div class="absolute bottom-3 inset-x-3">
                                <span class="text-[10px] font-mono text-emerald-400 font-bold block mb-0.5">
                                    {{ slide.status_label || 'Status: Dibuka' }} • Urutan #{{ slide.sort_order }}
                                </span>
                                <h3 class="text-xs font-black text-white line-clamp-1">
                                    {{ slide.title }}
                                </h3>
                            </div>
                        </div>

                        <!-- Card Body Details -->
                        <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="grid grid-cols-2 gap-2 text-[11px]">
                                    <div class="bg-white/5 p-2 rounded-xl border border-white/10">
                                        <span class="text-[9px] text-slate-400 block font-bold">💰 Standar Gaji:</span>
                                        <span class="font-black text-emerald-400 font-mono text-xs">{{ slide.salary_jpy }}</span>
                                        <span class="text-[9px] text-slate-400 block mt-0.5">{{ slide.salary_idr }}</span>
                                    </div>
                                    <div class="bg-white/5 p-2 rounded-xl border border-white/10">
                                        <span class="text-[9px] text-slate-400 block font-bold">🛫 Penempatan:</span>
                                        <span class="font-bold text-white line-clamp-1">{{ slide.placement_location }}</span>
                                        <span class="text-[9px] text-amber-300 font-bold block mt-0.5">{{ slide.facilities }}</span>
                                    </div>
                                </div>
                                <div v-if="slide.title_jp" class="text-[10px] text-slate-400 font-jp line-clamp-1 italic">
                                    🇯🇵 {{ slide.title_jp }}
                                </div>
                            </div>

                            <!-- Actions Footer -->
                            <div class="pt-3 border-t border-slate-800 flex items-center justify-between gap-2">
                                <span class="text-[10px] font-black text-japan-red uppercase tracking-wider truncate">
                                    🔘 {{ slide.cta_text || 'Daftar' }}
                                </span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button
                                        type="button"
                                        @click="openEditHeroSlideModal(slide)"
                                        class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors cursor-pointer"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteHeroSlide(slide)"
                                        class="px-2.5 py-1.5 rounded-lg bg-red-500/20 hover:bg-red-500 text-red-300 hover:text-white text-xs font-bold transition-colors cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12 border-2 border-dashed border-slate-200 rounded-3xl p-6">
                    <p class="text-sm font-bold text-slate-500">Belum ada slide kartu hero yang ditambahkan.</p>
                    <button
                        type="button"
                        @click="openCreateHeroSlideModal"
                        class="mt-3 px-4 py-2 rounded-xl bg-japan-red text-white text-xs font-black inline-flex items-center gap-2 cursor-pointer"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Tambah Slide Pertama</span>
                    </button>
                </div>
            </div>

            <!-- ================= TAB 2: REGISTRATION CARD ================= -->
            <div v-if="activeTab === 'registration'" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                            <span>📝</span> Banner &amp; Status Gelombang Pendaftaran
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Informasi kartu pendaftaran yang tampil di sisi kanan hero section.</p>
                    </div>
                </div>

                <form @submit.prevent="saveGeneralSettings" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Pendaftaran Gelombang *</label>
                            <select v-model="generalForm.reg_status_open" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-japan-red focus:border-japan-red">
                                <option value="1">🟢 Dibuka (Aktif Menerima Siswa)</option>
                                <option value="0">🔴 Ditutup Sementara</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul Gelombang Angkatan</label>
                            <input type="text" v-model="generalForm.reg_batch_title" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-japan-red focus:border-japan-red" />
                        </div>
                    </div>

                    <!-- 3 Poin Keunggulan Pendaftaran -->
                    <div class="space-y-4 pt-2 border-t border-slate-100">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">3 Poin Keunggulan di Kartu Pendaftaran</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Judul Poin 1</label>
                                <input type="text" v-model="generalForm.reg_point_1_title" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-bold" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Keterangan Poin 1</label>
                                <input type="text" v-model="generalForm.reg_point_1_desc" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Judul Poin 2</label>
                                <input type="text" v-model="generalForm.reg_point_2_title" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-bold" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Keterangan Poin 2</label>
                                <input type="text" v-model="generalForm.reg_point_2_desc" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Judul Poin 3</label>
                                <input type="text" v-model="generalForm.reg_point_3_title" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-bold" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Keterangan Poin 3</label>
                                <input type="text" v-model="generalForm.reg_point_3_desc" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" :disabled="isSaving" class="px-6 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white font-black text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer">
                            <Save class="w-4 h-4" />
                            <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ================= TAB 3: STATISTIK ================= -->
            <div v-if="activeTab === 'stats'" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                            <span>📊</span> 4 Angka Indikator Statistik Promosi
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Angka pencapaian yang tampil di bar hitam di bawah hero section.</p>
                    </div>
                </div>

                <form @submit.prevent="saveGeneralSettings" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block text-xs font-black text-amber-600 uppercase">Statistik 1 (Kelulusan)</label>
                            <input type="text" v-model="generalForm.stat_passing_rate" placeholder="Contoh: 98%" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm font-black font-mono" />
                            <input type="text" v-model="generalForm.stat_passing_label" placeholder="Label" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-600" />
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block text-xs font-black text-emerald-600 uppercase">Statistik 2 (Alumni)</label>
                            <input type="text" v-model="generalForm.stat_alumni_count" placeholder="Contoh: 350+" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm font-black font-mono" />
                            <input type="text" v-model="generalForm.stat_alumni_label" placeholder="Label" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-600" />
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block text-xs font-black text-blue-600 uppercase">Statistik 3 (Mitra Kerja)</label>
                            <input type="text" v-model="generalForm.stat_partner_count" placeholder="Contoh: 85+" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm font-black font-mono" />
                            <input type="text" v-model="generalForm.stat_partner_label" placeholder="Label" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-600" />
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block text-xs font-black text-rose-600 uppercase">Statistik 4 (Legalitas)</label>
                            <input type="text" v-model="generalForm.stat_legal_count" placeholder="Contoh: 100%" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm font-black font-mono" />
                            <input type="text" v-model="generalForm.stat_legal_label" placeholder="Label" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-600" />
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" :disabled="isSaving" class="px-6 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white font-black text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer">
                            <Save class="w-4 h-4" />
                            <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ================= TAB 4: TENTANG & FILOSOFI ================= -->
            <div v-if="activeTab === 'about'" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                            <span>📖</span> Profil Lembaga &amp; Filosofi Masayume
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Narasi perkenalan lembaga dan filosofi pembinaan etika kerja Jepang.</p>
                    </div>
                </div>

                <form @submit.prevent="saveGeneralSettings" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul Bagian Tentang Kami *</label>
                        <input type="text" v-model="generalForm.about_title" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Paragraf 1 (Profil Utama)</label>
                            <textarea rows="3" v-model="generalForm.about_p1" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Paragraf 2 (Metode &amp; Digital)</label>
                            <textarea rows="3" v-model="generalForm.about_p2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block text-xs font-bold text-slate-700">Kotak Keunggulan 1</label>
                            <input type="text" v-model="generalForm.about_feature_1_title" placeholder="Judul" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold" />
                            <input type="text" v-model="generalForm.about_feature_1_desc" placeholder="Keterangan singkat" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs" />
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block text-xs font-bold text-slate-700">Kotak Keunggulan 2</label>
                            <input type="text" v-model="generalForm.about_feature_2_title" placeholder="Judul" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold" />
                            <input type="text" v-model="generalForm.about_feature_2_desc" placeholder="Keterangan singkat" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <!-- Filosofi -->
                    <div class="p-5 rounded-2xl bg-red-50 border border-red-200 space-y-3">
                        <h3 class="text-xs font-black text-japan-red uppercase tracking-wider">Filosofi &amp; Slogan Lembaga</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Judul Slogan Filosofi</label>
                                <input type="text" v-model="generalForm.philosophy_title" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-bold" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Arti &amp; Makna Filosofi</label>
                                <textarea rows="2" v-model="generalForm.philosophy_desc" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" :disabled="isSaving" class="px-6 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white font-black text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer">
                            <Save class="w-4 h-4" />
                            <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ================= TAB 5: PROGRAM PELATIHAN ================= -->
            <div v-if="activeTab === 'programs'" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                            <span>💼</span> Kelola Program Pelatihan &amp; Penyaluran
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Atur deskripsi, estimasi gaji/uang saku, dan status aktif program di landing page.</p>
                    </div>

                    <button
                        type="button"
                        @click="openCreateProgramModal"
                        class="px-4 py-2 rounded-xl bg-japan-red hover:bg-red-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
                    >
                        <Plus class="w-4 h-4" />
                        <span>+ Tambah Program Baru</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="prog in programs" :key="prog.id" class="p-6 rounded-3xl border-2 border-slate-200 hover:border-japan-red/50 transition-all flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-3xl">{{ prog.icon || '💼' }}</span>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase" :class="prog.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500'">
                                    {{ prog.is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </div>

                            <h3 class="text-base font-black text-slate-900">{{ prog.name }}</h3>
                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">{{ prog.description }}</p>

                            <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Kisaran Gaji / Saku:</span>
                                <span class="font-black text-slate-900 font-mono">{{ prog.salary_range || 'Sesuai Standar Jepang' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                            <button
                                type="button"
                                @click="openEditProgramModal(prog)"
                                class="flex-1 py-2 rounded-xl bg-slate-900 hover:bg-japan-red text-white text-xs font-bold transition-colors cursor-pointer text-center"
                            >
                                ✏️ Edit
                            </button>
                            <button
                                type="button"
                                @click="deleteProgram(prog)"
                                class="px-3 py-2 rounded-xl bg-rose-50 text-japan-red hover:bg-rose-100 text-xs font-bold transition-colors cursor-pointer"
                            >
                                🗑️ Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 6: SEKTOR PEKERJAAN ================= -->
            <div v-if="activeTab === 'sectors'" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                            <span>🏭</span> Sektor Pekerjaan di Jepang
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Ubah ikon emoji, nama sektor, atau nonaktifkan sektor yang sedang tidak dibuka.</p>
                    </div>

                    <button
                        type="button"
                        @click="openCreateSectorModal"
                        class="px-4 py-2 rounded-xl bg-japan-red hover:bg-red-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
                    >
                        <Plus class="w-4 h-4" />
                        <span>+ Tambah Sektor Baru</span>
                    </button>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div v-for="sec in jobSectors" :key="sec.id" class="p-4 rounded-2xl border border-slate-200 text-center space-y-2 hover:shadow-sm transition-all flex flex-col justify-between" :class="sec.is_active ? 'bg-white' : 'bg-slate-50 opacity-60'">
                        <div class="space-y-1">
                            <span class="text-3xl block">{{ sec.icon || '🏢' }}</span>
                            <h4 class="text-xs font-bold text-slate-900 line-clamp-1">{{ sec.name }}</h4>
                            <span class="text-[10px] text-slate-400 font-jp block">{{ sec.name_jp || '-' }}</span>
                        </div>
                        
                        <div class="flex items-center gap-1 pt-2 border-t border-slate-100">
                            <button
                                type="button"
                                @click="openEditSectorModal(sec)"
                                class="flex-1 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-bold text-slate-700 transition-colors cursor-pointer"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                @click="deleteSector(sec)"
                                class="p-1 rounded-lg bg-rose-50 text-japan-red hover:bg-rose-100 text-[11px] font-bold transition-colors cursor-pointer"
                            >
                                ✕
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 7: TESTIMONI ALUMNI ================= -->
            <div v-if="activeTab === 'testimonials'" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                            <span>💬</span> Testimoni Alumni Siswa di Jepang
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Kisah sukses siswa yang bekerja di Jepang untuk meyakinkan calon pendaftar.</p>
                    </div>

                    <button
                        type="button"
                        @click="openCreateTestimonialModal"
                        class="px-4 py-2 rounded-xl bg-japan-red hover:bg-red-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
                    >
                        <Plus class="w-4 h-4" />
                        <span>+ Tambah Testimoni</span>
                    </button>
                </div>

                <div v-if="testimonials && testimonials.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="testi in testimonials" :key="testi.id" class="p-6 rounded-3xl bg-slate-50 border border-slate-200 flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900">
                                    📍 {{ testi.japan_location }}
                                </span>
                                <span class="text-[10px] font-bold" :class="testi.is_published ? 'text-emerald-600' : 'text-slate-400'">
                                    {{ testi.is_published ? '● Tampil di Web' : 'Draft' }}
                                </span>
                            </div>

                            <h3 class="text-sm font-black text-slate-900">{{ testi.name }}</h3>
                            <span class="text-[11px] font-bold text-slate-500 block">{{ testi.work_sector }} ({{ testi.program_type || 'Tokutei Ginou' }})</span>

                            <p class="text-xs text-slate-600 leading-relaxed italic bg-white p-3 rounded-xl border border-slate-200/80">
                                "{{ testi.testimony_text }}"
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                            <button @click="openEditTestimonialModal(testi)" class="px-3 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 cursor-pointer">
                                Edit
                            </button>
                            <button @click="deleteTestimonial(testi)" class="px-3 py-1 rounded-lg bg-rose-50 text-japan-red hover:bg-rose-100 text-xs font-bold cursor-pointer">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State Testimoni -->
                <div v-else class="text-center py-16 px-4 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 space-y-3">
                    <span class="text-4xl block">💬</span>
                    <h3 class="text-sm font-bold text-slate-900">Belum Ada Testimoni Alumni yang Terdaftar</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Tambahkan kisah sukses siswa yang telah lulus dan bekerja di Jepang untuk ditampilkan di halaman depan web LPK.</p>
                    <button
                        type="button"
                        @click="openCreateTestimonialModal"
                        class="mt-2 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-japan-red text-white text-xs font-bold shadow-xs hover:bg-red-700 transition-colors cursor-pointer"
                    >
                        <Plus class="w-4 h-4" />
                        <span>+ Tambah Testimoni Baru</span>
                    </button>
                </div>
            </div>

            <!-- ================= TAB: GALERI FOTO KEGIATAN ================= -->
            <div v-if="activeTab === 'galleries'" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                            <span>📸</span> Galeri Foto Dokumentasi Kegiatan LPK
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Upload foto pelatihan kelas, tryout CBT, wawancara kerja, dan pelepasan siswa di bandara.</p>
                    </div>

                    <button
                        type="button"
                        @click="openCreateGalleryModal"
                        class="px-4 py-2 rounded-xl bg-japan-red hover:bg-red-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
                    >
                        <Plus class="w-4 h-4" />
                        <span>+ Upload Foto Baru</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="gal in galleries" :key="gal.id" class="p-4 rounded-3xl bg-slate-50 border border-slate-200 flex flex-col justify-between space-y-3">
                        <div class="space-y-3">
                            <div class="relative aspect-video rounded-2xl overflow-hidden bg-slate-200 border border-slate-300">
                                <img :src="gal.image_path" :alt="gal.title" class="w-full h-full object-cover" />
                                <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-950/80 text-white backdrop-blur-xs">
                                    {{ gal.category }}
                                </span>
                                <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded text-[10px] font-bold" :class="gal.is_published ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-slate-300'">
                                    {{ gal.is_published ? 'Publik' : 'Draft' }}
                                </span>
                            </div>

                            <h3 class="text-xs font-black text-slate-900 line-clamp-2">{{ gal.title }}</h3>
                            <p v-if="gal.description" class="text-[11px] text-slate-500 line-clamp-2">{{ gal.description }}</p>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-200 text-xs">
                            <span class="text-[10px] text-slate-400 font-mono">{{ gal.activity_date || '-' }}</span>
                            <div class="flex items-center gap-1.5">
                                <button @click="openEditGalleryModal(gal)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 cursor-pointer">
                                    Edit
                                </button>
                                <button @click="deleteGallery(gal)" class="px-2.5 py-1 rounded-lg bg-rose-50 text-japan-red hover:bg-rose-100 text-xs font-bold cursor-pointer">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 8: KONTAK & LOKASI ================= -->
            <div v-if="activeTab === 'contact'" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                            <span>📍</span> Kontak, Alamat Kantor &amp; Jam Operasional
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Informasi resmi yang terhubung dengan tombol kontak cepat WhatsApp dan info footer.</p>
                    </div>
                </div>

                <form @submit.prevent="saveGeneralSettings" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp Pendaftaran *</label>
                            <input type="text" v-model="generalForm.contact_whatsapp" placeholder="Contoh: 6281234567890 (awalan 62)" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold font-mono" />
                            <span class="text-[10px] text-slate-400 mt-1 block">Format nomor tanpa simbol: 6281234567890</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Teks Tampilan No. Telepon / WA</label>
                            <input type="text" v-model="generalForm.contact_phone" placeholder="Contoh: 0812-3456-7890" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Resmi Lembaga *</label>
                            <input type="email" v-model="generalForm.contact_email" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Operasional Pelayanan</label>
                            <input type="text" v-model="generalForm.contact_hours" placeholder="Contoh: Senin - Sabtu: 08.00 - 17.00 WIB" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Lengkap Kantor LPK *</label>
                        <textarea rows="2" v-model="generalForm.contact_address" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Tautan / Link Google Maps Lokasi</label>
                        <input type="text" v-model="generalForm.contact_maps_url" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs" />
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" :disabled="isSaving" class="px-6 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white font-black text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer">
                            <Save class="w-4 h-4" />
                            <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ================= TAB 9: MEDIA SOSIAL & FOOTER ================= -->
            <div v-if="activeTab === 'social'" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-950 flex items-center gap-2">
                            <span>🌐</span> Tautan Media Sosial &amp; Copyright Footer
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tautan profil akun media sosial resmi lembaga dan catatan hak cipta di footer.</p>
                    </div>
                </div>

                <form @submit.prevent="saveGeneralSettings" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Instagram URL</label>
                            <input type="text" v-model="generalForm.social_instagram" placeholder="https://instagram.com/..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">TikTok URL</label>
                            <input type="text" v-model="generalForm.social_tiktok" placeholder="https://tiktok.com/@..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Facebook URL</label>
                            <input type="text" v-model="generalForm.social_facebook" placeholder="https://facebook.com/..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">YouTube Channel URL</label>
                            <input type="text" v-model="generalForm.social_youtube" placeholder="https://youtube.com/..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Teks Hak Cipta (Footer Copyright)</label>
                        <input type="text" v-model="generalForm.footer_copyright" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold" />
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" :disabled="isSaving" class="px-6 py-2.5 rounded-xl bg-japan-red hover:bg-red-700 text-white font-black text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer">
                            <Save class="w-4 h-4" />
                            <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- ================= MODAL PROGRAM (CREATE / EDIT) ================= -->
        <div v-if="showProgramModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-4 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-black text-slate-900">{{ isEditingProgram ? 'Edit Detail Program Pelatihan' : 'Tambah Program Pelatihan Baru' }}</h3>
                    <button @click="showProgramModal = false" class="text-slate-400 hover:text-slate-700 font-bold">✕</button>
                </div>

                <form @submit.prevent="submitProgram" class="space-y-4">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Program *</label>
                            <input type="text" v-model="programForm.name" required placeholder="Contoh: Tokutei Ginou (SSW)" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ikon (Emoji)</label>
                            <input type="text" v-model="programForm.icon" placeholder="💼" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-center" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Singkat</label>
                        <textarea rows="2" v-model="programForm.description" placeholder="Deskripsi mengenai program pelatihan dan penempatan kerja..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Badge Label</label>
                            <input type="text" v-model="programForm.badge_label" placeholder="Contoh: Program Unggulan" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kisaran Gaji / Saku</label>
                            <input type="text" v-model="programForm.salary_range" placeholder="180.000 - 250.000 JPY" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-mono" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="prog_active" v-model="programForm.is_active" class="rounded text-japan-red focus:ring-japan-red" />
                        <label for="prog_active" class="text-xs font-bold text-slate-700">Tampilkan Program di Halaman Depan</label>
                    </div>

                    <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                        <button type="button" @click="showProgramModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-japan-red text-white text-xs font-black">Simpan Program</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL SEKTOR (CREATE / EDIT) ================= -->
        <div v-if="showSectorModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-black text-slate-900">{{ isEditingSector ? 'Edit Sektor Pekerjaan' : 'Tambah Sektor Pekerjaan Baru' }}</h3>
                    <button @click="showSectorModal = false" class="text-slate-400 hover:text-slate-700 font-bold">✕</button>
                </div>

                <form @submit.prevent="submitSector" class="space-y-4">
                    <div class="grid grid-cols-4 gap-3">
                        <div class="col-span-3">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Sektor (ID) *</label>
                            <input type="text" v-model="sectorForm.name" required placeholder="Contoh: Keperawatan (Kaigo)" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ikon</label>
                            <input type="text" v-model="sectorForm.icon" placeholder="🏥" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-center" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Bahasa Jepang (Kanji)</label>
                        <input type="text" v-model="sectorForm.name_jp" placeholder="介護" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-jp" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="sec_active" v-model="sectorForm.is_active" class="rounded text-japan-red focus:ring-japan-red" />
                        <label for="sec_active" class="text-xs font-bold text-slate-700">Aktif &amp; Tampil di Web Depan</label>
                    </div>

                    <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                        <button type="button" @click="showSectorModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-japan-red text-white text-xs font-black">Simpan Sektor</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL TESTIMONIAL ================= -->
        <div v-if="showTestiModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-4 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-black text-slate-900">{{ isEditingTesti ? 'Edit Testimoni' : 'Tambah Testimoni Baru' }}</h3>
                    <button @click="showTestiModal = false" class="text-slate-400 hover:text-slate-700 font-bold">✕</button>
                </div>

                <form @submit.prevent="submitTestimonial" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Siswa *</label>
                            <input type="text" v-model="testiForm.name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Penempatan di Jepang *</label>
                            <input type="text" v-model="testiForm.japan_location" placeholder="Contoh: Tokyo, Jepang" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Bidang / Sektor Kerja *</label>
                            <input type="text" v-model="testiForm.work_sector" placeholder="Contoh: Caregiver (Kaigo)" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Program</label>
                            <input type="text" v-model="testiForm.program_type" placeholder="Tokutei Ginou / Magang" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pesan / Kesan Testimoni *</label>
                        <textarea rows="3" v-model="testiForm.testimony_text" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="testi_pub" v-model="testiForm.is_published" class="rounded text-japan-red focus:ring-japan-red" />
                        <label for="testi_pub" class="text-xs font-bold text-slate-700">Publikasikan di Halaman Depan</label>
                    </div>

                    <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                        <button type="button" @click="showTestiModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-japan-red text-white text-xs font-black">Simpan Testimoni</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL GALLERY FOTO ================= -->
        <div v-if="showGalleryModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-4 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-black text-slate-900">{{ isEditingGallery ? 'Edit Foto Kegiatan' : 'Upload Foto Kegiatan Baru' }}</h3>
                    <button @click="showGalleryModal = false" class="text-slate-400 hover:text-slate-700 font-bold">✕</button>
                </div>

                <form @submit.prevent="submitGallery" class="space-y-4" enctype="multipart/form-data">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Judul / Nama Kegiatan *</label>
                        <input type="text" v-model="galleryForm.title" required placeholder="Contoh: Pelatihan Percakapan Bahasa Jepang di Lab" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Kegiatan *</label>
                            <select v-model="galleryForm.category" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                                <option value="pelatihan">📖 Pelatihan Bahasa & Budaya</option>
                                <option value="cbt">💻 Simulasi Tryout CBT</option>
                                <option value="mensetsu">🤝 Wawancara (Mensetsu)</option>
                                <option value="keberangkatan">🛫 Pelepasan & Bandara</option>
                                <option value="asrama">🏠 Kehidupan Asrama & Fisik</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kegiatan</label>
                            <input type="date" v-model="galleryForm.activity_date" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <!-- File Upload or URL -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Upload File Foto (JPG / PNG / WebP, Max 5MB)</label>
                            <input type="file" @change="handleGalleryFileUpload" accept="image/*" class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-japan-red file:text-white hover:file:bg-red-700" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 mb-1">Atau Gunakan URL Gambar Eksternal</label>
                            <input type="text" v-model="galleryForm.image_url" placeholder="https://..." class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Singkat</label>
                        <textarea rows="2" v-model="galleryForm.description" placeholder="Deskripsi singkat momen kegiatan..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="gal_pub" v-model="galleryForm.is_published" class="rounded text-japan-red focus:ring-japan-red" />
                        <label for="gal_pub" class="text-xs font-bold text-slate-700">Tampilkan Foto di Galeri Halaman Depan</label>
                    </div>

                    <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                        <button type="button" @click="showGalleryModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-japan-red text-white text-xs font-black">Simpan Foto Kegiatan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL HERO SLIDE ================= -->
        <div v-if="showHeroSlideModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto animate-fade-in">
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-4 shadow-2xl border border-slate-200 my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <span>🎠</span>
                            <span>{{ isEditingHeroSlide ? 'Edit Slide Hero Card' : 'Tambah Slide Hero Card Baru' }}</span>
                        </h3>
                        <p class="text-xs text-slate-500">Konfigurasi tampilan kartu lowongan yang berputar di hero section.</p>
                    </div>
                    <button @click="showHeroSlideModal = false" class="text-slate-400 hover:text-slate-700 font-bold p-1 cursor-pointer">✕</button>
                </div>

                <form @submit.prevent="submitHeroSlide" class="space-y-4">
                    <!-- Image Upload & Live Preview -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">Foto Banner Kartu Hero *</label>
                        <div class="relative h-44 w-full rounded-2xl overflow-hidden bg-slate-950 border border-slate-200 shadow-inner group">
                            <img
                                :src="heroSlidePreview || '/images/hero-japan.jpg'"
                                alt="Preview Slide"
                                class="w-full h-full object-cover"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex items-end p-3">
                                <span class="text-white text-[11px] font-bold">🖼️ Live Preview Foto Banner</span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                            <label class="flex-1 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-bold cursor-pointer transition-colors text-center flex items-center justify-center gap-2">
                                <span>📁 Pilih File Gambar Baru (JPG/PNG/WebP)</span>
                                <input type="file" @change="handleHeroSlideFileChange" accept="image/*" class="hidden" />
                            </label>
                        </div>

                        <!-- Quick Presets -->
                        <div class="flex items-center gap-1.5 flex-wrap pt-1 text-[11px]">
                            <span class="text-slate-500 text-[10px] font-bold">Pilihan Cepat Foto:</span>
                            <button
                                type="button"
                                @click="selectPresetImage('/images/hero-japan.jpg')"
                                class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-medium border border-slate-200 cursor-pointer"
                            >
                                🗻 Tokyo &amp; Fuji
                            </button>
                            <button
                                type="button"
                                @click="selectPresetImage('/images/hero-manufacturing.jpg')"
                                class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-medium border border-slate-200 cursor-pointer"
                            >
                                ⚙️ Manufaktur Aichi
                            </button>
                            <button
                                type="button"
                                @click="selectPresetImage('/images/hero-hospitality.jpg')"
                                class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-medium border border-slate-200 cursor-pointer"
                            >
                                🍱 Resto &amp; Hotel Kyoto
                            </button>
                        </div>
                    </div>

                    <!-- Judul Lowongan / Gelombang -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Judul Slide (Indonesia) *</label>
                            <input
                                type="text"
                                v-model="heroSlideForm.title"
                                required
                                placeholder="Contoh: Gelombang Angkatan Baru - Kaigo"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold focus:ring-japan-red focus:border-japan-red"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Judul Slide (Jepang)</label>
                            <input
                                type="text"
                                v-model="heroSlideForm.title_jp"
                                placeholder="例: 新期生募集 - 特定技能 介護職"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-jp focus:ring-japan-red focus:border-japan-red"
                            />
                        </div>
                    </div>

                    <!-- Badge Pojok Atas & Status Pendaftaran -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Badge Pojok Kiri Atas</label>
                            <input
                                type="text"
                                v-model="heroSlideForm.badge_top"
                                placeholder="Contoh: 98% JLPT N4 / JFT"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold focus:ring-japan-red focus:border-japan-red"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status Pendaftaran (Badge Hijau)</label>
                            <input
                                type="text"
                                v-model="heroSlideForm.status_label"
                                placeholder="Contoh: Status: Dibuka"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold focus:ring-japan-red focus:border-japan-red"
                            />
                        </div>
                    </div>

                    <!-- Standar Gaji (JPY & IDR) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                        <div>
                            <label class="block text-xs font-black text-slate-800 mb-1">💰 Standar Gaji Jepang (Yen)</label>
                            <input
                                type="text"
                                v-model="heroSlideForm.salary_jpy"
                                placeholder="Contoh: 180k - 250k JPY"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-black text-emerald-700 font-mono"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-800 mb-1">💵 Estimasi Rupiah</label>
                            <input
                                type="text"
                                v-model="heroSlideForm.salary_idr"
                                placeholder="Contoh: ± Rp 20 - 28 Juta/bln"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 font-medium"
                            />
                        </div>
                    </div>

                    <!-- Penempatan & Fasilitas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                        <div>
                            <label class="block text-xs font-black text-slate-800 mb-1">🛫 Wilayah Penempatan</label>
                            <input
                                type="text"
                                v-model="heroSlideForm.placement_location"
                                placeholder="Contoh: Tokyo, Osaka, Kanagawa"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-800 mb-1">🏢 Fasilitas yang Didapat</label>
                            <input
                                type="text"
                                v-model="heroSlideForm.facilities"
                                placeholder="Contoh: Asrama & BPJS Jepang"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-amber-700"
                            />
                        </div>
                    </div>

                    <!-- Tombol CTA & Urutan -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Teks Tombol Aksi (CTA)</label>
                            <input
                                type="text"
                                v-model="heroSlideForm.cta_text"
                                placeholder="Contoh: Daftar Angkatan Baru Sekarang →"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold focus:ring-japan-red focus:border-japan-red"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Urutan Tampil</label>
                            <input
                                type="number"
                                v-model="heroSlideForm.sort_order"
                                min="1"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold"
                            />
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input
                            type="checkbox"
                            id="hero_slide_active"
                            v-model="heroSlideForm.is_active"
                            class="rounded text-japan-red focus:ring-japan-red"
                        />
                        <label for="hero_slide_active" class="text-xs font-bold text-slate-700">
                            Aktifkan slide ini di putaran carousel halaman depan
                        </label>
                    </div>

                    <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                        <button
                            type="button"
                            @click="showHeroSlideModal = false"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="heroSlideForm.processing"
                            class="px-6 py-2.5 rounded-xl bg-japan-red text-white text-xs font-black shadow-md hover:bg-red-700 transition-colors flex items-center gap-2 cursor-pointer"
                        >
                            <Save class="w-4 h-4" />
                            <span>{{ heroSlideForm.processing ? 'Menyimpan...' : 'Simpan Slide Hero' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { confirmDialog } from '@/Utils/alert';
import { 
    Globe, 
    Save, 
    Plus, 
    ExternalLink 
} from 'lucide-vue-next';

const props = defineProps({
    initialTab: {
        type: String,
        default: 'hero'
    },
    settings: Object,
    testimonials: Array,
    galleries: Array,
    programs: Array,
    jobSectors: Array,
    pipelineStages: Array,
    heroSlides: Array,
});

const activeTab = ref(props.initialTab || 'hero');
const isSaving = ref(false);

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam) {
        activeTab.value = tabParam;
    }
});

watch(() => props.initialTab, (newTab) => {
    if (newTab) {
        activeTab.value = newTab;
    }
});

const tabs = [
    { id: 'hero', name: 'Identitas & Hero', icon: '🎌' },
    { id: 'hero_slides', name: 'Slider Hero Card', icon: '🎠' },
    { id: 'registration', name: 'Info Pendaftaran', icon: '📝' },
    { id: 'stats', name: 'Statistik Bar', icon: '📊' },
    { id: 'about', name: 'Tentang & Filosofi', icon: '📖' },
    { id: 'programs', name: 'Program Pelatihan', icon: '💼' },
    { id: 'sectors', name: 'Sektor Kerja', icon: '🏭' },
    { id: 'testimonials', name: 'Testimoni Alumni', icon: '💬' },
    { id: 'galleries', name: 'Galeri Foto Kegiatan', icon: '📸' },
    { id: 'contact', name: 'Kontak & Lokasi', icon: '📍' },
    { id: 'social', name: 'Media Sosial & Footer', icon: '🌐' },
];

// General Settings Form (Sync with initial settings)
const generalForm = reactive({ ...props.settings });

const saveGeneralSettings = () => {
    isSaving.value = true;
    router.post(route('admin.website-settings.update-general'), generalForm, {
        preserveScroll: true,
        onFinish: () => {
            isSaving.value = false;
        },
    });
};

// Modal Program State
const showProgramModal = ref(false);
const isEditingProgram = ref(false);
const programForm = reactive({
    id: null,
    name: '',
    description: '',
    badge_label: '',
    salary_range: '',
    icon: '💼',
    is_active: true,
});

const openCreateProgramModal = () => {
    isEditingProgram.value = false;
    programForm.id = null;
    programForm.name = '';
    programForm.description = '';
    programForm.badge_label = '';
    programForm.salary_range = '';
    programForm.icon = '💼';
    programForm.is_active = true;
    showProgramModal.value = true;
};

const openEditProgramModal = (prog) => {
    isEditingProgram.value = true;
    programForm.id = prog.id;
    programForm.name = prog.name;
    programForm.description = prog.description;
    programForm.badge_label = prog.badge_label;
    programForm.salary_range = prog.salary_range;
    programForm.icon = prog.icon || '💼';
    programForm.is_active = prog.is_active;
    showProgramModal.value = true;
};

const submitProgram = () => {
    if (isEditingProgram.value) {
        router.put(route('admin.website-settings.programs.update', programForm.id), programForm, {
            preserveScroll: true,
            onSuccess: () => {
                showProgramModal.value = false;
            },
        });
    } else {
        router.post(route('admin.website-settings.programs.store'), programForm, {
            preserveScroll: true,
            onSuccess: () => {
                showProgramModal.value = false;
            },
        });
    }
};

const deleteProgram = (prog) => {
    confirmDialog('Hapus Program Pelatihan?', `Yakin ingin menghapus program "${prog.name}"?`).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('admin.website-settings.programs.destroy', prog.id), {
                preserveScroll: true,
            });
        }
    });
};

// Modal Sector State
const showSectorModal = ref(false);
const isEditingSector = ref(false);
const sectorForm = reactive({
    id: null,
    name: '',
    name_jp: '',
    icon: '🏢',
    is_active: true,
});

const openCreateSectorModal = () => {
    isEditingSector.value = false;
    sectorForm.id = null;
    sectorForm.name = '';
    sectorForm.name_jp = '';
    sectorForm.icon = '🏢';
    sectorForm.is_active = true;
    showSectorModal.value = true;
};

const openEditSectorModal = (sec) => {
    isEditingSector.value = true;
    sectorForm.id = sec.id;
    sectorForm.name = sec.name;
    sectorForm.name_jp = sec.name_jp;
    sectorForm.icon = sec.icon || '🏢';
    sectorForm.is_active = sec.is_active;
    showSectorModal.value = true;
};

const submitSector = () => {
    if (isEditingSector.value) {
        router.put(route('admin.website-settings.job-sectors.update', sectorForm.id), sectorForm, {
            preserveScroll: true,
            onSuccess: () => {
                showSectorModal.value = false;
            },
        });
    } else {
        router.post(route('admin.website-settings.job-sectors.store'), sectorForm, {
            preserveScroll: true,
            onSuccess: () => {
                showSectorModal.value = false;
            },
        });
    }
};

const deleteSector = (sec) => {
    confirmDialog('Hapus Sektor Pekerjaan?', `Yakin ingin menghapus sektor "${sec.name}"?`).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('admin.website-settings.job-sectors.destroy', sec.id), {
                preserveScroll: true,
            });
        }
    });
};

// Modal Testimonial State
const showTestiModal = ref(false);
const isEditingTesti = ref(false);
const testiForm = reactive({
    id: null,
    name: '',
    work_sector: '',
    japan_location: '',
    testimony_text: '',
    program_type: 'Tokutei Ginou',
    is_published: true,
});

const openCreateTestimonialModal = () => {
    isEditingTesti.value = false;
    testiForm.id = null;
    testiForm.name = '';
    testiForm.work_sector = '';
    testiForm.japan_location = '';
    testiForm.testimony_text = '';
    testiForm.program_type = 'Tokutei Ginou';
    testiForm.is_published = true;
    showTestiModal.value = true;
};

const openEditTestimonialModal = (testi) => {
    isEditingTesti.value = true;
    testiForm.id = testi.id;
    testiForm.name = testi.name;
    testiForm.work_sector = testi.work_sector;
    testiForm.japan_location = testi.japan_location;
    testiForm.testimony_text = testi.testimony_text;
    testiForm.program_type = testi.program_type;
    testiForm.is_published = testi.is_published;
    showTestiModal.value = true;
};

const submitTestimonial = () => {
    if (isEditingTesti.value) {
        router.put(route('admin.website-settings.testimonials.update', testiForm.id), testiForm, {
            preserveScroll: true,
            onSuccess: () => {
                showTestiModal.value = false;
            },
        });
    } else {
        router.post(route('admin.website-settings.testimonials.store'), testiForm, {
            preserveScroll: true,
            onSuccess: () => {
                showTestiModal.value = false;
            },
        });
    }
};

const deleteTestimonial = (testi) => {
    confirmDialog('Hapus Testimoni?', `Yakin ingin menghapus testimoni dari "${testi.name}"?`).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('admin.website-settings.testimonials.destroy', testi.id), {
                preserveScroll: true,
            });
        }
    });
};

// Modal Gallery State
const showGalleryModal = ref(false);
const isEditingGallery = ref(false);
const galleryForm = useForm({
    id: null,
    title: '',
    category: 'pelatihan',
    image_file: null,
    image_url: '',
    description: '',
    activity_date: '',
    is_published: true,
});

const handleGalleryFileUpload = (e) => {
    if (e.target.files && e.target.files[0]) {
        galleryForm.image_file = e.target.files[0];
    }
};

const openCreateGalleryModal = () => {
    isEditingGallery.value = false;
    galleryForm.reset();
    galleryForm.id = null;
    galleryForm.category = 'pelatihan';
    galleryForm.is_published = true;
    showGalleryModal.value = true;
};

const openEditGalleryModal = (gal) => {
    isEditingGallery.value = true;
    galleryForm.id = gal.id;
    galleryForm.title = gal.title;
    galleryForm.category = gal.category;
    galleryForm.image_file = null;
    galleryForm.image_url = gal.image_path;
    galleryForm.description = gal.description || '';
    galleryForm.activity_date = gal.activity_date ? gal.activity_date.substring(0, 10) : '';
    galleryForm.is_published = gal.is_published;
    showGalleryModal.value = true;
};

const submitGallery = () => {
    if (isEditingGallery.value) {
        galleryForm.post(route('admin.website-settings.galleries.update', galleryForm.id), {
            preserveScroll: true,
            onSuccess: () => {
                showGalleryModal.value = false;
            },
        });
    } else {
        galleryForm.post(route('admin.website-settings.galleries.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showGalleryModal.value = false;
            },
        });
    }
};

const deleteGallery = (gal) => {
    confirmDialog('Hapus Foto Kegiatan?', `Yakin ingin menghapus foto "${gal.title}"?`).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('admin.website-settings.galleries.destroy', gal.id), {
                preserveScroll: true,
            });
        }
    });
};

// ================= HERO SLIDER MANAGEMENT =================
const showHeroSlideModal = ref(false);
const isEditingHeroSlide = ref(false);
const heroSlidePreview = ref(null);

const heroSlideForm = useForm({
    id: null,
    title: '',
    title_jp: '',
    badge_top_label: 'TINGKAT KELULUSAN',
    badge_top: '98% JLPT N4 / JFT',
    status_label: 'Status: Dibuka',
    status_label_jp: '募集状況: 受付中',
    image_file: null,
    image_url: '',
    salary_jpy: '180k - 250k JPY',
    salary_idr: '± Rp 20 - 28 Juta/bln',
    placement_location: 'Tokyo, Osaka, Kanagawa',
    placement_location_jp: '東京・大阪・神奈川',
    facilities: 'Asrama & BPJS Jepang',
    facilities_jp: '社員寮・社会保険完備',
    cta_text: 'Daftar Angkatan Baru Sekarang →',
    cta_text_jp: '新期生募集に申し込む →',
    cta_url: '',
    sort_order: 1,
    is_active: true,
});

const openCreateHeroSlideModal = () => {
    isEditingHeroSlide.value = false;
    heroSlidePreview.value = '/images/hero-japan.jpg';
    heroSlideForm.reset();
    heroSlideForm.id = null;
    heroSlideForm.image_file = null;
    heroSlideForm.image_url = '/images/hero-japan.jpg';
    heroSlideForm.sort_order = (props.heroSlides?.length || 0) + 1;
    heroSlideForm.is_active = true;
    showHeroSlideModal.value = true;
};

const openEditHeroSlideModal = (slide) => {
    isEditingHeroSlide.value = true;
    heroSlidePreview.value = slide.image_path;
    heroSlideForm.id = slide.id;
    heroSlideForm.title = slide.title;
    heroSlideForm.title_jp = slide.title_jp || '';
    heroSlideForm.badge_top_label = slide.badge_top_label || 'TINGKAT KELULUSAN';
    heroSlideForm.badge_top = slide.badge_top || '98% JLPT N4 / JFT';
    heroSlideForm.status_label = slide.status_label || 'Status: Dibuka';
    heroSlideForm.status_label_jp = slide.status_label_jp || '募集状況: 受付中';
    heroSlideForm.image_file = null;
    heroSlideForm.image_url = slide.image_path;
    heroSlideForm.salary_jpy = slide.salary_jpy || '180k - 250k JPY';
    heroSlideForm.salary_idr = slide.salary_idr || '± Rp 20 - 28 Juta/bln';
    heroSlideForm.placement_location = slide.placement_location || 'Tokyo, Osaka, Kanagawa';
    heroSlideForm.placement_location_jp = slide.placement_location_jp || '東京・大阪・神奈川';
    heroSlideForm.facilities = slide.facilities || 'Asrama & BPJS Jepang';
    heroSlideForm.facilities_jp = slide.facilities_jp || '社員寮・社会保険完備';
    heroSlideForm.cta_text = slide.cta_text || 'Daftar Angkatan Baru Sekarang →';
    heroSlideForm.cta_text_jp = slide.cta_text_jp || '新期生募集に申し込む →';
    heroSlideForm.cta_url = slide.cta_url || '';
    heroSlideForm.sort_order = slide.sort_order || 1;
    heroSlideForm.is_active = Boolean(slide.is_active);
    showHeroSlideModal.value = true;
};

const selectPresetImage = (path) => {
    heroSlideForm.image_file = null;
    heroSlideForm.image_url = path;
    heroSlidePreview.value = path;
};

const handleHeroSlideFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        heroSlideForm.image_file = file;
        heroSlidePreview.value = URL.createObjectURL(file);
    }
};

const submitHeroSlide = () => {
    if (isEditingHeroSlide.value) {
        heroSlideForm.post(route('admin.website-settings.hero-slides.update', heroSlideForm.id), {
            preserveScroll: true,
            onSuccess: () => {
                showHeroSlideModal.value = false;
            },
        });
    } else {
        heroSlideForm.post(route('admin.website-settings.hero-slides.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showHeroSlideModal.value = false;
            },
        });
    }
};

const deleteHeroSlide = (slide) => {
    confirmDialog('Hapus Slide Hero?', `Yakin ingin menghapus slide "${slide.title}"?`).then((res) => {
        if (res.isConfirmed) {
            router.delete(route('admin.website-settings.hero-slides.destroy', slide.id), {
                preserveScroll: true,
            });
        }
    });
};
</script>
