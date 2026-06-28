<?php
// ============================================================
// views/history/index.php — Riwayat Pesanan Saya
// Data dari HistoryController::index():
//   $data['orders'] : array semua pesanan user (PDO::FETCH_ASSOC)
// ============================================================
?>

<style>
    .page-title-bar {
        background: linear-gradient(135deg, var(--color-darkest), var(--color-dark));
        border-radius: 16px;
        padding: 1.5rem 2rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .order-table th {
        background-color: var(--color-dark);
        color: white;
        font-weight: 600;
        letter-spacing: 0.4px;
        border: none;
    }
    .order-table td {
        vertical-align: middle;
        border-color: rgba(162, 123, 92, 0.15);
    }
    .order-table tbody tr {
        transition: background-color 0.2s ease;
    }
    .order-table tbody tr:hover {
        background-color: rgba(162, 123, 92, 0.04);
    }

    /* Status badges */
    .badge-pending    { background-color: #ffc107; color: #212529; }
    .badge-settlement { background-color: #28a745; color: white; }
    .badge-expire     { background-color: #6c757d; color: white; }
    .badge-cancel     { background-color: #dc3545; color: white; }

    .status-badge {
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .order-id-mono {
        font-family: monospace;
        font-size: 0.85rem;
        color: var(--color-dark);
    }
    .empty-history {
        padding: 4rem 2rem;
        text-align: center;
    }
    .btn-detail {
        font-size: 0.8rem;
        padding: 5px 14px;
        border-radius: 20px;
        border: 1px solid var(--color-coffee);
        color: var(--color-coffee);
        background: transparent;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-detail:hover {
        background: var(--color-coffee);
        color: white;
    }
</style>

<!-- Page Header -->
<div class="page-title-bar d-flex align-items-center gap-3">
    <i class="fas fa-history fa-2x"></i>
    <div>
        <h1 class="fw-bold mb-0" style="font-size: 1.6rem;">Riwayat Pesanan Saya</h1>
        <p class="mb-0 opacity-75 small">Kelola dan pantau status semua pesananmu di sini</p>
    </div>
</div>

<!-- Flash Messages -->
<?php if (!empty($data['flash'])): ?>
<div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <?= htmlspecialchars($data['flash']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!empty($data['flash_error'])): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <?= htmlspecialchars($data['flash_error']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (empty($data['orders'])): ?>
<!-- ── BELUM ADA PESANAN ──────────────────────────────────── -->
<div class="card shadow-sm border-0">
    <div class="empty-history">
        <div style="font-size: 4rem; margin-bottom: 1rem;">🧾</div>
        <h4 class="fw-bold mb-2" style="color: var(--color-darkest);">Belum ada pesanan</h4>
        <p class="text-muted mb-4">
            Kamu belum pernah melakukan pemesanan. Yuk mulai belanja kopi favoritmu!
        </p>
        <a href="<?= BASEURL; ?>" class="btn btn-coffee btn-lg rounded-pill px-5 shadow-sm">
            <i class="fas fa-store me-2"></i> Lihat Katalog Kopi
        </a>
    </div>
</div>

<?php else: ?>
<!-- ── TABEL RIWAYAT PESANAN ─────────────────────────────── -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <!-- Info jumlah pesanan -->
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
            <span class="fw-semibold" style="color: var(--color-dark);">
                <?= count($data['orders']); ?> pesanan ditemukan
            </span>
            <small class="text-muted">Diurutkan dari yang terbaru</small>
        </div>

        <div class="table-responsive">
            <table class="table order-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>ID Pesanan</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['orders'] as $i => $order): ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= $i + 1; ?></td>

                        <!-- Order ID (format: ORD-...) -->
                        <td>
                            <span class="order-id-mono">
                                <?= htmlspecialchars($order['order_id_midtrans'] ?? 'N/A'); ?>
                            </span>
                        </td>

                        <!-- Tanggal dibuat -->
                        <td class="text-center text-muted">
                            <?php
                            $tgl = new DateTime($order['created_at']);
                            echo $tgl->format('d M Y, H:i');
                            ?>
                        </td>

                        <!-- Total harga -->
                        <td class="text-center fw-bold" style="color: var(--color-coffee);">
                            Rp <?= number_format((float)$order['total_harga'], 0, ',', '.'); ?>
                        </td>

                        <!-- Status badge -->
                        <td class="text-center">
                            <?php
                            $status    = $order['status_pembayaran'];
                            $badgeMap  = [
                                'pending'    => 'badge-pending',
                                'settlement' => 'badge-settlement',
                                'expire'     => 'badge-expire',
                                'cancel'     => 'badge-cancel',
                            ];
                            $badgeCls  = $badgeMap[$status] ?? 'badge-pending';
                            $labelMap  = [
                                'pending'    => '⏳ Menunggu',
                                'settlement' => '✅ Lunas',
                                'expire'     => '⌛ Kadaluarsa',
                                'cancel'     => '❌ Dibatalkan',
                            ];
                            $label = $labelMap[$status] ?? ucfirst($status);
                            ?>
                            <span class="status-badge <?= $badgeCls; ?>">
                                <?= $label; ?>
                            </span>
                        </td>

                        <!-- Tombol Aksi -->
                        <td class="text-center pe-4">
                            <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                <a href="<?= BASEURL; ?>/history/detail/<?= $order['id']; ?>"
                                   class="btn-detail">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>
                                <?php if ($order['status_pembayaran'] === 'pending'): ?>
                                <!-- 🧪 Tombol Simulasi Sandbox (hanya untuk pending) -->
                                <form method="POST" action="<?= BASEURL; ?>/checkout/simulate" class="d-inline">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id_midtrans']); ?>">
                                    <button type="submit" class="btn btn-sm rounded-pill px-2 py-1"
                                            style="font-size:0.72rem; background: #28a745; color: white; border: none;"
                                            title="Simulasi pembayaran berhasil (Sandbox)"
                                            onclick="return confirm('Simulasikan pembayaran berhasil untuk order ini? (Sandbox Only)')">
                                        <i class="fas fa-check me-1"></i> Simulasi Bayar
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
