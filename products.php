<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/models/Product.php';

requireLogin();

$productModel = new Product();
$products = $productModel->getAll(true);

// Handle cart operations
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_to_cart') {
        $productId = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($quantity > 0) {
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] += $quantity;
            } else {
                $_SESSION['cart'][$productId] = $quantity;
            }
            flashMessage('Product added to cart!', 'success');
        }
    } elseif ($_POST['action'] === 'update_cart') {
        $productId = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($quantity > 0) {
            $_SESSION['cart'][$productId] = $quantity;
        } else {
            unset($_SESSION['cart'][$productId]);
        }
        flashMessage('Cart updated!', 'success');
    } elseif ($_POST['action'] === 'remove_from_cart') {
        $productId = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$productId]);
        flashMessage('Product removed from cart!', 'success');
    } elseif ($_POST['action'] === 'clear_cart') {
        $_SESSION['cart'] = [];
        flashMessage('Cart cleared!', 'success');
    }
    
    redirect('/products.php');
}

$pageTitle = 'Products';
require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Products</h2>
    <div>
        <a href="/cart.php" class="btn btn-primary">
            <i class="bi bi-cart"></i> Cart 
            <?php if (count($_SESSION['cart']) > 0): ?>
                <span class="badge bg-light text-dark"><?php echo count($_SESSION['cart']); ?></span>
            <?php endif; ?>
        </a>
    </div>
</div>

<?php if (empty($products)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No products available at the moment.
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="text-muted small">
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($product['category']); ?></span>
                        </p>
                        <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                        
                        <div class="alert alert-info small mb-3">
                            <strong><i class="bi bi-shield-check"></i> Safety Info:</strong><br>
                            <?php echo htmlspecialchars($product['safety_info']); ?>
                        </div>
                        
                        <p class="mb-2">
                            <strong>Available:</strong> 
                            <?php echo $product['quantity']; ?> <?php echo htmlspecialchars($product['unit']); ?>
                        </p>
                        
                        <?php if ($product['quantity'] > 0): ?>
                            <form method="POST" class="mt-3">
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <div class="input-group">
                                    <input type="number" name="quantity" class="form-control" min="1" 
                                           max="<?php echo $product['quantity']; ?>" value="1" required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>Out of Stock</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
