<?php
/**
 * Activate User Handler
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!is_admin()) {
    set_flash("Unauthorized action.", "danger");
    redirect('home.php');
}

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE newaccountregistration SET status = 'active' WHERE id = ?");
        $stmt->execute([$id]);
        set_flash("User activated successfully.");
    } catch (PDOException $e) {
        set_flash("Error activating user.", "danger");
    }
}

redirect('displayNewUser.php');
?>
