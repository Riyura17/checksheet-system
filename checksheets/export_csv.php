<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Get date range
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d', strtotime('-30 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Fetch data
$query = "SELECT c.*, o.name as operator_name 
          FROM checksheets c 
          LEFT JOIN operators o ON c.operator_id = o.id 
          WHERE c.check_date BETWEEN ? AND ? 
          ORDER BY c.check_date DESC, c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$from_date, $to_date]);
$checksheets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set headers to download as Excel (CSV format)
$filename = "check_sheets_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8 (fixes Excel encoding issues)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, ['#', 'Date', 'Operator', 'Created At']);

// Add data rows
foreach ($checksheets as $index => $checksheet) {
    fputcsv($output, [
        $index + 1,
        date('M d, Y', strtotime($checksheet['check_date'])),
        $checksheet['operator_name'] ?? 'N/A',
        date('M d, Y H:i', strtotime($checksheet['created_at']))
    ]);
}

fclose($output);
exit();