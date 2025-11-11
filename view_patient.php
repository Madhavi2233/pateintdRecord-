<?php
require_once 'config.php';
require_login();

$user_id = (int)$_SESSION['user_id'];
$patient_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare('SELECT * FROM patients WHERE id = ? AND user_id = ?');
$stmt->execute([$patient_id, $user_id]);
$patient = $stmt->fetch();
if (!$patient) {
    header('Location: patients.php');
    exit;
}

$rstmt = $pdo->prepare('SELECT * FROM medical_records WHERE patient_id = ? ORDER BY visit_date DESC');
$rstmt->execute([$patient_id]);
$records = $rstmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?=htmlspecialchars($patient['full_name'])?> | Medical Records</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 960px;
      margin: 40px auto;
      padding: 20px;
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
    aside.card {
      background: #f1f8f9;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 30px;
    }
    main .card {
      background: #fff;
      padding: 20px;
      border: 1px solid #eee;
      border-radius: 8px;
    }
    .history-list {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    .history-item {
      border: 1px solid #ddd;
      padding: 16px;
      border-radius: 6px;
      background: #fcfcfc;
    }
    .small {
      font-size: 13px;
      color: #666;
    }
    strong {
      color: #333;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand">
      <div class="logo">MH</div>
      <div>
        <h1><?=htmlspecialchars($patient['full_name'])?></h1>
        <p class="lead">DOB: <?=htmlspecialchars($patient['dob'])?> • <?=htmlspecialchars($patient['gender'])?></p>
      </div>
    </div>
    <div style="display:flex;gap:10px;">
      <a class="btn ghost" href="add_record.php?patient_id=<?=$patient_id?>">+ Add Record</a>
      <a class="btn ghost" href="patients.php">← Back</a>
    </div>
  </div>

  <aside class="card">
    <h3>Patient Info</h3>
    <p><strong>Name:</strong> <?=htmlspecialchars($patient['full_name'])?></p>
    <p class="small"><strong>Contact:</strong> <?=htmlspecialchars($patient['contact'])?></p>
    <p class="small"><strong>Created:</strong> <?=htmlspecialchars($patient['created_at'])?></p>
  </aside>

  <main>
    <div class="card">
      <h3 style="margin-top:0">Medical Records</h3>
      <?php if (!$records): ?>
        <p class="small">No records yet for this patient.</p>
      <?php else: ?>
        <div class="history-list">
          <?php foreach ($records as $rec): ?>
            <div class="history-item">
              <div style="display:flex;justify-content:space-between;align-items:center">
                <div>
                  <strong><?=htmlspecialchars($rec['visit_date'])?></strong>
                  <div class="small"><?=htmlspecialchars($rec['created_at'])?></div>
                </div>
                <div class="small">#<?= $rec['id'] ?></div>
              </div>
              <div style="margin-top:10px">
                <?php if ($rec['symptoms']): ?><p><strong>Symptoms:</strong><br><?=nl2br(htmlspecialchars($rec['symptoms']))?></p><?php endif; ?>
                <?php if ($rec['diagnosis']): ?><p><strong>Diagnosis:</strong><br><?=nl2br(htmlspecialchars($rec['diagnosis']))?></p><?php endif; ?>
                <?php if ($rec['prescriptions']): ?><p><strong>Prescriptions:</strong><br><?=nl2br(htmlspecialchars($rec['prescriptions']))?></p><?php endif; ?>
                <?php if ($rec['notes']): ?><p><strong>Notes:</strong><br><?=nl2br(htmlspecialchars($rec['notes']))?></p><?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>

