<?php
    include '../../utils/connect.php';

    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    $qid = $_GET['qid'] ?? 0;

    $stmt = $pdo->prepare('SELECT input,output FROM testcase WHERE question_id = ? LIMIT 2');
    $stmt->execute([ $qid]);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($result);
?>