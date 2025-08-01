<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center text-light p-3 border-bottom">
            <h5>Check Sheet System</h5>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="/checksheet-system/index.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'operators') !== false) ? 'active' : ''; ?>" href="/checksheet-system/operators/index.php">
                    <i class="fas fa-users"></i> Operators
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'categories') !== false) ? 'active' : ''; ?>" href="/checksheet-system/categories/index.php">
                    <i class="fas fa-list"></i> Categories
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'subcategories') !== false) ? 'active' : ''; ?>" href="/checksheet-system/subcategories/index.php">
                    <i class="fas fa-list-ol"></i> Sub-Categories
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'checksheets') !== false) ? 'active' : ''; ?>" href="/checksheet-system/checksheets/index.php">
                    <i class="fas fa-clipboard-check"></i> Check Sheets
                </a>
            </li>
        </ul>
    </div>
</nav>