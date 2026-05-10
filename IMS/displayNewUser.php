<?php
$pageTitle = 'User Management';
require_once 'includes/db.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if (!is_logged_in()) {
    redirect('loginpage.php');
}

// Check admin role
if (!is_admin()) {
    set_flash("Access denied. Admin only.", "danger");
    redirect('home.php');
}

// Fetch users
$status_filter = $_GET['status'] ?? 'all';
$query = "SELECT * FROM newaccountregistration WHERE 1=1";
$params = [];

if ($status_filter !== 'all') {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<div class="header-section" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1>User Management</h1>
        <p class="text-muted">Manage system access and account statuses</p>
    </div>
</div>

<div class="card" style="margin-bottom: 2rem; padding: 1rem;">
    <div style="display: flex; gap: 1rem;">
        <a href="?status=all" class="btn <?= $status_filter == 'all' ? 'btn-primary' : '' ?>" style="<?= $status_filter != 'all' ? 'background: #f1f5f9; color: var(--text);' : '' ?>">All Users</a>
        <a href="?status=pending" class="btn <?= $status_filter == 'pending' ? 'btn-primary' : '' ?>" style="<?= $status_filter != 'pending' ? 'background: #f1f5f9; color: var(--text);' : '' ?>">Pending</a>
        <a href="?status=active" class="btn <?= $status_filter == 'active' ? 'btn-primary' : '' ?>" style="<?= $status_filter != 'active' ? 'background: #f1f5f9; color: var(--text);' : '' ?>">Active</a>
        <a href="?status=suspended" class="btn <?= $status_filter == 'suspended' ? 'btn-primary' : '' ?>" style="<?= $status_filter != 'suspended' ? 'background: #f1f5f9; color: var(--text);' : '' ?>">Suspended</a>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-responsive">
        <table style="margin-top: 0;">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): 
                    $badge = 'badge-warning';
                    if ($user['status'] == 'active') $badge = 'badge-success';
                    if ($user['status'] == 'suspended') $badge = 'badge-danger';
                ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 40px; height: 40px; background: #eef2f7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--primary);">
                                <?= strtoupper(substr($user['username'], 0, 1)) ?>
                            </div>
                            <div style="font-weight: 600;"><?= e($user['username']) ?></div>
                        </div>
                    </td>
                    <td><span class="badge" style="background: #eef2f7; color: var(--primary);"><?= strtoupper($user['role']) ?></span></td>
                    <td><span class="badge <?= $badge ?>"><?= ucfirst($user['status']) ?></span></td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <?php if ($user['status'] !== 'active'): ?>
                                <a href="activateUser.php?id=<?= $user['id'] ?>" class="btn" style="padding: 0.4rem 0.6rem; background: rgba(42, 157, 143, 0.1); color: var(--success);" title="Activate" onclick="return confirm('Activate this user?')">
                                    <i class="fas fa-check"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($user['status'] !== 'suspended'): ?>
                                <a href="suspendUser.php?id=<?= $user['id'] ?>" class="btn" style="padding: 0.4rem 0.6rem; background: rgba(231, 111, 81, 0.1); color: var(--danger);" title="Suspend" onclick="return confirm('Suspend this user?')">
                                    <i class="fas fa-ban"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
