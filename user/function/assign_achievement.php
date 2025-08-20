<?php
  include_once '../../utils/connect.php';


  session_start();
 
  $uid = $_SESSION['userid'];

  //First Shot
  $stmt = $pdo->prepare("SELECT * FROM submission where uid = ? AND result = 'Success' LIMIT 1");
  $stmt->execute([$uid]);
  $first_shot = $stmt->fetch(PDO::FETCH_ASSOC);

  if($first_shot){
     addAchievement($pdo, $uid, "First Shot");
  }

  //category explorer
  $cat_stmt = $pdo->prepare("
    SELECT 
        SUM(CASE WHEN c.name = 'DSA' AND q.difficulty = 'Easy' AND s.result = 'Success' THEN 1 ELSE 0 END) AS dsa_easy,
        SUM(CASE WHEN c.name = 'DSA' AND q.difficulty = 'Medium' AND s.result = 'Success' THEN 1 ELSE 0 END) AS dsa_medium,
        SUM(CASE WHEN c.name = 'Non-DSA' AND q.difficulty = 'Easy' AND s.result = 'Success' THEN 1 ELSE 0 END) AS nondsa_easy,
        SUM(CASE WHEN c.name = 'Non-DSA' AND q.difficulty = 'Medium' AND s.result = 'Success' THEN 1 ELSE 0 END) AS nondsa_medium
    FROM submission s
    JOIN question q ON s.qid = q.id
    JOIN category c on q.category_id = c.id
    WHERE s.uid = :uid
");

  $cat_stmt->execute(['uid' => $uid]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if($result){
  if ($result['dsa_easy'] > 0 && $result['dsa_medium'] > 0 
      && $result['nondsa_easy'] > 0 && $result['nondsa_medium'] > 0) {
     addAchievement($pdo, $uid, "Category Explorer");
  }
}


//Daily streak(5 days) - Daily streak

  $streak_stmt = $pdo->prepare("
  SELECT DISTINCT DATE(timestamp) AS submit_date
  FROM submission
  WHERE uid = ?
  ORDER BY submit_date DESC
  ");
  $streak_stmt->execute([$uid]);
  $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

  $streak = 0;
  $today = new DateTime();
  $expected = clone $today;

  foreach ($dates as $dateStr) {
      $submitDate = new DateTime($dateStr);
      
      if ($submitDate->format('Y-m-d') === $expected->format('Y-m-d')) {
          $streak++;
          $expected->modify('-1 day'); 
      } else {
          break;
      }
  }

  if($streak>=5){
     addAchievement($pdo, $uid, "5 Days Streak");
  }

//Daily streak(30 days) - 30 Day warrior

  if($streak>=30){
    addAchievement($pdo, $uid, "30 Days Warrior");
  }

//Dsa Finisher
  $dsa_stmt = $pdo->prepare("
      SELECT 
          (SELECT COUNT(*) 
          FROM question q JOIN category c ON c.id = q.category_id 
          WHERE c.name = 'DSA') AS total_dsa,
          (SELECT COUNT(DISTINCT q.id) 
          FROM submission s 
          JOIN question q ON s.qid = q.id JOIN category c ON c.id=q.category_id
          WHERE s.uid = :uid 
            AND q.category_id = c.id AND c.name = 'Dsa'
            AND s.result = 'Success') AS solved_dsa
  ");
  $dsa_stmt->execute(['uid' => $uid]);
  $dsa_result = $dsa_stmt->fetch(PDO::FETCH_ASSOC);

  if ($dsa_result && $dsa_result['total_dsa'] == $dsa_result['solved_dsa']) {
    addAchievement($pdo, $uid, "Dsa Finisher");
  }


//Non-Dsa Finisher
  $ndsa_stmt = $pdo->prepare("
      SELECT 
          (SELECT COUNT(*) 
          FROM question q JOIN category c ON c.id = q.category_id 
          WHERE c.name = 'Non-DSA') AS total_dsa,
          (SELECT COUNT(DISTINCT q.id) 
          FROM submission s 
          JOIN question q ON s.qid = q.id JOIN category c ON c.id=q.category_id
          WHERE s.uid = :uid 
            AND q.category_id = c.id AND c.name = 'Non-Dsa'
            AND s.result = 'Success') AS solved_dsa
  ");
  $ndsa_stmt->execute(['uid' => $uid]);
  $ndsa_result = $ndsa_stmt->fetch(PDO::FETCH_ASSOC);

  if ($ndsa_result && $ndsa_result['total_dsa'] == $ndsa_result['solved_dsa']) {
   addAchievement($pdo, $uid, "Non-Dsa Finisher");
  } 


//Master
  $mas_stmt = $pdo->prepare("SELECT q.*
    FROM question q
    WHERE NOT EXISTS (
        SELECT 1
        FROM submission s
        WHERE s.qid = q.id
          AND s.uid = :userid
          AND s.result = 'Success'
    );
 ");

 $mas_stmt->execute([':userid' => $uid]);

 $ques = $mas_stmt->fetchAll(PDO::FETCH_ASSOC);

  if(!$ques){
    addAchievement($pdo, $uid, "Master");
  }

//Clean Sheet
$clean_stmt = $pdo->prepare("
    SELECT qid
    FROM submission s
    WHERE s.uid = :uid
      AND s.result = 'Success'
      AND NOT EXISTS (
          SELECT 1 
          FROM submission f
          WHERE f.uid = s.uid
            AND f.qid = s.qid
            AND f.result = 'Failed'
      )
    GROUP BY qid;
");
$clean_stmt->execute(['uid' => $uid]);
$clean_solved = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!empty($clean_solved)) {
  addAchievement($pdo, $uid, "Clean Sheet");
} 




//Rookie
  $rok_stmt = $pdo->prepare("
      SELECT 
          (SELECT COUNT(*) 
          FROM question q JOIN category c ON c.id = q.category_id
          WHERE c.name = 'DSA' AND q.difficulty = 'Easy') AS total_easy_dsa,
          (SELECT COUNT(DISTINCT q.id) 
          FROM submission s 
          JOIN question q ON s.qid = q.id JOIN category c ON c.id=q.category_id
          WHERE s.uid = :uid 
            AND q.category_id = c.id AND c.name = 'Dsa' 
            AND q.difficulty = 'Easy'
            AND s.result = 'Success') AS solved_easy_dsa
  ");
  $rok_stmt->execute(['uid' => $uid]);
  $rok_result = $rok_stmt->fetch(PDO::FETCH_ASSOC);

  if ($rok_result && $rok_result['total_easy_dsa'] == $rok_result['solved_easy_dsa']) {
    addAchievement($pdo, $uid, "Rookie");
  }


//Fighter
  $fig_stmt = $pdo->prepare("
      SELECT 
          (SELECT COUNT(*) 
          FROM question q JOIN category c ON c.id = q.category_id
          WHERE c.name = 'DSA' AND q.difficulty = 'Medium') AS total_medium_dsa,
          (SELECT COUNT(DISTINCT q.id) 
          FROM submission s 
          JOIN question q ON s.qid = q.id JOIN category c ON c.id=q.category_id
          WHERE s.uid = :uid 
            AND q.category_id = c.id AND c.name = 'Dsa' 
            AND q.difficulty = 'Easy'
            AND s.result = 'Success') AS solved_easy_dsa
  ");
  $fig_stmt->execute(['uid' => $uid]);
  $fig_result = $fig_stmt->fetch(PDO::FETCH_ASSOC);

  if ($fig_result && $fig_result['total_medium_dsa'] == $fig_result['solved_easy_dsa']) {
    addAchievement($pdo, $uid, "Fighter");
  }

  //add achievement function
  function addAchievement($pdo, $uid, $achievement_name) {

    $ach_stmt = $pdo->prepare("SELECT id FROM achievement WHERE title = ?");
    $ach_stmt->execute([$achievement_name]);
    $ach = $ach_stmt->fetch(PDO::FETCH_ASSOC);

    if ($ach) {
        $achievement_id = $ach['id'];

        $check_stmt = $pdo->prepare("SELECT 1 FROM user_achievement WHERE user_id = ? AND achievement_id = ?");
        $check_stmt->execute([$uid, $achievement_id]);
        $exists = $check_stmt->fetch();

        if (!$exists) {
            $insert_stmt = $pdo->prepare("INSERT INTO user_achievement (user_id, achievement_id, date_earned) VALUES (?, ?, NOW())");
            $insert_stmt->execute([$uid, $achievement_id]);
        }
    }
}


?>