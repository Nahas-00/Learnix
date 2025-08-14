<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'learnix';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    
    $pdo->exec("USE `$dbname`");

    // Users Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            username VARCHAR(50) NOT NULL,
            profile_pic VARCHAR(255)
        );
    ");
    

    // Category Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS category (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE
        );
    ");
   

    // Topic Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS topic (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE
        );
    ");
   

    // Question Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS question (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            category_id INT NOT NULL,
            topic_id INT NOT NULL,
            difficulty ENUM('Easy', 'Medium', 'Hard') NOT NULL,
            FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE CASCADE,
            FOREIGN KEY (topic_id) REFERENCES topic(id) ON DELETE CASCADE
        );
    ");
    

    // Testcase Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS testcase (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question_id INT NOT NULL,
            input TEXT NOT NULL,
            output TEXT NOT NULL,
            FOREIGN KEY (question_id) REFERENCES question(id) ON DELETE CASCADE
        );
    ");
   

    // Submission Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS submission (
            id INT AUTO_INCREMENT PRIMARY KEY,
            uid INT NOT NULL,
            qid INT NOT NULL,
            code TEXT NOT NULL,
            language VARCHAR(50) NOT NULL,
            result VARCHAR(100) NOT NULL,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (uid) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (qid) REFERENCES question(id) ON DELETE CASCADE
        );
    ");
   

    // Achievement Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS achievement (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL UNIQUE,
            description VARCHAR(255) NOT NULL,
            icon VARCHAR(255)
        );
    ");
    

    // User Achievement Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_achievement (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            achievement_id INT NOT NULL,
            date_earned DATE NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (achievement_id) REFERENCES achievement(id) ON DELETE CASCADE
        );
    ");
    

    // Certificate Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS certificate (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(80),
            issue_date DATE DEFAULT CURRENT_DATE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ");
    

    

} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage());
}
?>
