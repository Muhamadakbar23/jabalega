-- =============================================
-- JABALEGA ADMIN - Setup Database
-- Jalankan file ini sekali di phpMyAdmin > SQL
-- =============================================

CREATE DATABASE IF NOT EXISTS `jabalega` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `jabalega`;

-- Tabel users (admin login)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin: username=admin, password=admin123
INSERT IGNORE INTO `users` (`username`, `password`, `nama_lengkap`)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Jabalega');

-- Tabel PT
CREATE TABLE IF NOT EXISTS `pt` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(150) NOT NULL,
  `nama_usaha` VARCHAR(150),
  `alamat` TEXT,
  `no_telp` VARCHAR(20),
  `link_gdrive` VARCHAR(500),
  `status` ENUM('Proses','Selesai','Pending','Dibatalkan') DEFAULT 'Proses',
  `catatan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel NIB
CREATE TABLE IF NOT EXISTS `nib` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(150) NOT NULL,
  `nama_usaha` VARCHAR(150),
  `alamat` TEXT,
  `no_telp` VARCHAR(20),
  `link_gdrive` VARCHAR(500),
  `status` ENUM('Proses','Selesai','Pending','Dibatalkan') DEFAULT 'Proses',
  `catatan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel PIRT
CREATE TABLE IF NOT EXISTS `pirt` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(150) NOT NULL,
  `nama_usaha` VARCHAR(150),
  `alamat` TEXT,
  `no_telp` VARCHAR(20),
  `link_gdrive` VARCHAR(500),
  `status` ENUM('Proses','Selesai','Pending','Dibatalkan') DEFAULT 'Proses',
  `catatan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel BPOM
CREATE TABLE IF NOT EXISTS `bpom` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(150) NOT NULL,
  `nama_usaha` VARCHAR(150),
  `alamat` TEXT,
  `no_telp` VARCHAR(20),
  `link_gdrive` VARCHAR(500),
  `status` ENUM('Proses','Selesai','Pending','Dibatalkan') DEFAULT 'Proses',
  `catatan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel Halal
CREATE TABLE IF NOT EXISTS `halal` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(150) NOT NULL,
  `nama_usaha` VARCHAR(150),
  `alamat` TEXT,
  `no_telp` VARCHAR(20),
  `link_gdrive` VARCHAR(500),
  `status` ENUM('Proses','Selesai','Pending','Dibatalkan') DEFAULT 'Proses',
  `catatan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel Merek (HAKI)
CREATE TABLE IF NOT EXISTS `merek` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(150) NOT NULL,
  `nama_usaha` VARCHAR(150),
  `alamat` TEXT,
  `no_telp` VARCHAR(20),
  `link_gdrive` VARCHAR(500),
  `status` ENUM('Proses','Selesai','Pending','Dibatalkan') DEFAULT 'Proses',
  `catatan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Contoh data dummy (opsional, hapus jika tidak perlu)
INSERT INTO `pt` (`nama`, `nama_usaha`, `alamat`, `no_telp`, `link_gdrive`, `status`) VALUES
('Budi Santoso', 'PT Maju Bersama', 'Jl. Sudirman No. 10, Bandung', '08123456789', 'https://drive.google.com/example1', 'Proses'),
('Siti Rahayu', 'PT Karya Nusantara', 'Jl. Asia Afrika No. 5, Bandung', '08987654321', 'https://drive.google.com/example2', 'Selesai');

INSERT INTO `nib` (`nama`, `nama_usaha`, `alamat`, `no_telp`, `link_gdrive`, `status`) VALUES
('Ahmad Fauzi', 'Toko Berkah Jaya', 'Jl. Cihampelas No. 20, Bandung', '08112233445', 'https://drive.google.com/example3', 'Proses');

INSERT INTO `pirt` (`nama`, `nama_usaha`, `alamat`, `no_telp`, `link_gdrive`, `status`) VALUES
('Dewi Kusuma', 'UMKM Snack Enak', 'Jl. Braga No. 15, Bandung', '08556677889', 'https://drive.google.com/example4', 'Pending');

INSERT INTO `halal` (`nama`, `nama_usaha`, `alamat`, `no_telp`, `link_gdrive`, `status`) VALUES
('Rizki Ramadhan', 'Katering Halal Berkah', 'Jl. Diponegoro No. 8, Bandung', '08223344556', '', 'Proses');

INSERT INTO `merek` (`nama`, `nama_usaha`, `alamat`, `no_telp`, `link_gdrive`, `status`) VALUES
('Linda Permata', 'Brand Cantik Indonesia', 'Jl. Merdeka No. 3, Bandung', '08778899001', 'https://drive.google.com/example5', 'Selesai');
