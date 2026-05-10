<?php
$pageTitle = 'Dashboard';
require_once 'includes/db.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if (!is_logged_in()) {
    redirect('loginpage.php');
}

// Fetch stats
$total_items = $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
$low_stock = $pdo->query("SELECT COUNT(*) FROM items WHERE quantity <= low_stock_threshold")->fetchColumn();
$total_purchases = $pdo->query("SELECT COUNT(*) FROM purchase")->fetchColumn();
$active_users = $pdo->query("SELECT COUNT(*) FROM newaccountregistration WHERE status = 'active'")->fetchColumn();

// Low stock items
$low_stock_list = $pdo->query("SELECT i.name, i.quantity, i.low_stock_threshold, c.name as category_name 
                              FROM items i 
                              LEFT JOIN categories c ON i.category_id = c.id 
                              WHERE i.quantity <= i.low_stock_threshold 
                              ORDER BY i.quantity ASC LIMIT 5")->fetchAll();

// Top 5 items for chart
$top_items = $pdo->query("SELECT name, quantity FROM items ORDER BY quantity DESC LIMIT 5")->fetchAll();
$max_qty = !empty($top_items) ? max(array_column($top_items, 'quantity')) : 1;
?>

<div class="header-section" style="margin-bottom: 2rem;">
    <h1>System Overview</h1>
    <p class="text-muted">Welcome back, <?= e($_SESSION['username']) ?>! Here's what's happening today.</p>
</div>

<!-- Metrics Row -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
    <div class="card metric-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="metric-label">Total Inventory SKU</span>
            <i class="fas fa-box" style="color: var(--primary); opacity: 0.2;"></i>
        </div>
        <div class="metric-value"><?= number_format($total_items) ?></div>
        <div style="font-size: 0.75rem; color: var(--success); font-weight: 600;">
            <i class="fas fa-arrow-up"></i> System Active
        </div>
    </div>
    
    <div class="card metric-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="metric-label">Stock Alerts</span>
            <i class="fas fa-exclamation-triangle" style="color: var(--danger); opacity: 0.2;"></i>
        </div>
        <div class="metric-value" style="color: var(--danger);"><?= number_format($low_stock) ?></div>
        <div style="font-size: 0.75rem; color: var(--text-muted);">Items below threshold</div>
    </div>
    
    <div class="card metric-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="metric-label">Total Transactions</span>
            <i class="fas fa-shopping-cart" style="color: var(--success); opacity: 0.2;"></i>
        </div>
        <div class="metric-value"><?= number_format($total_purchases) ?></div>
        <div style="font-size: 0.75rem; color: var(--text-muted);">Purchase history count</div>
    </div>
    
    <div class="card metric-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="metric-label">Platform Users</span>
            <i class="fas fa-users" style="color: var(--primary); opacity: 0.2;"></i>
        </div>
        <div class="metric-value"><?= number_format($active_users) ?></div>
        <div style="font-size: 0.75rem; color: var(--text-muted);">Active staff accounts</div>
    </div>
</div>

<!-- Stock Movement & Recent Activity Row -->
<div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 2rem; margin-bottom: 2.5rem;">
    <!-- Stock Movement Chart (Mock) -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
            <div>
                <h3 style="margin: 0;">Stock Movement</h3>
                <p class="text-muted" style="font-size: 0.75rem;">Inventory flow over the last 7 days</p>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button class="badge" style="background: var(--bg); border: none; cursor: pointer; color: var(--primary);">Weekly</button>
                <button class="badge" style="background: transparent; border: none; cursor: pointer; color: var(--text-muted);">Monthly</button>
            </div>
        </div>
        <div style="padding: 2.5rem 1.5rem; height: 320px; display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; background: linear-gradient(to top, #f9fafb 0%, white 100%);">
            <?php 
            $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $mock_data = [
                ['in' => 40, 'out' => 20], ['in' => 65, 'out' => 45], 
                ['in' => 30, 'out' => 15], ['in' => 55, 'out' => 35], 
                ['in' => 85, 'out' => 60], ['in' => 45, 'out' => 25], 
                ['in' => 70, 'out' => 40]
            ];
            foreach($days as $index => $day): 
                $data = $mock_data[$index];
            ?>
            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                <div style="width: 100%; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 200px; justify-content: flex-end;">
                    <div style="width: 24px; height: <?= $data['in'] ?>%; background: var(--primary); border-radius: 4px 4px 0 0; position: relative;" title="Inbound: <?= $data['in'] ?>"></div>
                    <div style="width: 24px; height: <?= $data['out'] ?>%; background: var(--primary-light); opacity: 0.3; border-radius: 0 0 4px 4px;" title="Outbound: <?= $data['out'] ?>"></div>
                </div>
                <span style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted);"><?= $day ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="padding: 1rem; background: #f9fafb; border-top: 1px solid var(--border); display: flex; gap: 2rem; font-size: 0.75rem; font-weight: 600; justify-content: center;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 10px; height: 10px; background: var(--primary); border-radius: 2px;"></div>
                <span>Inbound (Stock In)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 10px; height: 10px; background: var(--primary-light); opacity: 0.3; border-radius: 2px;"></div>
                <span>Outbound (Stock Out)</span>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card" style="display: flex; flex-direction: column;">
        <h3 style="margin-bottom: 1.5rem;">Recent Activity</h3>
        <div style="flex: 1; display: flex; flex-direction: column; gap: 1.5rem;">
            <div style="display: flex; gap: 1rem;">
                <div style="width: 36px; height: 36px; background: rgba(42, 157, 143, 0.1); color: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-plus-circle" style="font-size: 0.9rem;"></i>
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600;">New Inventory Added</div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.25rem 0;">15 units of "Ergonomic Chair" added by admin.</p>
                    <span style="font-size: 0.7rem; color: var(--text-muted);">2 hours ago</span>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <div style="width: 36px; height: 36px; background: rgba(244, 162, 97, 0.1); color: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-bell" style="font-size: 0.9rem;"></i>
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600;">Low Stock Warning</div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.25rem 0;">"Wireless Mouse" reached threshold (3 units left).</p>
                    <span style="font-size: 0.7rem; color: var(--text-muted);">5 hours ago</span>
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                <div style="width: 36px; height: 36px; background: rgba(30, 58, 95, 0.1); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-truck" style="font-size: 0.9rem;"></i>
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600;">Outbound Shipment</div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.25rem 0;">Order #1024 dispatched to Warehouse A.</p>
                    <span style="font-size: 0.7rem; color: var(--text-muted);">8 hours ago</span>
                </div>
            </div>
        </div>
        <div style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1rem; text-align: center;">
            <a href="#" style="font-size: 0.875rem; font-weight: 600; color: var(--primary); text-decoration: none;">View All Activity</a>
        </div>
    </div>
</div>

<!-- Top Moving Products Table -->
<div class="card" style="padding: 0; overflow: hidden; border: 1px solid var(--border);">
    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0;">Top Moving Products</h3>
        <div style="display: flex; gap: 0.5rem;">
            <span class="badge" style="background: var(--primary); color: white;">All Categories</span>
            <span class="badge" style="background: #f1f5f9; color: var(--text-muted);">Electronics</span>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th style="width: 200px;">Stock Level</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $top_products = $pdo->query("SELECT i.*, c.name as cat_name FROM items i LEFT JOIN categories c ON i.category_id = c.id ORDER BY i.quantity DESC LIMIT 5")->fetchAll();
                foreach($top_products as $item): 
                    $stock_pct = min(100, ($item['quantity'] / ($item['low_stock_threshold'] * 3)) * 100);
                    $progress_color = 'var(--success)';
                    if ($item['quantity'] <= 0) $progress_color = 'var(--danger)';
                    elseif ($item['quantity'] <= $item['low_stock_threshold']) $progress_color = 'var(--warning)';
                ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                <i class="fas fa-image"></i>
                            </div>
                            <div style="font-weight: 600;"><?= e($item['name']) ?></div>
                        </div>
                    </td>
                    <td><span class="badge" style="background: #f1f5f9; color: var(--text-muted);"><?= e($item['cat_name'] ?? 'General') ?></span></td>
                    <td style="font-weight: 600;">NPR <?= number_format($item['price'], 2) ?></td>
                    <td>
                        <div class="progress-container">
                            <div class="progress-bar" style="width: <?= $stock_pct ?>%; background: <?= $progress_color ?>;"></div>
                        </div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 4px;"><?= $item['quantity'] ?> units available</div>
                    </td>
                    <td><span class="badge" style="background: <?= $item['quantity'] > $item['low_stock_threshold'] ? 'rgba(42,157,143,0.1)' : 'rgba(231,111,81,0.1)' ?>; color: <?= $item['quantity'] > $item['low_stock_threshold'] ? 'var(--success)' : 'var(--danger)' ?>;">
                        <?= $item['quantity'] > $item['low_stock_threshold'] ? 'IN STOCK' : 'LOW STOCK' ?>
                    </span></td>
                    <td style="text-align: right;">
                        <a href="fulldetail.php?id=<?= $item['id'] ?>" class="btn" style="padding: 0.5rem; color: var(--text-muted);"><i class="fas fa-ellipsis-v"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
