<?php
/**
 * One-Click Automatic Installer for cPanel Shared Hosting (No Terminal Needed)
 * TokoKita Digital Store Auto Database & Storage Installer
 */

define('LARAVEL_START', microtime(true));

// Load Composer Autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    die('<div style="font-family:sans-serif;padding:30px;background:#fee2e2;color:#991b1b;border-radius:12px;">❌ Error: Folder <b>vendor</b> tidak ditemukan. Pastikan seluruh isi ZIP ter-ekstrak dengan sempurna.</div>');
}

// Bootstrap Laravel App
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$message = '';
$status = 'info';

if (isset($_GET['run']) && $_GET['run'] === 'install') {
    try {
        // 1. Run Migration & Seeder
        $kernel->call('migrate:fresh', [
            '--force' => true,
            '--seed' => true,
        ]);

        // 2. Storage Symlink
        $targetFolder = __DIR__ . '/storage/app/public';
        $linkFolder   = __DIR__ . '/storage';
        if (!file_exists($linkFolder) && file_exists($targetFolder)) {
            @symlink($targetFolder, $linkFolder);
        }

        // 3. Clear Caches
        $kernel->call('config:clear');
        $kernel->call('view:clear');

        $status = 'success';
        $message = '🎉 SELAMAT! Seluruh tabel MySQL, Akun Admin (mhilal044@gmail.com), dan Produk Digital telah BERHASIL dibuat secara otomatis!';
    } catch (\Exception $e) {
        $status = 'error';
        $message = '❌ Gagal membuat database: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Setup Installer - TokoKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-gray-800 border border-gray-700 rounded-3xl p-6 shadow-2xl text-center">
        <div class="w-16 h-16 bg-gradient-to-tr from-orange-500 to-amber-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
            <span class="text-3xl">🚀</span>
        </div>

        <h1 class="text-2xl font-extrabold text-white mb-2">Auto Setup TokoKita</h1>
        <p class="text-xs text-gray-400 mb-6">Installer Otomatis cPanel Shared Hosting (Tanpa SSH / Terminal)</p>

        <?php if ($status === 'success'): ?>
            <div class="bg-green-500/20 border-2 border-green-500 text-green-300 p-4 rounded-2xl text-xs font-semibold mb-6 text-left leading-relaxed">
                <?= $message ?>
            </div>
            <div class="space-y-3">
                <a href="./" class="block w-full py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm rounded-xl hover:opacity-90 transition-opacity">
                    🌐 Buka Website Sekarang
                </a>
                <p class="text-[11px] text-amber-400">⚠️ Demi keamanan, silakan hapus file <code class="bg-gray-900 px-2 py-0.5 rounded text-white">setup.php</code> ini dari cPanel File Manager.</p>
            </div>
        <?php elseif ($status === 'error'): ?>
            <div class="bg-red-500/20 border-2 border-red-500 text-red-300 p-4 rounded-2xl text-xs font-semibold mb-6 text-left leading-relaxed">
                <?= $message ?>
            </div>
            <a href="?run=install" class="block w-full py-3 bg-red-600 text-white font-bold text-sm rounded-xl hover:bg-red-700 transition-colors">
                🔄 Coba Lagi
            </a>
        <?php else: ?>
            <div class="bg-gray-700/50 p-4 rounded-2xl text-xs text-gray-300 mb-6 text-left space-y-2">
                <p class="font-bold text-white mb-1">Yang akan diproses otomatis:</p>
                <p>✅ Membuat seluruh tabel MySQL di cPanel</p>
                <p>✅ Membuat akun Admin default (<code class="text-amber-300">mhilal044@gmail.com</code>)</p>
                <p>✅ Memasang produk digital & stok awal</p>
                <p>✅ Mengaktifkan link folder gambar / storage</p>
            </div>

            <a href="?run=install" class="block w-full py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold text-sm rounded-xl hover:opacity-90 transition-all shadow-lg hover:scale-105">
                ⚡ Jalankan Auto Setup Database (1-Klik)
            </a>
        <?php endif; ?>
    </div>

</body>
</html>
