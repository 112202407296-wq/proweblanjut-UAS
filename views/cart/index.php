<?php
// ============================================================
// views/cart/index.php — Halaman Keranjang Belanja
// Data yang tersedia dari CartController::index():
//   $data['cart']        : array item keranjang
//   $data['total']       : int total harga (rupiah)
//   $data['flash']       : string pesan sukses (opsional)
//   $data['flash_error'] : string pesan error (opsional)
// ============================================================
?>

<style>
    .cart-item-row {
        transition: background-color 0.2s ease;
    }
    .cart-item-row:hover {
        background-color: rgba(162, 123, 92, 0.05);
    }
    .qty-input {
        width: 70px;
        text-align: center;
        border: 1px solid var(--color-beige);
        border-radius: 8px;
        padding: 6px;
        font-weight: 600;
        color: var(--color-darkest);
    }
    .qty-input:focus {
        outline: none;
        border-color: var(--color-coffee);
        box-shadow: 0 0 0 3px rgba(162, 123, 92, 0.15);
    }
    .badge-status {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .table-cart th {
        background-color: var(--color-darkest);
        color: var(--color-white);
        border: none;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .table-cart td {
        vertical-align: middle;
        border-color: rgba(162, 123, 92, 0.2);
    }
    .product-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid var(--color-beige);
    }
    .summary-card {
        position: sticky;
        top: 20px;
    }
    .btn-remove {
        color: #dc3545;
        background: none;
        border: none;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background-color 0.2s ease;
    }
    .btn-remove:hover {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .empty-cart-icon {
        font-size: 5rem;
        color: var(--color-beige);
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
    }
</style>

<div class="row g-4">
    <div class="col-12">
        <h1 class="fw-bold mb-1" style="color: var(--color-darkest);">
            <i class="fas fa-shopping-cart me-2" style="color: var(--color-coffee);"></i>
            Keranjang Belanja
        </h1>
        <p class="text-muted">Periksa pilihan kopi kamu sebelum checkout.</p>
    </div>

    <!-- Flash Messages -->
    <?php if (!empty($data['flash'])): ?>
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($data['flash']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($data['flash_error'])): ?>
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($data['flash_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($data['cart'])): ?>
    <!-- ── KERANJANG KOSONG ───────────────────────────────── -->
        <div class="col-12">
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body">
                    <div class="empty-cart-icon mb-4">☕</div>
                    <h3 class="fw-bold mb-2" style="color: var(--color-darkest);">Keranjangmu masih kosong</h3>
                    <p class="text-muted mb-4">Yuk, temukan kopi favoritmu dan tambahkan ke sini!</p>
                    <a href="<?= BASEURL; ?>" class="btn btn-coffee btn-lg rounded-pill px-5 shadow-sm">
                        <i class="fas fa-store me-2"></i> Lihat Katalog Kopi
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
    <!-- ── ISI KERANJANG ─────────────────────────────────────  -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <!-- Tombol kosongkan semua -->
                    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                        <span class="fw-semibold" style="color: var(--color-dark);">
                            <?= count($data['cart']); ?> produk dalam keranjang
                        </span>
                        <a href="<?= BASEURL; ?>/cart/clear"
                           class="text-danger small fw-medium text-decoration-none"
                           onclick="return confirm('Yakin ingin mengosongkan semua keranjang?')">
                            <i class="fas fa-trash-alt me-1"></i> Kosongkan Semua
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-cart mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4" style="border-radius: 0;">Produk</th>
                                    <th class="text-center">Harga Satuan</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Subtotal</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['cart'] as $product_id => $item): ?>
                                <tr class="cart-item-row">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <img
                                                src="<?= BASEURL; ?>/img/<?= htmlspecialchars($item['gambar']); ?>"
                                                alt="<?= htmlspecialchars($item['nama_kopi']); ?>"
                                                class="product-thumb"
                                                onerror="this.src='<?= BASEURL; ?>/img/default.jpg'"
                                            >
                                            <div>
                                                <div class="fw-semibold" style="color: var(--color-darkest);">
                                                    <?= htmlspecialchars($item['nama_kopi']); ?>
                                                </div>
                                                <small class="text-muted">ID #<?= $product_id; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-medium" style="color: var(--color-coffee);">
                                        Rp <?= number_format($item['harga'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="text-center">
                                        <!-- Form update qty -->
                                        <form method="POST" action="<?= BASEURL; ?>/cart/update"
                                              class="d-inline-flex align-items-center gap-1">
                                            <input type="hidden" name="product_id" value="<?= $product_id; ?>">
                                            <input
                                                type="number"
                                                name="qty"
                                                value="<?= (int)$item['qty']; ?>"
                                                min="1"
                                                max="99"
                                                class="qty-input"
                                                id="qty-<?= $product_id; ?>"
                                            >
                                            <button type="submit" class="btn btn-sm btn-outline-secondary ms-1 rounded-2"
                                                    title="Perbarui">
                                                <i class="fas fa-sync-alt" style="font-size: 0.7rem;"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center fw-bold" style="color: var(--color-darkest);">
                                        Rp <?= number_format((int)$item['harga'] * (int)$item['qty'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <!-- Form hapus item -->
                                        <form method="POST" action="<?= BASEURL; ?>/cart/remove"
                                              onsubmit="return confirm('Hapus item ini dari keranjang?')">
                                            <input type="hidden" name="product_id" value="<?= $product_id; ?>">
                                            <button type="submit" class="btn-remove" title="Hapus">
                                                <i class="fas fa-times-circle fa-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── RINGKASAN TOTAL ──────────────────────────────── -->
        <div class="col-lg-4">
            <div class="card shadow border-0 summary-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4" style="color: var(--color-darkest);">
                        <i class="fas fa-receipt me-2" style="color: var(--color-coffee);"></i>
                        Ringkasan Pesanan
                    </h5>

                    <?php
                    $subtotal = 0;
                    foreach ($data['cart'] as $item) {
                        $subtotal += (int)$item['harga'] * (int)$item['qty'];
                    }
                    ?>

                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Subtotal (<?= count($data['cart']); ?> produk)</span>
                        <span>Rp <?= number_format($subtotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Biaya Pengiriman</span>
                        <span class="text-success fw-semibold">GRATIS</span>
                    </div>
                    <hr style="border-color: var(--color-beige);">
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-4" style="color: var(--color-darkest);">
                        <span>Total Tagihan</span>
                        <span style="color: var(--color-coffee);">Rp <?= number_format($subtotal, 0, ',', '.'); ?></span>
                    </div>

                    <a href="<?= BASEURL; ?>/checkout"
                       class="btn btn-coffee w-100 btn-lg rounded-pill fw-bold shadow-sm py-3">
                        <i class="fas fa-lock me-2"></i> Lanjut ke Pembayaran
                    </a>
                    <a href="<?= BASEURL; ?>"
                       class="btn btn-outline-secondary w-100 rounded-pill mt-2 fw-medium">
                        <i class="fas fa-arrow-left me-2"></i> Lanjut Belanja
                    </a>

                    <div class="mt-3 text-center text-muted small">
                        <i class="fas fa-shield-alt me-1"></i>
                        Transaksi aman & terenkripsi via Midtrans
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
