<?php
require_once 'Security.php'; 
require_once 'includes/db_connect.php';

session_start(); 
$security = new Security($conn); 

$security->enforceSessionTimeout(); 

$message = "";

// Step 1: Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    // Password validation
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
        $message = "
        <div class='message error'>
            <ul>
                <li>Password must be at least 8 characters long.</li>
                <li>Contain uppercase and lowercase letters.</li>
                <li>Contain a number.</li>
                <li>Contain a special character (e.g., @$!%*?&).</li>
            </ul>
        </div>";
    } else {
        try {
            // Step 2: Check if the email exists in the database
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Step 3: Check if the user has submitted new password fields
                if ($new_password && $confirm_password) {
                    if ($new_password === $confirm_password) {
                        // Update the password in the database
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                        // Reset the password
                        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                        $update_stmt->execute([$hashed_password, $email]);

                        $message = "<div class='message success'>Password reset successfully! <a href='login.html'>Go to Login</a></div>";
                    } else {
                        $message = "<div class='message error'>Error: Passwords do not match.</div>";
                    }
                }
            } else {
                $message = "<div class='message error'>Error: Email not found.</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='message error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            color: #9d9c9c;
            border-radius: 5px;
        }
        .success {
            background-color: #28a745;
        }
        .error {
            background-color: #f4f4f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>

        <?= $message ?>

        <form method="POST">
            <label for="email">Enter your email:</label>
            <input type="email" id="email" name="email" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
<?php
require_once 'Security.php'; 
require_once 'includes/db_connect.php';

session_start(); 
$security = new Security($conn); 

$security->enforceSessionTimeout(); 

$message = "";

// Step 1: Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    // Password validation
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
        $message = "
        <div class='message error'>
            <ul>
                <li>Password must be at least 8 characters long.</li>
                <li>Contain uppercase and lowercase letters.</li>
                <li>Contain a number.</li>
                <li>Contain a special character (e.g., @$!%*?&).</li>
            </ul>
        </div>";
    } else {
        try {
            // Step 2: Check if the email exists in the database
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Step 3: Check if the user has submitted new password fields
                if ($new_password && $confirm_password) {
                    if ($new_password === $confirm_password) {
                        // Update the password in the database
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                        // Reset the password
                        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                        $update_stmt->execute([$hashed_password, $email]);

                        $message = "<div class='message success'>Password reset successfully! <a href='login.html'>Go to Login</a></div>";
                    } else {
                        $message = "<div class='message error'>Error: Passwords do not match.</div>";
                    }
                }
            } else {
                $message = "<div class='message error'>Error: Email not found.</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='message error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            color: #9d9c9c;
            border-radius: 5px;
        }
        .success {
            background-color: #28a745;
        }
        .error {
            background-color: #f4f4f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>

        <?= $message ?>

        <form method="POST">
            <label for="email">Enter your email:</label>
            <input type="email" id="email" name="email" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>

