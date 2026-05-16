<?php
require_once __DIR__ . '/../includes/auth.php';
if (!mc_is_installed()) { header('Location: ../install.php'); exit; }
mc_require_login();

// no-cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$active = $active ?? '';
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$user = mc_user();
$masjidName = mc_setting('masjid_name', 'Masjid');
$cssVer = @filemtime(__DIR__ . '/../assets/css/admin.css') ?: time();
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= mc_e($pageTitle ?? 'Admin') ?> · <?= mc_e($masjidName) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= mc_e(mc_base_url()) ?>/assets/css/admin.css?v=<?= $cssVer ?>">
</head>
<body class="bg-slate-50 text-slate-800">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-slate-900 text-slate-300 sticky top-0 h-screen flex flex-col">
        <div class="px-5 py-5 border-b border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl font-black bg-amber-400 text-slate-900 shadow-lg">M</div>
                <div>
                    <div class="text-white font-bold text-sm">Muslim Clock</div>
                    <div class="text-xs text-slate-400 truncate max-w-[170px]"><?= mc_e($masjidName) ?></div>
                </div>
            </div>
        </div>

        <nav class="flex-1 py-4 overflow-y-auto">
            <?php
            $sections = [
                'Utama' => [
                    ['dashboard', 'Dashboard', 'index.php', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['settings',  'Identitas Masjid', 'settings.php', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['location',  'Lokasi & Jadwal',  'location.php', 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                ],
                'Konten' => [
                    ['slides',   'Slideshow',     'slides.php',  'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['running',  'Running Text',  'running.php', 'M4 6h16M4 10h16M4 14h16M4 18h16'],
                    ['imam',     'Jadwal Imam',   'imam.php',    'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['quran',    'Cuplikan Qur\'an', 'quran.php',  'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ],
                'Tampilan' => [
                    ['appearance', 'Tampilan & Tema', 'appearance.php', 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01'],
                    ['adzan',      'Adzan & Iqomah',  'adzan.php',      'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                ],
            ];
            foreach ($sections as $sectionName => $items): ?>
                <div class="px-5 mt-4 mb-1.5 text-[10px] font-bold uppercase tracking-[2px] text-slate-500"><?= $sectionName ?></div>
                <?php foreach ($items as [$key, $label, $url, $icon]): ?>
                    <a href="<?= mc_e($url) ?>" class="<?= $active === $key ? 'bg-amber-400/10 text-white border-l-2 border-amber-400' : 'text-slate-400 hover:text-white hover:bg-white/5 border-l-2 border-transparent' ?> flex items-center gap-3 px-5 py-2.5 text-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $icon ?>"/></svg>
                        <?= mc_e($label) ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <div class="border-t border-white/5 mt-6 pt-2">
                <a href="<?= mc_e(mc_base_url()) ?>/index.php" target="_blank" class="flex items-center gap-3 px-5 py-2.5 text-sm text-amber-300 hover:text-amber-200 hover:bg-white/5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Buka Layar
                </a>
            </div>
        </nav>

        <div class="px-5 py-4 border-t border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-xs font-bold text-amber-400">
                    <?= mc_e(strtoupper(substr($user['name'] ?? $user['username'], 0, 1))) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white text-xs font-semibold truncate"><?= mc_e($user['name'] ?? $user['username']) ?></div>
                    <a href="logout.php" class="text-[11px] text-slate-500 hover:text-amber-400">Keluar →</a>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 px-8 py-6 max-w-full">

        <!-- Page header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900"><?= mc_e($pageTitle ?? '') ?></h1>
                <?php if (!empty($pageSubtitle)): ?>
                    <p class="text-sm text-slate-500 mt-0.5"><?= mc_e($pageSubtitle) ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($pageActions)): ?>
                <div class="flex items-center gap-2"><?= $pageActions /* raw HTML allowed */ ?></div>
            <?php endif; ?>
        </div>

        <?php if ($flash): ?>
            <div class="mb-5 px-4 py-3 rounded-lg flex items-center gap-3 <?= $flash['type'] === 'ok' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <?php if ($flash['type'] === 'ok'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <?php else: ?>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <?php endif; ?>
                </svg>
                <?= mc_e($flash['msg']) ?>
            </div>
        <?php endif; ?>
