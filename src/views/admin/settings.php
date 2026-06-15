<?php
$dashboardTitle = 'System Settings';
$db = getDB();
$stats = [
    'total_users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_buyers' => $db->query("SELECT COUNT(*) FROM users WHERE role='buyer'")->fetchColumn(),
    'total_sellers' => $db->query("SELECT COUNT(*) FROM users WHERE role='seller'")->fetchColumn(),
    'total_products' => $db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'approved_products' => $db->query("SELECT COUNT(*) FROM products WHERE verification_status='approved'")->fetchColumn(),
    'pending_products' => $db->query("SELECT COUNT(*) FROM products WHERE verification_status='pending'")->fetchColumn(),
    'total_orders' => $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'completed_orders' => $db->query("SELECT COUNT(*) FROM orders WHERE status='completed'")->fetchColumn(),
    'total_revenue' => $db->query("SELECT COALESCE(SUM(total_amount+shipping_cost),0) FROM orders WHERE status='completed'")->fetchColumn(),
    'total_categories' => $db->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'total_reviews' => $db->query("SELECT COUNT(*) FROM reviews")->fetchColumn(),
];
?>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="bg-white rounded-3 border p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-gear me-1"></i> Informasi Sistem</h6>
            <table class="table table-borderless mb-0">
                <tr><td class="text-muted">Nama Aplikasi</td><td class="fw-semibold">SecondHand Marketplace</td></tr>
                <tr><td class="text-muted">Versi</td><td class="fw-semibold">1.0.0</td></tr>
                <tr><td class="text-muted">PHP Version</td><td class="fw-semibold"><?= phpversion() ?></td></tr>
                <tr><td class="text-muted">Database</td><td class="fw-semibold">MySQL (<?= DB_NAME ?>)</td></tr>
                <tr><td class="text-muted">Server</td><td class="fw-semibold"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></td></tr>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="bg-white rounded-3 border p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart me-1"></i> Statistik & Report</h6>
            <table class="table table-borderless mb-0">
                <tr><td class="text-muted">Total User</td><td class="fw-semibold"><?= $stats['total_users'] ?> (<?= $stats['total_buyers'] ?> buyer, <?= $stats['total_sellers'] ?> seller)</td></tr>
                <tr><td class="text-muted">Total Produk</td><td class="fw-semibold"><?= $stats['total_products'] ?> (<?= $stats['approved_products'] ?> approved, <?= $stats['pending_products'] ?> pending)</td></tr>
                <tr><td class="text-muted">Total Pesanan</td><td class="fw-semibold"><?= $stats['total_orders'] ?> (<?= $stats['completed_orders'] ?> selesai)</td></tr>
                <tr><td class="text-muted">Total Pendapatan</td><td class="fw-bold text-success"><?= formatRupiah($stats['total_revenue']) ?></td></tr>
                <tr><td class="text-muted">Total Kategori</td><td class="fw-semibold"><?= $stats['total_categories'] ?></td></tr>
                <tr><td class="text-muted">Total Review</td><td class="fw-semibold"><?= $stats['total_reviews'] ?></td></tr>
            </table>
        </div>
    </div>
</div>
