<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../models/Order.php';

requireAdmin();

$orderModel = new Order();
$orders = $orderModel->getAll();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    if ($orderModel->updateStatus($orderId, $status)) {
        flashMessage('Order status updated!', 'success');
    } else {
        flashMessage('Failed to update status.', 'error');
    }
    redirect('/admin/orders.php');
}

$pageTitle = 'Manage Orders';
require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4">All Orders</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Scheduled</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($order['user_name']); ?><br>
                                <small class="text-muted"><?php echo htmlspecialchars($order['user_email']); ?></small>
                            </td>
                            <td><?php echo ucfirst($order['delivery_type']); ?></td>
                            <td>
                                <?php echo formatDate($order['scheduled_date']); ?><br>
                                <small class="text-muted"><?php echo formatTime($order['scheduled_time']); ?></small>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="preparing" <?php echo $order['status'] === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                        <option value="ready" <?php echo $order['status'] === 'ready' ? 'selected' : ''; ?>>Ready</option>
                                        <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" style="display: none;"></button>
                                </form>
                            </td>
                            <td>
                                <a href="/order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
