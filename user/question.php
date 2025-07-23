<?php
    include_once '../utils/connect.php';
    $id = $_SESSION['userid'];
    $user_stmt = $pdo->prepare('SELECT *FROM users WHERE id = ?');
    $user_stmt->execute([$id]);
    $info = $user_stmt->fetch(PDO::FETCH_ASSOC);

    $top_stmt = $pdo->query('SELECT *FROM topic');
    $top = $top_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | Submissions</title>
  <link rel="stylesheet" href="../assets/admin_dash.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <link rel="stylesheet" href="../assets/question.css">
</head>
<body>
 
    <!--Toast Message-->
<div class="notification"></div>

    <div class="welcome-message">
     <div class="welcome-header">
       <h1>Questions</h1>
      <p class="welcome-text">See the question that we have</p>
     </div>

      <div class="profile-icon">
        <a href="function/profile.php">
        <img src="
         <?= 
            !empty($info['profile_pic'])&&file_exists('../uploads/'.$info['profile_pic'])? '../uploads/'.$info['profile_pic'] 
            : '../uploads/no_profile-user.png' ?>
        " alt="">
        <span class="profile-span" style="color: white;">View profile</span>
        </a>
      </div>
    </div>

    <!--Filter-->

    <div class="filter-title">
      <h1>Filters</h1>
    </div>
<div class="filterandadd">
    <div class="filters">
      <form action="" method="post">
        <select name="difficulty" id="">
          <option value="">All Difficulty</option>
          <option value="easy">Easy</option>
          <option value="medium">Medium</option>
          <option value="Hard">Hard</option>
        </select>

        <select name="category" id="" class="result">
          <option value="">All Category</option>
          <option value="Dsa">Dsa</option>
          <option value="Non-Dsa">Non-Dsa</option>
        </select>

        <select name="topic" id="" class="result">
            <option value="">All Topics</option>
            <?php foreach($top as $t): ?>
            <option value="<?= htmlspecialchars($t['name']) ?>"><?= htmlspecialchars($t['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="attempt" id="" class="result">
          <option value="unattempted">Unattempted</option>
          <option value="attempted">Attempted</option>
          <option value="all">All question</option>
        </select>

       <input type="submit" name="search" value="Apply" class="submit-btn">
      </form>
    </div>

   
</div>

</body>
</html>