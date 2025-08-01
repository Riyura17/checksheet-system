<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $employee_id = trim($_POST['employee_id']);
    $position = trim($_POST['position']);
    $department = trim($_POST['department']);
    
    // Validate required fields
    if (empty($name)) {
        setFlash('error', 'Name is required.');
        redirect('add.php');
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO operators (name, employee_id, position, department) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $employee_id, $position, $department]);
        
        setFlash('success', 'Operator added successfully.');
        redirect('index.php');
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            setFlash('error', 'Employee ID already exists.');
        } else {
            setFlash('error', 'Error adding operator: ' . $e->getMessage());
        }
        redirect('add.php');
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
    <?php include '../includes/flash_messages.php'; ?>
    
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Add Operator</h1>
        <a href="index.php" class="btn btn-secondary btn-custom">Back to List</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Name *</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                
                <div class="mb-3">
                    <label for="employee_id" class="form-label">Employee ID</label>
                    <input type="text" class="form-control" id="employee_id" name="employee_id">
                </div>
                
                <div class="mb-3">
                    <label for="position" class="form-label">Position</label>
                    <input type="text" class="form-control" id="position" name="position">
                </div>
                
                <div class="mb-3">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" class="form-control" id="department" name="department">
                </div>
                
                <button type="submit" class="btn btn-success">Save Operator</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>