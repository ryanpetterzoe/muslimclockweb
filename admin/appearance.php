<?php
$active = 'appearance';
$pageTitle = 'Tampilan & Tema';
$pageSubtitle = 'Pilih layout, font, dan kombinasi warna untuk layar masjid';

require __DIR__ . '/../includes/auth.php';
mc_require_login();

$presets = [
    'classic-blue'  => ['Klasik Biru',   '#0a4ea3', '#f5b301'],
    'royal-green'   => ['Hijau Royal',   '#14532d', '#facc15'],
    'midnight'      => ['Tengah Malam',  '#0f172a', '#22d3ee'],
    'sunset-purple' => ['Senja Ungu',    '#5b21b6', '#fbbf24'],
    'desert-gold'   => ['Padang Pasir',  '#92400e', '#fde68a'],
    'turquoise'     => ['Pirus',         '#0d9488', '#facc15'],
    'maroon'        => ['Maroon',        '#7f1d1d', '#fbbf24'],
    'graphite'      => ['Grafit',        '#1f2937', '#f59e0b'],
    'emerald'       => ['Zamrud',        '#065f46', '#fcd34d'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mc_csrf_check()) { $_SESSION['flash']=['type'=>'err','msg'=>'CSRF invalid']; mc_redirect('appearance.php'); }

    $layout = $_POST['layout'] ?? 'cinema';
    if (!array_key_exists($layout, mc_layouts())) $layout = 'cinema';
    mc_setting_set('layout', $layout);

    // Font
    $fontDisp = $_POST['font_display'] ?? 'Inter';
    $fontDigi = $_POST['font_digital'] ?? 'Orbitron';
    if (!array_key_exists($fontDisp, mc_fonts_display())) $fontDisp = 'Inter';
    if (!array_key_exists($fontDigi, mc_fonts_digital())) $fontDigi = 'Orbitron';
    mc_setting_set('font_display', $fontDisp);
    mc_setting_set('font_digital', $fontDigi);

    // Theme
    $preset = $_POST['preset'] ?? 'classic-blue';
    $primary = $_POST['primary'] ?? '#0a4ea3';
    $accent  = $_POST['accent']  ?? '#f5b301';
    if (isset($presets[$preset]) && empty($_POST['custom'])) {
        [$_, $primary, $accent] = $presets[$preset];
    }
    mc_setting_set('theme_preset', $preset);
    mc_setting_set('theme_primary', $primary);
    mc_setting_set('theme_accent', $accent);

    // Komponen tampilan
    $quranDisplay = ($_POST['quran_display'] ?? 'marquee') === 'card' ? 'card' : 'marquee';
    mc_setting_set('quran_display', $quranDisplay);
    mc_setting_set('show_analog', isset($_POST['show_analog']) ? '1' : '0');

    $_SESSION['flash']=['type'=>'ok','msg'=>'Tampilan disimpan. Buka layar untuk lihat hasilnya.'];
    mc_redirect('appearance.php');
}

require __DIR__ . '/_layout.php';

$layoutKey = mc_setting('layout', 'cinema');
$layouts = mc_layouts();
$fontDisp = mc_setting('font_display', 'Inter');
$fontDigi = mc_setting('font_digital', 'Orbitron');
$presetCur = mc_setting('theme_preset', 'classic-blue');
$primaryCur = mc_setting('theme_primary', '#0a4ea3');
$accentCur  = mc_setting('theme_accent', '#f5b301');
$quranDisplay = mc_setting('quran_display', 'marquee');
$showAnalog = (string)mc_setting('show_analog', '1') !== '0';
?>

<form method="post">
    <?= mc_csrf_field() ?>

    <!-- LAYOUT picker -->
    <div class="mc-card">
        <h2>
            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v2H4V6zM4 10h16v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8z"/></svg>
            Layout Tampilan
        </h2>
        <p class="text-sm text-slate-500 mb-3">Pilih salah satu dari 3 desain layout. Bisa preview tanpa menyimpan.</p>

        <div class="layout-picker">
            <?php
            $previews = [
                'cinema'  => '<svg viewBox="0 0 320 180" xmlns="http://www.w3.org/2000/svg"><rect width="320" height="180" fill="#0f172a"/><rect width="320" height="22" fill="#fff"/><circle cx="14" cy="11" r="6" fill="#f5b301"/><text x="26" y="14" font-size="8" fill="#0f172a" font-weight="700">Masjid</text><rect x="0" y="22" width="180" height="120" fill="#0a4ea3" opacity="0.6"/><circle cx="220" cy="80" r="22" fill="#fff"/><circle cx="220" cy="80" r="20" fill="#0a4ea3"/><line x1="220" y1="80" x2="220" y2="64" stroke="#0f172a" stroke-width="2"/><line x1="220" y1="80" x2="232" y2="80" stroke="#dc2626"/><rect x="200" y="105" width="40" height="14" rx="3" fill="rgba(255,255,255,0.15)"/><rect x="260" y="22" width="60" height="20" fill="#0a4ea3"/><rect x="260" y="44" width="60" height="20" fill="#0a4ea3"/><rect x="260" y="66" width="60" height="20" fill="#0b3a7a"/><rect x="260" y="88" width="60" height="20" fill="#0a4ea3"/><rect x="260" y="110" width="60" height="20" fill="#0a4ea3"/><rect x="260" y="132" width="60" height="20" fill="#0a4ea3"/><rect x="0" y="142" width="320" height="20" fill="#1e293b"/><rect x="0" y="162" width="320" height="18" fill="#f5b301"/></svg>',
                'minimal' => '<svg viewBox="0 0 320 180" xmlns="http://www.w3.org/2000/svg"><rect width="320" height="180" fill="#05060d"/><rect width="320" height="180" fill="url(#g1)" opacity="0.5"/><defs><linearGradient id="g1" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1e293b"/><stop offset="1" stop-color="#05060d"/></linearGradient></defs><rect x="10" y="6" width="100" height="14" rx="4" fill="rgba(255,255,255,0.08)"/><rect x="220" y="6" width="90" height="14" rx="4" fill="rgba(255,255,255,0.08)"/><text x="160" y="80" text-anchor="middle" font-size="48" font-weight="900" fill="#fff">17:43</text><rect x="120" y="92" width="80" height="10" rx="5" fill="rgba(0,0,0,0.5)"/><g transform="translate(0,118)"><rect x="10" y="0" width="46" height="36" rx="6" fill="rgba(255,255,255,0.06)"/><rect x="62" y="0" width="46" height="36" rx="6" fill="rgba(255,255,255,0.06)"/><rect x="114" y="0" width="46" height="36" rx="6" fill="rgba(245,179,1,0.18)" stroke="#f5b301"/><rect x="166" y="0" width="46" height="36" rx="6" fill="rgba(255,255,255,0.06)"/><rect x="218" y="0" width="46" height="36" rx="6" fill="rgba(255,255,255,0.06)"/><rect x="270" y="0" width="46" height="36" rx="6" fill="rgba(255,255,255,0.06)"/></g><rect x="0" y="158" width="320" height="14" fill="#0a4ea3"/><rect x="0" y="172" width="320" height="8" fill="#f5b301"/></svg>',
                'mosque'  => '<svg viewBox="0 0 320 180" xmlns="http://www.w3.org/2000/svg"><rect width="320" height="180" fill="#050514"/><circle cx="20" cy="40" r="50" fill="#f5b301" opacity="0.18"/><circle cx="300" cy="160" r="60" fill="#0a4ea3" opacity="0.2"/><rect width="320" height="22" fill="rgba(255,255,255,0.05)"/><path d="M155 12 Q160 6 165 12 Q160 17 155 12" fill="#f5b301"/><text x="180" y="14" font-size="8" font-weight="700" fill="#fff">Masjid</text><text x="160" y="80" text-anchor="middle" font-size="40" font-weight="900" fill="#fff">17:43</text><circle cx="270" cy="75" r="20" fill="#fff"/><circle cx="270" cy="75" r="18" fill="#0a4ea3"/><rect x="120" y="92" width="80" height="10" rx="5" fill="rgba(0,0,0,0.5)" stroke="#f5b301" stroke-opacity="0.4"/><g transform="translate(0,116)"><rect x="10" y="0" width="46" height="36" rx="8" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.1)"/><rect x="62" y="0" width="46" height="36" rx="8" fill="rgba(245,179,1,0.18)" stroke="#f5b301"/><rect x="114" y="0" width="46" height="36" rx="8" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.1)"/><rect x="166" y="0" width="46" height="36" rx="8" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.1)"/><rect x="218" y="0" width="46" height="36" rx="8" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.1)"/><rect x="270" y="0" width="46" height="36" rx="8" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.1)"/></g><rect x="0" y="158" width="320" height="14" fill="#0a4ea3"/><rect x="0" y="172" width="320" height="8" fill="#f5b301"/></svg>',
                'neon'    => '<svg viewBox="0 0 320 180" xmlns="http://www.w3.org/2000/svg"><defs><radialGradient id="ng" cx="50%" cy="0%" r="80%"><stop offset="0%" stop-color="#1a1f3a"/><stop offset="100%" stop-color="#050816"/></radialGradient><filter id="glow"><feGaussianBlur stdDeviation="2"/><feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><rect width="320" height="180" fill="url(#ng)"/><circle cx="280" cy="0" r="80" fill="#f5b301" opacity="0.15"/><circle cx="40" cy="180" r="100" fill="#0a4ea3" opacity="0.2"/><rect width="320" height="20" fill="rgba(255,255,255,0.04)"/><rect x="10" y="6" width="100" height="10" rx="3" fill="rgba(245,179,1,0.6)"/><rect x="40" y="30" width="180" height="80" rx="10" fill="rgba(255,255,255,0.03)" stroke="#f5b301" stroke-opacity="0.4"/><text x="130" y="80" text-anchor="middle" font-size="36" font-weight="900" fill="#fff" filter="url(#glow)">17:43</text><circle cx="270" cy="70" r="22" fill="rgba(255,255,255,0.05)" stroke="#f5b301" stroke-opacity="0.5"/><circle cx="270" cy="70" r="18" fill="#0a4ea3"/><g transform="translate(0,118)"><rect x="6" y="0" width="48" height="32" rx="6" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.1)"/><rect x="58" y="0" width="48" height="32" rx="6" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.1)"/><rect x="110" y="0" width="48" height="32" rx="6" fill="rgba(245,179,1,0.2)" stroke="#f5b301"/><rect x="162" y="0" width="48" height="32" rx="6" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.1)"/><rect x="214" y="0" width="48" height="32" rx="6" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.1)"/><rect x="266" y="0" width="48" height="32" rx="6" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.1)"/></g><rect x="0" y="156" width="320" height="14" fill="#0a4ea3"/><rect x="0" y="170" width="320" height="10" fill="#f5b301"/></svg>',
                'classic' => '<svg viewBox="0 0 320 180" xmlns="http://www.w3.org/2000/svg"><rect width="320" height="180" fill="#0a0e1f"/><pattern id="tilepat" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M10 0 L20 10 L10 20 L0 10 Z" fill="none" stroke="#f5b301" stroke-opacity="0.06"/></pattern><rect width="320" height="180" fill="url(#tilepat)"/><rect width="320" height="40" fill="rgba(10,78,163,0.4)"/><path d="M30 12 Q35 7 40 12 Q35 18 30 12" fill="#f5b301"/><text x="55" y="22" font-size="11" font-weight="800" fill="#fff">Masjid</text><text x="55" y="33" font-size="6" fill="#cbd5e1">Alamat</text><line x1="20" y1="40" x2="300" y2="40" stroke="#f5b301" stroke-opacity="0.4"/><circle cx="80" cy="90" r="28" fill="#fff"/><circle cx="80" cy="90" r="25" fill="#0a4ea3"/><line x1="80" y1="90" x2="80" y2="70" stroke="#0f172a" stroke-width="2"/><text x="80" y="135" text-anchor="middle" font-size="20" font-weight="900" fill="#fff">17:43</text><rect x="135" y="55" width="170" height="100" rx="8" fill="rgba(255,255,255,0.05)" stroke="rgba(255,255,255,0.1)"/><rect x="142" y="62" width="156" height="14" fill="rgba(10,78,163,0.5)"/><line x1="142" y1="92" x2="298" y2="92" stroke="rgba(255,255,255,0.08)"/><line x1="142" y1="106" x2="298" y2="106" stroke="rgba(255,255,255,0.08)"/><rect x="142" y="106" width="156" height="14" fill="rgba(245,179,1,0.12)"/><line x1="142" y1="120" x2="298" y2="120" stroke="rgba(255,255,255,0.08)"/><line x1="142" y1="134" x2="298" y2="134" stroke="rgba(255,255,255,0.08)"/><rect x="0" y="158" width="320" height="14" fill="#0a4ea3"/><rect x="0" y="172" width="320" height="8" fill="#f5b301"/></svg>',
                'compact' => '<svg viewBox="0 0 320 180" xmlns="http://www.w3.org/2000/svg"><rect width="320" height="180" fill="#0d1424"/><rect width="320" height="180" fill="url(#cg2)" opacity="0.5"/><defs><radialGradient id="cg2" cx="50%" cy="0%" r="60%"><stop offset="0" stop-color="rgba(10,78,163,0.5)"/><stop offset="1" stop-color="transparent"/></radialGradient></defs><rect width="320" height="22" fill="rgba(255,255,255,0.04)"/><rect x="10" y="7" width="80" height="8" rx="2" fill="rgba(255,255,255,0.15)"/><rect x="240" y="7" width="70" height="8" rx="2" fill="rgba(245,179,1,0.5)"/><circle cx="40" cy="55" r="20" fill="#fff"/><circle cx="40" cy="55" r="18" fill="#0a4ea3"/><text x="170" y="70" text-anchor="middle" font-size="44" font-weight="900" fill="#fff">17:43</text><rect x="265" y="35" width="50" height="40" rx="6" fill="rgba(255,255,255,0.05)"/><text x="290" y="55" text-anchor="middle" font-size="8" fill="#f5b301">ISYA</text><text x="290" y="68" text-anchor="middle" font-size="9" font-weight="800" fill="#fff">01:00</text><g transform="translate(0,90)"><rect x="6" y="0" width="100" height="28" rx="6" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.1)"/><rect x="110" y="0" width="100" height="28" rx="6" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.1)"/><rect x="214" y="0" width="100" height="28" rx="6" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.1)"/></g><g transform="translate(0,124)"><rect x="6" y="0" width="100" height="28" rx="6" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.1)"/><rect x="110" y="0" width="100" height="28" rx="6" fill="rgba(245,179,1,0.18)" stroke="#f5b301"/><rect x="214" y="0" width="100" height="28" rx="6" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.1)"/></g><rect x="0" y="158" width="320" height="14" fill="#0a4ea3"/><rect x="0" y="172" width="320" height="8" fill="#f5b301"/></svg>',
            ];
            foreach ($layouts as $key => [$name, $desc]): ?>
                <label class="layout-card <?= $layoutKey === $key ? 'selected' : '' ?>" data-key="<?= mc_e($key) ?>">
                    <input type="radio" name="layout" value="<?= mc_e($key) ?>" <?= $layoutKey === $key ? 'checked' : '' ?> hidden>
                    <div class="preview"><?= $previews[$key] ?? '' ?></div>
                    <div class="name"><?= mc_e($name) ?></div>
                    <div class="desc"><?= mc_e($desc) ?></div>
                    <a href="../index.php?preview=<?= mc_e($key) ?>" target="_blank" class="text-xs text-blue-700 hover:underline mt-2 inline-block">Preview ↗</a>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- FONT picker -->
    <div class="mc-card">
        <h2>
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            Tipografi
        </h2>
        <div class="row-2">
            <div>
                <label class="mc-label">Font Tampilan (label, judul)</label>
                <select name="font_display" class="mc-select">
                    <?php foreach (mc_fonts_display() as $key => $info): ?>
                        <option value="<?= mc_e($key) ?>" <?= $fontDisp === $key ? 'selected' : '' ?>><?= mc_e($info[0]) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="mc-label">Font Digital (jam)</label>
                <select name="font_digital" class="mc-select">
                    <?php foreach (mc_fonts_digital() as $key => $info): ?>
                        <option value="<?= mc_e($key) ?>" <?= $fontDigi === $key ? 'selected' : '' ?>><?= mc_e($info[0]) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- THEME -->
    <div class="mc-card">
        <h2>
            <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4z"/></svg>
            Tema Warna
        </h2>
        <div class="preset-list">
            <?php foreach ($presets as $key => [$name, $p, $a]): ?>
                <label class="preset-card <?= $presetCur === $key ? 'selected' : '' ?>">
                    <input type="radio" name="preset" value="<?= mc_e($key) ?>" <?= $presetCur === $key ? 'checked' : '' ?> hidden>
                    <div class="swatch"><span style="background:<?= $p ?>"></span><span style="background:<?= $a ?>"></span></div>
                    <?= mc_e($name) ?>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="mt-5 pt-5 border-t border-slate-200">
            <label class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                <input type="checkbox" name="custom" value="1" class="rounded">
                Gunakan warna kustom (override preset)
            </label>
            <div class="row-2 mt-3">
                <div>
                    <label class="mc-label">Primary</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="primary" value="<?= mc_e($primaryCur) ?>" class="w-12 h-10 rounded border border-slate-300">
                        <code class="text-xs text-slate-500"><?= mc_e($primaryCur) ?></code>
                    </div>
                </div>
                <div>
                    <label class="mc-label">Accent</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="accent" value="<?= mc_e($accentCur) ?>" class="w-12 h-10 rounded border border-slate-300">
                        <code class="text-xs text-slate-500"><?= mc_e($accentCur) ?></code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KOMPONEN TAMPILAN -->
    <div class="mc-card">
        <h2>
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Komponen Tampilan
        </h2>
        <p class="text-sm text-slate-500 mb-4">Atur cara komponen ditampilkan di layar. Berlaku untuk semua layout.</p>

        <!-- Quran display mode -->
        <label class="mc-label">Tampilan Cuplikan Al-Qur'an</label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-1">
            <label class="border-2 rounded-xl p-4 cursor-pointer transition <?= $quranDisplay === 'marquee' ? 'border-blue-600 bg-blue-50/40' : 'border-slate-200 hover:border-slate-300' ?>">
                <input type="radio" name="quran_display" value="marquee" class="hidden" <?= $quranDisplay === 'marquee' ? 'checked' : '' ?>>
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    <div>
                        <div class="font-bold text-sm text-slate-900">Running Text (Marquee)</div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Ayat berjalan terus seperti teks berita. Cocok untuk ayat panjang.</p>
                        <div class="mt-3 bg-slate-900 rounded text-white text-[9px] py-1.5 px-2 overflow-hidden whitespace-nowrap">
                            <span style="display:inline-block; animation: ticker 6s linear infinite;">إِنَّ مَعَ الْعُسْرِ يُسْرًا · QS. Asy-Syarh (94:6) ◈</span>
                        </div>
                    </div>
                </div>
            </label>
            <label class="border-2 rounded-xl p-4 cursor-pointer transition <?= $quranDisplay === 'card' ? 'border-blue-600 bg-blue-50/40' : 'border-slate-200 hover:border-slate-300' ?>">
                <input type="radio" name="quran_display" value="card" class="hidden" <?= $quranDisplay === 'card' ? 'checked' : '' ?>>
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3M3 6h18M3 14h18M3 18h18"/></svg>
                    <div>
                        <div class="font-bold text-sm text-slate-900">Kartu Statis</div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Ayat dan terjemahan tampil sebagai kartu, ganti otomatis tiap 30 detik. Tidak berjalan.</p>
                        <div class="mt-3 bg-blue-900 rounded text-white text-[9px] py-2 px-2.5">
                            <div dir="rtl" style="font-family:'Amiri',serif">إِنَّ مَعَ الْعُسْرِ يُسْرًا</div>
                            <div class="italic text-amber-100 mt-1">Sesungguhnya bersama kesulitan ada kemudahan.</div>
                            <div class="text-amber-300 mt-1 font-bold">QS. Asy-Syarh (94:6)</div>
                        </div>
                    </div>
                </div>
            </label>
        </div>

        <!-- Show analog clock toggle -->
        <div class="mt-5 pt-5 border-t border-slate-200">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="show_analog" value="1" <?= $showAnalog ? 'checked' : '' ?>
                       class="w-5 h-5 mt-0.5 rounded border-slate-300">
                <div>
                    <div class="font-bold text-sm text-slate-900">Tampilkan Jam Analog</div>
                    <p class="text-xs text-slate-500 mt-1">Centang untuk menampilkan jam analog di samping jam digital. Hilangkan centang jika hanya ingin jam digital.</p>
                </div>
            </label>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button class="mc-btn" type="submit">Simpan Tampilan</button>
        <a href="../index.php" target="_blank" class="mc-btn ghost">Buka Layar ↗</a>
    </div>
</form>

<script>
// Highlight selected layout / preset
document.querySelectorAll('.layout-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.layout-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        card.querySelector('input[type=radio]').checked = true;
    });
});
document.querySelectorAll('.preset-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.preset-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        card.querySelector('input[type=radio]').checked = true;
    });
});

// Quran display mode cards (highlight on click)
document.querySelectorAll('input[name="quran_display"]').forEach(input => {
    input.addEventListener('change', () => {
        document.querySelectorAll('input[name="quran_display"]').forEach(i => {
            const lbl = i.closest('label');
            if (i.checked) {
                lbl.classList.remove('border-slate-200');
                lbl.classList.add('border-blue-600', 'bg-blue-50/40');
            } else {
                lbl.classList.remove('border-blue-600', 'bg-blue-50/40');
                lbl.classList.add('border-slate-200');
            }
        });
    });
});
</script>

<?php require __DIR__ . '/_footer.php';
