<?php
/**
 * Footer:
 *  - Quran area (mode: 'marquee' atau 'card', diatur admin)
 *  - Running text masjid (selalu marquee)
 *  - Brand bar
 */
$quranArabSpeed  = max(20, (int)mc_setting('quran_arab_speed', 50));
$quranTransSpeed = max(20, (int)mc_setting('quran_trans_speed', 60));
$rtSpeed         = max(20, (int)mc_setting('running_text_speed', 60));
$quranDisplay    = mc_setting('quran_display', 'marquee'); // 'marquee' | 'card'
?>
<footer class="grid relative z-10" style="grid-template-rows: auto auto 28px;">

    <?php if ($quranDisplay === 'card'): ?>
        <!-- ===== MODE: CARD (statis, ganti otomatis lewat JS tiap 30 detik) ===== -->
        <div class="border-t-2 px-6 py-4 grid items-center gap-6"
             style="background: linear-gradient(90deg, var(--primary-dark), var(--primary), var(--primary-dark));
                    border-color: color-mix(in srgb, var(--accent) 60%, transparent);
                    grid-template-columns: 1fr auto;">
            <div class="min-w-0 text-center md:text-left" id="quranCard">
                <div class="font-arabic text-white" id="quranArab" dir="rtl"
                     style="font-size: clamp(20px, 1.8vw, 28px); line-height: 1.5; word-break: break-word;
                            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">—</div>
                <div class="italic text-amber-100 mt-1.5" id="quranTrans"
                     style="font-size: clamp(12px, 0.95vw, 15px); line-height: 1.4;
                            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">—</div>
            </div>
            <div class="text-right whitespace-nowrap pl-4 shrink-0">
                <div class="text-[10px] uppercase tracking-[3px] text-slate-300 font-semibold">Cuplikan</div>
                <div class="font-bold text-sm mt-0.5" id="quranRef" style="color: var(--accent);">—</div>
            </div>
        </div>
        <!-- hidden duplicates (JS tetap mengisi, tidak dipakai di mode card) -->
        <span class="hidden" id="quranArab2"></span>
        <span class="hidden" id="quranTrans2"></span>
        <span class="hidden" id="quranRef2"></span>
    <?php else: ?>
        <!-- ===== MODE: MARQUEE (default, 2 baris arah berlawanan) ===== -->
        <div class="grid" style="grid-template-rows: auto auto;">
            <!-- Arab + Referensi: gerak kanan -> kiri (RTL) -->
            <div class="overflow-hidden border-t-2"
                 style="background: linear-gradient(90deg, var(--primary-dark), var(--primary), var(--primary-dark));
                        border-color: color-mix(in srgb, var(--accent) 60%, transparent);">
                <div class="marquee marquee-rtl" style="height: 50px;">
                    <div class="marquee-inner h-full flex items-center" style="animation-duration: <?= $quranArabSpeed ?>s;">
                        <span class="font-arabic text-white px-8" id="quranArab" dir="rtl" style="font-size: 30px; line-height: 1;">—</span>
                        <span class="font-bold whitespace-nowrap px-6" id="quranRef" style="color: var(--accent); font-size: 14px;">—</span>
                        <span style="color: color-mix(in srgb, var(--accent) 50%, transparent);" class="px-8 text-2xl">◈</span>
                        <span class="font-arabic text-white px-8" id="quranArab2" dir="rtl" style="font-size: 30px; line-height: 1;">—</span>
                        <span class="font-bold whitespace-nowrap px-6" id="quranRef2" style="color: var(--accent); font-size: 14px;">—</span>
                        <span style="color: color-mix(in srgb, var(--accent) 50%, transparent);" class="px-8 text-2xl">◈</span>
                    </div>
                </div>
            </div>
            <!-- Terjemahan: gerak kiri -> kanan (LTR) -->
            <div class="overflow-hidden bg-slate-900/90">
                <div class="marquee" style="height: 36px;">
                    <div class="marquee-inner h-full flex items-center" style="animation-duration: <?= $quranTransSpeed ?>s;">
                        <span class="italic px-8 text-amber-100" id="quranTrans" style="font-size: 15px;">—</span>
                        <span class="text-slate-600 px-8">·</span>
                        <span class="italic px-8 text-amber-100" id="quranTrans2" style="font-size: 15px;">—</span>
                        <span class="text-slate-600 px-8">·</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Running text masjid (selalu marquee) -->
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
