<?php
require_once 'config.php';
require_login();

$user_id = (int)$_SESSION['user_id'];
$search = trim($_GET['q'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE user_id = ? AND full_name LIKE CONCAT('%',?,'%') ORDER BY created_at DESC");
    $stmt->execute([$user_id, $search]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
}
$patients = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patients | LAMBODAR Hospital</title>
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
    .form-group input {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    th {
      background: #e0f2f1;
      color: #00796b;
    }
    .small {
      font-size: 13px;
      color: #666;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand">
      <div class="logo">MH</div>
      <div>
        <h1>Patients</h1>
        <p class="lead">Manage patients & medical records</p>
      </div>
    </div>
    <div style="display:flex;gap:10px;">
      <span class="small">Hi, <?=htmlspecialchars($_SESSION['user_name'] ?? '')?></span>
      <a class="btn ghost" href="add_patient.php">+ New Patient</a>
      <a class="btn ghost" href="logout.php">Logout</a>
    </div>
  </div>

  <aside class="card">
    <h3>Search Patients</h3>
    <form method="get">
      <div class="form-group">
        <input name="q" placeholder="Search by name..." value="<?=htmlspecialchars($search)?>">
      </div>
      <div style="display:flex;gap:10px;margin-top:10px;">
        <button class="btn" type="submit">Search</button>
        <a class="btn ghost" href="patients.php">Clear</a>
      </div>
    </form>
  </aside>

  <main>
    <div class="card">
      <h3 style="margin-top:0">Patient List</h3>
      <?php if (!$patients): ?>
        <p class="small">No patients found. Add a new patient to get started.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr><th>Name</th><th>DOB</th><th>Gender</th><th>Contact</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($patients as $p): ?>
              <tr>
                <td><?=htmlspecialchars($p['full_name'])?></td>
                <td class="small"><?=htmlspecialchars($p['dob'])?></td>
                <td class="small"><?=htmlspecialchars($p['gender'])?></td>
                <td class="small"><?=htmlspecialchars($p['contact'])?></td>
                <td>
                  <a class="btn ghost" href="view_patient.php?id=<?= $p['id'] ?>">View</a>
                  <a class="btn ghost" href="add_record.php?patient_id=<?= $p['id'] ?>">Add Record</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>
