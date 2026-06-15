<?php
$pageTitle = 'Tracking Pesanan - SecondHand Marketplace';
$db = getDB();
$orderId = intval($_GET['order_id'] ?? 0);
$stmt = $db->prepare("SELECT o.*, p.status as pay_status FROM orders o LEFT JOIN payments p ON o.id = p.order_id WHERE o.id = ? AND o.buyer_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) { redirect('/index.php?page=buyer_orders'); }

$items = $db->prepare("SELECT oi.*, p.name, p.images FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$items->execute([$orderId]);
$orderItems = $items->fetchAll();

$steps = [
    'pending' => ['icon'=>'bi-clock','label'=>'Pesanan Dibuat','desc'=>'Menunggu pembayaran'],
    'confirmed' => ['icon'=>'bi-check-circle','label'=>'Pembayaran Dikonfirmasi','desc'=>'Pembayaran telah diverifikasi'],
    'processing' => ['icon'=>'bi-gear','label'=>'Diproses','desc'=>'Pesanan sedang diproses penjual'],
    'shipped' => ['icon'=>'bi-truck','label'=>'Dikirim','desc'=>'Paket dalam pengiriman'],
    'delivered' => ['icon'=>'bi-house-check','label'=>'Diterima','desc'=>'Paket telah sampai'],
    'completed' => ['icon'=>'bi-bag-check','label'=>'Selesai','desc'=>'Transaksi selesai'],
];
$statusOrder = array_keys($steps);
$currentIdx = array_search($order['status'], $statusOrder);
if ($currentIdx === false) $currentIdx = 0;
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="bg-white rounded-3 border p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Tracking Pesanan</h5>
                        <p class="text-muted mb-0">Invoice: <?= e($order['invoice_number']) ?></p>
                    </div>
                    <?= orderStatusLabel($order['status']) ?>
                </div>

                <?php if ($order['tracking_number']): ?>
                <div class="alert alert-info mb-4">
                    <i class="bi bi-truck me-1"></i> No. Resi: <strong><?= e($order['tracking_number']) ?></strong>
                </div>
                <?php endif; ?>

                <!-- Tracking Steps -->
                <div class="ps-2">
                    <?php foreach ($steps as $key => $step):
                        $idx = array_search($key, $statusOrder);
                        $completed = $idx < $currentIdx;
                        $active = $idx === $currentIdx;
                        if ($order['status'] === 'cancelled') { $completed = false; $active = ($key === 'pending'); }
                    ?>
                    <div class="tracking-step <?= $completed?'completed':'' ?> <?= $active?'active':'' ?>">
                        <div class="step-icon"><i class="bi <?= $step['icon'] ?>"></i></div>
                        <div>
                            <div class="fw-semibold"><?= $step['label'] ?></div>
                            <small class="text-muted"><?= $step['desc'] ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($order['status'] === 'shipped'): ?>
                <form method="POST" action="<?= BASE_URL ?>/index.php?action=buyer_confirm_received" class="mt-3">
                    <input type="hidden" name="order_id" value="<?= $orderId ?>">
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Konfirmasi barang sudah diterima?')">
                        <i class="bi bi-check-circle me-1"></i> Konfirmasi Barang Diterima
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-3 border p-4">
                <h6 class="fw-bold mb-3">Produk Dipesan</h6>
                <?php foreach ($orderItems as $oi):
                    $imgs = json_decode($oi['images'], true);
                ?>
                <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                    <img src="<?= productImage(!empty($imgs)?$imgs[0]:'') ?>" class="rounded" style="width:60px;height:60px;object-fit:cover">
                    <div class="flex-fill">
                        <div class="fw-semibold"><?= e($oi['name']) ?></div>
                        <small class="text-muted"><?= $oi['quantity'] ?> x <?= formatRupiah($oi['price']) ?></small>
                    </div>
                    <div class="fw-bold"><?= formatRupiah($oi['subtotal']) ?></div>
                </div>
                <?php endforeach; ?>
                <div class="d-flex justify-content-between pt-2">
                    <span class="text-muted">Ongkir</span><span><?= formatRupiah($order['shipping_cost']) ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total</span><span class="fw-bold text-primary fs-5"><?= formatRupiah($order['total_amount'] + $order['shipping_cost']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
