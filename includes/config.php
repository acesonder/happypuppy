<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'happypuppy');

// Application configuration
define('SITE_URL', 'http://localhost');
define('SITE_NAME', 'HappyPuppy');

// Session configuration
ini_set('session.cookie_httponly', 1);
session_start();

// Database connection
class Database {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if (self::$connection->connect_error) {
                    die("Connection failed: " . self::$connection->connect_error);
                }
                
                self::$connection->set_charset("utf8mb4");
            } catch (Exception $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
}

// Helper functions
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        redirect('/index.php');
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $db = Database::getConnection();
    $userId = $_SESSION['user_id'];
    
    $stmt = $db->prepare("SELECT id, email, name, phone, street, city, zip_code, role FROM users WHERE id = ? AND is_active = TRUE");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

function flashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatTime($time) {
    return date('g:i A', strtotime($time));
}

function formatDateTime($datetime) {
    return date('M d, Y g:i A', strtotime($datetime));
}
?>
