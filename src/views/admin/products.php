<?php
$dashboardTitle = 'Kelola Produk';
$db = getDB();
$products = $db->query("SELECT p.*, u.name as seller_name, c.name as category_name FROM products p JOIN users u ON p.seller_id = u.id JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();
?>

<div class="bg-white rounded-3 border p-4">
    <h6 class="fw-bold mb-3">Semua Produk (<?= count($products) ?>)</h6>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>Produk</th><th>Penjual</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Verifikasi</th><th>Views</th></tr></thead>
            <tbody>
            <?php foreach ($products as $p):
                $imgs = json_decode($p['images'], true);
            ?>
            <tr>
                <td><div class="d-flex align-items-center gap-2">
                    <img src="<?= productImage(!empty($imgs)?$imgs[0]:'') ?>" class="rounded" style="width:44px;height:44px;object-fit:cover">
                    <div class="fw-semibold small"><?= e(truncate($p['name'], 30)) ?></div>
                </div></td>
                <td class="small"><?= e($p['seller_name']) ?></td>
                <td class="small"><?= e($p['category_name']) ?></td>
                <td class="small fw-semibold"><?= formatRupiah($p['price']) ?></td>
                <td><span class="badge bg-<?= $p['stock']>0?'success':'danger' ?>"><?= $p['stock'] ?></span></td>
                <td><?= verificationLabel($p['verification_status']) ?></td>
                <td class="small text-muted"><?= $p['view_count'] ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
