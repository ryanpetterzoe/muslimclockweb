<?php
/**
 * Muslim Clock Web - Installer Wizard
 * 3 langkah: 1) Persyaratan, 2) Database, 3) Akun Admin & Masjid.
 */
declare(strict_types=1);

session_start();
$step = max(1, min(4, (int)($_GET['step'] ?? 1)));
$errors = [];

$ROOT = __DIR__;
$CONFIG_FILE = $ROOT . '/config/config.php';

// Jika sudah terinstall, blok kecuali user paksa lewat ?force=1
if (file_exists($CONFIG_FILE)) {
    $cfg = require $CONFIG_FILE;
    if (!empty($cfg['app']['installed']) && empty($_GET['force'])) {
        http_response_code(403);
        die('<h2>Aplikasi sudah terinstall.</h2><p>Hapus file <code>config/config.php</code> untuk install ulang, atau buka <a href="index.php">aplikasi</a>.</p>');
    }
}

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// STEP 2: simpan DB & test koneksi
if ($step === 2 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['install']['db'] = [
        'host'   => trim($_POST['db_host'] ?? 'localhost'),
        'port'   => (int)($_POST['db_port'] ?? 3306),
        'name'   => trim($_POST['db_name'] ?? ''),
        'user'   => trim($_POST['db_user'] ?? 'root'),
        'pass'   => $_POST['db_pass'] ?? '',
        'prefix' => trim($_POST['db_prefix'] ?? 'mc_'),
    ];
    try {
        $db = $_SESSION['install']['db'];
        $pdo = new PDO("mysql:host={$db['host']};port={$db['port']};charset=utf8mb4", $db['user'], $db['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        // create db jika belum ada
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        header('Location: install.php?step=3');
        exit;
    } catch (Throwable $e) {
        $errors[] = 'Koneksi DB gagal: ' . $e->getMessage();
    }
}

// STEP 3: simpan admin & nama masjid, lalu finalize
if ($step === 3 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $masjid    = trim($_POST['masjid_name'] ?? '');
    $alamat    = trim($_POST['masjid_address'] ?? '');
    $username  = trim($_POST['admin_user'] ?? '');
    $name      = trim($_POST['admin_name'] ?? '');
    $password  = $_POST['admin_pass'] ?? '';
    $password2 = $_POST['admin_pass2'] ?? '';

    if ($masjid === '' || $username === '' || $password === '') $errors[] = 'Field wajib belum lengkap.';
    if ($password !== $password2) $errors[] = 'Password tidak sama.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
    if (empty($_SESSION['install']['db'])) $errors[] = 'Konfigurasi DB hilang, mulai dari Step 2.';

    if (!$errors) {
        try {
            $db = $_SESSION['install']['db'];
            $pdo = new PDO("mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4",
                $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            // Jalankan schema dengan prefix
            $sql = file_get_contents(__DIR__ . '/includes/schema.sql');
            $sql = str_replace('{{P}}', $db['prefix'], $sql);
            foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
                if ($stmt !== '') $pdo->exec($stmt);
            }

            // Insert admin
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $st = $pdo->prepare("INSERT INTO `{$db['prefix']}users` (username,password,name,role) VALUES (?,?,?,?)
                                  ON DUPLICATE KEY UPDATE password=VALUES(password), name=VALUES(name)");
            $st->execute([$username, $hash, $name ?: $username, 'admin']);

            // Default settings
            $defaults = [
                'masjid_name'        => $masjid,
                'masjid_address'     => $alamat,
                'masjid_logo'        => '',
                'location_city_id'   => '1301', // default Jakarta (myQuran/Aladhan handled in API)
                'location_city_name' => 'Jakarta',
                'location_lat'       => '-6.2',
                'location_lng'       => '106.816666',
                'calc_method'        => '20', // Aladhan: 20 = Kemenag Indonesia (KEMENAG)
                'theme_preset'       => 'classic-blue',
                'theme_primary'      => '#0a4ea3',
                'theme_accent'       => '#f5b301',
                'layout'             => 'cinema',  // cinema | minimal | mosque
                'font_display'       => 'Inter',
                'font_digital'       => 'Orbitron',
                'adzan_message'      => 'Saatnya Waktu Sholat',
                'adzan_duration'     => '600', // detik (10 menit)
                'iqomah_duration'    => '600', // detik
                'running_text_speed' => '60',
                'quran_mode'         => 'auto', // auto|manual
                'quran_arab_speed'   => '50',
                'quran_trans_speed'  => '60',
                'quran_display'      => 'marquee', // marquee | card
                'show_analog'        => '1',  // 1|0
                'show_seconds'       => '1',
            ];
            $stIns = $pdo->prepare("INSERT INTO `{$db['prefix']}settings` (`key`,`value`) VALUES (?,?)
                                    ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
            foreach ($defaults as $k => $v) $stIns->execute([$k, $v]);

            // Default running text
            $pdo->prepare("INSERT INTO `{$db['prefix']}running_text` (text,is_active,sort_order) VALUES (?,?,?)")
                ->execute(['Selamat datang di ' . $masjid . '. Mari makmurkan masjid dengan sholat berjamaah.', 1, 1]);

            // Default jadwal imam (kosong, biar admin isi)
            // Tulis file config.php
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $base   = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
            $secret = bin2hex(random_bytes(16));
            $cfgArr = [
                'db' => [
                    'host'    => $db['host'],
                    'port'    => (int)$db['port'],
                    'name'    => $db['name'],
                    'user'    => $db['user'],
                    'pass'    => $db['pass'],
                    'charset' => 'utf8mb4',
                    'prefix'  => $db['prefix'],
                ],
                'app' => [
                    'name'      => 'Muslim Clock',
                    'base_url'  => $base,
                    'timezone'  => 'Asia/Jakarta',
                    'secret'    => $secret,
                    'installed' => true,
                ],
            ];
            $php = "<?php\nreturn " . var_export($cfgArr, true) . ";\n";
            if (!is_dir(__DIR__ . '/config')) mkdir(__DIR__ . '/config', 0775, true);
            file_put_contents($CONFIG_FILE, $php);

            unset($_SESSION['install']);
            header('Location: install.php?step=4');
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Instalasi gagal: ' . $e->getMessage();
        }
    }
}

?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Muslim Clock Web — Installer</title>
<style>
    :root { --pri:#0a4ea3; --acc:#f5b301; --bg:#0b1224; --card:#fff; --muted:#6b7280; }
    * { box-sizing: border-box; }
    body { margin:0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        background: linear-gradient(135deg,#0b1224,#0a4ea3); min-height:100vh; color:#1f2937; }
    .wrap { max-width: 720px; margin: 40px auto; padding: 0 20px; }
    .card { background:#fff; border-radius:14px; box-shadow:0 12px 30px rgba(0,0,0,.25); padding:32px; }
    h1 { margin:0 0 6px; color:var(--pri); font-size:26px; }
    .sub { color:var(--muted); margin-bottom:22px; }
    .steps { display:flex; gap:8px; margin-bottom:24px; }
    .steps div { flex:1; padding:10px; text-align:center; border-radius:8px; background:#eef2f7; font-size:13px; color:#475569; }
    .steps div.active { background:var(--pri); color:#fff; }
    .steps div.done { background:#16a34a; color:#fff; }
    label { display:block; font-weight:600; margin:12px 0 6px; font-size:14px; }
    input[type=text], input[type=password], input[type=number] {
        width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px;
    }
    input:focus { outline:none; border-color:var(--pri); box-shadow:0 0 0 3px rgba(10,78,163,.15); }
    .row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .btn { background:var(--pri); color:#fff; border:none; padding:12px 22px; border-radius:8px;
        font-size:14px; font-weight:600; cursor:pointer; }
    .btn:hover { background:#083d80; }
    .btn-link { background:transparent; color:var(--pri); padding:12px 0; }
    .err { background:#fee2e2; color:#991b1b; padding:10px 14px; border-radius:8px; margin-bottom:14px; }
    .ok  { background:#dcfce7; color:#166534; padding:10px 14px; border-radius:8px; margin-bottom:14px; }
    .check { display:flex; justify-content:space-between; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; margin-bottom:8px; }
    .ok-pill { background:#16a34a; color:#fff; padding:2px 10px; border-radius:99px; font-size:12px; }
    .bad-pill{ background:#dc2626; color:#fff; padding:2px 10px; border-radius:99px; font-size:12px; }
    .footer { text-align:center; color:#cbd5e1; margin-top:14px; font-size:12px; }
    code { background:#f1f5f9; padding:2px 6px; border-radius:4px; font-size:13px; }
</style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Muslim Clock Web</h1>
        <div class="sub">Wizard Instalasi — siapkan jam sholat masjid Anda dalam 3 langkah.</div>

        <div class="steps">
            <div class="<?= $step>=1?'active':'' ?><?= $step>1?' done':'' ?>">1. Persyaratan</div>
            <div class="<?= $step>=2?'active':'' ?><?= $step>2?' done':'' ?>">2. Database</div>
            <div class="<?= $step>=3?'active':'' ?><?= $step>3?' done':'' ?>">3. Admin & Masjid</div>
            <div class="<?= $step>=4?'active':'' ?>">4. Selesai</div>
        </div>

        <?php foreach ($errors as $e): ?>
            <div class="err"><?= h($e) ?></div>
        <?php endforeach; ?>

        <?php if ($step === 1):
            $checks = [
                'PHP >= 7.4'        => version_compare(PHP_VERSION, '7.4.0', '>='),
                'PDO MySQL'         => extension_loaded('pdo_mysql'),
                'JSON ext'          => extension_loaded('json'),
                'cURL ext'          => extension_loaded('curl'),
                'mbstring ext'      => extension_loaded('mbstring'),
                'config/ writable'  => is_writable(__DIR__.'/config') || (is_dir(__DIR__.'/config') && @touch(__DIR__.'/config/.t') && @unlink(__DIR__.'/config/.t')),
                'uploads/ writable' => is_writable(__DIR__.'/assets/uploads'),
            ];
            $allOk = !in_array(false, $checks, true);
        ?>
            <p>Pastikan persyaratan berikut terpenuhi:</p>
            <?php foreach ($checks as $name => $ok): ?>
                <div class="check"><span><?= h($name) ?></span><span class="<?= $ok?'ok-pill':'bad-pill' ?>"><?= $ok?'OK':'GAGAL' ?></span></div>
            <?php endforeach; ?>
            <p style="margin-top:18px">
                <a class="btn" href="install.php?step=2" <?= $allOk?'':'style="background:#9ca3af;pointer-events:none"' ?>>Lanjut →</a>
            </p>

        <?php elseif ($step === 2):
            $d = $_SESSION['install']['db'] ?? ['host'=>'localhost','port'=>3306,'name'=>'muslimclock','user'=>'root','pass'=>'','prefix'=>'mc_'];
        ?>
            <p>Masukkan informasi MySQL/MariaDB Anda (XAMPP default: user <code>root</code>, password kosong).</p>
            <form method="post">
                <div class="row">
                    <div><label>Host</label><input type="text" name="db_host" value="<?= h($d['host']) ?>" required></div>
                    <div><label>Port</label><input type="number" name="db_port" value="<?= h($d['port']) ?>" required></div>
                </div>
                <label>Nama Database</label>
                <input type="text" name="db_name" value="<?= h($d['name']) ?>" required>
                <small style="color:#6b7280">Akan dibuat otomatis jika belum ada.</small>
                <div class="row">
                    <div><label>Username</label><input type="text" name="db_user" value="<?= h($d['user']) ?>" required></div>
                    <div><label>Password</label><input type="password" name="db_pass" value="<?= h($d['pass']) ?>"></div>
                </div>
                <label>Prefix Tabel</label>
                <input type="text" name="db_prefix" value="<?= h($d['prefix']) ?>">
                <p style="margin-top:18px"><button class="btn" type="submit">Test & Lanjut →</button>
                   <a class="btn-link" href="install.php?step=1">← Kembali</a></p>
            </form>

        <?php elseif ($step === 3): ?>
            <p>Buat akun admin dan informasi dasar masjid.</p>
            <form method="post">
                <label>Nama Masjid / Mushola</label>
                <input type="text" name="masjid_name" value="Masjid Nurul Himmah" required>
                <label>Alamat</label>
                <input type="text" name="masjid_address" placeholder="Jl. Contoh No. 1, Kota">
                <hr style="margin:20px 0;border:none;border-top:1px solid #e5e7eb">
                <div class="row">
                    <div><label>Username Admin</label><input type="text" name="admin_user" value="admin" required></div>
                    <div><label>Nama Lengkap</label><input type="text" name="admin_name" value="Administrator"></div>
                </div>
                <div class="row">
                    <div><label>Password</label><input type="password" name="admin_pass" required></div>
                    <div><label>Ulangi Password</label><input type="password" name="admin_pass2" required></div>
                </div>
                <p style="margin-top:18px"><button class="btn" type="submit">Install Sekarang →</button>
                   <a class="btn-link" href="install.php?step=2">← Kembali</a></p>
            </form>

        <?php elseif ($step === 4): ?>
            <div class="ok">Instalasi berhasil!</div>
            <p>Aplikasi siap digunakan. Silakan login ke admin untuk mengatur slideshow, lokasi, jadwal imam, dan tema.</p>
            <p style="margin-top:18px">
                <a class="btn" href="admin/login.php">Masuk Admin</a>
                <a class="btn-link" href="index.php">Buka Tampilan Layar →</a>
            </p>
            <p style="color:#dc2626;margin-top:14px"><strong>Penting:</strong> Hapus file <code>install.php</code> dari server untuk keamanan.</p>
        <?php endif; ?>
    </div>
    <div class="footer">© Muslim Clock Web</div>
</div>
</body>
</html>
