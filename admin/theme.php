<?php
$active = 'theme';
require __DIR__ . '/../includes/auth.php';
mc_require_login();

$presets = [
    'classic-blue'  => ['Klasik Biru',   '#0a4ea3', '#f5b301'],
    'royal-green'   => ['Hijau Royal',   '#14532d', '#facc15'],
    'midnight'      => ['Tengah Malam',  '#0f172a', '#22d3ee'],
    'sunset-purple' => ['Senja Ungu',    '#5b21b6', '#fbbf24'],
    'desert-gold'   => ['Padang Pasir',  '#92400e', '#fde68a'],
    'turquoise'     => ['Pirus',         '#0d9488', '#facc15'],
    'maroon'        => ['Maroon',        '#7f1d1d', '#fbbf24'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mc_csrf_check()) { $_SESSION['flash']=['type'=>'err','msg'=>'CSRF invalid']; mc_redirect('theme.php'); }
    $preset = $_POST['preset'] ?? 'classic-blue';
    $primary = $_POST['primary'] ?? '#0a4ea3';
    $accent  = $_POST['accent']  ?? '#f5b301';
    if (isset($presets[$preset]) && empty($_POST['custom'])) {
        [$name, $primary, $accent] = $presets[$preset];
    }
    mc_setting_set('theme_preset', $preset);
    mc_setting_set('theme_primary', $primary);
    mc_setting_set('theme_accent', $accent);
    $_SESSION['flash']=['type'=>'ok','msg'=>'Tema disimpan.'];
    mc_redirect('theme.php');
}

require __DIR__ . '/_layout.php';
$cur = mc_setting('theme_preset','classic-blue');
?>
<h1>Tema Warna</h1>
<div class="card">
    <p>Pilih preset atau atur warna manual. Warna akan diterapkan pada layar tampilan masjid.</p>
    <form method="post">
        <?= mc_csrf_field() ?>
        <h2>Preset</h2>
        <div class="preset-list">
            <?php foreach ($presets as $key => [$name, $p, $a]): ?>
                <label class="preset <?= $cur===$key?'selected':'' ?>">
                    <input type="radio" name="preset" value="<?= mc_e($key) ?>" <?= $cur===$key?'checked':'' ?> hidden>
                    <div class="swatch"><span style="background:<?= $p ?>"></span><span style="background:<?= $a ?>"></span></div>
                    <?= mc_e($name) ?>
                </label>
            <?php endforeach; ?>
        </div>

        <h2 style="margin-top:24px">Kustom</h2>
        <label><input type="checkbox" name="custom" value="1"> Gunakan warna kustom (override preset)</label>
        <div class="row">
            <div>
                <label>Primary</label>
                <input type="color" name="primary" value="<?= mc_e(mc_setting('theme_primary','#0a4ea3')) ?>">
            </div>
            <div>
                <label>Accent</label>
                <input type="color" name="accent" value="<?= mc_e(mc_setting('theme_accent','#f5b301')) ?>">
            </div>
        </div>

        <p style="margin-top:18px"><button class="btn" type="submit">Simpan Tema</button></p>
    </form>
</div>

<script>
document.querySelectorAll('.preset').forEach(p => {
    p.addEventListener('click', () => {
        document.querySelectorAll('.preset').forEach(x => x.classList.remove('selected'));
        p.classList.add('selected');
        p.querySelector('input[type=radio]').checked = true;
    });
});
</script>
<?php require __DIR__ . '/_footer.php';
