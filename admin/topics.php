<?php

  include '../utils/connect.php';

  $msg = null;
  $title = null;

  if(isset($_POST['submit'])){
    $name = strtolower(trim($_POST['name']));

    if ($name === '') {
    $_SESSION['toast'] = [
      'title' => 'Warning',
      'msg' => 'Topic name cannot be empty.'
    ];
  }else{
    $stmt =$pdo->prepare("SELECT *FROM topic WHERE name = :name");
    $stmt->execute(['name' => $name]);
    $topic = $stmt->fetch();

    if($topic['name']){
       $msg = "Topic already exist.";
      $title = "Warning";
        
    
    }else{
      $stmt = $pdo->prepare("INSERT INTO topic(name) VALUES(?)");
      $stmt->execute([$name]);
      $msg = "New Topic added.";
      $title = "success";
    }
  }
}

$search = $_GET['q'] ?? '';

  if (!empty($search)  && trim($_GET['q']) !== '') {
  $stmt = $pdo->prepare("SELECT *FROM topic WHERE name LIKE :name ORDER BY id");
  $stmt->execute(['name' => "%$search%"]);
} else {
  $stmt = $pdo->query("SELECT *FROM topic ORDER BY id");
}
  $topic = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | Submissions</title>
  <link rel="stylesheet" href="../styles/admin_dash.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <link rel="stylesheet" href="../styles/toast.css">
 <link rel="stylesheet" href="../styles/topic.css">
</head>
<body>
 
    <!--Toast Message-->
<div class="notification"></div>

    <div class="welcome-message">
     <div class="welcome-header">
       <h1>Topics</h1>
      <p class="welcome-text">See how questions are classified</p>
     </div>

      <div class="profile-icon">
        <a href="functions/admin_profile.php">
        <img src="../uploads/no_profile.png" alt="">
        <span class="profile-span" style="color: white;">View profile</span>
        </a>
      </div>
    </div>

    <div class="topic-nav">
     <a href="#add-topic"><button class="add-btn">Add Topic</button></a>

          <div class="search-bar">
              <form method="get">
                <input type="hidden" name="page" value="topics">
                <input type="text" name="q" placeholder="Search topic..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button> 
              </form>
            </div>
    </div>


<!--Topic display-->

<div class="topic-grid">

   <?php foreach($topic as $topic): ?>

  <div class="topic-card">
    <span class="topic-id"> <?= htmlspecialchars($topic['id']) ?> </span>
    <h3 class="topic-name"> <?= ucwords(htmlspecialchars($topic['name'])) ?></h3>
  </div>

    <?php endforeach; ?>
  
</div>

<div class="topic-div-line"></div>

   <!--Add topic section-->

   <section id="add-topic">
    <h1 class="topic-title">Add Topic</h1>
    <form action="" method="post">
    <input type="text" name="name" placeholder="title" class="title-input">
    <input type="submit" name="submit" value="Add" class="title-add-btn">
    </form>
   </section>

   <?php if(isset($msg) && isset($title)): ?>
    <script>
        window.toastMsgData = {
            title: <?= json_encode($title) ?> ,
            msg : <?= json_encode($msg) ?> 
        }
    </script>
    <?php endif; ?>

    <script type="module" src="../scripts/add-user-toast.js"></script>
    <script src="../scripts/search.js"></script>

</body>
</html>

 
    

     