# SecondHand Marketplace

Platform jual beli barang bekas berkualitas dengan sistem verifikasi seller, verifikasi
pembayaran, dan pelacakan pesanan.

Repository ini berisi dua bentuk dari aplikasi yang sama:

| Bentuk | Lokasi | Keterangan |
| --- | --- | --- |
| **Versi statis** | [`site/`](site/) | HTML/CSS/JS murni, dikirim ke **GitHub Pages**. Semua interaksi berjalan di browser tanpa server. |
| **Versi PHP + MySQL** | [`index.php`](index.php), [`src/`](src/), [`database/`](database/) | Aplikasi penuh dengan session, PDO, dan 10 tabel MySQL. Dibutuhkan PHP 8+ dan MySQL/MariaDB. |

Demo statis berguna untuk showcase seluruh alur produk (katalog → keranjang → checkout →
pembayaran → lacak → dashboard) tanpa harus menyiapkan server.

**Live demo:** <https://dafinwork.github.io/secondhand-marketplace/>

---

## Demo Statis (GitHub Pages)

Situs dipublikasikan otomatis oleh GitHub Actions dari folder `site/`.

```
site/
├── index.html          # Beranda
├── products.html       # Katalog + filter/pencarian
├── product.html        # Detail produk (?p=<slug>)
├── cart.html           # Keranjang
├── checkout.html       # Form alamat, kurir, pembayaran
├── payment.html        # Instruksi + unggah bukti bayar
├── orders.html         # Daftar pesanan
├── tracking.html       # Lacak pesanan / nomor resi
├── review.html         # Tulis ulasan
├── wishlist.html       # Wishlist
├── dashboard.html      # Dashboard buyer / seller / admin
├── how-it-works.html   # Alur sistem + FAQ
├── seller.html         # Pendaftaran seller
├── login.html          # Login (demo, tanpa password)
├── register.html       # Registrasi
├── 404.html
├── assets/
│   ├── css/style.css   # Diadaptasi dari src/assets/css/style.css
│   ├── js/data.js      # Data dummy (mirror seed database.sql)
│   ├── js/app.js       # Layout, store, dan helper
│   └── img/products/   # Foto produk
├── robots.txt
├── sitemap.xml
└── .nojekyll
```

### Cara kerja tanpa server

- **Data** diturunkan dari `site/assets/js/data.js`, yang merupakan salinan data seed di
  `database/database.sql`.
- **State** (login, keranjang, wishlist, pesanan, ulasan, produk baru) disimpan di
  `localStorage` dengan awalan kunci `sh_`.
- **Autentikasi** disimulasikan: pilih peran di halaman login, tidak ada validasi password.

### Akun demo

| Peran | Email | Nama |
| --- | --- | --- |
| Admin | `admin@secondhand.com` | Admin SecondHand |
| Seller | `seller1@secondhand.com` | Toko Elektronik Jaya |
| Buyer | `buyer1@secondhand.com` | Budi Santoso |

Seller tambahan: `seller2@secondhand.com` (Preloved Fashion ID) dan
`seller3@secondhand.com` (Second Gadget Store). Buyer tambahan: `buyer2@secondhand.com`
(Siti Rahayu).

### Menjalankan secara lokal

Tidak perlu build step, cukup server statis:

```bash
# Python
python -m http.server 8080 --directory site

# atau Node
npx serve site
```

Buka `http://localhost:8080`.

---

## Versi PHP + MySQL

### Kebutuhan

- PHP 8.0 atau lebih baru (ekstensi `pdo_mysql`)
- MySQL 8.0 atau MariaDB 10.4+

### Instalasi

1. Buat database, lalu import skema:

   ```bash
   mysql -u root -p secondhand_marketplace < database/database.sql
   ```

   Baris `CREATE DATABASE`/`USE` sengaja dikomentari di file SQL agar bisa dipakai di
   hosting share maupun lokal. Aktifkan bila diperlukan.

2. Isi kredensial database di `src/config/database.php`:

   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_NAME', 'secondhand_marketplace');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('BASE_URL', '/niagaflow');
   ```

   `BASE_URL` harus disesuaikan dengan lokasi folder di web server.

3. Arahkan document root web server ke root repository lalu buka `index.php`.

### Struktur backend

```
index.php                     # Front controller + router
src/
├── config/
│   ├── database.php           # Koneksi PDO
│   └── helpers.php            # Auth, flash, format, query helper
├── controllers/
│   ├── AuthController.php     # Login, register, logout
│   ├── ActionController.php   # POST: checkout, bayar, ulasan, verifikasi admin
│   └── ApiController.php      # JSON: cart, wishlist, search, notifikasi
├── views/
│   ├── layouts/               # main_layout.php, dashboard_layout.php
│   ├── public/                # Halaman publik
│   ├── buyer/                 # Dashboard pembeli
│   ├── seller/                # Dashboard seller
│   └── admin/                 # Panel admin
├── uploads/                   # Foto produk, avatar, bukti bayar
└── assets/                    # CSS, JS, gambar
```

### Tabel database

`users`, `categories`, `products`, `carts`, `wishlist`, `orders`, `order_items`, `payments`,
`reviews`, `notifications`. Detail kolom dan relasi ada di
[`docs/DATABASE_SCHEMA.md`](docs/DATABASE_SCHEMA.md).

Semua password pada data seed memakai hash bcrypt dari string `password`.

---

## Pengujian

Skrip di `tools/` dipakai oleh workflow `.github/workflows/validate.yml`:

```bash
npm install --no-save jsdom

node tools/check-inline-scripts.js   # parse semua <script> inline
node tools/check-assets.js            # file wajib, gambar produk, link internal
node tools/smoke-test.js              # logika inti (cart, order, ulasan, notifikasi)
node tools/render-test.js             # render 40 skenario halaman di jsdom
```

Setelah repository GitHub baru dibuat, isi domain Pages pada `site/sitemap.xml` dan
`site/robots.txt`:

```bash
node tools/update-base-url.js https://dafinwork.github.io/secondhand-marketplace
```

Skrip itu idempoten: jalankan ulang kapan pun domain Pages berubah.

---

## Deployment

### GitHub Pages (versi statis)

Situs sudah aktif di <https://dafinwork.github.io/secondhand-marketplace/> dari repository
[`dafinwork/secondhand-marketplace`](https://github.com/dafinwork/secondhand-marketplace).

Workflow `.github/workflows/pages.yml` otomatis berjalan saat ada push ke `main` yang menyentuh
`site/`. Setup yang sudah diterapkan pada repository:

- Pages **Build and deployment → Source: GitHub Actions** (`build_type: workflow`)
- Deploy memakai environment `github-pages`, URL diambil dari output langkah *Deploy to GitHub Pages*

Sekali setelah repository baru dibuat, jalankan

```bash
node tools/update-base-url.js https://<user>.github.io/<repo>
```

agar `site/sitemap.xml`, `site/robots.txt`, dan tag canonical di `site/index.html` memakai domain asli.

> Tiap kali `site/` berubah, cukup `git push`. Workflow membuat artifact dari folder `site/`
> lalu menerbitkannya ke Pages.

### Hosting PHP

Versi PHP tidak bisa berjalan di GitHub Pages karena Pages hanya menyajikan file statis. Pilih salah
satu:

- **Shared hosting** (cPanel, Hostinger, 000webhost) — upload repository, buat database, import
  `database.sql`, sesuaikan `src/config/database.php`.
- **VPS** — deploy Nginx + Apache + PHP-FPM, arahkan `document root` ke root repository.

---

## Lisensi

Tujuan akademik. Bebas dipakai dan dimodifikasi.