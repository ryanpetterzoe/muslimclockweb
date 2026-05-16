<?php
/**
 * Layout: Neon
 * Cyberpunk modern — jam digital glow, kartu glassmorphism, grid layar gelap.
 */
?>
<style>
    .layout-neon {
        background: radial-gradient(ellipse at top, #1a1f3a 0%, #050816 60%);
    }
    .layout-neon::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(rgba(0,0,0,0) 0%, rgba(0,0,0,0) 100%),
            repeating-linear-gradient(0deg, transparent, transparent 39px, rgba(255,255,255,0.02) 40px),
            repeating-linear-gradient(90deg, transparent, transparent 39px, rgba(255,255,255,0.02) 40px);
        pointer-events: none;
    }
    .layout-neon .neon-glow {
        text-shadow:
            0 0 10px var(--accent),
            0 0 20px var(--accent),
            0 0 40px color-mix(in srgb, var(--accent) 50%, transparent);
    }
    .layout-neon .neon-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.04), rgba(255,255,255,0.01));
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        backdrop-filter: blur(12px);
        position: relative;
        overflow: hidden;
        transition: all .3s;
    }
    .layout-neon .neon-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--accent), transparent);
        opacity: 0.5;
    }
    .layout-neon .prayer.next {
        border-color: var(--accent);
        background: linear-gradient(135deg,
            color-mix(in srgb, var(--accent) 15%, transparent),
            transparent);
        box-shadow:
            0 0 0 1px var(--accent),
            0 0 30px -5px var(--accent),
            inset 0 0 20px -5px color-mix(in srgb, var(--accent) 30%, transparent);
    }
    .layout-neon .prayer.next [data-time] {
        color: var(--accent);
        text-shadow: 0 0 10px color-mix(in srgb, var(--accent) 80%, transparent);
    }
    .layout-neon .neon-frame {
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 20px;
        padding: 30px 40px;
        background: linear-gradient(135deg, rgba(255,255,255,0.03), rgba(0,0,0,0.2));
        box-shadow:
            0 20px 60px -10px rgba(0,0,0,0.6),
            inset 0 1px 0 rgba(255,255,255,0.08);
        position: relative;
    }
    .layout-neon .neon-frame::after {
        content: "";
        position: absolute;
        inset: -1px;
        border-radius: 20px;
        padding: 1px;
        background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 60%, transparent), transparent 50%, color-mix(in srgb, var(--primary) 60%, transparent));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }
</style>

<div class="layout-neon h-screen w-screen relative overflow-hidden">

    <!-- soft slideshow di belakang, blur kuat -->
    <div id="slideshow" class="absolute inset-0 opacity-15" style="filter: blur(8px);">
        <?php if ($slides) foreach ($slides as $i => $s): ?>
            <?php if ($s['type']==='video'): ?>
                <video class="slide<?= $i===0?' active':'' ?>" muted playsinline loop preload="metadata"><source src="<?= mc_e($s['path']) ?>"></video>
            <?php else: ?>
                <div class="slide<?= $i===0?' active':'' ?>" style="background-image:url('<?= mc_e($s['path']) ?>')"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- glowing orbs -->
    <div class="absolute -top-40 -right-40 w-[500px] h-[500px] rounded-full opacity-20 blur-3xl" style="background: var(--accent);"></div>
    <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] rounded-full opacity-25 blur-3xl" style="background: var(--primary);"></div>

    <div class="relative z-10 h-full grid" style="grid-template-rows: 80px 1fr auto auto;">

        <!-- HEADER -->
        <header class="flex items-center justify-between px-10 border-b" style="border-color: rgba(255,255,255,0.08);">
            <div class="flex items-center gap-3">
                <?php if ($logo): ?>
                    <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain rounded-xl">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-deep" style="background: var(--primary); color: var(--accent); box-shadow: 0 0 30px -5px var(--accent);">&#x262A;</div>
                <?php endif; ?>
                <div>
                    <div class="text-xl font-bold tracking-wide neon-glow" style="color: var(--accent);">
                        <?= mc_e($masjid) ?>
                    </div>
                    <div class="text-xs text-slate-400 tracking-wider"><?= mc_e($alamat) ?></div>
                </div>
            </div>
            <div class="text-right neon-card px-4 py-2">
                <div id="greg-date" class="text-sm font-bold text-white">—</div>
                <div id="hij-date" class="text-xs mt-0.5" style="color: var(--accent);">—</div>
            </div>
        </header>

        <!-- HERO: jam digital glow -->
        <main class="grid items-center px-10 gap-8" style="grid-template-columns: 1.4fr 1fr;">
            <!-- Big neon digital clock -->
            <div class="neon-frame text-center">
                <div class="text-[10px] uppercase tracking-[6px] text-slate-400 font-bold mb-3">⬢ WAKTU LOKAL ⬢</div>
                <div id="digital" class="font-digital font-black tracking-[0.06em] leading-none neon-glow"
                     style="font-size: clamp(120px, 14vw, 200px); color: #fff;">
                    --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.4em;" class="align-top ml-3">--</span>
                </div>
                <div class="mt-6 inline-flex items-center gap-3 px-5 py-2 rounded-full"
                     style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
                    <span class="w-2 h-2 rounded-full animate-pulse" style="background: var(--accent); box-shadow: 0 0 10px var(--accent);"></span>
                    <span class="text-xs uppercase tracking-[3px] text-slate-300 font-semibold">Menuju</span>
                    <span id="nextLabel" class="text-sm font-bold uppercase neon-glow" style="color: var(--accent);">—</span>
                    <span class="text-slate-500">·</span>
                    <span id="nextCountdown" class="font-digital text-sm font-semibold text-white">--:--:--</span>
                </div>
            </div>

            <!-- Analog clock + small info -->
            <div class="flex flex-col items-center justify-center gap-4">
                <?php $clockSize='w-44 h-44'; include __DIR__ . '/_analog_clock.php'; ?>
            </div>
        </main>

        <!-- 6 prayer cards bawah -->
        <section class="px-10 pb-6">
            <div class="grid grid-cols-6 gap-3">
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
                <div class="prayer neon-card p-4 text-center" data-key="<?= $key ?>">
                    <div class="text-[10px] uppercase tracking-[3px] font-bold text-slate-400 mb-2"><?= mc_e($label) ?></div>
                    <div class="font-digital text-3xl font-black text-white tabular-nums" data-time>--:--</div>
                    <?php if ($imam): ?>
                        <div class="text-[10px] mt-2 truncate" style="color: var(--accent);">⊳ <?= mc_e($imam) ?></div>
                    <?php else: ?>
                        <div class="text-[10px] mt-2 text-slate-700">·</div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
