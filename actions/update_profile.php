<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number']; // New Field
    $bio = $_POST['bio'];

    // 1. Handle File Upload
    $img_sql = "";
    // Start parameters array with text fields
    $params = [$full_name, $phone_number, $bio];

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_pic']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = "profile_" . $user_id . "_" . time() . "." . $ext;
            $destination = "../uploads/" . $new_filename;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
                $img_sql = ", profile_pic = ?";
                $params[] = $new_filename; // Add image to params if uploaded
                
                // Update session name
                $_SESSION['name'] = $full_name;
            }
        }
    }

    // 2. Update Database
    $params[] = $user_id; // Add ID for WHERE clause
    
    // Updated SQL Query to include phone_number
    $sql = "UPDATE users SET full_name = ?, phone_number = ?, bio = ? $img_sql WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    header("Location: ../profile.php?success=1");
    exit;
}