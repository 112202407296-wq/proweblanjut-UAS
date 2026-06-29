<div class="row mb-4">
    <div class="col-12 text-center">
        <h2 class="fw-bold mb-2" style="color: var(--color-darkest);">Katalog Kopi Pilihan</h2>
        <p class="text-muted">Temukan varian kopi terbaik untuk menemani senja Anda.</p>
    </div>
</div>

<div class="row g-4">
    <?php if(!empty($data['kopi'])): ?>
        <?php foreach ($data['kopi'] as $item) : ?>
            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0">
                    <!-- Gambar mengarah ke folder public/img/ -->
                    <img src="<?= BASEURL ?? ''; ?>/img/<?= $item['gambar'] ?: 'default.jpg'; ?>" class="card-img-top rounded-top-3" style="height: 220px; object-fit: cover;" alt="<?= $item['nama_kopi']; ?>">
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold mb-2" style="color: var(--color-dark);"><?= $item['nama_kopi']; ?></h5>
                        <p class="card-text text-muted small mb-4" style="line-height: 1.6;"><?= substr($item['deskripsi'], 0, 60); ?>...</p>
                        <h5 class="text-coffee fw-bold mt-auto mb-3">Rp <?= number_format($item['harga'], 0, ',', '.'); ?></h5>
                        <hr class="text-black-50 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Stok: <?= $item['stok']; ?></span>

                            <!-- POST form ke CartController::add() -->
                            <?php if ($item['stok'] > 0): ?>
                            <form method="POST" action="<?= BASEURL ?? ''; ?>/cart/add">
                                <input type="hidden" name="product_id" value="<?= $item['id']; ?>">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="btn btn-coffee btn-sm rounded-pill px-3 shadow-sm">
                                    <i class="fas fa-plus me-1"></i> Beli
                                </button>
                            </form>
                            <?php else: ?>
                            <button class="btn btn-secondary btn-sm rounded-pill px-3" disabled>Habis</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <h5 class="text-muted">Belum ada produk kopi yang tersedia.</h5>
        </div>
    <?php endif; ?>
</div>
