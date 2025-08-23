<?php
  include '../../utils/connect.php';
  session_start();

  if($_SESSION['logid'] !== 1){
    header('Location: ../../login/login.php');
    exit;
  }

  if(isset($_POST['ques-delete-btn'])){
    $id = $_POST['ques_id'];

    $stmt= $pdo->prepare('DELETE FROM question WHERE id = ?');
    $stmt->execute([$id]);

    $stmt_test = $pdo->prepare('DELETE FROM testcase WHERE question_id = ?');
    $stmt_test->execute([$id]);

    $confirm_stmt = $pdo->prepare('SELECT * FROM question where id = ?');
    $confirm_stmt->execute([$id]);

    $pass = $confirm_stmt->fetch(PDO::FETCH_ASSOC);

    if(!$pass){
    $_SESSION['toast'] = ['title' => 'Success', 'msg' => 'Question Deleted!'];
    }else{
      $_SESSION['toast'] = ['title' => 'Error', 'msg' => 'Question Deletion Failed!'];
    }

    header('location: ../dashboard.php?page=question_manage');
  }

?>