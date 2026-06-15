# User Manual - SecondHand Marketplace

Buku panduan ini menjelaskan cara penggunaan aplikasi SecondHand Marketplace untuk masing-class Role User.

## 1. Pembeli (Buyer)

Sebagai pembeli, Anda dapat mencari, melihat, dan membeli barang bekas berkualitas yang ada di platform kami.

**Cara Penggunaan:**
1. **Registrasi/Login**: Buka halaman utama dan klik tombol "Login" atau "Daftar". Pilih role sebagai Pembeli saat mendaftar.
2. **Mencari Produk**: Gunakan kotak pencarian di navbar atau klik menu "Produk" untuk melihat daftar barang. Anda juga bisa memfilter berdasarkan kategori.
3. **Memasukkan ke Keranjang**: Klik tombol keranjang pada produk yang Anda inginkan.
4. **Checkout & Pembayaran**: Masuk ke halaman keranjang Anda, klik "Checkout", isi alamat pengiriman, dan pilih metode pembayaran. Setelah checkout selesai, upload bukti pembayaran melalui halaman pesanan Anda.
5. **Melacak Pesanan**: Anda dapat melacak status pengiriman dari dashboard Anda di bagian "Pesanan Saya".
6. **Memberikan Review**: Setelah barang diterima, klik konfirmasi penerimaan dan berikan review (rating 1-5 bintang) beserta komentar/foto.

## 2. Penjual (Seller)

Sebagai penjual, Anda dapat membuka toko dan menjual barang preloved Anda.

**Cara Penggunaan:**
1. **Pendaftaran Penjual**: Daftar menggunakan opsi role "Seller" atau hubungi Admin jika akun Anda ingin di-upgrade dari pembeli ke penjual.
2. **Kelola Produk**: Masuk ke Dashboard Penjual, klik "Produk Saya" lalu "Tambah Produk Baru". Isi detail barang (nama, harga, deskripsi, kondisi, dan foto). Barang Anda akan masuk tahap verifikasi oleh Admin sebelum muncul di halaman publik.
3. **Kelola Pesanan**: Ketika pembeli membayar barang Anda dan status dikonfirmasi oleh Admin, pesanan akan masuk ke tab "Pesanan Masuk". Silakan proses dan masukkan nomor resi pengiriman untuk pembeli.
4. **Dashboard Statistik**: Anda bisa melihat rangkuman performa toko dan barang-barang yang terjual.

## 3. Administrator (Admin)

Admin bertanggung jawab untuk menjaga kualitas ekosistem SecondHand Marketplace.

**Fungsi Admin:**
1. **Dashboard Analytics**: Melihat overview total pendapatan, pengguna, dan transaksi.
2. **Kelola User**: Admin dapat memblokir pengguna (buyer/seller) yang melanggar aturan.
3. **Verifikasi Produk**: Setiap produk yang diupload oleh Penjual wajib diverifikasi oleh Admin. Admin akan mengecek foto dan spesifikasi sebelum menyetujui (`Approved`) atau menolak (`Rejected`) produk tersebut.
4. **Kelola Pesanan & Pembayaran**: Memverifikasi bukti transfer dan memperbarui status pesanan menjadi dikonfirmasi jika dana sudah masuk.
5. **Kategori Sistem**: Mengatur kategori barang.

## 4. System Automation

Sistem menjalankan otomatisasi di background tanpa campur tangan user secara manual, di antaranya:
- Pengurangan stok otomatis setelah barang berhasil di-checkout.
- Perhitungan ongkos kirim otomatis berdasarkan berat barang yang dikalikan dengan rate wilayah.
- Auto-generate invoice number dan update status pembayaran.
- Auto-notification email/in-app kepada penjual maupun pembeli saat terjadi status update (barang terkirim, dibayar, diverifikasi).
