<?php
/**
 * Create Purchase Record
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor = trim($_POST['vendor'] ?? '');
    $date = date("Y-m-d"); // Fixed date format

    if (empty($vendor)) {
        echo "Vendor name required";
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO purchase (vendor, date) VALUES (?, ?)");
        $stmt->execute([$vendor, $date]);
        
        echo $pdo->lastInsertId(); // Return ID for AJAX
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
}
?>