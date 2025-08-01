<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM operators WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Operator deleted successfully.');
    } catch (PDOException $e) {
        setFlash('error', 'Error deleting operator: ' . $e->getMessage());
    }
    redirect('index.php');
}

// Get all operators
$stmt = $pdo->query("SELECT * FROM operators ORDER BY name");
$operators = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
    <?php include '../includes/flash_messages.php'; ?>
    
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Operators</h1>
        <div>
            <a href="add.php" class="btn btn-success btn-custom">
                <i class="fas fa-plus"></i> Add Operator
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Employee ID</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($operators) > 0): ?>
                            <?php foreach ($operators as $index => $operator): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($operator['name']); ?></td>
                                    <td><?php echo htmlspecialchars($operator['employee_id']); ?></td>
                                    <td><?php echo htmlspecialchars($operator['position']); ?></td>
                                    <td><?php echo htmlspecialchars($operator['department']); ?></td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $operator['id']; ?>" 
                                           class="btn btn-warning btn-sm btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $operator['id']; ?>" 
                                           class="btn btn-danger btn-sm btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this operator?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No operators found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>