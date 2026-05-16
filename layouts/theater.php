<?php
/**
 * Layout: Theater
 * Slideshow FULLSCREEN sebagai background. Info (jam, jadwal, header)
 * floating di atasnya dengan glass overlay tipis. Imersif kayak nonton di bioskop.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-theater { background: #000; position: relative; }
    .layout-theater .vignette {
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse at center, transparent 30%, rgba(0,0,0,0.6) 90%),
            linear-gradient(180deg, rgba(0,0,0,0.55) 0%, transparent 25%, transparent 60%, rgba(0,0,0,0.7) 100%);
        pointer-events: none;
    }
    .layout-theater .glass-pill {
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(20px) saturate(140%);
        -webkit-backdrop-filter: blur(20px) saturate(140%);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 18px;
    }
    .layout-theater .float-prayer {
        background: rgba(0,0,0,0.55);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 10px 14px;
        text-align: center;
        transition: all .3s;
    }
    .layout-theater .float-prayer.next {
        border-color: var(--accent);
        background: linear-gradient(180deg, color-mix(in srgb, var(--accent) 28%, rgba(0,0,0,0.55)), rgba(0,0,0,0.55));
        box-shadow: 0 0 30px -6px var(--accent);
        transform: translateY(-3px);
    }
    .layout-theater .float-prayer.next [data-time] { color: var(--accent); }
</style>

<div class="layout-theater h-screen w-screen relative overflow-hidden">
    <!-- FULLSCREEN slideshow -->
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
    <div class="vignette"></div>

    <!-- Floating UI grid -->
    <div class="relative z-10 h-full grid p-6" style="grid-template-rows: auto 1fr auto auto; gap: 16px;">

        <!-- TOP HEADER (floating glass pill) -->
        <header class="glass-pill flex items-center justify-between px-6 py-3">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain rounded-lg">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl"
                         style="background: var(--primary); color: var(--accent);">&#x262A;</div>
                <?php endif; ?>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                        <span class="text-[9px] uppercase tracking-[4px] text-red-400 font-bold">LIVE</span>
                    </div>
                    <div class="text-xl font-extrabold text-white leading-tight"><?= mc_e($masjid) ?></div>
                    <div class="text-xs text-slate-300/80"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right">
                <div id="greg-date" class="text-sm font-bold text-white">—</div>
                <div id="hij-date" class="text-xs mt-0.5" style="color: var(--accent);">—</div>
            </div>
        </header>

        <!-- CENTER: floating digital clock -->
        <main class="flex items-end justify-center pb-4">
            <div class="text-center">
                <div id="digital"
                     class="font-digital font-black tracking-tight text-white leading-none"
                     style="font-size: clamp(120px, 14vw, 200px);
                            text-shadow: 0 8px 40px rgba(0,0,0,0.85), 0 0 80px rgba(0,0,0,0.5);">
                    --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.4em;" class="align-top ml-3">--</span>
                </div>
                <div class="mt-4 inline-flex items-center gap-3 px-6 py-2 glass-pill" style="border-radius: 999px;">
                    <span class="text-[10px] uppercase tracking-[3px] font-bold text-slate-300">Menuju</span>
                    <span id="nextLabel" class="text-base font-extrabold uppercase" style="color: var(--accent);">—</span>
                    <span class="text-slate-500">·</span>
                    <span id="nextCountdown" class="font-digital text-base font-bold text-white tabular-nums">--:--:--</span>
                </div>
            </div>
        </main>

        <!-- BOTTOM: floating prayer ribbon (6 cards) -->
        <section class="grid grid-cols-6 gap-2">
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
            <div class="prayer float-prayer" data-key="<?= $key ?>">
                <div class="text-[10px] uppercase tracking-[3px] font-bold text-slate-300"><?= mc_e($label) ?></div>
                <div class="font-digital text-2xl font-black text-white tabular-nums mt-0.5" data-time>--:--</div>
                <?php if ($imam && $showImam): ?>
                    <div class="text-[9px] mt-0.5 truncate" style="color: color-mix(in srgb, var(--accent) 80%, white);"><?= mc_e($imam) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </section>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
