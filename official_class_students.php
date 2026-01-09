<?php
require 'db.php';
require 'actions/grading_helper.php';
include 'header.php';

// 1. Get Teacher's Official Class
$teacher_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM official_class_teachers WHERE teacher_id = ?");
$stmt->execute([$teacher_id]);
$classConfig = $stmt->fetch();

if (!$classConfig) {
    echo "<div class='container p-5'><h3>You are not assigned as an Official Class Teacher.</h3></div>";
    include 'footer.php';
    exit;
}

$className = $classConfig['grade'] . " - " . $classConfig['class_letter'];
$grade = $classConfig['grade'];
$letter = $classConfig['class_letter'];

// 2. Fetch Students in this "Grade + Letter" combo
// Note: We need to find students who match this grade/letter. 
// Assuming `students` table has `grade` and `class_letter` columns? 
// Or are they enrolled in a specific class ID?
// Looking at `admin_students.php` or `create_student` logic might clarify, but usually it's direct columns.
$stm = $pdo->prepare("SELECT * FROM students WHERE grade = ? AND class_letter = ? ORDER BY name ASC");
$stm->execute([$grade, $letter]);
$students = $stm->fetchAll();

// 3. Calculate GPA for each student
foreach ($students as &$s) {
    // Get all marks for this student from ALL assignments
    // We join marks -> assignments.
    $mStmt = $pdo->prepare("
        SELECT m.final_percentage 
        FROM marks m 
        JOIN assignments a ON m.assignment_id = a.id
        WHERE m.student_id = ? AND m.final_percentage IS NOT NULL
    ");
    $mStmt->execute([$s['id']]);
    $marks = $mStmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($marks) > 0) {
        $average = array_sum($marks) / count($marks);
        $details = getGradeDetails($average);
        
        $s['avg_score'] = round($average, 1);
        $s['letter_grade'] = $details['grade'];
        $s['gpv'] = number_format($details['gpv'], 2);
    } else {
        $s['avg_score'] = '-';
        $s['letter_grade'] = '-';
        $s['gpv'] = '-';
    }
}
unset($s); // break ref
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">My Official Class: <?php echo htmlspecialchars($className); ?></h2>
            <p class="text-muted">Student Performance Overview & GPA</p>
        </div>
        <div>
            <button class="btn btn-outline-primary" onclick="window.print()">🖨️ Print List</button>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Student Name</th>
                            <th>Index No</th>
                            <th class="text-center">Avg. Score</th>
                            <th class="text-center">Grade</th>
                            <th class="text-center">GPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($students) > 0): ?>
                            <?php foreach($students as $s): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-main">
                                    <?php echo htmlspecialchars($s['name']); ?>
                                </td>
                                <td class="text-muted small font-monospace">
                                    <?php echo htmlspecialchars($s['index_no'] ?? '-'); ?>
                                </td>
                                <td class="text-center fw-bold">
                                    <?php echo $s['avg_score']; ?>%
                                </td>
                                <td class="text-center">
                                    <span class="badge 
                                        <?php 
                                            if($s['letter_grade'] == 'A' || $s['letter_grade'] == 'A-') echo 'bg-success';
                                            elseif(in_array($s['letter_grade'], ['B+','B','B-'])) echo 'bg-primary'; 
                                            elseif(in_array($s['letter_grade'], ['C+','C'])) echo 'bg-warning text-dark';
                                            elseif($s['letter_grade'] == 'S') echo 'bg-info text-dark';
                                            else echo 'bg-danger'; 
                                        ?> 
                                        rounded-pill px-3">
                                        <?php echo $s['letter_grade']; ?>
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-secondary">
                                    <?php echo $s['gpv']; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No students found in this class.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
