<?php
require 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

$grade = $_GET['grade'];
$letter = $_GET['letter'];

// --- HANDLE UPDATE STUDENT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    $sid = $_POST['student_id'];
    $name = $_POST['name'];
    $new_grade = $_POST['grade'];
    $new_letter = $_POST['class_letter'];

    $stmt = $pdo->prepare("UPDATE students SET name=?, grade=?, class_letter=? WHERE id=?");
    $stmt->execute([$name, $new_grade, $new_letter, $sid]);
    
    // Refresh page (if grade/letter changed, user disappears from this view, which is expected)
    header("Location: admin_official_view.php?grade=$grade&letter=$letter&updated=1");
    exit;
}

// --- HANDLE ASSIGN TEACHER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_teacher'])) {
    $tid = $_POST['teacher_id'];
    
    // Upsert logic (Insert or Update if exists)
    // Check if exists
    $check = $pdo->prepare("SELECT id FROM official_class_teachers WHERE grade = ? AND class_letter = ?");
    $check->execute([$grade, $letter]);
    $exists = $check->fetchColumn();

    if ($exists) {
        $stmt = $pdo->prepare("UPDATE official_class_teachers SET teacher_id = ? WHERE grade = ? AND class_letter = ?");
        $stmt->execute([$tid, $grade, $letter]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO official_class_teachers (grade, class_letter, teacher_id) VALUES (?, ?, ?)");
        $stmt->execute([$grade, $letter, $tid]);
    }
    
    header("Location: admin_official_view.php?grade=$grade&letter=$letter&assigned=1");
    exit;
}

// Fetch Students
$stmt = $pdo->prepare("SELECT * FROM students WHERE grade = ? AND class_letter = ? ORDER BY name ASC");
$stmt->execute([$grade, $letter]);
$students = $stmt->fetchAll();

// Fetch Assigned Teacher
$teacher_stmt = $pdo->prepare("
    SELECT u.full_name, u.profile_pic 
    FROM official_class_teachers oct
    JOIN users u ON oct.teacher_id = u.id
    WHERE oct.grade = ? AND oct.class_letter = ?
");
$teacher_stmt->execute([$grade, $letter]);
$class_teacher = $teacher_stmt->fetch();

// Fetch All Teachers for Dropdown
$all_teachers = $pdo->query("SELECT id, full_name FROM users WHERE role = 'teacher' ORDER BY full_name ASC")->fetchAll();

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="admin_classes.php" class="text-decoration-none text-muted small">&larr; Back to Classes</a>
        <h2 class="fw-bold mt-1">Class <?php echo "$grade$letter"; ?></h2>
    </div>
    <!-- Overall Report Button -->
    <a href="admin_official_report.php?grade=<?php echo $grade; ?>&letter=<?php echo $letter; ?>" class="btn btn-success">
       Generate Class Report (PDF)
    </a>
</div>

<div class="card mb-4 border-primary">
    <div class="card-body d-flex align-items-center justify-content-between bg-primary-subtle rounded">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-white p-2 rounded-circle shadow-sm text-primary">
               <!-- Icon -->
               <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
            </div>
            <div>
                <h6 class="mb-0 text-muted small fw-bold text-uppercase">Class Teacher</h6>
                <div class="fw-bold fs-5">
                    <?php if (isset($class_teacher) && $class_teacher): ?>
                        <?php echo htmlspecialchars($class_teacher['full_name']); ?>
                    <?php else: ?>
                        <span class="text-muted fst-italic">Not Assigned</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignTeacherModal">
            <?php echo (isset($class_teacher) && $class_teacher) ? 'Change Teacher' : 'Assign Teacher'; ?>
        </button>
    </div>
</div>

<?php if(isset($_GET['updated'])): ?>
<div class="alert alert-success">Student details updated successfully!</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $s): ?>
                <tr>
                    <td>#<?php echo $s['id']; ?></td>
                    <td class="fw-bold"><?php echo htmlspecialchars($s['name']); ?></td>
                    <td>
                        <!-- VIEW MARK SHEET BUTTON -->
                        <a href="admin_student_marks.php?student_id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                            View Marks
                        </a>

                        <!-- EDIT BUTTON (Triggers Modal) -->
                        <button class="btn btn-sm btn-outline-secondary" 
                                onclick="openEditModal(<?php echo $s['id']; ?>, '<?php echo addslashes($s['name']); ?>', '<?php echo $s['grade']; ?>', '<?php echo $s['class_letter']; ?>')">
                            Edit Info
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- EDIT STUDENT MODAL -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="edit_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Grade</label>
                            <input type="number" name="grade" id="edit_grade" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Section</label>
                            <select name="class_letter" id="edit_letter" class="form-select">
                                <option value="A">A</option><option value="B">B</option>
                                <option value="C">C</option><option value="D">D</option>
                                <option value="E">E</option><option value="F">F</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning small">
                        Note: Changing Grade/Section will move the student to that official class immediately.
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="update_student" value="1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ASSIGN TEACHER MODAL -->
<div class="modal fade" id="assignTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Class Teacher (<?php echo "$grade - $letter"; ?>)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">Select a teacher to be the official Class Teacher/Homeroom Teacher for this class.</p>
                    <div class="mb-3">
                        <label class="form-label">Teacher</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">-- Select Teacher --</option>
                            <?php foreach($all_teachers as $t): ?>
                                <option value="<?php echo $t['id']; ?>">
                                    <?php echo htmlspecialchars($t['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="assign_teacher" value="1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(id, name, grade, letter) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_grade').value = grade;
    document.getElementById('edit_letter').value = letter;
    new bootstrap.Modal(document.getElementById('editStudentModal')).show();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>