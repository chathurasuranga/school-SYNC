<?php 
require 'db.php'; 
include 'header.php';

// Validation & Data Fetching
if (!isset($_GET['assignment_id'])) die("Error: Assignment ID is missing.");
$assign_id = $_GET['assignment_id'];

// Get Assignment
$stmt = $pdo->prepare("SELECT a.*, c.class_name, c.id as class_id FROM assignments a JOIN teacher_classes c ON a.class_id = c.id WHERE a.id = ?");
$stmt->execute([$assign_id]);
$assignment = $stmt->fetch();

// Get Criteria
$stmt = $pdo->prepare("SELECT * FROM criteria WHERE assignment_id = ?");
$stmt->execute([$assign_id]);
$criteria = $stmt->fetchAll();

// Get Students & Marks
$sql = "SELECT s.id as student_id, s.name, m.final_percentage, m.id as mark_id 
        FROM students s 
        JOIN class_enrollments ce ON s.id = ce.student_id 
        LEFT JOIN marks m ON s.id = m.student_id AND m.assignment_id = ? 
        WHERE ce.class_id = ?
        ORDER BY s.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$assign_id, $assignment['class_id']]);
$students = $stmt->fetchAll();
?>

<!-- Header & View Toggles -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><?php echo htmlspecialchars($assignment['title']); ?></h2>
        <p class="text-muted small mb-0">Class: <?php echo htmlspecialchars($assignment['class_name']); ?> • Grading Dashboard</p>
    </div>
    
    <!-- View Switcher -->
    <div class="view-toggler btn-group mt-3 mt-md-0" role="group">

        <!-- PDF Button -->
    <a href="actions/report_pdf.php?assignment_id=<?php echo $assign_id; ?>" target="_blank" class="btn btn-outline-danger ms-2">
        <svg class="me-1" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/><path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.771.08-1.177.316-.61.958-1.226 2.031-1.292 1.057-.066 2.472.316 2.195 1.893-.18.988-.97 1.498-1.74 1.487-.976-.013-1.636-.583-1.99-1.026zm7.258-.585a.77.77 0 0 1-.224-.52c.004-.326.236-.615.58-.696.536-.126 1.07.168 1.133.727.054.484-.33.843-.722.86-.33.014-.654-.158-.767-.371zm-4.706-2.614c-1.353.18-2.283.848-2.52 1.49-.074.2-.096.38-.047.553.18.636.938.864 1.543.83 1.144-.063 1.956-.917 1.776-2.18-.086-.607-.387-1.12-.752-1.693zm1.185-2.246c.15-.38.086-.816-.164-1.127-.478-.601-1.348-.567-1.897.108-.344.42-.36.936-.174 1.258.196.34.62.535 1.096.54.78.01 1.06-.358 1.139-.779zm1.043 4.226c.264 1.291.956 1.747 1.565 1.77.58.022 1.077-.384 1.18-.946.064-.343-.058-.686-.27-.923-.48-.535-1.423-.376-2.475.1z"/></svg>
        Export PDF
    </a>

    <!-- Delete Button -->
    <form action="actions/delete_assignment.php" method="POST" class="d-inline ms-2" onsubmit="return confirm('Are you sure you want to delete this assignment permanently?');">
        <input type="hidden" name="assignment_id" value="<?php echo $assign_id; ?>">
        <button type="submit" class="btn btn-outline-secondary" title="Delete Assignment">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
        </button>
    </form>


        <button type="button" class="btn btn-white active" id="btn-list-view" onclick="switchView('list')">
            <svg class="me-1" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/></svg>
            List
        </button>
        <button type="button" class="btn btn-white" id="btn-table-view" onclick="switchView('table')">
            <svg class="me-1" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm1.5 0a.5.5 0 0 0-.5.5v12a.5.5 0 0 0 .5.5h12a.5.5 0 0 0 .5-.5V2.5a.5.5 0 0 0-.5-.5h-12zM1 3v1h14V3H1zm14 2H1v1h14V5zm0 2H1v1h14V7zm0 2H1v1h14V9zm0 2H1v1h14v-1z"/></svg>
            Table
        </button>

    </div>
</div>

<!-- VIEW 1: LIST VIEW (Cards like the image) -->
<div id="view-list">
    <?php foreach($students as $s): 
        $isGraded = !is_null($s['final_percentage']);
    ?>
    <div class="student-list-card">
        <div class="d-flex align-items-center">
            <!-- Icon -->
            <div class="status-icon <?php echo $isGraded ? 'status-success' : 'status-pending'; ?>">
                <?php if($isGraded): ?>
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/></svg>
                <?php else: ?>
                    <div style="width:16px; height:16px; background:#cbd5e1; border-radius:50%;"></div>
                <?php endif; ?>
            </div>
            <!-- Name -->
            <div>
                <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($s['name']); ?></h5>
                <small class="text-muted">ID: <?php echo $s['student_id']; ?></small>
            </div>
        </div>
        
        <!-- Score & Button -->
        <div class="d-flex align-items-center">
            <div class="text-end me-3 d-none d-sm-block">
                <div class="final-score-lbl">Final Score</div>
                <div class="final-score-val <?php echo $isGraded ? '' : 'pending'; ?>">
                    <?php echo $isGraded ? round($s['final_percentage']) . '%' : 'Pending'; ?>
                </div>
            </div>
            <button class="btn <?php echo $isGraded ? 'btn-outline-primary' : 'btn-primary'; ?> fw-bold" 
                    onclick="gradeStudentFromList(<?php echo $s['student_id']; ?>)">
                <?php echo $isGraded ? 'Regrade' : 'Grade Now'; ?>
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- VIEW 2: TABLE VIEW (Detailed Grid) -->
<div id="view-table" style="display: none;">
    <div class="card p-0 shadow-sm border-0">
        <div class="table-responsive" style="max-height: 75vh;"> 
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="sticky-top custom-thead" style="z-index: 20; top: 0;">
                    <tr>
                        <th class="ps-4 sticky-col custom-th shadow-sm" style="min-width: 150px; left: 0;">Student Name</th>
                        <?php foreach($criteria as $c): ?>
                            <th class="text-center small text-muted text-uppercase" style="min-width: 180px;">
                                <?php echo htmlspecialchars($c['criteria_name']); ?>
                            </th>
                        <?php endforeach; ?>
                        <th class="text-center">Total %</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $s): 
                        $isGraded = !is_null($s['final_percentage']);
                    ?>
                    <tr id="row-<?php echo $s['student_id']; ?>">
                        <td class="ps-4 fw-bold sticky-col custom-td" style="left: 0;">
                            <?php echo htmlspecialchars($s['name']); ?>
                        </td>
                        
                        <?php foreach($criteria as $c): ?>
                        <td class="text-center">
                            <div class="d-flex justify-content-center criteria-group" 
                                 data-student="<?php echo $s['student_id']; ?>" 
                                 data-crit-id="<?php echo $c['id']; ?>">
                                 
                                 <!-- Colored Buttons 1-4 -->
                                 <?php for($i=1; $i<=4; $i++): ?>
                                    <button type="button" class="btn rubric-btn score-<?php echo $i; ?>" 
                                            onclick="selectScore(this, <?php echo $i; ?>)">
                                        <?php echo $i; ?>
                                    </button>
                                 <?php endfor; ?>
                            </div>
                        </td>
                        <?php endforeach; ?>

                        <td class="text-center fw-bold text-primary fs-5">
                            <span id="score-<?php echo $s['student_id']; ?>" data-val="<?php echo $s['final_percentage']; ?>">
                                <?php echo $isGraded ? round($s['final_percentage']) . '%' : '-'; ?>
                            </span>
                        </td>

                        <td class="text-end pe-4">
                            <!-- Button Logic: Shows "Regrade" if exists, "Save" if new -->
                            <button class="btn btn-sm px-3 shadow-sm <?php echo $isGraded ? 'btn-outline-primary' : 'btn-primary'; ?>" 
                                    id="btn-save-<?php echo $s['student_id']; ?>"
                                    onclick="saveGrade(<?php echo $s['student_id']; ?>)">
                                <?php echo $isGraded ? 'Regrade' : 'Save'; ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JavaScript Logic -->
<script>
// --- GLOBAL VARIABLES ---
const criteriaCount = <?php echo count($criteria); ?>;
const studentIds = <?php echo json_encode(array_column($students, 'student_id')); ?>;
const assignId = <?php echo $assign_id; ?>;

// Store scores for Table View (Inline)
let tableGradingData = {}; 
// Store scores for Modal View (Popup)
let modalScores = {}; 
let currentStudentId = null;

// --- VIEW SWITCHER ---
function switchView(view) {
    const listView = document.getElementById('view-list');
    const tableView = document.getElementById('view-table');
    const btnList = document.getElementById('btn-list-view');
    const btnTable = document.getElementById('btn-table-view');

    if (view === 'list') {
        listView.style.display = 'block';
        tableView.style.display = 'none';
        btnList.classList.add('active');
        btnTable.classList.remove('active');
    } else {
        listView.style.display = 'none';
        tableView.style.display = 'block';
        btnList.classList.remove('active');
        btnTable.classList.add('active');
    }
}

// ==========================================
//  PART 1: TABLE VIEW LOGIC (Inline Grading)
// ==========================================

function selectScore(btn, score) {
    // 1. Identify Student & Criterion
    const parent = btn.parentElement;
    const studentId = parent.getAttribute('data-student');
    const critId = parent.getAttribute('data-crit-id');

    // 2. UI Update (Highlight button)
    const allBtns = parent.querySelectorAll('.rubric-btn');
    allBtns.forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    // 3. Data Update
    if (!tableGradingData[studentId]) tableGradingData[studentId] = {};
    tableGradingData[studentId][critId] = score;

    // 4. Recalculate This Student's Row
    calculateTablePercentage(studentId);
}

function calculateTablePercentage(studentId) {
    const scores = tableGradingData[studentId];
    if (!scores) return;

    let total = 0;
    let count = 0;
    for (let key in scores) {
        total += scores[key];
        count++;
    }

    // Update Percentage in Table Row
    if (count > 0) {
        const max = criteriaCount * 4;
        const percent = Math.round((total / max) * 100);
        
        const scoreEl = document.getElementById(`score-${studentId}`);
        scoreEl.innerText = percent + "%";
        scoreEl.dataset.val = percent;
    }
}

function saveGrade(studentId) {
    // 1. Get Calculated Percentage
    const scoreEl = document.getElementById(`score-${studentId}`);
    const percentage = scoreEl.dataset.val;

    

    // 2. Validation
    // If tableGradingData is empty for this student, it means the user 
    // hasn't clicked any buttons yet. 
    // If the student already has a grade (Regrade), we force them to click buttons again
    // because we don't store the individual button states in the DB.
    if (!tableGradingData[studentId]) {
        alert("Please select the scores for criteria 1-4 before saving.");
        return;
    }

    // 3. AJAX Save
    const formData = new FormData();
    formData.append('assignment_id', assignId);
    formData.append('student_id', studentId);
    formData.append('percentage', percentage);
    formData.append('scores', JSON.stringify(tableGradingData[studentId]));

    const btn = document.getElementById(`btn-save-${studentId}`);
    const originalText = btn.innerText;
    btn.disabled = true;
    btn.innerText = "Saving...";

    fetch('actions/save_grade.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        if(data.success) {
            // Success Feedback
            btn.innerText = "Regrade"; // Change text
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
            
            const row = document.getElementById(`row-${studentId}`);
            row.style.backgroundColor = "#ecfdf5"; // Green flash
            setTimeout(() => row.style.backgroundColor = "", 1500);
        } else {
            btn.innerText = originalText;
            alert('Error saving grade.');
        }
    });
}


// ==========================================
//  PART 2: MODAL LOGIC (Popup Grading)
// ==========================================

function gradeStudentFromList(studentId) {
    currentStudentId = studentId;
    modalScores = {}; // Reset modal scores
    
    // Get Name
    const nameEl = document.querySelector(`#row-${studentId} td:first-child`);
    const studentName = nameEl ? nameEl.innerText.trim() : "Student";
    
    // UI Reset
    document.getElementById('modalStudentName').innerText = studentName;
    document.getElementById('modalTotalPercent').innerText = "0%";
    document.getElementById('modalTotalPercent').style.color = "#EF4444";
    document.getElementById('modalCriteriaCount').innerText = "0";
    
    document.querySelectorAll('.btn-rating-lg').forEach(b => b.classList.remove('selected'));

    // Show Modal
    const modalEl = document.getElementById('gradingModal');
    let myModal = bootstrap.Modal.getInstance(modalEl);
    if (!myModal) myModal = new bootstrap.Modal(modalEl);
    myModal.show();
}

function modalSelectScore(btn, score, critId) {
    // UI Highlight
    const parent = btn.parentElement;
    parent.querySelectorAll('.btn-rating-lg').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    // Data Update
    modalScores[critId] = score;
    updateModalCalculation();
}

function updateModalCalculation() {
    let total = 0;
    let count = 0;
    for (let key in modalScores) {
        total += modalScores[key];
        count++;
    }

    document.getElementById('modalCriteriaCount').innerText = count;

    if (count > 0) {
        const percent = Math.round((total / (criteriaCount * 4)) * 100);
        const el = document.getElementById('modalTotalPercent');
        el.innerText = percent + "%";
        el.style.color = percent >= 50 ? "#10B981" : "#EF4444";
    }
}

function saveGradeFromModal(loadNext) {
    // Calculate Final %
    let total = 0;
    for (let key in modalScores) total += modalScores[key];
    const percentage = (criteriaCount > 0) ? (total / (criteriaCount * 4)) * 100 : 0;

    const formData = new FormData();
    formData.append('assignment_id', assignId);
    formData.append('student_id', currentStudentId);
    formData.append('percentage', percentage);
    formData.append('scores', JSON.stringify(modalScores));

    // Disable buttons
    const btns = document.querySelectorAll('.modal-footer button');
    btns.forEach(b => b.disabled = true);

    fetch('actions/save_grade.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btns.forEach(b => b.disabled = false);
        if(data.success) {
            if (loadNext) {
                // Next Student Logic
                const idx = studentIds.indexOf(currentStudentId);
                if (idx > -1 && idx < studentIds.length - 1) {
                    const nextId = studentIds[idx + 1];
                    document.getElementById('modalTotalPercent').innerText = "Saved!";
                    setTimeout(() => gradeStudentFromList(nextId), 400);
                } else {
                    alert("All students graded!");
                    location.reload();
                }
            } else {
                location.reload();
            }
        } else {
            alert('Error saving grade');
        }
    });
}
</script>
<!-- ================= GRADING MODAL ================= -->
<div class="modal fade" id="gradingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            
            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold fs-4">Grading: <span id="modalStudentName" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body pt-2 bg-light">
                
                <!-- Summary Card -->
                <div class="grade-summary-card">
                    <div>
                        <div class="grade-summary-title">Current Grade</div>
                        <div class="grade-summary-value" id="modalTotalPercent">0%</div>
                    </div>
                    <div class="criteria-met-box">
                        <div class="grade-summary-title">Criteria Met</div>
                        <div class="criteria-count"><span id="modalCriteriaCount">0</span> / <?php echo count($criteria); ?></div>
                    </div>
                </div>

                <!-- Criteria List -->
                <div id="modalCriteriaList">
                    <?php foreach($criteria as $index => $c): ?>
                    <div class="criterion-card" data-crit-id="<?php echo $c['id']; ?>">
                        <div class="criterion-title"><?php echo htmlspecialchars($c['criteria_name']); ?></div>
                        
                        <!-- Buttons 1-4 -->
                        <div class="rating-group">
                            <?php for($i=1; $i<=4; $i++): ?>
                                <button type="button" class="btn btn-rating-lg score-<?php echo $i; ?>" 
                                        data-score="<?php echo $i; ?>" 
                                        onclick="modalSelectScore(this, <?php echo $i; ?>, <?php echo $c['id']; ?>)">
                                    <?php echo $i; ?>
                                </button>
                            <?php endfor; ?>
                        </div>

                        <!-- Labels -->
                        <div class="rating-labels">
                            <span>Needs Work</span>
                            <span>Excellent</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- Footer -->
            <!-- Footer -->
            <div class="modal-footer border-0 justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                
                <div class="d-flex gap-2">
                    <!-- NEW BUTTON -->
                    <button type="button" class="btn btn-light border fw-bold text-muted" onclick="saveGradeFromModal(true)">
                        Save & Next
                    </button>
                    
                    <button type="button" class="btn btn-primary px-4 fw-bold" onclick="saveGradeFromModal(false)">
                        Save Grade
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
<?php include 'footer.php'; ?>