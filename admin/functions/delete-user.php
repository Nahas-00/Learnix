<?php
    require_once '../../utils/connect.php';
    session_start();

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])){
      $id = $_POST['user_id']; 

      $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC); 

      if($user&&$user['email'] == 'admin@learnix.com'){
         
         $_SESSION['toast'] = [
           'title' => 'Error',
          'msg' => 'Admin cannot be deleted.'
           
        ];
        header("Location: ../dashboard.php?page=user_manage");
         exit;
      }else{
      $stmt = $pdo ->prepare("Delete from users where id = ?");
      $del = $stmt->execute([$id]);
      if ($del) {
        $_SESSION['toast'] = [
          'title' => 'Success', 
          'msg' => 'User deleted successfully!'
            
        ];
    } else {
        $_SESSION['toast'] = [
           'title' => 'Error',
          'msg' => 'Failed to delete user.'
           
        ];
        exit;
    }
      header("location: ../dashboard.php?page=user_manage");
  }
    }else{
       $_SESSION['toast'] = [
        'title' => 'Error',
        'msg' => 'Invalid request for user deletion.'
        
    ];
    header("Location: ../dashboard.php?page=user_manage");
    exit;
    }
?>