<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SecondHand Marketplace</title>
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
            <h2 class="mt-3">Buat Akun Baru</h2>
            <p class="text-muted">Bergabung dengan SecondHand Marketplace</p>
        </div>
        <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> py-2"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=register">
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="Nama lengkap Anda" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">No. Telepon</label>
                <input type="text" name="phone" class="form-control" placeholder="081234567890">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Daftar Sebagai</label>
                <select name="role" class="form-select">
                    <option value="buyer">Pembeli</option>
                    <option value="seller">Penjual</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Konfirmasi Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password" required>
            </div>
            <button type="submit" class="btn btn-primary-custom w-100 py-2 mb-3">
                <i class="bi bi-person-plus me-1"></i> Daftar
            </button>
        </form>
        <p class="text-center text-muted mb-0">Sudah punya akun? <a href="<?= BASE_URL ?>/index.php?page=login" class="fw-semibold">Login</a></p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
