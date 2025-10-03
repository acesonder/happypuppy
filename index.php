<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
?>

<div class="text-center py-5">
    <h1 class="display-4 mb-4"><i class="bi bi-heart-fill text-primary"></i> Welcome to HappyPuppy</h1>
    <p class="lead">Harm Reduction Ordering System</p>
    <p class="text-muted">A comprehensive system for accessing harm reduction supplies with dignity and respect.</p>
    
    <?php if (!isLoggedIn()): ?>
        <div class="mt-5">
            <a href="/register.php" class="btn btn-primary btn-lg me-3">Get Started</a>
            <a href="/login.php" class="btn btn-outline-primary btn-lg">Login</a>
        </div>
    <?php else: ?>
        <div class="mt-5">
            <a href="/products.php" class="btn btn-primary btn-lg">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<div class="row mt-5">
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-shield-check text-primary" style="font-size: 3rem;"></i>
                <h4 class="mt-3">Safe & Secure</h4>
                <p class="text-muted">
                    Your privacy and safety are our top priorities. All orders are handled with discretion.
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check text-primary" style="font-size: 3rem;"></i>
                <h4 class="mt-3">Scheduled Delivery</h4>
                <p class="text-muted">
                    Choose pickup or delivery on Wednesdays and Fridays, 5:00 PM - 9:00 PM.
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-chat-dots text-primary" style="font-size: 3rem;"></i>
                <h4 class="mt-3">Direct Support</h4>
                <p class="text-muted">
                    Message our support team anytime for assistance or questions about your orders.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="mb-4">Our Approach</h3>
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="bi bi-check-circle text-success"></i> Non-judgmental</h5>
                        <p>We believe in treating everyone with respect and dignity.</p>
                        
                        <h5><i class="bi bi-check-circle text-success"></i> Safety-focused</h5>
                        <p>All products come with safety information and usage guidelines.</p>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="bi bi-check-circle text-success"></i> Accessible</h5>
                        <p>Easy ordering and communication through our messaging system.</p>
                        
                        <h5><i class="bi bi-check-circle text-success"></i> Confidential</h5>
                        <p>Your information and orders are kept strictly confidential.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
