<?php
require_once __DIR__ . '/../../src/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/csrf.php';
require_login();

header('Content-Type: application/json');
if (!csrf_verify()) { http_response_code(403); echo json_encode(['success'=>false,'error'=>'Invalid CSRF token']); exit; }
try{
    $pdo = get_pdo();
    $stmt = $pdo->prepare('UPDATE queue_entries SET status = "left" WHERE user_id = ? AND status = "waiting"');
    $stmt->execute([current_user_id()]);
    echo json_encode(['success'=>true]);
}catch(Exception $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
