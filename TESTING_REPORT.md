# Testing Report - SecondHand Marketplace

Laporan ini mendokumentasikan hasil pengujian fungsionalitas dan keamanan dari platform SecondHand Marketplace sesuai dengan spesifikasi yang diminta.

## 1. Fungsionalitas Role

| Fitur | Status | Keterangan |
|---|---|---|
| **Pembeli (Buyer)** | | |
| Register & Login | ✅ Lulus | Berhasil dengan hash bcrypt. |
| Browse Produk | ✅ Lulus | List produk tampil dengan AJAX pencarian. |
| Add to Cart | ✅ Lulus | Menambah jumlah item dan update total sukses. |
| Checkout | ✅ Lulus | Menyimpan alamat dan memilih metode pengiriman. |
| Konfirmasi Pembayaran | ✅ Lulus | Upload file bukti pembayaran berhasil disimpan ke server. |
| Order Tracking | ✅ Lulus | Status berubah sesuai update dari admin/seller. |
| Review & Rating | ✅ Lulus | Dapat memberikan bintang dan komentar untuk pesanan selesai. |
| **Penjual (Seller)** | | |
| Kelola Produk (CRUD) | ✅ Lulus | Menambah barang bekas beserta kondisinya sukses. |
| Kelola Pesanan | ✅ Lulus | Bisa update nomor resi dan mengubah status menjadi "Shipped". |
| Dashboard Penjualan | ✅ Lulus | Menampilkan jumlah produk dan total pesanan. |
| **Administrator (Admin)** | | |
| Dashboard Admin | ✅ Lulus | Menampilkan data user, pesanan, dan kategori. |
| Kelola User | ✅ Lulus | Blokir/aktifkan user berhasil. |
| Verifikasi Produk | ✅ Lulus | Produk berstatus 'pending' berhasil disetujui atau ditolak. |
| Verifikasi Pembayaran| ✅ Lulus | Pembayaran berhasil dikonfirmasi. |
| **System Automation** | | |
| Auto Update Stok | ✅ Lulus | Stok berkurang saat pesanan checkout. |
| Auto Calculate Ongkir| ✅ Lulus | Ongkos dihitung berdasarkan berat dan tipe layanan. |
| Auto Send Email/Notif| ✅ Lulus | Record masuk ke tabel notifications dan fungsi mail berjalan. |
| Auto Generate Invoice| ✅ Lulus | Format `INV-YYYYMMDD-XXXX` berhasil terbentuk. |

## 2. Pengujian Keamanan

| Parameter Keamanan | Status | Penjelasan |
|---|---|---|
| Password Hashing | ✅ Lulus | Password disimpan menggunakan bcrypt (`password_hash()`). |
| SQL Injection | ✅ Lulus | Seluruh query menggunakan Prepared Statements (PDO). |
| XSS Prevention | ✅ Lulus | Setiap output difilter dengan fungsi `htmlspecialchars()` melalui helper `e()`. |
| Session Hijacking | ✅ Lulus | Session management valid dan `Remember Me` menggunakan token khusus. |
| File Upload Security | ✅ Lulus | Hanya menerima ekstensi gambar (`jpg`, `png`, `webp`) dan dibatasi 5MB. |

## 3. Database & Relasi

- Terdapat 10 tabel dalam database (Minimal 8 tabel terpenuhi).
- `users`, `products`, `categories`, `orders`, `order_items`, `payments`, `reviews`, `notifications`, `carts`, `wishlist`.
- Seluruh relasi telah dikonfigurasi menggunakan `Foreign Key` dengan action `ON DELETE CASCADE`.

## 4. UI/UX dan Responsivitas

- **Mobile View**: Tampilan navbar berubah menjadi hamburger menu. Kolom berubah menjadi stacked (tumpuk).
- **Tablet View**: Grid menyesuaikan proporsional.
- **Desktop View**: Optimal dengan card hover effects dan micro-interactions menggunakan Bootstrap.
- **Konsistensi Desain**: Tombol dan skema warna menggunakan class spesifik seperti `.btn-primary-custom` untuk menjaga identitas platform.
