<?php
// Go up one folder to reach the includes directory
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($username === '' || $password === '') {
    $error = 'Please enter your username and password.';
  } else {
    // Look up by username only
    $stmt = $pdo->prepare("
      SELECT id, username, email, password_hash, role
      FROM users
      WHERE username = :username
      LIMIT 1
    ");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
      login_user($user);

      // ✅ Redirect to Crust Issues home page
      header('Location: crustissueshome.php');
      exit;
    } else {
      $error = 'Invalid username or password.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log in to Crust Issues</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    .error { color:#b00020; margin-bottom:12px; font-weight:600; }
  </style>
</head>
<body>
  <div class="container auth">

    <!-- Left side: form -->
    <div class="form-section">
      <div class="title">
        <img src="signin.png" alt="Sign In Title">
      </div>

      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <form action="" method="post" novalidate>
        <label for="username">USERNAME</label>
        <input type="text" name="username" id="usern" placeholder="Enter username" 
               value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="password">PASSWORD</label>
        <input type="password" name="password" id="password" placeholder="Enter password" required>

        <button type="submit" class="btn">LOGIN</button>
      </form>
    </div>

    <!-- Right side: welcome text -->
    <div class="welcome-section">
      <img src="logopink.png" alt="Logo">
      <h2>Welcome Back!</h2>
      <p>We offer people best way<br> to eat best pastries.</p>
      <p class="small">Don't have an account yet?</p>
      <a href="crustissuesregistration.php" class="btn">SIGN UP</a>

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
