<?php
require 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

$class_id = $_GET['class_id'];

// Get Class Info
$class = $pdo->prepare("SELECT * FROM teacher_classes WHERE id = ?");
$class->execute([$class_id]);
$classData = $class->fetch();

// Get Assignments (Modules)
$assigns = $pdo->prepare("SELECT * FROM assignments WHERE class_id = ? ORDER BY created_at DESC");
$assigns->execute([$class_id]);
$assignments = $assigns->fetchAll();

include 'header.php';
?>

<div class="container mt-4" style="max-width: 800px;">
    <h3 class="fw-bold">Generate GPA Report</h3>
    <p class="text-muted">Class: <?php echo htmlspecialchars($classData['class_name']); ?></p>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Select Modules</h5>
            <p class="small text-muted">Select the assignments you want to include in the final GPA calculation.</p>

            <form action="actions/generate_pdf_report.php" method="POST" target="_blank">
                <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                
                <div class="list-group mb-4">
                    <?php if(count($assignments) == 0): ?>
                        <div class="alert alert-warning">No assignments found for this class.</div>
                    <?php endif; ?>

                    <?php foreach($assignments as $a): ?>
                    <label class="list-group-item d-flex gap-3">
                        <input class="form-check-input flex-shrink-0" type="checkbox" name="modules[]" value="<?php echo $a['id']; ?>" checked>
                        <span>
                            <strong><?php echo htmlspecialchars($a['title']); ?></strong>
                            <br>
                            <small class="text-muted">Created: <?php echo date('Y-m-d', strtotime($a['created_at'])); ?></small>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Generate PDF Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>