<?php
/**
 * Layout: Kinetic
 * Tipografi raksasa, rotasi banner accent, jam super gede. Modern flat & bold.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-kinetic {
        background: var(--accent);
        position: relative;
        color: var(--primary-dark);
    }
    .layout-kinetic .kbar {
        background: var(--primary-dark);
        color: var(--accent);
        overflow: hidden;
        white-space: nowrap;
    }
    .layout-kinetic .kbar-inner {
        display: inline-block;
        animation: slide-left 25s linear infinite;
        font-weight: 900;
        font-size: 22px;
        letter-spacing: 3px;
        text-transform: uppercase;
        padding: 8px 0;
    }
    @keyframes slide-left {
        from { transform: translateX(0); }
        to   { transform: translateX(-50%); }
    }

    .layout-kinetic .huge-clock {
        font-weight: 900;
        font-size: clamp(180px, 22vw, 360px);
        line-height: 0.85;
        letter-spacing: -8px;
        color: var(--primary-dark);
    }

    .layout-kinetic .prayer-pill {
        background: var(--primary-dark);
        color: var(--accent);
        border-radius: 999px;
        padding: 10px 22px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 14px;
        transition: all .3s;
    }
    .layout-kinetic .prayer-pill .ptime {
        background: var(--accent);
        color: var(--primary-dark);
        padding: 2px 12px;
        border-radius: 999px;
        font-family: var(--font-digital);
        font-size: 18px;
    }
    .layout-kinetic .prayer-pill.next {
        background: #fff;
        color: var(--primary-dark);
        transform: scale(1.08);
        box-shadow: 0 8px 30px rgba(0,0,0,0.25);
    }
    .layout-kinetic .prayer-pill.next .ptime {
        background: var(--primary-dark);
        color: var(--accent);
    }

    .layout-kinetic .grid-list { display: grid; gap: 10px; }

    .layout-kinetic .arrow-dance {
        animation: dance 1.4s ease-in-out infinite;
        display: inline-block;
    }
    @keyframes dance {
        0%, 100% { transform: translateX(0); }
        50%      { transform: translateX(8px); }
    }

    .layout-kinetic .meta-corner {
        background: var(--primary-dark);
        color: var(--accent);
        padding: 14px 18px;
        border-radius: 14px;
        font-weight: 800;
    }
</style>

<div class="layout-kinetic h-screen w-screen relative overflow-hidden grid" style="grid-template-rows: 50px auto 1fr 50px auto auto;">

    <!-- TOP rotating banner -->
    <div class="kbar">
        <div class="kbar-inner">
            ★ <?= mc_e($masjid) ?> ★ JADWAL SHOLAT ★ LIVE ★ <?= mc_e($masjid) ?> ★ JADWAL SHOLAT ★ LIVE ★ <?= mc_e($masjid) ?> ★ JADWAL SHOLAT ★ LIVE ★
        </div>
    </div>

    <!-- HEADER -->
    <header class="px-12 py-5 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <?php if ($logo): ?>
                <img src="<?= mc_e($logo) ?>" alt="logo" class="w-14 h-14 object-contain">
            <?php else: ?>
                <div class="w-14 h-14 rounded-full flex items-center justify-center text-3xl font-black"
                     style="background: var(--primary-dark); color: var(--accent);">★</div>
            <?php endif; ?>
            <div>
                <div class="text-[10px] uppercase tracking-[5px] font-extrabold opacity-60">— Today's Schedule —</div>
                <div class="text-3xl font-black uppercase tracking-tight"><?= mc_e($masjid) ?></div>
            </div>
        </div>
        <div class="meta-corner text-right">
            <div id="greg-date" class="text-sm">—</div>
            <div id="hij-date" class="text-xs mt-1 opacity-80">—</div>
        </div>
    </header>

    <!-- BODY: huge clock left, prayers pills right -->
    <main class="grid items-center gap-8 px-12" style="grid-template-columns: 1.2fr 1fr; min-height:0;">
        <div>
            <div class="text-[14px] uppercase tracking-[8px] font-extrabold opacity-60 mb-4">It is now ↓</div>
            <div id="digital" class="huge-clock">
                --<span style="color: var(--primary-dark); opacity:0.4;">:</span>--<span style="font-size: 0.35em; opacity:0.4;" class="align-top ml-2">--</span>
            </div>
            <div class="mt-6 flex items-center gap-3 text-2xl font-black">
                <span class="opacity-60">NEXT:</span>
                <span id="nextLabel" class="px-4 py-1 rounded-full" style="background: var(--primary-dark); color: var(--accent);">—</span>
                <span class="arrow-dance">→</span>
                <span id="nextCountdown" class="font-digital tabular-nums">--:--:--</span>
            </div>
        </div>

        <div class="grid-list">
            <?php
            $prayers = [
                ['fajr',     'SUBUH',   'subuh'],
                ['sunrise',  'SYURUQ',  null],
                ['dhuhr',    $isFriday ? "JUM'AT" : 'DZUHUR', $isFriday ? 'jumat' : 'dzuhur'],
                ['asr',      'ASHAR',   'ashar'],
                ['maghrib',  'MAGHRIB', 'maghrib'],
                ['isha',     'ISYA',    'isya'],
            ];
            foreach ($prayers as [$key, $label, $imamKey]):
                $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
            ?>
            <div class="prayer flex items-center justify-between" data-key="<?= $key ?>">
                <div class="prayer-pill w-full justify-between">
                    <span class="text-base"><?= mc_e($label) ?>
                        <?php if ($imam && $showImam): ?>
                            <span class="opacity-60 text-xs ml-2 font-medium">/ <?= mc_e($imam) ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="ptime tabular-nums" data-time>--:--</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- BOTTOM rotating banner (reverse) -->
    <div class="kbar">
        <div class="kbar-inner" style="animation-direction: reverse;">
            ✦ MENUJU SHOLAT TEPAT WAKTU ✦ <?= mc_e($masjid) ?> ✦ MENUJU SHOLAT TEPAT WAKTU ✦ <?= mc_e($masjid) ?> ✦ MENUJU SHOLAT TEPAT WAKTU ✦
        </div>
    </div>

    <?php include __DIR__ . '/_footer.php'; ?>
</div>
