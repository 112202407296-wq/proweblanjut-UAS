<div class="row justify-content-center" style="min-height: 70vh; align-items: center;">
    <div class="col-lg-5 col-md-7 col-sm-10">

        <!-- Flash Error -->
        <?php if (!empty($data['error'])): ?>
            <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center gap-2 mb-3 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($data['error']); ?></span>
            </div>
        <?php endif; ?>

        <!-- Flash Success -->
        <?php if (!empty($data['success'])): ?>
            <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-2 mb-3 shadow-sm" role="alert">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($data['success']); ?></span>
            </div>
        <?php endif; ?>

        <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">

            <!-- Header Card -->
            <div class="text-center py-4 px-4" style="background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-darkest) 100%);">
                <div class="mb-2">
                    <span style="font-size: 2.5rem;">☕</span>
                </div>
                <h4 class="fw-bold text-white mb-1">Masuk ke KopSkuy</h4>
                <p class="text-white-50 small mb-0">Selamat datang kembali, kopi menunggumu!</p>
            </div>

            <!-- Body Form -->
            <div class="card-body p-4 p-md-5" style="background-color: var(--color-white);">
                <form action="<?= BASEURL; ?>/auth/processLogin" method="POST" id="form-login">

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="login-email" class="form-label fw-semibold small" style="color: var(--color-dark);">
                            <i class="fas fa-envelope me-1 text-coffee"></i> Alamat Email
                        </label>
                        <input
                            type="email"
                            id="login-email"
                            name="email"
                            class="form-control form-control-lg border-0 rounded-3"
                            placeholder="contoh@email.com"
                            style="background-color: var(--color-beige); font-size: 0.95rem;"
                            required
                            autocomplete="email"
                        >
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="login-password" class="form-label fw-semibold small" style="color: var(--color-dark);">
                            <i class="fas fa-lock me-1 text-coffee"></i> Password
                        </label>
                        <div class="input-group">
                            <input
                                type="password"
                                id="login-password"
                                name="password"
                                class="form-control form-control-lg border-0 rounded-start-3"
                                placeholder="Masukkan password"
                                style="background-color: var(--color-beige); font-size: 0.95rem;"
                                required
                                autocomplete="current-password"
                            >
                            <button
                                type="button"
                                class="btn border-0 rounded-end-3"
                                style="background-color: var(--color-beige); color: var(--color-dark);"
                                onclick="togglePassword('login-password', this)"
                                title="Tampilkan/Sembunyikan Password"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <button
                        type="submit"
                        id="btn-login"
                        class="btn btn-coffee w-100 py-3 fw-bold rounded-3 mt-2"
                        style="font-size: 1rem; letter-spacing: 0.5px;"
                    >
                        <i class="fas fa-sign-in-alt me-2"></i> Masuk Sekarang
                    </button>

                </form>

                <hr class="my-4" style="border-color: var(--color-beige);">

                <p class="text-center mb-0" style="color: var(--color-dark); font-size: 0.9rem;">
                    Belum punya akun?
                    <a href="<?= BASEURL; ?>/auth/register" class="fw-bold text-decoration-none" style="color: var(--color-coffee);">
                        Daftar di sini
                    </a>
                </p>
            </div>
        </div>

        <!-- Subtle copyright -->
        <p class="text-center mt-3 small text-muted">
            <i class="fas fa-shield-alt me-1"></i> Password dienkripsi & aman
        </p>
    </div>
</div>

<script>
// Toggle show/hide password
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
