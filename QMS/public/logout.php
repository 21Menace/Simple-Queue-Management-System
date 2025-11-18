<?php
require_once __DIR__ . '/../src/session.php';
logout();
header('Location: http://localhost/QMS/public/login.php');
exit;
