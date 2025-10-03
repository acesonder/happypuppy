<?php
require_once __DIR__ . '/../includes/config.php';

class Message {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function create($fromUserId, $toUserId, $content, $orderId = null) {
        $stmt = $this->db->prepare("INSERT INTO messages (from_user_id, to_user_id, content, order_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $fromUserId, $toUserId, $content, $orderId);
        
        return $stmt->execute();
    }
    
    public function getConversation($userId1, $userId2) {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   u1.name as from_name, u1.email as from_email,
                   u2.name as to_name, u2.email as to_email
            FROM messages m
            JOIN users u1 ON m.from_user_id = u1.id
            JOIN users u2 ON m.to_user_id = u2.id
            WHERE (m.from_user_id = ? AND m.to_user_id = ?) 
               OR (m.from_user_id = ? AND m.to_user_id = ?)
            ORDER BY m.created_at ASC
        ");
        $stmt->bind_param("iiii", $userId1, $userId2, $userId2, $userId1);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function markAsRead($userId1, $userId2) {
        $stmt = $this->db->prepare("UPDATE messages SET is_read = TRUE WHERE from_user_id = ? AND to_user_id = ?");
        $stmt->bind_param("ii", $userId2, $userId1);
        
        return $stmt->execute();
    }
    
    public function getUnreadCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM messages WHERE to_user_id = ? AND is_read = FALSE");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'];
    }
    
    public function getRecentConversations($userId) {
        $stmt = $this->db->prepare("
            SELECT DISTINCT 
                CASE 
                    WHEN m.from_user_id = ? THEN m.to_user_id 
                    ELSE m.from_user_id 
                END as other_user_id,
                u.name as other_user_name,
                u.email as other_user_email,
                MAX(m.created_at) as last_message_time
            FROM messages m
            JOIN users u ON (
                CASE 
                    WHEN m.from_user_id = ? THEN m.to_user_id 
                    ELSE m.from_user_id 
                END = u.id
            )
            WHERE m.from_user_id = ? OR m.to_user_id = ?
            GROUP BY other_user_id, other_user_name, other_user_email
            ORDER BY last_message_time DESC
            LIMIT 10
        ");
        $stmt->bind_param("iiii", $userId, $userId, $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
