<?php
// Crust Issues — Admin Registered Users
require __DIR__ . '/../../includes/db.php'; 
require __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['csrf_token'];

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// ---- Filters / Search / Paging ----
$q      = trim($_GET['q'] ?? '');
$role   = trim($_GET['role'] ?? ''); // '', 'user', 'admin'
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 20;
$offset = ($page - 1) * $limit;

$where  = [];
$params = [];

if ($q !== '') {
  $where[] = "(username LIKE :q OR email LIKE :q OR full_name LIKE :q)";
  $params[':q'] = "%{$q}%";
}
if ($role !== '' && in_array($role, ['user','admin'], true)) {
  $where[] = "role = :role";
  $params[':role'] = $role;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// counts
$total = 0;
try {
  $sqlCnt = "SELECT COUNT(*) FROM users {$whereSql}";
  $st = $pdo->prepare($sqlCnt);
  $st->execute($params);
  $total = (int)$st->fetchColumn();
} catch (Throwable $e) {
  $total = 0;
}

// list
$users = [];
try {
  $sql = "
    SELECT id, username, email, role, full_name, address, phone, created_at
    FROM users
    {$whereSql}
    ORDER BY created_at DESC, id DESC
    LIMIT :lim OFFSET :off
  ";
  $st = $pdo->prepare($sql);
  foreach ($params as $k => $v) $st->bindValue($k, $v);
  $st->bindValue(':lim', $limit, PDO::PARAM_INT);
  $st->bindValue(':off', $offset, PDO::PARAM_INT);
  $st->execute();
  $users = $st->fetchAll();
} catch (Throwable $e) {
  $users = [];
}

$pages = max(1, (int)ceil($total / $limit));
$prev  = max(1, $page - 1);
$next  = min($pages, $page + 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin — Registered Users | Crust Issues</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css">
  <style>
    .toolbar{display:flex;gap:10px;align-items:center;margin-bottom:14px;flex-wrap:wrap}
    .toolbar input[type="text"], .toolbar select{
      border:1.5px solid #e5e7eb;border-radius:10px;padding:8px 10px;font-size:14px;background:#fff
    }
    .btn{background:#000;color:#fff;border:none;border-radius:10px;padding:8px 12px;font-weight:700;cursor:pointer;text-decoration:none}
    .table{width:100%;border-collapse:separate;border-spacing:0 10px}
    .table thead th{font-size:12px;letter-spacing:.4px;text-transform:uppercase;color:#666;text-align:left;padding:8px 10px}
    .row{background:#fff;border-radius:14px;box-shadow:0 6px 18px rgba(0,0,0,.06)}
    .row td{padding:12px 10px;vertical-align:middle}
    .pill{display:inline-block;padding:6px 10px;border-radius:999px;background:#f6c7cf;color:#7a2a33;font-weight:700;font-size:12px}
    .muted{color:#777}
    .grid-footer{display:flex;justify-content:space-between;align-items:center;margin-top:16px;flex-wrap:wrap;gap:8px}

    /* Clean, branded pager */
    .pager{display:flex;justify-content:center;align-items:center;gap:10px;margin-top:20px;flex-wrap:wrap}
    .pg-btn{
      background:#fff;border:2px solid #e23c3c;color:#e23c3c;font-weight:600;text-decoration:none;
      padding:6px 12px;border-radius:8px;transition:all .2s ease
    }
    .pg-btn:hover{background:#e23c3c;color:#fff}
    .pg-current{
      font-weight:700;background:#e23c3c;color:#fff;padding:6px 14px;border-radius:8px
    }
  </style>
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
        <a href="./dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="./orders.php"><i class="fa-regular fa-clipboard"></i> Orders</a>
        <a href="./users.php" class="active"><i class="fa-regular fa-user"></i> Reg Users</a>
        <a href="./products_new.php"><i class="fa-solid fa-utensils"></i> Menu</a>
      </nav>
      <a class="logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOGOUT</a>
    </aside>

    <!-- Main -->
    <main class="main">
      <h1 class="hello">Registered <em>Users</em></h1>

      <section class="panel">
        <h2 class="panel-title">All Users</h2>

        <form class="toolbar" method="get">
          <input type="text" name="q" placeholder="Search name / username / email" value="<?= e($q) ?>">
          <select name="role">
            <option value="">All roles</option>
            <option value="user"  <?= $role==='user' ? 'selected':'' ?>>User</option>
            <option value="admin" <?= $role==='admin'? 'selected':'' ?>>Admin</option>
          </select>
          <button class="btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
          <?php if ($q !== '' || $role !== ''): ?>
            <a class="btn" style="background:#555" href="users.php">Clear</a>
          <?php endif; ?>
        </form>

        <?php if (!$users): ?>
          <div class="quote">No users found.</div>
        <?php else: ?>
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>User</th>
                <th>Contact</th>
                <th>Role</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Joined</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
                <tr class="row">
                  <td><span class="pill">#<?= (int)$u['id'] ?></span></td>
                  <td>
                    <div style="font-weight:700"><?= e($u['full_name'] ?: $u['username']) ?></div>
                    <div class="muted" style="font-size:12px">@<?= e($u['username']) ?></div>
                  </td>
                  <td><?= e($u['email']) ?></td>
                  <td><?= e($u['role']) ?></td>
                  <td style="max-width:260px"><?= e($u['address'] ?? '') ?></td>
                  <td><?= e($u['phone'] ?? '') ?></td>
                  <td><?= e($u['created_at'] ?? '') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="grid-footer">
            <div class="muted">Showing page <?= $page ?> of <?= $pages ?> (<?= $total ?> users)</div>
            <div class="pager">
              <?php if ($page > 1): ?>
                <a class="pg-btn" href="?<?= http_build_query(['q'=>$q,'role'=>$role,'page'=>1]) ?>">« First</a>
                <a class="pg-btn" href="?<?= http_build_query(['q'=>$q,'role'=>$role,'page'=>$prev]) ?>">‹ Prev</a>
              <?php endif; ?>

              <span class="pg-current">Page <?= $page ?> of <?= $pages ?></span>

              <?php if ($page < $pages): ?>
                <a class="pg-btn" href="?<?= http_build_query(['q'=>$q,'role'=>$role,'page'=>$next]) ?>">Next ›</a>
                <a class="pg-btn" href="?<?= http_build_query(['q'=>$q,'role'=>$role,'page'=>$pages]) ?>">Last »</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>
