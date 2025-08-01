<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    redirect('index.php');
}

$id = $_GET['id'];

// Get subcategory data
$stmt = $pdo->prepare("SELECT * FROM subcategories WHERE id = ?");
$stmt->execute([$id]);
$subcategory = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subcategory) {
    setFlash('error', 'Sub-category not found.');
    redirect('index.php');
}

$categories = getCategories($pdo);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    // Validate required fields
    if (empty($category_id) || empty($name)) {
        setFlash('error', 'Category and Name are required.');
        redirect('edit.php?id=' . $id);
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE subcategories SET category_id = ?, name = ?, description = ? WHERE id = ?");
        $stmt->execute([$category_id, $name, $description, $id]);
        
        setFlash('success', 'Sub-category updated successfully.');
        redirect('index.php');
    } catch (PDOException $e) {
        setFlash('error', 'Error updating sub-category: ' . $e->getMessage());
        redirect('edit.php?id=' . $id);
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
    <?php include '../includes/flash_messages.php'; ?>
    
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Sub-Category</h1>
        <a href="index.php" class="btn btn-secondary btn-custom">Back to List</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="category_id" class="form-label">Category *</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $subcategory['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name *</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($subcategory['name']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($subcategory['description']); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Update Sub-Category</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>