<?php
// Crust Issues - Profile page with header, cart, and logout
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

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Your global styles -->
  <link rel="stylesheet" href="styles.css">

  <!-- Inline small header/cart styles -->
  <style>
    .cart-link{position:relative;display:inline-flex;align-items:center;gap:8px;text-decoration:none;
      color:#000;font-weight:700}
    .cart-badge{position:absolute;top:-8px;right:-12px;background:var(--accent);color:#fff;
      border-radius:999px;min-width:20px;padding:2px 6px;font-size:12px;line-height:16px;
      display:inline-flex;justify-content:center;font-weight:800}
    .logout-btn{border:1.5px solid #e11d48;color:#e11d48;background:#fff;padding:8px 16px;
      border-radius:12px;font-weight:600;text-decoration:none;display:inline-flex;
      align-items:center;gap:6px;transition:.2s}
    .logout-btn:hover{background:#e11d48;color:#fff;box-shadow:0 0 10px rgba(225,29,72,.3)}
  </style>
</head>
<body>

  <!-- Floating header -->
  <header>
    <nav class="floating-nav container">
      <div class="nav-left">
        <a class="brand-icon" href="crustissueshome.php" title="Home">
          <i class="fa-solid fa-house"></i>
        </a>
        <div class="nav-links">
          <a href="crustissueshome.php">HOME</a>
          <a href="crustissuesabout.php">ABOUT US</a>
          <a href="crustissuesmenu.php">OUR MENU</a>
          <a href="crustissuescontact.php">CONTACT</a>
        </div>
      </div>

      <a class="nav-logo" href="crustissueshome.php" aria-label="Crust Issues">
        <img src="pictures/logo.png" alt="Crust Issues" />
      </a>

      <div class="nav-right" style="gap:14px;">
        <!-- Cart button -->
        <a href="crustissuescart.php" class="icon-btn cart-link">
          <i class="fa-solid fa-cart-shopping"></i> Cart
          <?php if ($cartCount > 0): ?>
            <span class="cart-badge"><?= $cartCount ?></span>
          <?php endif; ?>
        </a>

        <!-- Logout button -->
        <a href="logout.php" class="logout-btn">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
      </div>
    </nav>
  </header>

  <!-- Greeting -->
  <section class="greeting-section">
    <div class="container">
      <h1 class="greeting-text">
        <span class="hi">Hi,</span>
        <span class="name" id="greeting_name"><?= e(($user['full_name'] ?? $user['username']).'!') ?></span>
      </h1>
    </div>
  </section>

  <!-- Profile card -->
  <section class="account-card">
    <!-- My Account -->
    <div class="account-section">
      <div class="section-header">
        <h2 class="section-title">My Account</h2>
      </div>
      <div class="profile-info">
        <img src="pictures/profile.jpg" alt="Profile Picture" class="profile-picture">
        <div class="profile-details">
          <h3 id="card_full_name"><?= e($user['full_name'] ?? $user['username']) ?></h3>
          <p id="card_address"><?= e($user['address'] ?? '—') ?></p>
        </div>
      </div>
    </div>

    <!-- Personal Information -->
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

  <!-- JS for edit modal -->
  <script src="profile-editor.js" defer></script>
</body>
</html>
