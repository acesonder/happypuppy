<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/models/Message.php';
require_once __DIR__ . '/models/User.php';

requireLogin();

$messageModel = new Message();
$userModel = new User();

// Get admin user ID for messaging support
$adminUser = null;
$adminResult = Database::getConnection()->query("SELECT id, name, email FROM users WHERE role = 'admin' LIMIT 1");
if ($adminResult->num_rows > 0) {
    $adminUser = $adminResult->fetch_assoc();
}

// Determine conversation partner
$toUserId = null;
$toUserName = 'Support';
if (isset($_GET['to']) && $_GET['to'] === 'admin' && $adminUser) {
    $toUserId = $adminUser['id'];
    $toUserName = $adminUser['name'];
} elseif (isset($_GET['to']) && is_numeric($_GET['to'])) {
    $toUserId = (int)$_GET['to'];
    $toUser = $userModel->getById($toUserId);
    if ($toUser) {
        $toUserName = $toUser['name'];
    }
}

// Get conversations
$conversations = $messageModel->getRecentConversations($_SESSION['user_id']);

// Get messages for selected conversation
$messages = [];
if ($toUserId) {
    $messages = $messageModel->getConversation($_SESSION['user_id'], $toUserId);
    // Mark as read
    $messageModel->markAsRead($_SESSION['user_id'], $toUserId);
}

// Handle sending message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $content = sanitize($_POST['content'] ?? '');
    $toId = (int)($_POST['to_user_id'] ?? 0);
    
    if (!empty($content) && $toId > 0) {
        if ($messageModel->create($_SESSION['user_id'], $toId, $content)) {
            flashMessage('Message sent!', 'success');
            redirect('/messages.php?to=' . $toId);
        } else {
            flashMessage('Failed to send message.', 'error');
        }
    }
}

$pageTitle = 'Messages';
require_once __DIR__ . '/includes/header.php';
?>

<h2 class="mb-4">Messages</h2>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Conversations</h5>
            </div>
            <div class="list-group list-group-flush">
                <?php if ($adminUser): ?>
                    <a href="/messages.php?to=admin" class="list-group-item list-group-item-action <?php echo $toUserId == $adminUser['id'] ? 'active' : ''; ?>">
                        <i class="bi bi-headset"></i> <?php echo htmlspecialchars($adminUser['name']); ?> (Support)
                    </a>
                <?php endif; ?>
                
                <?php if (empty($conversations) && !$adminUser): ?>
                    <div class="list-group-item text-muted">
                        No conversations yet
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): ?>
                        <?php if ($conv['other_user_id'] != ($adminUser['id'] ?? 0)): ?>
                            <a href="/messages.php?to=<?php echo $conv['other_user_id']; ?>" 
                               class="list-group-item list-group-item-action <?php echo $toUserId == $conv['other_user_id'] ? 'active' : ''; ?>">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($conv['other_user_name']); ?>
                                <br><small class="text-muted"><?php echo formatDateTime($conv['last_message_time']); ?></small>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <?php if ($toUserId): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots"></i> Conversation with <?php echo htmlspecialchars($toUserName); ?>
                    </h5>
                </div>
                <div class="card-body" style="height: 400px; overflow-y: auto;" id="messages-container">
                    <?php if (empty($messages)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-square-text" style="font-size: 3rem;"></i>
                            <p class="mt-3">No messages yet. Start the conversation!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="mb-3 <?php echo $msg['from_user_id'] == $_SESSION['user_id'] ? 'text-end' : ''; ?>">
                                <div class="message-bubble <?php echo $msg['from_user_id'] == $_SESSION['user_id'] ? 'message-sent' : 'message-received'; ?> d-inline-block">
                                    <div><?php echo nl2br(htmlspecialchars($msg['content'])); ?></div>
                                    <small class="opacity-75"><?php echo formatTime($msg['created_at']); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <form method="POST">
                        <input type="hidden" name="to_user_id" value="<?php echo $toUserId; ?>">
                        <div class="input-group">
                            <input type="text" class="form-control" name="content" placeholder="Type your message..." required>
                            <button type="submit" name="send_message" class="btn btn-primary">
                                <i class="bi bi-send"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-chat-square-text text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-4 text-muted">Select a conversation to start messaging</h4>
                    <p class="text-muted">Choose a conversation from the left or message support</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Auto-scroll to bottom of messages
const messagesContainer = document.getElementById('messages-container');
if (messagesContainer) {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
