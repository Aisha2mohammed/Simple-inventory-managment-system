<?php
// Start the session

session_start(); 
// Destroy the session
session_unset();   // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the login page
header("Location: login.html");
exit;  // Ensure the script stops executing after the redirect
?>
