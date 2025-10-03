<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/models/Order.php';

requireLogin();

$orderModel = new Order();
$orders = $orderModel->getByUserId($_SESSION['user_id']);

$pageTitle = 'My Orders';
require_once __DIR__ . '/includes/header.php';
?>

<h2 class="mb-4">My Orders</h2>

<?php if (empty($orders)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> You haven't placed any orders yet. <a href="/products.php">Browse products</a>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($orders as $order): ?>
            <div class="col-12 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">
                                    Order #<?php echo $order['id']; ?>
                                    <?php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'preparing' => 'primary',
                                        'ready' => 'success',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$order['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $statusColor; ?> ms-2">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </h5>
                                <p class="mb-2">
                                    <i class="bi bi-calendar"></i> 
                                    <?php echo formatDate($order['scheduled_date']); ?> at 
                                    <?php echo formatTime($order['scheduled_time']); ?>
                                </p>
                                <p class="mb-2">
                                    <i class="bi bi-truck"></i> 
                                    <?php echo ucfirst($order['delivery_type']); ?>
                                </p>
                                <?php if ($order['delivery_type'] === 'delivery' && $order['street']): ?>
                                    <p class="mb-2 text-muted small">
                                        <i class="bi bi-geo-alt"></i> 
                                        <?php echo htmlspecialchars($order['street']); ?>, 
                                        <?php echo htmlspecialchars($order['city']); ?>, 
                                        <?php echo htmlspecialchars($order['zip_code']); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($order['notes']): ?>
                                    <p class="mb-2 text-muted small">
                                        <i class="bi bi-sticky"></i> 
                                        <?php echo htmlspecialchars($order['notes']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-end">
                                <p class="text-muted small mb-2">
                                    Placed: <?php echo formatDateTime($order['created_at']); ?>
                                </p>
                                <a href="/order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                                <a href="/messages.php?to=admin" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-chat"></i> Message Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
