<?php
$pageTitle = 'New Purchase';
require_once 'includes/db.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if (!is_logged_in()) {
    redirect('loginpage.php');
}

// Fetch items for selector
$items = $pdo->query("SELECT id, name, price FROM items ORDER BY name ASC")->fetchAll();
?>

<div class="header-section" style="margin-bottom: 2rem;">
    <h1>Purchase Registry</h1>
    <p class="text-muted">Add new items to stock via vendor purchase</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem;">
    <!-- Selector Panel -->
    <div class="card">
        <h3>Item Selection</h3>
        <form id="add-to-cart-form" style="margin-top: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Select Item</label>
                <select name="item" id="item-selector" class="form-control" required>
                    <option value="">-- Choose Item --</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= e($item['name']) ?>" myid="<?= $item['id'] ?>" price="<?= $item['price'] ?>">
                            <?= e($item['name']) ?> (NPR <?= number_format($item['price'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" id="quantity-input" class="form-control" min="1" value="1" required>
            </div>
            
            <button type="button" class="btn btn-accent" style="width: 100%; justify-content: center;" onclick="addtocart()">
                <i class="fas fa-cart-plus"></i> Add to Purchase List
            </button>
        </form>
    </div>

    <!-- Cart Panel -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3>Purchase List</h3>
            <span id="cart-count" class="badge badge-success">0 Items</span>
        </div>
        
        <div id="tbl" class="table-responsive" style="min-height: 200px; border: 1px solid var(--border); border-radius: 8px; background: #fafafa; padding: 1rem;">
            <p class="text-muted" style="text-align: center; margin-top: 4rem;">No items added yet.</p>
        </div>

        <div style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Vendor Name</label>
                <input type="text" name="vendor" id="vendor-input" class="form-control" placeholder="e.g. Acme Corp" required>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
                <div style="font-size: 1.25rem;">
                    <span class="text-muted">Grand Total:</span> 
                    <strong id="grand-total">NPR 0.00</strong>
                </div>
                <button type="button" class="btn btn-primary" onclick="savePurchases()">
                    <i class="fas fa-check-circle"></i> Confirm & Save Purchase
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Completion Modal -->
<div id="completion-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 3000; justify-content: center; align-items: center;">
    <div class="card" style="max-width: 600px; width: 90%; text-align: center; padding: 3rem;">
        <div style="font-size: 4rem; color: var(--success); margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Purchase Complete!</h2>
        <p class="text-muted" style="margin-bottom: 2rem;">The stock has been updated and the record saved.</p>
        <div id="bill-summary" style="margin-bottom: 2rem; text-align: left;"></div>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Bill</button>
            <button class="btn" style="background: var(--bg);" onclick="location.reload()">New Purchase</button>
        </div>
    </div>
</div>

<script src="assets/js/myscript.js"></script>
<?php require_once 'includes/footer.php'; ?>