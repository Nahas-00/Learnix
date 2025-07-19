<?php
  include '../../utils//connect.php';
  session_start();

 $qid = $_GET['id'];

  if(isset($_POST['add'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $solution = $_POST['solution'];
    $hint = $_POST['hint'];
    $category = $_POST['category'];
    $topic = $_POST['topic'];
    $difficulty = $_POST['difficulty'];
    $inputs = $_POST['input'];   
    $outputs = $_POST['output'];

    if ($title === '' || $description === '' || $solution === '' || $category === '' || $topic === '' || $difficulty === '') {
    echo "<script>alert('Please fill all required fields.');</script>";
   
  }else{


    $stmt = $pdo->prepare('UPDATE question SET title= :title, description= :description, solution= :solution, hint= :hint, difficulty= :difficulty, topic_id= :topic_id, category_id= :category_id WHERE id = :id');
    $stmt->execute([
      ':title' => $title,
      ':description' => $description,
      ':solution' => $solution,
      ':hint' => $hint,
      ':difficulty' => $difficulty,
      ':topic_id' => $topic,
      ':category_id' => $category,
      ':id' => $qid
    ]);
    $question_id = $pdo->lastInsertId();

    $testcase_stmt = $pdo->prepare('UPDATE testcase SET input= :input , output= :output WHERE question_id= :ques_id');

    foreach($inputs as $i=>$input){
      $testcase_stmt->execute([
        ':ques_id' => $qid,
        ':input' => $input,
        ':output'=> $outputs[$i]
      ]);
    }

    $_SESSION['toast'] = ['title' => 'Success', 'msg' => 'Question updated successfully!'];

    header('location: ../dashboard.php?page=question_manage');
  }
  }
   $stmt = $pdo->query('SELECT * FROM topic');
  $topic = $stmt->fetchAll(PDO::FETCH_ASSOC);

   $stmt = $pdo->query('SELECT * FROM category');
  $category = $stmt->fetchAll(PDO::FETCH_ASSOC);

 

  $val_stmt = $pdo->prepare('SELECT *FROM question where id = ?');
  $val_stmt->execute([$qid]);
  $quesVal = $val_stmt->fetchAll(PDO::FETCH_ASSOC);

  $test_stmt = $pdo->prepare("SELECT * FROM testcase WHERE question_id = ?");
  $test_stmt->execute([$qid]);
  $testcases = $test_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../../assets/images/web_icon.png" type="image/png">
  <link rel="stylesheet" href="../../styles/add-ques.css">
  <title>Edit | Question</title>
</head>
<body>
    <div class="container">
        <div class="title">
          <h1>Edit Question</h1>
        </div>
     <form action="" method="post">

      <?php foreach($quesVal as $q): ?>

        <div class="ques-header">
          <span>Question Title</span>
          <input type="text" placeholder="Enter Question title" name="title" value="<?= htmlspecialchars($q['title']) ?>">
        </div>

        <div class="ques-desc">
          <span>Description</span>
          <textarea name="description" id="" placeholder="Enter question description">
            <?= htmlspecialchars($q['description']) ?>
          </textarea>
        </div>

        <div class="ques-desc">
          <span>Solution</span>
          <textarea name="solution" id="" placeholder="Enter question solution">
            <?= htmlspecialchars($q['solution']) ?>
          </textarea>
        </div>

         <div class="ques-desc">
          <span>Hint</span>
          <textarea name="hint" id="" placeholder="Enter question hint">
            <?= htmlspecialchars($q['hint']) ?>
          </textarea>
        </div>

        <div class="dropdown">
          <div class="ques-category">
            <span>Category</span>
            <select name="category" id="">
              <option value="<?= htmlspecialchars($q['category_id']) ?>">
                <?php
                 $stmt = $pdo->prepare('SELECT name FROM category where id = ?');
                 $stmt->execute([$q['category_id']]);
                $catSelect = $stmt->fetchColumn();
                echo $catSelect;
              ?></option>
              <?php foreach($category as $ct): ?>
              <option value="<?= htmlspecialchars($ct['id']) ?>"><?= htmlspecialchars($ct['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="ques-difficulty">
            <span>Difficulty</span>
             <select name="difficulty" id="">
              <option value="<?= htmlspecialchars($q['difficulty']) ?>"><?= htmlspecialchars($q['difficulty']) ?></option>
              <option value="easy">Easy</option>
              <option value="medium">Medium</option>
              <option value="hard">Hard</option>
            </select>
          </div>
          <div class="ques-topic">
            <span>Topic</span>
             <select name="topic" id="">
              <option value="<?= htmlspecialchars($q['topic_id']) ?>">
                <?php
                 $stmt = $pdo->prepare('SELECT * FROM topic where id = ?');
                 $stmt->execute([$q['topic_id']]);
                $cat = $stmt->fetch(PDO::FETCH_ASSOC);
                echo $cat['name'];
              ?>
              </option>
              <?php foreach($topic as $tp): ?>
              <option value="<?= htmlspecialchars($tp['id']) ?>"><?= htmlspecialchars($tp['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

          <div class="ques-testcase">
            <span>Test Case</span>
               
            <div class="testcase-container" id="testcase-container">
          <?php if (!empty($testcases)): ?>
            <?php foreach ($testcases as $tc): ?>
              <div class="testcase-show">
                <div class="testcase-input">
                  <label>Input</label>
                  <textarea name="input[]"><?= htmlspecialchars($tc['input']) ?></textarea>
                </div>
                <div class="testcase-output">
                  <label>Output</label>
                  <textarea name="output[]"><?= htmlspecialchars($tc['output']) ?></textarea>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
           
            <div class="testcase-show">
              <div class="testcase-input">
                <label>Input</label>
                <textarea name="input[]"></textarea>
              </div>
              <div class="testcase-output">
                <label>Output</label>
                <textarea name="output[]"></textarea>
              </div>
            </div>
          <?php endif; ?>
        </div>


        <div class="add-testcase">  <span onclick="addTestCase()">+ Add Testcase</span>  </div> 

        <div class="ques-btn">
          <a href="../dashboard.php?page=question_manage">Cancel</a>
          <button type="submit" name="add">Update</button>
        </div>

        <?php endforeach; ?>
        </form>

    </div>


<script>
  function addTestCase() {
    const container = document.getElementById('testcase-container');

    const testcase = document.createElement('div');
    testcase.className = 'testcase-show';
    testcase.innerHTML = `
      <div class="testcase-input">
        <label>Input</label>
        <textarea name="input[]"></textarea>
      </div>
      <div class="testcase-output">
        <label>Output</label>
        <textarea name="output[]"></textarea>
      </div>
    `;

    container.appendChild(testcase);
  }
</script>

<script type="module" src="../../scripts/add-user-toast.js"></script>

</body>
</html>