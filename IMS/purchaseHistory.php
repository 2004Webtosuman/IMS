<?php
$pageTitle = 'Purchase History';
require_once 'includes/db.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if (!is_logged_in()) {
    redirect('loginpage.php');
}

// Fetch all purchases
$stmt = $pdo->query("SELECT * FROM purchase ORDER BY date DESC, id DESC");
$purchases = $stmt->fetchAll();
?>

<div class="header-section" style="margin-bottom: 2rem;">
    <h1>Purchase History</h1>
    <p class="text-muted">Track all incoming stock and vendor records</p>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table style="margin-top: 0;">
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Vendor Name</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total Value</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($purchases)): ?>
                    <tr><td colspan="6" style="text-align: center; padding: 3rem;">No purchase records found.</td></tr>
                <?php else: ?>
                    <?php foreach ($purchases as $p): 
                        // Get summary stats for this purchase
                        $stats = $pdo->prepare("SELECT COUNT(*) as item_count, SUM(quantity * price) as total_value FROM bill WHERE vendor_id = ?");
                        $stats->execute([$p['id']]);
                        $summary = $stats->fetch();
                    ?>
                    <tr>
                        <td><strong>PUR-<?= $p['id'] ?></strong></td>
                        <td style="font-weight: 600; color: var(--primary);"><?= e($p['vendor']) ?></td>
                        <td><?= date('M d, Y', strtotime($p['date'])) ?></td>
                        <td><?= $summary['item_count'] ?> items</td>
                        <td><strong>NPR <?= number_format($summary['total_value'], 2) ?></strong></td>
                        <td style="text-align: right;">
                            <button class="btn" style="background: var(--bg); color: var(--primary);" onclick="viewBill(<?= $p['id'] ?>)">
                                <i class="fas fa-file-invoice"></i> View Bill
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bill Modal -->
<div id="bill-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 3000; justify-content: center; align-items: center;">
    <div class="card" style="max-width: 700px; width: 90%; max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Purchase Receipt</h2>
            <button class="btn" onclick="$('#bill-modal').hide()"><i class="fas fa-times"></i></button>
        </div>
        <div id="bill-content">
            <!-- Loaded via JS -->
        </div>
        <div style="margin-top: 2rem; text-align: right;">
            <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Receipt</button>
        </div>
    </div>
</div>

<script>
function viewBill(id) {
    $.get('get_bill_details.php?id=' + id, function(data) {
        $('#bill-content').html(data);
        $('#bill-modal').css('display', 'flex');
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
