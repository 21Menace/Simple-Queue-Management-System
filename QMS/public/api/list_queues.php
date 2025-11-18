<?php
require_once __DIR__ . '/../../src/session.php';
require_once __DIR__ . '/../../config/config.php';
require_login();

$pdo = get_pdo();
$rows = $pdo->query('SELECT id, name, description, active FROM queues WHERE active = 1 ORDER BY name')->fetchAll();
header('Content-Type: application/json');
echo json_encode(['queues' => $rows]);
