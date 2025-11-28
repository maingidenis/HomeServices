<?php
/**
 * Shared Footer Component
 * Include this file at the bottom of all pages
 * Usage: <?php include 'includes/footer.php'; ?>
 */
?>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="bi bi-house-door-fill"></i> Home Services</h5>
                    <p class="text-muted">Your trusted platform for home maintenance and repair services.</p>
                </div>
                <div class="col-md-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php?page=dashboard" class="text-muted text-decoration-none">Dashboard</a></li>
                        <li><a href="index.php?page=service" class="text-muted text-decoration-none">Services</a></li>
                        <li><a href="index.php?page=appointment" class="text-muted text-decoration-none">Appointments</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6>Contact</h6>
                    <p class="text-muted mb-1"><i class="bi bi-envelope"></i> support@homeservices.com</p>
                    <p class="text-muted mb-1"><i class="bi bi-telephone"></i> +61 8 1234 5678</p>
                    <p class="text-muted"><i class="bi bi-geo-alt"></i> Adelaide, SA, Australia</p>
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; <?= date('Y') ?> Home Services. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-muted me-3"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Leaflet JS (for maps) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Custom Page Scripts (optional) -->
    <?php if (isset($customScripts)): ?>
    <script><?= $customScripts ?></script>
    <?php endif; ?>
</body>
</html>
