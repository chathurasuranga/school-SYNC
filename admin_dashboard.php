<?php 
require 'db.php'; 

// SECURITY CHECK
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Redirect non-admins back to teacher dashboard or login
    exit;
}

include 'header.php'; 

// Fetch Stats
$stats = [];
$stats['teachers'] = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'teacher'")->fetchColumn();
$stats['students'] = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$stats['classes']  = $pdo->query("SELECT COUNT(*) FROM teacher_classes")->fetchColumn();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Admin Dashboard</h2>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <!-- Teachers Count -->
    <div class="col-md-4">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Total Teachers</h5>
                <h1 class="display-4 fw-bold"><?php echo $stats['teachers']; ?></h1>
                <a href="admin_teachers.php" class="text-white text-decoration-none">Manage Teachers &rarr;</a>
            </div>
        </div>
    </div>
    
    <!-- Students Count -->
    <div class="col-md-4">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Master Student List</h5>
                <h1 class="display-4 fw-bold"><?php echo $stats['students']; ?></h1>
                <a href="admin_students.php" class="text-white text-decoration-none">Manage Students &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Active Classes Count -->
    <div class="col-md-4">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Active Classes</h5>
                <h1 class="display-4 fw-bold"><?php echo $stats['classes']; ?></h1>
                <small>Created by teachers</small>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>