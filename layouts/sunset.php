<?php
/**
 * Layout: Sunset
 * Gradient warna senja (oranye-merah-ungu), siluet menara masjid,
 * mood tenang sebelum maghrib. Jadwal sebagai pita horizontal.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-sunset {
        background: linear-gradient(180deg,
            #1a0a3e 0%,
            #4a1052 25%,
            #8a2052 50%,
            #d63a3a 75%,
            #f59e0b 95%,
            #fbbf24 100%);
        position: relative;
    }
    .layout-sunset::before {
        /* Sun glow */
        content: "";
        position: absolute;
        bottom: 18%;
        left: 50%;
        transform: translateX(-50%);
        width: 380px; height: 380px;
        background: radial-gradient(circle, rgba(255,220,150,0.6), rgba(255,180,80,0.3) 30%, transparent 70%);
        border-radius: 50%;
        filter: blur(20px);
        animation: sun-pulse 8s ease-in-out infinite;
        pointer-events: none;
    }
    @keyframes sun-pulse {
        0%, 100% { transform: translateX(-50%) scale(1); opacity: 0.8; }
        50%      { transform: translateX(-50%) scale(1.1); opacity: 1; }
    }
    .layout-sunset .silhouette {
        position: absolute;
        bottom: 22%;
        left: 0; right: 0;
        height: 90px;
        pointer-events: none;
        opacity: 0.95;
    }

    .layout-sunset .glass-warm {
        background: rgba(0,0,0,0.35);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(255,200,100,0.25);
        border-radius: 16px;
    }

    .layout-sunset .ribbon {
        background: rgba(0,0,0,0.55);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-top: 1px solid rgba(255,200,100,0.3);
        border-bottom: 1px solid rgba(255,200,100,0.3);
    }
    .layout-sunset .prayer-cell {
        text-align: center;
        padding: 14px 8px;
        position: relative;
    }
    .layout-sunset .prayer-cell + .prayer-cell {
        border-left: 1px solid rgba(255,200,100,0.15);
    }
    .layout-sunset .prayer-cell.next {
        background: linear-gradient(180deg, rgba(245,179,1,0.25), transparent);
    }
    .layout-sunset .prayer-cell.next [data-time] { color: #fde68a; text-shadow: 0 0 20px #f59e0b; }
    .layout-sunset .prayer-cell.next::after {
        content: "▼";
        position: absolute;
        bottom: -6px; left: 50%;
        transform: translateX(-50%);
        color: #fbbf24;
        font-size: 10px;
        text-shadow: 0 0 8px #f59e0b;
    }
</style>

<div class="layout-sunset h-screen w-screen relative overflow-hidden">

    <!-- Mosque silhouette -->
    <svg class="silhouette" viewBox="0 0 1200 90" preserveAspectRatio="none">
        <path d="M0,90 L0,60 L80,60 L80,40 Q90,30 100,40 L100,60 L150,60 L150,30 Q180,5 210,30 L210,60 L260,60 Q260,40 280,40 L280,20 Q300,0 320,20 L320,40 Q340,40 340,60 L390,60 L390,30 L410,15 L410,5 L420,5 L420,15 L440,30 L440,60 L490,60 Q490,40 510,40 L510,20 Q540,0 570,20 L570,40 Q590,40 590,60 L640,60 L640,30 Q670,5 700,30 L700,60 L750,60 L750,40 Q760,30 770,40 L770,60 L850,60 Q850,30 880,30 L880,15 L900,15 L900,30 Q930,30 930,60 L1000,60 L1000,40 Q1020,30 1040,40 L1040,60 L1100,60 L1100,40 L1120,40 L1120,60 L1200,60 L1200,90 Z"
              fill="#0a0218"/>
    </svg>

    <div class="relative z-10 h-full grid" style="grid-template-rows: auto 1fr auto auto auto;">

        <!-- HEADER -->
        <header class="flex items-center justify-between px-10 py-5 border-b border-white/10">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-14 h-14 object-contain rounded-xl">
                <?php else: ?>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl"
                         style="background: linear-gradient(135deg, #fbbf24, #d63a3a); box-shadow: 0 10px 30px rgba(0,0,0,0.4);">&#x262A;</div>
                <?php endif; ?>
                <div>
                    <div class="text-[10px] uppercase tracking-[5px] mb-1" style="color: #fde68a;">— Senja Suci —</div>
                    <div class="text-2xl font-extrabold text-white" style="text-shadow: 0 2px 8px rgba(0,0,0,0.5);"><?= mc_e($masjid) ?></div>
                    <div class="text-xs text-amber-100/80 mt-0.5"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right glass-warm px-5 py-2">
                <div id="greg-date" class="text-sm font-bold text-white">—</div>
                <div id="hij-date" class="text-xs mt-0.5" style="color: #fde68a;">—</div>
            </div>
        </header>

        <!-- BODY: huge clock centered with countdown -->
        <main class="flex flex-col items-center justify-center px-10 relative" style="padding-bottom: 32%;">
            <div class="text-center relative z-10">
                <div class="text-[10px] uppercase tracking-[8px] font-bold mb-4" style="color: #fde68a; text-shadow: 0 0 20px #f59e0b;">Bismillah</div>
                <div id="digital" class="font-digital font-black tracking-tight text-white leading-none"
                     style="font-size: clamp(120px, 15vw, 220px); text-shadow: 0 0 40px rgba(255,200,100,0.5), 0 8px 30px rgba(0,0,0,0.6);">
                    --<span style="color:#fde68a;">:</span>--<span style="color:#fde68a; font-size: 0.4em;" class="align-top ml-3">--</span>
                </div>
                <div class="mt-6 inline-flex items-center gap-3 px-6 py-2 rounded-full glass-warm">
                    <span class="text-[10px] uppercase tracking-[3px] font-bold text-amber-100">Menuju</span>
                    <span id="nextLabel" class="text-base font-extrabold uppercase" style="color: #fde68a;">—</span>
                    <span class="text-amber-200/40">·</span>
                    <span id="nextCountdown" class="font-digital text-base font-bold text-white tabular-nums">--:--:--</span>
                </div>
            </div>
        </main>

        <!-- Prayer ribbon -->
        <section class="ribbon grid grid-cols-6">
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
            <div class="prayer prayer-cell" data-key="<?= $key ?>">
                <div class="text-[10px] uppercase tracking-[3px] font-bold text-amber-100/90"><?= mc_e($label) ?></div>
                <div class="font-digital text-3xl font-black text-white tabular-nums mt-1" data-time>--:--</div>
                <?php if ($imam && $showImam): ?>
                    <div class="text-[9px] mt-0.5 truncate text-amber-100/70">Imam: <?= mc_e($imam) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </section>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
