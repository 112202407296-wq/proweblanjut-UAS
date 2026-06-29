<?php

// ============================================================
// HistoryController.php — Riwayat Pesanan Saya
// Routes:
//   GET /history → index() : Tampilkan semua order milik user yang login
// ============================================================

class HistoryController extends Controller {

    // Guard: Harus login untuk melihat riwayat pesanan
    public function __construct() {
        $this->guardLogin();
    }

    // ─────────────────────────────────────────────────────────
    // INDEX: Tampilkan riwayat pesanan user yang sedang login
    // URL: GET /history
    // ─────────────────────────────────────────────────────────
    public function index() {
        $user_id = (int)$_SESSION['user_id'];
        $db      = new Database();

        // ── Ambil semua pesanan milik user ini, terbaru dulu ──
        $stmt = $db->prepare(
            "SELECT 
                o.id,
                o.order_id_midtrans,
                o.total_harga,
                o.status_pembayaran,
                o.created_at,
                COUNT(oi.id) AS jumlah_item
             FROM orders o
             LEFT JOIN order_items oi ON oi.order_id = o.id
             WHERE o.user_id = :user_id
             GROUP BY o.id
             ORDER BY o.created_at DESC"
        );
        $stmt->execute([':user_id' => $user_id]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data['judul']       = 'Riwayat Pesanan Saya — KopSkuy';
        $data['orders']      = $orders;
        $data['flash']       = $_SESSION['flash_success'] ?? null;
        $data['flash_error'] = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $this->view('layouts/header', $data);
        $this->view('history/index', $data);
        $this->view('layouts/footer');
    }

    // ─────────────────────────────────────────────────────────
    // DETAIL: Tampilkan detail item satu pesanan
    // URL: GET /history/detail/{order_db_id}
    // ─────────────────────────────────────────────────────────
    public function detail($id = null) {
        if (!$id) {
            header('Location: ' . BASEURL . '/history');
            exit;
        }

        $user_id = (int)$_SESSION['user_id'];
        $db      = new Database();

        // Ambil header order (pastikan milik user ini)
        $stmt = $db->prepare(
            "SELECT * FROM orders WHERE id = :id AND user_id = :user_id"
        );
        $stmt->execute([':id' => $id, ':user_id' => $user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            $_SESSION['flash_error'] = 'Pesanan tidak ditemukan.';
            header('Location: ' . BASEURL . '/history');
            exit;
        }

        // Ambil detail item pesanan
        $stmt_items = $db->prepare(
            "SELECT oi.qty, oi.harga_satuan, p.nama_kopi, p.gambar
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = :order_id"
        );
        $stmt_items->execute([':order_id' => $order['id']]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        $data['judul'] = 'Detail Pesanan ' . htmlspecialchars($order['order_id_midtrans']) . ' — KopSkuy';
        $data['order'] = $order;
        $data['items'] = $items;

        $this->view('layouts/header', $data);
        $this->view('history/detail', $data);
        $this->view('layouts/footer');
    }
}
