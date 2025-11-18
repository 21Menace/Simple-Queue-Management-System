<?php
// config/config.php
// Update these constants with your local MySQL credentials

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'qms');
define('DB_USER', 'root');
// For XAMPP default no password; for WAMP/MAMP adjust accordingly
define('DB_PASS', '');

define('APP_NAME', 'Campus Queue Management');

define('BASE_URL', '/QMS/public/'); // Adjust if hosted in a subdirectory

function get_pdo(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
