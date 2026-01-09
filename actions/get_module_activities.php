<?php
require '../db.php';
header('Content-Type: application/json');

if (isset($_POST['module_id'])) {
    $stmt = $pdo->prepare("SELECT id, title FROM assignments WHERE module_id = ?");
    $stmt->execute([$_POST['module_id']]);
    echo json_encode($stmt->fetchAll());
}
?>