# SecondHand Marketplace

> Platform Jual Beli Barang Bekas Berkualitas dengan Sistem Verifikasi Kualitas Produk

## 📋 Deskripsi

SecondHand Marketplace adalah platform e-commerce modern untuk jual beli barang bekas berkualitas yang mempertemukan penjual dan pembeli dengan sistem validasi produk. Setiap barang yang dijual harus melalui proses verifikasi oleh admin untuk memastikan kelayakan dan kualitas.

## 🛠 Teknologi

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript (Vanilla)
- **Backend**: PHP Native (MVC Pattern)
- **Database**: MySQL
- **Server**: Laragon (Apache + PHP + MySQL)

## 📁 Struktur Folder

```
├── src/
│   ├── config/
│   │   ├── database.php          # Konfigurasi database & koneksi PDO
│   │   └── helpers.php           # Helper functions (security, formatting)
│   ├── controllers/
│   │   ├── AuthController.php    # Login, Register, Logout
│   │   ├── ApiController.php     # AJAX API endpoints
│   │   └── ActionController.php  # Form POST handlers
│   ├── models/                   # Folder models
│   ├── views/
│   │   ├── admin/                # Dashboard admin
│   │   ├── seller/               # Dashboard penjual
│   │   ├── buyer/                # Dashboard pembeli
│   │   ├── public/               # Halaman publik (home, produk, login, dll)
│   │   └── layouts/              # Layout template
│   ├── assets/
│   │   ├── css/style.css         # Stylesheet utama
│   │   ├── js/app.js             # JavaScript utama
│   │   └── img/                  # Gambar assets
│   └── uploads/                  # Upload files (products, payments, reviews)
├── database/
│   └── database.sql              # SQL Database + Dummy Data
├── docs/
│   ├── README.md                 # Dokumentasi utama
│   ├── USER_MANUAL.md            # Panduan user
│   └── DATABASE_SCHEMA.md        # Skema database
├── presentation/                 # Folder presentasi
├── index.php                     # Entry point / Router
└── TESTING_REPORT.md             # Laporan pengujian
```

## 👥 User Roles

| Role | Deskripsi |
|------|-----------|
| **Pembeli (Buyer)** | Browse, beli, review produk |
| **Penjual (Seller)** | CRUD produk, kelola pesanan |
| **Admin** | Verifikasi produk, kelola semua data |
| **System Automation** | Auto update stok, invoice, notifikasi |

## 🚀 Cara Instalasi

### Prasyarat
- Laragon (Apache, PHP 7.4+, MySQL)

### Langkah-langkah

1. **Clone/copy project** ke folder `C:\laragon\www\`

2. **Buat database** melalui phpMyAdmin:
   - Buka `http://localhost/phpmyadmin`
   - Buat database baru: `secondhand_marketplace`
   - Import file: `database/secondhand_marketplace.sql`

3. **Konfigurasi** (opsional):
   - Edit `config/database.php` jika perlu mengubah kredensial database

4. **Akses website**:
   - Buka browser: `http://localhost/EAS_INFO2526_202210715241_MUHAMADDAFINALDZAKY 1/`

## 🔐 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@secondhand.com | password |
| Seller | seller1@secondhand.com | password |
| Seller | seller2@secondhand.com | password |
| Buyer | buyer1@secondhand.com | password |
| Buyer | buyer2@secondhand.com | password |

> **Note**: Password hash di database menggunakan bcrypt. Password default semua akun demo adalah `password`.

## ✨ Fitur Utama

### Pembeli
- ✅ Register & Login (bcrypt hashing)
- ✅ Browse & search produk realtime (AJAX)
- ✅ Filter kategori, harga, kondisi
- ✅ Detail produk lengkap
- ✅ Add to cart (AJAX)
- ✅ Wishlist
- ✅ Checkout & payment
- ✅ Upload bukti pembayaran
- ✅ Order tracking
- ✅ Review & rating

### Penjual
- ✅ CRUD produk dengan multiple images
- ✅ Kelola stok
- ✅ Kelola pesanan masuk
- ✅ Konfirmasi & input resi pengiriman
- ✅ Dashboard statistik penjualan

### Admin
- ✅ Dashboard analytics
- ✅ Manage users (aktif/nonaktif)
- ✅ Manage kategori
- ✅ Verifikasi kualitas produk
- ✅ Manage pesanan
- ✅ System settings & report

### System Automation
- ✅ Auto update stok setelah checkout
- ✅ Auto generate invoice number
- ✅ Auto calculate ongkir
- ✅ Auto notification system (in-app + email)
- ✅ Remember Me login

## 🔒 Keamanan

- Password hashing dengan **bcrypt**
- SQL Injection prevention dengan **PDO Prepared Statements**
- XSS protection dengan **htmlspecialchars()**
- Session management
- Form validation (client & server side)

## 📱 Responsive Design

Website fully responsive untuk semua ukuran layar:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (< 768px)

---

**Dibuat untuk EAS INFO2526 - 2022**
