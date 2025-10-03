<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/models/Notification.php';

requireLogin();

$notificationModel = new Notification();

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_read'])) {
        $notifId = (int)$_POST['notification_id'];
        $notificationModel->markAsRead($notifId, $_SESSION['user_id']);
        redirect('/notifications.php');
    } elseif (isset($_POST['mark_all_read'])) {
        $notificationModel->markAllAsRead($_SESSION['user_id']);
        flashMessage('All notifications marked as read', 'success');
        redirect('/notifications.php');
    }
}

$notifications = $notificationModel->getByUserId($_SESSION['user_id'], 50);

$pageTitle = 'Notifications';
require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Notifications</h2>
    <?php if (!empty($notifications)): ?>
        <form method="POST">
            <button type="submit" name="mark_all_read" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-check-all"></i> Mark All as Read
            </button>
        </form>
    <?php endif; ?>
</div>

<?php if (empty($notifications)): ?>
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-bell-slash text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-4 text-muted">No notifications</h4>
            <p class="text-muted">You're all caught up!</p>
        </div>
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            <?php foreach ($notifications as $notif): ?>
                <div class="list-group-item notification-item <?php echo !$notif['is_read'] ? 'unread' : ''; ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <?php
                                $typeIcons = [
                                    'order' => 'bi-cart-check text-primary',
                                    'message' => 'bi-chat-dots text-info',
                                    'system' => 'bi-info-circle text-secondary',
                                    'checkin' => 'bi-heart-pulse text-danger'
                                ];
                                $icon = $typeIcons[$notif['type']] ?? 'bi-bell';
                                ?>
                                <i class="bi <?php echo $icon; ?> me-2" style="font-size: 1.5rem;"></i>
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($notif['title']); ?></h6>
                                    <small class="text-muted"><?php echo formatDateTime($notif['created_at']); ?></small>
                                </div>
                            </div>
                            <p class="mb-0"><?php echo htmlspecialchars($notif['message']); ?></p>
                            
                            <?php if ($notif['related_id'] && $notif['type'] === 'order'): ?>
                                <a href="/order-details.php?id=<?php echo $notif['related_id']; ?>" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-eye"></i> View Order
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if (!$notif['is_read']): ?>
                            <form method="POST" class="ms-3">
                                <input type="hidden" name="notification_id" value="<?php echo $notif['id']; ?>">
                                <button type="submit" name="mark_read" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-check"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
