<?php 
require 'db.php'; 

// SECURITY CHECK: Only Admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// 1. HANDLE CREATE TEACHER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_teacher'])) {
    $fullName = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password, role, is_first_login) VALUES (?, ?, ?, 'teacher', 1)");
        $stmt->execute([$fullName, $username, $hashedPassword]);
        $successMsg = "Teacher created successfully!";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry error code
            $errorMsg = "Error: Username already exists.";
        } else {
            $errorMsg = "Database Error: " . $e->getMessage();
        }
    }
}

// 2. HANDLE DELETE TEACHER
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    // Prevent admin from deleting themselves
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'teacher'");
        $stmt->execute([$id]);
        header("Location: admin_teachers.php?deleted=1");
        exit;
    }
}

// 3. FETCH TEACHERS
$teachers = $pdo->query("SELECT * FROM users WHERE role = 'teacher' ORDER BY created_at DESC")->fetchAll();

include 'header.php'; 
?>

<!-- Title & Button -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Manage Teachers</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTeacherModal">
        + Add New Teacher
    </button>
</div>

<!-- Alerts -->
<?php if(isset($successMsg)): ?>
    <div class="alert alert-success"><?php echo $successMsg; ?></div>
<?php endif; ?>
<?php if(isset($errorMsg)): ?>
    <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
<?php endif; ?>
<?php if(isset($_GET['deleted'])): ?>
    <div class="alert alert-warning">Teacher account deleted.</div>
<?php endif; ?>

<!-- Teacher List Table -->
<div class="card shadow-sm">
    <div class="card-body">
        <?php if(count($teachers) == 0): ?>
            <p class="text-center text-muted my-3">No teachers found. Click "Add New Teacher" to create one.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($teachers as $t): ?>
                        <tr>
                            <td>#<?php echo $t['id']; ?></td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($t['full_name']); ?></div>
                            </td>
                            <td><code><?php echo htmlspecialchars($t['username']); ?></code></td>
                            <td>
                                <?php if($t['is_first_login']): ?>
                                    <span class="badge bg-warning text-dark">New</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small">
                                <?php echo date('M d, Y', strtotime($t['created_at'])); ?>
                            </td>
                            <td>
                                <a href="admin_teachers.php?delete_id=<?php echo $t['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Are you sure? This will delete the teacher AND all their classes/assignments.');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- CREATE TEACHER MODAL -->
<div class="modal fade" id="createTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Create Teacher Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username (Login ID)</label>
                        <input type="text" name="username" class="form-control" placeholder="e.g. jdoe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Initial Password</label>
                        <input type="text" name="password" class="form-control" value="Welcome123" required>
                        <small class="text-muted">You can change this default.</small>
                    </div>
                    <input type="hidden" name="create_teacher" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>