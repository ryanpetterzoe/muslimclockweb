<?php
$active = 'adzan';
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
<h1>Adzan & Iqomah</h1>
<div class="card">
    <p>Saat masuk waktu sholat, layar akan menampilkan overlay penuh dengan pesan kustom dan hitungan mundur. Setelah adzan selesai, akan dilanjutkan hitung mundur iqomah.</p>
    <form method="post">
        <?= mc_csrf_field() ?>
        <label>Pesan Saat Adzan</label>
        <input type="text" name="adzan_message" value="<?= mc_e(mc_setting('adzan_message','Saatnya Waktu Sholat')) ?>" required>

        <div class="row">
            <div>
                <label>Durasi Adzan (detik)</label>
                <input type="number" min="60" max="3600" name="adzan_duration" value="<?= (int)mc_setting('adzan_duration',600) ?>">
            </div>
            <div>
                <label>Durasi Iqomah (detik)</label>
                <input type="number" min="60" max="3600" name="iqomah_duration" value="<?= (int)mc_setting('iqomah_duration',600) ?>">
            </div>
        </div>

        <p style="margin-top:18px"><button class="btn" type="submit">Simpan</button></p>
    </form>
</div>
<?php require __DIR__ . '/_footer.php';
