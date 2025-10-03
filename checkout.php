<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Order.php';
require_once __DIR__ . '/models/Notification.php';

requireLogin();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    flashMessage('Your cart is empty!', 'error');
    redirect('/products.php');
}

$productModel = new Product();
$cartItems = [];

foreach ($_SESSION['cart'] as $productId => $quantity) {
    $product = $productModel->getById($productId);
    if ($product) {
        $cartItems[] = [
            'product' => $product,
            'quantity' => $quantity
        ];
    }
}

$user = getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deliveryType = $_POST['delivery_type'] ?? '';
    $scheduledDate = $_POST['scheduled_date'] ?? '';
    $scheduledTime = $_POST['scheduled_time'] ?? '';
    $street = sanitize($_POST['street'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $zipCode = sanitize($_POST['zip_code'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');
    
    // Validation
    if (empty($deliveryType) || empty($scheduledDate) || empty($scheduledTime)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Validate date (should be Wednesday or Friday)
        $dayOfWeek = date('N', strtotime($scheduledDate));
        if ($dayOfWeek != 3 && $dayOfWeek != 5) { // 3 = Wednesday, 5 = Friday
            $error = 'Orders can only be scheduled for Wednesday or Friday.';
        } else {
            // Prepare order items
            $orderItems = [];
            foreach ($cartItems as $item) {
                $orderItems[] = [
                    'product_id' => $item['product']['id'],
                    'quantity' => $item['quantity']
                ];
            }
            
            // Create order
            $orderModel = new Order();
            $orderId = $orderModel->create(
                $user['id'],
                $orderItems,
                $deliveryType,
                $scheduledDate,
                $scheduledTime,
                $deliveryType === 'delivery' ? $street : null,
                $deliveryType === 'delivery' ? $city : null,
                $deliveryType === 'delivery' ? $zipCode : null,
                $notes
            );
            
            if ($orderId) {
                // Create notification
                $notificationModel = new Notification();
                $notificationModel->create(
                    $user['id'],
                    'Order Placed',
                    'Your order #' . $orderId . ' has been placed successfully.',
                    'order',
                    $orderId
                );
                
                // Clear cart
                $_SESSION['cart'] = [];
                
                flashMessage('Order placed successfully!', 'success');
                redirect('/orders.php');
            } else {
                $error = 'Failed to place order. Please try again.';
            }
        }
    }
}

$pageTitle = 'Checkout';
require_once __DIR__ . '/includes/header.php';
?>

<h2 class="mb-4">Checkout</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="card-title mb-4">Order Summary</h4>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product']['name']); ?></td>
                                    <td><?php echo $item['quantity']; ?> <?php echo htmlspecialchars($item['product']['unit']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-4">Delivery Information</h4>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Delivery Type *</label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="delivery_type" id="pickup" 
                                       value="pickup" required <?php echo ($_POST['delivery_type'] ?? '') === 'pickup' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="pickup">
                                    Pickup
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="delivery_type" id="delivery" 
                                       value="delivery" <?php echo ($_POST['delivery_type'] ?? '') === 'delivery' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="delivery">
                                    Delivery
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="delivery-address" style="display: none;">
                        <h5 class="mb-3">Delivery Address</h5>
                        <div class="mb-3">
                            <label for="street" class="form-label">Street</label>
                            <input type="text" class="form-control" id="street" name="street" 
                                   value="<?php echo htmlspecialchars($_POST['street'] ?? $user['street'] ?? ''); ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?php echo htmlspecialchars($_POST['city'] ?? $user['city'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="zip_code" class="form-label">Zip Code</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                       value="<?php echo htmlspecialchars($_POST['zip_code'] ?? $user['zip_code'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="scheduled_date" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" required
                                   value="<?php echo htmlspecialchars($_POST['scheduled_date'] ?? ''); ?>">
                            <small class="text-muted">Only Wednesday and Friday available</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="scheduled_time" class="form-label">Time *</label>
                            <select class="form-select" id="scheduled_time" name="scheduled_time" required>
                                <option value="">Select a time</option>
                                <option value="17:00">5:00 PM</option>
                                <option value="17:30">5:30 PM</option>
                                <option value="18:00">6:00 PM</option>
                                <option value="18:30">6:30 PM</option>
                                <option value="19:00">7:00 PM</option>
                                <option value="19:30">7:30 PM</option>
                                <option value="20:00">8:00 PM</option>
                                <option value="20:30">8:30 PM</option>
                                <option value="21:00">9:00 PM</option>
                            </select>
                            <small class="text-muted">Available 5:00 PM - 9:00 PM</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/cart.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Cart
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Place Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Important Information</h5>
                <ul class="small">
                    <li>Orders are FREE of charge</li>
                    <li>Pickup/Delivery: Wednesday & Friday only</li>
                    <li>Time slots: 5:00 PM - 9:00 PM (30-minute intervals)</li>
                    <li>All orders are confidential</li>
                    <li>You'll receive a confirmation notification</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliveryRadio = document.getElementById('delivery');
    const pickupRadio = document.getElementById('pickup');
    const deliveryAddress = document.getElementById('delivery-address');
    
    function toggleDeliveryAddress() {
        if (deliveryRadio.checked) {
            deliveryAddress.style.display = 'block';
        } else {
            deliveryAddress.style.display = 'none';
        }
    }
    
    deliveryRadio.addEventListener('change', toggleDeliveryAddress);
    pickupRadio.addEventListener('change', toggleDeliveryAddress);
    
    // Initial check
    toggleDeliveryAddress();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
