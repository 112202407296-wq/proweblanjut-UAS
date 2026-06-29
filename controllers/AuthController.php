<?php

// ============================================================
// AuthController.php — Mengurus Login, Register, Logout
// ============================================================

class AuthController extends Controller {

    // ── Tampilkan Halaman Login ───────────────────────────────
    public function login() {
        // Jika sudah login, langsung redirect ke tujuan
        if (isset($_SESSION['user_id'])) {
            $this->redirectByRole();
        }
        $data['judul']   = 'Login — KopSkuy';
        $data['error']   = $_SESSION['flash_error'] ?? null;
        $data['success'] = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        $this->view('layouts/header', $data);
        $this->view('auth/login', $data);
        $this->view('layouts/footer');
    }

    // ── Tampilkan Halaman Register ────────────────────────────
    public function register() {
        if (isset($_SESSION['user_id'])) {
            $this->redirectByRole();
        }
        $data['judul'] = 'Daftar Akun — KopSkuy';
        $data['error'] = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $this->view('layouts/header', $data);
        $this->view('auth/register', $data);
        $this->view('layouts/footer');
    }

    // ── Proses Pendaftaran (POST) ─────────────────────────────
    public function processRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/auth/register');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validasi dasar
        if (empty($username) || empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'Semua kolom wajib diisi!';
            header('Location: ' . BASEURL . '/auth/register');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['flash_error'] = 'Password minimal 6 karakter!';
            header('Location: ' . BASEURL . '/auth/register');
            exit;
        }

        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();

        // Cek email duplikat
        if ($userModel->isEmailExists($email)) {
            $_SESSION['flash_error'] = 'Email sudah terdaftar. Silakan gunakan email lain.';
            header('Location: ' . BASEURL . '/auth/register');
            exit;
        }

        // Simpan ke database (password di-hash di dalam model)
        if ($userModel->register($username, $email, $password)) {
            $_SESSION['flash_success'] = 'Akun berhasil dibuat! Silakan login.';
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        } else {
            $_SESSION['flash_error'] = 'Gagal membuat akun. Coba lagi.';
            header('Location: ' . BASEURL . '/auth/register');
            exit;
        }
    }

    // ── Proses Login (POST) ───────────────────────────────────
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'Email dan password wajib diisi!';
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }

        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        $user      = $userModel->getUserByEmail($email);

        // ⚠️ CPMK 5: Verifikasi password dengan password_verify()
        if ($user && password_verify($password, $user['password'])) {

            // ─── KONTRAK SESSION MUTLAK ───────────────────────
            // Key ini TIDAK BOLEH diubah (Randi & Annas bergantung di sini)
            $_SESSION['user_id']  = $user['id'];       // Dipakai Randi (cart/order)
            $_SESSION['username'] = $user['username']; // Dipakai Annas (navbar)
            $_SESSION['role']     = $user['role'];     // 'admin' atau 'customer'
            // ─────────────────────────────────────────────────

            $this->redirectByRole();

        } else {
            $_SESSION['flash_error'] = 'Email atau password salah!';
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }

    // ── Logout: Hancurkan Session ─────────────────────────────
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . BASEURL . '/auth/login');
        exit;
    }

    // ── Helper: Redirect berdasarkan Role ────────────────────
    private function redirectByRole() {
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            header('Location: ' . BASEURL . '/admin');
        } else {
            header('Location: ' . BASEURL);
        }
        exit;
    }
}
