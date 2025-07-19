<?php 
  
  include '../utils/connect.php';

  $showAddUser = false;
  $msg = null;
  $title = null;

if (isset($_SESSION['toast'])) {
  $msg = $_SESSION['toast']['msg'];
  $title = $_SESSION['toast']['title'];
  unset($_SESSION['toast']);
}

  $search = $_GET['q'] ?? '';

  if(!empty($search) && $search !== ''){
    $stmt = $pdo->prepare("SELECT q.id as ques_id,q.title,q.difficulty,q.solution,q.description,q.hint,t.name,c.name as cat_name FROM question q JOIN topic t ON q.topic_id = t.id JOIN category c ON c.id = q.category_id WHERE q.title LIKE :title OR t.name LIKE :topic ORDER BY q.id DESC");
    $stmt->execute(['title'=>'%'.$search.'%',
    'topic'=>'%'.$search.'%']);
  }else{
    $stmt = $pdo->query("SELECT q.id as ques_id,q.title,q.difficulty,q.solution,q.description,q.hint,t.name,c.name as cat_name FROM question q JOIN topic t ON q.topic_id = t.id JOIN category c ON c.id = q.category_id ORDER BY q.id DESC");
  }
  $question = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if(isset($_POST['search'])){

      $difficulty = $_POST['difficulty']??'';
      $category = $_POST['category']??'';

      if($difficulty !== '' || !$category !== ''){

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
  <title>Admin | Question</title>
  <link rel="stylesheet" href="../styles/admin_dash.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <link rel="stylesheet" href="../styles/toast.css">
  <link rel="stylesheet" href="../styles/add-user.css">
  <link rel="stylesheet" href="../styles/question.css">
  <link rel="stylesheet" href="../styles/showques.css">
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

       <input type="submit" name="search" value="Apply" class="submit-btn">
      </form>
    </div>

    <a href="functions/add-question.php" style="text-decoration: none; color:white;"><div class="add-ques-btn"><i class="fa-solid fa-plus"></i></div></a>
</div>


<!--Question dsiplay-->

<div class="table-section">
        <div class="table-header">
          <h1 class="table-title">Questions</h1>

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
            <td class="fixed-title"><?= htmlspecialchars($q['title']) ?></td>           
            <td><?= htmlspecialchars($q['name']) ?></td>
            <td ><span class="difficulty-col difficult-<?= strtolower(htmlspecialchars($q['difficulty']))?>"> <?= htmlspecialchars($q['difficulty']) ?></span></td>
            <td><?= htmlspecialchars($q['cat_name']) ?></td>

            <td> <a href="functions/edit-question.php?id=<?= htmlspecialchars($q['ques_id']) ?>"><button class="edit-btn" type="button">
              <i class="fa-solid fa-pen-to-square"></i>
              </button></a>

          <form method="post" action="functions/delete_ques.php" style="display:inline;">
            <input type="hidden" name="ques_id" value="<?= htmlspecialchars($q['ques_id'])?>">
            <button type="submit" class="delete-btn" name="ques-delete-btn" onclick="return confirm('Delete this question?');">
              <i class="fa-solid fa-trash"></i>
            </button>
          </form>

          <button onclick="showCode(this)" class="ques-view-btn" data-title="<?= htmlspecialchars($q['title']) ?>"
          data-name="<?= htmlspecialchars($q['name']) ?>" data-difficulty="<?= htmlspecialchars($q['difficulty']) ?>"
          data-category="<?= htmlspecialchars($q['cat_name']) ?>"
          data-solution="<?= htmlspecialchars($q['solution']) ?>"
          data-hint="<?= htmlspecialchars($q['hint']) ?>"
          data-description="<?= htmlspecialchars($q['description']) ?>"
         data-id="<?= htmlspecialchars($q['ques_id']) ?>"
          >View</button>
        </td>
          </tr>
           <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="code-overlay" id="code-overlay"></div>
        <div class="code-dis" id="code-dis">
        <div class="code-close-btn"><button onclick="closeCode()"><i class="fa-solid fa-xmark"></i></button></div>
        <div class="ques-display-info">
        <div class="ques-title" id="ques-title"></div>
        <div class="ques-info">
          <span class="ques-cat" id="ques-cat"></span>
          <span class="ques-cat" id="ques-topic"></span>
          <span class="ques-diff" id="ques-diff"></span>
        </div>
        <div class="ques-line"></div>
        <div class="ques-description ques-text-style">
        <span >Description</span>  
        <p id="ques-des"> </p>
      </div>
        <div class="ques-solution ques-text-style">
        <span id="">Solution</span>  
       <pre><p id="ques-solution"> 

       </p></pre></div>
        <div class="ques-hint ques-text-style"> <span>Hint</span>
        <p class="ques-hint" id="ques-hint"> </p></div>
        <div class="ques-testcase ques-text-style"> 
        <span>Test Case</span>  
        <pre><p> </p></pre> </div>
        </div>
    </div>

    <div class="no-use"></div>

     <?php if(isset($msg) && isset($title)): ?>
    <script>
        window.toastMsgData = {
            title: <?= json_encode($title) ?> ,
            msg : <?= json_encode($msg) ?> 
        }
    </script>
    <?php endif; ?>

    <script src="../scripts/question.js"></script>
    <script src="../scripts/showQuestion.js"></script>
    <script type="module" src="../scripts/add-user-toast.js"></script>

</body>
</html>
