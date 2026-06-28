<?php
// ============================================================
// views/history/detail.php — Detail Satu Pesanan
// Data dari HistoryController::detail():
//   $data['order'] : array header order
//   $data['items'] : array item-item pesanan
// ============================================================
?>

<style>
    .detail-header-card {
        background: linear-gradient(135deg, var(--color-darkest), var(--color-dark));
        border-radius: 16px 16px 0 0;
        padding: 1.5rem 2rem;
        color: white;
    }
    .badge-pending    { background-color: #ffc107; color: #212529; }
    .badge-settlement { background-color: #28a745; color: white; }
    .badge-expire     { background-color: #6c757d; color: white; }
    .badge-cancel     { background-color: #dc3545; color: white; }
    .status-badge {
        padding: 6px 18px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .detail-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .info-box {
        background: rgba(162, 123, 92, 0.06);
        border: 1px solid rgba(162, 123, 92, 0.15);
        border-radius: 12px;
        padding: 1rem;
    }
    .info-box .label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--color-coffee);
        font-weight: 600;
        margin-bottom: 4px;
    }
    .info-box .value {
        font-weight: 700;
        color: var(--color-darkest);
    }
    .item-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(162, 123, 92, 0.1);
    }
    .item-row:last-child { border-bottom: none; }
    .item-thumb {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid var(--color-beige);
        flex-shrink: 0;
    }
    .total-row {
        background: linear-gradient(135deg, var(--color-beige), #e8e2d6);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">

        <!-- Back Link -->
        <a href="<?= BASEURL; ?>/history" class="d-inline-flex align-items-center gap-2 text-muted text-decoration-none mb-3 fw-medium">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat Pesanan
        </a>

        <div class="card shadow border-0" style="border-radius: 16px; overflow: hidden;">
            <!-- Header -->
            <div class="detail-header-card d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <div style="font-size: 0.75rem; opacity: 0.7; letter-spacing: 1px; text-transform: uppercase;">ID Pesanan</div>
                    <div style="font-family: monospace; font-size: 1.05rem; font-weight: 700;">
                        <?= htmlspecialchars($data['order']['order_id_midtrans'] ?? 'N/A'); ?>
                    </div>
                </div>
                <?php
                $status   = $data['order']['status_pembayaran'];
                $badgeMap = ['pending'=>'badge-pending','settlement'=>'badge-settlement','expire'=>'badge-expire','cancel'=>'badge-cancel'];
                $badgeCls = $badgeMap[$status] ?? 'badge-pending';
                $labelMap = ['pending'=>'⏳ Menunggu','settlement'=>'✅ Lunas','expire'=>'⌛ Kadaluarsa','cancel'=>'❌ Dibatalkan'];
                $label    = $labelMap[$status] ?? ucfirst($status);
                ?>
                <span class="status-badge <?= $badgeCls; ?>"><?= $label; ?></span>
            </div>

            <div class="card-body p-4">
                <!-- Info Grid -->
                <div class="detail-info-grid mb-4">
                    <div class="info-box">
                        <div class="label">Tanggal Pesan</div>
                        <div class="value">
                            <?php
                            $tgl = new DateTime($data['order']['created_at']);
                            echo $tgl->format('d M Y, H:i');
                            ?>
                        </div>
                    </div>
                    <div class="info-box">
                        <div class="label">Total Tagihan</div>
                        <div class="value" style="color: var(--color-coffee);">
                            Rp <?= number_format((float)$data['order']['total_harga'], 0, ',', '.'); ?>
                        </div>
                    </div>
                </div>

                <hr style="border-color: rgba(162,123,92,0.2);">

                <!-- Daftar Item -->
                <h6 class="fw-bold mb-3 text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 1px;">
                    Detail Item Pesanan
                </h6>

                <?php if (empty($data['items'])): ?>
                    <p class="text-muted text-center py-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Detail item tidak tersedia untuk pesanan ini.
                    </p>
                <?php else: ?>
                    <?php foreach ($data['items'] as $item): ?>
                    <div class="item-row">
                        <img
                            src="<?= BASEURL; ?>/img/<?= htmlspecialchars($item['gambar']); ?>"
                            alt="<?= htmlspecialchars($item['nama_kopi']); ?>"
                            class="item-thumb"
                            onerror="this.src='<?= BASEURL; ?>/img/default.jpg'"
                        >
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="color: var(--color-darkest);">
                                <?= htmlspecialchars($item['nama_kopi']); ?>
                            </div>
                            <small class="text-muted">
                                <?= $item['qty']; ?> × Rp <?= number_format((float)$item['harga_satuan'], 0, ',', '.'); ?>
                            </small>
                        </div>
                        <div class="fw-bold text-end" style="color: var(--color-coffee);">
                            Rp <?= number_format((float)$item['harga_satuan'] * (int)$item['qty'], 0, ',', '.'); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Total -->
                <div class="total-row">
                    <span class="fw-bold fs-6" style="color: var(--color-darkest);">Total Pembayaran</span>
                    <span class="fw-bold fs-5" style="color: var(--color-coffee);">
                        Rp <?= number_format((float)$data['order']['total_harga'], 0, ',', '.'); ?>
                    </span>
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2 mt-4">
                    <a href="<?= BASEURL; ?>/history" class="btn btn-outline-secondary rounded-pill px-4 fw-medium flex-fill">
                        <i class="fas fa-list-alt me-1"></i> Riwayat
                    </a>
                    <a href="<?= BASEURL; ?>" class="btn btn-coffee rounded-pill px-4 fw-medium flex-fill shadow-sm">
                        <i class="fas fa-store me-1"></i> Belanja Lagi
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
