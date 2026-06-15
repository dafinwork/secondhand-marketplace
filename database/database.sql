-- =====================================================
-- SecondHand Marketplace Database
-- Platform Jual Beli Barang Bekas Berkualitas
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+07:00";

-- CREATE DATABASE IF NOT EXISTS `secondhand_marketplace` 
-- DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `secondhand_marketplace`;

-- =====================================================
-- Tabel Users
-- =====================================================
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT 'default.png',
  `role` ENUM('buyer','seller','admin') NOT NULL DEFAULT 'buyer',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel Categories
-- =====================================================
CREATE TABLE `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `icon` VARCHAR(50) DEFAULT 'bi-tag',
  `description` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel Products
-- =====================================================
CREATE TABLE `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `seller_id` INT(11) NOT NULL,
  `category_id` INT(11) NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `description` TEXT NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `original_price` DECIMAL(15,2) DEFAULT NULL,
  `stock` INT(11) NOT NULL DEFAULT 1,
  `condition_status` ENUM('like_new','good','fair') NOT NULL DEFAULT 'good',
  `weight` INT(11) DEFAULT 1000 COMMENT 'dalam gram',
  `images` TEXT DEFAULT NULL COMMENT 'JSON array of image filenames',
  `verification_status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `verification_note` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `view_count` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_product_seller` (`seller_id`),
  KEY `fk_product_category` (`category_id`),
  CONSTRAINT `fk_product_seller` FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel Carts
-- =====================================================
CREATE TABLE `carts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cart_user` (`user_id`),
  KEY `fk_cart_product` (`product_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel Wishlist
-- =====================================================
CREATE TABLE `wishlist` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_wishlist` (`user_id`, `product_id`),
  KEY `fk_wishlist_user` (`user_id`),
  KEY `fk_wishlist_product` (`product_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel Orders
-- =====================================================
CREATE TABLE `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
  `buyer_id` INT(11) NOT NULL,
  `total_amount` DECIMAL(15,2) NOT NULL,
  `shipping_cost` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `shipping_address` TEXT NOT NULL,
  `shipping_method` VARCHAR(50) DEFAULT 'reguler',
  `status` ENUM('pending','confirmed','processing','shipped','delivered','completed','cancelled') NOT NULL DEFAULT 'pending',
  `tracking_number` VARCHAR(100) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_order_buyer` (`buyer_id`),
  CONSTRAINT `fk_order_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel Order Items
-- =====================================================
CREATE TABLE `order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `seller_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL DEFAULT 1,
  `price` DECIMAL(15,2) NOT NULL,
  `subtotal` DECIMAL(15,2) NOT NULL,
  `status` ENUM('pending','confirmed','shipped','delivered','completed','cancelled') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `fk_oi_order` (`order_id`),
  KEY `fk_oi_product` (`product_id`),
  KEY `fk_oi_seller` (`seller_id`),
  CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_oi_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_oi_seller` FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel Payments
-- =====================================================
CREATE TABLE `payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `payment_method` ENUM('bank_transfer','ewallet','cod') NOT NULL DEFAULT 'bank_transfer',
  `bank_name` VARCHAR(50) DEFAULT NULL,
  `account_number` VARCHAR(50) DEFAULT NULL,
  `account_name` VARCHAR(100) DEFAULT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `proof_image` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `verified_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_payment_order` (`order_id`),
  CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel Reviews
-- =====================================================
CREATE TABLE `reviews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `order_id` INT(11) NOT NULL,
  `rating` TINYINT(1) NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment` TEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_review_product` (`product_id`),
  KEY `fk_review_user` (`user_id`),
  KEY `fk_review_order` (`order_id`),
  CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Tabel Notifications
-- =====================================================
CREATE TABLE `notifications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `type` ENUM('order','payment','product','system','review') NOT NULL DEFAULT 'system',
  `reference_id` INT(11) DEFAULT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_notif_user` (`user_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- DUMMY DATA
-- =====================================================

-- Admin (password: password)
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `role`) VALUES
('Admin SecondHand', 'admin@secondhand.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'admin');

-- Sellers (password: password)
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `address`, `role`) VALUES
('Toko Elektronik Jaya', 'seller1@secondhand.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891', 'Jl. Sudirman No. 45, Jakarta Selatan', 'seller'),
('Preloved Fashion ID', 'seller2@secondhand.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567892', 'Jl. Braga No. 12, Bandung', 'seller'),
('Second Gadget Store', 'seller3@secondhand.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567893', 'Jl. Malioboro No. 8, Yogyakarta', 'seller');

-- Buyers (password: password)
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `address`, `role`) VALUES
('Budi Santoso', 'buyer1@secondhand.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567894', 'Jl. Merdeka No. 10, Surabaya', 'buyer'),
('Siti Rahayu', 'buyer2@secondhand.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567895', 'Jl. Diponegoro No. 5, Semarang', 'buyer');

-- Categories
INSERT INTO `categories` (`name`, `slug`, `icon`, `description`) VALUES
('Elektronik', 'elektronik', 'bi-laptop', 'Laptop, PC, dan perangkat elektronik bekas'),
('Smartphone', 'smartphone', 'bi-phone', 'Smartphone dan tablet bekas berkualitas'),
('Fashion', 'fashion', 'bi-bag', 'Pakaian, sepatu, dan aksesoris preloved'),
('Kamera', 'kamera', 'bi-camera', 'Kamera dan aksesoris fotografi bekas'),
('Furniture', 'furniture', 'bi-house', 'Perabotan dan furniture bekas berkualitas'),
('Olahraga', 'olahraga', 'bi-bicycle', 'Peralatan olahraga dan outdoor bekas'),
('Buku & Hobi', 'buku-hobi', 'bi-book', 'Buku, koleksi, dan hobi'),
('Otomotif', 'otomotif', 'bi-car-front', 'Aksesoris dan sparepart kendaraan');

-- Products
INSERT INTO `products` (`seller_id`, `category_id`, `name`, `slug`, `description`, `price`, `original_price`, `stock`, `condition_status`, `weight`, `images`, `verification_status`, `view_count`) VALUES
(2, 1, 'MacBook Air M1 2020 Bekas', 'macbook-air-m1-2020-bekas', 'MacBook Air M1 2020 kondisi 95%. RAM 8GB, SSD 256GB. Fullset box dan charger. Battery cycle count masih rendah. Tidak ada dent atau scratch yang berarti. Cocok untuk produktivitas dan coding.', 8500000.00, 14999000.00, 2, 'like_new', 1290, '["macbook1.jpg","macbook2.jpg"]', 'approved', 245),
(2, 1, 'ASUS ROG Strix G15 Gaming Laptop', 'asus-rog-strix-g15-gaming', 'ASUS ROG Strix G15 Ryzen 7 5800H, RTX 3060. RAM 16GB, SSD 512GB. Kondisi mulus 90%, keyboard nyala semua. Cocok untuk gaming dan rendering. Bonus cooling pad.', 11200000.00, 18999000.00, 1, 'good', 2300, '["rog1.jpg","rog2.jpg"]', 'approved', 189),
(4, 2, 'iPhone 13 Pro 256GB Bekas', 'iphone-13-pro-256gb-bekas', 'iPhone 13 Pro 256GB Sierra Blue. Kondisi 93%, battery health 87%. Fullset box, charger, dan case. Face ID normal, semua fitur berfungsi sempurna. iCloud clean.', 9200000.00, 16999000.00, 1, 'good', 204, '["iphone1.jpg","iphone2.jpg"]', 'approved', 312),
(4, 2, 'Samsung Galaxy S22 Ultra 5G', 'samsung-galaxy-s22-ultra', 'Samsung Galaxy S22 Ultra 256GB Phantom Black. Kondisi 90%, layar mulus tanpa scratch. S-Pen normal. Fullset dengan box dan charger original. SEIN resmi.', 7800000.00, 17999000.00, 2, 'good', 229, '["samsung1.jpg","samsung2.jpg"]', 'approved', 278),
(2, 4, 'Canon EOS 80D Body Only', 'canon-eos-80d-body-only', 'Canon EOS 80D body only. Shutter count 25rb (masih rendah). Kondisi 88%, normal semua fitur. Bonus memory card 32GB dan tas kamera. Cocok untuk foto dan video.', 6500000.00, 12999000.00, 1, 'good', 730, '["canon1.jpg","canon2.jpg"]', 'approved', 156),
(3, 3, 'Nike Air Jordan 1 Retro High OG', 'nike-air-jordan-1-retro', 'Nike Air Jordan 1 Retro High OG ukuran 42. Kondisi VNDS (Very Near Dead Stock) 95%. Cuma pernah dipakai 2 kali. Outsole masih tebal. Bonus box original.', 2100000.00, 3200000.00, 1, 'like_new', 900, '["jordan1.jpg","jordan2.jpg"]', 'approved', 201),
(3, 3, 'Adidas Ultraboost 22 Running', 'adidas-ultraboost-22', 'Adidas Ultraboost 22 ukuran 43. Boost masih empuk, outsole masih 80%. Warna Core Black. Cocok untuk running atau daily. Sudah dicuci bersih.', 850000.00, 2800000.00, 2, 'good', 680, '["adidas1.jpg","adidas2.jpg"]', 'approved', 134),
(2, 5, 'IKEA KALLAX Rak 4x2 Putih', 'ikea-kallax-rak-4x2', 'IKEA KALLAX shelf unit 4x2 warna putih. Kondisi 85%, ada sedikit goresan minor. Sudah dirakit, bisa dibongkar untuk pengiriman. Dimensi 147x77cm.', 650000.00, 1499000.00, 1, 'fair', 25000, '["kallax1.jpg","kallax2.jpg"]', 'approved', 98),
(4, 2, 'iPad Air 4 64GB WiFi', 'ipad-air-4-64gb-wifi', 'iPad Air 4 64GB WiFi Only Sky Blue. Kondisi 92%, layar mulus. Battery health 95%. Fullset box dan charger. Bonus case dan tempered glass. Cocok untuk kuliah dan desain.', 5500000.00, 9499000.00, 1, 'like_new', 458, '["ipad1.jpg","ipad2.jpg"]', 'approved', 167),
(3, 4, 'Sony Alpha A6400 + Kit Lens', 'sony-alpha-a6400-kit', 'Sony A6400 dengan lens kit 16-50mm. Shutter count 10rb. Kondisi 90%, LCD flip normal. Bonus memory card 64GB, tas, dan filter UV. Mirrorless terbaik untuk vlog.', 9800000.00, 14499000.00, 1, 'good', 850, '["sony1.jpg","sony2.jpg"]', 'approved', 223),
(2, 1, 'ThinkPad X1 Carbon Gen 9', 'thinkpad-x1-carbon-gen9', 'Lenovo ThinkPad X1 Carbon Gen 9. Intel i7-1165G7, 16GB RAM, 512GB SSD. Layar 14" FHD IPS. Keyboard legendaris. Kondisi 92%, cocok untuk profesional.', 10500000.00, 22000000.00, 1, 'like_new', 1130, '["thinkpad1.jpg","thinkpad2.jpg"]', 'approved', 176),
(3, 5, 'Kursi Gaming SecretLab Titan', 'kursi-gaming-secretlab-titan', 'SecretLab Titan 2022 warna Stealth. Kondisi 88%, bantal masih empuk. Lumbar support dan armrest berfungsi normal. Bonus bantal leher original.', 3200000.00, 6499000.00, 1, 'good', 28000, '["secretlab1.jpg","secretlab2.jpg"]', 'approved', 145);

-- Dummy orders
INSERT INTO `orders` (`invoice_number`, `buyer_id`, `total_amount`, `shipping_cost`, `shipping_address`, `shipping_method`, `status`, `tracking_number`) VALUES
('INV-20250501-0001', 5, 8500000.00, 25000.00, 'Jl. Merdeka No. 10, Surabaya, Jawa Timur 60111', 'reguler', 'completed', 'JNE1234567890'),
('INV-20250510-0002', 6, 2100000.00, 15000.00, 'Jl. Diponegoro No. 5, Semarang, Jawa Tengah 50241', 'reguler', 'shipped', 'SiCepat9876543210'),
('INV-20250515-0003', 5, 9200000.00, 20000.00, 'Jl. Merdeka No. 10, Surabaya, Jawa Timur 60111', 'express', 'processing', NULL);

INSERT INTO `order_items` (`order_id`, `product_id`, `seller_id`, `quantity`, `price`, `subtotal`, `status`) VALUES
(1, 1, 2, 1, 8500000.00, 8500000.00, 'completed'),
(2, 6, 3, 1, 2100000.00, 2100000.00, 'shipped'),
(3, 3, 4, 1, 9200000.00, 9200000.00, 'processing');

INSERT INTO `payments` (`order_id`, `payment_method`, `bank_name`, `amount`, `status`, `verified_at`) VALUES
(1, 'bank_transfer', 'BCA', 8525000.00, 'verified', '2025-05-01 14:00:00'),
(2, 'bank_transfer', 'Mandiri', 2115000.00, 'verified', '2025-05-10 16:30:00'),
(3, 'bank_transfer', 'BNI', 9220000.00, 'pending', NULL);

INSERT INTO `reviews` (`product_id`, `user_id`, `order_id`, `rating`, `comment`) VALUES
(1, 5, 1, 5, 'Barang sesuai deskripsi, kondisi mulus banget. Packing rapi dan aman. Seller ramah dan fast response. Recommended!');

INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `reference_id`) VALUES
(5, 'Pesanan Selesai', 'Pesanan INV-20250501-0001 telah selesai. Terima kasih telah berbelanja!', 'order', 1),
(2, 'Produk Disetujui', 'Produk MacBook Air M1 2020 telah diverifikasi dan dipublikasikan.', 'product', 1),
(6, 'Pesanan Dikirim', 'Pesanan INV-20250510-0002 sedang dalam pengiriman. No. Resi: SiCepat9876543210', 'order', 2);

COMMIT;
