<?php
require_once __DIR__ . '/../../src/session.php';
require_once __DIR__ . '/../../config/config.php';
require_login();

header('Content-Type: application/json');
$pdo = get_pdo();

// If queue_id provided, return waiting count for that queue (for pills)
if (isset($_GET['queue_id'])) {
    $qid = (int)$_GET['queue_id'];
    $stmt = $pdo->prepare('SELECT COUNT(*) AS waiting FROM queue_entries WHERE queue_id = ? AND status = "waiting"');
    $stmt->execute([$qid]);
    $row = $stmt->fetch();
    echo json_encode(['waiting' => (int)($row['waiting'] ?? 0)]);
    exit;
}

// Otherwise return current user's status
$stmt = $pdo->prepare('SELECT qe.id, qe.queue_id, q.name, q.average_service_seconds
  FROM queue_entries qe
  JOIN queues q ON q.id = qe.queue_id
  WHERE qe.user_id = ? AND qe.status = "waiting"
  ORDER BY qe.id ASC LIMIT 1');
$stmt->execute([current_user_id()]);
$entry = $stmt->fetch();

if (!$entry) { echo json_encode(['in_queue'=>false]); exit; }

// Position is count of waiting entries with lower id in same queue
$posStmt = $pdo->prepare('SELECT COUNT(*) AS pos FROM queue_entries WHERE queue_id = ? AND status = "waiting" AND id < ?');
$posStmt->execute([(int)$entry['queue_id'], (int)$entry['id']]);
$pos = (int)$posStmt->fetch()['pos'] + 1; // +1 for current

$eta = null;
if (!empty($entry['average_service_seconds'])) {
    $etaSeconds = (int)$entry['average_service_seconds'] * ($pos - 1);
    $mins = floor($etaSeconds / 60); $secs = $etaSeconds % 60;
    $eta = sprintf('%d:%02d', $mins, $secs);
}

echo json_encode([
    'in_queue' => true,
    'queue_id' => (int)$entry['queue_id'],
    'queue_name' => $entry['name'],
    'position' => $pos,
    'eta' => $eta,
]);
