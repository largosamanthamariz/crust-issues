<?php
// Checkout page + handler
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

require_role('user');
$user = current_user();

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['csrf_token'];

$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
  header('Location: crustissuescart.php'); // nothing to checkout
  exit;
}

$err = '';
$msg = '';
$address = $user['address'] ?? '';
$note = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // CSRF
  if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
    $err = 'Invalid request. Please reload the page.';
  } else {
    $address = trim($_POST['address'] ?? '');
    $note    = trim($_POST['note'] ?? '');

    if ($address === '') {
      $err = 'Please provide a delivery address.';
    } else {
      try {
        $pdo->beginTransaction();

        // Lock products and verify stock
        $ids = implode(',', array_map('intval', array_keys($cart)));
        $stmt = $pdo->query("SELECT id, name, price, stock FROM products WHERE id IN ($ids) FOR UPDATE");
        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $products[$row['id']] = $row;
        }

        $subtotal = 0.0;
        foreach ($cart as $pid => $qty) {
          $qty = max(1, (int)$qty);
          if (!isset($products[$pid])) {
            throw new Exception("A product in your cart no longer exists.");
          }
          if (isset($products[$pid]['stock']) && $products[$pid]['stock'] !== null) {
            if ((int)$products[$pid]['stock'] < $qty) {
              throw new Exception("Not enough stock for: " . $products[$pid]['name']);
            }
          }
          $price = (float)$products[$pid]['price'];
          $subtotal += $price * $qty;
        }

        // Optional discount example (set to 0 for now)
        $discount = 0.00;
        $total = max(0, $subtotal - $discount);

        // Insert order
        $insOrder = $pdo->prepare("
          INSERT INTO orders (user_id, total, address, note, created_at)
          VALUES (?, ?, ?, ?, NOW())
        ");
        $insOrder->execute([$user['id'], $total, $address, $note]);
        $orderId = (int)$pdo->lastInsertId();

        // Insert items + decrement stock
        $insItem = $pdo->prepare("
          INSERT INTO order_items (order_id, product_id, quantity, price)
          VALUES (?, ?, ?, ?)
        ");
        $decStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($cart as $pid => $qty) {
          $qty = max(1, (int)$qty);
          $price = (float)$products[$pid]['price'];
          $insItem->execute([$orderId, $pid, $qty, $price]);

          // Decrement stock if the column exists
          if (isset($products[$pid]['stock']) && $products[$pid]['stock'] !== null) {
            $decStock->execute([$qty, $pid]);
          }
        }

        $pdo->commit();

        // Clear cart and go to success page
        unset($_SESSION['cart']);
        header('Location: order_success.php?order_id=' . $orderId);
        exit;

      } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $err = $e->getMessage();
      }
    }
  }
}

// helper
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Checkout - Crust Issues</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .checkout-card{background:#fff;border-radius:18px;box-shadow:0 12px 30px rgba(0,0,0,.08);max-width:720px;margin:24px auto;padding:22px}
    .checkout-title{font-family:'Inter',sans-serif;font-weight:800;margin:0 0 12px}
    .row{display:grid;gap:8px;margin:10px 0}
    label{font-weight:700;font-size:14px;color:#555}
    textarea,input[type=text]{width:100%;border:1.5px solid #e5e7eb;border-radius:12px;padding:10px 12px;font-size:14px;outline:none}
    .actions{display:flex;gap:10px;margin-top:14px}
    .btn-ghost{background:transparent;border:2px solid var(--accent);color:var(--accent);padding:10px 14px;border-radius:12px;font-weight:700;text-decoration:none}
    .btn-ghost:hover{background:var(--accent);color:#fff}
    .btn{background:#000;color:#fff;border:none;padding:10px 16px;border-radius:12px;font-weight:700;cursor:pointer}
    .error{color:#b00020;font-weight:700;margin-bottom:10px}
    .note{font-size:12px;color:#666}
    .summary{background:var(--deep-pink);border-radius:12px;padding:12px;margin-top:10px}
    .summary p{margin:6px 0}
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <section class="container">
    <div class="checkout-card">
      <h1 class="checkout-title">Checkout</h1>
      <?php if ($err): ?><div class="error"><?= e($err) ?></div><?php endif; ?>

      <?php
        // quick mini summary (no DB calls here; read from session + cached prices if needed)
        // For a simple summary now, we’ll show item count and rely on cart page for details.
        $qtyTotal = array_sum(array_map('intval', $cart));
      ?>
      <div class="summary">
        <p><strong>Items:</strong> <?= (int)$qtyTotal ?></p>
        <p class="note">Total will be finalized at submission (verifies stock & prices).</p>
      </div>

      <form method="post">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">

        <div class="row">
          <label for="address">Delivery Address</label>
          <textarea id="address" name="address" rows="3" required><?= e($address) ?></textarea>
        </div>

        <div class="row">
          <label for="note">Order Note <span class="note">(optional)</span></label>
          <input id="note" name="note" type="text" placeholder="e.g. Gate code / leave at door" value="<?= e($note) ?>">
        </div>

        <div class="actions">
          <a class="btn-ghost" href="crustissuescart.php"><i class="fa-solid fa-chevron-left"></i> Back to Cart</a>
          <button class="btn" type="submit">Place Order</button>
        </div>
      </form>
    </div>
  </section>
</body>
</html>
