<?php

class Database {
    private $dbh;

    public function __construct() {
        // Menangkap file .env di root project
        $env_path = __DIR__ . '/../.env';
        if (!file_exists($env_path)) {
            die("Fatal Error: File .env tidak ditemukan!");
        }

        // Parser .env manual yang tangguh (anti-error karakter khusus)
        $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue; // Abaikan komentar
            if (strpos($line, '=') === false) continue;   // Abaikan baris tanpa '='
            list($name, $value) = explode('=', $line, 2);
            $env[trim($name)] = trim($value);
        }

        // DSN untuk MySQL lokal (XAMPP)
        $dsn = "mysql:host=" . $env['DB_HOST'] . ";port=" . $env['DB_PORT'] . ";dbname=" . $env['DB_NAME'] . ";charset=utf8mb4";

        try {
            $this->dbh = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die("Koneksi Database Gagal: " . $e->getMessage());
        }
    }

    public function prepare($sql) {
        return $this->dbh->prepare($sql);
    }
}
