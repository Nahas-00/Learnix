<?php

  session_start();
  require_once '../utils/connect.php';

  if(isset($_POST['submit'])){

    $email = $_POST['email'] ?? '';
    $pass = $_POST['pass'] ?? '';
    $role = 'user';

    if(!$email || !$pass){
      $msg = "Enter both email and password";
      $title= 'Warning';
    }

    $stmt=$pdo->prepare("select * from users where email= :email LIMIT 1");
    $stmt->execute(['email'=>$email]);
    $user=$stmt->fetch();

    if($user && password_verify($pass,$user['password'])){
      
      $_SESSION['userid'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $msg = "Login Successfull";
      $title= 'Success';

      if($email == "admin@learnix.com"){
        $role = 'admin';
      }
      

    }else{
      $msg = "Invalid Email or Password";
      $title= 'Error';
    }


  }

?>