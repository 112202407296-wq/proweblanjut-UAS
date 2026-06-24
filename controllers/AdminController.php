<?php

// ============================================================
// AdminController.php — Panel Manajemen Produk Kopi
// ⚠️  DILINDUNGI: Hanya bisa diakses role 'admin'
// ============================================================

class AdminController extends Controller {

    // ─── POS SATPAM: Dipanggil otomatis saat controller diload ──
    public function __construct() {
        // Cek manual di sini karena __construct() dijalankan sebelum session siap
        // di beberapa skenario — double guard untuk keamanan penuh
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }

    // ── INDEX: Tampilkan daftar semua produk kopi ─────────────
    public function index() {
        require_once __DIR__ . '/../models/Product.php';
        $data['judul'] = 'Manajemen Produk — Admin KopSkuy';
        $data['kopi']  = (new Product())->getAllProducts();
        $data['flash'] = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        $this->view('layouts/header', $data);
        $this->view('admin/products/index', $data);
        $this->view('layouts/footer');
    }

    // ── CREATE: Tampilkan form tambah produk ──────────────────
    public function create() {
        $data['judul'] = 'Tambah Produk — Admin KopSkuy';
        $data['error'] = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $this->view('layouts/header', $data);
        $this->view('admin/products/create', $data);
        $this->view('layouts/footer');
    }

    // ── STORE: Proses simpan produk baru (POST) ───────────────
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/admin/create');
            exit;
        }

        $nama_kopi = trim($_POST['nama_kopi'] ?? '');
        $harga     = $_POST['harga'] ?? 0;
        $stok      = $_POST['stok'] ?? 0;
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        // Validasi dasar
        if (empty($nama_kopi) || empty($harga) || empty($deskripsi)) {
            $_SESSION['flash_error'] = 'Semua kolom wajib diisi!';
            header('Location: ' . BASEURL . '/admin/create');
            exit;
        }

        // ─── 🛡️ SECURE UPLOAD GAMBAR (CPMK 5) ─────────────────
        $nama_gambar = 'default.jpg'; // Fallback jika tidak upload

        if (!empty($_FILES['gambar']['name'])) {
            $file_name    = $_FILES['gambar']['name'];
            $tmp_name     = $_FILES['gambar']['tmp_name'];
            $file_size    = $_FILES['gambar']['size'];
            $ext          = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Whitelist ekstensi yang diizinkan
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (!in_array($ext, $allowed_ext)) {
                $_SESSION['flash_error'] = 'Format gambar tidak valid! Gunakan JPG, PNG, atau WEBP.';
                header('Location: ' . BASEURL . '/admin/create');
                exit;
            }

            // Batasi ukuran file maks 2MB
            if ($file_size > 2 * 1024 * 1024) {
                $_SESSION['flash_error'] = 'Ukuran gambar maksimal 2MB!';
                header('Location: ' . BASEURL . '/admin/create');
                exit;
            }

            // Enkripsi nama file: kombinasi hash unik + timestamp → anti-overwrite
            $nama_gambar   = md5(uniqid() . time()) . '.' . $ext;
            $tujuan_simpan = __DIR__ . '/../img/' . $nama_gambar;

            if (!move_uploaded_file($tmp_name, $tujuan_simpan)) {
                $_SESSION['flash_error'] = 'Gagal mengunggah gambar. Pastikan folder img/ bisa ditulis.';
                header('Location: ' . BASEURL . '/admin/create');
                exit;
            }
        }

        // ─── INSERT ke Database ──────────────────────────────
        $db   = new Database();
        $stmt = $db->prepare(
            "INSERT INTO products (nama_kopi, deskripsi, harga, stok, gambar) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$nama_kopi, $deskripsi, $harga, $stok, $nama_gambar]);

        $_SESSION['flash_success'] = "Produk '{$nama_kopi}' berhasil ditambahkan ke gudang!";
        header('Location: ' . BASEURL . '/admin');
        exit;
    }

    // ── EDIT: Tampilkan form edit produk ──────────────────────
    public function edit($id = null) {
        if (!$id) {
            header('Location: ' . BASEURL . '/admin');
            exit;
        }

        require_once __DIR__ . '/../models/Product.php';
        $produk = (new Product())->getProductById($id);

        if (!$produk) {
            $_SESSION['flash_error'] = 'Produk tidak ditemukan!';
            header('Location: ' . BASEURL . '/admin');
            exit;
        }

        $data['judul']  = 'Edit Produk — Admin KopSkuy';
        $data['produk'] = $produk;
        $data['error']  = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $this->view('layouts/header', $data);
        $this->view('admin/products/edit', $data);
        $this->view('layouts/footer');
    }

    // ── UPDATE: Proses simpan perubahan produk (POST) ─────────
    public function update($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            header('Location: ' . BASEURL . '/admin');
            exit;
        }

        $nama_kopi = trim($_POST['nama_kopi'] ?? '');
        $harga     = $_POST['harga'] ?? 0;
        $stok      = $_POST['stok'] ?? 0;
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $gambar_lama = $_POST['gambar_lama'] ?? 'default.jpg';

        // Cek apakah ada upload gambar baru
        $nama_gambar = $gambar_lama; // Default: pakai gambar lama

        if (!empty($_FILES['gambar']['name'])) {
            $file_name = $_FILES['gambar']['name'];
            $tmp_name  = $_FILES['gambar']['tmp_name'];
            $file_size = $_FILES['gambar']['size'];
            $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (!in_array($ext, $allowed_ext)) {
                $_SESSION['flash_error'] = 'Format gambar tidak valid!';
                header('Location: ' . BASEURL . '/admin/edit/' . $id);
                exit;
            }

            if ($file_size > 2 * 1024 * 1024) {
                $_SESSION['flash_error'] = 'Ukuran gambar maksimal 2MB!';
                header('Location: ' . BASEURL . '/admin/edit/' . $id);
                exit;
            }

            // Hapus gambar lama jika bukan default
            if ($gambar_lama !== 'default.jpg') {
                $path_lama = __DIR__ . '/../img/' . $gambar_lama;
                if (file_exists($path_lama)) {
                    unlink($path_lama);
                }
            }

            // Upload gambar baru dengan nama unik
            $nama_gambar   = md5(uniqid() . time()) . '.' . $ext;
            $tujuan_simpan = __DIR__ . '/../img/' . $nama_gambar;
            move_uploaded_file($tmp_name, $tujuan_simpan);
        }

        // UPDATE database
        $db   = new Database();
        $stmt = $db->prepare(
            "UPDATE products SET nama_kopi=?, deskripsi=?, harga=?, stok=?, gambar=? WHERE id=?"
        );
        $stmt->execute([$nama_kopi, $deskripsi, $harga, $stok, $nama_gambar, $id]);

        $_SESSION['flash_success'] = "Produk '{$nama_kopi}' berhasil diperbarui!";
        header('Location: ' . BASEURL . '/admin');
        exit;
    }

    // ── DELETE: Hapus produk + file gambarnya ─────────────────
    public function delete($id = null) {
        if (!$id) {
            header('Location: ' . BASEURL . '/admin');
            exit;
        }

        $db   = new Database();
        // Ambil data gambar dulu sebelum dihapus
        $stmt = $db->prepare("SELECT gambar, nama_kopi FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $produk = $stmt->fetch();

        if ($produk) {
            // Hapus file gambar dari server (kecuali default.jpg)
            if ($produk['gambar'] !== 'default.jpg') {
                $path_gambar = __DIR__ . '/../img/' . $produk['gambar'];
                if (file_exists($path_gambar)) {
                    unlink($path_gambar);
                }
            }

            // Hapus dari database
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['flash_success'] = "Produk '{$produk['nama_kopi']}' berhasil dihapus.";
        }

        header('Location: ' . BASEURL . '/admin');
        exit;
    }
}
