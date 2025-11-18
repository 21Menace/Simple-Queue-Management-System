<?php
// src/csrf.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(): bool {
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $token = '';

    // Prefer header
    $headers = getallheaders();
    if ($headers && isset($headers['X-CSRF-Token'])) {
        $token = $headers['X-CSRF-Token'];
    }
    // Fallback to POST field
    if (!$token && isset($_POST['csrf_token'])) {
        $token = (string)$_POST['csrf_token'];
    }

    return is_string($sessionToken) && $sessionToken !== '' && hash_equals($sessionToken, (string)$token);
}
