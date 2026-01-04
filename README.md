[Uploading simba (1).sql…]()
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2026 at 05:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simba`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama`, `username`, `password`, `created_at`) VALUES
(3, 'Maman Abdullah', 'Maman', '$2y$10$0EK.vYnBpnTfZRbb.pfKG.BYktziWEpU40v2.anZImnU9JVWVJ1.O', '2026-01-03 07:39:45'),
(4, 'Test Admin', 'testadmin', '$2y$10$zRnt.RqKWG8Of309GUbDFuFtA5RoZ7i4x8KG5T52wlcdiyCYgpevu', '2026-01-03 08:25:19'),
(5, 'Ahmad Bustomi', 'Ahmad', '$2y$10$QTLcmWvLrC7bR65YLWcY0.HOSakUz9WGpe4beBRCUKm5oMqJa9Nt2', '2026-01-04 15:11:19');

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `kode_barang` varchar(20) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `kode_barang`, `nama_barang`, `stok`, `deskripsi`, `created_at`) VALUES
(1, 'BRG-001', 'Terminal', 22, '', '2026-01-03 07:57:03'),
(2, 'BRG-002', 'Proyektor', 2, '', '2026-01-03 08:31:37'),
(3, 'BRG-003', 'Jam Dinding', 3, '', '2026-01-03 08:34:58'),
(5, 'BRG-004', 'Printer', 0, '', '2026-01-03 15:21:18'),
(6, 'BRG-005', 'Charger Type C', 10, '', '2026-01-03 15:21:48'),
(7, 'BRG-006', 'Charger Micro', 10, '', '2026-01-03 15:22:19'),
(8, 'BRG-007', 'Papan Tulis Besar', 10, '', '2026-01-03 15:22:39'),
(9, 'BRG-008', 'Papan Tulis Kecil', 20, '', '2026-01-03 15:23:07'),
(10, 'BRG-009', 'Meja Kecil', 10, '', '2026-01-03 15:23:24'),
(11, 'BRG-010', 'Spidol', 19, 'tinta permanen', '2026-01-03 15:24:12');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_pinjam` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tgl_pinjam` date NOT NULL,
  `tgl_kembali` date NOT NULL,
  `status` enum('dipinjam','dikembalikan') DEFAULT 'dipinjam',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_pinjam`, `id_user`, `id_barang`, `jumlah`, `tgl_pinjam`, `tgl_kembali`, `status`, `created_at`) VALUES
(1, 4, 1, 2, '2026-01-03', '2026-01-03', 'dikembalikan', '2026-01-03 08:06:06'),
(2, 4, 3, 1, '2026-01-03', '2026-01-03', 'dikembalikan', '2026-01-03 08:39:44'),
(3, 5, 1, 2, '2026-01-03', '2028-11-11', 'dipinjam', '2026-01-03 09:39:43'),
(4, 4, 3, 1, '2026-01-03', '2026-01-03', 'dikembalikan', '2026-01-03 15:20:15'),
(5, 4, 7, 2, '2026-01-03', '2222-02-22', 'dikembalikan', '2026-01-03 15:36:33'),
(6, 4, 11, 11, '2026-01-03', '2222-11-11', 'dikembalikan', '2026-01-03 15:36:48'),
(7, 4, 9, 2, '2026-01-03', '2222-02-22', 'dikembalikan', '2026-01-03 15:37:03'),
(8, 4, 5, 1, '2026-01-03', '2026-01-03', 'dikembalikan', '2026-01-03 15:59:57'),
(9, 4, 3, 1, '2026-01-03', '2026-01-03', 'dikembalikan', '2026-01-03 16:30:58'),
(10, 4, 7, 3, '2026-01-04', '2026-01-04', 'dikembalikan', '2026-01-04 08:12:00'),
(11, 4, 5, 1, '2026-01-04', '2026-01-05', 'dipinjam', '2026-01-04 16:39:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `username`, `password`, `created_at`) VALUES
(4, 'Abdul Hasyim', 'A Hasyim', '$2y$10$i5xyavxsr1DvZcH79KySwuvBegOdQCV6/2J7vdxg1Xph4tumRaNHq', '2026-01-03 07:39:45'),
(5, 'Rahmat', 'Rahmat', '$2y$10$WHXTNpixJn870C3fvOcRqeknMFuN2bWrb.NMxesgxXHaCcAeTII8K', '2026-01-03 09:39:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD UNIQUE KEY `kode_barang` (`kode_barang`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_pinjam`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_pinjam` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `fk_pinjam_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pinjam_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
