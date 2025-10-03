<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/models/User.php';

requireLogin();

$userModel = new User();
$user = getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $street = sanitize($_POST['street'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $zipCode = sanitize($_POST['zip_code'] ?? '');
    
    if (empty($name) || empty($phone)) {
        $error = 'Name and phone are required.';
    } else {
        if ($userModel->update($_SESSION['user_id'], $name, $phone, $street, $city, $zipCode)) {
            $_SESSION['name'] = $name;
            flashMessage('Profile updated successfully!', 'success');
            redirect('/profile.php');
        } else {
            $error = 'Failed to update profile.';
        }
    }
}

$pageTitle = 'Profile';
require_once __DIR__ . '/includes/header.php';
?>

<h2 class="mb-4">My Profile</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-4">Personal Information</h4>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?php echo htmlspecialchars($user['name']); ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone *</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required
                                   value="<?php echo htmlspecialchars($user['phone']); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        <small class="text-muted">Email cannot be changed</small>
                    </div>
                    
                    <hr class="my-4">
                    <h5 class="mb-3">Address</h5>
                    
                    <div class="mb-3">
                        <label for="street" class="form-label">Street</label>
                        <input type="text" class="form-control" id="street" name="street"
                               value="<?php echo htmlspecialchars($user['street'] ?? ''); ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city"
                                   value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="zip_code" class="form-label">Zip Code</label>
                            <input type="text" class="form-control" id="zip_code" name="zip_code"
                                   value="<?php echo htmlspecialchars($user['zip_code'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title">Account Info</h5>
                <p class="mb-2">
                    <strong>Role:</strong> 
                    <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                        <?php echo ucfirst($user['role']); ?>
                    </span>
                </p>
                <p class="mb-2">
                    <strong>Member since:</strong><br>
                    <?php echo formatDate($user['created_at']); ?>
                </p>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Quick Links</h5>
                <div class="d-grid gap-2">
                    <a href="/orders.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-cart-check"></i> My Orders
                    </a>
                    <a href="/messages.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-chat"></i> Messages
                    </a>
                    <a href="/notifications.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-bell"></i> Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
