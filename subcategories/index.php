<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM subcategories WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Sub-category deleted successfully.');
    } catch (PDOException $e) {
        setFlash('error', 'Error deleting sub-category: ' . $e->getMessage());
    }
    redirect('index.php');
}

// Get all subcategories with category names
$stmt = $pdo->prepare("SELECT s.*, c.name as category_name 
                       FROM subcategories s 
                       LEFT JOIN categories c ON s.category_id = c.id 
                       ORDER BY c.name, s.name");
$stmt->execute();
$subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
    <?php include '../includes/flash_messages.php'; ?>
    
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Sub-Categories</h1>
        <div>
            <a href="add.php" class="btn btn-success btn-custom">
                <i class="fas fa-plus"></i> Add Sub-Category
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
                            <th>Category</th>
                            <th>Sub-Category</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($subcategories) > 0): ?>
                            <?php foreach ($subcategories as $index => $subcategory): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($subcategory['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($subcategory['name']); ?></td>
                                    <td><?php echo htmlspecialchars($subcategory['description']); ?></td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $subcategory['id']; ?>" 
                                           class="btn btn-warning btn-sm btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $subcategory['id']; ?>" 
                                           class="btn btn-danger btn-sm btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this sub-category?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No sub-categories found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>