<?php
require_once 'Security.php'; 

session_start(); 
$security = new Security($conn); 

$security->enforceSessionTimeout(); 
$security->checkAuthentication(); 

require_once 'includes/db_connect.php';
require_once 'InventoryActions.php';

$inventoryActions = new InventoryActions($conn);
$borrowingHistory = $inventoryActions->getBorrowingHistory($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowing History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Borrowing History</h1>
        <?php if (!empty($borrowingHistory)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Material Type</th>
                        <th>Quantity</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowingHistory as $history): ?>
                        <tr>
                            <td><?= htmlspecialchars($history['item_id']) ?></td>
                            <td><?= htmlspecialchars($history['material']) ?></td>
                            <td><?= htmlspecialchars($history['quantity']) ?></td>
                            <td><?= htmlspecialchars($history['borrow_date']) ?></td>
                            <td><?= htmlspecialchars($history['return_date']) ?></td>
                            <td>
                                <?= strtotime($history['return_date']) < time() ? 'Returned' : 'Not Returned' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No borrowing history found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
