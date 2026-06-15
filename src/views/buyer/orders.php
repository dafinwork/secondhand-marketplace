<?php
$dashboardTitle = 'Pesanan Saya';
$db = getDB();
$userId = $_SESSION['user_id'];
$orders = $db->prepare("SELECT o.*, p.status as pay_status FROM orders o LEFT JOIN payments p ON o.id = p.order_id WHERE o.buyer_id = ? ORDER BY o.created_at DESC");
$orders->execute([$userId]);
$orderList = $orders->fetchAll();
?>

<div class="bg-white rounded-3 border p-4">
    <h6 class="fw-bold mb-3">Semua Pesanan</h6>
    <?php if (empty($orderList)): ?>
    <div class="empty-state"><i class="bi bi-bag-x"></i><h5>Belum Ada Pesanan</h5><p>Mulai belanja sekarang!</p></div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead><tr><th>Invoice</th><th>Total</th><th>Status</th><th>Pembayaran</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($orderList as $o): ?>
            <tr>
                <td class="fw-semibold"><?= e($o['invoice_number']) ?></td>
                <td><?= formatRupiah($o['total_amount'] + $o['shipping_cost']) ?></td>
                <td><?= orderStatusLabel($o['status']) ?></td>
                <td>
                    <?php if ($o['pay_status'] === 'verified'): ?>
                    <span class="badge bg-success">Verified</span>
                    <?php elseif ($o['status'] === 'pending'): ?>
                    <a href="<?= BASE_URL ?>/index.php?page=payment&order_id=<?= $o['id'] ?>" class="btn btn-warning btn-sm">Bayar</a>
                    <?php else: ?>
                    <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                </td>
                <td class="small text-muted"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/index.php?page=order_tracking&order_id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-primary">Tracking</a>
                    <?php if ($o['status'] === 'completed'):
                        // Check if review exists
                        $items = $db->prepare("SELECT oi.product_id FROM order_items oi WHERE oi.order_id = ? LIMIT 1");
                        $items->execute([$o['id']]);
                        $firstItem = $items->fetch();
                        if ($firstItem):
                            $hasReview = $db->prepare("SELECT id FROM reviews WHERE order_id = ? AND user_id = ?");
                            $hasReview->execute([$o['id'], $userId]);
                            if (!$hasReview->fetch()):
                    ?>
                    <a href="<?= BASE_URL ?>/index.php?page=review&product_id=<?= $firstItem['product_id'] ?>&order_id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-warning">Review</a>
                    <?php endif; endif; endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
