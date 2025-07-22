<?php
  include '../utils/connect.php';


  
 $id = $_SESSION['userid'];

   $stmt = $pdo->prepare("Select s.id as sub_id ,s.code,s.language,s.result,s.timestamp,u.username,u.profile_pic,q.title from submission s JOIN users u on u.id=s.uid JOIN question q on s.qid=q.id WHERE s.uid = ? ORDER BY timestamp DESC ");
  $stmt->execute([$id]);


   $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(isset($_POST['submit'])){
    $result=$_POST['result'] ?? '';
    $date=$_POST['specific_date'] ?? '';
    $language = $_POST['language'] ?? '';

    if($result !== "" || $date !== "" || $language !== ""){
       $sql="Select s.id as sub_id ,s.code,s.language,s.result,s.timestamp,u.username,u.profile_pic,q.title from submission s JOIN users u ON u.id = s.uid JOIN question q on s.qid=q.id WHERE s.uid = :uid AND 1=1";

       $conditions = [];
       $params = [];

       $params[':uid'] = $id;

       if(!empty($result)){
        $conditions[] = "s.result = :result";
        $params[':result'] = $result;
       }

       if(!empty($language)){
        $conditions[] = "s.language = :language";
        $params[':language'] = $language;
       }

       if(!empty($date)){
        $conditions[] = "DATE(s.timestamp) = :date";
        $params[':date'] = $date;
       }

       if (!empty($conditions)) {
          $sql .= " AND " .implode(' AND ', $conditions);
        }

       $sql.= " ORDER BY timestamp DESC";

       $stmt= $pdo->prepare($sql);
       $stmt->execute($params);
       $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 }

  $user_stmt = $pdo->prepare('SELECT *FROM users WHERE id = ?');
  $user_stmt->execute([$id]);
  $info = $user_stmt->fetch(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | Submissions</title>
  <link rel="stylesheet" href="../assets/admin_dash.css">
  <link rel="stylesheet" href="../assets/add-user.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <link rel="stylesheet" href="../assets/submission.css">
  <link rel="stylesheet" href="../assets/showcode.css">
</head>
<body>
 
    <!--Toast Message-->
<div class="notification"></div>

    <div class="welcome-message">
     <div class="welcome-header">
       <h1>Your Submissions</h1>
      <p class="welcome-text">See the submissions you made</p>
     </div>

      <div class="profile-icon">
        <a href="functions/admin_profile.php">
        <img src="
         <?= 
            !empty($info['profile_pic'])&&file_exists('../uploads/'.$info['profile_pic'])? '../uploads/'.$info['profile_pic'] 
            : '../uploads/no_profile-user.png' ?>
        " alt="">
        <span class="profile-span" style="color: white;">View profile</span>
        </a>
      </div>
    </div>

<!--Filters-->
<div class="filter-title">
  <h1>Filters</h1>
</div>

    <div class="filters">
      <form action="" method="post">
        <select name="language" id="">
          <option value="">All Languages</option>
          <option value="python">Python</option>
          <option value="c">C</option>
          <option value="cpp">C++</option>
          <option value="java">Java</option>
          <option value="ruby">Ruby</option>
          <option value="golang">Golang</option>
        </select>

        <select name="result" id="" class="result">
          <option value="">All Result</option>
          <option value="success">Success</option>
          <option value="fail">Fail</option>
        </select>

        <input type="date" name="specific_date" class="date-field" value="<?= htmlspecialchars($_GET['specific_date'] ?? '') ?>">

       <input type="submit" name="submit" value="Apply" class="submit-btn">
      </form>
    </div>


<!--Table submission-->

<div class="table-section">
        <div class="table-header">
          <h1 class="table-title">Submissions</h1>

            

           
        </div>

        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Question</th>
                    <th>Language</th>
                    <th>Result</th>
                    <th>Timestamp</th>
                    <th>Action</th>
                    
                </tr>
            </thead>

            <tbody>
              <?php if (empty($user)): ?>
                  <tr>
                    <td colspan="6" style="text-align: center; padding: 1rem; color: #aaa; font-size: 1.2rem; font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif">
                      No submissions have made.
                    </td>
                  </tr>
              <?php else: ?>
               <?php foreach($user as $user): ?>
          <tr>

           <td><?= htmlspecialchars($user['sub_id']) ?> </td>
            <td><?= htmlspecialchars($user['title']) ?></td>
            <td><?= htmlspecialchars($user['language']) ?></td>
            <td> <span class="status status-<?= strtolower(htmlspecialchars($user['result']))?>"> <?= htmlspecialchars($user['result']) ?> </span></td>
            <td><?= htmlspecialchars($user['timestamp']) ?></td>
           <td><button class="code-btn" data-code="<?= htmlspecialchars($user['code']) ?>" onclick="showCode(this)">View Code</button></td>

          </tr>
           <?php endforeach; ?>
           <?php endif; ?>
            </tbody>
        </table>
    </div>

      <div class="code-overlay" id="code-overlay"></div>
    <div class="code-dis" id="code-dis">
      <div class="code-close-btn"><button onclick="closeCode()"><i class="fa-solid fa-xmark"></i></button></div>
      <pre><p id="code-area" style="margin: left 0.2rem;"> </p></pre>
    </div>

    <script src="../scripts/submission.js"></script>
    <script src="../scripts/showcode.js"></script>
</body>
</html>