<?php
/**
 * Authentication Controller
 */
class AuthController {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                setFlash('danger', 'Email dan password harus diisi.');
                redirect('/index.php?page=login');
            }

            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if (!empty($_POST['remember'])) {
                    setRememberMeCookie($user['id'], $user['password']);
                } else {
                    clearRememberMeCookie();
                }

                setFlash('success', 'Selamat datang, ' . $user['name'] . '!');
                redirect('/index.php?page=' . $user['role'] . '_dashboard');
            } else {
                clearRememberMeCookie();
                setFlash('danger', 'Email atau password salah.');
                redirect('/index.php?page=login');
            }
            return;
        }

        if (isLoggedIn()) redirect('/index.php');
        require_once __DIR__ . '/../views/public/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'buyer';

            if (empty($name) || empty($email) || empty($password)) {
                setFlash('danger', 'Semua field wajib diisi.');
                redirect('/index.php?page=register');
            }
            if ($password !== $confirm) {
                setFlash('danger', 'Konfirmasi password tidak cocok.');
                redirect('/index.php?page=register');
            }
            if (strlen($password) < 6) {
                setFlash('danger', 'Password minimal 6 karakter.');
                redirect('/index.php?page=register');
            }
            if (!in_array($role, ['buyer', 'seller'])) $role = 'buyer';

            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                setFlash('danger', 'Email sudah terdaftar.');
                redirect('/index.php?page=register');
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $hash, $role]);

            setFlash('success', 'Registrasi berhasil! Silakan login.');
            redirect('/index.php?page=login');
            return;
        }

        if (isLoggedIn()) redirect('/index.php');
        require_once __DIR__ . '/../views/public/register.php';
    }

    public function logout() {
        clearRememberMeCookie();
        session_destroy();
        session_start();
        setFlash('success', 'Anda telah logout.');
        redirect('/index.php');
    }
}
