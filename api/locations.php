<?php
/**
 * Cari lokasi (kota) berbasis OpenStreetMap Nominatim.
 * GET ?q=Depok
 * return [{name, lat, lon, display_name}]
 */
require_once __DIR__ . '/../includes/functions.php';

$q = trim($_GET['q'] ?? '');
if ($q === '' || strlen($q) < 2) mc_json([]);

$cacheDir = __DIR__ . '/../assets/uploads/cache';
if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);
$cacheFile = $cacheDir . '/loc_' . md5(strtolower($q)) . '.json';
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 7 * 86400) {
    echo file_get_contents($cacheFile);
    exit;
}

$url = 'https://nominatim.openstreetmap.org/search?format=json&limit=8&accept-language=id&q=' . urlencode($q . ' Indonesia');
$ctx = stream_context_create(['http' => ['timeout' => 8, 'header' => "User-Agent: MuslimClockWeb/1.0\r\n"]]);
$json = @file_get_contents($url, false, $ctx);
$rows = $json ? json_decode($json, true) : [];
$out = [];
foreach ((array)$rows as $r) {
    $out[] = [
        'name'    => $r['display_name'] ?? '',
        'lat'     => $r['lat'] ?? '',
        'lon'     => $r['lon'] ?? '',
    ];
}
file_put_contents($cacheFile, json_encode($out));
header('Content-Type: application/json; charset=utf-8');
echo json_encode($out, JSON_UNESCAPED_UNICODE);
