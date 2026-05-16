<?php
$active = 'settings';
require __DIR__ . '/../includes/auth.php';
mc_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mc_csrf_check()) { $_SESSION['flash'] = ['type'=>'err','msg'=>'CSRF invalid']; mc_redirect('settings.php'); }
    mc_setting_set('masjid_name', trim($_POST['masjid_name'] ?? ''));
    mc_setting_set('masjid_address', trim($_POST['masjid_address'] ?? ''));

    // logo upload (optional)
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
<h1>Identitas Masjid</h1>
<div class="card">
    <form method="post" enctype="multipart/form-data">
        <?= mc_csrf_field() ?>
        <label>Nama Masjid / Mushola</label>
        <input type="text" name="masjid_name" value="<?= mc_e(mc_setting('masjid_name')) ?>" required>
        <label>Alamat (akan tampil di header)</label>
        <input type="text" name="masjid_address" value="<?= mc_e(mc_setting('masjid_address')) ?>">

        <label>Logo (PNG/JPG/SVG)</label>
        <?php if (mc_setting('masjid_logo')): ?>
            <p><img src="<?= mc_e(mc_base_url() . '/' . mc_setting('masjid_logo')) ?>" style="height:60px;background:#0b2447;padding:6px;border-radius:6px"></p>
            <label><input type="checkbox" name="remove_logo" value="1"> Hapus logo</label>
        <?php endif; ?>
        <input type="file" name="logo" accept="image/*">

        <p style="margin-top:18px"><button class="btn" type="submit">Simpan</button></p>
    </form>
</div>
<?php require __DIR__ . '/_footer.php';
