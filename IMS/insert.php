<?php
/**
 * Item Insertion Handler
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash("Invalid security token.", "danger");
        redirect('insertitem.php');
    }

    $name = trim($_POST['itemname'] ?? '');
    $price = floatval($_POST['itemprice'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $country = trim($_POST['country'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');
    $threshold = intval($_POST['low_stock_threshold'] ?? 10);

    // Validation
    if (empty($name) || $price <= 0 || $quantity < 0 || $category_id <= 0) {
        set_flash("Please fill all required fields correctly. Price and Quantity must be positive.", "danger");
        redirect('insertitem.php');
    }

    // Handle Photo Upload
    $photo_name = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload = upload_photo($_FILES['photo']);
        if (isset($upload['error'])) {
            set_flash($upload['error'], "danger");
            redirect('insertitem.php');
        }
        $photo_name = $upload['success'];
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO items (name, price, quantity, category_id, country, photo, remarks, low_stock_threshold) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $quantity, $category_id, $country, $photo_name, $remarks, $threshold]);

        set_flash("Item added to inventory successfully!");
        redirect('viewitem.php');

    } catch (PDOException $e) {
        error_log($e->getMessage());
        set_flash("An error occurred while saving the item.", "danger");
        redirect('insertitem.php');
    }
} else {
    redirect('insertitem.php');
}
?>