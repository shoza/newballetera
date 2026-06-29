<?php
require_once __DIR__ . '/auth.php';
requireLogin();

header('Content-Type: application/json');

$uploadDir = __DIR__ . '/../img/uploads/';
$images = [];

if (is_dir($uploadDir)) {
    // Get all files sorted by modified time (newest first)
    $files = glob($uploadDir . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    foreach ($files as $file) {
        $images[] = 'img/uploads/' . basename($file);
    }
}

echo json_encode(['success' => true, 'images' => $images]);
