# Draft Outline PowerPoint Presentasi UAS - SecondHand Marketplace
**Tema:** Platform Jual Beli Barang Bekas Berkualitas dengan Verifikasi Kualitas Produk  
**Mahasiswa:** MUHAMAD DAFIN ALDZAKY  
**NIM:** 202210715241  

---

### Slide 1: COVER
*   **Judul:** SecondHand Marketplace
*   **Sub-judul:** Platform E-commerce Jual Beli Barang Bekas Berkualitas dengan Sistem Verifikasi Produk
*   **Identitas:** 
    *   Nama: Muhamad Dafin Aldzaky
    *   NIM: 202210715241
    *   Program Studi Informatika - Fakultas Ilmu Komputer
    *   Universitas Bhayangkara Jakarta Raya
*   *(Tambahkan Logo UBHARA Jaya)*

---

### Slide 2: LATAR BELAKANG
*   **Poin Utama:**
    *   Meningkatnya tren jual-beli barang bekas (thrifting) secara online di Indonesia.
    *   **Masalah Utama:** Banyak pembeli merasa tertipu karena kondisi fisik barang bekas tidak sesuai dengan deskripsi/foto penjual.
    *   **Solusi:** Membangun marketplace barang bekas yang mengintegrasikan sistem verifikasi kualitas oleh tim Admin sebelum produk dipublikasikan.

---

### Slide 3: TUJUAN & MANFAAT
*   **Tujuan:**
    *   Membangun platform e-commerce yang aman dan transparan bagi pembeli barang bekas.
    *   Menyediakan wadah bagi penjual untuk memasarkan barang preloved mereka.
*   **Manfaat:**
    *   *Bagi Pembeli:* Memperoleh kepastian kualitas kondisi produk (like new, good, fair) karena diverifikasi admin.
    *   *Bagi Penjual:* Meningkatkan kepercayaan pembeli sehingga produk lebih cepat laku.

---

### Slide 4: FITUR UTAMA
*   **Overview 4 Role User:**
    1.  **Pembeli (Buyer):** Browse & filter produk, add to cart, checkout dengan ongkir dinamis, upload bukti bayar, tracking order, dan review barang.
    2.  **Penjual (Seller):** Kelola produk (CRUD) beserta kondisi fisiknya, kelola order masuk, input resi pengiriman, dashboard penjualan.
    3.  **Admin:** Dashboard analitik, kelola status aktif user, kelola kategori produk, verifikasi produk pending (approve/reject), verifikasi pembayaran.
    4.  **System Automation:** Auto-update stok, auto-calculate ongkir (berat * tarif), auto-generate invoice, in-app & email notification.

---

### Slide 5: ARSITEKTUR SISTEM
*   **Poin Utama:**
    *   **Pola Desain:** MVC (Model-View-Controller) Custom Pattern menggunakan PHP Native.
    *   **Struktur Direktori:** Bersih dan terstruktur (dipisahkan antara berkas inti di `src/`, database di `database/`, dan dokumen pendukung di `docs/`).
    *   **Routing System:** Single entry point (`index.php`) bertindak sebagai front controller yang mengatur navigasi.

---

### Slide 6: DATABASE SCHEMA
*   **Struktur Data:**
    *   Menggunakan MySQL Database dengan **10 Tabel** (melebihi batas minimal 8 tabel).
    *   **Tabel yang Digunakan:** `users`, `products`, `categories`, `orders`, `order_items`, `payments`, `reviews`, `notifications`, `carts`, `wishlist`.
    *   **Relasi:** Hubungan One-to-Many antar tabel dengan integritas data menggunakan `Foreign Key` (`ON DELETE CASCADE`).

---

### Slide 7: TEKNOLOGI
*   **Frontend:** HTML5, CSS3 (Custom Styles), Bootstrap 5 (Responsive Layout), JavaScript (Vanilla) & AJAX.
*   **Backend:** PHP 8.x (Native Object-Oriented Programming).
*   **Database:** MySQL 8.x.
*   **Development Tools:** Laragon (Apache Server, phpMyAdmin), Git, VS Code.

---

### Slide 8: DEMO: PEMBELI
*   **Alur Kerja Pembeli:**
    *   Registrasi dan Login menggunakan password terenkripsi.
    *   Melakukan pencarian produk secara realtime dengan fitur AJAX Search.
    *   Memasukkan produk ke Keranjang dan melakukan Checkout.
    *   Melakukan pembayaran secara Bank Transfer dan mengunggah Bukti Pembayaran.
    *   Menulis review produk berupa ulasan teks dan rating bintang.

---

### Slide 9: DEMO: PENJUAL
*   **Alur Kerja Penjual:**
    *   Membuka Dashboard Penjual untuk memantau performa toko.
    *   Menambahkan produk bekas baru, mendeskripsikan kondisinya, dan mengunggah foto produk.
    *   Menunggu verifikasi admin (produk berstatus `pending`).
    *   Memantau pesanan masuk, memproses pengemasan, dan menginput nomor resi pengiriman.

---

### Slide 10: DEMO: SYSTEM AUTOMATION
*   **Proses yang Berjalan Otomatis:**
    *   **Auto-Update Stok:** Stok berkurang otomatis ketika pembeli menyelesaikan checkout.
    *   **Auto-Calculate Ongkir:** Sistem mengalikan berat produk (gram) dengan ongkos kirim standar.
    *   **Auto-Generate Invoice:** Membuat nomor invoice unik dengan format `INV-YYYYMMDD-XXXX`.
    *   **Notification System:** Notifikasi masuk ke database secara otomatis untuk memberi tahu pengguna saat status order berubah.

---

### Slide 11: DEMO: ADMIN
*   **Aktivitas Administrator:**
    *   Dashboard utama berisi metrik jumlah user, total penjualan, dan total barang.
    *   Memverifikasi kualitas produk: Admin meninjau kelayakan detail produk dan menyetujui (`Approved`) agar produk tampil secara publik atau menolaknya (`Rejected`).
    *   Konfirmasi pembayaran: Memvalidasi bukti unggahan transfer pembeli untuk memproses transaksi.

---

### Slide 12: SECURITY FEATURES
*   **Langkah Pengamanan:**
    *   **Password Security:** Hashing menggunakan algoritma modern **bcrypt** via `password_hash()`.
    *   **SQL Injection Prevention:** Seluruh query database memanfaatkan **PDO Prepared Statements**.
    *   **XSS Protection:** Penggunaan `htmlspecialchars()` pada semua output data user melalui fungsi helper `e()`.
    *   **Upload Validation:** Membatasi ukuran file maksimal 5MB dan hanya mengizinkan ekstensi file gambar yang aman.

---

### Slide 13: TESTING
*   **Hasil Pengujian:**
    *   **Functional Testing:** Seluruh skenario uji pada 4 role (Pembeli, Penjual, Admin, dan Otomasi Sistem) berstatus **100% Lulus (Pass)**.
    *   **Security Testing:** Celah keamanan SQL Injection dan XSS telah diuji dan dinyatakan **Aman**.
    *   *(Detail pengujian lengkap terdapat di berkas `TESTING_REPORT.pdf`)*

---

### Slide 14: KENDALA & SOLUSI
*   **Kendala:**
    *   Sinkronisasi asset gambar produk dummy antara database dengan file sistem di awal pengembangan.
    *   Menjaga performa pencarian produk tetap cepat tanpa reload halaman.
*   **Solusi:**
    *   Melakukan pembersihan nama gambar dan pemetaan ulang direktori `src/uploads/products/`.
    *   Mengimplementasikan endpoint API khusus dan memanfaatkan pemanggilan AJAX asinkron di frontend.

---

### Slide 15: KESIMPULAN
*   **Kesimpulan:**
    *   Platform SecondHand Marketplace telah berhasil dikembangkan sesuai dengan standar spesifikasi Tugas Besar Pemrograman Web Informatika UBHARA Jaya.
    *   Aplikasi memiliki 4 role yang berjalan selaras dengan otomatisasi sistem yang fungsional.
*   **Rencana Pengembangan Masa Depan:**
    *   Integrasi dengan Payment Gateway (seperti Midtrans) untuk otomatisasi pembayaran.
    *   Integrasi RajaOngkir API untuk penarifan ongkos kirim ekspedisi asli (JNE, J&T, POS).

---

### Slide 16: Q&A
*   **Slide Penutup:**
    *   Terima Kasih atas Perhatiannya!
    *   Sesi tanya jawab dipersilakan.
