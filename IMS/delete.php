<?php
/**
 * Item Delete Handler
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    try {
        // Fetch photo to delete file
        $stmt = $pdo->prepare("SELECT photo FROM items WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        
        if ($item && $item['photo']) {
            $filepath = UPLOAD_PATH . $item['photo'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
        $stmt->execute([$id]);

        set_flash("Item deleted successfully!");
    } catch (PDOException $e) {
        error_log($e->getMessage());
        set_flash("Error deleting item.", "danger");
    }
}

redirect('viewitem.php');
?>