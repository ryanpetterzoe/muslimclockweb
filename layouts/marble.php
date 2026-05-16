<?php
/**
 * Layout: Marble
 * Mewah marble premium: tekstur marble emas-putih, ukiran emas, jadwal medali.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-marble {
        position: relative;
        background:
            radial-gradient(ellipse at 30% 20%, #f5e9d4 0%, transparent 60%),
            radial-gradient(ellipse at 70% 80%, #d4af37 0%, transparent 50%),
            linear-gradient(135deg, #1a1410 0%, #2d2419 50%, #1a1410 100%);
    }
    .layout-marble::before {
        /* marble veining */
        content: "";
        position: absolute; inset: 0;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='600' height='600'><filter id='m'><feTurbulence baseFrequency='0.012' numOctaves='4'/><feColorMatrix values='0 0 0 0 0.83  0 0 0 0 0.69  0 0 0 0 0.22  0 0 0 0.4 0'/></filter><rect width='100%' height='100%' filter='url(%23m)'/></svg>");
        background-size: 800px 800px;
        opacity: 0.35;
        pointer-events: none;
    }
    .layout-marble::after {
        /* warm light overlay */
        content: "";
        position: absolute; inset: 0;
        background: radial-gradient(circle at 50% 30%, rgba(255,220,150,0.15), transparent 60%);
        pointer-events: none;
    }

    .layout-marble .gold-border {
        border: 2px solid #d4af37;
        border-radius: 6px;
        position: relative;
        background: linear-gradient(180deg, rgba(212,175,55,0.08), rgba(0,0,0,0.4));
        backdrop-filter: blur(6px);
    }
    .layout-marble .gold-border::before {
        content: "";
        position: absolute; inset: 4px;
        border: 1px solid rgba(212,175,55,0.5);
        border-radius: 4px;
        pointer-events: none;
    }

    .layout-marble .medal {
        background: linear-gradient(180deg, rgba(245,233,212,0.08), rgba(0,0,0,0.5));
        border: 1.5px solid #d4af37;
        border-radius: 50%;
        aspect-ratio: 1;
        position: relative;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        text-align: center;
        padding: 12px;
        box-shadow: inset 0 0 24px rgba(0,0,0,0.4), 0 8px 24px -6px rgba(0,0,0,0.6);
    }
    .layout-marble .medal::before {
        content: "";
        position: absolute; inset: 6px;
        border: 1px dashed rgba(212,175,55,0.4);
        border-radius: 50%;
    }
    .layout-marble .medal.next {
        background: linear-gradient(180deg, rgba(212,175,55,0.3), rgba(212,175,55,0.05));
        box-shadow: inset 0 0 24px rgba(0,0,0,0.4), 0 8px 30px -4px #d4af37;
    }
    .layout-marble .medal.next [data-time] { color: #fde68a; text-shadow: 0 0 18px #d4af37; }

    .layout-marble .gold-text {
        background: linear-gradient(180deg, #fff8e7 0%, #d4af37 50%, #8b6914 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
    }
    .layout-marble .ornament-divider {
        display: flex; align-items: center; gap: 12px;
        color: #d4af37;
    }
    .layout-marble .ornament-divider::before,
    .layout-marble .ornament-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, transparent, #d4af37, transparent);
    }
</style>

<div class="layout-marble h-screen w-screen relative overflow-hidden">

    <div class="relative z-10 h-full grid p-10" style="grid-template-rows: auto 1fr auto auto; gap: 18px;">

        <!-- HEADER -->
        <header class="text-center">
            <div class="ornament-divider mb-3">
                <span style="font-size:14px;">❖ ❖ ❖</span>
            </div>
            <div class="flex items-center justify-center gap-5">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-14 h-14 object-contain">
                <?php endif; ?>
                <div>
                    <div class="gold-text text-4xl font-extrabold tracking-wide" style="font-family: 'Playfair Display', 'Amiri', Georgia, serif;"><?= mc_e($masjid) ?></div>
                    <div class="text-xs mt-1" style="color: rgba(212,175,55,0.7); letter-spacing: 4px; text-transform: uppercase;"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="ornament-divider mt-3">
                <span style="font-size:14px;">❖ ❖ ❖</span>
            </div>
        </header>

        <!-- BODY: clock center, medals around -->
        <main class="grid items-center gap-6" style="grid-template-columns: 1fr 1.3fr 1fr;">

            <!-- LEFT medals (3) -->
            <div class="grid grid-cols-1 gap-3">
                <?php
                $leftPrayers = [
                    ['fajr',    'Subuh',  'subuh'],
                    ['sunrise', 'Syuruq', null],
                    ['dhuhr',   $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur'],
                ];
                foreach ($leftPrayers as [$key, $label, $imamKey]):
                    $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                ?>
                <div class="prayer medal" data-key="<?= $key ?>" style="aspect-ratio: 2.2/1; border-radius: 100px;">
                    <div class="text-[10px] uppercase tracking-[3px] font-bold" style="color: #d4af37;"><?= mc_e($label) ?></div>
                    <div class="font-digital text-2xl font-black gold-text tabular-nums mt-1" data-time>--:--</div>
                    <?php if ($imam && $showImam): ?>
                        <div class="text-[9px] mt-0.5 truncate" style="color: rgba(245,233,212,0.7);"><?= mc_e($imam) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- CENTER: ornate clock -->
            <div class="gold-border p-8 text-center">
                <div class="text-[10px] uppercase tracking-[6px] font-bold mb-3" style="color: rgba(212,175,55,0.8);">Hora Sancta</div>
                <div id="digital" class="font-digital font-black tracking-tight gold-text leading-none"
                     style="font-size: clamp(110px, 13vw, 200px); -webkit-text-stroke: 1px rgba(0,0,0,0.2);">
                    --<span>:</span>--<span style="font-size: 0.4em;" class="align-top ml-3">--</span>
                </div>
                <div class="ornament-divider my-5">
                    <span style="font-size:12px;">✧</span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-left">
                    <div>
                        <div class="text-[9px] uppercase tracking-[3px] font-bold" style="color: rgba(212,175,55,0.7);">Tanggal</div>
                        <div id="greg-date" class="text-base font-bold gold-text mt-1">—</div>
                        <div id="hij-date" class="text-xs mt-1 font-arabic" style="color: rgba(245,233,212,0.7); font-size: 16px;">—</div>
                    </div>
                    <div class="text-right border-l border-amber-700/30 pl-3">
                        <div class="text-[9px] uppercase tracking-[3px] font-bold" style="color: rgba(212,175,55,0.7);">Berikutnya</div>
                        <div id="nextLabel" class="text-lg font-extrabold gold-text mt-1">—</div>
                        <div id="nextCountdown" class="font-digital text-sm font-bold mt-0.5 tabular-nums" style="color: #fde68a;">--:--:--</div>
                    </div>
                </div>
            </div>

            <!-- RIGHT medals (3) -->
            <div class="grid grid-cols-1 gap-3">
                <?php
                $rightPrayers = [
                    ['asr',     'Ashar',   'ashar'],
                    ['maghrib', 'Maghrib', 'maghrib'],
                    ['isha',    'Isya',    'isya'],
                ];
                foreach ($rightPrayers as [$key, $label, $imamKey]):
                    $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                ?>
                <div class="prayer medal" data-key="<?= $key ?>" style="aspect-ratio: 2.2/1; border-radius: 100px;">
                    <div class="text-[10px] uppercase tracking-[3px] font-bold" style="color: #d4af37;"><?= mc_e($label) ?></div>
                    <div class="font-digital text-2xl font-black gold-text tabular-nums mt-1" data-time>--:--</div>
                    <?php if ($imam && $showImam): ?>
                        <div class="text-[9px] mt-0.5 truncate" style="color: rgba(245,233,212,0.7);"><?= mc_e($imam) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </main>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
