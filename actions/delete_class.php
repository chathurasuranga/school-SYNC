<?php
require '../db.php';
require '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { die("Access denied"); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];
    $teacher_id = $_SESSION['user_id'];

    // Verify Ownership
    $stmt = $pdo->prepare("SELECT id FROM teacher_classes WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$class_id, $teacher_id]);
    
    if ($stmt->fetch()) {
        // DELETE (Cascading deletes handles students/assignments/marks usually, 
        // but if not set in DB, we'd need to manually delete. 
        // Assuming ON DELETE CASCADE is set for foreign keys or we just rely on cleaning up content.)
        
        // Manual cleanup to be safe if FKs aren't strict
        $pdo->prepare("DELETE FROM assignments WHERE class_id = ?")->execute([$class_id]);
        $pdo->prepare("DELETE FROM class_enrollments WHERE class_id = ?")->execute([$class_id]);
        $pdo->prepare("DELETE FROM modules WHERE class_id = ?")->execute([$class_id]);
        
        // Delete the class
        $del = $pdo->prepare("DELETE FROM teacher_classes WHERE id = ?");
        $del->execute([$class_id]);

        header("Location: ../index.php?msg=Class+deleted+successfully");
    } else {
        header("Location: ../index.php?error=Unauthorized+access");
    }
} else {
    header("Location: ../index.php");
}
