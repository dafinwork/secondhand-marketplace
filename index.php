<?php
session_start();
require_once __DIR__ . '/src/config/database.php';
require_once __DIR__ . '/src/config/helpers.php';
checkRememberMe();

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? '';

// API Routes
if ($page === 'api') {
    header('Content-Type: application/json');
    require_once __DIR__ . '/src/controllers/ApiController.php';
    $api = new ApiController();
    $endpoint = $_GET['endpoint'] ?? '';
    switch ($endpoint) {
        case 'cart_add': $api->addToCart(); break;
        case 'cart_update': $api->updateCart(); break;
        case 'cart_remove': $api->removeFromCart(); break;
        case 'cart_count': $api->cartCount(); break;
        case 'wishlist_toggle': $api->toggleWishlist(); break;
        case 'search': $api->searchProducts(); break;
        case 'notifications': $api->getNotifications(); break;
        case 'mark_read': $api->markNotifRead(); break;
        default: echo json_encode(['error' => 'Invalid']); break;
    }
    exit;
}

// Auth
if (in_array($page, ['login','register','logout'])) {
    require_once __DIR__ . '/src/controllers/AuthController.php';
    $auth = new AuthController();
    if ($page === 'login') $auth->login();
    elseif ($page === 'register') $auth->register();
    else $auth->logout();
    exit;
}

// POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    require_once __DIR__ . '/src/controllers/ActionController.php';
    $ctrl = new ActionController();
    switch ($action) {
        case 'checkout_process': $ctrl->processCheckout(); break;
        case 'upload_payment': $ctrl->uploadPayment(); break;
        case 'submit_review': $ctrl->submitReview(); break;
        case 'seller_save_product': $ctrl->saveProduct(); break;
        case 'seller_delete_product': $ctrl->deleteProduct(); break;
        case 'seller_confirm_order': $ctrl->confirmOrder(); break;
        case 'seller_ship_order': $ctrl->shipOrder(); break;
        case 'admin_verify_product': $ctrl->verifyProduct(); break;
        case 'admin_save_category': $ctrl->saveCategory(); break;
        case 'admin_toggle_user': $ctrl->toggleUser(); break;
        case 'admin_update_order': $ctrl->adminUpdateOrder(); break;
        case 'buyer_confirm_received': $ctrl->confirmReceived(); break;
        default: redirect('/index.php'); break;
    }
    exit;
}

// Page routes
$routes = [
    'home' => 'public/home.php',
    'products' => 'public/products.php',
    'product_detail' => 'public/product_detail.php',
    'cart' => 'public/cart.php',
    'checkout' => 'public/checkout.php',
    'payment' => 'public/payment.php',
    'order_tracking' => 'public/order_tracking.php',
    'review' => 'public/review.php',
    'buyer_dashboard' => 'buyer/dashboard.php',
    'buyer_orders' => 'buyer/orders.php',
    'buyer_wishlist' => 'buyer/wishlist.php',
    'buyer_notifications' => 'buyer/notifications.php',
    'seller_dashboard' => 'seller/dashboard.php',
    'seller_products' => 'seller/products.php',
    'seller_product_form' => 'seller/product_form.php',
    'seller_orders' => 'seller/orders.php',
    'admin_dashboard' => 'admin/dashboard.php',
    'admin_users' => 'admin/users.php',
    'admin_categories' => 'admin/categories.php',
    'admin_products' => 'admin/products.php',
    'admin_orders' => 'admin/orders.php',
    'admin_verify' => 'admin/verify_product.php',
    'admin_settings' => 'admin/settings.php',
];

$authPages = ['cart','checkout','payment','order_tracking','review',
    'buyer_dashboard','buyer_orders','buyer_wishlist','buyer_notifications',
    'seller_dashboard','seller_products','seller_product_form','seller_orders',
    'admin_dashboard','admin_users','admin_categories','admin_products','admin_orders','admin_verify','admin_settings'];

$roleMap = [
    'buyer_' => 'buyer', 'seller_' => 'seller', 'admin_' => 'admin'
];

if (isset($routes[$page])) {
    if (in_array($page, $authPages)) requireLogin();
    foreach ($roleMap as $prefix => $role) {
        if (strpos($page, $prefix) === 0) { requireRole($role); break; }
    }
    $isDashboard = preg_match('/^(admin_|seller_|buyer_)/', $page);
    $contentView = $routes[$page];
    if ($isDashboard) {
        require_once __DIR__ . '/src/views/layouts/dashboard_layout.php';
    } else {
        require_once __DIR__ . '/src/views/layouts/main_layout.php';
    }
} else {
    $contentView = 'public/404.php';
    require_once __DIR__ . '/src/views/layouts/main_layout.php';
}
