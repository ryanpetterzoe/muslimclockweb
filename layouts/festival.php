<?php
/**
 * Layout: Festival
 * Slideshow fullscreen + animasi confetti/sparkle yang jatuh terus.
 * Cocok untuk Ramadhan, Idul Fitri, Idul Adha. Mood meriah.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-festival { background: #1a0a3e; position: relative; }
    .layout-festival .bg-stage {
        position: absolute; inset: 0;
        overflow: hidden;
    }
    .layout-festival .bg-stage::after {
        content: "";
        position: absolute; inset: 0;
        background:
            linear-gradient(180deg, rgba(245,179,1,0.12) 0%, transparent 30%, transparent 50%, rgba(0,0,0,0.7) 100%),
            radial-gradient(ellipse at 50% 50%, transparent 30%, rgba(0,0,0,0.5) 90%);
        pointer-events: none;
    }

    .layout-festival .confetti {
        position: absolute;
        width: 8px; height: 14px;
        opacity: 0.9;
        animation: fall linear infinite;
        will-change: transform;
        pointer-events: none;
    }
    @keyframes fall {
        0%   { transform: translateY(-30px) rotate(0deg);   opacity: 0; }
        10%  { opacity: 1; }
        100% { transform: translateY(110vh) rotate(720deg); opacity: 0.7; }
    }

    .layout-festival .sparkle {
        position: absolute;
        color: var(--accent);
        animation: sparkle 2.5s ease-in-out infinite;
        pointer-events: none;
        text-shadow: 0 0 12px var(--accent);
    }
    @keyframes sparkle {
        0%, 100% { opacity: 0; transform: scale(0.5); }
        50%      { opacity: 1; transform: scale(1.2); }
    }

    .layout-festival .glass-festival {
        background: rgba(0,0,0,0.55);
        backdrop-filter: blur(20px) saturate(150%);
        -webkit-backdrop-filter: blur(20px) saturate(150%);
        border: 2px solid color-mix(in srgb, var(--accent) 50%, transparent);
        border-radius: 18px;
        box-shadow:
            0 12px 40px rgba(0,0,0,0.5),
            0 0 60px color-mix(in srgb, var(--accent) 30%, transparent);
    }

    .layout-festival .ramadan-banner {
        background: linear-gradient(90deg, var(--accent), color-mix(in srgb, var(--accent) 50%, white), var(--accent));
        background-size: 200% 100%;
        animation: shine 3s linear infinite;
        color: #1a0a3e;
        font-weight: 900;
        letter-spacing: 5px;
        text-transform: uppercase;
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 11px;
        display: inline-block;
    }
    @keyframes shine {
        0%   { background-position: 200% 50%; }
        100% { background-position: -200% 50%; }
    }

    .layout-festival .lantern {
        position: absolute;
        width: 60px; height: 70px;
        background:
            radial-gradient(ellipse at 50% 50%, var(--accent), color-mix(in srgb, var(--accent) 50%, #b91c1c));
        border-radius: 50% / 60%;
        box-shadow: 0 0 40px color-mix(in srgb, var(--accent) 60%, transparent);
        animation: swing 4s ease-in-out infinite;
        transform-origin: top center;
    }
    .layout-festival .lantern::before {
        content: "";
        position: absolute;
        top: -10px; left: 50%;
        transform: translateX(-50%);
        width: 1px; height: 30px;
        background: rgba(255,255,255,0.4);
    }
    .layout-festival .lantern::after {
        content: "";
        position: absolute;
        bottom: -8px; left: 50%;
        transform: translateX(-50%);
        width: 12px; height: 8px;
        background: var(--accent);
        border-radius: 0 0 4px 4px;
    }
    @keyframes swing {
        0%, 100% { transform: rotate(-3deg); }
        50%      { transform: rotate(3deg); }
    }

    .layout-festival .prayer-pearl {
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid color-mix(in srgb, var(--accent) 30%, transparent);
        border-radius: 999px;
        padding: 12px 18px;
        text-align: center;
        transition: all .3s;
        position: relative;
    }
    .layout-festival .prayer-pearl.next {
        background: linear-gradient(180deg, color-mix(in srgb, var(--accent) 30%, rgba(0,0,0,0.6)), rgba(0,0,0,0.6));
        border-color: var(--accent);
        box-shadow: 0 0 30px var(--accent), inset 0 0 20px color-mix(in srgb, var(--accent) 20%, transparent);
        transform: scale(1.05);
    }
    .layout-festival .prayer-pearl.next [data-time] {
        color: var(--accent);
        text-shadow: 0 0 12px var(--accent);
    }
</style>

<div class="layout-festival h-screen w-screen relative overflow-hidden">

    <!-- BG slideshow -->
    <div class="bg-stage">
        <div id="slideshow" class="absolute inset-0" style="<?= ((string)mc_setting('show_slideshow','1')==='0')?'display:none;':'' ?>filter: brightness(0.55) saturate(1.2);">
            <?php if (!$slides): ?>
                <div class="slide active" style="background:#0a1a3c url('assets/img/default-bg.svg') center/cover"></div>
            <?php else: foreach ($slides as $i => $s): ?>
                <?php if ($s['type']==='video'): ?>
                    <video class="slide<?= $i===0?' active':'' ?> w-full h-full object-cover" autoplay muted playsinline loop preload="auto"><source src="<?= mc_e($s['path']) ?>" type="<?= mc_e(mc_video_mime($s['path'])) ?>"></video>
                <?php else: ?>
                    <div class="slide<?= $i===0?' active':'' ?>" style="background-image:url('<?= mc_e($s['path']) ?>')"></div>
                <?php endif; ?>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Lanterns -->
    <div class="lantern" style="top: 20px; left: 8%; animation-delay: 0s;"></div>
    <div class="lantern" style="top: 30px; right: 10%; animation-delay: -2s; transform-origin: top center;"></div>
    <div class="lantern" style="top: 15px; left: 50%; transform: translateX(-50%) scale(0.85); animation-delay: -1s;"></div>

    <!-- Confetti (30) -->
    <?php
    $colors = ['var(--accent)', '#fbbf24', '#fde047', '#22d3ee', '#fff', '#dc2626'];
    for ($i = 0; $i < 35; $i++):
        $left = rand(0, 100);
        $delay = rand(0, 60) / 10;
        $dur = rand(60, 130) / 10;
        $color = $colors[$i % count($colors)];
        $shape = ($i % 3 === 0) ? 'border-radius:50%;width:6px;height:6px;' : '';
    ?>
        <div class="confetti" style="left:<?= $left ?>%; background:<?= $color ?>; animation-delay:-<?= $delay ?>s; animation-duration:<?= $dur ?>s; <?= $shape ?>"></div>
    <?php endfor; ?>

    <!-- Sparkles (random positions) -->
    <?php for ($i = 0; $i < 15; $i++):
        $x = rand(5, 95); $y = rand(10, 80); $del = rand(0, 50) / 10;
        $sz = rand(14, 28);
    ?>
        <div class="sparkle" style="left:<?= $x ?>%; top:<?= $y ?>%; animation-delay:-<?= $del ?>s; font-size:<?= $sz ?>px;">✦</div>
    <?php endfor; ?>

    <!-- Foreground UI -->
    <div class="relative z-10 h-full grid p-6" style="grid-template-rows: auto 1fr auto auto; gap: 16px;">

        <!-- HEADER -->
        <header class="glass-festival flex items-center justify-between px-6 py-3">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain rounded-lg">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl"
                         style="background: var(--primary); color: var(--accent); box-shadow: 0 0 20px var(--accent);">&#x262A;</div>
                <?php endif; ?>
                <div>
                    <span class="ramadan-banner">✦ Ahlan Wa Sahlan ✦</span>
                    <div class="text-2xl font-extrabold text-white mt-1"><?= mc_e($masjid) ?></div>
                </div>
            </div>
            <div class="text-right">
                <div id="greg-date" class="text-sm font-bold text-white">—</div>
                <div id="hij-date" class="text-base mt-1 font-bold font-arabic" style="color: var(--accent); font-size: 18px;">—</div>
            </div>
        </header>

        <!-- CENTER: festive clock -->
        <main class="flex items-center justify-center">
            <div class="glass-festival px-12 py-8 text-center relative">
                <div style="position: absolute; top: -16px; left: 50%; transform: translateX(-50%); font-size: 24px; color: var(--accent); text-shadow: 0 0 20px var(--accent);">✦ ✧ ✦</div>
                <div class="text-[10px] uppercase tracking-[6px] font-bold mb-3" style="color: var(--accent);">— Marhaban —</div>
                <div id="digital" class="font-digital font-black tracking-tight text-white leading-none"
                     style="font-size: clamp(110px, 13vw, 200px);
                            text-shadow: 0 0 50px color-mix(in srgb, var(--accent) 70%, transparent), 0 8px 30px rgba(0,0,0,0.7);">
                    --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.4em;" class="align-top ml-3">--</span>
                </div>
                <div class="mt-5 flex items-center justify-center gap-3">
                    <span style="color: var(--accent); font-size: 14px;">✦</span>
                    <span class="text-[10px] uppercase tracking-[4px] font-bold text-slate-300">Menuju</span>
                    <span id="nextLabel" class="text-lg font-extrabold uppercase" style="color: var(--accent);">—</span>
                    <span class="text-slate-500">·</span>
                    <span id="nextCountdown" class="font-digital text-base font-bold text-white tabular-nums">--:--:--</span>
                    <span style="color: var(--accent); font-size: 14px;">✦</span>
                </div>
                <div style="position: absolute; bottom: -16px; left: 50%; transform: translateX(-50%); font-size: 24px; color: var(--accent); text-shadow: 0 0 20px var(--accent);">✦ ✧ ✦</div>
            </div>
        </main>

        <!-- BOTTOM: prayer pearls -->
        <section class="grid grid-cols-6 gap-3">
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
            <div class="prayer prayer-pearl" data-key="<?= $key ?>">
                <div class="text-[10px] uppercase tracking-[3px] font-bold" style="color: var(--accent);"><?= mc_e($label) ?></div>
                <div class="font-digital text-2xl font-black text-white tabular-nums mt-0.5" data-time>--:--</div>
                <?php if ($imam && $showImam): ?>
                    <div class="text-[9px] mt-0.5 truncate text-slate-300"><?= mc_e($imam) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </section>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
