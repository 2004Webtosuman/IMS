<?php
$pageTitle = 'Edit Item';
require_once 'includes/db.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if (!is_logged_in()) {
    redirect('loginpage.php');
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    set_flash("Item not found.", "danger");
    redirect('viewitem.php');
}

// Fetch categories
$stmt_cat = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt_cat->fetchAll();
?>

<div class="header-section" style="margin-bottom: 2rem;">
    <h1>Edit Item: <?= e($item['name']) ?></h1>
    <p class="text-muted">Update item information and stock levels</p>
</div>

<form action="update.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
    <input type="hidden" name="id" value="<?= $item['id'] ?>">
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Form Details -->
        <div class="card">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="itemname" class="form-control" value="<?= e($item['name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $item['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Price (NPR)</label>
                    <input type="number" name="itemprice" class="form-control" step="0.01" min="0" value="<?= $item['price'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="0" value="<?= $item['quantity'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Country of Origin</label>
                    <input type="text" name="country" class="form-control" value="<?= e($item['country']) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Low Stock Threshold</label>
                    <input type="number" name="low_stock_threshold" class="form-control" value="<?= $item['low_stock_threshold'] ?>" min="1">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="4"><?= e($item['remarks']) ?></textarea>
            </div>
            
            <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Item
                </button>
                <a href="viewitem.php" class="btn" style="background: #e2e8f0; color: var(--text);">Cancel</a>
            </div>
        </div>

        <!-- Image Upload -->
        <div class="card" style="text-align: center;">
            <h3>Item Photo</h3>
            <div style="margin: 1.5rem 0;">
                <div id="image-preview" style="width: 100%; height: 250px; border: 1px solid var(--border); border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #fafafa;">
                    <?php if ($item['photo']): ?>
                        <img src="uploads/photostorage/<?= e($item['photo']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <span class="text-muted">No image</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <input type="file" name="photo" id="photo-input" class="form-control" accept="image/*">
                <input type="hidden" name="old_photo" value="<?= $item['photo'] ?>">
            </div>
            <p class="text-muted" style="font-size: 0.8rem;">Leave blank to keep current photo.</p>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#photo-input').change(function() {
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    $('#image-preview').html(`<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`);
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>