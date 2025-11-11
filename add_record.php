<?php
require_once 'config.php';
require_login();

$user_id = (int)$_SESSION['user_id'];
$patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : (int)($_POST['patient_id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM patients WHERE id = ? AND user_id = ?');
$stmt->execute([$patient_id, $user_id]);
$patient = $stmt->fetch();
if (!$patient) {
    header('Location: patients.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visit_date = $_POST['visit_date'] ?? date('Y-m-d');
    $symptoms = trim($_POST['symptoms'] ?? '');
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $prescriptions = trim($_POST['prescriptions'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($visit_date === '') {
        $errors[] = 'Visit date is required.';
    } else {
        $ins = $pdo->prepare('INSERT INTO medical_records (patient_id, visit_date, symptoms, diagnosis, prescriptions, notes) VALUES (?,?,?,?,?,?)');
        $ins->execute([$patient_id, $visit_date, $symptoms, $diagnosis, $prescriptions, $notes]);
        header('Location: view_patient.php?id=' . $patient_id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Record | <?=htmlspecialchars($patient['full_name'])?></title>
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
    input, textarea {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    textarea {
      min-height: 80px;
      resize: vertical;
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
        <h1>Add Record</h1>
        <p class="lead"><?=htmlspecialchars($patient['full_name'])?></p>
      </div>
    </div>
    <div><a class="btn ghost" href="view_patient.php?id=<?=$patient_id?>">← Back</a></div>
  </div>

  <?php if ($errors): ?>
    <div class="error-box">
      <ul><?php foreach ($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="patient_id" value="<?= $patient_id ?>">
    <div class="form-group">
      <label>Visit Date</label>
      <input name="visit_date" type="date" value="<?=htmlspecialchars($_POST['visit_date'] ?? date('Y-m-d'))?>">
    </div>
    <div class="form-group">
      <label>Symptoms</label>
      <textarea name="symptoms"><?=htmlspecialchars($_POST['symptoms'] ?? '')?></textarea>
    </div>
    <div class="form-group">
      <label>Diagnosis</label>
      <textarea name="diagnosis"><?=htmlspecialchars($_POST['diagnosis'] ?? '')?></textarea>
    </div>
    <div class="form-group">
      <label>Prescriptions</label>
      <textarea name="prescriptions"><?=htmlspecialchars($_POST['prescriptions'] ?? '')?></textarea>
    </div>
    <div class="form-group">
      <label>Notes</label>
      <textarea name="notes"><?=htmlspecialchars($_POST['notes'] ?? '')?></textarea>
    </div>
    <div style="display:flex;gap:10px;">
      <button class="btn" type="submit">Save Record</button>
      <a class="btn ghost" href="view_patient.php?id=<?=$patient_id?>">Cancel</a>
    </div>
  </form>
</div>
</body>
</html>
