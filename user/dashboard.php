
<?php
require_once '../auth/auth_validate.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="../assets/images/web_icon.png" type="image/png">
  <link rel="stylesheet" href="../assets/user_dash.css">
  <link rel="stylesheet" href="../styles/toast.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Space+Grotesk:wght@400;600&display=swap">
  <title>User | Dashboard</title>
</head>
<body>
  <div class="left">
      <div class="logo">
          <span class="icon">^_^</span>
          <span class="title">Learnix</span>
      </div>

      <div class="nav-bar">
      
          <a href="?page=home" class="nav-item active">
            <i class="fa-solid fa-gauge-high nav-icon"></i>
            Dashboard
          </a>
          <a href="?page=question" class="nav-item">
            <i class="fa-solid fa-laptop-code nav-icon"></i>
            Questions
          </a>
          <a href="?page=submission" class="nav-item">
            <i class="fa-solid fa-code nav-icon"></i>
            Submissions
          </a>
        
      </div>
  </div>

  <div class="right">

  
  <?php
      if(isset($_GET['page'])){
        $page = $_GET['page'];

        $allowed = ['home', 'question','submission'];

        if(in_array($page,$allowed)&&file_exists($page.".php")){
          if($page === 'add-achievement' ){include 'functions/add-achievement.php';}else{
        include $page.".php";}
        }else{
          echo "<div class=not-found>Error 404 ! <br> Page not found</div>";
        }
      }else{
        include "home.php";
      }
    ?>
   
  </div>


  <script src="../scripts/dashboard.js"></script>
</body>
</html>


