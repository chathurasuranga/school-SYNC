<?php
require 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit; }

$class_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM teacher_classes WHERE id = ?");
$stmt->execute([$class_id]);
$class = $stmt->fetch();

if(!$class) die("Class not found");

// Fetch Students
$stmt = $pdo->prepare("
    SELECT s.* FROM students s
    JOIN class_enrollments ce ON s.id = ce.student_id
    WHERE ce.class_id = ?
    ORDER BY s.name ASC
");
$stmt->execute([$class_id]);
$students = $stmt->fetchAll();

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="admin_classes.php" class="text-decoration-none small">&larr; Back</a>
        <h2 class="fw-bold"><?php echo htmlspecialchars($class['class_name']); ?></h2>
    </div>
    <!-- THIS BUTTON GOES TO THE REPORT GENERATOR -->
    <a href="admin_report_generator.php?class_id=<?php echo $class_id; ?>" class="btn btn-success">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/></svg>
        Generate GPA Report
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-light">
        <strong>Enrolled Students (<?php echo count($students); ?>)</strong>
    </div>
    <ul class="list-group list-group-flush">
        <?php foreach($students as $s): ?>
            <li class="list-group-item d-flex justify-content-between">
                <span><?php echo htmlspecialchars($s['name']); ?></span>
                <span class="badge bg-secondary"><?php echo $s['grade'].$s['class_letter']; ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include 'footer.php'; ?>