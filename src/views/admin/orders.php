<?php
$dashboardTitle = 'Kelola Pesanan';
$db = getDB();
$orders = $db->query("SELECT o.*, u.name as buyer_name, p.status as pay_status, p.proof_image FROM orders o JOIN users u ON o.buyer_id = u.id LEFT JOIN payments p ON o.id = p.order_id ORDER BY o.created_at DESC")->fetchAll();
?>

<div class="bg-white rounded-3 border p-4">
    <h6 class="fw-bold mb-3">Semua Pesanan (<?= count($orders) ?>)</h6>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>Invoice</th><th>Pembeli</th><th>Total</th><th>Ongkir</th><th>Status</th><th>Pembayaran</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td class="fw-semibold small"><?= e($o['invoice_number']) ?></td>
                <td class="small"><?= e($o['buyer_name']) ?></td>
                <td class="small"><?= formatRupiah($o['total_amount']) ?></td>
                <td class="small"><?= formatRupiah($o['shipping_cost']) ?></td>
                <td><?= orderStatusLabel($o['status']) ?></td>
                <td>
                    <?php if ($o['pay_status'] === 'verified'): ?>
                    <span class="badge bg-success">Verified</span>
                    <?php elseif ($o['proof_image']): ?>
                    <span class="badge bg-warning text-dark">Bukti Diupload</span>
                    <?php else: ?>
                    <span class="badge bg-secondary">Belum Bayar</span>
                    <?php endif; ?>
                </td>
                <td class="small text-muted"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu">
                            <?php foreach (['confirmed'=>'Konfirmasi','processing'=>'Proses','shipped'=>'Kirim','delivered'=>'Terkirim','completed'=>'Selesai','cancelled'=>'Batalkan'] as $st => $lb): ?>
                            <li><form method="POST" action="<?= BASE_URL ?>/index.php?action=admin_update_order" class="d-inline">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>"><input type="hidden" name="status" value="<?= $st ?>">
                                <button class="dropdown-item"><?= $lb ?></button>
                            </form></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
