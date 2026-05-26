-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Bulan Mei 2026 pada 03.10
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------
-- Membuat Database jabalega
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `jabalega` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `jabalega`;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jabalega`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bpom`
--

CREATE TABLE `bpom` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` varchar(50) NOT NULL,
  `nama_usaha` text NOT NULL,
  `alamat` text NOT NULL,
  `no_telefon` int(11) NOT NULL,
  `link_gdrive` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `halal`
--

CREATE TABLE `halal` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` varchar(50) NOT NULL,
  `nama_usaha` text NOT NULL,
  `alamat` text NOT NULL,
  `no_telefon` int(11) NOT NULL,
  `link_gdrive` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `merek`
--

CREATE TABLE `merek` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` varchar(50) NOT NULL,
  `nama_usaha` text NOT NULL,
  `alamat` text NOT NULL,
  `no_telefon` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `nib`
--

CREATE TABLE `nib` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` varchar(50) NOT NULL,
  `nama_usaha` text NOT NULL,
  `alamat` text NOT NULL,
  `no_telefon` int(11) NOT NULL,
  `link_gdrive` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pirt`
--

CREATE TABLE `pirt` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` varchar(50) NOT NULL,
  `nama_usaha` text NOT NULL,
  `alamat` text NOT NULL,
  `no_telefon` int(11) NOT NULL,
  `link_gdrive` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `psat`
--

CREATE TABLE `psat` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` varchar(50) NOT NULL,
  `nama_usaha` text NOT NULL,
  `izin` varchar(100) NOT NULL,
  `no_telefon` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `link_gdrive` text NOT NULL,
  `tanggal_terbit` date NOT NULL,
  `masa_berlaku` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `izin_lainnya`
--

CREATE TABLE `izin_lainnya` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` varchar(50) NOT NULL,
  `nama_usaha` text NOT NULL,
  `izin` varchar(100) NOT NULL,
  `no_telefon` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `link_gdrive` text NOT NULL,
  `tanggal_terbit` date NOT NULL,
  `masa_berlaku` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pt`
--

CREATE TABLE `pt` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` varchar(50) NOT NULL,
  `nama_usaha` text NOT NULL,
  `alamat` text NOT NULL,
  `no_telefon` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pt`
--

INSERT INTO `pt` (`nama`, `nama_usaha`, `alamat`, `no_telefon`) VALUES
('vghvhvj', 'bvhjvkiubgi', 'vgjv jhvk', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `created_at`) VALUES
(1, 'admin', '$2y$10$PZvbRhTMIUaP4u4CF7sTl.ygcVyyZXag3foRb24sTyQO/SrpXewvK', 'akbar angin', '2026-04-12 03:42:56'),
(4, 'admin2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator Jabalega', '2026-04-12 03:46:45'),
(5, 'admin123', 'admin458', 'muhamad_akbar', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
