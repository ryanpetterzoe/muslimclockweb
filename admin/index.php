<?php
$active = 'dashboard';
$pageTitle = 'Dashboard';
$pageSubtitle = 'Ringkasan konfigurasi dan konten';
$pageActions = '<a href="' . mc_e(mc_base_url()) . '/index.php" target="_blank" class="mc-btn">Buka Layar</a>';
require __DIR__ . '/_layout.php';

$pdo = mc_db();
$counts = [
    'slides'  => (int)$pdo->query("SELECT COUNT(*) FROM " . mc_table('slides'))->fetchColumn(),
    'running' => (int)$pdo->query("SELECT COUNT(*) FROM " . mc_table('running_text'))->fetchColumn(),
    'imam'    => (int)$pdo->query("SELECT COUNT(*) FROM " . mc_table('imam_schedule'))->fetchColumn(),
    'quran'   => (int)$pdo->query("SELECT COUNT(*) FROM " . mc_table('quran_quotes'))->fetchColumn(),
];
$layouts = mc_layouts();
$layoutKey = mc_setting('layout', 'cinema');
?>

<div class="stat-grid">
    <div class="stat-tile">
        <div class="label">Slideshow</div>
        <div class="value"><?= $counts['slides'] ?></div>
    </div>
    <div class="stat-tile">
        <div class="label">Running Text</div>
        <div class="value"><?= $counts['running'] ?></div>
    </div>
    <div class="stat-tile">
        <div class="label">Jadwal Imam</div>
        <div class="value"><?= $counts['imam'] ?></div>
    </div>
    <div class="stat-tile">
        <div class="label">Cuplikan Qur'an</div>
        <div class="value"><?= $counts['quran'] ?></div>
    </div>
</div>

<div class="row-2">
    <div class="mc-card">
        <h2>
            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Konfigurasi Saat Ini
        </h2>
        <table class="mc-table">
            <tr><th class="!w-40">Nama Masjid</th><td><?= mc_e(mc_setting('masjid_name')) ?></td></tr>
            <tr><th>Alamat</th><td><?= mc_e(mc_setting('masjid_address')) ?: '<span class="text-slate-400">—</span>' ?></td></tr>
            <tr><th>Lokasi</th><td><?= mc_e(mc_setting('location_city_name')) ?> (<?= mc_e(mc_setting('location_lat')) ?>, <?= mc_e(mc_setting('location_lng')) ?>)</td></tr>
            <tr><th>Metode</th><td>Aladhan #<?= mc_e(mc_setting('calc_method')) ?></td></tr>
            <tr><th>Layout</th><td><?= mc_e($layouts[$layoutKey][0] ?? $layoutKey) ?></td></tr>
            <tr><th>Pesan Adzan</th><td><?= mc_e(mc_setting('adzan_message')) ?></td></tr>
            <tr><th>Durasi Adzan</th><td><?= (int)mc_setting('adzan_duration') ?> detik</td></tr>
            <tr><th>Durasi Iqomah</th><td><?= (int)mc_setting('iqomah_duration') ?> detik</td></tr>
        </table>
    </div>

    <div class="mc-card">
        <h2>
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Aksi Cepat
        </h2>
        <div class="grid grid-cols-2 gap-3">
            <a href="settings.php" class="mc-btn secondary justify-center">Edit Identitas</a>
            <a href="location.php" class="mc-btn secondary justify-center">Atur Lokasi</a>
            <a href="slides.php" class="mc-btn secondary justify-center">Kelola Slideshow</a>
            <a href="appearance.php" class="mc-btn secondary justify-center">Tampilan & Tema</a>
            <a href="imam.php" class="mc-btn secondary justify-center">Jadwal Imam</a>
            <a href="adzan.php" class="mc-btn secondary justify-center">Pengaturan Adzan</a>
        </div>
    </div>
</div>

<div class="mc-card">
    <h2>Selamat datang, <?= mc_e($user['name'] ?? $user['username']) ?> 👋</h2>
    <p class="text-sm text-slate-600 leading-relaxed">
        Aplikasi siap digunakan. Beberapa hal yang biasanya perlu diatur:
    </p>
    <ul class="text-sm text-slate-600 mt-2 space-y-1 list-disc pl-5">
        <li>Pilih <a href="appearance.php" class="text-blue-700 font-semibold hover:underline">layout & tema</a> yang sesuai (3 layout tersedia: Cinema, Minimal, Mosque)</li>
        <li>Atur <a href="location.php" class="text-blue-700 font-semibold hover:underline">lokasi masjid</a> agar jadwal sholat presisi</li>
        <li>Unggah <a href="slides.php" class="text-blue-700 font-semibold hover:underline">foto/video masjid</a> untuk slideshow</li>
        <li>Isi <a href="imam.php" class="text-blue-700 font-semibold hover:underline">jadwal imam</a> harian</li>
    </ul>
</div>

<?php require __DIR__ . '/_footer.php';
