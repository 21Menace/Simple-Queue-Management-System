<?php
// src/session.php
require_once __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user_id(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function current_user_role(): ?string {
    return $_SESSION['role'] ?? null;
}

function current_user_name(): ?string {
    return $_SESSION['name'] ?? null;
}

function require_login(): void {
    if (!current_user_id()) {
        // If it's an API request expecting JSON, return JSON instead of HTML redirect
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $isApi = (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) || stripos($accept, 'application/json') !== false;
        if ($isApi) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

function require_admin(): void {
    require_login();
    if (current_user_role() !== 'admin') {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $isApi = (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) || stripos($accept, 'application/json') !== false;
        if ($isApi) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }
        http_response_code(403);
        echo 'Access denied.';
        exit;
    }
}

function set_user_session(int $user_id, string $role, string $name): void {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $role;
    $_SESSION['name'] = $name;
}

function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}
