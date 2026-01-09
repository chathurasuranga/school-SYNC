<?php
require '../db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $class_id = $_POST['class_id'];
    $module_name = trim($_POST['module_name']);

    if (empty($module_name)) {
        echo json_encode(['success' => false, 'message' => 'Module name required']);
        exit;
    }

    try {
        // Insert new module
        $stmt = $pdo->prepare("INSERT INTO modules (class_id, module_name) VALUES (?, ?)");
        $stmt->execute([$class_id, $module_name]);
        $new_id = $pdo->lastInsertId();

        echo json_encode([
            'success' => true, 
            'id' => $new_id, 
            'name' => $module_name
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}