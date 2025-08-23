<?php
  include_once '../../utils/connect.php';
  include 'assign_achievement.php';

  if($_SESSION['logid'] !== 2){
    header('Location: ../../login/login.php');
    exit;
  }


  $uid = $_SESSION['userid'];
  $user_stmt = $pdo->prepare("SELECT * FROM users WHERE id= :id");
  $user_stmt->execute(['id'=>$uid]);
  $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

  //level detector
  // total points for a user
  $stmt = $pdo->prepare("
      SELECT 
          SUM(
              CASE 
                  WHEN q.difficulty = 'Easy' THEN 10
                  WHEN q.difficulty = 'Medium' THEN 20
                  WHEN q.difficulty = 'Hard' THEN 40
              END
          ) AS problem_points
      FROM submission s
      JOIN question q ON s.qid = q.id
      WHERE s.uid = :uid AND s.result = 'Success'
  ");
  $stmt->execute(['uid' => $uid]);
  $points = $stmt->fetchColumn();

  // now assign level
  if ($points < 160) {
      $level = "Beginner";
  } elseif ($points < 380) {
      $level = "Intermediate";
  } elseif ($points < 610) {
      $level = "Expert";
  } else {
      $level = "Master";
  }



  //update details
  if(isset($_POST['submit'])){
    $username =  !empty($_POST['username']) ? $_POST['username'] : $user['username'];
    $pass = $_POST['password'] ?? '';
    $confPass = $_POST['confirmPassword'];
    $file = $_FILES['profile_pic'] ?? $user['profile_pic'];
    $filename = $user['profile_pic']; 


    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_pic'];
        $allowed = ['image/jpeg', 'image/png', 'image/webp' , 'image/jpg'];

        if (!in_array($file['type'], $allowed)) {
            $msg = 'Invalid image type.';
            $title = 'Warning';
        } else {
            $originalName = basename($file['name']);
            $destination = '../../uploads/'.$originalName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
               if ($user['profile_pic'] && $user['profile_pic'] !== $originalName && file_exists('../../uploads/' . $user['profile_pic'])) {
                  unlink('../../uploads/' . $user['profile_pic']);
              }
                $filename = $originalName;
            } else {
                $msg = "Failed to move uploaded file.";
                $title = 'Error';
            }
        }
    }
      

    if($pass !== ''){

      if($pass != $confPass){
        $msg = "Password doesnt match";
        $title= 'Error';
    }else if(strlen($pass)< 5){
        $msg= "Password must be at least 5 characters";
        $title= 'Warning';
    }else if(!preg_match('/[0-9]/' , $pass)){
        $msg= "Password must contain atleast 1 number";
        $title= 'Warning';
    }else{
        $stmt = $pdo->prepare("select *from users where username= :username");
        $stmt->execute(['username'=>$username]);
        $user_name=$stmt->fetch();

        if($user_name['username'] && $user_name['id']!=$uid){
            $msg="Username already taken.";
            $title= 'warning';
        }else{
        $hash_pass = password_hash($pass,PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, profile_pic = ? WHERE id = ?");
        $stmt->execute([$username, $hash_pass, $filename, $uid]);
        $msg = "Profile updated successfully.";
        $title = 'Success';
        
        // Re-fetch updated user data to reflect new image
        $user_stmt = $pdo->prepare("SELECT * FROM users WHERE id= :id");
        $user_stmt->execute(['id' => $uid]);
        $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    }

    }

  }else{
     $stmt = $pdo->prepare("UPDATE users SET username = ?, profile_pic = ? WHERE id = ?");
     $stmt->execute([$username, $filename, $uid]);
     $msg = "Profile updated successfully.";
     $title = 'Success';

     // Re-fetch updated user data to reflect new image
    $user_stmt = $pdo->prepare("SELECT * FROM users WHERE id= :id");
    $user_stmt->execute(['id' => $uid]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

  }
}

  //submission_count

  $stmt = $pdo->prepare("SELECT COUNT(*) AS total_success FROM submission WHERE uid = ? AND result = 'Success'");
  $stmt->execute([$uid]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  $totalSuccess = $row['total_success'];

  //accuracy
  $sub_stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM submission WHERE uid = ?");
  $sub_stmt->execute([$uid]);
  $total_row = $sub_stmt->fetch(PDO::FETCH_ASSOC);
  $totalSubmission = $total_row['total'];
  if($totalSubmission != 0){
  $accuracy = ($totalSuccess/$totalSubmission) * 100;
  }else{
    $accuracy = 0;
  }

  //last active
  $stmt = $pdo->prepare("SELECT MAX(timestamp) as last_submit FROM submission WHERE uid = ?");
  $stmt->execute([$uid]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $lastSubmit = $result['last_submit'];

  if ($lastSubmit) {
      $formattedDate = date('d M Y', strtotime($lastSubmit));
  } else {
      $formattedDate = 'Null';
  }

  //streak

  $stmt = $pdo->prepare("
  SELECT DISTINCT DATE(timestamp) AS submit_date
  FROM submission
  WHERE uid = ?
  ORDER BY submit_date DESC
  ");
  $stmt->execute([$uid]);
  $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

  $streak = 0;
  $today = new DateTime();
  $expected = clone $today;

  foreach ($dates as $dateStr) {
      $submitDate = new DateTime($dateStr);
      
      if ($submitDate->format('Y-m-d') === $expected->format('Y-m-d')) {
          $streak++;
          $expected->modify('-1 day'); // go to previous day
      } else {
          break; // streak broken
      }
  }

  //recently solved

  $ques_stmt = $pdo->prepare("SELECT q.title , q.description ,q.difficulty , t.name ,s.code, s.timestamp FROM question q JOIN topic t on q.topic_id=t.id JOIN submission s on q.id=s.qid WHERE s.result = 'Success' AND s.uid = :user_id  ORDER BY s.timestamp DESC Limit 4");
  $ques_stmt->execute(['user_id' => $uid]);
  $question = $ques_stmt->fetchAll(PDO::FETCH_ASSOC);

  //achievement display

  $achieve_stmt = $pdo->prepare("SELECT a.icon , a.title FROM achievement a JOIN user_achievement u ON a.id = achievement_id WHERE u.user_id = ? Limit 3");
  $achieve_stmt->execute([$uid]);
  $achievement = $achieve_stmt->fetchAll(PDO::FETCH_ASSOC);

  $sql = "SELECT a.title, a.description, a.icon, ua.date_earned
        FROM user_achievement ua
        JOIN achievement a ON ua.achievement_id = a.id
        WHERE ua.user_id = :uid
        ORDER BY ua.date_earned DESC";

  $stmt = $pdo->prepare($sql);
  $stmt->execute(['uid' => $uid]);
  $all_achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User | Profile</title>
  <link rel="icon" href="../../assets/images/web_icon.png" type="image/png">
  <link rel="stylesheet" href="../../styles/toast.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/profile.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cal-heatmap/cal-heatmap.css">
<script src="https://cdn.jsdelivr.net/npm/cal-heatmap/dist/cal-heatmap.min.js"></script>
</head>
<body>

<div class="notification"></div>
  <!--Header-->
  <header>
    <div class="container">
      <nav>
        <div>
        <a href="#" class="logo">
          <span>^_^</span>
            <span>Learnix</span>
        </a>
        </div>

            <div class="nav-links">
                <a href="../../auth/logout.php">Logout</a>
                <a href="../dashboard.php">Dashboard</a>
            </div>
      </nav>
    </div>
  </header>

  <!--profile header-->
  <section class="profile-header">
    <div class="profile-background"></div>
      <div class="container">
        <div class="profile-avatar">
          <img src="
          <?= 
            !empty($user['profile_pic'])&&file_exists('../../uploads/'.$user['profile_pic'])? '../../uploads/'.$user['profile_pic'] 
            : '../../uploads/no_profile-user.png' ?>
          " alt="">
        </div>

        <div class="profile-info">
          <h1><?= htmlspecialchars($user['username']) ?></h1>
          <div class="profile-username">
            <span><?= htmlspecialchars($user['email']) ?></span>
            <i class="fas fa-badge-check" style="color: var(--primary);"></i>
            <button class="edit-btn" onclick="editDetails()"><i class="fa-solid fa-pen-to-square"></i></button>
          </div>

          <div class="profile-badges">
            <div class="badge">
              <i class="fas fa-calendar-alt"></i>
              Last Active: <?= htmlspecialchars($formattedDate) ?>
            </div>

            <div class="badge">
            <i class="fa-solid fa-medal"></i>
            <?= htmlspecialchars(ucfirst($level)) ?>
            </div>
            
          </div>

          <div class="profile-stats">
            <div class="stat-item">
              <div class="stat-value"><?= htmlspecialchars($totalSuccess)?></div>
              <div class="stat-label">Problems Solved</div>
            </div>
            <div class="stat-item">
              <div class="stat-value"><?= htmlspecialchars(round($accuracy,2))?>%</div>
              <div class="stat-label">Accuracy</div>
            </div>
            <div class="stat-item">
              <div class="stat-value"><?= htmlspecialchars($streak) ?></div>
              <div class="stat-label">Day Streak</div>
            </div>
          </div>
        </div>
      </div>
  </section>

  <div class="container">
    <div class="profile-content">
      <div class="left-column">
        <!--Solved problems-->
        <div class="card">
          <div class="card-header">
            <h2>Recently Solved Problems</h2>
            <a href="../dashboard.php?page=submission">View All</a>
          </div>

          <div class="problems-grid">

          <?php foreach($question as $ques): ?>
            <div class="problem-card">
              <div class="problem-header">
                <h3 class="problem-title"><?= htmlspecialchars(ucwords($ques['title'])) ?></h3>
                <span class="problem-difficulty difficulty-<?= htmlspecialchars(strtolower($ques['difficulty'])) ?>"><?= htmlspecialchars(($ques['difficulty'])) ?></span>
                </div>
                <div class="problem-meta">
                  <span><?= htmlspecialchars(ucwords($ques['name'])) ?></span>
                  <?php
                    $submitDate = new DateTime($ques['timestamp']);
                    $now = new DateTime();
                    $diff = $now->diff($submitDate);

                    if ($diff->y > 0) {
                        $ago = $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
                    } elseif ($diff->m > 0) {
                        $ago = $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
                    } elseif ($diff->d > 0) {
                        $ago = $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
                    } elseif ($diff->h > 0) {
                        $ago = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
                    } elseif ($diff->i > 0) {
                        $ago = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
                    } else {
                        $ago = 'just now';
                    }
                  ?>
                  <span>Solved: <?= $ago ?></span>

                 
                </div>
                <p class="problem-description"><?= htmlspecialchars(mb_strimwidth($ques['description'], 0, 80, '...')) ?>.</p>
                <div class="problem-footer">
                    <button class="btn btn-outline" onclick="showSolution(this)" data-code="<?= htmlspecialchars($ques['code']) ?>">View Solution</button>
              </div>
            </div>
            <?php endforeach; ?>

           
          </div>
        </div>
      </div>

      <div class="right-column">
        
      <div class="card">
        <div class="card-header">
        <h2>Achievements</h2>
        <button class="achieve-btn"> View All</button>
      </div>

      <?php foreach($achievement as $ac): ?>
      <div class="achievements-grid">
        <div class="achievement-item">
          <div class="achievement-icon">
              <i class="<?= htmlspecialchars($ac['icon']) ?>"></i>
          </div>
          <div class="achievement-title"><?= htmlspecialchars($ac['title']) ?></div>
      </div>
      <?php endforeach ?>

       </div>
      </div>

    </div>
  </div>

  </div>

      <!--Edit tab-->

  <div class="overlay" id="overlay"></div>

  <div class="edit-tab" id="edit-div">
    <div class="title">
      <h1>Profile Update</h1>
      <button onclick="closeField()"><i class="fa-solid fa-circle-xmark"></i></button>
    </div>

    <div class="edit-details">
      <form action="" method="post" enctype="multipart/form-data">
        <div class="username-field">
          <div><i class="fa-solid fa-user"></i>
          <span>Current Username: <span style="color: rgba(170, 139, 255, 1);"><?= htmlspecialchars($user['username']) ?></span></span>
          </div>
          <input type="text" placeholder="Enter new username" name="username">
        </div>

        <div class="password-field">
          <div><i class="fa-solid fa-key"></i>
          <span>New Password</span>
          </div>
          <input type="password" name="password" placeholder="Enter new password">
        </div>

        <div class="password-field">
          <div><i class="fa-solid fa-key"></i>
          <span>Re-Enter Password</span>
          </div>
          <input type="password" name="confirmPassword" placeholder="Re-enter new password">
        </div>

        <div class="dp-field">
          <div class="input-dp">
            <div>
            <label for="photoUpload" class="upload-btn">📤 Upload New Photo</label>
            <input type="file" id="photoUpload" name="profile_pic" accept="image/*" style="display: none;">
            </div>
          </div>

          <div class="dp-display">
            <img src="
            <?=!empty($user['profile_pic'])&&file_exists('../../uploads/'.$user['profile_pic'])? '../../uploads/'.$user['profile_pic'] 
            : '../../uploads/no_profile-user.png' ?>
            " alt="">
          </div>
        </div>


          <div class="update-btn-field">
          <button type="submit" name="submit" class="update-btn">Update</button>
        </div>

      </form>
    </div>
  </div>

      <!--View Solution-->
      <div class="view-solution" id="view-solution">
        <div class="header-sol">
          <h2><i class="fa-solid fa-code"></i> Solution</h2>
          <button onclick="closeSolution()" title="Close">&times;</button>
        </div>
        <div class="solution-body">
          <pre><code id="code-dis" class="language-php"></code></pre>
        </div>
      </div>

      <!-- View All Achievements Modal -->
    <div class="view-achievements" id="view-achievements">
      <div class="header-achieve">
        <h2><i class="fa-solid fa-trophy"></i> All Achievements</h2>
        <button onclick="closeAchievements()" title="Close">&times;</button>
      </div>
      <div class="achievements-list">
        <?php foreach($all_achievements as $ach): ?>
          <div class="achievement-card">
            <div class="achievement-icon-large">
              <i class="<?= htmlspecialchars($ach['icon']) ?>"></i>
            </div>
            <div class="achievement-info">
              <h3><?= htmlspecialchars($ach['title']) ?></h3>
              <p><?= htmlspecialchars($ach['description']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>



  <script>
     document.querySelectorAll('.achievement-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                const icon = this.querySelector('.achievement-icon');
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease';
            });
            
            item.addEventListener('mouseleave', function() {
                const icon = this.querySelector('.achievement-icon');
                icon.style.transform = 'scale(1)';
            });
        });
        
  </script>

    <script>
    window.toastMsgData = {
        title: <?= json_encode($title) ?> ,
        msg : <?= json_encode($msg) ?> 
    }
  </script>

  <script src="../../scripts/edit-user-info.js"></script>
  <script type="module" src="../../scripts/add-user-toast.js"></script>
</body>
</html>