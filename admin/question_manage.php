<?php 
  
  include '../utils/connect.php';

  $search = $_GET['q'] ?? '';

  if(!empty($search) && $search !== ''){
    $stmt = $pdo->prepare("SELECT q.id as ques_id,q.title,q.difficulty,t.name,c.name as cat_name FROM question q JOIN topic t ON q.topic_id = t.id JOIN category c ON c.id = q.category_id WHERE q.title LIKE :title OR t.topic LIKE :topic ORDER BY q.id DESC");
    $stmt->execute(['title'=>'%'.$search.'%',
    'topic'=>'%'.$search.'%']);
  }else{
    $stmt = $pdo->prepare("SELECT q.id as ques_id,q.title,q.difficulty,t.name,c.name as cat_name FROM question q JOIN topic t ON q.topic_id = t.id JOIN category c ON c.id = q.category_id ORDER BY q.id DESC");
    $stmt->execute();
  }
  $question = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | Question</title>
  <link rel="stylesheet" href="../styles/admin_dash.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <link rel="stylesheet" href="../styles/toast.css">
  <link rel="stylesheet" href="../styles/add-user.css">
  <link rel="stylesheet" href="../styles/question.css">
</head>
<body>
  
  <div class="notification"></div>

    <div class="welcome-message">
     <div class="welcome-header">
       <h1>User's Submissions</h1>
      <p class="welcome-text">See all or specific submissions</p>
     </div>

      <div class="profile-icon">
        <img src="../uploads/no_profile.png" alt="">
        <span class="profile-span">View profile</span>
      </div>
    </div>

<!--Question dsiplay-->

<div class="table-section">
        <div class="table-header">
          <h1 class="table-title">Recent Submissions</h1>

            <div class="search-bar">
              <form method="get">
                <input type="hidden" name="page" value="question_manage">
                <input type="text" name="q" placeholder="Search title or topic..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button> 
              </form>
            </div>

           
        </div>

        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>title</th>
                    <th>Topic</th>
                    <th>Difficulty</th>
                    <th>Category</th>
                    <th>Action</th>
                    
                </tr>
            </thead>

            <tbody>
               <?php foreach($question as $q): ?>
          <tr>

            <td><?= htmlspecialchars($q['ques_id']) ?> </td>
            <td><?= htmlspecialchars($q['title']) ?></td>           
            <td><?= htmlspecialchars($q['name']) ?></td>
            <td ><span class="difficulty-col difficult-<?= strtolower(htmlspecialchars($q['difficulty']))?>"> <?= htmlspecialchars($q['difficulty']) ?></span></td>
            <td><?= htmlspecialchars($q['cat_name']) ?></td>
            <td><?= htmlspecialchars($q['cat_name']) ?></td>
          </tr>
           <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="../scripts/question.js"></script>

</body>
</html>