<?php
/**
 * Layout: Geometric
 * Pattern Arabic geometric tessellation (8-point star) sebagai latar,
 * jam besar di tengah, jadwal di kartu hexagonal-feel.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-geometric {
        background: var(--primary-dark);
        position: relative;
    }
    .layout-geometric::before {
        content: "";
        position: absolute; inset: 0;
        background-image:
            radial-gradient(circle at 50% 50%, transparent 30%, var(--primary-dark) 80%),
            url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'><g fill='none' stroke='%23f5b301' stroke-opacity='0.18' stroke-width='1'><polygon points='60,10 70,40 100,40 76,58 86,88 60,70 34,88 44,58 20,40 50,40'/><polygon points='60,30 65,45 80,45 68,55 73,70 60,61 47,70 52,55 40,45 55,45'/><circle cx='60' cy='60' r='25'/><line x1='60' y1='10' x2='60' y2='110'/><line x1='10' y1='60' x2='110' y2='60'/></g></svg>");
        background-size: cover, 120px 120px;
        opacity: 0.6;
        pointer-events: none;
    }
    .layout-geometric .panel {
        background: rgba(0,0,0,0.45);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid color-mix(in srgb, var(--accent) 25%, transparent);
        border-radius: 12px;
    }
    .layout-geometric .panel-strong {
        background: rgba(0,0,0,0.6);
        border: 2px solid color-mix(in srgb, var(--accent) 50%, transparent);
        border-radius: 12px;
        position: relative;
    }
    .layout-geometric .panel-strong::before,
    .layout-geometric .panel-strong::after {
        content: "";
        position: absolute;
        width: 14px; height: 14px;
        border: 2px solid var(--accent);
    }
    .layout-geometric .panel-strong::before { top: -6px; left: -6px; border-right: none; border-bottom: none; }
    .layout-geometric .panel-strong::after  { bottom: -6px; right: -6px; border-left: none; border-top: none; }

    .layout-geometric .geo-card {
        background: linear-gradient(180deg, rgba(255,255,255,0.07), rgba(0,0,0,0.3));
        border: 1px solid color-mix(in srgb, var(--accent) 25%, transparent);
        border-radius: 8px;
        padding: 12px 14px;
        position: relative;
        overflow: hidden;
        transition: all .3s;
    }
    .layout-geometric .geo-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, var(--accent), transparent);
        opacity: 0.4;
    }
    .layout-geometric .geo-card.next {
        border-color: var(--accent);
        background: linear-gradient(180deg, color-mix(in srgb, var(--accent) 22%, transparent), color-mix(in srgb, var(--accent) 4%, transparent));
        box-shadow: 0 8px 30px -6px var(--accent);
    }
    .layout-geometric .geo-card.next [data-time] { color: var(--accent); }

    .layout-geometric .star8 {
        width: 44px; height: 44px;
        background: var(--accent);
        position: relative;
        margin: 0 auto;
        clip-path: polygon(50% 0%, 60% 35%, 95% 35%, 68% 57%, 78% 91%, 50% 70%, 22% 91%, 32% 57%, 5% 35%, 40% 35%);
        opacity: 0.85;
    }
</style>

<div class="layout-geometric h-screen w-screen relative overflow-hidden">

    <div class="relative z-10 h-full grid p-8" style="grid-template-rows: auto 1fr auto auto; gap: 16px;">

        <!-- HEADER -->
        <header class="panel flex items-center justify-between px-6 py-3">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain">
                <?php else: ?>
                    <div class="star8" style="width:38px;height:38px"></div>
                <?php endif; ?>
                <div>
                    <div class="text-2xl font-extrabold text-white"><?= mc_e($masjid) ?></div>
                    <div class="text-xs text-slate-400 mt-0.5"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right">
                <div id="greg-date" class="text-base font-bold text-white">—</div>
                <div id="hij-date" class="text-sm font-arabic mt-1" style="color: var(--accent); font-size:18px;">—</div>
            </div>
        </header>

        <!-- BODY -->
        <main class="grid items-center gap-6" style="grid-template-columns: 1fr 1.4fr;">
            <!-- LEFT: prayer grid 2x3 -->
            <div class="panel p-5">
                <div class="text-[10px] uppercase tracking-[4px] font-bold text-center mb-4" style="color: var(--accent);">⬢ مَوَاقِيتُ الصَّلاَةِ ⬢</div>
                <div class="grid grid-cols-2 gap-3">
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
                    <div class="prayer geo-card text-center" data-key="<?= $key ?>">
                        <div class="text-[10px] uppercase tracking-[2px] font-bold text-slate-300"><?= mc_e($label) ?></div>
                        <div class="font-digital text-2xl font-black text-white tabular-nums mt-1" data-time>--:--</div>
                        <?php if ($imam && $showImam): ?>
                            <div class="text-[9px] mt-0.5 truncate" style="color: color-mix(in srgb, var(--accent) 70%, white);"><?= mc_e($imam) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- RIGHT: huge clock + analog -->
            <div class="panel-strong p-8 text-center">
                <div class="flex items-center justify-center gap-4 mb-3">
                    <div class="star8" style="width:18px;height:18px;"></div>
                    <div class="text-[10px] uppercase tracking-[5px] font-bold" style="color: var(--accent);">Waktu Setempat</div>
                    <div class="star8" style="width:18px;height:18px;"></div>
                </div>
                <div id="digital" class="font-digital font-black tracking-tight text-white leading-none drop-shadow-[0_8px_30px_rgba(0,0,0,0.8)]"
                     style="font-size: clamp(100px, 12vw, 180px);">
                    --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.4em;" class="align-top ml-3">--</span>
                </div>
                <div class="flex items-center justify-center gap-8 mt-6 pt-4 border-t border-white/10">
                    <?php $clockSize='w-24 h-24'; include __DIR__ . '/_analog_clock.php'; ?>
                    <div class="text-left">
                        <div class="text-[10px] uppercase tracking-[3px] text-slate-400 font-bold">Berikutnya</div>
                        <div id="nextLabel" class="text-xl font-extrabold uppercase mt-1" style="color: var(--accent);">—</div>
                        <div id="nextCountdown" class="font-digital text-lg text-white tabular-nums mt-1">--:--:--</div>
                    </div>
                </div>
            </div>
        </main>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
