<?php
// File: config.php
// Database config - update DB_USER and DB_PASS as needed
declare(strict_types=1);
session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'medical_history');
define('DB_CHAR', 'utf8mb4');
define('DB_USER', 'root');
define('DB_PASS', '');

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHAR;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed.";
    exit;
}

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
