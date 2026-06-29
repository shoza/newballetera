<?php
require_once __DIR__ . '/auth.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;

if ($id && $pdo) {
    $stmt = $pdo->prepare("DELETE FROM productions WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
