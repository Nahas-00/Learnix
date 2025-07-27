<?php
  include_once '../../utils/connect.php';

  $qid = $_GET['id'] ?? null;

  if ($qid) {
     $stmt = $pdo->prepare("SELECT q.id as ques_id,q.title,q.difficulty,q.solution,q.description,q.hint,t.name,c.name as cat_name FROM question q JOIN topic t ON q.topic_id = t.id JOIN category c ON c.id = q.category_id WHERE q.id = ?");
     $stmt->execute([$qid]);
     $question = $stmt->fetch(PDO::FETCH_ASSOC);

     $test_stmt = $pdo->prepare("SELECT * FROM testcase WHERE question_id = ?");
     $test_stmt->execute([$qid]);
     $testcases = $test_stmt->fetchAll(PDO::FETCH_ASSOC);
  } else {
    echo "Invalid question ID.";
    exit;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/images/web_icon.png" type="image/png">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Space+Grotesk:wght@400;600&display=swap">
    
    <link rel="stylesheet" href="../../assets/codearea.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css">
  <title>Editor | Learnix</title>
</head>
<body>

<header>
  <div class="logo">
    <span>^_^</span>
    <h1>Learnix</h1>
  </div>

  <div class="controls">
    <button id="back-btn">
      <a href="../dashboard.php?page=question" style="text-decoration: none; color:white;">
      <i class="bi bi-box-arrow-left"></i> Go Back
      </a>
    </button>
    <button id="run-btn">
      <i class="fas fa-play"></i> Run
    </button>
    <button id="clear-btn">
      <i class="fas fa-broom"></i> Clear
    </button>
    <button id="submit-btn">
      <i class="fa-solid fa-upload"></i> Submit
    </button>

  </div>
</header>

<div class="container">
  <div class="question-container">
    <div class="panel-header">
      <div class="panel-title">
        <i class="fas fa-book-open"></i>
        <span>Problem Statement</span>
      </div>
      <div class="hint-sol-btn">
        <button class="sol-hint-btn">Hint</button>
        <button class="sol-hint-btn">Solution</button>
      </div>
    </div>

    <div class="question-content">
       <div class="ques-title-diff"> <h2><?= ucwords(htmlspecialchars($question['title'])) ?></h2> <span class="difficulty difficult-<?= strtolower(htmlspecialchars($question['difficulty'])) ?>"><?= htmlspecialchars($question['difficulty']) ?></span> </div>
       <br>
        <p><pre style="background-color: transparent;">
          <?= htmlspecialchars($question['description']) ?>.
          </pre>
        </p>
        <br>
        
        <h3>Testcase:</h3>
        <?php foreach($testcases as $tc): ?>
        <pre>Input: <br>  <?= htmlspecialchars($tc['input']) ?> <br>
Output: <?= htmlspecialchars($tc['output']) ?>
      </pre>
          <?php endforeach; ?>
       
    </div>
  </div>

  <div class="right-pane">
    <div class="editor-container">
      <div class="panel-header">
        <div class="panel-title">
          <i class="fas fa-file-code"></i>
          <span>Editor</span>
        </div>
        <select name="" id="language-selector" class="language-selector">
          <option value="54">C++</option>
          <option value="50">C</option>
          <option value="71">Python</option>
          <option value="62">Java</option>
          <option value="60">Golang</option>
          <option value="72">Ruby</option>
        </select>
      </div>
      <textarea name="" id="editor">
        #include <iostream>
        using namespace std;
          int main() {
              cout << "Hello, World!";
              return 0;
          }
      </textarea>
    </div>

    <div class="io-container">
      <div class="io-tabs">
        <button class="tab-button active" data-tab="testcase">
          <i class="fas fa-keyboard"></i> Testcase
        </button>
        <button class="tab-button" data-tab="output">
          <i class="fas fa-terminal"></i> Output
        </button>
      </div>
      <div class="io-content-wrapper">
        <div id="testcase-panel" class="tab-panel active">
         <div class="test-input">Input <br> <?= $testcases[0]['input'] ?> </div>
         <div class="test-output"> Output <br> <?= $testcases[0]['output'] ?>  </div>
         <div class="testcase-status"></div>
        </div>
        <div id="output-panel" class="tab-panel">
            <div id="output-content" class="output-content">
              // Your output will appear here
          </div>
        </div>
      </div>

       <div class="status-bar">
        <div class="status-item">
            <i class="fas fa-microchip"></i>
            <span id="status-memory">Memory: -</span>
        </div>
        <div class="status-item">
            <i class="fas fa-stopwatch"></i>
            <span id="status-time">Time: -</span>
        </div>
        <div class="status-item">
            <i class="fas fa-info-circle"></i>
            <span id="status-exit">Exit Code: -</span>
        </div>
        <div id="loader" class="loader"></div>
      </div>

    </div>
  </div>
</div>
  
 <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/clike/clike.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/python/python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/ruby/ruby.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/matchbrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closebrackets.min.js"></script>

    <script src="../../scripts/codearea.js"></script>
</body>
</html>
