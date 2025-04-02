<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('session.save_path', '/home/hmtchir1/public_html/sessions');
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// آمار فرم‌ها
$all = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$approved = $conn->query("SELECT COUNT(*) FROM products WHERE status = 'approved'")->fetch_row()[0];
$rejected = $conn->query("SELECT COUNT(*) FROM products WHERE status = 'rejected'")->fetch_row()[0];
$delivered = $conn->query("SELECT COUNT(*) FROM products WHERE status = 'delivered'")->fetch_row()[0];

// کاربران
$users = $conn->query("SELECT id, username, role FROM users");
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>داشبورد مدیریت</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Tahoma;
      background: linear-gradient(to right, #00c9ff, #92fe9d);
      padding: 20px;
    }
    .stat-box {
      padding: 20px;
      background: white;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    .table th, .table td {
      vertical-align: middle;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h3>داشبورد مدیریت</h3>
      <a href="logout.php" class="btn btn-danger">خروج</a>
    </div>

    <div class="row text-center">
      <div class="col-md-3"><div class="stat-box bg-info text-white">کل فرم‌ها<br><?= $all ?></div></div>
      <div class="col-md-3"><div class="stat-box bg-success text-white">تأیید شده<br><?= $approved ?></div></div>
      <div class="col-md-3"><div class="stat-box bg-danger text-white">رد شده<br><?= $rejected ?></div></div>
      <div class="col-md-3"><div class="stat-box bg-warning text-dark">تحویل شده<br><?= $delivered ?></div></div>
    </div>

    <canvas id="myChart" height="100"></canvas>

    <div class="d-flex justify-content-between mt-4 mb-2">
      <h5>لیست کاربران</h5>
      <a href="add_user.php" class="btn btn-primary">➕ افزودن کاربر</a>
    </div>

    <table class="table table-bordered bg-white">
      <thead class="table-light">
        <tr>
          <th>نام کاربری</th>
          <th>نقش</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $users->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td>
              <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️ ویرایش</a>
              <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('آیا مطمئنید؟')">🗑️ حذف</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

<script>
const ctx = document.getElementById('myChart');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['کل', 'تأیید شده', 'رد شده', 'تحویل شده'],
    datasets: [{
      label: 'آمار فرم‌ها',
      data: [<?= $all ?>, <?= $approved ?>, <?= $rejected ?>, <?= $delivered ?>],
      borderWidth: 1,
      backgroundColor: ['#17a2b8', '#28a745', '#dc3545', '#ffc107']
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});
</script>

</body>
</html>
