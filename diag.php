<?php
/**
 * Diagnostic page — bantu mendeteksi penyebab 500.
 * Buka: /diag.php
 * Hapus file ini setelah selesai debugging.
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Muslim Clock — Diagnostic</title>
<style>
body{font-family:system-ui,sans-serif;background:#0b1224;color:#e2e8f0;padding:30px;line-height:1.5}
h1{color:#f5b301}h2{color:#fff;margin-top:30px;border-bottom:1px solid #334;padding-bottom:6px}
table{border-collapse:collapse;width:100%;margin-top:8px}
td,th{padding:8px 12px;border-bottom:1px solid #1f2937;text-align:left;font-size:14px}
.ok{color:#22c55e;font-weight:700}.bad{color:#ef4444;font-weight:700}
code{background:#111827;padding:2px 6px;border-radius:4px;font-size:13px;color:#fbbf24}
pre{background:#000;padding:12px;border-radius:8px;overflow:auto;font-size:12px;color:#cbd5e1}
.btn{display:inline-block;background:#0a4ea3;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;margin-top:10px}
</style></head><body>
<h1>Muslim Clock Web — Diagnostic</h1>
<p>Halaman ini membantu mendeteksi penyebab Internal Server Error. <strong>Hapus file <code>diag.php</code> setelah selesai.</strong></p>

<h2>1. Lingkungan PHP</h2>
<table>
<tr><th>PHP Version</th><td><?= htmlspecialchars(PHP_VERSION) ?></td></tr>
<tr><th>SAPI</th><td><?= htmlspecialchars(PHP_SAPI) ?></td></tr>
<tr><th>OS</th><td><?= htmlspecialchars(PHP_OS) ?></td></tr>
<tr><th>Document Root</th><td><?= htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? '?') ?></td></tr>
<tr><th>Script Path</th><td><?= htmlspecialchars(__FILE__) ?></td></tr>
<tr><th>Server Software</th><td><?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? '?') ?></td></tr>
</table>

<h2>2. Ekstensi PHP</h2>
<table>
<?php foreach (['pdo_mysql','curl','mbstring','json','intl','openssl','fileinfo'] as $ext): ?>
<tr><td><?= $ext ?></td><td class="<?= extension_loaded($ext)?'ok':'bad' ?>">
<?= extension_loaded($ext) ? 'OK loaded' : 'MISSING' ?></td></tr>
<?php endforeach; ?>
</table>

<h2>3. Folder & Permission</h2>
<?php
$paths = [
    __DIR__.'/config'              => 'writable (utk install.php)',
    __DIR__.'/assets/uploads'      => 'writable (utk upload)',
    __DIR__.'/assets/uploads/cache'=> 'writable (utk cache JSON)',
    __DIR__.'/assets/uploads/slideshow'=> 'writable (utk slide)',
];
?>
<table>
<?php foreach ($paths as $p => $desc): ?>
<tr><td><code><?= htmlspecialchars($p) ?></code><br><small><?= $desc ?></small></td>
<td class="<?= is_writable($p)?'ok':'bad' ?>">
<?php
    if (!file_exists($p)) echo 'TIDAK ADA';
    elseif (!is_writable($p)) echo 'TIDAK writable';
    else echo 'OK writable';
?></td></tr>
<?php endforeach; ?>
</table>

<h2>4. Apache modules (jika info tersedia)</h2>
<?php if (function_exists('apache_get_modules')): ?>
<pre><?= htmlspecialchars(implode("\n", apache_get_modules())) ?></pre>
<?php else: ?>
<p>apache_get_modules() tidak tersedia (mungkin PHP jalan via FastCGI).</p>
<?php endif; ?>

<h2>5. .htaccess Test</h2>
<p>Apache 500 paling sering disebabkan oleh directive <code>.htaccess</code> yang ditolak <code>AllowOverride</code>.</p>
<p>Coba langkah ini:</p>
<ol>
<li>Rename file <code>.htaccess</code> sementara → <code>.htaccess.off</code>, lalu reload halaman utama.</li>
<li>Jika sudah TIDAK 500 → masalahnya di <code>.htaccess</code> / <code>AllowOverride</code> vhost.</li>
<li>Kalau MASIH 500 → cek error log Apache: <code>C:\xampp\apache\logs\error.log</code> baris paling akhir.</li>
</ol>

<h2>6. Status install</h2>
<?php
$cfgFile = __DIR__.'/config/config.php';
if (file_exists($cfgFile)) {
    echo '<p class="ok">config.php sudah ada.</p>';
    $cfg = @include $cfgFile;
    if (is_array($cfg) && !empty($cfg['app']['installed'])) {
        echo '<p class="ok">App ditandai installed.</p>';
        echo '<p><a class="btn" href="index.php">Buka tampilan</a> &nbsp; <a class="btn" href="admin/login.php">Login admin</a></p>';
    } else {
        echo '<p class="bad">config.php ada tapi belum lengkap. Buka <a href="install.php">install.php</a>.</p>';
    }
} else {
    echo '<p class="bad">Belum terinstall. <a class="btn" href="install.php">Jalankan installer</a></p>';
}
?>

<h2>7. Test koneksi sederhana</h2>
<p>PHP file ini berhasil dijalankan = artinya PHP <strong>tidak</strong> error. Jika halaman lain (<code>index.php</code>, <code>install.php</code>) tetap 500 padahal halaman ini OK, kemungkinan besar masalahnya di <code>.htaccess</code>.</p>

</body></html>
