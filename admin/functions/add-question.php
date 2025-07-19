<?php
  include '../../utils//connect.php';
  session_start();

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


    $stmt = $pdo->prepare('INSERT INTO question(title,description,solution,hint,difficulty,topic_id,category_id) VALUES(:title, :description, :solution, :hint, :difficulty, :topic_id, :category_id)');
    $stmt->execute([
      ':title' => $title,
      ':description' => $description,
      ':solution' => $solution,
      ':hint' => $hint,
      ':difficulty' => $difficulty,
      ':topic_id' => $topic,
      ':category_id' => $category
    ]);

    $question_id = $pdo->lastInsertId();

    $testcase_stmt = $pdo->prepare('INSERT INTO testcase(question_id,input,output) VALUES(:question_id, :input, :output)');

    foreach($inputs as $i=>$input){
      $testcase_stmt->execute([
        ':question_id' => $question_id,
        ':input' => $input,
        ':output'=> $outputs[$i]
      ]);
    }

    $_SESSION['toast'] = ['title' => 'Success', 'msg' => 'Question added successfully!'];

    header('location: ../dashboard.php?page=question_manage');
  }
  }
   $stmt = $pdo->query('SELECT * FROM topic');
  $topic = $stmt->fetchAll(PDO::FETCH_ASSOC);

   $stmt = $pdo->query('SELECT * FROM category');
  $category = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../../assets/images/web_icon.png" type="image/png">
  <link rel="stylesheet" href="../../styles/add-ques.css">
  <title>Add | Question</title>
</head>
<body>
    <div class="container">
        <div class="title">
          <h1>Add Question</h1>
        </div>
     <form action="" method="post">
        <div class="ques-header">
          <span>Question Title</span>
          <input type="text" placeholder="Enter Question title" name="title">
        </div>

        <div class="ques-desc">
          <span>Description</span>
          <textarea name="description" id="" placeholder="Enter question description"></textarea>
        </div>

        <div class="ques-desc">
          <span>Solution</span>
          <textarea name="solution" id="" placeholder="Enter question solution"></textarea>
        </div>

         <div class="ques-desc">
          <span>Hint</span>
          <textarea name="hint" id="" placeholder="Enter question hint"></textarea>
        </div>

        <div class="dropdown">
          <div class="ques-category">
            <span>Category</span>
            <select name="category" id="">
              <option value="">Select category</option>
              <?php foreach($category as $ct): ?>
              <option value="<?= htmlspecialchars($ct['id']) ?>"><?= htmlspecialchars($ct['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="ques-difficulty">
            <span>Difficulty</span>
             <select name="difficulty" id="">
              <option value="">Select difficulty</option>
              <option value="easy">Easy</option>
              <option value="medium">Medium</option>
              <option value="hard">Hard</option>
            </select>
          </div>
          <div class="ques-topic">
            <span>Topic</span>
             <select name="topic" id="">
              <option value="">Select topic</option>
              <?php foreach($topic as $tp): ?>
              <option value="<?= htmlspecialchars($tp['id']) ?>"><?= htmlspecialchars($tp['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

          <div class="ques-testcase">
            <span>Test Case</span>
               
            <div class="testcase-container" id="testcase-container">
              <div class="testcase-show">
               <div class="testcase-input">
              <label for="">Input</label>
              <textarea name="input[]" id=""></textarea>
            </div>
             <div class="testcase-output">
              <label for="">output</label>
              <textarea name="output[]" id=""></textarea>
              </div>
            </div>
            </div>
          </div>

        <div class="add-testcase">  <span onclick="addTestCase()">+ Add Testcase</span>  </div> 

        <div class="ques-btn">
          <a href="../dashboard.php?page=question_manage">Cancel</a>
          <button type="submit" name="add">Add +</button>
        </div>

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