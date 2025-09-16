<?php
session_start();
require 'db_connect.php'; // your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $question_id = intval($_POST['question_id']);
    $code = $_POST['code'];
    $lang=$_POST['language'];
    $stmt = $conn->prepare("INSERT INTO submission (uid, qid, code,language, result) VALUES (?, ?, ?, ?, 'LastTry')");
    $stmt->bind_param("iis", $user_id, $question_id, $code,$lang);

    if ($stmt->execute()) {
        echo "Last try saved";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
?>
