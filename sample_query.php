<?php
require_once 'includes/db_connect.php';

$stmt = $conn->query("SELECT * FROM users");
$users = $stmt->fetchAll();

echo "<h1>User List</h1>";
if ($users) {
    foreach ($users as $user) {
        echo "ID: {$user['user_id']}, Name: {$user['name']}, Email: {$user['email']}, Role: {$user['role']}<br>";
    }
} else {
    echo "No users found.";
}
?>
