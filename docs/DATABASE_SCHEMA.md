# Database Schema - SecondHand Marketplace

## 1. Tabel `users`
Menyimpan data pengguna (Admin, Penjual, Pembeli).

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | INT(11) | Primary Key, Auto Increment |
| name | VARCHAR(100) | Nama lengkap |
| email | VARCHAR(100) | Email unik |
| password | VARCHAR(255) | Password hashed (bcrypt) |
| phone | VARCHAR(20) | Nomor telepon |
| address | TEXT | Alamat rumah/toko |
| avatar | VARCHAR(255) | Foto profil |
| role | ENUM | 'buyer', 'seller', 'admin' |
| is_active | TINYINT(1) | Status aktif akun |
| created_at | TIMESTAMP | Waktu daftar |
| updated_at | TIMESTAMP | Waktu update |

## 2. Tabel `categories`
Menyimpan data kategori produk.

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | INT(11) | Primary Key, Auto Increment |
| name | VARCHAR(100) | Nama kategori |
| slug | VARCHAR(100) | URL slug kategori unik |
| icon | VARCHAR(50) | Class icon Bootstrap |
| description | TEXT | Deskripsi singkat |
| is_active | TINYINT(1) | Status aktif kategori |
| created_at | TIMESTAMP | Waktu dibuat |

## 3. Tabel `products`
Menyimpan data produk/barang bekas yang dijual.

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | INT(11) | Primary Key, Auto Increment |
| seller_id | INT(11) | Foreign Key (users.id) |
| category_id | INT(11) | Foreign Key (categories.id) |
| name | VARCHAR(200) | Nama produk |
| slug | VARCHAR(220) | URL slug produk unik |
| description | TEXT | Deskripsi lengkap produk |
| price | DECIMAL(15,2) | Harga jual saat ini |
| original_price| DECIMAL(15,2) | Harga beli asli (opsional) |
| stock | INT(11) | Jumlah stok tersedia |
| condition_status| ENUM | 'like_new', 'good', 'fair' |
| weight | INT(11) | Berat dalam gram |
| images | TEXT | JSON array foto produk |
| verification_status | ENUM | 'pending', 'approved', 'rejected' |
| verification_note | TEXT | Alasan penolakan dari admin |
| is_active | TINYINT(1) | Status publikasi |
| view_count | INT(11) | Jumlah produk dilihat |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diupdate |

## 4. Tabel `orders`
Menyimpan data transaksi keseluruhan.

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | INT(11) | Primary Key, Auto Increment |
| invoice_number| VARCHAR(50) | Nomor invoice unik |
| buyer_id | INT(11) | Foreign Key (users.id) |
| total_amount | DECIMAL(15,2) | Total harga produk |
| shipping_cost | DECIMAL(15,2) | Total ongkos kirim |
| shipping_address| TEXT | Alamat pengiriman lengkap |
| shipping_method | VARCHAR(50) | Metode pengiriman (reguler/express)|
| status | ENUM | 'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'completed', 'cancelled' |
| tracking_number | VARCHAR(100) | Nomor resi pengiriman |
| notes | TEXT | Catatan pembeli |
| created_at | TIMESTAMP | Waktu order dibuat |
| updated_at | TIMESTAMP | Waktu order diupdate |

## 5. Tabel `order_items`
Menyimpan rincian produk yang dibeli per transaksi.

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | INT(11) | Primary Key, Auto Increment |
| order_id | INT(11) | Foreign Key (orders.id) |
| product_id | INT(11) | Foreign Key (products.id) |
| seller_id | INT(11) | Foreign Key (users.id) |
| quantity | INT(11) | Jumlah dibeli |
| price | DECIMAL(15,2) | Harga per item saat dibeli |
| subtotal | DECIMAL(15,2) | quantity * price |
| status | ENUM | Status item dari penjual |

## 6. Tabel `payments`
Menyimpan data pembayaran pembeli.

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | INT(11) | Primary Key, Auto Increment |
| order_id | INT(11) | Foreign Key (orders.id) |
| payment_method| ENUM | 'bank_transfer', 'ewallet', 'cod' |
| bank_name | VARCHAR(50) | Nama bank pengirim |
| account_number| VARCHAR(50) | No rekening pengirim |
| account_name | VARCHAR(100) | Atas nama pengirim |
| amount | DECIMAL(15,2) | Jumlah yang dibayar |
| proof_image | VARCHAR(255) | File bukti transfer |
| status | ENUM | 'pending', 'verified', 'rejected' |
| verified_at | TIMESTAMP | Waktu diverifikasi admin |
| created_at | TIMESTAMP | Waktu upload bukti |

## 7. Tabel `reviews`
Menyimpan rating dan ulasan barang.

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | INT(11) | Primary Key, Auto Increment |
| product_id | INT(11) | Foreign Key (products.id) |
| user_id | INT(11) | Foreign Key (users.id) pembeli |
| order_id | INT(11) | Foreign Key (orders.id) |
| rating | TINYINT(1) | Bintang 1-5 |
| comment | TEXT | Ulasan text |
| image | VARCHAR(255) | Foto ulasan |
| created_at | TIMESTAMP | Waktu review dibuat |

## 8. Tabel `notifications`
Menyimpan notifikasi in-app untuk pengguna.

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | INT(11) | Primary Key, Auto Increment |
| user_id | INT(11) | Foreign Key (users.id) |
| title | VARCHAR(200) | Judul notifikasi |
| message | TEXT | Isi notifikasi |
| type | ENUM | 'order', 'payment', 'product', 'system', 'review' |
| reference_id | INT(11) | ID referensi data terkait |
| is_read | TINYINT(1) | Status sudah dibaca |
| created_at | TIMESTAMP | Waktu notifikasi muncul |
