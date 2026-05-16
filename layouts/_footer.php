<?php
/**
 * Footer: Quran (2 baris marquee — Arab LTR + terjemahan RTL) +
 * running text + brand bar.
 */
$quranArabSpeed = max(20, (int)mc_setting('quran_arab_speed', 50));
$quranTransSpeed = max(20, (int)mc_setting('quran_trans_speed', 60));
$rtSpeed = max(20, (int)mc_setting('running_text_speed', 60));
?>
<footer class="grid relative z-10" style="grid-template-rows: auto auto auto 28px;">

    <!-- Quran: ARAB + REFERENSI, scroll LTR (kiri → kanan) -->
    <div class="overflow-hidden border-t-2"
         style="background: linear-gradient(90deg, var(--primary-dark), var(--primary), var(--primary-dark));
                border-color: color-mix(in srgb, var(--accent) 60%, transparent);">
        <div class="marquee" style="height: 50px;">
            <div class="marquee-inner h-full flex items-center" style="animation-duration: <?= $quranArabSpeed ?>s;">
                <span class="font-arabic text-white px-8" id="quranArab" dir="rtl" style="font-size: 30px; line-height: 1;">—</span>
                <span class="font-bold whitespace-nowrap px-6" id="quranRef" style="color: var(--accent); font-size: 14px;">—</span>
                <span style="color: color-mix(in srgb, var(--accent) 50%, transparent);" class="px-8 text-2xl">◈</span>
                <!-- duplicate for seamless loop -->
                <span class="font-arabic text-white px-8" id="quranArab2" dir="rtl" style="font-size: 30px; line-height: 1;">—</span>
                <span class="font-bold whitespace-nowrap px-6" id="quranRef2" style="color: var(--accent); font-size: 14px;">—</span>
                <span style="color: color-mix(in srgb, var(--accent) 50%, transparent);" class="px-8 text-2xl">◈</span>
            </div>
        </div>
    </div>

    <!-- Quran: TERJEMAHAN, scroll RTL (kanan → kiri) -->
    <div class="overflow-hidden bg-slate-900/90">
        <div class="marquee marquee-rtl" style="height: 36px;">
            <div class="marquee-inner h-full flex items-center" style="animation-duration: <?= $quranTransSpeed ?>s;">
                <span class="italic px-8 text-amber-100" id="quranTrans" style="font-size: 15px;">—</span>
                <span class="text-slate-600 px-8">·</span>
                <span class="italic px-8 text-amber-100" id="quranTrans2" style="font-size: 15px;">—</span>
                <span class="text-slate-600 px-8">·</span>
            </div>
        </div>
    </div>

    <!-- Running text masjid -->
    <div class="bg-slate-950 overflow-hidden marquee" style="height: 38px;">
        <div class="marquee-inner font-semibold text-white" style="animation-duration: <?= $rtSpeed ?>s; font-size: 15px;">
            <?php if ($running): ?>
                <?= mc_e(implode('   •   ', $running)) ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php endif; ?>
        </div>
    </div>

    <!-- Brand bar -->
    <div class="text-center text-xs font-extrabold leading-7 tracking-[3px] uppercase"
         style="background: var(--accent); color: var(--primary-dark);">
        <?= mc_e($masjid) ?> · Muslim Clock Web
    </div>
</footer>
