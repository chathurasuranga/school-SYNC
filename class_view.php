<?php 
require 'db.php'; 
require 'includes/classes.php';
require 'includes/functions.php'; // For h() if we want to use it later, or just general practice
include 'header.php';

$class_id = $_GET['id'];

// 1. Fetch Class Info
$class = get_class_details($pdo, $class_id);

// 2. Check if class exists
if (!$class) {
    die("<div class='alert alert-danger m-4'>Class not found.</div>");
}

// 3. SECURITY CHECK: Allow Access only if (Owner OR Admin)
if (!can_access_class($class, $_SESSION['user_id'], $_SESSION['role'])) {
    die("<div class='alert alert-danger m-4'>Access Denied: You do not have permission to view this class.</div>");
}

// 4. Get Enrolled Count
$student_count = get_class_enrollment_count($pdo, $class_id);

// 5. Get Assignments
$assignments = get_class_assignments($pdo, $class_id);

// 6. Get Modules for Dropdown
$modules = get_class_modules($pdo, $class_id);
?>
<!-- Class Banner -->
<div class="class-banner">
    <h1 class="banner-title"><?php echo htmlspecialchars($class['class_name']); ?></h1>
    <div class="d-flex align-items-center mt-2 banner-subtitle">
        <svg class="me-2" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>
        <span><?php echo $student_count; ?> Students Enrolled</span>
    </div>
    <div class="mt-4">
        <a href="enroll_students.php?class_id=<?php echo $class_id; ?>" class="btn btn-light text-primary fw-bold shadow-sm me-2">
            + Enroll Students
        </a>
        <button class="btn btn-outline-light fw-bold" data-bs-toggle="modal" data-bs-target="#newAssignmentModal">
            + New Assignment
        </button>

        <!-- ... existing buttons ... -->
    
    <!-- Button 1: Module Grade Report -->
    <button class="btn btn-warning fw-bold text-dark me-2" data-bs-toggle="modal" data-bs-target="#moduleReportModal">
        📄 Module Grade (30/70)
    </button>

    <!-- Button 2: Final GPA -->
    <button class="btn btn-dark fw-bold border-warning" data-bs-toggle="modal" data-bs-target="#finalGpaModal">
        🎓 Final GPA
    </button>
    
    <!-- Button 3: Delete Class -->
    <form action="actions/delete_class.php" method="POST" class="d-inline ms-2" onsubmit="return confirm('WARNING: Are you sure you want to delete this ENTIRE CLASS? All students, assignments, and grades will be permanently deleted. This cannot be undone.');">
        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
        <button type="submit" class="btn btn-outline-danger fw-bold bg-dark bg-opacity-25 border-0 text-white">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
            Delete Class
        </button>
    </form>


    </div>
</div>

<!-- Assignments List -->
<h5 class="text-muted fw-bold mb-3 px-1">Assignments</h5>

<?php if(count($assignments) == 0): ?>
    <div class="text-center py-5 text-muted">
        <p>No assignments yet.</p>
    </div>
<?php endif; ?>

<div class="row">
    <?php foreach($assignments as $a): ?>
    <div class="col-12 col-lg-10">
        <a href="grading_dashboard.php?assignment_id=<?php echo $a['id']; ?>" class="text-decoration-none text-dark">
            <div class="assignment-card">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
                    </div>
                    <div>
                        <!-- Show Module Name if exists -->
                        <?php if($a['module_name']): ?>
                            <span class="badge bg-light text-secondary border mb-1"><?php echo htmlspecialchars($a['module_name']); ?></span>
                        <?php endif; ?>
                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($a['title']); ?></h5>
                        <small class="text-muted">Posted: <?php echo date('M d, Y', strtotime($a['created_at'])); ?></small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-primary fw-bold small d-none d-sm-block">
                        Grade <span style="font-size: 1.2rem;">&rsaquo;</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- ================= MODAL 1: CREATE ASSIGNMENT ================= -->
<div class="modal fade" id="newAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="actions/create_assignment.php" method="POST" class="modal-content">
            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
            
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create New Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <!-- Module Dropdown -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Module</label>
                    <select id="moduleSelect" name="module_id" class="form-select" onchange="checkModuleDropdown()">
                        <option value="">-- No Module --</option>
                        <?php foreach($modules as $m): ?>
                            <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['module_name']); ?></option>
                        <?php endforeach; ?>
                        <option value="new_module_option" class="fw-bold text-primary">+ Add New Module...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Assignment Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Midterm Essay" required>
                </div>
                
                <label class="form-label fw-bold">Grading Criteria</label>
                <div id="criteria-container">
                    <input type="text" name="criteria[]" class="form-control mb-2" placeholder="Criterion 1 (e.g. Grammar)" required>
                    <input type="text" name="criteria[]" class="form-control mb-2" placeholder="Criterion 2">
                </div>
                
                <button type="button" class="btn btn-outline-secondary w-100 border-dashed mt-2" onclick="addCriterion()">
                    + Add Another Criterion
                </button>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Assignment</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: ADD NEW MODULE ================= -->
<div class="modal fade" id="addModuleModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-6 fw-bold">Add Module</h5>
                <button type="button" class="btn-close" onclick="closeModuleModal()"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="newModuleName" class="form-control" placeholder="Module Name (e.g. Geometry)">
                <div id="moduleError" class="text-danger small mt-1" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" onclick="saveNewModule()">Save Module</button>
            </div>
        </div>
    </div>
</div>

<script>
// --- Assignment Modal Logic ---
function addCriterion() {
    const container = document.getElementById('criteria-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'criteria[]';
    input.className = 'form-control mb-2';
    input.placeholder = 'New Criterion';
    container.appendChild(input);
}

// --- Module Logic ---
const moduleSelect = document.getElementById('moduleSelect');
const assignModalEl = document.getElementById('newAssignmentModal');
const moduleModalEl = document.getElementById('addModuleModal');
let assignModalInstance;
let moduleModalInstance;

function checkModuleDropdown() {
    if (moduleSelect.value === 'new_module_option') {
        // 1. Hide Assignment Modal (but don't destroy it)
        assignModalInstance = bootstrap.Modal.getInstance(assignModalEl);
        assignModalEl.classList.add('fade'); // Ensure visual transition
        assignModalInstance.hide();

        // 2. Show Module Modal
        moduleModalInstance = new bootstrap.Modal(moduleModalEl);
        moduleModalInstance.show();
        
        // Reset Dropdown in case they cancel
        moduleSelect.value = ""; 
    }
}

function closeModuleModal() {
    // Close Module Modal and Re-open Assignment Modal
    moduleModalInstance.hide();
    assignModalInstance.show();
}

function saveNewModule() {
    const name = document.getElementById('newModuleName').value;
    const errorEl = document.getElementById('moduleError');

    if (!name.trim()) {
        errorEl.innerText = "Please enter a name";
        errorEl.style.display = 'block';
        return;
    }

    // AJAX Save
    const formData = new FormData();
    formData.append('class_id', <?php echo $class_id; ?>);
    formData.append('module_name', name);

    fetch('actions/create_module.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // 1. Create new option
            const option = document.createElement('option');
            option.value = data.id;
            option.text = data.name;
            
            // 2. Add to dropdown (before the "Add New" option)
            const addNewOption = moduleSelect.querySelector('option[value="new_module_option"]');
            moduleSelect.insertBefore(option, addNewOption);
            
            // 3. Select it
            moduleSelect.value = data.id;

            // 4. Close Module Modal & Open Assignment Modal
            document.getElementById('newModuleName').value = ""; // Clear input
            errorEl.style.display = 'none';
            closeModuleModal();
        } else {
            errorEl.innerText = data.message || "Error saving";
            errorEl.style.display = 'block';
        }
    });
}
</script>

<!-- ================= MODAL 1: MODULE REPORT (30% / 70%) ================= -->
<div class="modal fade" id="moduleReportModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="actions/report_module_final.php" method="GET" target="_blank" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Calculate Module Grade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Select the module and the specific activity that contributes 30%.</p>
                
                <!-- Select Module -->
                <div class="mb-3">
                    <label class="fw-bold">1. Select Module</label>
                    <select name="module_id" id="rep_module_id" class="form-select" required onchange="loadActivities()">
                        <option value="">-- Select --</option>
                        <?php 
                        // Fetch modules again for this dropdown
                        $modStmt = $pdo->prepare("SELECT * FROM modules WHERE class_id = ?");
                        $modStmt->execute([$class_id]);
                        foreach($modStmt->fetchAll() as $m) {
                            echo "<option value='{$m['id']}'>{$m['module_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Select 30% Activity -->
                <div class="mb-3">
                    <label class="fw-bold">2. Select 30% Activity</label>
                    <select name="weight_30_id" id="rep_activity_id" class="form-select" required>
                        <option value="">-- First Select Module --</option>
                    </select>
                    <div class="form-text">This activity gets 30%. The average of all others gets 70%.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning fw-bold">Generate Sheet</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: FINAL GPA (Select Modules) ================= -->
<div class="modal fade" id="finalGpaModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="actions/report_final_gpa.php" method="POST" target="_blank" class="modal-content">
            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Calculate Final GPA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Select modules to include in the Final GPA calculation.</p>
                <label class="fw-bold mb-2">Select Modules:</label>
                
                <div class="list-group">
                    <?php 
                    $modStmt->execute([$class_id]); // Reuse statement
                    foreach($modStmt->fetchAll() as $m): ?>
                    <label class="list-group-item">
                        <input class="form-check-input me-2" type="checkbox" name="modules[]" value="<?php echo $m['id']; ?>" checked>
                        <?php echo htmlspecialchars($m['module_name']); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-dark fw-bold">Calculate GPA</button>
            </div>
        </form>
    </div>
</div>

<script>
function loadActivities() {
    const modId = document.getElementById('rep_module_id').value;
    const actSelect = document.getElementById('rep_activity_id');
    
    actSelect.innerHTML = '<option>Loading...</option>';

    const formData = new FormData();
    formData.append('module_id', modId);

    fetch('actions/get_module_activities.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        actSelect.innerHTML = '<option value="">-- Select 30% Activity --</option>';
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.text = item.title;
            actSelect.appendChild(opt);
        });
    });
}
</script>


<?php include 'footer.php'; ?>