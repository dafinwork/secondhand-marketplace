<?php
/**
 * API Controller - Handles AJAX requests
 */
class ApiController {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    // Add product to cart
    public function addToCart() {
        if (!isLoggedIn()) { echo json_encode(['success'=>false,'message'=>'Login required']); return; }
        $productId = intval($_POST['product_id'] ?? 0);
        $qty = intval($_POST['quantity'] ?? 1);
        $userId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("SELECT * FROM carts WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $this->db->prepare("UPDATE carts SET quantity = quantity + ? WHERE id = ?");
            $stmt->execute([$qty, $existing['id']]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $productId, $qty]);
        }

        $count = getCartCount($userId);
        echo json_encode(['success'=>true,'message'=>'Ditambahkan ke keranjang!','cartCount'=>$count]);
    }

    // Update cart quantity
    public function updateCart() {
        if (!isLoggedIn()) { echo json_encode(['success'=>false]); return; }
        $cartId = intval($_POST['cart_id'] ?? 0);
        $qty = intval($_POST['quantity'] ?? 1);
        if ($qty < 1) $qty = 1;

        $stmt = $this->db->prepare("UPDATE carts SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$qty, $cartId, $_SESSION['user_id']]);
        echo json_encode(['success'=>true]);
    }

    // Remove from cart
    public function removeFromCart() {
        if (!isLoggedIn()) { echo json_encode(['success'=>false]); return; }
        $cartId = intval($_POST['cart_id'] ?? 0);
        $stmt = $this->db->prepare("DELETE FROM carts WHERE id = ? AND user_id = ?");
        $stmt->execute([$cartId, $_SESSION['user_id']]);
        echo json_encode(['success'=>true,'cartCount'=>getCartCount($_SESSION['user_id'])]);
    }

    // Cart count
    public function cartCount() {
        if (!isLoggedIn()) { echo json_encode(['count'=>0]); return; }
        echo json_encode(['count'=>getCartCount($_SESSION['user_id'])]);
    }

    // Toggle wishlist
    public function toggleWishlist() {
        if (!isLoggedIn()) { echo json_encode(['success'=>false,'message'=>'Login required']); return; }
        $productId = intval($_POST['product_id'] ?? 0);
        $userId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $exists = $stmt->fetch();

        if ($exists) {
            $stmt = $this->db->prepare("DELETE FROM wishlist WHERE id = ?");
            $stmt->execute([$exists['id']]);
            echo json_encode(['success'=>true,'added'=>false,'message'=>'Dihapus dari wishlist']);
        } else {
            $stmt = $this->db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$userId, $productId]);
            echo json_encode(['success'=>true,'added'=>true,'message'=>'Ditambahkan ke wishlist']);
        }
    }

    // Search products
    public function searchProducts() {
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) { echo json_encode([]); return; }

        $stmt = $this->db->prepare("SELECT p.id, p.name, p.slug, p.price, p.images, p.condition_status FROM products p WHERE p.name LIKE ? AND p.verification_status = 'approved' AND p.is_active = 1 LIMIT 8");
        $stmt->execute(["%$q%"]);
        $results = $stmt->fetchAll();

        foreach ($results as &$r) {
            $imgs = json_decode($r['images'], true);
            $r['image'] = !empty($imgs) ? $imgs[0] : '';
            $r['price_formatted'] = formatRupiah($r['price']);
            unset($r['images']);
        }
        echo json_encode($results);
    }

    // Get notifications
    public function getNotifications() {
        if (!isLoggedIn()) { echo json_encode([]); return; }
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
        $stmt->execute([$_SESSION['user_id']]);
        $notifs = $stmt->fetchAll();
        foreach ($notifs as &$n) { $n['time_ago'] = timeAgo($n['created_at']); }
        echo json_encode($notifs);
    }

    // Mark notification as read
    public function markNotifRead() {
        if (!isLoggedIn()) { echo json_encode(['success'=>false]); return; }
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $_SESSION['user_id']]);
        } else {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        }
        echo json_encode(['success'=>true]);
    }
}
