<?php
// Run from browser: /newballetera.com/setup/run_migrate.php
// Delete this file after running.
require_once __DIR__ . '/../config/database.php';

if (!$pdo) {
    die('Database connection failed.');
}

$sql = file_get_contents(__DIR__ . '/migrate.sql');

// Split on semicolons to run each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));

$errors = [];
foreach ($statements as $stmt) {
    if (empty($stmt)) continue;
    try {
        $pdo->exec($stmt);
        echo "<p style='color:green'>OK: " . htmlspecialchars(substr($stmt, 0, 80)) . "...</p>";
    } catch (PDOException $e) {
        $errors[] = $e->getMessage();
        echo "<p style='color:red'>ERR: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo empty($errors) ? "<h2>Migration complete.</h2>" : "<h2>Migration finished with errors above.</h2>";
