<?php
require '../db.php';
require 'grading_helper.php'; 
require 'fpdf.php'; // Ensure path is correct

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['modules'])) {
    die("Error: No modules selected.");
}

$grade = $_POST['grade'];
$letter = $_POST['letter'];
$module_ids = $_POST['modules'];

// 1. Fetch Students in this Official Class (No enrollment table needed here)
$stmt = $pdo->prepare("SELECT id, name FROM students WHERE grade = ? AND class_letter = ? ORDER BY name ASC");
$stmt->execute([$grade, $letter]);
$students = $stmt->fetchAll();

// 2. Fetch Selected Module Info
$placeholders = implode(',', array_fill(0, count($module_ids), '?'));
$stmt = $pdo->prepare("
    SELECT a.id, a.title, tc.class_name 
    FROM assignments a 
    JOIN teacher_classes tc ON a.class_id = tc.id
    WHERE a.id IN ($placeholders)
");
$stmt->execute($module_ids);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Fetch Marks for these students & modules
$stmt = $pdo->prepare("
    SELECT student_id, assignment_id, final_percentage 
    FROM marks 
    WHERE assignment_id IN ($placeholders)
");
$stmt->execute($module_ids);
$raw_marks = $stmt->fetchAll();

// Map Marks: [student_id][assignment_id] = percentage
$marksMap = [];
foreach ($raw_marks as $m) {
    $marksMap[$m['student_id']][$m['assignment_id']] = $m['final_percentage'];
}

// --- GENERATE PDF ---
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Official Academic Report', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        // We use GLOBALS to access grade/letter inside the class
        $this->Cell(0, 8, 'Grade ' . $GLOBALS['grade'] . ' - ' . $GLOBALS['letter'], 0, 1, 'C');
        $this->Line(10, 30, 200, 30);
        $this->Ln(10);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'SchoolSync Generated Report', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);

foreach ($students as $student) {
    $sid = $student['id'];

    // Student Header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(0, 10, strtoupper($student['name']), 1, 1, 'L', true);

    // Table Headers
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(80, 8, 'Subject / Module', 1);
    $pdf->Cell(50, 8, 'Class', 1); // Which class it came from
    $pdf->Cell(20, 8, 'Mark', 1);
    $pdf->Cell(20, 8, 'Grade', 1);
    $pdf->Cell(20, 8, 'GPV', 1, 1);

    $pdf->SetFont('Arial', '', 9);
    
    $total_gpv = 0;
    $count = 0;

    foreach ($modules as $mod) {
        $mid = $mod['id'];
        $mark = $marksMap[$sid][$mid] ?? null;
        
        // Get Grade Details
        $details = getGradeDetails($mark); // From grading_helper.php

        $pdf->Cell(80, 8, utf8_decode($mod['title']), 1);
        $pdf->Cell(50, 8, utf8_decode($mod['class_name']), 1);
        $pdf->Cell(20, 8, ($mark !== null ? $mark : '-'), 1);
        $pdf->Cell(20, 8, $details['grade'], 1);
        $pdf->Cell(20, 8, number_format($details['gpv'], 2), 1, 1);

        if (is_numeric($mark)) {
            $total_gpv += $details['gpv'];
            $count++;
        }
    }

    // Final Calculation
    $final_gpa = ($count > 0) ? ($total_gpv / $count) : 0;
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(170, 10, 'Overall GPA:', 1, 0, 'R');
    $pdf->Cell(20, 10, number_format($final_gpa, 2), 1, 1, 'C', true);
    
    $pdf->Ln(10);
}

$pdf->Output('I', 'Report_Grade_' . $grade . $letter . '.pdf');
?>