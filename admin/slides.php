<?php
$active = 'slides';
$pageTitle = 'Slideshow';
$pageSubtitle = 'Foto/video yang akan tampil di area slideshow';

require __DIR__ . '/../includes/auth.php';
mc_require_login();

$pdo = mc_db();
$T = mc_table('slides');

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
    $pdo->prepare("UPDATE $T SET is_active = 1 - is_active WHERE id=?")->execute([(int)$_GET['id']]);
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

$rows = $pdo->query("SELECT * FROM $T ORDER BY sort_order,id")->fetchAll();

require __DIR__ . '/_layout.php';
?>

<div class="mc-card">
    <h2>Unggah Foto / Video</h2>
    <form method="post" enctype="multipart/form-data">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="upload">
        <label class="mc-label">Pilih beberapa file (JPG/PNG/WebP/MP4/WebM)</label>
        <input type="file" name="files[]" accept="image/*,video/*" multiple required class="mc-input">
        <label class="mc-label">Caption (opsional)</label>
        <input type="text" name="caption" class="mc-input" placeholder="mis. Bagian luar masjid">
        <button class="mc-btn mt-4" type="submit">Unggah</button>
    </form>
</div>

<div class="mc-card">
    <h2>Daftar Slide (<?= count($rows) ?>)</h2>
    <?php if (!$rows): ?>
        <p class="text-sm text-slate-500">Belum ada slide. Unggah dulu.</p>
    <?php else: ?>
    <table class="mc-table">
        <thead><tr><th>Preview</th><th>Tipe</th><th>Caption</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td>
                        <?php if ($r['type']==='video'): ?>
                            <video class="thumb" muted><source src="<?= mc_e('../'.$r['path']) ?>"></video>
                        <?php else: ?>
                            <img class="thumb" src="<?= mc_e('../'.$r['path']) ?>">
                        <?php endif; ?>
                    </td>
                    <td class="text-xs uppercase font-semibold text-slate-500"><?= mc_e($r['type']) ?></td>
                    <td class="text-sm"><?= mc_e($r['caption']) ?: '<span class="text-slate-400">—</span>' ?></td>
                    <td><span class="mc-badge <?= $r['is_active']?'on':'off' ?>"><?= $r['is_active']?'Aktif':'Off' ?></span></td>
                    <td class="text-right whitespace-nowrap">
                        <a class="mc-btn ghost small" href="?a=toggle&id=<?= (int)$r['id'] ?>">Toggle</a>
                        <a class="mc-btn danger small" href="?a=delete&id=<?= (int)$r['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/_footer.php';
