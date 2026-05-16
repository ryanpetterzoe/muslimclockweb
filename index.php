<?php
require_once __DIR__ . '/includes/functions.php';

if (!mc_is_installed()) {
    header('Location: install.php');
    exit;
}

// No browser cache for HTML
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Cache-busting
$cssVer = @filemtime(__DIR__ . '/assets/css/screen.css') ?: time();
$jsVer  = @filemtime(__DIR__ . '/assets/js/clock.js') ?: time();
$buildVer = substr(md5("$cssVer-$jsVer"), 0, 7);

$pdo = mc_db();

$masjid     = mc_setting('masjid_name', 'Masjid');
$alamat     = mc_setting('masjid_address', '');
$logo       = mc_setting('masjid_logo', '');
$primary    = mc_setting('theme_primary', '#0a4ea3');
$accent     = mc_setting('theme_accent', '#f5b301');
$adzanMsg   = mc_setting('adzan_message', 'Saatnya Waktu Sholat');
$adzanDur   = (int)mc_setting('adzan_duration', '600');
$iqomahDur  = (int)mc_setting('iqomah_duration', '600');

// Layout & font
$layout     = mc_setting('layout', 'cinema');
$fontDisp   = mc_setting('font_display', 'Inter');
$fontDigi   = mc_setting('font_digital', 'Orbitron');

// Override via ?preview= (untuk admin live preview, tidak persisten)
$allowed = ['cinema','minimal','mosque','neon','classic','compact','aurora','magazine','stadium','frame','theater','showcase','split','polaroid','window','festival','portrait'];
if (isset($_GET['preview']) && in_array($_GET['preview'], $allowed, true)) {
    $layout = $_GET['preview'];
}
if (!in_array($layout, $allowed, true)) $layout = 'cinema';

$slides  = $pdo->query("SELECT * FROM " . mc_table('slides') . " WHERE is_active=1 ORDER BY sort_order,id")->fetchAll();
$running = $pdo->query("SELECT text FROM " . mc_table('running_text') . " WHERE is_active=1 ORDER BY sort_order,id")->fetchAll(PDO::FETCH_COLUMN);

$today = (int)date('w');
$imamRows = [];
$st = $pdo->prepare("SELECT * FROM " . mc_table('imam_schedule') . " WHERE day_of_week=:d");
$st->execute([':d' => $today]);
foreach ($st->fetchAll() as $r) $imamRows[$r['prayer']] = $r;

$isFriday = $today === 5;
$fontsUrl = mc_google_fonts_url([$fontDisp, $fontDigi, 'Amiri']);
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= mc_e($masjid) ?> — Jadwal Sholat</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php if ($fontsUrl): ?>
    <link href="<?= mc_e($fontsUrl) ?>" rel="stylesheet">
<?php endif; ?>
<link rel="stylesheet" href="assets/css/screen.css?v=<?= $cssVer ?>">
<style>
    :root {
        --primary: <?= mc_e($primary) ?>;
        --accent:  <?= mc_e($accent) ?>;
        --primary-dark:  color-mix(in srgb, <?= mc_e($primary) ?> 60%, black);
        --primary-light: color-mix(in srgb, <?= mc_e($primary) ?> 80%, white);
        --accent-shadow: color-mix(in srgb, <?= mc_e($accent) ?> 40%, transparent);
        --font-display: <?= mc_font_stack($fontDisp, 'display') ?>;
        --font-digital: <?= mc_font_stack($fontDigi, 'digital') ?>;
    }
</style>
</head>
<body data-adzan-msg="<?= mc_e($adzanMsg) ?>"
      data-adzan-dur="<?= (int)$adzanDur ?>"
      data-iqomah-dur="<?= (int)$iqomahDur ?>"
      data-show-analog="<?= ((string)mc_setting('show_analog','1') === '0') ? '0' : '1' ?>"
      data-show-slideshow="<?= ((string)mc_setting('show_slideshow','1') === '0') ? '0' : '1' ?>"
      data-show-running="<?= ((string)mc_setting('show_running','1') === '0') ? '0' : '1' ?>"
      data-show-quran="<?= ((string)mc_setting('show_quran','1') === '0') ? '0' : '1' ?>"
      data-show-countdown="<?= ((string)mc_setting('show_countdown','1') === '0') ? '0' : '1' ?>"
      data-show-imam="<?= ((string)mc_setting('show_imam','1') === '0') ? '0' : '1' ?>"
      data-quran-display="<?= mc_e(mc_setting('quran_display', 'marquee')) ?>">

<?php
$layoutFile = __DIR__ . '/layouts/' . $layout . '.php';
if (file_exists($layoutFile)) {
    include $layoutFile;
} else {
    include __DIR__ . '/layouts/cinema.php';
}
?>

<!-- Adzan overlay (shared across layouts) -->
<div id="adzanOverlay" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background: rgba(3,7,18,.96); backdrop-filter: blur(6px);">
    <div class="absolute inset-0" style="background: radial-gradient(circle at 30% 30%, color-mix(in srgb, var(--primary) 30%, transparent), transparent 60%), radial-gradient(circle at 70% 70%, color-mix(in srgb, var(--accent) 20%, transparent), transparent 60%);"></div>
    <div class="relative text-center text-white">
        <div class="text-7xl font-black tracking-[10px]" id="ovPrayer" style="color: var(--accent);">MAGHRIB</div>
        <div class="text-7xl font-extrabold mt-4 mb-10" id="ovMsg"><?= mc_e($adzanMsg) ?></div>
        <div class="font-digital font-black leading-none tabular-nums text-white" id="ovCount" style="font-size: 14rem;">10:00</div>
        <div class="text-3xl mt-4 tracking-[6px] uppercase" id="ovSub" style="color: color-mix(in srgb, var(--accent) 80%, white);">Berlangsung</div>
    </div>
    <div class="absolute top-6 right-8 text-slate-500 text-xs">tekan ESC untuk tutup</div>
</div>

<!-- Floating debug bar -->
<div id="debugBar">
    <button id="testAdzan" type="button">▶ Test Adzan</button>
    <span class="px-2 py-1 rounded" style="background: rgba(20,25,40,0.8);">build <?= $buildVer ?> · <?= mc_e($layout) ?></span>
</div>

<script>
window.MC_TODAY_IMAMS = <?= json_encode($imamRows, JSON_UNESCAPED_UNICODE) ?>;
window.MC_BASE  = <?= json_encode(mc_base_url()) ?>;
window.MC_BUILD = <?= json_encode($buildVer) ?>;
window.MC_LAYOUT= <?= json_encode($layout) ?>;
</script>
<script src="assets/js/clock.js?v=<?= $jsVer ?>"></script>
</body>
</html>
