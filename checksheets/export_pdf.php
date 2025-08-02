<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Autoload DomPDF
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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

// Generate HTML content
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 20px; font-weight: bold; }
        .subtitle { color: #666; margin-bottom: 15px; }
        .date-range { font-style: italic; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Manufacturing Check Sheet Report</div>
        <div class="subtitle">Quality Control System</div>
        <div class="date-range">Date Range: ' . date('M d, Y', strtotime($from_date)) . ' to ' . date('M d, Y', strtotime($to_date)) . '</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Operator</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>';

foreach ($checksheets as $index => $checksheet) {
    $html .= '<tr>';
    $html .= '<td>' . ($index + 1) . '</td>';
    $html .= '<td>' . date('M d, Y', strtotime($checksheet['check_date'])) . '</td>';
    $html .= '<td>' . htmlspecialchars($checksheet['operator_name'] ?? 'N/A') . '</td>';
    $html .= '<td>' . date('M d, Y H:i', strtotime($checksheet['created_at'])) . '</td>';
    $html .= '</tr>';
}

$html .= '
        </tbody>
    </table>
    
    <div class="footer">
        Report generated on ' . date('M d, Y H:i') . '
    </div>
</body>
</html>';

// Configure DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output PDF as download
$dompdf->stream("check_sheets_" . date('Y-m-d') . ".pdf", [
    "Attachment" => true
]);
exit();