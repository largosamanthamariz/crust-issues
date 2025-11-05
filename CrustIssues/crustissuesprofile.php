<?php
// Crust Issues - Profile with Cart + Logout inside card
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

require_role('user');
$userSession = current_user();

// Start session + CSRF
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Fetch user info
$stmt = $pdo->prepare("SELECT id, full_name, username, email, address, phone FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$userSession['id']]);
$user = $stmt->fetch() ?: $userSession;

// Cart count
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $q) $cartCount += (int)$q;
}

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Account - Crust Issues</title>
  <meta name="csrf-token" content="<?= e($csrf) ?>" />

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles.css">

  <style>
    .cart-link{
      background:transparent;border:2px solid var(--accent);color:var(--accent);
      padding:8px 16px;border-radius:12px;font-weight:600;font-size:14px;
      display:flex;align-items:center;gap:6px;transition:.2s;cursor:pointer;text-decoration:none;
    }
    .cart-link:hover{background:var(--accent);color:#fff}
    .cart-badge{
      background:var(--accent);color:#fff;border-radius:999px;
      padding:2px 6px;font-size:12px;font-weight:800;margin-left:4px;
    }
    .logout-btn{
      border:2px solid var(--accent);color:var(--accent);
      padding:8px 16px;border-radius:12px;font-weight:600;
      text-decoration:none;display:flex;align-items:center;gap:6px;
      transition:.2s;background:transparent;cursor:pointer;
    }
    .logout-btn:hover{background:var(--accent);color:#fff}
    .account-header-buttons{
      display:flex;gap:10px;align-items:center;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <section class="greeting-section">
    <div class="container">
      <h1 class="greeting-text">
        <span class="hi">Hi,</span>
        <span class="name" id="greeting_name"><?= e(($user['full_name'] ?? $user['username']).'!') ?></span>
      </h1>
    </div>
  </section>

  <section class="account-card">
    <div class="account-section">
      <div class="section-header">
        <h2 class="section-title">My Account</h2>

        <!-- 🔘 Cart + Logout Buttons inside the white box -->
        <div class="account-header-buttons">
          <a href="crustissuescart.php" class="cart-link">
            <i class="fa-solid fa-cart-shopping"></i>
            Cart
            <?php if ($cartCount > 0): ?>
              <span class="cart-badge"><?= $cartCount ?></span>
            <?php endif; ?>
          </a>
          <a href="logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
          </a>
        </div>
      </div>

      <div class="profile-info">
        <img src="pictures/profile.jpg" alt="Profile Picture" class="profile-picture">
        <div class="profile-details">
          <h3 id="card_full_name"><?= e($user['full_name'] ?? $user['username']) ?></h3>
          <p id="card_address"><?= e($user['address'] ?? '—') ?></p>
        </div>
      </div>
    </div>

    <div class="personal-info">
      <div class="section-header">
        <h2 class="section-title">Personal Information</h2>
        <button class="edit-btn"><i class="fa-solid fa-pencil"></i> Edit</button>
      </div>

      <div class="info-grid">
        <div class="info-item">
          <span class="info-label">Full Name</span>
          <span class="info-value" id="full_name_value"><?= e($user['full_name'] ?? '—') ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Username</span>
          <span class="info-value" id="username_value"><?= e($user['username'] ?? '—') ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Email Address</span>
          <span class="info-value" id="email_value"><?= e($user['email'] ?? '—') ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Phone</span>
          <span class="info-value" id="phone_value"><?= e($user['phone'] ?? '—') ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">Address</span>
          <span class="info-value" id="address_value"><?= e($user['address'] ?? '—') ?></span>
        </div>
      </div>
    </div>
  </section>

  <script src="profile-editor.js" defer></script>
</body>
</html>
