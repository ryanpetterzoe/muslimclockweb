<?php
$active = 'location';
require __DIR__ . '/../includes/auth.php';
mc_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!mc_csrf_check()) { $_SESSION['flash'] = ['type'=>'err','msg'=>'CSRF invalid']; mc_redirect('location.php'); }
    mc_setting_set('location_city_name', trim($_POST['city_name'] ?? ''));
    mc_setting_set('location_lat', trim($_POST['lat'] ?? ''));
    mc_setting_set('location_lng', trim($_POST['lng'] ?? ''));
    mc_setting_set('calc_method', trim($_POST['method'] ?? '20'));

    // hapus cache prayer biar fresh
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
<h1>Lokasi Masjid & Metode Perhitungan</h1>
<div class="card">
    <p>Cari kota lalu pilih untuk mengisi koordinat secara otomatis. Jadwal sholat dihitung via <a href="https://aladhan.com/" target="_blank">Aladhan API</a> dengan metode pilihan (default: <strong>KEMENAG (20)</strong>).</p>
    <form method="post">
        <?= mc_csrf_field() ?>
        <label>Cari Kota</label>
        <input type="text" id="searchCity" placeholder="ketik nama kota / kecamatan, mis. Depok">
        <div id="searchResult" style="margin-top:6px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;display:none;max-height:240px;overflow:auto"></div>

        <div class="row3" style="margin-top:14px">
            <div>
                <label>Nama Kota</label>
                <input type="text" name="city_name" id="cityName" value="<?= mc_e(mc_setting('location_city_name')) ?>" required>
            </div>
            <div>
                <label>Latitude</label>
                <input type="text" name="lat" id="lat" value="<?= mc_e(mc_setting('location_lat')) ?>" required>
            </div>
            <div>
                <label>Longitude</label>
                <input type="text" name="lng" id="lng" value="<?= mc_e(mc_setting('location_lng')) ?>" required>
            </div>
        </div>

        <label>Metode Perhitungan</label>
        <select name="method">
            <?php foreach ($methods as $k => $v): ?>
                <option value="<?= $k ?>" <?= mc_setting('calc_method')==$k?'selected':'' ?>><?= mc_e($v) ?></option>
            <?php endforeach; ?>
        </select>

        <p style="margin-top:18px"><button class="btn" type="submit">Simpan</button></p>
    </form>
</div>

<script>
const inp = document.getElementById('searchCity');
const box = document.getElementById('searchResult');
let to = null;
inp.addEventListener('input', () => {
    clearTimeout(to);
    const q = inp.value.trim();
    if (q.length < 3) { box.style.display='none'; return; }
    to = setTimeout(async () => {
        const r = await fetch('../api/locations.php?q=' + encodeURIComponent(q));
        const list = await r.json();
        if (!list.length) { box.style.display='none'; return; }
        box.innerHTML = list.map(x => `<div class="loc-item" data-lat="${x.lat}" data-lon="${x.lon}" data-name="${x.name.replace(/"/g,'&quot;')}"
            style="padding:8px 12px;cursor:pointer;border-bottom:1px solid #eee">${x.name}</div>`).join('');
        box.style.display='block';
    }, 350);
});
box.addEventListener('click', (e) => {
    const it = e.target.closest('.loc-item'); if (!it) return;
    document.getElementById('lat').value = it.dataset.lat;
    document.getElementById('lng').value = it.dataset.lon;
    document.getElementById('cityName').value = it.dataset.name.split(',')[0];
    box.style.display='none';
});
</script>
<?php require __DIR__ . '/_footer.php';
