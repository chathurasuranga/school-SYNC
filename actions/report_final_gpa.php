<?php
require '../db.php';
require 'fpdf.php';
require 'grading_helper.php';

$class_id = $_POST['class_id'];
$selected_modules = $_POST['modules'] ?? [];

if (empty($selected_modules)) die("No modules selected.");

// 1. Get Selected Module Names
$placeholders = str_repeat('?,', count($selected_modules) - 1) . '?';
$stmt = $pdo->prepare("SELECT id, module_name FROM modules WHERE id IN ($placeholders)");
$stmt->execute($selected_modules);
$modules = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [id => name]

// 2. Fetch All Marks for these modules
// We fetch raw marks. We will average them per module to get Module Score.
$sql = "SELECT s.id, s.name, a.module_id, mk.final_percentage
        FROM students s
        JOIN class_enrollments ce ON s.id = ce.student_id
        JOIN assignments a ON a.class_id = ce.class_id
        LEFT JOIN marks mk ON s.id = mk.student_id AND mk.assignment_id = a.id
        WHERE a.module_id IN ($placeholders)
        ORDER BY s.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($selected_modules);
$data = $stmt->fetchAll();

// Process Data: [Student][Module] = Average Mark
$student_results = [];
foreach ($data as $row) {
    $student_results[$row['id']]['name'] = $row['name'];
    if (!isset($student_results[$row['id']]['modules'][$row['module_id']])) {
        $student_results[$row['id']]['modules'][$row['module_id']] = ['sum'=>0, 'count'=>0];
    }
    if ($row['final_percentage'] !== null) {
        $student_results[$row['id']]['modules'][$row['module_id']]['sum'] += $row['final_percentage'];
        $student_results[$row['id']]['modules'][$row['module_id']]['count']++;
    }
}

// --- PDF ---
$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Final GPA Report',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,10,'Based on selected modules',0,1,'C');
$pdf->Ln(5);

// Header
$pdf->SetFillColor(50, 50, 50);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(60, 10, 'Student Name', 1, 0, 'L', true);

// Module Columns
$col_width = 180 / (count($modules) + 1); // Dynamic width
foreach ($modules as $mid => $name) {
    $pdf->Cell($col_width, 10, substr($name,0,10), 1, 0, 'C', true);
}
$pdf->Cell(30, 10, 'Final GPA', 1, 1, 'C', true);

// Rows
$pdf->SetTextColor(0);
$pdf->SetFont('Arial','',10);

foreach ($student_results as $sid => $stu) {
    $pdf->Cell(60, 10, $stu['name'], 1);
    
    $total_gpv = 0;
    $module_count = 0;

    foreach ($modules as $mid => $mname) {
        $mod_data = $stu['modules'][$mid] ?? ['sum'=>0, 'count'=>0];
        
        // Calculate Module Average
        $mod_avg = ($mod_data['count'] > 0) ? ($mod_data['sum'] / $mod_data['count']) : 0;
        
        // Convert to GPV
        $details = getGradeDetails($mod_avg);
        
        $pdf->Cell($col_width, 10, number_format($details['gpv'], 2) . " (" . $details['grade'] . ")", 1, 0, 'C');
        
        if ($mod_data['count'] > 0) {
            $total_gpv += $details['gpv'];
            $module_count++;
        }
    }

    // Final GPA Calculation (Average of Module GPVs)
    $final_gpa = ($module_count > 0) ? ($total_gpv / $module_count) : 0;
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30, 10, number_format($final_gpa, 2), 1, 1, 'C');
    $pdf->SetFont('Arial','',10);
}

$pdf->Output();
?>