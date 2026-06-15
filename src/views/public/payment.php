<?php
$pageTitle = 'Pembayaran - SecondHand Marketplace';
$db = getDB();
$orderId = intval($_GET['order_id'] ?? 0);
$stmt = $db->prepare("SELECT o.*, p.status as pay_status, p.proof_image FROM orders o LEFT JOIN payments p ON o.id = p.order_id WHERE o.id = ? AND o.buyer_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) { redirect('/index.php?page=buyer_orders'); }
$grandTotal = $order['total_amount'] + $order['shipping_cost'];
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="bg-white rounded-3 border p-4 text-center mb-4 fade-in">
                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px">
                    <i class="bi bi-check-circle text-success" style="font-size:2.5rem"></i>
                </div>
                <h4 class="fw-bold">Pesanan Berhasil Dibuat!</h4>
                <p class="text-muted">No. Invoice: <strong><?= e($order['invoice_number']) ?></strong></p>
            </div>

            <div class="bg-white rounded-3 border p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-credit-card me-2"></i>Detail Pembayaran</h5>
                <div class="alert alert-info">
                    <strong>Total yang harus dibayar:</strong>
                    <span class="fs-4 fw-bold text-primary d-block"><?= formatRupiah($grandTotal) ?></span>
                </div>

                <h6 class="fw-bold mt-4 mb-3">Transfer ke Rekening:</h6>
                <div class="border rounded-3 p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><strong>Bank BCA</strong><br><span class="text-muted">1234567890</span><br><small>a.n. SecondHand Marketplace</small></div>
                        <button class="btn btn-outline-primary btn-sm" onclick="navigator.clipboard.writeText('1234567890');showToast('Nomor rekening disalin!')">Copy</button>
                    </div>
                </div>
                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><strong>Bank Mandiri</strong><br><span class="text-muted">0987654321</span><br><small>a.n. SecondHand Marketplace</small></div>
                        <button class="btn btn-outline-primary btn-sm" onclick="navigator.clipboard.writeText('0987654321');showToast('Nomor rekening disalin!')">Copy</button>
                    </div>
                </div>

                <?php if ($order['pay_status'] !== 'verified'): ?>
                <h6 class="fw-bold mt-4 mb-3">Upload Bukti Pembayaran</h6>
                <form method="POST" action="<?= BASE_URL ?>/index.php?action=upload_payment" enctype="multipart/form-data">
                    <input type="hidden" name="order_id" value="<?= $orderId ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Bank Pengirim</label>
                        <input type="text" name="bank_name" class="form-control" required placeholder="BCA / Mandiri / dll">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pemilik Rekening</label>
                        <input type="text" name="account_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Rekening Pengirim</label>
                        <input type="text" name="account_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bukti Transfer</label>
                        <input type="file" name="proof_image" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-upload me-1"></i> Upload Bukti</button>
                </form>
                <?php else: ?>
                <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i> Pembayaran telah diverifikasi.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
