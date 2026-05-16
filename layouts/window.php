<?php
/**
 * Layout: Window
 * Slideshow ditampilkan di "jendela" tengah dengan border tebal & blurred BG yang
 * sama persis dari slideshow itu. Jadwal melingkari di sekeliling.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-window { background: #050810; position: relative; }
    .layout-window .bg-blur {
        position: absolute; inset: 0;
        overflow: hidden;
        z-index: 0;
    }
    .layout-window .bg-blur > #slideshowBg {
        position: absolute; inset: -40px;
        filter: blur(50px) brightness(0.45) saturate(1.3);
        transform: scale(1.1);
    }
    .layout-window .window-frame {
        position: relative;
        background: #000;
        border: 8px solid rgba(255,255,255,0.08);
        border-radius: 18px;
        box-shadow:
            0 30px 80px -10px rgba(0,0,0,0.7),
            inset 0 0 0 1px color-mix(in srgb, var(--accent) 60%, transparent),
            0 0 60px color-mix(in srgb, var(--accent) 20%, transparent);
        overflow: hidden;
    }
    .layout-window .window-frame::before {
        content: "";
        position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, transparent 25%, transparent 75%, rgba(0,0,0,0.6) 100%);
        pointer-events: none;
        z-index: 5;
    }
    .layout-window .window-frame .corner {
        position: absolute;
        width: 24px; height: 24px;
        border: 2px solid var(--accent);
        z-index: 6;
        opacity: 0.8;
    }
    .layout-window .window-frame .corner.tl { top: 8px;    left: 8px;    border-right: none; border-bottom: none; }
    .layout-window .window-frame .corner.tr { top: 8px;    right: 8px;   border-left:  none; border-bottom: none; }
    .layout-window .window-frame .corner.bl { bottom: 8px; left: 8px;    border-right: none; border-top:    none; }
    .layout-window .window-frame .corner.br { bottom: 8px; right: 8px;   border-left:  none; border-top:    none; }

    .layout-window .ring-card {
        background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(0,0,0,0.4));
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 14px;
        padding: 12px 14px;
        text-align: center;
        transition: all .3s;
    }
    .layout-window .ring-card.next {
        border-color: var(--accent);
        background: linear-gradient(180deg, color-mix(in srgb, var(--accent) 25%, transparent), color-mix(in srgb, var(--accent) 5%, transparent));
        box-shadow: 0 12px 30px -8px var(--accent), inset 0 0 18px color-mix(in srgb, var(--accent) 15%, transparent);
        transform: scale(1.04);
    }
    .layout-window .ring-card.next [data-time] { color: var(--accent); }
</style>

<div class="layout-window h-screen w-screen relative overflow-hidden">
    <!-- Blurred background slideshow (mirror of main one) -->
    <div class="bg-blur">
        <div id="slideshowBg" style="<?= ((string)mc_setting('show_slideshow','1')==='0')?'display:none;':'' ?>">
            <?php if (!$slides): ?>
                <div class="slide active" style="background:#0a1a3c url('assets/img/default-bg.svg') center/cover; position: absolute; inset: 0;"></div>
            <?php else: foreach ($slides as $i => $s): ?>
                <?php if ($s['type']==='video'): ?>
                    <video class="slide<?= $i===0?' active':'' ?>" autoplay muted playsinline loop preload="auto" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"><source src="<?= mc_e($s['path']) ?>" type="<?= mc_e(mc_video_mime($s['path'])) ?>"></video>
                <?php else: ?>
                    <div class="slide<?= $i===0?' active':'' ?>" style="background-image:url('<?= mc_e($s['path']) ?>'); position: absolute; inset: 0;"></div>
                <?php endif; ?>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <div class="relative z-10 h-full grid p-6" style="grid-template-rows: auto auto 1fr auto auto auto; gap: 14px;">

        <!-- HEADER -->
        <header class="flex items-center justify-between px-2">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain rounded-lg shadow-deep">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-deep"
                         style="background: var(--primary); color: var(--accent);">&#x262A;</div>
                <?php endif; ?>
                <div>
                    <div class="text-2xl font-extrabold text-white leading-tight drop-shadow-md"><?= mc_e($masjid) ?></div>
                    <div class="text-xs text-slate-300/80"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right">
                <div id="greg-date" class="text-base font-bold text-white drop-shadow-md">—</div>
                <div id="hij-date" class="text-sm mt-0.5 font-arabic" style="color: var(--accent); font-size: 18px;">—</div>
            </div>
        </header>

        <!-- TOP RING: 3 prayer cards (Subuh, Syuruq, Dzuhur) -->
        <section class="grid grid-cols-3 gap-3">
            <?php
            $topPrayers = [
                ['fajr',    'Subuh',  'subuh'],
                ['sunrise', 'Syuruq', null],
                ['dhuhr',   $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur'],
            ];
            foreach ($topPrayers as [$key, $label, $imamKey]):
                $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
            ?>
            <div class="prayer ring-card" data-key="<?= $key ?>">
                <div class="text-[10px] uppercase tracking-[3px] font-bold text-slate-300"><?= mc_e($label) ?></div>
                <div class="font-digital text-2xl font-black text-white tabular-nums mt-0.5" data-time>--:--</div>
                <?php if ($imam && $showImam): ?>
                    <div class="text-[9px] mt-0.5 truncate" style="color: color-mix(in srgb, var(--accent) 80%, white);">Imam: <?= mc_e($imam) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </section>

        <!-- CENTER: window with slideshow + clock overlay -->
        <main class="window-frame relative grid" style="grid-template-columns: 1fr; min-height: 0;">
            <span class="corner tl"></span>
            <span class="corner tr"></span>
            <span class="corner bl"></span>
            <span class="corner br"></span>

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

            <!-- Clock floating in window -->
            <div class="relative z-10 flex flex-col items-center justify-center text-center p-6">
                <div class="text-[10px] uppercase tracking-[6px] font-bold mb-2" style="color: var(--accent); text-shadow: 0 2px 12px rgba(0,0,0,0.9);">— LIVE TIME —</div>
                <div id="digital" class="font-digital font-black tracking-tight text-white leading-none"
                     style="font-size: clamp(110px, 13vw, 200px);
                            text-shadow: 0 8px 40px rgba(0,0,0,0.95), 0 0 80px rgba(0,0,0,0.6);">
                    --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.4em;" class="align-top ml-3">--</span>
                </div>
                <div class="mt-4 inline-flex items-center gap-3 px-5 py-2 rounded-full"
                     style="background: rgba(0,0,0,0.55); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.15);">
                    <span class="text-[10px] uppercase tracking-[3px] font-bold text-slate-300">Menuju</span>
                    <span id="nextLabel" class="text-base font-extrabold uppercase" style="color: var(--accent);">—</span>
                    <span class="text-slate-500">·</span>
                    <span id="nextCountdown" class="font-digital text-base font-bold text-white tabular-nums">--:--:--</span>
                </div>
            </div>
        </main>

        <!-- BOTTOM RING: 3 prayer cards (Ashar, Maghrib, Isya) -->
        <section class="grid grid-cols-3 gap-3">
            <?php
            $bottomPrayers = [
                ['asr',     'Ashar',   'ashar'],
                ['maghrib', 'Maghrib', 'maghrib'],
                ['isha',    'Isya',    'isya'],
            ];
            foreach ($bottomPrayers as [$key, $label, $imamKey]):
                $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
            ?>
            <div class="prayer ring-card" data-key="<?= $key ?>">
                <div class="text-[10px] uppercase tracking-[3px] font-bold text-slate-300"><?= mc_e($label) ?></div>
                <div class="font-digital text-2xl font-black text-white tabular-nums mt-0.5" data-time>--:--</div>
                <?php if ($imam && $showImam): ?>
                    <div class="text-[9px] mt-0.5 truncate" style="color: color-mix(in srgb, var(--accent) 80%, white);">Imam: <?= mc_e($imam) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </section>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
