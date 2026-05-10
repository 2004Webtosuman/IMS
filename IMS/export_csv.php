<?php
/**
 * Export Inventory to CSV
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!is_logged_in()) redirect('loginpage.php');

$stmt = $pdo->query("SELECT i.id, i.name, c.name as category, i.price, i.quantity, i.country, i.created_at 
                    FROM items i LEFT JOIN categories c ON i.category_id = c.id 
                    ORDER BY i.name ASC");
$items = $stmt->fetchAll();

$filename = "inventory_export_" . date('Y-m-d') . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Header
fputcsv($output, ['ID', 'Item Name', 'Category', 'Price', 'Quantity', 'Country', 'Added Date']);

// Data
foreach ($items as $item) {
    fputcsv($output, [
        $item['id'],
        $item['name'],
        $item['category'] ?? 'Uncategorized',
        $item['price'],
        $item['quantity'],
        $item['country'],
        $item['created_at']
    ]);
}

fclose($output);
exit;
?>
