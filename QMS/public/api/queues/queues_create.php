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
$service_id = (int)($input['service_id'] ?? 0);

if ($service_id === 0) { 
    http_response_code(400);
    echo json_encode(['error'=>'Service ID required']); 
    exit; 
}

try {
    $pdo = get_pdo();
    
    // Get service name
    $stmt = $pdo->prepare('SELECT name FROM services WHERE id = ?');
    $stmt->execute([$service_id]);
    $service = $stmt->fetch();
    
    if (!$service) {
        http_response_code(404);
        echo json_encode(['error'=>'Service not found']);
        exit;
    }
    
    // Create queue for this service
    $stmt = $pdo->prepare('INSERT INTO queues (service_id, name, active) VALUES (?, ?, 1)');
    $stmt->execute([$service_id, $service['name']]);
    echo json_encode(['success'=>true, 'id' => (int)$pdo->lastInsertId()]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}