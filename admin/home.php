<?php

    include_once '../utils/connect.php';

    $stmt = $pdo->query("SELECT COUNT(*) AS total_rows FROM question");
    $ques = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT COUNT(*) AS total_rows FROM users");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT COUNT(*) AS total_rows FROM submission");
    $sub = $stmt->fetch(PDO::FETCH_ASSOC);

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
        JOIN question q ON q.id = s.qid
        ORDER BY s.timestamp DESC
        LIMIT 3
    ");
$sub_stmt->execute();
$submission_view = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);

  $most_attempted_stmt = $pdo->prepare("
      SELECT 
          q.id AS question_id,
          q.title,
          q.difficulty,
          COUNT(s.id) AS attempt_count
      FROM question q
      LEFT JOIN submission s ON s.qid = q.id
      GROUP BY q.id
      ORDER BY attempt_count DESC
      LIMIT 3
  ");
  $most_attempted_stmt->execute();
  $most_attempted = $most_attempted_stmt->fetchAll(PDO::FETCH_ASSOC);




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="../styles/admin_dash.css">
</head>
<body>
     <div class="welcome-message">
     <div class="welcome-header">
       <h1>Welcome , Admin</h1>
      <p class="welcome-text">Here's what's happening with your platform</p>
     </div>

         <div class="logout-btn">
         <a href="../auth/logout.php"><button type="submit" >Logout</button></a>
      </div>

      <div class="profile-icon">
        <a href="functions/admin_profile.php">
        <img src="../uploads/no_profile.png" alt="">
        <span class="profile-span" style="color: white;">View profile</span>
        </a>
      </div>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="card-title">
                <h3 class="card-heading">Total Users</h3>
                <div class="card-icon">
                    <i class="fa-solid fa-users nav-icon"></i>
                </div>
            </div>
              <div class="card-value"><?= htmlspecialchars($user['total_rows']) ?></div>
        </div>

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
              <div class="card-value"><?= htmlspecialchars($sub['total_rows']) ?></div>
        </div>

    </div>

    <!--Submissions table Section-->


    <div class="table-section">
        <div class="table-header">
          <h1 class="table-title">Recent Submissions</h1>
          <a href="dashboard.php?page=view_submission" class="view-link">View All</a>
        </div>

        <table class="print-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Problem</th>
                    <th>Language</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($submission_view as $submission): ?>
          <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="user-avatar"><img src="../uploads/<?= htmlspecialchars($submission['profile_pic'] ?? 'no_profile.png') ?>" alt=""></div>
                <span><?= htmlspecialchars($submission['username']) ?></span>
              </div>
            </td>
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
            </tbody>
        </table>
    </div>


<!--Questions table Section-->

    <div class="table-section">
        <div class="table-header">
          <h1 class="table-title">Most attempted Questions</h1>
          <a href="dashboard.php?page=question_manage" class="view-link">View All</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Difficulty</th>
                    <th>Attempts</th>
                </tr>
            </thead>

            <tbody>
               <?php foreach ($most_attempted as $q): ?>
          <tr>
            <td><?= htmlspecialchars($q['title']) ?></td>
            <td><span class="problem-tag difficult-<?= strtolower(htmlspecialchars($q['difficulty'])) ?>"><?= ucfirst(htmlspecialchars($q['difficulty'])) ?></span></td>
            <td><?= htmlspecialchars($q['attempt_count']) ?></td>
          </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>


      <div class="not-used"></div>
</body>
</html>