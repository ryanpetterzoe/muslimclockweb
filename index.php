<?php
require_once __DIR__ . '/includes/functions.php';

if (!mc_is_installed()) {
    header('Location: install.php');
    exit;
}

$pdo = mc_db();
$prefix = mc_table('');

$masjid     = mc_setting('masjid_name', 'Masjid');
$alamat     = mc_setting('masjid_address', '');
$logo       = mc_setting('masjid_logo', '');
$theme      = mc_setting('theme_preset', 'classic-blue');
$primary    = mc_setting('theme_primary', '#0a4ea3');
$accent     = mc_setting('theme_accent', '#f5b301');
$adzanMsg   = mc_setting('adzan_message', 'Saatnya Waktu Sholat');
$adzanDur   = (int)mc_setting('adzan_duration', '600');
$iqomahDur  = (int)mc_setting('iqomah_duration', '600');

$slides = $pdo->query("SELECT * FROM " . mc_table('slides') . " WHERE is_active=1 ORDER BY sort_order,id")->fetchAll();
$running = $pdo->query("SELECT text FROM " . mc_table('running_text') . " WHERE is_active=1 ORDER BY sort_order,id")->fetchAll(PDO::FETCH_COLUMN);

// Hari & Jadwal Imam (untuk hari ini)
$today = (int)date('w'); // 0=Min..6=Sab
$imamRows = [];
$st = $pdo->prepare("SELECT * FROM " . mc_table('imam_schedule') . " WHERE day_of_week=:d");
$st->execute([':d' => $today]);
foreach ($st->fetchAll() as $r) $imamRows[$r['prayer']] = $r;
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= mc_e($masjid) ?> — Jadwal Sholat</title>
<link rel="stylesheet" href="assets/css/screen.css?v=2">
<style>
    :root {
        --primary: <?= mc_e($primary) ?>;
        --accent:  <?= mc_e($accent) ?>;
    }
    .lt-text { animation-duration: <?= (int)mc_setting('running_text_speed','60') ?>s !important; }
</style>
</head>
<body data-adzan-msg="<?= mc_e($adzanMsg) ?>" data-adzan-dur="<?= (int)$adzanDur ?>" data-iqomah-dur="<?= (int)$iqomahDur ?>">

<div id="screen">
    <!-- HEADER -->
    <header class="topbar">
        <div class="masjid">
            <?php if ($logo): ?><img src="<?= mc_e($logo) ?>" alt="logo"><?php endif; ?>
            <div>
                <h1><span class="kw">Masjid</span> <strong><?= mc_e(preg_replace('/^Masjid\s+/i','',$masjid)) ?></strong></h1>
                <div class="addr"><?= mc_e($alamat) ?></div>
            </div>
        </div>
        <div class="hijri">
            <div class="greg" id="greg-date">—</div>
            <div class="hij" id="hij-date">—</div>
        </div>
    </header>

    <!-- BODY -->
    <main class="body">
        <!-- LEFT: slideshow + clock + ticker -->
        <section class="left">
            <div class="slideshow" id="slideshow">
                <?php if (!$slides): ?>
                    <div class="slide active" style="background:#0a1a3c url('assets/img/default-bg.svg') center/cover"></div>
                <?php else: foreach ($slides as $i => $s): ?>
                    <?php if ($s['type']==='video'): ?>
                        <video class="slide<?= $i===0?' active':'' ?>" muted playsinline loop preload="metadata">
                            <source src="<?= mc_e($s['path']) ?>">
                        </video>
                    <?php else: ?>
                        <div class="slide<?= $i===0?' active':'' ?>" style="background-image:url('<?= mc_e($s['path']) ?>')"></div>
                    <?php endif; ?>
                <?php endforeach; endif; ?>
            </div>

            <!-- Analog clock overlay -->
            <div class="clock-wrap">
                <div class="clock" id="analogClock">
                    <div class="hand hour" id="handH"></div>
                    <div class="hand minute" id="handM"></div>
                    <div class="hand second" id="handS"></div>
                    <div class="dot"></div>
                    <div class="brand"><?= mc_e($masjid) ?></div>
                    <?php for ($i=1;$i<=12;$i++): ?>
                        <div class="num n<?= $i ?>"><?= $i ?></div>
                    <?php endfor; ?>
                </div>
                <div class="digital" id="digitalClock">--:--:--</div>
            </div>

            <!-- Lower-third running text -->
            <div class="lowerthird">
                <div class="lt-time" id="ltGreg">—</div>
                <div class="lt-track">
                    <div class="lt-text" id="ltText">
                        <?php if ($running): ?>
                            <?= mc_e(implode('  •  ', $running)) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- RIGHT: prayer times -->
        <aside class="right">
            <div class="prayer" data-key="fajr">
                <div class="label">
                    الفجر
                    <?php if (!empty($imamRows['subuh']['imam_name'])): ?><div class="imam">Imam: <?= mc_e($imamRows['subuh']['imam_name']) ?></div><?php endif; ?>
                </div>
                <div class="time" data-time>--:--</div>
            </div>
            <div class="prayer" data-key="sunrise">
                <div class="label">الشروق</div>
                <div class="time" data-time>--:--</div>
            </div>
            <div class="prayer" data-key="dhuhr">
                <div class="label" id="dhuhrLabel">
                    <?php if ((int)date('w') === 5): ?>الجمعة<?php else: ?>الظهر<?php endif; ?>
                    <?php
                        $dh = (int)date('w') === 5 ? ($imamRows['jumat']['imam_name'] ?? '') : ($imamRows['dzuhur']['imam_name'] ?? '');
                    ?>
                    <?php if ($dh): ?><div class="imam">Imam: <?= mc_e($dh) ?></div><?php endif; ?>
                </div>
                <div class="time" data-time>--:--</div>
            </div>
            <div class="prayer" data-key="asr">
                <div class="label">
                    العصر
                    <?php if (!empty($imamRows['ashar']['imam_name'])): ?><div class="imam">Imam: <?= mc_e($imamRows['ashar']['imam_name']) ?></div><?php endif; ?>
                </div>
                <div class="time" data-time>--:--</div>
            </div>
            <div class="prayer" data-key="maghrib">
                <div class="label">
                    المغرب
                    <?php if (!empty($imamRows['maghrib']['imam_name'])): ?><div class="imam">Imam: <?= mc_e($imamRows['maghrib']['imam_name']) ?></div><?php endif; ?>
                </div>
                <div class="time" data-time>--:--</div>
            </div>
            <div class="prayer" data-key="isha">
                <div class="label">
                    العشاء
                    <?php if (!empty($imamRows['isya']['imam_name'])): ?><div class="imam">Imam: <?= mc_e($imamRows['isya']['imam_name']) ?></div><?php endif; ?>
                </div>
                <div class="time" data-time>--:--</div>
            </div>
        </aside>
    </main>

    <!-- FOOTER -->
    <footer class="bottom">
        <div class="quran">
            <div class="qa" id="quranArab">—</div>
            <div class="qt" id="quranTrans">—</div>
            <div class="qr" id="quranRef">—</div>
        </div>
        <div class="brandbar"><?= mc_e($masjid) ?> • Muslim Clock Web</div>
    </footer>

    <!-- ADZAN OVERLAY -->
    <div id="adzanOverlay" class="overlay hidden">
        <div class="ov-inner">
            <div class="ov-prayer" id="ovPrayer">MAGHRIB</div>
            <div class="ov-msg"><?= mc_e($adzanMsg) ?></div>
            <div class="ov-count" id="ovCount">10:00</div>
            <div class="ov-sub" id="ovSub">Berlangsung</div>
        </div>
    </div>
</div>

<!-- Imam schedule (today) -->
<script>
window.MC_TODAY_IMAMS = <?= json_encode($imamRows, JSON_UNESCAPED_UNICODE) ?>;
window.MC_BASE = <?= json_encode(mc_base_url()) ?>;
</script>
<script src="assets/js/clock.js?v=2"></script>
</body>
</html>
