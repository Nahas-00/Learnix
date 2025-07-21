<?php
  include '../utils/connect.php';


  
 $search = $_GET['q']??'';

 if(!empty($search) && $search!==''){
  $stmt = $pdo->prepare("Select s.id as sub_id ,s.code,s.language,s.result,s.timestamp,u.username,u.profile_pic,q.title from submission s JOIN users u on u.id=s.uid JOIN question q on s.qid=q.id WHERE u.username LIKE :username OR u.email LIKE :email ORDER BY timestamp DESC ");
  $stmt->execute(['username'=>'%'.$search.'%',
    'email'=>'%'.$search.'%']);
 }else{
   $stmt = $pdo->prepare("Select s.id as sub_id ,s.code,s.language,s.result,s.timestamp,u.username,u.profile_pic,q.title from submission s JOIN users u on u.id=s.uid JOIN question q on s.qid=q.id ORDER BY timestamp DESC ");
  $stmt->execute();
 }

   $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(isset($_POST['submit'])){
    $result=$_POST['result'] ?? '';
    $date=$_POST['specific_date'] ?? '';
    $language = $_POST['language'] ?? '';

    if($result !== "" || $date !== "" || $language !== ""){
       $sql="Select s.id as sub_id ,s.code,s.language,s.result,s.timestamp,u.username,u.profile_pic,q.title from submission s JOIN users u ON u.id = s.uid JOIN question q on s.qid=q.id WHERE 1=1";

       $conditions = [];
       $params = [];

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



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | Submissions</title>
  <link rel="stylesheet" href="../styles/admin_dash.css">
  <link rel="stylesheet" href="../styles/add-user.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <link rel="stylesheet" href="../styles/toast.css">
  <link rel="stylesheet" href="../styles/submission.css">
  <link rel="stylesheet" href="../styles/showcode.css">
</head>
<body>
 
    <!--Toast Message-->
<div class="notification"></div>

    <div class="welcome-message">
     <div class="welcome-header">
       <h1>User's Submissions</h1>
      <p class="welcome-text">See all or specific submissions</p>
     </div>

      <div class="profile-icon">
        <a href="functions/admin_profile.php">
        <img src="../uploads/no_profile.png" alt="">
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
          <h1 class="table-title">Recent Submissions</h1>

            <div class="search-bar">
              <form method="get">
                <input type="hidden" name="page" value="view_submission">
                <input type="text" name="q" placeholder="Search users..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button> 
              </form>
            </div>

           
        </div>

        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>User</th>
                    <th>Question</th>
                    <th>Language</th>
                    <th>Result</th>
                    <th>Timestamp</th>
                    <th>Action</th>
                    
                </tr>
            </thead>

            <tbody>
               <?php foreach($user as $user): ?>
          <tr>

           <td><?= htmlspecialchars($user['sub_id']) ?> </td>
            <td>
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="user-avatar"><img src="<?= 
                !empty($user['profile_pic'])&&file_exists('../uploads/'.$user['profile_pic'])? '../uploads/'.$user['profile_pic'] 
                : '../uploads/no_profile-user.png' ?>
                " alt="user_profile_photo"></div>
                <span><?= htmlspecialchars($user['username']) ?></span>
              </div>
            </td>
            <td><?= htmlspecialchars($user['title']) ?></td>
            <td><?= htmlspecialchars($user['language']) ?></td>
            <td> <span class="status status-<?= strtolower(htmlspecialchars($user['result']))?>"> <?= htmlspecialchars($user['result']) ?> </span></td>
            <td><?= htmlspecialchars($user['timestamp']) ?></td>
           <td><button class="code-btn" data-code="<?= htmlspecialchars($user['code']) ?>" onclick="showCode(this)">View Code</button></td>

          </tr>
           <?php endforeach; ?>
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