-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 06, 2025 at 07:49 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gudang`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang`
--

CREATE TABLE `tbl_barang` (
  `id_barang` varchar(5) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `jenis` int NOT NULL,
  `stok_minimum` int NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `satuan` int NOT NULL,
  `id_rak` int DEFAULT NULL,
  `id_keranjang` int DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_barang`
--

INSERT INTO `tbl_barang` (`id_barang`, `nama_barang`, `serial_number`, `jenis`, `stok_minimum`, `stok`, `satuan`, `id_rak`, `id_keranjang`, `foto`) VALUES
('B0001', 'modem', '0987908668', 5, 1, 12, 1, 3, NULL, '45262b6ddc63b39c448a2b4a0fc6bd34669af3cf.png');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang_keluar`
--

CREATE TABLE `tbl_barang_keluar` (
  `id` int NOT NULL,
  `id_transaksi` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `tanggal_pengembalian` date DEFAULT NULL,
  `barang` varchar(5) NOT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `pemohon` varchar(100) DEFAULT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `id_rak` int DEFAULT NULL,
  `id_keranjang` int DEFAULT NULL,
  `status` enum('Menunggu Persetujuan','Disetujui','Ditolak') DEFAULT 'Menunggu Persetujuan',
  `tanggal_persetujuan` datetime DEFAULT NULL,
  `tanggal_penolakan` datetime DEFAULT NULL,
  `kondisi` varchar(50) DEFAULT NULL,
  `catatan` text,
  `created_by` int DEFAULT NULL,
  `foto_pengembalian` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_barang_keluar`
--

INSERT INTO `tbl_barang_keluar` (`id`, `id_transaksi`, `tanggal`, `tanggal_pengembalian`, `barang`, `serial_number`, `jumlah`, `pemohon`, `jenis`, `id_rak`, `id_keranjang`, `status`, `tanggal_persetujuan`, `tanggal_penolakan`, `kondisi`, `catatan`, `foto_pengembalian`) VALUES
(100, 'TK-0000001', '2025-09-04', NULL, 'B0001', '089797779-01', 1, NULL, 'Keluar', 3, NULL, 'Menunggu Persetujuan', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_barang_masuk`
--

CREATE TABLE `tbl_barang_masuk` (
  `id_transaksi` varchar(10) NOT NULL,
  `tanggal` date NOT NULL,
  `barang` varchar(5) NOT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `id_rak` int DEFAULT NULL,
  `id_keranjang` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_barang_masuk`
--

INSERT INTO `tbl_barang_masuk` (`id_transaksi`, `tanggal`, `barang`, `serial_number`, `jumlah`, `id_rak`, `id_keranjang`) VALUES
('TM-0000001', '2025-09-04', 'B0001', '089797779', 12, 3, NULL);

--
-- Triggers `tbl_barang_masuk`
--
DELIMITER $$
CREATE TRIGGER `hapus_stok_masuk` BEFORE DELETE ON `tbl_barang_masuk` FOR EACH ROW BEGIN
UPDATE tbl_barang SET stok=stok-OLD.jumlah
WHERE id_barang=OLD.barang;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `stok_masuk` AFTER INSERT ON `tbl_barang_masuk` FOR EACH ROW BEGIN
UPDATE tbl_barang SET stok=stok+NEW.jumlah
WHERE id_barang=NEW.barang;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_jenis`
--

CREATE TABLE `tbl_jenis` (
  `id_jenis` int NOT NULL,
  `nama_jenis` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_jenis`
--

INSERT INTO `tbl_jenis` (`id_jenis`, `nama_jenis`) VALUES
(1, 'Minuman'),
(3, 'Elektronik'),
(5, 'MODEM');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_keranjang`
--

CREATE TABLE `tbl_keranjang` (
  `id_keranjang` int NOT NULL,
  `kode_keranjang` varchar(10) NOT NULL,
  `nama_keranjang` varchar(100) NOT NULL,
  `id_rak` int DEFAULT NULL,
  `kapasitas` int DEFAULT '0',
  `kondisi` varchar(50) DEFAULT 'Baik',
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_keranjang`
--

INSERT INTO `tbl_keranjang` (`id_keranjang`, `kode_keranjang`, `nama_keranjang`, `id_rak`, `kapasitas`, `kondisi`, `keterangan`) VALUES
(3, 'K003', 'Keranjang B1', 2, 15, 'Baik', 'Keranjang untuk kabel'),
(4, 'K004', 'Keranjang B2', 2, 15, 'Baik', 'keranjang buat barang');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rak`
--

CREATE TABLE `tbl_rak` (
  `id_rak` int NOT NULL,
  `kode_rak` varchar(10) NOT NULL,
  `nama_rak` varchar(100) NOT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  `kapasitas` int DEFAULT '0',
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_rak`
--

INSERT INTO `tbl_rak` (`id_rak`, `kode_rak`, `nama_rak`, `lokasi`, `kapasitas`, `keterangan`) VALUES
(2, 'R002', 'Rak Utama 2', 'Lantai 1', 50, ''),
(3, 'R003', 'Rak Cadangan', 'Lantai 2', 30, ''),
(4, 'RO01', 'Rak Utama 1', 'Lantai 1', 100, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_satuan`
--

CREATE TABLE `tbl_satuan` (
  `id_satuan` int NOT NULL,
  `nama_satuan` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_satuan`
--

INSERT INTO `tbl_satuan` (`id_satuan`, `nama_satuan`) VALUES
(1, 'pcs'),
(2, 'box'),
(3, 'Meter'),
(4, 'Kg'),
(5, 'Cm');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_serial_inventory`
--

CREATE TABLE `tbl_serial_inventory` (
  `id` int NOT NULL,
  `id_barang` varchar(5) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `status` enum('Available','Reserved','Used','Returned') DEFAULT 'Available',
  `reserved_for_transaction` varchar(10) DEFAULT NULL,
  `used_in_transaction` varchar(10) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_serial_inventory`
--

INSERT INTO `tbl_serial_inventory` (`id`, `id_barang`, `serial_number`, `status`, `reserved_for_transaction`, `used_in_transaction`, `created_date`, `updated_date`) VALUES
(14, 'B0004', '364828648', 'Used', NULL, 'TK-0000079', '2025-08-28 06:22:53', '2025-08-28 06:23:22'),
(22, 'B0004', '876368163', 'Used', NULL, 'TK-0000072', '2025-08-28 06:59:08', '2025-08-28 06:59:39'),
(23, 'B0004', '4752224002136', 'Used', NULL, 'TK-0000072', '2025-08-28 07:03:14', '2025-08-28 07:03:53'),
(24, 'B0004', '7747828624', 'Used', NULL, 'TK-0000075', '2025-08-28 07:33:15', '2025-08-29 01:27:20'),
(25, 'B0004', '764728864', 'Used', NULL, 'TK-0000077', '2025-08-28 07:56:45', '2025-08-29 01:07:23'),
(26, 'B0004', '38287737323', 'Used', NULL, 'TK-0000080', '2025-08-29 01:28:06', '2025-08-29 01:29:06'),
(27, 'B0004', '738764827378', 'Used', NULL, 'TK-0000082', '2025-08-30 02:06:49', '2025-08-30 02:07:12'),
(28, 'B0004', '273648273', 'Used', NULL, 'TK-0000083', '2025-08-30 02:07:43', '2025-08-30 02:08:02'),
(29, 'B0004', '129102100', 'Available', NULL, NULL, '2025-09-03 01:38:47', '2025-09-03 01:38:47'),
(30, 'B0001', '089797779-01', 'Reserved', 'TK-0000001', NULL, '2025-09-04 01:07:01', '2025-09-04 02:31:59'),
(31, 'B0001', '089797779-02', 'Available', NULL, NULL, '2025-09-04 01:07:01', '2025-09-04 01:07:01'),
(32, 'B0001', '089797779-03', 'Available', NULL, NULL, '2025-09-04 01:07:01', '2025-09-04 01:07:01'),
(33, 'B0001', '089797779-04', 'Available', NULL, NULL, '2025-09-04 01:07:01', '2025-09-04 01:07:01'),
(34, 'B0001', '089797779-05', 'Available', NULL, NULL, '2025-09-04 01:07:01', '2025-09-04 01:07:01'),
(35, 'B0001', '089797779-06', 'Available', NULL, NULL, '2025-09-04 01:07:01', '2025-09-04 01:07:01'),
(36, 'B0001', '089797779-07', 'Available', NULL, NULL, '2025-09-04 01:07:01', '2025-09-04 01:07:01'),
(37, 'B0001', '089797779-08', 'Available', NULL, NULL, '2025-09-04 01:07:01', '2025-09-04 01:07:01'),
(38, 'B0001', '089797779-09', 'Available', NULL, NULL, '2025-09-04 01:07:01', '2025-09-04 01:07:01'),
(39, 'B0001', '089797779-10', 'Available', NULL, NULL, '2025-09-04 01:07:01', '2025-09-04 01:07:01'),
(40, 'B0001', '089797779-11', 'Available', NULL, NULL, '2025-09-04 01:07:01', '2025-09-04 01:07:01'),
(41, 'B0001', '089797779-12', 'Available', NULL, NULL, '2025-09-04 01:07:01', '2025-09-04 01:07:01');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` int NOT NULL,
  `nama_user` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `hak_akses` enum('Administrator','Admin Gudang','Kepala Gudang','User','Teknisi') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `nama_user`, `username`, `email`, `password`, `foto_profil`, `hak_akses`) VALUES
(1, 'Admin', 'administrator', 'administrator@gudang.com', '$2y$12$Yi/I5f1jPoQNQnh6lWoVfuz.RtZ3OHcKN6PU.I62P0fYK1tJ7xMRi', NULL, 'Administrator'),
(2, 'Admin Gudang', 'admin gudang', 'admin_gudang@gudang.com', '$2y$12$BeRYh13zfPXej97VgcfeNucYJGTElha5sRyIUQm1278D2u2Aqf6DS', NULL, 'Admin Gudang'),
(3, 'Kepala Gudang', 'kepala gudang', 'kepala_gudang@gudang.com', '$2y$12$odXcPs.RLJJH6Ghv3s42c.5zg5qAOz/S3Adr0lXGNcVSJ6f1hHS6G', NULL, 'Kepala Gudang'),
(4, 'teknisi', 'teknisi', 'teknisi@gudang.com', '$2y$12$UeGjpw3hEKiiKMtqEun6n.Rv3ls5RjtLV5F4ckNYWVx63yxTkO78C', NULL, 'Teknisi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_barang`
--
ALTER TABLE `tbl_barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `fk_barang_rak` (`id_rak`),
  ADD KEY `fk_barang_keranjang` (`id_keranjang`);

--
-- Indexes for table `tbl_barang_keluar`
--
ALTER TABLE `tbl_barang_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `fk_barang_keluar_rak` (`id_rak`),
  ADD KEY `fk_barang_keluar_keranjang` (`id_keranjang`),
  ADD KEY `fk_barang_keluar_user` (`created_by`);

--
-- Indexes for table `tbl_barang_masuk`
--
ALTER TABLE `tbl_barang_masuk`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_barang_masuk_rak` (`id_rak`),
  ADD KEY `fk_barang_masuk_keranjang` (`id_keranjang`);

--
-- Indexes for table `tbl_jenis`
--
ALTER TABLE `tbl_jenis`
  ADD PRIMARY KEY (`id_jenis`);

--
-- Indexes for table `tbl_keranjang`
--
ALTER TABLE `tbl_keranjang`
  ADD PRIMARY KEY (`id_keranjang`),
  ADD UNIQUE KEY `kode_keranjang` (`kode_keranjang`),
  ADD KEY `fk_rak` (`id_rak`);

--
-- Indexes for table `tbl_rak`
--
ALTER TABLE `tbl_rak`
  ADD PRIMARY KEY (`id_rak`),
  ADD UNIQUE KEY `kode_rak` (`kode_rak`);

--
-- Indexes for table `tbl_satuan`
--
ALTER TABLE `tbl_satuan`
  ADD PRIMARY KEY (`id_satuan`);

--
-- Indexes for table `tbl_serial_inventory`
--
ALTER TABLE `tbl_serial_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_serial` (`serial_number`),
  ADD KEY `idx_id_barang` (`id_barang`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_barang_keluar`
--
ALTER TABLE `tbl_barang_keluar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `tbl_jenis`
--
ALTER TABLE `tbl_jenis`
  MODIFY `id_jenis` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_keranjang`
--
ALTER TABLE `tbl_keranjang`
  MODIFY `id_keranjang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_rak`
--
ALTER TABLE `tbl_rak`
  MODIFY `id_rak` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_satuan`
--
ALTER TABLE `tbl_satuan`
  MODIFY `id_satuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_serial_inventory`
--
ALTER TABLE `tbl_serial_inventory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_barang`
--
ALTER TABLE `tbl_barang`
  ADD CONSTRAINT `fk_barang_keranjang` FOREIGN KEY (`id_keranjang`) REFERENCES `tbl_keranjang` (`id_keranjang`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_rak` FOREIGN KEY (`id_rak`) REFERENCES `tbl_rak` (`id_rak`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_barang_keluar`
--
ALTER TABLE `tbl_barang_keluar`
  ADD CONSTRAINT `fk_barang_keluar_keranjang` FOREIGN KEY (`id_keranjang`) REFERENCES `tbl_keranjang` (`id_keranjang`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_keluar_rak` FOREIGN KEY (`id_rak`) REFERENCES `tbl_rak` (`id_rak`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_keluar_user` FOREIGN KEY (`created_by`) REFERENCES `tbl_user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_barang_masuk`
--
ALTER TABLE `tbl_barang_masuk`
  ADD CONSTRAINT `fk_barang_masuk_keranjang` FOREIGN KEY (`id_keranjang`) REFERENCES `tbl_keranjang` (`id_keranjang`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_barang_masuk_rak` FOREIGN KEY (`id_rak`) REFERENCES `tbl_rak` (`id_rak`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_keranjang`
--
ALTER TABLE `tbl_keranjang`
  ADD CONSTRAINT `tbl_keranjang_ibfk_1` FOREIGN KEY (`id_rak`) REFERENCES `tbl_rak` (`id_rak`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
