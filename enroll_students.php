<?php require 'db.php'; include 'header.php';
$class_id = $_GET['class_id'];
$grade_filter = $_GET['grade'] ?? '';
$letter_filter = $_GET['letter'] ?? '';

// Build Query
$sql = "SELECT * FROM students WHERE id NOT IN (SELECT student_id FROM class_enrollments WHERE class_id = ?) ";
$params = [$class_id];
if ($grade_filter) { $sql .= " AND grade = ?"; $params[] = $grade_filter; }
if ($letter_filter) { $sql .= " AND class_letter = ?"; $params[] = $letter_filter; }
$sql .= " ORDER BY grade ASC, class_letter ASC, name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>

<!-- Simple Header -->
<div class="mb-3 d-flex align-items-center">
    <a href="class_view.php?id=<?php echo $class_id; ?>" class="btn btn-light btn-sm me-3 border">← Back</a>
    <h4 class="fw-bold mb-0">Enroll Students</h4>
</div>

<!-- Filters -->
<div class="card p-3 mb-4 border-0 bg-light rounded-3">
    <form method="GET" class="row g-2">
        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
        <div class="col-5">
            <select name="grade" class="form-select border-0 shadow-sm">
                <option value="">All Grades</option>
                <?php for($i=6; $i<=13; $i++) echo "<option value='$i' ".($grade_filter==$i?'selected':'').">Grade $i</option>"; ?>
            </select>
        </div>
        <div class="col-4">
            <select name="letter" class="form-select border-0 shadow-sm">
                <option value="">All Classes</option>
                <?php foreach(range('A','F') as $L) echo "<option value='$L' ".($letter_filter==$L?'selected':'').">Class $L</option>"; ?>
            </select>
        </div>
        <div class="col-3">
            <button type="submit" class="btn btn-dark w-100 shadow-sm">Go</button>
        </div>
    </form>
</div>

<!-- Student List Form -->
<form action="actions/enroll.php" method="POST" id="enrollForm">
    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
    
    <div class="mb-5 pb-5"> <!-- Bottom padding for sticky bar -->
        <?php if(count($students) == 0): ?>
            <div class="text-center p-5 text-muted">
                <p>No students found matching filters.</p>
            </div>
        <?php endif; ?>

        <?php foreach($students as $s): ?>
        <label class="student-select-row">
            <input type="checkbox" name="students[]" value="<?php echo $s['id']; ?>" class="big-checkbox">
            <div>
                <div class="fw-bold text-dark"><?php echo htmlspecialchars($s['name']); ?></div>
                <div class="text-muted small">Grade <?php echo $s['grade'] . $s['class_letter']; ?></div>
            </div>
        </label>
        <?php endforeach; ?>
    </div>

    <!-- Sticky Bottom Action Bar -->
    <div class="enroll-action-bar">
        <div class="text-muted small">
            Check students to add
        </div>
        <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow">
            Confirm Enrollment
        </button>
    </div>
</form>

<?php include 'footer.php'; ?>