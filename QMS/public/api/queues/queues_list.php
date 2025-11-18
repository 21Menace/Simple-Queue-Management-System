<?php
require_once __DIR__ . '/../../../src/session.php';
require_once __DIR__ . '/../../../config/config.php';
require_admin();

header('Content-Type: application/json');
try{
    $pdo = get_pdo();
    $rows = $pdo->query('SELECT q.id AS id, q.name AS service_name, IFNULL(w.waiting_count,0) AS waiting_count
      FROM queues q
      LEFT JOIN (
        SELECT queue_id, COUNT(*) AS waiting_count
        FROM queue_entries
        WHERE status = "waiting"
        GROUP BY queue_id
      ) w ON w.queue_id = q.id
      WHERE q.active = 1
      GROUP BY q.id, q.name, w.waiting_count
      ORDER BY q.name')->fetchAll();
    echo json_encode(['queues'=>$rows]);
}catch(Exception $e){
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}
