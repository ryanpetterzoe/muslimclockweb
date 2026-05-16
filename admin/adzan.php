<?php
$active = 'adzan';
$pageTitle = 'Adzan & Iqomah';
$pageSubtitle = 'Pesan dan durasi countdown saat masuk waktu sholat';

require __DIR__ . '/../includes/auth.php';
mc_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mc_csrf_check()) { $_SESSION['flash']=['type'=>'err','msg'=>'CSRF invalid']; mc_redirect('adzan.php'); }
    mc_setting_set('adzan_message', trim($_POST['adzan_message'] ?? 'Saatnya Waktu Sholat'));
    mc_setting_set('adzan_duration', max(60, (int)($_POST['adzan_duration'] ?? 600)));
    mc_setting_set('iqomah_duration', max(60, (int)($_POST['iqomah_duration'] ?? 600)));
    $_SESSION['flash']=['type'=>'ok','msg'=>'Pengaturan adzan disimpan.'];
    mc_redirect('adzan.php');
}

require __DIR__ . '/_layout.php';
?>

<form method="post">
    <?= mc_csrf_field() ?>
    <div class="mc-card">
        <p class="text-sm text-slate-500 mb-4">Saat masuk waktu sholat, layar akan menampilkan overlay penuh dengan pesan kustom dan hitungan mundur. Setelah adzan selesai, akan dilanjutkan hitung mundur iqomah.</p>

        <label class="mc-label">Pesan Saat Adzan</label>
        <input type="text" name="adzan_message" class="mc-input" value="<?= mc_e(mc_setting('adzan_message','Saatnya Waktu Sholat')) ?>" required>

        <div class="row-2 mt-2">
            <div>
                <label class="mc-label">Durasi Adzan (detik)</label>
                <input type="number" min="60" max="3600" name="adzan_duration" class="mc-input" value="<?= (int)mc_setting('adzan_duration',600) ?>">
            </div>
            <div>
                <label class="mc-label">Durasi Iqomah (detik)</label>
                <input type="number" min="60" max="3600" name="iqomah_duration" class="mc-input" value="<?= (int)mc_setting('iqomah_duration',600) ?>">
            </div>
        </div>
    </div>

    <button class="mc-btn" type="submit">Simpan</button>
</form>

<?php require __DIR__ . '/_footer.php';
