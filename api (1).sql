-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2022 at 07:01 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `api`
--

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `brand_name`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'BREAND AP', '2022-03-21 23:41:52', '2022-03-22 17:20:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `designator`
--

CREATE TABLE `designator` (
  `designator_id` int(11) NOT NULL,
  `designator_code` varchar(255) NOT NULL,
  `designator_desc` text NOT NULL,
  `product_id` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `designator`
--

INSERT INTO `designator` (`designator_id`, `designator_code`, `designator_desc`, `product_id`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'DC-OF-SM-12D', 'Pengadaan dan pemasangan Kabel Duct Fiber Optik Single Mode 12 core G 652 D', 1, '2022-03-21 23:43:42', '2022-03-22 18:53:25', NULL),
(2, 'DC-OF-SM-24D', 'Pengadaan dan pemasangan Kabel Duct Fiber Optik Single Mode 24 core G 652 D', 2, '2022-03-22 16:00:49', '2022-03-22 18:53:56', NULL),
(3, 'DC-OF-SM-96D', 'Pengadaan dan pemasangan Kabel Duct Fiber Optik Single Mode 96 core G 652 D', 3, '2022-03-22 16:04:03', '2022-03-22 18:54:38', NULL),
(4, 'DC-OF-SM-48D', 'Pengadaan dan pemasangan Kabel Duct Fiber Optik Single Mode 48 core G 652 D', 4, '2022-03-22 16:12:04', '2022-03-22 18:54:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `designator_package`
--

CREATE TABLE `designator_package` (
  `designator_package_id` int(11) NOT NULL,
  `designator_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `material_price` double NOT NULL,
  `service_price` double NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `designator_package`
--

INSERT INTO `designator_package` (`designator_package_id`, `designator_id`, `package_id`, `material_price`, `service_price`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, 1, 10000, 2000, '2022-03-21 23:43:55', NULL, NULL),
(2, 2, 1, 300000, 40000, '2022-03-22 16:01:29', NULL, NULL),
(3, 3, 1, 200000, 30000, '2022-03-22 16:15:59', NULL, NULL),
(4, 4, 1, 100000, 200000, '2022-03-22 16:16:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `do`
--

CREATE TABLE `do` (
  `do_id` int(11) NOT NULL,
  `do_code` varchar(75) NOT NULL,
  `do_date` date NOT NULL,
  `ro_date` date DEFAULT NULL,
  `witel_id` int(11) NOT NULL,
  `do_subtotal` double NOT NULL,
  `do_charge` double NOT NULL,
  `do_grandtotal` double NOT NULL,
  `do_status` enum('issued','processed','done') NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `do`
--

INSERT INTO `do` (`do_id`, `do_code`, `do_date`, `ro_date`, `witel_id`, `do_subtotal`, `do_charge`, `do_grandtotal`, `do_status`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'sdasd', '2022-03-03', '2022-03-21', 1, 8880000, 0, 8880000, 'done', '2022-03-21 23:52:52', '2022-03-21 23:53:10', NULL),
(2, 'DO-001', '2022-03-23', '2022-03-22', 1, 13098000, 0, 13098000, 'done', '2022-03-22 17:04:34', '2022-03-22 17:05:40', NULL),
(3, 'DO-003', '2022-03-23', NULL, 1, 0, 0, 0, 'issued', '2022-03-22 17:10:35', '2022-03-22 17:11:00', '2022-03-22 10:11:00'),
(4, 'DO-004', '2022-03-23', '2022-03-22', 1, 8899999110, 0, 8899999110, 'done', '2022-03-22 17:11:22', '2022-03-22 17:11:36', NULL),
(5, '123', '2022-03-22', '2022-03-22', 1, 733084000, 0, 733084000, 'done', '2022-03-22 18:42:29', '2022-03-22 18:43:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `do_item`
--

CREATE TABLE `do_item` (
  `item_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `item_price` double NOT NULL,
  `item_qty` int(11) NOT NULL,
  `item_total` double NOT NULL,
  `do_id` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `do_item`
--

INSERT INTO `do_item` (`item_id`, `stock_id`, `item_price`, `item_qty`, `item_total`, `do_id`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, 222000, 40, 8880000, 1, '2022-03-21 23:52:52', NULL, NULL),
(2, 1, 222000, 59, 13098000, 2, '2022-03-22 17:04:34', NULL, NULL),
(3, 2, 99999990, 89, 8899999110, 4, '2022-03-22 17:11:22', NULL, NULL),
(4, 1, 222000, 122, 27084000, 5, '2022-03-22 18:42:29', NULL, NULL),
(5, 3, 12000, 500, 6000000, 5, '2022-03-22 18:42:29', NULL, NULL),
(6, 4, 100000, 3000, 300000000, 5, '2022-03-22 18:42:29', NULL, NULL),
(7, 6, 20000, 20000, 400000000, 5, '2022-03-22 18:42:29', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job`
--

CREATE TABLE `job` (
  `job_id` int(11) NOT NULL,
  `job_name` varchar(255) NOT NULL,
  `job_percent` decimal(10,0) NOT NULL,
  `job_day` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `job`
--

INSERT INTO `job` (`job_id`, `job_name`, `job_percent`, `job_day`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'survey dan izin', '10', 0, '2022-03-19 09:50:26', '2022-03-21 17:07:55', NULL),
(2, 'pengambilan material', '5', 0, '2022-03-21 17:08:11', '2022-03-21 17:08:41', NULL),
(3, 'instalasi ', '15', 0, '2022-03-21 17:08:30', NULL, NULL),
(4, 'terminasi ', '10', 0, '2022-03-21 17:09:06', NULL, NULL),
(5, 'valid 3', '15', 0, '2022-03-21 17:09:28', NULL, NULL),
(6, 'labeling ', '5', 0, '2022-03-21 17:09:42', '2022-03-21 19:16:42', NULL),
(7, 'valid 4', '5', 0, '2022-03-21 17:10:06', '2022-03-21 19:16:33', NULL),
(8, 'reconsiliasi ', '15', 0, '2022-03-21 17:10:25', '2022-03-22 16:38:26', NULL),
(9, 'pemberkasan', '10', 0, '2022-03-21 17:10:50', NULL, NULL),
(10, 'submit ', '5', 0, '2022-03-21 17:11:07', '2022-03-22 16:38:22', NULL),
(11, 'PAID', '5', 0, '2022-03-21 17:11:41', '2022-03-21 19:16:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `moduleCode` int(11) NOT NULL,
  `module` varchar(75) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`moduleCode`, `module`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'Permission', '2022-03-19 09:04:38', NULL, NULL),
(2, 'Role', '2022-03-19 09:04:38', NULL, NULL),
(3, 'User', '2022-03-19 09:04:38', NULL, NULL),
(4, 'Brand', '2022-03-19 09:04:38', NULL, NULL),
(5, 'Product', '2022-03-19 09:04:38', NULL, NULL),
(6, 'Region', '2022-03-19 09:04:38', NULL, NULL),
(7, 'Witel', '2022-03-19 09:04:38', NULL, NULL),
(8, 'Job', '2022-03-19 09:04:39', NULL, NULL),
(9, 'Supplier', '2022-03-19 09:04:39', NULL, NULL),
(10, 'Package', '2022-03-19 09:04:39', NULL, NULL),
(11, 'Designator', '2022-03-19 09:04:39', NULL, NULL),
(12, 'PurchaseOrder', '2022-03-19 09:04:39', NULL, NULL),
(13, 'DeliveryOrder', '2022-03-19 09:04:39', NULL, NULL),
(14, 'ReciveOrder', '2022-03-19 09:04:39', NULL, NULL),
(15, 'Project', '2022-03-19 09:04:39', NULL, NULL),
(16, 'Stock', '2022-03-23 23:15:25', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `package_desc` text NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `package`
--

INSERT INTO `package` (`package_id`, `package_name`, `package_desc`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'Paket 4', 'DKI Jakarta', '2022-03-21 23:43:34', '2022-03-22 18:52:21', NULL),
(2, 'Paket 1', 'Aceh medan', '2022-03-24 19:20:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `permissionCode` int(11) NOT NULL,
  `permission` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `moduleCode` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`permissionCode`, `permission`, `description`, `moduleCode`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'RMP', 'See permission and module', 1, '2022-03-19 09:04:38', NULL, NULL),
(2, 'RR', 'See roles', 2, '2022-03-19 09:04:38', NULL, NULL),
(3, 'CR', 'Create role', 2, '2022-03-19 09:04:38', NULL, NULL),
(4, 'UR', 'Update role', 2, '2022-03-19 09:04:38', NULL, NULL),
(5, 'DR', 'Delete role', 2, '2022-03-19 09:04:38', NULL, NULL),
(6, 'CRP', 'Add permission to role', 2, '2022-03-19 09:04:38', NULL, NULL),
(7, 'DRP', 'Delete permission from role', 2, '2022-03-19 09:04:38', NULL, NULL),
(8, 'RRP', 'See module and permission by role', 2, '2022-03-19 09:04:38', NULL, NULL),
(9, 'RU', 'See users', 3, '2022-03-19 09:04:38', NULL, NULL),
(10, 'CU', 'Create user', 3, '2022-03-19 09:04:38', NULL, NULL),
(11, 'UU', 'Update user', 3, '2022-03-19 09:04:38', NULL, NULL),
(12, 'DU', 'Delete user', 3, '2022-03-19 09:04:38', NULL, NULL),
(13, 'CRU', 'Add role to user', 3, '2022-03-19 09:04:38', NULL, NULL),
(14, 'DRU', 'Delete role from user', 3, '2022-03-19 09:04:38', NULL, NULL),
(15, 'CUP', 'Add permission to user', 3, '2022-03-19 09:04:38', NULL, NULL),
(16, 'DUP', 'Delete permission from user', 3, '2022-03-19 09:04:38', NULL, NULL),
(17, 'RDRMPU', 'Read role, module, permission from user', 3, '2022-03-19 09:04:38', NULL, NULL),
(18, 'RB', 'See brands', 4, '2022-03-19 09:04:38', NULL, NULL),
(19, 'CB', 'Create brand', 4, '2022-03-19 09:04:38', NULL, NULL),
(20, 'UB', 'Update brand', 4, '2022-03-19 09:04:38', NULL, NULL),
(21, 'DB', 'Delete brand', 4, '2022-03-19 09:04:38', NULL, NULL),
(22, 'RPP', 'See products', 5, '2022-03-19 09:04:38', NULL, NULL),
(23, 'CPP', 'Create product', 5, '2022-03-19 09:04:38', NULL, NULL),
(24, 'UPP', 'Update product', 5, '2022-03-19 09:04:38', NULL, NULL),
(25, 'DPP', 'Delete product', 5, '2022-03-19 09:04:38', NULL, NULL),
(26, 'RRR', 'See regions', 6, '2022-03-19 09:04:38', NULL, NULL),
(27, 'CRR', 'Create region', 6, '2022-03-19 09:04:38', NULL, NULL),
(28, 'URR', 'Update region', 6, '2022-03-19 09:04:38', NULL, NULL),
(29, 'DRR', 'Delete region', 6, '2022-03-19 09:04:38', NULL, NULL),
(30, 'RW', 'See witels', 7, '2022-03-19 09:04:38', NULL, NULL),
(31, 'CW', 'Create witel', 7, '2022-03-19 09:04:38', NULL, NULL),
(32, 'UW', 'Update witel', 7, '2022-03-19 09:04:38', NULL, NULL),
(33, 'DW', 'Delete witel', 7, '2022-03-19 09:04:38', NULL, NULL),
(34, 'RWUU', 'See user from witel', 7, '2022-03-19 09:04:38', NULL, NULL),
(35, 'CWU', 'Add user to witel', 7, '2022-03-19 09:04:38', NULL, NULL),
(36, 'DWU', 'Delete user from witel', 7, '2022-03-19 09:04:39', NULL, NULL),
(37, 'RJ', 'See jobs', 8, '2022-03-19 09:04:39', NULL, NULL),
(41, 'RS', 'See suppliers', 9, '2022-03-19 09:04:39', NULL, NULL),
(42, 'CS', 'Create supplier', 9, '2022-03-19 09:04:39', NULL, NULL),
(43, 'US', 'Update supplier', 9, '2022-03-19 09:04:39', NULL, NULL),
(44, 'DS', 'Delete supplier', 9, '2022-03-19 09:04:39', NULL, NULL),
(45, 'RP', 'See packages', 10, '2022-03-19 09:04:39', NULL, NULL),
(46, 'CP', 'Create package', 10, '2022-03-19 09:04:39', NULL, NULL),
(47, 'UP', 'Update package', 10, '2022-03-19 09:04:39', NULL, NULL),
(48, 'DP', 'Delete package', 10, '2022-03-19 09:04:39', NULL, NULL),
(49, 'RPD', 'See designator and price from package', 10, '2022-03-19 09:04:39', NULL, NULL),
(50, 'RD', 'See designators', 11, '2022-03-19 09:04:39', NULL, NULL),
(51, 'CD', 'Create designator', 11, '2022-03-19 09:04:39', NULL, NULL),
(52, 'UD', 'Update designator', 11, '2022-03-19 09:04:39', NULL, NULL),
(53, 'DD', 'Delete designator', 11, '2022-03-19 09:04:39', NULL, NULL),
(54, 'CDPP', 'Add designator to package', 11, '2022-03-19 09:04:39', NULL, NULL),
(55, 'DDPP', 'Delete designator from package', 11, '2022-03-19 09:04:39', NULL, NULL),
(56, 'RPO', 'See purchase orders', 12, '2022-03-19 09:04:39', NULL, NULL),
(57, 'CPO', 'Create purchase order', 12, '2022-03-19 09:04:39', NULL, NULL),
(58, 'UPO', 'Update purchase order', 12, '2022-03-19 09:04:39', NULL, NULL),
(59, 'DPO', 'Delete purchase order', 12, '2022-03-19 09:04:39', NULL, NULL),
(60, 'UCPO', 'Add charge purchase order', 12, '2022-03-19 09:04:39', NULL, NULL),
(61, 'RPOI', 'See item purchase order', 12, '2022-03-19 09:04:39', NULL, NULL),
(62, 'CPOI', 'Add item purchase order', 12, '2022-03-19 09:04:39', NULL, NULL),
(63, 'UPOI', 'Update item purchase order', 12, '2022-03-19 09:04:39', NULL, NULL),
(64, 'DPOI', 'Delete item purchase order', 12, '2022-03-19 09:04:39', NULL, NULL),
(65, 'USPOITP', 'Update status PO from issued to processed', 12, '2022-03-19 09:04:39', NULL, NULL),
(66, 'USPOPTD', 'Update status PO from processed to done', 12, '2022-03-19 09:04:39', NULL, NULL),
(67, 'RDO', 'See delevery orders', 13, '2022-03-19 09:04:39', NULL, NULL),
(68, 'CDO', 'Create delevery order', 13, '2022-03-19 09:04:39', NULL, NULL),
(69, 'UDO', 'Update delevery order', 13, '2022-03-19 09:04:39', NULL, NULL),
(70, 'DDO', 'Delete delevery order', 13, '2022-03-19 09:04:39', NULL, NULL),
(71, 'RDOBW', 'See delevery order on witel', 13, '2022-03-19 09:04:39', NULL, NULL),
(72, 'UCDO', 'Add charge delevery order', 13, '2022-03-19 09:04:39', NULL, NULL),
(73, 'RDOI', 'See item delevery order', 13, '2022-03-19 09:04:39', NULL, NULL),
(74, 'CDOI', 'Add item delevery order', 13, '2022-03-19 09:04:39', NULL, NULL),
(75, 'UDOI', 'Update item delevery order', 13, '2022-03-19 09:04:39', NULL, NULL),
(76, 'DDOI', 'Delete item delevery order', 13, '2022-03-19 09:04:39', NULL, NULL),
(77, 'USDOITP', 'Update status DO from issued to processed', 13, '2022-03-19 09:04:39', NULL, NULL),
(78, 'USDOPTD', 'Update status DO from processed to done', 13, '2022-03-19 09:04:39', NULL, NULL),
(79, 'RROPODO', 'See delevery orders and purchase orders', 14, '2022-03-19 09:04:39', NULL, NULL),
(80, 'RROPODOBW', 'See delevery orders by witel', 14, '2022-03-19 09:04:39', NULL, NULL),
(81, 'RPRO', 'See project', 15, '2022-03-19 09:04:39', NULL, NULL),
(82, 'CPRO', 'Create project', 15, '2022-03-19 09:04:39', NULL, NULL),
(83, 'UPRO', 'Update project', 15, '2022-03-19 09:04:39', NULL, NULL),
(84, 'DPRO', 'Delete project', 15, '2022-03-19 09:04:39', NULL, NULL),
(85, 'APRO', 'Approve project', 15, '2022-03-19 09:04:39', NULL, NULL),
(86, 'DEPRO', 'Decline project', 15, '2022-03-19 09:04:39', NULL, NULL),
(87, 'CTEC', 'Add technician', 15, '2022-03-19 09:04:39', NULL, NULL),
(88, 'UTEC', 'Update technician', 15, '2022-03-19 09:04:39', NULL, NULL),
(89, 'CUSI', 'Create and update sitax', 15, '2022-03-19 09:04:39', NULL, NULL),
(90, 'CFED', 'Create feeder', 15, '2022-03-19 09:04:39', NULL, NULL),
(91, 'UFED', 'Update feeder', 15, '2022-03-19 09:04:39', NULL, NULL),
(92, 'DFED', 'Delete feeder', 15, '2022-03-19 09:04:39', NULL, NULL),
(93, 'CDIS', 'Create distribusi', 15, '2022-03-19 09:04:39', NULL, NULL),
(94, 'UDIS', 'Update distribusi', 15, '2022-03-19 09:04:39', NULL, NULL),
(95, 'DDIS', 'Delete distribusi', 15, '2022-03-19 09:04:39', NULL, NULL),
(96, 'CFLS', 'Add file survey', 15, '2022-03-19 09:04:39', NULL, NULL),
(97, 'DFLS', 'Delete file survey', 15, '2022-03-19 09:04:39', NULL, NULL),
(98, 'CKHSL', 'Add list khs', 15, '2022-03-19 09:04:39', NULL, NULL),
(99, 'UKHSL', 'Update list khs', 15, '2022-03-19 09:04:39', NULL, NULL),
(100, 'DKHSL', 'Delete list khs', 15, '2022-03-19 09:04:39', NULL, NULL),
(101, 'CTKHS', 'Change status from survey to KHS Check', 15, '2022-03-19 09:04:39', NULL, NULL),
(102, 'CMKHS', 'Select source material', 15, '2022-03-19 09:04:39', NULL, NULL),
(103, 'CSAI', 'Change status from instalation to approve instalation', 15, '2022-03-19 09:04:39', NULL, NULL),
(104, 'CFLI', 'Add file instalation', 15, '2022-03-19 09:04:39', NULL, NULL),
(105, 'DFLI', 'Delete file instalation', 15, '2022-03-19 09:04:39', NULL, NULL),
(106, 'CSAT', 'Change status from approve instalation to termination', 15, '2022-03-19 09:04:39', NULL, NULL),
(107, 'CFLT', 'Add file termination', 15, '2022-03-19 09:04:39', NULL, NULL),
(108, 'DFLT', 'Delete file termination', 15, '2022-03-19 09:04:39', NULL, NULL),
(109, 'CSV3', 'Change status from termination to valid 3', 15, '2022-03-19 09:04:39', NULL, NULL),
(110, 'CDSV3', 'Complete data in step valid 3', 15, '2022-03-19 09:04:39', NULL, NULL),
(111, 'CSL', 'Change status from valid 3 to labeling', 15, '2022-03-19 09:04:39', NULL, NULL),
(112, 'CFLL', 'Add file labeling', 15, '2022-03-19 09:04:39', NULL, NULL),
(113, 'DFLL', 'Delete file labeling', 15, '2022-03-19 09:04:39', NULL, NULL),
(114, 'CSV4', 'Change status from labeling to valid 4', 15, '2022-03-19 09:04:39', NULL, NULL),
(115, 'CDV4', 'Complete data in step valid 4', 15, '2022-03-19 09:04:40', NULL, NULL),
(116, 'CSTD', 'Change status from valid 4 to reconsiliasi', 15, '2022-03-19 09:04:40', '2022-03-21 20:00:25', NULL),
(117, 'URECON', 'Add and edit reconsiliasi', 15, '2022-03-21 20:23:42', NULL, NULL),
(118, 'CRTP', 'Change status from reconsiliasi to pemberkasan', 15, '2022-03-21 20:24:37', NULL, NULL),
(119, 'CPTS', 'Change status from pemberkasan to submit', 15, '2022-03-21 20:25:27', NULL, NULL),
(120, 'CSTP', 'Change status from submit to paid', 15, '2022-03-21 20:26:05', NULL, NULL),
(121, 'RJOB', 'See Jobs', 15, '2022-03-23 01:13:18', NULL, NULL),
(122, 'UJOB', 'Update estimation date', 15, '2022-03-23 01:14:23', NULL, NULL),
(125, 'RSTOCKHO', 'See stock HO', 16, '2022-03-23 23:17:01', NULL, NULL),
(126, 'RSTOCKWITEL', 'See stock WITEL', 16, '2022-03-23 23:17:01', NULL, NULL),
(127, 'RSTOCKBYWITEL', 'See stock witel by witel user', 16, '2022-03-23 23:17:36', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `po`
--

CREATE TABLE `po` (
  `po_id` int(11) NOT NULL,
  `po_code` varchar(75) NOT NULL,
  `po_date` date NOT NULL,
  `ro_date` date DEFAULT NULL,
  `supplier_id` int(11) NOT NULL,
  `po_subtotal` double NOT NULL,
  `po_charge` double NOT NULL,
  `po_grandtotal` double NOT NULL,
  `po_status` enum('issued','processed','done') NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `po`
--

INSERT INTO `po` (`po_id`, `po_code`, `po_date`, `ro_date`, `supplier_id`, `po_subtotal`, `po_charge`, `po_grandtotal`, `po_status`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, '8080', '2022-03-03', '2022-03-21', 1, 222000000, 0, 222000000, 'done', '2022-03-21 23:52:11', '2022-03-21 23:52:29', NULL),
(2, 'PO-001', '2022-03-23', '2022-03-22', 1, 8666656658, 0, 8666656658, 'done', '2022-03-22 17:05:17', '2022-03-22 18:37:28', NULL),
(3, 'PO-003', '2022-03-23', '2022-03-22', 1, 8999999100, 0, 8999999100, 'done', '2022-03-22 17:10:09', '2022-03-22 17:10:41', NULL),
(4, '1222', '2022-03-22', '2022-03-22', 1, 36010000000, 20000, 36010020000, 'done', '2022-03-22 18:36:37', '2022-03-22 18:37:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `po_item`
--

CREATE TABLE `po_item` (
  `item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `item_price` double NOT NULL,
  `item_qty` int(11) NOT NULL,
  `item_total` double NOT NULL,
  `po_id` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `po_item`
--

INSERT INTO `po_item` (`item_id`, `product_id`, `item_price`, `item_qty`, `item_total`, `po_id`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, 222000, 1000, 222000000, 1, '2022-03-21 23:52:11', NULL, NULL),
(2, 1, 787877878, 11, 8666656658, 2, '2022-03-22 17:05:17', NULL, NULL),
(3, 4, 99999990, 90, 8999999100, 3, '2022-03-22 17:10:09', NULL, NULL),
(4, 3, 12000, 1000000, 12000000000, 4, '2022-03-22 18:36:37', NULL, NULL),
(5, 2, 100000, 200000, 20000000000, 4, '2022-03-22 18:36:37', NULL, NULL),
(6, 1, 10000, 1000, 10000000, 4, '2022-03-22 18:36:37', NULL, NULL),
(7, 4, 20000, 200000, 4000000000, 4, '2022-03-22 18:36:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_portion` varchar(10) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_portion`, `brand_id`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'Papb', 'Unit', 1, '2022-03-21 23:42:07', '2022-03-24 19:55:02', NULL),
(2, 'Kabel Udara', 'mtr', 1, '2022-03-22 16:00:14', '2022-03-24 19:54:30', NULL),
(3, 'Kabel Air', 'MTR', 1, '2022-03-22 16:03:48', '2022-03-24 19:54:42', NULL),
(4, 'KABEL TANAM', 'MTR', 1, '2022-03-22 16:11:51', '2022-03-22 17:21:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `project_id` int(11) NOT NULL,
  `project_code` varchar(100) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `label_cat` int(11) NOT NULL,
  `project_date` date NOT NULL,
  `project_status` varchar(100) NOT NULL,
  `witel_id` int(11) NOT NULL,
  `project_note` text DEFAULT NULL,
  `project_reconsiliasi` longtext DEFAULT NULL,
  `userCode` int(11) DEFAULT NULL,
  `project_start` date NOT NULL,
  `project_done` date NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`project_id`, `project_code`, `cat_id`, `label_cat`, `project_date`, `project_status`, `witel_id`, `project_note`, `project_reconsiliasi`, `userCode`, `project_start`, `project_done`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'Pps', 8, 1, '2022-03-31', 'Paid', 1, NULL, '[{\"name\":\"uji teskom\",\"status\":1},{\"name\":\"uji terima\",\"status\":1},{\"name\":\"recon jasa\",\"status\":1},{\"name\":\"recon material\",\"status\":1},{\"name\":\"prpo\",\"status\":1},{\"name\":\"sp\",\"status\":1},{\"name\":\"pemberkasan\",\"status\":1},{\"name\":\"invoice\",\"status\":1},{\"name\":\"submit\",\"status\":1},{\"name\":\"paid\",\"status\":1}]', NULL, '0000-00-00', '0000-00-00', '2022-03-22 09:16:08', '2022-03-24 12:20:51', '2022-03-24 05:20:51'),
(2, '-ggggg', 6, 0, '2022-03-23', 'Termination', 1, NULL, NULL, 55, '0000-00-00', '0000-00-00', '2022-03-22 15:52:58', '2022-03-24 20:36:01', NULL),
(3, 'PM-YO', 5, 0, '2022-03-23', 'Paid', 1, 'Jajs', '[{\"name\":\"uji teskom\",\"status\":1},{\"name\":\"uji terima\",\"status\":1},{\"name\":\"recon jasa\",\"status\":1},{\"name\":\"recon material\",\"status\":1},{\"name\":\"prpo\",\"status\":1},{\"name\":\"sp\",\"status\":1},{\"name\":\"pemberkasan\",\"status\":1},{\"name\":\"invoice\",\"status\":1},{\"name\":\"submit\",\"status\":1},{\"name\":\"paid\",\"status\":1}]', NULL, '0000-00-00', '0000-00-00', '2022-03-22 16:12:49', '2022-04-22 15:23:02', NULL),
(4, 'Sttf', 6, 0, '2022-03-24', 'Approved Instalation', 1, 'Youtube', NULL, 52, '0000-00-00', '0000-00-00', '2022-03-22 16:18:30', '2022-03-24 20:17:12', NULL),
(5, '-', 4, 0, '2022-03-23', 'Instalation', 1, '', NULL, NULL, '0000-00-00', '0000-00-00', '2022-03-22 16:49:20', '2022-03-24 09:22:05', NULL),
(6, 'Kdaps', 5, 0, '2022-03-22', 'KHS Check', 1, NULL, NULL, NULL, '0000-00-00', '0000-00-00', '2022-03-22 17:18:49', '2022-03-28 15:41:07', NULL),
(7, 'Sttf-api-01', 6, 0, '2022-03-22', 'Instalation', 1, 'Pm pn', NULL, NULL, '0000-00-00', '0000-00-00', '2022-03-22 17:25:18', '2022-03-24 09:23:33', NULL),
(8, 'Sttf-api-12', 6, 0, '2022-03-22', 'Instalation', 1, 'Youtube', NULL, NULL, '0000-00-00', '0000-00-00', '2022-03-22 17:30:38', '2022-03-24 12:22:18', NULL),
(9, 'Sttf-api-88', 6, 0, '2022-03-24', 'Instalation', 1, 'Yuyy', NULL, NULL, '0000-00-00', '0000-00-00', '2022-03-22 18:34:50', '2022-03-27 21:43:04', NULL),
(10, 'Api_13', 4, 0, '2022-03-24', 'Approved Instalation', 1, 'Cataran', NULL, 52, '0000-00-00', '0000-00-00', '2022-03-24 13:53:40', '2022-03-24 15:24:10', NULL),
(11, 'Apo', 5, 0, '2022-03-24', 'Instalation', 1, 'Note', NULL, NULL, '0000-00-00', '0000-00-00', '2022-03-24 15:28:10', '2022-03-25 09:10:17', NULL),
(12, '-yy', 3, 0, '2022-03-24', 'Survey', 2, 'Catatan', NULL, NULL, '0000-00-00', '0000-00-00', '2022-03-24 19:31:30', '2022-04-24 16:15:14', NULL),
(13, 'Pm', 5, 0, '2022-03-25', 'Approved Instalation', 1, 'Pm', NULL, 52, '0000-00-00', '0000-00-00', '2022-03-25 12:52:20', '2022-03-25 14:28:56', NULL),
(14, '-', 10, 0, '2022-03-27', 'KHS Check', 1, '', NULL, NULL, '0000-00-00', '0000-00-00', '2022-03-27 23:11:14', '2022-03-27 23:14:57', NULL),
(15, 'Api-01', 5, 0, '2022-03-28', 'Approved Instalation', 1, 'Yyy', NULL, 52, '0000-00-00', '0000-00-00', '2022-03-28 20:23:40', '2022-04-06 09:44:53', NULL),
(16, 'API-98', 4, 0, '2022-03-30', 'Paid', 1, 'cata tanku ada disini', NULL, 52, '0000-00-00', '0000-00-00', '2022-03-30 06:24:27', '2022-03-30 18:06:11', NULL),
(17, 'W12', 4, 0, '2022-03-30', 'Paid', 1, 'Pekerjaan PT2 SC : 16910335 ( Febri )', NULL, 52, '0000-00-00', '0000-00-00', '2022-03-30 17:02:44', '2022-03-30 18:05:10', NULL),
(18, 'API-12', 4, 0, '2022-03-30', 'Approve', 1, 'yyy', NULL, NULL, '0000-00-00', '0000-00-00', '2022-03-30 18:35:29', '2022-04-06 09:31:29', NULL),
(19, '-a', 4, 0, '2022-04-05', 'Paid', 1, 'asd', '[{\"name\":\"uji teskom\",\"status\":1},{\"name\":\"uji terima\",\"status\":1},{\"name\":\"recon jasa\",\"status\":1},{\"name\":\"recon material\",\"status\":1},{\"name\":\"prpo\",\"status\":1},{\"name\":\"sp\",\"status\":1},{\"name\":\"pemberkasan\",\"status\":1},{\"name\":\"invoice\",\"status\":1},{\"name\":\"submit\",\"status\":1},{\"name\":\"paid\",\"status\":1}]', 52, '0000-00-00', '0000-00-00', '2022-04-02 21:47:43', '2022-04-05 21:04:06', NULL),
(20, '-', 10, 0, '2022-04-30', 'Instalation', 1, 'asdf', NULL, NULL, '0000-00-00', '0000-00-00', '2022-04-03 19:51:38', '2022-04-05 20:17:29', NULL),
(21, '101456789', 5, 0, '2022-04-05', 'Instalation', 1, 'Kick Off Tahap 2', NULL, NULL, '0000-00-00', '0000-00-00', '2022-04-05 14:08:21', '2022-04-05 15:10:53', NULL),
(22, '-', 3, 0, '2022-04-13', 'Instalation', 1, '', NULL, NULL, '0000-00-00', '0000-00-00', '2022-04-05 15:08:25', '2022-04-05 15:21:00', NULL),
(23, '-', 4, 0, '2022-04-12', 'Instalation', 1, '', NULL, NULL, '0000-00-00', '0000-00-00', '2022-04-05 19:14:27', '2022-04-05 19:17:48', NULL),
(24, '-asd', 6, 0, '2022-04-27', 'Instalation', 1, '', NULL, NULL, '0000-00-00', '0000-00-00', '2022-04-05 20:03:28', '2022-04-05 20:16:25', NULL),
(25, '-asdf', 6, 0, '2022-04-02', 'KHS Check', 1, '', NULL, NULL, '0000-00-00', '0000-00-00', '2022-04-05 20:20:20', '2022-04-05 20:23:57', NULL),
(26, '-ttt', 5, 0, '2022-04-30', 'Pending', 1, 'asdf', NULL, NULL, '2022-04-08', '2022-04-16', '2022-04-07 08:39:20', NULL, NULL),
(27, '-', 3, 0, '2022-04-07', 'Termination', 1, '', NULL, 55, '2022-04-07', '2022-04-14', '2022-04-07 19:22:29', '2022-04-07 19:48:28', NULL),
(28, '12131414', 5, 0, '2022-04-07', 'Survey', 1, 'STTF tahap 2', NULL, NULL, '2022-04-09', '2022-04-14', '2022-04-07 20:49:51', '2022-04-07 20:51:24', NULL),
(29, 'Api12', 3, 0, '2022-04-11', 'Survey', 1, 'Notes catatan', NULL, NULL, '2022-04-11', '2022-04-30', '2022-04-11 20:25:05', '2022-04-11 20:25:58', NULL),
(30, '12', 16, 0, '2022-04-17', 'Survey', 1, 'asdasd', NULL, NULL, '2022-04-18', '2022-04-20', '2022-04-17 22:20:35', '2022-04-17 22:21:37', NULL),
(31, 'Test', 6, 0, '2022-04-22', 'Survey', 1, '', NULL, NULL, '2022-04-22', '2022-04-30', '2022-04-22 15:19:39', '2022-04-22 15:20:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_cat`
--

CREATE TABLE `project_cat` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(100) NOT NULL,
  `cat_parent` int(11) NOT NULL,
  `cat_action` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project_cat`
--

INSERT INTO `project_cat` (`cat_id`, `cat_name`, `cat_parent`, `cat_action`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'OSP', 0, 0, '2022-03-19 10:04:20', NULL, NULL),
(2, 'QE', 0, 0, '2022-03-19 10:04:20', '2022-04-03 16:17:04', NULL),
(3, 'PT2', 1, 1, '2022-03-19 10:04:20', '2022-04-03 16:25:14', NULL),
(4, 'PT3', 1, 1, '2022-03-19 10:04:20', '2022-04-03 16:25:22', NULL),
(5, 'STTF', 1, 1, '2022-03-19 10:04:20', '2022-04-03 16:25:28', NULL),
(6, 'T-CLOUD', 1, 1, '2022-03-19 10:04:20', '2022-04-03 16:25:37', NULL),
(7, 'HEM', 1, 1, '2022-03-19 10:04:20', '2022-04-03 16:25:43', NULL),
(8, 'NOD-B', 1, 1, '2022-03-19 10:04:20', '2022-04-03 16:25:50', NULL),
(9, 'FEDERISASI', 1, 1, '2022-03-19 10:04:20', '2022-04-03 16:25:59', NULL),
(10, 'GAMAS', 2, 1, '2022-03-19 10:04:20', '2022-04-03 16:24:55', NULL),
(11, 'PT2', 2, 1, '2022-03-19 10:04:20', '2022-04-03 16:24:29', NULL),
(12, 'PT3', 2, 1, '2022-03-19 10:04:20', NULL, NULL),
(13, 'GESTI', 2, 1, '2022-03-19 10:04:20', NULL, NULL),
(14, 'REBOUNDRY', 2, 1, '2022-03-19 10:04:20', NULL, NULL),
(15, 'BENJAR', 2, 0, '2022-03-19 10:04:20', NULL, NULL),
(16, 'BENJAR ODP', 15, 1, '2022-03-19 10:04:20', NULL, NULL),
(17, 'BENJAR ODC', 15, 1, '2022-03-19 10:04:20', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_feeder`
--

CREATE TABLE `project_feeder` (
  `project_feeder_id` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project_feeder`
--

INSERT INTO `project_feeder` (`project_feeder_id`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, '2022-03-22 09:17:37', '2022-03-24 12:20:51', '2022-03-24 05:20:51'),
(2, '2022-03-22 15:56:55', '2022-03-22 15:57:14', NULL),
(3, '2022-03-22 15:58:54', NULL, NULL),
(4, '2022-03-22 17:32:32', NULL, NULL),
(5, '2022-03-24 13:59:52', NULL, NULL),
(6, '2022-03-25 09:06:17', NULL, NULL),
(7, '2022-03-25 14:21:29', NULL, NULL),
(8, '2022-03-26 08:33:39', NULL, NULL),
(9, '2022-03-28 20:25:21', NULL, NULL),
(10, '2022-03-30 06:29:27', '2022-03-30 06:42:00', NULL),
(11, '2022-03-30 17:18:13', '2022-03-30 17:57:06', NULL),
(12, '2022-03-30 18:45:14', NULL, NULL),
(13, '2022-03-30 21:43:27', NULL, NULL),
(14, '2022-04-02 22:01:17', '2022-04-05 20:43:34', NULL),
(15, '2022-04-03 19:52:20', '2022-04-03 19:52:30', NULL),
(16, '2022-04-05 20:08:19', NULL, NULL),
(17, '2022-04-05 20:22:29', NULL, NULL),
(18, '2022-04-07 19:24:04', NULL, NULL),
(19, '2022-04-07 20:56:08', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_gpon`
--

CREATE TABLE `project_gpon` (
  `project_gpon_id` int(11) NOT NULL,
  `gpon` varchar(100) NOT NULL,
  `slot` varchar(20) NOT NULL,
  `port` varchar(20) NOT NULL,
  `output_feeder` varchar(20) NOT NULL,
  `outout_pasif` varchar(20) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL,
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `project_job`
--

CREATE TABLE `project_job` (
  `project_job_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_done` date DEFAULT NULL,
  `est_date_start` date DEFAULT NULL,
  `est_date_done` date DEFAULT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL,
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project_job`
--

INSERT INTO `project_job` (`project_job_id`, `project_id`, `job_id`, `date_start`, `date_done`, `est_date_start`, `est_date_done`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, 1, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 09:16:08', NULL, NULL),
(2, 1, 2, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 09:16:08', NULL, NULL),
(3, 1, 3, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 09:16:08', NULL, NULL),
(4, 1, 4, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 09:16:08', NULL, NULL),
(5, 1, 5, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 09:16:08', NULL, NULL),
(6, 1, 6, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 09:16:08', NULL, NULL),
(7, 1, 7, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 09:16:08', NULL, NULL),
(8, 1, 8, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 09:16:08', NULL, NULL),
(9, 1, 9, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 09:16:08', NULL, NULL),
(10, 1, 10, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 09:16:08', NULL, NULL),
(11, 1, 11, '2022-03-22', NULL, NULL, NULL, '2022-03-22 09:16:08', NULL, NULL),
(12, 2, 1, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 15:52:58', NULL, NULL),
(13, 2, 2, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 15:52:58', NULL, NULL),
(14, 2, 3, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 15:52:58', NULL, NULL),
(15, 2, 4, '2022-03-22', NULL, NULL, NULL, '2022-03-22 15:52:58', NULL, NULL),
(16, 2, 5, NULL, NULL, NULL, NULL, '2022-03-22 15:52:58', NULL, NULL),
(17, 2, 6, NULL, NULL, NULL, NULL, '2022-03-22 15:52:58', NULL, NULL),
(18, 2, 7, NULL, NULL, NULL, NULL, '2022-03-22 15:52:58', NULL, NULL),
(19, 2, 8, NULL, NULL, NULL, NULL, '2022-03-22 15:52:58', NULL, NULL),
(20, 2, 9, NULL, NULL, NULL, NULL, '2022-03-22 15:52:58', NULL, NULL),
(21, 2, 10, NULL, NULL, NULL, NULL, '2022-03-22 15:52:58', NULL, NULL),
(22, 2, 11, NULL, NULL, NULL, NULL, '2022-03-22 15:52:58', NULL, NULL),
(23, 3, 1, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 16:12:49', NULL, NULL),
(24, 3, 2, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 16:12:49', NULL, NULL),
(25, 3, 3, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 16:12:49', NULL, NULL),
(26, 3, 4, '2022-03-22', '2022-03-30', NULL, NULL, '2022-03-22 16:12:49', NULL, NULL),
(27, 3, 5, '2022-03-30', '2022-03-30', NULL, NULL, '2022-03-22 16:12:49', NULL, NULL),
(28, 3, 6, '2022-03-30', '2022-03-30', NULL, NULL, '2022-03-22 16:12:49', NULL, NULL),
(29, 3, 7, '2022-03-30', '2022-03-30', NULL, NULL, '2022-03-22 16:12:49', NULL, NULL),
(30, 3, 8, '2022-03-30', '2022-04-22', NULL, NULL, '2022-03-22 16:12:49', NULL, NULL),
(31, 3, 9, '2022-04-22', '2022-04-22', NULL, NULL, '2022-03-22 16:12:49', NULL, NULL),
(32, 3, 10, '2022-04-22', NULL, NULL, NULL, '2022-03-22 16:12:49', NULL, NULL),
(33, 3, 11, '2022-04-22', '2022-04-22', NULL, NULL, '2022-03-22 16:12:49', NULL, NULL),
(34, 4, 1, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 16:18:30', NULL, NULL),
(35, 4, 2, '2022-03-22', '2022-03-22', NULL, NULL, '2022-03-22 16:18:30', NULL, NULL),
(36, 4, 3, '2022-03-22', NULL, NULL, NULL, '2022-03-22 16:18:30', NULL, NULL),
(37, 4, 4, NULL, NULL, NULL, NULL, '2022-03-22 16:18:30', NULL, NULL),
(38, 4, 5, NULL, NULL, NULL, NULL, '2022-03-22 16:18:30', NULL, NULL),
(39, 4, 6, NULL, NULL, NULL, NULL, '2022-03-22 16:18:30', NULL, NULL),
(40, 4, 7, NULL, NULL, NULL, NULL, '2022-03-22 16:18:30', NULL, NULL),
(41, 4, 8, NULL, NULL, NULL, NULL, '2022-03-22 16:18:30', NULL, NULL),
(42, 4, 9, NULL, NULL, NULL, NULL, '2022-03-22 16:18:30', NULL, NULL),
(43, 4, 10, NULL, NULL, NULL, NULL, '2022-03-22 16:18:30', NULL, NULL),
(44, 4, 11, NULL, NULL, NULL, NULL, '2022-03-22 16:18:30', NULL, NULL),
(45, 5, 1, '2022-03-22', '2022-03-24', NULL, NULL, '2022-03-22 16:49:20', NULL, NULL),
(46, 5, 2, '2022-03-24', '2022-03-22', NULL, NULL, '2022-03-22 16:49:20', NULL, NULL),
(47, 5, 3, '2022-03-22', NULL, NULL, NULL, '2022-03-22 16:49:20', NULL, NULL),
(48, 5, 4, NULL, NULL, NULL, NULL, '2022-03-22 16:49:20', NULL, NULL),
(49, 5, 5, NULL, NULL, NULL, NULL, '2022-03-22 16:49:20', NULL, NULL),
(50, 5, 6, NULL, NULL, NULL, NULL, '2022-03-22 16:49:20', NULL, NULL),
(51, 5, 7, NULL, NULL, NULL, NULL, '2022-03-22 16:49:20', NULL, NULL),
(52, 5, 8, NULL, NULL, NULL, NULL, '2022-03-22 16:49:20', NULL, NULL),
(53, 5, 9, NULL, NULL, NULL, NULL, '2022-03-22 16:49:20', NULL, NULL),
(54, 5, 10, NULL, NULL, NULL, NULL, '2022-03-22 16:49:20', NULL, NULL),
(55, 5, 11, NULL, NULL, NULL, NULL, '2022-03-22 16:49:20', NULL, NULL),
(56, 6, 1, '2022-03-28', NULL, '2022-03-04', '2022-03-19', '2022-03-22 17:18:49', NULL, NULL),
(57, 6, 2, NULL, NULL, '2022-03-01', '2022-03-05', '2022-03-22 17:18:49', NULL, NULL),
(58, 6, 3, NULL, NULL, '2022-03-05', '2022-03-26', '2022-03-22 17:18:49', NULL, NULL),
(59, 6, 4, NULL, NULL, '2022-03-27', '2022-03-28', '2022-03-22 17:18:49', NULL, NULL),
(60, 6, 5, NULL, NULL, '2022-03-24', '2022-03-26', '2022-03-22 17:18:49', NULL, NULL),
(61, 6, 6, NULL, NULL, NULL, NULL, '2022-03-22 17:18:49', NULL, NULL),
(62, 6, 7, NULL, NULL, NULL, NULL, '2022-03-22 17:18:49', NULL, NULL),
(63, 6, 8, NULL, NULL, NULL, NULL, '2022-03-22 17:18:49', NULL, NULL),
(64, 6, 9, NULL, NULL, NULL, NULL, '2022-03-22 17:18:49', NULL, NULL),
(65, 6, 10, NULL, NULL, NULL, NULL, '2022-03-22 17:18:49', NULL, NULL),
(66, 6, 11, NULL, NULL, NULL, NULL, '2022-03-22 17:18:49', NULL, NULL),
(67, 7, 1, '2022-03-22', '2022-03-24', NULL, NULL, '2022-03-22 17:25:18', NULL, NULL),
(68, 7, 2, '2022-03-24', NULL, NULL, NULL, '2022-03-22 17:25:18', NULL, NULL),
(69, 7, 3, NULL, NULL, NULL, NULL, '2022-03-22 17:25:18', NULL, NULL),
(70, 7, 4, NULL, NULL, NULL, NULL, '2022-03-22 17:25:18', NULL, NULL),
(71, 7, 5, NULL, NULL, NULL, NULL, '2022-03-22 17:25:18', NULL, NULL),
(72, 7, 6, NULL, NULL, NULL, NULL, '2022-03-22 17:25:18', NULL, NULL),
(73, 7, 7, NULL, NULL, NULL, NULL, '2022-03-22 17:25:18', NULL, NULL),
(74, 7, 8, NULL, NULL, NULL, NULL, '2022-03-22 17:25:18', NULL, NULL),
(75, 7, 9, NULL, NULL, NULL, NULL, '2022-03-22 17:25:18', NULL, NULL),
(76, 7, 10, NULL, NULL, NULL, NULL, '2022-03-22 17:25:18', NULL, NULL),
(77, 7, 11, NULL, NULL, NULL, NULL, '2022-03-22 17:25:18', NULL, NULL),
(78, 8, 1, '2022-03-22', '2022-03-24', NULL, NULL, '2022-03-22 17:30:38', NULL, NULL),
(79, 8, 2, '2022-03-24', NULL, NULL, NULL, '2022-03-22 17:30:38', NULL, NULL),
(80, 8, 3, NULL, NULL, NULL, NULL, '2022-03-22 17:30:38', NULL, NULL),
(81, 8, 4, NULL, NULL, NULL, NULL, '2022-03-22 17:30:38', NULL, NULL),
(82, 8, 5, NULL, NULL, NULL, NULL, '2022-03-22 17:30:38', NULL, NULL),
(83, 8, 6, NULL, NULL, NULL, NULL, '2022-03-22 17:30:38', NULL, NULL),
(84, 8, 7, NULL, NULL, NULL, NULL, '2022-03-22 17:30:38', NULL, NULL),
(85, 8, 8, NULL, NULL, NULL, NULL, '2022-03-22 17:30:38', NULL, NULL),
(86, 8, 9, NULL, NULL, NULL, NULL, '2022-03-22 17:30:38', NULL, NULL),
(87, 8, 10, NULL, NULL, NULL, NULL, '2022-03-22 17:30:38', NULL, NULL),
(88, 8, 11, NULL, NULL, NULL, NULL, '2022-03-22 17:30:38', NULL, NULL),
(89, 9, 1, '2022-03-25', '2022-03-27', NULL, NULL, '2022-03-22 18:34:50', NULL, NULL),
(90, 9, 2, '2022-03-27', NULL, NULL, NULL, '2022-03-22 18:34:50', NULL, NULL),
(91, 9, 3, NULL, NULL, NULL, NULL, '2022-03-22 18:34:50', NULL, NULL),
(92, 9, 4, NULL, NULL, NULL, NULL, '2022-03-22 18:34:50', NULL, NULL),
(93, 9, 5, NULL, NULL, NULL, NULL, '2022-03-22 18:34:50', NULL, NULL),
(94, 9, 6, NULL, NULL, NULL, NULL, '2022-03-22 18:34:50', NULL, NULL),
(95, 9, 7, NULL, NULL, NULL, NULL, '2022-03-22 18:34:50', NULL, NULL),
(96, 9, 8, NULL, NULL, NULL, NULL, '2022-03-22 18:34:50', NULL, NULL),
(97, 9, 9, NULL, NULL, NULL, NULL, '2022-03-22 18:34:50', NULL, NULL),
(98, 9, 10, NULL, NULL, NULL, NULL, '2022-03-22 18:34:50', NULL, NULL),
(99, 9, 11, NULL, NULL, NULL, NULL, '2022-03-22 18:34:50', NULL, NULL),
(100, 10, 1, '2022-03-24', '2022-03-24', NULL, NULL, '2022-03-24 13:53:40', NULL, NULL),
(101, 10, 2, '2022-03-24', '2022-03-24', NULL, NULL, '2022-03-24 13:53:40', NULL, NULL),
(102, 10, 3, '2022-03-24', NULL, NULL, NULL, '2022-03-24 13:53:40', NULL, NULL),
(103, 10, 4, NULL, NULL, NULL, NULL, '2022-03-24 13:53:40', NULL, NULL),
(104, 10, 5, NULL, NULL, NULL, NULL, '2022-03-24 13:53:40', NULL, NULL),
(105, 10, 6, NULL, NULL, NULL, NULL, '2022-03-24 13:53:40', NULL, NULL),
(106, 10, 7, NULL, NULL, NULL, NULL, '2022-03-24 13:53:40', NULL, NULL),
(107, 10, 8, NULL, NULL, NULL, NULL, '2022-03-24 13:53:40', NULL, NULL),
(108, 10, 9, NULL, NULL, NULL, NULL, '2022-03-24 13:53:40', NULL, NULL),
(109, 10, 10, NULL, NULL, NULL, NULL, '2022-03-24 13:53:40', NULL, NULL),
(110, 10, 11, NULL, NULL, NULL, NULL, '2022-03-24 13:53:40', NULL, NULL),
(111, 11, 1, '2022-03-24', '2022-03-25', NULL, NULL, '2022-03-24 15:28:10', NULL, NULL),
(112, 11, 2, '2022-03-25', NULL, NULL, NULL, '2022-03-24 15:28:10', NULL, NULL),
(113, 11, 3, NULL, NULL, NULL, NULL, '2022-03-24 15:28:10', NULL, NULL),
(114, 11, 4, NULL, NULL, NULL, NULL, '2022-03-24 15:28:10', NULL, NULL),
(115, 11, 5, NULL, NULL, NULL, NULL, '2022-03-24 15:28:10', NULL, NULL),
(116, 11, 6, NULL, NULL, NULL, NULL, '2022-03-24 15:28:10', NULL, NULL),
(117, 11, 7, NULL, NULL, NULL, NULL, '2022-03-24 15:28:10', NULL, NULL),
(118, 11, 8, NULL, NULL, NULL, NULL, '2022-03-24 15:28:10', NULL, NULL),
(119, 11, 9, NULL, NULL, NULL, NULL, '2022-03-24 15:28:10', NULL, NULL),
(120, 11, 10, NULL, NULL, NULL, NULL, '2022-03-24 15:28:10', NULL, NULL),
(121, 11, 11, NULL, NULL, NULL, NULL, '2022-03-24 15:28:10', NULL, NULL),
(122, 12, 1, '2022-04-24', NULL, NULL, NULL, '2022-03-24 19:31:30', NULL, NULL),
(123, 12, 2, NULL, NULL, NULL, NULL, '2022-03-24 19:31:30', NULL, NULL),
(124, 12, 3, NULL, NULL, NULL, NULL, '2022-03-24 19:31:30', NULL, NULL),
(125, 12, 4, NULL, NULL, NULL, NULL, '2022-03-24 19:31:30', NULL, NULL),
(126, 12, 5, NULL, NULL, NULL, NULL, '2022-03-24 19:31:30', NULL, NULL),
(127, 12, 6, NULL, NULL, NULL, NULL, '2022-03-24 19:31:30', NULL, NULL),
(128, 12, 7, NULL, NULL, NULL, NULL, '2022-03-24 19:31:30', NULL, NULL),
(129, 12, 8, NULL, NULL, NULL, NULL, '2022-03-24 19:31:30', NULL, NULL),
(130, 12, 9, NULL, NULL, NULL, NULL, '2022-03-24 19:31:30', NULL, NULL),
(131, 12, 10, NULL, NULL, NULL, NULL, '2022-03-24 19:31:30', NULL, NULL),
(132, 12, 11, NULL, NULL, NULL, NULL, '2022-03-24 19:31:30', NULL, NULL),
(133, 13, 1, '2022-03-25', '2022-03-25', '2022-03-25', '2022-03-31', '2022-03-25 12:52:20', NULL, NULL),
(134, 13, 2, '2022-03-25', '2022-03-25', '2022-04-01', '2022-04-13', '2022-03-25 12:52:20', NULL, NULL),
(135, 13, 3, '2022-03-25', NULL, '2022-04-13', '2022-04-20', '2022-03-25 12:52:20', NULL, NULL),
(136, 13, 4, NULL, NULL, '2022-04-20', '2022-04-29', '2022-03-25 12:52:20', NULL, NULL),
(137, 13, 5, NULL, NULL, NULL, NULL, '2022-03-25 12:52:20', NULL, NULL),
(138, 13, 6, NULL, NULL, NULL, NULL, '2022-03-25 12:52:20', NULL, NULL),
(139, 13, 7, NULL, NULL, NULL, NULL, '2022-03-25 12:52:20', NULL, NULL),
(140, 13, 8, NULL, NULL, NULL, NULL, '2022-03-25 12:52:20', NULL, NULL),
(141, 13, 9, NULL, NULL, NULL, NULL, '2022-03-25 12:52:20', NULL, NULL),
(142, 13, 10, NULL, NULL, NULL, NULL, '2022-03-25 12:52:20', NULL, NULL),
(143, 13, 11, NULL, NULL, NULL, NULL, '2022-03-25 12:52:20', NULL, NULL),
(144, 14, 1, '2022-03-27', NULL, '2022-03-27', '2022-03-31', '2022-03-27 23:11:14', NULL, NULL),
(145, 14, 2, NULL, NULL, '2022-04-01', '2022-04-09', '2022-03-27 23:11:14', NULL, NULL),
(146, 14, 3, NULL, NULL, '2022-04-10', '2022-05-16', '2022-03-27 23:11:14', NULL, NULL),
(147, 14, 4, NULL, NULL, NULL, NULL, '2022-03-27 23:11:14', NULL, NULL),
(148, 14, 5, NULL, NULL, NULL, NULL, '2022-03-27 23:11:14', NULL, NULL),
(149, 14, 6, NULL, NULL, NULL, NULL, '2022-03-27 23:11:14', NULL, NULL),
(150, 14, 7, NULL, NULL, NULL, NULL, '2022-03-27 23:11:14', NULL, NULL),
(151, 14, 8, NULL, NULL, NULL, NULL, '2022-03-27 23:11:14', NULL, NULL),
(152, 14, 9, NULL, NULL, NULL, NULL, '2022-03-27 23:11:14', NULL, NULL),
(153, 14, 10, NULL, NULL, NULL, NULL, '2022-03-27 23:11:14', NULL, NULL),
(154, 14, 11, NULL, NULL, NULL, NULL, '2022-03-27 23:11:14', NULL, NULL),
(155, 15, 1, '2022-03-28', '2022-04-06', NULL, NULL, '2022-03-28 20:23:40', NULL, NULL),
(156, 15, 2, '2022-04-06', '2022-04-06', NULL, NULL, '2022-03-28 20:23:40', NULL, NULL),
(157, 15, 3, '2022-04-06', NULL, NULL, NULL, '2022-03-28 20:23:40', NULL, NULL),
(158, 15, 4, NULL, NULL, NULL, NULL, '2022-03-28 20:23:40', NULL, NULL),
(159, 15, 5, NULL, NULL, NULL, NULL, '2022-03-28 20:23:40', NULL, NULL),
(160, 15, 6, NULL, NULL, NULL, NULL, '2022-03-28 20:23:40', NULL, NULL),
(161, 15, 7, NULL, NULL, NULL, NULL, '2022-03-28 20:23:40', NULL, NULL),
(162, 15, 8, NULL, NULL, NULL, NULL, '2022-03-28 20:23:40', NULL, NULL),
(163, 15, 9, NULL, NULL, NULL, NULL, '2022-03-28 20:23:40', NULL, NULL),
(164, 15, 10, NULL, NULL, NULL, NULL, '2022-03-28 20:23:40', NULL, NULL),
(165, 15, 11, NULL, NULL, NULL, NULL, '2022-03-28 20:23:40', NULL, NULL),
(166, 16, 1, '2022-03-29', '2022-03-29', NULL, NULL, '2022-03-30 06:24:27', NULL, NULL),
(167, 16, 2, '2022-03-29', '2022-03-29', NULL, NULL, '2022-03-30 06:24:27', NULL, NULL),
(168, 16, 3, '2022-03-29', '2022-03-29', NULL, NULL, '2022-03-30 06:24:27', NULL, NULL),
(169, 16, 4, '2022-03-29', '2022-03-29', NULL, NULL, '2022-03-30 06:24:27', NULL, NULL),
(170, 16, 5, '2022-03-29', '2022-03-29', NULL, NULL, '2022-03-30 06:24:27', NULL, NULL),
(171, 16, 6, '2022-03-29', '2022-03-29', NULL, NULL, '2022-03-30 06:24:27', NULL, NULL),
(172, 16, 7, '2022-03-29', '2022-03-29', NULL, NULL, '2022-03-30 06:24:27', NULL, NULL),
(173, 16, 8, '2022-03-29', '2022-03-30', NULL, NULL, '2022-03-30 06:24:27', NULL, NULL),
(174, 16, 9, '2022-03-30', '2022-03-30', NULL, NULL, '2022-03-30 06:24:27', NULL, NULL),
(175, 16, 10, '2022-03-30', NULL, NULL, NULL, '2022-03-30 06:24:27', NULL, NULL),
(176, 16, 11, '2022-03-30', '2022-03-30', NULL, NULL, '2022-03-30 06:24:27', NULL, NULL),
(177, 17, 1, '2022-03-30', '2022-03-30', '2022-03-30', '2022-04-03', '2022-03-30 17:02:44', NULL, NULL),
(178, 17, 2, '2022-03-30', '2022-03-30', '2022-03-31', '2022-03-31', '2022-03-30 17:02:44', NULL, NULL),
(179, 17, 3, '2022-03-30', '2022-03-30', '2022-04-01', '2022-04-03', '2022-03-30 17:02:44', NULL, NULL),
(180, 17, 4, '2022-03-30', '2022-03-30', '2022-04-02', '2022-04-03', '2022-03-30 17:02:44', NULL, NULL),
(181, 17, 5, '2022-03-30', '2022-03-30', '2022-04-10', '2022-04-11', '2022-03-30 17:02:44', NULL, NULL),
(182, 17, 6, '2022-03-30', '2022-03-30', '2022-04-12', '2022-04-16', '2022-03-30 17:02:44', NULL, NULL),
(183, 17, 7, '2022-03-30', '2022-03-30', '2022-04-17', '2022-04-20', '2022-03-30 17:02:44', NULL, NULL),
(184, 17, 8, '2022-03-30', '2022-03-30', '2022-04-24', '2022-04-29', '2022-03-30 17:02:44', NULL, NULL),
(185, 17, 9, '2022-03-30', '2022-03-30', '2022-05-01', '2022-05-05', '2022-03-30 17:02:44', NULL, NULL),
(186, 17, 10, '2022-03-30', NULL, '2022-05-08', '2022-05-14', '2022-03-30 17:02:44', NULL, NULL),
(187, 17, 11, '2022-03-30', '2022-03-30', '2022-06-05', '2022-06-10', '2022-03-30 17:02:44', NULL, NULL),
(188, 18, 1, '2022-03-30', NULL, '2022-03-30', '2022-03-31', '2022-03-30 18:35:29', NULL, NULL),
(189, 18, 2, NULL, NULL, '2022-04-01', '2022-04-01', '2022-03-30 18:35:29', NULL, NULL),
(190, 18, 3, NULL, NULL, '2022-04-02', '2022-04-03', '2022-03-30 18:35:29', NULL, NULL),
(191, 18, 4, NULL, NULL, '2022-04-03', '2022-04-04', '2022-03-30 18:35:29', NULL, NULL),
(192, 18, 5, NULL, NULL, '2022-04-10', '2022-04-12', '2022-03-30 18:35:29', NULL, NULL),
(193, 18, 6, NULL, NULL, '2022-04-13', '2022-04-17', '2022-03-30 18:35:29', NULL, NULL),
(194, 18, 7, NULL, NULL, '2022-04-18', '2022-04-20', '2022-03-30 18:35:29', NULL, NULL),
(195, 18, 8, NULL, NULL, '2022-04-20', '2022-04-21', '2022-03-30 18:35:29', NULL, NULL),
(196, 18, 9, NULL, NULL, '2022-04-25', '2022-04-26', '2022-03-30 18:35:29', NULL, NULL),
(197, 18, 10, NULL, NULL, '2022-04-27', '2022-04-30', '2022-03-30 18:35:29', NULL, NULL),
(198, 18, 11, NULL, NULL, '2022-04-29', '2022-04-30', '2022-03-30 18:35:29', NULL, NULL),
(199, 19, 1, '2022-04-02', '2022-04-05', NULL, NULL, '2022-04-02 21:47:43', NULL, NULL),
(200, 19, 2, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-02 21:47:43', NULL, NULL),
(201, 19, 3, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-02 21:47:43', NULL, NULL),
(202, 19, 4, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-02 21:47:43', NULL, NULL),
(203, 19, 5, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-02 21:47:43', NULL, NULL),
(204, 19, 6, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-02 21:47:43', NULL, NULL),
(205, 19, 7, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-02 21:47:43', NULL, NULL),
(206, 19, 8, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-02 21:47:43', NULL, NULL),
(207, 19, 9, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-02 21:47:43', NULL, NULL),
(208, 19, 10, '2022-04-05', NULL, NULL, NULL, '2022-04-02 21:47:43', NULL, NULL),
(209, 19, 11, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-02 21:47:43', NULL, NULL),
(210, 20, 1, '2022-04-03', '2022-04-05', NULL, NULL, '2022-04-03 19:51:38', NULL, NULL),
(211, 20, 2, '2022-04-05', NULL, NULL, NULL, '2022-04-03 19:51:38', NULL, NULL),
(212, 20, 3, NULL, NULL, NULL, NULL, '2022-04-03 19:51:38', NULL, NULL),
(213, 20, 4, NULL, NULL, NULL, NULL, '2022-04-03 19:51:38', NULL, NULL),
(214, 20, 5, NULL, NULL, NULL, NULL, '2022-04-03 19:51:38', NULL, NULL),
(215, 20, 6, NULL, NULL, NULL, NULL, '2022-04-03 19:51:38', NULL, NULL),
(216, 20, 7, NULL, NULL, NULL, NULL, '2022-04-03 19:51:38', NULL, NULL),
(217, 20, 8, NULL, NULL, NULL, NULL, '2022-04-03 19:51:38', NULL, NULL),
(218, 20, 9, NULL, NULL, NULL, NULL, '2022-04-03 19:51:38', NULL, NULL),
(219, 20, 10, NULL, NULL, NULL, NULL, '2022-04-03 19:51:38', NULL, NULL),
(220, 20, 11, NULL, NULL, NULL, NULL, '2022-04-03 19:51:38', NULL, NULL),
(221, 21, 1, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-05 14:08:21', NULL, NULL),
(222, 21, 2, '2022-04-05', NULL, NULL, NULL, '2022-04-05 14:08:21', NULL, NULL),
(223, 21, 3, NULL, NULL, NULL, NULL, '2022-04-05 14:08:21', NULL, NULL),
(224, 21, 4, NULL, NULL, NULL, NULL, '2022-04-05 14:08:21', NULL, NULL),
(225, 21, 5, NULL, NULL, NULL, NULL, '2022-04-05 14:08:21', NULL, NULL),
(226, 21, 6, NULL, NULL, NULL, NULL, '2022-04-05 14:08:21', NULL, NULL),
(227, 21, 7, NULL, NULL, NULL, NULL, '2022-04-05 14:08:21', NULL, NULL),
(228, 21, 8, NULL, NULL, NULL, NULL, '2022-04-05 14:08:21', NULL, NULL),
(229, 21, 9, NULL, NULL, NULL, NULL, '2022-04-05 14:08:21', NULL, NULL),
(230, 21, 10, NULL, NULL, NULL, NULL, '2022-04-05 14:08:21', NULL, NULL),
(231, 21, 11, NULL, NULL, NULL, NULL, '2022-04-05 14:08:21', NULL, NULL),
(232, 22, 1, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-05 15:08:25', NULL, NULL),
(233, 22, 2, '2022-04-05', NULL, NULL, NULL, '2022-04-05 15:08:25', NULL, NULL),
(234, 22, 3, NULL, NULL, NULL, NULL, '2022-04-05 15:08:25', NULL, NULL),
(235, 22, 4, NULL, NULL, NULL, NULL, '2022-04-05 15:08:25', NULL, NULL),
(236, 22, 5, NULL, NULL, NULL, NULL, '2022-04-05 15:08:25', NULL, NULL),
(237, 22, 6, NULL, NULL, NULL, NULL, '2022-04-05 15:08:25', NULL, NULL),
(238, 22, 7, NULL, NULL, NULL, NULL, '2022-04-05 15:08:25', NULL, NULL),
(239, 22, 8, NULL, NULL, NULL, NULL, '2022-04-05 15:08:25', NULL, NULL),
(240, 22, 9, NULL, NULL, NULL, NULL, '2022-04-05 15:08:25', NULL, NULL),
(241, 22, 10, NULL, NULL, NULL, NULL, '2022-04-05 15:08:25', NULL, NULL),
(242, 22, 11, NULL, NULL, NULL, NULL, '2022-04-05 15:08:25', NULL, NULL),
(243, 23, 1, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-05 19:14:27', NULL, NULL),
(244, 23, 2, '2022-04-05', NULL, NULL, NULL, '2022-04-05 19:14:27', NULL, NULL),
(245, 23, 3, NULL, NULL, NULL, NULL, '2022-04-05 19:14:27', NULL, NULL),
(246, 23, 4, NULL, NULL, NULL, NULL, '2022-04-05 19:14:27', NULL, NULL),
(247, 23, 5, NULL, NULL, NULL, NULL, '2022-04-05 19:14:27', NULL, NULL),
(248, 23, 6, NULL, NULL, NULL, NULL, '2022-04-05 19:14:27', NULL, NULL),
(249, 23, 7, NULL, NULL, NULL, NULL, '2022-04-05 19:14:27', NULL, NULL),
(250, 23, 8, NULL, NULL, NULL, NULL, '2022-04-05 19:14:27', NULL, NULL),
(251, 23, 9, NULL, NULL, NULL, NULL, '2022-04-05 19:14:27', NULL, NULL),
(252, 23, 10, NULL, NULL, NULL, NULL, '2022-04-05 19:14:27', NULL, NULL),
(253, 23, 11, NULL, NULL, NULL, NULL, '2022-04-05 19:14:27', NULL, NULL),
(254, 24, 1, '2022-04-05', '2022-04-05', NULL, NULL, '2022-04-05 20:03:28', NULL, NULL),
(255, 24, 2, '2022-04-05', NULL, NULL, NULL, '2022-04-05 20:03:28', NULL, NULL),
(256, 24, 3, NULL, NULL, NULL, NULL, '2022-04-05 20:03:28', NULL, NULL),
(257, 24, 4, NULL, NULL, NULL, NULL, '2022-04-05 20:03:28', NULL, NULL),
(258, 24, 5, NULL, NULL, NULL, NULL, '2022-04-05 20:03:28', NULL, NULL),
(259, 24, 6, NULL, NULL, NULL, NULL, '2022-04-05 20:03:28', NULL, NULL),
(260, 24, 7, NULL, NULL, NULL, NULL, '2022-04-05 20:03:28', NULL, NULL),
(261, 24, 8, NULL, NULL, NULL, NULL, '2022-04-05 20:03:28', NULL, NULL),
(262, 24, 9, NULL, NULL, NULL, NULL, '2022-04-05 20:03:28', NULL, NULL),
(263, 24, 10, NULL, NULL, NULL, NULL, '2022-04-05 20:03:28', NULL, NULL),
(264, 24, 11, NULL, NULL, NULL, NULL, '2022-04-05 20:03:28', NULL, NULL),
(265, 25, 1, '2022-04-05', NULL, NULL, NULL, '2022-04-05 20:20:20', NULL, NULL),
(266, 25, 2, NULL, NULL, NULL, NULL, '2022-04-05 20:20:20', NULL, NULL),
(267, 25, 3, NULL, NULL, NULL, NULL, '2022-04-05 20:20:20', NULL, NULL),
(268, 25, 4, NULL, NULL, NULL, NULL, '2022-04-05 20:20:20', NULL, NULL),
(269, 25, 5, NULL, NULL, NULL, NULL, '2022-04-05 20:20:20', NULL, NULL),
(270, 25, 6, NULL, NULL, NULL, NULL, '2022-04-05 20:20:20', NULL, NULL),
(271, 25, 7, NULL, NULL, NULL, NULL, '2022-04-05 20:20:20', NULL, NULL),
(272, 25, 8, NULL, NULL, NULL, NULL, '2022-04-05 20:20:20', NULL, NULL),
(273, 25, 9, NULL, NULL, NULL, NULL, '2022-04-05 20:20:20', NULL, NULL),
(274, 25, 10, NULL, NULL, NULL, NULL, '2022-04-05 20:20:20', NULL, NULL),
(275, 25, 11, NULL, NULL, NULL, NULL, '2022-04-05 20:20:20', NULL, NULL),
(276, 26, 1, NULL, NULL, NULL, NULL, '2022-04-07 08:39:20', NULL, NULL),
(277, 26, 2, NULL, NULL, NULL, NULL, '2022-04-07 08:39:20', NULL, NULL),
(278, 26, 3, NULL, NULL, NULL, NULL, '2022-04-07 08:39:20', NULL, NULL),
(279, 26, 4, NULL, NULL, NULL, NULL, '2022-04-07 08:39:20', NULL, NULL),
(280, 26, 5, NULL, NULL, NULL, NULL, '2022-04-07 08:39:20', NULL, NULL),
(281, 26, 6, NULL, NULL, NULL, NULL, '2022-04-07 08:39:20', NULL, NULL),
(282, 26, 7, NULL, NULL, NULL, NULL, '2022-04-07 08:39:20', NULL, NULL),
(283, 26, 8, NULL, NULL, NULL, NULL, '2022-04-07 08:39:20', NULL, NULL),
(284, 26, 9, NULL, NULL, NULL, NULL, '2022-04-07 08:39:20', NULL, NULL),
(285, 26, 10, NULL, NULL, NULL, NULL, '2022-04-07 08:39:20', NULL, NULL),
(286, 26, 11, NULL, NULL, NULL, NULL, '2022-04-07 08:39:20', NULL, NULL),
(287, 27, 1, '2022-04-07', '2022-04-07', NULL, NULL, '2022-04-07 19:22:29', NULL, NULL),
(288, 27, 2, '2022-04-07', '2022-04-07', NULL, NULL, '2022-04-07 19:22:29', NULL, NULL),
(289, 27, 3, '2022-04-07', '2022-04-07', NULL, NULL, '2022-04-07 19:22:29', NULL, NULL),
(290, 27, 4, '2022-04-07', NULL, NULL, NULL, '2022-04-07 19:22:29', NULL, NULL),
(291, 27, 5, NULL, NULL, NULL, NULL, '2022-04-07 19:22:29', NULL, NULL),
(292, 27, 6, NULL, NULL, NULL, NULL, '2022-04-07 19:22:29', NULL, NULL),
(293, 27, 7, NULL, NULL, NULL, NULL, '2022-04-07 19:22:29', NULL, NULL),
(294, 27, 8, NULL, NULL, NULL, NULL, '2022-04-07 19:22:29', NULL, NULL),
(295, 27, 9, NULL, NULL, NULL, NULL, '2022-04-07 19:22:29', NULL, NULL),
(296, 27, 10, NULL, NULL, NULL, NULL, '2022-04-07 19:22:29', NULL, NULL),
(297, 27, 11, NULL, NULL, NULL, NULL, '2022-04-07 19:22:29', NULL, NULL),
(298, 28, 1, '2022-04-07', NULL, NULL, NULL, '2022-04-07 20:49:51', NULL, NULL),
(299, 28, 2, NULL, NULL, NULL, NULL, '2022-04-07 20:49:51', NULL, NULL),
(300, 28, 3, NULL, NULL, NULL, NULL, '2022-04-07 20:49:51', NULL, NULL),
(301, 28, 4, NULL, NULL, NULL, NULL, '2022-04-07 20:49:51', NULL, NULL),
(302, 28, 5, NULL, NULL, NULL, NULL, '2022-04-07 20:49:51', NULL, NULL),
(303, 28, 6, NULL, NULL, NULL, NULL, '2022-04-07 20:49:51', NULL, NULL),
(304, 28, 7, NULL, NULL, NULL, NULL, '2022-04-07 20:49:51', NULL, NULL),
(305, 28, 8, NULL, NULL, NULL, NULL, '2022-04-07 20:49:51', NULL, NULL),
(306, 28, 9, NULL, NULL, NULL, NULL, '2022-04-07 20:49:51', NULL, NULL),
(307, 28, 10, NULL, NULL, NULL, NULL, '2022-04-07 20:49:51', NULL, NULL),
(308, 28, 11, NULL, NULL, NULL, NULL, '2022-04-07 20:49:51', NULL, NULL),
(309, 29, 1, '2022-04-11', NULL, NULL, NULL, '2022-04-11 20:25:05', NULL, NULL),
(310, 29, 2, NULL, NULL, NULL, NULL, '2022-04-11 20:25:05', NULL, NULL),
(311, 29, 3, NULL, NULL, NULL, NULL, '2022-04-11 20:25:05', NULL, NULL),
(312, 29, 4, NULL, NULL, NULL, NULL, '2022-04-11 20:25:05', NULL, NULL),
(313, 29, 5, NULL, NULL, NULL, NULL, '2022-04-11 20:25:05', NULL, NULL),
(314, 29, 6, NULL, NULL, NULL, NULL, '2022-04-11 20:25:05', NULL, NULL),
(315, 29, 7, NULL, NULL, NULL, NULL, '2022-04-11 20:25:05', NULL, NULL),
(316, 29, 8, NULL, NULL, NULL, NULL, '2022-04-11 20:25:05', NULL, NULL),
(317, 29, 9, NULL, NULL, NULL, NULL, '2022-04-11 20:25:05', NULL, NULL),
(318, 29, 10, NULL, NULL, NULL, NULL, '2022-04-11 20:25:05', NULL, NULL),
(319, 29, 11, NULL, NULL, NULL, NULL, '2022-04-11 20:25:05', NULL, NULL),
(320, 30, 1, '2022-04-17', NULL, NULL, NULL, '2022-04-17 22:20:35', NULL, NULL),
(321, 30, 2, NULL, NULL, NULL, NULL, '2022-04-17 22:20:35', NULL, NULL),
(322, 30, 3, NULL, NULL, NULL, NULL, '2022-04-17 22:20:35', NULL, NULL),
(323, 30, 4, NULL, NULL, NULL, NULL, '2022-04-17 22:20:35', NULL, NULL),
(324, 30, 5, NULL, NULL, NULL, NULL, '2022-04-17 22:20:35', NULL, NULL),
(325, 30, 6, NULL, NULL, NULL, NULL, '2022-04-17 22:20:35', NULL, NULL),
(326, 30, 7, NULL, NULL, NULL, NULL, '2022-04-17 22:20:35', NULL, NULL),
(327, 30, 8, NULL, NULL, NULL, NULL, '2022-04-17 22:20:35', NULL, NULL),
(328, 30, 9, NULL, NULL, NULL, NULL, '2022-04-17 22:20:35', NULL, NULL),
(329, 30, 10, NULL, NULL, NULL, NULL, '2022-04-17 22:20:35', NULL, NULL),
(330, 30, 11, NULL, NULL, NULL, NULL, '2022-04-17 22:20:35', NULL, NULL),
(331, 31, 1, '2022-04-22', NULL, NULL, NULL, '2022-04-22 15:19:39', NULL, NULL),
(332, 31, 2, NULL, NULL, NULL, NULL, '2022-04-22 15:19:39', NULL, NULL),
(333, 31, 3, NULL, NULL, NULL, NULL, '2022-04-22 15:19:39', NULL, NULL),
(334, 31, 4, NULL, NULL, NULL, NULL, '2022-04-22 15:19:39', NULL, NULL),
(335, 31, 5, NULL, NULL, NULL, NULL, '2022-04-22 15:19:39', NULL, NULL),
(336, 31, 6, NULL, NULL, NULL, NULL, '2022-04-22 15:19:39', NULL, NULL),
(337, 31, 7, NULL, NULL, NULL, NULL, '2022-04-22 15:19:39', NULL, NULL),
(338, 31, 8, NULL, NULL, NULL, NULL, '2022-04-22 15:19:39', NULL, NULL),
(339, 31, 9, NULL, NULL, NULL, NULL, '2022-04-22 15:19:39', NULL, NULL),
(340, 31, 10, NULL, NULL, NULL, NULL, '2022-04-22 15:19:39', NULL, NULL),
(341, 31, 11, NULL, NULL, NULL, NULL, '2022-04-22 15:19:39', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_khs`
--

CREATE TABLE `project_khs` (
  `khs_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `khs_source` varchar(100) NOT NULL,
  `khs_material_total` bigint(20) NOT NULL,
  `khs_service_total` bigint(20) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project_khs`
--

INSERT INTO `project_khs` (`khs_id`, `project_id`, `khs_source`, `khs_material_total`, `khs_service_total`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, 'WITEL', 10000, 2000, '2022-03-22 10:09:34', NULL, NULL),
(2, 3, 'WITEL', 3000000, 2300000, '2022-03-22 16:17:45', NULL, NULL),
(3, 4, 'WITEL', 2600000, 2240000, '2022-03-22 16:21:48', NULL, NULL),
(4, 2, 'WITEL', 1520000, 214000, '2022-03-22 16:23:51', NULL, NULL),
(5, 5, 'WITEL', 4300000, 8600000, '2022-03-22 16:51:58', '2022-03-22 17:11:51', NULL),
(6, 7, '', 0, 0, '2022-03-24 09:23:33', NULL, NULL),
(7, 8, '', 100000, 20000, '2022-03-24 12:22:18', NULL, NULL),
(8, 10, '', 0, 0, '2022-03-24 15:14:16', NULL, NULL),
(9, 11, '', 300000, 40000, '2022-03-25 09:10:17', NULL, NULL),
(10, 13, '', 70000000, 10000000, '2022-03-25 14:28:38', NULL, NULL),
(11, 9, '', 0, 0, '2022-03-27 21:43:04', NULL, NULL),
(12, 16, '', 100000, 20000, '2022-03-30 06:35:23', NULL, NULL),
(13, 17, '', 5000000, 700000, '2022-03-30 17:41:56', NULL, NULL),
(14, 21, '', 0, 0, '2022-04-05 15:10:53', NULL, NULL),
(15, 22, '', 0, 0, '2022-04-05 15:21:00', NULL, NULL),
(16, 23, '', 0, 0, '2022-04-05 19:17:48', NULL, NULL),
(17, 24, '', 0, 0, '2022-04-05 20:16:25', NULL, NULL),
(18, 20, '', 0, 0, '2022-04-05 20:17:29', NULL, NULL),
(19, 19, '', 10000, 2000, '2022-04-05 20:34:16', NULL, NULL),
(20, 15, '', 1000000, 2000000, '2022-04-06 09:44:05', NULL, NULL),
(21, 27, '', 3000000, 400000, '2022-04-07 19:42:42', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_khs_list`
--

CREATE TABLE `project_khs_list` (
  `khs_list_id` int(11) NOT NULL,
  `tipe` enum('Feeder','Penggelaran','ODP','ODC','GPON') NOT NULL,
  `tipe_id` int(11) NOT NULL,
  `khs_id` int(11) NOT NULL,
  `designator_id` int(11) NOT NULL,
  `khs_list_qty` int(11) NOT NULL,
  `khs_list_material_price` bigint(20) NOT NULL,
  `khs_list_service_price` bigint(20) NOT NULL,
  `khs_list_material_total` bigint(20) NOT NULL,
  `khs_list_service_total` bigint(20) NOT NULL,
  `khs_source` enum('TA','WITEL') DEFAULT NULL,
  `stock_id` int(11) DEFAULT NULL,
  `userCode` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project_khs_list`
--

INSERT INTO `project_khs_list` (`khs_list_id`, `tipe`, `tipe_id`, `khs_id`, `designator_id`, `khs_list_qty`, `khs_list_material_price`, `khs_list_service_price`, `khs_list_material_total`, `khs_list_service_total`, `khs_source`, `stock_id`, `userCode`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'Feeder', 0, 1, 1, 1, 10000, 2000, 10000, 2000, 'TA', NULL, 36, '2022-03-22 09:18:04', '2022-03-22 10:09:34', NULL),
(2, 'Feeder', 0, 2, 1, 2, 10000, 2000, 20000, 4000, 'TA', NULL, 52, '2022-03-22 15:59:27', '2022-03-22 16:23:51', NULL),
(3, 'Feeder', 0, 2, 2, 3, 300000, 40000, 900000, 120000, 'TA', NULL, 52, '2022-03-22 16:04:31', '2022-03-22 16:23:51', NULL),
(4, 'Feeder', 0, 2, 3, 3, 200000, 30000, 600000, 90000, 'TA', NULL, 52, '2022-03-22 16:04:53', '2022-03-22 16:23:51', NULL),
(5, 'Feeder', 0, 3, 4, 10, 100000, 200000, 1000000, 2000000, 'TA', NULL, 52, '2022-03-22 16:14:18', '2022-03-22 16:17:45', NULL),
(6, 'Feeder', 0, 3, 3, 10, 200000, 30000, 2000000, 300000, 'TA', NULL, 52, '2022-03-22 16:14:36', '2022-03-22 16:17:45', NULL),
(7, 'Feeder', 0, 4, 4, 10, 100000, 200000, 1000000, 2000000, 'TA', NULL, 52, '2022-03-22 16:19:57', '2022-03-22 16:21:48', NULL),
(8, 'Feeder', 0, 4, 3, 8, 200000, 30000, 1600000, 240000, 'TA', NULL, 52, '2022-03-22 16:20:05', '2022-03-22 16:21:48', NULL),
(9, 'Feeder', 0, 5, 4, 43, 100000, 200000, 4300000, 8600000, 'WITEL', NULL, 52, '2022-03-22 16:51:22', '2022-03-24 09:05:57', NULL),
(10, 'Feeder', 0, 8, 1, 10, 10000, 2000, 100000, 20000, 'WITEL', NULL, 52, '2022-03-22 17:33:52', '2022-03-24 12:22:18', NULL),
(11, 'Feeder', 0, 10, 1, 10, 0, 0, 0, 0, 'TA', NULL, 36, '2022-03-24 14:01:30', NULL, NULL),
(12, 'Feeder', 0, 10, 2, 20, 0, 0, 0, 0, 'TA', NULL, 36, '2022-03-24 14:01:40', NULL, NULL),
(13, 'Feeder', 0, 11, 2, 1, 300000, 40000, 300000, 40000, 'WITEL', NULL, 36, '2022-03-25 09:06:37', '2022-03-25 09:10:17', NULL),
(14, 'Feeder', 0, 13, 2, 100, 300000, 40000, 30000000, 4000000, 'WITEL', NULL, 52, '2022-03-25 14:23:11', '2022-03-25 14:28:38', NULL),
(15, 'Feeder', 0, 13, 3, 200, 200000, 30000, 40000000, 6000000, 'WITEL', NULL, 52, '2022-03-25 14:23:24', '2022-03-25 14:28:38', NULL),
(16, 'Feeder', 0, 9, 1, 10, 0, 0, 0, 0, 'TA', 1, 52, '2022-03-26 08:34:24', '2022-03-27 21:37:19', NULL),
(17, 'Feeder', 0, 14, 4, 20, 0, 0, 0, 0, 'WITEL', 7, 36, '2022-03-27 23:13:58', '2022-03-27 23:20:30', NULL),
(18, 'Feeder', 0, 6, 3, 10, 0, 0, 0, 0, 'WITEL', 5, 36, '2022-03-28 15:41:03', '2022-03-28 15:42:58', NULL),
(19, 'Feeder', 0, 15, 1, 1000, 0, 0, 0, 0, 'TA', NULL, 55, '2022-03-28 20:26:00', '2022-03-28 20:27:16', NULL),
(20, 'Feeder', 0, 15, 4, 10, 100000, 200000, 1000000, 2000000, 'WITEL', 7, 36, '2022-03-28 20:26:11', '2022-04-06 09:44:05', NULL),
(21, 'Feeder', 0, 16, 1, 10, 10000, 2000, 100000, 20000, 'WITEL', 4, 52, '2022-03-30 06:31:29', '2022-03-30 06:35:23', NULL),
(22, 'Feeder', 0, 16, 3, 10, 0, 0, 0, 0, 'TA', 5, 52, '2022-03-30 06:31:52', '2022-03-30 06:34:08', NULL),
(23, 'Feeder', 0, 17, 2, 10, 300000, 40000, 3000000, 400000, 'WITEL', 6, 52, '2022-03-30 17:33:38', '2022-03-30 17:41:56', NULL),
(24, 'Feeder', 0, 17, 3, 10, 200000, 30000, 2000000, 300000, 'WITEL', 5, 52, '2022-03-30 17:34:18', '2022-03-30 17:41:56', NULL),
(25, 'Feeder', 0, 18, 4, 12, 0, 0, 0, 0, 'TA', NULL, 52, '2022-04-04 09:45:56', NULL, NULL),
(26, 'Feeder', 0, 24, 2, 123, 0, 0, 0, 0, 'TA', NULL, 36, '2022-04-05 20:09:41', NULL, NULL),
(27, 'Feeder', 0, 20, 4, 123, 0, 0, 0, 0, 'TA', NULL, 36, '2022-04-05 20:16:52', NULL, NULL),
(28, 'Feeder', 0, 25, 3, 12, 0, 0, 0, 0, 'WITEL', 5, 36, '2022-04-05 20:23:12', '2022-04-05 20:25:25', NULL),
(29, 'Feeder', 0, 19, 1, 1, 10000, 2000, 10000, 2000, 'WITEL', 4, 36, '2022-04-05 20:33:07', '2022-04-05 20:34:16', NULL),
(30, 'Feeder', 0, 27, 2, 10, 300000, 40000, 3000000, 400000, 'WITEL', 6, 55, '2022-04-07 19:24:46', '2022-04-07 19:42:42', NULL),
(31, 'Feeder', 0, 29, 1, 1000, 0, 0, 0, 0, 'TA', NULL, 52, '2022-04-11 20:29:00', NULL, NULL),
(32, 'Feeder', 0, 29, 1, 17, 0, 0, 0, 0, 'TA', NULL, 52, '2022-04-12 11:12:42', NULL, NULL),
(33, 'Feeder', 0, 29, 2, 10, 0, 0, 0, 0, 'TA', NULL, 52, '2022-04-17 20:30:45', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_odc`
--

CREATE TABLE `project_odc` (
  `project_odc_id` int(11) NOT NULL,
  `odc` varchar(255) NOT NULL,
  `capacity` varchar(20) NOT NULL,
  `address` longtext NOT NULL,
  `lg` varchar(20) NOT NULL,
  `lt` varchar(20) NOT NULL,
  `benchmark_address` text NOT NULL,
  `port` varchar(20) NOT NULL,
  `core` varchar(20) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL,
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `project_odp`
--

CREATE TABLE `project_odp` (
  `project_odp_id` int(11) NOT NULL,
  `odp` varchar(255) NOT NULL,
  `kukd` varchar(20) NOT NULL,
  `dropcore` varchar(255) NOT NULL,
  `odp_valid_3` varchar(20) DEFAULT NULL,
  `hasil_ukur_odp_valid_3` varchar(20) DEFAULT NULL,
  `odp_valid_4` varchar(20) DEFAULT NULL,
  `hasil_ukur_odp_valid_4` varchar(20) DEFAULT NULL,
  `address` longtext NOT NULL,
  `benchmark_address` longtext NOT NULL,
  `lg` varchar(20) NOT NULL,
  `lt` varchar(20) NOT NULL,
  `core` varchar(20) NOT NULL,
  `core_opsi` varchar(20) DEFAULT NULL,
  `distribusi_core` varchar(12) NOT NULL,
  `distribusi_core_opsi` varchar(12) NOT NULL,
  `capacity` varchar(20) NOT NULL,
  `note` longtext NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL,
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `project_penggelaran`
--

CREATE TABLE `project_penggelaran` (
  `project_penggelaran_id` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `project_sitax`
--

CREATE TABLE `project_sitax` (
  `sitax_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `sitax_list` varchar(255) DEFAULT NULL,
  `sitax_total` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project_sitax`
--

INSERT INTO `project_sitax` (`sitax_id`, `project_id`, `sitax_list`, `sitax_total`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, NULL, 0, '2022-03-22 09:17:45', NULL, NULL),
(2, 2, '[\"rt\",\"rw\",\"kelurahan\"]', 1000000, '2022-03-22 15:55:51', NULL, NULL),
(3, 8, '[\"rt\",\"rw\"]', 1000000, '2022-03-22 17:31:55', NULL, NULL),
(4, 10, '[\"rt\",\"rw\",\"kelurahan\"]', 10, '2022-03-24 14:01:16', NULL, NULL),
(5, 11, NULL, 0, '2022-03-25 09:06:23', NULL, NULL),
(6, 13, '[\"rt\",\"rw\",\"kelurahan\",\"lain-lain\"]', 100000, '2022-03-25 14:22:53', NULL, NULL),
(7, 9, '[\"rt\",\"rw\",\"kelurahan\"]', 10000000, '2022-03-26 08:34:12', NULL, NULL),
(8, 14, NULL, 0, '2022-03-27 23:13:20', NULL, NULL),
(9, 15, NULL, 0, '2022-03-28 20:25:48', NULL, NULL),
(10, 16, '[\"rt\",\"lain-lain\",\"rw\"]', 10000000, '2022-03-30 06:30:43', NULL, NULL),
(11, 17, '[\"rt\",\"rw\",\"kelurahan\",\"pu\",\"lain-lain\"]', 3000000, '2022-03-30 17:28:36', NULL, NULL),
(12, 24, '[\"rw\",\"rt\"]', 1000000, '2022-04-05 20:07:58', NULL, NULL),
(13, 20, NULL, 0, '2022-04-05 20:16:43', NULL, NULL),
(14, 25, '[\"rw\",\"rt\"]', 1233333, '2022-04-05 20:22:44', NULL, NULL),
(15, 19, '[\"rw\",\"rt\"]', 1000000, '2022-04-05 20:32:44', NULL, NULL),
(16, 27, NULL, 0, '2022-04-07 19:24:10', NULL, NULL),
(17, 29, '[\"rw\",\"kelurahan\",\"rt\"]', 1000000, '2022-04-11 20:28:45', NULL, NULL),
(18, 30, '[\"rt\",\"rw\"]', 1000000, '2022-04-17 22:21:54', NULL, NULL),
(19, 31, '[\"rt\",\"rw\",\"kelurahan\"]', 3000000, '2022-04-22 15:20:26', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_survey`
--

CREATE TABLE `project_survey` (
  `survey_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `survey_file` varchar(100) NOT NULL,
  `direktori` varchar(255) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project_survey`
--

INSERT INTO `project_survey` (`survey_id`, `project_id`, `survey_file`, `direktori`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, '623931d0640de2022_03_22.png', 'survey', '2022-03-22 09:17:52', NULL, NULL),
(2, 1, '62393e090aeaa2022_03_22.png', 'instalasi', '2022-03-22 10:10:01', NULL, NULL),
(3, 1, '62393e154c5cd2022_03_22.png', 'terminasi', '2022-03-22 10:10:13', NULL, NULL),
(4, 1, '62393e502f9c62022_03_22.png', 'labeling', '2022-03-22 10:11:12', NULL, NULL),
(5, 2, '62398f2b8456f2022_03_22.jpeg', 'survey', '2022-03-22 15:56:11', NULL, NULL),
(6, 8, '6239a5f140b5f2022_03_22.jpeg', 'survey', '2022-03-22 17:33:21', NULL, NULL),
(7, 8, '6239a845c6ca82022_03_22.jpeg', 'survey', '2022-03-22 17:43:17', NULL, NULL),
(8, 8, '6239aa49dcaa52022_03_22.pdf', 'survey', '2022-03-22 17:51:53', NULL, NULL),
(9, 10, '623c172d47eae2022_03_24.pdf', 'survey', '2022-03-24 14:01:01', NULL, NULL),
(10, 10, '623c2b04105252022_03_24.pdf', 'instalasi', '2022-03-24 15:25:40', NULL, NULL),
(11, 16, '624396b64a3432022_03_29.pdf', 'survey', '2022-03-30 06:31:02', NULL, NULL),
(12, 16, '6243987bce1292022_03_29.pdf', 'instalasi', '2022-03-30 06:38:35', NULL, NULL),
(13, 16, '624398b6e7d132022_03_29.pdf', 'terminasi', '2022-03-30 06:39:34', NULL, NULL),
(14, 16, '624398d38629e2022_03_29.pdf', 'terminasi', '2022-03-30 06:40:03', NULL, NULL),
(15, 16, '6243999dd5f092022_03_29.pdf', 'labeling', '2022-03-30 06:43:25', NULL, NULL),
(16, 17, '6244310f393e52022_03_30.pdf', 'survey', '2022-03-30 17:29:35', NULL, NULL),
(17, 17, '624431259cf4f2022_03_30.pdf', 'survey', '2022-03-30 17:29:57', NULL, NULL),
(18, 17, '624435e8d0f932022_03_30.jpeg', 'terminasi', '2022-03-30 17:50:16', NULL, NULL),
(19, 17, '62443801a125f2022_03_30.jpeg', 'labeling', '2022-03-30 17:59:13', NULL, NULL),
(20, 17, '624438371e2d62022_03_30.jpeg', 'labeling', '2022-03-30 18:00:07', '2022-03-30 18:00:09', '2022-03-30 11:00:09'),
(21, 17, '62443850193ee2022_03_30.jpeg', 'labeling', '2022-03-30 18:00:32', NULL, NULL),
(22, 24, '624c3f70241eb2022_04_05.png', 'survey', '2022-04-05 20:09:04', NULL, NULL),
(23, 20, '624c4155cbc2a2022_04_05.jpeg', 'survey', '2022-04-05 20:17:09', NULL, NULL),
(24, 25, '624c42b1bdc932022_04_05.jpeg', 'survey', '2022-04-05 20:22:57', NULL, NULL),
(25, 19, '624c4508bd9312022_04_05.jpeg', 'survey', '2022-04-05 20:32:56', NULL, NULL),
(26, 19, '624c45a73c0822022_04_05.jpeg', 'instalasi', '2022-04-05 20:35:35', NULL, NULL),
(27, 19, '624c4692e8b4f2022_04_05.jpeg', 'terminasi', '2022-04-05 20:39:30', NULL, NULL),
(28, 19, '624c489a970c62022_04_05.jpeg', 'labeling', '2022-04-05 20:48:10', NULL, NULL),
(29, 27, '624ed7fb717912022_04_07.png', 'survey', '2022-04-07 19:24:27', NULL, NULL),
(30, 27, '624edd35395de2022_04_07.png', 'instalasi', '2022-04-07 19:46:45', NULL, NULL),
(31, 30, '625c30f316e5c2022_04_17.png', 'survey', '2022-04-17 22:23:31', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_user`
--

CREATE TABLE `project_user` (
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `userCode` int(11) NOT NULL,
  `user_leader` int(1) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project_user`
--

INSERT INTO `project_user` (`user_id`, `project_id`, `userCode`, `user_leader`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, 36, 1, '2022-03-22 09:17:08', '2022-03-22 12:49:51', '2022-03-22 05:49:51'),
(2, 1, 52, 0, '2022-03-22 09:17:08', '2022-03-22 12:49:51', '2022-03-22 05:49:51'),
(3, 1, 36, 1, '2022-03-22 12:49:51', NULL, NULL),
(4, 1, 52, 0, '2022-03-22 12:49:51', NULL, NULL),
(5, 2, 55, 1, '2022-03-22 15:55:06', '2022-04-22 14:47:06', '2022-04-22 07:47:06'),
(6, 2, 52, 0, '2022-03-22 15:55:06', '2022-04-22 14:47:06', '2022-04-22 07:47:06'),
(7, 3, 52, 0, '2022-03-22 16:13:10', '2022-04-21 13:17:46', '2022-04-21 06:17:46'),
(8, 3, 55, 1, '2022-03-22 16:13:10', '2022-04-21 13:17:46', '2022-04-21 06:17:46'),
(9, 4, 52, 0, '2022-03-22 16:19:27', '2022-04-25 12:25:25', '2022-04-25 05:25:25'),
(10, 4, 55, 1, '2022-03-22 16:19:27', '2022-04-25 12:25:25', '2022-04-25 05:25:25'),
(11, 5, 52, 0, '2022-03-22 16:50:10', NULL, NULL),
(12, 5, 55, 1, '2022-03-22 16:50:10', NULL, NULL),
(13, 7, 55, 0, '2022-03-22 17:27:15', NULL, NULL),
(14, 7, 52, 1, '2022-03-22 17:27:15', NULL, NULL),
(15, 8, 52, 1, '2022-03-22 17:31:26', NULL, NULL),
(16, 8, 55, 0, '2022-03-22 17:31:26', NULL, NULL),
(17, 10, 52, 1, '2022-03-24 13:55:13', NULL, NULL),
(18, 10, 53, 0, '2022-03-24 13:55:13', NULL, NULL),
(19, 11, 52, 1, '2022-03-24 15:29:34', NULL, NULL),
(20, 11, 55, 0, '2022-03-24 15:29:34', NULL, NULL),
(21, 9, 52, 1, '2022-03-25 12:51:07', NULL, NULL),
(22, 9, 56, 0, '2022-03-25 12:51:07', NULL, NULL),
(23, 13, 52, 1, '2022-03-25 14:19:20', NULL, NULL),
(24, 13, 56, 0, '2022-03-25 14:19:20', NULL, NULL),
(25, 14, 52, 1, '2022-03-27 23:13:07', NULL, NULL),
(26, 14, 56, 0, '2022-03-27 23:13:07', NULL, NULL),
(27, 6, 36, 1, '2022-03-28 15:40:51', NULL, NULL),
(28, 6, 52, 0, '2022-03-28 15:40:51', NULL, NULL),
(29, 15, 52, 1, '2022-03-28 20:24:53', NULL, NULL),
(30, 15, 56, 0, '2022-03-28 20:24:53', NULL, NULL),
(31, 16, 52, 1, '2022-03-30 06:25:24', NULL, NULL),
(32, 16, 56, 0, '2022-03-30 06:25:24', NULL, NULL),
(33, 17, 52, 0, '2022-03-30 17:12:59', '2022-03-30 17:45:21', '2022-03-30 10:45:21'),
(34, 17, 56, 1, '2022-03-30 17:12:59', '2022-03-30 17:45:21', '2022-03-30 10:45:21'),
(35, 17, 52, 0, '2022-03-30 17:45:21', NULL, NULL),
(36, 17, 56, 1, '2022-03-30 17:45:21', NULL, NULL),
(37, 17, 55, 0, '2022-03-30 17:45:21', NULL, NULL),
(38, 18, 52, 0, '2022-03-30 18:41:28', '2022-04-06 09:39:52', '2022-04-06 02:39:52'),
(39, 18, 56, 1, '2022-03-30 18:41:28', '2022-04-06 09:39:52', '2022-04-06 02:39:52'),
(40, 19, 52, 1, '2022-04-02 21:48:55', NULL, NULL),
(41, 19, 53, 0, '2022-04-02 21:48:55', NULL, NULL),
(42, 20, 52, 0, '2022-04-03 19:51:58', NULL, NULL),
(43, 20, 53, 1, '2022-04-03 19:51:58', NULL, NULL),
(44, 21, 52, 1, '2022-04-05 15:10:40', NULL, NULL),
(45, 21, 56, 0, '2022-04-05 15:10:40', NULL, NULL),
(46, 22, 55, 1, '2022-04-05 15:20:32', NULL, NULL),
(47, 22, 52, 0, '2022-04-05 15:20:32', NULL, NULL),
(48, 23, 55, 1, '2022-04-05 19:15:30', NULL, NULL),
(49, 23, 52, 0, '2022-04-05 19:15:30', NULL, NULL),
(50, 24, 52, 1, '2022-04-05 20:04:00', NULL, NULL),
(51, 24, 53, 0, '2022-04-05 20:04:00', NULL, NULL),
(52, 25, 52, 1, '2022-04-05 20:20:52', NULL, NULL),
(53, 25, 54, 0, '2022-04-05 20:20:52', NULL, NULL),
(54, 18, 52, 0, '2022-04-06 09:31:40', '2022-04-06 09:39:52', '2022-04-06 02:39:52'),
(55, 18, 56, 1, '2022-04-06 09:31:40', '2022-04-06 09:39:52', '2022-04-06 02:39:52'),
(56, 18, 52, 1, '2022-04-06 09:36:19', '2022-04-06 09:39:52', '2022-04-06 02:39:52'),
(57, 18, 56, 0, '2022-04-06 09:36:19', '2022-04-06 09:39:52', '2022-04-06 02:39:52'),
(58, 18, 57, 0, '2022-04-06 09:36:19', '2022-04-06 09:39:52', '2022-04-06 02:39:52'),
(59, 18, 52, 0, '2022-04-06 09:39:52', NULL, NULL),
(60, 18, 56, 0, '2022-04-06 09:39:52', NULL, NULL),
(61, 18, 55, 1, '2022-04-06 09:39:52', NULL, NULL),
(62, 27, 52, 0, '2022-04-07 19:23:09', NULL, NULL),
(63, 27, 55, 1, '2022-04-07 19:23:09', NULL, NULL),
(64, 28, 52, 1, '2022-04-07 20:51:24', NULL, NULL),
(65, 28, 56, 0, '2022-04-07 20:51:24', NULL, NULL),
(66, 29, 52, 1, '2022-04-11 20:25:58', NULL, NULL),
(67, 29, 56, 0, '2022-04-11 20:25:58', NULL, NULL),
(68, 3, 52, 0, '2022-04-17 21:15:37', '2022-04-21 13:17:46', '2022-04-21 06:17:46'),
(69, 3, 55, 1, '2022-04-17 21:15:37', '2022-04-21 13:17:46', '2022-04-21 06:17:46'),
(70, 3, 56, 0, '2022-04-17 21:15:37', '2022-04-21 13:17:46', '2022-04-21 06:17:46'),
(71, 30, 55, 1, '2022-04-17 22:21:37', NULL, NULL),
(72, 30, 52, 0, '2022-04-17 22:21:37', NULL, NULL),
(73, 3, 52, 1, '2022-04-21 13:17:46', NULL, NULL),
(74, 3, 55, 0, '2022-04-21 13:17:46', NULL, NULL),
(75, 3, 56, 0, '2022-04-21 13:17:46', NULL, NULL),
(76, 2, 55, 1, '2022-04-22 14:47:06', NULL, NULL),
(77, 2, 52, 0, '2022-04-22 14:47:06', NULL, NULL),
(78, 31, 52, 1, '2022-04-22 15:20:08', NULL, NULL),
(79, 31, 56, 0, '2022-04-22 15:20:08', NULL, NULL),
(80, 12, 56, 1, '2022-04-24 16:15:14', NULL, NULL),
(81, 12, 52, 0, '2022-04-24 16:15:14', NULL, NULL),
(82, 4, 52, 0, '2022-04-25 12:25:25', NULL, NULL),
(83, 4, 55, 1, '2022-04-25 12:25:25', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

CREATE TABLE `region` (
  `region_id` int(11) NOT NULL,
  `region_name` varchar(255) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `region`
--

INSERT INTO `region` (`region_id`, `region_name`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'Region 1', '2022-03-21 23:42:54', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `roleCode` int(11) NOT NULL,
  `role` varchar(75) NOT NULL,
  `type` enum('Master','Public') NOT NULL DEFAULT 'Public',
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`roleCode`, `role`, `type`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'Super Admin', 'Master', '2022-02-26 03:06:51', NULL, NULL),
(10, 'Project Manager', 'Public', '2022-03-19 11:11:59', NULL, NULL),
(11, 'Technician', 'Public', '2022-03-19 14:14:45', NULL, NULL),
(12, 'Admin HO', 'Public', '2022-03-21 10:04:09', NULL, NULL),
(13, 'Warehouse', 'Public', '2022-03-21 10:04:29', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE `role_permission` (
  `rpCode` int(11) NOT NULL,
  `permissionCode` int(11) NOT NULL,
  `roleCode` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `role_permission`
--

INSERT INTO `role_permission` (`rpCode`, `permissionCode`, `roleCode`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, 1, '2022-03-19 09:04:40', NULL, NULL),
(2, 2, 1, '2022-03-19 09:04:40', NULL, NULL),
(3, 3, 1, '2022-03-19 09:04:40', NULL, NULL),
(4, 4, 1, '2022-03-19 09:04:40', NULL, NULL),
(5, 5, 1, '2022-03-19 09:04:40', NULL, NULL),
(6, 6, 1, '2022-03-19 09:04:40', NULL, NULL),
(7, 7, 1, '2022-03-19 09:04:40', NULL, NULL),
(8, 8, 1, '2022-03-19 09:04:40', NULL, NULL),
(9, 9, 1, '2022-03-19 09:04:40', NULL, NULL),
(10, 10, 1, '2022-03-19 09:04:40', NULL, NULL),
(11, 11, 1, '2022-03-19 09:04:40', NULL, NULL),
(12, 12, 1, '2022-03-19 09:04:40', NULL, NULL),
(13, 13, 1, '2022-03-19 09:04:40', NULL, NULL),
(14, 14, 1, '2022-03-19 09:04:40', NULL, NULL),
(15, 15, 1, '2022-03-19 09:04:40', NULL, NULL),
(16, 16, 1, '2022-03-19 09:04:40', NULL, NULL),
(17, 17, 1, '2022-03-19 09:04:40', NULL, NULL),
(18, 18, 1, '2022-03-19 09:04:40', NULL, NULL),
(19, 19, 1, '2022-03-19 09:04:40', NULL, NULL),
(20, 20, 1, '2022-03-19 09:04:40', NULL, NULL),
(21, 21, 1, '2022-03-19 09:04:40', NULL, NULL),
(22, 22, 1, '2022-03-19 09:04:40', NULL, NULL),
(23, 23, 1, '2022-03-19 09:04:40', NULL, NULL),
(24, 24, 1, '2022-03-19 09:04:40', NULL, NULL),
(25, 25, 1, '2022-03-19 09:04:40', NULL, NULL),
(26, 26, 1, '2022-03-19 09:04:40', NULL, NULL),
(27, 27, 1, '2022-03-19 09:04:40', NULL, NULL),
(28, 28, 1, '2022-03-19 09:04:40', NULL, NULL),
(29, 29, 1, '2022-03-19 09:04:40', NULL, NULL),
(30, 30, 1, '2022-03-19 09:04:40', NULL, NULL),
(31, 31, 1, '2022-03-19 09:04:40', NULL, NULL),
(32, 32, 1, '2022-03-19 09:04:40', NULL, NULL),
(33, 33, 1, '2022-03-19 09:04:40', NULL, NULL),
(34, 34, 1, '2022-03-19 09:04:40', NULL, NULL),
(35, 35, 1, '2022-03-19 09:04:40', NULL, NULL),
(36, 36, 1, '2022-03-19 09:04:40', NULL, NULL),
(37, 37, 1, '2022-03-19 09:04:40', NULL, NULL),
(41, 41, 1, '2022-03-19 09:04:40', NULL, NULL),
(42, 42, 1, '2022-03-19 09:04:40', NULL, NULL),
(43, 43, 1, '2022-03-19 09:04:40', NULL, NULL),
(44, 44, 1, '2022-03-19 09:04:40', NULL, NULL),
(45, 45, 1, '2022-03-19 09:04:40', NULL, NULL),
(46, 46, 1, '2022-03-19 09:04:40', NULL, NULL),
(47, 47, 1, '2022-03-19 09:04:40', NULL, NULL),
(48, 48, 1, '2022-03-19 09:04:40', NULL, NULL),
(49, 49, 1, '2022-03-19 09:04:40', NULL, NULL),
(50, 50, 1, '2022-03-19 09:04:40', NULL, NULL),
(51, 51, 1, '2022-03-19 09:04:40', NULL, NULL),
(52, 52, 1, '2022-03-19 09:04:40', NULL, NULL),
(53, 53, 1, '2022-03-19 09:04:40', NULL, NULL),
(54, 54, 1, '2022-03-19 09:04:40', NULL, NULL),
(55, 55, 1, '2022-03-19 09:04:40', NULL, NULL),
(56, 56, 1, '2022-03-19 09:04:40', NULL, NULL),
(57, 57, 1, '2022-03-19 09:04:40', NULL, NULL),
(58, 58, 1, '2022-03-19 09:04:40', NULL, NULL),
(59, 59, 1, '2022-03-19 09:04:40', NULL, NULL),
(60, 60, 1, '2022-03-19 09:04:40', NULL, NULL),
(61, 61, 1, '2022-03-19 09:04:40', NULL, NULL),
(62, 62, 1, '2022-03-19 09:04:40', NULL, NULL),
(63, 63, 1, '2022-03-19 09:04:40', NULL, NULL),
(64, 64, 1, '2022-03-19 09:04:40', NULL, NULL),
(65, 65, 1, '2022-03-19 09:04:40', NULL, NULL),
(66, 66, 1, '2022-03-19 09:04:40', NULL, NULL),
(67, 67, 1, '2022-03-19 09:04:40', NULL, NULL),
(68, 68, 1, '2022-03-19 09:04:40', NULL, NULL),
(69, 69, 1, '2022-03-19 09:04:40', NULL, NULL),
(70, 70, 1, '2022-03-19 09:04:40', NULL, NULL),
(71, 71, 1, '2022-03-19 09:04:40', NULL, NULL),
(72, 72, 1, '2022-03-19 09:04:40', NULL, NULL),
(73, 73, 1, '2022-03-19 09:04:40', NULL, NULL),
(74, 74, 1, '2022-03-19 09:04:40', NULL, NULL),
(75, 75, 1, '2022-03-19 09:04:40', NULL, NULL),
(76, 76, 1, '2022-03-19 09:04:40', NULL, NULL),
(77, 77, 1, '2022-03-19 09:04:40', NULL, NULL),
(78, 78, 1, '2022-03-19 09:04:40', NULL, NULL),
(79, 79, 1, '2022-03-19 09:04:40', NULL, NULL),
(80, 80, 1, '2022-03-19 09:04:40', NULL, NULL),
(81, 81, 1, '2022-03-19 09:04:40', NULL, NULL),
(82, 82, 1, '2022-03-19 09:04:40', NULL, NULL),
(83, 83, 1, '2022-03-19 09:04:40', NULL, NULL),
(84, 84, 1, '2022-03-19 09:04:40', NULL, NULL),
(85, 85, 1, '2022-03-19 09:04:40', NULL, NULL),
(86, 86, 1, '2022-03-19 09:04:40', NULL, NULL),
(87, 87, 1, '2022-03-19 09:04:40', NULL, NULL),
(88, 88, 1, '2022-03-19 09:04:40', NULL, NULL),
(89, 89, 1, '2022-03-19 09:04:40', NULL, NULL),
(90, 90, 1, '2022-03-19 09:04:40', NULL, NULL),
(91, 91, 1, '2022-03-19 09:04:40', NULL, NULL),
(92, 92, 1, '2022-03-19 09:04:40', NULL, NULL),
(93, 93, 1, '2022-03-19 09:04:40', NULL, NULL),
(94, 94, 1, '2022-03-19 09:04:40', NULL, NULL),
(95, 95, 1, '2022-03-19 09:04:40', NULL, NULL),
(96, 96, 1, '2022-03-19 09:04:40', NULL, NULL),
(97, 97, 1, '2022-03-19 09:04:40', NULL, NULL),
(98, 98, 1, '2022-03-19 09:04:40', NULL, NULL),
(99, 99, 1, '2022-03-19 09:04:40', NULL, NULL),
(100, 100, 1, '2022-03-19 09:04:40', NULL, NULL),
(101, 101, 1, '2022-03-19 09:04:40', NULL, NULL),
(102, 102, 1, '2022-03-19 09:04:40', NULL, NULL),
(103, 103, 1, '2022-03-19 09:04:40', NULL, NULL),
(104, 104, 1, '2022-03-19 09:04:40', NULL, NULL),
(105, 105, 1, '2022-03-19 09:04:40', NULL, NULL),
(106, 106, 1, '2022-03-19 09:04:40', NULL, NULL),
(107, 107, 1, '2022-03-19 09:04:40', NULL, NULL),
(108, 108, 1, '2022-03-19 09:04:40', NULL, NULL),
(109, 109, 1, '2022-03-19 09:04:40', NULL, NULL),
(110, 110, 1, '2022-03-19 09:04:40', NULL, NULL),
(111, 111, 1, '2022-03-19 09:04:40', NULL, NULL),
(112, 112, 1, '2022-03-19 09:04:40', NULL, NULL),
(113, 113, 1, '2022-03-19 09:04:40', NULL, NULL),
(114, 114, 1, '2022-03-19 09:04:40', NULL, NULL),
(115, 115, 1, '2022-03-19 09:04:40', NULL, NULL),
(116, 116, 1, '2022-03-19 09:04:40', NULL, NULL),
(117, 81, 10, '2022-03-19 11:12:49', NULL, NULL),
(118, 82, 10, '2022-03-19 11:15:17', NULL, NULL),
(119, 9, 10, '2022-03-19 11:17:40', NULL, NULL),
(120, 85, 10, '2022-03-19 11:18:32', '2022-03-21 10:19:07', '2022-03-21 03:19:07'),
(121, 86, 10, '2022-03-19 11:19:17', '2022-03-21 10:19:08', '2022-03-21 03:19:08'),
(122, 87, 10, '2022-03-19 11:19:29', NULL, NULL),
(123, 89, 10, '2022-03-19 11:32:01', '2022-03-21 10:20:47', '2022-03-21 03:20:47'),
(124, 90, 10, '2022-03-19 11:33:49', '2022-03-21 10:20:48', '2022-03-21 03:20:48'),
(125, 98, 10, '2022-03-19 11:34:18', '2022-03-21 10:20:49', '2022-03-21 03:20:49'),
(126, 93, 10, '2022-03-19 11:34:29', '2022-03-21 10:20:50', '2022-03-21 03:20:50'),
(127, 50, 10, '2022-03-19 11:34:42', NULL, NULL),
(128, 96, 10, '2022-03-19 12:00:29', '2022-03-21 10:20:51', '2022-03-21 03:20:51'),
(129, 9, 12, '2022-03-21 10:04:51', NULL, NULL),
(130, 10, 12, '2022-03-21 10:04:57', NULL, NULL),
(131, 4, 12, '2022-03-21 10:05:00', '2022-03-21 10:05:08', '2022-03-21 03:05:08'),
(132, 11, 12, '2022-03-21 10:05:12', NULL, NULL),
(133, 12, 12, '2022-03-21 10:05:18', NULL, NULL),
(134, 13, 12, '2022-03-21 10:05:29', NULL, NULL),
(135, 14, 12, '2022-03-21 10:05:33', NULL, NULL),
(136, 6, 12, '2022-03-21 10:05:40', '2022-03-21 10:05:54', '2022-03-21 03:05:54'),
(137, 7, 12, '2022-03-21 10:05:43', '2022-03-21 10:05:55', '2022-03-21 03:05:55'),
(138, 15, 12, '2022-03-21 10:05:51', '2022-03-21 10:11:22', '2022-03-21 03:11:22'),
(139, 16, 12, '2022-03-21 10:06:03', '2022-03-21 10:11:19', '2022-03-21 03:11:19'),
(140, 17, 12, '2022-03-21 10:06:14', NULL, NULL),
(141, 18, 12, '2022-03-21 10:06:43', NULL, NULL),
(142, 19, 12, '2022-03-21 10:06:47', NULL, NULL),
(143, 20, 12, '2022-03-21 10:06:51', NULL, NULL),
(144, 21, 12, '2022-03-21 10:06:55', NULL, NULL),
(145, 22, 12, '2022-03-21 10:07:01', NULL, NULL),
(146, 23, 12, '2022-03-21 10:07:06', NULL, NULL),
(147, 24, 12, '2022-03-21 10:07:10', NULL, NULL),
(148, 25, 12, '2022-03-21 10:07:17', NULL, NULL),
(149, 26, 12, '2022-03-21 10:07:28', NULL, NULL),
(150, 27, 12, '2022-03-21 10:07:35', NULL, NULL),
(151, 28, 12, '2022-03-21 10:07:48', NULL, NULL),
(152, 29, 12, '2022-03-21 10:07:51', NULL, NULL),
(153, 30, 12, '2022-03-21 10:07:55', NULL, NULL),
(154, 31, 12, '2022-03-21 10:07:58', NULL, NULL),
(155, 32, 12, '2022-03-21 10:08:03', NULL, NULL),
(156, 33, 12, '2022-03-21 10:08:07', NULL, NULL),
(157, 34, 12, '2022-03-21 10:08:12', NULL, NULL),
(158, 35, 12, '2022-03-21 10:08:16', NULL, NULL),
(159, 36, 12, '2022-03-21 10:08:22', NULL, NULL),
(160, 41, 12, '2022-03-21 10:08:42', NULL, NULL),
(161, 42, 12, '2022-03-21 10:08:46', NULL, NULL),
(162, 43, 12, '2022-03-21 10:08:51', NULL, NULL),
(163, 44, 12, '2022-03-21 10:08:56', NULL, NULL),
(164, 50, 12, '2022-03-21 10:09:10', NULL, NULL),
(165, 51, 12, '2022-03-21 10:09:21', NULL, NULL),
(166, 52, 12, '2022-03-21 10:09:24', NULL, NULL),
(167, 53, 12, '2022-03-21 10:09:28', NULL, NULL),
(168, 48, 12, '2022-03-21 10:09:33', '2022-03-21 10:09:40', '2022-03-21 03:09:40'),
(169, 54, 12, '2022-03-21 10:09:49', NULL, NULL),
(170, 55, 12, '2022-03-21 10:09:56', NULL, NULL),
(171, 45, 12, '2022-03-21 10:10:05', NULL, NULL),
(172, 46, 12, '2022-03-21 10:10:09', NULL, NULL),
(173, 47, 12, '2022-03-21 10:10:32', NULL, NULL),
(174, 48, 12, '2022-03-21 10:10:45', NULL, NULL),
(175, 49, 12, '2022-03-21 10:10:55', NULL, NULL),
(176, 56, 12, '2022-03-21 10:11:34', NULL, NULL),
(177, 57, 12, '2022-03-21 10:11:38', NULL, NULL),
(178, 58, 12, '2022-03-21 10:12:10', NULL, NULL),
(179, 59, 12, '2022-03-21 10:12:20', NULL, NULL),
(180, 60, 12, '2022-03-21 10:12:29', NULL, NULL),
(181, 61, 12, '2022-03-21 10:12:37', NULL, NULL),
(182, 62, 12, '2022-03-21 10:12:54', NULL, NULL),
(183, 63, 12, '2022-03-21 10:13:24', NULL, NULL),
(184, 64, 12, '2022-03-21 10:13:54', NULL, NULL),
(185, 65, 12, '2022-03-21 10:14:10', NULL, NULL),
(186, 66, 12, '2022-03-21 10:14:15', NULL, NULL),
(187, 67, 12, '2022-03-21 10:14:42', NULL, NULL),
(188, 68, 12, '2022-03-21 10:14:50', NULL, NULL),
(189, 69, 12, '2022-03-21 10:14:57', NULL, NULL),
(190, 70, 12, '2022-03-21 10:15:03', NULL, NULL),
(191, 72, 12, '2022-03-21 10:15:24', NULL, NULL),
(192, 73, 12, '2022-03-21 10:15:36', NULL, NULL),
(193, 74, 12, '2022-03-21 10:15:44', NULL, NULL),
(194, 75, 12, '2022-03-21 10:15:56', NULL, NULL),
(195, 76, 12, '2022-03-21 10:16:02', NULL, NULL),
(196, 77, 12, '2022-03-21 10:16:11', NULL, NULL),
(197, 79, 12, '2022-03-21 10:16:39', NULL, NULL),
(198, 81, 13, '2022-03-21 10:17:56', NULL, NULL),
(199, 103, 13, '2022-03-21 10:18:19', NULL, NULL),
(200, 81, 12, '2022-03-21 10:19:34', NULL, NULL),
(201, 85, 12, '2022-03-21 10:19:41', NULL, NULL),
(202, 86, 12, '2022-03-21 10:19:48', NULL, NULL),
(203, 88, 10, '2022-03-21 10:20:41', NULL, NULL),
(204, 83, 10, '2022-03-21 10:21:00', NULL, NULL),
(205, 84, 10, '2022-03-21 10:21:05', NULL, NULL),
(206, 81, 11, '2022-03-21 10:21:33', NULL, NULL),
(207, 89, 11, '2022-03-21 10:21:56', NULL, NULL),
(208, 90, 11, '2022-03-21 10:22:02', NULL, NULL),
(209, 91, 11, '2022-03-21 10:22:11', NULL, NULL),
(210, 92, 11, '2022-03-21 10:22:18', NULL, NULL),
(211, 93, 11, '2022-03-21 10:22:26', NULL, NULL),
(212, 94, 11, '2022-03-21 10:22:32', NULL, NULL),
(213, 95, 11, '2022-03-21 10:22:42', NULL, NULL),
(214, 96, 11, '2022-03-21 10:23:34', NULL, NULL),
(215, 97, 11, '2022-03-21 10:23:40', NULL, NULL),
(216, 98, 11, '2022-03-21 10:23:51', NULL, NULL),
(217, 100, 11, '2022-03-21 10:23:59', NULL, NULL),
(218, 102, 10, '2022-03-21 10:24:45', NULL, NULL),
(219, 101, 10, '2022-03-21 10:24:55', NULL, NULL),
(220, 104, 11, '2022-03-21 10:26:06', NULL, NULL),
(221, 105, 11, '2022-03-21 10:26:13', NULL, NULL),
(222, 107, 11, '2022-03-21 10:26:19', NULL, NULL),
(223, 108, 11, '2022-03-21 10:26:28', NULL, NULL),
(224, 110, 11, '2022-03-21 10:34:38', NULL, NULL),
(225, 112, 11, '2022-03-21 10:34:42', NULL, NULL),
(226, 113, 11, '2022-03-21 10:34:46', NULL, NULL),
(227, 115, 11, '2022-03-21 10:35:13', NULL, NULL),
(228, 106, 10, '2022-03-21 10:36:14', NULL, NULL),
(229, 109, 10, '2022-03-21 10:36:24', NULL, NULL),
(230, 111, 10, '2022-03-21 10:36:29', NULL, NULL),
(231, 114, 10, '2022-03-21 10:36:36', NULL, NULL),
(232, 116, 10, '2022-03-21 10:36:39', NULL, NULL),
(233, 50, 11, '2022-03-21 13:52:11', NULL, NULL),
(234, 80, 13, '2022-03-21 16:36:46', NULL, NULL),
(235, 78, 13, '2022-03-21 16:39:23', NULL, NULL),
(236, 117, 13, '2022-03-22 11:30:52', NULL, NULL),
(237, 118, 13, '2022-03-22 11:30:55', NULL, NULL),
(238, 119, 13, '2022-03-22 11:31:02', NULL, NULL),
(239, 120, 13, '2022-03-22 11:31:06', NULL, NULL),
(240, 11, 10, '2022-03-24 20:02:29', NULL, NULL),
(241, 121, 10, '2022-03-24 21:51:04', NULL, NULL),
(242, 122, 10, '2022-03-24 21:51:22', NULL, NULL),
(243, 37, 10, '2022-03-24 21:54:50', NULL, NULL),
(244, 99, 10, '2022-03-27 23:19:55', NULL, NULL),
(245, 34, 13, '2022-03-30 18:16:55', NULL, NULL),
(246, 126, 13, '2022-03-30 18:18:43', NULL, NULL),
(247, 127, 13, '2022-03-30 18:19:29', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `ruCode` int(11) NOT NULL,
  `userCode` int(11) NOT NULL,
  `roleCode` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`ruCode`, `userCode`, `roleCode`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 36, 1, '2022-02-26 03:19:11', NULL, NULL),
(9, 47, 10, '2022-03-19 11:13:09', NULL, NULL),
(10, 48, 10, '2022-03-19 11:13:25', '2022-03-21 10:57:37', '2022-03-21 03:57:37'),
(11, 49, 13, '2022-03-21 10:47:42', NULL, NULL),
(12, 48, 11, '2022-03-21 10:57:40', NULL, NULL),
(13, 50, 12, '2022-03-21 11:10:15', NULL, NULL),
(14, 51, 10, '2022-03-21 16:44:39', NULL, NULL),
(15, 52, 11, '2022-03-22 11:29:42', NULL, NULL),
(16, 53, 12, '2022-03-22 11:29:53', NULL, NULL),
(17, 54, 13, '2022-03-22 11:30:04', NULL, NULL),
(18, 55, 10, '2022-03-22 11:30:13', NULL, NULL),
(19, 56, 11, '2022-03-24 13:56:56', NULL, NULL),
(20, 57, 12, '2022-03-24 19:20:59', '2022-03-24 19:22:23', '2022-03-24 12:22:23'),
(21, 57, 13, '2022-03-24 19:22:28', NULL, NULL),
(22, 58, 10, '2022-03-24 19:30:53', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_ho`
--

CREATE TABLE `stock_ho` (
  `stock_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stock_price` double NOT NULL,
  `stock_qty` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stock_ho`
--

INSERT INTO `stock_ho` (`stock_id`, `product_id`, `stock_price`, `stock_qty`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, 222000, 779, '2022-03-21 23:52:29', '2022-03-22 18:42:40', NULL),
(2, 4, 99999990, 1, '2022-03-22 17:10:41', '2022-03-22 17:11:31', NULL),
(3, 3, 12000, 999500, '2022-03-22 18:37:16', '2022-03-22 18:42:40', NULL),
(4, 2, 100000, 197000, '2022-03-22 18:37:16', '2022-03-22 18:42:40', NULL),
(5, 1, 10000, 1000, '2022-03-22 18:37:16', NULL, NULL),
(6, 4, 20000, 180000, '2022-03-22 18:37:16', '2022-03-22 18:42:40', NULL),
(7, 1, 787877878, 11, '2022-03-22 18:37:28', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_witel`
--

CREATE TABLE `stock_witel` (
  `stock_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stock_price` double NOT NULL,
  `stock_qty` int(11) NOT NULL,
  `witel_id` int(11) NOT NULL,
  `do_id` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stock_witel`
--

INSERT INTO `stock_witel` (`stock_id`, `product_id`, `stock_price`, `stock_qty`, `witel_id`, `do_id`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, 222000, 26, 1, 1, '2022-03-21 23:53:10', '2022-03-24 12:22:18', NULL),
(2, 1, 222000, 59, 1, 2, '2022-03-22 17:05:40', NULL, NULL),
(3, 4, 99999990, 3, 1, 4, '2022-03-22 17:11:36', '2022-03-24 09:22:05', NULL),
(4, 1, 222000, 111, 1, 5, '2022-03-22 18:43:20', '2022-04-05 20:34:16', NULL),
(5, 3, 12000, 290, 1, 5, '2022-03-22 18:43:20', '2022-03-30 17:41:56', NULL),
(6, 2, 100000, 2879, 1, 5, '2022-03-22 18:43:20', '2022-04-07 19:42:42', NULL),
(7, 4, 20000, 19990, 1, 5, '2022-03-22 18:43:20', '2022-04-06 09:44:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_phone` varchar(16) NOT NULL,
  `supplier_address` text NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `supplier_name`, `supplier_phone`, `supplier_address`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'supplier 2', '080', 'adres1', '2022-03-21 23:43:26', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userCode` int(11) NOT NULL,
  `name` varchar(75) NOT NULL,
  `photo` longtext DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nik_ta` varchar(100) NOT NULL,
  `nik_api` varchar(100) NOT NULL,
  `package_id` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userCode`, `name`, `photo`, `email`, `password`, `nik_ta`, `nik_api`, `package_id`, `createAt`, `updateAt`, `deleteAt`) VALUES
(36, 'Super Admin', NULL, 'su@mail.com', '202cb962ac59075b964b07152d234b70', '123', '123', 1, '2022-02-26 03:18:28', '2022-03-19 09:50:54', NULL),
(52, 'teknisi', NULL, 'teknisi@mail.com', '202cb962ac59075b964b07152d234b70', '123', '123', 1, '2022-03-21 23:45:58', NULL, NULL),
(53, 'admin ho', NULL, 'admin@mail.com', '202cb962ac59075b964b07152d234b70', '123', '123', 0, '2022-03-22 11:28:28', NULL, NULL),
(54, 'admin area/warehouse', NULL, 'wh@mail.com', '202cb962ac59075b964b07152d234b70', '123', '123', 1, '2022-03-22 11:29:08', NULL, NULL),
(55, 'pm', NULL, 'pm@mail.com', '202cb962ac59075b964b07152d234b70', '123', '123', 1, '2022-03-22 11:29:22', NULL, NULL),
(56, 'Teknisi 2', NULL, 'teknisi2@mail.com', '202cb962ac59075b964b07152d234b70', '0', '0', 1, '2022-03-24 13:56:31', NULL, NULL),
(57, 'Keranggan witel', NULL, 'wh1@mail.com', '202cb962ac59075b964b07152d234b70', '0', '0', 2, '2022-03-24 19:19:45', '2022-03-25 12:46:54', NULL),
(58, 'Pm1', NULL, 'pm1@mail.com', '202cb962ac59075b964b07152d234b70', '0', '0', 2, '2022-03-24 19:29:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_permission`
--

CREATE TABLE `user_permission` (
  `upCode` int(11) NOT NULL,
  `userCode` int(11) NOT NULL,
  `permissionCode` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_permission`
--

INSERT INTO `user_permission` (`upCode`, `userCode`, `permissionCode`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 36, 117, '2022-03-21 23:57:45', NULL, NULL),
(2, 36, 118, '2022-03-21 23:57:52', NULL, NULL),
(3, 36, 119, '2022-03-21 23:58:12', NULL, NULL),
(4, 36, 120, '2022-03-21 23:58:18', NULL, NULL),
(5, 36, 121, '2022-03-23 10:38:24', NULL, NULL),
(6, 36, 122, '2022-03-23 10:38:29', NULL, NULL),
(7, 36, 125, '2022-03-24 00:14:18', NULL, NULL),
(8, 36, 126, '2022-03-24 00:14:24', NULL, NULL),
(9, 36, 127, '2022-03-24 07:21:45', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `witel`
--

CREATE TABLE `witel` (
  `witel_id` int(11) NOT NULL,
  `witel_code` varchar(255) NOT NULL,
  `witel_name` varchar(255) NOT NULL,
  `region_id` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `witel`
--

INSERT INTO `witel` (`witel_id`, `witel_code`, `witel_name`, `region_id`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 'witel code 1', 'witel 1', 1, '2022-03-21 23:43:05', NULL, NULL),
(2, 'Krg-001', 'Kranggan', 1, '2022-03-24 19:25:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `witel_user`
--

CREATE TABLE `witel_user` (
  `witel_user_id` int(11) NOT NULL,
  `witel_id` int(11) NOT NULL,
  `userCode` int(11) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updateAt` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleteAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `witel_user`
--

INSERT INTO `witel_user` (`witel_user_id`, `witel_id`, `userCode`, `createAt`, `updateAt`, `deleteAt`) VALUES
(1, 1, 36, '2022-03-21 23:43:17', '2022-03-22 15:52:43', '2022-03-22 08:52:43'),
(2, 1, 55, '2022-03-22 15:52:21', NULL, NULL),
(3, 1, 52, '2022-03-22 15:52:23', NULL, NULL),
(4, 1, 54, '2022-03-22 15:52:28', NULL, NULL),
(5, 1, 36, '2022-03-22 15:52:49', NULL, NULL),
(6, 2, 57, '2022-03-24 19:25:55', NULL, NULL),
(7, 2, 58, '2022-03-24 19:30:07', NULL, NULL),
(8, 2, 36, '2022-03-24 19:33:01', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `designator`
--
ALTER TABLE `designator`
  ADD PRIMARY KEY (`designator_id`);

--
-- Indexes for table `designator_package`
--
ALTER TABLE `designator_package`
  ADD PRIMARY KEY (`designator_package_id`);

--
-- Indexes for table `do`
--
ALTER TABLE `do`
  ADD PRIMARY KEY (`do_id`);

--
-- Indexes for table `do_item`
--
ALTER TABLE `do_item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `job`
--
ALTER TABLE `job`
  ADD PRIMARY KEY (`job_id`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`moduleCode`);

--
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`permissionCode`),
  ADD UNIQUE KEY `permission` (`permission`);

--
-- Indexes for table `po`
--
ALTER TABLE `po`
  ADD PRIMARY KEY (`po_id`);

--
-- Indexes for table `po_item`
--
ALTER TABLE `po_item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `project_cat`
--
ALTER TABLE `project_cat`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `project_feeder`
--
ALTER TABLE `project_feeder`
  ADD PRIMARY KEY (`project_feeder_id`);

--
-- Indexes for table `project_gpon`
--
ALTER TABLE `project_gpon`
  ADD PRIMARY KEY (`project_gpon_id`);

--
-- Indexes for table `project_job`
--
ALTER TABLE `project_job`
  ADD PRIMARY KEY (`project_job_id`);

--
-- Indexes for table `project_khs`
--
ALTER TABLE `project_khs`
  ADD PRIMARY KEY (`khs_id`);

--
-- Indexes for table `project_khs_list`
--
ALTER TABLE `project_khs_list`
  ADD PRIMARY KEY (`khs_list_id`);

--
-- Indexes for table `project_odc`
--
ALTER TABLE `project_odc`
  ADD PRIMARY KEY (`project_odc_id`);

--
-- Indexes for table `project_odp`
--
ALTER TABLE `project_odp`
  ADD PRIMARY KEY (`project_odp_id`);

--
-- Indexes for table `project_penggelaran`
--
ALTER TABLE `project_penggelaran`
  ADD PRIMARY KEY (`project_penggelaran_id`);

--
-- Indexes for table `project_sitax`
--
ALTER TABLE `project_sitax`
  ADD PRIMARY KEY (`sitax_id`);

--
-- Indexes for table `project_survey`
--
ALTER TABLE `project_survey`
  ADD PRIMARY KEY (`survey_id`);

--
-- Indexes for table `project_user`
--
ALTER TABLE `project_user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `region`
--
ALTER TABLE `region`
  ADD PRIMARY KEY (`region_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`roleCode`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`rpCode`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`ruCode`);

--
-- Indexes for table `stock_ho`
--
ALTER TABLE `stock_ho`
  ADD PRIMARY KEY (`stock_id`);

--
-- Indexes for table `stock_witel`
--
ALTER TABLE `stock_witel`
  ADD PRIMARY KEY (`stock_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userCode`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_permission`
--
ALTER TABLE `user_permission`
  ADD PRIMARY KEY (`upCode`);

--
-- Indexes for table `witel`
--
ALTER TABLE `witel`
  ADD PRIMARY KEY (`witel_id`);

--
-- Indexes for table `witel_user`
--
ALTER TABLE `witel_user`
  ADD PRIMARY KEY (`witel_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `designator`
--
ALTER TABLE `designator`
  MODIFY `designator_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `designator_package`
--
ALTER TABLE `designator_package`
  MODIFY `designator_package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `do`
--
ALTER TABLE `do`
  MODIFY `do_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `do_item`
--
ALTER TABLE `do_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `job`
--
ALTER TABLE `job`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `moduleCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `permissionCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `po`
--
ALTER TABLE `po`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `po_item`
--
ALTER TABLE `po_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `project_cat`
--
ALTER TABLE `project_cat`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `project_feeder`
--
ALTER TABLE `project_feeder`
  MODIFY `project_feeder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `project_gpon`
--
ALTER TABLE `project_gpon`
  MODIFY `project_gpon_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_job`
--
ALTER TABLE `project_job`
  MODIFY `project_job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=342;

--
-- AUTO_INCREMENT for table `project_khs`
--
ALTER TABLE `project_khs`
  MODIFY `khs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `project_khs_list`
--
ALTER TABLE `project_khs_list`
  MODIFY `khs_list_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `project_odc`
--
ALTER TABLE `project_odc`
  MODIFY `project_odc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_odp`
--
ALTER TABLE `project_odp`
  MODIFY `project_odp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_penggelaran`
--
ALTER TABLE `project_penggelaran`
  MODIFY `project_penggelaran_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_sitax`
--
ALTER TABLE `project_sitax`
  MODIFY `sitax_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `project_survey`
--
ALTER TABLE `project_survey`
  MODIFY `survey_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `project_user`
--
ALTER TABLE `project_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `region`
--
ALTER TABLE `region`
  MODIFY `region_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `roleCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `rpCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=248;

--
-- AUTO_INCREMENT for table `role_user`
--
ALTER TABLE `role_user`
  MODIFY `ruCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `stock_ho`
--
ALTER TABLE `stock_ho`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `stock_witel`
--
ALTER TABLE `stock_witel`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `user_permission`
--
ALTER TABLE `user_permission`
  MODIFY `upCode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `witel`
--
ALTER TABLE `witel`
  MODIFY `witel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `witel_user`
--
ALTER TABLE `witel_user`
  MODIFY `witel_user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
