<?php
require 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') exit;

$grade = $_GET['grade'];
$letter = $_GET['letter'];

// Fetch ALL assignments from the system, grouped by their Teacher Class
// This helps the admin pick "Science 6A Term Test" easily.
$sql = "
    SELECT a.id, a.title, tc.class_name, u.full_name as teacher_name
    FROM assignments a
    JOIN teacher_classes tc ON a.class_id = tc.id
    JOIN users u ON tc.teacher_id = u.id
    ORDER BY tc.class_name ASC, a.created_at DESC
";
$assignments = $pdo->query($sql)->fetchAll();

include 'header.php';
?>

<div class="container mt-4" style="max-width: 900px;">
    <h3 class="fw-bold">Configure Report: Grade <?php echo "$grade - $letter"; ?></h3>
    <p class="text-muted">Select the modules (assignments) from the Teacher Classes to include in this report.</p>

    <form action="actions/generate_official_pdf.php" method="POST" target="_blank">
        <input type="hidden" name="grade" value="<?php echo $grade; ?>">
        <input type="hidden" name="letter" value="<?php echo $letter; ?>">
        
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-primary text-white">
                Available Assignments (Grouped by Class)
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                
                <?php 
                // Group logic for display
                $current_class = ""; 
                foreach($assignments as $a): 
                    if($current_class != $a['class_name']):
                        $current_class = $a['class_name'];
                        echo "<h6 class='mt-3 fw-bold text-primary border-bottom pb-1'>" . htmlspecialchars($current_class) . " <span class='text-muted small fw-normal'>(" . htmlspecialchars($a['teacher_name']) . ")</span></h6>";
                    endif;
                ?>
                
                <div class="form-check ms-3 mb-2">
                    <input class="form-check-input" type="checkbox" name="modules[]" value="<?php echo $a['id']; ?>" id="mod_<?php echo $a['id']; ?>">
                    <label class="form-check-label" for="mod_<?php echo $a['id']; ?>">
                        <?php echo htmlspecialchars($a['title']); ?>
                    </label>
                </div>
                
                <?php endforeach; ?>

            </div>
            <div class="card-footer bg-light p-3">
                <button type="submit" class="btn btn-success w-100 fw-bold">Generate Final Report PDF</button>
            </div>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>