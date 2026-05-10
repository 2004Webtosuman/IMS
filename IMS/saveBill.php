<?php
/**
 * Save Bill Item and Update Stock
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id = intval($_POST['vendor_id'] ?? 0);
    $item_id = intval($_POST['item_id'] ?? 0);
    $itemname = $_POST['itemname'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);

    if ($vendor_id <= 0 || $item_id <= 0 || $quantity <= 0) {
        echo "Invalid data";
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Insert into bill table
        $stmt = $pdo->prepare("INSERT INTO bill (vendor_id, item_id, itemname, quantity, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$vendor_id, $item_id, $itemname, $quantity, $price]);

        // 2. Update stock quantity in items table
        $stmt = $pdo->prepare("UPDATE items SET quantity = quantity + ? WHERE id = ?");
        $stmt->execute([$quantity, $item_id]);

        $pdo->commit();
        echo "Success";

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>