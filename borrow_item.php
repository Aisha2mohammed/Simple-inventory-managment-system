<?php
session_start();

// Session Timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

require_once 'includes/db_connect.php';
require_once 'InventoryActions.php';

$inventoryActions = new InventoryActions($conn);

if (!isset($_GET['item_id'])) {
    header("Location: search_item.php");
    exit;
}

$item_id = $_GET['item_id'];
$item = $inventoryActions->getItemById($item_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];
    $result = $inventoryActions->borrowItem($item_id, $user_id, $quantity);

    if ($result['success']) {
        echo "<script>alert('{$result['message']}'); window.location.href='search_item.php';</script>";
    } else {
        echo "<script>alert('{$result['message']}'); window.location.href='borrow_item.php?item_id=$item_id';</script>";
    }
}



?>
<!-- HTML Code Below -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Item</title>
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
        form input, form select, form button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Borrow Item</h1>
        <?php if ($message): ?>
            <p style="color: green;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if ($item): ?>
            <form method="POST">
    <p><strong>Item ID:</strong> <?= htmlspecialchars($item['item_id']) ?></p>
    <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
    <p><strong>Subcategory:</strong> <?= htmlspecialchars($item['subcategory']) ?></p>
    <p><strong>Material:</strong> <?= htmlspecialchars($item['material']) ?></p>

    <label for="user_id">User ID:</label>
    <input type="text" id="user_id" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>" readonly>

    <label for="quantity">Quantity:</label>
    <input type="number" id="quantity" name="quantity" min="1" max="<?= htmlspecialchars($item['quantity']) ?>" required>

    <button type="submit">Borrow</button>
</form>

        <?php else: ?>
            <p>Item not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
