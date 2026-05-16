<?php
$active = 'imam';
$pageTitle = 'Jadwal Imam';
$pageSubtitle = 'Nama imam untuk setiap waktu sholat (Senin–Minggu) + Sholat Jum\'at';

require __DIR__ . '/../includes/auth.php';
mc_require_login();

$pdo = mc_db();
$T = mc_table('imam_schedule');

$days = [
    1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
    5 => "Jum'at", 6 => 'Sabtu', 0 => 'Minggu'
];
$prayers = ['subuh','dzuhur','ashar','maghrib','isya'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mc_csrf_check()) { $_SESSION['flash']=['type'=>'err','msg'=>'CSRF invalid']; mc_redirect('imam.php'); }
    $items = $_POST['items'] ?? [];
    $up = $pdo->prepare("INSERT INTO $T (day_of_week,prayer,imam_name,khatib_name,bilal_name) VALUES (?,?,?,?,?)
                        ON DUPLICATE KEY UPDATE imam_name=VALUES(imam_name),khatib_name=VALUES(khatib_name),bilal_name=VALUES(bilal_name)");
    foreach ($items as $key => $vals) {
        [$dow, $pr] = explode('|', $key);
        $up->execute([(int)$dow, $pr, trim($vals['imam'] ?? ''), trim($vals['khatib'] ?? '') ?: null, trim($vals['bilal'] ?? '') ?: null]);
    }
    if (!empty($_POST['jumat'])) {
        $up->execute([5, 'jumat', trim($_POST['jumat']['imam'] ?? ''), trim($_POST['jumat']['khatib'] ?? '') ?: null, trim($_POST['jumat']['bilal'] ?? '') ?: null]);
    }
    $_SESSION['flash'] = ['type'=>'ok','msg'=>'Jadwal imam disimpan.'];
    mc_redirect('imam.php');
}

$existing = [];
foreach ($pdo->query("SELECT * FROM $T")->fetchAll() as $r) {
    $existing[$r['day_of_week'].'|'.$r['prayer']] = $r;
}

require __DIR__ . '/_layout.php';
?>

<form method="post">
    <?= mc_csrf_field() ?>

    <div class="mc-card">
        <h2>Jadwal Harian</h2>
        <p class="text-sm text-slate-500 mb-3">Kosongkan jika tidak ingin menampilkan nama imam pada hari/sholat tertentu.</p>
        <div class="overflow-x-auto">
            <table class="mc-table">
                <thead>
                    <tr><th>Hari</th><?php foreach ($prayers as $p): ?><th><?= ucfirst($p) ?></th><?php endforeach; ?></tr>
                </thead>
                <tbody>
                    <?php foreach ($days as $dow => $name): ?>
                        <tr>
                            <td class="font-semibold text-slate-900 whitespace-nowrap"><?= mc_e($name) ?></td>
                            <?php foreach ($prayers as $p):
                                $key = "$dow|$p";
                                $val = $existing[$key]['imam_name'] ?? ''; ?>
                                <td>
                                    <input type="text" name="items[<?= mc_e($key) ?>][imam]" value="<?= mc_e($val) ?>" placeholder="Nama imam" class="mc-input">
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mc-card">
        <h2>Sholat Jum'at</h2>
        <?php $j = $existing['5|jumat'] ?? []; ?>
        <div class="row-3">
            <div><label class="mc-label">Imam</label><input type="text" name="jumat[imam]" class="mc-input" value="<?= mc_e($j['imam_name'] ?? '') ?>"></div>
            <div><label class="mc-label">Khatib</label><input type="text" name="jumat[khatib]" class="mc-input" value="<?= mc_e($j['khatib_name'] ?? '') ?>"></div>
            <div><label class="mc-label">Bilal/Muadzin</label><input type="text" name="jumat[bilal]" class="mc-input" value="<?= mc_e($j['bilal_name'] ?? '') ?>"></div>
        </div>
    </div>

    <button class="mc-btn" type="submit">Simpan Jadwal</button>
</form>

<?php require __DIR__ . '/_footer.php';
