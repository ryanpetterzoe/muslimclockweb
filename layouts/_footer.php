<?php
/**
 * Footer: Quran verse as marquee + running text + brand bar.
 */
?>
<footer class="grid relative z-10" style="grid-template-rows: auto auto 28px;">
    <!-- Quran verse: full marquee, never cut -->
    <div class="border-t-2 px-0 py-2 overflow-hidden flex items-center"
         style="background: linear-gradient(90deg, var(--primary-dark), var(--primary), var(--primary-dark)); border-color: color-mix(in srgb, var(--accent) 60%, transparent); height: 56px;">
        <div class="marquee marquee-rtl flex-1 h-full">
            <div class="marquee-inner h-full flex items-center" style="animation-duration: 50s;">
                <span class="font-arabic text-white px-6" id="quranArab" style="font-size: 28px;">—</span>
                <span class="text-amber-100 italic px-4" id="quranTrans" style="font-size: 16px;">—</span>
                <span class="font-bold px-4 whitespace-nowrap" id="quranRef" style="color: var(--accent); font-size: 14px;">—</span>
                <span class="text-slate-500 px-8">◈</span>
                <!-- duplicate for seamless loop -->
                <span class="font-arabic text-white px-6" style="font-size: 28px;" id="quranArab2">—</span>
                <span class="text-amber-100 italic px-4" id="quranTrans2" style="font-size: 16px;">—</span>
                <span class="font-bold px-4 whitespace-nowrap" id="quranRef2" style="color: var(--accent); font-size: 14px;">—</span>
                <span class="text-slate-500 px-8">◈</span>
            </div>
        </div>
    </div>

    <!-- Running text -->
    <div class="bg-slate-900 marquee" style="height: 40px;">
        <div class="marquee-inner font-semibold text-white" style="animation-duration: <?= (int)mc_setting('running_text_speed','60') ?>s; font-size: 16px;">
            <?php if ($running): ?>
                <?= mc_e(implode('   •   ', $running)) ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php endif; ?>
        </div>
    </div>

    <!-- Brand bar -->
    <div class="text-center text-xs font-extrabold leading-7 tracking-[3px] uppercase" style="background: var(--accent); color: var(--primary-dark);">
        <?= mc_e($masjid) ?> · Muslim Clock Web
    </div>
</footer>
