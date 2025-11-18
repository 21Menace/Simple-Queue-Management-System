<?php
require_once __DIR__ . '/../../../src/session.php';
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../src/csrf.php';
require_admin();

header('Content-Type: application/json');
if (!csrf_verify()) { 
    http_response_code(403); 
    echo json_encode(['error'=>'Invalid CSRF token']); 
    exit; 
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$name = trim($input['name'] ?? '');
$description = trim($input['description'] ?? '');

if ($name === '') { 
    http_response_code(400);
    echo json_encode(['error'=>'Service name is required']); 
    exit; 
}

try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO services (name, description) VALUES (?, ?)');
    $stmt->execute([$name, $description ?: null]);
    echo json_encode(['success'=>true, 'id' => (int)$pdo->lastInsertId()]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}