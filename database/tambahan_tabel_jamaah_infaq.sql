-- =========================================================
-- Tambahan tabel untuk fitur Login Jamaah & Infaq
-- Jalankan file ini di phpMyAdmin pada database db_masjid
-- =========================================================

CREATE TABLE IF NOT EXISTS jamaah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS infaq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_jamaah INT NOT NULL,
    nama_jamaah VARCHAR(100) NOT NULL,
    jenis_infaq VARCHAR(50) NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    pesan VARCHAR(255) DEFAULT NULL,
    tanggal DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_jamaah) REFERENCES jamaah(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
