-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 06:26 AM
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
-- Database: `db_lecture_struk_belanja`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDetailTransaksi2` (IN `transid_in` VARCHAR(20))   BEGIN
    SELECT 
        d.DetailID,
        d.TransID,
        p.ProductName,
        d.Qty,
        d.Subtotal
    FROM DetailTransaksi d
    INNER JOIN Produk p ON p.ProductID = d.ProductID
    WHERE d.TransID = transid_in;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `HitungPPN` (`nominal` INT) RETURNS INT(11)  BEGIN
    RETURN nominal * 0.11;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `detailtransaksi`
--

CREATE TABLE `detailtransaksi` (
  `DetailID` int(11) NOT NULL,
  `TransID` varchar(20) DEFAULT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `Qty` int(11) DEFAULT NULL,
  `HargaSatuan` int(11) DEFAULT NULL,
  `Subtotal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detailtransaksi`
--

INSERT INTO `detailtransaksi` (`DetailID`, `TransID`, `ProductID`, `Qty`, `HargaSatuan`, `Subtotal`) VALUES
(1, '004-130106001', 1, 1, 5350, 5350),
(2, '004-130106001', 2, 1, 9890, 9890),
(3, '004-130106001', 3, 1, 9200, 9200),
(4, '004-130106001', 4, 1, 2600, 2600),
(5, '004-130106001', 5, 1, 6900, 6900),
(10, '003-251216001', 6, 10, 3500, 35000),
(11, '003-251216001', 5, 1, 6900, 6900);

--
-- Triggers `detailtransaksi`
--
DELIMITER $$
CREATE TRIGGER `trg_subtotal` BEFORE INSERT ON `detailtransaksi` FOR EACH ROW BEGIN
    SET NEW.Subtotal = NEW.Qty * NEW.HargaSatuan;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `kasir`
--

CREATE TABLE `kasir` (
  `KasirID` varchar(10) NOT NULL,
  `KasirNama` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kasir`
--

INSERT INTO `kasir` (`KasirID`, `KasirNama`) VALUES
('003', 'Bambang Telo'),
('004', 'Anggun Laila'),
('005', 'Aurora Cahyani'),
('006', 'Siti Aminah'),
('007', 'Rizky Pratama');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `ProductID` int(11) NOT NULL,
  `ProductName` varchar(100) DEFAULT NULL,
  `HargaSatuan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`ProductID`, `ProductName`, `HargaSatuan`) VALUES
(1, 'AMANIA MARGARINE 200G', 5350),
(2, 'SADRI SAUS LADA HITAM 163ML', 9890),
(3, 'DELMONTE BARBEQUE SAUCE POUCH 250G', 9200),
(4, 'SADRI SAUS TIRAM SACHET TS23 23ML', 2600),
(5, 'GJT SUMPIT BAMBU PACK', 6900),
(6, 'INDOMIE GORENG 85G', 3500);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `TransID` varchar(20) NOT NULL,
  `KasirID` varchar(10) DEFAULT NULL,
  `Tanggal` datetime DEFAULT NULL,
  `Total` int(11) DEFAULT NULL,
  `Potongan` int(11) DEFAULT NULL,
  `Tunai` int(11) DEFAULT NULL,
  `Kembali` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`TransID`, `KasirID`, `Tanggal`, `Total`, `Potongan`, `Tunai`, `Kembali`) VALUES
('003-251216001', '003', '2025-12-16 10:29:25', 41900, 10000, 50000, 18100),
('004-130106001', '004', '2025-11-16 11:30:11', 33940, 40, 100000, 66100);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  ADD PRIMARY KEY (`DetailID`),
  ADD KEY `TransID` (`TransID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `kasir`
--
ALTER TABLE `kasir`
  ADD PRIMARY KEY (`KasirID`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`ProductID`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`TransID`),
  ADD KEY `KasirID` (`KasirID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  MODIFY `DetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  ADD CONSTRAINT `detailtransaksi_ibfk_1` FOREIGN KEY (`TransID`) REFERENCES `transaksi` (`TransID`),
  ADD CONSTRAINT `detailtransaksi_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `produk` (`ProductID`);

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`KasirID`) REFERENCES `kasir` (`KasirID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
