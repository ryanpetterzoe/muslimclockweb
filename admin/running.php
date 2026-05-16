<?php
$active = 'running';
require __DIR__ . '/../includes/auth.php';
mc_require_login();

$pdo = mc_db();
$T = mc_table('running_text');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mc_csrf_check()) { $_SESSION['flash']=['type'=>'err','msg'=>'CSRF invalid']; mc_redirect('running.php'); }
    $action = $_POST['a'] ?? '';
    if ($action === 'add') {
        $text = trim($_POST['text'] ?? '');
        if ($text !== '') {
            $pdo->prepare("INSERT INTO $T (text,is_active,sort_order) VALUES (?,1,?)")
                ->execute([$text, (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM $T")->fetchColumn() + 1]);
            $_SESSION['flash']=['type'=>'ok','msg'=>'Teks ditambahkan.'];
        }
    } elseif ($action === 'speed') {
        mc_setting_set('running_text_speed', (int)($_POST['speed'] ?? 60));
        $_SESSION['flash']=['type'=>'ok','msg'=>'Kecepatan disimpan.'];
    }
    mc_redirect('running.php');
}
$ga = $_GET['a'] ?? '';
if ($ga === 'toggle') { $pdo->prepare("UPDATE $T SET is_active=1-is_active WHERE id=?")->execute([(int)$_GET['id']]); mc_redirect('running.php'); }
if ($ga === 'delete') { $pdo->prepare("DELETE FROM $T WHERE id=?")->execute([(int)$_GET['id']]); mc_redirect('running.php'); }

$rows = $pdo->query("SELECT * FROM $T ORDER BY sort_order,id")->fetchAll();

require __DIR__ . '/_layout.php';
?>
<h1>Running Text (Lower Third)</h1>

<div class="card">
    <h2>Tambah Teks</h2>
    <form method="post">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="add">
        <textarea name="text" placeholder="Mis: Mari makmurkan masjid dengan sholat berjamaah" required></textarea>
        <p style="margin-top:14px"><button class="btn" type="submit">Tambah</button></p>
    </form>
</div>

<div class="card">
    <h2>Kecepatan Animasi</h2>
    <form method="post">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="speed">
        <label>Durasi satu putaran (detik). Lebih besar = lebih lambat.</label>
        <input type="number" name="speed" min="20" max="180" value="<?= mc_e(mc_setting('running_text_speed','60')) ?>">
        <p style="margin-top:14px"><button class="btn" type="submit">Simpan</button></p>
    </form>
</div>

<div class="card">
    <h2>Daftar Teks</h2>
    <?php if (!$rows): ?>
        <p style="color:#6b7280">Belum ada teks.</p>
    <?php else: ?>
    <table>
        <thead><tr><th>Teks</th><th>Status</th><th>Aksi</th></tr></thead>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= mc_e($r['text']) ?></td>
                <td><span class="badge <?= $r['is_active']?'on':'off' ?>"><?= $r['is_active']?'Aktif':'Nonaktif' ?></span></td>
                <td>
                    <a class="btn small secondary" href="?a=toggle&id=<?= (int)$r['id'] ?>">Toggle</a>
                    <a class="btn small danger" href="?a=delete&id=<?= (int)$r['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php';
