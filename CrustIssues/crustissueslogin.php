<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scaale=1.0">
  <title>Log in to Crust Issues</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" rel="stylesheet">

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="container auth">
    
    <!-- Left side: form -->
    <div class="form-section">
      <!-- Title as image -->
      <div class="title">
        <img src="signin.png" alt="Sign In Title">
      </div>

      <form action="#">
       
        <label for="username">USERNAME</label>
        <input type="username" id="usern" placeholder="Enter username" required>


        <label for="email">EMAIL</label>
        <input type="email" id="email" placeholder="Enter email" required>

        <label for="password">PASSWORD</label>
        <input type="password" id="password" placeholder="Enter password" required>

        <label for="confirm">CONFIRM PASSWORD</label>
        <input type="password" id="confirm" placeholder="Confirm password" required>

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


      <!-- Social login inside right container -->
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