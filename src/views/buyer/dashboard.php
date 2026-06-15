<?php
$dashboardTitle = 'Dashboard Pembeli';
$db = getDB();
$userId = $_SESSION['user_id'];
$totalOrders = $db->prepare("SELECT COUNT(*) FROM orders WHERE buyer_id = ?"); $totalOrders->execute([$userId]); $totalOrders = $totalOrders->fetchColumn();
$pendingOrders = $db->prepare("SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status IN ('pending','confirmed','processing')"); $pendingOrders->execute([$userId]); $pendingOrders = $pendingOrders->fetchColumn();
$completedOrders = $db->prepare("SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status = 'completed'"); $completedOrders->execute([$userId]); $completedOrders = $completedOrders->fetchColumn();
$wishlistCount = $db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?"); $wishlistCount->execute([$userId]); $wishlistCount = $wishlistCount->fetchColumn();
$recentOrders = $db->prepare("SELECT * FROM orders WHERE buyer_id = ? ORDER BY created_at DESC LIMIT 5"); $recentOrders->execute([$userId]); $recentOrders = $recentOrders->fetchAll();
?>

<div class="row g-4 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div><div class="stat-label">Total Pesanan</div><div class="stat-value"><?= $totalOrders ?></div></div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-bag"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div><div class="stat-label">Dalam Proses</div><div class="stat-value"><?= $pendingOrders ?></div></div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div><div class="stat-label">Selesai</div><div class="stat-value"><?= $completedOrders ?></div></div>
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div><div class="stat-label">Wishlist</div><div class="stat-value"><?= $wishlistCount ?></div></div>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-heart"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-3 border p-4">
    <h6 class="fw-bold mb-3">Pesanan Terbaru</h6>
    <?php if (empty($recentOrders)): ?>
    <p class="text-muted text-center py-3">Belum ada pesanan.</p>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead><tr><th>Invoice</th><th>Total</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($recentOrders as $o): ?>
            <tr>
                <td class="fw-semibold"><?= e($o['invoice_number']) ?></td>
                <td><?= formatRupiah($o['total_amount'] + $o['shipping_cost']) ?></td>
                <td><?= orderStatusLabel($o['status']) ?></td>
                <td class="small text-muted"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                <td><a href="<?= BASE_URL ?>/index.php?page=order_tracking&order_id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
