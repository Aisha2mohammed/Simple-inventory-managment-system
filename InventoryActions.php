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
    
            // Insert the borrow request
            $stmt = $this->conn->prepare("
                INSERT INTO borrow_requests (user_id, item_id, quantity, borrow_date, return_date)
                VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 6 MONTH))
            ");
            $stmt->execute([$user_id, $item_id, $quantity]);
    
            // Commit the transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback the transaction on failure
            $this->conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    public function generateReport($startDate = null, $endDate = null) {
        $query = "SELECT br.request_id, br.user_id, br.item_id, br.quantity, br.borrow_date, br.return_date, br.status,
                  i.category, i.subcategory, i.material
                  FROM borrow_requests br
                  JOIN inventory_items i ON br.item_id = i.item_id
                  WHERE 1=1";
        $params = [];
    
        if ($startDate) {
            $query .= " AND br.borrow_date >= ?";
            $params[] = $startDate;
        }
    
        if ($endDate) {
            $query .= " AND br.borrow_date <= ?";
            $params[] = $endDate;
        }
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
     
    //  Approve a borrow request
     public function approveRequest($request_id) {
        $stmt = $this->conn->prepare("UPDATE borrow_requests SET status = 'approved' WHERE request_id = ?");
        return $stmt->execute([$request_id]);
    }
    


    // public function approveRequest($request_id) {
    //     $stmt = $this->conn->prepare("
    //         UPDATE borrow_requests 
    //         SET status = 'pending_manager' 
    //         WHERE request_id = ?
    //     ");
    //     return $stmt->execute([$request_id]);
    // }
    
// Reject a borrow request
public function rejectRequest($request_id) {
    try {
        $stmt = $this->conn->prepare("UPDATE borrow_requests SET status = 'rejected' WHERE request_id = ?");
        $stmt->execute([$request_id]);
        return true;
    } catch (PDOException $e) {
        // Log or handle the error for debugging
        error_log("Error rejecting request: " . $e->getMessage());
        return false;
    }
}

// Get all pending borrow requests
public function getPendingRequests() {
    $stmt = $this->conn->query("
        SELECT br.request_id, br.user_id, br.item_id, br.quantity, br.borrow_date, br.status, 
               i.material, i.category, i.subcategory
        FROM borrow_requests br
        JOIN inventory_items i ON br.item_id = i.item_id
        WHERE br.status = 'pending'
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function approveRequestFinal($request_id) {
    try {
        $this->conn->beginTransaction();

        // Fetch the request details
        $stmt = $this->conn->prepare("SELECT item_id, quantity FROM borrow_requests WHERE request_id = ?");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            throw new Exception("Request not found.");
        }

        $item_id = $request['item_id'];
        $quantity = $request['quantity'];

        // Reduce inventory
        $stmt = $this->conn->prepare("UPDATE inventory_items SET quantity = quantity - ? WHERE item_id = ?");
        $stmt->execute([$quantity, $item_id]);

        // Update request status
        $stmt = $this->conn->prepare("UPDATE borrow_requests SET status = 'approved' WHERE request_id = ?");
        $stmt->execute([$request_id]);

        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("Error in final approval: " . $e->getMessage());
        return false;
    }
}

public function getManagerPendingRequests() {
    $stmt = $this->conn->query("
        SELECT br.request_id, br.user_id, br.item_id, br.quantity, br.borrow_date, br.status, 
               i.material, i.category, i.subcategory
        FROM borrow_requests br
        JOIN inventory_items i ON br.item_id = i.item_id
        WHERE br.status = 'approved' AND br.manager_approval = 'pending'
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// public function getManagerPendingRequests() {
//     $stmt = $this->conn->query("
//         SELECT br.request_id, br.user_id, br.item_id, br.quantity, br.borrow_date, 
//                i.material, i.category, i.subcategory
//         FROM borrow_requests br
//         JOIN inventory_items i ON br.item_id = i.item_id
//         WHERE br.status = 'pending_manager'
//     ");
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
// }



public function approveRequestManager($request_id) {
    try {
        $this->conn->beginTransaction();
        
        // Get request details
        $stmt = $this->conn->prepare("SELECT item_id, quantity FROM borrow_requests WHERE request_id = ?");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            throw new Exception("Request not found.");
        }

        // Reduce item quantity
        $stmt = $this->conn->prepare("UPDATE inventory_items SET quantity = quantity - ? WHERE item_id = ?");
        $stmt->execute([$request['quantity'], $request['item_id']]);

        // Update request status
        $stmt = $this->conn->prepare("UPDATE borrow_requests SET manager_approval = 'approved', status = 'approved' WHERE request_id = ?");
        $stmt->execute([$request_id]);

        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("Error approving request: " . $e->getMessage());
        return false;
    }
}




}
?>

