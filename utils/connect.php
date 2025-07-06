<?php

  require_once '../sql/init.php';
  
  $host = 'localhost';
  $dbname = 'learnix';
  $user = 'root';
  $pass = '';

  try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$user,$pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
  }catch(PDOException $e){

    die("Databse Connection failed: ".$e->getMessage());
  }

  
?>