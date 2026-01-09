<?php
require 'db.php';
require 'actions/grading_helper.php'; // Reuse your logic for A/B/C grades

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || !isset($_GET['student_id'])) {
    header("Location: admin_classes.php");
    exit;
}

$student_id = $_GET['student_id'];

// 1. Fetch Student Info
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) die("Student not found.");

// 2. Fetch All Marks for this student
// We join assignments -> teacher_classes -> users to get Subject Name and Teacher Name
$sql = "
    SELECT m.final_percentage, m.graded_at,
           a.title as assignment_name,
           tc.class_name as subject,
           u.full_name as teacher_name
    FROM marks m
    JOIN assignments a ON m.assignment_id = a.id
    JOIN teacher_classes tc ON a.class_id = tc.id
    JOIN users u ON tc.teacher_id = u.id
    WHERE m.student_id = ?
    ORDER BY m.graded_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$student_id]);
$marks = $stmt->fetchAll();

include 'header.php';
?>

<div class="container mt-4" style="max-width: 900px;">
    
    <!-- Header Area -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="admin_official_view.php?grade=<?php echo $student['grade']; ?>&letter=<?php echo $student['class_letter']; ?>" class="text-decoration-none text-muted small">
                &larr; Back to Class <?php echo $student['grade'].$student['class_letter']; ?>
            </a>
            <h2 class="fw-bold mt-1"><?php echo htmlspecialchars($student['name']); ?></h2>
            <span class="badge bg-primary">Grade <?php echo $student['grade'] . ' - ' . $student['class_letter']; ?></span>
        </div>
        
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg>
            Print Sheet
        </button>
    </div>

    <!-- Marks Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-light fw-bold">
            Academic Record
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Subject / Class</th>
                            <th>Assignment / Module</th>
                            <th class="text-center">Mark (%)</th>
                            <th class="text-center">Grade</th>
                            <th class="text-center">GPV</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($marks) == 0): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No marks recorded yet.</td></tr>
                        <?php endif; ?>

                        <?php foreach($marks as $m): 
                            $details = getGradeDetails($m['final_percentage']); 
                        ?>
                        <tr>
                            <td class="text-muted small"><?php echo date('M d, Y', strtotime($m['graded_at'])); ?></td>
                            <td>
                                <div class="fw-bold text-primary"><?php echo htmlspecialchars($m['subject']); ?></div>
                                <div class="small text-muted">Tr. <?php echo htmlspecialchars($m['teacher_name']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($m['assignment_name']); ?></td>
                            
                            <td class="text-center fw-bold fs-5"><?php echo $m['final_percentage']; ?></td>
                            
                            <td class="text-center">
                                <span class="badge bg-secondary" style="width: 35px;"><?php echo $details['grade']; ?></span>
                            </td>
                            
                            <td class="text-center fw-medium text-muted">
                                <?php echo number_format($details['gpv'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>