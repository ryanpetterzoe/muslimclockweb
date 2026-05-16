<?php
/**
 * Layout: Aurora
 * Gradient aurora modern dengan animated glow blobs, glassmorphism, jadwal kartu vertikal.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-aurora {
        background: #050816;
        position: relative;
    }
    .layout-aurora .aurora-bg {
        position: absolute; inset: 0;
        overflow: hidden;
        pointer-events: none;
    }
    .layout-aurora .blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(90px);
        opacity: 0.55;
        animation: float 18s ease-in-out infinite;
    }
    .layout-aurora .blob.b1 { top: -10%; left: -5%; width: 540px; height: 540px; background: var(--primary); }
    .layout-aurora .blob.b2 { top: 30%;  right: -10%; width: 620px; height: 620px; background: var(--accent); animation-delay: -6s; opacity: .35; }
    .layout-aurora .blob.b3 { bottom: -15%; left: 30%; width: 700px; height: 700px; background: color-mix(in srgb, var(--primary) 70%, var(--accent)); animation-delay: -12s; opacity: .35; }
    @keyframes float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33%      { transform: translate(40px, -30px) scale(1.05); }
        66%      { transform: translate(-30px, 40px) scale(0.95); }
    }
    .layout-aurora::after {
        /* subtle grain */
        content: "";
        position: absolute; inset: 0;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='120'><filter id='n'><feTurbulence baseFrequency='0.9'/><feColorMatrix values='0 0 0 0 1  0 0 0 0 1  0 0 0 0 1  0 0 0 0.06 0'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>");
        opacity: 0.6;
        pointer-events: none;
        mix-blend-mode: overlay;
    }
    .layout-aurora .glass-pane {
        background: linear-gradient(135deg, rgba(255,255,255,0.07), rgba(255,255,255,0.02));
        border: 1px solid rgba(255,255,255,0.10);
        border-radius: 22px;
        backdrop-filter: blur(18px) saturate(140%);
        -webkit-backdrop-filter: blur(18px) saturate(140%);
        box-shadow: 0 30px 80px -20px rgba(0,0,0,0.6);
    }
    .layout-aurora .prayer-pill {
        display: grid;
        grid-template-columns: 64px 1fr auto;
        align-items: center;
        gap: 16px;
        padding: 14px 18px;
        border-radius: 16px;
        background: linear-gradient(120deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01));
        border: 1px solid rgba(255,255,255,0.07);
        transition: all .3s;
    }
    .layout-aurora .prayer-pill .pidx {
        width: 40px; height: 40px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 14px;
        background: rgba(255,255,255,0.06);
        color: color-mix(in srgb, var(--accent) 80%, white);
        border: 1px solid rgba(255,255,255,0.06);
    }
    .layout-aurora .prayer-pill.next {
        border-color: var(--accent);
        background: linear-gradient(120deg, color-mix(in srgb, var(--accent) 18%, transparent), color-mix(in srgb, var(--accent) 4%, transparent));
        box-shadow: 0 12px 40px -10px var(--accent);
    }
    .layout-aurora .prayer-pill.next .pidx {
        background: var(--accent);
        color: #0a0a14;
    }
    .layout-aurora .prayer-pill.next [data-time] { color: var(--accent); }
</style>

<div class="layout-aurora h-screen w-screen relative overflow-hidden">
    <div class="aurora-bg">
        <div class="blob b1"></div>
        <div class="blob b2"></div>
        <div class="blob b3"></div>
    </div>

    <!-- subtle slideshow -->
    <div id="slideshow" class="absolute inset-0 opacity-15" style="<?= ((string)mc_setting('show_slideshow','1')==='0')?'display:none;':'' ?>filter: blur(4px);">
        <?php if ($slides) foreach ($slides as $i => $s): ?>
            <?php if ($s['type']==='video'): ?>
                <video class="slide<?= $i===0?' active':'' ?> w-full h-full object-cover" autoplay muted playsinline loop preload="auto"><source src="<?= mc_e($s['path']) ?>" type="<?= mc_e(mc_video_mime($s['path'])) ?>"></video>
            <?php else: ?>
                <div class="slide<?= $i===0?' active':'' ?>" style="background-image:url('<?= mc_e($s['path']) ?>')"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="relative z-10 h-full grid" style="grid-template-rows: 88px 1fr auto auto;">

        <!-- HEADER -->
        <header class="flex items-center justify-between px-10 border-b" style="border-color: rgba(255,255,255,0.07);">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-14 h-14 object-contain rounded-xl">
                <?php else: ?>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl"
                         style="background: linear-gradient(135deg, var(--primary), color-mix(in srgb, var(--primary) 60%, var(--accent))); color: var(--accent); box-shadow: 0 10px 30px -5px var(--primary);">&#x262A;</div>
                <?php endif; ?>
                <div>
                    <div class="text-[10px] uppercase tracking-[5px] mb-1" style="color: var(--accent);">Aurora · Live</div>
                    <div class="text-2xl font-extrabold text-white"><?= mc_e($masjid) ?></div>
                    <div class="text-xs text-slate-400 mt-0.5"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right glass-pane px-5 py-2">
                <div id="greg-date" class="text-sm font-bold text-white">—</div>
                <div id="hij-date" class="text-xs mt-0.5" style="color: var(--accent);">—</div>
            </div>
        </header>

        <!-- BODY -->
        <main class="grid items-center px-10 gap-8" style="grid-template-columns: 1.15fr 1fr;">
            <!-- LEFT: huge digital + analog -->
            <div class="glass-pane p-8 text-center">
                <div class="text-[10px] uppercase tracking-[6px] text-slate-400 font-bold mb-3">Waktu Saat Ini</div>
                <div id="digital" class="font-digital font-black tracking-[0.04em] text-white leading-none drop-shadow-[0_10px_40px_rgba(0,0,0,0.7)]"
                     style="font-size: clamp(110px, 13vw, 200px);">
                    --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.42em;" class="align-top ml-3">--</span>
                </div>
                <div class="flex items-center justify-center gap-6 mt-6">
                    <?php $clockSize='w-32 h-32'; include __DIR__ . '/_analog_clock.php'; ?>
                    <div class="text-left">
                        <div class="text-[10px] uppercase tracking-[3px] text-slate-400 font-semibold">Menuju</div>
                        <div id="nextLabel" class="text-2xl font-extrabold uppercase mt-1" style="color: var(--accent);">—</div>
                        <div id="nextCountdown" class="font-digital text-xl font-semibold text-white tabular-nums mt-1">--:--:--</div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: prayer list (vertical) -->
            <div class="glass-pane p-5">
                <div class="flex items-center justify-between px-2 mb-3">
                    <span class="text-[10px] uppercase tracking-[3px] font-bold" style="color: var(--accent);">Jadwal Sholat Hari Ini</span>
                    <span class="text-xs text-slate-500 font-arabic" style="font-size:14px;">مَوَاقِيتُ الصَّلاَةِ</span>
                </div>
                <div class="grid gap-2">
                    <?php
                    $prayers = [
                        ['fajr',     'Subuh',   'subuh',   '01'],
                        ['sunrise',  'Syuruq',  null,      '02'],
                        ['dhuhr',    $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur', '03'],
                        ['asr',      'Ashar',   'ashar',   '04'],
                        ['maghrib',  'Maghrib', 'maghrib', '05'],
                        ['isha',     'Isya',    'isya',    '06'],
                    ];
                    foreach ($prayers as [$key, $label, $imamKey, $idx]):
                        $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                    ?>
                    <div class="prayer prayer-pill" data-key="<?= $key ?>">
                        <div class="pidx"><?= $idx ?></div>
                        <div class="min-w-0">
                            <div class="text-base font-bold uppercase tracking-wider text-white"><?= mc_e($label) ?></div>
                            <?php if ($imam && $showImam): ?>
                                <div class="text-[11px] mt-0.5 truncate" style="color: color-mix(in srgb, var(--accent) 70%, white);">Imam: <?= mc_e($imam) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="font-digital text-2xl font-bold text-white tabular-nums" data-time>--:--</div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
