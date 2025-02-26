<?php

class Security {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection; // Pass the database connection to the class
    }

    // 1. Check if the user is logged in
    public function checkAuthentication() {
        if (!isset($_SESSION['user_id'])) {
            // Store the current URL before redirecting
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            header("Location: login.html");
            exit;
        }
    }

    // 2. Check if the user has the required role
    public function checkAuthorization($requiredRole) {
        if ($_SESSION['role'] !== $requiredRole) {
            // Redirect unauthorized users
            header("Location: unauthorized.html");
            exit;
        }
    }

    // 3. Validate input to prevent XSS and sanitize data
    public function sanitizeInput($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    // 4. Hash the password
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // 5. Verify the password
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    // 6. Protect against SQL injection by using prepared statements (example method)
    public function preparedQuery($query, $params) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 7. Burn the session to prevent unauthorized access after logout
    public function destroySession() {
        session_unset();
        session_destroy();
        header("Location: login.html");
        exit;
    }

    // 8. Enforce session timeout
    public function enforceSessionTimeout() {
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 180)) {
            session_unset();
            session_destroy();
            header("Location: login.html");
            exit;
        }
        $_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time
        
    }

    // 9. Redirect if user tries to access a page directly (unauthorized access)
    public function restrictDirectAccess() {
        if (basename($_SERVER['PHP_SELF']) !== 'login.html' && !isset($_SESSION['user_id'])) {
            header("Location: login.html");
            exit;
        }
    }

    public function enforcePageSession() {
        // Generate a new token for every page load
        if (!isset($_SESSION['page_token'])) {
            $_SESSION['page_token'] = bin2hex(random_bytes(16)); // Generate a unique token
        }
    
        // If the user tries to navigate forward (by missing token), destroy the session
        if (!isset($_POST['current_token']) || $_POST['current_token'] !== $_SESSION['page_token']) {
            session_unset();
            session_destroy();
            header("Location: login.html");
            exit;
        }
    
        // Update token for the next page
        $_SESSION['page_token'] = bin2hex(random_bytes(16));
    }
    

// class PageToken {
    // The session key for storing the token
    // private $sessionKey = 'page_token';

    // // Constructor to start the session
    // public function __construct() {
    //     if (session_status() == PHP_SESSION_NONE) {
    //         session_start();
    //     }
    //     $this->generateToken();
    // }

    // // Generate a new token if one doesn't exist in the session
    // public function generateToken() {
    //     if (empty($_SESSION[$this->sessionKey])) {
    //         $_SESSION[$this->sessionKey] = bin2hex(random_bytes(32)); // Generate a random token
    //     }
    // }

    // // Get the token from the session
    // public function getToken() {
    //     return $_SESSION[$this->sessionKey];
    // }

    // // Validate the submitted token
    // public function validateToken($submittedToken) {
    //     if ($submittedToken !== $_SESSION[$this->sessionKey]) {
    //         die('Invalid form submission');
    //     }
    //     $this->clearToken(); // Clear the token after validation
    // }

    // // Clear the token after it's used or for security reasons
    // public function clearToken() {
    //     unset($_SESSION[$this->sessionKey]);
    // }
// }


}
?>

    
    

