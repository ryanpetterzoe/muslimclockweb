<?php
/**
 * Layout: Split
 * Split-screen: dua slideshow paralel kiri-kanan dengan offset waktu berbeda.
 * Jam dan jadwal di tengah sebagai vertical center bar.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-split { background: #000; position: relative; }
    .layout-split .slide-pane {
        position: relative;
        overflow: hidden;
        background: #000;
    }
    .layout-split .slide-pane::after {
        content: "";
        position: absolute; inset: 0;
        background: linear-gradient(180deg, transparent 0%, transparent 60%, rgba(0,0,0,0.7) 100%);
        pointer-events: none;
    }
    .layout-split .slide-pane.left::after  {
        background: linear-gradient(90deg, transparent 0%, transparent 70%, rgba(0,0,0,0.6) 100%), linear-gradient(180deg, rgba(0,0,0,0.3) 0%, transparent 30%, transparent 70%, rgba(0,0,0,0.6) 100%);
    }
    .layout-split .slide-pane.right::after {
        background: linear-gradient(270deg, transparent 0%, transparent 70%, rgba(0,0,0,0.6) 100%), linear-gradient(180deg, rgba(0,0,0,0.3) 0%, transparent 30%, transparent 70%, rgba(0,0,0,0.6) 100%);
    }

    .layout-split .center-bar {
        background: linear-gradient(180deg, rgba(10,15,30,0.92), rgba(5,8,16,0.96));
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-left: 2px solid color-mix(in srgb, var(--accent) 60%, transparent);
        border-right: 2px solid color-mix(in srgb, var(--accent) 60%, transparent);
        position: relative;
    }
    .layout-split .center-bar::before,
    .layout-split .center-bar::after {
        content: "";
        position: absolute;
        left: 0; right: 0;
        height: 80px;
        background: linear-gradient(180deg, var(--accent), transparent);
        opacity: 0.15;
    }
    .layout-split .center-bar::before { top: 0; }
    .layout-split .center-bar::after  { bottom: 0; transform: scaleY(-1); }

    .layout-split .pane-tag {
        position: absolute; top: 16px;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.15);
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: var(--accent);
        font-weight: 700;
        z-index: 5;
    }
    .layout-split .pane-tag.left  { left: 16px; }
    .layout-split .pane-tag.right { right: 16px; }

    .layout-split .pl-row {
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: center;
        gap: 10px;
        padding: 11px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        transition: all .3s;
    }
    .layout-split .pl-row.next {
        background: linear-gradient(90deg, color-mix(in srgb, var(--accent) 22%, transparent), transparent);
        border-left: 3px solid var(--accent);
    }
    .layout-split .pl-row.next [data-time] { color: var(--accent); }
</style>

<div class="layout-split h-screen w-screen relative overflow-hidden grid"
     style="grid-template-columns: 1fr 360px 1fr; grid-template-rows: 1fr auto auto;">

    <!-- LEFT slideshow pane -->
    <section class="slide-pane left" style="grid-row: 1;">
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
        <div class="pane-tag left">◀ MASJID</div>
        <!-- Bottom info on left pane -->
        <div class="absolute bottom-5 left-5 right-20 z-5">
            <div class="text-white text-2xl font-extrabold drop-shadow-[0_4px_12px_rgba(0,0,0,0.9)]"><?= mc_e($masjid) ?></div>
            <div class="text-xs text-slate-200/80 drop-shadow-md mt-1"><?= mc_e($alamat) ?></div>
        </div>
    </section>

    <!-- CENTER BAR: clock + schedule -->
    <aside class="center-bar flex flex-col" style="grid-row: 1;">
        <!-- top date -->
        <div class="px-5 pt-5 text-center">
            <?php if ($logo): ?>
                <img src="<?= mc_e($logo) ?>" alt="logo" class="w-12 h-12 object-contain mx-auto mb-2">
            <?php else: ?>
                <div class="w-12 h-12 mx-auto rounded-xl flex items-center justify-center text-2xl mb-2"
                     style="background: var(--primary); color: var(--accent);">&#x262A;</div>
            <?php endif; ?>
            <div id="greg-date" class="text-sm font-bold text-white">—</div>
            <div id="hij-date" class="text-xs mt-0.5" style="color: var(--accent);">—</div>
        </div>

        <!-- middle clock -->
        <div class="px-4 py-5 text-center border-t border-b border-white/10 my-3">
            <div class="text-[9px] uppercase tracking-[5px] font-bold mb-2" style="color: var(--accent);">⌬ NOW ⌬</div>
            <div id="digital" class="font-digital font-black tracking-tight text-white leading-none"
                 style="font-size: clamp(48px, 5.5vw, 78px);">
                --<span style="color: var(--accent);">:</span>--<span style="color: var(--accent); font-size: 0.4em;" class="align-top ml-1">--</span>
            </div>
            <div class="mt-3">
                <div class="text-[9px] uppercase tracking-[3px] text-slate-400 font-bold">Menuju</div>
                <div class="mt-1">
                    <span id="nextLabel" class="text-base font-extrabold uppercase" style="color: var(--accent);">—</span>
                </div>
                <div id="nextCountdown" class="font-digital text-sm font-bold text-white tabular-nums mt-1">--:--:--</div>
            </div>
        </div>

        <!-- prayer list -->
        <div class="flex-1 flex flex-col justify-around">
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
            <div class="prayer pl-row" data-key="<?= $key ?>">
                <div class="min-w-0">
                    <div class="text-sm font-bold uppercase tracking-wider text-white"><?= mc_e($label) ?></div>
                    <?php if ($imam && $showImam): ?>
                        <div class="text-[9px] mt-0.5 truncate" style="color: color-mix(in srgb, var(--accent) 75%, white);"><?= mc_e($imam) ?></div>
                    <?php endif; ?>
                </div>
                <div class="font-digital text-xl font-bold text-white tabular-nums" data-time>--:--</div>
            </div>
            <?php endforeach; ?>
        </div>
    </aside>

    <!-- RIGHT slideshow pane (offset/reverse) -->
    <section class="slide-pane right" style="grid-row: 1;">
        <div id="slideshow2" class="absolute inset-0" style="<?= ((string)mc_setting('show_slideshow','1')==='0')?'display:none;':'' ?>">
            <?php
            // Reverse order so right pane shows DIFFERENT slide than left
            $rslides = $slides ? array_reverse($slides) : [];
            if (!$rslides): ?>
                <div class="slide active" style="background:#0a1a3c url('assets/img/default-bg.svg') center/cover"></div>
            <?php else: foreach ($rslides as $i => $s): ?>
                <?php if ($s['type']==='video'): ?>
                    <video class="slide<?= $i===0?' active':'' ?> w-full h-full object-cover" autoplay muted playsinline loop preload="auto"><source src="<?= mc_e($s['path']) ?>" type="<?= mc_e(mc_video_mime($s['path'])) ?>"></video>
                <?php else: ?>
                    <div class="slide<?= $i===0?' active':'' ?>" style="background-image:url('<?= mc_e($s['path']) ?>')"></div>
                <?php endif; ?>
            <?php endforeach; endif; ?>
        </div>
        <div class="pane-tag right">JADWAL ▶</div>
        <div class="absolute bottom-5 right-5 left-20 z-5 text-right">
            <div class="text-[10px] uppercase tracking-[3px] font-bold text-white/80 drop-shadow-md">Hari Ini</div>
            <div class="text-2xl font-extrabold text-white drop-shadow-[0_4px_12px_rgba(0,0,0,0.9)] mt-1">
                <span style="color: var(--accent);" id="nextLabel2">—</span> sebentar lagi
            </div>
            <script>
                // Mirror nextLabel to nextLabel2
                document.addEventListener('DOMContentLoaded', () => {
                    const obs = new MutationObserver(() => {
                        const t = document.getElementById('nextLabel')?.textContent;
                        const m = document.getElementById('nextLabel2');
                        if (t && m) m.textContent = t;
                    });
                    const n = document.getElementById('nextLabel');
                    if (n) obs.observe(n, { childList: true, characterData: true, subtree: true });
                });
            </script>
        </div>
    </section>

    <?php include __DIR__ . '/_footer.php'; ?>
</div>
