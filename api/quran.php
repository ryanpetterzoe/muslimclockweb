<?php
/**
 * Quran verse rotator.
 * Mode 'manual' -> ambil random dari mc_quran_quotes.
 * Mode 'auto'   -> ambil dari API alquran.cloud (random ayat) + cache.
 */
require_once __DIR__ . '/../includes/functions.php';

if (!mc_is_installed()) mc_json(['error' => 'not installed'], 503);

$mode = mc_setting('quran_mode', 'auto');
$out = ['arabic' => '', 'translation' => '', 'reference' => ''];

if ($mode === 'manual') {
    $row = mc_db()->query("SELECT * FROM " . mc_table('quran_quotes') . " WHERE is_active=1 ORDER BY RAND() LIMIT 1")->fetch();
    if ($row) {
        $out = [
            'arabic'      => $row['arabic'],
            'translation' => $row['translation'] ?? '',
            'reference'   => $row['reference']   ?? '',
        ];
        mc_json($out);
    }
}

// AUTO: random ayah from a curated list of inspirational ayahs
$curated = [
    [2, 152],[2, 153],[2, 186],[2, 255],[2, 286],
    [3, 8],[3, 159],[3, 173],
    [13, 28],[14, 7],[16, 97],[20, 25],[20, 114],
    [25, 74],[39, 53],[40, 60],[55, 13],[65, 3],[94, 5],[94, 6],
    [103, 1],[112, 1],[113, 1],[114, 1],
];
[$s, $a] = $curated[array_rand($curated)];

$cacheDir = __DIR__ . '/../assets/uploads/cache';
if (!is_dir($cacheDir)) @mkdir($cacheDir, 0775, true);
$cacheFile = $cacheDir . "/quran_{$s}_{$a}.json";

if (file_exists($cacheFile)) {
    echo file_get_contents($cacheFile);
    exit;
}

$ctx = stream_context_create(['http' => ['timeout' => 6, 'header' => "User-Agent: MuslimClockWeb\r\n"]]);
$arab = @file_get_contents("https://api.alquran.cloud/v1/ayah/{$s}:{$a}/ar.alafasy", false, $ctx);
$idn  = @file_get_contents("https://api.alquran.cloud/v1/ayah/{$s}:{$a}/id.indonesian", false, $ctx);
$arabJ = $arab ? json_decode($arab, true) : null;
$idnJ  = $idn  ? json_decode($idn, true)  : null;

if ($arabJ && !empty($arabJ['data']['text'])) {
    $surahName = $arabJ['data']['surah']['englishName'] ?? "Surah $s";
    $surahId   = $arabJ['data']['surah']['number'] ?? $s;
    $out = [
        'arabic'      => $arabJ['data']['text'],
        'translation' => $idnJ['data']['text'] ?? '',
        'reference'   => "QS. {$surahName} ({$surahId}:{$a})",
    ];
    file_put_contents($cacheFile, json_encode($out, JSON_UNESCAPED_UNICODE));
} else {
    $out = [
        'arabic'      => 'إِنَّ مَعَ الْعُسْرِ يُسْرًا',
        'translation' => 'Sesungguhnya bersama kesulitan ada kemudahan.',
        'reference'   => 'QS. Asy-Syarh (94:6)',
    ];
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($out, JSON_UNESCAPED_UNICODE);
