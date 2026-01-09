<?php
require '../db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_id = $_POST['assignment_id'];
    $student_id = $_POST['student_id'];
    $percentage = $_POST['percentage'];
    
    // Get the JSON string of scores: {"12": 4, "13": 3} (CriteriaID: Score)
    $scores = isset($_POST['scores']) ? json_decode($_POST['scores'], true) : [];

    try {
        $pdo->beginTransaction();

        // 1. Save Final Percentage (marks table)
        // Check if exists
        $stmt = $pdo->prepare("SELECT id FROM marks WHERE assignment_id = ? AND student_id = ?");
        $stmt->execute([$assignment_id, $student_id]);
        $mark = $stmt->fetch();

        if ($mark) {
            $stmt = $pdo->prepare("UPDATE marks SET final_percentage = ?, graded_at = NOW() WHERE id = ?");
            $stmt->execute([$percentage, $mark['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO marks (assignment_id, student_id, final_percentage) VALUES (?, ?, ?)");
            $stmt->execute([$assignment_id, $student_id, $percentage]);
        }

        // 2. Save Individual Scores (student_criteria_marks table)
        if (!empty($scores)) {
            // First, remove old scores for this student/assignment to avoid duplicates
            $stmt = $pdo->prepare("DELETE FROM student_criteria_marks WHERE assignment_id = ? AND student_id = ?");
            $stmt->execute([$assignment_id, $student_id]);

            // Insert new scores
            $sql = "INSERT INTO student_criteria_marks (assignment_id, student_id, criteria_id, score) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            foreach ($scores as $crit_id => $score_val) {
                $stmt->execute([$assignment_id, $student_id, $crit_id, $score_val]);
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}