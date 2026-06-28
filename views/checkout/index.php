<?php
// ============================================================
// views/checkout/index.php — Halaman Checkout & Pembayaran
// Data dari CheckoutController::index():
//   $data['cart']          : array item keranjang
//   $data['total_tagihan'] : int total harga
//   $data['flash_error']   : string error (opsional)
// ============================================================
?>

<style>
    .checkout-card {
        border-radius: 20px;
        overflow: hidden;
    }
    .checkout-header {
        background: linear-gradient(135deg, var(--color-darkest), var(--color-dark));
        color: white;
        padding: 2rem;
    }
    .item-list-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(162, 123, 92, 0.15);
    }
    .item-list-row:last-child {
        border-bottom: none;
    }
    .pay-button {
        background: linear-gradient(135deg, var(--color-coffee), #8c6a4f);
        color: white;
        border: none;
        padding: 14px 20px;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 50px;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(162, 123, 92, 0.4);
    }
    .pay-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(162, 123, 92, 0.5);
        color: white;
    }
    .pay-button:disabled {
        opacity: 0.7;
        transform: none;
        cursor: not-allowed;
    }
    .security-badge {
        background: rgba(162, 123, 92, 0.08);
        border: 1px solid rgba(162, 123, 92, 0.2);
        border-radius: 12px;
        padding: 12px 16px;
    }
    .spinner-pay {
        display: none;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">

        <?php if (!empty($data['flash_error'])): ?>
        <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($data['flash_error']); ?>
        </div>
        <?php endif; ?>

        <div class="card checkout-card shadow-lg border-0">
            <!-- Header -->
            <div class="checkout-header">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-shopping-bag fa-2x"></i>
                    <div>
                        <h4 class="fw-bold mb-0">Konfirmasi Pesanan</h4>
                        <p class="mb-0 opacity-75 small">Periksa pesananmu sebelum membayar</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Daftar Item -->
                <h6 class="fw-bold mb-3 text-muted text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">
                    Rincian Pesanan
                </h6>

                <?php foreach ($data['cart'] as $item): ?>
                <div class="item-list-row">
                    <div class="d-flex align-items-center gap-3">
                        <img
                            src="<?= BASEURL; ?>/img/<?= htmlspecialchars($item['gambar']); ?>"
                            alt="<?= htmlspecialchars($item['nama_kopi']); ?>"
                            style="width:48px; height:48px; object-fit:cover; border-radius:8px;"
                            onerror="this.src='<?= BASEURL; ?>/img/default.jpg'"
                        >
                        <div>
                            <div class="fw-semibold" style="color: var(--color-darkest); font-size: 0.9rem;">
                                <?= htmlspecialchars($item['nama_kopi']); ?>
                            </div>
                            <small class="text-muted">
                                <?= $item['qty']; ?> × Rp <?= number_format($item['harga'], 0, ',', '.'); ?>
                            </small>
                        </div>
                    </div>
                    <span class="fw-bold" style="color: var(--color-coffee);">
                        Rp <?= number_format((int)$item['harga'] * (int)$item['qty'], 0, ',', '.'); ?>
                    </span>
                </div>
                <?php endforeach; ?>

                <!-- Total -->
                <div class="mt-4 p-4 rounded-3 text-center" style="background: linear-gradient(135deg, var(--color-beige), #e8e2d6);">
                    <p class="mb-1 text-muted fw-medium">Total Tagihan</p>
                    <h2 class="fw-bold mb-0" style="color: var(--color-coffee); font-size: 2rem;">
                        Rp <?= number_format($data['total_tagihan'] ?? 0, 0, ',', '.'); ?>
                    </h2>
                </div>

                <!-- Tombol Bayar -->
                <div class="mt-4">
                    <button
                        id="pay-button"
                        class="pay-button"
                        onclick="processBayar()"
                    >
                        <span id="pay-text">
                            <i class="fas fa-lock me-2"></i> BAYAR SEKARANG
                        </span>
                        <span class="spinner-pay" id="pay-spinner">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Memproses Pembayaran...
                        </span>
                    </button>
                </div>

                <!-- Security Badge -->
                <div class="security-badge text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1" style="color: var(--color-coffee);"></i>
                        Pembayaran aman & terenkripsi melalui <strong>Midtrans</strong>
                    </small>
                    <br>
                    <small class="text-muted">
                        <i class="fas fa-tag me-1"></i> Mode Sandbox (Testing)
                    </small>
                </div>

                <!-- Kembali ke Keranjang -->
                <div class="text-center mt-3">
                    <a href="<?= BASEURL; ?>/cart" class="text-muted small text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Script Midtrans Snap (Sandbox) ─────────────────────── -->
<script
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="Mid-client-419knqG4zTjaCZZT">
</script>

<script>
    function resetButton() {
        const btn     = document.getElementById('pay-button');
        const payText = document.getElementById('pay-text');
        const spinner = document.getElementById('pay-spinner');
        btn.disabled          = false;
        payText.style.display = 'inline';
        spinner.style.display = 'none';
    }

    function processBayar() {
        const btn     = document.getElementById('pay-button');
        const payText = document.getElementById('pay-text');
        const spinner = document.getElementById('pay-spinner');

        btn.disabled          = true;
        payText.style.display = 'none';
        spinner.style.display = 'inline';

        // ── Minta Snap Token dari backend ─────────────────────
        fetch('<?= BASEURL; ?>/checkout/process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: '_token=checkout'
        })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert('❌ Error: ' + data.error);
                resetButton();
                return;
            }

            // ── Buka popup Midtrans Snap ──────────────────────
            snap.pay(data.snap_token, {

                onSuccess: function(result) {
                    window.location.href = '<?= BASEURL; ?>/checkout/success'
                        + '?order_id=' + encodeURIComponent(result.order_id)
                        + '&transaction_status=' + encodeURIComponent(result.transaction_status);
                },

                onPending: function(result) {
                    const card = document.querySelector('.checkout-card .card-body');
                    card.innerHTML = `
                        <div class="text-center py-4">
                            <div style="font-size:3.5rem;">⏳</div>
                            <h4 class="fw-bold mt-3" style="color: var(--color-darkest);">Menunggu Pembayaran</h4>
                            <p class="text-muted">Pesanan <strong>${result.order_id}</strong> sedang menunggu pembayaran.</p>
                            <div class="alert alert-warning border-0 rounded-3 text-start mt-3">
                                <strong><i class="fas fa-info-circle me-2"></i>Instruksi:</strong><br>
                                Selesaikan pembayaran sesuai metode yang dipilih.<br>
                                Status pesanan akan otomatis diperbarui setelah pembayaran dikonfirmasi.
                            </div>
                            <a href="<?= BASEURL; ?>/history" class="btn btn-coffee rounded-pill px-5 mt-3 fw-bold shadow-sm">
                                <i class="fas fa-list-alt me-2"></i> Lihat Riwayat Pesanan
                            </a>
                        </div>
                    `;
                },

                onError: function(result) {
                    alert('❌ Pembayaran gagal: ' + (result.status_message || 'Terjadi kesalahan.'));
                    resetButton();
                },

                onClose: function() {
                    resetButton();
                }
            });
        })
        .catch(err => {
            alert('❌ Gagal terhubung ke server. Periksa console untuk detail.');
            console.error('Checkout fetch error:', err);
            resetButton();
        });
    }
</script>
