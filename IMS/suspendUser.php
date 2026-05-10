<?php
/**
 * Suspend User Handler
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
        $stmt = $pdo->prepare("UPDATE newaccountregistration SET status = 'suspended' WHERE id = ?");
        $stmt->execute([$id]);
        set_flash("User suspended.");
    } catch (PDOException $e) {
        set_flash("Error suspending user.", "danger");
    }
}

redirect('displayNewUser.php');
?>
