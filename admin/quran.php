<?php
$active = 'quran';
require __DIR__ . '/../includes/auth.php';
mc_require_login();

$pdo = mc_db();
$T = mc_table('quran_quotes');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mc_csrf_check()) { $_SESSION['flash']=['type'=>'err','msg'=>'CSRF invalid']; mc_redirect('quran.php'); }
    $action = $_POST['a'] ?? '';
    if ($action === 'mode') {
        mc_setting_set('quran_mode', $_POST['mode'] ?? 'auto');
        $_SESSION['flash']=['type'=>'ok','msg'=>'Mode disimpan.'];
    } elseif ($action === 'add') {
        $arabic = trim($_POST['arabic'] ?? '');
        $tr     = trim($_POST['translation'] ?? '');
        $ref    = trim($_POST['reference'] ?? '');
        if ($arabic !== '') {
            $pdo->prepare("INSERT INTO $T (arabic,translation,reference,is_active) VALUES (?,?,?,1)")
                ->execute([$arabic, $tr, $ref]);
            $_SESSION['flash']=['type'=>'ok','msg'=>'Cuplikan ditambahkan.'];
        }
    }
    mc_redirect('quran.php');
}
$ga = $_GET['a'] ?? '';
if ($ga === 'toggle') { $pdo->prepare("UPDATE $T SET is_active=1-is_active WHERE id=?")->execute([(int)$_GET['id']]); mc_redirect('quran.php'); }
if ($ga === 'delete') { $pdo->prepare("DELETE FROM $T WHERE id=?")->execute([(int)$_GET['id']]); mc_redirect('quran.php'); }

$rows = $pdo->query("SELECT * FROM $T ORDER BY id DESC")->fetchAll();
$mode = mc_setting('quran_mode','auto');
require __DIR__ . '/_layout.php';
?>
<h1>Cuplikan Al-Qur'an</h1>

<div class="card">
    <h2>Mode Sumber</h2>
    <form method="post">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="mode">
        <label><input type="radio" name="mode" value="auto" <?= $mode==='auto'?'checked':'' ?>>
            <strong>Otomatis</strong> — ambil ayat acak dari API <a href="https://alquran.cloud/" target="_blank">alquran.cloud</a> (kurasi pilihan).</label>
        <label><input type="radio" name="mode" value="manual" <?= $mode==='manual'?'checked':'' ?>>
            <strong>Manual</strong> — gunakan daftar di bawah ini.</label>
        <p style="margin-top:14px"><button class="btn" type="submit">Simpan Mode</button></p>
    </form>
</div>

<div class="card">
    <h2>Tambah Cuplikan Manual</h2>
    <form method="post">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="add">
        <label>Teks Arab</label>
        <textarea name="arabic" dir="rtl" style="font-size:20px"></textarea>
        <label>Terjemahan</label>
        <textarea name="translation"></textarea>
        <label>Referensi (mis. QS. Al-Baqarah: 153)</label>
        <input type="text" name="reference">
        <p style="margin-top:14px"><button class="btn" type="submit">Tambah</button></p>
    </form>
</div>

<div class="card">
    <h2>Daftar Cuplikan</h2>
    <?php if (!$rows): ?>
        <p style="color:#6b7280">Belum ada cuplikan manual.</p>
    <?php else: ?>
    <table>
        <thead><tr><th>Arab</th><th>Terjemahan</th><th>Referensi</th><th>Status</th><th></th></tr></thead>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td dir="rtl" style="font-size:18px;max-width:280px"><?= mc_e($r['arabic']) ?></td>
                <td><?= mc_e($r['translation']) ?></td>
                <td><?= mc_e($r['reference']) ?></td>
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
