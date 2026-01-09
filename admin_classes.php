<?php
require 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit; }

// --- TAB 1: TEACHERS LIST (Instead of raw classes) ---
// We get teachers and count how many classes they have
$teachers = $pdo->query("
    SELECT u.id, u.full_name, u.username,
           (SELECT COUNT(*) FROM teacher_classes tc WHERE tc.teacher_id = u.id) as class_count
    FROM users u
    WHERE u.role = 'teacher'
    ORDER BY u.full_name ASC
")->fetchAll();

// --- TAB 2: Official Classes ---
// --- TAB 2: Official Classes ---
$official_classes = $pdo->query("
    SELECT s.grade, s.class_letter, COUNT(s.id) as student_count, u.full_name as class_teacher
    FROM students s
    LEFT JOIN official_class_teachers oct ON s.grade = oct.grade AND s.class_letter = oct.class_letter
    LEFT JOIN users u ON oct.teacher_id = u.id
    GROUP BY s.grade, s.class_letter
    ORDER BY s.grade ASC, s.class_letter ASC
")->fetchAll();

// Gradients for Official Classes
$gradients = [
    'linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%)',
    'linear-gradient(135deg, #EC4899 0%, #DB2777 100%)',
    'linear-gradient(135deg, #F59E0B 0%, #D97706 100%)',
    'linear-gradient(135deg, #10B981 0%, #059669 100%)',
    'linear-gradient(135deg, #3B82F6 0%, #2563EB 100%)'
];

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Manage Classes</h2>
</div>

<!-- TABS -->
<ul class="nav nav-tabs mb-4" id="classTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" id="teacher-tab" data-bs-toggle="tab" data-bs-target="#teacher-content" type="button">
            By Teacher (Subject Classes)
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="official-tab" data-bs-toggle="tab" data-bs-target="#official-content" type="button">
            Official Sections
        </button>
    </li>
</ul>

<div class="tab-content" id="classTabsContent">

    <!-- TAB 1: TEACHER LIST -->
    <div class="tab-pane fade show active" id="teacher-content">
        <div class="alert alert-info small">Select a teacher to view their dashboard as they see it.</div>
        
        <div class="row g-3">
            <?php foreach ($teachers as $t): ?>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($t['full_name']); ?></h5>
                            <small class="text-muted">Username: <?php echo $t['username']; ?></small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-secondary mb-2 d-block"><?php echo $t['class_count']; ?> Classes</span>
                            <!-- LINK TO FAKE LOGIN VIEW -->
                            <a href="admin_view_teacher.php?teacher_id=<?php echo $t['id']; ?>" class="btn btn-sm btn-primary">
                                View Classes &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- TAB 2: OFFICIAL CLASSES (TILES) -->
    <div class="tab-pane fade" id="official-content">
        <div class="row g-4">
            <?php 
            $i = 0;
            foreach ($official_classes as $oc): 
                $bg_style = $gradients[$i % count($gradients)];
                $i++;
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header border-0 p-4" style="background: <?php echo $bg_style; ?>; border-radius: 5px 5px 0 0;">
                        <h3 class="fw-bold text-white mb-0"><?php echo $oc['grade'] . $oc['class_letter']; ?></h3>
                        <div class="text-white-50 small mb-1">Official Section</div>
                        <div class="text-white fw-bold d-flex align-items-center gap-2">
                             <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                             <?php echo $oc['class_teacher'] ? htmlspecialchars($oc['class_teacher']) : '<span class="opacity-50 fw-normal">No Teacher</span>'; ?>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mt-3 text-muted fw-medium">
                            <?php echo $oc['student_count']; ?> Students Enrolled
                        </div>
                        <div class="mt-auto pt-3 border-top text-end">
                            <a href="admin_official_view.php?grade=<?php echo $oc['grade']; ?>&letter=<?php echo $oc['class_letter']; ?>" 
                               class="text-decoration-none fw-bold text-secondary stretched-link">
                                OPEN CLASS &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>