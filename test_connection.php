<?php
require_once 'includes/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


try {
    $stmt = $conn->query("SHOW TABLES");
    echo "<h1>Connected to the Database Successfully!</h1>";
    echo "<p>Tables in the database:</p><ul>";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "<li>{$row[0]}</li>";
    }
    echo "</ul>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
