<?php
require_once 'config.php';
session_start();

if (is_logged_in()) {
    header('Location: patients.php');
    exit;
}

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$name || !$email || !$password || !$password2) {
        $errors[] = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } elseif ($password !== $password2) {
        $errors[] = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = 'Email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, $hash]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | LAMBODAR Hospital</title>
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
    .row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
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
    <h1>Create Your Account</h1>
    <p class="lead">Register to manage patient records securely</p>

    <?php if (!empty($errors)): ?>
      <div class="error-box">
        <ul>
          <?php foreach ($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="form-group">
        <label>Full Name</label>
        <input name="name" type="text" value="<?=htmlspecialchars($name)?>" required>
      </div>
      <div class="form-group">
        <label>Email Address</label>
        <input name="email" type="email" value="<?=htmlspecialchars($email)?>" required>
      </div>
      <div class="row">
        <div class="form-group">
          <label>Password</label>
          <input name="password" type="password" required>
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <input name="password2" type="password" required>
        </div>
      </div>
      <div style="margin-top: 30px; display: flex; gap: 10px;">
        <button class="btn" type="submit">Register</button>
        <a class="btn ghost" href="login.php">Login</a>
      </div>
    </form>
  </div>
</body>
</html>
