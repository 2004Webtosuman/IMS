<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-boxes-stacked"></i>
        <span>IMS Pro</span>
    </div>
    
    <nav class="sidebar-nav">
        <a href="home.php" class="nav-item <?= $current_page == 'home.php' ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="viewitem.php" class="nav-item <?= in_array($current_page, ['viewitem.php', 'insertitem.php', 'editItem.php']) ? 'active' : '' ?>">
            <i class="fas fa-inventory"></i>
            <span>Inventory</span>
        </a>

        <a href="categories.php" class="nav-item <?= $current_page == 'categories.php' ? 'active' : '' ?>">
            <i class="fas fa-tags"></i>
            <span>Categories</span>
        </a>
        
        <a href="purchaseForm.php" class="nav-item <?= in_array($current_page, ['purchaseForm.php', 'purchaseHistory.php']) ? 'active' : '' ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>Purchases</span>
        </a>
        
        <?php if (is_admin()): ?>
        <a href="displayNewUser.php" class="nav-item <?= $current_page == 'displayNewUser.php' ? 'active' : '' ?>">
            <i class="fas fa-user-shield"></i>
            <span>Staff Management</span>
        </a>

        <!-- Added Settings -->
        <a href="#" class="nav-item">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        <?php endif; ?>
    </nav>

    <div style="padding: 1rem;">
        <a href="insertitem.php" class="btn btn-primary" style="width: 100%; padding: 0.8rem; border-radius: 10px; font-weight: 700; background: var(--primary-light);">
            <i class="fas fa-plus"></i> Create Entry
        </a>
    </div>
    
    <div class="sidebar-footer">
        <div class="user-avatar" style="width: 40px; height: 40px; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--primary);">
            <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
        </div>
        <div class="user-info" style="flex: 1;">
            <div style="font-weight: 500; font-size: 0.9rem;"><?= e($_SESSION['username'] ?? 'Guest') ?></div>
            <a href="logout.php" style="color: rgba(255,255,255,0.5); font-size: 0.8rem; text-decoration: none;">Logout</a>
        </div>
    </div>
</aside>
<main class="main-content">
