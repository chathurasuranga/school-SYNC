<?php
require 'db.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $error = "Passwords do not match!";
    } elseif (strlen($new_pass) < 3) {
        $error = "Password must be at least 3 characters.";
    } else {
        // Hash the new password
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        
        // Update DB: Set new password AND turn off is_first_login
        $stmt = $pdo->prepare("UPDATE users SET password = ?, is_first_login = 0 WHERE id = ?");
        $stmt->execute([$hash, $_SESSION['user_id']]);

        // Redirect to the Dashboard
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password - SchoolSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card p-5 shadow-sm" style="width: 400px;">
        <h4 class="mb-3 text-center">Change Password</h4>
        <p class="text-muted text-center small mb-4">Since this is your first login, you must set a new password.</p>
        
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Password & Login</button>
        </form>
    </div>
</body>
</html>