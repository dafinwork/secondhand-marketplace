<?php
$dashboardTitle = 'Dashboard Penjual';
$db = getDB();
$sellerId = $_SESSION['user_id'];
$totalProducts = $db->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ?"); $totalProducts->execute([$sellerId]); $totalProducts = $totalProducts->fetchColumn();
$activeProducts = $db->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ? AND verification_status = 'approved'"); $activeProducts->execute([$sellerId]); $activeProducts = $activeProducts->fetchColumn();
$totalOrderItems = $db->prepare("SELECT COUNT(*) FROM order_items WHERE seller_id = ?"); $totalOrderItems->execute([$sellerId]); $totalOrderItems = $totalOrderItems->fetchColumn();
$revenue = $db->prepare("SELECT COALESCE(SUM(oi.subtotal),0) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.seller_id = ? AND o.status = 'completed'"); $revenue->execute([$sellerId]); $revenue = $revenue->fetchColumn();
$pendingVerif = $db->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ? AND verification_status = 'pending'"); $pendingVerif->execute([$sellerId]); $pendingVerif = $pendingVerif->fetchColumn();
$recentOrders = $db->prepare("SELECT oi.*, o.invoice_number, o.status as order_status, o.created_at as order_date, p.name as product_name FROM order_items oi JOIN orders o ON oi.order_id = o.id JOIN products p ON oi.product_id = p.id WHERE oi.seller_id = ? ORDER BY o.created_at DESC LIMIT 5");
$recentOrders->execute([$sellerId]); $recentOrders = $recentOrders->fetchAll();
?>

<div class="row g-4 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-card"><div class="d-flex justify-content-between align-items-start">
            <div><div class="stat-label">Total Produk</div><div class="stat-value"><?= $totalProducts ?></div></div>
            <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-box-seam"></i></div>
        </div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card"><div class="d-flex justify-content-between align-items-start">
            <div><div class="stat-label">Pesanan Masuk</div><div class="stat-value"><?= $totalOrderItems ?></div></div>
            <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-receipt"></i></div>
        </div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card"><div class="d-flex justify-content-between align-items-start">
            <div><div class="stat-label">Pendapatan</div><div class="stat-value" style="font-size:1.2rem"><?= formatRupiah($revenue) ?></div></div>
            <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-wallet2"></i></div>
        </div></div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card"><div class="d-flex justify-content-between align-items-start">
            <div><div class="stat-label">Menunggu Verifikasi</div><div class="stat-value"><?= $pendingVerif ?></div></div>
            <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock"></i></div>
        </div></div>
    </div>
</div>

<div class="bg-white rounded-3 border p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Pesanan Terbaru</h6>
        <a href="<?= BASE_URL ?>/index.php?page=seller_orders" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <?php if (empty($recentOrders)): ?>
    <p class="text-muted text-center py-3">Belum ada pesanan masuk.</p>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead><tr><th>Invoice</th><th>Produk</th><th>Qty</th><th>Subtotal</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
            <?php foreach ($recentOrders as $o): ?>
            <tr>
                <td class="fw-semibold small"><?= e($o['invoice_number']) ?></td>
                <td><?= e(truncate($o['product_name'], 30)) ?></td>
                <td><?= $o['quantity'] ?></td>
                <td><?= formatRupiah($o['subtotal']) ?></td>
                <td><?= orderStatusLabel($o['order_status']) ?></td>
                <td class="small text-muted"><?= date('d M Y', strtotime($o['order_date'])) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
