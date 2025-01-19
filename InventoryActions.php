<?php
require_once 'includes/db_connect.php';

class InventoryActions {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Log a borrow request
    public function logBorrowRequest($user_id, $item_id, $quantity) {
        $stmt = $this->conn->prepare("INSERT INTO borrow_requests (user_id, item_id, quantity) VALUES (?, ?, ?)");
        return $stmt->execute([$user_id, $item_id, $quantity]);
    }



    public function searchItems($search_term = null, $category = null, $subcategory = null) {
        $query = "SELECT * FROM inventory_items WHERE 1=1";
        $params = [];
    
        if ($search_term) {
            $query .= " AND (item_id LIKE ? OR material LIKE ?)";
            $params[] = "%$search_term%";
            $params[] = "%$search_term%";
        }
        if ($category) {
            $query .= " AND category = ?";
            $params[] = $category;
        }
        if ($subcategory) {
            $query .= " AND subcategory = ?";
            $params[] = $subcategory;
        }
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    


    // View borrowing history
    public function getBorrowingHistory($user_id = null) {
        $query = "SELECT br.request_id, br.quantity, br.borrow_date, br.return_date, br.status,
                  i.item_id, i.category, i.subcategory, i.material
                  FROM borrow_requests br
                  JOIN inventory_items i ON br.item_id = i.item_id";
        $params = [];
        if ($user_id) {
            $query .= " WHERE br.user_id = ?";
            $params[] = $user_id;
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mark item as returned
    public function markAsReturned($request_id) {
        $stmt = $this->conn->prepare("UPDATE borrow_requests SET status = 'returned', return_date = CURRENT_TIMESTAMP WHERE request_id = ?");
        return $stmt->execute([$request_id]);
    }

    public function getItemById($item_id) {
        $query = "SELECT * FROM inventory_items WHERE item_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$item_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    



    //borrow

    public function borrowItem($item_id, $user_id, $quantity) {
        try {
            // Begin a transaction
            $this->conn->beginTransaction();
    
            // Fetch the current quantity of the item
            $stmt = $this->conn->prepare("SELECT quantity FROM inventory_items WHERE item_id = ?");
            $stmt->execute([$item_id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$item) {
                throw new Exception("Item not found.");
            }
    
            if ($item['quantity'] < $quantity) {
                throw new Exception("Not enough quantity available.");
            }
    
            // Decrease the quantity in the inventory
            $stmt = $this->conn->prepare("UPDATE inventory_items SET quantity = quantity - ? WHERE item_id = ?");
            $stmt->execute([$quantity, $item_id]);
    
            // Insert into borrow_requests table
            $stmt = $this->conn->prepare("INSERT INTO borrow_requests (item_id, user_id, quantity, borrow_date) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$item_id, $user_id, $quantity]);
    
            // Commit the transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback the transaction on failure
            $this->conn->rollBack();
            return false;
        }
    }

    // public function getBorrowingHistory($user_id) {
    //     $sql = "SELECT br.item_id, i.material, br.quantity, br.borrow_date, br.return_date 
    //             FROM borrow_requests br 
    //             JOIN inventory_items i ON br.item_id = i.item_id 
    //             WHERE br.user_id = ? 
    //             ORDER BY br.borrow_date DESC";
    
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->execute([$user_id]);
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }
    
    
}
?>

