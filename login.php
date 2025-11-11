<?php
require_once 'config.php';
session_start();

if (is_logged_in()) {
    header('Location: patients.php');
    exit;
}

$err = '';
$registered = !empty($_GET['registered']);
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $err = 'Email and password are required.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password_hash, name FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: patients.php');
            exit;
        } else {
            $err = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | LAMBODAR Hospital</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #e0f7fa, #ffffff);
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 60px auto;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    h1 {
      margin-bottom: 10px;
      font-size: 28px;
      color: #00796b;
    }
    .lead {
      font-size: 16px;
      color: #555;
      margin-bottom: 30px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
    }
    input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    .btn {
      background: #00796b;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }
    .btn:hover {
      background: #004d40;
    }
    .ghost {
      background: transparent;
      color: #00796b;
      border: 1px solid #00796b;
    }
    .ghost:hover {
      background: #e0f2f1;
    }
    .info-box {
      background: #f0fff7;
      border: 1px solid #def7ec;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 6px;
      color: #2e7d32;
    }
    .error-box {
      background: #ffebee;
      border: 1px solid #ffcdd2;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 6px;
      color: #c62828;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Login to Your Account</h1>
    <p class="lead">Access your medical records dashboard</p>

    <?php if ($registered): ?>
      <div class="info-box">Registration successful — please login.</div>
    <?php endif; ?>
    <?php if ($err): ?>
      <div class="error-box"><?=htmlspecialchars($err)?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="form-group">
        <label>Email Address</label>
        <input name="email" type="email" value="<?=htmlspecialchars($email)?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input name="password" type="password" required>
      </div>
      <div style="margin-top: 30px; display: flex; gap: 10px;">
        <button class="btn" type="submit">Login</button>
        <a class="btn ghost" href="register.php">Register</a>
      </div>
    </form>
  </div>
</body>
</html>
