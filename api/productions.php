<?php
// api/productions.php

// Set headers to allow cross-origin requests and define content type as JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';

try {
    if (!$pdo) {
        throw new Exception("Database connection failed. Please ensure setup/init_db.php has been run.");
    }

    $stmt = $pdo->prepare("SELECT * FROM productions ORDER BY date DESC, id DESC");
    $stmt->execute();
    
    $productions = $stmt->fetchAll();
    
    // Return data as JSON
    echo json_encode([
        'status' => 'success',
        'data' => $productions
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
