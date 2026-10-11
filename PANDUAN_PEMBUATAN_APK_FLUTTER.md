# 📱 PANDUAN LENGKAP PEMBUATAN APLIKASI ANDROID (APK) DENGAN FLUTTER
## Arsitektur: Live Web-Shell (Realtime Cloud Sync) — LMS & CBT Masayume LPK

Dokumen ini adalah panduan teknis langkah demi langkah untuk membangun dan meng-compile aplikasi Android (**APK**) menggunakan framework **Flutter**. 

Arsitektur ini dirancang khusus agar:
> **Setiap kali Anda mengubah menu, fitur, atau memperbaiki bug di cloud hosting, aplikasi APK di HP seluruh siswa otomatis ter-update saat itu juga tanpa perlu siswa mengunduh ulang file APK.**

---

## 📑 DAFTAR ISI
1. [Konsep & Arsitektur Sistem](#1-konsep--arsitektur-sistem)
2. [Prasyarat & Kesiapan Server](#2-prasyarat--kesiapan-server)
3. [Inisialisasi Proyek Flutter](#3-inisialisasi-proyek-flutter)
4. [Konfigurasi Dependencies (pubspec.yaml)](#4-konfigurasi-dependencies-pubspecyaml)
5. [Konfigurasi Android Native (AndroidManifest.xml)](#5-konfigurasi-android-native-androidmanifestxml)
6. [Implementasi Kode Utama (lib/main.dart)](#6-implementasi-kode-utama-libmaindart)
7. [Pengaturan Ikon & Splash Screen](#7-pengaturan-ikon--splash-screen)
8. [Build & Export File APK Release](#8-build--export-file-apk-release)
9. [Tips Maintenance & Deployment Cloud](#9-tips-maintenance--deployment-cloud)

---

## 1. Konsep & Arsitektur Sistem

Aplikasi Android dibangun menggunakan model **Flutter InAppWebView Shell**:
```
+--------------------------------------------------------------+
|                  APLIKASI ANDROID SISWA (APK)                |
|                                                              |
|  +--------------------------------------------------------+  |
|  | Native App Wrapper (Flutter):                          |  |
|  | - Splash Screen Logo Masayume                         |  |
|  | - Android Hardware Back Button Controller              |  |
|  | - Native Audio/Microphone/Camera Permissions           |  |
|  | - Offline Detection & Retry Screen                     |  |
|  |                                                        |  |
|  |  +--------------------------------------------------+  |  |
|  |  | InAppWebView Engine (Chromium Embedded):         |  |  |
|  |  | Memuat: https://lms.masayume.co.id               |  |  |
|  |  | - Layout Mobile Responsive & Bottom Dock Swipe   |  |  |
|  |  | - Kotoba Audio Flashcards                        |  |  |
|  |  | - Kanji Quiz & CBT Anti-Cheat Fullscreen         |  |  |
|  |  +--------------------------------------------------+  |  |
|  +--------------------------------------------------------+  |
+--------------------------------------------------------------+
                               |
                   Internet (HTTPS Cloud Sync)
                               |
                               v
+--------------------------------------------------------------+
|             SERVER CLOUD HOSTING (Laravel + Vue 3)           |
|  - Saat dideploy: Kode terbaru langsung aktif detik itu juga |
+--------------------------------------------------------------+
```

### Mengapa Model Ini Paling Tepat?
1. **Instan Update**: Begitu `git push` dan deploy di server cloud selesai, semua siswa langsung menikmati tampilan dan menu terbaru saat membuka APK.
2. **Ukuran File Sangat Kecil**: Ukuran file APK hanya berkisar **12 MB – 18 MB** (sangat ringan untuk HP spesifikasi standar).
3. **Satu Basis Kode**: Anda tidak perlu memelihara dua kode berbeda (cukup rawat proyek Laravel).

---

## 2. Prasyarat & Kesiapan Server

Sebelum membuat APK, pastikan:
1. **Server Cloud Hosting**:
   - Web sudah memiliki domain resmi (contoh: `https://lms.masayume.co.id`).
   - Wajib menggunakan protokol aman **HTTPS** dengan sertifikat SSL aktif (Let's Encrypt / Cloudflare).
2. **Komputer Pengembang**:
   - Telah terinstall **Flutter SDK** versi 3.19 ke atas.
   - Telah terinstall **Android Studio** & **Android SDK** (Android 8.0 Oreo sampai Android 14+).
   - Java JDK 17.

---

## 3. Inisialisasi Proyek Flutter

Buka terminal/command prompt di komputer Anda dan jalankan perintah berikut:

```bash
# 1. Buat proyek Flutter baru dengan package name resmi LPK
flutter create --org id.masayume --project-name lms_masayume lms_masayume

# 2. Masuk ke direktori proyek
cd lms_masayume
```

---

## 4. Konfigurasi Dependencies (pubspec.yaml)

Buka file `pubspec.yaml` di dalam folder proyek Flutter, lalu sesuaikan bagian `dependencies` dan `dev_dependencies`:

```yaml
name: lms_masayume
description: "Aplikasi Resmi Pembelajaran Siswa LPK Masayume Training Center"
publish_to: 'none'
version: 1.0.0+1

environment:
  sdk: '>=3.2.0 <4.0.0'

dependencies:
  flutter:
    sdk: flutter
  # Komponen Inti Browser Engine
  flutter_inappwebview: ^6.0.0
  # Untuk membuka link eksternal (WhatsApp, PDF download luar, dll)
  url_launcher: ^6.2.5
  # Penanganan izin hardware (Audio, Kamera, Penyimpanan)
  permission_handler: ^11.3.0
  # Deteksi status koneksi internet siswa
  connectivity_plus: ^5.0.2

dev_dependencies:
  flutter_test:
    sdk: flutter
  flutter_lints: ^3.0.0
  # Otomatisasi Icon Launcher APK
  flutter_launcher_icons: ^0.13.1
  # Otomatisasi Native Splash Screen
  flutter_native_splash: ^2.4.0

flutter:
  uses-material-design: true
  assets:
    - assets/images/
```

Jalankan perintah untuk mengunduh package:
```bash
flutter pub get
```

---

## 5. Konfigurasi Android Native (AndroidManifest.xml)

Buka file `android/app/src/main/AndroidManifest.xml`, lalu perbarui izin dan konfigurasi hardware:

```xml
<manifest xmlns:android="http://schemas.android.com/apk/res/android"
    package="id.masayume.lms_masayume">

    <!-- Izin Internet & Jaringan -->
    <uses-permission android:name="android.permission.INTERNET"/>
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE"/>
    
    <!-- Izin Audio untuk Kotoba & Ujian CBT Listening -->
    <uses-permission android:name="android.permission.RECORD_AUDIO"/>
    <uses-permission android:name="android.permission.MODIFY_AUDIO_SETTINGS"/>

    <!-- Izin Kamera & Berkas untuk Upload Dokumen/Foto Siswa -->
    <uses-permission android:name="android.permission.CAMERA"/>
    <uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" android:maxSdkVersion="32"/>
    <uses-permission android:name="android.permission.READ_MEDIA_IMAGES"/>

    <application
        android:label="Masayume LPK"
        android:name="${applicationName}"
        android:icon="@mipmap/ic_launcher"
        android:usesCleartextTraffic="false"
        android:hardwareAccelerated="true">

        <activity
            android:name=".MainActivity"
            android:exported="true"
            android:launchMode="singleTop"
            android:theme="@style/LaunchTheme"
            android:configChanges="orientation|keyboardHidden|keyboard|screenSize|smallestScreenSize|locale|layoutDirection|fontScale|screenLayout|density|uiMode"
            android:hardwareAccelerated="true"
            android:windowSoftInputMode="adjustResize">
            
            <meta-data
              android:name="io.flutter.embedding.android.NormalTheme"
              android:resource="@style/NormalTheme"
              />
            <intent-filter>
                <action android:name="android.intent.action.MAIN"/>
                <category android:name="android.intent.category.LAUNCHER"/>
            </intent-filter>
        </activity>

        <meta-data
            android:name="flutterEmbedding"
            android:value="2" />
    </application>
</manifest>
```

---

## 6. Implementasi Kode Utama (lib/main.dart)

Ganti seluruh isi file `lib/main.dart` dengan kode lengkap berikut:

```dart
import 'dart:async';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_inappwebview/flutter_inappwebview.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:connectivity_plus/connectivity_plus.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Kunci status bar agar serasi dengan tema Masayume (Dark Navy / Torii Red)
  SystemChrome.setSystemUIOverlayStyle(
    const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
      systemNavigationBarColor: Colors.white,
      systemNavigationBarIconBrightness: Brightness.dark,
    ),
  );

  runApp(const MasayumeLmsApp());
}

class MasayumeLmsApp extends StatelessWidget {
  const MasayumeLmsApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Masayume LPK',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        fontFamily: 'Roboto',
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFFDC2626)),
        useMaterial3: true,
      ),
      home: const WebViewScreen(),
    );
  }
}

class WebViewScreen extends StatefulWidget {
  const WebViewScreen({super.key});

  @override
  State<WebViewScreen> createState() => _WebViewScreenState();
}

class _WebViewScreenState extends State<WebViewScreen> {
  // GANTI DENGAN DOMAIN CLOUD RESMI ANDA
  final String _initialUrl = "https://lms.masayume.co.id/siswa/dashboard";

  InAppWebViewController? _webViewController;
  PullToRefreshController? _pullToRefreshController;
  
  double _progress = 0;
  bool _isLoading = true;
  bool _isOffline = false;

  @override
  void initState() {
    super.initState();

    // Inisialisasi fitur Pull to Refresh (Geser ke bawah untuk segarkan)
    _pullToRefreshController = kIsWeb
        ? null
        : PullToRefreshController(
            settings: PullToRefreshSettings(
              color: const Color(0xFFDC2626),
            ),
            onRefresh: () async {
              if (defaultTargetPlatform == TargetPlatform.android) {
                _webViewController?.reload();
              } else if (defaultTargetPlatform == TargetPlatform.iOS) {
                _webViewController?.loadUrl(
                  urlRequest: URLRequest(url: await _webViewController?.getUrl()),
                );
              }
            },
          );

    _checkConnectivity();
  }

  Future<void> _checkConnectivity() async {
    final connectivityResult = await Connectivity().checkConnectivity();
    if (connectivityResult == ConnectivityResult.none) {
      setState(() => _isOffline = true);
    }
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvoked: (didPop) async {
        if (didPop) return;
        // Penanganan Tombol Back Fisik Android
        if (_webViewController != null && await _webViewController!.canGoBack()) {
          _webViewController!.goBack();
        } else {
          // Dialog Konfirmasi Keluar Aplikasi jika sudah di beranda
          _showExitDialog(context);
        }
      },
      child: Scaffold(
        backgroundColor: Colors.white,
        body: SafeArea(
          bottom: false,
          child: Stack(
            children: [
              if (!_isOffline)
                InAppWebView(
                  initialUrlRequest: URLRequest(url: WebUri(_initialUrl)),
                  pullToRefreshController: _pullToRefreshController,
                  initialSettings: InAppWebViewSettings(
                    useShouldOverrideUrlLoading: true,
                    mediaPlaybackRequiresUserGesture: false,
                    allowsInlineMediaPlayback: true,
                    useHybridComposition: true,
                    domStorageEnabled: true,
                    databaseEnabled: true,
                    cacheEnabled: true,
                    supportZoom: false,
                    verticalScrollBarEnabled: false,
                    horizontalScrollBarEnabled: false,
                    transparentBackground: false,
                  ),
                  onWebViewCreated: (controller) {
                    _webViewController = controller;
                  },
                  onLoadStart: (controller, url) {
                    setState(() {
                      _isLoading = true;
                      _isOffline = false;
                    });
                  },
                  onLoadStop: (controller, url) async {
                    _pullToRefreshController?.endRefreshing();
                    setState(() => _isLoading = false);
                  },
                  onProgressChanged: (controller, progress) {
                    if (progress == 100) {
                      _pullToRefreshController?.endRefreshing();
                    }
                    setState(() {
                      _progress = progress / 100;
                      if (progress >= 90) _isLoading = false;
                    });
                  },
                  onReceivedError: (controller, request, error) {
                    _pullToRefreshController?.endRefreshing();
                    if (error.type == WebResourceErrorType.HOST_LOOKUP) {
                      setState(() => _isOffline = true);
                    }
                  },
                  shouldOverrideUrlLoading: (controller, navigationAction) async {
                    var uri = navigationAction.request.url!;

                    // Buka WhatsApp, YouTube, atau Telp di aplikasi native luar
                    if (![
                      "http",
                      "https",
                      "file",
                      "chrome",
                      "data",
                      "javascript",
                      "about"
                    ].contains(uri.scheme) || uri.host.contains("whatsapp.com") || uri.host.contains("wa.me")) {
                      if (await canLaunchUrl(uri)) {
                        await launchUrl(uri, mode: LaunchMode.externalApplication);
                        return NavigationActionPolicy.CANCEL;
                      }
                    }

                    return NavigationActionPolicy.ALLOW;
                  },
                ),

              // Garis Progres Pemuatan (Top Loading Indicator)
              if (_isLoading && _progress < 1.0)
                LinearProgressIndicator(
                  value: _progress,
                  backgroundColor: Colors.transparent,
                  color: const Color(0xFFDC2626), // Torii Red Masayume
                  minHeight: 3.0,
                ),

              // Layar Indikator saat Internet Terputus
              if (_isOffline)
                Center(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 24),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(
                          Icons.wifi_off_rounded,
                          size: 72,
                          color: Color(0xFFDC2626),
                        ),
                        const SizedBox(height: 16),
                        const Text(
                          "Koneksi Internet Terputus",
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: Color(0xFF0F172A),
                          ),
                        ),
                        const SizedBox(height: 8),
                        const Text(
                          "Pastikan paket data atau Wi-Fi Anda aktif untuk melanjutkan pembelajaran LMS Masayume.",
                          textAlign: TextAlign.center,
                          style: TextStyle(fontSize: 13, color: Colors.grey),
                        ),
                        const SizedBox(height: 24),
                        ElevatedButton.icon(
                          onPressed: () {
                            setState(() => _isOffline = false);
                            _webViewController?.reload();
                          },
                          icon: const Icon(Icons.refresh_rounded, color: Colors.white),
                          label: const Text(
                            "Coba Muat Ulang",
                            style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white),
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFFDC2626),
                            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }

  void _showExitDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text("Tutup Aplikasi?"),
        content: const Text("Apakah Anda yakin ingin keluar dari aplikasi LMS Masayume?"),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text("Batal"),
          ),
          ElevatedButton(
            onPressed: () => SystemNavigator.pop(),
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFFDC2626),
            ),
            child: const Text("Keluar", style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );
  }
}
```

---

## 7. Pengaturan Ikon & Splash Screen

Simpan logo LPK (resolusi 512x512 PNG transparan) ke dalam folder:
`assets/images/logo_masayume.png`

Tambahkan di akhir file `pubspec.yaml`:
```yaml
flutter_launcher_icons:
  android: "launcher_icon"
  ios: true
  image_path: "assets/images/logo_masayume.png"
  adaptive_icon_background: "#0F172A" # Dark Navy Masayume
  adaptive_icon_foreground: "assets/images/logo_masayume.png"

flutter_native_splash:
  color: "#0F172A"
  image: "assets/images/logo_masayume.png"
  android_12:
    image: "assets/images/logo_masayume.png"
    icon_background_color: "#0F172A"
```

Lalu jalankan generator otomatis:
```bash
flutter pub run flutter_launcher_icons
flutter pub run flutter_native_splash:create
```

---

## 8. Build & Export File APK Release

Untuk menghasilkan file APK siap pakai yang bisa dibagikan ke siswa atau diinstall di HP:

```bash
# Bersihkan cache build lama
flutter clean
flutter pub get

# Compile file APK (Release Optimized)
flutter build apk --release
```

Hasil file APK yang sudah jadi akan berada di folder:
```
build/app/outputs/flutter-apk/app-release.apk
```
> **Catatan**: File `app-release.apk` inilah yang Anda bagikan ke siswa untuk di-install langsung di HP Android mereka.

---

## 9. Tips Maintenance & Deployment Cloud

1. **Aturan Caching Nginx/Cloudflare di Server**:
   - File assets build JS/CSS (`/build/assets/*`) boleh di-cache agresif karena menggunakan hash unik Vite.
   - File HTML index (`/`, `/siswa/*`, dll.) pastikan menggunakan header `Cache-Control: no-cache, must-revalidate` agar setiap kali Anda deploy versi baru, WebView APK langsung memuat referensi script terbaru tanpa tertahan cache lama.
2. **Kapan Perlu Update APK?**:
   - Jika Anda mengubah materi, menambah bank soal, mengubah kartu kuis, atau menambah tombol di dock navigasi: **Cukup deploy web di hosting, APK otomatis ikut ter-update**.
   - APK baru hanya perlu di-compile jika Anda mengganti nama aplikasi di HP, mengganti logo icon launcher, atau memperbarui versi SDK Android.
