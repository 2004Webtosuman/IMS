<?php
/**
 * Item Update Handler
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash("Invalid security token.", "danger");
        redirect('viewitem.php');
    }

    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['itemname'] ?? '');
    $price = floatval($_POST['itemprice'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $country = trim($_POST['country'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');
    $threshold = intval($_POST['low_stock_threshold'] ?? 10);
    $old_photo = $_POST['old_photo'] ?? '';

    // Validation
    if ($id <= 0 || empty($name) || $price <= 0 || $quantity < 0 || $category_id <= 0) {
        set_flash("Please fill all required fields correctly.", "danger");
        redirect('editItem.php?id=' . $id);
    }

    // Handle Photo Upload
    $photo_name = $old_photo;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload = upload_photo($_FILES['photo']);
        if (isset($upload['error'])) {
            set_flash($upload['error'], "danger");
            redirect('editItem.php?id=' . $id);
        }
        $photo_name = $upload['success'];
        // Optional: delete old photo file here
    }

    try {
        $stmt = $pdo->prepare("UPDATE items SET name = ?, price = ?, quantity = ?, category_id = ?, country = ?, photo = ?, remarks = ?, low_stock_threshold = ? WHERE id = ?");
        $stmt->execute([$name, $price, $quantity, $category_id, $country, $photo_name, $remarks, $threshold, $id]);

        set_flash("Item updated successfully!");
        redirect('viewitem.php');

    } catch (PDOException $e) {
        error_log($e->getMessage());
        set_flash("An error occurred while updating the item.", "danger");
        redirect('editItem.php?id=' . $id);
    }
} else {
    redirect('viewitem.php');
}
?>
