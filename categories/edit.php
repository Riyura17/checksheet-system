<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    redirect('index.php');
}

$id = $_GET['id'];

// Get category data
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    setFlash('error', 'Category not found.');
    redirect('index.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    // Validate required fields
    if (empty($name)) {
        setFlash('error', 'Name is required.');
        redirect('edit.php?id=' . $id);
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description, $id]);
        
        setFlash('success', 'Category updated successfully.');
        redirect('index.php');
    } catch (PDOException $e) {
        setFlash('error', 'Error updating category: ' . $e->getMessage());
        redirect('edit.php?id=' . $id);
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
    <?php include '../includes/flash_messages.php'; ?>
    
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Category</h1>
        <a href="index.php" class="btn btn-secondary btn-custom">Back to List</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Name *</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Update Category</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>