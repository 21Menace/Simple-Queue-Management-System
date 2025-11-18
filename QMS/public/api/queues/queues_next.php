<?php
require_once __DIR__ . '/../../../src/session.php';
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../src/csrf.php';
require_admin();

header('Content-Type: application/json');
if (!csrf_verify()) { http_response_code(403); echo json_encode(['error'=>'Invalid CSRF token']); exit; }

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$queue_id = isset($input['queue_id']) ? (int)$input['queue_id'] : 0;
if ($queue_id <= 0) { echo json_encode(['error'=>'Invalid queue']); exit; }

try{
    $pdo = get_pdo();
    $pdo->beginTransaction();
    
    // Get the next student with their details
    $stmt = $pdo->prepare('
        SELECT qe.id, qe.user_id, u.name as student_name, u.student_id, q.name as queue_name
        FROM queue_entries qe
        JOIN users u ON u.id = qe.user_id
        JOIN queues q ON q.id = qe.queue_id
        WHERE qe.queue_id = ? AND qe.status = "waiting"
        ORDER BY qe.id ASC LIMIT 1 FOR UPDATE
    ');
    $stmt->execute([$queue_id]);
    $next = $stmt->fetch();
    
    if(!$next){
        $pdo->commit();
        echo json_encode(['closed'=>true]);
        exit;
    }
    
    // Update the served student
    $upd = $pdo->prepare('UPDATE queue_entries SET status = "served", served_at = NOW() WHERE id = ?');
    $upd->execute([(int)$next['id']]);
    
    // Trigger notification for the next student in line
    $nextStmt = $pdo->prepare('
        SELECT qe.id, qe.user_id, u.name as student_name, u.student_id, q.name as queue_name
        FROM queue_entries qe
        JOIN users u ON u.id = qe.user_id
        JOIN queues q ON q.id = qe.queue_id
        WHERE qe.queue_id = ? AND qe.status = "waiting"
        ORDER BY qe.id ASC LIMIT 1
    ');
    $nextStmt->execute([$queue_id]);
    $nextStudent = $nextStmt->fetch();
    
    if ($nextStudent) {
        // Store notification for the next student
        $notificationData = [
            'type' => 'you_are_next',
            'user_id' => $nextStudent['user_id'],
            'queue_name' => $nextStudent['queue_name'],
            'message' => 'You are next in line for ' . $nextStudent['queue_name'],
            'timestamp' => time()
        ];
        
        $notificationFile = __DIR__ . '/../../../notifications/' . $nextStudent['user_id'] . '.json';
        if (!is_dir(__DIR__ . '/../../../notifications')) {
            mkdir(__DIR__ . '/../../../notifications', 0755, true);
        }
        file_put_contents($notificationFile, json_encode($notificationData));
    }
    
    $pdo->commit();
    echo json_encode([
        'served' => [
            'id' => (int)$next['id'],
            'student_name' => $next['student_name'],
            'student_id' => $next['student_id']
        ],
        'next_notified' => $nextStudent ? true : false
    ]);
}catch(Exception $e){
    if($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}
