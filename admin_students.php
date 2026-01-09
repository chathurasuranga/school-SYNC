<?php 
require 'db.php'; 

// SECURITY CHECK
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// HANDLE FORM SUBMISSION (Add Student)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $index_no = $_POST['index_no'];
    $name = $_POST['name'];
    $grade = $_POST['grade'];
    $letter = $_POST['class_letter'];

    try {
        $stmt = $pdo->prepare("INSERT INTO students (index_no, name, grade, class_letter) VALUES (?, ?, ?, ?)");
        $stmt->execute([$index_no, $name, $grade, $letter]);
        header("Location: admin_students.php?success=1");
        exit;
    } catch (PDOException $e) {
        $error = "Error adding student: " . $e->getMessage();
    }
}

// HANDLE BULK UPLOAD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        // Skip header row
        fgetcsv($handle);
        
        $count = 0;
        $errors = [];
        
        $stmt = $pdo->prepare("INSERT INTO students (index_no, name, grade, class_letter) VALUES (?, ?, ?, ?)");
        
        while (($row = fgetcsv($handle)) !== FALSE) {
            // Expected Row: [0] => Index No, [1] => Name, [2] => Grade, [3] => Class
            if (count($row) < 4) continue;
            
            $idx = trim($row[0]);
            $nm = trim($row[1]);
            $gr = intval($row[2]);
            $cl = strtoupper(trim($row[3]));
            
            try {
                $stmt->execute([$idx, $nm, $gr, $cl]);
                $count++;
            } catch (Exception $e) {
                $errors[] = "Failed to add $idx: " . $e->getMessage();
            }
        }
        fclose($handle);
        
        $msg = "Imported $count students successfully.";
        if (!empty($errors)) $msg .= " Errors: " . count($errors);
        
        header("Location: admin_students.php?msg=" . urlencode($msg));
        exit;
    }
}

// HANDLE DELETION
if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: admin_students.php?deleted=1");
    exit;
}

// Fetch Students
$students = $pdo->query("SELECT * FROM students ORDER BY grade ASC, class_letter ASC, name ASC")->fetchAll();

include 'header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Manage Master Student List</h2>
    <div class="d-flex gap-2">
        <!-- Download Template Link (Optional, distinct from button) -->
        
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-1"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/></svg>
            Bulk Upload
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            + Add New Student
        </button>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_GET['msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Table -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Index No</th>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Class Letter</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $s): ?>
                    <tr>
                        <td>#<?php echo $s['id']; ?></td>
                        <td><span class="font-monospace text-muted"><?php echo htmlspecialchars($s['index_no'] ?? '-'); ?></span></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($s['name']); ?></td>
                        <td><span class="badge bg-secondary">Grade <?php echo $s['grade']; ?></span></td>
                        <td><span class="badge bg-info text-dark">Class <?php echo $s['class_letter']; ?></span></td>
                        <td>
                            <a href="admin_students.php?delete_id=<?php echo $s['id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure? This will remove the student from ALL teacher classes.');">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ADD STUDENT MODAL -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Student to Master List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Index Number</label>
                        <input type="text" name="index_no" class="form-control" placeholder="e.g. ST001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Grade (6-13)</label>
                            <input type="number" name="grade" min="6" max="13" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Class Letter (A-F)</label>
                            <select name="class_letter" class="form-select" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="add_student" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- BULK UPLOAD MODAL -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Upload Students</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">Upload a CSV file with the following columns: <strong>Index No, Name, Grade, Class Letter</strong></p>
                    <div class="mb-3">
                        <a href="download_template.php" class="btn btn-outline-primary btn-sm w-100">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-2"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                            Download CSV Template
                        </a>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
