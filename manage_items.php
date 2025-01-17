<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'store_manager') {
    header("Location: login.html");
    exit;
}

require_once 'includes/db_connect.php';

class Inventory {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllItems() {
        $stmt = $this->conn->query("SELECT * FROM inventory_items");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Create instance of Inventory
$inventory = new Inventory($conn);
$items = $inventory->getAllItems();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 1000px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th {
            background-color: #007bff;
            color: white;
            padding: 10px;
        }
        td {
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Inventory</h1>
        <table>
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Material</th>
                    <th>Condition</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_id']) ?></td>
                        <td><?= htmlspecialchars($item['category']) ?></td>
                        <td><?= htmlspecialchars($item['subcategory']) ?></td>
                        <td><?= htmlspecialchars($item['material']) ?></td>
                        <td><?= htmlspecialchars($item['condition']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= htmlspecialchars($item['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
