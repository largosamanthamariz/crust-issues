<?php
// Crust Issues — Our Menu (dynamic)
$root = __DIR__;
$incDb  = $root . '/includes/db.php';
$incDb2 = $root . '/../includes/db.php';              // fallback if your tree is nested
$incAuth  = $root . '/includes/auth.php';
$incAuth2 = $root . '/../includes/auth.php';

require file_exists($incDb)  ? $incDb  : $incDb2;
require file_exists($incAuth)? $incAuth: $incAuth2;

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['csrf_token'];
$me   = current_user();

// Fetch all products (optionally grouped by category)
$rows = [];
try {
  // If you don't have a `category` column, remove it from the SELECT and use a single section below.
  $stmt = $pdo->query("SELECT id, name, price, image, category FROM products ORDER BY created_at DESC");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  $rows = [];
}

// Group by category (classic/special/drinks); unknowns bucket to 'others'
$groups = ['classic'=>[], 'special'=>[], 'drinks'=>[], 'others'=>[]];
foreach ($rows as $r) {
  $cat = strtolower(trim($r['category'] ?? 'classic'));
  $key = isset($groups[$cat]) ? $cat : 'others';
  $groups[$key][] = $r;
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Menu - Crust Issues</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .menu-head-actions{display:flex;gap:10px;align-items:center}
    .btn-ghost{background:transparent;border:2px solid var(--accent);color:var(--accent);
      padding:8px 14px;border-radius:12px;font-weight:700;text-decoration:none}
    .btn-ghost:hover{background:var(--accent);color:#fff}
    .section-title-inline{display:flex;align-items:center;justify-content:space-between;gap:16px}
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <section class="page-header">
    <div class="container">
      <div class="page-header-content section-title-inline">
        <div>
          <h1 class="page-title">Our Menu</h1>
          <p class="page-subtitle">Freshly baked favorites—add to cart and enjoy.</p>
        </div>

        <!-- Show Add Product shortcut only to admins -->
        <?php if (($me['role'] ?? 'user') === 'admin'): ?>
          <div class="menu-head-actions">
            <a class="btn-ghost" href="Admin/product_new.php"><i class="fa-solid fa-plus"></i> Add Product</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php
  // Helper to render a category block
  function render_category($label, $items, $csrf){
    if (!$items) return;
    ?>
    <section class="products-section">
      <div class="container">
        <h2 class="section-title" style="margin:0 0 8px;font-family:'Inter',sans-serif"><?= e($label) ?></h2>
        <div class="products-grid">
          <?php foreach ($items as $p): ?>
            <div class="product-card">
              <img src="<?= e($p['image'] ?: 'pictures/placeholder.jpg') ?>"
                   alt="<?= e($p['name']) ?>" class="product-image">
              <div class="product-info">
                <h3 class="product-name"><?= e($p['name']) ?></h3>
                <p class="product-price">P<?= number_format((float)$p['price'], 2) ?></p>

                <form action="add_to_cart.php" method="post">
                  <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                  <input type="hidden" name="id"  value="<?= (int)$p['id'] ?>">
                  <input type="hidden" name="qty" value="1">
                  <button class="add-cart-btn" type="submit">
                    <i class="fa-solid fa-cart-plus"></i> Add to Cart
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php
  }
  ?>

  <?php
    // Render sections in your preferred order
    render_category('Classic Pastries', $groups['classic'], $csrf);
    render_category('Specials',          $groups['special'], $csrf);
    render_category('Drinks',            $groups['drinks'],  $csrf);
    render_category('More Goodies',      $groups['others'],  $csrf);

    // If there are no products at all, show a friendly message
    if (!$rows): ?>
      <section class="products-section">
        <div class="container">
          <div class="products-grid">
            <div style="grid-column:1/-1;background:#fff;border-radius:14px;padding:16px;text-align:center">
              No products yet. <?php if (($me['role'] ?? 'user') === 'admin'): ?>
              <a href="Admin/product_new.php">Add your first product</a>.
              <?php endif; ?>
            </div>
          </div>
        </div>
      </section>
  <?php endif; ?>

</body>
</html>
