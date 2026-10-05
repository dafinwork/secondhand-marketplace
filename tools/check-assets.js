const fs = require('fs');
const path = require('path');

const siteDir = path.join(__dirname, '..', 'site');
const problems = [];

const required = [
  'index.html', 'products.html', 'product.html', 'cart.html', 'checkout.html', 'payment.html',
  'orders.html', 'wishlist.html', 'tracking.html', 'review.html', 'login.html', 'register.html',
  'dashboard.html', 'how-it-works.html', 'seller.html', '404.html',
  'robots.txt', 'sitemap.xml', '.nojekyll',
  'assets/css/style.css', 'assets/js/data.js', 'assets/js/app.js', 'assets/img/no-image.svg',
];

required.forEach((f) => {
  if (!fs.existsSync(path.join(siteDir, f))) problems.push('missing required file: ' + f);
});

const dataJs = fs.readFileSync(path.join(siteDir, 'assets/js/data.js'), 'utf8');
const data = eval(dataJs + '; ({ CATEGORIES, PRODUCTS, SELLERS, SEED_ORDERS, SEED_REVIEWS })');

data.PRODUCTS.forEach((p) => {
  const img = path.join(siteDir, 'assets/img/products', p.image);
  if (!fs.existsSync(img)) problems.push(`product ${p.id} (${p.slug}) references missing image: ${p.image}`);
  if (!data.CATEGORIES.some((c) => c.id === p.categoryId)) problems.push(`product ${p.id} has unknown categoryId ${p.categoryId}`);
  if (!data.SELLERS[p.sellerId]) problems.push(`product ${p.id} has unknown sellerId ${p.sellerId}`);
});

data.SEED_ORDERS.forEach((o) => {
  o.items.forEach((i) => {
    if (!data.PRODUCTS.some((p) => p.id === i.productId)) problems.push(`order ${o.invoice} references unknown productId ${i.productId}`);
  });
});

data.SEED_REVIEWS.forEach((r) => {
  if (!data.PRODUCTS.some((p) => p.id === r.productId)) problems.push(`review ${r.id} references unknown productId ${r.productId}`);
});

if (new Set(data.PRODUCTS.map((p) => p.slug)).size !== data.PRODUCTS.length) problems.push('duplicate product slug found');
if (new Set(data.CATEGORIES.map((c) => c.slug)).size !== data.CATEGORIES.length) problems.push('duplicate category slug found');

const pages = fs.readdirSync(siteDir).filter((f) => f.endsWith('.html'));
pages.forEach((page) => {
  const raw = fs.readFileSync(path.join(siteDir, page), 'utf8');
  const html = raw.replace(/<script[\s\S]*?<\/script>/g, '');
  const links = [...html.matchAll(/(?:href|src)="(?!https?:|#|data:|mailto:)([^"]+)"/g)].map((m) => m[1]);
  links.forEach((link) => {
    const target = link.split('#')[0].split('?')[0];
    if (!target) return;
    if (!fs.existsSync(path.join(siteDir, target))) problems.push(`${page} links to missing file: ${target}`);
  });
});

if (problems.length) {
  problems.forEach((p) => console.error('FAIL ' + p));
  process.exit(1);
}
console.log(`OK - ${pages.length} pages, ${required.length} required files, ${data.PRODUCTS.length} products validated`);