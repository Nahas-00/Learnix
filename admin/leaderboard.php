<?php
  include_once '../utils/connect.php';

      $week_stmt = $pdo->prepare("
        SELECT u.id,u.username,u.profile_pic, COUNT(DISTINCT s.qid) AS solved_count
        FROM submission s
        JOIN users u ON s.uid = u.id
        WHERE s.result = 'Success'
          AND YEARWEEK(s.timestamp, 1) = YEARWEEK(CURDATE(), 1)
        GROUP BY u.id
        ORDER BY solved_count DESC
        LIMIT 10
    ");

    $week_stmt->execute();
    $week_rows = $week_stmt->fetchAll(PDO::FETCH_ASSOC);

    $all_stmt = $pdo->prepare("
        SELECT u.id,u.username,u.profile_pic,COUNT(DISTINCT s.qid) AS solved_count
        FROM submission s
        JOIN users u ON s.uid = u.id
        WHERE s.result = 'Success'
        GROUP BY u.id
        ORDER BY solved_count DESC
        LIMIT 10
    ");


  $all_stmt->execute();
  $all_rows = $all_stmt->fetchAll(PDO::FETCH_ASSOC);

  $streakStmt = $pdo->prepare("
      SELECT COUNT(DISTINCT DATE(timestamp)) AS streak
      FROM submission
      WHERE uid = :uid
        AND result = 'Success'
        AND timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
  ");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leader Board</title>
  <link rel="stylesheet" href="../assets/leaderboard.css">
</head>
<body>
  <div class="container">
      <div class="head">
        <h1>Learnix Cosmic Leaders</h1>
        <p>See who's topping the charts this week. Keep coding, keep climbing!</p>
      </div>
  </div>

  <div class="leaderboard-controls">
    <button class="active toggle-btn" onclick="showBoard('weekly'); setActive(this);">Weekly Champions</button>
    <button class="toggle-btn" onclick="showBoard('alltime'); setActive(this);">All-Time Champions</button>
  </div>

  <div id="weekly-leaderboard" class="weekly">
  <div class="title">
    <div><i class="fa-solid fa-rocket"></i></div>
    <h1>Weekly Top Performers</h1>
  </div>

  <?php if (count($week_rows) > 0): ?>
    <?php foreach ($week_rows as $index => $row): ?>
      <?php
        $streakStmt->execute(['uid' => $row['id']]);
        $streak = $streakStmt->fetchColumn();
        $isCurrentUser =0;
      ?>

      <?php if ($index === 0): ?>
        <!-- First Rank -->
        <div class="first-rank">
          <div class="profile">
            <img src="<?= 
                !empty($row['profile_pic']) && file_exists('../uploads/'.$row['profile_pic']) 
                  ? '../uploads/'.$row['profile_pic'] 
                  : '../uploads/no_profile-user.png' 
            ?>" alt="profile_pic">
          </div>
          <div class="username">
            <?= htmlspecialchars($row['username']) ?> <?= $isCurrentUser ? ' (You)' : '' ?> 
          </div>
          <div class="streak">
            <i class="fas fa-fire fire-gold"></i> Streak: <?= htmlspecialchars($streak) ?> days
          </div>
        </div>
      <?php else: ?>
        <!-- Other Ranks -->
        <div class="coders-list">
          <div class="coder <?= $isCurrentUser ? 'highlight-user' : '' ?>">
            <div class="coder-rank"><?= htmlspecialchars($index + 1) ?></div>
            <img src="<?= 
                !empty($row['profile_pic']) && file_exists('../uploads/'.$row['profile_pic']) 
                  ? '../uploads/'.$row['profile_pic'] 
                  : '../uploads/no_profile-user.png' 
            ?>" alt="profile_pic" class="coder-avatar">
            <div class="coder-info">
              <div class="coder-name">@<?= htmlspecialchars($row['username']) ?> <?= $isCurrentUser ? ' (You)' : '' ?> </div>
              <div class="coder-stats">
                <div class="coder-stat streak"><i class="fas fa-fire"></i> <?= htmlspecialchars($streak) ?> days</div>
              </div>
            </div>
            <div class="medal silver">
              <?php if($index===1): ?>
              <i class="fas fa-medal silver"></i>
              <?php elseif($index==2): ?>
                <i class="fas fa-medal bronze"></i>
                <?php endif;?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endif; ?>
</div>


  <div id="alltime-leaderboard" class="alltime">
    <div class="title">
    <div><i class="fa-solid fa-crown"></i></div>
    <h1>All Time Top Performers</h1>
  </div>

  <?php if (count($all_rows) > 0): ?>
    <?php foreach ($all_rows as $index => $row): ?>
      <?php
        $streakStmt->execute(['uid' => $row['id']]);
        $streak = $streakStmt->fetchColumn();
        $isCurrentUser = 0;
      ?>

      <?php if ($index === 0): ?>
        <!-- First Rank -->
        <div class="first-rank">
          <div class="profile">
            <img src="<?= 
                !empty($row['profile_pic']) && file_exists('../uploads/'.$row['profile_pic']) 
                  ? '../uploads/'.$row['profile_pic'] 
                  : '../uploads/no_profile-user.png' 
            ?>" alt="profile_pic">
          </div>
          <div class="username">
            <?= htmlspecialchars($row['username']) ?> <?= $isCurrentUser ? ' (You)' : '' ?>
          </div>
          <div class="streak">
            <i class="fas fa-fire fire-gold"></i> Streak: <?= htmlspecialchars($streak) ?> days
          </div>
        </div>
      <?php else: ?>
        <!-- Other Ranks -->
        <div class="coders-list">
          <div class="coder <?= $isCurrentUser ? 'highlight-user' : '' ?>">
            <div class="coder-rank"><?= htmlspecialchars($index + 1) ?></div>
            <img src="<?= 
                !empty($row['profile_pic']) && file_exists('../uploads/'.$row['profile_pic']) 
                  ? '../uploads/'.$row['profile_pic'] 
                  : '../uploads/no_profile-user.png' 
            ?>" alt="profile_pic" class="coder-avatar">
            <div class="coder-info">
              <div class="coder-name">@<?= htmlspecialchars($row['username']) ?> <?= $isCurrentUser ? ' (You)' : '' ?> </div>
              <div class="coder-stats">
                <div class="coder-stat streak"><i class="fas fa-fire"></i> <?= htmlspecialchars($streak) ?> days</div>
              </div>
            </div>
            <div class="medal silver"><?php if($index===1): ?>
              <i class="fas fa-medal silver"></i>
              <?php elseif($index==2): ?>
                <i class="fas fa-medal bronze"></i>
                <?php endif;?></div>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endif; ?>
  </div>

  <div class="no-use"></div>

  <script src="../scripts/leaderboard.js"></script>
</body>
</html>