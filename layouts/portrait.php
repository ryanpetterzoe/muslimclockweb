<?php
/**
 * Layout: Portrait
 * Slideshow vertical besar di kiri (rasio portrait), info modular di kanan.
 * Cocok untuk display landscape yang ingin nuansa majalah dengan foto besar.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-portrait { background: #06080f; position: relative; }
    .layout-portrait .portrait-stage {
        position: relative;
        overflow: hidden;
        background: #000;
        border-radius: 0 24px 24px 0;
        box-shadow: 8px 0 30px -10px rgba(0,0,0,0.7);
    }
    .layout-portrait .portrait-stage::after {
        content: "";
        position: absolute; inset: 0;
        background:
            linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.85) 100%);
        pointer-events: none;
    }
    .layout-portrait .stage-overlay {
        position: absolute;
        bottom: 24px; left: 24px; right: 24px;
        z-index: 5;
    }
    .layout-portrait .badge {
        background: var(--accent);
        color: var(--primary-dark);
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 3px;
        text-transform: uppercase;
        display: inline-block;
        box-shadow: 0 4px 14px color-mix(in srgb, var(--accent) 60%, transparent);
    }
    .layout-portrait .module {
        background: linear-gradient(160deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01));
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 16px;
        padding: 16px 18px;
    }
    .layout-portrait .module.accent {
        background: linear-gradient(160deg, color-mix(in srgb, var(--accent) 16%, transparent), color-mix(in srgb, var(--primary) 30%, transparent));
        border-color: color-mix(in srgb, var(--accent) 40%, transparent);
    }

    .layout-portrait .pr-row {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 12px;
        align-items: center;
        padding: 10px 14px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 10px;
        transition: all .3s;
    }
    .layout-portrait .pr-row.next {
        background: linear-gradient(90deg, color-mix(in srgb, var(--accent) 22%, transparent), transparent);
        border-color: var(--accent);
        box-shadow: 0 8px 24px -6px var(--accent);
    }
    .layout-portrait .pr-row.next [data-time] { color: var(--accent); }
    .layout-portrait .pr-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        background: rgba(255,255,255,0.05);
        color: var(--accent);
    }
</style>

<div class="layout-portrait h-screen w-screen relative overflow-hidden grid"
     style="grid-template-columns: 0.85fr 1fr; grid-template-rows: 1fr auto auto; gap: 0;">

    <!-- LEFT: full-height portrait stage (slideshow) -->
    <section class="portrait-stage" style="grid-row: 1;">
        <div id="slideshow" class="absolute inset-0" style="<?= ((string)mc_setting('show_slideshow','1')==='0')?'display:none;':'' ?>">
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

        <!-- Top brand strip -->
        <div class="absolute top-6 left-6 right-6 z-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain rounded-lg shadow-deep">
                <?php endif; ?>
                <span class="badge">PORTRAIT</span>
            </div>
            <div class="text-right">
                <div class="text-[9px] uppercase tracking-[4px] font-bold text-white/70 drop-shadow-md">— Live —</div>
            </div>
        </div>

        <!-- Bottom title overlay -->
        <div class="stage-overlay">
            <div class="text-[10px] uppercase tracking-[5px] font-bold mb-2" style="color: var(--accent); text-shadow: 0 2px 8px rgba(0,0,0,0.7);">— FEATURED —</div>
            <div class="text-3xl font-extrabold text-white leading-tight drop-shadow-[0_4px_12px_rgba(0,0,0,0.9)]"><?= mc_e($masjid) ?></div>
            <div class="text-sm text-slate-200/85 mt-1 drop-shadow-md"><?= mc_e($alamat) ?></div>
        </div>
    </section>

    <!-- RIGHT: modular info -->
    <aside class="grid p-6 gap-4 min-h-0" style="grid-template-rows: auto 1fr; grid-row: 1;">

        <!-- TOP: clock + date module -->
        <div class="module accent text-center py-6">
            <div class="text-[10px] uppercase tracking-[6px] font-bold mb-2" style="color: var(--accent);">⏱ NOW</div>
            <div id="digital" class="font-digital font-black tracking-tight text-white leading-none"
                 style="font-size: clamp(72px, 8.5vw, 120px); text-shadow: 0 4px 20px rgba(0,0,0,0.6);">
                --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.4em;" class="align-top ml-2">--</span>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-5 text-left">
                <div class="border-r border-white/10 pr-3">
                    <div class="text-[9px] uppercase tracking-[3px] text-slate-400 font-bold">Tanggal</div>
                    <div id="greg-date" class="text-sm font-bold text-white mt-1">—</div>
                    <div id="hij-date" class="text-xs mt-0.5" style="color: var(--accent);">—</div>
                </div>
                <div>
                    <div class="text-[9px] uppercase tracking-[3px] text-slate-400 font-bold">Berikutnya</div>
                    <div id="nextLabel" class="text-lg font-extrabold mt-1" style="color: var(--accent);">—</div>
                    <div id="nextCountdown" class="font-digital text-sm font-bold text-white mt-0.5">--:--:--</div>
                </div>
            </div>
        </div>

        <!-- BOTTOM: prayer list -->
        <div class="module">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] uppercase tracking-[4px] font-bold" style="color: var(--accent);">⬢ Jadwal Sholat</span>
                <span class="text-xs text-slate-500 font-arabic" style="font-size:14px;">مَوَاقِيتُ الصَّلاَةِ</span>
            </div>
            <div class="grid gap-2">
                <?php
                $prayers = [
                    ['fajr',     'Subuh',   'subuh',   '🌅'],
                    ['sunrise',  'Syuruq',  null,      '☀'],
                    ['dhuhr',    $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur', '🌞'],
                    ['asr',      'Ashar',   'ashar',   '🌇'],
                    ['maghrib',  'Maghrib', 'maghrib', '🌆'],
                    ['isha',     'Isya',    'isya',    '🌙'],
                ];
                foreach ($prayers as [$key, $label, $imamKey, $icon]):
                    $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                ?>
                <div class="prayer pr-row" data-key="<?= $key ?>">
                    <div class="pr-icon"><?= $icon ?></div>
                    <div class="min-w-0">
                        <div class="text-base font-bold uppercase tracking-wider text-white"><?= mc_e($label) ?></div>
                        <?php if ($imam && $showImam): ?>
                            <div class="text-[10px] mt-0.5 truncate" style="color: color-mix(in srgb, var(--accent) 75%, white);">Imam: <?= mc_e($imam) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="font-digital text-2xl font-bold text-white tabular-nums" data-time>--:--</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>

    <?php include __DIR__ . '/_footer.php'; ?>
</div>
