<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard - SecondHand Marketplace') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/src/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php
$role = $_SESSION['role'] ?? 'buyer';
$currentPage = $_GET['page'] ?? '';
?>
<!-- Sidebar -->
<aside class="dashboard-sidebar" id="dashSidebar">
    <div class="sidebar-brand">
        <a href="<?= BASE_URL ?>/index.php" class="text-decoration-none text-dark">
            <i class="bi bi-recycle"></i> Second<span>Hand</span>
        </a>
    </div>

    <?php if ($role === 'admin'): ?>
    <div class="sidebar-section-title">Menu Utama</div>
    <nav class="sidebar-nav nav flex-column">
        <div class="nav-item"><a class="nav-link <?= $currentPage=='admin_dashboard'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=admin_dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></div>
        <div class="nav-item"><a class="nav-link <?= $currentPage=='admin_users'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=admin_users"><i class="bi bi-people"></i> Kelola User</a></div>
        <div class="nav-item"><a class="nav-link <?= $currentPage=='admin_categories'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=admin_categories"><i class="bi bi-tags"></i> Kategori</a></div>
        <div class="nav-item"><a class="nav-link <?= $currentPage=='admin_products'||$currentPage=='admin_verify'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=admin_products"><i class="bi bi-box-seam"></i> Produk</a></div>
        <div class="nav-item"><a class="nav-link <?= $currentPage=='admin_verify'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=admin_verify"><i class="bi bi-patch-check"></i> Verifikasi</a></div>
        <div class="nav-item"><a class="nav-link <?= $currentPage=='admin_orders'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=admin_orders"><i class="bi bi-receipt"></i> Pesanan</a></div>
        <div class="nav-item"><a class="nav-link <?= $currentPage=='admin_settings'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=admin_settings"><i class="bi bi-gear"></i> Settings</a></div>
    </nav>

    <?php elseif ($role === 'seller'): ?>
    <div class="sidebar-section-title">Menu Penjual</div>
    <nav class="sidebar-nav nav flex-column">
        <div class="nav-item"><a class="nav-link <?= $currentPage=='seller_dashboard'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=seller_dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></div>
        <div class="nav-item"><a class="nav-link <?= $currentPage=='seller_products'||$currentPage=='seller_product_form'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=seller_products"><i class="bi bi-box-seam"></i> Produk Saya</a></div>
        <div class="nav-item"><a class="nav-link <?= $currentPage=='seller_orders'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=seller_orders"><i class="bi bi-receipt"></i> Pesanan</a></div>
    </nav>

    <?php else: ?>
    <div class="sidebar-section-title">Menu Pembeli</div>
    <nav class="sidebar-nav nav flex-column">
        <div class="nav-item"><a class="nav-link <?= $currentPage=='buyer_dashboard'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=buyer_dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></div>
        <div class="nav-item"><a class="nav-link <?= $currentPage=='buyer_orders'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=buyer_orders"><i class="bi bi-bag"></i> Pesanan Saya</a></div>
        <div class="nav-item"><a class="nav-link <?= $currentPage=='buyer_wishlist'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=buyer_wishlist"><i class="bi bi-heart"></i> Wishlist</a></div>
        <div class="nav-item"><a class="nav-link <?= $currentPage=='buyer_notifications'?'active':'' ?>" href="<?= BASE_URL ?>/index.php?page=buyer_notifications"><i class="bi bi-bell"></i> Notifikasi</a></div>
    </nav>
    <?php endif; ?>

    <div class="sidebar-section-title mt-3">Lainnya</div>
    <nav class="sidebar-nav nav flex-column">
        <div class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php"><i class="bi bi-house"></i> Ke Beranda</a></div>
        <div class="nav-item"><a class="nav-link text-danger" href="<?= BASE_URL ?>/index.php?page=logout"><i class="bi bi-box-arrow-right"></i> Logout</a></div>
    </nav>
</aside>

<!-- Main Content -->
<div class="dashboard-main">
    <div class="dashboard-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
            <h5 class="mb-0 fw-bold"><?= e($dashboardTitle ?? 'Dashboard') ?></h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small"><?= e($_SESSION['name'] ?? '') ?></span>
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-weight:700;">
                <?= strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)) ?>
            </div>
        </div>
    </div>

    <div class="p-4">
        <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php require_once __DIR__ . '/../' . $contentView; ?>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/src/assets/js/app.js"></script>
<script>
// Sidebar toggle for mobile
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('dashSidebar').classList.toggle('show');
});
</script>
</body>
</html>
