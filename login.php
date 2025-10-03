<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/models/User.php';

// If already logged in, redirect to products
if (isLoggedIn()) {
    redirect('/products.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $userModel = new User();
        if ($userModel->login($email, $password)) {
            flashMessage('Welcome back!', 'success');
            redirect('/products.php');
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body p-5">
                <h2 class="text-center mb-4"><i class="bi bi-heart-fill text-primary"></i> HappyPuppy</h2>
                <h4 class="text-center mb-4">Login</h4>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                </form>
                
                <div class="text-center">
                    <p>Don't have an account? <a href="/register.php">Register here</a></p>
                </div>
                
                <hr class="my-4">
                
                <div class="alert alert-info">
                    <strong>Test Credentials:</strong><br>
                    <small>
                        <strong>Admin:</strong> admin@happypuppy.com / admin123<br>
                        <strong>User:</strong> user@test.com / user123
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
