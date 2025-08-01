<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM checksheets WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Check sheet deleted successfully.');
    } catch (PDOException $e) {
        setFlash('error', 'Error deleting check sheet: ' . $e->getMessage());
    }
    redirect('index.php');
}

// Handle date filter
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d', strtotime('-30 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Build query with date filter
$query = "SELECT c.*, o.name as operator_name 
          FROM checksheets c 
          LEFT JOIN operators o ON c.operator_id = o.id 
          WHERE c.check_date BETWEEN ? AND ? 
          ORDER BY c.check_date DESC, c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$from_date, $to_date]);
$checksheets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
    <?php include '../includes/flash_messages.php'; ?>
    
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Check Sheets</h1>
        <div>
            <a href="create.php" class="btn btn-success btn-custom">
                <i class="fas fa-plus"></i> Create Check Sheet
            </a>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" 
                           value="<?php echo htmlspecialchars($from_date); ?>">
                </div>
                <div class="col-md-4">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" 
                           value="<?php echo htmlspecialchars($to_date); ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="index.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <strong>Showing results from <?php echo date('M d, Y', strtotime($from_date)); ?> 
                    to <?php echo date('M d, Y', strtotime($to_date)); ?></strong>
                </div>
                <div>
                    <div class="btn-group">
                        <a href="export_excel.php?from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>" 
                           class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="export_pdf.php?from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>" 
                           class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Date</th>
                            <th>Operator</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($checksheets) > 0): ?>
                            <?php foreach ($checksheets as $index => $checksheet): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($checksheet['check_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($checksheet['operator_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($checksheet['created_at'])); ?></td>
                                    <td>
                                        <a href="create.php?id=<?php echo $checksheet['id']; ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="create.php?id=<?php echo $checksheet['id']; ?>&edit=1" 
                                           class="btn btn-warning btn-sm btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $checksheet['id']; ?>" 
                                           class="btn btn-danger btn-sm btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this check sheet?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No check sheets found for the selected date range.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>