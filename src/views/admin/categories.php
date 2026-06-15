<?php
$dashboardTitle = 'Kelola Kategori';
$db = getDB();
$categories = $db->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) as product_count FROM categories c ORDER BY c.name")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="fw-bold mb-0">Kategori (<?= count($categories) ?>)</h6>
    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#catModal" onclick="document.getElementById('catForm').reset();document.getElementById('catId').value=''"><i class="bi bi-plus-lg me-1"></i> Tambah Kategori</button>
</div>

<div class="bg-white rounded-3 border">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>Icon</th><th>Nama</th><th>Slug</th><th>Produk</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
            <tr>
                <td><i class="bi <?= e($c['icon']) ?> fs-4 text-primary"></i></td>
                <td class="fw-semibold"><?= e($c['name']) ?></td>
                <td class="small text-muted"><?= e($c['slug']) ?></td>
                <td><span class="badge bg-primary"><?= $c['product_count'] ?></span></td>
                <td><button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('catId').value='<?= $c['id'] ?>';document.getElementById('catName').value='<?= e($c['name']) ?>';document.getElementById('catIcon').value='<?= e($c['icon']) ?>';document.getElementById('catDesc').value='<?= e($c['description'] ?? '') ?>';new bootstrap.Modal(document.getElementById('catModal')).show()"><i class="bi bi-pencil"></i></button></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="catModal"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h6 class="modal-title fw-bold">Form Kategori</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="<?= BASE_URL ?>/index.php?action=admin_save_category" id="catForm">
        <div class="modal-body">
            <input type="hidden" name="category_id" id="catId">
            <div class="mb-3"><label class="form-label fw-semibold">Nama</label><input type="text" name="name" id="catName" class="form-control" required></div>
            <div class="mb-3"><label class="form-label fw-semibold">Icon (Bootstrap Icons class)</label><input type="text" name="icon" id="catIcon" class="form-control" value="bi-tag" placeholder="bi-laptop"></div>
            <div class="mb-3"><label class="form-label fw-semibold">Deskripsi</label><textarea name="description" id="catDesc" class="form-control" rows="2"></textarea></div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary-custom">Simpan</button></div>
    </form>
</div></div></div>
