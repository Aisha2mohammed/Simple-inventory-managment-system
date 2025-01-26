<?php
require_once 'Security.php'; 

session_start(); 
$security = new Security($conn); 

$security->enforceSessionTimeout(); 
$security->checkAuthentication(); 

require_once 'includes/db_connect.php';

// Fetch User Details
try {
    $stmt = $conn->prepare("SELECT user_id, name, email, role FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Error fetching user details: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
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
        p {
            font-size: 16px;
            line-height: 1.5;
        }
        .link {
            display: block;
            margin-top: 20px;
            text-align: center;
        }
        .link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Profile</h1>
        <?php if ($user): ?>
            <p><strong>User ID:</strong> <?= htmlspecialchars($user['user_id']) ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <?php else: ?>
            <p>Error fetching user details. Please try again later.</p>
        <?php endif; ?>
        
        <div class="link">
            <a href="view_borrowing_history.php">View Borrowing History</a>
        </div>
    </div>
</body>
</html>
