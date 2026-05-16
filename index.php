<?php
require_once __DIR__ . '/includes/functions.php';

if (!mc_is_installed()) {
    header('Location: install.php');
    exit;
}

$pdo = mc_db();

$masjid     = mc_setting('masjid_name', 'Masjid');
$alamat     = mc_setting('masjid_address', '');
$logo       = mc_setting('masjid_logo', '');
$primary    = mc_setting('theme_primary', '#0a4ea3');
$accent     = mc_setting('theme_accent', '#f5b301');
$adzanMsg   = mc_setting('adzan_message', 'Saatnya Waktu Sholat');
$adzanDur   = (int)mc_setting('adzan_duration', '600');
$iqomahDur  = (int)mc_setting('iqomah_duration', '600');

$slides  = $pdo->query("SELECT * FROM " . mc_table('slides') . " WHERE is_active=1 ORDER BY sort_order,id")->fetchAll();
$running = $pdo->query("SELECT text FROM " . mc_table('running_text') . " WHERE is_active=1 ORDER BY sort_order,id")->fetchAll(PDO::FETCH_COLUMN);

// Hari & Jadwal Imam (untuk hari ini)
$today = (int)date('w'); // 0=Min..6=Sab
$imamRows = [];
$st = $pdo->prepare("SELECT * FROM " . mc_table('imam_schedule') . " WHERE day_of_week=:d");
$st->execute([':d' => $today]);
foreach ($st->fetchAll() as $r) $imamRows[$r['prayer']] = $r;

$isFriday = $today === 5;
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= mc_e($masjid) ?> — Jadwal Sholat</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Orbitron:wght@500;600;700;800;900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/screen.css?v=5">
<style>
    :root {
        --primary: <?= mc_e($primary) ?>;
        --accent:  <?= mc_e($accent) ?>;
        --primary-dark: color-mix(in srgb, <?= mc_e($primary) ?> 60%, black);
        --primary-light: color-mix(in srgb, <?= mc_e($primary) ?> 80%, white);
    }
    body { font-family: 'Inter', system-ui, sans-serif; }
    .font-digital { font-family: 'Orbitron', monospace; }
    .font-arabic  { font-family: 'Amiri', 'Traditional Arabic', serif; }
    .bg-primary   { background-color: var(--primary); }
    .bg-primary-dark { background-color: var(--primary-dark); }
    .text-accent  { color: var(--accent); }
    .bg-accent    { background-color: var(--accent); }
    .border-accent{ border-color: var(--accent); }
    .lt-text      { animation-duration: <?= (int)mc_setting('running_text_speed','60') ?>s !important; }

    /* gradient prayer column */
    .prayer-col {
        background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
    }
    /* glassmorphism untuk jam wrap */
    .glass {
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(255,255,255,0.12);
    }
</style>
</head>
<body class="bg-slate-950 text-white overflow-hidden h-screen w-screen"
      data-adzan-msg="<?= mc_e($adzanMsg) ?>"
      data-adzan-dur="<?= (int)$adzanDur ?>"
      data-iqomah-dur="<?= (int)$iqomahDur ?>">

<div id="screen" class="h-screen w-screen grid" style="grid-template-rows: 88px 1fr auto;">

    <!-- ========== HEADER ========== -->
    <header class="bg-gradient-to-r from-white via-slate-50 to-white text-slate-900 flex items-center justify-between px-8 border-b-4 border-accent">
        <div class="flex items-center gap-4">
            <?php if ($logo): ?>
                <img src="<?= mc_e($logo) ?>" alt="logo" class="w-14 h-14 object-contain">
            <?php else: ?>
                <div class="w-14 h-14 rounded-full bg-primary flex items-center justify-center text-accent text-2xl">&#x262A;</div>
            <?php endif; ?>
            <div>
                <h1 class="text-3xl font-extrabold leading-tight">
                    <span class="text-accent">Masjid</span>
                    <strong class="text-slate-900"><?= mc_e(preg_replace('/^Masjid\s+/i','',$masjid)) ?></strong>
                </h1>
                <div class="text-sm text-slate-500"><?= mc_e($alamat) ?></div>
            </div>
        </div>
        <div class="text-right">
            <div id="greg-date" class="text-base font-bold text-slate-900">—</div>
            <div id="hij-date"  class="text-sm font-semibold text-slate-500 mt-0.5">—</div>
        </div>
    </header>

    <!-- ========== BODY (3 kolom: slideshow | clock | prayer) ========== -->
    <main class="grid min-h-0" style="grid-template-columns: 1fr 360px 360px;">

        <!-- LEFT: slideshow -->
        <section class="relative overflow-hidden bg-black">
            <div id="slideshow" class="absolute inset-0">
                <?php if (!$slides): ?>
                    <div class="slide active absolute inset-0" style="background:#0a1a3c url('assets/img/default-bg.svg') center/cover"></div>
                <?php else: foreach ($slides as $i => $s): ?>
                    <?php if ($s['type']==='video'): ?>
                        <video class="slide<?= $i===0?' active':'' ?> absolute inset-0 w-full h-full object-cover" muted playsinline loop preload="metadata">
                            <source src="<?= mc_e($s['path']) ?>">
                        </video>
                    <?php else: ?>
                        <div class="slide<?= $i===0?' active':'' ?> absolute inset-0 bg-cover bg-center"
                             style="background-image:url('<?= mc_e($s['path']) ?>')"></div>
                    <?php endif; ?>
                <?php endforeach; endif; ?>
            </div>
            <!-- subtle dark overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>
        </section>

        <!-- CENTER: Clock column (analog + digital) -->
        <section class="bg-gradient-to-b from-slate-900 to-slate-950 flex flex-col items-center justify-center px-6 py-8 gap-6 relative">
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent"></div>

            <!-- Analog clock SVG (smaller, subtle) -->
            <div class="relative">
                <svg id="analog" viewBox="0 0 200 200" class="w-44 h-44 drop-shadow-[0_10px_30px_rgba(0,0,0,0.7)]">
                    <defs>
                        <radialGradient id="face" cx="50%" cy="35%" r="80%">
                            <stop offset="0%"  stop-color="#ffffff"/>
                            <stop offset="60%" stop-color="#f1f5f9"/>
                            <stop offset="100%" stop-color="#cbd5e1"/>
                        </radialGradient>
                        <linearGradient id="bezel" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%"  stop-color="var(--accent)"/>
                            <stop offset="100%" stop-color="var(--primary)"/>
                        </linearGradient>
                        <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
                            <feDropShadow dx="0" dy="2" stdDeviation="1.5" flood-opacity="0.35"/>
                        </filter>
                    </defs>

                    <!-- Outer bezel (gold gradient) -->
                    <circle cx="100" cy="100" r="98" fill="url(#bezel)"/>
                    <!-- Inner ring -->
                    <circle cx="100" cy="100" r="92" fill="var(--primary)"/>
                    <!-- Face -->
                    <circle cx="100" cy="100" r="86" fill="url(#face)"/>

                    <!-- Hour ticks -->
                    <g stroke="#0f172a" stroke-linecap="round">
                        <?php for ($i=0; $i<60; $i++):
                            $angle = $i * 6;
                            $isHour = $i % 5 === 0;
                            $r1 = $isHour ? 75 : 80;
                            $r2 = 84;
                            $sw = $isHour ? 2.2 : 0.8;
                            $x1 = 100 + $r1 * cos(deg2rad($angle - 90));
                            $y1 = 100 + $r1 * sin(deg2rad($angle - 90));
                            $x2 = 100 + $r2 * cos(deg2rad($angle - 90));
                            $y2 = 100 + $r2 * sin(deg2rad($angle - 90));
                        ?>
                        <line x1="<?= number_format($x1,2) ?>" y1="<?= number_format($y1,2) ?>"
                              x2="<?= number_format($x2,2) ?>" y2="<?= number_format($y2,2) ?>"
                              stroke-width="<?= $sw ?>"/>
                        <?php endfor; ?>
                    </g>

                    <!-- Hour numbers -->
                    <g fill="#0f172a" font-family="Inter, sans-serif" font-weight="800" font-size="11"
                       text-anchor="middle" dominant-baseline="central">
                        <?php for ($n=1; $n<=12; $n++):
                            $a = $n * 30;
                            $x = 100 + 65 * cos(deg2rad($a - 90));
                            $y = 100 + 65 * sin(deg2rad($a - 90));
                        ?>
                        <text x="<?= number_format($x,2) ?>" y="<?= number_format($y,2) ?>"><?= $n ?></text>
                        <?php endfor; ?>
                    </g>

                    <!-- Center accent -->
                    <circle cx="100" cy="100" r="32" fill="none" stroke="var(--accent)" stroke-width="0.6" opacity="0.5"/>

                    <!-- Hands -->
                    <g filter="url(#shadow)">
                        <!-- Hour hand -->
                        <rect id="handH" x="98.5" y="55" width="3" height="50" rx="1.5"
                              fill="#0f172a" transform-origin="100 100" transform="rotate(0)"/>
                        <!-- Minute hand -->
                        <rect id="handM" x="99" y="35" width="2" height="70" rx="1"
                              fill="#0f172a" transform-origin="100 100" transform="rotate(0)"/>
                        <!-- Second hand -->
                        <g id="handS" transform-origin="100 100" transform="rotate(0)">
                            <rect x="99.6" y="25" width="0.8" height="85" fill="#dc2626"/>
                            <rect x="99.4" y="100" width="1.2" height="18" fill="#dc2626"/>
                        </g>
                    </g>

                    <!-- Center dot -->
                    <circle cx="100" cy="100" r="3.5" fill="#dc2626"/>
                    <circle cx="100" cy="100" r="1.5" fill="#fff"/>
                </svg>
            </div>

            <!-- Digital clock + date (PRIMARY focus) -->
            <div class="flex flex-col items-center gap-3 w-full">
                <div class="glass rounded-2xl px-6 py-6 w-full text-center">
                    <div id="digital" class="font-digital font-bold tracking-[0.12em] text-white leading-none"
                         style="font-size: clamp(48px, 6vw, 84px);">
                        --:--<span class="text-accent ml-2 align-top" style="font-size:0.45em" id="digitalSec">--</span>
                    </div>
                </div>

                <!-- Next prayer countdown -->
                <div class="text-center mt-1">
                    <div class="text-[10px] uppercase tracking-[3px] text-slate-400 font-semibold">Menuju Sholat</div>
                    <div class="mt-1.5">
                        <span id="nextLabel" class="text-accent text-lg font-bold uppercase tracking-wider">—</span>
                        <span class="text-slate-500 mx-1">·</span>
                        <span id="nextCountdown" class="font-digital text-xl font-semibold text-white">--:--:--</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- RIGHT: Prayer times -->
        <aside class="prayer-col flex flex-col">
            <?php
            $prayers = [
                ['fajr',     'Subuh',   'subuh'],
                ['sunrise',  'Syuruq',  null],
                ['dhuhr',    $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur'],
                ['asr',      'Ashar',   'ashar'],
                ['maghrib',  'Maghrib', 'maghrib'],
                ['isha',     'Isya',    'isya'],
            ];
            foreach ($prayers as [$key, $label, $imamKey]):
                $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
            ?>
            <div class="prayer flex-1 flex items-center justify-between px-5 border-b border-white/10 last:border-b-0 transition" data-key="<?= $key ?>">
                <div>
                    <div class="text-xl font-bold uppercase tracking-wider text-white"><?= mc_e($label) ?></div>
                    <?php if ($imam): ?>
                        <div class="text-[11px] text-amber-200 mt-0.5 font-medium">Imam: <?= mc_e($imam) ?></div>
                    <?php endif; ?>
                </div>
                <div class="font-digital text-3xl font-bold text-white tabular-nums" data-time>--:--</div>
            </div>
            <?php endforeach; ?>
        </aside>
    </main>

    <!-- ========== FOOTER ========== -->
    <footer class="bg-black grid" style="grid-template-rows: auto auto 28px;">
        <!-- ROW 1: Quran verse (full width, prominent) -->
        <div class="bg-gradient-to-r from-primary-dark via-primary to-primary-dark border-t-2 border-accent/70 px-8 py-3 grid items-center"
             style="grid-template-columns: 1fr auto;">
            <div class="min-w-0 flex items-baseline gap-6">
                <div class="font-arabic text-white shrink-0" id="quranArab" dir="rtl"
                     style="font-size: clamp(22px, 2vw, 32px); line-height: 1.4;">—</div>
                <div class="text-amber-100 italic min-w-0 truncate hidden md:block" id="quranTrans"
                     style="font-size: clamp(13px, 1vw, 16px);">—</div>
            </div>
            <div class="text-accent font-bold text-sm whitespace-nowrap pl-4" id="quranRef">—</div>
        </div>

        <!-- ROW 2: Running text -->
        <div class="bg-slate-900 overflow-hidden flex items-center" style="height: 44px;">
            <div class="lt-text whitespace-nowrap inline-block pl-[100%] font-semibold text-white"
                 style="animation: ticker 60s linear infinite; font-size: clamp(14px, 1vw, 17px);">
                <?php if ($running): ?>
                    <?= mc_e(implode('   •   ', $running)) ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ROW 3: Brand bar -->
        <div class="bg-accent text-slate-900 text-center text-xs font-extrabold leading-7 tracking-[3px] uppercase">
            <?= mc_e($masjid) ?> · Muslim Clock Web
        </div>
    </footer>

    <!-- ========== ADZAN OVERLAY ========== -->
    <div id="adzanOverlay" class="hidden fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-sm flex items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-transparent to-accent/10"></div>
        <div class="relative text-center text-white">
            <div class="text-accent text-7xl font-black tracking-[10px]" id="ovPrayer">MAGHRIB</div>
            <div class="text-7xl font-extrabold mt-4 mb-10" id="ovMsg"><?= mc_e($adzanMsg) ?></div>
            <div class="font-digital font-black text-[14rem] leading-none tabular-nums text-white" id="ovCount">10:00</div>
            <div class="text-3xl text-amber-200 mt-4 tracking-[6px] uppercase" id="ovSub">Berlangsung</div>
        </div>
    </div>
</div>

<script>
window.MC_TODAY_IMAMS = <?= json_encode($imamRows, JSON_UNESCAPED_UNICODE) ?>;
window.MC_BASE = <?= json_encode(mc_base_url()) ?>;
</script>
<script src="assets/js/clock.js?v=5"></script>
</body>
</html>
