<?php
session_start();

// Check if session variables are set
if (isset($_SESSION['user_id'], $_SESSION['email'], $_SESSION['role'], $_SESSION['name'])) {
    echo "User ID: " . htmlspecialchars($_SESSION['user_id']) . "<br>";
    echo "Email: " . htmlspecialchars($_SESSION['email']) . "<br>";
    echo "Role: " . htmlspecialchars($_SESSION['role']) . "<br>";
    echo "Name: " . htmlspecialchars($_SESSION['name']) . "<br>";
} else {
    echo "Session variables are not set.";
}
