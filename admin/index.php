<?php
$active = 'dashboard';
require __DIR__ . '/_layout.php';

$pdo = mc_db();
$counts = [
    'slides'   => (int)$pdo->query("SELECT COUNT(*) FROM " . mc_table('slides'))->fetchColumn(),
    'running'  => (int)$pdo->query("SELECT COUNT(*) FROM " . mc_table('running_text'))->fetchColumn(),
    'imam'     => (int)$pdo->query("SELECT COUNT(*) FROM " . mc_table('imam_schedule'))->fetchColumn(),
    'quran'    => (int)$pdo->query("SELECT COUNT(*) FROM " . mc_table('quran_quotes'))->fetchColumn(),
];
?>
<h1>Dashboard</h1>
<div class="card">
    <p>Selamat datang, <strong><?= mc_e($user['name'] ?? $user['username']) ?></strong>.
       Kelola tampilan layar masjid dari menu di sebelah kiri.</p>
</div>

<div class="row" style="grid-template-columns:repeat(4,1fr);">
    <div class="card"><div style="font-size:12px;color:#6b7280">Slideshow</div><div style="font-size:28px;font-weight:800"><?= $counts['slides'] ?></div></div>
    <div class="card"><div style="font-size:12px;color:#6b7280">Running Text</div><div style="font-size:28px;font-weight:800"><?= $counts['running'] ?></div></div>
    <div class="card"><div style="font-size:12px;color:#6b7280">Jadwal Imam</div><div style="font-size:28px;font-weight:800"><?= $counts['imam'] ?></div></div>
    <div class="card"><div style="font-size:12px;color:#6b7280">Cuplikan Qur'an</div><div style="font-size:28px;font-weight:800"><?= $counts['quran'] ?></div></div>
</div>

<div class="card">
    <h2>Konfigurasi Saat Ini</h2>
    <table>
        <tr><th>Nama Masjid</th><td><?= mc_e(mc_setting('masjid_name')) ?></td></tr>
        <tr><th>Alamat</th><td><?= mc_e(mc_setting('masjid_address')) ?></td></tr>
        <tr><th>Lokasi</th><td><?= mc_e(mc_setting('location_city_name')) ?>
            (<?= mc_e(mc_setting('location_lat')) ?>, <?= mc_e(mc_setting('location_lng')) ?>)</td></tr>
        <tr><th>Metode Hitung</th><td>Aladhan #<?= mc_e(mc_setting('calc_method')) ?> (KEMENAG = 20)</td></tr>
        <tr><th>Tema</th><td><?= mc_e(mc_setting('theme_preset')) ?></td></tr>
        <tr><th>Pesan Adzan</th><td><?= mc_e(mc_setting('adzan_message')) ?></td></tr>
        <tr><th>Durasi Adzan</th><td><?= (int)mc_setting('adzan_duration') ?> detik</td></tr>
        <tr><th>Durasi Iqomah</th><td><?= (int)mc_setting('iqomah_duration') ?> detik</td></tr>
    </table>
</div>
<?php require __DIR__ . '/_footer.php';
