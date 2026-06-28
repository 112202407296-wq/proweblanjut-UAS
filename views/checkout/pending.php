<?php
// views/checkout/pending.php — Halaman Pembayaran Pending
// Data: $data['order_id']
?>
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8" style="margin-top: 3rem;">
        <div class="card shadow border-0 text-center" style="border-radius: 24px; overflow: hidden;">
            <div style="background: linear-gradient(135deg, #ffc107, #fd7e14); padding: 2.5rem 2rem 3rem;">
                <div style="width:80px;height:80px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="fas fa-clock fa-2x text-white"></i>
                </div>
                <h3 class="text-white fw-bold mb-1">Menunggu Pembayaran</h3>
                <p class="text-white opacity-75 mb-0">Pesanan kamu sedang diproses</p>
            </div>
            <div class="card-body p-4">
                <p class="text-muted mb-3">ID Pesanan:</p>
                <div style="background:rgba(162,123,92,0.08);border:1px dashed var(--color-coffee);border-radius:10px;padding:10px 20px;font-family:monospace;margin-bottom:1.5rem;">
                    <?= htmlspecialchars($data['order_id'] ?? 'N/A'); ?>
                </div>
                <div class="alert alert-info border-0 rounded-3 text-start">
                    <i class="fas fa-info-circle me-2"></i>
                    Selesaikan pembayaran sesuai instruksi yang dikirim ke email kamu.
                    Status akan diperbarui otomatis setelah pembayaran dikonfirmasi.
                </div>
                <div class="d-grid gap-2 mt-3">
                    <a href="<?= BASEURL; ?>/history" class="btn btn-coffee btn-lg rounded-pill fw-bold shadow-sm">
                        <i class="fas fa-list-alt me-2"></i> Pantau Riwayat Pesanan
                    </a>
                    <a href="<?= BASEURL; ?>" class="btn btn-outline-secondary rounded-pill fw-medium">
                        <i class="fas fa-store me-2"></i> Kembali ke Katalog
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
