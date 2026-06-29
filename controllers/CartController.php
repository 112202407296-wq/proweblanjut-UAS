<?php

// ============================================================
// CartController.php — Keranjang Belanja Berbasis $_SESSION
// Routes:
//   GET  /cart          → index()       : Tampilkan isi keranjang
//   POST /cart/add      → add()         : Tambah item ke keranjang
//   POST /cart/remove   → remove()      : Hapus satu item dari keranjang
//   POST /cart/update   → update()      : Update kuantitas item
//   GET  /cart/clear    → clear()       : Kosongkan seluruh keranjang
// ============================================================

class CartController extends Controller {

    // ── Helper: Hitung total gross amount dari isi keranjang ──
    private function calculateTotal(): int {
        $total = 0;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $total += (int)$item['harga'] * (int)$item['qty'];
            }
        }
        return $total;
    }

    // ── Helper: Hitung total jumlah item (untuk badge navbar) ─
    private function calculateItemCount(): int {
        $count = 0;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $count += (int)$item['qty'];
            }
        }
        return $count;
    }

    // ─────────────────────────────────────────────────────────
    // INDEX: Tampilkan halaman keranjang
    // URL: GET /cart
    // ─────────────────────────────────────────────────────────
    public function index() {
        $data['judul']       = 'Keranjang Belanja — KopSkuy';
        $data['cart']        = $_SESSION['cart'] ?? [];
        $data['total']       = $this->calculateTotal();
        $data['item_count']  = $this->calculateItemCount();
        $data['flash']       = $_SESSION['flash_success'] ?? null;
        $data['flash_error'] = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->view('layouts/header', $data);
        $this->view('cart/index', $data);
        $this->view('layouts/footer');
    }

    // ─────────────────────────────────────────────────────────
    // ADD: Tambah produk ke keranjang
    // URL: POST /cart/add
    // POST body: product_id (int), qty (int, opsional default 1)
    // ─────────────────────────────────────────────────────────
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL);
            exit;
        }

        $product_id = (int)($_POST['product_id'] ?? 0);
        $qty        = max(1, (int)($_POST['qty'] ?? 1));

        if ($product_id <= 0) {
            $_SESSION['flash_error'] = 'Produk tidak valid.';
            header('Location: ' . BASEURL);
            exit;
        }

        // Ambil data produk dari DB untuk mendapatkan harga & nama yang akurat
        require_once __DIR__ . '/../models/Product.php';
        $produk = (new Product())->getProductById($product_id);

        if (!$produk) {
            $_SESSION['flash_error'] = 'Produk tidak ditemukan.';
            header('Location: ' . BASEURL);
            exit;
        }

        // Cek stok: total qty di keranjang + qty baru tidak boleh melebihi stok
        $qty_di_keranjang = $_SESSION['cart'][$product_id]['qty'] ?? 0;
        $total_qty_baru   = $qty_di_keranjang + $qty;

        if ($total_qty_baru > (int)$produk['stok']) {
            $_SESSION['flash_error'] = "Stok '{$produk['nama_kopi']}' hanya tersisa {$produk['stok']} item.";
            header('Location: ' . BASEURL . '/cart');
            exit;
        }

        // ── Inisialisasi session cart jika belum ada ──────────
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // ── Jika produk sudah ada di keranjang → tambah qty ──
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['qty'] += $qty;
        } else {
            // ── Produk baru → masukkan sebagai item baru ───────
            $_SESSION['cart'][$product_id] = [
                'product_id' => $product_id,
                'nama_kopi'  => $produk['nama_kopi'],
                'harga'      => (int)$produk['harga'], // Simpan sebagai integer (rupiah)
                'gambar'     => $produk['gambar'],
                'qty'        => $qty,
            ];
        }

        $_SESSION['flash_success'] = "'{$produk['nama_kopi']}' berhasil ditambahkan ke keranjang! ☕";
        header('Location: ' . BASEURL . '/cart');
        exit;
    }

    // ─────────────────────────────────────────────────────────
    // UPDATE: Perbarui kuantitas item di keranjang
    // URL: POST /cart/update
    // POST body: product_id (int), qty (int)
    // ─────────────────────────────────────────────────────────
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/cart');
            exit;
        }

        $product_id = (int)($_POST['product_id'] ?? 0);
        $qty        = (int)($_POST['qty'] ?? 1);

        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            if ($qty <= 0) {
                // Qty 0 atau negatif = hapus item
                unset($_SESSION['cart'][$product_id]);
                $_SESSION['flash_success'] = 'Item dihapus dari keranjang.';
            } else {
                // Validasi stok sebelum update
                require_once __DIR__ . '/../models/Product.php';
                $produk = (new Product())->getProductById($product_id);
                if ($produk && $qty > (int)$produk['stok']) {
                    $_SESSION['flash_error'] = "Stok '{$produk['nama_kopi']}' hanya {$produk['stok']} item.";
                    header('Location: ' . BASEURL . '/cart');
                    exit;
                }
                $_SESSION['cart'][$product_id]['qty'] = $qty;
                $_SESSION['flash_success'] = 'Keranjang berhasil diperbarui.';
            }
        }

        header('Location: ' . BASEURL . '/cart');
        exit;
    }

    // ─────────────────────────────────────────────────────────
    // REMOVE: Hapus satu item dari keranjang
    // URL: POST /cart/remove
    // POST body: product_id (int)
    // ─────────────────────────────────────────────────────────
    public function remove() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/cart');
            exit;
        }

        $product_id = (int)($_POST['product_id'] ?? 0);

        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            $nama = $_SESSION['cart'][$product_id]['nama_kopi'];
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['flash_success'] = "'{$nama}' dihapus dari keranjang.";
        }

        header('Location: ' . BASEURL . '/cart');
        exit;
    }

    // ─────────────────────────────────────────────────────────
    // CLEAR: Kosongkan seluruh keranjang
    // URL: GET /cart/clear
    // ─────────────────────────────────────────────────────────
    public function clear() {
        $_SESSION['cart'] = [];
        $_SESSION['flash_success'] = 'Keranjang berhasil dikosongkan.';
        header('Location: ' . BASEURL . '/cart');
        exit;
    }
}
