<?php
$active = 'location';
$pageTitle = 'Lokasi & Jadwal';
$pageSubtitle = 'Set koordinat masjid untuk menghitung jadwal sholat presisi';

require __DIR__ . '/../includes/auth.php';
mc_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mc_csrf_check()) { $_SESSION['flash'] = ['type'=>'err','msg'=>'CSRF invalid']; mc_redirect('location.php'); }
    mc_setting_set('location_city_name', trim($_POST['city_name'] ?? ''));
    mc_setting_set('location_lat', trim($_POST['lat'] ?? ''));
    mc_setting_set('location_lng', trim($_POST['lng'] ?? ''));
    mc_setting_set('calc_method', trim($_POST['method'] ?? '20'));
    foreach (glob(__DIR__ . '/../assets/uploads/cache/prayer_*.json') ?: [] as $f) @unlink($f);
    $_SESSION['flash'] = ['type'=>'ok','msg'=>'Lokasi & metode disimpan. Cache jadwal dibersihkan.'];
    mc_redirect('location.php');
}

require __DIR__ . '/_layout.php';

$methods = [
    '20' => 'Kemenag Indonesia (KEMENAG) — direkomendasikan',
    '3'  => 'Muslim World League',
    '2'  => 'ISNA',
    '4'  => 'Umm Al-Qura (Mekkah)',
    '5'  => 'Egyptian General Authority',
    '8'  => 'Gulf Region',
    '11' => 'Majlis Ugama Islam Singapura',
    '13' => 'Diyanet (Turki)',
];
?>

<form method="post">
    <?= mc_csrf_field() ?>

    <div class="mc-card">
        <h2>
            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Cari Lokasi
        </h2>
        <p class="text-sm text-slate-500 mb-3">Ketik nama kota atau kecamatan, klik hasil untuk auto-fill koordinat. Sumber data: <a href="https://aladhan.com/" target="_blank" class="text-blue-700 hover:underline">Aladhan API</a> dengan metode <strong>KEMENAG (20)</strong>.</p>

        <input type="text" id="searchCity" class="mc-input" placeholder="Contoh: Depok, Bandung, Surabaya...">
        <div id="searchResult" class="hidden mt-2 bg-white border border-slate-200 rounded-lg max-h-60 overflow-auto shadow-soft"></div>

        <div class="row-3 mt-4">
            <div>
                <label class="mc-label">Nama Kota</label>
                <input type="text" name="city_name" id="cityName" class="mc-input" value="<?= mc_e(mc_setting('location_city_name')) ?>" required>
            </div>
            <div>
                <label class="mc-label">Latitude</label>
                <input type="text" name="lat" id="lat" class="mc-input" value="<?= mc_e(mc_setting('location_lat')) ?>" required>
            </div>
            <div>
                <label class="mc-label">Longitude</label>
                <input type="text" name="lng" id="lng" class="mc-input" value="<?= mc_e(mc_setting('location_lng')) ?>" required>
            </div>
        </div>

        <label class="mc-label">Metode Perhitungan</label>
        <select name="method" class="mc-select">
            <?php foreach ($methods as $k => $v): ?>
                <option value="<?= $k ?>" <?= mc_setting('calc_method')==$k?'selected':'' ?>><?= mc_e($v) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button class="mc-btn" type="submit">Simpan</button>
</form>

<script>
const inp = document.getElementById('searchCity');
const box = document.getElementById('searchResult');
let to = null;
inp.addEventListener('input', () => {
    clearTimeout(to);
    const q = inp.value.trim();
    if (q.length < 3) { box.classList.add('hidden'); return; }
    to = setTimeout(async () => {
        const r = await fetch('../api/locations.php?q=' + encodeURIComponent(q));
        const list = await r.json();
        if (!list.length) { box.classList.add('hidden'); return; }
        box.innerHTML = list.map(x => `<div class="loc-item p-3 hover:bg-slate-50 border-b border-slate-100 cursor-pointer text-sm" data-lat="${x.lat}" data-lon="${x.lon}" data-name="${x.name.replace(/"/g,'&quot;')}">${x.name}</div>`).join('');
        box.classList.remove('hidden');
    }, 350);
});
box.addEventListener('click', e => {
    const it = e.target.closest('.loc-item'); if (!it) return;
    document.getElementById('lat').value = it.dataset.lat;
    document.getElementById('lng').value = it.dataset.lon;
    document.getElementById('cityName').value = it.dataset.name.split(',')[0];
    box.classList.add('hidden');
});
</script>

<?php require __DIR__ . '/_footer.php';
