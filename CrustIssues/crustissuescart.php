<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

// Require login; remove this line if cart should be usable without login
require_role('user');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['csrf_token'];

$cart = $_SESSION['cart'] ?? [];
$items = [];
$subtotal = 0.0;

if ($cart) {
  // Build a safe prepared IN(...) list
  $ids = array_map('intval', array_keys($cart));
  $placeholders = implode(',', array_fill(0, count($ids), '?'));

  $stmt = $pdo->prepare("SELECT id, name, price, image FROM products WHERE id IN ($placeholders)");
  $stmt->execute($ids);

  $found = [];
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id  = (int)$row['id'];
    $found[$id] = true;

    $qty = max(0, (int)($cart[$id] ?? 0));
    if ($qty === 0) continue;

    $price = (float)$row['price'];
    $lineTotal = $qty * $price;
    $subtotal += $lineTotal;

    $items[] = [
      'id'    => $id,
      'name'  => $row['name'] ?: ("Product #$id"),
      'price' => $price,
      'qty'   => $qty,
      'line'  => $lineTotal,
      'image' => $row['image'] ?: 'pictures/placeholder.jpg',
    ];
  }

  // If some IDs in session no longer exist in DB, ignore them gracefully
  foreach ($cart as $id => $_qty) {
    if (!isset($found[(int)$id])) {
      // Optionally, unset from cart:
      // unset($_SESSION['cart'][(int)$id]);
    }
  }
}

$discountRate = 0.10; // 10%
$discount = $subtotal * $discountRate;
$total = max(0, $subtotal - $discount);

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Food Cart - Crust Issues</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <style>
    .page-title{font-family:'Inter',sans-serif;font-weight:800;font-size:clamp(28px,5vw,48px);text-align:center;margin:26px 0;color:var(--text)}
    .page-title .accent{color:var(--accent)}
    .cart-wrap{max-width:980px;margin:0 auto 50px;padding:0 10px}
    .cart-card{background:var(--white);border-radius:18px;box-shadow:0 10px 30px rgba(0,0,0,.08);padding:18px 18px 6px}
    .cart-table{width:100%;border-collapse:separate;border-spacing:0 12px}
    .cart-head th{font-size:13px;text-transform:uppercase;color:#666;text-align:left;padding:8px 10px}
    .cart-row{background:var(--deep-pink);border-radius:12px}
    .cart-row td{padding:12px 10px;vertical-align:middle}
    .prod-cell{display:flex;align-items:center;gap:12px;color:#222;font-weight:600}
    .prod-thumb{width:56px;height:56px;border-radius:12px;object-fit:cover;background:#f8f8f8}
    .qtybox{display:inline-flex;align-items:center;gap:6px;background:#fff;border-radius:999px;border:1.5px solid #eee;padding:4px 8px}
    .qtybox form{display:inline}
    .qtybox button{background:#000;color:#fff;border:none;border-radius:999px;width:24px;height:24px;line-height:24px;font-weight:700;cursor:pointer}
    .qtybox input[type="number"]{width:40px;border:none;text-align:center;background:transparent;font-weight:700}
    .remove-btn{background:transparent;border:none;color:#b11;cursor:pointer;font-size:18px}
    .summary{display:grid;grid-template-columns:1fr 1fr;gap:28px;margin-top:26px}
    .summary-card{background:var(--white);border-radius:18px;box-shadow:0 10px 30px rgba(0,0,0,.08);padding:18px}
    .summary-row{display:flex;justify-content:space-between;padding:8px 0}
    .summary-row.total{font-weight:800}
    .checkout{margin-top:10px}
    .empty{background:var(--white);border-radius:18px;box-shadow:0 10px 30px rgba(0,0,0,.08);padding:30px;text-align:center}
    @media (max-width:900px){
      .summary{grid-template-columns:1fr}
      .cart-head{display:none}
      .cart-row td{display:block}
      .prod-cell{margin-bottom:8px}
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container">
    <h1 class="page-title">Your Food <span class="accent">Cart</span></h1>

    <div class="cart-wrap">
      <?php if (!$items): ?>
        <div class="empty">
          <p>Your cart is empty.</p>
          <a href="crustissuesmenu.php" class="btn" style="margin-top:8px;">Browse Menu</a>
        </div>
      <?php else: ?>
        <div class="cart-card">
          <table class="cart-table">
            <thead class="cart-head">
              <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): ?>
              <tr class="cart-row">
                <td>
                  <div class="prod-cell">
                    <img src="<?= e($it['image']) ?>" class="prod-thumb" alt="">
                    <div><?= e($it['name']) ?></div>
                  </div>
                </td>
                <td>
                  <div class="qtybox">
                    <!-- minus -->
                    <form action="update_cart.php" method="post">
                      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                      <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                      <input type="hidden" name="qty" value="<?= max(0, (int)$it['qty'] - 1) ?>">
                      <button type="submit">−</button>
                    </form>

                    <!-- direct qty input -->
                    <form action="update_cart.php" method="post" style="display:inline;">
                      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                      <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                      <input type="number" name="qty" value="<?= (int)$it['qty'] ?>" min="0" onChange="this.form.submit()">
                    </form>

                    <!-- plus -->
                    <form action="update_cart.php" method="post">
                      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                      <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                      <input type="hidden" name="qty" value="<?= (int)$it['qty'] + 1 ?>">
                      <button type="submit">+</button>
                    </form>
                  </div>
                </td>
                <td><strong>P<?= number_format($it['line'], 2) ?></strong></td>
                <td>
                  <form action="remove_from_cart.php" method="post">
                    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                    <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                    <button class="remove-btn" title="Remove" aria-label="Remove">🗑️</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="summary">
          <div class="summary-card">
            <div class="summary-row"><span>Sub Total</span><span>P<?= number_format($subtotal, 2) ?></span></div>
            <div class="summary-row"><span>Discount (10%)</span><span>-P<?= number_format($discount, 2) ?></span></div>
            <div class="summary-row total"><span>Total</span><span>P<?= number_format($total, 2) ?></span></div>

            <!-- Checkout -->
            <form class="checkout" action="crustissuescheckout.php" method="post">
              <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
              <button class="btn" type="submit">Checkout Now</button>
            </form>
          </div>

          <div class="summary-card" style="display:flex;align-items:center;justify-content:center;min-height:160px">
            <div style="text-align:left;font-weight:800;line-height:1.2;">
              <div style="font-size:28px;">SWEETNESS</div>
              <div style="font-size:28px;">DELIVERED IN</div>
              <div style="font-size:28px;">EVERY BOX,</div>
              <div style="font-size:28px;color:var(--accent);">GUARANTEED</div>
              <div style="font-size:28px;">FRESH</div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
