<?php
require __DIR__ . '/../includes/auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$orderId = (int)($_GET['order_id'] ?? 0);

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Order Placed - Crust Issues</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .card{background:#fff;border-radius:18px;box-shadow:0 12px 30px rgba(0,0,0,.08);max-width:620px;margin:24px auto;padding:26px;text-align:center}
    .card h1{font-family:'Inter',sans-serif;font-weight:800;margin:0 0 10px}
    .card p{margin:8px 0;color:#555}
    .btn{display:inline-block;margin-top:14px;background:#000;color:#fff;padding:10px 16px;border-radius:12px;text-decoration:none;font-weight:700}
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <section class="container">
    <div class="card">
      <h1>Thank you! 🎉</h1>
      <p>Your order <?= $orderId ? ' #' . e($orderId) : '' ?> has been placed successfully.</p>
      <p>We’re getting it ready. You’ll receive updates soon.</p>
      <a class="btn" href="crustissueshome.php">Back to Home</a>
    </div>
  </section>
</body>
</html>
