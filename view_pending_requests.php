
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

srequire_once 'Security.php'; 

session_start(); 
$security = new Security($conn); 

$security->enforceSessionTimeout(); 
$security->checkAuthentication(); 
$security->checkAuthorization('department_head'); 

require_once 'includes/db_connect.php';
require_once 'InventoryActions.php';

$inventoryActions = new InventoryActions($conn);
$pendingRequests = $inventoryActions->getPendingRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Pending Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
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
            margin: 20px 0;
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
        .link-container {
            margin-top: 20px;
            text-align: center;
        }
        .link-container a {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .link-container a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>View Pending Requests</h1>

        <?php if (!empty($pendingRequests)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>User ID</th>
                        <th>Item ID</th>
                        <th>Material</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Quantity</th>
                        <th>Borrow Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingRequests as $request): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['request_id']) ?></td>
                            <td><?= htmlspecialchars($request['user_id']) ?></td>
                            <td><?= htmlspecialchars($request['item_id']) ?></td>
                            <td><?= htmlspecialchars($request['material']) ?></td>
                            <td><?= htmlspecialchars($request['category']) ?></td>
                            <td><?= htmlspecialchars($request['subcategory']) ?></td>
                            <td><?= htmlspecialchars($request['quantity']) ?></td>
                            <td><?= htmlspecialchars($request['borrow_date']) ?></td>
                            <td><?= htmlspecialchars($request['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending requests found.</p>
        <?php endif; ?>

        <div class="link-container">
            <a href="approve_department_head.php">Approve Borrow Requests</a>
        </div>
    </div>
</body>
</html>
