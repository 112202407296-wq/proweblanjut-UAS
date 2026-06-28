<?php

// ============================================================
// User.php — Model untuk tabel `users`
// Dipakai oleh AuthController untuk Register & Login
// ============================================================

class User {
    private $db;

    public function __construct() {
        // Gunakan Database class yang sudah dibuat (dimuat di index.php)
        require_once __DIR__ . '/../core/Database.php';
        $this->db = new Database();
    }

    // ── REGISTER ─────────────────────────────────────────────
    // ⚠️ CPMK 5: Password WAJIB di-hash sebelum disimpan ke DB
    public function register($username, $email, $password) {
        // Enkripsi password menggunakan BCRYPT (standar keamanan industri)
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        // Prepared Statement untuk mencegah SQL Injection
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'customer')"
        );
        return $stmt->execute([$username, $email, $password_hashed]);
    }

    // ── CARI USER BERDASARKAN EMAIL (untuk proses Login) ─────
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(); // Mengembalikan 1 baris atau false
    }

    // ── CEK APAKAH EMAIL SUDAH TERDAFTAR ─────────────────────
    public function isEmailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }
}
