<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../assets/images/web_icon.png" type="image/png">
  <link rel="stylesheet" href="../styles/login.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <title>Learnix | Sign Up</title>
</head>
<body>
  
  <div class="login-container">
      <div class="logo">
          <h1>Learnix</h1>
          <p>Master coding through challenges</p>
      </div>

      <form action="" method="post">

          <div class="input-group">
              <label for="username">Username</label>
              <input type="text" name="username" id="username" placeholder="Enter your username" required>
          </div>

          <div class="input-group">
              <label for="email">Email</label>
              <input type="email" name="email" id="email" placeholder="Enter your Email" required>
          </div>

          <div class="input-group">
              <label for="password">Password</label>
              <input type="password" id="password" name="pass" placeholder="Enter your password" required>
              <i class="bi bi-shield-lock shield-icon" onclick="togglePassword()"></i>
          </div>

          <div class="input-group">
              <label for="password">Confirm Password</label>
              <input type="password" id="password" name="confirmpass" placeholder="Confirm password" required>
              <i class="bi bi-shield-lock shield-icon" onclick="togglePassword()"></i>
          </div>

          <button type="submit" class="login-btn">Log In</button>

          <div class="signup-link">
              Already have an account? <a href="../login/login.php">Log In</a>
          </div>
      </form>

  </div>

  <script src="../scripts/togglePass.js"></script>
</body>
</html>