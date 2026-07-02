<?php
require_once __DIR__ . '/auth.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';

$id = (int)($_GET['id'] ?? 0);

if ($id && $pdo) {
    $stmt = $pdo->prepare("SELECT headshot_path, resume_path, dance_photos FROM dancer_applications WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $root = __DIR__ . '/../';
        foreach (['headshot_path', 'resume_path'] as $col) {
            if (!empty($row[$col])) @unlink($root . $row[$col]);
        }
        foreach (json_decode($row['dance_photos'] ?? '[]', true) ?: [] as $path) {
            @unlink($root . $path);
        }
        $pdo->prepare("DELETE FROM dancer_applications WHERE id = ?")->execute([$id]);
    }
}

header("Location: index.php?tab=dancers");
exit;
