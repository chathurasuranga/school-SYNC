<?php 
require 'db.php'; 

// SECURITY CHECK: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$teacher_id = $_GET['teacher_id'];

// Fetch Teacher Info
$stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch();
if(!$teacher) die("Teacher not found");

// Fetch That Teacher's Classes (Just like index.php)
$stmt = $pdo->prepare("SELECT tc.*, (SELECT COUNT(*) FROM class_enrollments ce WHERE ce.class_id = tc.id) as student_count FROM teacher_classes tc WHERE teacher_id = ? ORDER BY id DESC");
$stmt->execute([$teacher_id]);
$classes = $stmt->fetchAll();

$gradients = [
    'linear-gradient(135deg, #3B82F6 0%, #2563EB 100%)',
    'linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%)',
    'linear-gradient(135deg, #10B981 0%, #059669 100%)',
    'linear-gradient(135deg, #F59E0B 0%, #D97706 100%)',
    'linear-gradient(135deg, #EC4899 0%, #DB2777 100%)'
];

include 'header.php'; 
?>

<!-- Admin Navigation Context -->
<div class="alert alert-secondary d-flex justify-content-between align-items-center">
    <span>
        <strong>Admin Mode:</strong> You are viewing 
        <span class="text-primary fw-bold"><?php echo htmlspecialchars($teacher['full_name']); ?></span>'s dashboard.
    </span>
    <a href="admin_classes.php" class="btn btn-sm btn-outline-dark">Exit & Return to List</a>
</div>

<!-- Same Title & Button as index.php -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">My Classes (View Only)</h2>
    <!-- Only show Create Button if you want admin to create class FOR this teacher -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClassModal">
        + Create Class for <?php echo htmlspecialchars($teacher['full_name']); ?>
    </button>
</div>

<!-- Grid Section (Copied from index.php) -->
<div class="row g-4">
    <?php if(count($classes) == 0): ?>
        <div class="col-12 text-center text-muted py-5">
            <h4>No classes found for this teacher.</h4>
        </div>
    <?php endif; ?>

    <?php foreach($classes as $c): 
        $bg_style = $gradients[$c['id'] % count($gradients)];
    ?>
    <div class="col-md-6 col-lg-4">
        <div class="card class-card h-100">
            <div class="card-header-color" style="background: <?php echo $bg_style; ?>; padding: 1.5rem; border-radius: 5px 5px 0 0;">
                <h4 class="card-title fw-bold mb-0 text-white"><?php echo htmlspecialchars($c['class_name']); ?></h4>
                <small class="text-white-50">Class ID: #<?php echo $c['id']; ?></small>
            </div>
            <div class="card-body d-flex flex-column p-3 border border-top-0 rounded-bottom">
                <div class="d-flex align-items-center text-muted mb-4 mt-2">
                    <span class="fw-medium"><?php echo $c['student_count']; ?> Students Enrolled</span>
                </div>
                <div class="mt-auto d-flex justify-content-end border-top pt-3">
                    <!-- IMPORTANT: Link to class_view.php -->
                    <a href="class_view.php?id=<?php echo $c['id']; ?>" class="text-decoration-none fw-bold" style="color: #4b5563">OPEN CLASS &rarr;</a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Create Class Modal (Specific for this teacher) -->
<div class="modal fade" id="createClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="actions/create_class.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Create Class for <?php echo htmlspecialchars($teacher['full_name']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Class Name</label>
                    <input type="text" name="class_name" class="form-control" placeholder="e.g. Science 6A" required>
                    <!-- Force the teacher ID -->
                    <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">
                    <!-- Force redirection back to this page -->
                    <input type="hidden" name="redirect_to" value="../admin_view_teacher.php?teacher_id=<?php echo $teacher_id; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>