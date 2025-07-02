<?php
    include '../auth/validate_sign.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../assets/images/web_icon.png" type="image/png">
  <link rel="stylesheet" href="../styles/login.css">
  <link rel="stylesheet" href="../styles/toast.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <title>Learnix | Sign Up</title>
</head>
<body>

<div class="notification"></div>
  
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
              <input type="text" name="email" id="email" placeholder="Enter your Email" >
          </div>

          <div class="input-group">
              <label for="password">Password</label>
              <input type="password" id="password" name="password" placeholder="Enter your password">
              <i class="bi bi-shield-lock shield-icon" onclick="togglePassword('password', this)"></i>
          </div>

          <div class="input-group">
              <label for="password">Confirm Password</label>
              <input type="password" id="confirmpassword" name="confirmpassword" placeholder="Confirm password">
              <i class="bi bi-shield-lock shield-icon" onclick="togglePassword('confirmpassword', this)"></i>

          </div>

          <button type="submit" name="submit" class="login-btn">Sign Up</button>

          <div class="signup-link">
              Already have an account? <a href="../login/login.php">Log In</a>
          </div>
      </form>

  </div>

  <?php if(isset($msg) || isset($title)): ?>
        <script>
            window.toastMsgData = {
                title: <?= json_encode($title) ?>,
                msg: <?= json_encode($msg) ?> 
            }
        </script>
    <?php endif; ?>
  <script type="module" src="../scripts/sign.js"></script>
  <script src="../scripts/togglePass.js" ></script>
</body>
</html>