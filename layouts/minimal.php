<?php
/**
 * Layout: Minimal
 * Slideshow penuh background, jam digital BESAR di tengah,
 * jadwal sholat 6 kartu di bawah dengan shadow modern.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<div class="layout-minimal h-screen w-screen relative overflow-hidden" style="background:#05060d;">

    <!-- BG slideshow full -->
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
    <!-- dark blue veil -->
    <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(5,8,20,0.55) 0%, rgba(5,8,20,0.85) 60%, rgba(5,8,20,0.95) 100%);"></div>

    <!-- Foreground grid -->
    <div class="relative z-10 h-full grid" style="grid-template-rows: 80px 1fr auto auto;">

        <!-- HEADER -->
        <header class="flex items-center justify-between px-10">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain rounded-xl">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-soft" style="background: var(--primary); color: var(--accent);">&#x262A;</div>
                <?php endif; ?>
                <div>
                    <div class="text-xl font-bold tracking-tight">
                        <span style="color: var(--accent);">Masjid</span>
                        <span class="text-white"><?= mc_e(preg_replace('/^Masjid\s+/i','',$masjid)) ?></span>
                    </div>
                    <div class="text-xs text-slate-400"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right glass-dark rounded-xl px-4 py-2">
                <div id="greg-date" class="text-sm font-bold text-white">—</div>
                <div id="hij-date" class="text-xs mt-0.5" style="color: var(--accent);">—</div>
            </div>
        </header>

        <!-- HERO: huge digital clock + small analog -->
        <main class="flex flex-col items-center justify-center px-10 gap-6">
            <!-- Next prayer pill -->
            <div class="glass-dark rounded-full px-6 py-2 flex items-center gap-3 shadow-soft">
                <span class="w-2 h-2 rounded-full animate-pulse" style="background: var(--accent);"></span>
                <span class="text-[11px] uppercase tracking-[3px] text-slate-300 font-semibold">Menuju</span>
                <span id="nextLabel" class="font-bold uppercase" style="color: var(--accent);">—</span>
                <span class="text-slate-500">·</span>
                <span id="nextCountdown" class="font-digital font-semibold text-white">--:--:--</span>
            </div>

            <!-- Big digital clock -->
            <div class="text-center">
                <div id="digital" class="font-digital font-black tracking-[0.06em] text-white leading-none drop-shadow-[0_8px_30px_rgba(0,0,0,0.8)]"
                     style="font-size: clamp(120px, 16vw, 220px);">
                    --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.4em;" class="align-top ml-3">--</span>
                </div>
            </div>

            <!-- Small analog clock + label -->
            <div class="flex items-center gap-5">
                <?php include __DIR__ . '/_analog_clock.php'; ?>
                <div class="text-left">
                    <div class="text-[10px] uppercase tracking-[3px] text-slate-400 font-semibold">Waktu</div>
                    <div class="text-sm font-semibold text-white">Server Lokal</div>
                </div>
            </div>
        </main>

        <!-- Prayer cards row -->
        <section class="px-10 pb-6">
            <div class="grid grid-cols-6 gap-4">
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
                <div class="prayer prayer-card p-5 backdrop-blur-md" data-key="<?= $key ?>">
                    <div class="label text-xs uppercase tracking-[3px] font-bold text-slate-300"><?= mc_e($label) ?></div>
                    <div class="font-digital text-4xl font-bold text-white tabular-nums mt-2" data-time>--:--</div>
                    <?php if ($imam && $showImam): ?>
                        <div class="text-[10px] mt-2 truncate" style="color: var(--accent);">Imam: <?= mc_e($imam) ?></div>
                    <?php else: ?>
                        <div class="text-[10px] mt-2 text-slate-500">&nbsp;</div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- FOOTER -->
        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
