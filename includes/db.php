<?php
/**
 * Database connection (PDO).
 */

function mc_config()
{
    static $cfg = null;
    if ($cfg === null) {
        $file = __DIR__ . '/../config/config.php';
        if (!file_exists($file)) {
            // Not installed yet
            return null;
        }
        $cfg = require $file;
    }
    return $cfg;
}

function mc_db()
{
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $cfg = mc_config();
    if (!$cfg) return null;
    $db = $cfg['db'];

    $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset={$db['charset']}";
    try {
        $pdo = new PDO($dsn, $db['user'], $db['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        die('DB connection failed: ' . htmlspecialchars($e->getMessage()));
    }
    return $pdo;
}

function mc_table($name)
{
    $cfg = mc_config();
    return ($cfg['db']['prefix'] ?? 'mc_') . $name;
}

function mc_setting($key, $default = null)
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            $rows = mc_db()->query("SELECT `key`,`value` FROM " . mc_table('settings'))->fetchAll();
            foreach ($rows as $r) $cache[$r['key']] = $r['value'];
        } catch (Throwable $e) { /* not installed */ }
    }
    return array_key_exists($key, $cache) ? $cache[$key] : $default;
}

function mc_setting_set($key, $value)
{
    $sql = "INSERT INTO " . mc_table('settings') . " (`key`,`value`) VALUES (:k,:v)
            ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)";
    $st = mc_db()->prepare($sql);
    $st->execute([':k' => $key, ':v' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value]);
}
