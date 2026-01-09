<?php
require '../db.php';
require 'fpdf.php';
require 'grading_helper.php';

$module_id = $_GET['module_id'];
$weight_30_id = $_GET['weight_30_id'];

// 1. Get Module & Activity Info
$stmt = $pdo->prepare("SELECT m.module_name, c.class_name FROM modules m JOIN teacher_classes c ON m.class_id = c.id WHERE m.id = ?");
$stmt->execute([$module_id]);
$info = $stmt->fetch();

// 2. Get All Assignments in this Module
$stmt = $pdo->prepare("SELECT id, title FROM assignments WHERE module_id = ?");
$stmt->execute([$module_id]);
$assignments = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [id => title]

// 3. Fetch Students and Marks
$sql = "SELECT s.id, s.name, mk.assignment_id, mk.final_percentage
        FROM students s
        JOIN class_enrollments ce ON s.id = ce.student_id
        JOIN modules m ON m.class_id = ce.class_id
        LEFT JOIN marks mk ON s.id = mk.student_id
        WHERE m.id = ?
        ORDER BY s.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$module_id]);
$data = $stmt->fetchAll();

// Organize Data by Student
$student_marks = [];
foreach ($data as $row) {
    $student_marks[$row['id']]['name'] = $row['name'];
    if ($row['assignment_id']) {
        $student_marks[$row['id']]['marks'][$row['assignment_id']] = $row['final_percentage'];
    }
}

// --- PDF GENERATION ---
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'Module Final Grade Report',0,1,'C');
        $this->Ln(5);
    }
}

$pdf = new PDF('L','mm','A4'); // Landscape for more columns
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

// Info Block
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8, "Class: " . $info['class_name'] . "  |  Module: " . $info['module_name'], 0, 1);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,8, "Grading Logic: [" . $assignments[$weight_30_id] . "] = 30% | Average of Others = 70%", 0, 1);
$pdf->Ln(5);

// Header Row
$pdf->SetFillColor(220,220,220);
$pdf->Cell(50, 10, 'Student Name', 1, 0, 'L', true);

foreach($assignments as $aid => $title) {
    $headerText = (strlen($title) > 10 ? substr($title,0,8).".." : $title);
    if ($aid == $weight_30_id) $pdf->SetFont('Arial','B',9);
    else $pdf->SetFont('Arial','',9);
    
    $pdf->Cell(25, 10, $headerText . ($aid == $weight_30_id ? " (30%)" : ""), 1, 0, 'C', true);
}
$pdf->SetFont('Arial','B',9);
$pdf->Cell(20, 10, 'Final %', 1, 0, 'C', true);
$pdf->Cell(15, 10, 'Grade', 1, 0, 'C', true);
$pdf->Cell(15, 10, 'GPV', 1, 1, 'C', true);

// Rows
$pdf->SetFont('Arial','',10);

foreach ($student_marks as $sid => $stu) {
    $pdf->Cell(50, 10, $stu['name'], 1);
    
    $mark_30 = 0;
    $sum_70 = 0;
    $count_70 = 0;

    foreach($assignments as $aid => $title) {
        $val = $stu['marks'][$aid] ?? 0;
        $pdf->Cell(25, 10, round($val), 1, 0, 'C');

        if ($aid == $weight_30_id) {
            $mark_30 = $val;
        } else {
            $sum_70 += $val;
            $count_70++;
        }
    }

    // Calculation
    $avg_70 = ($count_70 > 0) ? ($sum_70 / $count_70) : 0;
    $final_mark = ($mark_30 * 0.30) + ($avg_70 * 0.70);
    
    // Get GPV Details
    $details = getGradeDetails(round($final_mark));

    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(20, 10, round($final_mark), 1, 0, 'C');
    $pdf->Cell(15, 10, $details['grade'], 1, 0, 'C');
    $pdf->Cell(15, 10, number_format($details['gpv'], 2), 1, 1, 'C');
    $pdf->SetFont('Arial','',10);
}

$pdf->Output();
?>