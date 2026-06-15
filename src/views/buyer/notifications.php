<?php
$dashboardTitle = 'Notifikasi';
$db = getDB();
$userId = $_SESSION['user_id'];
$notifs = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$notifs->execute([$userId]);
$notifList = $notifs->fetchAll();
// Mark all as read
$db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$userId]);
?>

<div class="bg-white rounded-3 border p-4">
    <h6 class="fw-bold mb-3"><i class="bi bi-bell me-1"></i> Notifikasi</h6>
    <?php if (empty($notifList)): ?>
    <div class="empty-state"><i class="bi bi-bell-slash"></i><h5>Belum Ada Notifikasi</h5></div>
    <?php else: ?>
    <?php foreach ($notifList as $n): ?>
    <div class="d-flex gap-3 p-3 border-bottom <?= !$n['is_read'] ? 'bg-primary bg-opacity-10' : '' ?>" style="border-radius:var(--radius-sm)">
        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px">
            <i class="bi bi-<?= $n['type']==='order'?'bag':($n['type']==='payment'?'credit-card':($n['type']==='product'?'box':'bell')) ?> text-primary"></i>
        </div>
        <div>
            <div class="fw-semibold"><?= e($n['title']) ?></div>
            <p class="mb-0 small text-muted"><?= e($n['message']) ?></p>
            <small class="text-muted"><?= timeAgo($n['created_at']) ?></small>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
