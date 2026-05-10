<?php
$pageTitle = 'Product Details';
require_once 'includes/db.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if (!is_logged_in()) {
    redirect('loginpage.php');
}

$id = $_GET['id'] ?? 0;

// Handle Quick Adjustment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adjust_stock'])) {
    if (validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $type = $_POST['type']; // 'in' or 'out'
        $amount = (int)$_POST['amount'];
        if ($amount > 0) {
            $new_qty = ($type === 'in') ? "quantity + $amount" : "quantity - $amount";
            $stmt = $pdo->prepare("UPDATE items SET quantity = $new_qty WHERE id = ?");
            $stmt->execute([$id]);
            set_flash("Stock updated successfully!");
            redirect("fulldetail.php?id=$id");
        }
    }
}

$stmt = $pdo->prepare("SELECT i.*, c.name as category_name FROM items i LEFT JOIN categories c ON i.category_id = c.id WHERE i.id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    set_flash("Product not found.", "danger");
    redirect('viewitem.php');
}

$status_class = 'badge-success';
$status_text = 'IN STOCK';
if ($item['quantity'] <= 0) {
    $status_class = 'badge-danger';
    $status_text = 'OUT OF STOCK';
} elseif ($item['quantity'] <= $item['low_stock_threshold']) {
    $status_class = 'badge-warning';
    $status_text = 'LOW STOCK';
}
?>

<div class="header-section" style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
            <a href="viewitem.php" style="color: var(--text-muted); text-decoration: none;"><i class="fas fa-arrow-left"></i> Products</a>
            <span style="color: var(--border);">/</span>
            <span style="font-weight: 600;">#<?= $item['id'] ?></span>
        </div>
        <h1><?= e($item['name']) ?></h1>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <button class="btn" style="background: white; border: 1px solid var(--border);" onclick="window.print()"><i class="fas fa-print"></i> Print SKU</button>
        <a href="editItem.php?id=<?= $item['id'] ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Product</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 320px 1fr 300px; gap: 2rem;">
    <!-- Media Section -->
    <div class="card" style="padding: 1rem;">
        <div style="border-radius: 12px; overflow: hidden; background: #f1f5f9; height: 300px; display: flex; align-items: center; justify-content: center;">
            <?php if ($item['photo']): ?>
                <img src="uploads/photostorage/<?= e($item['photo']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <i class="fas fa-box" style="font-size: 4rem; color: #cbd5e1;"></i>
            <?php endif; ?>
        </div>
        <div style="margin-top: 1.5rem;">
            <span class="badge <?= $status_class ?>" style="font-size: 0.9rem; padding: 0.5rem 1rem; width: 100%; text-align: center; display: block;">
                <?= $status_text ?>
            </span>
        </div>
    </div>

    <!-- Details Section -->
    <div class="card">
        <h3 style="margin-bottom: 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">General Information</h3>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
            <div>
                <label class="form-label" style="text-transform: uppercase; font-size: 0.7rem; color: var(--text-muted);">Category</label>
                <div style="font-weight: 600; font-size: 1.1rem;"><?= e($item['category_name'] ?? 'Uncategorized') ?></div>
            </div>
            <div>
                <label class="form-label" style="text-transform: uppercase; font-size: 0.7rem; color: var(--text-muted);">Selling Price</label>
                <div style="font-weight: 700; font-size: 1.1rem; color: var(--primary);">NPR <?= number_format($item['price'], 2) ?></div>
            </div>
            <div>
                <label class="form-label" style="text-transform: uppercase; font-size: 0.7rem; color: var(--text-muted);">Origin / Country</label>
                <div style="font-weight: 600;"><?= e($item['country'] ?: 'Global') ?></div>
            </div>
            <div>
                <label class="form-label" style="text-transform: uppercase; font-size: 0.7rem; color: var(--text-muted);">Added On</label>
                <div style="font-weight: 600;"><?= date('M d, Y', strtotime($item['created_at'])) ?></div>
            </div>
        </div>

        <div style="margin-top: 2.5rem;">
            <label class="form-label" style="text-transform: uppercase; font-size: 0.7rem; color: var(--text-muted);">Description / Remarks</label>
            <div style="line-height: 1.6; color: #4b5563; background: #f9fafb; padding: 1.5rem; border-radius: 12px; margin-top: 0.5rem;">
                <?= nl2br(e($item['remarks'])) ?: 'No description provided.' ?>
            </div>
        </div>
    </div>

    <!-- Inventory Actions -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="card" style="background: var(--primary); color: white;">
            <label style="opacity: 0.7; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Current Stock</label>
            <div style="font-size: 2.5rem; font-weight: 800; margin: 0.5rem 0;"><?= $item['quantity'] ?></div>
            <div style="font-size: 0.8rem; opacity: 0.8;">Threshold: <?= $item['low_stock_threshold'] ?> units</div>
        </div>

        <div class="card">
            <h4 style="margin-bottom: 1.25rem;">Stock Adjustment</h4>
            <form action="" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="form-group">
                    <select name="type" class="form-control" style="background: #f1f5f9;">
                        <option value="in">📦 Stock In (+) </option>
                        <option value="out">🚚 Stock Out (-)</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="number" name="amount" class="form-control" placeholder="Quantity" min="1" required>
                </div>
                <button type="submit" name="adjust_stock" class="btn btn-accent" style="width: 100%; height: 44px; font-weight: 700;">
                    Update Stock
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>