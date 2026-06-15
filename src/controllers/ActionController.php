<?php
/**
 * Action Controller - Handles form POST actions
 */
class ActionController {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    // Process checkout
    public function processCheckout() {
        requireLogin();
        $userId = $_SESSION['user_id'];
        $address = trim($_POST['shipping_address'] ?? '');
        $method = $_POST['shipping_method'] ?? 'reguler';
        $notes = trim($_POST['notes'] ?? '');

        if (empty($address)) { setFlash('danger', 'Alamat pengiriman harus diisi.'); redirect('/index.php?page=checkout'); }

        // Get cart items
        $stmt = $this->db->prepare("SELECT c.*, p.price, p.name, p.seller_id, p.stock, p.weight FROM carts c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll();

        if (empty($items)) { setFlash('warning', 'Keranjang kosong.'); redirect('/index.php?page=cart'); }

        $this->db->beginTransaction();
        try {
            $totalAmount = 0;
            $totalWeight = 0;
            foreach ($items as $item) {
                if ($item['quantity'] > $item['stock']) { throw new Exception("Stok {$item['name']} tidak cukup."); }
                $totalAmount += $item['price'] * $item['quantity'];
                $totalWeight += $item['weight'] * $item['quantity'];
            }

            $shippingCost = calculateShipping($totalWeight, $method);
            $invoice = generateInvoice();

            // Create order
            $stmt = $this->db->prepare("INSERT INTO orders (invoice_number, buyer_id, total_amount, shipping_cost, shipping_address, shipping_method, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$invoice, $userId, $totalAmount, $shippingCost, $address, $method, $notes]);
            $orderId = $this->db->lastInsertId();

            // Create order items & update stock
            foreach ($items as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $stmt = $this->db->prepare("INSERT INTO order_items (order_id, product_id, seller_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$orderId, $item['product_id'], $item['seller_id'], $item['quantity'], $item['price'], $subtotal]);

                // Auto update stock
                $stmt = $this->db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['product_id']]);

                // Notify seller
                createNotification($item['seller_id'], 'Pesanan Baru!', "Anda mendapat pesanan baru #{$invoice} untuk {$item['name']}.", 'order', $orderId);
            }

            // Clear cart
            $stmt = $this->db->prepare("DELETE FROM carts WHERE user_id = ?");
            $stmt->execute([$userId]);

            // Create payment record
            $stmt = $this->db->prepare("INSERT INTO payments (order_id, amount) VALUES (?, ?)");
            $stmt->execute([$orderId, $totalAmount + $shippingCost]);

            // Notify buyer
            createNotification($userId, 'Pesanan Dibuat', "Pesanan #{$invoice} berhasil dibuat. Silakan lakukan pembayaran.", 'order', $orderId);

            $this->db->commit();
            setFlash('success', "Pesanan #{$invoice} berhasil dibuat!");
            redirect('/index.php?page=payment&order_id=' . $orderId);
        } catch (Exception $e) {
            $this->db->rollBack();
            setFlash('danger', $e->getMessage());
            redirect('/index.php?page=checkout');
        }
    }

    // Upload payment proof
    public function uploadPayment() {
        requireLogin();
        $orderId = intval($_POST['order_id'] ?? 0);
        $bankName = trim($_POST['bank_name'] ?? '');
        $accountName = trim($_POST['account_name'] ?? '');
        $accountNumber = trim($_POST['account_number'] ?? '');

        if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === 0) {
            $filename = uploadFile($_FILES['proof_image'], PAYMENT_IMG_PATH);
            if ($filename) {
                $stmt = $this->db->prepare("UPDATE payments SET bank_name = ?, account_name = ?, account_number = ?, proof_image = ?, payment_method = 'bank_transfer' WHERE order_id = ?");
                $stmt->execute([$bankName, $accountName, $accountNumber, $filename, $orderId]);

                $stmt = $this->db->prepare("UPDATE orders SET status = 'confirmed' WHERE id = ?");
                $stmt->execute([$orderId]);

                createNotification($_SESSION['user_id'], 'Pembayaran Diupload', 'Bukti pembayaran berhasil diupload.', 'payment', $orderId);
                setFlash('success', 'Bukti pembayaran berhasil diupload!');
            }
        } else {
            setFlash('danger', 'Silakan upload bukti pembayaran.');
        }
        redirect('/index.php?page=buyer_orders');
    }

    // Submit review
    public function submitReview() {
        requireLogin();
        $productId = intval($_POST['product_id'] ?? 0);
        $orderId = intval($_POST['order_id'] ?? 0);
        $rating = intval($_POST['rating'] ?? 5);
        $comment = trim($_POST['comment'] ?? '');
        $image = null;

        if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] === 0) {
            $image = uploadFile($_FILES['review_image'], REVIEW_IMG_PATH);
        }

        $stmt = $this->db->prepare("INSERT INTO reviews (product_id, user_id, order_id, rating, comment, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$productId, $_SESSION['user_id'], $orderId, $rating, $comment, $image]);

        setFlash('success', 'Review berhasil dikirim!');
        redirect('/index.php?page=buyer_orders');
    }

    // Seller: Save product (create/update)
    public function saveProduct() {
        requireRole('seller');
        $id = intval($_POST['product_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $categoryId = intval($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $originalPrice = floatval($_POST['original_price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 1);
        $condition = $_POST['condition_status'] ?? 'good';
        $weight = intval($_POST['weight'] ?? 1000);

        if (empty($name) || $price <= 0) { setFlash('danger', 'Nama dan harga harus diisi.'); redirect('/index.php?page=seller_product_form'); }

        // Handle images
        $images = [];
        if (!empty($_POST['existing_images'])) {
            $images = json_decode($_POST['existing_images'], true) ?: [];
        }
        if (isset($_FILES['images'])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp) {
                if ($_FILES['images']['error'][$key] === 0) {
                    $file = ['name'=>$_FILES['images']['name'][$key], 'tmp_name'=>$tmp, 'error'=>0, 'size'=>$_FILES['images']['size'][$key]];
                    $uploaded = uploadFile($file, PRODUCT_IMG_PATH);
                    if ($uploaded) $images[] = $uploaded;
                }
            }
        }
        $imagesJson = json_encode($images);

        if ($id > 0) {
            // Update
            $stmt = $this->db->prepare("UPDATE products SET name=?, slug=?, category_id=?, description=?, price=?, original_price=?, stock=?, condition_status=?, weight=?, images=?, verification_status='pending' WHERE id=? AND seller_id=?");
            $stmt->execute([$name, createSlug($name), $categoryId, $description, $price, $originalPrice, $stock, $condition, $weight, $imagesJson, $id, $_SESSION['user_id']]);
            setFlash('success', 'Produk berhasil diupdate!');
        } else {
            // Create
            $stmt = $this->db->prepare("INSERT INTO products (seller_id, category_id, name, slug, description, price, original_price, stock, condition_status, weight, images) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $categoryId, $name, createSlug($name), $description, $price, $originalPrice, $stock, $condition, $weight, $imagesJson]);
            setFlash('success', 'Produk berhasil ditambahkan! Menunggu verifikasi admin.');
        }
        redirect('/index.php?page=seller_products');
    }

    // Seller: Delete product
    public function deleteProduct() {
        requireRole('seller');
        $id = intval($_POST['product_id'] ?? 0);
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        setFlash('success', 'Produk berhasil dihapus.');
        redirect('/index.php?page=seller_products');
    }

    // Seller: Confirm order
    public function confirmOrder() {
        requireRole('seller');
        $itemId = intval($_POST['item_id'] ?? 0);
        $stmt = $this->db->prepare("UPDATE order_items SET status = 'confirmed' WHERE id = ? AND seller_id = ?");
        $stmt->execute([$itemId, $_SESSION['user_id']]);
        setFlash('success', 'Pesanan dikonfirmasi.');
        redirect('/index.php?page=seller_orders');
    }

    // Seller: Ship order
    public function shipOrder() {
        requireRole('seller');
        $itemId = intval($_POST['item_id'] ?? 0);
        $orderId = intval($_POST['order_id'] ?? 0);
        $tracking = trim($_POST['tracking_number'] ?? '');

        $stmt = $this->db->prepare("UPDATE order_items SET status = 'shipped' WHERE id = ? AND seller_id = ?");
        $stmt->execute([$itemId, $_SESSION['user_id']]);

        if ($tracking) {
            $stmt = $this->db->prepare("UPDATE orders SET tracking_number = ?, status = 'shipped' WHERE id = ?");
            $stmt->execute([$tracking, $orderId]);
        }

        // Notify buyer
        $stmt = $this->db->prepare("SELECT o.buyer_id, o.invoice_number FROM orders o WHERE o.id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if ($order) {
            createNotification($order['buyer_id'], 'Pesanan Dikirim', "Pesanan #{$order['invoice_number']} telah dikirim." . ($tracking ? " No. Resi: $tracking" : ''), 'order', $orderId);
        }

        setFlash('success', 'Pesanan telah dikirim!');
        redirect('/index.php?page=seller_orders');
    }

    // Admin: Verify product
    public function verifyProduct() {
        requireRole('admin');
        $id = intval($_POST['product_id'] ?? 0);
        $status = $_POST['status'] ?? 'pending';
        $note = trim($_POST['note'] ?? '');

        $stmt = $this->db->prepare("UPDATE products SET verification_status = ?, verification_note = ? WHERE id = ?");
        $stmt->execute([$status, $note, $id]);

        $stmt = $this->db->prepare("SELECT seller_id, name FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product) {
            $msg = $status === 'approved' ? "Produk '{$product['name']}' telah disetujui." : "Produk '{$product['name']}' ditolak. Alasan: $note";
            createNotification($product['seller_id'], 'Verifikasi Produk', $msg, 'product', $id);
        }

        setFlash('success', 'Status verifikasi diupdate.');
        redirect('/index.php?page=admin_verify');
    }

    // Admin: Save category
    public function saveCategory() {
        requireRole('admin');
        $id = intval($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? 'bi-tag');
        $desc = trim($_POST['description'] ?? '');

        if ($id > 0) {
            $stmt = $this->db->prepare("UPDATE categories SET name=?, icon=?, description=? WHERE id=?");
            $stmt->execute([$name, $icon, $desc, $id]);
        } else {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)));
            $stmt = $this->db->prepare("INSERT INTO categories (name, slug, icon, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $icon, $desc]);
        }
        setFlash('success', 'Kategori berhasil disimpan.');
        redirect('/index.php?page=admin_categories');
    }

    // Admin: Toggle user active status
    public function toggleUser() {
        requireRole('admin');
        $id = intval($_POST['user_id'] ?? 0);
        $stmt = $this->db->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ? AND role != 'admin'");
        $stmt->execute([$id]);
        setFlash('success', 'Status user diupdate.');
        redirect('/index.php?page=admin_users');
    }

    // Admin: Update order status
    public function adminUpdateOrder() {
        requireRole('admin');
        $id = intval($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        // Also update payment if confirmed
        if ($status === 'confirmed') {
            $stmt = $this->db->prepare("UPDATE payments SET status = 'verified', verified_at = NOW() WHERE order_id = ?");
            $stmt->execute([$id]);
        }
        setFlash('success', 'Status pesanan diupdate.');
        redirect('/index.php?page=admin_orders');
    }

    // Buyer: Confirm received
    public function confirmReceived() {
        requireLogin();
        $orderId = intval($_POST['order_id'] ?? 0);
        $stmt = $this->db->prepare("UPDATE orders SET status = 'completed' WHERE id = ? AND buyer_id = ?");
        $stmt->execute([$orderId, $_SESSION['user_id']]);
        $stmt = $this->db->prepare("UPDATE order_items SET status = 'completed' WHERE order_id = ?");
        $stmt->execute([$orderId]);
        setFlash('success', 'Pesanan dikonfirmasi selesai!');
        redirect('/index.php?page=buyer_orders');
    }
}
