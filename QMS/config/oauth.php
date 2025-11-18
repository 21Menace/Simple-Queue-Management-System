<?php
// config/oauth.php
// Fill these with your Google OAuth 2.0 credentials from Google Cloud Console
// Create OAuth client ID (Web application) and add an Authorized redirect URI:
//   http://qms.local/oauth/google_callback.php
// Or if using a subfolder, e.g.: http://localhost/qms/oauth/google_callback.php

const GOOGLE_CLIENT_ID = 'YOUR_GOOGLE_CLIENT_ID';
const GOOGLE_CLIENT_SECRET = 'YOUR_GOOGLE_CLIENT_SECRET';

// Requested scopes: basic profile and email
const GOOGLE_OAUTH_SCOPES = [
    'openid',
    'email',
    'profile'
];

function oauth_build_redirect_uri(string $path): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = defined('BASE_URL') ? BASE_URL : '/';
    if ($base === '' || $base[0] !== '/') { $base = '/' . $base; }
    return $scheme . '://' . $host . rtrim($base, '/') . '/' . ltrim($path, '/');
}
