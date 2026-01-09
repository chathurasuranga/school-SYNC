<?php 
require 'db.php'; 
require 'includes/classes.php';
include 'header.php'; 

// Fetch Classes
$classes = get_teacher_classes($pdo, $_SESSION['user_id']);

// Fetch Performance Stats
$stats = get_class_performance_stats($pdo, $_SESSION['user_id']);
$chart_labels = [];
$chart_data   = [];
foreach($stats as $s) {
    $chart_labels[] = $s['class_name'];
    $chart_data[]   = round($s['average_score'], 1);
}

// Gradient Map (Still useful for card headers, but we can make them subtler)
$gradients = [
    'linear-gradient(135deg, #6366F1 0%, #4F46E5 100%)', // Indigo
    'linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%)', // Violet
    'linear-gradient(135deg, #10B981 0%, #059669 100%)', // Emerald
    'linear-gradient(135deg, #F59E0B 0%, #D97706 100%)', // Amber
    'linear-gradient(135deg, #EC4899 0%, #DB2777 100%)'  // Pink
];
?>

<!-- Title & Button Section -->
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold fs-2" style="color: var(--text-main);">Dashboard</h2>
        <p class="mb-0" style="color: var(--text-muted);">Overview of your classes and student performance.</p>
    </div>
    
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createClassModal">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
        New Class
    </button>
</div>

<!-- Performance Chart Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card p-4">
            <h5 class="fw-bold mb-4" style="color: var(--text-main);">Class Performance Overview</h5>
            <div style="height: 300px; width: 100%;">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Classes Grid -->
<h4 class="fw-bold mb-4" style="color: var(--text-main);">Your Classes</h4>
<div class="row g-4 mb-5">
    <?php if(count($classes) == 0): ?>
        <div class="col-12 text-center py-5" style="color: var(--text-muted);">
            <h4>No classes found</h4>
            <p>Create your first class to get started.</p>
        </div>
    <?php endif; ?>

    <?php foreach($classes as $c): 
        $bg_style = $gradients[$c['id'] % count($gradients)];
    ?>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.2s;">
            <div class="card-header-color text-white p-4" style="background: <?php echo $bg_style; ?>;">
                <h4 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($c['class_name']); ?></h4>
                <small class="opacity-75">Class ID: #<?php echo $c['id']; ?></small>
            </div>
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center mb-4 mt-2" style="color: var(--text-muted);">
                    <svg class="me-2" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>
                    <span class="fw-medium"><?php echo $c['student_count']; ?> Students Enrolled</span>
                </div>
                <div class="mt-auto pt-3 border-top" style="border-color: var(--border-color) !important;">
                    <a href="class_view.php?id=<?php echo $c['id']; ?>" class="text-decoration-none fw-bold d-flex justify-content-end align-items-center" style="color: var(--primary);">
                        OPEN CLASS 
                        <svg class="ms-2" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- THE MODAL (Must be at the bottom of the page) -->
<div class="modal fade" id="createClassModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="actions/create_class.php" method="POST">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalLabel">Create New Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <label class="form-label fw-bold" style="color: var(--text-muted);">Class Name</label>
                    <input type="text" name="class_name" class="form-control form-control-lg" placeholder="e.g. Science 6A" required 
                           style="background: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Class</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts & Chart Logic -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart Data
    const ctx = document.getElementById('performanceChart');
    const labels = <?php echo json_encode($chart_labels); ?>;
    const dataPoints = <?php echo json_encode($chart_data); ?>;

    // Detect Theme for Chart Colors
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#94A3B8' : '#64748B';
    const gridColor = isDark ? '#334155' : '#E2E8F0';

    if (labels.length > 0) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Avg. Score (%)',
                    data: dataPoints,
                    backgroundColor: 'rgba(79, 70, 229, 0.7)', // Indigo
                    borderColor: '#4F46E5',
                    borderWidth: 1,
                    borderRadius: 6,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: gridColor },
                        ticks: { color: textColor }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor }
                    }
                }
            }
        });
    } else {
        // Show empty state if no data
        ctx.style.display = 'none';
        const container = ctx.parentElement;
        container.innerHTML = '<div class="h-100 d-flex align-items-center justify-content-center text-muted">No grading data available yet.</div>';
    }
</script>

<!-- Close the tags opened in header.php -->
</div>
</div>
</body>
</html>