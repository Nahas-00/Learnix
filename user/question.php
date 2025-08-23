<?php
    include_once '../utils/connect.php';

  if($_SESSION['logid'] !== 2){
    header('Location: ../../login/login.php');
    exit;
  }

    $id = $_SESSION['userid'];
    $user_stmt = $pdo->prepare('SELECT *FROM users WHERE id = ?');
    $user_stmt->execute([$id]);
    $info = $user_stmt->fetch(PDO::FETCH_ASSOC);

    $top_stmt = $pdo->query('SELECT *FROM topic');
    $top = $top_stmt->fetchAll(PDO::FETCH_ASSOC);

    $search = $_GET['q'] ?? '';

  if(!empty($search) && $search !== ''){
    $stmt = $pdo->prepare("SELECT q.id as ques_id,q.title,q.difficulty,q.solution,q.description,q.hint,t.name,c.name as cat_name FROM question q JOIN topic t ON q.topic_id = t.id JOIN category c ON c.id = q.category_id WHERE q.title LIKE :title ORDER BY q.id DESC");
    $stmt->execute(['title'=>'%'.$search.'%']);
  }else{
    $stmt = $pdo->prepare("SELECT q.id as ques_id,q.title,q.difficulty,q.solution,q.description,q.hint,t.name,c.name as cat_name FROM question q JOIN topic t ON q.topic_id = t.id JOIN category c ON c.id = q.category_id WHERE q.id NOT IN (SELECT qid FROM submission WHERE uid = ? AND result = 'Success') ORDER BY q.id DESC");
    $stmt->execute([$id]);
  }
  $question = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if(isset($_POST['search'])){

      $difficulty = $_POST['difficulty']??'';
      $category = $_POST['category']??'';
      $topic = $_POST['topic']??'';
      $attempt = $_POST['attempt']??'';

      if($difficulty !== '' || !$category !== '' || $topic !== '' || $attempt !== ''){

        $sql = "SELECT q.id as ques_id,q.title,q.difficulty,q.solution,q.description,q.hint,t.name,c.name as cat_name FROM question q JOIN topic t ON q.topic_id = t.id JOIN category c ON c.id = q.category_id WHERE 1=1";

        $conditions = [];
        $params = [];

        if(!empty($difficulty)){
          $conditions[] = "q.difficulty = :difficulty";
          $params[':difficulty']  = $difficulty;
        }

        if(!empty($category)){
          $conditions[] = "c.name = :category";
          $params[':category']  = $category;
        }

        if(!empty($topic)){
          $conditions[] = "t.name = :topic";
          $params[':topic']  = $topic;
        }

        if($attempt === 'attempted'){
          $conditions[] = "q.id IN (SELECT qid FROM submission WHERE uid = :uid)";
          $params[':uid'] = $id;
        } elseif($attempt === 'unattempted' ) { 
          $conditions[] = "q.id NOT IN (SELECT qid FROM submission WHERE uid = :uid)";
          $params[':uid'] = $id;
        }

        if(!empty($conditions)){
          $sql .= " AND " .implode(' AND ',$conditions);
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $question = $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
  }

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
          <option value="">All question</option>
        </select>

       <input type="submit" name="search" value="Apply" class="submit-btn">
      </form>

    </div>

    <div class="search-bar">
              <form method="get">
                <input type="hidden" name="page" value="question">
                <input type="text" name="q" placeholder="Search questions..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button> 
              </form>
            </div>

   
</div>

            <!--Question display section-->

      <div class="disp-ques">
        <?php foreach($question as $index => $q): ?>
          <a href="function/code-area.php?id=<?= $q['ques_id'] ?>" class="ques-link" styl>
          <div class="ques <?= $index % 2 === 0 ? 'even' : 'odd' ?>">
            <div class="id"><?= htmlspecialchars($q['ques_id']) ?></div>
            <div class="title"><?= htmlspecialchars($q['title']) ?></div>
            <div class="topic"><?= htmlspecialchars($q['name']) ?></div>
            <div class="">
             <span class="difficulty-col <?= 'difficult-' . strtolower($q['difficulty']) ?>"> <?= ucfirst($q['difficulty']) ?> </span>
            </div>
          </div>
          </a>
        <?php endforeach; ?>
      </div>


      <script src="../scripts/question.js"></script>

</body>
</html>