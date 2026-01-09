<?php
// download_template.php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="student_upload_template.csv"');

$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, ['Index No', 'Full Name', 'Grade', 'Class Letter']);

// Add a sample row to guide the user
fputcsv($output, ['ST001', 'John Doe', '6', 'A']);

fclose($output);
exit;
