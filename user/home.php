<?php
  include_once '../utils/connect.php';
  $id = $_SESSION['userid'];

  $stmt = $pdo->prepare('SELECT *FROM users where id = ?');
  $stmt->execute([$id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT COUNT(*) AS total_rows FROM question");
    $ques = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_rows FROM submission WHERE uid = ?");
    $stmt->execute([$id]);
    $subm = $stmt->fetch(PDO::FETCH_ASSOC);

    //recent submissions

     $sub_stmt = $pdo->prepare("
        SELECT 
        s.id as sub_id,
        s.code,
        s.language,
        s.result,
        s.timestamp,
        u.username,
        u.profile_pic,
        q.title AS question_title
        FROM submission s
        JOIN users u ON u.id = s.uid
        JOIN question q ON q.id = s.qid WHERE s.uid = ?
        ORDER BY s.timestamp DESC
        LIMIT 3
    ");
$sub_stmt->execute([$id]);
$submission_view = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);

$most_attempted_stmt = $pdo->prepare("
    SELECT 
        q.id AS question_id,
        q.title,
        q.difficulty,
        t.name
    FROM question q 
    JOIN topic t ON q.topic_id = t.id
    WHERE q.id NOT IN (
        SELECT qid FROM submission WHERE uid = :userid
    )
    LIMIT 3
");
$most_attempted_stmt->execute(['userid' => $id]);
$most_attempted = $most_attempted_stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="../assets/admin_dash.css">
</head>
<body>
     <div class="welcome-message">
     <div class="welcome-header">
       <h1>Welcome , <?= htmlspecialchars(ucfirst($_SESSION['username'])) ?></h1>
      <p class="welcome-text">Let your Setbacks be Setups for your Comeback</p>
     </div>

         <div class="logout-btn">
         <a href="../auth/logout.php"><button type="submit" >Logout</button></a>
      </div>

      <div class="profile-icon">
        <a href="function/profile.php">
        <img src="
          <?= 
            !empty($user['profile_pic'])&&file_exists('../uploads/'.$user['profile_pic'])? '../uploads/'.$user['profile_pic'] 
            : '../uploads/no_profile-user.png' ?>
        " alt="profile_pic">
        <span class="profile-span" style="color: white;">View profile</span>
        </a>
      </div>
    </div>

    <div class="dashboard-grid">
         <div class="card">
            <div class="card-title">
                <h3 class="card-heading">Total Questions</h3>
                <div class="card-icon">
                    <i class="fa-solid fa-laptop-code nav-icon"></i>
                </div>
            </div>
              <div class="card-value"><?= htmlspecialchars($ques['total_rows']) ?></div>
        </div>

         <div class="card">
            <div class="card-title">
                <h3 class="card-heading">Total Submissions</h3>
                <div class="card-icon">
                    <i class="fa-solid fa-code nav-icon"></i>
                </div>
            </div>
              <div class="card-value"><?= htmlspecialchars($subm['total_rows']) ?></div>
        </div>

    </div>

    <!--Submissions table Section-->


    <div class="table-section">
        <div class="table-header">
          <h1 class="table-title">Recent Submissions</h1>
          <a href="dashboard.php?page=submission" class="view-link">View All</a>
        </div>

        <table class="print-table">
            <thead>
                <tr>
                    <th>Problem</th>
                    <th>Language</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>

             <?php if (empty($submission_view)): ?>
                  <tr>
                    <td colspan="4" style="text-align: center; padding: 1rem; color: #aaa; font-size: 1.2rem; font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif">
                      No submissions have made.
                    </td>
                  </tr>
              <?php else: ?>
            <?php foreach ($submission_view as $submission): ?>
          <tr>
          
            <td><?= htmlspecialchars($submission['question_title']) ?></td>
            <td><?= htmlspecialchars($submission['language']) ?></td>
            <td>
              <?php if (strtolower($submission['result']) === 'success'): ?>
                <span class="status status-success"><?= htmlspecialchars($submission['result']) ?></span>
              <?php else: ?>
                <span class="status status-failed"><?= htmlspecialchars($submission['result']) ?></span>
              <?php endif; ?>
            </td>
            <td><?= date('Y-m-d', strtotime($submission['timestamp'])) ?></td>

          </tr>

              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
        </table>
    </div>


<!--Questions table Section-->

    <div class="table-section">
        <div class="table-header">
          <h1 class="table-title">Continue Learning</h1>
          <a href="dashboard.php?page=question" class="view-link">View All</a>
        </div>

        <table class="ques-table">
            <thead>
              
            </thead>

            <tbody>
               <?php foreach ($most_attempted as $q): ?>
          <tr>
            <td><?= htmlspecialchars($q['question_id']) ?></td>
            <td><?= htmlspecialchars($q['title']) ?></td>
            <td><span class="problem-tag difficulty-col difficult-<?= strtolower(htmlspecialchars($q['difficulty']))?>"><?= ucfirst(htmlspecialchars($q['difficulty'])) ?></span></td>
            <td><?= htmlspecialchars(ucfirst($q['name'])) ?></td>
          </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>


      <div class="not-used"></div>
</body>
</html>