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
</script>

<?php require __DIR__ . '/_footer.php';
