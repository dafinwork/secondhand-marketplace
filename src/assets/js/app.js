/**
 * SecondHand Marketplace - Main JavaScript
 */
const BASE = document.querySelector('meta[name="base-url"]')?.content || '';

// Navbar scroll effect
window.addEventListener('scroll', () => {
    const nav = document.getElementById('mainNavbar');
    if (nav) nav.classList.toggle('scrolled', window.scrollY > 50);
});

// Toast notification
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const icons = { success: 'bi-check-circle-fill', danger: 'bi-x-circle-fill', warning: 'bi-exclamation-circle-fill', info: 'bi-info-circle-fill' };
    const toast = document.createElement('div');
    toast.className = `toast show align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `<div class="d-flex"><div class="toast-body"><i class="bi ${icons[type] || icons.info} me-2"></i>${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    container.appendChild(toast);
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 4000);
}

// AJAX Helper
function apiCall(endpoint, data = {}, method = 'POST') {
    const basePath = getBasePath();
    const url = method === 'GET'
        ? `${basePath}/index.php?page=api&endpoint=${endpoint}&${new URLSearchParams(data)}`
        : `${basePath}/index.php?page=api&endpoint=${endpoint}`;

    const options = { method };
    if (method === 'POST') {
        const formData = new FormData();
        for (const [key, val] of Object.entries(data)) formData.append(key, val);
        options.body = formData;
    }
    return fetch(url, options).then(r => r.json());
}

function getBasePath() {
    const scripts = document.querySelectorAll('script[src]');
    for (const s of scripts) {
        const idx = s.src.indexOf('/public/assets/js/app.js');
        if (idx !== -1) {
            const fullPath = s.src.substring(0, idx);
            const urlObj = new URL(fullPath);
            return urlObj.pathname;
        }
    }
    return '';
}

// Add to Cart
function addToCart(productId, qty = 1) {
    apiCall('cart_add', { product_id: productId, quantity: qty }).then(res => {
        if (res.success) {
            showToast(res.message, 'success');
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = res.cartCount;
        } else {
            showToast(res.message || 'Gagal menambahkan ke keranjang', 'danger');
        }
    });
}

// Toggle Wishlist
function toggleWishlist(productId, btn) {
    apiCall('wishlist_toggle', { product_id: productId }).then(res => {
        if (res.success) {
            showToast(res.message, res.added ? 'success' : 'info');
            if (btn) btn.classList.toggle('active', res.added);
        } else {
            showToast(res.message || 'Login terlebih dahulu', 'warning');
        }
    });
}

// Live Search
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
let searchTimeout;

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 2) { searchResults?.classList.remove('active'); return; }

        searchTimeout = setTimeout(() => {
            apiCall('search', { q }, 'GET').then(results => {
                if (!searchResults) return;
                if (results.length === 0) {
                    searchResults.innerHTML = '<div class="p-3 text-center text-muted">Tidak ada hasil</div>';
                } else {
                    const basePath = getBasePath();
                    searchResults.innerHTML = results.map(p => `
                        <a href="${basePath}/index.php?page=product_detail&id=${p.id}" class="search-item text-decoration-none text-dark">
                            <img src="${basePath}/public/assets/img/no-image.png" alt="">
                            <div>
                                <div class="fw-semibold" style="font-size:0.9rem">${p.name}</div>
                                <div class="text-primary fw-bold" style="font-size:0.85rem">${p.price_formatted}</div>
                            </div>
                        </a>
                    `).join('');
                }
                searchResults.classList.add('active');
            });
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-bar')) searchResults?.classList.remove('active');
    });
}

// Load Notifications
function loadNotifications() {
    const list = document.getElementById('notifList');
    if (!list) return;
    apiCall('notifications', {}, 'GET').then(notifs => {
        if (notifs.length === 0) {
            list.innerHTML = '<div class="p-3 text-center text-muted"><i class="bi bi-bell-slash"></i><br>Belum ada notifikasi</div>';
            return;
        }
        list.innerHTML = notifs.map(n => `
            <div class="notif-item ${n.is_read == 0 ? 'unread' : ''}" onclick="markNotifRead(${n.id}, this)">
                <div class="notif-title">${n.title}</div>
                <div class="notif-text">${n.message}</div>
                <div class="notif-time">${n.time_ago}</div>
            </div>
        `).join('');
    });
}

function markNotifRead(id, el) {
    apiCall('mark_read', { id }).then(() => {
        if (el) el.classList.remove('unread');
    });
}

// Load notifs on dropdown open
document.querySelector('[data-bs-toggle="dropdown"]')?.addEventListener('show.bs.dropdown', loadNotifications);

// Star Rating
document.querySelectorAll('.star-rating i').forEach(star => {
    star.addEventListener('click', function() {
        const val = this.dataset.value;
        const input = document.getElementById('ratingInput');
        if (input) input.value = val;
        this.parentElement.querySelectorAll('i').forEach(s => {
            s.classList.toggle('active', s.dataset.value <= val);
        });
    });
});

// Fade in animation
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
