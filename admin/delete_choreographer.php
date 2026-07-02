<?php
require_once __DIR__ . '/auth.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';

$id = (int)($_GET['id'] ?? 0);

if ($id && $pdo) {
    $stmt = $pdo->prepare("SELECT resume_path, cover_letter_path, bio_path FROM choreographer_applications WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $root = __DIR__ . '/../';
        foreach (['resume_path', 'cover_letter_path', 'bio_path'] as $col) {
            if (!empty($row[$col])) @unlink($root . $row[$col]);
        }
        $pdo->prepare("DELETE FROM choreographer_applications WHERE id = ?")->execute([$id]);
    }
}

header("Location: index.php?tab=choreos");
exit;
