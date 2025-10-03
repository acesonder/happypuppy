<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../models/Product.php';

requireAdmin();

$productModel = new Product();
$products = $productModel->getAll();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $productId = (int)$_POST['product_id'];
    if ($productModel->delete($productId)) {
        flashMessage('Product deleted successfully!', 'success');
    } else {
        flashMessage('Failed to delete product.', 'error');
    }
    redirect('/admin/products.php');
}

$pageTitle = 'Manage Products';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Products</h2>
    <a href="/admin/product-edit.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Product
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($product['category']); ?></span></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td><?php echo htmlspecialchars($product['unit']); ?></td>
                            <td>
                                <?php if ($product['is_available']): ?>
                                    <span class="badge bg-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admin/product-edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-sm btn-outline-danger" 
                                            data-confirm-delete>
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
