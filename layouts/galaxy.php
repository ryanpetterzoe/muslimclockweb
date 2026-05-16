<?php
/**
 * Layout: Galaxy
 * Tema luar angkasa: bintang berkelap-kelip, nebula, jam di tengah dengan orbit ring,
 * jadwal sholat sebagai planet di orbit.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-galaxy {
        background: radial-gradient(ellipse at 50% 40%, #1a1f4a 0%, #050816 60%, #000 100%);
        position: relative;
    }
    .layout-galaxy .stars {
        position: absolute; inset: 0;
        overflow: hidden;
        pointer-events: none;
    }
    .layout-galaxy .star {
        position: absolute;
        background: #fff;
        border-radius: 50%;
        animation: twinkle 4s ease-in-out infinite;
    }
    @keyframes twinkle {
        0%, 100% { opacity: 0.3; transform: scale(1); }
        50%      { opacity: 1;   transform: scale(1.4); }
    }
    .layout-galaxy .nebula {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.5;
        animation: drift 30s ease-in-out infinite alternate;
    }
    .layout-galaxy .nebula.n1 { top: -10%; left: 10%;  width: 480px; height: 480px; background: var(--primary); }
    .layout-galaxy .nebula.n2 { bottom: 5%; right: 5%;  width: 520px; height: 520px; background: var(--accent); opacity: 0.25; animation-delay: -10s; }
    .layout-galaxy .nebula.n3 { top: 40%; left: 50%;    width: 360px; height: 360px; background: #8b5cf6; opacity: 0.25; animation-delay: -20s; }
    @keyframes drift {
        0%   { transform: translate(0,0) scale(1); }
        100% { transform: translate(40px, -30px) scale(1.1); }
    }

    .layout-galaxy .central-core {
        position: relative;
        width: 380px; height: 380px;
        margin: 0 auto;
    }
    .layout-galaxy .orbit-ring {
        position: absolute; inset: 0;
        border: 1px dashed rgba(255,255,255,0.18);
        border-radius: 50%;
        animation: rotate-orbit 60s linear infinite;
    }
    .layout-galaxy .orbit-ring.r2 {
        inset: 22px;
        border-color: color-mix(in srgb, var(--accent) 30%, transparent);
        animation-direction: reverse;
        animation-duration: 90s;
    }
    @keyframes rotate-orbit { to { transform: rotate(360deg); } }
    .layout-galaxy .core {
        position: absolute;
        inset: 56px;
        border-radius: 50%;
        background:
            radial-gradient(circle at 30% 30%, color-mix(in srgb, var(--accent) 70%, white) 0%, var(--primary) 40%, var(--primary-dark) 100%);
        box-shadow:
            inset 0 0 60px rgba(0,0,0,0.5),
            0 0 80px color-mix(in srgb, var(--primary) 50%, transparent),
            0 0 30px color-mix(in srgb, var(--accent) 50%, transparent);
        display: flex; align-items: center; justify-content: center;
    }

    .layout-galaxy .planet-card {
        background: linear-gradient(160deg, rgba(255,255,255,0.07), rgba(255,255,255,0.01));
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 20px;
        padding: 14px 16px;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        transition: all .3s;
        position: relative;
        overflow: hidden;
    }
    .layout-galaxy .planet-card::before {
        content: "";
        position: absolute;
        top: -30px; right: -30px;
        width: 60px; height: 60px;
        background: radial-gradient(circle, color-mix(in srgb, var(--accent) 20%, transparent), transparent);
        border-radius: 50%;
    }
    .layout-galaxy .planet-card.next {
        border-color: var(--accent);
        background: linear-gradient(160deg, color-mix(in srgb, var(--accent) 18%, transparent), color-mix(in srgb, var(--accent) 4%, transparent));
        box-shadow: 0 0 40px -8px var(--accent);
    }
    .layout-galaxy .planet-card.next [data-time] { color: var(--accent); }
</style>

<div class="layout-galaxy h-screen w-screen relative overflow-hidden">
    <div class="nebula n1"></div>
    <div class="nebula n2"></div>
    <div class="nebula n3"></div>

    <!-- random stars (50) -->
    <div class="stars">
        <?php for ($i = 0; $i < 80; $i++):
            $x = rand(0, 100); $y = rand(0, 100);
            $s = rand(1, 3); $d = rand(0, 40) / 10; $del = rand(0, 4);
        ?>
            <div class="star" style="top:<?= $y ?>%; left:<?= $x ?>%; width:<?= $s ?>px; height:<?= $s ?>px; animation-delay: -<?= $del ?>s;"></div>
        <?php endfor; ?>
    </div>

    <div class="relative z-10 h-full grid p-8" style="grid-template-rows: auto 1fr auto auto; gap: 14px;">

        <!-- HEADER -->
        <header class="flex items-center justify-between px-2">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-14 h-14 object-contain rounded-xl">
                <?php else: ?>
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-3xl"
                         style="background: radial-gradient(circle at 30% 30%, var(--accent), var(--primary)); box-shadow: 0 0 30px var(--accent);">&#x262A;</div>
                <?php endif; ?>
                <div>
                    <div class="text-[10px] uppercase tracking-[5px] mb-1" style="color: var(--accent);">⌬ Galaxy Mode</div>
                    <div class="text-2xl font-extrabold text-white"><?= mc_e($masjid) ?></div>
                    <div class="text-xs text-slate-400 mt-0.5"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right">
                <div id="greg-date" class="text-sm font-bold text-white">—</div>
                <div id="hij-date" class="text-xs mt-0.5" style="color: var(--accent);">—</div>
            </div>
        </header>

        <!-- BODY: 3 planets left | core | 3 planets right -->
        <main class="grid items-center gap-6" style="grid-template-columns: 1fr 1.2fr 1fr;">

            <!-- LEFT planets -->
            <div class="grid gap-3">
                <?php
                $leftPrayers = [
                    ['fajr',    'Subuh',  'subuh',  '🌅'],
                    ['sunrise', 'Syuruq', null,     '☀️'],
                    ['dhuhr',   $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur', '🌞'],
                ];
                foreach ($leftPrayers as [$key, $label, $imamKey, $icon]):
                    $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                ?>
                <div class="prayer planet-card" data-key="<?= $key ?>">
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg shrink-0"
                             style="background: radial-gradient(circle at 30% 30%, color-mix(in srgb, var(--accent) 60%, white), var(--primary));"><?= $icon ?></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[10px] uppercase tracking-[2px] text-slate-400 font-bold"><?= mc_e($label) ?></div>
                            <div class="font-digital text-2xl font-black text-white tabular-nums" data-time>--:--</div>
                            <?php if ($imam && $showImam): ?>
                                <div class="text-[10px] mt-0.5 truncate" style="color: color-mix(in srgb, var(--accent) 70%, white);"><?= mc_e($imam) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- CENTRAL CORE -->
            <div class="text-center">
                <div class="central-core">
                    <div class="orbit-ring"></div>
                    <div class="orbit-ring r2"></div>
                    <div class="core">
                        <div class="text-center px-4">
                            <div class="text-[9px] uppercase tracking-[5px] font-bold opacity-70" style="color: var(--accent);">⌖ NOW ⌖</div>
                            <div id="digital" class="font-digital font-black tracking-tight leading-none mt-2 text-white drop-shadow-[0_4px_15px_rgba(0,0,0,0.7)]"
                                 style="font-size: clamp(48px, 6vw, 78px);">
                                --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.5em;" class="align-top ml-1">--</span>
                            </div>
                            <div class="mt-3 text-[9px] uppercase tracking-[3px] font-bold opacity-70" style="color: var(--accent);">— Menuju —</div>
                            <div class="mt-1">
                                <span id="nextLabel" class="font-bold uppercase tracking-wider text-white text-sm">—</span>
                            </div>
                            <div id="nextCountdown" class="font-digital text-base font-bold text-white tabular-nums mt-0.5">--:--:--</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT planets -->
            <div class="grid gap-3">
                <?php
                $rightPrayers = [
                    ['asr',     'Ashar',   'ashar',   '🌇'],
                    ['maghrib', 'Maghrib', 'maghrib', '🌆'],
                    ['isha',    'Isya',    'isya',    '🌙'],
                ];
                foreach ($rightPrayers as [$key, $label, $imamKey, $icon]):
                    $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                ?>
                <div class="prayer planet-card" data-key="<?= $key ?>">
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg shrink-0"
                             style="background: radial-gradient(circle at 30% 30%, color-mix(in srgb, var(--accent) 60%, white), var(--primary));"><?= $icon ?></div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[10px] uppercase tracking-[2px] text-slate-400 font-bold"><?= mc_e($label) ?></div>
                            <div class="font-digital text-2xl font-black text-white tabular-nums" data-time>--:--</div>
                            <?php if ($imam && $showImam): ?>
                                <div class="text-[10px] mt-0.5 truncate" style="color: color-mix(in srgb, var(--accent) 70%, white);"><?= mc_e($imam) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
