<?php
$active = 'running';
$pageTitle = 'Running Text';
$pageSubtitle = 'Teks berjalan di lower-third layar';

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

<div class="mc-card">
    <h2>Tambah Teks</h2>
    <form method="post">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="add">
        <textarea name="text" class="mc-textarea" placeholder="Mis: Mari makmurkan masjid dengan sholat berjamaah" required></textarea>
        <button class="mc-btn mt-3" type="submit">Tambah</button>
    </form>
</div>

<div class="mc-card">
    <h2>Kecepatan Animasi</h2>
    <form method="post">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="speed">
        <p class="text-sm text-slate-500 mb-2">Durasi satu putaran (detik). Lebih besar = lebih lambat.</p>
        <input type="number" name="speed" min="20" max="180" class="mc-input" value="<?= mc_e(mc_setting('running_text_speed','60')) ?>">
        <button class="mc-btn mt-3" type="submit">Simpan</button>
    </form>
</div>

<div class="mc-card">
    <h2>Daftar Teks (<?= count($rows) ?>)</h2>
    <?php if (!$rows): ?>
        <p class="text-sm text-slate-500">Belum ada teks.</p>
    <?php else: ?>
    <table class="mc-table">
        <thead><tr><th>Teks</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td class="text-sm"><?= mc_e($r['text']) ?></td>
                <td><span class="mc-badge <?= $r['is_active']?'on':'off' ?>"><?= $r['is_active']?'Aktif':'Off' ?></span></td>
                <td class="text-right whitespace-nowrap">
                    <a class="mc-btn ghost small" href="?a=toggle&id=<?= (int)$r['id'] ?>">Toggle</a>
                    <a class="mc-btn danger small" href="?a=delete&id=<?= (int)$r['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/_footer.php';
