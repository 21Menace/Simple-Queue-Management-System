<?php
require_once __DIR__ . '/../../src/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/csrf.php';
require_login();

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$queue_id = isset($input['queue_id']) ? (int)$input['queue_id'] : 0;

header('Content-Type: application/json');
if (!csrf_verify()) { http_response_code(403); echo json_encode(['success'=>false,'error'=>'Invalid CSRF token']); exit; }
if ($queue_id <= 0) { echo json_encode(['success'=>false,'error'=>'Invalid queue']); exit; }

try {
    $pdo = get_pdo();
    // Check already in waiting state for this queue
    $stmt = $pdo->prepare('SELECT id FROM queue_entries WHERE queue_id = ? AND user_id = ? AND status = "waiting"');
    $stmt->execute([$queue_id, current_user_id()]);
    if ($stmt->fetch()) { echo json_encode(['success'=>true,'message'=>'Already joined']); exit; }

    // Ensure queue exists and active
    $q = $pdo->prepare('SELECT id FROM queues WHERE id = ? AND active = 1');
    $q->execute([$queue_id]);
    if (!$q->fetch()) { echo json_encode(['success'=>false,'error'=>'Queue not available']); exit; }

    $stmt = $pdo->prepare('INSERT INTO queue_entries (queue_id, user_id, status) VALUES (?,?,"waiting")');
    $stmt->execute([$queue_id, current_user_id()]);
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
