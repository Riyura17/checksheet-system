<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    redirect('index.php');
}

$id = $_GET['id'];

// Get operator data
$stmt = $pdo->prepare("SELECT * FROM operators WHERE id = ?");
$stmt->execute([$id]);
$operator = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$operator) {
    setFlash('error', 'Operator not found.');
    redirect('index.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $employee_id = trim($_POST['employee_id']);
    $position = trim($_POST['position']);
    $department = trim($_POST['department']);
    
    // Validate required fields
    if (empty($name)) {
        setFlash('error', 'Name is required.');
        redirect('edit.php?id=' . $id);
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE operators SET name = ?, employee_id = ?, position = ?, department = ? WHERE id = ?");
        $stmt->execute([$name, $employee_id, $position, $department, $id]);
        
        setFlash('success', 'Operator updated successfully.');
        redirect('index.php');
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            setFlash('error', 'Employee ID already exists.');
        } else {
            setFlash('error', 'Error updating operator: ' . $e->getMessage());
        }
        redirect('edit.php?id=' . $id);
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
    <?php include '../includes/flash_messages.php'; ?>
    
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Operator</h1>
        <a href="index.php" class="btn btn-secondary btn-custom">Back to List</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Name *</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($operator['name']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="employee_id" class="form-label">Employee ID</label>
                    <input type="text" class="form-control" id="employee_id" name="employee_id" value="<?php echo htmlspecialchars($operator['employee_id']); ?>">
                </div>
                
                <div class="mb-3">
                    <label for="position" class="form-label">Position</label>
                    <input type="text" class="form-control" id="position" name="position" value="<?php echo htmlspecialchars($operator['position']); ?>">
                </div>
                
                <div class="mb-3">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" class="form-control" id="department" name="department" value="<?php echo htmlspecialchars($operator['department']); ?>">
                </div>
                
                <button type="submit" class="btn btn-success">Update Operator</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>