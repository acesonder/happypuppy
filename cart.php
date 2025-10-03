<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/models/Product.php';

requireLogin();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productModel = new Product();
$cartItems = [];
$total = 0;

foreach ($_SESSION['cart'] as $productId => $quantity) {
    $product = $productModel->getById($productId);
    if ($product) {
        $cartItems[] = [
            'product' => $product,
            'quantity' => $quantity
        ];
    }
}

$pageTitle = 'Shopping Cart';
require_once __DIR__ . '/includes/header.php';
?>

<h2 class="mb-4">Shopping Cart</h2>

<?php if (empty($cartItems)): ?>
    <div class="alert alert-info">
        <i class="bi bi-cart-x"></i> Your cart is empty. <a href="/products.php">Browse products</a>
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Available</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['product']['name']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($item['product']['description']); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($item['product']['category']); ?></span>
                                </td>
                                <td>
                                    <form method="POST" action="/products.php" class="d-inline">
                                        <input type="hidden" name="action" value="update_cart">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                        <div class="input-group" style="width: 150px;">
                                            <input type="number" name="quantity" class="form-control form-control-sm" 
                                                   min="0" max="<?php echo $item['product']['quantity']; ?>" 
                                                   value="<?php echo $item['quantity']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                                <td><?php echo $item['product']['quantity']; ?> <?php echo htmlspecialchars($item['product']['unit']); ?></td>
                                <td>
                                    <form method="POST" action="/products.php" class="d-inline">
                                        <input type="hidden" name="action" value="remove_from_cart">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <div>
                    <form method="POST" action="/products.php" class="d-inline">
                        <input type="hidden" name="action" value="clear_cart">
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash"></i> Clear Cart
                        </button>
                    </form>
                    <a href="/products.php" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-arrow-left"></i> Continue Shopping
                    </a>
                </div>
                <div>
                    <a href="/checkout.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle"></i> Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
