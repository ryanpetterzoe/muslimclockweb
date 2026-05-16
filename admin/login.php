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
$masjidName = mc_setting('masjid_name', 'Masjid');
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Admin · <?= mc_e($masjidName) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>body { font-family: 'Inter', system-ui, sans-serif; }</style>
</head>
<body class="login-bg">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-amber-400 items-center justify-center text-blue-900 text-3xl font-black shadow-2xl">&#x262A;</div>
            <h1 class="text-white text-2xl font-bold mt-3">Muslim Clock Web</h1>
            <p class="text-slate-400 text-sm"><?= mc_e($masjidName) ?></p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-bold text-slate-900 mb-1">Masuk Admin</h2>
            <p class="text-sm text-slate-500 mb-6">Kelola tampilan layar masjid Anda.</p>

            <?php if ($err): ?>
                <div class="bg-red-50 text-red-700 text-sm rounded-lg px-4 py-3 mb-4 border border-red-100"><?= mc_e($err) ?></div>
            <?php endif; ?>

            <form method="post" class="space-y-4">
                <?= mc_csrf_field() ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Username</label>
                    <input type="text" name="username" autofocus required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
                <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 rounded-lg transition shadow-md mt-2">Masuk</button>
            </form>
        </div>

        <div class="text-center mt-4">
            <a href="../index.php" class="text-slate-400 text-xs hover:text-amber-400">← Lihat tampilan layar</a>
        </div>
    </div>
</body>
</html>
