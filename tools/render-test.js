const fs = require('fs');
const path = require('path');
const { JSDOM, VirtualConsole } = require('jsdom');

const root = path.join(__dirname, '..', 'site');
const dataJs = fs.readFileSync(path.join(root, 'assets/js/data.js'), 'utf8');
const appJs = fs.readFileSync(path.join(root, 'assets/js/app.js'), 'utf8');

function renderWith(html, url, storage) {
  const errors = [];
  const virtualConsole = new VirtualConsole();
  virtualConsole.on('jsdomError', (e) => {
    if (/Could not load|css|stylesheet|Not implemented/i.test(e.message)) return;
    errors.push('jsdomError: ' + e.message);
  });
  virtualConsole.on('error', (msg) => errors.push('console.error: ' + msg));

  const dom = new JSDOM(html, { url, runScripts: 'outside-only', pretendToBeVisual: true, virtualConsole });
  const { window } = dom;

  for (const [k, v] of Object.entries(storage)) window.localStorage.setItem(k, v);
  window.addEventListener('error', (e) => errors.push('window.error: ' + e.message));

  window.eval(`window.bootstrap = new Proxy({}, { get: () => class { constructor() {} show() {} hide() {} dispose() {} } });`);

  const inline = [...window.document.querySelectorAll('script:not([src])')].map((s) => s.textContent);
  try {
    window.eval([dataJs, appJs, ...inline].join('\n;\n'));
  } catch (e) {
    errors.push(e.message + ' @ ' + ((e.stack || '').split('\n')[1] || '').trim());
  }
  return { errors, dom, window };
}

const buyer = { sh_user: JSON.stringify({ id: 5, name: 'Budi Santoso', email: 'buyer1@secondhand.com', phone: '0812', address: 'Jl. Merdeka', role: 'buyer' }) };
const seller = { sh_user: JSON.stringify({ id: 2, name: 'Toko Elektronik Jaya', email: 'seller1@secondhand.com', role: 'seller' }) };
const admin = { sh_user: JSON.stringify({ id: 1, name: 'Admin SecondHand', email: 'admin@secondhand.com', role: 'admin' }) };

const filled = {
  ...buyer,
  sh_cart: JSON.stringify([{ productId: 1, qty: 1 }, { productId: 8, qty: 2 }]),
  sh_wishlist: JSON.stringify([1, 7]),
  sh_orders: JSON.stringify([{
    id: 999, invoice: 'INV-20260101-0001', buyerId: 5, total: 10000000, subtotal: 9800000, shipping: 20000,
    shippingMethod: 'express', address: 'Jl. Test 1, Jakarta', status: 'delivered', tracking: 'JNE999',
    paymentMethod: 'bank_transfer', paymentStatus: 'verified', createdAt: '2026-01-01 10:00',
    items: [{ productId: 1, sellerId: 2, qty: 1, price: 9800000, subtotal: 9800000, status: 'delivered' }],
  }]),
};

const sellerFilled = {
  ...seller,
  sh_listings: JSON.stringify([{
    id: 800, sellerId: 2, categoryId: 1, name: 'Produk Uji Seller', slug: 'produk-uji-seller',
    description: 'Deskripsi uji', price: 1000000, originalPrice: 2000000, stock: 2, condition: 'good',
    weight: 500, image: 'thinkpad-x1.jpg', verification: 'pending', views: 0, rating: 0, reviewCount: 0, createdAt: '2026-02-01',
  }]),
};

const scenarios = [];
const pages = fs.readdirSync(root).filter((f) => f.endsWith('.html') && !/^(login|register)\.html$/.test(f));

pages.forEach((p) => scenarios.push([p, {}]));
['cart.html', 'checkout.html', 'payment.html', 'orders.html', 'wishlist.html', 'review.html', 'tracking.html', 'dashboard.html']
  .forEach((p) => scenarios.push([p, buyer]));
['dashboard.html'].forEach((p) => scenarios.push([p, seller]));

const queryScenarios = [
  ['payment.html?o=999', filled], ['review.html?o=999', filled], ['tracking.html?o=999', filled],
  ['product.html?p=macbook-air-m1-2020-bekas', {}], ['product.html?p=tidak-ada', {}],
  ['products.html?q=iphone&cat=smartphone&sort=price-asc', {}],
  ['products.html?cond=fair&page=2&sort=discount', {}],
  ['products.html?q=&cat=', {}],
  ['login.html', {}], ['register.html', {}], ['login.html?next=orders.html', {}],
];

const roleTabs = {
  buyer: ['overview', 'orders', 'wishlist', 'notifications', 'profile'],
  seller: ['overview', 'listings', 'new-listing', 'orders', 'profile'],
  admin: ['overview', 'verify', 'products', 'categories', 'users', 'orders', 'settings'],
};
const tabHtml = fs.readFileSync(path.join(root, 'dashboard.html'), 'utf8');
Object.entries(roleTabs).forEach(([role, tabs]) => {
  const store = role === 'buyer' ? filled : role === 'seller' ? sellerFilled : admin;
  tabs.forEach((t) => scenarios.push(['dashboard.html?t=' + t, store, tabHtml]));
});

let failed = 0;
let checks = 0;
scenarios.forEach(([target, storage, html]) => {
  const [file, query] = target.split('?');
  const url = 'https://example.github.io/' + file + (query ? '?' + query : '');
  const source = html || fs.readFileSync(path.join(root, file), 'utf8');
  const { errors } = renderWith(source, url, storage);
  checks++;
  if (errors.length) {
    failed++;
    console.log('FAIL ' + target + ' [' + JSON.stringify(Object.keys(storage)) + ']');
    errors.forEach((e) => console.log('     ' + e));
  }
});

console.log((failed ? 'FAILED ' + failed + ' of ' + checks : 'OK - all ' + checks + ' scenarios rendered without script errors'));
process.exit(failed ? 1 : 0);