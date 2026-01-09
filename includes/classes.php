<?php

/**
 * Get all classes for a specific teacher with student counts
 */
function get_teacher_classes($pdo, $teacher_id) {
    $sql = "SELECT tc.*, 
            (SELECT COUNT(*) FROM class_enrollments ce WHERE ce.class_id = tc.id) as student_count 
            FROM teacher_classes tc 
            WHERE teacher_id = ? 
            ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$teacher_id]);
    return $stmt->fetchAll();
}

/**
 * Get single class details
 */
function get_class_details($pdo, $class_id) {
    $stmt = $pdo->prepare("SELECT * FROM teacher_classes WHERE id = ?");
    $stmt->execute([$class_id]);
    return $stmt->fetch();
}

/**
 * Verify if a user has access to a class
 */
function can_access_class($class, $user_id, $role) {
    if (!$class) return false;
    if ($role === 'admin') return true;
    return $class['teacher_id'] == $user_id;
}

/**
 * Get student enrollment count for a class
 */
function get_class_enrollment_count($pdo, $class_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM class_enrollments WHERE class_id = ?");
    $stmt->execute([$class_id]);
    return $stmt->fetchColumn();
}

/**
 * Get assignments for a class including module names
 */
function get_class_assignments($pdo, $class_id) {
    $sql = "SELECT a.*, m.module_name 
            FROM assignments a 
            LEFT JOIN modules m ON a.module_id = m.id 
            WHERE a.class_id = ? 
            ORDER BY m.module_name ASC, a.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$class_id]);
    return $stmt->fetchAll();
}

/**
 * Get available modules for a class
 */
function get_class_modules($pdo, $class_id) {
    $stmt = $pdo->prepare("SELECT * FROM modules WHERE class_id = ? ORDER BY module_name ASC");
    $stmt->execute([$class_id]);
    return $stmt->fetchAll();
}

/**
 * Get average final percentage per class for a teacher (For Chart)
 */
function get_class_performance_stats($pdo, $teacher_id) {
    $sql = "SELECT tc.class_name, AVG(m.final_percentage) as average_score 
            FROM teacher_classes tc
            JOIN assignments a ON a.class_id = tc.id
            JOIN marks m ON m.assignment_id = a.id
            WHERE tc.teacher_id = ?
            GROUP BY tc.id, tc.class_name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$teacher_id]);
    return $stmt->fetchAll();
}
?>
