# MASTER DESIGN SYSTEM: LMS & CBT LPK PENYALURAN JEPANG
**Aesthetic Direction:** Modern Japan Tech Minimalist  
**Stack:** Laravel 12 + Inertia.js + Vue 3 + Tailwind CSS + Lucide Icons (Web) | Flutter (Mobile)  
**Single Source of Truth:** `MASTER_PLAN_LMS_LPK.md` & `AGENTS.md`  

---

## 1. Filosofi & Karakteristik Desain

1. **Modern Japan Tech Minimalist**:
   - Terinspirasi dari estetika modern Jepang: tenang (*shizuka*), teratur (*chitsujo*), presisi, dan fungsional.
   - Pemanfaatan *white space* (ruang negatif) yang proporsional untuk mereduksi kelelahan kognitif (*cognitive load*) siswa selama belajar berjam-jam.
2. **Zero Redundansi (SOP AGENTS.md)**:
   - Dilarang membuat tombol atau elemen dengan aksi/fungsi ganda dalam satu layar atau modal.
   - Setiap elemen interaktif memiliki tujuan tunggal yang jelas (*single clear intent*).
3. **Declutter & Bebas Visual Noise**:
   - Menghilangkan badge, banner, atau dekorasi yang tidak esensial.
   - Informasi disajikan bertingkat (*progressive disclosure*).
4. **Zero-Latency Feel**:
   - Transisi antarmuka secepat kilat (150–200ms ease-out). Khusus CBT: pergantian soal **0ms (instant)** di memori Vue state.

---

## 2. Palet Warna & Visual Tokens

| Token Semantik | Hex Code | Tailwind Equivalent | Penggunaan Utama |
| :--- | :--- | :--- | :--- |
| `--surface-canvas` | `#F8FAFC` | `bg-slate-50` | Background utama aplikasi (soft off-white) |
| `--surface-card` | `#FFFFFF` | `bg-white` | Kontainer card, modal, bento grid cell |
| `--surface-subtle` | `#F1F5F9` | `bg-slate-100` | Background opsi, header tabel, chip |
| `--text-primary` | `#0F172A` | `text-slate-900` | Heading, teks soal, nilai utama |
| `--text-secondary` | `#475569` | `text-slate-600` | Body text, sub-heading, metadata |
| `--text-muted` | `#94A3B8` | `text-slate-400` | Placeholder, furigana rt, status belum dijawab |
| `--border-subtle` | `#E2E8F0` | `border-slate-200` | Divider card, border tabel, input outline |
| `--brand-primary` | `#0F172A` | `bg-slate-900` | Navigasi utama, tombol utama institusi |
| `--brand-navy` | `#1E293B` | `bg-slate-800` | Sidebar Sensei & Admin, badge batch |
| `--accent-torii` | `#DC2626` | `bg-red-600` | Aksen budaya Jepang (Torii Red), CTA utama, penanda penting |
| `--accent-rose` | `#E11D48` | `bg-rose-600` | Tombol submit, status darurat/remedi |
| `--status-success` | `#10B981` | `text-emerald-600 / bg-emerald-500` | CBT Terjawab, Lulus N4 (合格), Nilai Kuis $\ge 75$ |
| `--status-warning` | `#F59E0B` | `text-amber-600 / bg-amber-500` | CBT Ragu-ragu (Flagged), Sedang Berjalan |
| `--status-danger` | `#EF4444` | `text-red-600 / bg-red-500` | CBT Belum Dijawab, Tidak Lulus (不合格), Butuh Remedi |

---

## 3. Standar Tipografi Dwi-Bahasa (Bilingual Typography)

Sistem menggunakan font khusus untuk keterbacaan aksara Latin dan Kanji/Kana:

```css
/* Font Stacks */
--font-sans: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
--font-japanese: 'Noto Sans JP', 'Zen Kaku Gothic New', sans-serif;
```

### Aturan Rendering Furigana (`<ruby>` & `<rt>`):
1. Tag `<ruby>` wajib digunakan untuk kosakata ber-kanji pada modul materi & soal CBT.
2. Teks `<rt>` (bacaan kana) disetel dengan ukuran proporsional (55–60% dari kanji induk), font-weight medium, dan warna sedikit lebih lembut:
   ```html
   <ruby class="font-japanese font-semibold text-slate-900 text-lg">
     日本語
     <rt class="text-xs text-slate-500 font-normal">にほんご</rt>
   </ruby>
   ```
3. Line-height paragraf yang memuat Furigana harus minimal `leading-loose` (`line-height: 2` atau `2.2`) untuk mencegah furigana bertumpuk dengan baris di atasnya.

---

## 4. Layout Patterns

### A. Bento Grid System (Dashboard Sensei & Siswa)
- Layout modular dengan grid CSS 12 kolom (`grid grid-cols-12 gap-4 md:gap-6`).
- Setiap *card* bento menyajikan satu informasi utama:
  - **Tile 1 (Hero/Status)**: Sapaan siswa, progres kurikulum Bab 1–50, dan countdown ujian CBT terdekat.
  - **Tile 2 (Quick Actions)**: Flashcards Kotoba hari ini, lanjutkan bab terakhir.
  - **Tile 3 (Analisis Kelemahan)**: Heatmap kelemahan materi (Moji-Goi, Bunpou, Choukai).
  - **Tile 4 (Dokumen Pipeline)**: Status MCU, Paspor, COE, Visa.

### B. Zen Focus Mode (Engine CBT N4)
- **Layar Penuh (Distraction-Free)**:
  - Menghilangkan header navbar global dan sidebar menu.
  - Hanya menampilkan: Header Ujian Ringkas (Timer, Section, Tombol Selesai), Area Soal Bersih, dan Bar Navigasi Nomor Soal di bawah atau samping kanan.
- **Question Card**:
  - Ukuran font teks soal minimal `text-lg` (18px) hingga `text-xl` (20px).
  - Pilihan ganda (A, B, C, D) berupa kartu klik luas dengan padding `p-4` atau `p-5`, border `border-2`, dan efek transisi hover/active seketika tanpa jeda.
  - Active state: Border berwarna biru tua/torii red dengan centang ikonik yang jelas.
- **Audio Choukai Player Anti-Cheat**:
  - Player ringkas dengan visualizer waveform gelombang audio.
  - Tanpa slider seeking/scrubbing; hanya tombol Putar (dengan kuota putar, e.g., "Sisa 1x Putar").

---

## 5. Touch Target & Mobile Accessibility (WCAG 2.1 AA)

1. **Ukuran Target Sentuh (Touch Target)**:
   - Seluruh tombol, tombol nomor soal, dan opsi radio/checkbox wajib memiliki area sentuh minimal **44×44px** (`min-h-[44px] min-w-[44px]`).
   - Jarak antar tombol nomor soal CBT minimal `gap-2` (8px) untuk mencegah *accidental tap* di layar sentuh ponsel/tablet siswa.
2. **Kontras Warna**:
   - Semua teks utama memiliki rasio kontras $\ge 4.5:1$ terhadap background.
   - Status badge (hijau/kuning/merah) wajib menggunakan teks kontras tinggi (e.g., `bg-emerald-50 text-emerald-800 border-emerald-200`).
3. **Keyboard Navigation & Visible Focus**:
   - Setiap elemen yang dapat diklik memiliki `focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2`.
   - Shortcut keyboard untuk CBT: `1`/`2`/`3`/`4` atau `A`/`B`/`C`/`D` untuk memilih jawaban, `ArrowRight` untuk soal berikutnya, `ArrowLeft` untuk soal sebelumnya.

---

## 6. Pre-Delivery Checklist untuk Komponen Baru

Setiap pembuatan atau modifikasi antarmuka di proyek ini wajib mematuhi checklist berikut:
- [ ] **Tidak ada emoji** yang dijadikan ikon navigasi/tombol (wajib menggunakan Lucide SVG icons).
- [ ] **Zero Redundansi**: Tidak ada tombol duplikat (misal: tombol 'Simpan' di header dan 'Simpan' di footer modal).
- [ ] **Touch Target $\ge 44\text{px}$** pada seluruh elemen interaktif di mobile/tablet.
- [ ] **Furigana Spacing**: Paragraf teks Jepang memiliki `line-height` cukup agar `<rt>` tidak terpotong.
- [ ] **Hover/Press Feedback**: Semua kartu dan tombol memiliki state visual responsif (150–200ms).
- [ ] **Responsif**: Tampilan teruji pada breakpoint 375px (mobile), 768px (tablet), dan 1280px+ (desktop).
