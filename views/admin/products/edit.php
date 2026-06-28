<!-- ═══════════════════════════════════════════════════════
     Admin Panel: Form Edit Produk Kopi
     ═══════════════════════════════════════════════════════ -->

<?php $produk = $data['produk']; ?>

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item">
                    <a href="<?= BASEURL; ?>/admin" style="color: var(--color-coffee); text-decoration: none;">
                        <i class="fas fa-boxes-stacked me-1"></i> Manajemen Produk
                    </a>
                </li>
                <li class="breadcrumb-item active text-muted">
                    Edit: <?= htmlspecialchars($produk['nama_kopi']); ?>
                </li>
            </ol>
        </nav>

        <!-- Flash Error -->
        <?php if (!empty($data['error'])): ?>
            <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($data['error']); ?></span>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4" style="overflow: hidden;">

            <!-- Header Card -->
            <div class="px-4 py-3 d-flex align-items-center gap-3" style="background: linear-gradient(135deg, var(--color-coffee) 0%, #8c6a4f 100%);">
                <div style="width:42px; height:42px; background: rgba(255,255,255,0.2); border-radius:10px;" class="d-flex align-items-center justify-content-center">
                    <i class="fas fa-pen-to-square text-white"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-white mb-0">Edit Produk Kopi</h5>
                    <p class="text-white-50 small mb-0">ID: #<?= $produk['id']; ?> — Perbarui informasi produk</p>
                </div>
            </div>

            <!-- Form Body -->
            <div class="card-body p-4 p-md-5" style="background-color: var(--color-white);">
                <!-- ⚠️ WAJIB: enctype="multipart/form-data" untuk upload gambar baru -->
                <form action="<?= BASEURL; ?>/admin/update/<?= $produk['id']; ?>" method="POST" enctype="multipart/form-data" id="form-edit-produk">

                    <!-- Field tersembunyi: simpan nama gambar lama -->
                    <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($produk['gambar']); ?>">

                    <div class="row g-4">

                        <!-- Kolom Kiri: Data Teks -->
                        <div class="col-md-7">

                            <!-- Nama Kopi -->
                            <div class="mb-4">
                                <label for="edit-nama-kopi" class="form-label fw-semibold small" style="color: var(--color-dark);">
                                    <i class="fas fa-mug-hot me-1 text-coffee"></i> Nama Kopi <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="edit-nama-kopi"
                                    name="nama_kopi"
                                    class="form-control form-control-lg border-0 rounded-3"
                                    style="background-color: var(--color-beige); font-size: 0.95rem;"
                                    value="<?= htmlspecialchars($produk['nama_kopi']); ?>"
                                    required
                                    maxlength="150"
                                >
                            </div>

                            <!-- Harga -->
                            <div class="mb-4">
                                <label for="edit-harga" class="form-label fw-semibold small" style="color: var(--color-dark);">
                                    <i class="fas fa-tag me-1 text-coffee"></i> Harga (Rp) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 fw-semibold" style="background-color: var(--color-beige); color: var(--color-coffee);">Rp</span>
                                    <input
                                        type="number"
                                        id="edit-harga"
                                        name="harga"
                                        class="form-control form-control-lg border-0 rounded-end-3"
                                        style="background-color: var(--color-beige); font-size: 0.95rem;"
                                        value="<?= htmlspecialchars($produk['harga']); ?>"
                                        required
                                        min="1000"
                                        step="500"
                                    >
                                </div>
                            </div>

                            <!-- Stok -->
                            <div class="mb-4">
                                <label for="edit-stok" class="form-label fw-semibold small" style="color: var(--color-dark);">
                                    <i class="fas fa-layer-group me-1 text-coffee"></i> Stok <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="edit-stok"
                                    name="stok"
                                    class="form-control form-control-lg border-0 rounded-3"
                                    style="background-color: var(--color-beige); font-size: 0.95rem;"
                                    value="<?= htmlspecialchars($produk['stok']); ?>"
                                    required
                                    min="0"
                                >
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-4">
                                <label for="edit-deskripsi" class="form-label fw-semibold small" style="color: var(--color-dark);">
                                    <i class="fas fa-align-left me-1 text-coffee"></i> Deskripsi Rasa <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    id="edit-deskripsi"
                                    name="deskripsi"
                                    class="form-control border-0 rounded-3"
                                    rows="4"
                                    style="background-color: var(--color-beige); font-size: 0.95rem; resize: none;"
                                    required
                                    maxlength="500"
                                ><?= htmlspecialchars($produk['deskripsi']); ?></textarea>
                                <small class="text-muted" id="edit-desc-counter">
                                    <?= mb_strlen($produk['deskripsi']); ?> / 500 karakter
                                </small>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Gambar -->
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small" style="color: var(--color-dark);">
                                <i class="fas fa-image me-1 text-coffee"></i> Foto Produk
                            </label>

                            <!-- Preview gambar yang ada saat ini -->
                            <div
                                class="rounded-4 mb-2 position-relative"
                                style="height: 200px; overflow: hidden; border: 2px solid var(--color-beige);"
                            >
                                <img
                                    id="img-preview-edit"
                                    src="<?= BASEURL; ?>/img/<?= htmlspecialchars($produk['gambar'] ?: 'default.jpg'); ?>"
                                    alt="<?= htmlspecialchars($produk['nama_kopi']); ?>"
                                    class="w-100 h-100"
                                    style="object-fit: cover;"
                                    onerror="this.src='https://placehold.co/300x200/DCD7C9/A27B5C?text=☕'"
                                >
                                <!-- Overlay saat hover -->
                                <div
                                    class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                    style="background: rgba(0,0,0,0.4); opacity:0; transition: opacity 0.3s; cursor:pointer; border-radius: 14px;"
                                    onmouseover="this.style.opacity='1'"
                                    onmouseout="this.style.opacity='0'"
                                    onclick="document.getElementById('gambar-baru').click()"
                                >
                                    <div class="text-white text-center">
                                        <i class="fas fa-camera fa-2x mb-2"></i>
                                        <p class="mb-0 small fw-semibold">Ganti Foto</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Label nama file gambar saat ini -->
                            <p class="text-muted text-center mb-3" style="font-size: 0.75rem;">
                                <i class="fas fa-file-image me-1"></i>
                                File aktif: <code style="font-size: 0.7rem;"><?= htmlspecialchars(mb_substr($produk['gambar'], 0, 20)); ?>...</code>
                            </p>

                            <!-- Input file baru (tersembunyi) -->
                            <input
                                type="file"
                                id="gambar-baru"
                                name="gambar"
                                class="d-none"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                                onchange="previewNewImage(this)"
                            >

                            <button
                                type="button"
                                class="btn w-100 rounded-3 fw-medium small"
                                style="border: 1.5px solid var(--color-coffee); color: var(--color-coffee); background: transparent;"
                                onclick="document.getElementById('gambar-baru').click()"
                                id="btn-ganti-gambar"
                            >
                                <i class="fas fa-arrow-up-from-bracket me-2"></i> Ganti Foto Produk
                            </button>

                            <p class="text-muted small mt-2 text-center">
                                <i class="fas fa-info-circle me-1"></i>
                                Kosongkan jika tidak ingin mengganti foto
                            </p>
                        </div>

                    </div><!-- end row -->

                    <hr style="border-color: var(--color-beige);" class="my-4">

                    <!-- Tombol Aksi -->
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="<?= BASEURL; ?>/admin" class="btn rounded-pill px-4 py-2" style="border: 1.5px solid var(--color-dark); color: var(--color-dark);" id="btn-batal-edit">
                            <i class="fas fa-arrow-left me-2"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-coffee rounded-pill px-5 py-2 fw-bold shadow-sm" id="btn-simpan-edit">
                            <i class="fas fa-floppy-disk me-2"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Preview gambar baru saat dipilih
function previewNewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('⚠️ Ukuran gambar melebihi 2MB!');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('img-preview-edit').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

// Counter karakter deskripsi
document.getElementById('edit-deskripsi').addEventListener('input', function() {
    document.getElementById('edit-desc-counter').textContent = this.value.length + ' / 500 karakter';
});
</script>
