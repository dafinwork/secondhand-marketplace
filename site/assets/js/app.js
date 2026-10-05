/* =====================================================
   SecondHand Marketplace - Static Demo Core
   Menyediakan layout, store (localStorage), dan helper UI.
   Versi statis untuk GitHub Pages (tanpa PHP/MySQL).
   ===================================================== */

/* ---------- Storage ---------- */
const KEY = {
  user: 'sh_user',
  cart: 'sh_cart',
  wishlist: 'sh_wishlist',
  orders: 'sh_orders',
  notifications: 'sh_notifications',
  reviews: 'sh_reviews',
  listings: 'sh_listings',
  settings: 'sh_settings',
  readNotif: 'sh_read_notif',
};

function load(key, fallback) {
  try {
    const raw = localStorage.getItem(key);
    return raw ? JSON.parse(raw) : fallback;
  } catch (e) {
    return fallback;
  }
}

function save(key, value) {
  localStorage.setItem(key, JSON.stringify(value));
}

function getUser() {
  return load(KEY.user, null);
}

function isLoggedIn() {
  return !!getUser();
}

function requireLogin(next) {
  if (!isLoggedIn()) {
    location.href = 'login.html?next=' + encodeURIComponent(next || location.pathname.split('/').pop());
    return false;
  }
  return true;
}

/* ---------- Catalog (seed + seller-created) ---------- */
function allProducts() {
  const base = PRODUCTS.map((p) => ({ ...p }));
  const extra = load(KEY.listings, []);
  extra.forEach((p) => base.push({ ...p }));
  return base;
}

function getProductBySlug(slug) {
  return allProducts().find((p) => p.slug === slug);
}

function getProductById(id) {
  return allProducts().find((p) => p.id === Number(id));
}

function getCategory(slugOrId) {
  return CATEGORIES.find((c) => c.slug === slugOrId || c.id === Number(slugOrId));
}

/* ---------- Cart ---------- */
function getCart() {
  return load(KEY.cart, []);
}

function saveCart(cart) {
  save(KEY.cart, cart);
  paintCartBadge();
}

function addToCart(productId, qty) {
  if (!requireLogin('product.html?p=' + (getProductById(productId) || {}).slug)) return;
  const cart = getCart();
  const item = cart.find((i) => i.productId === Number(productId));
  const product = getProductById(productId);
  const wanted = (item ? item.qty : 0) + (qty || 1);
  if (product && wanted > product.stock) {
    toast(`Stok tersisa ${product.stock} unit`, 'warning');
    return;
  }
  if (item) item.qty = wanted;
  else cart.push({ productId: Number(productId), qty: qty || 1, addedAt: Date.now() });
  saveCart(cart);
  toast('Produk ditambahkan ke keranjang', 'success');
}

function updateCartQty(productId, qty) {
  const cart = getCart();
  const item = cart.find((i) => i.productId === Number(productId));
  if (!item) return;
  const product = getProductById(productId);
  if (qty <= 0) {
    saveCart(cart.filter((i) => i.productId !== Number(productId)));
  } else {
    if (product && qty > product.stock) qty = product.stock;
    item.qty = qty;
    saveCart(cart);
  }
  paintCartBadge();
}

function removeFromCart(productId) {
  saveCart(getCart().filter((i) => i.productId !== Number(productId)));
  paintCartBadge();
}

function cartCount() {
  return getCart().reduce((s, i) => s + i.qty, 0);
}

function cartDetail() {
  return getCart()
    .map((i) => {
      const product = getProductById(i.productId);
      if (!product) return null;
      return { ...i, product, subtotal: product.price * i.qty };
    })
    .filter(Boolean);
}

function cartSubtotal() {
  return cartDetail().reduce((s, i) => s + i.subtotal, 0);
}

function paintCartBadge() {
  const badge = document.getElementById('cartBadge');
  if (badge) badge.textContent = cartCount();
}

/* ---------- Wishlist ---------- */
function getWishlist() {
  return load(KEY.wishlist, []);
}

function toggleWishlist(productId) {
  if (!requireLogin('product.html?p=' + (getProductById(productId) || {}).slug)) return false;
  const list = getWishlist();
  const id = Number(productId);
  const idx = list.indexOf(id);
  if (idx >= 0) {
    save(KEY.wishlist, list.filter((x) => x !== id));
    toast('Dihapus dari wishlist', 'secondary');
    return false;
  }
  list.push(id);
  save(KEY.wishlist, list);
  toast('Ditambahkan ke wishlist', 'success');
  return true;
}

function inWishlist(productId) {
  return getWishlist().includes(Number(productId));
}

/* ---------- Orders ---------- */
function getOrders() {
  const mine = load(KEY.orders, []);
  const seed = SEED_ORDERS.filter((o) => {
    const user = getUser();
    if (!user) return false;
    if (user.role === 'buyer') return o.buyerId === user.id;
    if (user.role === 'seller') return o.items.some((i) => SELLERS[user.id] && i.sellerId === user.id);
    return true;
  });
  return mine.concat(seed);
}

function createOrder(payload) {
  const orders = load(KEY.orders, []);
  const seq = String(Date.now()).slice(-6);
  const order = {
    id: Date.now(),
    invoice: 'INV-' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '-' + seq,
    buyerId: getUser() ? getUser().id : 5,
    status: 'pending',
    tracking: null,
    createdAt: new Date().toLocaleString('id-ID'),
    ...payload,
  };
  orders.unshift(order);
  save(KEY.orders, orders);

  const notifs = load(KEY.notifications, SEED_NOTIFICATIONS);
  notifs.unshift({
    id: Date.now(),
    userId: order.buyerId,
    title: 'Pesanan Dibuat',
    message: 'Pesanan ' + order.invoice + ' menunggu pembayaran.',
    type: 'order',
    read: false,
    createdAt: order.createdAt,
  });
  save(KEY.notifications, notifs);

  order.items.forEach((i) => {
    const p = getProductById(i.productId);
    if (p) p.stock = Math.max(0, p.stock - i.qty);
  });
  saveCart([]);
  return order;
}

function advanceOrder(orderId, status) {
  const orders = load(KEY.orders, []);
  const o = orders.find((x) => x.id === Number(orderId));
  if (!o) return;
  o.status = status;
  o.items.forEach((i) => (i.status = status));
  if (status === 'shipped' && !o.tracking) o.tracking = 'JNE' + Math.floor(Math.random() * 1e10);
  save(KEY.orders, orders);
}

function allReviews() {
  return SEED_REVIEWS.concat(load(KEY.reviews, []));
}

function reviewsFor(productId) {
  return allReviews().filter((r) => r.productId === Number(productId));
}

function ratingOf(productId) {
  const list = reviewsFor(productId);
  if (!list.length) return 0;
  return list.reduce((s, r) => s + r.rating, 0) / list.length;
}

function submitReview(payload) {
  const reviews = load(KEY.reviews, []);
  reviews.unshift({
    id: Date.now(),
    createdAt: new Date().toLocaleString('id-ID'),
    userId: getUser().id,
    ...payload,
  });
  save(KEY.reviews, reviews);
}

function getNotifications() {
  const list = load(KEY.notifications, SEED_NOTIFICATIONS);
  const user = getUser();
  if (!user) return [];
  const read = load(KEY.readNotif, []);
  return list.filter((n) => n.userId === user.id || user.role === 'admin').map((n) => ({ ...n, read: read.includes(n.id) }));
}

function unreadCount() {
  return getNotifications().filter((n) => !n.read).length;
}

function markAllRead() {
  save(KEY.readNotif, getNotifications().map((n) => n.id));
}

/* ---------- Helpers ---------- */
function fmt(n) {
  return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function fmtDate(d) {
  if (!d) return '-';
  const date = new Date(d);
  if (isNaN(date)) return d;
  return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
}

function qs(key) {
  return new URLSearchParams(location.search).get(key);
}

function discount(p) {
  if (!p.originalPrice || p.originalPrice <= p.price) return 0;
  return Math.round((1 - p.price / p.originalPrice) * 100);
}

function stars(rating, size) {
  const r = Math.round(rating);
  let html = '';
  for (let i = 1; i <= 5; i++) {
    html += `<i class="bi bi-star${i <= r ? '-fill' : ''}"></i>`;
  }
  return `<span class="star-row ${size || ''}">${html}</span>`;
}

function esc(s) {
  return String(s == null ? '' : s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

function toast(message, type) {
  let box = document.getElementById('toastContainer');
  if (!box) {
    box = document.createElement('div');
    box.id = 'toastContainer';
    box.className = 'toast-container';
    document.body.appendChild(box);
  }
  const id = 't' + Date.now() + Math.random().toString(16).slice(2);
  box.insertAdjacentHTML('beforeend', `
    <div class="toast align-items-center border-0" id="${id}" role="alert" aria-live="assertive">
      <div class="d-flex">
        <div class="toast-body">${esc(message)}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>`);
  const el = document.getElementById(id);
  el.classList.add('bg-' + (type || 'dark'), 'text-white');
  const t = new bootstrap.Toast(el, { delay: 2600 });
  t.show();
  el.addEventListener('hidden.bs.toast', () => el.remove());
}

/* ---------- Layout ---------- */
const NAV_ITEMS = [
  { href: 'products.html', label: 'Produk', icon: 'bi-grid' },
  { href: 'how-it-works.html', label: 'Cara Kerja', icon: 'bi-diagram-3' },
  { href: 'seller.html', label: 'Jadi Seller', icon: 'bi-shop' },
];

function navbarHtml(active) {
  const user = getUser();
  const unread = unreadCount();
  const links = NAV_ITEMS.map(
    (i) => `<li class="nav-item"><a class="nav-link ${active === i.href ? 'active' : ''}" href="${i.href}"><i class="bi ${i.icon}"></i> ${i.label}</a></li>`
  ).join('');

  const right = user
    ? `
      ${user.role === 'buyer' ? `
      <li class="nav-item"><a class="nav-link nav-icon" href="wishlist.html" title="Wishlist"><i class="bi bi-heart"></i></a></li>
      <li class="nav-item"><a class="nav-link nav-icon" href="cart.html" title="Keranjang"><i class="bi bi-cart3"></i><span class="badge bg-danger" id="cartBadge">${cartCount()}</span></a></li>` : ''}
      <li class="nav-item dropdown">
        <a class="nav-link nav-icon dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside" title="Notifikasi">
          <i class="bi bi-bell"></i>${unread ? `<span class="badge bg-danger">${unread}</span>` : ''}
        </a>
        <div class="dropdown-menu dropdown-menu-end notif-dropdown" id="notifDropdown">
          <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Notifikasi</h6>
            <button class="btn btn-sm btn-link p-0" id="notifReadAll">Tandai dibaca</button>
          </div>
          <div id="notifList"><div class="p-3 text-center text-muted small">Memuat...</div></div>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
          <div class="avatar-circle">${esc(user.name.charAt(0).toUpperCase())}</div>
          <span class="d-none d-lg-inline">${esc(user.name)}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li class="dropdown-header text-uppercase small text-muted">${esc(user.role)}</li>
          <li><a class="dropdown-item" href="dashboard.html"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
          ${user.role === 'buyer' ? `<li><a class="dropdown-item" href="orders.html"><i class="bi bi-bag me-2"></i>Pesanan Saya</a></li>
          <li><a class="dropdown-item" href="wishlist.html"><i class="bi bi-heart me-2"></i>Wishlist</a></li>` : ''}
          <li><a class="dropdown-item" href="tracking.html"><i class="bi bi-geo-alt me-2"></i>Lacak Pesanan</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="#" id="logoutBtn"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </li>`
    : `<li class="nav-item"><a class="nav-link" href="login.html"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
       <li class="nav-item"><a class="btn btn-primary-custom btn-sm" href="register.html">Daftar</a></li>`;

  return `
  <nav class="navbar navbar-expand-lg navbar-main sticky-top" id="mainNavbar">
    <div class="container">
      <a class="navbar-brand" href="index.html"><i class="bi bi-recycle"></i> Second<span>Hand</span></a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-label="Toggle navigasi">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <div class="search-bar mx-auto my-2 my-lg-0">
          <i class="bi bi-search search-icon"></i>
          <input type="search" id="searchInput" class="form-control" placeholder="Cari barang bekas berkualitas..." aria-label="Cari produk">
          <div class="search-results" id="searchResults"></div>
        </div>
        <ul class="navbar-nav ms-auto align-items-center gap-1">${links}${right}</ul>
      </div>
    </div>
  </nav>`;
}

function footerHtml() {
  const cats = CATEGORIES.slice(0, 4);
  return `
  <footer class="footer">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <h5><i class="bi bi-recycle"></i> SecondHand</h5>
          <p>Platform jual beli barang bekas berkualitas dengan sistem verifikasi. Temukan barang impianmu dengan harga terjangkau.</p>
          <div class="d-flex gap-3 mt-3">
            <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a>
          </div>
        </div>
        <div class="col-lg-2 col-md-4">
          <h5>Kategori</h5>
          <ul>${cats.map((c) => `<li><a href="products.html?cat=${c.slug}">${c.name}</a></li>`).join('')}</ul>
        </div>
        <div class="col-lg-2 col-md-4">
          <h5>Informasi</h5>
          <ul>
            <li><a href="how-it-works.html">Cara Kerja</a></li>
            <li><a href="seller.html">Jadi Seller</a></li>
            <li><a href="tracking.html">Lacak Pesanan</a></li>
            <li><a href="how-it-works.html#faq">Pertanyaan Umum</a></li>
          </ul>
        </div>
        <div class="col-lg-4 col-md-4">
          <h5>Hubungi Kami</h5>
          <ul>
            <li><i class="bi bi-envelope me-2"></i>support@secondhand.com</li>
            <li><i class="bi bi-phone me-2"></i>+62 812-3456-7890</li>
            <li><i class="bi bi-geo-alt me-2"></i>Jakarta, Indonesia</li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p class="mb-0">&copy; 2025 SecondHand Marketplace. Demo statis untuk GitHub Pages &mdash; data tersimpan di browser Anda.</p>
      </div>
    </div>
  </footer>`;
}

function mountLayout(active) {
  const nav = document.getElementById('siteNav');
  const foot = document.getElementById('siteFooter');
  if (nav) nav.innerHTML = navbarHtml(active);
  if (foot) foot.innerHTML = footerHtml();

  const logout = document.getElementById('logoutBtn');
  if (logout) {
    logout.addEventListener('click', (e) => {
      e.preventDefault();
      localStorage.removeItem(KEY.user);
      toast('Anda telah logout', 'secondary');
      setTimeout(() => (location.href = 'index.html'), 400);
    });
  }
  paintCartBadge();
  initSearch();
  initNotifications();
  initNavbarScroll();
}

function initNavbarScroll() {
  const nav = document.getElementById('mainNavbar');
  if (!nav) return;
  const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 8);
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
}

function initSearch() {
  const input = document.getElementById('searchInput');
  const box = document.getElementById('searchResults');
  if (!input || !box) return;

  const close = () => box.classList.remove('active');
  const render = () => {
    const q = input.value.trim().toLowerCase();
    if (q.length < 2) return close();
    const hits = allProducts()
      .filter((p) => p.name.toLowerCase().includes(q) || p.description.toLowerCase().includes(q))
      .slice(0, 6);
    if (!hits.length) {
      box.innerHTML = '<div class="p-3 text-center text-muted small">Produk tidak ditemukan</div>';
    } else {
      box.innerHTML = hits
        .map(
          (p) => `<a class="search-item text-decoration-none" href="product.html?p=${p.slug}">
            <img src="assets/img/products/${p.image}" alt="${esc(p.name)}" loading="lazy">
            <div class="flex-grow-1">
              <div class="fw-semibold small text-dark text-truncate">${esc(p.name)}</div>
              <div class="small text-primary fw-bold">${fmt(p.price)}</div>
            </div></a>`
        )
        .join('');
    }
    box.classList.add('active');
  };

  input.addEventListener('input', render);
  input.addEventListener('focus', render);
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      location.href = 'products.html?q=' + encodeURIComponent(input.value.trim());
    }
    if (e.key === 'Escape') close();
  });
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.search-bar')) close();
  });
}

function initNotifications() {
  const list = document.getElementById('notifList');
  if (!list) return;
  const render = () => {
    const items = getNotifications();
    if (!items.length) {
      list.innerHTML = '<div class="p-3 text-center text-muted small">Belum ada notifikasi</div>';
      return;
    }
    list.innerHTML = items
      .slice(0, 12)
      .map(
        (n) => `<a class="notif-item d-block text-decoration-none text-reset ${n.read ? '' : 'unread'}" href="#">
          <div class="notif-title"><i class="bi ${n.read ? 'bi-bell' : 'bi-bell-fill text-primary'}"></i> ${esc(n.title)}</div>
          <div class="notif-text">${esc(n.message)}</div>
          <div class="notif-time">${esc(n.createdAt)}</div></a>`
      )
      .join('');
  };
  render();
  const btn = document.getElementById('notifReadAll');
  if (btn) {
    btn.addEventListener('click', () => {
      markAllRead();
      render();
      toast('Semua notifikasi ditandai dibaca', 'success');
    });
  }
}

/* ---------- Product card ---------- */
function productCardHtml(p) {
  const cat = getCategory(p.categoryId);
  const seller = SELLERS[p.sellerId] || { name: 'Seller' };
  const off = discount(p);
  const cond = CONDITION_LABELS[p.condition] || CONDITION_LABELS.good;
  const r = ratingOf(p.id);
  const hidden = p.verification !== 'approved' ? ' style="opacity:.55"' : '';
  return `
  <div class="col-6 col-md-4 col-lg-3 mb-4"${hidden}>
    <div class="product-card h-100 fade-in">
      <a class="card-img-wrapper" href="product.html?p=${p.slug}" aria-label="${esc(p.name)}">
        <img src="assets/img/products/${p.image}" alt="${esc(p.name)}" loading="lazy">
        <span class="condition-badge badge bg-${cond.badge}"><i class="bi ${cond.icon}"></i> ${cond.label}</span>
        ${off ? `<span class="discount-badge">-${off}%</span>` : ''}
      </a>
      <button class="wishlist-btn ${inWishlist(p.id) ? 'active' : ''}" data-wishlist="${p.id}" aria-label="Simpan ke wishlist" title="Wishlist">
        <i class="bi bi-heart${inWishlist(p.id) ? '-fill' : ''}"></i>
      </button>
      <div class="card-body">
        <div class="small text-muted mb-1">${esc(cat ? cat.name : '')}</div>
        <a class="product-title text-decoration-none" href="product.html?p=${p.slug}">${esc(p.name)}</a>
        <div class="product-price">${fmt(p.price)}</div>
        ${p.originalPrice > p.price ? `<div class="original-price">${fmt(p.originalPrice)}</div>` : ''}
        <div class="d-flex align-items-center gap-1 mt-1 small text-muted">
          <i class="bi bi-star-fill text-warning"></i>
          <span>${r ? r.toFixed(1) : 'Baru'}</span>
          <span class="text-truncate ms-1">&middot; ${esc(seller.name)}</span>
        </div>
        <div class="product-seller d-flex justify-content-between align-items-center">
          <span class="verified-badge"><i class="bi bi-patch-check-fill"></i> Terverifikasi</span>
          <span class="text-muted">Sisa ${p.stock}</span>
        </div>
        <button class="btn btn-primary btn-sm mt-2 w-100" data-add-cart="${p.id}" ${p.stock < 1 ? 'disabled' : ''}>
          <i class="bi bi-cart-plus"></i> Keranjang
        </button>
      </div>
    </div>
  </div>`;
}

function bindProductCards(root) {
  (root || document).querySelectorAll('[data-add-cart]').forEach((btn) => {
    btn.addEventListener('click', () => addToCart(btn.dataset.addCart));
  });
  (root || document).querySelectorAll('[data-wishlist]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const active = toggleWishlist(btn.dataset.wishlist);
      btn.classList.toggle('active', active);
      btn.innerHTML = `<i class="bi bi-heart${active ? '-fill' : ''}"></i>`;
    });
  });
}

function renderProducts(container, list) {
  if (!container) return;
  if (!list.length) {
    container.innerHTML = `<div class="col-12"><div class="empty-state">
      <i class="bi bi-search"></i>
      <h5>Produk tidak ditemukan</h5>
      <p>Coba ubah kata kunci atau filter kategori.</p>
      <a class="btn btn-primary-custom" href="products.html">Reset pencarian</a>
    </div></div>`;
    return;
  }
  container.innerHTML = list.map(productCardHtml).join('');
  bindProductCards(container);
}

document.addEventListener('DOMContentLoaded', () => {
  const active = document.body.dataset.nav || '';
  mountLayout(active);
});