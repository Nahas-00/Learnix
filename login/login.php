<?php
    include '../auth/validate_login.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../assets/images/web_icon.png" type="image/png">
  <link rel="stylesheet" href="../styles/login.css">
  <link rel="stylesheet" href="../styles/toast.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <title>Learnix | Login</title>
</head>
<body>

    <div class="notification">

    </div>
  
  <div class="login-container">
      <div class="logo">
          <h1>Learnix</h1>
          <p>Master coding through challenges</p>
      </div>

      <form action="" method="post">
          <div class="input-group">
              <label for="email">Email</label>
              <input type="email" name="email" id="email" placeholder="Enter your Email" required>
          </div>

          <div class="input-group">
              <label for="password">Password</label>
              <input type="password" id="password" name="pass" placeholder="Enter your password" required>
              <i class="bi bi-shield-lock shield-icon" onclick="togglePassword()"></i>
          </div>

          <button type="submit" name="submit" class="login-btn">Log In</button>

          <div class="signup-link">
              Don't have an account? <a href="../sign-up/signup.php">Sign up</a>
          </div>
      </form>
    
  </div>

<!--passing php variable to js-->

    <?php if(isset($msg) && isset($title)): ?>
    <script>
        window.toastMsgData = {
            title: <?= json_encode($title) ?> ,
            msg : <?= json_encode($msg) ?> ,
            role: <?= json_encode($role)?>
        }
    </script>
    <?php endif; ?>

  <script src="../scripts/togglePass.js" type="module"></script>
  <script src="../scripts/login.js" type="module"></script>
</body>
</html>