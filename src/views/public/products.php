<?php
$pageTitle = 'Produk - SecondHand Marketplace';
$db = getDB();

$categoryId = intval($_GET['category'] ?? 0);
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'newest';
$minPrice = intval($_GET['min_price'] ?? 0);
$maxPrice = intval($_GET['max_price'] ?? 0);
$condition = $_GET['condition'] ?? '';
$pageNum = max(1, intval($_GET['p'] ?? 1));
$perPage = 12;
$offset = ($pageNum - 1) * $perPage;

// Build query
$where = "WHERE p.verification_status = 'approved' AND p.is_active = 1";
$params = [];
if ($categoryId > 0) { $where .= " AND p.category_id = ?"; $params[] = $categoryId; }
if ($search) { $where .= " AND p.name LIKE ?"; $params[] = "%$search%"; }
if ($minPrice > 0) { $where .= " AND p.price >= ?"; $params[] = $minPrice; }
if ($maxPrice > 0) { $where .= " AND p.price <= ?"; $params[] = $maxPrice; }
if ($condition) { $where .= " AND p.condition_status = ?"; $params[] = $condition; }

$orderBy = match($sort) {
    'price_low' => 'p.price ASC',
    'price_high' => 'p.price DESC',
    'popular' => 'p.view_count DESC',
    default => 'p.created_at DESC'
};

// Count
$countStmt = $db->prepare("SELECT COUNT(*) FROM products p $where");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $perPage);

// Fetch
$stmt = $db->prepare("SELECT p.*, u.name as seller_name, c.name as category_name FROM products p JOIN users u ON p.seller_id = u.id JOIN categories c ON p.category_id = c.id $where ORDER BY $orderBy LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $db->query("SELECT * FROM categories WHERE is_active = 1")->fetchAll();
$currentCat = null;
if ($categoryId) { foreach ($categories as $c) { if ($c['id'] == $categoryId) { $currentCat = $c; break; } } }
?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Home</a></li>
                <li class="breadcrumb-item active"><?= $currentCat ? e($currentCat['name']) : 'Semua Produk' ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        <!-- Sidebar Filter -->
        <div class="col-lg-3">
            <div class="bg-white p-3 rounded-3 border mb-3">
                <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-1"></i> Filter</h6>
                <form method="GET" action="<?= BASE_URL ?>/index.php">
                    <input type="hidden" name="page" value="products">
                    <?php if ($search): ?><input type="hidden" name="search" value="<?= e($search) ?>"><?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kategori</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="0">Semua Kategori</option>
                            <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $categoryId==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Harga Minimum</label>
                        <input type="number" name="min_price" class="form-control form-control-sm" value="<?= $minPrice ?: '' ?>" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Harga Maksimum</label>
                        <input type="number" name="max_price" class="form-control form-control-sm" value="<?= $maxPrice ?: '' ?>" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kondisi</label>
                        <select name="condition" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="like_new" <?= $condition=='like_new'?'selected':'' ?>>Seperti Baru</option>
                            <option value="good" <?= $condition=='good'?'selected':'' ?>>Baik</option>
                            <option value="fair" <?= $condition=='fair'?'selected':'' ?>>Cukup Baik</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary-custom btn-sm w-100">Terapkan Filter</button>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <p class="mb-0 text-muted"><strong><?= $total ?></strong> produk ditemukan</p>
                <div class="d-flex gap-2 align-items-center">
                    <label class="small text-muted">Urutkan:</label>
                    <select class="form-select form-select-sm" style="width:auto" onchange="location.href=this.value">
                        <?php
                        $baseQ = BASE_URL . "/index.php?page=products" . ($categoryId ? "&category=$categoryId" : '') . ($search ? "&search=".urlencode($search) : '');
                        ?>
                        <option value="<?= $baseQ ?>&sort=newest" <?= $sort=='newest'?'selected':'' ?>>Terbaru</option>
                        <option value="<?= $baseQ ?>&sort=price_low" <?= $sort=='price_low'?'selected':'' ?>>Harga Terendah</option>
                        <option value="<?= $baseQ ?>&sort=price_high" <?= $sort=='price_high'?'selected':'' ?>>Harga Tertinggi</option>
                        <option value="<?= $baseQ ?>&sort=popular" <?= $sort=='popular'?'selected':'' ?>>Terpopuler</option>
                    </select>
                </div>
            </div>

            <?php if (empty($products)): ?>
            <div class="empty-state bg-white rounded-3 border">
                <i class="bi bi-search"></i>
                <h5>Produk tidak ditemukan</h5>
                <p>Coba ubah filter atau kata kunci pencarian</p>
            </div>
            <?php else: ?>
            <div class="row g-3">
                <?php foreach ($products as $p):
                    $imgs = json_decode($p['images'], true);
                    $img = !empty($imgs) ? $imgs[0] : '';
                    $discount = $p['original_price'] > 0 ? round((1 - $p['price']/$p['original_price'])*100) : 0;
                ?>
                <div class="col-6 col-md-4">
                    <div class="product-card">
                        <div class="card-img-wrapper">
                            <img src="<?= productImage($img) ?>" alt="<?= e($p['name']) ?>">
                            <div class="condition-badge"><?= conditionLabel($p['condition_status']) ?></div>
                            <button class="wishlist-btn" onclick="toggleWishlist(<?= $p['id'] ?>, this)"><i class="bi bi-heart"></i></button>
                            <?php if ($discount > 0): ?><span class="discount-badge">-<?= $discount ?>%</span><?php endif; ?>
                        </div>
                        <div class="card-body">
                            <a href="<?= BASE_URL ?>/index.php?page=product_detail&id=<?= $p['id'] ?>" class="text-decoration-none">
                                <div class="product-title"><?= e($p['name']) ?></div>
                            </a>
                            <div class="product-price"><?= formatRupiah($p['price']) ?></div>
                            <?php if ($p['original_price'] > 0): ?><div class="original-price"><?= formatRupiah($p['original_price']) ?></div><?php endif; ?>
                            <div class="product-seller"><i class="bi bi-shop"></i> <?= e($p['seller_name']) ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $pageNum<=1?'disabled':'' ?>">
                        <a class="page-link" href="<?= $baseQ ?>&sort=<?= $sort ?>&p=<?= $pageNum-1 ?>"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i==$pageNum?'active':'' ?>">
                        <a class="page-link" href="<?= $baseQ ?>&sort=<?= $sort ?>&p=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $pageNum>=$totalPages?'disabled':'' ?>">
                        <a class="page-link" href="<?= $baseQ ?>&sort=<?= $sort ?>&p=<?= $pageNum+1 ?>"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
