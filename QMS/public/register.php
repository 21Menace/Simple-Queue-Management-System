<?php
require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/csrf.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Security check failed. Please refresh and try again.';
    } else {
    $student_id = trim($_POST['student_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($student_id === '' || $name === '' || $password === '') {
        $error = 'Student ID, Name and Password are required';
    } else {
        try {
            $pdo = get_pdo();
            $stmt = $pdo->prepare('SELECT id FROM users WHERE student_id = ? OR email = ?');
            $stmt->execute([$student_id, $email ?: null]);
            if ($stmt->fetch()) {
                $error = 'Student ID or Email already exists';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare('INSERT INTO users (student_id, name, email, password_hash, role) VALUES (?,?,?,?,\'student\')');
                $stmt->execute([$student_id, $name, $email ?: null, $hash]);
                header('Location: http://localhost/QMS/public/login.php?registered=1');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - <?= htmlspecialchars(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <style>
    *, *:before, *:after { 
      padding: 0; 
      margin: 0; 
      box-sizing: border-box; 
    }
    
    body { 
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: url('https://images.pexels.com/photos/34211745/pexels-photo-34211745.jpeg') center/cover no-repeat;
      position: relative;
      padding: 2rem;
    }

    body::before {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(2px);
    }

    .container {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 500px;
    }

    .register-card {
      background: rgba(255, 255, 255, 0.25); /* more translucent */
      backdrop-filter: blur(16px) saturate(180%);
      -webkit-backdrop-filter: blur(16px) saturate(180%);
      border-radius: 32px;
      padding: 48px 40px 40px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      position: relative;
      border: 1px solid rgba(255,255,255,0.18); /* subtle border for glass effect */
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 48px;
    }

    .brand {
      font-size: 18px;
      font-weight: 600;
      color: #1a1a1a;
      letter-spacing: -0.3px;
    }

    .login-link {
      font-size: 15px;
      color: #1a1a1a;
      text-decoration: none;
      font-weight: 500;
      transition: opacity 0.3s;
    }

    .login-link:hover {
      opacity: 0.7;
    }

    h1 {
      font-size: 42px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 36px;
      letter-spacing: -1px;
    }

    .error {
      text-align: center;
      color: #dc2626;
      font-size: 14px;
      font-weight: 500;
      background: rgba(254, 226, 226, 0.9);
      border: 1px solid #f87171;
      padding: 12px 16px;
      border-radius: 16px;
      margin-bottom: 24px;
    }

    .input-wrapper {
      position: relative;
      margin-bottom: 16px;
    }

    .input-icon {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      color: #6b7280;
      font-size: 18px;
      pointer-events: none;
    }

    .input-field {
      width: 100%;
      height: 56px;
      background: rgba(255, 255, 255, 0.25); /* translucent glassmorph */
      border: none;
      border-radius: 28px;
      padding: 0 56px;
      font-size: 15px;
      font-weight: 400;
      color: #1a1a1a;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
      backdrop-filter: blur(8px) saturate(180%);
      -webkit-backdrop-filter: blur(8px) saturate(180%);
    }

    .input-field:focus {
      outline: none;
      background: rgba(255, 255, 255, 1);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .input-field::placeholder {
      color: #9ca3af;
      font-weight: 400;
    }

    .password-wrapper {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #6b7280;
      cursor: pointer;
      padding: 4px;
      font-size: 18px;
      transition: color 0.3s;
    }

    .toggle-password:hover {
      color: #1a1a1a;
    }

    .tagline {
      font-size: 14px;
      line-height: 1.6;
      color: #4b5563;
      margin: 24px 0;
      font-weight: 400;
    }

    .submit-btn {
      width: 100%;
      height: 56px;
      background: #1a1a1a;
      color: #ffffff;
      border: none;
      border-radius: 28px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .submit-btn:hover {
      background: #2d2d2d;
      transform: translateY(-2px);
      box-shadow: 0 6px 24px rgba(0, 0, 0, 0.2);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    .arrow-icon {
      width: 20px;
      height: 20px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .footer-text {
      text-align: center;
      margin-top: 32px;
      padding-top: 24px;
      border-top: 1px solid rgba(0, 0, 0, 0.1);
      font-size: 15px;
      font-weight: 500;
      color: #1a1a1a;
    }

    .age-notice {
      font-size: 13px;
      line-height: 1.5;
      color: #6b7280;
      margin-top: 16px;
      font-weight: 400;
    }

    #eye-icon {
      display: inline-block;
      width: 22px;
      height: 22px;
      vertical-align: middle;
      background: none;
      position: relative;
    }
    .eye-open::before, .eye-closed::before {
      content: '';
      display: block;
      width: 22px;
      height: 22px;
      background-repeat: no-repeat;
      background-size: contain;
    }
    .eye-open::before {
      background-image: url('data:image/svg+xml;utf8,<svg fill="none" stroke="gray" stroke-width="2" viewBox="0 0 24 24" width="22" height="22" xmlns="http://www.w3.org/2000/svg"><ellipse cx="12" cy="12" rx="8" ry="6"/><circle cx="12" cy="12" r="2"/><path d="M2 12c2-4 6-8 10-8s8 4 10 8"/></svg>');
    }
    .eye-closed::before {
      background-image: url('data:image/svg+xml;utf8,<svg fill="none" stroke="gray" stroke-width="2" viewBox="0 0 24 24" width="22" height="22" xmlns="http://www.w3.org/2000/svg"><ellipse cx="12" cy="12" rx="8" ry="6"/><circle cx="12" cy="12" r="2"/><line x1="4" y1="4" x2="20" y2="20" stroke="gray" stroke-width="2"/></svg>');
    }

    .eye-icon {
      display: inline-block;
      width: 22px;
      height: 22px;
      vertical-align: middle;
      background: none;
      position: relative;
    }

    @media (max-width: 600px) {
      .register-card {
        padding: 36px 28px 32px;
      }

      h1 {
        font-size: 36px;
      }

      .input-field {
        height: 52px;
        padding: 0 52px;
      }

      .submit-btn {
        height: 52px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="register-card">
      <div class="header">
        <div class="brand"><?= htmlspecialchars(APP_NAME) ?></div>
        <a href="login.php" class="login-link">Log in</a>
      </div>

      <h1>Sign up</h1>
      
      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        
        <div class="input-wrapper">
          <input type="text" 
                 id="student_id" 
                 name="student_id" 
                 class="input-field" 
                 placeholder="Student ID" 
                 required>
        </div>

        <div class="input-wrapper">
          <input type="text" 
                 id="name" 
                 name="name" 
                 class="input-field" 
                 placeholder="Full name" 
                 required>
        </div>

        <div class="input-wrapper">
          <input type="email" 
                 id="email" 
                 name="email" 
                 class="input-field" 
                 placeholder="Email (optional)">
        </div>

        <div class="input-wrapper password-wrapper">
          <input type="password" 
                 id="password" 
                 name="password" 
                 class="input-field" 
                 placeholder="Create password" 
                 required>
          <button type="button" class="toggle-password" onclick="togglePassword(this)">
          <span class="eye-icon eye-open"></span>
          </button>
        </div>

        <p class="tagline">
          Join our campus community today. Access all services seamlessly, 
          track your queue positions in real-time, and experience a 
          streamlined campus life—no waiting, just efficiency.
        </p>

        <button type="submit" class="submit-btn">
          <span>Create account</span>
          <span class="arrow-icon">→</span>
        </button>

        <p class="age-notice">
          By signing up, you confirm that you're a registered student and 
          agree to our campus service policies.
        </p>
      </form>

      <div class="footer-text">
        Create, manage, call queues – all in one place.
      </div>
    </div>
  </div>

  <script>
    function togglePassword(btn) {
  const passwordField = btn.parentElement.querySelector('input');
  const eyeIcon = btn.querySelector('.eye-icon');
  
  if (passwordField.type === 'password') {
    passwordField.type = 'text';
    eyeIcon.classList.remove('eye-open');
    eyeIcon.classList.add('eye-closed');
  } else {
    passwordField.type = 'password';
    eyeIcon.classList.remove('eye-closed');
    eyeIcon.classList.add('eye-open');
  }
}
  </script>
</body>
</html>