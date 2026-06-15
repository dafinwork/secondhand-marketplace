<?php
$dashboardTitle = 'Wishlist Saya';
$db = getDB();
$userId = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT w.id as wishlist_id, p.*, u.name as seller_name FROM wishlist w JOIN products p ON w.product_id = p.id JOIN users u ON p.seller_id = u.id WHERE w.user_id = ? ORDER BY w.created_at DESC");
$stmt->execute([$userId]);
$items = $stmt->fetchAll();
?>

<div class="bg-white rounded-3 border p-4">
    <h6 class="fw-bold mb-3"><i class="bi bi-heart me-1"></i> Wishlist (<?= count($items) ?>)</h6>
    <?php if (empty($items)): ?>
    <div class="empty-state"><i class="bi bi-heart"></i><h5>Wishlist Kosong</h5><p>Simpan produk favoritmu di sini</p></div>
    <?php else: ?>
    <div class="row g-3">
        <?php foreach ($items as $p):
            $imgs = json_decode($p['images'], true);
            $discount = $p['original_price'] > 0 ? round((1 - $p['price']/$p['original_price'])*100) : 0;
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="product-card">
                <div class="card-img-wrapper">
                    <img src="<?= productImage(!empty($imgs)?$imgs[0]:'') ?>" alt="<?= e($p['name']) ?>">
                    <div class="condition-badge"><?= conditionLabel($p['condition_status']) ?></div>
                    <button class="wishlist-btn active" onclick="toggleWishlist(<?= $p['id'] ?>, this);setTimeout(()=>location.reload(),500)"><i class="bi bi-heart-fill"></i></button>
                    <?php if ($discount > 0): ?><span class="discount-badge">-<?= $discount ?>%</span><?php endif; ?>
                </div>
                <div class="card-body">
                    <a href="<?= BASE_URL ?>/index.php?page=product_detail&id=<?= $p['id'] ?>" class="text-decoration-none"><div class="product-title"><?= e($p['name']) ?></div></a>
                    <div class="product-price"><?= formatRupiah($p['price']) ?></div>
                    <div class="product-seller"><i class="bi bi-shop"></i> <?= e($p['seller_name']) ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
