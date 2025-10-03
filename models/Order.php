<?php
require_once __DIR__ . '/../includes/config.php';

class Order {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function create($userId, $items, $deliveryType, $scheduledDate, $scheduledTime, $street = null, $city = null, $zipCode = null, $notes = null) {
        // Start transaction
        $this->db->begin_transaction();
        
        try {
            // Create order
            $stmt = $this->db->prepare("INSERT INTO orders (user_id, delivery_type, scheduled_date, scheduled_time, street, city, zip_code, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $userId, $deliveryType, $scheduledDate, $scheduledTime, $street, $city, $zipCode, $notes);
            $stmt->execute();
            
            $orderId = $this->db->insert_id;
            
            // Add order items
            $stmt = $this->db->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            
            foreach ($items as $item) {
                $stmt->bind_param("iii", $orderId, $item['product_id'], $item['quantity']);
                $stmt->execute();
                
                // Decrement product quantity
                $updateStmt = $this->db->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                $updateStmt->bind_param("ii", $item['quantity'], $item['product_id']);
                $updateStmt->execute();
            }
            
            $this->db->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        
        if ($order) {
            // Get order items
            $stmt = $this->db->prepare("SELECT oi.*, p.name as product_name, p.unit FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $itemsResult = $stmt->get_result();
            $order['items'] = $itemsResult->fetch_all(MYSQLI_ASSOC);
        }
        
        return $order;
    }
    
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAll() {
        $result = $this->db->query("SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        
        return $stmt->execute();
    }
}
?>
