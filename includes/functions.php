<?php
/**
 * Common helpers (CSRF, sanitize, redirect).
 */
require_once __DIR__ . '/db.php';

function mc_start_session()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('MCSESSID');
        session_start();
    }
}

function mc_is_installed(): bool
{
    $cfg = mc_config();
    return $cfg && !empty($cfg['app']['installed']);
}

function mc_csrf_token(): string
{
    mc_start_session();
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function mc_csrf_check(): bool
{
    mc_start_session();
    $t = $_POST['_csrf'] ?? $_GET['_csrf'] ?? '';
    return !empty($t) && hash_equals($_SESSION['_csrf'] ?? '', $t);
}

function mc_csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(mc_csrf_token()) . '">';
}

function mc_e($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function mc_redirect(string $path)
{
    header('Location: ' . $path);
    exit;
}

function mc_base_url(): string
{
    $cfg = mc_config();
    if ($cfg && !empty($cfg['app']['base_url'])) return rtrim($cfg['app']['base_url'], '/');
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host;
}

function mc_json($data, int $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Daftar font Google yang tersedia untuk dipilih admin.
 * key => [label, weight string for google import, css family stack]
 */
function mc_fonts_display(): array
{
    return [
        'Inter'        => ['Inter (Modern)',     '400;500;600;700;800;900', "'Inter', system-ui, sans-serif"],
        'Poppins'      => ['Poppins',            '400;500;600;700;800;900', "'Poppins', system-ui, sans-serif"],
        'Plus Jakarta Sans' => ['Plus Jakarta Sans', '400;500;600;700;800', "'Plus Jakarta Sans', system-ui, sans-serif"],
        'Manrope'      => ['Manrope',            '400;500;600;700;800',     "'Manrope', system-ui, sans-serif"],
        'Outfit'       => ['Outfit (Geometric)', '400;500;600;700;800;900', "'Outfit', system-ui, sans-serif"],
        'Sora'         => ['Sora',               '400;500;600;700;800',     "'Sora', system-ui, sans-serif"],
        'Lexend'       => ['Lexend',             '400;500;600;700;800',     "'Lexend', system-ui, sans-serif"],
        'Montserrat'   => ['Montserrat',         '400;500;600;700;800;900', "'Montserrat', system-ui, sans-serif"],
        'Rubik'        => ['Rubik',              '400;500;600;700;800',     "'Rubik', system-ui, sans-serif"],
    ];
}

function mc_fonts_digital(): array
{
    return [
        'Orbitron'    => ['Orbitron (Futuristik)',     '500;600;700;800;900', "'Orbitron', monospace"],
        'JetBrains Mono' => ['JetBrains Mono',         '400;500;600;700;800', "'JetBrains Mono', monospace"],
        'Space Mono'  => ['Space Mono',                '400;700',             "'Space Mono', monospace"],
        'Major Mono Display' => ['Major Mono Display', '400',                 "'Major Mono Display', monospace"],
        'Share Tech Mono' => ['Share Tech Mono',       '400',                 "'Share Tech Mono', monospace"],
        'IBM Plex Mono' => ['IBM Plex Mono',           '400;500;600;700',     "'IBM Plex Mono', monospace"],
        'DSEG7 Classic' => ['Tujuh Segmen (LCD)',      '400;700',             "'DSEG7 Classic', 'Orbitron', monospace"], // local fallback
        'Anton'       => ['Anton (Tebal)',             '400',                 "'Anton', sans-serif"],
        'Bebas Neue'  => ['Bebas Neue',                '400',                 "'Bebas Neue', sans-serif"],
    ];
}

function mc_layouts(): array
{
    return [
        'cinema'  => ['Cinema',   'Slideshow besar di kiri, jam di tengah, jadwal kanan. Sinematik dengan akcent emas.'],
        'minimal' => ['Minimal',  'Jam digital raksasa, jadwal di bawah dalam baris kartu rapih.'],
        'mosque'  => ['Mosque',   'Tema masjid: ornamen Islamic, jadwal grid 6 kartu, jam digital sentral.'],
        'neon'    => ['Neon',     'Cyberpunk: jam digital glow neon, jadwal kartu glassmorphism, cocok layar gelap.'],
        'classic' => ['Klasik',   'Tradisional: ornamen Arabic, latar pattern, jadwal sholat dalam tabel elegan.'],
        'compact' => ['Compact',  'Padat info: header, jam besar, jadwal full-width 2 baris, tanpa slideshow.'],
    ];
}

/** Bangun URL Google Fonts untuk semua font yang dibutuhkan. */
function mc_google_fonts_url(array $names): string
{
    $all = array_merge(mc_fonts_display(), mc_fonts_digital(), [
        'Amiri' => ['Amiri', '400;700', "'Amiri', serif"],
    ]);
    $parts = [];
    foreach (array_unique($names) as $n) {
        if (!isset($all[$n])) continue;
        $weights = $all[$n][1];
        $family  = str_replace(' ', '+', $n);
        $parts[] = "family=$family:wght@$weights";
    }
    if (!$parts) return '';
    return 'https://fonts.googleapis.com/css2?' . implode('&', $parts) . '&display=swap';
}

function mc_font_stack(string $name, string $kind = 'display'): string
{
    $list = $kind === 'digital' ? mc_fonts_digital() : mc_fonts_display();
    if (isset($list[$name])) return $list[$name][2];
    // fallback
    return $kind === 'digital' ? "'Orbitron', monospace" : "'Inter', system-ui, sans-serif";
}

/** Return MIME type for a video file based on extension. */
function mc_video_mime(string $path): string
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return [
        'mp4'  => 'video/mp4',
        'm4v'  => 'video/mp4',
        'mov'  => 'video/mp4',
        'webm' => 'video/webm',
        'ogg'  => 'video/ogg',
        'ogv'  => 'video/ogg',
    ][$ext] ?? 'video/mp4';
}
