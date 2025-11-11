<?php
require_once 'config.php';
require_login();

$user_id = (int)$_SESSION['user_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $dob = $_POST['dob'] ?? null;
    $gender = $_POST['gender'] ?? 'Other';
    $contact = trim($_POST['contact'] ?? '');

    if ($full_name === '') {
        $errors[] = 'Patient name is required.';
    } else {
        $ins = $pdo->prepare('INSERT INTO patients (user_id, full_name, dob, gender, contact) VALUES (?,?,?,?,?)');
        $ins->execute([$user_id, $full_name, $dob ?: null, $gender, $contact]);
        header('Location: patients.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Patient | LAMBODAR Hospital</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 800px;
      margin: 40px auto;
      padding: 30px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 30px;
    }
    .brand {
      display: flex;
      gap: 20px;
      align-items: center;
    }
    .logo {
      background: #00796b;
      color: white;
      font-weight: bold;
      font-size: 20px;
      padding: 10px 14px;
      border-radius: 6px;
    }
    h1 {
      margin: 0;
      font-size: 26px;
      color: #00796b;
    }
    .lead {
      font-size: 14px;
      color: #555;
    }
    .btn {
      padding: 10px 16px;
      font-size: 14px;
      border-radius: 6px;
      text-decoration: none;
      border: none;
      cursor: pointer;
    }
    .ghost {
      background: transparent;
      color: #00796b;
      border: 1px solid #00796b;
    }
    .ghost:hover {
      background: #e0f2f1;
    }
    .form-group {
      margin-bottom: 20px;
    }
    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
    }
    input, select {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    .row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
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
  <div class="header">
    <div class="brand">
      <div class="logo">MH</div>
      <div>
        <h1>Add Patient</h1>
        <p class="lead">Enter basic patient details</p>
      </div>
    </div>
    <div><a class="btn ghost" href="patients.php">← Back</a></div>
  </div>

  <?php if ($errors): ?>
    <div class="error-box">
      <ul><?php foreach ($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="form-group">
      <label>Full Name</label>
      <input name="full_name" type="text" value="<?=htmlspecialchars($_POST['full_name'] ?? '')?>" required>
    </div>
    <div class="row">
      <div class="form-group">
        <label>Date of Birth</label>
        <input name="dob" type="date" value="<?=htmlspecialchars($_POST['dob'] ?? '')?>">
      </div>
      <div class="form-group">
        <label>Gender</label>
        <select name="gender">
          <option value="Female" <?= (($_POST['gender'] ?? '') === 'Female') ? 'selected':'' ?>>Female</option>
          <option value="Male" <?= (($_POST['gender'] ?? '') === 'Male') ? 'selected':'' ?>>Male</option>
          <option value="Other" <?= (($_POST['gender'] ?? '') === 'Other') ? 'selected':'' ?>>Other</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label>Contact (Phone / Email)</label>
      <input name="contact" type="text" value="<?=htmlspecialchars($_POST['contact'] ?? '')?>">
    </div>
    <div style="display:flex;gap:10px;">
      <button class="btn" type="submit">Save Patient</button>
      <a class="btn ghost" href="patients.php">Cancel</a>
    </div>
  </form>
</div>
</body>
</html>
