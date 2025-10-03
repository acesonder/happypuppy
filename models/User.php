<?php
require_once __DIR__ . '/../includes/config.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function register($email, $password, $name, $phone, $street, $city, $zipCode) {
        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return false;
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $this->db->prepare("INSERT INTO users (email, password, name, phone, street, city, zip_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $email, $hashedPassword, $name, $phone, $street, $city, $zipCode);
        
        return $stmt->execute();
    }
    
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT id, email, password, name, role FROM users WHERE email = ? AND is_active = TRUE");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return false;
        }
        
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_destroy();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, email, name, phone, street, city, zip_code, role, is_active, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function getAll() {
        $result = $this->db->query("SELECT id, email, name, phone, role, is_active, created_at FROM users ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function update($id, $name, $phone, $street, $city, $zipCode) {
        $stmt = $this->db->prepare("UPDATE users SET name = ?, phone = ?, street = ?, city = ?, zip_code = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $phone, $street, $city, $zipCode, $id);
        
        return $stmt->execute();
    }
    
    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $id);
        
        return $stmt->execute();
    }
}
?>
