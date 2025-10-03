    </main>

    <footer class="bg-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-heart-fill text-primary"></i> HappyPuppy</h5>
                    <p class="text-muted">Harm Reduction Ordering System</p>
                    <p class="small text-muted">
                        This system is designed with harm reduction principles: non-judgmental approach, 
                        safety-focused services, and support team accessibility.
                    </p>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="/index.php" class="text-decoration-none">Home</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="/products.php" class="text-decoration-none">Products</a></li>
                            <li><a href="/orders.php" class="text-decoration-none">My Orders</a></li>
                            <li><a href="/messages.php" class="text-decoration-none">Messages</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Support</h6>
                    <p class="small text-muted">
                        For support or questions, please contact us through the messaging system.
                    </p>
                </div>
            </div>
            <hr>
            <div class="text-center text-muted small">
                <p>&copy; <?php echo date('Y'); ?> HappyPuppy. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/public/js/main.js"></script>
</body>
</html>
