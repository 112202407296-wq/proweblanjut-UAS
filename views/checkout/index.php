<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-lg border-0" style="border-radius: 16px;">
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-bag fa-3x text-coffee mb-3" style="color: var(--color-coffee);"></i>
                    <h4 class="fw-bold" style="color: var(--color-darkest);">Ringkasan Pembayaran</h4>
                    <p class="text-muted">Harap periksa kembali pesanan Anda sebelum melanjutkan pembayaran.</p>
                </div>
                
                <div class="p-4 rounded-3 mb-4" style="background-color: var(--color-beige);">
                    <p class="mb-1" style="color: var(--color-dark);">Total Tagihan Anda:</p>
                    <!-- Angka tagihan dan Token ini nanti diisi oleh Controller Randi -->
                    <h2 class="text-coffee fw-bold mb-0">Rp <?= number_format($data['total_tagihan'] ?? 0, 0, ',', '.'); ?></h2>
                </div>
                
                <button id="pay-button" class="btn btn-coffee btn-lg w-100 rounded-pill py-3 fw-bold shadow">
                    <i class="fas fa-lock me-2"></i> BAYAR SEKARANG
                </button>
                <div class="mt-3 text-muted small">
                    <i class="fas fa-shield-alt me-1"></i> Pembayaran aman dan terenkripsi
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Midtrans Sandbox -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="MASUKKAN_CLIENT_KEY_SANDBOX_KALIAN_DISINI"></script>
<script>
    document.getElementById('pay-button').onclick = function(){
        // Variabel snap_token ini akan disuntikkan oleh Randi dari backend!
        snap.pay('<?= $data['snap_token'] ?? ''; ?>');
    };
</script>
