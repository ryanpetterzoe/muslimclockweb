<?php
/**
 * Layout: Compact
 * Padat info: header, jam besar di tengah, jadwal full-width 2 baris,
 * tanpa slideshow. Cocok untuk monitor kecil / posisi sempit.
 */
?>
<style>
    .layout-compact {
        background: linear-gradient(180deg, #0d1424 0%, #050816 100%);
        position: relative;
    }
    .layout-compact::before {
        content: "";
        position: absolute; inset: 0;
        background:
          radial-gradient(circle at 50% 0%, color-mix(in srgb, var(--primary) 35%, transparent), transparent 50%);
        pointer-events: none;
    }
    .layout-compact .pcell {
        background: linear-gradient(160deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01));
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: all .3s;
    }
    .layout-compact .pcell.next {
        border-color: var(--accent);
        background: linear-gradient(160deg, color-mix(in srgb, var(--accent) 18%, transparent), color-mix(in srgb, var(--accent) 4%, transparent));
        box-shadow: 0 8px 30px -10px var(--accent);
    }
    .layout-compact .pcell.next [data-time] { color: var(--accent); }
    .layout-compact .pcell.next .label { color: var(--accent); }
    .layout-compact .info-tile {
        background: linear-gradient(160deg, rgba(255,255,255,0.04), rgba(255,255,255,0.01));
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 12px;
        padding: 14px 18px;
    }
</style>

<div class="layout-compact h-screen w-screen relative overflow-hidden">

    <div class="relative z-10 h-full grid" style="grid-template-rows: 80px auto auto auto 1fr auto auto;">

        <!-- HEADER -->
        <header class="flex items-center justify-between px-8 border-b" style="border-color: rgba(255,255,255,0.06);">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain rounded-xl">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-deep" style="background: var(--primary); color: var(--accent);">&#x262A;</div>
                <?php endif; ?>
                <div>
                    <div class="text-xl font-bold text-white"><?= mc_e($masjid) ?></div>
                    <div class="text-xs text-slate-400"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right">
                <div id="greg-date" class="text-sm font-bold text-white">—</div>
                <div id="hij-date" class="text-xs mt-0.5" style="color: var(--accent);">—</div>
            </div>
        </header>

        <!-- HERO: huge digital clock with analog beside -->
        <main class="px-8 py-6 grid items-center gap-8" style="grid-template-columns: auto 1fr auto;">
            <!-- analog clock -->
            <?php $clockSize='w-32 h-32'; include __DIR__ . '/_analog_clock.php'; ?>

            <!-- digital -->
            <div class="text-center">
                <div id="digital" class="font-digital font-black tracking-tight text-white leading-none drop-shadow-[0_8px_30px_rgba(0,0,0,0.7)]"
                     style="font-size: clamp(110px, 13vw, 180px);">
                    --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.4em;" class="align-top ml-2">--</span>
                </div>
            </div>

            <!-- next prayer pill -->
            <div class="info-tile w-56 text-center">
                <div class="text-[10px] uppercase tracking-[3px] text-slate-400 font-bold">Menuju</div>
                <div id="nextLabel" class="text-2xl font-extrabold uppercase mt-1" style="color: var(--accent);">—</div>
                <div id="nextCountdown" class="font-digital text-xl font-semibold text-white tabular-nums mt-1">--:--:--</div>
            </div>
        </main>

        <!-- 1st row prayers (3 cols) -->
        <section class="px-8">
            <div class="grid grid-cols-3 gap-3">
                <?php
                $prayers1 = [
                    ['fajr',     'Subuh',   'subuh'],
                    ['sunrise',  'Syuruq',  null],
                    ['dhuhr',    $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur'],
                ];
                foreach ($prayers1 as [$key, $label, $imamKey]):
                    $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                ?>
                <div class="prayer pcell" data-key="<?= $key ?>">
                    <div class="flex-1 min-w-0">
                        <div class="label text-xs uppercase tracking-[3px] font-bold text-slate-300"><?= mc_e($label) ?></div>
                        <?php if ($imam && $showImam): ?><div class="text-[10px] mt-0.5 truncate" style="color: var(--accent);">Imam: <?= mc_e($imam) ?></div><?php endif; ?>
                    </div>
                    <div class="font-digital text-3xl font-bold text-white tabular-nums" data-time>--:--</div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 2nd row prayers (3 cols) -->
        <section class="px-8 pt-3 pb-2">
            <div class="grid grid-cols-3 gap-3">
                <?php
                $prayers2 = [
                    ['asr',      'Ashar',   'ashar'],
                    ['maghrib',  'Maghrib', 'maghrib'],
                    ['isha',     'Isya',    'isya'],
                ];
                foreach ($prayers2 as [$key, $label, $imamKey]):
                    $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                ?>
                <div class="prayer pcell" data-key="<?= $key ?>">
                    <div class="flex-1 min-w-0">
                        <div class="label text-xs uppercase tracking-[3px] font-bold text-slate-300"><?= mc_e($label) ?></div>
                        <?php if ($imam && $showImam): ?><div class="text-[10px] mt-0.5 truncate" style="color: var(--accent);">Imam: <?= mc_e($imam) ?></div><?php endif; ?>
                    </div>
                    <div class="font-digital text-3xl font-bold text-white tabular-nums" data-time>--:--</div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- spacer (decorative) -->
        <div class="px-8 flex items-end pb-3">
            <!-- decorative line -->
            <div class="w-full h-px" style="background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--accent) 40%, transparent), transparent);"></div>
        </div>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
