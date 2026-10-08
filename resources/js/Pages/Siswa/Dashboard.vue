<template>
    <Head title="Siswa Training Camp - Masayume" />

    <AuthenticatedLayout>
        <div class="space-y-8 animate-fade-in pb-12">
            <!-- ================= SISWA HERO BANNER (LEFT-ALIGNED & RESPONSIVE) ================= -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white p-4 sm:p-7 md:p-8 shadow-2xl border border-slate-800/80">
                <!-- Subtle Japanese Kanji Ambient Watermark -->
                <div class="absolute -right-6 -bottom-10 select-none pointer-events-none opacity-5 font-jp font-black text-9xl sm:text-[14rem] text-white">
                    夢
                </div>
                <div class="absolute -left-12 -top-12 w-56 h-56 bg-japan-red/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-stretch justify-between gap-5 sm:gap-8">
                    <!-- SISI KIRI: PASFOTO CV + IDENTITAS SISWA (SELALU RATA KIRI) -->
                    <div class="flex flex-row items-start gap-3.5 sm:gap-6 text-left w-full lg:w-auto">
                        <!-- PASFOTO FORMAL MODEL CV (3:4 DI SISI KIRI) -->
                        <Link 
                            :href="route('profile.edit')" 
                            class="relative group shrink-0 block" 
                            title="Klik untuk melihat atau mengganti Pasfoto CV"
                        >
                            <div class="w-24 h-32 sm:w-32 sm:h-43 md:w-36 md:h-48 rounded-xl sm:rounded-2xl overflow-hidden border-2 border-slate-700/80 group-hover:border-amber-400 bg-slate-900 shadow-xl transition-all duration-300 ring-2 sm:ring-4 ring-white/5 group-hover:ring-amber-400/20 group-hover:shadow-amber-500/10 flex flex-col items-center justify-center relative">
                                <img
                                    v-if="$page.props.auth.user.avatar_url"
                                    :src="$page.props.auth.user.avatar_url"
                                    alt="Pasfoto Formal CV Siswa"
                                    class="w-full h-full object-cover object-top transition-transform duration-300 group-hover:scale-105"
                                    @error="(e) => e.target.style.display = 'none'"
                                />
                                <div v-else class="w-full h-full bg-gradient-to-b from-slate-850 to-slate-900 text-slate-400 flex flex-col items-center justify-center p-2 text-center">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-slate-800 flex items-center justify-center mb-1 text-amber-400 font-black text-lg sm:text-xl">
                                        {{ $page.props.auth.user.name.charAt(0) }}
                                    </div>
                                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-300">Pasfoto CV</span>
                                    <span class="text-[8px] text-slate-500 mt-0.5">3:4</span>
                                </div>

                                <!-- Label Rirekisho Tag -->
                                <div class="absolute bottom-0 inset-x-0 bg-slate-950/85 backdrop-blur-xs py-0.5 sm:py-1 px-1 text-center border-t border-white/10">
                                    <span class="text-[8px] sm:text-[9px] font-bold text-amber-300 tracking-wider uppercase block">
                                        CV 3:4
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol Edit Quick Action -->
                            <div class="absolute -bottom-1.5 -right-1.5 sm:-bottom-2 sm:-right-2 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-md sm:rounded-lg bg-japan-red hover:bg-rose-700 text-white text-[9px] sm:text-[10px] font-bold flex items-center gap-1 shadow-lg ring-2 ring-slate-950 transition-all group-hover:scale-105">
                                <span>📷</span>
                                <span class="hidden sm:inline">Ganti</span>
                            </div>
                        </Link>

                        <!-- BIODATA & STATUS SISWA (RATA KIRI) -->
                        <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5 space-y-2 sm:space-y-3 text-left">
                            <div class="space-y-1">
                                <div class="flex items-center justify-start gap-1.5 sm:gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Kandidat Aktif
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-white/10 text-slate-300 border border-white/10">
                                        {{ batch?.name || 'Siswa Mandiri' }}
                                    </span>
                                </div>

                                <h1 class="text-base sm:text-2xl md:text-3xl font-black tracking-tight text-white leading-tight text-left">
                                    Konnichiwa, {{ $page.props.auth.user.name.split('(')[0].trim() }}
                                </h1>

                                <p class="text-[11px] sm:text-xs md:text-sm text-slate-400 font-medium text-left line-clamp-2 sm:line-clamp-none">
                                    LPK Masayume • Program Persiapan Kerja Jepang (N4 / A2)
                                </p>
                            </div>

                            <!-- Fast Navigation Buttons (Rata Kiri) -->
                            <div class="flex items-center justify-start gap-1.5 sm:gap-2 pt-0.5 flex-wrap text-left">
                                <Link 
                                    :href="route('profile.edit')"
                                    class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl bg-white/10 hover:bg-white/20 text-white text-[11px] sm:text-xs font-bold transition-all border border-white/10"
                                >
                                    <span>👤 Biodata CV</span>
                                </Link>
                                <Link 
                                    :href="route('siswa.flashcards.index')"
                                    class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-[11px] sm:text-xs font-bold transition-all border border-amber-500/30"
                                >
                                    <span>🃏 Flashcards Kotoba</span>
                                </Link>
                                <Link 
                                    :href="route('siswa.kanjis.index')"
                                    class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 text-[11px] sm:text-xs font-bold transition-all border border-indigo-500/30"
                                >
                                    <span>🈸 Kartu Kanji</span>
                                </Link>
                                <Link 
                                    :href="route('siswa.exams.index')"
                                    class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl bg-japan-red/20 hover:bg-japan-red/30 text-rose-300 text-[11px] sm:text-xs font-bold transition-all border border-japan-red/30"
                                >
                                    <span>🎯 Ujian CBT N4</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- SISI KANAN: WIDGET KESIAPAN UJIAN N4 (RATA KIRI DI MOBILE) -->
                    <div class="w-full lg:w-auto self-stretch lg:self-center pt-2 sm:pt-0">
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 p-3 sm:p-5 rounded-xl sm:rounded-2xl flex items-center justify-start lg:justify-between gap-3.5 sm:gap-6 shadow-inner text-left">
                            <div class="relative w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center shrink-0">
                                <svg class="w-12 h-12 sm:w-16 sm:h-16 transform -rotate-90">
                                    <circle cx="32" cy="32" r="26" stroke="currentColor" stroke-width="5" class="text-white/10" fill="transparent" />
                                    <circle cx="32" cy="32" r="26" stroke="currentColor" stroke-width="5" class="text-amber-400" stroke-linecap="round" fill="transparent"
                                        :stroke-dasharray="2 * Math.PI * 26" :stroke-dashoffset="2 * Math.PI * 26 * (1 - (readiness.percentage / 100))" />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="font-black text-xs sm:text-base text-white">{{ readiness.percentage }}%</span>
                                </div>
                            </div>
                            <div class="space-y-0.5 text-left">
                                <span class="text-[9px] sm:text-[10px] text-slate-400 font-bold uppercase tracking-wider block">
                                    Kesiapan Ujian N4
                                </span>
                                <h4 class="text-xs sm:text-sm font-black text-amber-300">
                                    {{ readiness.status_label || 'Aktif Belajar' }}
                                </h4>
                                <p class="text-[10px] sm:text-[11px] text-slate-400">
                                    Penyaluran Wawancara Kerja
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= 3-PILLAR BENTO GRID LEARNING HUB ================= -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
                <!-- 1. KURIKULUM MINNA NO NIHONGO (LMS) -->
                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                                    <BookOpen class="w-4 h-4" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-950">Kurikulum LMS</h3>
                                    <p class="text-[11px] text-slate-500 font-medium">Minna no Nihongo</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-black px-2.5 py-1 rounded-full bg-blue-50 text-blue-800 border border-blue-100 shrink-0">
                                Bab {{ currentChapter?.chapter_number || 1 }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div v-if="currentChapter" class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 mb-4 space-y-2">
                            <span class="text-[10px] font-black text-japan-red uppercase tracking-wider block font-jp">
                                Bab {{ currentChapter.chapter_number }} (第{{ currentChapter.chapter_number }}課)
                            </span>
                            <h4 class="font-black text-xs sm:text-sm text-slate-900 line-clamp-1">
                                {{ currentChapter.title }}
                            </h4>
                            <p class="text-[11px] text-slate-600 font-medium leading-relaxed line-clamp-2">
                                {{ currentChapter.description || 'Pola tata bahasa resmi, kosakata kerja, dan percakapan Minna no Nihongo.' }}
                            </p>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 font-semibold pt-1 border-t border-slate-200/60">
                                <span>📚 {{ currentChapter.vocabularies?.length || 0 }} Kosakata</span>
                                <span class="text-emerald-700 font-bold">Tersedia</span>
                            </div>
                        </div>

                        <div v-else class="p-6 rounded-2xl bg-slate-50 border border-dashed border-slate-200/80 mb-4 text-center">
                            <BookOpen class="w-8 h-8 text-slate-300 mx-auto mb-1.5" />
                            <h4 class="text-xs font-bold text-slate-700">Materi Disiapkan</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Materi kurikulum sedang disusun Sensei.</p>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2">
                        <Link 
                            :href="route('siswa.lms.index')"
                            class="w-full py-2.5 sm:py-3 rounded-xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-sm hover:shadow flex items-center justify-center gap-1.5 cursor-pointer"
                        >
                            <BookOpen class="w-3.5 h-3.5 text-amber-400" />
                            <span>📖 Buka Modul Kurikulum</span>
                        </Link>
                        <Link 
                            :href="route('siswa.lms.index')"
                            class="w-full py-2 rounded-xl text-center block text-xs font-black text-slate-600 hover:text-slate-950 transition-colors"
                        >
                            Eksplorasi Bab 1 - 50 →
                        </Link>
                    </div>
                </div>

                <!-- 2. PUSAT HAFALAN KOTOBA -->
                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                                    <Layers class="w-4 h-4" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-950">Pusat Kotoba</h3>
                                    <p class="text-[11px] text-slate-500 font-medium">Flashcard & Kuis Kilat</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-black px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 shrink-0">
                                Gym Interaktif
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 mb-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black text-amber-800 uppercase tracking-wider block">
                                    Target Hafalan Hari Ini
                                </span>
                                <span class="text-[10px] font-bold text-slate-500">
                                    Audio Tokyo 🔊
                                </span>
                            </div>
                            <h4 class="font-black text-xs sm:text-sm text-slate-900">
                                {{ todayVocabularies?.length || 0 }} Kartu Terpilih
                            </h4>
                            <p class="text-[11px] text-slate-600 font-medium leading-relaxed line-clamp-2">
                                Kartu 3D interaktif pelafalan asli Jepang, contoh kalimat, dan simulasi kuis kilat cepat.
                            </p>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 font-semibold pt-1 border-t border-slate-200/60">
                                <span>⚡ Mode Kuis Kilat</span>
                                <span class="text-amber-700 font-bold">Latihan Mandiri</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2">
                        <Link 
                            :href="route('siswa.flashcards.index')"
                            class="w-full py-2.5 sm:py-3 rounded-xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-sm hover:shadow flex items-center justify-center gap-1.5 cursor-pointer"
                        >
                            <Layers class="w-3.5 h-3.5 text-amber-400" />
                            <span>🃏 Buka Flashcard Gym & Kuis</span>
                        </Link>
                        <Link 
                            :href="route('siswa.flashcards.index')"
                            class="w-full py-2 rounded-xl text-center block text-xs font-black text-slate-600 hover:text-slate-950 transition-colors"
                        >
                            Mulai Sesi Hafalan Harian →
                        </Link>
                    </div>
                </div>

                <!-- 3. TUGAS UJIAN CBT AKTIF DARI SENSEI -->
                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-japan-red shrink-0">
                                    <Clock class="w-4 h-4" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-950">Tugas Ujian CBT</h3>
                                    <p class="text-[11px] text-slate-500 font-medium">Simulasi Resmi Sensei</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-black px-2.5 py-1 rounded-full bg-rose-50 text-japan-red border border-rose-100 shrink-0">
                                {{ assignedExams?.length || 0 }} Siap
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div v-if="assignedExams && assignedExams.length > 0" class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 mb-4 space-y-2.5">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-japan-red/10 text-japan-red">
                                    {{ assignedExams[0].subject?.name || 'Ujian N4' }}
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                    KKM: {{ assignedExams[0].passing_score }}%
                                </span>
                            </div>
                            <h4 class="font-black text-xs sm:text-sm text-slate-900 line-clamp-2 leading-snug">
                                {{ assignedExams[0].title }}
                            </h4>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 font-semibold pt-1 border-t border-slate-200/60">
                                <span>⏱️ {{ assignedExams[0].duration_minutes }} Menit</span>
                                <span>📝 {{ assignedExams[0].questions_count || 0 }} Butir Soal</span>
                            </div>
                            <p v-if="assignedExams.length > 1" class="text-[10px] text-slate-500 text-center font-bold pt-0.5">
                                + {{ assignedExams.length - 1 }} paket ujian lainnya tersedia
                            </p>
                        </div>

                        <div v-else class="p-6 rounded-2xl bg-slate-50 border border-dashed border-slate-200/80 mb-4 text-center">
                            <Clock class="w-8 h-8 text-slate-300 mx-auto mb-1.5" />
                            <h4 class="text-xs font-bold text-slate-700">Belum Ada Ujian Baru</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Semua penugasan ujian CBT telah selesai.</p>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2">
                        <button 
                            v-if="assignedExams && assignedExams.length > 0"
                            type="button"
                            @click="startExam(assignedExams[0])"
                            class="w-full py-2.5 sm:py-3 rounded-xl bg-japan-red hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-sm hover:shadow flex items-center justify-center gap-1.5 cursor-pointer"
                        >
                            <span>🚀 Kerjakan CBT Sekarang</span>
                        </button>
                        <Link 
                            :href="route('siswa.exams.index')"
                            class="w-full py-2 rounded-xl text-center block text-xs font-black text-slate-600 hover:text-japan-red transition-colors"
                        >
                            Lihat Semua Jadwal Ujian →
                        </Link>
                    </div>
                </div>
            </div>

            <!-- ================= PERSONAL CBT EXAM GRADEBOOK ================= -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-950 flex items-center gap-2">
                        <Award class="w-5 h-5 text-emerald-600" />
                        Rekam Jejak & Rapor Nilai CBT Anda
                    </h3>
                    <span class="text-xs font-bold text-slate-500">
                        {{ recentSessions?.length || 0 }} Kali Ujian
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3 px-4">Nama Paket CBT</th>
                                <th class="py-3 px-4">Waktu Ujian</th>
                                <th class="py-3 px-4">Skor Diperoleh</th>
                                <th class="py-3 px-4">Integritas Ujian</th>
                                <th class="py-3 px-4">Hasil Kelulusan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="session in recentSessions" :key="session.id" class="hover:bg-slate-50/60">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ session.exam?.title }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500">
                                    {{ session.created_at ? new Date(session.created_at).toLocaleDateString('id-ID') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ session.total_score || 0 }} / {{ session.exam?.max_score || 180 }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span v-if="session.is_disqualified" class="px-2 py-0.5 rounded-md font-bold text-[11px] bg-rose-100 text-rose-800 border border-rose-200">
                                        ⛔ Didiskualifikasi
                                    </span>
                                    <span v-else-if="session.violation_count > 0" class="px-2 py-0.5 rounded-md font-bold text-[11px] bg-amber-100 text-amber-800 border border-amber-200">
                                        ⚠️ {{ session.violation_count }}x Pindah Tab
                                    </span>
                                    <span v-else class="px-2 py-0.5 rounded-md font-bold text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        🟢 Tertib (Jujur)
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-xs"
                                        :class="session.is_passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                                        {{ session.is_passed ? '合格 Lulus Passing Grade' : '不合格 Belum Lulus' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!recentSessions || recentSessions.length === 0">
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Anda belum memiliki riwayat ujian CBT. Klik tombol "Mulai Ujian Fullscreen" di atas untuk mengerjakan tugas ujian dari Sensei.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ================= TRAINEE JOURNEY MILESTONES (#dokumen) ================= -->
            <div id="dokumen" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
                    <h3 class="text-base font-black text-slate-950 flex items-center gap-2">
                        <CheckCircle2 class="w-5 h-5 text-emerald-600" />
                        Status Dokumen & 7 Tahapan Penyaluran Anda ke Jepang
                    </h3>
                    <Link :href="route('siswa.journey.index')" class="text-xs font-bold text-japan-red hover:underline">
                        Lihat Rincian Perjalanan →
                    </Link>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                    <div v-for="(stage, idx) in formattedPipelineStages" :key="stage.code"
                        class="p-3.5 rounded-2xl text-center transition-all relative"
                        :class="[
                            stage.isCompleted ? 'bg-emerald-50 border border-emerald-200' :
                            stage.isCurrent ? 'bg-amber-50 border-2 border-amber-400 shadow-md ring-2 ring-amber-400/30' :
                            'bg-slate-50 border border-slate-200 opacity-75'
                        ]"
                    >
                        <div class="mb-1.5 flex justify-center">
                            <CheckCircle2 v-if="stage.isCompleted" class="w-5 h-5 text-emerald-600" />
                            <Clock v-else-if="stage.isCurrent" class="w-5 h-5 text-amber-600 animate-spin" />
                            <component v-else :is="stage.iconComponent" class="w-5 h-5 text-slate-400" />
                        </div>
                        <span class="text-xs font-black block" :class="stage.isCompleted ? 'text-emerald-950' : stage.isCurrent ? 'text-amber-950' : 'text-slate-700'">
                            {{ stage.name }}
                        </span>
                        <span class="text-[10px] font-bold block mt-0.5" :class="stage.isCompleted ? 'text-emerald-800' : stage.isCurrent ? 'text-amber-800' : 'text-slate-500'">
                            {{ stage.statusLabel }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { confirmDialog } from '@/Utils/alert';
import { 
    Clock, BookOpen, Layers, CheckCircle2, 
    Award, Users, Activity, FileText, PlaneTakeoff 
} from 'lucide-vue-next';

const page = usePage();

const props = defineProps({
    readiness: Object,
    batch: Object,
    currentChapter: Object,
    assignedExams: Array,
    todayVocabularies: Array,
    recentSessions: Array,
    pipelineStages: Array,
});

// 7 Milestone Penyaluran Dinamis Siswa
const defaultStageList = [
    { code: 'pelatihan', name: 'Pelatihan N4', icon: BookOpen },
    { code: 'lulus_n4', name: 'Lulus N4/JFT', icon: Award },
    { code: 'matching', name: 'Matching User', icon: Users },
    { code: 'mcu', name: 'MCU Fit', icon: Activity },
    { code: 'coe', name: 'COE Imigrasi', icon: FileText },
    { code: 'visa', name: 'Visa Kerja', icon: CheckCircle2 },
    { code: 'terbang', name: 'Terbang ✈️', icon: PlaneTakeoff },
];

const formattedPipelineStages = computed(() => {
    const userStageCode = page.props.auth?.user?.pipeline_stage || 'pelatihan';
    const stageCodes = defaultStageList.map(s => s.code);
    const userStageIndex = stageCodes.indexOf(userStageCode);
    const activeIdx = userStageIndex !== -1 ? userStageIndex : 0;

    return defaultStageList.map((stage, index) => {
        const isCompleted = index < activeIdx;
        const isCurrent = index === activeIdx;
        const isUpcoming = index > activeIdx;
        
        let statusLabel = 'Target';
        if (isCompleted) statusLabel = 'Tuntas ✅';
        else if (isCurrent) statusLabel = 'Sedang Berjalan ⚡';
        else statusLabel = 'Tahap Berikutnya';

        return {
            ...stage,
            iconComponent: stage.icon,
            isCompleted,
            isCurrent,
            isUpcoming,
            statusLabel,
        };
    });
});

const startExam = (exam) => {
    confirmDialog(
        `Mulai Ujian CBT: ${exam.title}?`,
        `⏱️ Durasi: ${exam.duration_minutes} Menit.\n🖥️ Layar akan otomatis masuk ke MODE FULLSCREEN.\n⚠️ PERINGATAN: Dilarang berpindah tab browser, membuka AI, atau meminimalkan HP.`
    ).then((result) => {
        if (result.isConfirmed) {
            router.post(route('siswa.cbt.start', exam.id));
        }
    });
};
</script>
