<?php
require_once 'includes/db_connect.php';

class Inventory {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addItem($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO inventory_items 
            (item_id, category, subcategory, material, `condition`, quantity, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['item_id'],
            $data['category'],
            $data['subcategory'],
            $data['material'],
            $data['condition'],
            $data['quantity'],
            $data['status']
        ]);
    }
    

    public function updateItem($data) {
        $stmt = $this->conn->prepare("
            UPDATE inventory_items 
            SET 
                category = :category,
                subcategory = :subcategory,
                material = :material,
                `condition` = :condition,
                quantity = :quantity
            WHERE item_id = :item_id
        ");
        return $stmt->execute([
            ':category' => $data['category'],
            ':subcategory' => $data['subcategory'],
            ':material' => $data['material'],
            ':condition' => $data['condition'],
            ':quantity' => $data['quantity'],
            ':item_id' => $data['item_id'],
        ]);
    }
    
    
    
    public function getItemById($item_id) {
        $stmt = $this->conn->prepare("SELECT * FROM inventory_items WHERE item_id = ?");
        $stmt->execute([$item_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    public function deleteItem($item_id) {
        $stmt = $this->conn->prepare("DELETE FROM inventory_items WHERE item_id = ?");
        return $stmt->execute([$item_id]);
    }

    public function getAllItems() {
        $stmt = $this->conn->query("SELECT * FROM inventory_items");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

