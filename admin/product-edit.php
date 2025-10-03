<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../models/Product.php';

requireAdmin();

$productModel = new Product();
$product = null;
$isEdit = false;

if (isset($_GET['id'])) {
    $isEdit = true;
    $productId = (int)$_GET['id'];
    $product = $productModel->getById($productId);
    
    if (!$product) {
        flashMessage('Product not found.', 'error');
        redirect('/admin/products.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 0);
    $unit = sanitize($_POST['unit'] ?? '');
    $safetyInfo = sanitize($_POST['safety_info'] ?? '');
    $isAvailable = isset($_POST['is_available']) ? 1 : 0;
    $image = sanitize($_POST['image'] ?? '');
    
    if (empty($name) || empty($description) || empty($category) || empty($unit) || empty($safetyInfo)) {
        $error = 'Please fill in all required fields.';
    } else {
        if ($isEdit) {
            if ($productModel->update($productId, $name, $description, $category, $quantity, $unit, $safetyInfo, $isAvailable)) {
                flashMessage('Product updated successfully!', 'success');
                redirect('/admin/products.php');
            } else {
                $error = 'Failed to update product.';
            }
        } else {
            if ($productModel->create($name, $description, $category, $quantity, $unit, $safetyInfo, $image)) {
                flashMessage('Product created successfully!', 'success');
                redirect('/admin/products.php');
            } else {
                $error = 'Failed to create product.';
            }
        }
    }
}

$pageTitle = $isEdit ? 'Edit Product' : 'Add New Product';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="mb-3">
    <a href="/admin/products.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Products
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="mb-4"><?php echo $isEdit ? 'Edit Product' : 'Add New Product'; ?></h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name *</label>
                <input type="text" class="form-control" id="name" name="name" required
                       value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>">
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description *</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">Category *</label>
                    <input type="text" class="form-control" id="category" name="category" required
                           value="<?php echo htmlspecialchars($product['category'] ?? ''); ?>">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="quantity" class="form-label">Quantity *</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="0" required
                           value="<?php echo $product['quantity'] ?? 0; ?>">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="unit" class="form-label">Unit *</label>
                    <input type="text" class="form-control" id="unit" name="unit" required
                           value="<?php echo htmlspecialchars($product['unit'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="safety_info" class="form-label">Safety Information *</label>
                <textarea class="form-control" id="safety_info" name="safety_info" rows="4" required><?php echo htmlspecialchars($product['safety_info'] ?? ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Image URL</label>
                <input type="text" class="form-control" id="image" name="image"
                       value="<?php echo htmlspecialchars($product['image'] ?? ''); ?>">
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_available" name="is_available"
                       <?php echo ($product['is_available'] ?? true) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_available">
                    Product is available for ordering
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> <?php echo $isEdit ? 'Update Product' : 'Create Product'; ?>
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
