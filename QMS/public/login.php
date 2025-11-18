<?php
require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/csrf.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Security check failed. Please refresh and try again.';
    } else {
    $student_id = trim($_POST['student_id'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($student_id === '' || $password === '') {
        $error = 'Please enter Student ID and Password';
    } else {
        $pdo = get_pdo();
        $stmt = $pdo->prepare('SELECT id, name, role, password_hash FROM users WHERE student_id = ?');
        $stmt->execute([$student_id]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            set_user_session((int)$user['id'], $user['role'], $user['name']);
            if ($user['role'] === 'admin') {
                header('Location: http://localhost/QMS/public/admin.php');
            } else {
                header('Location: http://localhost/QMS/public/student.php');
            }
            exit;
        } else {
            $error = 'Invalid credentials';
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
  <title>Login - <?= htmlspecialchars(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

    .login-card {
      background: rgba(255, 255, 255, 0.25); /* Lower alpha for more translucency */
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

    .signup-link {
      font-size: 15px;
      color: #1a1a1a;
      text-decoration: none;
      font-weight: 500;
      transition: opacity 0.3s;
    }

    .signup-link:hover {
      opacity: 0.7;
    }

    h1 {
      font-size: 42px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 36px;
      letter-spacing: -1px;
    }

    .notice {
      text-align: center;
      color: #059669;
      font-size: 14px;
      font-weight: 500;
      background: rgba(209, 250, 229, 0.9);
      border: 1px solid #34d399;
      padding: 12px 16px;
      border-radius: 16px;
      margin-bottom: 24px;
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
      background: rgba(255, 255, 255, 0.25); /* translucent */
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

    .forgot-password {
      display: block;
      text-align: left;
      color: #1a1a1a;
      font-size: 14px;
      text-decoration: underline;
      margin: 12px 0 28px 0;
      font-weight: 400;
      transition: opacity 0.3s;
    }

    .forgot-password:hover {
      opacity: 0.7;
    }

    .tagline {
      font-size: 14px;
      line-height: 1.6;
      color: #4b5563;
      margin-bottom: 24px;
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

    @media (max-width: 600px) {
      .login-card {
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
    <div class="login-card">
      <div class="header">
        <div class="brand"><?= htmlspecialchars(APP_NAME) ?></div>
        <a href="register.php" class="signup-link">Sign up</a>
      </div>

      <h1>Log in</h1>

      <?php if (isset($_GET['registered']) && $_GET['registered'] == '1'): ?>
        <div class="notice">✨ Account created! Please login.</div>
      <?php endif; ?>
      
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

        <div class="input-wrapper password-wrapper">
          <input type="password" 
                 id="password" 
                 name="password" 
                 class="input-field" 
                 placeholder="password" 
                 required>
          <button type="button" class="toggle-password" onclick="togglePassword()">
            <span id="eye-icon" class="eye-open"></span>
          </button>
        </div>

        <a href="#" class="forgot-password">Forgot password?</a>

        <p class="tagline">
          Access your campus services seamlessly. We promote efficiency, 
          mutual respect, and a community where your time matters—no waiting, 
          just balance and growth.
        </p>

        <button type="submit" class="submit-btn">
          <span>Continue</span>
          <span class="arrow-icon">→</span>
        </button>
      </form>

      <div class="footer-text">
        Create, manage, call queues – all in one place.
      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passwordField = document.getElementById('password');
      const eyeIcon = document.getElementById('eye-icon');
      
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