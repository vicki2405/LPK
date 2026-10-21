<?php
/**
 * LPK CPanel Deployment Helper
 * Akses melalui: https://domain-anda.com/install_helper.php?token=lpk_cpanel_2026
 */

$secret_token = 'lpk_cpanel_2026';

if (!isset($_GET['token']) || $_GET['token'] !== $secret_token) {
    http_response_code(403);
    echo '<!DOCTYPE html><html><head><title>Access Denied</title><style>body{font-family:sans-serif;padding:40px;background:#f8fafc;color:#1e293b;}</style></head><body>';
    echo '<h2>403 - Akses Ditolak</h2>';
    echo '<p>Gunakan URL dengan token keamanan yang benar, contoh: <code>/install_helper.php?token=' . htmlspecialchars($secret_token) . '</code></p>';
    echo '</body></html>';
    exit;
}

// Aksi hapus file sendiri
if (isset($_POST['action']) && $_POST['action'] === 'delete_self') {
    @unlink(__FILE__);
    echo '<!DOCTYPE html><html><head><title>Helper Dihapus</title><style>body{font-family:sans-serif;padding:40px;background:#f0fdf4;color:#166534;}</style></head><body>';
    echo '<h2>✅ File install_helper.php Berhasil Dihapus!</h2>';
    echo '<p>Sistem Anda sekarang aman dan siap digunakan sepenuhnya. <a href="/">Menuju Halaman Utama</a></p>';
    echo '</body></html>';
    exit;
}

$base_dir = dirname(__DIR__);
$messages = [];

// 1. Cek .env
$env_file = $base_dir . '/.env';
if (!file_exists($env_file)) {
    if (file_exists($base_dir . '/.env.cpanel')) {
        @copy($base_dir . '/.env.cpanel', $env_file);
        $messages[] = ['type' => 'success', 'text' => 'File .env otomatis dibuat dari template .env.cpanel!'];
    } else {
        $messages[] = ['type' => 'error', 'text' => 'File .env belum ditemukan! Silakan salin .env.cpanel menjadi .env'];
    }
} else {
    $messages[] = ['type' => 'info', 'text' => 'File .env sudah ada.'];
}

// 2. Buat Storage Symlink
$target = $base_dir . '/storage/app/public';
$shortcut = __DIR__ . '/storage';

if (!file_exists($target)) {
    @mkdir($target, 0755, true);
}

if (!file_exists($shortcut)) {
    if (@symlink($target, $shortcut)) {
        $messages[] = ['type' => 'success', 'text' => 'Symlink storage (public/storage) berhasil dibuat!'];
    } else {
        // Fallback jika symlink dimatikan oleh hosting:
        $messages[] = ['type' => 'warning', 'text' => 'Symlink otomatis gagal dibuat oleh fungsi PHP hosting. Anda dapat membuat symlink manual atau menghubungi penyedia hosting.'];
    }
} else {
    $messages[] = ['type' => 'info', 'text' => 'Symlink public/storage sudah aktif.'];
}

// 3. Pastikan permission folder storage dan bootstrap/cache writable
$writable_dirs = [
    $base_dir . '/storage',
    $base_dir . '/storage/app',
    $base_dir . '/storage/app/public',
    $base_dir . '/storage/framework',
    $base_dir . '/storage/framework/cache',
    $base_dir . '/storage/framework/cache/data',
    $base_dir . '/storage/framework/sessions',
    $base_dir . '/storage/framework/views',
    $base_dir . '/storage/logs',
    $base_dir . '/bootstrap/cache',
];

foreach ($writable_dirs as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
    @chmod($dir, 0755);
}
$messages[] = ['type' => 'success', 'text' => 'Folder storage dan bootstrap/cache telah diverifikasi siap & writable.'];

// 4. Bersihkan Cache Bootstrap & View lama jika ada
$cache_files = glob($base_dir . '/bootstrap/cache/*.php');
foreach ($cache_files as $cf) {
    @unlink($cf);
}
$messages[] = ['type' => 'success', 'text' => 'Cache bootstrap dibersihkan untuk memastikan konfigurasi hosting baru langsung dibaca.'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPK cPanel Deploy Helper</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #0f172a; color: #f8fafc; padding: 30px; margin: 0; }
        .card { max-width: 650px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 28px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: 1px solid #334155; }
        h1 { margin-top: 0; font-size: 22px; color: #38bdf8; display: flex; align-items: center; gap: 10px; }
        .msg { padding: 12px 16px; border-radius: 8px; margin-bottom: 12px; font-size: 14px; line-height: 1.5; }
        .msg.success { background: #064e3b; color: #a7f3d0; border-left: 4px solid #10b981; }
        .msg.info { background: #1e3a5f; color: #bae6fd; border-left: 4px solid #38bdf8; }
        .msg.warning { background: #713f12; color: #fde047; border-left: 4px solid #eab308; }
        .msg.error { background: #7f1d1d; color: #fecaca; border-left: 4px solid #ef4444; }
        .btn { display: inline-block; padding: 10px 20px; font-weight: 600; border-radius: 6px; text-decoration: none; cursor: pointer; border: none; font-size: 14px; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-primary { background: #0284c7; color: white; margin-right: 8px; }
        .btn-primary:hover { background: #0369a1; }
        .footer { margin-top: 24px; padding-top: 18px; border-top: 1px solid #334155; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <div class="card">
        <h1>⚙️ LPK cPanel Deployment Helper</h1>
        <p style="color: #94a3b8; font-size: 14px;">Script ini membantu inisialisasi symlink media dan izin folder sistem di hosting cPanel.</p>
        
        <div style="margin: 20px 0;">
            <?php foreach ($messages as $m): ?>
                <div class="msg <?= htmlspecialchars($m['type']) ?>">
                    <?= htmlspecialchars($m['text']) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="footer">
            <a href="/" class="btn btn-primary">Buka Website LMS</a>
            
            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus helper ini demi keamanan?');">
                <input type="hidden" name="action" value="delete_self">
                <button type="submit" class="btn btn-danger">Hapus Helper Ini</button>
            </form>
        </div>
    </div>
</body>
</html>
