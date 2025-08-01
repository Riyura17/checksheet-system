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

// Create Excel file
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="check_sheets_' . date('Y-m-d') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

echo '<table border="1">';
echo '<tr>';
echo '<th>#</th>';
echo '<th>Date</th>';
echo '<th>Operator</th>';
echo '<th>Created At</th>';
echo '</tr>';

foreach ($checksheets as $index => $checksheet) {
    echo '<tr>';
    echo '<td>' . ($index + 1) . '</td>';
    echo '<td>' . date('M d, Y', strtotime($checksheet['check_date'])) . '</td>';
    echo '<td>' . htmlspecialchars($checksheet['operator_name'] ?? 'N/A') . '</td>';
    echo '<td>' . date('M d, Y H:i', strtotime($checksheet['created_at'])) . '</td>';
    echo '</tr>';
}

echo '</table>';
exit();