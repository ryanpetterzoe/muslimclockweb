<?php
/**
 * Layout: Terminal
 * Estetika code editor / hacker terminal: monospace, hijau di latar hitam,
 * nomor baris, prompt $, blinking cursor.
 */
?>
<?php
  $showImam      = (string)mc_setting('show_imam',      '1') !== '0';
  $showCountdown = (string)mc_setting('show_countdown', '1') !== '0';
?>
<style>
    .layout-terminal {
        background: #0a0e0a;
        font-family: 'JetBrains Mono', 'Share Tech Mono', 'Courier New', monospace;
        position: relative;
        color: #4ade80;
    }
    .layout-terminal::before {
        /* CRT scanlines */
        content: "";
        position: absolute; inset: 0;
        background-image: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(74, 222, 128, 0.025) 3px);
        pointer-events: none;
    }
    .layout-terminal .term-window {
        background: #050805;
        border: 1px solid rgba(74, 222, 128, 0.3);
        border-radius: 8px;
        overflow: hidden;
        position: relative;
    }
    .layout-terminal .term-titlebar {
        background: linear-gradient(180deg, #11181b, #080c08);
        border-bottom: 1px solid rgba(74, 222, 128, 0.25);
        padding: 8px 14px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .layout-terminal .term-dot {
        width: 11px; height: 11px;
        border-radius: 50%;
    }
    .layout-terminal .term-content {
        padding: 18px 22px;
        line-height: 1.55;
    }
    .layout-terminal .term-content .ln {
        display: grid;
        grid-template-columns: 32px 1fr;
        gap: 12px;
        font-size: 14px;
    }
    .layout-terminal .term-content .ln-num {
        color: rgba(74, 222, 128, 0.4);
        text-align: right;
        user-select: none;
    }
    .layout-terminal .kw      { color: #c084fc; }   /* keyword */
    .layout-terminal .var     { color: #38bdf8; }   /* variable */
    .layout-terminal .str     { color: #fde047; }   /* string */
    .layout-terminal .num     { color: #fb923c; }   /* number */
    .layout-terminal .comment { color: rgba(74, 222, 128, 0.45); font-style: italic; }
    .layout-terminal .punc    { color: rgba(255,255,255,0.5); }

    .layout-terminal .blinking-cursor {
        display: inline-block;
        width: 0.55em; height: 1em;
        background: var(--accent);
        margin-left: 2px;
        animation: blink-cursor 1s steps(2) infinite;
        vertical-align: middle;
    }
    @keyframes blink-cursor { 50% { opacity: 0; } }

    .layout-terminal .prompt-line {
        font-size: 18px;
        color: #4ade80;
    }
    .layout-terminal .prompt-line .promptchar { color: var(--accent); }
    .layout-terminal .prompt-line .arg { color: #fde047; }

    .layout-terminal .big-clock {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        color: var(--accent);
        text-shadow:
            0 0 10px var(--accent),
            0 0 20px color-mix(in srgb, var(--accent) 60%, transparent),
            0 0 40px color-mix(in srgb, var(--accent) 30%, transparent);
        letter-spacing: -2px;
        font-size: clamp(90px, 11vw, 160px);
        line-height: 0.95;
    }

    .layout-terminal .prayer-row {
        display: grid;
        grid-template-columns: 32px 24px 1fr 80px;
        gap: 10px;
        align-items: center;
        padding: 8px 14px;
        border-bottom: 1px dashed rgba(74, 222, 128, 0.12);
        font-size: 14px;
    }
    .layout-terminal .prayer-row.next {
        background: rgba(74, 222, 128, 0.07);
        border-left: 3px solid var(--accent);
    }
    .layout-terminal .prayer-row.next [data-time] { color: var(--accent); }
    .layout-terminal .prayer-row .arrow {
        color: rgba(74, 222, 128, 0.3);
    }
    .layout-terminal .prayer-row.next .arrow {
        color: var(--accent);
    }
    .layout-terminal .prayer-row .pname { color: #38bdf8; font-weight: 700; }
    .layout-terminal .prayer-row [data-time] {
        font-family: monospace;
        font-weight: 800;
        color: #fde047;
        text-align: right;
    }
    .layout-terminal .badge-status {
        background: var(--accent);
        color: #050805;
        padding: 2px 8px;
        font-weight: 700;
        font-size: 10px;
        border-radius: 3px;
    }
</style>

<div class="layout-terminal h-screen w-screen relative overflow-hidden">
    <div class="relative z-10 h-full grid p-5" style="grid-template-rows: 1fr auto auto; gap: 12px;">

        <!-- MAIN TERMINAL -->
        <div class="term-window grid" style="grid-template-rows: auto 1fr; min-height: 0;">
            <!-- title bar -->
            <div class="term-titlebar">
                <div class="term-dot" style="background:#ef4444"></div>
                <div class="term-dot" style="background:#fbbf24"></div>
                <div class="term-dot" style="background:#22c55e"></div>
                <span class="text-xs ml-2" style="color: rgba(74,222,128,0.6);">~/masjid/jadwal-sholat — bash — 80×24</span>
                <span class="ml-auto text-xs" style="color: rgba(74,222,128,0.4);">⌬ <?= mc_e($masjid) ?></span>
            </div>

            <!-- term content -->
            <div class="term-content grid gap-5" style="grid-template-columns: 1.3fr 1fr; min-height: 0;">
                <!-- LEFT: code-style prayer schedule -->
                <div>
                    <div class="prompt-line mb-3">
                        <span class="promptchar">$</span>
                        <span class="text-emerald-300">prayer</span>
                        <span class="arg">--today</span>
                        <span class="arg">--format=table</span>
                    </div>

                    <div class="space-y-0">
                        <?php
                        $prayers = [
                            ['fajr',     'Subuh',   'subuh',   '01'],
                            ['sunrise',  'Syuruq',  null,      '02'],
                            ['dhuhr',    $isFriday ? "Jum'at" : 'Dzuhur', $isFriday ? 'jumat' : 'dzuhur', '03'],
                            ['asr',      'Ashar',   'ashar',   '04'],
                            ['maghrib',  'Maghrib', 'maghrib', '05'],
                            ['isha',     'Isya',    'isya',    '06'],
                        ];
                        foreach ($prayers as [$key, $label, $imamKey, $idx]):
                            $imam = $imamKey ? ($imamRows[$imamKey]['imam_name'] ?? '') : '';
                        ?>
                        <div class="prayer prayer-row" data-key="<?= $key ?>">
                            <span style="color: rgba(74,222,128,0.4);"><?= $idx ?></span>
                            <span class="arrow">▸</span>
                            <span>
                                <span class="pname"><?= str_pad(mc_e($label), 8) ?></span>
                                <?php if ($imam && $showImam): ?>
                                    <span class="comment ml-2">// imam: <?= mc_e($imam) ?></span>
                                <?php endif; ?>
                            </span>
                            <span data-time>--:--</span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="prompt-line mt-4">
                        <span class="promptchar">$</span>
                        <span class="blinking-cursor"></span>
                    </div>
                </div>

                <!-- RIGHT: huge digital clock + meta -->
                <div class="border-l pl-6 flex flex-col" style="border-color: rgba(74,222,128,0.15);">
                    <div class="comment text-xs mb-2"># SYSTEM TIME</div>
                    <div id="digital" class="big-clock">
                        --<span style="color:#fde047">:</span>--<span style="color:#fde047; font-size: 0.35em;" class="align-top ml-2">--</span>
                    </div>

                    <div class="grid gap-2 mt-6 text-sm">
                        <div class="flex justify-between border-b border-emerald-900/30 pb-1.5">
                            <span class="comment">date</span>
                            <span id="greg-date" style="color: #fde047;">—</span>
                        </div>
                        <div class="flex justify-between border-b border-emerald-900/30 pb-1.5">
                            <span class="comment">hijri</span>
                            <span id="hij-date" style="color: #fde047;">—</span>
                        </div>
                        <div class="flex justify-between border-b border-emerald-900/30 pb-1.5">
                            <span class="comment">next</span>
                            <span><span id="nextLabel" class="kw">—</span></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="comment">eta</span>
                            <span id="nextCountdown" class="num font-bold tabular-nums">--:--:--</span>
                        </div>
                    </div>

                    <div class="mt-auto pt-4">
                        <div class="comment text-xs mb-1">// status</div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs" style="color: #4ade80;">ONLINE</span>
                            <span class="badge-status ml-2">LIVE</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</div>
