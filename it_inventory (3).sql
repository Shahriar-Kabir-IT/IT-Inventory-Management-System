-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2025 at 02:03 PM
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
-- Database: `it_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `asset_id` varchar(20) NOT NULL,
  `asset_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `serial_number` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  `location` varchar(100) NOT NULL,
  `assigned_to` varchar(100) DEFAULT NULL,
  `department` varchar(50) NOT NULL,
  `purchase_date` date NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `warranty_expiry` date NOT NULL,
  `last_maintenance` date DEFAULT NULL,
  `priority` varchar(10) NOT NULL DEFAULT 'Medium',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `asset_id`, `asset_name`, `category`, `brand`, `model`, `serial_number`, `status`, `location`, `assigned_to`, `department`, `purchase_date`, `purchase_price`, `warranty_expiry`, `last_maintenance`, `priority`, `notes`) VALUES
(4, 'IT-20250703090519', 'Dell PC', 'Desktop', 'Dell', 'w454', 'OPX56456416', 'ACTIVE', 'ABM', 'Kazi Mokammel', 'ICT', '2024-07-05', 50000.00, '2025-07-25', '2025-07-05', 'High', '');

-- --------------------------------------------------------

--
-- Table structure for table `assets_abm`
--

CREATE TABLE `assets_abm` (
  `asset_id` varchar(50) NOT NULL,
  `asset_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `serial_number` varchar(50) NOT NULL,
  `status` varchar(20) DEFAULT 'ACTIVE',
  `location` varchar(50) NOT NULL,
  `assigned_to` varchar(50) DEFAULT 'Unassigned',
  `department` varchar(50) NOT NULL,
  `purchase_date` date NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `warranty_expiry` date NOT NULL,
  `last_maintenance` date DEFAULT NULL,
  `priority` varchar(20) DEFAULT 'Medium',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assets_abm`
--

INSERT INTO `assets_abm` (`asset_id`, `asset_name`, `category`, `brand`, `model`, `serial_number`, `status`, `location`, `assigned_to`, `department`, `purchase_date`, `purchase_price`, `warranty_expiry`, `last_maintenance`, `priority`, `notes`) VALUES
('IT-20250706160638', 'Dell Ideapad', 'Laptop', 'Lenevo', 'IdeaPad', 'OPX56456416', 'ACTIVE', 'ABM', 'Tajul', 'ICT', '2025-07-06', 50000.00, '2025-07-06', '2025-07-06', 'Medium', ''),
('IT-20250706160730', 'Hp Ideapad', 'Network', 'Lenevo', 'IdeaPad', 'OPX56456416', 'ACTIVE', 'ABM', 'Tajul', 'ICT', '2025-07-06', 50000.00, '2025-07-06', '2025-07-06', 'Medium', '!@#$');

-- --------------------------------------------------------

--
-- Table structure for table `assets_agl`
--

CREATE TABLE `assets_agl` (
  `asset_id` varchar(50) NOT NULL,
  `asset_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `serial_number` varchar(50) NOT NULL,
  `status` varchar(20) DEFAULT 'ACTIVE',
  `location` varchar(50) NOT NULL,
  `assigned_to` varchar(50) DEFAULT 'Unassigned',
  `department` varchar(50) NOT NULL,
  `purchase_date` date NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `warranty_expiry` date NOT NULL,
  `last_maintenance` date DEFAULT NULL,
  `priority` varchar(20) DEFAULT 'Medium',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assets_agl`
--

INSERT INTO `assets_agl` (`asset_id`, `asset_name`, `category`, `brand`, `model`, `serial_number`, `status`, `location`, `assigned_to`, `department`, `purchase_date`, `purchase_price`, `warranty_expiry`, `last_maintenance`, `priority`, `notes`) VALUES
('IT-20250706170731', 'Hp Ideapad', 'Desktop', 'Lenevo', 'IdeaPad', 'OPX56456416', 'ACTIVE', 'ABM', 'Tajul', 'ICT', '2025-07-06', 50000.00, '2025-07-06', '2025-07-06', 'Medium', '1234'),
('IT-20250706171726', 'Ipad', 'Laptop', 'Apple', 'Ipad Pro', 'OPX56456416', 'ACTIVE', 'AGL', 'Musleh', 'ICT', '2025-07-06', 50000.00, '2025-07-06', '2025-07-06', 'High', '');

-- --------------------------------------------------------

--
-- Table structure for table `deleted_assets_abm`
--

CREATE TABLE `deleted_assets_abm` (
  `id` int(11) NOT NULL,
  `original_asset_id` varchar(50) NOT NULL,
  `asset_name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `assigned_to` varchar(100) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `last_maintenance` date DEFAULT NULL,
  `priority` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `removal_reason` varchar(100) NOT NULL,
  `removal_notes` text DEFAULT NULL,
  `removed_by` varchar(100) DEFAULT NULL,
  `removal_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `deleted_assets_abm`
--

INSERT INTO `deleted_assets_abm` (`id`, `original_asset_id`, `asset_name`, `category`, `brand`, `model`, `serial_number`, `status`, `location`, `assigned_to`, `department`, `purchase_date`, `purchase_price`, `warranty_expiry`, `last_maintenance`, `priority`, `notes`, `removal_reason`, `removal_notes`, `removed_by`, `removal_date`) VALUES
(1, 'IT-20250703120057', '1', 'Laptop', '1', '11', '11', 'ACTIVE', 'Head Office', 'Kazi Mokammel', 'HR', '2025-07-03', 1.00, '2025-07-03', '2025-07-03', 'Medium', '', 'Transfer', 'AJL', 'System', '2025-07-03 10:01:08'),
(2, 'IT-20250705125401', 'Dell XPS 13 Plus', 'Laptop', 'Dell', ' XPS 13 Plus', 'OPX56456416', 'ACTIVE', 'Head Office', 'Kazi Mokammel', 'ICT', '2025-07-05', 50000.00, '2025-07-31', '2025-07-06', 'High', '', 'Disposed', '1234', 'abm', '2025-07-06 04:36:40'),
(3, 'IT-20250703090519', 'Dell PC', 'Desktop', 'Dell', 'w454', 'OPX56456416', 'ACTIVE', 'ABM', 'Kazi Mokammel', 'ICT', '2024-07-05', 50000.00, '2025-07-25', '2025-07-06', 'High', '', 'Transfer', 'Trasfer To AGL', 'abm', '2025-07-06 07:05:23'),
(4, 'IT-20250706153835', 'Lenevo Ideapad', 'Laptop', 'Lenevo', 'IdeaPad', 'OPX56456416', 'ACTIVE', 'ABM', 'Tajul', 'ICT', '2025-07-06', 50000.00, '2025-07-06', '2025-07-06', 'Medium', 'Good', 'Lost/Stolen', 'BAD', 'abm', '2025-07-06 11:06:48');

-- --------------------------------------------------------

--
-- Table structure for table `deleted_assets_agl`
--

CREATE TABLE `deleted_assets_agl` (
  `id` int(11) NOT NULL,
  `original_asset_id` varchar(50) NOT NULL,
  `asset_name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `assigned_to` varchar(100) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `last_maintenance` date DEFAULT NULL,
  `priority` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `removal_reason` varchar(100) NOT NULL,
  `removal_notes` text DEFAULT NULL,
  `removed_by` varchar(100) DEFAULT NULL,
  `removal_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_approvals`
--

CREATE TABLE `pending_approvals` (
  `id` int(11) NOT NULL,
  `asset_id` varchar(50) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `requested_by` varchar(100) NOT NULL,
  `requesting_factory` varchar(20) NOT NULL,
  `factory` enum('agl','ajl','abm','pwpl','head office') NOT NULL,
  `request_date` datetime DEFAULT current_timestamp(),
  `current_status` varchar(50) DEFAULT NULL,
  `action_details` longtext DEFAULT NULL,
  `status` varchar(20) DEFAULT 'PENDING',
  `approval_date` datetime DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pending_approvals`
--

INSERT INTO `pending_approvals` (`id`, `asset_id`, `action_type`, `requested_by`, `requesting_factory`, `factory`, `request_date`, `current_status`, `action_details`, `status`, `approval_date`, `approved_by`) VALUES
(24, 'IT-20250706153835', 'ADD', 'abm', '', 'abm', '2025-07-06 15:38:35', 'PENDING', '{\"asset_name\":\"Lenevo Ideapad\",\"category\":\"Laptop\",\"brand\":\"Lenevo\",\"model\":\"IdeaPad\",\"serial_number\":\"OPX56456416\",\"status\":\"PENDING\",\"location\":\"ABM\",\"assigned_to\":\"Tajul\",\"department\":\"ICT\",\"purchase_date\":\"2025-07-06\",\"purchase_price\":\"50000\",\"warranty_expiry\":\"2025-07-06\",\"last_maintenance\":\"2025-07-06\",\"priority\":\"Medium\",\"notes\":\"Good\",\"action_type\":\"ADD\",\"requested_by\":\"abm\",\"factory\":\"abm\"}', 'APPROVED', '2025-07-06 15:56:37', 'Md. Shahriar Kabir'),
(25, 'IT-20250706160638', 'ADD', 'abm', '', 'abm', '2025-07-06 16:06:38', 'PENDING', '{\"asset_name\":\"Dell Ideapad\",\"category\":\"Laptop\",\"brand\":\"Lenevo\",\"model\":\"IdeaPad\",\"serial_number\":\"OPX56456416\",\"status\":\"PENDING\",\"location\":\"ABM\",\"assigned_to\":\"Tajul\",\"department\":\"ICT\",\"purchase_date\":\"2025-07-06\",\"purchase_price\":\"50000\",\"warranty_expiry\":\"2025-07-06\",\"last_maintenance\":\"2025-07-06\",\"priority\":\"Medium\",\"notes\":\"\",\"action_type\":\"ADD\",\"requested_by\":\"abm\",\"factory\":\"abm\"}', 'APPROVED', '2025-07-06 16:06:54', 'Md. Shahriar Kabir'),
(26, 'IT-20250706160730', 'ADD', 'abm', '', 'abm', '2025-07-06 16:07:30', 'PENDING', '{\"asset_name\":\"Hp Ideapad\",\"category\":\"Network\",\"brand\":\"Lenevo\",\"model\":\"IdeaPad\",\"serial_number\":\"OPX56456416\",\"status\":\"PENDING\",\"location\":\"ABM\",\"assigned_to\":\"Tajul\",\"department\":\"ICT\",\"purchase_date\":\"2025-07-06\",\"purchase_price\":\"50000\",\"warranty_expiry\":\"2025-07-06\",\"last_maintenance\":\"2025-07-06\",\"priority\":\"Medium\",\"notes\":\"!@#$\",\"action_type\":\"ADD\",\"requested_by\":\"abm\",\"factory\":\"abm\"}', 'APPROVED', '2025-07-06 16:33:41', 'Md. Shahriar Kabir'),
(27, 'IT-20250706153835', 'DELETE', 'abm', '', 'abm', '2025-07-06 17:06:40', NULL, '{\"remove_reason\":\"Lost\\/Stolen\",\"remove_notes\":\"BAD\",\"action_type\":\"DELETE\",\"requested_by\":\"abm\",\"factory\":\"abm\"}', 'APPROVED', '2025-07-06 17:06:48', 'Md. Shahriar Kabir'),
(28, 'IT-20250706170731', 'ADD', 'AGL', '', 'agl', '2025-07-06 17:07:31', 'PENDING', '{\"asset_name\":\"Hp Ideapad\",\"category\":\"Desktop\",\"brand\":\"Lenevo\",\"model\":\"IdeaPad\",\"serial_number\":\"OPX56456416\",\"status\":\"PENDING\",\"location\":\"ABM\",\"assigned_to\":\"Tajul\",\"department\":\"ICT\",\"purchase_date\":\"2025-07-06\",\"purchase_price\":\"50000\",\"warranty_expiry\":\"2025-07-06\",\"last_maintenance\":\"2025-07-06\",\"priority\":\"Medium\",\"notes\":\"1234\",\"action_type\":\"ADD\",\"requested_by\":\"AGL\",\"factory\":\"abm\"}', 'APPROVED', '2025-07-06 17:07:41', 'Md. Shahriar Kabir'),
(29, 'IT-20250706171726', 'ADD', 'AGL', '', 'agl', '2025-07-06 17:17:26', 'PENDING', '{\"asset_name\":\"Ipad\",\"category\":\"Laptop\",\"brand\":\"Apple\",\"model\":\"Ipad Pro\",\"serial_number\":\"OPX56456416\",\"status\":\"PENDING\",\"location\":\"AGL\",\"assigned_to\":\"Musleh\",\"department\":\"ICT\",\"purchase_date\":\"2025-07-06\",\"purchase_price\":\"50000\",\"warranty_expiry\":\"2025-07-06\",\"last_maintenance\":\"2025-07-06\",\"priority\":\"High\",\"notes\":\"\",\"action_type\":\"ADD\",\"requested_by\":\"AGL\",\"factory\":\"abm\"}', 'APPROVED', '2025-07-06 17:17:55', 'Md. Shahriar Kabir'),
(30, 'IT-20250706171726', 'SERVICE', 'AGL', '', 'agl', '2025-07-06 17:18:34', 'MAINTENANCE', '{\"status\":\"MAINTENANCE\",\"service_type\":\"Repair\",\"service_notes\":\"Change Battery\",\"service_by\":\"Lamia Telecom\",\"last_maintenance\":\"2025-07-06\",\"action_type\":\"SERVICE\",\"requested_by\":\"AGL\",\"factory\":\"abm\"}', 'APPROVED', '2025-07-06 17:19:00', 'Md. Shahriar Kabir'),
(31, 'IT-20250706171726', 'COMPLETE_SERVICE', 'AGL', '', 'agl', '2025-07-06 17:19:11', NULL, '{\"action_type\":\"COMPLETE_SERVICE\",\"requested_by\":\"AGL\",\"factory\":\"abm\",\"completion_notes\":\"DONE\"}', 'APPROVED', '2025-07-06 17:19:44', 'Md. Shahriar Kabir'),
(32, 'IT-20250706171726', 'COMPLETE_SERVICE', 'AGL', '', 'agl', '2025-07-06 17:34:58', NULL, '{\"action_type\":\"COMPLETE_SERVICE\",\"requested_by\":\"AGL\",\"factory\":\"abm\",\"completion_notes\":\"Work Done\"}', 'APPROVED', '2025-07-06 17:35:21', 'Md. Shahriar Kabir'),
(33, 'IT-20250706160638', 'SERVICE', 'abm', '', 'abm', '2025-07-06 17:39:11', 'MAINTENANCE', '{\"status\":\"MAINTENANCE\",\"service_type\":\"Upgrade\",\"service_notes\":\"RAM\",\"service_by\":\"Dream IT\",\"last_maintenance\":\"2025-07-06\",\"action_type\":\"SERVICE\",\"requested_by\":\"abm\",\"factory\":\"abm\"}', 'APPROVED', '2025-07-06 17:39:24', 'Md. Shahriar Kabir'),
(34, 'IT-20250706160638', 'COMPLETE_SERVICE', 'abm', '', 'abm', '2025-07-06 17:39:33', NULL, '{\"action_type\":\"COMPLETE_SERVICE\",\"requested_by\":\"abm\",\"factory\":\"abm\",\"completion_notes\":\"ok\"}', 'APPROVED', '2025-07-06 17:39:45', 'Md. Shahriar Kabir'),
(35, 'IT-20250706160730', 'SERVICE', 'abm', '', 'abm', '2025-07-06 17:40:12', 'MAINTENANCE', '{\"status\":\"MAINTENANCE\",\"service_type\":\"Scheduled Maintenance\",\"service_notes\":\"GPU\",\"service_by\":\"Dream IT\",\"last_maintenance\":\"2025-07-06\",\"action_type\":\"SERVICE\",\"requested_by\":\"abm\",\"factory\":\"abm\"}', 'APPROVED', '2025-07-06 17:40:19', 'Md. Shahriar Kabir'),
(36, 'IT-20250706160730', 'COMPLETE_SERVICE', 'abm', '', 'abm', '2025-07-06 17:40:32', NULL, '{\"action_type\":\"COMPLETE_SERVICE\",\"requested_by\":\"abm\",\"factory\":\"abm\",\"completion_notes\":\"DONE\"}', 'APPROVED', '2025-07-06 17:40:48', 'Md. Shahriar Kabir'),
(37, 'IT-20250706160638', 'SERVICE', 'abm', '', 'abm', '2025-07-06 17:52:06', 'MAINTENANCE', '{\"status\":\"MAINTENANCE\",\"service_type\":\"Inspection\",\"service_notes\":\"1234\",\"service_by\":\"Global\",\"last_maintenance\":\"2025-07-06\",\"action_type\":\"SERVICE\",\"requested_by\":\"abm\",\"factory\":\"abm\"}', 'APPROVED', '2025-07-06 17:52:30', 'Md. Shahriar Kabir'),
(38, 'IT-20250706160638', 'COMPLETE_SERVICE', 'abm', '', 'abm', '2025-07-06 17:52:45', NULL, '{\"action_type\":\"COMPLETE_SERVICE\",\"requested_by\":\"abm\",\"factory\":\"abm\",\"completion_notes\":\"Done\"}', 'APPROVED', '2025-07-06 17:53:00', 'Md. Shahriar Kabir'),
(39, 'IT-20250706171726', 'SERVICE', 'AGL', '', 'agl', '2025-07-06 17:53:30', 'MAINTENANCE', '{\"status\":\"MAINTENANCE\",\"service_type\":\"Upgrade\",\"service_notes\":\"1234\",\"service_by\":\"Global\",\"last_maintenance\":\"2025-07-06\",\"action_type\":\"SERVICE\",\"requested_by\":\"AGL\",\"factory\":\"abm\"}', 'APPROVED', '2025-07-06 17:54:06', 'Md. Shahriar Kabir'),
(40, 'IT-20250706171726', 'COMPLETE_SERVICE', 'AGL', '', 'agl', '2025-07-06 17:54:21', NULL, '{\"action_type\":\"COMPLETE_SERVICE\",\"requested_by\":\"AGL\",\"factory\":\"abm\",\"completion_notes\":\"\"}', 'APPROVED', '2025-07-06 17:54:45', 'Md. Shahriar Kabir');

-- --------------------------------------------------------

--
-- Table structure for table `service_history`
--

CREATE TABLE `service_history` (
  `id` int(11) NOT NULL,
  `asset_id` varchar(20) NOT NULL,
  `service_date` date NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `service_notes` text DEFAULT NULL,
  `service_by` varchar(100) DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_history`
--

INSERT INTO `service_history` (`id`, `asset_id`, `service_date`, `service_type`, `service_notes`, `service_by`, `completion_date`, `status`) VALUES
(1, 'IT-20250701133055', '2025-07-01', 'Repair', 'Ram Problem', 'Global', '2025-07-01', 'COMPLETED'),
(2, 'IT-20250701133055', '2025-07-01', 'Repair', 'DRAM', 'ST', '2025-07-01', 'COMPLETED'),
(3, 'IT-20250701135422', '2025-07-01', 'Repair', 'Display Issue', 'Dream IT', '2025-07-01', 'COMPLETED'),
(4, 'IT-20250703090519', '2025-07-05', 'Inspection', '1', 'Global', '2025-07-05', 'COMPLETED');

-- --------------------------------------------------------

--
-- Table structure for table `service_history_abm`
--

CREATE TABLE `service_history_abm` (
  `id` int(11) NOT NULL,
  `asset_id` varchar(20) NOT NULL,
  `service_date` date NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `service_notes` text DEFAULT NULL,
  `service_by` varchar(100) DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_history_abm`
--

INSERT INTO `service_history_abm` (`id`, `asset_id`, `service_date`, `service_type`, `service_notes`, `service_by`, `completion_date`, `status`) VALUES
(8, 'IT-20250703090519', '2025-07-06', 'Upgrade', 'GPU', 'Dream IT', '2025-07-06', 'COMPLETED'),
(9, 'IT-20250706160638', '2025-07-06', 'Upgrade', 'RAM', 'Dream IT', '2025-07-06', 'COMPLETED'),
(10, 'IT-20250706160730', '2025-07-06', 'Scheduled Maintenance', 'GPU', 'Dream IT', '2025-07-06', 'COMPLETED'),
(11, 'IT-20250706160638', '2025-07-06', 'Inspection', '1234', 'Global', '2025-07-06', 'COMPLETED');

-- --------------------------------------------------------

--
-- Table structure for table `service_history_agl`
--

CREATE TABLE `service_history_agl` (
  `id` int(11) NOT NULL,
  `asset_id` varchar(20) NOT NULL,
  `service_date` date NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `service_notes` text DEFAULT NULL,
  `service_by` varchar(100) DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_history_agl`
--

INSERT INTO `service_history_agl` (`id`, `asset_id`, `service_date`, `service_type`, `service_notes`, `service_by`, `completion_date`, `status`) VALUES
(1, 'IT-20250706171726', '2025-07-06', 'Repair', 'Change Battery', 'Lamia Telecom', '2025-07-06', 'COMPLETED'),
(2, 'IT-20250706171726', '2025-07-06', 'Upgrade', '1234', 'Global', '2025-07-06', 'COMPLETED');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `user_type` enum('admin','user') NOT NULL DEFAULT 'user',
  `factory` enum('agl','ajl','abm','pwpl','head office') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `employee_id`, `user_type`, `factory`, `created_at`, `updated_at`) VALUES
(5, 'abm', 'abm', '$2y$10$kjQ8A/nuZL8/XyI5aSxk4usnSe0WN1xTipwpMHh5DTK8hOSfhB5Lu', '1234', 'user', 'abm', '2025-07-03 06:31:08', '2025-07-03 06:31:08'),
(10, 'Md. Shahriar Kabir', 'arko', '$2y$10$qbmP92qAjOHNYaf1d/lRRe45eOGSCyR9DxmVCeXM3/4C72iVpHSmW', '001', 'admin', 'head office', '2025-07-06 04:17:10', '2025-07-06 04:17:10'),
(11, 'AGL', 'agl', '$2y$10$T6fU87Opb56Oc7gmrVHUle4G38kTIRZ..HSy45KyZm2U1ez6J2DTO', '002', 'user', 'agl', '2025-07-06 04:57:27', '2025-07-06 04:57:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_id` (`asset_id`);

--
-- Indexes for table `assets_abm`
--
ALTER TABLE `assets_abm`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indexes for table `assets_agl`
--
ALTER TABLE `assets_agl`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indexes for table `deleted_assets_abm`
--
ALTER TABLE `deleted_assets_abm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `original_asset_id` (`original_asset_id`);

--
-- Indexes for table `deleted_assets_agl`
--
ALTER TABLE `deleted_assets_agl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `original_asset_id` (`original_asset_id`);

--
-- Indexes for table `pending_approvals`
--
ALTER TABLE `pending_approvals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_history`
--
ALTER TABLE `service_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `service_history_abm`
--
ALTER TABLE `service_history_abm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `service_history_agl`
--
ALTER TABLE `service_history_agl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `deleted_assets_abm`
--
ALTER TABLE `deleted_assets_abm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `deleted_assets_agl`
--
ALTER TABLE `deleted_assets_agl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pending_approvals`
--
ALTER TABLE `pending_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `service_history`
--
ALTER TABLE `service_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `service_history_abm`
--
ALTER TABLE `service_history_abm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `service_history_agl`
--
ALTER TABLE `service_history_agl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
