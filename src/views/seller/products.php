<?php
$dashboardTitle = 'Produk Saya';
$db = getDB();
$sellerId = $_SESSION['user_id'];
$products = $db->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.seller_id = ? ORDER BY p.created_at DESC");
$products->execute([$sellerId]);
$productList = $products->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="fw-bold mb-0">Produk Saya (<?= count($productList) ?>)</h6>
    <a href="<?= BASE_URL ?>/index.php?page=seller_product_form" class="btn btn-primary-custom btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Produk</a>
</div>

<div class="bg-white rounded-3 border">
    <?php if (empty($productList)): ?>
    <div class="empty-state"><i class="bi bi-box"></i><h5>Belum Ada Produk</h5><p>Mulai jual barang bekasmu!</p></div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($productList as $p):
                $imgs = json_decode($p['images'], true);
            ?>
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <img src="<?= productImage(!empty($imgs)?$imgs[0]:'') ?>" class="rounded" style="width:48px;height:48px;object-fit:cover">
                        <div>
                            <div class="fw-semibold"><?= e(truncate($p['name'], 35)) ?></div>
                            <small class="text-muted"><?= conditionLabel($p['condition_status']) ?></small>
                        </div>
                    </div>
                </td>
                <td class="small"><?= e($p['category_name']) ?></td>
                <td class="fw-semibold"><?= formatRupiah($p['price']) ?></td>
                <td><span class="badge bg-<?= $p['stock']>0?'success':'danger' ?>"><?= $p['stock'] ?></span></td>
                <td><?= verificationLabel($p['verification_status']) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/index.php?page=seller_product_form&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="<?= BASE_URL ?>/index.php?action=seller_delete_product" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
