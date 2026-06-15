<?php
$dashboardTitle = 'Kelola User';
$db = getDB();
$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="bg-white rounded-3 border p-4">
    <h6 class="fw-bold mb-3">Semua User (<?= count($users) ?>)</h6>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>User</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Bergabung</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:0.8rem;font-weight:700"><?= strtoupper(substr($u['name'],0,1)) ?></div>
                    <span class="fw-semibold"><?= e($u['name']) ?></span>
                </div></td>
                <td class="small"><?= e($u['email']) ?></td>
                <td class="small"><?= e($u['phone'] ?? '-') ?></td>
                <td><span class="badge bg-<?= $u['role']==='admin'?'danger':($u['role']==='seller'?'info':'success') ?>"><?= ucfirst($u['role']) ?></span></td>
                <td><span class="badge bg-<?= $u['is_active']?'success':'secondary' ?>"><?= $u['is_active']?'Aktif':'Nonaktif' ?></span></td>
                <td class="small text-muted"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <?php if ($u['role'] !== 'admin'): ?>
                    <form method="POST" action="<?= BASE_URL ?>/index.php?action=admin_toggle_user" class="d-inline">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <button class="btn btn-sm btn-outline-<?= $u['is_active']?'warning':'success' ?>"><?= $u['is_active']?'Nonaktifkan':'Aktifkan' ?></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
