<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Install TCPDF via Composer or download manually
// For this example, we'll use a simple HTML to PDF approach
// In production, you should use a proper library like TCPDF, FPDF, or DomPDF

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

// Create HTML content for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Check Sheets Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
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

// Convert HTML to PDF using DomPDF (you need to install it)
// This is a simplified version - in production, use a proper PDF library

// For this example, we'll just output the HTML with a PDF header
header("Content-type: application/pdf");
header("Content-Disposition: attachment; filename=check_sheets_" . date('Y-m-d') . ".pdf");
header("Cache-Control: max-age=0");

// In a real implementation, you would use:
// $dompdf = new Dompdf();
// $dompdf->loadHtml($html);
// $dompdf->setPaper('A4', 'portrait');
// $dompdf->render();
// $dompdf->stream("check_sheets_" . date('Y-m-d') . ".pdf", array("Attachment" => 1));

// For now, we'll just output a message
echo "<h1>PDF Export Functionality</h1>";
echo "<p>To enable PDF export, please install DomPDF or TCPDF library.</p>";
echo "<p>Run: composer require dompdf/dompdf</p>";
echo "<p>Then update this file to use the library for proper PDF generation.</p>";

exit();