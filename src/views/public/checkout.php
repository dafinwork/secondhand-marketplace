<?php
$pageTitle = 'Checkout - SecondHand Marketplace';
$db = getDB();
$userId = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT c.*, p.name, p.price, p.images, p.weight FROM carts c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$userId]);
$items = $stmt->fetchAll();
if (empty($items)) { redirect('/index.php?page=cart'); }

$total = 0; $totalWeight = 0;
foreach ($items as $item) { $total += $item['price'] * $item['quantity']; $totalWeight += $item['weight'] * $item['quantity']; }

$user = $db->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$userId]);
$userData = $user->fetch();

$shippingReg = calculateShipping($totalWeight, 'reguler');
$shippingExp = calculateShipping($totalWeight, 'express');
?>

<div class="container py-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-bag-check me-2"></i>Checkout</h4>
    <form method="POST" action="<?= BASE_URL ?>/index.php?action=checkout_process">
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Alamat -->
                <div class="bg-white rounded-3 border p-4 mb-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt me-1"></i> Alamat Pengiriman</h6>
                    <textarea name="shipping_address" class="form-control" rows="3" required placeholder="Masukkan alamat lengkap..."><?= e($userData['address'] ?? '') ?></textarea>
                </div>
                <!-- Items -->
                <div class="bg-white rounded-3 border p-4 mb-3">
                    <h6 class="fw-bold mb-3">Produk Dipesan</h6>
                    <?php foreach ($items as $item):
                        $imgs = json_decode($item['images'], true);
                    ?>
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                        <img src="<?= productImage(!empty($imgs)?$imgs[0]:'') ?>" class="rounded" style="width:60px;height:60px;object-fit:cover">
                        <div class="flex-fill">
                            <div class="fw-semibold"><?= e($item['name']) ?></div>
                            <small class="text-muted"><?= $item['quantity'] ?> x <?= formatRupiah($item['price']) ?></small>
                        </div>
                        <div class="fw-bold"><?= formatRupiah($item['price'] * $item['quantity']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Pengiriman -->
                <div class="bg-white rounded-3 border p-4 mb-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-truck me-1"></i> Metode Pengiriman</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="shipping_method" value="reguler" id="shipReg" checked onchange="updateShipping(<?= $shippingReg ?>)">
                        <label class="form-check-label" for="shipReg">Reguler (3-5 hari) - <?= formatRupiah($shippingReg) ?></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="shipping_method" value="express" id="shipExp" onchange="updateShipping(<?= $shippingExp ?>)">
                        <label class="form-check-label" for="shipExp">Express (1-2 hari) - <?= formatRupiah($shippingExp) ?></label>
                    </div>
                </div>
                <!-- Notes -->
                <div class="bg-white rounded-3 border p-4">
                    <h6 class="fw-bold mb-3">Catatan</h6>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Catatan untuk penjual (opsional)"></textarea>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bg-white rounded-3 border p-4 sticky-top" style="top:80px">
                    <h6 class="fw-bold mb-3">Ringkasan Pesanan</h6>
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span><?= formatRupiah($total) ?></span></div>
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Ongkir</span><span id="shippingCostDisplay"><?= formatRupiah($shippingReg) ?></span></div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3"><span class="fw-bold">Total</span><span class="fw-bold text-primary fs-5" id="grandTotal"><?= formatRupiah($total + $shippingReg) ?></span></div>
                    <button type="submit" class="btn btn-primary-custom w-100 py-2"><i class="bi bi-lock me-1"></i> Bayar Sekarang</button>
                    <p class="text-center small text-muted mt-2 mb-0"><i class="bi bi-shield-check me-1"></i>Pembayaran aman & terproteksi</p>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
function updateShipping(cost) {
    const subtotal = <?= $total ?>;
    document.getElementById('shippingCostDisplay').textContent = 'Rp ' + cost.toLocaleString('id-ID');
    document.getElementById('grandTotal').textContent = 'Rp ' + (subtotal + cost).toLocaleString('id-ID');
}
</script>
