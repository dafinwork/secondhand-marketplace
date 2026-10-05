/* =====================================================
   SecondHand Marketplace - Static Demo Data
   Data mirroring database/database.sql (seed data)
   ===================================================== */

const CATEGORIES = [
  { id: 1, name: 'Elektronik', slug: 'elektronik', icon: 'bi-laptop', description: 'Laptop, PC, dan perangkat elektronik bekas' },
  { id: 2, name: 'Smartphone', slug: 'smartphone', icon: 'bi-phone', description: 'Smartphone dan tablet bekas berkualitas' },
  { id: 3, name: 'Fashion', slug: 'fashion', icon: 'bi-bag', description: 'Pakaian, sepatu, dan aksesoris preloved' },
  { id: 4, name: 'Kamera', slug: 'kamera', icon: 'bi-camera', description: 'Kamera dan aksesoris fotografi bekas' },
  { id: 5, name: 'Furniture', slug: 'furniture', icon: 'bi-house', description: 'Perabotan dan furniture bekas berkualitas' },
  { id: 6, name: 'Olahraga', slug: 'olahraga', icon: 'bi-bicycle', description: 'Peralatan olahraga dan outdoor bekas' },
  { id: 7, name: 'Buku & Hobi', slug: 'buku-hobi', icon: 'bi-book', description: 'Buku, koleksi, dan hobi' },
  { id: 8, name: 'Otomotif', slug: 'otomotif', icon: 'bi-car-front', description: 'Aksesoris dan sparepart kendaraan' },
];

const SELLERS = {
  2: { id: 2, name: 'Toko Elektronik Jaya', address: 'Jl. Sudirman No. 45, Jakarta Selatan', phone: '081234567891', joined: '2023-03-14', rating: 4.9, sold: 128 },
  3: { id: 3, name: 'Preloved Fashion ID', address: 'Jl. Braga No. 12, Bandung', phone: '081234567892', joined: '2023-07-02', rating: 4.8, sold: 214 },
  4: { id: 4, name: 'Second Gadget Store', address: 'Jl. Malioboro No. 8, Yogyakarta', phone: '081234567893', joined: '2024-01-21', rating: 4.7, sold: 96 },
};

const BUYERS = {
  5: { id: 5, name: 'Budi Santoso', email: 'buyer1@secondhand.com', phone: '081234567894', address: 'Jl. Merdeka No. 10, Surabaya' },
  6: { id: 6, name: 'Siti Rahayu', email: 'buyer2@secondhand.com', phone: '081234567895', address: 'Jl. Diponegoro No. 5, Semarang' },
};

const USERS = {
  1: { id: 1, name: 'Admin SecondHand', email: 'admin@secondhand.com', role: 'admin', phone: '081234567890', active: true },
  2: { id: 2, name: 'Toko Elektronik Jaya', email: 'seller1@secondhand.com', role: 'seller', phone: '081234567891', active: true },
  3: { id: 3, name: 'Preloved Fashion ID', email: 'seller2@secondhand.com', role: 'seller', phone: '081234567892', active: true },
  4: { id: 4, name: 'Second Gadget Store', email: 'seller3@secondhand.com', role: 'seller', phone: '081234567893', active: true },
  5: { id: 5, name: 'Budi Santoso', email: 'buyer1@secondhand.com', role: 'buyer', phone: '081234567894', active: true },
  6: { id: 6, name: 'Siti Rahayu', email: 'buyer2@secondhand.com', role: 'buyer', phone: '081234567895', active: true },
};

const PRODUCTS = [
  {
    id: 1, sellerId: 2, categoryId: 1, name: 'MacBook Air M1 2020 Bekas', slug: 'macbook-air-m1-2020-bekas',
    description: 'MacBook Air M1 2020 kondisi 95%. RAM 8GB, SSD 256GB. Fullset box dan charger. Battery cycle count masih rendah. Tidak ada dent atau scratch yang berarti. Cocok untuk produktivitas dan coding.',
    price: 8500000, originalPrice: 14999000, stock: 2, condition: 'like_new', weight: 1290,
    image: 'macbook-air-m1.jpg', verification: 'approved', views: 245, rating: 5.0, reviewCount: 1,
    createdAt: '2025-04-18',
  },
  {
    id: 2, sellerId: 2, categoryId: 1, name: 'ASUS ROG Strix G15 Gaming Laptop', slug: 'asus-rog-strix-g15-gaming',
    description: 'ASUS ROG Strix G15 Ryzen 7 5800H, RTX 3060. RAM 16GB, SSD 512GB. Kondisi mulus 90%, keyboard nyala semua. Cocok untuk gaming dan rendering. Bonus cooling pad.',
    price: 11200000, originalPrice: 18999000, stock: 1, condition: 'good', weight: 2300,
    image: 'asus-rog.jpg', verification: 'approved', views: 189, rating: 0, reviewCount: 0,
    createdAt: '2025-04-25',
  },
  {
    id: 3, sellerId: 4, categoryId: 2, name: 'iPhone 13 Pro 256GB Bekas', slug: 'iphone-13-pro-256gb-bekas',
    description: 'iPhone 13 Pro 256GB Sierra Blue. Kondisi 93%, battery health 87%. Fullset box, charger, dan case. Face ID normal, semua fitur berfungsi sempurna. iCloud clean.',
    price: 9200000, originalPrice: 16999000, stock: 1, condition: 'good', weight: 204,
    image: 'iphone-13-pro.jpg', verification: 'approved', views: 312, rating: 0, reviewCount: 0,
    createdAt: '2025-05-02',
  },
  {
    id: 4, sellerId: 4, categoryId: 2, name: 'Samsung Galaxy S22 Ultra 5G', slug: 'samsung-galaxy-s22-ultra',
    description: 'Samsung Galaxy S22 Ultra 256GB Phantom Black. Kondisi 90%, layar mulus tanpa scratch. S-Pen normal. Fullset dengan box dan charger original. SEIN resmi.',
    price: 7800000, originalPrice: 17999000, stock: 2, condition: 'good', weight: 229,
    image: 'galaxy-s22-ultra.jpg', verification: 'approved', views: 278, rating: 0, reviewCount: 0,
    createdAt: '2025-04-30',
  },
  {
    id: 5, sellerId: 2, categoryId: 4, name: 'Canon EOS 80D Body Only', slug: 'canon-eos-80d-body-only',
    description: 'Canon EOS 80D body only. Shutter count 25rb (masih rendah). Kondisi 88%, normal semua fitur. Bonus memory card 32GB dan tas kamera. Cocok untuk foto dan video.',
    price: 6500000, originalPrice: 12999000, stock: 1, condition: 'good', weight: 730,
    image: 'canon-80d.jpg', verification: 'approved', views: 156, rating: 0, reviewCount: 0,
    createdAt: '2025-04-12',
  },
  {
    id: 6, sellerId: 3, categoryId: 3, name: 'Nike Air Jordan 1 Retro High OG', slug: 'nike-air-jordan-1-retro',
    description: 'Nike Air Jordan 1 Retro High OG ukuran 42. Kondisi VNDS (Very Near Dead Stock) 95%. Cuma pernah dipakai 2 kali. Outsole masih tebal. Bonus box original.',
    price: 2100000, originalPrice: 3200000, stock: 1, condition: 'like_new', weight: 900,
    image: 'jordan-1.jpg', verification: 'approved', views: 201, rating: 0, reviewCount: 0,
    createdAt: '2025-05-05',
  },
  {
    id: 7, sellerId: 3, categoryId: 3, name: 'Adidas Ultraboost 22 Running', slug: 'adidas-ultraboost-22',
    description: 'Adidas Ultraboost 22 ukuran 43. Boost masih empuk, outsole masih 80%. Warna Core Black. Cocok untuk running atau daily. Sudah dicuci bersih.',
    price: 850000, originalPrice: 2800000, stock: 2, condition: 'good', weight: 680,
    image: 'ultraboost-22.jpg', verification: 'approved', views: 134, rating: 0, reviewCount: 0,
    createdAt: '2025-05-08',
  },
  {
    id: 8, sellerId: 2, categoryId: 5, name: 'IKEA KALLAX Rak 4x2 Putih', slug: 'ikea-kallax-rak-4x2',
    description: 'IKEA KALLAX shelf unit 4x2 warna putih. Kondisi 85%, ada sedikit goresan minor. Sudah dirakit, bisa dibongkar untuk pengiriman. Dimensi 147x77cm.',
    price: 650000, originalPrice: 1499000, stock: 1, condition: 'fair', weight: 25000,
    image: 'ikea-kallax.jpg', verification: 'approved', views: 98, rating: 0, reviewCount: 0,
    createdAt: '2025-04-08',
  },
  {
    id: 9, sellerId: 4, categoryId: 2, name: 'iPad Air 4 64GB WiFi', slug: 'ipad-air-4-64gb-wifi',
    description: 'iPad Air 4 64GB WiFi Only Sky Blue. Kondisi 92%, layar mulus. Battery health 95%. Fullset box dan charger. Bonus case dan tempered glass. Cocok untuk kuliah dan desain.',
    price: 5500000, originalPrice: 9499000, stock: 1, condition: 'like_new', weight: 458,
    image: 'ipad-air-4.jpg', verification: 'approved', views: 167, rating: 0, reviewCount: 0,
    createdAt: '2025-05-10',
  },
  {
    id: 10, sellerId: 3, categoryId: 4, name: 'Sony Alpha A6400 + Kit Lens', slug: 'sony-alpha-a6400-kit',
    description: 'Sony A6400 dengan lens kit 16-50mm. Shutter count 10rb. Kondisi 90%, LCD flip normal. Bonus memory card 64GB, tas, dan filter UV. Mirrorless terbaik untuk vlog.',
    price: 9800000, originalPrice: 14499000, stock: 1, condition: 'good', weight: 850,
    image: 'sony-a6400.jpg', verification: 'approved', views: 223, rating: 0, reviewCount: 0,
    createdAt: '2025-04-22',
  },
  {
    id: 11, sellerId: 2, categoryId: 1, name: 'ThinkPad X1 Carbon Gen 9', slug: 'thinkpad-x1-carbon-gen9',
    description: 'Lenovo ThinkPad X1 Carbon Gen 9. Intel i7-1165G7, 16GB RAM, 512GB SSD. Layar 14" FHD IPS. Keyboard legendaris. Kondisi 92%, cocok untuk profesional.',
    price: 10500000, originalPrice: 22000000, stock: 1, condition: 'like_new', weight: 1130,
    image: 'thinkpad-x1.jpg', verification: 'approved', views: 176, rating: 0, reviewCount: 0,
    createdAt: '2025-05-01',
  },
  {
    id: 12, sellerId: 3, categoryId: 5, name: 'Kursi Gaming SecretLab Titan', slug: 'kursi-gaming-secretlab-titan',
    description: 'SecretLab Titan 2022 warna Stealth. Kondisi 88%, bantal masih empuk. Lumbar support dan armrest berfungsi normal. Bonus bantal leher original.',
    price: 3200000, originalPrice: 6499000, stock: 1, condition: 'good', weight: 28000,
    image: 'secretlab-titan.jpg', verification: 'approved', views: 145, rating: 0, reviewCount: 0,
    createdAt: '2025-04-15',
  },
  {
    id: 13, sellerId: 4, categoryId: 1, name: 'PC Gaming Ryzen 5 RTX 3060', slug: 'pc-gaming-ryzen-5-rtx-3060',
    description: 'Desktop gaming Ryzen 5 5600X, RTX 3060 12GB, RAM 32GB DDR4, SSD NVMe 512GB. PSU 650W Gold. Semua komponen sudah diuji 2 minggu penuh, tanpa artifact. Pendingin air AIO.',
    price: 13500000, originalPrice: 21000000, stock: 1, condition: 'good', weight: 9500,
    image: 'asus-rog.jpg', verification: 'approved', views: 188, rating: 0, reviewCount: 0,
    createdAt: '2025-05-12',
  },
  {
    id: 14, sellerId: 3, categoryId: 3, name: 'Tas Kanvas Canvas Premium', slug: 'tas-kanvas-canvas-premium',
    description: 'Tas ransel kanvas premium jahitan tangan. Kapasitas 25L, ada compartment laptop 15". Warna natural beige. Kondisi 90%, tali sedikit mengendur.',
    price: 320000, originalPrice: 750000, stock: 3, condition: 'good', weight: 700,
    image: 'ultraboost-22.jpg', verification: 'approved', views: 76, rating: 0, reviewCount: 0,
    createdAt: '2025-05-11',
  },
  {
    id: 15, sellerId: 2, categoryId: 6, name: 'Sepeda MTB Polygon Trinus', slug: 'sepeda-mtb-polygon-trinus',
    description: 'Sepeda MTB Polygon Trinus 26 inch, frame alum, 21 speed Shimano. Sudah di servis lengkap, rem baru. Ban still tebal. Cocok untukfundamental bersepeda.',
    price: 2400000, originalPrice: 4200000, stock: 1, condition: 'good', weight: 13000,
    image: 'thinkpad-x1.jpg', verification: 'pending', views: 41, rating: 0, reviewCount: 0,
    createdAt: '2025-05-14',
  },
];

const SEED_ORDERS = [
  {
    id: 1, invoice: 'INV-20250501-0001', buyerId: 5, total: 8500000, shipping: 25000, shippingMethod: 'reguler',
    address: 'Jl. Merdeka No. 10, Surabaya, Jawa Timur 60111', status: 'completed', tracking: 'JNE1234567890',
    paymentMethod: 'bank_transfer', bankName: 'BCA', accountNumber: '1234567890', accountName: 'Budi Santoso',
    paymentStatus: 'verified', createdAt: '2025-05-01 10:12',
    items: [{ productId: 1, sellerId: 2, qty: 1, price: 8500000, status: 'completed' }],
  },
  {
    id: 2, invoice: 'INV-20250510-0002', buyerId: 6, total: 2100000, shipping: 15000, shippingMethod: 'reguler',
    address: 'Jl. Diponegoro No. 5, Semarang, Jawa Tengah 50241', status: 'shipped', tracking: 'SiCepat9876543210',
    paymentMethod: 'ewallet', paymentStatus: 'verified', createdAt: '2025-05-10 15:40',
    items: [{ productId: 6, sellerId: 3, qty: 1, price: 2100000, status: 'shipped' }],
  },
  {
    id: 3, invoice: 'INV-20250515-0003', buyerId: 5, total: 9200000, shipping: 20000, shippingMethod: 'express',
    address: 'Jl. Merdeka No. 10, Surabaya, Jawa Timur 60111', status: 'processing', tracking: null,
    paymentMethod: 'bank_transfer', bankName: 'BNI', accountNumber: '0987654321', accountName: 'Budi Santoso',
    paymentStatus: 'pending', createdAt: '2025-05-15 09:05',
    items: [{ productId: 3, sellerId: 4, qty: 1, price: 9200000, status: 'processing' }],
  },
];

const SEED_REVIEWS = [
  { id: 1, productId: 1, userId: 5, orderId: 1, rating: 5, comment: 'Barang sesuai deskripsi, kondisi mulus banget. Packing rapi dan aman. Seller ramah dan fast response. Recommended!', createdAt: '2025-05-06 18:20' },
  { id: 2, productId: 6, userId: 6, orderId: 2, rating: 5, comment: 'VNDS beneran, box original masih ada. Fast response seller, recommended!', createdAt: '2025-05-14 12:05' },
  { id: 3, productId: 4, userId: 5, orderId: 3, rating: 4, comment: 'Layar mulus, S-Pen berfungsi normal. Pengiriman agak lama tapi worth it.', createdAt: '2025-05-16 09:44' },
];

const SEED_NOTIFICATIONS = [
  { id: 1, userId: 5, title: 'Pesanan Selesai', message: 'Pesanan INV-20250501-0001 telah selesai. Terima kasih telah berbelanja!', type: 'order', read: false, createdAt: '2025-05-01 14:00' },
  { id: 2, userId: 2, title: 'Produk Disetujui', message: 'Produk MacBook Air M1 2020 telah diverifikasi dan dipublikasikan.', type: 'product', read: false, createdAt: '2025-04-19 10:30' },
  { id: 3, userId: 6, title: 'Pesanan Dikirim', message: 'Pesanan INV-20250510-0002 sedang dalam pengiriman. No. Resi: SiCepat9876543210', type: 'order', read: false, createdAt: '2025-05-12 08:15' },
];

const ORDER_FLOW = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'completed'];

const CONDITION_LABELS = {
  like_new: { label: 'Like New', badge: 'success', icon: 'bi-stars' },
  good: { label: 'Good', badge: 'primary', icon: 'bi-check-circle' },
  fair: { label: 'Fair', badge: 'warning', icon: 'bi-exclamation-circle' },
};

const ORDER_STATUS = {
  pending: { label: 'Menunggu Pembayaran', badge: 'warning', icon: 'bi-hourglass-split' },
  confirmed: { label: 'Dikonfirmasi', badge: 'info', icon: 'bi-check2' },
  processing: { label: 'Diproses Seller', badge: 'info', icon: 'bi-box-seam' },
  shipped: { label: 'Dikirim', badge: 'primary', icon: 'bi-truck' },
  delivered: { label: 'Diterima', badge: 'primary', icon: 'bi-box2-heart' },
  completed: { label: 'Selesai', badge: 'success', icon: 'bi-check2-all' },
  cancelled: { label: 'Dibatalkan', badge: 'danger', icon: 'bi-x-circle' },
};

const SHIPPING_METHODS = [
  { id: 'reguler', name: 'Reguler (JNE / SiCepat)', cost: 25000, estimate: '3-5 hari kerja' },
  { id: 'express', name: 'Express (JNE YES / TIKI)', cost: 45000, estimate: '1-2 hari kerja' },
  { id: 'instant', name: 'Instant (GoSend / GrabSend)', cost: 75000, estimate: 'hari ini juga' },
];

const BANK_LIST = ['BCA', 'BNI', 'Mandiri', 'BSI', 'BRI', 'CIMB Niaga'];

const DEMO_ACCOUNTS = [
  { label: 'Admin', email: 'admin@secondhand.com', role: 'admin' },
  { label: 'Seller', email: 'seller1@secondhand.com', role: 'seller' },
  { label: 'Buyer', email: 'buyer1@secondhand.com', role: 'buyer' },
];