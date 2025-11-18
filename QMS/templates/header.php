<?php
// templates/header.php
require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/csrf.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$title = $title ?? (APP_NAME . ' - Admin');
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="csrf-token" content="<?= htmlspecialchars(csrf_token()) ?>">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>styles.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
  body {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
  }
  
  header { position: relative; z-index: 2; }

  .glass-header{
    background: rgba(255,255,255,0.25);
    backdrop-filter: blur(30px) saturate(180%);
    -webkit-backdrop-filter: blur(30px) saturate(180%);
    border-radius: 32px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    border: 1px solid rgba(255,255,255,0.18);
  }
  </style>
</head>
<body class="text-slate-900">
  <header>
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between glass-header">
      <div class="font-semibold"><?= htmlspecialchars(APP_NAME) ?></div>
      <div class="text-sm">Hello, <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?> · <a class="text-blue-600" href="<?= BASE_URL ?>logout.php">Logout</a></div>
    </div>
  </header>
  <main class="max-w-6xl mx-auto px-4 py-6">
