<?php
require_once __DIR__ . '/../includes/config.php';

class Notification {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function create($userId, $title, $message, $type, $relatedId = null) {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, title, message, type, related_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $userId, $title, $message, $type, $relatedId);
        
        return $stmt->execute();
    }
    
    public function getByUserId($userId, $limit = 20) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function markAsRead($id, $userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $userId);
        
        return $stmt->execute();
    }
    
    public function markAllAsRead($userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        
        return $stmt->execute();
    }
    
    public function getUnreadCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'];
    }
}
?>
