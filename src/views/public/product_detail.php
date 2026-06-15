<?php
$db = getDB();
$id = intval($_GET['id'] ?? 0);
if (!$id) { redirect('/index.php?page=products'); }

// Increment view count
$db->prepare("UPDATE products SET view_count = view_count + 1 WHERE id = ?")->execute([$id]);

$stmt = $db->prepare("SELECT p.*, u.name as seller_name, u.phone as seller_phone, u.created_at as seller_since, c.name as category_name FROM products p JOIN users u ON p.seller_id = u.id JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.verification_status = 'approved'");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { redirect('/index.php?page=products'); }

$images = json_decode($product['images'], true) ?: [];
$discount = $product['original_price'] > 0 ? round((1 - $product['price']/$product['original_price'])*100) : 0;

// Reviews
$reviews = $db->prepare("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$reviews->execute([$id]);
$reviewList = $reviews->fetchAll();
$avgRating = 0;
if (count($reviewList) > 0) {
    $avgRating = round(array_sum(array_column($reviewList, 'rating')) / count($reviewList), 1);
}

// Check wishlist
$inWishlist = false;
if (isLoggedIn()) {
    $ws = $db->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $ws->execute([$_SESSION['user_id'], $id]);
    $inWishlist = (bool)$ws->fetch();
}

// Related products
$related = $db->prepare("SELECT p.*, u.name as seller_name FROM products p JOIN users u ON p.seller_id = u.id WHERE p.category_id = ? AND p.id != ? AND p.verification_status = 'approved' AND p.is_active = 1 LIMIT 4");
$related->execute([$product['category_id'], $id]);
$relatedProducts = $related->fetchAll();

$pageTitle = e($product['name']) . ' - SecondHand Marketplace';
?>

<div class="breadcrumb-custom">
    <div class="container">
        <nav><ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=products"><?= e($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active"><?= e(truncate($product['name'], 40)) ?></li>
        </ol></nav>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        <!-- Images -->
        <div class="col-lg-5">
            <div class="bg-white rounded-3 border p-3">
                <div class="mb-3" style="height:350px;overflow:hidden;border-radius:var(--radius)">
                    <img id="mainImage" src="<?= productImage(!empty($images) ? $images[0] : '') ?>" alt="<?= e($product['name']) ?>" class="w-100 h-100" style="object-fit:cover">
                </div>
                <?php if (count($images) > 1): ?>
                <div class="d-flex gap-2 overflow-auto">
                    <?php foreach ($images as $i => $img): ?>
                    <img src="<?= productImage($img) ?>" class="rounded border" style="width:70px;height:70px;object-fit:cover;cursor:pointer;opacity:<?= $i===0?'1':'0.6' ?>" onclick="document.getElementById('mainImage').src=this.src; this.parentElement.querySelectorAll('img').forEach(i=>i.style.opacity='0.6'); this.style.opacity='1';">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-7">
            <div class="bg-white rounded-3 border p-4">
                <div class="d-flex gap-2 mb-2">
                    <?= conditionLabel($product['condition_status']) ?>
                    <?= verificationLabel($product['verification_status']) ?>
                </div>
                <h1 class="h4 fw-bold mb-2"><?= e($product['name']) ?></h1>

                <?php if (count($reviewList) > 0): ?>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="text-warning">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star<?= $i <= round($avgRating) ? '-fill' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="fw-semibold"><?= $avgRating ?></span>
                    <span class="text-muted">(<?= count($reviewList) ?> ulasan)</span>
                    <span class="text-muted">• <?= $product['view_count'] ?> dilihat</span>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <span class="h3 fw-bold text-primary"><?= formatRupiah($product['price']) ?></span>
                    <?php if ($product['original_price'] > 0): ?>
                    <span class="text-muted text-decoration-line-through ms-2"><?= formatRupiah($product['original_price']) ?></span>
                    <span class="badge bg-danger ms-1">-<?= $discount ?>%</span>
                    <?php endif; ?>
                </div>

                <div class="row g-3 mb-3 small">
                    <div class="col-6"><span class="text-muted">Kondisi:</span> <strong><?= $product['condition_status'] === 'like_new' ? 'Seperti Baru' : ($product['condition_status'] === 'good' ? 'Baik' : 'Cukup Baik') ?></strong></div>
                    <div class="col-6"><span class="text-muted">Berat:</span> <strong><?= number_format($product['weight']) ?>g</strong></div>
                    <div class="col-6"><span class="text-muted">Stok:</span> <strong><?= $product['stock'] ?> pcs</strong></div>
                    <div class="col-6"><span class="text-muted">Kategori:</span> <strong><?= e($product['category_name']) ?></strong></div>
                </div>

                <hr>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:44px;height:44px;font-weight:700"><?= strtoupper(substr($product['seller_name'], 0, 1)) ?></div>
                    <div>
                        <div class="fw-bold"><?= e($product['seller_name']) ?> <i class="bi bi-patch-check-fill text-primary"></i></div>
                        <small class="text-muted">Bergabung sejak <?= date('M Y', strtotime($product['seller_since'])) ?></small>
                    </div>
                </div>
                <hr>

                <?php if ($product['stock'] > 0): ?>
                <div class="d-flex gap-2 mb-3 align-items-center">
                    <label class="small fw-semibold">Jumlah:</label>
                    <div class="input-group" style="width:130px">
                        <button class="btn btn-outline-secondary btn-sm" onclick="let q=document.getElementById('qty');q.value=Math.max(1,parseInt(q.value)-1)">-</button>
                        <input type="number" id="qty" class="form-control form-control-sm text-center" value="1" min="1" max="<?= $product['stock'] ?>">
                        <button class="btn btn-outline-secondary btn-sm" onclick="let q=document.getElementById('qty');q.value=Math.min(<?= $product['stock'] ?>,parseInt(q.value)+1)">+</button>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary-custom flex-fill" onclick="addToCart(<?= $product['id'] ?>, document.getElementById('qty').value)">
                        <i class="bi bi-cart-plus me-1"></i> Tambah ke Keranjang
                    </button>
                    <button class="btn btn-outline-danger <?= $inWishlist ? 'active' : '' ?>" onclick="toggleWishlist(<?= $product['id'] ?>, this)" style="border-radius:50px">
                        <i class="bi bi-heart<?= $inWishlist ? '-fill' : '' ?>"></i>
                    </button>
                </div>
                <?php else: ?>
                <div class="alert alert-warning"><i class="bi bi-exclamation-circle me-1"></i> Stok habis</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Description & Reviews -->
    <div class="row g-4 mt-2">
        <div class="col-lg-8">
            <div class="bg-white rounded-3 border p-4">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#descTab">Deskripsi</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#reviewTab">Ulasan (<?= count($reviewList) ?>)</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="descTab">
                        <div style="white-space:pre-line"><?= e($product['description']) ?></div>
                    </div>
                    <div class="tab-pane fade" id="reviewTab">
                        <?php if (empty($reviewList)): ?>
                        <p class="text-muted text-center py-3">Belum ada ulasan untuk produk ini.</p>
                        <?php else: ?>
                        <?php foreach ($reviewList as $rv): ?>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <strong><?= e($rv['user_name']) ?></strong>
                                <div class="text-warning small"><?php for($i=1;$i<=5;$i++) echo '<i class="bi bi-star'.($i<=$rv['rating']?'-fill':'').'"></i>'; ?></div>
                                <small class="text-muted"><?= timeAgo($rv['created_at']) ?></small>
                            </div>
                            <p class="mb-0"><?= e($rv['comment']) ?></p>
                            <?php if ($rv['image']): ?>
                            <img src="<?= BASE_URL ?>/public/uploads/reviews/<?= e($rv['image']) ?>" class="mt-2 rounded" style="max-height:100px">
                            <?php endif; ?>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Related -->
        <div class="col-lg-4">
            <div class="bg-white rounded-3 border p-4">
                <h6 class="fw-bold mb-3">Produk Serupa</h6>
                <?php foreach ($relatedProducts as $rp):
                    $ri = json_decode($rp['images'], true);
                ?>
                <a href="<?= BASE_URL ?>/index.php?page=product_detail&id=<?= $rp['id'] ?>" class="d-flex gap-3 mb-3 text-decoration-none text-dark">
                    <img src="<?= productImage(!empty($ri) ? $ri[0] : '') ?>" class="rounded" style="width:70px;height:70px;object-fit:cover">
                    <div>
                        <div class="small fw-semibold" style="line-height:1.3"><?= e(truncate($rp['name'], 50)) ?></div>
                        <div class="text-primary fw-bold small"><?= formatRupiah($rp['price']) ?></div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
