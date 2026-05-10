<?php
$pageTitle = 'View Items';
require_once 'includes/db.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if (!is_logged_in()) {
    redirect('loginpage.php');
}

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filters
$search = $_GET['search'] ?? '';
$cat_filter = $_GET['category'] ?? '';
$country_filter = $_GET['country'] ?? '';

// Build Query
$query = "SELECT i.*, c.name as category_name FROM items i LEFT JOIN categories c ON i.category_id = c.id WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (i.name LIKE ? OR i.remarks LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($cat_filter)) {
    $query .= " AND i.category_id = ?";
    $params[] = $cat_filter;
}

if (!empty($country_filter)) {
    $query .= " AND i.country LIKE ?";
    $params[] = "%$country_filter%";
}

// Get total for pagination
$count_stmt = $pdo->prepare($query);
$count_stmt->execute($params);
$total_items = $count_stmt->rowCount();
$total_pages = ceil($total_items / $limit);

// Get paginated results
$query .= " ORDER BY i.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();

// Fetch categories for filter
$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="header-section" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1>Inventory Items</h1>
        <p class="text-muted">Manage your stock and track availability</p>
    </div>
    <a href="insertitem.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Item
    </a>
</div>

<!-- Metrics Summary -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
    <div class="card metric-card" style="margin-bottom: 0;">
        <span class="metric-label">Total SKUs</span>
        <div class="metric-value"><?= $total_items ?></div>
    </div>
    <div class="card metric-card" style="margin-bottom: 0;">
        <span class="metric-label">Low Stock Items</span>
        <div class="metric-value" style="color: var(--danger);"><?= $pdo->query("SELECT COUNT(*) FROM items WHERE quantity <= low_stock_threshold")->fetchColumn() ?></div>
    </div>
    <div class="card metric-card" style="margin-bottom: 0;">
        <span class="metric-label">Out of Stock</span>
        <div class="metric-value" style="color: var(--text-muted);"><?= $pdo->query("SELECT COUNT(*) FROM items WHERE quantity <= 0")->fetchColumn() ?></div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card" style="padding: 1.25rem; background: #f8fafc; border: none;">
    <form method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Search Inventory</label>
            <input type="text" name="search" class="form-control" value="<?= e($search) ?>" placeholder="Search by name, SKU or remarks...">
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Category</label>
            <select name="category" class="form-control">
                <option value="">All Categories</option>
                <?php foreach ($cats as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat_filter == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Location/Country</label>
            <input type="text" name="country" class="form-control" value="<?= e($country_filter) ?>" placeholder="Origin country">
        </div>

        <button type="submit" class="btn btn-primary" style="height: 42px;">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<!-- Items Table -->
<div class="card" style="padding: 0; overflow: hidden; border: 1px solid var(--border);">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 80px;">Media</th>
                    <th>Product Details</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th style="width: 200px;">Inventory Level</th>
                    <th style="text-align: right;">Management</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="6" style="text-align: center; padding: 4rem;">
                        <div style="font-size: 3rem; opacity: 0.1; margin-bottom: 1rem;"><i class="fas fa-search"></i></div>
                        <p class="text-muted">No items found matching your filters.</p>
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): 
                        $stock_pct = min(100, ($item['quantity'] / ($item['low_stock_threshold'] * 3)) * 100);
                        $progress_color = 'var(--success)';
                        if ($item['quantity'] <= 0) $progress_color = 'var(--danger)';
                        elseif ($item['quantity'] <= $item['low_stock_threshold']) $progress_color = 'var(--warning)';
                    ?>
                    <tr>
                        <td>
                            <?php if ($item['photo']): ?>
                                <img src="uploads/photostorage/<?= e($item['photo']) ?>" width="48" height="48" style="object-fit: cover; border-radius: 6px;">
                            <?php else: ?>
                                <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                    <i class="fas fa-box"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text);"><?= e($item['name']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= e($item['country'] ?: 'No Location') ?></div>
                        </td>
                        <td><span class="badge" style="background: #f1f5f9; color: #475569;"><?= e($item['category_name'] ?? 'General') ?></span></td>
                        <td style="font-weight: 600;">NPR <?= number_format($item['price'], 2) ?></td>
                        <td>
                            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 600; margin-bottom: 2px;">
                                <span style="color: <?= $progress_color ?>;"><?= $item['quantity'] ?> units</span>
                                <span class="text-muted"><?= $item['low_stock_threshold'] ?> min</span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: <?= $stock_pct ?>%; background: <?= $progress_color ?>;"></div>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 0.25rem; justify-content: flex-end;">
                                <a href="fulldetail.php?id=<?= $item['id'] ?>" class="btn" style="padding: 0.5rem; background: transparent; color: var(--text-muted);" title="View"><i class="fas fa-eye"></i></a>
                                <a href="editItem.php?id=<?= $item['id'] ?>" class="btn" style="padding: 0.5rem; background: transparent; color: var(--primary);" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="delete.php?id=<?= $item['id'] ?>" class="btn" style="padding: 0.5rem; background: transparent; color: var(--danger);" title="Delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem;">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page-1 ?>&search=<?= e($search) ?>&category=<?= $cat_filter ?>&country=<?= $country_filter ?>" class="btn" style="background: white;"><i class="fas fa-chevron-left"></i> Prev</a>
    <?php endif; ?>
    
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>&search=<?= e($search) ?>&category=<?= $cat_filter ?>&country=<?= $country_filter ?>" class="btn <?= $i == $page ? 'btn-primary' : '' ?>" style="<?= $i != $page ? 'background: white;' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page+1 ?>&search=<?= e($search) ?>&category=<?= $cat_filter ?>&country=<?= $country_filter ?>" class="btn" style="background: white;">Next <i class="fas fa-chevron-right"></i></a>
    <?php endif; ?>
</div>
<?php endif; ?>

<div style="margin-top: 2rem; text-align: right;">
    <a href="export_csv.php" class="btn btn-accent"><i class="fas fa-file-csv"></i> Download CSV</a>
</div>

<?php require_once 'includes/footer.php'; ?>