<?php
$db = getDB();
// Get categories
$cats = $db->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.verification_status='approved' AND p.is_active=1) as product_count FROM categories c WHERE c.is_active = 1")->fetchAll();
// Get featured products
$featured = $db->query("SELECT p.*, u.name as seller_name, c.name as category_name FROM products p JOIN users u ON p.seller_id = u.id JOIN categories c ON p.category_id = c.id WHERE p.verification_status = 'approved' AND p.is_active = 1 ORDER BY p.view_count DESC LIMIT 8")->fetchAll();
// Stats
$totalProducts = $db->query("SELECT COUNT(*) FROM products WHERE verification_status='approved'")->fetchColumn();
$totalUsers = $db->query("SELECT COUNT(*) FROM users WHERE role='buyer'")->fetchColumn();
$totalSellers = $db->query("SELECT COUNT(*) FROM users WHERE role='seller'")->fetchColumn();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container position-relative" style="z-index:2">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="slide-up">
                    <span class="badge bg-white text-primary px-3 py-2 mb-3 fw-semibold" style="font-size:0.85rem">
                        <i class="bi bi-patch-check-fill me-1"></i> Barang Terverifikasi
                    </span>
                    <h1 class="hero-title">Temukan Barang Bekas <span>Berkualitas</span> Harga Terbaik</h1>
                    <p class="hero-subtitle">Platform jual beli barang bekas terpercaya dengan sistem verifikasi kualitas. Hemat hingga 70% dari harga baru!</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="<?= BASE_URL ?>/index.php?page=products" class="btn btn-primary-custom btn-lg">
                            <i class="bi bi-search me-2"></i>Mulai Belanja
                        </a>
                        <a href="<?= BASE_URL ?>/index.php?page=register" class="btn btn-outline-custom btn-lg">
                            <i class="bi bi-shop me-2"></i>Mulai Jualan
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <h3><?= number_format($totalProducts) ?>+</h3>
                            <p>Produk Tersedia</p>
                        </div>
                        <div class="hero-stat">
                            <h3><?= number_format($totalUsers) ?>+</h3>
                            <p>Pembeli Aktif</p>
                        </div>
                        <div class="hero-stat">
                            <h3><?= number_format($totalSellers) ?>+</h3>
                            <p>Penjual Terpercaya</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-5">
    <div class="container">
        <div class="section-header">
            <h2>Jelajahi Kategori</h2>
            <p>Temukan berbagai kategori barang bekas berkualitas</p>
            <div class="section-divider"></div>
        </div>
        <div class="row g-3">
            <?php foreach ($cats as $cat): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= BASE_URL ?>/index.php?page=products&category=<?= $cat['id'] ?>" class="text-decoration-none">
                    <div class="category-card">
                        <div class="icon"><i class="bi <?= e($cat['icon']) ?>"></i></div>
                        <h6><?= e($cat['name']) ?></h6>
                        <small class="text-muted"><?= $cat['product_count'] ?> produk</small>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="section-header">
            <h2>Produk Populer</h2>
            <p>Produk bekas terlaris pilihan para pembeli</p>
            <div class="section-divider"></div>
        </div>
        <div class="row g-4">
            <?php foreach ($featured as $p):
                $imgs = json_decode($p['images'], true);
                $img = !empty($imgs) ? $imgs[0] : '';
                $discount = $p['original_price'] > 0 ? round((1 - $p['price']/$p['original_price'])*100) : 0;
            ?>
            <div class="col-6 col-md-4 col-lg-3 animate-on-scroll">
                <div class="product-card">
                    <div class="card-img-wrapper">
                        <img src="<?= productImage($img) ?>" alt="<?= e($p['name']) ?>">
                        <div class="condition-badge"><?= conditionLabel($p['condition_status']) ?></div>
                        <button class="wishlist-btn" onclick="toggleWishlist(<?= $p['id'] ?>, this)" title="Wishlist">
                            <i class="bi bi-heart"></i>
                        </button>
                        <?php if ($discount > 0): ?>
                        <span class="discount-badge">-<?= $discount ?>%</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <a href="<?= BASE_URL ?>/index.php?page=product_detail&id=<?= $p['id'] ?>" class="text-decoration-none">
                            <div class="product-title"><?= e($p['name']) ?></div>
                        </a>
                        <div class="product-price"><?= formatRupiah($p['price']) ?></div>
                        <?php if ($p['original_price'] > 0): ?>
                        <div class="original-price"><?= formatRupiah($p['original_price']) ?></div>
                        <?php endif; ?>
                        <div class="product-seller">
                            <i class="bi bi-shop"></i> <?= e($p['seller_name']) ?>
                            <span class="verified-badge"><i class="bi bi-patch-check-fill"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/index.php?page=products" class="btn btn-primary-custom">
                Lihat Semua Produk <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-5">
    <div class="container">
        <div class="section-header">
            <h2>Cara Kerja</h2>
            <p>Mudah, aman, dan terpercaya</p>
            <div class="section-divider"></div>
        </div>
        <div class="row g-4">
            <div class="col-md-4 text-center animate-on-scroll">
                <div class="rounded-circle bg-primary-subtle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px">
                    <i class="bi bi-search fs-2 text-primary"></i>
                </div>
                <h5 class="fw-bold">1. Cari & Pilih</h5>
                <p class="text-muted">Jelajahi ribuan barang bekas berkualitas dengan harga terjangkau</p>
            </div>
            <div class="col-md-4 text-center animate-on-scroll">
                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px">
                    <i class="bi bi-patch-check fs-2 text-success"></i>
                </div>
                <h5 class="fw-bold">2. Verifikasi</h5>
                <p class="text-muted">Semua produk diverifikasi kualitasnya oleh tim kami sebelum dipublikasikan</p>
            </div>
            <div class="col-md-4 text-center animate-on-scroll">
                <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px">
                    <i class="bi bi-bag-check fs-2 text-warning"></i>
                </div>
                <h5 class="fw-bold">3. Beli & Terima</h5>
                <p class="text-muted">Bayar dengan aman dan terima barang langsung di rumahmu</p>
            </div>
        </div>
    </div>
</section>
