<?php
require_once __DIR__ . '/../includes/auth.php';
if (!mc_is_installed()) { header('Location: ../install.php'); exit; }
mc_start_session();

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mc_csrf_check()) {
        $err = 'CSRF token tidak valid.';
    } else {
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password'] ?? '';
        if (mc_login($u, $p)) {
            mc_redirect('index.php');
        } else {
            $err = 'Username atau password salah.';
        }
    }
}
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Admin</title>
<link rel="stylesheet" href="<?= mc_e(mc_base_url()) ?>/assets/css/admin.css?v=2">
<style>
    body { display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg,#0b2447,#0a4ea3); }
    .sidebar { display: none; }
</style>
</head>
<body>
<div class="login-wrap">
    <div class="card">
        <h1>Muslim Clock — Admin</h1>
        <?php if ($err): ?><div class="flash err"><?= mc_e($err) ?></div><?php endif; ?>
        <form method="post">
            <?= mc_csrf_field() ?>
            <label>Username</label>
            <input type="text" name="username" autofocus required>
            <label>Password</label>
            <input type="password" name="password" required>
            <p style="margin-top:18px"><button class="btn" type="submit" style="width:100%">Masuk</button></p>
        </form>
    </div>
</div>
</body>
</html>
