<?php
/**
 * Layout: Polaroid
 * Slideshow ditampilkan dalam frame foto polaroid (ada tape, sedikit miring),
 * di atas latar wood/cork board. Info berbingkai paper note. Vintage album.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-polaroid {
        background:
            radial-gradient(ellipse at 30% 20%, #6b4f2c 0%, #3a2a18 60%, #1f1610 100%);
        position: relative;
    }
    .layout-polaroid::before {
        /* wood grain texture */
        content: "";
        position: absolute; inset: 0;
        background-image:
            repeating-linear-gradient(95deg, rgba(0,0,0,0.08) 0 1px, transparent 1px 6px),
            repeating-linear-gradient(95deg, rgba(255,200,140,0.04) 0 2px, transparent 2px 14px);
        pointer-events: none;
    }
    .layout-polaroid::after {
        content: "";
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at center, transparent 40%, rgba(0,0,0,0.5) 100%);
        pointer-events: none;
    }

    .layout-polaroid .polaroid {
        background: #fafaf6;
        border-radius: 4px;
        box-shadow:
            0 16px 40px -8px rgba(0,0,0,0.6),
            0 4px 12px rgba(0,0,0,0.4),
            inset 0 0 0 1px rgba(0,0,0,0.05);
        padding: 14px 14px 60px 14px;
        position: relative;
        transform-origin: center;
    }
    .layout-polaroid .polaroid .photo-area {
        background: #000;
        overflow: hidden;
        position: relative;
        height: 100%;
    }
    .layout-polaroid .polaroid::before {
        /* tape */
        content: "";
        position: absolute;
        top: -10px; left: 50%;
        transform: translateX(-50%) rotate(-2deg);
        width: 90px; height: 22px;
        background: rgba(245,233,180,0.7);
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    .layout-polaroid .polaroid .caption {
        position: absolute;
        bottom: 16px; left: 14px; right: 14px;
        font-family: 'Caveat', 'Brush Script MT', cursive;
        color: #444;
        font-size: 18px;
        text-align: center;
    }

    .layout-polaroid .paper-note {
        background: linear-gradient(180deg, #fdf6e3 0%, #f3e7c8 100%);
        border-radius: 3px;
        box-shadow:
            0 12px 30px -6px rgba(0,0,0,0.5),
            inset 0 0 0 1px rgba(0,0,0,0.06);
        padding: 14px 18px;
        color: #3a2a18;
        position: relative;
    }
    .layout-polaroid .paper-note::before {
        content: "";
        position: absolute;
        top: -8px; right: 24px;
        width: 70px; height: 18px;
        background: rgba(220,38,38,0.4);
        transform: rotate(2deg);
        border: 1px solid rgba(0,0,0,0.08);
    }

    .layout-polaroid .stamp {
        font-family: 'Courier New', monospace;
        color: #b91c1c;
        border: 2px solid #b91c1c;
        padding: 4px 10px;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 3px;
        font-size: 10px;
        display: inline-block;
        transform: rotate(-4deg);
        opacity: 0.85;
    }

    .layout-polaroid .schedule-card {
        background: linear-gradient(180deg, #fdf6e3, #ede0bf);
        border-radius: 3px;
        box-shadow: 0 6px 20px -4px rgba(0,0,0,0.5), inset 0 0 0 1px rgba(0,0,0,0.05);
        padding: 8px 10px;
        text-align: center;
        color: #3a2a18;
        position: relative;
        transition: all .3s;
    }
    .layout-polaroid .schedule-card .lbl {
        font-family: 'Courier New', monospace;
        text-transform: uppercase;
        font-size: 10px;
        font-weight: 700;
        color: #6b4f2c;
        letter-spacing: 2px;
    }
    .layout-polaroid .schedule-card .tm {
        font-family: 'Courier New', monospace;
        font-size: 22px;
        font-weight: 900;
        color: #1a0f06;
        margin-top: 2px;
    }
    .layout-polaroid .schedule-card.next {
        background: linear-gradient(180deg, #fde68a, #f5b301);
        box-shadow: 0 8px 24px -4px rgba(245,179,1,0.7), inset 0 0 0 1px rgba(0,0,0,0.1);
        transform: translateY(-3px) rotate(-1deg);
    }
    .layout-polaroid .schedule-card.next .lbl { color: #6b4500; }
    .layout-polaroid .schedule-card.next .tm  { color: #1a0f06; }

    .layout-polaroid .clock-display {
        font-family: 'Courier New', 'Special Elite', monospace;
        font-weight: 900;
        color: #3a2a18;
        text-shadow: 0 2px 4px rgba(255,255,255,0.4);
        letter-spacing: -2px;
        line-height: 1;
    }
</style>

<div class="layout-polaroid h-screen w-screen relative overflow-hidden">

    <div class="relative z-10 h-full grid p-8" style="grid-template-rows: auto 1fr auto auto; gap: 16px;">

        <!-- HEADER: paper note -->
        <header class="paper-note flex items-center justify-between" style="transform: rotate(-0.5deg);">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain">
                <?php endif; ?>
                <div>
                    <span class="stamp">— Album Sholat —</span>
                    <div class="text-2xl font-extrabold mt-1" style="font-family: 'Caveat', 'Brush Script MT', cursive;"><?= mc_e($masjid) ?></div>
                    <div class="text-xs opacity-70"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right">
                <div id="greg-date" class="text-base font-bold" style="font-family: 'Courier New', monospace;">—</div>
                <div id="hij-date" class="text-sm mt-0.5 italic">—</div>
            </div>
        </header>

        <!-- BODY: 2 polaroids + clock note -->
        <main class="grid items-center gap-6" style="grid-template-columns: 1.4fr 1fr;">

            <!-- LEFT: stacked polaroids with slideshow -->
            <div class="relative h-full flex items-center justify-center">
                <!-- Bottom polaroid (tilted) -->
                <div class="polaroid absolute" style="transform: rotate(-6deg); width: 86%; height: 88%; opacity: 0.75; z-index: 1;">
                    <div class="photo-area" style="background: linear-gradient(135deg, #2a1810, #6b4f2c);"></div>
                    <div class="caption">— Memory —</div>
                </div>
                <!-- Top polaroid (with active slideshow) -->
                <div class="polaroid relative" style="transform: rotate(2deg); width: 86%; height: 88%; z-index: 2;">
                    <div class="photo-area">
                        <div id="slideshow" class="absolute inset-0" style="<?= ((string)mc_setting('show_slideshow','1')==='0')?'display:none;':'' ?>">
                            <?php if (!$slides): ?>
                                <div class="slide active" style="background:#0a1a3c url('assets/img/default-bg.svg') center/cover; filter: sepia(0.3);"></div>
                            <?php else: foreach ($slides as $i => $s): ?>
                                <?php if ($s['type']==='video'): ?>
                                    <video class="slide<?= $i===0?' active':'' ?> w-full h-full object-cover" autoplay muted playsinline loop preload="auto" style="filter: sepia(0.15) contrast(1.05);"><source src="<?= mc_e($s['path']) ?>" type="<?= mc_e(mc_video_mime($s['path'])) ?>"></video>
                                <?php else: ?>
                                    <div class="slide<?= $i===0?' active':'' ?>" style="background-image:url('<?= mc_e($s['path']) ?>'); filter: sepia(0.15) contrast(1.05);"></div>
                                <?php endif; ?>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                    <div class="caption" id="slideCaption"><?= mc_e($masjid) ?> — Today</div>
                </div>
            </div>

            <!-- RIGHT: paper note with clock -->
            <div class="paper-note text-center" style="transform: rotate(1deg);">
                <span class="stamp">— Hora Salat —</span>
                <div id="digital" class="clock-display mt-3" style="font-size: clamp(80px, 9vw, 130px);">
                    --<span style="color: #b91c1c;">:</span>--<span style="color: #b91c1c; font-size: 0.4em;" class="align-top ml-1">--</span>
                </div>
                <div class="mt-3 pt-3 border-t-2 border-dashed border-amber-900/30">
                    <div class="text-[10px] uppercase tracking-[3px] font-bold opacity-60" style="font-family: 'Courier New', monospace;">— Berikutnya —</div>
                    <div class="mt-1">
                        <span id="nextLabel" class="text-2xl font-extrabold" style="font-family: 'Caveat', cursive;">—</span>
                    </div>
                    <div id="nextCountdown" class="text-base font-bold mt-1" style="font-family: 'Courier New', monospace;">--:--:--</div>
                </div>
            </div>
        </main>

        <!-- BOTTOM: 6 schedule cards (sticky-note style) -->
        <section class="grid grid-cols-6 gap-3">
            <?php
            $prayers = [
                ['fajr',     'Subuh',   'subuh',   '-1deg'],
                ['sunrise',  'Syuruq',  null,      '0.5deg'],
                ['dhuhr',    $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur', '-0.5deg'],
                ['asr',      'Ashar',   'ashar',   '1deg'],
                ['maghrib',  'Maghrib', 'maghrib', '-0.8deg'],
                ['isha',     'Isya',    'isya',    '0.6deg'],
            ];
            foreach ($prayers as [$key, $label, $imamKey, $rot]):
                $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
            ?>
            <div class="prayer schedule-card" data-key="<?= $key ?>" style="transform: rotate(<?= $rot ?>);">
                <div class="lbl"><?= mc_e($label) ?></div>
                <div class="tm" data-time>--:--</div>
                <?php if ($imam && $showImam): ?>
                    <div class="text-[9px] mt-0.5 truncate opacity-75" style="font-family: 'Caveat', cursive; font-size: 12px;"><?= mc_e($imam) ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </section>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
