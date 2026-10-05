const fs = require('fs');
const path = require('path');
const { execFileSync } = require('child_process');

const root = path.join(__dirname, '..');
const jsDir = path.join(root, 'site', 'assets', 'js');
const stub = `
const store = new Map();
globalThis.localStorage = {
  getItem: (k) => (store.has(k) ? store.get(k) : null),
  setItem: (k, v) => store.set(k, String(v)),
  removeItem: (k) => store.delete(k),
};
globalThis.location = { href: '', search: '', pathname: '/products.html' };
globalThis.history = { replaceState() {} };
const stubEl = () => ({
  id: 'x', classList: { add() {}, remove() {}, toggle() {} }, innerHTML: '', textContent: '',
  addEventListener: () => {}, remove() {}, insertAdjacentHTML() {}, appendChild() {}, closest: () => null,
});
globalThis.document = {
  getElementById: stubEl,
  querySelector: stubEl,
  querySelectorAll: () => [],
  addEventListener: () => {},
  createElement: stubEl,
  body: { dataset: {}, appendChild() {} },
};
globalThis.bootstrap = { Toast: class { constructor() {} show() {} } };
globalThis.window = { scrollY: 0, addEventListener() {} };
`;

const body = `
const out = (label, val) => console.log(String(label).padEnd(34), val);
out('categories', CATEGORIES.length);
out('products', allProducts().length);
out('approved products', allProducts().filter((p) => p.verification === 'approved').length);
out('slug lookup', getProductBySlug('macbook-air-m1-2020-bekas')?.name);
out('category slug -> id', getCategory('elektronik')?.id);
out('rating product 1', ratingOf(1));
out('reviews product 1', reviewsFor(1).length);

save(KEY.user, { ...USERS[5] });
addToCart(1, 1);
addToCart(6, 2);
out('add qty>stock rejected', cartCount());
addToCart(6, 1);
out('cart count', cartCount());
out('cart subtotal', fmt(cartSubtotal()));
updateCartQty(6, 0);
out('subtotal after qty 0', fmt(cartSubtotal()));
out('cart rows', cartDetail().length);

out('isLoggedIn', isLoggedIn());
out('wishlist add 1', toggleWishlist(1));
out('inWishlist(1)', inWishlist(1));
out('wishlist remove 1', toggleWishlist(1));

saveCart([{ productId: 3, qty: 1 }, { productId: 4, qty: 1 }]);
const order = createOrder({
  total: 17150000, subtotal: 17000000, shipping: 15000, shippingMethod: 'reguler',
  address: 'Jl. Test No. 1, Jakarta', paymentMethod: 'bank_transfer', paymentStatus: 'pending',
  bankName: 'BCA', accountNumber: '1234567890', accountName: 'Budi Santoso',
  items: cartDetail().map((i) => ({ productId: i.productId, sellerId: i.product.sellerId, qty: i.qty, price: i.product.price, subtotal: i.subtotal, status: 'pending' })),
});
out('order invoice', order.invoice);
out('cart cleared', cartCount());
out('buyer orders', getOrders().length);
['confirmed', 'processing', 'shipped'].forEach((s) => advanceOrder(order.id, s));
const shipped = getOrders().find((o) => o.id === order.id);
out('status after advance', shipped.status);
out('tracking generated', !!shipped.tracking);
out('unread notifications', unreadCount());
markAllRead();
out('unread after markRead', unreadCount());

submitReview({ productId: 1, orderId: order.id, rating: 4, comment: 'Mantap' });
out('rating product 1', ratingOf(1).toFixed(2));
out('discount product 1', discount(getProductById(1)) + '%');
out('fmt(8500000)', fmt(8500000));
out('escaping', esc('<img src=x onerror=alert(1)>'));
out('product card render len', productCardHtml(getProductById(1)).length > 200);
`;

const src =
  stub +
  fs.readFileSync(path.join(jsDir, 'data.js'), 'utf8') +
  '\n' +
  fs.readFileSync(path.join(jsDir, 'app.js'), 'utf8') +
  '\n' +
  body;

const tmp = path.join(require('os').tmpdir(), 'sh-smoke-test.js');
fs.writeFileSync(tmp, src, 'utf8');
try {
  const output = execFileSync(process.execPath, [tmp], { encoding: 'utf8' });
  process.stdout.write(output);
} catch (e) {
  process.stdout.write(e.stdout || '');
  process.stderr.write(e.stderr || '');
  process.exit(e.status || 1);
}