<?php
session_start();
require_once 'includes/db_connect.php';
// require_once 'Security.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// $security = new Security($conn);
// $security->redirectAfterLogin();

// Rate Limiting: Define helper functions
function getLoginAttempts($ip) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
    $stmt->execute([$ip]);
    return $stmt->fetchColumn();
}

function logLoginAttempt($ip) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO login_attempts (ip_address) VALUES (?)");
    $stmt->execute([$ip]);
}

// Track login attempts
$ip_address = $_SERVER['REMOTE_ADDR'];
if (getLoginAttempts($ip_address) >= 5) {
    echo "<script>alert('Too many login attempts. Please try again later.'); window.location.href='login.html';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.location.href='login.html';</script>";
        exit;
    }

    try {
        // Fetch user from database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect to the universal dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            // Log failed login attempt
            logLoginAttempt($ip_address);
            echo "<script>alert('Invalid credentials. Please try again.'); window.location.href='login.html';</script>";
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        echo "<script>alert('An error occurred. Please try again later.'); window.location.href='login.html';</script>";
    }
}
?>

