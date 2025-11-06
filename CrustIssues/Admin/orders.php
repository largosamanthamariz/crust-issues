<?php
// Crust Issues — Admin Orders
require __DIR__ . '/../../includes/db.php'; 
require __DIR__ . '/../../includes/auth.php';
require_role('admin');

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Pagination
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 20;
$offset = ($page - 1) * $limit;

// Fetch orders (join users)
$orders = [];
$totalCount = 0;

try {
  $totalCount = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

  $q = $pdo->prepare("
    SELECT o.id, o.user_id, o.total, o.address, o.note, o.created_at,
           u.username, u.email
    FROM orders o
    LEFT JOIN users u ON u.id = o.user_id
    ORDER BY o.created_at DESC
    LIMIT :lim OFFSET :off
  ");
  $q->bindValue(':lim',  $limit,  PDO::PARAM_INT);
  $q->bindValue(':off',  $offset, PDO::PARAM_INT);
  $q->execute();
  $orders = $q->fetchAll();
} catch (Throwable $e) {
  $orders = [];
}

// Preload items for the orders on this page
$orderIds = array_column($orders, 'id');
$itemsByOrder = [];

if ($orderIds) {
  $ph = implode(',', array_fill(0, count($orderIds), '?'));
  $qi = $pdo->prepare("
    SELECT oi.order_id, oi.product_id, oi.quantity, oi.price, p.name, p.image
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id IN ($ph)
    ORDER BY oi.order_id ASC, oi.id ASC
  ");
  $qi->execute($orderIds);
  while ($row = $qi->fetch()) {
    $itemsByOrder[(int)$row['order_id']][] = $row;
  }
}

// Pager helpers
$pages = max(1, (int)ceil($totalCount / $limit));
$prev  = max(1, $page - 1);
$next  = min($pages, $page + 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin — Orders | Crust Issues</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css">
  <style>
    .table{width:100%;border-collapse:separate;border-spacing:0 10px}
    .table thead th{font-size:12px;letter-spacing:.4px;text-transform:uppercase;color:#666;text-align:left;padding:8px 10px}
    .row{background:#fff;border-radius:14px;box-shadow:0 6px 18px rgba(0,0,0,.06)}
    .row td{padding:12px 10px;vertical-align:middle}
    .pill{display:inline-block;padding:6px 10px;border-radius:999px;background:#f6c7cf;color:#7a2a33;font-weight:700;font-size:12px}
    .btn-ghost{background:transparent;border:2px solid #e23c3c;color:#e23c3c;border-radius:10px;padding:6px 10px;font-weight:700;text-decoration:none;cursor:pointer}
    .items{display:none;background:#fff;border-left:4px solid #f2a6b1;margin:6px 0 14px;border-radius:10px;padding:10px}
    .item{display:grid;grid-template-columns:56px 1fr auto auto;gap:10px;align-items:center;padding:6px 0}
    .thumb{width:56px;height:56px;border-radius:10px;object-fit:cover;background:#f7f7f7}
    .muted{color:#777}

    .grid-footer{display:flex;justify-content:space-between;align-items:center;margin-top:16px;flex-wrap:wrap;gap:8px}
    /* Pretty pager (same as Users) */
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
        <a href="./orders.php" class="active"><i class="fa-regular fa-clipboard"></i> Orders</a>
        <a href="./users.php"><i class="fa-regular fa-user"></i> Reg Users</a>
        <a href="./products.php"><i class="fa-solid fa-utensils"></i> Menu</a>
      </nav>
      <a class="logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOGOUT</a>
    </aside>

    <!-- Main -->
    <main class="main">
      <h1 class="hello">Orders <em>Overview</em></h1>

      <section class="panel">
        <h2 class="panel-title">Recent Orders</h2>

        <?php if (!$orders): ?>
          <div class="quote">No orders found.</div>
        <?php else: ?>
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Placed</th>
                <th>Address</th>
                <th>Note</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $o):
              $oid   = (int)$o['id'];
              $its   = $itemsByOrder[$oid] ?? [];
              $cnt   = 0; foreach ($its as $it) $cnt += (int)$it['quantity'];
            ?>
              <tr class="row">
                <td><span class="pill">#<?= $oid ?></span></td>
                <td>
                  <div style="font-weight:700"><?= e($o['username'] ?? ('User '.$o['user_id'])) ?></div>
                  <div class="muted" style="font-size:12px"><?= e($o['email'] ?? '') ?></div>
                </td>
                <td><?= (int)$cnt ?></td>
                <td><b>P<?= number_format((float)$o['total'], 2) ?></b></td>
                <td><?= e(date('Y-m-d H:i', strtotime($o['created_at']))) ?></td>
                <td style="max-width:260px"><?= e($o['address'] ?? '') ?></td>
                <td style="max-width:200px"><?= e($o['note'] ?? '') ?></td>
                <td>
                  <button class="btn-ghost" onclick="toggleItems(<?= $oid ?>)"><i class="fa-regular fa-eye"></i> View</button>
                </td>
              </tr>
              <tr>
                <td colspan="8">
                  <div class="items" id="box-<?= $oid ?>">
                    <?php if (!$its): ?>
                      <div class="muted">No items.</div>
                    <?php else: foreach ($its as $it): ?>
                      <div class="item">
                        <img class="thumb" src="<?= e($it['image'] ?: '../pictures/placeholder.jpg') ?>" alt="">
                        <div>
                          <div style="font-weight:700"><?= e($it['name'] ?? ('Product #'.$it['product_id'])) ?></div>
                          <div class="muted" style="font-size:12px">ID <?= (int)$it['product_id'] ?></div>
                        </div>
                        <div>x<?= (int)$it['quantity'] ?></div>
                        <div>P<?= number_format((float)$it['price'] * (int)$it['quantity'], 2) ?></div>
                      </div>
                    <?php endforeach; endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>

          <div class="grid-footer">
            <div class="muted">Showing page <?= $page ?> of <?= $pages ?> (<?= $totalCount ?> orders)</div>
            <div class="pager">
              <?php if ($page > 1): ?>
                <a class="pg-btn" href="?page=1">« First</a>
                <a class="pg-btn" href="?page=<?= $prev ?>">‹ Prev</a>
              <?php endif; ?>
              <span class="pg-current">Page <?= $page ?> of <?= $pages ?></span>
              <?php if ($page < $pages): ?>
                <a class="pg-btn" href="?page=<?= $next ?>">Next ›</a>
                <a class="pg-btn" href="?page=<?= $pages ?>">Last »</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </section>
    </main>
  </div>

  <script>
    function toggleItems(id){
      const box = document.getElementById('box-' + id);
      if (!box) return;
      box.style.display = box.style.display === 'block' ? 'none' : 'block';
    }
  </script>
</body>
</html>
