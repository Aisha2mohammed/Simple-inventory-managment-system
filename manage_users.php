<?php
require_once 'includes/db_connect.php'; 
require_once 'Security.php'; 

session_start(); 
$security = new Security($conn); 

$security->enforceSessionTimeout(); 
$security->checkAuthentication(); 
$security->checkAuthorization('admin'); 

require_once 'User.php'; // Ensure the correct path
require_once 'includes/db_connect.php'; // Ensure the correct path

// Create an instance of the User class, passing the database connection
$user = new User($conn);

function validatePassword($password) {
    // Check if the password meets the specified criteria
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
    return preg_match($pattern, $password);
}

// Handle operations (add, update, delete) with PRG pattern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $password = $_POST['password'];


        // Validate password
        if (!validatePassword($password)) {
            $_SESSION['message'] = "Password must be at least 8 characters long, contain both uppercase and lowercase letters, a number, and a special character.";
            header("Location: manage_users.php");
            exit;
        }

        
    if ($action === 'add') {
        // Hash the password before storing it
        $hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $user->addUser($_POST['name'], $_POST['email'], $hashedPassword, $_POST['role']);
        $_SESSION['message'] = "User added successfully!";
    } elseif ($action === 'update') {
        $user->updateUser($_POST['user_id'], $_POST['name'], $_POST['email'], $_POST['role']);
        $_SESSION['message'] = "User updated successfully!";
    } elseif ($action === 'delete') {
        $user->deleteUser($_POST['user_id']);
        $_SESSION['message'] = "User deleted successfully!";
    }
    // Redirect to the same page to prevent resubmission
    header("Location: manage_users.php");
    exit;
}

// Display the message after redirect and clear it
$message = $_SESSION['message'] ?? "";
unset($_SESSION['message']);

// Fetch all users for display
$users = $user->getAllUsers();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
        h1, h2 {
            text-align: center;
            color: #333;
        }

        form {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
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
            cursor: pointer;

            width: auto;
            display: inline-block;
        }
        form button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr{
            /* height:10px; */
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Users</h1>

        <!-- Display message if set -->
        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Add User Form -->
        <h2>Add New User</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="store_manager">Store Manager</option>
                <option value="store_keeper">Store Keeper</option>
                <option value="department_head">Department Head</option>
                <option value="inventory_employee">Inventory Employee</option>
            </select>
            <button type="submit">Add User</button>
        </form>

        <!-- Existing Users Table -->
        <h2>Existing Users</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <form method="POST" action="update_user.php" style="display:inline;">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                            <button type="submit">Update</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                            <button type="submit" style="background-color: #dc3545; color: white;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
