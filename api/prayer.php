<?php
/**
 * Prayer times via Aladhan API (KEMENAG method = 20).
 * GET ?d=today|tomorrow
 */
require_once __DIR__ . '/../includes/functions.php';

if (!mc_is_installed()) mc_json(['error' => 'not installed'], 503);

$lat = mc_setting('location_lat', '-6.2');
$lng = mc_setting('location_lng', '106.816666');
$method = mc_setting('calc_method', '20'); // 20 = Indonesia KEMENAG
$tz   = (mc_config())['app']['timezone'] ?? 'Asia/Jakarta';

$which = $_GET['d'] ?? 'today';
$date = new DateTime('now', new DateTimeZone($tz));
if ($which === 'tomorrow') $date->modify('+1 day');
$dParam = $date->format('d-m-Y');

$cacheDir = __DIR__ . '/../assets/uploads/cache';
if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);
$cacheFile = $cacheDir . '/prayer_' . md5("$lat,$lng,$method,$dParam") . '.json';

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 21600) {
    echo file_get_contents($cacheFile);
    exit;
}

$url = "https://api.aladhan.com/v1/timings/$dParam?latitude=" . urlencode($lat)
     . "&longitude=" . urlencode($lng) . "&method=" . urlencode($method)
     . "&school=0&timezonestring=" . urlencode($tz);

$ctx = stream_context_create(['http' => ['timeout' => 8, 'header' => "User-Agent: MuslimClockWeb\r\n"]]);
$json = @file_get_contents($url, false, $ctx);
if (!$json) {
    // Fallback offline (kira-kira) supaya tampilan tidak kosong
    $fallback = ['data' => ['timings' => [
        'Fajr'    => '04:30','Sunrise'=>'05:45','Dhuhr'=>'12:00','Asr'=>'15:15',
        'Maghrib' => '18:00','Isha'=>'19:15'
    ]]];
    $json = json_encode($fallback);
}
$data = json_decode($json, true);
$timings = $data['data']['timings'] ?? [];
$out = [
    'date'    => $dParam,
    'lat'     => $lat,
    'lng'     => $lng,
    'method'  => $method,
    'timings' => $timings,
];
file_put_contents($cacheFile, json_encode($out));
header('Content-Type: application/json; charset=utf-8');
echo json_encode($out, JSON_UNESCAPED_UNICODE);
