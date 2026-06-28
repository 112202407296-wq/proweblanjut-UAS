<!-- ═══════════════════════════════════════════════════════
     Admin Panel: Form Tambah Produk Kopi Baru
     ═══════════════════════════════════════════════════════ -->

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
                <li class="breadcrumb-item active text-muted">Tambah Produk Baru</li>
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
            <div class="px-4 py-3 d-flex align-items-center gap-3" style="background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-darkest) 100%);">
                <div style="width:42px; height:42px; background: rgba(162,123,92,0.3); border-radius:10px;" class="d-flex align-items-center justify-content-center">
                    <i class="fas fa-plus text-white"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-white mb-0">Tambah Varian Kopi Baru</h5>
                    <p class="text-white-50 small mb-0">Isi detail produk dan unggah foto kopi</p>
                </div>
            </div>

            <!-- Form Body -->
            <div class="card-body p-4 p-md-5" style="background-color: var(--color-white);">
                <!-- ⚠️ WAJIB: enctype="multipart/form-data" agar gambar terkirim -->
                <form action="<?= BASEURL; ?>/admin/store" method="POST" enctype="multipart/form-data" id="form-tambah-produk">

                    <div class="row g-4">

                        <!-- Kolom Kiri: Data Teks -->
                        <div class="col-md-7">

                            <!-- Nama Kopi -->
                            <div class="mb-4">
                                <label for="nama_kopi" class="form-label fw-semibold small" style="color: var(--color-dark);">
                                    <i class="fas fa-mug-hot me-1 text-coffee"></i> Nama Kopi <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_kopi"
                                    name="nama_kopi"
                                    class="form-control form-control-lg border-0 rounded-3"
                                    placeholder="cth: Arabika Gayo Premium"
                                    style="background-color: var(--color-beige); font-size: 0.95rem;"
                                    required
                                    maxlength="150"
                                >
                            </div>

                            <!-- Harga -->
                            <div class="mb-4">
                                <label for="harga" class="form-label fw-semibold small" style="color: var(--color-dark);">
                                    <i class="fas fa-tag me-1 text-coffee"></i> Harga (Rp) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 fw-semibold" style="background-color: var(--color-beige); color: var(--color-coffee);">Rp</span>
                                    <input
                                        type="number"
                                        id="harga"
                                        name="harga"
                                        class="form-control form-control-lg border-0 rounded-end-3"
                                        placeholder="25000"
                                        style="background-color: var(--color-beige); font-size: 0.95rem;"
                                        required
                                        min="1000"
                                        step="500"
                                    >
                                </div>
                            </div>

                            <!-- Stok -->
                            <div class="mb-4">
                                <label for="stok" class="form-label fw-semibold small" style="color: var(--color-dark);">
                                    <i class="fas fa-layer-group me-1 text-coffee"></i> Stok Awal <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="stok"
                                    name="stok"
                                    class="form-control form-control-lg border-0 rounded-3"
                                    placeholder="50"
                                    style="background-color: var(--color-beige); font-size: 0.95rem;"
                                    required
                                    min="0"
                                >
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-4">
                                <label for="deskripsi" class="form-label fw-semibold small" style="color: var(--color-dark);">
                                    <i class="fas fa-align-left me-1 text-coffee"></i> Deskripsi Rasa <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    id="deskripsi"
                                    name="deskripsi"
                                    class="form-control border-0 rounded-3"
                                    rows="4"
                                    placeholder="Deskripsikan aroma, cita rasa, dan keunikan kopi ini..."
                                    style="background-color: var(--color-beige); font-size: 0.95rem; resize: none;"
                                    required
                                    maxlength="500"
                                ></textarea>
                                <small class="text-muted" id="desc-counter">0 / 500 karakter</small>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Upload Gambar -->
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small" style="color: var(--color-dark);">
                                <i class="fas fa-image me-1 text-coffee"></i> Foto Produk
                            </label>

                            <!-- Preview area -->
                            <div
                                id="preview-container"
                                class="d-flex align-items-center justify-content-center rounded-4 mb-3 position-relative"
                                style="height: 220px; background-color: var(--color-beige); border: 2px dashed var(--color-coffee); cursor: pointer; transition: all 0.3s;"
                                onclick="document.getElementById('gambar').click()"
                            >
                                <div id="preview-placeholder" class="text-center p-3">
                                    <i class="fas fa-cloud-arrow-up fa-2x mb-2" style="color: var(--color-coffee);"></i>
                                    <p class="mb-1 fw-semibold small" style="color: var(--color-dark);">Klik untuk pilih gambar</p>
                                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">JPG, PNG, WEBP — Maks. 2MB</p>
                                </div>
                                <img
                                    id="img-preview"
                                    src=""
                                    alt="Preview"
                                    class="rounded-4 d-none"
                                    style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;"
                                >
                            </div>

                            <!-- Input file (tersembunyi) -->
                            <input
                                type="file"
                                id="gambar"
                                name="gambar"
                                class="d-none"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                                onchange="previewImage(this)"
                            >

                            <button
                                type="button"
                                class="btn w-100 rounded-3 fw-medium small"
                                style="border: 1.5px solid var(--color-coffee); color: var(--color-coffee); background: transparent;"
                                onclick="document.getElementById('gambar').click()"
                                id="btn-pilih-gambar"
                            >
                                <i class="fas fa-folder-open me-2"></i> Pilih File Gambar
                            </button>

                            <p class="text-muted small mt-2 text-center">
                                <i class="fas fa-info-circle me-1"></i>
                                Nama file akan dienkripsi otomatis
                            </p>
                        </div>

                    </div><!-- end row -->

                    <hr style="border-color: var(--color-beige);" class="my-4">

                    <!-- Tombol Aksi -->
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="<?= BASEURL; ?>/admin" class="btn rounded-pill px-4 py-2" style="border: 1.5px solid var(--color-dark); color: var(--color-dark);" id="btn-batal-tambah">
                            <i class="fas fa-arrow-left me-2"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-coffee rounded-pill px-5 py-2 fw-bold shadow-sm" id="btn-simpan-produk">
                            <i class="fas fa-floppy-disk me-2"></i> Simpan ke Gudang
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Preview gambar sebelum upload
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validasi ukuran di sisi klien
        if (file.size > 2 * 1024 * 1024) {
            alert('⚠️ Ukuran gambar melebihi 2MB!\nSilakan pilih gambar yang lebih kecil.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('img-preview').src = e.target.result;
            document.getElementById('img-preview').classList.remove('d-none');
            document.getElementById('preview-placeholder').classList.add('d-none');
        };
        reader.readAsDataURL(file);
    }
}

// Counter karakter deskripsi
document.getElementById('deskripsi').addEventListener('input', function() {
    document.getElementById('desc-counter').textContent = this.value.length + ' / 500 karakter';
});
</script>
