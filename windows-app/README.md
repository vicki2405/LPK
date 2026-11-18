# 🖥️ Aplikasi Desktop Windows MASAYUME LMS & CBT
### Untuk Sistem: `https://masayume.id`

Folder ini berisi file instalasi resmi Windows desktop untuk **LPK Masayume**.

---

## 📦 Berkas Utama

### 1. `Masayume-LMS-Setup.exe` (File Installer Resmi)
File instalasi mandiri (**Model 2: Setup Wizard**). Cukup bagikan 1 file ini ke siswa, sensei, atau staff LPK.
- **Ukuran File**: ~74 KB (sangat ringan dan cepat diunduh).
- **Fitur Wizard**:
  - Langkah 1: Sambutan selamat datang & deskripsi platform.
  - Langkah 2: Pemilihan folder instalasi (default: `%LOCALAPPDATA%\Programs\MasayumeLMS`).
  - Langkah 3: Opsi pembuatan shortcut Desktop & Start Menu.
  - Langkah 4: Proses instalasi dengan progress bar animasi.
  - Langkah 5: Selesai dengan opsi langsung menjalankan aplikasi.
- **Terintegrasi Windows**:
  - Terdaftar resmi di *Control Panel -> Programs and Features* / *Settings -> Installed Apps*.
  - Menyertakan program uninstaller resmi (`uninstall.exe`).

### 2. Berkas Pendukung Lainnya
- `Masayume.exe` : Program eksekusi utama yang sudah ter-compile.
- `app.ico` : Ikon resmi resolusi tinggi Masayume.
- `app.png` : Gambar ikon format PNG.
- `SetupWizard.cs` : Kode sumber C# untuk Setup Wizard installer.
- `Uninstall.cs` : Kode sumber C# untuk program Uninstaller.
- `Program.cs` : Kode sumber C# untuk aplikasi utama.
- `build-installer.ps1` : Script otomatis untuk meng-compile ulang installer kapan pun diperlukan.
- `electron/` : Source code proyek versi Electron jika ingin membuat packaging alternatif.
