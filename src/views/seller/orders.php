<?php
$dashboardTitle = 'Pesanan Masuk';
$db = getDB();
$sellerId = $_SESSION['user_id'];
$orders = $db->prepare("SELECT oi.*, o.invoice_number, o.status as order_status, o.tracking_number, o.shipping_address, o.created_at as order_date, p.name as product_name, p.images, u.name as buyer_name FROM order_items oi JOIN orders o ON oi.order_id = o.id JOIN products p ON oi.product_id = p.id JOIN users u ON o.buyer_id = u.id WHERE oi.seller_id = ? ORDER BY o.created_at DESC");
$orders->execute([$sellerId]);
$orderList = $orders->fetchAll();
?>

<div class="bg-white rounded-3 border p-4">
    <h6 class="fw-bold mb-3">Pesanan Masuk (<?= count($orderList) ?>)</h6>
    <?php if (empty($orderList)): ?>
    <div class="empty-state"><i class="bi bi-receipt"></i><h5>Belum Ada Pesanan</h5></div>
    <?php else: ?>
    <?php foreach ($orderList as $o):
        $imgs = json_decode($o['images'], true);
    ?>
    <div class="border rounded-3 p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <span class="fw-bold"><?= e($o['invoice_number']) ?></span>
                <span class="text-muted small ms-2"><?= date('d M Y H:i', strtotime($o['order_date'])) ?></span>
            </div>
            <?= orderStatusLabel($o['order_status']) ?>
        </div>
        <div class="d-flex gap-3 mb-2">
            <img src="<?= productImage(!empty($imgs)?$imgs[0]:'') ?>" class="rounded" style="width:60px;height:60px;object-fit:cover">
            <div class="flex-fill">
                <div class="fw-semibold"><?= e($o['product_name']) ?></div>
                <small class="text-muted"><?= $o['quantity'] ?> x <?= formatRupiah($o['price']) ?></small>
                <div class="small"><i class="bi bi-person"></i> <?= e($o['buyer_name']) ?></div>
                <div class="small text-muted"><i class="bi bi-geo-alt"></i> <?= e(truncate($o['shipping_address'], 60)) ?></div>
            </div>
            <div class="fw-bold text-end"><?= formatRupiah($o['subtotal']) ?></div>
        </div>
        <div class="d-flex gap-2 justify-content-end">
            <?php if ($o['status'] === 'pending' && $o['order_status'] === 'confirmed'): ?>
            <form method="POST" action="<?= BASE_URL ?>/index.php?action=seller_confirm_order" class="d-inline">
                <input type="hidden" name="item_id" value="<?= $o['id'] ?>">
                <button class="btn btn-sm btn-success"><i class="bi bi-check me-1"></i>Konfirmasi</button>
            </form>
            <?php endif; ?>
            <?php if ($o['status'] === 'confirmed' || ($o['status'] === 'pending' && $o['order_status'] === 'processing')): ?>
            <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#shipModal<?= $o['id'] ?>"><i class="bi bi-truck me-1"></i>Kirim</button>
            <!-- Ship Modal -->
            <div class="modal fade" id="shipModal<?= $o['id'] ?>"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h6 class="modal-title fw-bold">Input Resi</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="<?= BASE_URL ?>/index.php?action=seller_ship_order"><div class="modal-body">
                <input type="hidden" name="item_id" value="<?= $o['id'] ?>">
                <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
                <div class="mb-3"><label class="form-label">Nomor Resi</label><input type="text" name="tracking_number" class="form-control" required placeholder="Masukkan nomor resi"></div>
            </div><div class="modal-footer"><button type="submit" class="btn btn-primary-custom">Kirim</button></div></form></div></div></div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
