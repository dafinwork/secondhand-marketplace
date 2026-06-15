<?php
$dashboardTitle = 'Form Produk';
$db = getDB();
$sellerId = $_SESSION['user_id'];
$editId = intval($_GET['id'] ?? 0);
$product = null;
$existingImages = [];

if ($editId > 0) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
    $stmt->execute([$editId, $sellerId]);
    $product = $stmt->fetch();
    if ($product) $existingImages = json_decode($product['images'], true) ?: [];
}
$categories = $db->query("SELECT * FROM categories WHERE is_active = 1")->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="bg-white rounded-3 border p-4">
            <h5 class="fw-bold mb-4"><?= $product ? 'Edit Produk' : 'Tambah Produk Baru' ?></h5>
            <form method="POST" action="<?= BASE_URL ?>/index.php?action=seller_save_product" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?= $editId ?>">
                <input type="hidden" name="existing_images" value='<?= json_encode($existingImages) ?>'>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Produk *</label>
                    <input type="text" name="name" class="form-control" value="<?= e($product['name'] ?? '') ?>" required placeholder="Contoh: MacBook Air M1 2020 Bekas">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kategori *</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($product['category_id'] ?? '')==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kondisi *</label>
                        <select name="condition_status" class="form-select" required>
                            <option value="like_new" <?= ($product['condition_status'] ?? '')=='like_new'?'selected':'' ?>>Seperti Baru (90-100%)</option>
                            <option value="good" <?= ($product['condition_status'] ?? 'good')=='good'?'selected':'' ?>>Baik (70-89%)</option>
                            <option value="fair" <?= ($product['condition_status'] ?? '')=='fair'?'selected':'' ?>>Cukup Baik (50-69%)</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi *</label>
                    <textarea name="description" class="form-control" rows="5" required placeholder="Jelaskan kondisi barang secara detail..."><?= e($product['description'] ?? '') ?></textarea>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Harga Jual (Rp) *</label>
                        <input type="number" name="price" class="form-control" value="<?= $product['price'] ?? '' ?>" required min="1000">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Harga Baru (Rp)</label>
                        <input type="number" name="original_price" class="form-control" value="<?= $product['original_price'] ?? '' ?>" placeholder="Opsional">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Stok</label>
                        <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?? 1 ?>" min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Berat (g)</label>
                        <input type="number" name="weight" class="form-control" value="<?= $product['weight'] ?? 1000 ?>" min="1">
                    </div>
                </div>
                <?php if (!empty($existingImages)): ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Saat Ini</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <?php foreach ($existingImages as $img): ?>
                        <img src="<?= productImage($img) ?>" class="rounded border" style="width:80px;height:80px;object-fit:cover">
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Upload Foto Produk</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">Max 5MB per foto. Format: JPG, PNG, WebP</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> <?= $product ? 'Update' : 'Simpan' ?> Produk</button>
                    <a href="<?= BASE_URL ?>/index.php?page=seller_products" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
