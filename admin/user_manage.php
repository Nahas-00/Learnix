<?php

include 'functions/add-user.php';

$showAddUser = false;
$msg = null;
$title = null;

if (isset($_SESSION['toast'])) {
  $msg = $_SESSION['toast']['msg'];
  $title = $_SESSION['toast']['title'];
  unset($_SESSION['toast']);
}

if (isset($_SESSION['show_add_user'])) {
  $showAddUser = true;
  unset($_SESSION['show_add_user']);
}

$search = $_GET['q'] ?? '';

if (!empty($search)  && trim($_GET['q']) !== '') {
  $stmt = $pdo->prepare("SELECT id, username, email , profile_pic FROM users WHERE username LIKE :search OR email LIKE :search");
  $stmt->execute(['search' => "%$search%"]);
} else {
  $stmt = $pdo->query("SELECT id, username, email , profile_pic FROM users");
}
$user=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | User Manage</title>
    <link rel="stylesheet" href="../styles/admin_dash.css">
    <link rel="stylesheet" href="../styles/add-user.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
     <link rel="stylesheet" href="../styles/toast.css">
</head>
<body>

<!--Toast Message-->
<div class="notification"></div>

    <div class="welcome-message">
     <div class="welcome-header">
       <h1>Manage Users</h1>
      <p class="welcome-text">Add, Edit or Delete User</p>
     </div>

      <div class="profile-icon">
        <img src="../uploads/no_profile.png" alt="">
        <span class="profile-span">View profile</span>
      </div>
    </div>


    <div class="overlay" id="overlay"></div>
       
        <!--Add user form-->

    <div class="add-user" id="add-user">
        <div class="add-user-title">
            <h1>Add User</h1>
            <i class="fa-solid fa-xmark" onclick="closeAddUser()"></i>
        </div>
        
        <form  class="user-form" method="post">
            <div class="username">
            <label for="username">Username</label>
            <input type="text" placeholder="eg: John Doe" name="username">
            </div>

            <div class="email">
                <label for="email">Email</label>
                <input type="text" placeholder=" eg: test@gmail.com" name="email">
            </div>

            <div class="password">
                <label for="password">Password</label>
                <input type="password" placeholder="Atleast 5 character nad minimum 1 number" name="pass">
            </div>

            <div class="submit-btn">
                <input type="submit" value="Add" name="submit">
            </div>
        </form>
    </div>


    <div class="table-section">
        <div class="table-header">
          <h1 class="table-title">Recent Submissions</h1>

            <div class="search-bar">
              <form method="get">
                <input type="hidden" name="page" value="user_manage">
                <input type="text" name="q" placeholder="Search users..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button> 
              </form>
            </div>

           <button onclick="openAddUser()" type="button" class="add-btn">Add User</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Action</th>
                    
                </tr>
            </thead>

            <tbody>
               <?php foreach($user as $user): ?>
          <tr>

           <td><?= htmlspecialchars($user['id']) ?> </td>
            <td>
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="user-avatar"><img src="<?= 
                !empty($user['profile_pic'])&&file_exists('../uploads/'.$user['profile_pic'])? '../uploads/'.$user['profile_pic'] 
                : '../uploads/no_profile-user.png' ?>
                " alt="user_profile_photo"></div>
                <span><?= htmlspecialchars($user['username']) ?></span>
              </div>
            </td>
           
            <td><?= htmlspecialchars($user['email']) ?></td>
            
            <td>
              <a href="functions/edit-user.php?id=<?= $user['id'] ?>"><button class="edit-btn" type="button" data-id="<?= $user['id'] ?>">
              <i class="fa-solid fa-pen-to-square"></i>
              </button></a>

          <form method="post" action="functions/delete-user.php" style="display:inline;">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <button type="submit" class="delete-btn" onclick="return confirm('Delete this user?');">
              <i class="fa-solid fa-trash"></i>
            </button>
          </form>
            </td>

          </tr>
           <?php endforeach; ?>
            </tbody>
        </table>
    </div>

     <?php if(isset($msg) && isset($title)): ?>
    <script>
        window.toastMsgData = {
            title: <?= json_encode($title) ?> ,
            msg : <?= json_encode($msg) ?> 
        }
    </script>
    <?php endif; ?>

    
 

    <script src="../scripts/add-user.js"></script>
    <script type="module" src="../scripts/add-user-toast.js"></script>
    
</body>
</html>