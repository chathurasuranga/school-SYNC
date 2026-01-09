<?php
require '../db.php';
require '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { die("Access denied"); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment_id'])) {
    $assign_id = $_POST['assignment_id'];
    $teacher_id = $_SESSION['user_id'];

    // Verify Ownership via Class
    $stmt = $pdo->prepare("
        SELECT a.id, a.class_id 
        FROM assignments a 
        JOIN teacher_classes c ON a.class_id = c.id 
        WHERE a.id = ? AND c.teacher_id = ?
    ");
    $stmt->execute([$assign_id, $teacher_id]);
    $result = $stmt->fetch();
    
    if ($result) {
        $class_id = $result['class_id'];
        
        // Delete marks first
        $pdo->prepare("DELETE FROM marks WHERE assignment_id = ?")->execute([$assign_id]);
        
        // Delete assignment
        $del = $pdo->prepare("DELETE FROM assignments WHERE id = ?");
        $del->execute([$assign_id]);

        header("Location: ../class_view.php?id=$class_id&msg=Assignment+deleted");
    } else {
        header("Location: ../index.php?error=Unauthorized+access");
    }
} else {
    header("Location: ../index.php");
}
