<?php
require_once __DIR__ . '/../../../src/session.php';
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../src/csrf.php';
require_admin();

header('Content-Type: application/json');

if (!csrf_verify()) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$queue_id = isset($input['queue_id']) ? (int)$input['queue_id'] : 0;
if ($queue_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid queue']);
    exit;
}

try {
    $pdo = get_pdo();
    $pdo->beginTransaction();

    // Ensure queue exists and is active
    $stmt = $pdo->prepare('SELECT id, active FROM queues WHERE id = ? FOR UPDATE');
    $stmt->execute([$queue_id]);
    $queue = $stmt->fetch();
    if (!$queue) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['error' => 'Queue not found']);
        exit;
    }

    // Mark all waiting entries as left/cancelled
    $updEntries = $pdo->prepare('UPDATE queue_entries SET status = "left" WHERE queue_id = ? AND status = "waiting"');
    $updEntries->execute([$queue_id]);

    // Deactivate the queue
    $updQueue = $pdo->prepare('UPDATE queues SET active = 0 WHERE id = ?');
    $updQueue->execute([$queue_id]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}


