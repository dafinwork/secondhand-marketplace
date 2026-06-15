<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SecondHand Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card fade-in">
        <div class="text-center mb-4">
            <a href="<?= BASE_URL ?>/index.php" class="text-decoration-none">
                <h4 class="fw-bold"><i class="bi bi-recycle text-primary"></i> Second<span class="text-primary">Hand</span></h4>
            </a>
            <h2 class="mt-3">Selamat Datang!</h2>
            <p class="text-muted">Masuk ke akun Anda</p>
        </div>
        <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> py-2"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=login">
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                <label class="form-check-label" for="rememberMe">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-primary-custom w-100 py-2 mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </button>
        </form>
        <p class="text-center text-muted mb-0">Belum punya akun? <a href="<?= BASE_URL ?>/index.php?page=register" class="fw-semibold">Daftar Sekarang</a></p>
        <hr>
        <div class="small text-muted text-center">
            <p class="mb-1"><strong>Demo Login:</strong></p>
            <p class="mb-0">Admin: admin@secondhand.com</p>
            <p class="mb-0">Seller: seller1@secondhand.com</p>
            <p class="mb-0">Buyer: buyer1@secondhand.com</p>
            <p class="mb-0">Password: password (semua akun)</p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
