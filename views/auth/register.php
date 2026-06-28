<div class="row justify-content-center" style="min-height: 70vh; align-items: center;">
    <div class="col-lg-5 col-md-7 col-sm-10">

        <!-- Flash Error -->
        <?php if (!empty($data['error'])): ?>
            <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center gap-2 mb-3 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($data['error']); ?></span>
            </div>
        <?php endif; ?>

        <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">

            <!-- Header Card -->
            <div class="text-center py-4 px-4" style="background: linear-gradient(135deg, var(--color-coffee) 0%, var(--color-dark) 100%);">
                <div class="mb-2">
                    <span style="font-size: 2.5rem;">☕</span>
                </div>
                <h4 class="fw-bold text-white mb-1">Buat Akun KopSkuy</h4>
                <p class="text-white-50 small mb-0">Bergabung dan nikmati kopi pilihan terbaik!</p>
            </div>

            <!-- Body Form -->
            <div class="card-body p-4 p-md-5" style="background-color: var(--color-white);">
                <form action="<?= BASEURL; ?>/auth/processRegister" method="POST" id="form-register">

                    <!-- Username -->
                    <div class="mb-3">
                        <label for="reg-username" class="form-label fw-semibold small" style="color: var(--color-dark);">
                            <i class="fas fa-user me-1 text-coffee"></i> Username
                        </label>
                        <input
                            type="text"
                            id="reg-username"
                            name="username"
                            class="form-control form-control-lg border-0 rounded-3"
                            placeholder="Nama panggilanmu"
                            style="background-color: var(--color-beige); font-size: 0.95rem;"
                            required
                            minlength="3"
                            autocomplete="username"
                        >
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="reg-email" class="form-label fw-semibold small" style="color: var(--color-dark);">
                            <i class="fas fa-envelope me-1 text-coffee"></i> Alamat Email
                        </label>
                        <input
                            type="email"
                            id="reg-email"
                            name="email"
                            class="form-control form-control-lg border-0 rounded-3"
                            placeholder="contoh@email.com"
                            style="background-color: var(--color-beige); font-size: 0.95rem;"
                            required
                            autocomplete="email"
                        >
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="reg-password" class="form-label fw-semibold small" style="color: var(--color-dark);">
                            <i class="fas fa-lock me-1 text-coffee"></i> Password
                        </label>
                        <div class="input-group">
                            <input
                                type="password"
                                id="reg-password"
                                name="password"
                                class="form-control form-control-lg border-0 rounded-start-3"
                                placeholder="Minimal 6 karakter"
                                style="background-color: var(--color-beige); font-size: 0.95rem;"
                                required
                                minlength="6"
                                autocomplete="new-password"
                            >
                            <button
                                type="button"
                                class="btn border-0 rounded-end-3"
                                style="background-color: var(--color-beige); color: var(--color-dark);"
                                onclick="togglePassword('reg-password', this)"
                                title="Tampilkan/Sembunyikan Password"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <!-- Password strength bar -->
                        <div class="mt-2">
                            <div id="password-strength-bar" class="progress" style="height: 4px; border-radius: 10px;">
                                <div id="strength-fill" class="progress-bar" role="progressbar" style="width: 0%; transition: width 0.3s;"></div>
                            </div>
                            <small id="strength-text" class="text-muted" style="font-size: 0.78rem;"></small>
                        </div>
                    </div>

                    <!-- Syarat & Ketentuan -->
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agree-terms" required>
                            <label class="form-check-label small" for="agree-terms" style="color: var(--color-dark);">
                                Saya setuju dengan <a href="#" style="color: var(--color-coffee);">syarat & ketentuan</a> KopSkuy
                            </label>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <button
                        type="submit"
                        id="btn-register"
                        class="btn btn-coffee w-100 py-3 fw-bold rounded-3"
                        style="font-size: 1rem; letter-spacing: 0.5px;"
                    >
                        <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                    </button>

                </form>

                <hr class="my-4" style="border-color: var(--color-beige);">

                <p class="text-center mb-0" style="color: var(--color-dark); font-size: 0.9rem;">
                    Sudah punya akun?
                    <a href="<?= BASEURL; ?>/auth/login" class="fw-bold text-decoration-none" style="color: var(--color-coffee);">
                        Login di sini
                    </a>
                </p>
            </div>
        </div>

        <p class="text-center mt-3 small text-muted">
            <i class="fas fa-shield-alt me-1"></i> Password dienkripsi dengan BCRYPT
        </p>
    </div>
</div>

<script>
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

// Password strength indicator
document.getElementById('reg-password').addEventListener('input', function() {
    const val = this.value;
    const bar = document.getElementById('strength-fill');
    const text = document.getElementById('strength-text');
    let strength = 0;
    let label = '';
    let color = '';

    if (val.length >= 6) strength++;
    if (val.length >= 10) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    if (strength <= 1) { label = 'Lemah'; color = '#dc3545'; }
    else if (strength <= 3) { label = 'Cukup'; color = '#ffc107'; }
    else { label = 'Kuat'; color = '#28a745'; }

    bar.style.width = (strength * 20) + '%';
    bar.style.backgroundColor = color;
    text.textContent = val.length > 0 ? 'Kekuatan password: ' + label : '';
    text.style.color = color;
});
</script>
