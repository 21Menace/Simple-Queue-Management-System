<?php
require_once __DIR__ . '/../../../src/session.php';
require_once __DIR__ . '/../../../config/config.php';
require_admin();

header('Content-Type: application/json');

try {
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT id, name, description FROM services ORDER BY name');
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['services' => $services]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}