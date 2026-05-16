<?php
/**
 * Layout: Cinema
 * Variabel yg tersedia: $masjid, $alamat, $logo, $slides, $running, $imamRows, $isFriday, $adzanMsg
 */
?>
<div class="layout-cinema h-screen w-screen grid bg-slate-950" style="grid-template-rows: 92px 1fr auto;">

    <!-- HEADER -->
    <header class="bg-gradient-to-r from-white via-slate-50 to-white text-slate-900 flex items-center justify-between px-8 border-b-4 shadow-soft" style="border-color: var(--accent);">
        <div class="flex items-center gap-4">
            <?php if ($logo): ?>
                <img src="<?= mc_e($logo) ?>" alt="logo" class="w-14 h-14 object-contain rounded-lg">
            <?php else: ?>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shadow-deep" style="background: var(--primary); color: var(--accent);">&#x262A;</div>
            <?php endif; ?>
            <div>
                <h1 class="text-3xl font-extrabold leading-tight">
                    <span style="color: var(--accent);">Masjid</span>
                    <strong class="text-slate-900"><?= mc_e(preg_replace('/^Masjid\s+/i','',$masjid)) ?></strong>
                </h1>
                <div class="text-sm text-slate-500"><?= mc_e($alamat) ?></div>
            </div>
        </div>
        <div class="text-right">
            <div id="greg-date" class="text-base font-bold text-slate-900">—</div>
            <div id="hij-date" class="text-sm font-semibold mt-0.5" style="color: var(--primary);">—</div>
        </div>
    </header>

    <!-- BODY: 3 columns -->
    <main class="grid min-h-0" style="grid-template-columns: 1fr 380px 360px;">

        <!-- LEFT: slideshow -->
        <section class="relative overflow-hidden bg-black">
            <div id="slideshow" class="absolute inset-0">
                <?php if (!$slides): ?>
                    <div class="slide active" style="background:#0a1a3c url('assets/img/default-bg.svg') center/cover"></div>
                <?php else: foreach ($slides as $i => $s): ?>
                    <?php if ($s['type']==='video'): ?>
                        <video class="slide<?= $i===0?' active':'' ?>" muted playsinline loop preload="metadata"><source src="<?= mc_e($s['path']) ?>"></video>
                    <?php else: ?>
                        <div class="slide<?= $i===0?' active':'' ?>" style="background-image:url('<?= mc_e($s['path']) ?>')"></div>
                    <?php endif; ?>
                <?php endforeach; endif; ?>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>
        </section>

        <!-- CENTER: clock -->
        <section class="bg-gradient-to-b from-slate-900 to-slate-950 flex flex-col items-center justify-center px-6 py-6 gap-5 relative">
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent to-transparent" style="--accent: var(--accent); background-image: linear-gradient(90deg, transparent, color-mix(in srgb, var(--accent) 40%, transparent), transparent);"></div>

            <?php include __DIR__ . '/_analog_clock.php'; ?>

            <div class="flex flex-col items-center gap-2 w-full">
                <div class="glass shadow-deep rounded-2xl px-4 py-5 w-full text-center overflow-hidden">
                    <div id="digital" class="font-digital font-bold tracking-wider text-white leading-none whitespace-nowrap" style="font-size: 60px;">
                        --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.45em;" class="align-top ml-2">--</span>
                    </div>
                </div>
                <div class="text-center mt-1">
                    <div class="text-[10px] uppercase tracking-[3px] text-slate-400 font-semibold">Menuju Sholat</div>
                    <div class="mt-1.5">
                        <span id="nextLabel" class="text-base font-bold uppercase tracking-wider" style="color: var(--accent);">—</span>
                        <span class="text-slate-500 mx-1">·</span>
                        <span id="nextCountdown" class="font-digital text-lg font-semibold text-white">--:--:--</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- RIGHT: prayer column -->
        <aside class="prayer-col flex flex-col">
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
            <div class="prayer flex-1 flex items-center justify-between px-5 border-b border-white/10 last:border-b-0" data-key="<?= $key ?>">
                <div>
                    <div class="text-xl font-bold uppercase tracking-wider text-white"><?= mc_e($label) ?></div>
                    <?php if ($imam): ?>
                        <div class="text-[11px] mt-0.5 font-medium" style="color: color-mix(in srgb, var(--accent) 80%, white);">Imam: <?= mc_e($imam) ?></div>
                    <?php endif; ?>
                </div>
                <div class="font-digital text-3xl font-bold text-white tabular-nums" data-time>--:--</div>
            </div>
            <?php endforeach; ?>
        </aside>
    </main>

    <!-- FOOTER -->
    <?php include __DIR__ . '/_footer.php'; ?>
</div>
