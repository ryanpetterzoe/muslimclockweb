<?php
/**
 * Layout: Mosque
 * Tema masjid: ornamen islamic, jadwal grid 6 kartu, jam digital sentral.
 */
?>
<div class="layout-mosque h-screen w-screen relative overflow-hidden ornament-pattern">

    <!-- soft glow corners -->
    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full opacity-30 blur-3xl pointer-events-none" style="background: var(--accent);"></div>
    <div class="absolute -bottom-32 -right-32 w-[500px] h-[500px] rounded-full opacity-25 blur-3xl pointer-events-none" style="background: var(--primary);"></div>

    <!-- bg slideshow at low opacity -->
    <div id="slideshow" class="absolute inset-0 opacity-25">
        <?php if ($slides) foreach ($slides as $i => $s): ?>
            <?php if ($s['type']==='video'): ?>
                <video class="slide<?= $i===0?' active':'' ?>" muted playsinline loop preload="metadata"><source src="<?= mc_e($s['path']) ?>"></video>
            <?php else: ?>
                <div class="slide<?= $i===0?' active':'' ?>" style="background-image:url('<?= mc_e($s['path']) ?>')"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="relative z-10 h-full grid" style="grid-template-rows: 100px 1fr auto auto;">

        <!-- HEADER with star ornaments -->
        <header class="flex items-center justify-between px-10 border-b" style="border-color: color-mix(in srgb, var(--accent) 30%, transparent);">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-16 h-16 object-contain">
                <?php else: ?>
                    <svg viewBox="0 0 100 100" class="w-16 h-16">
                        <defs>
                            <linearGradient id="domeG" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="var(--accent)"/>
                                <stop offset="100%" stop-color="var(--primary)"/>
                            </linearGradient>
                        </defs>
                        <path d="M50 15 L52 25 Q70 30 70 55 L70 80 L30 80 L30 55 Q30 30 48 25 Z" fill="url(#domeG)"/>
                        <circle cx="50" cy="12" r="3" fill="var(--accent)"/>
                        <line x1="50" y1="9" x2="50" y2="3" stroke="var(--accent)" stroke-width="1.5"/>
                    </svg>
                <?php endif; ?>
                <div>
                    <div class="text-2xl font-extrabold tracking-tight text-white">
                        <span style="color: var(--accent);">Masjid</span>
                        <?= mc_e(preg_replace('/^Masjid\s+/i','',$masjid)) ?>
                    </div>
                    <div class="text-sm text-slate-400 mt-0.5"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right">
                <div id="greg-date" class="text-base font-bold text-white">—</div>
                <div id="hij-date" class="text-sm font-semibold mt-0.5" style="color: var(--accent);">—</div>
            </div>
        </header>

        <!-- CENTER: jam digital + analog -->
        <main class="grid items-center px-10" style="grid-template-columns: 1fr auto;">
            <!-- Center: digital clock -->
            <div class="text-center">
                <div class="text-[10px] uppercase tracking-[5px] text-slate-400 font-bold mb-2">Waktu Lokal</div>
                <div id="digital" class="font-digital font-black tracking-wide text-white leading-none drop-shadow-[0_10px_40px_rgba(0,0,0,0.8)]"
                     style="font-size: clamp(100px, 14vw, 200px);">
                    --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.4em;" class="align-top ml-3">--</span>
                </div>
                <!-- Next prayer countdown -->
                <div class="mt-6 inline-flex items-center gap-3 glass-dark rounded-full px-6 py-2 shadow-soft">
                    <span class="w-2 h-2 rounded-full animate-pulse" style="background: var(--accent);"></span>
                    <span class="text-xs uppercase tracking-[3px] text-slate-300 font-semibold">Menuju</span>
                    <span id="nextLabel" class="text-sm font-bold uppercase" style="color: var(--accent);">—</span>
                    <span class="text-slate-500">·</span>
                    <span id="nextCountdown" class="font-digital text-sm font-semibold text-white">--:--:--</span>
                </div>
            </div>

            <!-- Right: analog clock framed -->
            <div class="pl-10">
                <?php include __DIR__ . '/_analog_clock.php'; ?>
            </div>
        </main>

        <!-- 6 prayer grid cells -->
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
                <div class="prayer prayer-grid-cell p-4 text-center backdrop-blur-md" data-key="<?= $key ?>">
                    <div class="text-[10px] uppercase tracking-[3px] font-bold text-slate-400 mb-2"><?= mc_e($label) ?></div>
                    <div class="font-digital text-3xl font-bold text-white tabular-nums" data-time>--:--</div>
                    <?php if ($imam): ?>
                        <div class="text-[10px] mt-2 truncate" style="color: var(--accent);">Imam: <?= mc_e($imam) ?></div>
                    <?php else: ?>
                        <div class="text-[10px] mt-2 text-slate-600">&nbsp;</div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- FOOTER -->
        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
