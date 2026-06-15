<?php
$dashboardTitle = 'Dashboard Admin';
$db = getDB();
$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue = $db->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status = 'completed'")->fetchColumn();
$pendingVerif = $db->query("SELECT COUNT(*) FROM products WHERE verification_status = 'pending'")->fetchColumn();
$pendingPayments = $db->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn();
$recentOrders = $db->query("SELECT o.*, u.name as buyer_name FROM orders o JOIN users u ON o.buyer_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
$recentUsers = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<div class="row g-4 mb-4">
    <div class="col-md-3 col-6"><div class="stat-card"><div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-label">Total User</div><div class="stat-value"><?= $totalUsers ?></div></div>
        <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people"></i></div>
    </div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card"><div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-label">Total Produk</div><div class="stat-value"><?= $totalProducts ?></div></div>
        <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-box-seam"></i></div>
    </div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card"><div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-label">Total Pesanan</div><div class="stat-value"><?= $totalOrders ?></div></div>
        <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-receipt"></i></div>
    </div></div></div>
    <div class="col-md-3 col-6"><div class="stat-card"><div class="d-flex justify-content-between align-items-start">
        <div><div class="stat-label">Pendapatan</div><div class="stat-value" style="font-size:1.1rem"><?= formatRupiah($totalRevenue) ?></div></div>
        <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-wallet2"></i></div>
    </div></div></div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="stat-card border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div><h6 class="fw-bold mb-1"><i class="bi bi-patch-check text-warning me-1"></i> Verifikasi Produk</h6><p class="text-muted mb-0"><?= $pendingVerif ?> produk menunggu verifikasi</p></div>
                <a href="<?= BASE_URL ?>/index.php?page=admin_verify" class="btn btn-warning btn-sm">Review</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div><h6 class="fw-bold mb-1"><i class="bi bi-credit-card text-info me-1"></i> Pembayaran Pending</h6><p class="text-muted mb-0"><?= $pendingPayments ?> pembayaran belum diverifikasi</p></div>
                <a href="<?= BASE_URL ?>/index.php?page=admin_orders" class="btn btn-info btn-sm text-white">Kelola</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="bg-white rounded-3 border p-4">
            <h6 class="fw-bold mb-3">Pesanan Terbaru</h6>
            <div class="table-responsive"><table class="table table-custom mb-0"><thead><tr><th>Invoice</th><th>Pembeli</th><th>Total</th><th>Status</th></tr></thead><tbody>
            <?php foreach ($recentOrders as $o): ?>
            <tr><td class="small fw-semibold"><?= e($o['invoice_number']) ?></td><td><?= e($o['buyer_name']) ?></td><td><?= formatRupiah($o['total_amount']) ?></td><td><?= orderStatusLabel($o['status']) ?></td></tr>
            <?php endforeach; ?>
            </tbody></table></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="bg-white rounded-3 border p-4">
            <h6 class="fw-bold mb-3">User Terbaru</h6>
            <?php foreach ($recentUsers as $u): ?>
            <div class="d-flex gap-2 align-items-center mb-2 pb-2 border-bottom">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:0.8rem;font-weight:700"><?= strtoupper(substr($u['name'],0,1)) ?></div>
                <div class="flex-fill"><div class="fw-semibold small"><?= e($u['name']) ?></div><small class="text-muted"><?= e($u['email']) ?></small></div>
                <span class="badge bg-<?= $u['role']==='admin'?'danger':($u['role']==='seller'?'info':'success') ?>"><?= $u['role'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
