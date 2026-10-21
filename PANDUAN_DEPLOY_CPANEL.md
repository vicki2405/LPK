# Panduan Lengkap Deploy Sistem LMS & CBT LPK ke cPanel

Paket file ZIP `lpk_cpanel_deploy.zip` telah disiapkan lengkap beserta dependensi `vendor/`, aset frontend `public/build/`, file database SQL, template `.env.cpanel`, dan script helper otomatis.

---

## 📦 Isi Paket Deployment
1. **Source Code & Vendor**: Aplikasi Laravel 11/12 + Inertia Vue 3 + folder `vendor/` siap pakai.
2. **Aset Frontend Terkompilasi**: `public/build/` (Vite, CSS, JS terkompilasi).
3. **Skema Database Lengkap**: `database_schema.sql` (30 tabel + master data + admin + seeder kurikulum).
4. **Template Konfigurasi**: `.env.cpanel` (dengan `APP_KEY` valid siap pakai).
5. **Script Inisialisasi 1-Klik**: `public/install_helper.php` (otomatis buat symlink storage & bersihkan cache).

---

## 🚀 3 Langkah Mudah Deploy di cPanel

### Langkah 1: Upload & Ekstrak File ZIP
1. Login ke cPanel, buka menu **File Manager**.
2. Masuk ke direktori web Anda:
   * **Jika Document Root domain Anda mengarah ke subfolder (Rekomendasi)**:
     Ekstrak di `/home/username/lpk`, lalu arahkan Document Root domain ke `/home/username/lpk/public`.
   * **Jika menggunakan `public_html` standar**:
     Ekstrak di luar `public_html` (misal `/home/username/lpk`), lalu pindahkan isi folder `/home/username/lpk/public/*` ke dalam `/home/username/public_html/`.
3. Salin (rename/copy) file `.env.cpanel` menjadi `.env`.

---

### Langkah 2: Buat Database & Import `database_schema.sql`
1. Buka menu **MySQL Databases** di cPanel:
   * Buat Database baru (contoh: `user_lms`).
   * Buat Pengguna MySQL baru & Password.
   * Tambahkan Pengguna ke Database dengan centang **ALL PRIVILEGES**.
2. Buka menu **phpMyAdmin**:
   * Pilih database `user_lms` yang baru dibuat.
   * Klik tab **Import** (Impor).
   * Pilih file `database_schema.sql` yang ada di hasil ekstrak, lalu klik tombol **Import** di bawah. Seluruh tabel dan data master akan langsung terisi.
3. Buka file `.env` di File Manager cPanel, edit bagian database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=user_lms
   DB_USERNAME=user_lmsuser
   DB_PASSWORD=password_database_anda
   APP_URL=https://domain-anda.com
   ```

---

### Langkah 3: Bersihkan & Optimalkan Cache
1. **Jika Memiliki Terminal cPanel**:
   Jalankan:
   ```bash
   php artisan optimize:clear
   php artisan optimize
   ```
   *(Catatan: Anda **TIDAK PERLU** menjalankan `php artisan storage:link` karena folder `public/storage` sudah berwujud folder fisik asli).*

2. **Atau Inisialisasi Lewat Browser (Tanpa Terminal)**:
   Akses URL helper:
   ```text
   https://domain-anda.com/install_helper.php?token=lpk_cpanel_2026
   ```
   Halaman helper akan otomatis membersihkan cache dan mengatur izin folder `storage/` dan `bootstrap/cache/`.
3. Selesai! Website siap digunakan.

---

### 🔑 Akun Default Setelah Import Database
* **Email Admin**: `admin@lpk.test` (atau akun admin yang sudah terdaftar di seeder)
* **Password**: `password`
*(Pastikan segera ubah password setelah login pertama kali)*

---

### ⚙️ Versi PHP yang Dibutuhkan
* Pastikan di cPanel (menu **Select PHP Version** / **MultiPHP Manager**) domain Anda menggunakan **PHP 8.2** atau **PHP 8.3**.
