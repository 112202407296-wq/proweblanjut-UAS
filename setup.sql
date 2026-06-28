-- ============================================================
-- setup.sql — KopSkuy Database Schema
-- Jalankan file ini di phpMyAdmin atau MySQL CLI
-- Contoh CLI: mysql -u root -p < setup.sql
-- ============================================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS `kopskuy`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `kopskuy`;

-- ─────────────────────────────────────────────────────────────
-- Tabel: users
-- Menyimpan data pembeli (customer) dan admin
-- Kolom password: VARCHAR(255) untuk menampung hash BCRYPT
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(100)    NOT NULL,
    `email`      VARCHAR(150)    NOT NULL UNIQUE,
    `password`   VARCHAR(255)    NOT NULL COMMENT 'Disimpan sebagai hash BCRYPT (password_hash)',
    `role`       ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Tabel: products
-- Menyimpan varian kopi yang dijual
-- Kolom gambar: nama file saja (bukan path penuh)
--   → file fisik ada di proweblanjut-UAS/img/
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `products` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `nama_kopi`  VARCHAR(150)    NOT NULL,
    `deskripsi`  TEXT,
    `harga`      DECIMAL(12, 2)  NOT NULL DEFAULT 0.00,
    `stok`       INT             NOT NULL DEFAULT 0,
    `gambar`     VARCHAR(255)    NOT NULL DEFAULT 'default.jpg'
                    COMMENT 'Nama file gambar saja, bukan URL penuh',
    `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Tabel: orders
-- Menyimpan data pesanan dan status pembayaran Midtrans
-- Dipakai oleh WebhookController untuk update status
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `orders` (
    `id`                  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`             INT UNSIGNED  NOT NULL,
    `order_id_midtrans`   VARCHAR(100)  UNIQUE COMMENT 'ID unik dari Midtrans, format: ORD-xxx',
    `total_harga`         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `snap_token`          VARCHAR(255)  COMMENT 'Token snap Midtrans untuk pembayaran',
    `status_pembayaran`   ENUM('pending', 'settlement', 'expire', 'cancel')
                          NOT NULL DEFAULT 'pending',
    `created_at`          TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_order_midtrans` (`order_id_midtrans`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Tabel: order_items (opsional — untuk detail item tiap order)
-- Disiapkan untuk Randi jika butuh detail per-item
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `order_items` (
    `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `order_id`     INT UNSIGNED  NOT NULL,
    `product_id`   INT UNSIGNED  NOT NULL,
    `qty`          INT           NOT NULL DEFAULT 1,
    `harga_satuan` DECIMAL(12,2) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_order_id` (`order_id`),
    INDEX `idx_product_id` (`product_id`),
    CONSTRAINT `fk_oi_order`   FOREIGN KEY (`order_id`)   REFERENCES `orders`(`id`)   ON DELETE CASCADE,
    CONSTRAINT `fk_oi_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- SEED DATA: Akun Admin Default
--
-- ⚠️  GANTI HASH DI BAWAH INI dengan hash baru yang di-generate
--     Caranya: jalankan PHP CLI:
--     php -r "echo password_hash('admin123', PASSWORD_BCRYPT);"
--     Lalu tempel hasilnya di bawah ini menggantikan $2y$...
-- ─────────────────────────────────────────────────────────────
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `role`) VALUES
(
    'admin',
    'admin@kopskuy.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    -- Hash di atas = password "password" (untuk development saja!)
    -- WAJIB ganti sebelum deploy ke production!
    'admin'
);

-- ─────────────────────────────────────────────────────────────
-- SEED DATA: Contoh Produk Kopi
-- Hapus bagian ini jika tidak butuh data contoh
-- ─────────────────────────────────────────────────────────────
INSERT IGNORE INTO `products` (`id`, `nama_kopi`, `deskripsi`, `harga`, `stok`, `gambar`) VALUES
(1, 'Arabika Gayo Premium', 'Kopi Arabika dari dataran tinggi Gayo, Aceh. Aroma floral yang kuat dengan cita rasa cokelat hitam dan sedikit fruity. Keasaman yang menyegarkan dan after-taste yang panjang.', 45000, 50, 'default.jpg'),
(2, 'Robusta Lampung Bold', 'Robusta pilihan dari Lampung dengan body yang kuat dan tebal. Cocok untuk espresso dan kopi susu. Rasa pahit yang nikmat dengan aroma earthy yang khas.', 30000, 80, 'default.jpg'),
(3, 'Mandheling Sumatera', 'Kopi Mandheling dari Sumatera Utara dengan proses basah. Full-body, rendah keasaman, dan rasa yang kompleks dengan nuansa cokelat, cedar, dan rempah-rempah.', 55000, 35, 'default.jpg'),
(4, 'Toraja Kalosi Special', 'Kopi Toraja dari pegunungan Sulawesi Selatan. Memiliki aroma dark chocolate, winy, dan after-taste yang panjang. Salah satu kopi single origin terbaik Indonesia.', 65000, 25, 'default.jpg');
