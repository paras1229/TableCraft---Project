<?php
declare(strict_types=1);

$DB_HOST = 'localhost';
$DB_NAME = 'tablecraft_db';
$DB_USER = 'root';
$DB_PASS = '';
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (Throwable $e) {
    $pdo = null;
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['db_error'] = 'Database connection failed. Import database/tablecraft.sql and verify WAMP MySQL settings.';
}
