<?php
/**
 * Layout: Klasik
 * Tradisional dengan ornamen Arabic, latar pattern, jadwal table elegan.
 */
?>
<style>
    .layout-classic {
        background:
            radial-gradient(circle at 0% 0%, color-mix(in srgb, var(--primary) 25%, transparent), transparent 50%),
            radial-gradient(circle at 100% 100%, color-mix(in srgb, var(--accent) 15%, transparent), transparent 50%),
            #0a0e1f;
    }
    /* Geometric pattern overlay */
    .layout-classic .pattern {
        position: absolute;
        inset: 0;
        opacity: 0.06;
        background-image:
          repeating-linear-gradient(45deg, var(--accent) 0, var(--accent) 1px, transparent 1px, transparent 30px),
          repeating-linear-gradient(-45deg, var(--accent) 0, var(--accent) 1px, transparent 1px, transparent 30px);
        pointer-events: none;
    }
    .layout-classic .ornament-divider {
        display: flex; align-items: center; gap: 16px;
        color: var(--accent);
    }
    .layout-classic .ornament-divider::before,
    .layout-classic .ornament-divider::after {
        content: ""; flex: 1; height: 1px;
        background: linear-gradient(90deg, transparent, var(--accent), transparent);
    }
    .layout-classic .prayer-row {
        display: grid;
        grid-template-columns: 60px 1fr auto 1fr;
        align-items: center;
        gap: 20px;
        padding: 14px 24px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    .layout-classic .prayer-row.next {
        background: linear-gradient(90deg, color-mix(in srgb, var(--accent) 8%, transparent), transparent);
    }
    .layout-classic .prayer-row.next .pname { color: var(--accent); }
    .layout-classic .prayer-row.next [data-time] {
        color: var(--accent);
        font-weight: 800;
    }
</style>

<div class="layout-classic h-screen w-screen relative overflow-hidden">
    <div class="pattern"></div>

    <!-- background slideshow with strong overlay -->
    <div id="slideshow" class="absolute inset-0 opacity-20">
        <?php if ($slides) foreach ($slides as $i => $s): ?>
            <?php if ($s['type']==='video'): ?>
                <video class="slide<?= $i===0?' active':'' ?>" muted playsinline loop preload="metadata"><source src="<?= mc_e($s['path']) ?>"></video>
            <?php else: ?>
                <div class="slide<?= $i===0?' active':'' ?>" style="background-image:url('<?= mc_e($s['path']) ?>')"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="relative z-10 h-full grid" style="grid-template-rows: auto 1fr auto auto;">

        <!-- HEADER w/ Arabic-style ornament -->
        <header class="px-12 py-5 relative" style="background: linear-gradient(180deg, color-mix(in srgb, var(--primary) 40%, transparent), transparent);">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <?php if ($logo): ?>
                        <img src="<?= mc_e($logo) ?>" alt="logo" class="w-16 h-16 object-contain">
                    <?php else: ?>
                        <!-- decorative crescent -->
                        <svg viewBox="0 0 100 100" class="w-16 h-16">
                            <defs>
                                <linearGradient id="cg" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="var(--accent)"/>
                                    <stop offset="100%" stop-color="var(--primary)"/>
                                </linearGradient>
                            </defs>
                            <path d="M50,15 A35,35 0 1,0 75,75 A28,28 0 1,1 50,15 Z" fill="url(#cg)"/>
                            <path d="M75,30 L80,40 L90,42 L82,50 L84,60 L75,55 L66,60 L68,50 L60,42 L70,40 Z" fill="var(--accent)" opacity="0.9"/>
                        </svg>
                    <?php endif; ?>
                    <div>
                        <div class="text-[10px] uppercase tracking-[5px] mb-1" style="color: var(--accent);">ٱلسَّلاَمُ عَلَيْكُمْ</div>
                        <div class="text-3xl font-extrabold text-white"><?= mc_e($masjid) ?></div>
                        <div class="text-sm text-slate-300 mt-0.5"><?= mc_e($alamat) ?></div>
                    </div>
                </div>
                <div class="text-right">
                    <div id="greg-date" class="text-base font-bold text-white">—</div>
                    <div id="hij-date" class="text-sm font-semibold mt-1" style="color: var(--accent);">—</div>
                </div>
            </div>
            <!-- ornament divider -->
            <div class="ornament-divider mt-4">
                <span class="text-2xl">❋</span>
            </div>
        </header>

        <!-- BODY -->
        <main class="grid px-12 py-6 gap-8 items-center" style="grid-template-columns: 1fr 1.2fr;">
            <!-- LEFT: clock + countdown -->
            <div class="text-center">
                <div class="flex items-center justify-center mb-6">
                    <?php $clockSize='w-52 h-52'; include __DIR__ . '/_analog_clock.php'; ?>
                </div>
                <div id="digital" class="font-digital font-black tracking-wide text-white leading-none drop-shadow-[0_8px_20px_rgba(0,0,0,0.6)]" style="font-size: 80px;">
                    --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.5em;" class="align-top ml-2">--</span>
                </div>
                <div class="ornament-divider mt-4 text-sm">
                    <span style="color: var(--accent);">✦</span>
                </div>
                <div class="mt-3">
                    <div class="text-[10px] uppercase tracking-[4px] text-slate-400 font-semibold">Menuju Sholat</div>
                    <div class="mt-1.5">
                        <span id="nextLabel" class="text-lg font-bold uppercase tracking-wider" style="color: var(--accent);">—</span>
                        <span class="text-slate-500 mx-2">·</span>
                        <span id="nextCountdown" class="font-digital text-lg font-semibold text-white">--:--:--</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: prayer table -->
            <div class="rounded-2xl overflow-hidden" style="background: linear-gradient(135deg, rgba(255,255,255,0.06), rgba(0,0,0,0.3)); border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 20px 60px -10px rgba(0,0,0,0.5);">
                <div class="px-6 py-3 border-b border-white/10 flex items-center justify-between" style="background: color-mix(in srgb, var(--primary) 30%, transparent);">
                    <span class="text-xs uppercase tracking-[3px] font-bold" style="color: var(--accent);">Jadwal Sholat Hari Ini</span>
                    <span class="text-xs text-slate-400 font-arabic" style="font-size:14px;">مَوَاقِيتُ الصَّلاَةِ</span>
                </div>
                <?php
                $prayers = [
                    ['fajr',     'Subuh',   'subuh',   'الفجر'],
                    ['sunrise',  'Syuruq',  null,      'الشروق'],
                    ['dhuhr',    $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur', $isFriday ? 'الجمعة' : 'الظهر'],
                    ['asr',      'Ashar',   'ashar',   'العصر'],
                    ['maghrib',  'Maghrib', 'maghrib', 'المغرب'],
                    ['isha',     'Isya',    'isya',    'العشاء'],
                ];
                foreach ($prayers as [$key, $label, $imamKey, $arab]):
                    $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                ?>
                <div class="prayer prayer-row" data-key="<?= $key ?>">
                    <div class="font-arabic text-2xl text-right" style="color: var(--accent);"><?= mc_e($arab) ?></div>
                    <div>
                        <div class="pname text-base font-bold uppercase tracking-wider text-white"><?= mc_e($label) ?></div>
                        <?php if ($imam): ?>
                            <div class="text-[11px] text-slate-400 mt-0.5">Imam: <?= mc_e($imam) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="text-slate-600">›</div>
                    <div class="font-digital text-3xl font-bold text-white tabular-nums text-right" data-time>--:--</div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
