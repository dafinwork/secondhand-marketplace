<?php
$dashboardTitle = 'Verifikasi Produk';
$db = getDB();
$products = $db->query("SELECT p.*, u.name as seller_name, c.name as category_name FROM products p JOIN users u ON p.seller_id = u.id JOIN categories c ON p.category_id = c.id WHERE p.verification_status = 'pending' ORDER BY p.created_at ASC")->fetchAll();
?>

<div class="bg-white rounded-3 border p-4">
    <h6 class="fw-bold mb-3"><i class="bi bi-patch-check me-1"></i> Produk Menunggu Verifikasi (<?= count($products) ?>)</h6>
    <?php if (empty($products)): ?>
    <div class="empty-state"><i class="bi bi-patch-check"></i><h5>Semua Produk Sudah Diverifikasi</h5><p>Tidak ada produk yang perlu direview</p></div>
    <?php else: ?>
    <?php foreach ($products as $p):
        $imgs = json_decode($p['images'], true);
    ?>
    <div class="border rounded-3 p-4 mb-3">
        <div class="row g-3">
            <div class="col-md-3">
                <img src="<?= productImage(!empty($imgs)?$imgs[0]:'') ?>" class="w-100 rounded" style="height:180px;object-fit:cover">
                <?php if (count($imgs ?? []) > 1): ?>
                <div class="d-flex gap-1 mt-2">
                    <?php foreach (array_slice($imgs, 1, 3) as $img): ?>
                    <img src="<?= productImage($img) ?>" class="rounded" style="width:50px;height:50px;object-fit:cover">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold mb-1"><?= e($p['name']) ?></h6>
                        <small class="text-muted"><i class="bi bi-shop"></i> <?= e($p['seller_name']) ?> | <i class="bi bi-tag"></i> <?= e($p['category_name']) ?></small>
                    </div>
                    <?= conditionLabel($p['condition_status']) ?>
                </div>
                <div class="mb-2">
                    <span class="fw-bold text-primary"><?= formatRupiah($p['price']) ?></span>
                    <?php if ($p['original_price'] > 0): ?> <span class="text-muted text-decoration-line-through small"><?= formatRupiah($p['original_price']) ?></span><?php endif; ?>
                    <span class="text-muted small ms-2">Stok: <?= $p['stock'] ?> | Berat: <?= number_format($p['weight']) ?>g</span>
                </div>
                <p class="small text-muted mb-3"><?= e(truncate($p['description'], 200)) ?></p>
                <div class="d-flex gap-2">
                    <form method="POST" action="<?= BASE_URL ?>/index.php?action=admin_verify_product" class="d-inline">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <input type="hidden" name="status" value="approved">
                        <input type="hidden" name="note" value="">
                        <button class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i> Setujui</button>
                    </form>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $p['id'] ?>"><i class="bi bi-x-circle me-1"></i> Tolak</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal<?= $p['id'] ?>"><div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h6 class="modal-title fw-bold">Tolak Produk</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="<?= BASE_URL ?>/index.php?action=admin_verify_product"><div class="modal-body">
            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
            <input type="hidden" name="status" value="rejected">
            <div class="mb-3"><label class="form-label fw-semibold">Alasan Penolakan</label><textarea name="note" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea></div>
        </div><div class="modal-footer"><button type="submit" class="btn btn-danger">Tolak Produk</button></div></form>
    </div></div></div>
    <?php endforeach; endif; ?>
</div>
