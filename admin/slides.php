<?php
$active = 'slides';
require __DIR__ . '/../includes/auth.php';
mc_require_login();

$pdo = mc_db();
$T = mc_table('slides');

// actions
$action = $_GET['a'] ?? $_POST['a'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !mc_csrf_check()) {
    $_SESSION['flash'] = ['type'=>'err','msg'=>'CSRF invalid']; mc_redirect('slides.php');
}

if ($action === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $files = $_FILES['files'] ?? null;
    $caption = trim($_POST['caption'] ?? '');
    $count = 0;
    if ($files && is_array($files['tmp_name'])) {
        $dir = __DIR__ . '/../assets/uploads/slideshow';
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        foreach ($files['tmp_name'] as $i => $tmp) {
            if (!$tmp || !is_uploaded_file($tmp)) continue;
            $name = $files['name'][$i];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $type = in_array($ext, ['mp4','webm','ogg','mov']) ? 'video'
                  : (in_array($ext, ['jpg','jpeg','png','webp','gif']) ? 'image' : null);
            if (!$type) continue;
            $newName = uniqid('slide_') . '.' . $ext;
            move_uploaded_file($tmp, $dir . '/' . $newName);
            $rel = 'assets/uploads/slideshow/' . $newName;
            $st = $pdo->prepare("INSERT INTO $T (type,path,caption,sort_order,is_active) VALUES (?,?,?,?,1)");
            $st->execute([$type, $rel, $caption ?: null, $count + (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM $T")->fetchColumn() + 1]);
            $count++;
        }
    }
    $_SESSION['flash'] = ['type' => $count?'ok':'err','msg' => $count? "$count file diunggah." : "Tidak ada file yang valid."];
    mc_redirect('slides.php');
}

if ($action === 'toggle' && isset($_GET['id'])) {
    $st = $pdo->prepare("UPDATE $T SET is_active = 1 - is_active WHERE id=?");
    $st->execute([(int)$_GET['id']]);
    mc_redirect('slides.php');
}
if ($action === 'delete' && isset($_GET['id'])) {
    $st = $pdo->prepare("SELECT path FROM $T WHERE id=?");
    $st->execute([(int)$_GET['id']]);
    $row = $st->fetch();
    if ($row && file_exists(__DIR__.'/../'.$row['path'])) @unlink(__DIR__.'/../'.$row['path']);
    $pdo->prepare("DELETE FROM $T WHERE id=?")->execute([(int)$_GET['id']]);
    mc_redirect('slides.php');
}
if ($action === 'reorder' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ((array)($_POST['order'] ?? []) as $i => $id) {
        $pdo->prepare("UPDATE $T SET sort_order=? WHERE id=?")->execute([(int)$i, (int)$id]);
    }
    $_SESSION['flash'] = ['type'=>'ok','msg'=>'Urutan diperbarui.'];
    mc_redirect('slides.php');
}

$rows = $pdo->query("SELECT * FROM $T ORDER BY sort_order,id")->fetchAll();

require __DIR__ . '/_layout.php';
?>
<h1>Slideshow</h1>

<div class="card">
    <h2>Unggah Foto / Video</h2>
    <form method="post" enctype="multipart/form-data">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="upload">
        <label>Pilih beberapa file (JPG/PNG/WebP/MP4/WebM)</label>
        <input type="file" name="files[]" accept="image/*,video/*" multiple required>
        <label>Caption (opsional)</label>
        <input type="text" name="caption" placeholder="mis. Bagian luar masjid">
        <p style="margin-top:14px"><button class="btn" type="submit">Unggah</button></p>
    </form>
</div>

<div class="card">
    <h2>Daftar Slide</h2>
    <?php if (!$rows): ?>
        <p style="color:#6b7280">Belum ada slide. Unggah dulu.</p>
    <?php else: ?>
    <form method="post" id="orderForm">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="reorder">
        <table>
            <thead><tr><th style="width:30px">#</th><th>Preview</th><th>Tipe</th><th>Caption</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody id="slideTable">
            <?php foreach ($rows as $r): ?>
                <tr data-id="<?= (int)$r['id'] ?>">
                    <td>≡ <input type="hidden" name="order[]" value="<?= (int)$r['id'] ?>"></td>
                    <td>
                        <?php if ($r['type']==='video'): ?>
                            <video class="thumb" muted><source src="<?= mc_e('../'.$r['path']) ?>"></video>
                        <?php else: ?>
                            <img class="thumb" src="<?= mc_e('../'.$r['path']) ?>">
                        <?php endif; ?>
                    </td>
                    <td><?= mc_e($r['type']) ?></td>
                    <td><?= mc_e($r['caption']) ?></td>
                    <td><span class="badge <?= $r['is_active']?'on':'off' ?>"><?= $r['is_active']?'Aktif':'Nonaktif' ?></span></td>
                    <td>
                        <a class="btn small secondary" href="?a=toggle&id=<?= (int)$r['id'] ?>">Toggle</a>
                        <a class="btn small danger" href="?a=delete&id=<?= (int)$r['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p style="margin-top:12px"><button class="btn" type="submit">Simpan Urutan</button></p>
    </form>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php';
