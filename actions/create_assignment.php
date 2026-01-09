<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $title = $_POST['title'];
    $module_id = $_POST['module_id']; // <--- NEW LINE
    $criteria_list = $_POST['criteria'];

    // If "Select Module" (empty value) is chosen, make it NULL
    if (empty($module_id) || $module_id == 'new') {
        $module_id = NULL;
    }

    // 1. Create Assignment with Module ID
    $stmt = $pdo->prepare("INSERT INTO assignments (class_id, module_id, title) VALUES (?, ?, ?)");
    $stmt->execute([$class_id, $module_id, $title]);
    $assign_id = $pdo->lastInsertId();

    // 2. Create Criteria
    $sql = "INSERT INTO criteria (assignment_id, criteria_name) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    foreach ($criteria_list as $c) {
        if(!empty($c)) $stmt->execute([$assign_id, $c]);
    }
    
    header("Location: ../class_view.php?id=$class_id");
}