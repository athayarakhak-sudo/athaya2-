-- =========================================================
--  GUDANGKU - Sistem Informasi Penyimpanan Barang
--  File   : database.sql
--  Fungsi : Struktur database MySQL
-- ---------------------------------------------------------
--  Nama      : Athaya Rakha Khairullah
--  Kelas     : XI TKJ 4
--  No. Absen : 03
--  Server    : Linux / CentOS
-- =========================================================

CREATE DATABASE IF NOT EXISTS db_gudang_barang
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;

USE db_gudang_barang;

-- ---------------------------------------------------------
-- Tabel: users  (akun untuk login)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Catatan:
-- Akun admin default (username: admin / password: admin) akan
-- otomatis dibuat oleh aplikasi (config.php) saat pertama kali
-- diakses, dengan password yang sudah di-hash otomatis oleh PHP.

-- ---------------------------------------------------------
-- Tabel: kategori (kategori barang)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS kategori (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO kategori (nama_kategori) VALUES
('Elektronik'),
('Alat Tulis Kantor'),
('Alat Kebersihan'),
('Furnitur'),
('Peralatan Jaringan');

-- ---------------------------------------------------------
-- Tabel: barang (data utama barang di penyimpanan/gudang)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS barang (
    id INT(11) NOT NULL AUTO_INCREMENT,
    kode_barang VARCHAR(30) NOT NULL UNIQUE,
    nama_barang VARCHAR(150) NOT NULL,
    kategori_id INT(11) DEFAULT NULL,
    jumlah INT(11) NOT NULL DEFAULT 0,
    satuan VARCHAR(30) NOT NULL DEFAULT 'Pcs',
    lokasi_rak VARCHAR(50) DEFAULT NULL,
    kondisi ENUM('Baik','Rusak Ringan','Rusak Berat') NOT NULL DEFAULT 'Baik',
    foto VARCHAR(255) DEFAULT NULL,
    keterangan TEXT,
    tanggal_masuk DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY fk_kategori (kategori_id),
    CONSTRAINT fk_barang_kategori FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Contoh data barang (opsional, boleh dihapus)
-- ---------------------------------------------------------
INSERT INTO barang (kode_barang, nama_barang, kategori_id, jumlah, satuan, lokasi_rak, kondisi, foto, keterangan, tanggal_masuk) VALUES
('BRG-0001', 'Laptop Lenovo ThinkPad', 1, 5, 'Unit', 'Rak A1', 'Baik', NULL, 'Digunakan untuk praktik siswa', '2026-01-10'),
('BRG-0002', 'Switch TP-Link 24 Port', 5, 3, 'Unit', 'Rak B2', 'Baik', NULL, 'Perangkat jaringan lab TKJ', '2026-02-15'),
('BRG-0003', 'Kabel UTP Cat6', 5, 20, 'Roll', 'Rak B3', 'Baik', NULL, 'Stok untuk praktik kabel jaringan', '2026-03-01'),
('BRG-0004', 'Kursi Kantor', 4, 10, 'Unit', 'Rak C1', 'Rusak Ringan', NULL, 'Perlu perbaikan pada roda kursi', '2025-11-20'),
('BRG-0005', 'Sapu Lantai', 3, 8, 'Pcs', 'Gudang Belakang', 'Baik', NULL, 'Alat kebersihan ruang lab', '2026-04-05');
