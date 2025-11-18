<?php
require_once __DIR__ . '/../../src/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/csrf.php';
require_admin();

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$name = trim($input['name'] ?? '');
$description = trim($input['description'] ?? '');
$avg = isset($input['average_service_seconds']) && $input['average_service_seconds'] !== null ? (int)$input['average_service_seconds'] : null;

header('Content-Type: application/json');
if (!csrf_verify()) { http_response_code(403); echo json_encode(['success'=>false,'error'=>'Invalid CSRF token']); exit; }
if ($name === '') { echo json_encode(['success'=>false,'error'=>'Name required']); exit; }

try{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO queues (name, description, average_service_seconds, active) VALUES (?,?,?,1)');
    $stmt->execute([$name, $description ?: null, $avg]);
    echo json_encode(['success'=>true]);
}catch(Exception $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
