<?php
/**
 * Layout: Stadium
 * Papan skor LED stadion: jam digital BESAR ala scoreboard, jadwal seperti scoreboard,
 * tone gelap dengan dot-matrix grid.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-stadium {
        background: #02030a;
        position: relative;
    }
    .layout-stadium::before {
        content: "";
        position: absolute; inset: 0;
        /* Dot matrix grid */
        background-image:
            radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
        background-size: 14px 14px;
        pointer-events: none;
    }
    .layout-stadium .led-panel {
        background: linear-gradient(180deg, #0a0d18, #04050d);
        border: 2px solid #161a2c;
        border-radius: 14px;
        position: relative;
        box-shadow:
            inset 0 0 0 1px rgba(255,255,255,0.04),
            0 24px 60px -10px rgba(0,0,0,0.7);
    }
    .layout-stadium .led-panel::before {
        content: "";
        position: absolute; inset: 0;
        background-image:
            repeating-linear-gradient(0deg, transparent, transparent 3px, rgba(255,255,255,0.025) 4px);
        pointer-events: none;
        border-radius: 14px;
    }
    .layout-stadium .led-text {
        color: var(--accent);
        text-shadow:
            0 0 6px var(--accent),
            0 0 14px color-mix(in srgb, var(--accent) 60%, transparent),
            0 0 28px color-mix(in srgb, var(--accent) 30%, transparent);
    }
    .layout-stadium .led-text.white {
        color: #fff;
        text-shadow:
            0 0 6px #fff,
            0 0 14px rgba(255,255,255,0.6);
    }
    .layout-stadium .scoreboard-row {
        display: grid;
        grid-template-columns: 110px 1fr auto;
        align-items: center;
        gap: 14px;
        padding: 12px 18px;
        background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(0,0,0,0.2));
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 10px;
    }
    .layout-stadium .scoreboard-row.next {
        border-color: var(--accent);
        background: linear-gradient(180deg,
            color-mix(in srgb, var(--accent) 18%, transparent),
            color-mix(in srgb, var(--accent) 4%, transparent));
        box-shadow: 0 0 30px -8px var(--accent);
    }
    .layout-stadium .scoreboard-row.next [data-time] { color: var(--accent); }
    .layout-stadium .pcode {
        font-family: var(--font-digital, 'Orbitron', monospace);
        font-weight: 900;
        font-size: 12px;
        letter-spacing: 4px;
        color: rgba(255,255,255,0.5);
        background: rgba(0,0,0,0.4);
        border: 1px solid rgba(255,255,255,0.08);
        padding: 4px 10px;
        border-radius: 6px;
        text-align: center;
    }
    .layout-stadium .blink-dot {
        animation: blink 1.4s steps(2) infinite;
    }
    @keyframes blink { 50% { opacity: 0; } }
</style>

<div class="layout-stadium h-screen w-screen relative overflow-hidden grid" style="grid-template-rows: 80px 1fr auto auto; gap: 12px; padding: 16px;">

    <!-- HEADER -->
    <header class="led-panel flex items-center justify-between px-6">
        <div class="flex items-center gap-4 relative z-10">
            <?php if ($logo): ?>
                <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain rounded-lg">
            <?php else: ?>
                <div class="w-12 h-12 flex items-center justify-center text-2xl rounded-lg" style="background: var(--primary); color: var(--accent);">&#x262A;</div>
            <?php endif; ?>
            <div>
                <div class="led-text font-digital font-black text-xl tracking-[3px] uppercase"><?= mc_e($masjid) ?></div>
                <div class="text-xs text-slate-500 mt-0.5 font-mono"><?= mc_e($alamat) ?></div>
            </div>
        </div>
        <div class="text-right relative z-10">
            <div class="flex items-center gap-2 justify-end">
                <span class="w-2 h-2 rounded-full blink-dot" style="background:#dc2626; box-shadow: 0 0 8px #dc2626;"></span>
                <span class="text-[10px] uppercase tracking-[3px] font-bold text-red-500">LIVE</span>
            </div>
            <div id="greg-date" class="led-text white font-digital text-sm font-bold mt-1">—</div>
            <div id="hij-date" class="led-text font-digital text-xs mt-0.5">—</div>
        </div>
    </header>

    <!-- HERO: huge scoreboard digital + analog -->
    <main class="led-panel grid items-center px-8 gap-8" style="grid-template-columns: 1fr auto;">
        <div class="text-center relative z-10">
            <div class="text-[10px] uppercase tracking-[6px] font-bold mb-2" style="color: rgba(255,255,255,0.4);">⬢ MAIN CLOCK ⬢</div>
            <div id="digital" class="led-text white font-digital font-black tracking-tight leading-none"
                 style="font-size: clamp(140px, 17vw, 240px);">
                --<span class="led-text">:</span>--<span class="led-text" style="font-size: 0.4em;" class="align-top ml-3">--</span>
            </div>
            <div class="mt-5 inline-flex items-center gap-3 px-6 py-2 rounded"
                 style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1);">
                <span class="w-2 h-2 rounded-full blink-dot" style="background: var(--accent); box-shadow: 0 0 10px var(--accent);"></span>
                <span class="text-[10px] uppercase tracking-[3px] font-bold" style="color: rgba(255,255,255,0.5);">NEXT</span>
                <span id="nextLabel" class="led-text font-digital text-base font-bold uppercase">—</span>
                <span style="color: rgba(255,255,255,0.3);">·</span>
                <span id="nextCountdown" class="led-text white font-digital text-base font-bold">--:--:--</span>
            </div>
        </div>
        <div class="relative z-10 pl-4 border-l border-white/10">
            <?php $clockSize='w-44 h-44'; include __DIR__ . '/_analog_clock.php'; ?>
        </div>
    </main>

    <!-- SCOREBOARD: 6 prayers in 2 rows of 3 -->
    <section class="grid grid-cols-3 gap-3">
        <?php
        $prayers = [
            ['fajr',     'Subuh',   'subuh',   'P1'],
            ['sunrise',  'Syuruq',  null,      'P2'],
            ['dhuhr',    $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur', 'P3'],
            ['asr',      'Ashar',   'ashar',   'P4'],
            ['maghrib',  'Maghrib', 'maghrib', 'P5'],
            ['isha',     'Isya',    'isya',    'P6'],
        ];
        foreach ($prayers as [$key, $label, $imamKey, $code]):
            $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
        ?>
        <div class="prayer scoreboard-row" data-key="<?= $key ?>">
            <div class="pcode"><?= $code ?></div>
            <div class="min-w-0">
                <div class="led-text white font-digital text-base font-bold tracking-[2px] uppercase"><?= mc_e($label) ?></div>
                <?php if ($imam && $showImam): ?>
                    <div class="text-[10px] mt-0.5 truncate font-mono" style="color: rgba(255,255,255,0.4);">IMAM · <?= mc_e($imam) ?></div>
                <?php else: ?>
                    <div class="text-[10px] mt-0.5 font-mono" style="color: rgba(255,255,255,0.2);">—</div>
                <?php endif; ?>
            </div>
            <div class="led-text white font-digital text-3xl font-black tabular-nums" data-time>--:--</div>
        </div>
        <?php endforeach; ?>
    </section>

    <?php include __DIR__ . '/_footer.php'; ?>
</div>
