<?php
require_once __DIR__ . '/../includes/auth.php';
if (!mc_is_installed()) { header('Location: ../install.php'); exit; }
mc_require_login();

$active = $active ?? '';
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$user = mc_user();
$masjidName = mc_setting('masjid_name', 'Masjid');
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin — <?= mc_e($masjidName) ?></title>
<link rel="stylesheet" href="<?= mc_e(mc_base_url()) ?>/assets/css/admin.css?v=2">
</head>
<body>
<aside class="sidebar">
    <div class="brand">
        <div class="logo">MC</div>
        <div>
            <div class="t">Muslim Clock</div>
            <div class="s"><?= mc_e($masjidName) ?></div>
        </div>
    </div>
    <nav>
        <a href="index.php"      class="<?= $active==='dashboard'?'active':'' ?>">Dashboard</a>
        <a href="settings.php"   class="<?= $active==='settings'?'active':'' ?>">Identitas Masjid</a>
        <a href="location.php"   class="<?= $active==='location'?'active':'' ?>">Lokasi & Jadwal</a>
        <a href="slides.php"     class="<?= $active==='slides'?'active':'' ?>">Slideshow</a>
        <a href="running.php"    class="<?= $active==='running'?'active':'' ?>">Running Text</a>
        <a href="imam.php"       class="<?= $active==='imam'?'active':'' ?>">Jadwal Imam</a>
        <a href="adzan.php"      class="<?= $active==='adzan'?'active':'' ?>">Adzan & Iqomah</a>
        <a href="quran.php"      class="<?= $active==='quran'?'active':'' ?>">Cuplikan Al-Qur'an</a>
        <a href="theme.php"      class="<?= $active==='theme'?'active':'' ?>">Tema Warna</a>
        <a href="<?= mc_e(mc_base_url()) ?>/index.php" target="_blank" class="link-external">Lihat Layar ↗</a>
    </nav>
    <div class="user">
        <div><?= mc_e($user['name'] ?? $user['username']) ?></div>
        <a href="logout.php">Keluar</a>
    </div>
</aside>
<main class="content">
    <?php if ($flash): ?>
        <div class="flash <?= mc_e($flash['type']) ?>"><?= mc_e($flash['msg']) ?></div>
    <?php endif; ?>
