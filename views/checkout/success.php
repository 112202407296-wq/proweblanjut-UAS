<?php
// ============================================================
// views/checkout/success.php — Halaman Pembayaran Berhasil
// Data dari CheckoutController::success():
//   $data['order_id'] : string ID pesanan
// ============================================================
?>

<style>
    .success-wrapper {
        max-width: 520px;
        margin: 3rem auto;
    }
    .success-icon {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, #28a745, #20c997);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes popIn {
        0%   { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    .order-id-badge {
        background: rgba(162, 123, 92, 0.1);
        border: 1px dashed var(--color-coffee);
        border-radius: 10px;
        padding: 10px 20px;
        font-family: monospace;
        font-size: 0.95rem;
        letter-spacing: 1px;
        color: var(--color-dark);
    }
</style>

<div class="success-wrapper">
    <div class="card shadow-lg border-0 text-center" style="border-radius: 24px; overflow: hidden;">
        <!-- Header hijau -->
        <div style="background: linear-gradient(135deg, #28a745, #20c997); padding: 2.5rem 2rem 3rem;">
            <div class="success-icon">
                <i class="fas fa-check fa-2x text-white"></i>
            </div>
            <h3 class="text-white fw-bold mb-1">Pembayaran Berhasil!</h3>
            <p class="text-white opacity-75 mb-0">Terima kasih telah memesan di KopSkuy ☕</p>
        </div>

        <div class="card-body p-4">
            <p class="text-muted mb-3">Pesanan kamu dengan ID berikut sedang kami proses:</p>

            <div class="order-id-badge mb-4">
                <?= htmlspecialchars($data['order_id'] ?? 'N/A'); ?>
            </div>

            <p class="text-muted small mb-4">
                <i class="fas fa-envelope me-1"></i>
                Konfirmasi pesanan akan dikirim ke email kamu.
                Cek halaman <strong>Riwayat Pesanan</strong> untuk melihat status terkini.
            </p>

            <div class="d-grid gap-2">
                <a href="<?= BASEURL; ?>/history" class="btn btn-coffee btn-lg rounded-pill fw-bold shadow-sm">
                    <i class="fas fa-list-alt me-2"></i> Lihat Riwayat Pesanan
                </a>
                <a href="<?= BASEURL; ?>" class="btn btn-outline-secondary rounded-pill fw-medium">
                    <i class="fas fa-store me-2"></i> Kembali ke Katalog
                </a>
            </div>
        </div>
    </div>
</div>
