<?php

// ============================================================
// WebhookController.php — Penerima Notifikasi Midtrans
// Controller ini TIDAK memiliki View. Murni mesin pembaca JSON.
// URL: POST /webhook/midtrans (daftarkan URL ini di dashboard Midtrans)
// ============================================================

class WebhookController extends Controller {

    public function midtrans() {
        // ── 1. Tangkap payload JSON mentah dari server Midtrans ──
        $raw_json = file_get_contents('php://input');
        $notif    = json_decode($raw_json, true);

        // Jika payload kosong atau bukan JSON valid → abaikan
        if (!$notif || !isset($notif['order_id'])) {
            http_response_code(400);
            echo "Bad Request";
            exit;
        }

        $order_id_midtrans  = $notif['order_id'];
        $transaction_status = $notif['transaction_status'];
        $fraud_status       = $notif['fraud_status'] ?? null;

        // Log untuk debugging (opsional, hapus di production)
        // file_put_contents(__DIR__ . '/../midtrans_log.txt', $raw_json . PHP_EOL, FILE_APPEND);

        $db = new Database();

        // ── 2. Pembayaran Berhasil (Settlement / Capture) ─────────
        if ($transaction_status === 'settlement') {
            $stmt = $db->prepare(
                "UPDATE orders SET status_pembayaran = 'settlement' WHERE order_id_midtrans = ?"
            );
            $stmt->execute([$order_id_midtrans]);

        } elseif ($transaction_status === 'capture') {
            // Capture: khusus kartu kredit — cek fraud status
            if ($fraud_status === 'accept') {
                $stmt = $db->prepare(
                    "UPDATE orders SET status_pembayaran = 'settlement' WHERE order_id_midtrans = ?"
                );
                $stmt->execute([$order_id_midtrans]);
            } elseif ($fraud_status === 'challenge') {
                $stmt = $db->prepare(
                    "UPDATE orders SET status_pembayaran = 'pending' WHERE order_id_midtrans = ?"
                );
                $stmt->execute([$order_id_midtrans]);
            }

        // ── 3. Pembayaran Pending (menunggu) ──────────────────────
        } elseif ($transaction_status === 'pending') {
            $stmt = $db->prepare(
                "UPDATE orders SET status_pembayaran = 'pending' WHERE order_id_midtrans = ?"
            );
            $stmt->execute([$order_id_midtrans]);

        // ── 4. Pembayaran Kadaluarsa / Dibatalkan ─────────────────
        } elseif ($transaction_status === 'expire' || $transaction_status === 'cancel') {
            $stmt = $db->prepare(
                "UPDATE orders SET status_pembayaran = ? WHERE order_id_midtrans = ?"
            );
            $stmt->execute([$transaction_status, $order_id_midtrans]);
        }

        // ── 5. Konfirmasi ke Midtrans bahwa notifikasi diterima ───
        http_response_code(200);
        echo "OK";
        exit;
    }
}
