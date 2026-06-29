<?php
// setup/init_db.php

$host = 'localhost';
$user = 'root';
$pass = ''; // Leave this completely empty if your XAMPP password is blank
$dbName = 'newballetera';

try {
    // Connect without database selected
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database `$dbName` created or already exists.<br>\n";

    // Select the database
    $pdo->exec("USE `$dbName`");

    // Read the schema file
    $schemaFile = __DIR__ . '/schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        $pdo->exec($sql);
        echo "Schema imported successfully.<br>\n";

        // Add a default admin user (password: admin123)
        $username = 'admin';
        $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);

        // Check if admin exists to avoid duplicate entry error on multiple runs
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() == 0) {
            $insert = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $insert->execute([$username, $passwordHash]);
            echo "Default admin user created (username: admin, password: admin123).<br>\n";
        }

    } else {
        echo "Error: schema.sql file not found.<br>\n";
    }

} catch (\PDOException $e) {
    die("DB Setup Error: " . $e->getMessage());
}
