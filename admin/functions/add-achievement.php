<?php 
include '../../utils/connect.php';

session_start();

  if($_SESSION['logid'] !== 1){
    header('Location: ../../login/login.php');
    exit;
  }

  if(isset($_POST['submit'])){
    $title = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $icon = $_POST['icon-img'] ?? '';

    if($title == '' || $description == '' || $icon == ''){
      $_SESSION['toast'] = ['title' => 'Warning' , 'msg' => 'All fields requierd'];
      header('Location: ../dashboard.php?page=achievements');
    }else{
      $stmt = $pdo->prepare("INSERT INTO achievement(title,description,icon) values(?,?,?)");
      $stmt->execute([$title,$description,$icon]);
      $_SESSION['toast'] = ['title' => 'Success' , 'msg' => ' Achievement added'];
      header('Location: ../dashboard.php?page=achievements');
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Achievement</title>
  <link rel="stylesheet" href="../styles/achieve.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Space+Grotesk:wght@400;600&display=swap">
</head>
<body>
  
       
    

</body>
</html>