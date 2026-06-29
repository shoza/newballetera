<?php
// config/database.php

$host = 'localhost';
$db   = 'newballetera';
$user = 'shoza_nbe';
$pass = 'REDACTED_SEE_secrets.php';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // If database doesn't exist yet, we catch the error.
    // For init script, we might want to connect without a database.
    if ($e->getCode() == 1049) {
        // Unknown database, we will handle this in the setup script
        $pdo = null;
    } else {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
