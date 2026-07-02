<?php
// config/database.php

$host = 'localhost';
$db = 'newballetera';
$charset = 'utf8mb4';

$is_local = str_starts_with($_SERVER['HTTP_HOST'] ?? '', 'localhost');

// Credentials live in config/secrets.php (gitignored). See secrets.example.php.
$__secrets = is_file(__DIR__ . '/secrets.php') ? require __DIR__ . '/secrets.php' : [];
$user = $__secrets['db_user'] ?? '';
$pass = $__secrets['db_pass'] ?? '';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    $pdo = null;
}
