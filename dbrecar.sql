-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Nov 2025 pada 08.06
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbrecar`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_laporan`
--

CREATE TABLE `tbl_laporan` (
  `id_laporan` int(11) NOT NULL,
  `id_sewa` varchar(10) DEFAULT NULL,
  `id_mobil` varchar(10) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `tanggal_sewa` date DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `total_bayar` decimal(12,2) DEFAULT NULL,
  `denda` decimal(12,2) DEFAULT NULL,
  `total_pendapatan` decimal(12,2) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_laporan`
--

INSERT INTO `tbl_laporan` (`id_laporan`, `id_sewa`, `id_mobil`, `id_user`, `tanggal_sewa`, `tanggal_kembali`, `total_bayar`, `denda`, `total_pendapatan`, `keterangan`, `tanggal_dibuat`) VALUES
(1, 'SW00001', 'MBL00001', 3, '2025-11-05', '2025-11-05', 2450000.00, 0.00, 2450000.00, 'Pengembalian selesai. Kondisi: Baik', '2025-11-05 03:50:45'),
(2, 'SW00002', 'MBL00005', 0, '2025-11-05', '2025-11-05', 3140000.00, 500000.00, 3140000.00, 'Pengembalian selesai. Kondisi: Ada Lecet', '2025-11-05 06:30:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_mobil`
--

CREATE TABLE `tbl_mobil` (
  `id_mobil` varchar(10) NOT NULL,
  `nama_mobil` varchar(100) NOT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `kursi` int(11) DEFAULT NULL,
  `harga_per_hari` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('Tersedia','Disewa') DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_mobil`
--

INSERT INTO `tbl_mobil` (`id_mobil`, `nama_mobil`, `jenis`, `kursi`, `harga_per_hari`, `foto`, `status`) VALUES
('MBL00001', 'Toyota Avanza', 'MPV', 7, 350000, 'avanza.jpg', 'Tersedia'),
('MBL00002', 'Toyota Innova', 'MPV Premium', 7, 500000, 'innova.jpg', 'Tersedia'),
('MBL00003', 'Honda Brio', 'City Car', 5, 250000, 'brio.jpg', 'Tersedia'),
('MBL00004', 'Mitsubishi Pajero Sport', 'SUV', 7, 650000, 'pajero.jpg', 'Tersedia'),
('MBL00005', 'Daihatsu Xenia', 'MPV', 7, 330000, 'xenia.jpg', 'Tersedia'),
('MBL00006', 'Suzuki Ertiga', 'MPV', 7, 340000, 'ertiga.jpg', 'Tersedia'),
('MBL00007', 'Honda HR-V', 'SUV', 5, 450000, 'hrv.jpg', 'Tersedia'),
('MBL00008', 'Toyota Alphard', 'Luxury MPV', 7, 1200000, 'alphard.jpg', 'Tersedia');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_pembayaran`
--

CREATE TABLE `tbl_pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_sewa` varchar(10) NOT NULL,
  `metode` enum('Transfer Bank','Tunai Saat Pengambilan') DEFAULT NULL,
  `jumlah_bayar` decimal(12,2) NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `status_pembayaran` enum('Pending','Sudah Dibayar','Dibatalkan') DEFAULT 'Pending',
  `tanggal_bayar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_pembayaran`
--

INSERT INTO `tbl_pembayaran` (`id_pembayaran`, `id_sewa`, `metode`, `jumlah_bayar`, `bukti_bayar`, `status_pembayaran`, `tanggal_bayar`) VALUES
(1, 'SW00001', 'Transfer Bank', 2450000.00, 'bukti_SW00001.png', 'Sudah Dibayar', '2025-11-05 08:27:37'),
(2, 'SW00002', 'Transfer Bank', 2640000.00, 'bukti_SW00002.png', 'Sudah Dibayar', '2025-11-05 11:04:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_pengembalian`
--

CREATE TABLE `tbl_pengembalian` (
  `id_pengembalian` int(11) NOT NULL,
  `id_sewa` varchar(10) NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `kondisi_mobil` text DEFAULT NULL,
  `denda` decimal(12,2) DEFAULT 0.00,
  `total_bayar` decimal(12,2) DEFAULT 0.00,
  `status_pengembalian` varchar(100) NOT NULL,
  `catatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_pengembalian`
--

INSERT INTO `tbl_pengembalian` (`id_pengembalian`, `id_sewa`, `tanggal_kembali`, `kondisi_mobil`, `denda`, `total_bayar`, `status_pengembalian`, `catatan`) VALUES
(2, 'SW00001', '2025-11-05', 'Baik', 0.00, 2450000.00, 'Selesai', ''),
(3, 'SW00002', '2025-11-05', 'Ada Lecet', 500000.00, 3140000.00, 'Selesai', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_pesan`
--

CREATE TABLE `tbl_pesan` (
  `id_pesan` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `subjek` varchar(150) DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_sewa`
--

CREATE TABLE `tbl_sewa` (
  `id_sewa` varchar(10) NOT NULL,
  `id_mobil` varchar(10) NOT NULL,
  `nama_penyewa` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat_awal` text DEFAULT NULL,
  `alamat_tujuan` text DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `total_harga` decimal(12,2) DEFAULT NULL,
  `denda` decimal(12,2) DEFAULT 0.00,
  `metode_bayar` enum('Transfer Bank','Tunai Saat Pengambilan') DEFAULT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status_pembayaran` enum('Pending','Sudah Dibayar','Dibatalkan') DEFAULT 'Pending',
  `status` enum('Pending','Disewa','Selesai','Dibatalkan') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_sewa`
--

INSERT INTO `tbl_sewa` (`id_sewa`, `id_mobil`, `nama_penyewa`, `email`, `no_telp`, `alamat_awal`, `alamat_tujuan`, `tanggal_mulai`, `tanggal_selesai`, `total_harga`, `denda`, `metode_bayar`, `bukti_transfer`, `status_pembayaran`, `status`, `created_at`) VALUES
('SW00001', 'MBL00001', 'jafar', 'efgh@gmail.com', '84673485634', 'pekalongan', 'batang', '2025-11-05', '2025-11-12', 2450000.00, 0.00, 'Transfer Bank', NULL, 'Sudah Dibayar', 'Selesai', '2025-11-05 01:03:29'),
('SW00002', 'MBL00005', 'jafar', 'efgh@gmail.com', '84673485634', 'pekalongan', 'tegal', '2025-11-05', '2025-11-13', 2640000.00, 0.00, 'Transfer Bank', NULL, 'Sudah Dibayar', 'Selesai', '2025-11-05 04:03:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(225) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'petugas',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `no_hp`, `role`, `created_at`, `updated_at`) VALUES
(1, 'aku', '1234', 'Aku', 'aku@gmail.com', '0983945237', 'pengguna', '2025-10-01 11:35:50', '2025-10-01 11:35:50'),
(2, 'admin', 'admin', 'Administrator', 'admin@gmail.com', '236473429', 'admin', '2025-10-03 10:37:19', '2025-10-03 10:38:13'),
(3, 'jafar', '$2y$10$mjzOb.GNzMmOLTmYYkwHre5QHZ4pVl4dR44ex2krn32UF0oZJiuKG', 'jafar', 'efgh@gmail.com', '84673485634', 'pengguna', '2025-11-05 07:44:22', '2025-11-05 07:44:22'),
(4, 'admin2', '$2y$10$YqzTi5jBWWQwmDPGvNUaMu7WlrNNnB3uItdZf6/Pfpo6Oq9nJlIrq', 'admin', 'admin@gmail.com', '0983945237', 'admin', '2025-11-05 08:04:45', '2025-11-05 08:05:11');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbl_laporan`
--
ALTER TABLE `tbl_laporan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indeks untuk tabel `tbl_mobil`
--
ALTER TABLE `tbl_mobil`
  ADD PRIMARY KEY (`id_mobil`);

--
-- Indeks untuk tabel `tbl_pembayaran`
--
ALTER TABLE `tbl_pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`);

--
-- Indeks untuk tabel `tbl_pengembalian`
--
ALTER TABLE `tbl_pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`);

--
-- Indeks untuk tabel `tbl_pesan`
--
ALTER TABLE `tbl_pesan`
  ADD PRIMARY KEY (`id_pesan`);

--
-- Indeks untuk tabel `tbl_sewa`
--
ALTER TABLE `tbl_sewa`
  ADD PRIMARY KEY (`id_sewa`);

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
-- AUTO_INCREMENT untuk tabel `tbl_laporan`
--
ALTER TABLE `tbl_laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tbl_pembayaran`
--
ALTER TABLE `tbl_pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tbl_pengembalian`
--
ALTER TABLE `tbl_pengembalian`
  MODIFY `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tbl_pesan`
--
ALTER TABLE `tbl_pesan`
  MODIFY `id_pesan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
