<?php
$pageTitle = 'Keranjang - SecondHand Marketplace';
$db = getDB();
$userId = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT c.*, p.name, p.price, p.stock, p.images, p.weight, u.name as seller_name FROM carts c JOIN products p ON c.product_id = p.id JOIN users u ON p.seller_id = u.id WHERE c.user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();
$total = 0;
?>

<div class="container py-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-cart3 me-2"></i>Keranjang Belanja</h4>
    <?php if (empty($cartItems)): ?>
    <div class="empty-state bg-white rounded-3 border">
        <i class="bi bi-cart-x"></i>
        <h5>Keranjang Kosong</h5>
        <p>Yuk mulai belanja barang bekas berkualitas!</p>
        <a href="<?= BASE_URL ?>/index.php?page=products" class="btn btn-primary-custom">Mulai Belanja</a>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="bg-white rounded-3 border">
                <?php foreach ($cartItems as $item):
                    $imgs = json_decode($item['images'], true);
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                <div class="d-flex gap-3 p-3 border-bottom align-items-center cart-item-<?= $item['id'] ?>">
                    <img src="<?= productImage(!empty($imgs) ? $imgs[0] : '') ?>" class="rounded" style="width:80px;height:80px;object-fit:cover">
                    <div class="flex-fill">
                        <a href="<?= BASE_URL ?>/index.php?page=product_detail&id=<?= $item['product_id'] ?>" class="fw-semibold text-dark text-decoration-none"><?= e($item['name']) ?></a>
                        <div class="small text-muted"><i class="bi bi-shop"></i> <?= e($item['seller_name']) ?></div>
                        <div class="fw-bold text-primary mt-1"><?= formatRupiah($item['price']) ?></div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group" style="width:120px">
                            <button class="btn btn-outline-secondary btn-sm" onclick="updateCartQty(<?= $item['id'] ?>, -1, <?= $item['stock'] ?>)">-</button>
                            <input type="text" class="form-control form-control-sm text-center" id="cartQty<?= $item['id'] ?>" value="<?= $item['quantity'] ?>" readonly>
                            <button class="btn btn-outline-secondary btn-sm" onclick="updateCartQty(<?= $item['id'] ?>, 1, <?= $item['stock'] ?>)">+</button>
                        </div>
                        <button class="btn btn-outline-danger btn-sm" onclick="removeCartItem(<?= $item['id'] ?>)"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="bg-white rounded-3 border p-4 sticky-top" style="top:80px">
                <h6 class="fw-bold mb-3">Ringkasan Belanja</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total (<?= count($cartItems) ?> produk)</span>
                    <span class="fw-bold" id="cartTotal"><?= formatRupiah($total) ?></span>
                </div>
                <hr>
                <a href="<?= BASE_URL ?>/index.php?page=checkout" class="btn btn-primary-custom w-100">
                    <i class="bi bi-bag-check me-1"></i> Checkout
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function updateCartQty(cartId, delta, maxStock) {
    const input = document.getElementById('cartQty' + cartId);
    let newQty = parseInt(input.value) + delta;
    if (newQty < 1) newQty = 1;
    if (newQty > maxStock) newQty = maxStock;
    input.value = newQty;
    apiCall('cart_update', { cart_id: cartId, quantity: newQty }).then(() => location.reload());
}
function removeCartItem(cartId) {
    if (!confirm('Hapus item ini?')) return;
    apiCall('cart_remove', { cart_id: cartId }).then(res => {
        if (res.success) { document.querySelector('.cart-item-'+cartId)?.remove(); location.reload(); }
    });
}
</script>
