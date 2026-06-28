<?php

// ============================================================
// CheckoutController.php — Integrasi Midtrans Snap
// Routes:
//   GET  /checkout          → index()   : Tampilkan halaman checkout
//   POST /checkout/process  → process() : Generate/reuse Snap Token & simpan ke DB
//   GET  /checkout/success  → success() : Hapus cart, tampilkan sukses
//   GET  /checkout/pending  → pending() : Tampilkan halaman pending
// ============================================================

class CheckoutController extends Controller {

    public function __construct() {
        $this->guardLogin();
    }

    // ─────────────────────────────────────────────────────────
    // INDEX: Tampilkan ringkasan pesanan & tombol bayar
    // ─────────────────────────────────────────────────────────
    public function index() {
        if (empty($_SESSION['cart'])) {
            $_SESSION['flash_error'] = 'Keranjang kamu masih kosong. Pilih kopi dulu!';
            header('Location: ' . BASEURL);
            exit;
        }

        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += (int)$item['harga'] * (int)$item['qty'];
        }

        // ── BUG FIX #1: Reset snap_token lama saat halaman checkout dibuka ──
        // Ini memaksa token baru dibuat, menghindari reuse token expired.
        // Token hanya di-cache DALAM satu sesi "klik bayar" (di process()).
        unset($_SESSION['snap_token'], $_SESSION['pending_order_id']);

        $data['judul']         = 'Checkout — KopSkuy';
        $data['cart']          = $_SESSION['cart'];
        $data['total_tagihan'] = $total;
        $data['flash_error']   = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $this->view('layouts/header', $data);
        $this->view('checkout/index', $data);
        $this->view('layouts/footer');
    }

    // ─────────────────────────────────────────────────────────
    // PROCESS: Generate Snap Token dari Midtrans & INSERT ke DB
    // BUG FIX #1: Jika snap_token sudah ada di sesi (dalam satu
    // sesi checkout yang sama), REUSE token itu. Jangan buat order baru.
    // ─────────────────────────────────────────────────────────
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            exit;
        }

        if (empty($_SESSION['cart'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Keranjang kosong.']);
            exit;
        }

        header('Content-Type: application/json');

        // ── BUG FIX #1: Reuse snap_token yang sudah ada ──────
        // Jika user klik "Bayar", tutup popup, lalu klik lagi →
        // jangan buat order baru. Pakai token yang sama.
        if (!empty($_SESSION['snap_token']) && !empty($_SESSION['pending_order_id'])) {
            echo json_encode([
                'snap_token' => $_SESSION['snap_token'],
                'order_id'   => $_SESSION['pending_order_id'],
            ]);
            exit;
        }

        // ── 1. Load Midtrans SDK ──────────────────────────────
        $midtrans_sdk = __DIR__ . '/../vendor/midtrans/midtrans-php/Midtrans.php';
        if (!file_exists($midtrans_sdk)) {
            http_response_code(500);
            echo json_encode(['error' => 'Midtrans SDK tidak ditemukan.']);
            exit;
        }
        require_once $midtrans_sdk;

        // ── 2. Baca .env ──────────────────────────────────────
        $env_path = __DIR__ . '/../.env';
        $env = [];
        if (file_exists($env_path)) {
            $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
                [$name, $value] = explode('=', $line, 2);
                $env[trim($name)] = trim($value);
            }
        }

        // ── 3. Konfigurasi Midtrans ───────────────────────────
        \Midtrans\Config::$serverKey    = $env['MIDTRANS_SERVER_KEY'] ?? '';
        \Midtrans\Config::$clientKey    = $env['MIDTRANS_CLIENT_KEY'] ?? '';
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        if (empty(\Midtrans\Config::$serverKey)) {
            http_response_code(500);
            echo json_encode(['error' => 'MIDTRANS_SERVER_KEY belum diisi di .env!']);
            exit;
        }

        // ── 4. Mapping cart → item_details ────────────────────
        $item_details = [];
        $gross_amount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $gross_amount += (int)$item['harga'] * (int)$item['qty'];
            $item_details[] = [
                'id'       => (string)$item['product_id'],
                'price'    => (int)$item['harga'],
                'quantity' => (int)$item['qty'],
                'name'     => substr($item['nama_kopi'], 0, 50),
            ];
        }

        // ── 5. Buat order_id unik ─────────────────────────────
        $user_id  = (int)$_SESSION['user_id'];
        $order_id = 'ORD-' . $user_id . '-' . time() . '-' . rand(1000, 9999);

        // ── 6. Parameter Snap ─────────────────────────────────
        $params = [
            'transaction_details' => [
                'order_id'     => $order_id,
                'gross_amount' => $gross_amount,
            ],
            'item_details'    => $item_details,
            'customer_details' => [
                'first_name' => $_SESSION['username'] ?? 'Customer',
                'email'      => $_SESSION['email'] ?? 'customer@kopskuy.com',
            ],
            // ── BUG FIX #3 (Localhost Webhook): ──────────────
            // Definisikan finish_redirect_url & unfinish_redirect_url
            // Ini menggantikan webhook untuk environment localhost.
            // Midtrans akan redirect browser ke URL ini setelah bayar.
            'callbacks' => [
                'finish' => BASEURL . '/checkout/finish',
            ],
        ];

        // ── 7. Request Snap Token ─────────────────────────────
        try {
            $snap_token = \Midtrans\Snap::getSnapToken($params);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Midtrans Error: ' . $e->getMessage()]);
            exit;
        }

        // ── 8. INSERT ke DB (status awal: 'pending') ──────────
        try {
            $db   = new Database();
            $stmt = $db->prepare(
                "INSERT INTO orders
                    (user_id, order_id_midtrans, total_harga, snap_token, status_pembayaran)
                 VALUES
                    (:user_id, :order_id, :total, :snap_token, 'pending')"
            );
            $stmt->execute([
                ':user_id'    => $user_id,
                ':order_id'   => $order_id,
                ':total'      => $gross_amount,
                ':snap_token' => $snap_token,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database Error: ' . $e->getMessage()]);
            exit;
        }

        // ── 9. Cache token di session (untuk reuse) ───────────
        $_SESSION['snap_token']       = $snap_token;
        $_SESSION['pending_order_id'] = $order_id;

        echo json_encode([
            'snap_token' => $snap_token,
            'order_id'   => $order_id,
        ]);
        exit;
    }

    // ─────────────────────────────────────────────────────────
    // FINISH: Endpoint redirect dari Midtrans setelah bayar
    // BUG FIX #3: Ini pengganti webhook untuk localhost.
    // Midtrans redirect browser ke sini setelah popup ditutup.
    // URL: GET /checkout/finish?order_id=ORD-...&transaction_status=settlement
    // ─────────────────────────────────────────────────────────
    public function finish() {
        $order_id_midtrans  = $_GET['order_id'] ?? '';
        $transaction_status = $_GET['transaction_status'] ?? '';
        $fraud_status       = $_GET['fraud_status'] ?? 'accept';

        if (!empty($order_id_midtrans)) {
            // Tentukan status baru berdasarkan parameter GET dari Midtrans
            $new_status = 'pending'; // default

            if ($transaction_status === 'settlement') {
                $new_status = 'settlement';
            } elseif ($transaction_status === 'capture' && $fraud_status === 'accept') {
                $new_status = 'settlement';
            } elseif ($transaction_status === 'expire') {
                $new_status = 'expire';
            } elseif ($transaction_status === 'cancel' || $transaction_status === 'deny') {
                $new_status = 'cancel';
            }

            // Update status di DB
            try {
                $db   = new Database();
                $stmt = $db->prepare(
                    "UPDATE orders SET status_pembayaran = :status WHERE order_id_midtrans = :order_id"
                );
                $stmt->execute([
                    ':status'   => $new_status,
                    ':order_id' => $order_id_midtrans,
                ]);
            } catch (PDOException $e) {
                // Gagal update DB — tetap lanjut ke halaman sukses/pending
            }

            // Redirect berdasarkan status
            if ($new_status === 'settlement') {
                $_SESSION['cart'] = [];
                unset($_SESSION['snap_token']);
                $data['judul']    = 'Pembayaran Berhasil — KopSkuy';
                $data['order_id'] = $_SESSION['pending_order_id'] ?? $order_id_midtrans;
                unset($_SESSION['pending_order_id']);

                $this->view('layouts/header', $data);
                $this->view('checkout/success', $data);
                $this->view('layouts/footer');
                return;
            } else {
                // Pending / cancel / expire → arahkan ke riwayat
                header('Location: ' . BASEURL . '/history');
                exit;
            }
        }

        // Fallback jika tidak ada parameter
        header('Location: ' . BASEURL . '/history');
        exit;
    }

    // ─────────────────────────────────────────────────────────
    // SUCCESS: Dipanggil oleh onSuccess callback JS Snap
    // ─────────────────────────────────────────────────────────
    public function success() {
        $order_id = $_SESSION['pending_order_id'] ?? ($_GET['order_id'] ?? 'N/A');

        // Update status di DB jika ada order_id dari GET (callback Midtrans)
        if (!empty($_GET['order_id'])) {
            try {
                $db   = new Database();
                $stmt = $db->prepare(
                    "UPDATE orders SET status_pembayaran = 'settlement'
                     WHERE order_id_midtrans = :order_id AND user_id = :user_id"
                );
                $stmt->execute([
                    ':order_id' => $_GET['order_id'],
                    ':user_id'  => (int)$_SESSION['user_id'],
                ]);
            } catch (PDOException $e) {
                // Silent fail — halaman tetap tampil
            }
        }

        // Kosongkan keranjang
        $_SESSION['cart'] = [];
        unset($_SESSION['snap_token']);

        $data['judul']    = 'Pembayaran Berhasil — KopSkuy';
        $data['order_id'] = $order_id;
        unset($_SESSION['pending_order_id']);

        $this->view('layouts/header', $data);
        $this->view('checkout/success', $data);
        $this->view('layouts/footer');
    }

    // ─────────────────────────────────────────────────────────
    // PENDING: Halaman untuk pembayaran yang menunggu
    // ─────────────────────────────────────────────────────────
    public function pending() {
        $data['judul']    = 'Menunggu Pembayaran — KopSkuy';
        $data['order_id'] = $_SESSION['pending_order_id'] ?? 'N/A';

        $this->view('layouts/header', $data);
        $this->view('checkout/pending', $data);
        $this->view('layouts/footer');
    }
}
