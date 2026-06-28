<!-- ═══════════════════════════════════════════════════════
     Admin Panel: Daftar Produk Kopi
     ═══════════════════════════════════════════════════════ -->

<!-- Flash Sukses -->
<?php if (!empty($data['flash'])): ?>
    <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
        <i class="fas fa-check-circle fa-lg"></i>
        <span><?= htmlspecialchars($data['flash']); ?></span>
    </div>
<?php endif; ?>

<!-- Header Panel -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--color-darkest);">
            <i class="fas fa-boxes-stacked me-2" style="color: var(--color-coffee);"></i> Manajemen Gudang Kopi
        </h2>
        <p class="text-muted mb-0 small">Kelola semua varian kopi yang tersedia di KopSkuy</p>
    </div>
    <a href="<?= BASEURL; ?>/admin/create" class="btn btn-coffee rounded-pill px-4 py-2 fw-semibold shadow-sm" id="btn-tambah-produk">
        <i class="fas fa-plus me-2"></i> Tambah Produk Baru
    </a>
</div>

<!-- Statistik Singkat -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3" style="background: linear-gradient(135deg, var(--color-dark), var(--color-darkest));">
            <div style="width:50px; height:50px; background: rgba(162,123,92,0.3); border-radius:12px;" class="d-flex align-items-center justify-content-center">
                <i class="fas fa-mug-hot fa-lg text-white"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 text-white"><?= count($data['kopi']); ?></div>
                <div class="text-white-50 small">Total Varian Kopi</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3" style="background: linear-gradient(135deg, var(--color-coffee), #8c6a4f);">
            <div style="width:50px; height:50px; background: rgba(255,255,255,0.2); border-radius:12px;" class="d-flex align-items-center justify-content-center">
                <i class="fas fa-layer-group fa-lg text-white"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 text-white"><?= array_sum(array_column($data['kopi'], 'stok')); ?></div>
                <div class="text-white-50 small">Total Stok</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3" style="background: linear-gradient(135deg, #3d8b6e, #2a6350);">
            <div style="width:50px; height:50px; background: rgba(255,255,255,0.2); border-radius:12px;" class="d-flex align-items-center justify-content-center">
                <i class="fas fa-check-circle fa-lg text-white"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 text-white"><?= count(array_filter($data['kopi'], fn($k) => $k['stok'] > 0)); ?></div>
                <div class="text-white-50 small">Produk Tersedia</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Daftar Produk -->
<div class="card border-0 shadow-sm rounded-4" style="overflow: hidden;">
    <div class="card-body p-0">
        <?php if (!empty($data['kopi'])): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" id="tabel-produk">
                <thead style="background-color: var(--color-darkest); color: var(--color-white);">
                    <tr>
                        <th class="ps-4 py-3" style="width: 5%;">#</th>
                        <th class="py-3" style="width: 8%;">Foto</th>
                        <th class="py-3" style="width: 25%;">Nama Kopi</th>
                        <th class="py-3" style="width: 20%;">Deskripsi</th>
                        <th class="py-3" style="width: 12%;">Harga</th>
                        <th class="py-3 text-center" style="width: 8%;">Stok</th>
                        <th class="pe-4 py-3 text-center" style="width: 22%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($data['kopi'] as $item): ?>
                    <tr id="row-produk-<?= $item['id']; ?>">
                        <td class="ps-4 text-muted small"><?= $no++; ?></td>
                        <td>
                            <img
                                src="<?= BASEURL; ?>/img/<?= htmlspecialchars($item['gambar'] ?: 'default.jpg'); ?>"
                                alt="<?= htmlspecialchars($item['nama_kopi']); ?>"
                                class="rounded-3"
                                style="width: 55px; height: 55px; object-fit: cover;"
                                onerror="this.src='https://placehold.co/55x55/DCD7C9/A27B5C?text=☕'"
                            >
                        </td>
                        <td>
                            <div class="fw-semibold" style="color: var(--color-darkest);">
                                <?= htmlspecialchars($item['nama_kopi']); ?>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= htmlspecialchars(mb_substr($item['deskripsi'], 0, 50)); ?>...
                            </small>
                        </td>
                        <td>
                            <span class="fw-bold" style="color: var(--color-coffee);">
                                Rp <?= number_format($item['harga'], 0, ',', '.'); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ($item['stok'] > 5): ?>
                                <span class="badge rounded-pill px-3 py-2" style="background-color: #d4edda; color: #28a745;"><?= $item['stok']; ?></span>
                            <?php elseif ($item['stok'] > 0): ?>
                                <span class="badge rounded-pill px-3 py-2" style="background-color: #fff3cd; color: #856404;"><?= $item['stok']; ?></span>
                            <?php else: ?>
                                <span class="badge rounded-pill px-3 py-2" style="background-color: #f8d7da; color: #842029;">Habis</span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-4 text-center">
                            <!-- Tombol Edit -->
                            <a
                                href="<?= BASEURL; ?>/admin/edit/<?= $item['id']; ?>"
                                class="btn btn-sm rounded-pill px-3 me-1 fw-medium"
                                style="background-color: #e8f4f8; color: var(--color-dark); border: 1px solid #cce5f0;"
                                id="btn-edit-<?= $item['id']; ?>"
                            >
                                <i class="fas fa-pen-to-square me-1"></i> Edit
                            </a>

                            <!-- Tombol Hapus (dengan konfirmasi) -->
                            <a
                                href="<?= BASEURL; ?>/admin/delete/<?= $item['id']; ?>"
                                class="btn btn-sm rounded-pill px-3 fw-medium"
                                style="background-color: #fde8e8; color: #842029; border: 1px solid #f5c6cb;"
                                id="btn-hapus-<?= $item['id']; ?>"
                                onclick="return confirmDelete('<?= htmlspecialchars($item['nama_kopi']); ?>')"
                            >
                                <i class="fas fa-trash-can me-1"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <!-- State kosong -->
        <div class="text-center py-5 px-4">
            <span style="font-size: 4rem;">☕</span>
            <h5 class="fw-bold mt-3 mb-2" style="color: var(--color-darkest);">Gudang Masih Kosong!</h5>
            <p class="text-muted mb-4">Belum ada produk kopi yang ditambahkan. Yuk mulai isi gudangmu!</p>
            <a href="<?= BASEURL; ?>/admin/create" class="btn btn-coffee rounded-pill px-5 py-2 fw-bold shadow">
                <i class="fas fa-plus me-2"></i> Tambah Produk Pertama
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmDelete(namaProduk) {
    return confirm('⚠️ Hapus produk "' + namaProduk + '"?\n\nTindakan ini tidak bisa dibatalkan dan gambar produk akan ikut dihapus dari server.');
}
</script>
