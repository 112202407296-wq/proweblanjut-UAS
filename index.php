<?php

// ============================================================
// FRONT CONTROLLER — Entry point semua request
// ============================================================

// Mulai session sebelum output apapun
session_start();

// ─── Konfigurasi Dasar ────────────────────────────────────────
// Sesuaikan BASEURL jika folder berbeda
define('BASEURL', 'http://localhost/proweblanjut-UAS');

// ─── Autoload Core Files ──────────────────────────────────────
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/App.php';

// ─── Jalankan Router ─────────────────────────────────────────
$app = new App();
