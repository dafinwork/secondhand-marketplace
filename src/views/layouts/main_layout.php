<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SecondHand Marketplace - Platform Jual Beli Barang Bekas Berkualitas">
    <title><?= e($pageTitle ?? 'SecondHand Marketplace - Jual Beli Barang Bekas Berkualitas') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/src/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-main sticky-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">
                <i class="bi bi-recycle"></i> Second<span>Hand</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <!-- Search Bar -->
                <div class="search-bar mx-auto my-2 my-lg-0">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari barang bekas berkualitas...">
                    <div class="search-results" id="searchResults"></div>
                </div>
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php?page=products"><i class="bi bi-grid"></i> Produk</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isRole('buyer')): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-icon" href="<?= BASE_URL ?>/index.php?page=buyer_wishlist">
                                <i class="bi bi-heart"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-icon" href="<?= BASE_URL ?>/index.php?page=cart">
                                <i class="bi bi-cart3"></i>
                                <span class="badge bg-danger" id="cartBadge"><?= getCartCount($_SESSION['user_id']) ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <!-- Notifications -->
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-icon dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <i class="bi bi-bell"></i>
                                <?php $unread = getUnreadNotifCount($_SESSION['user_id']); ?>
                                <?php if ($unread > 0): ?>
                                <span class="badge bg-danger"><?= $unread ?></span>
                                <?php endif; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notif-dropdown" id="notifDropdown">
                                <div class="p-3 border-bottom"><h6 class="mb-0 fw-bold">Notifikasi</h6></div>
                                <div id="notifList"><div class="p-3 text-center text-muted">Memuat...</div></div>
                            </div>
                        </li>
                        <!-- User Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:0.8rem;font-weight:700;">
                                    <?= strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <span class="d-none d-lg-inline"><?= e($_SESSION['name'] ?? '') ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php
                                $dashPage = $_SESSION['role'] . '_dashboard';
                                ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/index.php?page=<?= $dashPage ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                <?php if (isRole('buyer')): ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/index.php?page=buyer_orders"><i class="bi bi-bag me-2"></i>Pesanan Saya</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/index.php?page=logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/index.php?page=login"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary-custom btn-sm" href="<?= BASE_URL ?>/index.php?page=register">Daftar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Flash Message -->
    <?php $flash = getFlash(); if ($flash): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Page Content -->
    <main>
        <?php require_once __DIR__ . '/../' . $contentView; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5><i class="bi bi-recycle"></i> SecondHand</h5>
                    <p>Platform jual beli barang bekas berkualitas dengan sistem verifikasi. Temukan barang impianmu dengan harga terjangkau.</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="fs-5"><i class="bi bi-twitter-x"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5>Kategori</h5>
                    <ul><li><a href="#">Elektronik</a></li><li><a href="#">Smartphone</a></li><li><a href="#">Fashion</a></li><li><a href="#">Kamera</a></li></ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5>Informasi</h5>
                    <ul><li><a href="#">Tentang Kami</a></li><li><a href="#">Cara Kerja</a></li><li><a href="#">Kebijakan</a></li><li><a href="#">FAQ</a></li></ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5>Hubungi Kami</h5>
                    <ul>
                        <li><i class="bi bi-envelope me-2"></i>support@secondhand.com</li>
                        <li><i class="bi bi-phone me-2"></i>+62 812-3456-7890</li>
                        <li><i class="bi bi-geo-alt me-2"></i>Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">&copy; 2025 SecondHand Marketplace. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/src/assets/js/app.js"></script>
</body>
</html>
