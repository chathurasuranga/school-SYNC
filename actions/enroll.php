<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $students = $_POST['students'] ?? []; // Array of IDs

    if (!empty($students)) {
        $sql = "INSERT INTO class_enrollments (class_id, student_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);

        foreach ($students as $student_id) {
            $stmt->execute([$class_id, $student_id]);
        }
    }

    // Redirect back to the class view
    header("Location: ../class_view.php?id=$class_id");
    exit;
}