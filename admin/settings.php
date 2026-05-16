<?php
$active = 'settings';
$pageTitle = 'Identitas Masjid';
$pageSubtitle = 'Nama, alamat, dan logo masjid yang ditampilkan di header layar';

require __DIR__ . '/../includes/auth.php';
mc_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mc_csrf_check()) { $_SESSION['flash'] = ['type'=>'err','msg'=>'CSRF invalid']; mc_redirect('settings.php'); }
    mc_setting_set('masjid_name', trim($_POST['masjid_name'] ?? ''));
    mc_setting_set('masjid_address', trim($_POST['masjid_address'] ?? ''));

    if (!empty($_FILES['logo']['tmp_name']) && is_uploaded_file($_FILES['logo']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['png','jpg','jpeg','svg','webp'])) {
            $dir = __DIR__ . '/../assets/uploads';
            if (!is_dir($dir)) mkdir($dir, 0775, true);
            $name = 'logo_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['logo']['tmp_name'], $dir . '/' . $name);
            mc_setting_set('masjid_logo', 'assets/uploads/' . $name);
        }
    }
    if (!empty($_POST['remove_logo'])) mc_setting_set('masjid_logo', '');

    $_SESSION['flash'] = ['type'=>'ok','msg'=>'Identitas masjid disimpan.'];
    mc_redirect('settings.php');
}

require __DIR__ . '/_layout.php';
?>

<form method="post" enctype="multipart/form-data">
    <?= mc_csrf_field() ?>

    <div class="mc-card">
        <label class="mc-label">Nama Masjid / Mushola</label>
        <input type="text" name="masjid_name" class="mc-input" value="<?= mc_e(mc_setting('masjid_name')) ?>" required>

        <label class="mc-label">Alamat (akan tampil di header)</label>
        <input type="text" name="masjid_address" class="mc-input" value="<?= mc_e(mc_setting('masjid_address')) ?>" placeholder="Jl. Contoh No. 1, Kota">

        <label class="mc-label">Logo (PNG/JPG/SVG)</label>
        <?php if (mc_setting('masjid_logo')): ?>
            <div class="flex items-center gap-3 mb-3">
                <img src="<?= mc_e(mc_base_url() . '/' . mc_setting('masjid_logo')) ?>" class="h-16 bg-slate-900 p-2 rounded-lg">
                <label class="text-sm text-red-700"><input type="checkbox" name="remove_logo" value="1" class="rounded"> Hapus logo</label>
            </div>
        <?php endif; ?>
        <input type="file" name="logo" accept="image/*" class="mc-input">
    </div>

    <button type="submit" class="mc-btn">Simpan</button>
</form>

<?php require __DIR__ . '/_footer.php';
