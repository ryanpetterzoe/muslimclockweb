<?php
/**
 * Auth helpers (admin login).
 */
require_once __DIR__ . '/functions.php';

function mc_user(): ?array
{
    mc_start_session();
    if (empty($_SESSION['user_id'])) return null;
    static $user = null;
    if ($user !== null) return $user;
    $st = mc_db()->prepare("SELECT id,username,name,role FROM " . mc_table('users') . " WHERE id=:id LIMIT 1");
    $st->execute([':id' => $_SESSION['user_id']]);
    $user = $st->fetch() ?: null;
    return $user;
}

function mc_require_login()
{
    if (!mc_user()) {
        mc_redirect('login.php');
    }
}

function mc_login(string $username, string $password): bool
{
    mc_start_session();
    $st = mc_db()->prepare("SELECT * FROM " . mc_table('users') . " WHERE username=:u LIMIT 1");
    $st->execute([':u' => $username]);
    $u = $st->fetch();
    if (!$u) return false;
    if (!password_verify($password, $u['password'])) return false;
    $_SESSION['user_id'] = (int)$u['id'];
    session_regenerate_id(true);
    return true;
}

function mc_logout()
{
    mc_start_session();
    $_SESSION = [];
    session_destroy();
}
