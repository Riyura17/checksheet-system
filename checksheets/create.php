<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$operators = getOperators($pdo);
$categories = getCategories($pdo);

$checksheet_id = isset($_GET['id']) ? $_GET['id'] : null;
$is_edit = isset($_GET['edit']) && $_GET['edit'] == 1;

// Initialize variables
$operator_id = '';
$check_date = date('Y-m-d');
$check_items = [];

// If editing or viewing existing check sheet
if ($checksheet_id) {
    $stmt = $pdo->prepare("SELECT * FROM checksheets WHERE id = ?");
    $stmt->execute([$checksheet_id]);
    $checksheet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$checksheet) {
        setFlash('error', 'Check sheet not found.');
        redirect('index.php');
    }
    
    $operator_id = $checksheet['operator_id'];
    $check_date = $checksheet['check_date'];
    
    // Get existing check items
    $stmt = $pdo->prepare("SELECT * FROM check_items WHERE checksheet_id = ?");
    $stmt->execute([$checksheet_id]);
    $existing_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($existing_items as $item) {
        $check_items[$item['subcategory_id']] = [
            'status' => $item['status'],
            'description' => $item['description']
        ];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operator_id = $_POST['operator_id'];
    $check_date = $_POST['check_date'];
    
    // Validate required fields
    if (empty($operator_id) || empty($check_date)) {
        setFlash('error', 'Operator and Date are required.');
        redirect($_SERVER['REQUEST_URI']);
    }
    
    try {
        $pdo->beginTransaction();
        
        // Insert or update checksheet
        if ($checksheet_id && !$is_edit) {
            // Viewing mode, just redirect
            $pdo->commit();
            redirect($_SERVER['REQUEST_URI']);
        } elseif ($checksheet_id) {
            // Update existing checksheet
            $stmt = $pdo->prepare("UPDATE checksheets SET operator_id = ?, check_date = ? WHERE id = ?");
            $stmt->execute([$operator_id, $check_date, $checksheet_id]);
            
            $current_checksheet_id = $checksheet_id;
        } else {
            // Insert new checksheet
            $stmt = $pdo->prepare("INSERT INTO checksheets (operator_id, check_date) VALUES (?, ?)");
            $stmt->execute([$operator_id, $check_date]);
            $current_checksheet_id = $pdo->lastInsertId();
        }
        
        // Clear existing check items if editing
        if ($checksheet_id && $is_edit) {
            $stmt = $pdo->prepare("DELETE FROM check_items WHERE checksheet_id = ?");
            $stmt->execute([$checksheet_id]);
        } elseif (!$checksheet_id) {
            // For new checksheets, we don't need to delete anything
            $current_checksheet_id = $pdo->lastInsertId();
        }
        
        // Insert check items
        if (isset($_POST['subcategory'])) {
            foreach ($_POST['subcategory'] as $subcategory_id => $data) {
                $status = $data['status'] ?? 'no';
                $description = $data['description'] ?? '';
                
                $stmt = $pdo->prepare("INSERT INTO check_items (checksheet_id, subcategory_id, status, description) VALUES (?, ?, ?, ?)");
                $stmt->execute([$current_checksheet_id, $subcategory_id, $status, $description]);
            }
        }
        
        $pdo->commit();
        
        if ($is_edit) {
            setFlash('success', 'Check sheet updated successfully.');
            redirect('index.php');
        } else {
            setFlash('success', 'Check sheet saved successfully.');
            redirect('index.php');
        }
        
    } catch (PDOException $e) {
        $pdo->rollback();
        setFlash('error', 'Error saving check sheet: ' . $e->getMessage());
        redirect($_SERVER['REQUEST_URI']);
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
    <?php include '../includes/flash_messages.php'; ?>
    
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><?php echo $checksheet_id ? ($is_edit ? 'Edit' : 'View') : 'Create'; ?> Check Sheet</h1>
        <a href="index.php" class="btn btn-secondary btn-custom">Back to List</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="operator_id" class="form-label">Operator *</label>
                        <select class="form-select" id="operator_id" name="operator_id" <?php echo (!$is_edit && $checksheet_id) ? 'disabled' : 'required'; ?>>
                            <option value="">Select Operator</option>
                            <?php foreach ($operators as $operator): ?>
                                <option value="<?php echo $operator['id']; ?>" <?php echo ($operator['id'] == $operator_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($operator['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!$is_edit && $checksheet_id): ?>
                            <input type="hidden" name="operator_id" value="<?php echo $operator_id; ?>">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="check_date" class="form-label">Date *</label>
                        <input type="date" class="form-control" id="check_date" name="check_date" 
                               value="<?php echo $check_date; ?>" <?php echo (!$is_edit && $checksheet_id) ? 'disabled' : 'required'; ?>>
                        <?php if (!$is_edit && $checksheet_id): ?>
                            <input type="hidden" name="check_date" value="<?php echo $check_date; ?>">
                        <?php endif; ?>
                    </div>
                </div>
                
                <hr>
                
                <h5>Check Items</h5>
                <p class="text-muted">Please check all items below:</p>
                
                <div class="check-items-container">
                    <?php $itemNumber = 1; ?>
                    <?php foreach ($categories as $category): ?>
                        <?php 
                        $subcategories = getSubcategories($pdo, $category['id']);
                        if (count($subcategories) == 0) continue;
                        ?>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                <?php if ($category['description']): ?>
                                    <small class="text-muted d-block"><?php echo htmlspecialchars($category['description']); ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php foreach ($subcategories as $subcategory): ?>
                                    <div class="check-item-row">
                                        <div class="row align-items-center">
                                            <div class="col-md-1">
                                                <strong><?php echo $itemNumber++; ?>.</strong>
                                            </div>
                                            <div class="col-md-4">
                                                <strong><?php echo htmlspecialchars($subcategory['name']); ?></strong>
                                                <?php if ($subcategory['description']): ?>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($subcategory['description']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" 
                                                           name="subcategory[<?php echo $subcategory['id']; ?>][status]" 
                                                           id="status_yes_<?php echo $subcategory['id']; ?>" 
                                                           value="yes" 
                                                           <?php echo (isset($check_items[$subcategory['id']]) && $check_items[$subcategory['id']]['status'] == 'yes') ? 'checked' : ''; ?>
                                                           <?php echo (!$is_edit && $checksheet_id) ? 'disabled' : ''; ?>>
                                                    <label class="form-check-label" for="status_yes_<?php echo $subcategory['id']; ?>">
                                                        Yes
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" 
                                                           name="subcategory[<?php echo $subcategory['id']; ?>][status]" 
                                                           id="status_no_<?php echo $subcategory['id']; ?>" 
                                                           value="no" 
                                                           <?php echo (!isset($check_items[$subcategory['id']]) || $check_items[$subcategory['id']]['status'] == 'no') ? 'checked' : ''; ?>
                                                           <?php echo (!$is_edit && $checksheet_id) ? 'disabled' : ''; ?>>
                                                    <label class="form-check-label" for="status_no_<?php echo $subcategory['id']; ?>">
                                                        No
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <textarea class="form-control form-control-sm" 
                                                          name="subcategory[<?php echo $subcategory['id']; ?>][description]" 
                                                          placeholder="Enter description if needed..." 
                                                          <?php echo (!$is_edit && $checksheet_id) ? 'disabled' : ''; ?>><?php 
                                                          echo isset($check_items[$subcategory['id']]) ? htmlspecialchars($check_items[$subcategory['id']]['description']) : ''; 
                                                          ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if ($itemNumber == 1): ?>
                        <div class="alert alert-info">No categories or sub-categories found. Please add some first.</div>
                    <?php endif; ?>
                </div>
                
                <?php if ($is_edit || !$checksheet_id): ?>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-success">
                            <?php echo $checksheet_id ? 'Update' : 'Save'; ?> Check Sheet
                        </button>
                        <a href="index.php" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>