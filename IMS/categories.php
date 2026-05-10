<?php
$pageTitle = 'Categories';
require_once 'includes/db.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if (!is_logged_in()) {
    redirect('loginpage.php');
}

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    if (validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $name = trim($_POST['name'] ?? '');
        if (!empty($name)) {
            // CHECK FOR DUPLICATES
            $check = $pdo->prepare("SELECT id FROM categories WHERE LOWER(name) = LOWER(?)");
            $check->execute([$name]);
            
            if ($check->rowCount() > 0) {
                set_flash("Category '$name' already exists!", "warning");
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                $stmt->execute([$name]);
                set_flash("Category added successfully!");
                // REDIRECT TO PREVENT DUPLICATES ON RELOAD
                redirect('categories.php');
            }
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    set_flash("Category deleted.");
    redirect('categories.php');
}

// Fetch Categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<div class="header-section" style="margin-bottom: 2.5rem;">
    <h1>Category Management</h1>
    <p class="text-muted">Organize your products into logical groups</p>
</div>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 2.5rem; align-items: start;">
    <!-- Add Form -->
    <div class="card" style="position: sticky; top: 2rem;">
        <h3 style="margin-bottom: 1.5rem;">Create New Category</h3>
        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Electronics" required autofocus>
            </div>
            <button type="submit" name="add_category" class="btn btn-primary" style="width: 100%; height: 48px;">
                <i class="fas fa-plus"></i> Save Category
            </button>
        </form>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
            <p style="font-size: 0.8rem; color: var(--text-muted);">
                <i class="fas fa-info-circle"></i> Categories help you filter products in the inventory view and dashboard reports.
            </p>
        </div>
    </div>

    <!-- Category List -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">System Categories</h3>
            <span class="badge" style="background: var(--bg); color: var(--primary);"><?= count($categories) ?> Total</span>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Category Name</th>
                        <th>Created Date</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="4" style="text-align: center; padding: 4rem;">
                            <div style="font-size: 3rem; opacity: 0.1; margin-bottom: 1rem;"><i class="fas fa-folder-open"></i></div>
                            <p class="text-muted">No categories created yet.</p>
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="text-muted">#<?= $cat['id'] ?></td>
                            <td>
                                <div style="font-weight: 600; font-size: 1rem; color: var(--primary);"><?= e($cat['name']) ?></div>
                            </td>
                            <td class="text-muted"><?= date('M d, Y', strtotime($cat['created_at'])) ?></td>
                            <td style="text-align: right;">
                                <a href="?delete=<?= $cat['id'] ?>" class="btn" style="padding: 0.5rem; background: transparent; color: var(--danger);" 
                                   onclick="return confirm('Are you sure? This will not delete products in this category.')" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
