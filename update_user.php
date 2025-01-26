<?php
require_once 'Security.php'; 

session_start(); 
$security = new Security($conn); 

$security->enforceSessionTimeout(); 
$security->checkAuthentication(); 
$security->checkAuthorization('admin'); 


require_once 'User.php';
require_once 'includes/db_connect.php';

$user = new User($conn);

$user_id = $_POST['user_id'] ?? $_GET['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['message'] = "No user selected for update!";
    header("Location: manage_users.php");
    exit;
}

$userDetails = $user->getUserById($user_id);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if ($user->updateUser($user_id, $name, $email, $role)) {
        $_SESSION['message'] = "User updated successfully!";
        header("Location: manage_users.php");
        exit;
    } else {
        $message = "User updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
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
        form input, form select, form button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update User</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?= strpos($message, 'Failed') !== false ? 'error' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($userDetails['user_id']) ?>">

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($userDetails['name']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($userDetails['email']) ?>" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="admin" <?= $userDetails['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="store_manager" <?= $userDetails['role'] === 'store_manager' ? 'selected' : '' ?>>Store Manager</option>
                <option value="store_keeper" <?= $userDetails['role'] === 'store_keeper' ? 'selected' : '' ?>>Store Keeper</option>
                <option value="department_head" <?= $userDetails['role'] === 'department_head' ? 'selected' : '' ?>>Department Head</option>
                <option value="inventory_employee" <?= $userDetails['role'] === 'inventory_employee' ? 'selected' : '' ?>>Inventory Employee</option>
            </select>

            <button type="submit" name="update_user">Update User</button>
        </form>

        <div style="text-align:center; margin-top:20px;">
            <a href="manage_users.php" style="color: #007bff; text-decoration: none;">Back to Manage Users</a>
        </div>
    </div>
</body>
</html>
