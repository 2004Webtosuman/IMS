<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    // Get purchase info
    $stmt = $pdo->prepare("SELECT * FROM purchase WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();

    // Get bill items
    $stmt = $pdo->prepare("SELECT * FROM bill WHERE vendor_id = ?");
    $stmt->execute([$id]);
    $items = $stmt->fetchAll();

    if ($p) {
        echo '<div style="margin-bottom: 2rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <p class="text-muted" style="margin-bottom: 0;">Vendor</p>
                    <p style="font-size: 1.25rem; font-weight: 700;">' . e($p['vendor']) . '</p>
                </div>
                <div style="text-align: right;">
                    <p class="text-muted" style="margin-bottom: 0;">Date</p>
                    <p style="font-weight: 600;">' . date('F j, Y', strtotime($p['date'])) . '</p>
                    <p class="text-muted">PUR-' . $p['id'] . '</p>
                </div>
              </div>';
        
        echo '<table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>';
        
        $grand_total = 0;
        foreach ($items as $item) {
            $total = $item['quantity'] * $item['price'];
            $grand_total += $total;
            echo '<tr>
                    <td style="font-weight: 600;">' . e($item['itemname']) . '</td>
                    <td>' . $item['quantity'] . '</td>
                    <td>NPR ' . number_format($item['price'], 2) . '</td>
                    <td style="text-align: right; font-weight: 700;">NPR ' . number_format($total, 2) . '</td>
                  </tr>';
        }
        
        echo '</tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align: right; padding-top: 2rem;">Grand Total</th>
                        <th style="text-align: right; padding-top: 2rem; font-size: 1.5rem; color: var(--primary);">NPR ' . number_format($grand_total, 2) . '</th>
                    </tr>
                </tfoot>
              </table>';
    }
}
?>
