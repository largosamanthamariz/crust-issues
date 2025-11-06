<?php
// Admin — Products list
require __DIR__ . '/../../includes/db.php'; 
require __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['csrf_token'];

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// search & pagination
$q      = trim($_GET['q'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 12;
$offset = ($page - 1) * $limit;

$where  = [];
$params = [];

if ($q !== '') {
  $where[] = "(name LIKE :q OR category LIKE :q)";
  $params[':q'] = "%{$q}%";
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// counts
$total = 0;
$rows  = [];
try {
  $st = $pdo->prepare("SELECT COUNT(*) FROM products {$whereSql}");
  $st->execute($params);
  $total = (int)$st->fetchColumn();

  $sql = "
    SELECT id, name, price, image, category, stock, created_at
    FROM products
    {$whereSql}
    ORDER BY created_at DESC, id DESC
    LIMIT :lim OFFSET :off
  ";
  $st = $pdo->prepare($sql);
  foreach ($params as $k=>$v) $st->bindValue($k, $v);
  $st->bindValue(':lim', $limit, PDO::PARAM_INT);
  $st->bindValue(':off', $offset, PDO::PARAM_INT);
  $st->execute();
  $rows = $st->fetchAll();
} catch (Throwable $e) {
  $rows = [];
}

$pages = max(1, (int)ceil($total / $limit));
$prev  = max(1, $page - 1);
$next  = min($pages, $page + 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin — Products | Crust Issues</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css">
  <style>
    .toolbar{display:flex;gap:10px;align-items:center;margin-bottom:14px;flex-wrap:wrap}
    .toolbar input[type="text"]{border:1.5px solid #e5e7eb;border-radius:10px;padding:8px 10px;font-size:14px;background:#fff}
    .btn{background:#000;color:#fff;border:none;border-radius:10px;padding:8px 12px;font-weight:700;cursor:pointer;text-decoration:none}
    .btn-outline{background:#fff;border:2px solid #e23c3c;color:#e23c3c}
    .btn-outline:hover{background:#e23c3c;color:#fff}
    .grid{grid-template-columns:260px 1fr}
    .cards{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
    @media (max-width:1100px){.cards{grid-template-columns:repeat(2,1fr)}}
    @media (max-width:720px){.cards{grid-template-columns:1fr}}
    .card{background:#fff;border-radius:16px;box-shadow:0 8px 24px rgba(0,0,0,.06);overflow:hidden;display:flex;flex-direction:column}
    .thumb{width:100%;height:180px;object-fit:cover;background:#f6c7cf}
    .card-body{padding:12px;display:flex;flex-direction:column;gap:8px}
    .name{font-weight:700}
    .meta{display:flex;justify-content:space-between;font-size:13px;color:#666}
    .actions{display:flex;gap:8px;margin-top:8px}
    .badge{display:inline-block;padding:4px 8px;border-radius:999px;background:#f6c7cf;color:#7a2a33;font-weight:700;font-size:12px}
    .pager{display:flex;justify-content:center;align-items:center;gap:10px;margin-top:20px;flex-wrap:wrap}
    .pg-btn{background:#fff;border:2px solid #e23c3c;color:#e23c3c;font-weight:600;text-decoration:none;padding:6px 12px;border-radius:8px;transition:all .2s ease}
    .pg-btn:hover{background:#e23c3c;color:#fff}
    .pg-current{font-weight:700;background:#e23c3c;color:#fff;padding:6px 14px;border-radius:8px}
  </style>
</head>
<body>
  <div class="grid">
    <aside class="side">
      <div class="logo">
        <img src="../pictures/logo.png" alt="Crust Issues"><b>CRUST <span style="color:#d34f4f">ISSUES</span></b>
      </div>
      <nav class="nav">
        <a href="./dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="./orders.php"><i class="fa-regular fa-clipboard"></i> Orders</a>
        <a href="./users.php"><i class="fa-regular fa-user"></i> Reg Users</a>
        <a href="./products.php" class="active"><i class="fa-solid fa-utensils"></i> Menu</a>
      </nav>
      <a class="logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOGOUT</a>
    </aside>

    <main class="main">
      <h1 class="hello">Products <em>Manager</em></h1>

      <section class="panel">
        <h2 class="panel-title">Products</h2>

        <div class="toolbar">
          <form method="get" style="display:flex;gap:10px;align-items:center">
            <input type="text" name="q" placeholder="Search name / category" value="<?= e($q) ?>">
            <button class="btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            <?php if ($q !== ''): ?>
              <a class="btn" style="background:#555" href="products.php">Clear</a>
            <?php endif; ?>
          </form>
          <a class="btn-outline" href="products_new.php"><i class="fa-solid fa-plus"></i> Add Product</a>
        </div>

        <?php if (!$rows): ?>
          <div class="quote">No products yet. Click <b>Add Product</b> to create one.</div>
        <?php else: ?>
          <div class="cards">
            <?php foreach ($rows as $p): ?>
              <div class="card">
                <img class="thumb" src="<?= e($p['image'] ?: '../pictures/placeholder.jpg') ?>" alt="">
                <div class="card-body">
                  <div class="name"><?= e($p['name']) ?></div>
                  <div class="meta">
                    <span>P<?= number_format((float)$p['price'], 2) ?></span>
                    <span class="badge"><?= e(ucfirst($p['category'])) ?></span>
                  </div>
                  <div class="meta">
                    <span>Stock: <b><?= (int)$p['stock'] ?></b></span>
                    <span class="muted">#<?= (int)$p['id'] ?></span>
                  </div>
                  <div class="actions">
                    <a class="btn" href="products_edit.php?id=<?= (int)$p['id'] ?>"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                    <form action="products_delete.php" method="post" onsubmit="return confirm('Delete this product?')">
                      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                      <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                      <button class="btn" style="background:#b61e2e"><i class="fa-regular fa-trash-can"></i> Delete</button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="pager">
            <?php if ($page > 1): ?>
              <a class="pg-btn" href="?<?= http_build_query(['q'=>$q,'page'=>1]) ?>">« First</a>
              <a class="pg-btn" href="?<?= http_build_query(['q'=>$q,'page'=>$prev]) ?>">‹ Prev</a>
            <?php endif; ?>
            <span class="pg-current">Page <?= $page ?> of <?= $pages ?></span>
            <?php if ($page < $pages): ?>
              <a class="pg-btn" href="?<?= http_build_query(['q'=>$q,'page'=>$next]) ?>">Next ›</a>
              <a class="pg-btn" href="?<?= http_build_query(['q'=>$q,'page'=>$pages]) ?>">Last »</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>
