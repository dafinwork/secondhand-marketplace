<?php
$pageTitle = 'Beri Ulasan - SecondHand Marketplace';
$db = getDB();
$productId = intval($_GET['product_id'] ?? 0);
$orderId = intval($_GET['order_id'] ?? 0);
$stmt = $db->prepare("SELECT p.name, p.images FROM products p WHERE p.id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();
if (!$product) { redirect('/index.php?page=buyer_orders'); }
$imgs = json_decode($product['images'], true);
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="bg-white rounded-3 border p-4 fade-in">
                <h5 class="fw-bold mb-4"><i class="bi bi-star me-2"></i>Beri Ulasan</h5>
                <div class="d-flex gap-3 mb-4 pb-3 border-bottom">
                    <img src="<?= productImage(!empty($imgs)?$imgs[0]:'') ?>" class="rounded" style="width:70px;height:70px;object-fit:cover">
                    <div><div class="fw-semibold"><?= e($product['name']) ?></div></div>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/index.php?action=submit_review" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?= $productId ?>">
                    <input type="hidden" name="order_id" value="<?= $orderId ?>">
                    <input type="hidden" name="rating" id="ratingInput" value="5">
                    <div class="mb-3 text-center">
                        <label class="form-label fw-semibold d-block">Rating</label>
                        <div class="star-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi bi-star-fill active" data-value="<?= $i ?>" style="font-size:2rem;cursor:pointer"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Komentar</label>
                        <textarea name="comment" class="form-control" rows="4" placeholder="Ceritakan pengalaman Anda..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto (opsional)</label>
                        <input type="file" name="review_image" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-send me-1"></i> Kirim Ulasan</button>
                </form>
            </div>
        </div>
    </div>
</div>
