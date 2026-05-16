<?php
/**
 * Layout: Frame
 * Bingkai ornamen Islamic tradisional dengan border emas, kaligrafi sentral,
 * jadwal sholat dalam 2 kolom kanan-kiri (3 atas, 3 bawah).
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-frame {
        background:
            radial-gradient(circle at 50% 30%, color-mix(in srgb, var(--primary) 30%, transparent), transparent 55%),
            #060812;
        position: relative;
    }
    .layout-frame .frame-border {
        position: absolute;
        inset: 14px;
        border: 2px solid color-mix(in srgb, var(--accent) 70%, transparent);
        border-radius: 6px;
        pointer-events: none;
    }
    .layout-frame .frame-border::before,
    .layout-frame .frame-border::after {
        content: "";
        position: absolute;
        inset: 6px;
        border: 1px solid color-mix(in srgb, var(--accent) 30%, transparent);
        border-radius: 4px;
    }
    .layout-frame .corner {
        position: absolute;
        width: 56px; height: 56px;
        color: var(--accent);
    }
    .layout-frame .corner.tl { top: 8px; left: 8px; }
    .layout-frame .corner.tr { top: 8px; right: 8px; transform: scaleX(-1); }
    .layout-frame .corner.bl { bottom: 8px; left: 8px; transform: scaleY(-1); }
    .layout-frame .corner.br { bottom: 8px; right: 8px; transform: scale(-1,-1); }

    .layout-frame .arch-card {
        background: linear-gradient(180deg, rgba(255,255,255,0.05), rgba(0,0,0,0.25));
        border: 1px solid color-mix(in srgb, var(--accent) 30%, transparent);
        border-radius: 80px 80px 14px 14px;
        padding: 24px 18px 16px;
        text-align: center;
        position: relative;
    }
    .layout-frame .arch-card::before {
        content: "";
        position: absolute;
        top: 6px; left: 50%;
        transform: translateX(-50%);
        width: 80%; height: 70%;
        border: 1px solid color-mix(in srgb, var(--accent) 25%, transparent);
        border-radius: 70px 70px 0 0;
        pointer-events: none;
    }
    .layout-frame .arch-card.next {
        border-color: var(--accent);
        background: linear-gradient(180deg, color-mix(in srgb, var(--accent) 20%, transparent), color-mix(in srgb, var(--accent) 4%, transparent));
        box-shadow: 0 12px 40px -10px var(--accent);
    }
    .layout-frame .arch-card.next [data-time] { color: var(--accent); }
    .layout-frame .arch-card.next .label { color: var(--accent); }

    .layout-frame .center-medallion {
        background: radial-gradient(circle at 50% 30%, color-mix(in srgb, var(--primary) 60%, transparent), transparent 70%);
        border: 1px solid color-mix(in srgb, var(--accent) 40%, transparent);
        border-radius: 50%;
        padding: 30px;
        position: relative;
    }
    .layout-frame .center-medallion::before {
        content: "";
        position: absolute;
        inset: 8px;
        border: 1px dashed color-mix(in srgb, var(--accent) 35%, transparent);
        border-radius: 50%;
    }
</style>

<div class="layout-frame h-screen w-screen relative overflow-hidden">

    <!-- soft slideshow at low opacity -->
    <div id="slideshow" class="absolute inset-0 opacity-15" style="<?= ((string)mc_setting('show_slideshow','1')==='0')?'display:none;':'' ?>">
        <?php if ($slides) foreach ($slides as $i => $s): ?>
            <?php if ($s['type']==='video'): ?>
                <video class="slide<?= $i===0?' active':'' ?> w-full h-full object-cover" autoplay muted playsinline loop preload="auto"><source src="<?= mc_e($s['path']) ?>" type="<?= mc_e(mc_video_mime($s['path'])) ?>"></video>
            <?php else: ?>
                <div class="slide<?= $i===0?' active':'' ?>" style="background-image:url('<?= mc_e($s['path']) ?>')"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- ornament frame -->
    <div class="frame-border"></div>

    <!-- corner ornaments -->
    <svg class="corner tl" viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.4">
        <path d="M2 28 Q2 2 28 2"/>
        <path d="M8 28 Q8 8 28 8"/>
        <circle cx="14" cy="14" r="3"/>
        <path d="M2 18 L18 2"/>
    </svg>
    <svg class="corner tr" viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.4">
        <path d="M2 28 Q2 2 28 2"/>
        <path d="M8 28 Q8 8 28 8"/>
        <circle cx="14" cy="14" r="3"/>
        <path d="M2 18 L18 2"/>
    </svg>
    <svg class="corner bl" viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.4">
        <path d="M2 28 Q2 2 28 2"/>
        <path d="M8 28 Q8 8 28 8"/>
        <circle cx="14" cy="14" r="3"/>
        <path d="M2 18 L18 2"/>
    </svg>
    <svg class="corner br" viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="1.4">
        <path d="M2 28 Q2 2 28 2"/>
        <path d="M8 28 Q8 8 28 8"/>
        <circle cx="14" cy="14" r="3"/>
        <path d="M2 18 L18 2"/>
    </svg>

    <div class="relative z-10 h-full grid p-10" style="grid-template-rows: auto 1fr auto auto;">

        <!-- HEADER -->
        <header class="flex items-center justify-between px-4 mb-3">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-14 h-14 object-contain">
                <?php else: ?>
                    <svg viewBox="0 0 100 100" class="w-14 h-14">
                        <path d="M50,15 A35,35 0 1,0 75,75 A28,28 0 1,1 50,15 Z" fill="var(--accent)"/>
                    </svg>
                <?php endif; ?>
                <div>
                    <div class="text-[9px] uppercase tracking-[6px] font-bold" style="color: var(--accent);">بِسْمِ ٱللَّٰهِ</div>
                    <div class="text-2xl font-extrabold text-white mt-0.5"><?= mc_e($masjid) ?></div>
                    <div class="text-xs text-slate-400"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right">
                <div id="greg-date" class="text-base font-bold text-white">—</div>
                <div id="hij-date" class="text-sm font-semibold mt-1 font-arabic" style="color: var(--accent); font-size: 18px;">—</div>
            </div>
        </header>

        <!-- BODY: 3 prayers left | clock medallion | 3 prayers right -->
        <main class="grid items-center gap-6 px-4" style="grid-template-columns: 1fr 1.4fr 1fr;">

            <!-- LEFT prayers -->
            <div class="grid gap-3">
                <?php
                $leftPrayers = [
                    ['fajr',    'Subuh',  'subuh',  'الفجر'],
                    ['sunrise', 'Syuruq', null,     'الشروق'],
                    ['dhuhr',   $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur', $isFriday ? 'الجمعة' : 'الظهر'],
                ];
                foreach ($leftPrayers as [$key, $label, $imamKey, $arab]):
                    $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                ?>
                <div class="prayer arch-card" data-key="<?= $key ?>">
                    <div class="font-arabic text-2xl mb-1" style="color: var(--accent);"><?= mc_e($arab) ?></div>
                    <div class="label text-xs uppercase tracking-[3px] font-bold text-slate-300"><?= mc_e($label) ?></div>
                    <div class="font-digital text-3xl font-black text-white tabular-nums mt-1" data-time>--:--</div>
                    <?php if ($imam && $showImam): ?>
                        <div class="text-[10px] mt-1 truncate" style="color: color-mix(in srgb, var(--accent) 70%, white);">Imam: <?= mc_e($imam) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- CENTER medallion: clock -->
            <div class="text-center">
                <div class="center-medallion">
                    <?php $clockSize='w-32 h-32 mx-auto mb-3'; include __DIR__ . '/_analog_clock.php'; ?>
                    <div id="digital" class="font-digital font-black tracking-wide text-white leading-none"
                         style="font-size: clamp(60px, 8vw, 110px); text-shadow: 0 4px 20px rgba(0,0,0,0.7);">
                        --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.45em;" class="align-top ml-2">--</span>
                    </div>
                    <div class="mt-3 text-[10px] uppercase tracking-[5px] font-bold" style="color: var(--accent);">— Menuju —</div>
                    <div class="mt-1">
                        <span id="nextLabel" class="font-bold uppercase tracking-wider text-white">—</span>
                        <span class="text-slate-500 mx-2">·</span>
                        <span id="nextCountdown" class="font-digital font-semibold tabular-nums" style="color: var(--accent);">--:--:--</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT prayers -->
            <div class="grid gap-3">
                <?php
                $rightPrayers = [
                    ['asr',     'Ashar',   'ashar',   'العصر'],
                    ['maghrib', 'Maghrib', 'maghrib', 'المغرب'],
                    ['isha',    'Isya',    'isya',    'العشاء'],
                ];
                foreach ($rightPrayers as [$key, $label, $imamKey, $arab]):
                    $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                ?>
                <div class="prayer arch-card" data-key="<?= $key ?>">
                    <div class="font-arabic text-2xl mb-1" style="color: var(--accent);"><?= mc_e($arab) ?></div>
                    <div class="label text-xs uppercase tracking-[3px] font-bold text-slate-300"><?= mc_e($label) ?></div>
                    <div class="font-digital text-3xl font-black text-white tabular-nums mt-1" data-time>--:--</div>
                    <?php if ($imam && $showImam): ?>
                        <div class="text-[10px] mt-1 truncate" style="color: color-mix(in srgb, var(--accent) 70%, white);">Imam: <?= mc_e($imam) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </main>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
