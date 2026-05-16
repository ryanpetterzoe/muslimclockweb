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
