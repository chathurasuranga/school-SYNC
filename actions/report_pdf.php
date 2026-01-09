<?php
require '../db.php';
// IMPORTANT: Ensure you have fpdf.php in the actions folder!
require 'fpdf.php';

if (!isset($_GET['assignment_id'])) die("Assignment ID missing");
$assign_id = $_GET['assignment_id'];

// 1. Fetch Assignment & Module Details
$sql = "SELECT a.title, a.created_at, m.module_name, tc.class_name 
        FROM assignments a
        LEFT JOIN modules m ON a.module_id = m.id
        JOIN teacher_classes tc ON a.class_id = tc.id
        WHERE a.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$assign_id]);
$info = $stmt->fetch();

// 2. Fetch Criteria Columns
$stmt = $pdo->prepare("SELECT * FROM criteria WHERE assignment_id = ? ORDER BY id ASC");
$stmt->execute([$assign_id]);
$criteria = $stmt->fetchAll();

// 3. Fetch Students & Marks & Individual Scores
// We fetch students, final %, and a GROUP_CONCAT of their individual scores
$sql = "SELECT s.id, s.name, mk.final_percentage, DATE(mk.graded_at) as graded_date,
        (SELECT GROUP_CONCAT(CONCAT(criteria_id, ':', score)) 
         FROM student_criteria_marks scm 
         WHERE scm.student_id = s.id AND scm.assignment_id = ?) as raw_scores
        FROM students s
        JOIN class_enrollments ce ON s.id = ce.student_id
        JOIN assignments a ON a.class_id = ce.class_id
        LEFT JOIN marks mk ON s.id = mk.student_id AND mk.assignment_id = ?
        WHERE a.id = ?
        ORDER BY s.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$assign_id, $assign_id, $assign_id]);
$students = $stmt->fetchAll();

// --- PDF GENERATION ---

class PDF extends FPDF {
    function Header() {
        // School Name
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'R/KALAWANA NATIONAL SCHOOL',0,1,'C');
        $this->Ln(5);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// Sub-headings
$pdf->SetFont('Arial','B',12);
$pdf->Cell(40, 10, 'Activity:', 0, 0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0, 10, $info['title'], 0, 1);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(40, 10, 'Module:', 0, 0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0, 10, ($info['module_name'] ?? 'General'), 0, 1);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(40, 10, 'Class:', 0, 0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0, 10, $info['class_name'], 0, 1);

$pdf->Ln(5);

// --- TABLE HEADER ---
$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('Arial','B',10);

// Name Column
$pdf->Cell(60, 10, 'Student Name', 1, 0, 'L', true);

// Criteria Columns (Dynamic)
// Calculate width based on remaining space (Total ~190mm - 60name - 25total - 30date)
$crit_count = count($criteria);
$col_width = ($crit_count > 0) ? (75 / $crit_count) : 0; 

foreach($criteria as $c) {
    // Truncate long criteria names
    $c_name = (strlen($c['criteria_name']) > 8) ? substr($c['criteria_name'],0,6).'..' : $c['criteria_name'];
    $pdf->Cell($col_width, 10, $c_name, 1, 0, 'C', true);
}

// Total & Date Columns
$pdf->Cell(25, 10, 'Total %', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Date', 1, 1, 'C', true);

// --- TABLE ROWS ---
$pdf->SetFont('Arial','',10);

foreach($students as $s) {
    $pdf->Cell(60, 10, $s['name'], 1);

    // Parse Raw Scores: "15:4,16:3" -> Map to Array
    $student_scores = [];
    if ($s['raw_scores']) {
        $pairs = explode(',', $s['raw_scores']);
        foreach($pairs as $p) {
            list($cid, $scr) = explode(':', $p);
            $student_scores[$cid] = $scr;
        }
    }

    // Determine Status
    $is_absent = is_null($s['final_percentage']);

    // Criteria Cells
    foreach($criteria as $c) {
        if ($is_absent) {
            $pdf->Cell($col_width, 10, '-', 1, 0, 'C');
        } else {
            $score = $student_scores[$c['id']] ?? '-';
            $pdf->Cell($col_width, 10, $score, 1, 0, 'C');
        }
    }

    // Total % (AB if absent)
    if ($is_absent) {
        $pdf->SetTextColor(255, 0, 0); // Red for AB
        $pdf->Cell(25, 10, 'AB', 1, 0, 'C');
        $pdf->SetTextColor(0);
        $pdf->Cell(30, 10, '-', 1, 1, 'C');
    } else {
        $pdf->Cell(25, 10, round($s['final_percentage']), 1, 0, 'C');
        $pdf->Cell(30, 10, $s['graded_date'], 1, 1, 'C');
    }
}

$pdf->Output();
?>