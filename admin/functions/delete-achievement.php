<?php
include '../../utils/connect.php';
session_start();

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("DELETE FROM achievement WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['toast'] = ['title' => 'Success', 'msg' => 'Achievement deleted successfully'];
    } else {
        $_SESSION['toast'] = ['title' => 'Error', 'msg' => 'Failed to delete achievement'];
    }
}

header("Location: ../dashboard.php?page=achievements"); // Redirect back
exit;
