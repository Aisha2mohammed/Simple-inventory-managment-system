<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$name = htmlspecialchars($_SESSION['name']);
$role = htmlspecialchars($_SESSION['role']);

$role_actions = [
    'admin' => [
        'Manage Users' => 'manage_users.php',
    ],
    'store_manager' => [
        'Generate Reports' => 'generate_reports.php',
        'Approve Borrow Requests' => 'approve_requests.php',
        'Search Item' => 'search_item.php',
        'Borrow Item' => 'borrow_item.php',
        'View Borrowing History' => 'view_borrowing_history.php',
        'View Profile' => 'view_profile.php', // Add this

    ],
    'store_keeper' => [
        'Manage Items' => 'manage_items.php',
        'Search Item' => 'search_item.php',
        'Borrow Item' => 'borrow_item.php',
        'View Borrowing History' => 'view_borrowing_history.php',
        'View Profile' => 'view_profile.php', // Add this

    ],
    'department_head' => [
        'Approve Borrow Requests' => 'approve_department_head.php',
        'View Pending Requests' => 'view_pending_requests.php',
        'Search Item' => 'search_item.php',
        'Borrow Item' => 'borrow_item.php',
        'View Borrowing History' => 'view_borrowing_history.php',
        'View Profile' => 'view_profile.php', // Add this

    ],
    'inventory_employee' => [
        'Search Item' => 'search_item.php',
        'Borrow Item' => 'borrow_item.php',
        'View Borrowing History' => 'view_borrowing_history.php',
        'View Profile' => 'view_profile.php', // Add this

    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        .role-section {
            margin-top: 20px;
            padding: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .logout {
            margin-top: 20px;
            text-align: center;
        }
        .logout a {
            color: #fff;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .logout a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Dashboard</h1>
        <p>Hello, <strong><?php echo $name; ?></strong>!</p>

        <div class="role-section">
            <h3>Your Role: <?php echo $role; ?></h3>
            <?php if (isset($role_actions[$role])): ?>
                <ul>
                    <?php foreach ($role_actions[$role] as $action_name => $action_link): ?>
                        <li><a href="<?php echo htmlspecialchars($action_link); ?>"><?php echo htmlspecialchars($action_name); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Your role does not have specific permissions yet. Please contact the administrator.</p>
            <?php endif; ?>
        </div>

        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
