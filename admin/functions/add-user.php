<?php

include_once '../utils/connect.php';

if($_SESSION['logid'] !== 1){
    header('Location: ../../login/login.php');
    exit;
  }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];

  if (empty($username) || empty($email) || empty($pass)) {
    $_SESSION['toast'] = ['title' => 'Warning', 'msg' => 'Enter all fields'];
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['toast'] = ['title' => 'Error', 'msg' => 'Invalid Email!'];
  } elseif (strlen($pass) < 5) {
    $_SESSION['toast'] = ['title' => 'Warning', 'msg' => 'Password must be at least 5 characters'];
  } elseif (!preg_match('/[0-9]/', $pass)) {
    $_SESSION['toast'] = ['title' => 'Warning', 'msg' => 'Password must contain at least 1 number'];
  } else {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
      $_SESSION['toast'] = ['title' => 'Warning', 'msg' => 'Email already exists!'];
    } else {

      if($user['username']){
        $_SESSION['toast'] = ['title' => 'Warning', 'msg' => 'Username already exists!'];
      }else{
      $hash_pass = password_hash($pass, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (email, password, username) VALUES (?, ?, ?)");
      $insertion = $stmt->execute([$email, $hash_pass, $username]);

      if ($insertion) {
        $_SESSION['toast'] = ['title' => 'Success', 'msg' => 'User added successfully!'];
      } else {
        $_SESSION['toast'] = ['title' => 'Error', 'msg' => 'Signup failed!'];
        
      }
    }
    }
  }

  header("Location: ../admin/dashboard.php?page=user_manage"); 
  exit;
}
?>
