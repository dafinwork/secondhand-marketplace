<?php
/**
 * Helper Functions
 * SecondHand Marketplace
 */

// XSS Protection - sanitize output
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Redirect helper
function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function isRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Silakan login terlebih dahulu.'];
        redirect('/index.php?page=login');
    }
}

// Require specific role
function requireRole($role) {
    requireLogin();
    if (!isRole($role)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Anda tidak memiliki akses ke halaman ini.'];
        redirect('/index.php');
    }
}

// Format Rupiah
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Generate invoice number
function generateInvoice() {
    return 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

// Calculate shipping cost (simple)
function calculateShipping($weight, $method = 'reguler') {
    $base = ceil($weight / 1000) * 8000;
    if ($method === 'express') $base *= 2;
    if ($method === 'same_day') $base *= 3;
    return $base;
}

// Create slug from string
function createSlug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $slug . '-' . substr(uniqid(), -5);
}

// Flash message
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function createRememberMeToken($userId, $passwordHash) {
    return hash_hmac('sha256', $userId . '|' . $passwordHash, APP_SECRET_KEY);
}

function setRememberMeCookie($userId, $passwordHash) {
    $token = createRememberMeToken($userId, $passwordHash);
    setcookie('remember_me', $userId . '|' . $token, time() + 60 * 60 * 24 * 30, '/', '', false, true);
}

function clearRememberMeCookie() {
    setcookie('remember_me', '', time() - 3600, '/', '', false, true);
    if (isset($_COOKIE['remember_me'])) {
        unset($_COOKIE['remember_me']);
    }
}

function checkRememberMe() {
    if (isLoggedIn()) {
        return;
    }
    if (empty($_COOKIE['remember_me'])) {
        return;
    }
    $parts = explode('|', $_COOKIE['remember_me'], 2);
    if (count($parts) !== 2) {
        clearRememberMeCookie();
        return;
    }
    [$userId, $token] = $parts;
    $userId = intval($userId);
    if ($userId <= 0) {
        clearRememberMeCookie();
        return;
    }

    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ? AND is_active = 1');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) {
        clearRememberMeCookie();
        return;
    }
    if (!hash_equals(createRememberMeToken($user['id'], $user['password']), $token)) {
        clearRememberMeCookie();
        return;
    }
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
}

function sendEmail($to, $subject, $body) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SITE_NAME . " <" . SITE_EMAIL . ">\r\n";
    return mail($to, $subject, $body, $headers);
}

function createNotification($userId, $title, $message, $type = 'system', $refId = null) {
    $db = getDB();
    $stmt = $db->prepare('INSERT INTO notifications (user_id, title, message, type, reference_id) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$userId, $title, $message, $type, $refId]);

    $stmt = $db->prepare('SELECT email FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if ($user && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $emailBody = '<h2>' . e(SITE_NAME) . '</h2><p>' . e($message) . '</p><p><strong>Referensi:</strong> ' . e($title) . '</p>';
        sendEmail($user['email'], $title, $emailBody);
    }
}

// Get unread notification count
function getUnreadNotifCount($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

// Get cart count
function getCartCount($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT COALESCE(SUM(quantity), 0) FROM carts WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

// Upload file helper
function uploadFile($file, $destination, $allowedTypes = ['jpg','jpeg','png','webp']) {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) return false;
    
    if ($file['size'] > 5 * 1024 * 1024) return false; // Max 5MB
    
    $filename = uniqid() . '_' . time() . '.' . $ext;
    
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $destination . $filename)) {
        return $filename;
    }
    return false;
}

// Time ago
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->y > 0) return $diff->y . ' tahun lalu';
    if ($diff->m > 0) return $diff->m . ' bulan lalu';
    if ($diff->d > 0) return $diff->d . ' hari lalu';
    if ($diff->h > 0) return $diff->h . ' jam lalu';
    if ($diff->i > 0) return $diff->i . ' menit lalu';
    return 'Baru saja';
}

// Condition label
function conditionLabel($status) {
    $labels = [
        'like_new' => '<span class="badge bg-success">Seperti Baru</span>',
        'good' => '<span class="badge bg-primary">Baik</span>',
        'fair' => '<span class="badge bg-warning text-dark">Cukup Baik</span>'
    ];
    return $labels[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}

// Verification label
function verificationLabel($status) {
    $labels = [
        'pending' => '<span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Menunggu Verifikasi</span>',
        'approved' => '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Terverifikasi</span>',
        'rejected' => '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>'
    ];
    return $labels[$status] ?? '';
}

// Order status label
function orderStatusLabel($status) {
    $labels = [
        'pending' => '<span class="badge bg-warning text-dark">Menunggu Pembayaran</span>',
        'confirmed' => '<span class="badge bg-info">Dikonfirmasi</span>',
        'processing' => '<span class="badge bg-primary">Diproses</span>',
        'shipped' => '<span class="badge bg-info">Dikirim</span>',
        'delivered' => '<span class="badge bg-success">Diterima</span>',
        'completed' => '<span class="badge bg-success">Selesai</span>',
        'cancelled' => '<span class="badge bg-danger">Dibatalkan</span>'
    ];
    return $labels[$status] ?? '';
}

// Get product image URL
function productImage($filename) {
    if (!$filename || !file_exists(PRODUCT_IMG_PATH . $filename)) {
        return BASE_URL . '/src/assets/img/no-image.svg';
    }
    return BASE_URL . '/src/uploads/products/' . $filename;
}

// Truncate text
function truncate($text, $length = 100) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}
