<?php
/**
 * Footer:
 *  - Quran area (mode: 'marquee' | 'card' | 'fullcard' | 'slide' | 'typewriter')
 *  - Running text masjid
 *  - Brand bar (selalu tampil)
 */
$quranArabSpeed  = max(20, (int)mc_setting('quran_arab_speed', 50));
$quranTransSpeed = max(20, (int)mc_setting('quran_trans_speed', 60));
$rtSpeed         = max(20, (int)mc_setting('running_text_speed', 60));
$quranDisplay    = mc_setting('quran_display', 'marquee');
$showQuran       = (string)mc_setting('show_quran',   '1') !== '0';
$showRunning     = (string)mc_setting('show_running', '1') !== '0';

// Build grid template rows dynamically
$rows = [];
if ($showQuran)   $rows[] = 'auto';
if ($showRunning) $rows[] = 'auto';
$gridRows = implode(' ', $rows);
?>
<footer class="grid relative z-10" style="grid-template-rows: <?= $gridRows ?>;">

    <?php if ($showQuran): ?>
        <?php if ($quranDisplay === 'card'): ?>
            <!-- MODE: CARD (compact) -->
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
            <span class="hidden" id="quranArab2"></span>
            <span class="hidden" id="quranTrans2"></span>
            <span class="hidden" id="quranRef2"></span>

        <?php elseif ($quranDisplay === 'fullcard'): ?>
            <!-- MODE: FULL CARD - teks lengkap auto-fit, font ngecil otomatis biar muat -->
            <div id="quranCard" class="quran-fullcard border-t-2 px-8 py-5 relative overflow-hidden"
                 style="background:
                            radial-gradient(circle at 0% 0%, color-mix(in srgb, var(--accent) 20%, transparent), transparent 50%),
                            linear-gradient(90deg, var(--primary-dark), var(--primary), var(--primary-dark));
                        border-color: color-mix(in srgb, var(--accent) 70%, transparent);">
                <!-- corner ornaments -->
                <div class="absolute top-2 left-3 text-2xl opacity-30" style="color: var(--accent);">﴿</div>
                <div class="absolute top-2 right-3 text-2xl opacity-30" style="color: var(--accent);">﴾</div>

                <div class="grid items-center gap-5" style="grid-template-columns: 1fr auto;">
                    <div class="min-w-0">
                        <!-- Arabic: full text, auto-fit via JS, NO line-clamp -->
                        <div id="quranArab" class="font-arabic text-white quran-autofit-arab text-center" dir="rtl"
                             style="line-height: 1.6; word-break: keep-all; overflow-wrap: break-word; max-height: 90px; overflow: hidden;">—</div>
                        <!-- Translation: full text, auto-fit, NO line-clamp -->
                        <div id="quranTrans" class="italic text-amber-100 mt-2 quran-autofit-trans text-center"
                             style="line-height: 1.45; word-break: normal; overflow-wrap: break-word; max-height: 56px; overflow: hidden;">—</div>
                    </div>
                    <div class="text-right whitespace-nowrap pl-5 shrink-0 border-l border-white/15">
                        <div class="text-[10px] uppercase tracking-[3px] text-slate-300 font-semibold">Al-Qur'an</div>
                        <div class="font-bold text-sm mt-1" id="quranRef" style="color: var(--accent);">—</div>
                        <div class="text-[10px] text-slate-400 mt-1" id="quranProgress">— / —</div>
                    </div>
                </div>
            </div>
            <span class="hidden" id="quranArab2"></span>
            <span class="hidden" id="quranTrans2"></span>
            <span class="hidden" id="quranRef2"></span>

        <?php elseif ($quranDisplay === 'slide'): ?>
            <!-- MODE: SLIDE - ayat masuk dari kanan, tampil 8 detik, geser keluar ke kiri -->
            <div id="quranCard" class="quran-slide border-t-2 px-8 py-4 relative overflow-hidden"
                 style="background: linear-gradient(90deg, var(--primary-dark), var(--primary), var(--primary-dark));
                        border-color: color-mix(in srgb, var(--accent) 60%, transparent);
                        min-height: 96px;">
                <div class="grid items-center gap-5 transition-all" style="grid-template-columns: 1fr auto;">
                    <div class="min-w-0 text-center">
                        <!-- Arabic full -->
                        <div id="quranArab" class="font-arabic text-white quran-slide-arab" dir="rtl"
                             style="font-size: clamp(20px, 2vw, 30px); line-height: 1.5; word-break: keep-all; overflow-wrap: break-word;">—</div>
                        <!-- Translation full -->
                        <div id="quranTrans" class="italic text-amber-100 mt-1.5"
                             style="font-size: clamp(12px, 1vw, 16px); line-height: 1.4;">—</div>
                    </div>
                    <div class="text-right whitespace-nowrap pl-4 shrink-0">
                        <div class="text-[10px] uppercase tracking-[3px] text-slate-300 font-semibold">Ayat</div>
                        <div class="font-bold text-sm mt-0.5" id="quranRef" style="color: var(--accent);">—</div>
                    </div>
                </div>
            </div>
            <span class="hidden" id="quranArab2"></span>
            <span class="hidden" id="quranTrans2"></span>
            <span class="hidden" id="quranRef2"></span>

        <?php elseif ($quranDisplay === 'typewriter'): ?>
            <!-- MODE: TYPEWRITER - efek mesin tik per huruf -->
            <div id="quranCard" class="quran-typewriter border-t-2 px-8 py-4 relative overflow-hidden"
                 style="background: linear-gradient(90deg, var(--primary-dark), var(--primary), var(--primary-dark));
                        border-color: color-mix(in srgb, var(--accent) 60%, transparent);
                        min-height: 100px;">
                <div class="grid items-center gap-5" style="grid-template-columns: 1fr auto;">
                    <div class="min-w-0 text-center">
                        <div class="font-arabic text-white" dir="rtl"
                             style="font-size: clamp(20px, 1.9vw, 28px); line-height: 1.5; min-height: 1.5em; word-break: keep-all;">
                            <span id="quranArab">—</span><span class="quran-cursor" data-cursor-arab>▌</span>
                        </div>
                        <div class="italic text-amber-100 mt-1"
                             style="font-size: clamp(12px, 1vw, 15px); line-height: 1.4; min-height: 1.4em;">
                            <span id="quranTrans">—</span><span class="quran-cursor" data-cursor-trans style="display:none;">▌</span>
                        </div>
                    </div>
                    <div class="text-right whitespace-nowrap pl-4 shrink-0 border-l border-white/15">
                        <div class="text-[10px] uppercase tracking-[3px] text-slate-300 font-semibold">Tafakkur</div>
                        <div class="font-bold text-sm mt-1" id="quranRef" style="color: var(--accent);">—</div>
                    </div>
                </div>
            </div>
            <span class="hidden" id="quranArab2"></span>
            <span class="hidden" id="quranTrans2"></span>
            <span class="hidden" id="quranRef2"></span>

        <?php else: ?>
            <!-- MODE: MARQUEE (default, 2 baris arah berlawanan) -->
            <div class="grid" style="grid-template-rows: auto auto;">
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
    <?php endif; ?>

    <?php if ($showRunning): ?>
        <!-- Running text masjid -->
        <div class="bg-slate-950 overflow-hidden marquee" style="height: 38px;">
            <div class="marquee-inner font-semibold text-white" style="animation-duration: <?= $rtSpeed ?>s; font-size: 15px;">
                <?php if ($running): ?>
                    <?= mc_e(implode('   •   ', $running)) ?>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</footer>
