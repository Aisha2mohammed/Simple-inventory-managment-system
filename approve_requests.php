<?php
require_once 'includes/db_connect.php'; 
require_once 'Security.php'; 

session_start(); 
$security = new Security($conn); 

$security->enforceSessionTimeout(); 
$security->checkAuthentication(); 
$security->checkAuthorization('store_manager'); 

require_once 'InventoryActions.php';

$inventoryActions = new InventoryActions($conn);
$message = "";

// Handle Approve/Reject Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $request_id = $_POST['request_id'];

    if ($action === 'approve') {
        $inventoryActions->approveRequestManager($request_id);
        $message = "Request approved successfully.";
    } elseif ($action === 'reject') {
        $inventoryActions->rejectRequest($request_id);
        $message = "Request rejected successfully.";
    }
}

// Fetch pending requests for the store manager
$pendingRequests = $inventoryActions->getManagerPendingRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Borrow Requests</title>
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
        .message {
            color: green;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .approve-btn {
            background-color: #28a745;
            color: white;
        }
        .reject-btn {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Approve Borrow Requests</h1>

        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

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
                        <th>Actions</th>
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
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn approve-btn">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn reject-btn">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending requests found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
