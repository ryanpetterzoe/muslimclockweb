<?php
$active = 'imam';
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
    // Jum'at khusus
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
<h1>Jadwal Imam Sholat</h1>
<div class="card">
    <p>Isi nama imam (dan khatib/bilal jika perlu) untuk setiap waktu sholat. Akan ditampilkan otomatis pada kartu waktu sholat saat ini.</p>
    <form method="post">
        <?= mc_csrf_field() ?>
        <table>
            <thead>
                <tr><th>Hari</th><?php foreach ($prayers as $p): ?><th><?= ucfirst($p) ?></th><?php endforeach; ?></tr>
            </thead>
            <tbody>
                <?php foreach ($days as $dow => $name): ?>
                    <tr>
                        <td><strong><?= mc_e($name) ?></strong></td>
                        <?php foreach ($prayers as $p):
                            $key = "$dow|$p";
                            $val = $existing[$key]['imam_name'] ?? ''; ?>
                            <td>
                                <input type="text" name="items[<?= mc_e($key) ?>][imam]" value="<?= mc_e($val) ?>" placeholder="Nama imam">
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Sholat Jum'at</h2>
        <?php $j = $existing['5|jumat'] ?? []; ?>
        <div class="row3">
            <div><label>Imam</label><input type="text" name="jumat[imam]" value="<?= mc_e($j['imam_name'] ?? '') ?>"></div>
            <div><label>Khatib</label><input type="text" name="jumat[khatib]" value="<?= mc_e($j['khatib_name'] ?? '') ?>"></div>
            <div><label>Bilal/Muadzin</label><input type="text" name="jumat[bilal]" value="<?= mc_e($j['bilal_name'] ?? '') ?>"></div>
        </div>

        <p style="margin-top:18px"><button class="btn" type="submit">Simpan Jadwal</button></p>
    </form>
</div>
<?php require __DIR__ . '/_footer.php';
