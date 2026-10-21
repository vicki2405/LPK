# Master Plan: Sistem LMS & CBT Terpadu LPK Penyaluran Jepang

Dokumen ini merupakan spesifikasi teknis dan fungsional resmi (**Single Source of Truth**) untuk pengembangan Sistem LMS & CBT LPK Penyaluran Jepang. Dokumen ini menjadi pedoman mutlak seluruh alur kerja, arsitektur, hak akses, dan implementasi kode agar terarah dan konsisten.

---

## 1. Visi & Profil Sistem
Sistem ini dirancang khusus untuk **Lembaga Pelatihan Kerja (LPK)** yang berfokus pada pelatihan dan penyaluran tenaga kerja ke Jepang (Program *Tokutei Ginou* / SSW, Magang / *Ginou Jisshuusei*, dan *Gijinkoku*).

### Pilar Utama Sistem:
1. **Standarisasi Kelulusan Bahasa Jepang N4**: Memastikan siswa siap lulus ujian resmi JLPT N4 / JFT-Basic A2 melalui simulasi CBT presisi tinggi.
2. **Kedaulatan Sensei dalam Akademik**: Sensei memiliki kontrol penuh atas bank soal, kurikulum bab, dan penjadwalan ujian CBT sesuai progres kelas.
3. **Performa Tinggi (Zero-Latency CBT)**: Pengalaman ujian tanpa jeda (*0ms delay* antar soal), audio *choukai* instan, dan perlindungan data anti-terputus.
4. **Pipeline Penyaluran Transparan**: Pelacakan dokumen dan tahapan siswa dari pendaftaran hingga keberangkatan ke Jepang.

---

## 2. Matriks Hak Akses & Peran Pengguna (RBAC)

```
┌──────────────────────────────────────────────────────────────────────────────────────────┐
│                                  ROLE & ACCESS MATRIX                                    │
├─────────────────┬────────────────────────────────────────────────────────────────────────┤
│ Role            │ Tanggung Jawab & Wewenang Utama                                        │
├─────────────────┼────────────────────────────────────────────────────────────────────────┤
│ 1. Admin LPK    │ • MANAJEMEN AKUN PENGGUNA (User Management CRUD):                      │
│                 │   - Manajemen Akun Siswa (Biodata, Batch, Reset Password, Status)      │
│                 │   - Manajemen Akun Sensei (Keahlian Bahasa, Kelas Binaan, Kontak)      │
│                 │   - Manajemen Akun Administrator & Staf LPK                            │
│                 │ • PENGATURAN ROLE & HAK AKSES (Role & Permission Manager):             │
│                 │   - Matriks Hak Akses Visual Spatie (Perizinan Modul & Rute)           │
│                 │ • Manajemen Angkatan (Batch), Kelas & Penugasan Sensei                 │
│                 │ • Tracker Dokumen Penyaluran (MCU, Paspor, COE, Visa, Tiket Terbang)   │
│                 │ • Laporan Operasional & Ekspor Data Kelulusan                          │
├─────────────────┼────────────────────────────────────────────────────────────────────────┤
│ 2. Sensei       │ • MANAJEMEN NILAI & KETUNTASAN SISWA (Gradebook & Mastery Matrix):     │
│    (Instruktur) │   - Matriks Ketuntasan Belajar per Bab (Bab 1–50 Tuntas/Belum)         │
│                 │   - Buku Nilai Terpadu (Kuis Harian, Tryout CBT, Mensetsu, Sikap 5S)   │
│                 │   - Fitur Penugasan Remedi & Rekomendasi Siap Ujian Resmi N4           │
│                 │ • PEMBUATAN & MANAJEMEN MATERI (LMS Content Studio):                   │
│                 │   - Membuat Bab & Modul Pelajaran (Hiragana, Kanji, Bunpou, Dokkai)    │
│                 │   - Mengunggah Video Pembelajaran, Audio Pelafalan, PDF & Catatan      │
│                 │   - Menyusun Daftar Kosakata (Kotoba) & Flashcards per Bab             │
│                 │   - Mengatur Urutan Pembelajaran & Prasyarat Materi                    │
│                 │ • KONTROL PENUH CBT: Membuat Bank Soal & Paket Ujian                   │
│                 │ • Menentukan Jadwal Ujian/Kuis per Kelas & Batas Waktu                 │
│                 │ • Memantau Nilai Siswa & Analisis Kelemahan Materi (Weakness Heatmap)  │
│                 │ • Menilai & Review Video Jikoshoukai / Latihan Wawancara (Mensetsu)    │
├─────────────────┼────────────────────────────────────────────────────────────────────────┤
│ 3. Siswa        │ • AKSES & PEMBELAJARAN MATERI (Student Learning Hub):                  │
│    (Trainee)    │   - Membaca modul bab terstruktur (Teks, Furigana, Audio, & Video)     │
│                 │   - Latihan Flashcards Kosakata (Kotoba) & Urutan Goresan Kanji        │
│                 │   - Menandai Progres Belajar (Mark as Complete / Checklist Bab)        │
│                 │   - Mengunduh Bahan Bacaan & Lembar Kerja PDF dari Sensei              │
│                 │ • Mengerjakan Ujian/Kuis CBT yang ditugaskan Sensei                    │
│                 │ • Melihat hasil skor seketika & pembahasan soal (Kaisetsu)             │
│                 │ • Mengunggah video perkenalan diri (Jikoshoukai) & berkas dokumen      │
│                 │ • Memantau progres pipeline penyaluran menuju Jepang                   │
├─────────────────┼────────────────────────────────────────────────────────────────────────┤
│ 4. Mitra Jepang │ • (Fase Lanjutan) Melihat katalog kandidat siap wawancara (Rirekisho,   │
│    (Kumiai/AO)  │   nilai N4, & video Jikoshoukai)                                       │
└─────────────────┴────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Spesifikasi Engine CBT (Simulasi N4 & JFT-Basic)

### A. Format Struktur Ujian N4:
1. **Gengo Chishiki - Moji & Goi (Huruf & Kosakata)**
   - *Kanji Yomikata* (Membaca Kanji)
   - *Hyoki* (Menulis Kanji)
   - *Bunmyaku Kitei* (Konteks Kata)
   - *Iikae Ruii* (Persamaan Kata)
2. **Gengo Chishiki - Bunpou & Dokkai (Tata Bahasa & Membaca)**
   - *Bun no Bunpou* (Pola Kalimat & Partikel)
   - *Bun no Kousei* (Menyusun Kalimat Bintang `★`)
   - *Dokkai* (Wacana Pendek, Menengah, & Pencarian Informasi)
3. **Choukai (Mendengarkan / Audio)**
   - *Kadai Rikai* (Pemahaman Tugas)
   - *Point Rikai* (Pemahaman Poin Kunci)
   - *Hatsuwa Hyougen* (Ungkapan Situasi)
   - *Sokuji Outou* (Respon Cepat)

### B. Fitur & Aturan Teknis CBT:
- **Dukungan Furigana Native**: Menampilkan huruf kanji dengan furigana di atasnya menggunakan tag `<ruby>`:
  $$\text{例: } \text{日本語} \rightarrow \text{にほんご}$$
- **Zero-Latency Navigation**: Seluruh soal di-load di awal ujian (JSON terenkripsi/terstruktur ringan). Pindah antar soal berlangsung 0 detik di memori browser (Vue 3 State).
- **Anti-Cheat Audio Player**:
  - Audio hanya bisa diputar sesuai limit ujian (misal: 1x putar).
  - Tombol *seek/scrubbing* dinonaktifkan.
  - Visualizer audio (animasi gelombang suara modern).
- **Timer Presisi Server-Sync**: Waktu mundur berjalan otomatis per sesi ujian. Sistem otomatis melakukan *auto-submit* saat timer habis.
- **Optimistic Background Auto-Save + Offline Backup**:
  - Setiap klik opsi jawaban langsung tersimpan di *IndexedDB/LocalStorage* browser siswa.
  - Jawaban dikirim ke server di latar belakang secara asinkron tanpa memblokir interaksi siswa.
- **Scoring Engine**:
  - Perhitungan otomatis nilai per sesi (*Sectional Score*) dan nilai total.
  - Penentuan status: **合格 (LULUS)** atau **不合格 (TIDAK LULUS)** berdasarkan standar *passing grade* resmi.
- **Review & Pembahasan (Kaisetsu)**:
  - Siswa dapat melihat analisis jawaban benar/salah, penjelasan tata bahasa, dan arti kalimat setelah ujian selesai dan diizinkan oleh Sensei.

---

## 4. Spesifikasi Modul Pembuatan & Akses Materi Pembelajaran (LMS Content & Learning Hub)

### A. Fitur Sensei: LMS Content Studio (Pembuatan Materi)
1. **Hirarki Kurikulum Terstruktur**:
   - Level Kursus (N5, N4, Tokutei Ginou Kaigo/Gaishoku, dll.) $\rightarrow$ Bab/Pelajaran (Chou / Dai 1-50 Ka) $\rightarrow$ Sub-Materi (Kotoba, Bunpou, Dokkai, Kanji, Kaiwa).
2. **Rich Content Editor & Multimedia Uploader**:
   - Editor teks dengan dukungan **Furigana / Ruby**, tabel tata bahasa Jepang, dan *callout notes*.
   - Unggah Video Pembelajaran (Embed YouTube/Vimeo atau video internal MP4).
   - Unggah File Audio Pelafalan Kosakata (Pronunciation MP3).
   - Unggah Dokumen Pendukung (Handout PDF, Lembar Latihan Kanji).
3. **Penyusun Kosakata & Flashcards Interaktif (Kotoba Manager)**:
   - Sensei menginput tabel kosakata: Huruf Kanji, Hiragana/Katakana, Romaji, Arti Bahasa Indonesia, Jenis Kata (Kata Benda, Kerja, Sifat), dan Contoh Kalimat (*Reibun*).
   - Sistem otomatis mengonversinya menjadi kartu hafalan (*Flashcards*) digital untuk siswa.
4. **Pengaturan Akses Materi per Kelas**:
   - Sensei dapat mengatur status rilis materi (Draft, Terbit / Published, atau Terjadwal otomatis per pertemuan).

### B. Fitur Siswa: Student Learning Hub (Membaca & Mempelajari Materi)
1. **Interactive Course Viewer**:
   - Tampilan materi yang bersih (*zen-mode*), bebas distraksi, dengan navigasi daftar isi bab di samping.
   - Pemutar audio pelafalan kosakata yang dapat diklik langsung di samping tiap kata.
2. **Flashcards & Kanji Drill Mode**:
   - Mode belajar kosakata interaktif (klik kartu untuk membalik arti dan memutar pelafalan vokal).
   - Animasi urutan goresan kanji (*stroke order animation*).
3. **Progress Tracker & Checklist Bab**:
   - Tombol "Tandai Selesai" (*Mark as Complete*) di setiap bab/topik.
   - Bar persentase kemajuan belajar siswa per level (misal: *Progress Belajar N4: 65%*).
4. **Akses Download Bahan Ajar**:
   - Siswa dapat mengunduh materi PDF atau lembar tugas yang diunggah Sensei.

---

## 5. Spesifikasi Modul Manajemen Admin & Evaluasi Ketuntasan Siswa (Sensei Gradebook)

### A. Modul Admin: Manajemen Akun & Matriks Hak Akses
1. **Manajemen Akun Siswa & Sensei (`/admin/users`)**:
   - CRUD Akun Siswa: Biodata lengkap, penugasan angkatan (*Batch*), status aktif/alumni, reset sandi.
   - CRUD Akun Sensei: Profil instruktur, keahlian level bahasa (N2/N1), penugasan kelas yang diampu.
   - CRUD Akun Admin: Pengaturan staf operasional LPK.
2. **Matriks Role & Hak Akses Spatie (`/admin/roles-permissions`)**:
   - Tampilan matriks perizinan visual (centang izin modul CBT, Kurikulum, Dokumen, dan Pengguna).
   - Pengaturan hak akses dinamis dan pembuatan peran tambahan.

### B. Modul Sensei: Buku Nilai & Matriks Ketuntasan Siswa (`/sensei/grades`)
1. **Matriks Ketuntasan Belajar per Bab (Chapter Mastery Matrix)**:
   - Tabel visual interaktif: Siswa per Kelas vs Bab 1–50.
   - Indikator otomatis: 🟢 Tuntas (Lulus Kuis Bab $\ge 75$), 🟡 Sedang Berjalan, 🔴 Belum Tuntas / Butuh Remedi.
2. **Buku Nilai Terpadu (Comprehensive Gradebook)**:
   - Rekapitulasi nilai Kuis Bab, Nilai Simulasi CBT N4, dan input nilai manual Sensei (Pelafalan *Hatsuon*, Sikap *Aisatsu & 5S*, Latihan Wawancara *Mensetsu*).
3. **Intervensi Remedi & Rekomendasi Ujian Resmi**:
   - Tombol satu-klik untuk menugaskan remedi kuis bab tertentu bagi siswa yang belum tuntas.
   - Penanda kelayakan: *"Siap Ujian Resmi N4 / JFT"* atau *"Butuh Bimbingan Tambahan"*.

---

## 6. Arsitektur Teknis & Standar Performa

### A. Tech Stack:
- **Backend**: Laravel 12 (`^12.0`) dengan arsitektur MVC + Service/Action Pattern.
- **Frontend Web (Admin, Sensei, Siswa)**: Inertia.js + Vue 3 + Tailwind CSS + Lucide Icons.
- **Mobile Ready**: RESTful API (`/api/v1/...`) dengan autentikasi Laravel Sanctum untuk koneksi ke Flutter / PWA.
- **Database**: MySQL / MariaDB (Optimized Indexing & Relational Constraints).
- **State Management & Audio**: Pinia (Vue 3) + Howler.js (Audio Engine).
- **Cache & Performance**: Redis / File Cache untuk Bank Soal & Scoring Matrix.

### B. Standar Target Performa:
| Metrik | Target | Metode Implementasi |
| :--- | :--- | :--- |
| **First Page Load** | < 1.2 detik | Vite tree-shaking, code splitting, Gzip/Brotli |
| **Pindah Soal CBT** | **0 detik (Instant)** | In-Memory Client State (Vue 3 SPA) |
| **Putar Audio Choukai** | < 200 ms | Audio streaming terkompresi AAC/Opus 64-96kbps |
| **Beban Server Serentak**| Stabil 500+ siswa | Background queue, indexed queries, optimistic sync |
| **Ketahanan Koneksi** | 100% data aman | LocalStorage / IndexedDB fallback autosave |

---

## 7. Konsep Desain UI/UX: *Modern Japan Tech Minimalist*

### A. Palet Warna & Visual Token:
- **Primary / Slate**: `#0F172A` (Deep Navy Slate), `#1E293B` (Dark Navy)
- **Accent / Japan Heritage**: `#DC2626` (Torii Crimson Red), `#E11D48` (Rose Red)
- **Status Colors**:
  - Sukses / Terjawab: `#10B981` (Emerald)
  - Ragu-ragu / Flagged: `#F59E0B` (Amber)
  - Belum Terjawab: `#94A3B8` (Slate Light)
- **Backgrounds**: `#F8FAFC` (Off-white clean surface) & Dark Mode Ready.

### B. Tipografi:
- **Latin Font**: *Plus Jakarta Sans* / *Inter* (Sleek, modern, legible).
- **Japanese Font**: *Noto Sans JP* / *Zen Kaku Gothic New* (Rendering kanji tajam).

### C. Karakteristik Antarmuka:
- **Bento Grid Layout** pada Dashboard Sensei dan Siswa untuk menyajikan ringkasan progres secara modular dan modern.
- **Zen Focus Mode** pada halaman CBT: Layar penuh, sidebar tersembunyi, kontras tinggi untuk kenyamanan mata selama ujian 60+ menit.
- **Micro-Interactions**: Transisi halus pada kartu soal, indikator timer yang berubah warna saat waktu menipis, dan feedback instan saat memilih jawaban.

---

## 8. Roadmap Pengembangan Sistem (Phasing Plan)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        PHASED IMPLEMENTATION ROADMAP                        │
├─────────┬───────────────────────────────┬───────────────────────────────────┤
│ Fase    │ Fokus Modul                   │ Deliverables                      │
├─────────┼───────────────────────────────┼───────────────────────────────────┤
│ Fase 1  │ Fondasi Proyek & RBAC         │ • Inisialisasi Laravel 12 (^12.0) │
│         │                               │   + Inertia Vue 3 + Tailwind      │
│         │                               │ • Skema Database & Multi-Auth     │
│         │                               │   (Admin, Sensei, Siswa)          │
│         │                               │ • Base Layout & Theme Tokens      │
├─────────┼───────────────────────────────┼───────────────────────────────────┤
│ Fase 2  │ CBT Engine N4 & Audio Player  │ • Komponen Ujian Fullscreen       │
│         │ (Core Priority)               │ • Zero-delay Question Navigator   │
│         │                               │ • Audio Choukai Player            │
│         │                               │ • Furigana (<ruby>) Parser        │
│         │                               │ • Timer & Offline Auto-Save       │
│         │                               │ • Scoring & Passing Grade Calc    │
├─────────┼───────────────────────────────┼───────────────────────────────────┤
│ Fase 3  │ Sensei Portal & Bank Soal     │ • CRUD Bank Soal & Kategori Bab   │
│         │                               │ • Generator Paket Ujian N4/JFT    │
│         │                               │ • Jadwal Ujian per Kelas/Batch    │
│         │                               │ • Buku Nilai & Ketuntasan Siswa   │
│         │                               │ • Live Proctor & Weakness Heatmap │
├─────────┼───────────────────────────────┼───────────────────────────────────┤
│ Fase 4  │ Modul LMS Akademik & Materi   │ • Kurikulum Bab (N5, N4, SSW)     │
│         │                               │ • LMS Content Studio (Sensei)     │
│         │                               │ • Student Learning Hub (Siswa)    │
│         │                               │ • Kotoba Flashcards & Kanji Drill │
│         │                               │ • Jikoshoukai / Mensetsu Video    │
├─────────┼───────────────────────────────┼───────────────────────────────────┤
│ Fase 5  │ Admin Ops & Trainee Pipeline  │ • Manajemen Pengguna (CRUD Akun)  │
│         │                               │ • Role & Permission Manager       │
│         │                               │ • Dokumen Tracker (MCU, COE, Visa)│
│         │                               │ • Manajemen Batch Siswa           │
│         │                               │ • Ekspor Nilai & Rapor Kelulusan  │
├─────────┼───────────────────────────────┼───────────────────────────────────┤
│ Fase 6  │ Optimasi Performa & Mobile API│ • REST API Endpoints Lengkap      │
│         │                               │ • Redis Caching & PWA Support     │
└─────────┴───────────────────────────────┴───────────────────────────────────┘
```

---

## 9. Komitmen Kualitas Kode
1. **Tidak ada jalan pintas (No shortcuts)**: Setiap komponen dibangun modular, terstruktur rapi, dan mudah dikembangkan.
2. **Kesesuaian Rencana**: Setiap langkah pengembangan akan merujuk kembali ke dokumen ini.
3. **Clean Code**: Penamaan variabel ekspresif, komentar penjelasan pada logika kompleks, dan validasi data ketat.
