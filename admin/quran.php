<?php
$active = 'quran';
$pageTitle = "Cuplikan Al-Qur'an";
$pageSubtitle = 'Ayat yang ditampilkan di kaki layar (running marquee)';

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
    } elseif ($action === 'speed') {
        mc_setting_set('quran_arab_speed',  max(20, (int)($_POST['arab_speed']  ?? 50)));
        mc_setting_set('quran_trans_speed', max(20, (int)($_POST['trans_speed'] ?? 60)));
        $_SESSION['flash']=['type'=>'ok','msg'=>'Kecepatan running Qur\'an disimpan.'];
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

<div class="mc-card">
    <h2>Mode Sumber</h2>
    <form method="post">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="mode">
        <div class="space-y-3">
            <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50">
                <input type="radio" name="mode" value="auto" class="mt-1" <?= $mode==='auto'?'checked':'' ?>>
                <div>
                    <strong class="text-sm">Otomatis</strong>
                    <p class="text-xs text-slate-500 mt-1">Ambil ayat acak dari API <a href="https://alquran.cloud/" target="_blank" class="text-blue-700">alquran.cloud</a> (kurasi ayat motivasi).</p>
                </div>
            </label>
            <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50">
                <input type="radio" name="mode" value="manual" class="mt-1" <?= $mode==='manual'?'checked':'' ?>>
                <div>
                    <strong class="text-sm">Manual</strong>
                    <p class="text-xs text-slate-500 mt-1">Hanya gunakan daftar yang Anda kelola di bawah.</p>
                </div>
            </label>
        </div>
        <button class="mc-btn mt-4" type="submit">Simpan Mode</button>
    </form>
</div>

<div class="mc-card">
    <h2>Kecepatan Running Qur'an</h2>
    <p class="text-sm text-slate-500 mb-3">Durasi 1 putaran (detik). Lebih besar = lebih lambat. Direkomendasikan 40–80 detik untuk memberi waktu jamaah membaca.</p>
    <form method="post">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="speed">
        <div class="row-2">
            <div>
                <label class="mc-label">Arab + Referensi (gerak kanan → kiri)</label>
                <input type="number" name="arab_speed" min="20" max="180" class="mc-input" value="<?= mc_e(mc_setting('quran_arab_speed','50')) ?>">
            </div>
            <div>
                <label class="mc-label">Terjemahan (gerak kiri → kanan)</label>
                <input type="number" name="trans_speed" min="20" max="180" class="mc-input" value="<?= mc_e(mc_setting('quran_trans_speed','60')) ?>">
            </div>
        </div>
        <button class="mc-btn mt-4" type="submit">Simpan Kecepatan</button>
    </form>
</div>

<div class="mc-card">
    <h2>Tambah Cuplikan Manual</h2>
    <form method="post">
        <?= mc_csrf_field() ?>
        <input type="hidden" name="a" value="add">
        <label class="mc-label">Teks Arab</label>
        <textarea name="arabic" dir="rtl" style="font-size:20px;font-family:'Amiri',serif" class="mc-textarea"></textarea>
        <label class="mc-label">Terjemahan</label>
        <textarea name="translation" class="mc-textarea"></textarea>
        <label class="mc-label">Referensi (mis. QS. Al-Baqarah: 153)</label>
        <input type="text" name="reference" class="mc-input">
        <button class="mc-btn mt-4" type="submit">Tambah</button>
    </form>
</div>

<div class="mc-card">
    <h2>Daftar Cuplikan (<?= count($rows) ?>)</h2>
    <?php if (!$rows): ?>
        <p class="text-sm text-slate-500">Belum ada cuplikan manual.</p>
    <?php else: ?>
    <table class="mc-table">
        <thead><tr><th>Arab</th><th>Terjemahan</th><th>Referensi</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td dir="rtl" style="font-size:18px;font-family:'Amiri',serif;max-width:280px"><?= mc_e($r['arabic']) ?></td>
                <td class="text-sm max-w-sm"><?= mc_e($r['translation']) ?></td>
                <td class="text-xs text-slate-500"><?= mc_e($r['reference']) ?></td>
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
