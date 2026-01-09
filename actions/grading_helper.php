<?php
function getGradeDetails($mark) {
    // Ensure mark is a number
    if (!is_numeric($mark)) return ['grade' => '-', 'gpv' => 0.00];

    if ($mark >= 80) return ['grade' => 'A',  'gpv' => 4.00];
    if ($mark >= 75) return ['grade' => 'A-', 'gpv' => 3.67];
    if ($mark >= 70) return ['grade' => 'B+', 'gpv' => 3.33];
    if ($mark >= 65) return ['grade' => 'B',  'gpv' => 3.00];
    if ($mark >= 60) return ['grade' => 'B-', 'gpv' => 2.67];
    if ($mark >= 55) return ['grade' => 'C+', 'gpv' => 2.33];
    if ($mark >= 50) return ['grade' => 'C',  'gpv' => 2.00];
    if ($mark >= 45) return ['grade' => 'S',  'gpv' => 1.67];
    if ($mark >= 40) return ['grade' => 'D',  'gpv' => 1.33];
    if ($mark >= 35) return ['grade' => 'E',  'gpv' => 1.00];
    return ['grade' => 'F', 'gpv' => 0.00];
}
?>