<?php
require_once __DIR__ . '/../includes/config.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function getAll($availableOnly = false) {
        $query = "SELECT * FROM products";
        if ($availableOnly) {
            $query .= " WHERE is_available = TRUE";
        }
        $query .= " ORDER BY name ASC";
        
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function create($name, $description, $category, $quantity, $unit, $safetyInfo, $image = '') {
        $stmt = $this->db->prepare("INSERT INTO products (name, description, category, quantity, unit, safety_info, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $description, $category, $quantity, $unit, $safetyInfo, $image);
        
        return $stmt->execute();
    }
    
    public function update($id, $name, $description, $category, $quantity, $unit, $safetyInfo, $isAvailable) {
        $stmt = $this->db->prepare("UPDATE products SET name = ?, description = ?, category = ?, quantity = ?, unit = ?, safety_info = ?, is_available = ? WHERE id = ?");
        $stmt->bind_param("sssissii", $name, $description, $category, $quantity, $unit, $safetyInfo, $isAvailable, $id);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    public function updateQuantity($id, $quantity) {
        $stmt = $this->db->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $id);
        
        return $stmt->execute();
    }
    
    public function decrementQuantity($id, $amount) {
        $stmt = $this->db->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
        $stmt->bind_param("iii", $amount, $id, $amount);
        
        return $stmt->execute();
    }
}
?>
