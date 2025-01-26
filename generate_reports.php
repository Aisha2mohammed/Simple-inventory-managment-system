<?php
require_once 'Security.php'; 

session_start(); 
$security = new Security($conn); 

$security->enforceSessionTimeout(); 
$security->checkAuthentication(); 

require_once 'includes/db_connect.php';
require_once 'InventoryActions.php';

$inventoryActions = new InventoryActions($conn);
$reports = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;
    $reports = $inventoryActions->generateReport($startDate, $endDate);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports</title>
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
        form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            padding: 10px;
            background: #e9ecef;
            border-radius: 5px;
        }
        form input, form button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        form button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        form button:hover {
            background-color: #0056b3;
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
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Reports</h1>
        <form method="POST">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date">
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date">
            <button type="submit">Generate Report</button>
        </form>

        <?php if (!empty($reports)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>User ID</th>
                        <th>Item ID</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Material</th>
                        <th>Quantity</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?= htmlspecialchars($report['request_id']) ?></td>
                            <td><?= htmlspecialchars($report['user_id']) ?></td>
                            <td><?= htmlspecialchars($report['item_id']) ?></td>
                            <td><?= htmlspecialchars($report['category']) ?></td>
                            <td><?= htmlspecialchars($report['subcategory']) ?></td>
                            <td><?= htmlspecialchars($report['material']) ?></td>
                            <td><?= htmlspecialchars($report['quantity']) ?></td>
                            <td><?= htmlspecialchars($report['borrow_date']) ?></td>
                            <td><?= htmlspecialchars($report['return_date']) ?></td>
                            <td><?= htmlspecialchars($report['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No data found for the specified period.</p>
        <?php endif; ?>
    </div>
</body>
</html>
