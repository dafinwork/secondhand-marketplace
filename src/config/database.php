<?php
/**
 * Database Configuration
 * SecondHand Marketplace
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'secondhand_marketplace');

// Base URL - sesuaikan dengan nama folder di Laragon
define('BASE_URL', '/EAS_INFO2526_202210715241_MUHAMADDAFINALDZAKY 1');

// Site info
define('SITE_NAME', 'SecondHand Marketplace');
define('SITE_TAGLINE', 'Jual Beli Barang Bekas Berkualitas');
define('SITE_EMAIL', 'no-reply@secondhand.local');
define('APP_SECRET_KEY', 'ChangeThisToASecretKey123!');

// Upload paths
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('PRODUCT_IMG_PATH', UPLOAD_PATH . 'products/');
define('AVATAR_PATH', UPLOAD_PATH . 'avatars/');
define('PAYMENT_IMG_PATH', UPLOAD_PATH . 'payments/');
define('REVIEW_IMG_PATH', UPLOAD_PATH . 'reviews/');

// PDO Database Connection
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
    return $pdo;
}
