<?php
  session_start();
  require_once '../../utils/connect.php';

  if($_SESSION['logid'] !== 1){
    header('Location: ../../login/login.php');
    exit;
  }

    if(!isset($_GET['id'])){
      header("location: ../dashboard.php?page=user_manage");
      exit;
    }

    $id = $_GET['id'];
    $smt = $pdo->prepare("select *from users where id=?");
    $smt->execute([$id]);
    $user = $smt->fetch();

    if(!$user){
      $_SESSION['toast'] = ['title'=>'Error' , 
                            'msg'=>'user not found'];
                            exit;
      
    }

    if($_SERVER['REQUEST_METHOD']=='POST'){
      $username = $_POST['username'];
      $email = $_POST['email'];
      $password = $_POST['password'];

        if($email== '' || $password == ''  || $username == ''){
           $_SESSION['toast'] = ['title' => 'Warning', 'msg' => 'Enter all fields'];
            
        }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
           $_SESSION['toast'] = ['title' => 'Error', 'msg' => 'Enter valid email'];
        }else{

      if($password == 'unchanged'){
        $stmt= $pdo->prepare("update users set email=?,username=? where id= ?");
        $stmt->execute([$email,$username,$id]);
      }else{
        if(strlen($password)< 5){
           $_SESSION['toast'] = ['title' => 'Warning', 'msg' => 'Password must be at least 5 characters'];
            header("Location: ../dashboard.php?page=user_manage");
           exit;
        }else if(!preg_match('/[0-9]/' , $password)){
            $_SESSION['toast'] = ['title' => 'Warning', 'msg' => 'Password must contain at least 1 number'];
             header("Location: ../dashboard.php?page=user_manage");
            exit;
        }
        $pass=password_hash($password,PASSWORD_DEFAULT);
        $stmt= $pdo->prepare("update users set email=?,username=?,password=? where id= ?");
        $stmt->execute([$email,$username,$pass,$id]);

      }

      $_SESSION['toast'] = ['title' => 'Success', 'msg' => 'User credentials updated'];
        header("Location: ../dashboard.php?page=user_manage");
        exit;
    }
  }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../assets/images/web_icon.png" type="image/png">
  <link rel="stylesheet" href="../../styles/add-user.css">
  <link rel="stylesheet" href="../../styles/edit-user.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <title>Edit User</title>
</head>
<body>
  <div class="container-form">
    
    <form action="" method="post">

    <h1>Edit User Info</h1>

    <div class="username">
        <label for="username">Username</label>
        <input type="text" name="username" value="<?= $user['username']?>">
    </div>
       
    <div class="email">
        <label for="email">email</label>
      <input type="email" name="email" value="<?= $user['email']?>">
    </div>

    <div class="password">
       <label for="password">Password</label>
       <input type="password" name="password" value="unchanged">
    </div>

    <div class="profile">
       
       <img src=" <?= !empty($user['profile_pic'])&&file_exists('../../uploads/'.$user['profile_pic'])? '../../uploads/'.$user['profile_pic'] : '../../uploads/no_profile-user.png' ?>" alt="">
    </div>

    <div class="submit-btn">
      <input type="submit" name="submit" value="update">
    </div>
    </form>
  </div>

  <div class="back-btn">
    <a href="../dashboard.php?page=user_manage">
        <button class="back-button">Go Back <i class="fa-solid fa-arrow-left"></i></button>
    </a> 
  </div>
</body>
</html>