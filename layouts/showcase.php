<?php
/**
 * Layout: Showcase
 * Slideshow GEDE 60% di atas (foto/video diutamakan), info panel
 * glass-overlay melayang di atas slideshow, jadwal di panel bawah elegan.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-showcase { background: #050810; position: relative; }
    .layout-showcase .stage {
        position: relative;
        overflow: hidden;
        background: #000;
    }
    .layout-showcase .stage::after {
        content: "";
        position: absolute; inset: 0;
        background:
            linear-gradient(180deg, rgba(0,0,0,0.4) 0%, transparent 30%, transparent 70%, rgba(5,8,16,0.95) 100%);
        pointer-events: none;
    }
    .layout-showcase .glass-card {
        background: rgba(15,20,40,0.5);
        backdrop-filter: blur(18px) saturate(140%);
        -webkit-backdrop-filter: blur(18px) saturate(140%);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 16px;
    }
    .layout-showcase .stage-overlay {
        position: absolute; z-index: 5;
    }
    .layout-showcase .stage-overlay.tl { top: 18px; left: 20px; right: 20px; }
    .layout-showcase .stage-overlay.bl { bottom: 22px; left: 20px; }
    .layout-showcase .stage-overlay.br { bottom: 22px; right: 20px; }

    .layout-showcase .ps-card {
        background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.02));
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        padding: 14px 16px;
        text-align: center;
        transition: all .3s;
    }
    .layout-showcase .ps-card.next {
        border-color: var(--accent);
        background: linear-gradient(180deg, color-mix(in srgb, var(--accent) 22%, transparent), color-mix(in srgb, var(--accent) 5%, transparent));
        box-shadow: 0 12px 40px -10px var(--accent);
        transform: translateY(-2px);
    }
    .layout-showcase .ps-card.next [data-time] { color: var(--accent); }
</style>

<div class="layout-showcase h-screen w-screen relative overflow-hidden grid" style="grid-template-rows: 60% 1fr auto auto;">

    <!-- TOP STAGE: huge slideshow -->
    <section class="stage">
        <div id="slideshow" class="absolute inset-0" style="<?= ((string)mc_setting('show_slideshow','1')==='0')?'display:none;background:#0a1a3c url(assets/img/default-bg.svg) center/cover;':'' ?>">
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

        <!-- TOP overlay: header -->
        <div class="stage-overlay tl flex items-center justify-between gap-4">
            <div class="glass-card px-5 py-3 flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain rounded-lg">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl"
                         style="background: var(--primary); color: var(--accent);">&#x262A;</div>
                <?php endif; ?>
                <div>
                    <div class="text-xl font-extrabold text-white leading-tight"><?= mc_e($masjid) ?></div>
                    <div class="text-xs text-slate-300/80"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="glass-card px-5 py-3 text-right">
                <div id="greg-date" class="text-sm font-bold text-white">—</div>
                <div id="hij-date" class="text-xs mt-0.5" style="color: var(--accent);">—</div>
            </div>
        </div>

        <!-- BOTTOM-LEFT overlay: huge clock -->
        <div class="stage-overlay bl">
            <div class="text-[10px] uppercase tracking-[5px] font-bold mb-2" style="color: var(--accent); text-shadow: 0 2px 10px rgba(0,0,0,0.7);">Now Showing</div>
            <div id="digital"
                 class="font-digital font-black tracking-tight text-white leading-none"
                 style="font-size: clamp(80px, 9vw, 140px);
                        text-shadow: 0 6px 30px rgba(0,0,0,0.9), 0 0 50px rgba(0,0,0,0.5);">
                --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.4em;" class="align-top ml-2">--</span>
            </div>
        </div>

        <!-- BOTTOM-RIGHT overlay: countdown -->
        <div class="stage-overlay br glass-card px-5 py-3 text-right">
            <div class="text-[9px] uppercase tracking-[3px] font-bold text-slate-300">Menuju</div>
            <div id="nextLabel" class="text-xl font-extrabold uppercase mt-1" style="color: var(--accent);">—</div>
            <div id="nextCountdown" class="font-digital text-lg font-bold text-white tabular-nums mt-0.5">--:--:--</div>
        </div>
    </section>

    <!-- BOTTOM PANEL: 6 prayer cards -->
    <section class="px-8 py-5 grid grid-cols-6 gap-3 items-center">
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
        <div class="prayer ps-card" data-key="<?= $key ?>">
            <div class="text-[10px] uppercase tracking-[3px] font-bold text-slate-300"><?= mc_e($label) ?></div>
            <div class="font-digital text-3xl font-black text-white tabular-nums mt-1" data-time>--:--</div>
            <?php if ($imam && $showImam): ?>
                <div class="text-[10px] mt-1 truncate" style="color: color-mix(in srgb, var(--accent) 80%, white);">Imam: <?= mc_e($imam) ?></div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </section>

    <?php include __DIR__ . '/_footer.php'; ?>
</div>
