<?php
/**
 * Layout: Magazine
 * Gaya editorial majalah: typography besar, hero photo besar di kiri, jadwal di kanan
 * dengan grid bergaya koran modern.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-magazine {
        background: #f8f5ee;
        color: #111;
    }
    .layout-magazine .ed-header {
        border-bottom: 4px double #111;
    }
    .layout-magazine .ed-issue {
        font-family: var(--font-display, 'Inter', serif);
        letter-spacing: .35em;
        text-transform: uppercase;
        font-weight: 800;
    }
    .layout-magazine .ed-title {
        font-family: 'Amiri', 'Playfair Display', Georgia, serif;
        font-weight: 700;
        line-height: .95;
    }
    .layout-magazine .hero-photo {
        position: relative;
        background: #222;
        overflow: hidden;
        border-radius: 4px;
    }
    .layout-magazine .hero-photo .gradient-veil {
        position: absolute; inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0) 30%, rgba(0,0,0,.85) 100%);
    }
    .layout-magazine .hero-meta {
        position: absolute;
        left: 24px; right: 24px; bottom: 22px;
        color: #fff;
    }
    .layout-magazine .ed-divider {
        height: 1px; background: #111; opacity: .15;
    }
    .layout-magazine .ed-prayer {
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: baseline;
        padding: 14px 0;
        border-bottom: 1px solid rgba(0,0,0,0.12);
    }
    .layout-magazine .ed-prayer.next {
        background: linear-gradient(90deg, color-mix(in srgb, var(--accent) 25%, transparent), transparent 70%);
        padding-left: 14px;
        border-left: 4px solid var(--accent);
    }
    .layout-magazine .ed-prayer.next .pname {
        color: color-mix(in srgb, var(--primary) 70%, black);
    }
    .layout-magazine .ed-prayer .pname {
        font-family: 'Amiri', Georgia, serif;
        font-size: 30px;
        font-weight: 700;
    }
    .layout-magazine .ed-prayer [data-time] {
        font-family: var(--font-digital, 'Orbitron', monospace);
        font-size: 32px;
        font-weight: 800;
        letter-spacing: .02em;
        color: #111;
    }
    .layout-magazine .col-rules {
        column-rule: 1px solid rgba(0,0,0,0.12);
    }
    .layout-magazine .pull-quote {
        border-left: 4px solid var(--accent);
        padding-left: 14px;
        font-family: 'Amiri', Georgia, serif;
        font-style: italic;
        font-size: 16px;
        color: #444;
        line-height: 1.45;
    }
    .layout-magazine .digital-clock {
        font-family: var(--font-digital, 'Orbitron', monospace);
        font-weight: 900;
        line-height: .9;
        letter-spacing: -0.02em;
        color: #111;
    }
</style>

<div class="layout-magazine h-screen w-screen relative overflow-hidden grid" style="grid-template-rows: 92px 1fr auto auto;">

    <!-- HEADER (masthead) -->
    <header class="ed-header px-10 flex items-end justify-between pb-3 pt-5">
        <div class="flex items-end gap-5">
            <?php if ($logo): ?>
                <img src="<?= mc_e($logo) ?>" alt="logo" class="w-14 h-14 object-contain">
            <?php else: ?>
                <div class="w-14 h-14 flex items-center justify-center text-3xl rounded" style="background:#111; color: var(--accent);">&#x262A;</div>
            <?php endif; ?>
            <div>
                <div class="ed-issue text-[10px] text-neutral-500">Vol. MMXXVI · Jadwal Harian</div>
                <div class="ed-title text-4xl mt-0.5"><?= mc_e($masjid) ?></div>
            </div>
        </div>
        <div class="text-right">
            <div id="greg-date" class="text-base font-bold">—</div>
            <div id="hij-date" class="text-xs mt-0.5" style="color: color-mix(in srgb, var(--primary) 80%, black);">—</div>
            <div class="ed-issue text-[9px] text-neutral-500 mt-1.5"><?= mc_e($alamat) ?></div>
        </div>
    </header>

    <!-- BODY: 2 columns editorial -->
    <main class="grid gap-8 px-10 py-6 min-h-0" style="grid-template-columns: 1.2fr 1fr;">

        <!-- LEFT: hero photo + huge clock -->
        <section class="grid gap-5" style="grid-template-rows: 1fr auto;">
            <div class="hero-photo">
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
                <div class="gradient-veil"></div>
                <div class="hero-meta">
                    <div class="ed-issue text-[10px] mb-2" style="color: var(--accent);">— Cover Story</div>
                    <div class="ed-title text-3xl">Sholat tepat waktu, kunci ketenangan jiwa.</div>
                </div>
            </div>

            <!-- huge digital clock -->
            <div class="flex items-end justify-between gap-6 pt-3 border-t-4 border-double border-black/80">
                <div>
                    <div class="ed-issue text-[10px] text-neutral-500 mb-1">Waktu Lokal</div>
                    <div id="digital" class="digital-clock" style="font-size: clamp(80px, 9vw, 140px);">
                        --<span style="color: var(--primary);">:</span>--<span style="color: var(--primary); font-size: 0.4em;" class="align-top ml-2">--</span>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="ed-issue text-[10px] text-neutral-500 mb-1">Menuju</div>
                    <div id="nextLabel" class="ed-title text-2xl" style="color: var(--primary);">—</div>
                    <div id="nextCountdown" class="font-digital text-xl font-bold tabular-nums mt-1">--:--:--</div>
                </div>
            </div>
        </section>

        <!-- RIGHT: prayer schedule editorial -->
        <section>
            <div class="ed-issue text-[10px] text-neutral-500 mb-1">Section II</div>
            <h2 class="ed-title text-3xl mb-4 leading-tight">
                Jadwal Sholat<br>
                <span style="color: var(--primary);">Hari Ini</span>
            </h2>

            <div class="pull-quote mb-5">
                "Sesungguhnya sholat itu adalah kewajiban yang ditentukan waktunya atas orang-orang yang beriman."
                <span class="block not-italic text-xs font-bold mt-1" style="color: color-mix(in srgb, var(--primary) 70%, black);">— QS. An-Nisa: 103</span>
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
            <div class="prayer ed-prayer" data-key="<?= $key ?>">
                <div>
                    <div class="pname"><?= mc_e($label) ?> <span class="text-base text-neutral-400 ml-2"><?= mc_e($arab) ?></span></div>
                    <?php if ($imam && $showImam): ?>
                        <div class="text-[11px] uppercase tracking-[2px] mt-0.5 text-neutral-500">Imam — <?= mc_e($imam) ?></div>
                    <?php endif; ?>
                </div>
                <div data-time>--:--</div>
            </div>
            <?php endforeach; ?>
        </section>
    </main>

    <?php include __DIR__ . '/_footer.php'; ?>
</div>
