<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/models/Order.php';

requireLogin();

$orderId = (int)($_GET['id'] ?? 0);
$orderModel = new Order();
$order = $orderModel->getById($orderId);

// Check if order exists and belongs to user (or user is admin)
if (!$order || ($order['user_id'] != $_SESSION['user_id'] && !isAdmin())) {
    flashMessage('Order not found or access denied.', 'error');
    redirect('/orders.php');
}

$pageTitle = 'Order Details';
require_once __DIR__ . '/includes/header.php';
?>

<div class="mb-3">
    <a href="/orders.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <h2 class="mb-4">
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
        </h2>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <h5>Order Information</h5>
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Order Date:</th>
                        <td><?php echo formatDateTime($order['created_at']); ?></td>
                    </tr>
                    <tr>
                        <th>Customer:</th>
                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td><span class="badge bg-<?php echo $statusColor; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6 mb-4">
                <h5>Delivery Details</h5>
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Type:</th>
                        <td><?php echo ucfirst($order['delivery_type']); ?></td>
                    </tr>
                    <tr>
                        <th>Scheduled Date:</th>
                        <td><?php echo formatDate($order['scheduled_date']); ?></td>
                    </tr>
                    <tr>
                        <th>Scheduled Time:</th>
                        <td><?php echo formatTime($order['scheduled_time']); ?></td>
                    </tr>
                    <?php if ($order['delivery_type'] === 'delivery' && $order['street']): ?>
                        <tr>
                            <th>Address:</th>
                            <td>
                                <?php echo htmlspecialchars($order['street']); ?><br>
                                <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['zip_code']); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        
        <?php if ($order['notes']): ?>
            <div class="mb-4">
                <h5>Notes</h5>
                <div class="alert alert-info">
                    <?php echo nl2br(htmlspecialchars($order['notes'])); ?>
                </div>
            </div>
        <?php endif; ?>
        
        <h5 class="mb-3">Order Items</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo htmlspecialchars($item['unit']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <a href="/messages.php?to=admin&order=<?php echo $order['id']; ?>" class="btn btn-primary">
                <i class="bi bi-chat"></i> Message Support About This Order
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
