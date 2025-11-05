<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $full_name = trim($_POST['fname'] ?? '');
  $username  = trim($_POST['username'] ?? '');
  $address   = trim($_POST['address'] ?? '');
  $email     = trim($_POST['email'] ?? '');
  $password  = $_POST['password'] ?? '';
  $confirm   = $_POST['confirm'] ?? '';

  // Validation
  if ($password !== $confirm) {
    $error = 'Passwords do not match.';
  } elseif (!$full_name || !$username || !$address || !$email || !$password) {
    $error = 'All fields are required.';
  } else {
    // Check for existing username/email
    $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $check->execute([$username, $email]);
    if ($check->fetchColumn() > 0) {
      $error = 'Username or email already exists.';
    } else {
      // ✅ Create new user with full_name and address
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("
        INSERT INTO users (full_name, username, email, address, password_hash, role)
        VALUES (?, ?, ?, ?, ?, 'user')
      ");
      $stmt->execute([$full_name, $username, $email, $address, $hash]);
      $message = 'Account created successfully! You can now log in.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign up for Crust Issues</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .message { color: #0a0; font-weight: 600; margin-bottom: 10px; }
    .error { color: #b00020; font-weight: 600; margin-bottom: 10px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="form-section">
      <div class="title">
        <img src="signup.png" alt="Sign Up Title">
      </div>

      <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
      <?php elseif ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <label for="fname">FULL NAME</label>
        <input type="text" name="fname" id="fname" placeholder="Enter full name" 
               value="<?= htmlspecialchars($_POST['fname'] ?? '') ?>" required>

        <label for="username">USERNAME</label>
        <input type="text" name="username" id="usern" placeholder="Enter username" 
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>

        <label for="address">ADDRESS</label>
        <input type="text" name="address" id="address" placeholder="Enter delivery address" 
               value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" required>

        <label for="email">EMAIL</label>
        <input type="email" name="email" id="email" placeholder="Enter email" 
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

        <label for="password">PASSWORD</label>
        <input type="password" name="password" id="password" placeholder="Enter password" required>

        <label for="confirm">CONFIRM PASSWORD</label>
        <input type="password" name="confirm" id="confirm" placeholder="Confirm password" required>

        <button type="submit" class="btn">REGISTER</button>
      </form>
    </div>

    <!-- Right side -->
    <div class="welcome-section">
      <img src="logopink.png" alt="Logo">
      <h2>Hello There!</h2>
      <p>We offer people best way<br> to eat best pastries.</p>
      <p class="small">Already have an account?</p>
      <a href="crustissueslogin.php" class="btn">SIGN IN</a>

      <div class="social-login">
        <p>Or sign in with</p>
        <div class="icons">
          <a href="https://www.facebook.com/login"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.instagram.com/accounts/login"><i class="fab fa-instagram"></i></a>
          <a href="https://x.com/login"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
