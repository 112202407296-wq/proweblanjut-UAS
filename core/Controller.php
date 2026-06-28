<?php

// ============================================================
// Controller.php — Base Controller (Induk semua Controller)
// Menyediakan: view(), model(), guardAdmin()
// ============================================================

class Controller {

    // ── Render View ───────────────────────────────────────────
    // $view   : path relatif dari views/, contoh 'auth/login'
    // $data   : array data yang bisa diakses di view sebagai $data[]
    public function view($view, $data = []) {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;  // $data tersedia di scope view
        } else {
            die("<b>View Error:</b> File '<code>views/{$view}.php</code>' tidak ditemukan.");
        }
    }

    // ── Load & Instantiate Model ──────────────────────────────
    public function model($model) {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die("<b>Model Error:</b> File '<code>models/{$model}.php</code>' tidak ditemukan.");
        }
    }

    // ── Pos Satpam Admin (Middleware RBAC) ───────────────────
    // Panggil di __construct() controller admin mana saja
    public function guardAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }

    // ── Guard Login (Wajib Login) ─────────────────────────────
    // Panggil jika halaman hanya boleh diakses user yang sudah login
    public function guardLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }
}
