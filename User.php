<?php
require_once 'includes/db_connect.php'; // Ensure the correct path

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllUsers() {
        $stmt = $this->conn->query("SELECT user_id, name, email, role FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUser($name, $email, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $role]);
    }

    public function updateUser($user_id, $name, $email, $role) {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE user_id = ?");
        $stmt->execute([$name, $email, $role, $user_id]);
    }

    public function deleteUser($user_id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }
}
?>

