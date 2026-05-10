<?php
$pageTitle = 'Add Item';
require_once 'includes/db.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if (!is_logged_in()) {
    redirect('loginpage.php');
}

// Fetch categories for dropdown
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<div class="header-section" style="margin-bottom: 2rem;">
    <h1>Add New Inventory Item</h1>
    <p class="text-muted">Register a new item in your stock</p>
</div>

<form action="insert.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Form Details -->
        <div class="card">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="itemname" class="form-control" required placeholder="e.g. Dell Latitude 5420">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Price (NPR)</label>
                    <input type="number" name="itemprice" class="form-control" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="0" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Country of Origin</label>
                    <input type="text" name="country" class="form-control" placeholder="e.g. Nepal">
                </div>

                <div class="form-group">
                    <label class="form-label">Low Stock Threshold</label>
                    <input type="number" name="low_stock_threshold" class="form-control" value="10" min="1">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="4" placeholder="Additional details..."></textarea>
            </div>
            
            <div style="margin-top: 1rem;">
                <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem;">
                    <i class="fas fa-save"></i> Save Item to Inventory
                </button>
            </div>
        </div>

        <!-- Image Upload -->
        <div class="card" style="text-align: center;">
            <h3>Item Photo</h3>
            <div style="margin: 1.5rem 0;">
                <div id="image-preview" style="width: 100%; height: 250px; border: 2px dashed var(--border); border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #fafafa;">
                    <span class="text-muted">No image selected</span>
                </div>
            </div>
            <div class="form-group">
                <input type="file" name="photo" id="photo-input" class="form-control" accept="image/*" required>
            </div>
            <p class="text-muted" style="font-size: 0.8rem;">Allowed: JPG, PNG, GIF. Max 2MB.</p>
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
