<?php
// Crust Issues — Admin Dashboard
require __DIR__ . '/../../includes/db.php'; 
require __DIR__ . '/../../includes/auth.php';

// Require admin
$user = current_user();
if (!$user || ($user['role'] ?? '') !== 'admin') {
  header('Location: ../crustissueslogin.php');
  exit;
}

// ---- KPIs (safe queries with fallbacks) ----
function kpi($pdo, $sql, $params = []) {
  try { $st = $pdo->prepare($sql); $st->execute($params); return (int)$st->fetchColumn(); }
  catch (Throwable $e) { return 0; }
}

$totalUsers  = kpi($pdo, "SELECT COUNT(*) FROM users");
$newUsers    = kpi($pdo, "SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()");
$activeUsers = kpi($pdo, "SELECT COUNT(DISTINCT o.user_id) FROM orders o WHERE o.created_at >= (CURRENT_DATE - INTERVAL 30 DAY)");
$newOrders   = kpi($pdo, "SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()");
$totalOrders = kpi($pdo, "SELECT COUNT(*) FROM orders");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Crust Issues — Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="grid">
    <!-- Sidebar -->
    <aside class="side">
      <div class="logo">
        <img src="../pictures/logo.png" alt="Crust Issues">
        <b>CRUST <span style="color:#d34f4f">ISSUES</span></b>
      </div>

      <nav class="nav">
        <a href="./dashboard.php" class="active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="./orders.php"><i class="fa-regular fa-clipboard"></i> Orders</a>
        <a href="./users.php"><i class="fa-regular fa-user"></i> Reg Users</a>
        <!-- 👇 Menu now goes to products_new.php -->
        <a href="./products_new.php"><i class="fa-solid fa-utensils"></i> Menu</a>
      </nav>

      <a class="logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOGOUT</a>
    </aside>

    <!-- Main -->
    <main class="main">
      <h1 class="hello">Hello, <em>Admin!</em></h1>

      <section class="panel">
        <h2 class="panel-title">DASHBOARD</h2>
        <div class="kpis">
          <div class="kpi"><span class="chip">New Users</span><span class="value"><?= $newUsers ?></span><div class="sub">new users</div></div>
          <div class="kpi"><span class="chip">Active Users</span><span class="value"><?= $activeUsers ?></span><div class="sub">active users (30d)</div></div>
          <div class="kpi"><span class="chip">Total Users</span><span class="value"><?= $totalUsers ?></span><div class="sub">total users</div></div>
        </div>
      </section>

      <section class="panel">
        <h2 class="panel-title">ORDERS</h2>
        <div class="orders">
          <div class="kpi"><span class="chip">New Orders</span><span class="value"><?= $newOrders ?></span><div class="sub">new orders today</div></div>
          <div class="kpi"><span class="chip">Total Orders</span><span class="value"><?= $totalOrders ?></span><div class="sub">total orders</div></div>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
