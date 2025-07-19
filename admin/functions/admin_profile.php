<?php
  include_once '../../utils/connect.php';

  session_start();
  $id = $_SESSION['userid'];

  $stmt = $pdo->prepare('SELECT * FROM users where id = ?');
  $stmt->execute([$id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  $stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
  $userCount = $stmt->fetch(PDO::FETCH_ASSOC);

  $stmt = $pdo->query("SELECT COUNT(*) AS total_sub FROM submission");
  $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT COUNT(*) AS total_ques FROM question");
  $question = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../../assets/images/web_icon.png" type="image/png">
  <title>Admin | profile</title>
  <link rel="stylesheet" href="../../styles/profile.css">
</head>
<body>
  <div class="nav">
    <div class="back-btn">
      <a href="../dashboard.php">
        <button>Go Back</button>
      </a>
    </div>
    <div class="logout-btn">
     <a href="../../auth/logout.php">
        <button>Logout</button>
     </a>
    </div>
  </div>

  <div class="profile">
    <img src="<?= 
                !empty($user['profile_pic'])&&file_exists('../../uploads/'.$user['profile_pic'])? '../../uploads/'.$user['profile_pic'] 
                : '../../uploads/no_profile-user.png' ?>" alt="">
                <div class="active"></div>
  </div>

  <div class="email-username">
    <h1><?= htmlspecialchars($user['username']) ?></h1>
    <h2><?= htmlspecialchars($user['email']) ?></h2>
    
  </div>

  <div class="line"></div>

  <div class="info">

        <div class="user">
            <h1><?= htmlspecialchars($userCount['total_users']) ?></h1>
            <p>Total Uses</p>
        </div>

        <div class="question">
             <h1><?= htmlspecialchars($question['total_ques']) ?></h1>
            <p>Total Question</p>
        </div>

        <div class="submission">
             <h1><?= htmlspecialchars($submission['total_sub']) ?></h1>
            <p>Total Submission</p>
        </div>

  </div>


</body>
</html>