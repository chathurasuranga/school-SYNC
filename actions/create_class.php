<?php
require '../db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO teacher_classes (teacher_id, class_name) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $_POST['class_name']]);
    header("Location: ../index.php");
}