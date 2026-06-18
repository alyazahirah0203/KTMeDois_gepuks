-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2026 at 11:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ktm_edois`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` bigint(20) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Success','Failed') NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `timestamp`, `username`, `module`, `action`, `description`, `status`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, '2026-06-10 22:55:47', 'SAZ Global Service', 'Invoice', 'SUBMIT', 'Failed: SQLSTATE[22007]: Invalid datetime format: 1366 Incorrect integer value: \'SAZ001\' for column `ktm_edois`.`invoices`.`vendor_id` at row 1 (SQL: insert into `invoices` (`invoice_no`, `uuid`, `do_id`, `vendor_id`, `invoice_date`, `customer_no`, `line_total`, `service_tax`, `shipping`, `discount`, `penalty`, `total`, `payments`, `credits`, `financial_charges`, `balance_due`, `payment_terms`, `due_date`, `proof_of_delivery`, `status`, `updated_at`, `created_at`) values (KTMB/INV/20260611/0001, ed9f6bd2-7f8c-4104-9b40-66e6aa8583d3, 1, SAZ001, 2026-06-11 00:00:00, ?, 2200, 132, 0, 0, 0, 2332, 0, 0, 0, 2332, 30 DAYS, 2026-07-11 06:55:47, proofs/yeQN7KDtPzSlZDRky3bt9f4wTu6ZAs9GwBUVbixu.png, Submitted, 2026-06-11 06:55:47, 2026-06-11 06:55:47))', 'Failed', '127.0.0.1', '2026-06-10 22:55:47', '2026-06-10 22:55:47'),
(2, '2026-06-10 22:58:11', 'SAZ Global Service', 'Invoice', 'SUBMIT', 'Invoice KTMB/INV/20260611/0001 submitted for DO DO/2025/001. Total: RM 318', 'Success', '127.0.0.1', '2026-06-10 22:58:11', '2026-06-10 22:58:11'),
(3, '2026-06-10 23:04:55', 'SAZ Global Service', 'Invoice', 'SUBMIT', 'Invoice KTMB/INV/20260611/0002 submitted for DO DO/2025/001. Total: RM 2548.24', 'Success', '127.0.0.1', '2026-06-10 23:04:55', '2026-06-10 23:04:55'),
(4, '2026-06-10 23:14:50', 'SAZ Global Service', 'Invoice', 'SUBMIT', 'Invoice KTMB/INV/20260611/0003 submitted for DO DO/2025/001. Total: RM 2442.18', 'Success', '127.0.0.1', '2026-06-10 23:14:50', '2026-06-10 23:14:50'),
(5, '2026-06-14 01:32:58', 'Ahmad Abdullah', 'Invoice', 'STATUS_CHANGE', 'Invoice KTMB/INV/20260611/0001 from Finance Review to Payment Processing', 'Success', '127.0.0.1', '2026-06-14 01:32:58', '2026-06-14 01:32:58'),
(6, '2026-06-14 01:38:16', 'Ahmad Abdullah', 'Invoice', 'STATUS_CHANGE', 'Invoice KTMB/INV/20260611/0001 from Finance Review to Payment Processing', 'Success', '127.0.0.1', '2026-06-14 01:38:16', '2026-06-14 01:38:16');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_orders`
--

CREATE TABLE `delivery_orders` (
  `do_id` bigint(20) UNSIGNED NOT NULL,
  `do_number` varchar(255) NOT NULL,
  `po_number` varchar(255) NOT NULL,
  `vendor_id` varchar(25) DEFAULT NULL,
  `order_date` date NOT NULL,
  `shipping_address` text NOT NULL,
  `invoice_address` text NOT NULL,
  `delivery_date` date NOT NULL,
  `delivery_time` time NOT NULL,
  `remarks` text DEFAULT NULL,
  `receiver_signature` varchar(255) DEFAULT NULL,
  `status` enum('Draft','Submitted','Under Review','Approved','Rejected') NOT NULL DEFAULT 'Draft',
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_orders`
--

INSERT INTO `delivery_orders` (`do_id`, `do_number`, `po_number`, `vendor_id`, `order_date`, `shipping_address`, `invoice_address`, `delivery_date`, `delivery_time`, `remarks`, `receiver_signature`, `status`, `reason`, `created_at`, `updated_at`) VALUES
(1, 'DO/2025/001', 'PO/2025/001', 'SAZ001', '2025-01-01', 'Hotel Seri Malaysia Ipoh, Jalan Sturrock, Ipoh', 'Hotel Seri Malaysia Ipoh, Jalan Sturrock, Ipoh', '2025-01-15', '14:00:00', NULL, NULL, 'Approved', NULL, NULL, NULL),
(8, 'DO/2025/002', 'PO/2025/002', 'ASZ002', '2025-02-01', 'KTMB Headquarters, Jalan Sultan Hishamuddin, 50621 Kuala Lumpur', 'ASZ Handasa Enterprise, Tingkat 1, Jalan Teluk Air Tawar, Taman Air Tawar Indah, 13050 Butterworth, Pulau Pinang', '2025-02-10', '10:00:00', NULL, NULL, 'Approved', NULL, '2026-06-11 09:31:43', '2026-06-11 09:31:43'),
(9, 'DO/2025/003', 'PO/2025/003', 'SAZ001', '2026-06-11', 'Stesen Keretapi Kuala Lumpur, Jalan Sultan Hishamuddin, 50000 Kuala Lumpur', 'SAZ Global Services, No 56, Jalan Klebang Indah 1, Medan Klebang Indah, 30010 Ipoh, Perak', '2026-06-11', '15:00:00', NULL, NULL, 'Approved', NULL, '2026-06-11 09:31:43', '2026-06-11 09:31:43'),
(10, 'DO/2025/004', 'PO/2025/004', 'TECH005', '2025-03-01', 'Depoh Kereta Api Sentul, Jalan Perusahaan, 51100 Kuala Lumpur', 'Teknologi Elektrik Malaysia, No 12, Jalan Teknologi, 63000 Cyberjaya', '2025-03-20', '09:30:00', NULL, NULL, 'Approved', NULL, '2026-06-11 09:31:43', '2026-06-11 09:31:43'),
(11, 'DO/2025/005', 'PO/2025/005', 'LOG007', '2025-04-01', 'Gudang KTMB, Pelabuhan Klang, 42000 Pelabuhan Klang, Selangor', 'Logistik Cepat Sdn Bhd, No 88, Jalan Perdagangan, 42000 Pelabuhan Klang', '2025-04-05', '11:00:00', NULL, NULL, 'Draft', NULL, '2026-06-11 09:31:43', '2026-06-11 09:31:43'),
(12, 'DO/2025/006', 'PO/2025/006', 'CLEAN010', '2025-05-01', 'Stesen KL Sentral, 50470 Kuala Lumpur', 'Clean Services Malaysia, No 45, Jalan Bersih, 50400 Kuala Lumpur', '2025-05-10', '08:00:00', NULL, NULL, 'Under Review', NULL, '2026-06-11 09:31:43', '2026-06-11 09:31:43');

-- --------------------------------------------------------

--
-- Table structure for table `do_items`
--

CREATE TABLE `do_items` (
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `do_id` bigint(20) UNSIGNED NOT NULL,
  `item_no` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `do_items`
--

INSERT INTO `do_items` (`item_id`, `do_id`, `item_no`, `description`, `quantity`, `created_at`, `updated_at`) VALUES
(47, 1, '1001', 'Jaring Keselamatan (2m x 5m)', 5, NULL, NULL),
(48, 1, '1002', 'Besi C-channel 4 inch (6m)', 20, NULL, NULL),
(49, 1, '1003', 'Skylift Rental (30m height)', 3, NULL, NULL),
(50, 8, '2001', 'Kerusi Plastik (Model K-01)', 50, NULL, NULL),
(51, 8, '2002', 'Meja Lipat (Model M-02)', 25, NULL, NULL),
(52, 9, '3001', 'Kabel Fiber Optik (100m roll)', 10, NULL, NULL),
(53, 9, '3002', 'Router Gigabit (Model R-100)', 15, NULL, NULL),
(54, 9, '3003', 'Switch 24 Port (Model S-24)', 5, NULL, NULL),
(55, 10, '4001', 'Transformer 11kV (1000kVA)', 2, NULL, NULL),
(56, 10, '4002', 'Kabel XLPE (240mm, 100m)', 8, NULL, NULL),
(57, 11, '5001', 'Penghantaran Kargo (Pallet)', 10, NULL, NULL),
(58, 12, '6001', 'Perkhidmatan Pembersihan (Area)', 3, NULL, NULL),
(59, 12, '6002', 'Bahan Kimia Pembersih (20L)', 15, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(255) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `do_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` varchar(25) NOT NULL,
  `invoice_date` date NOT NULL,
  `customer_no` varchar(255) DEFAULT NULL,
  `line_total` decimal(15,2) NOT NULL,
  `service_tax` decimal(15,2) NOT NULL,
  `shipping` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `penalty` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL,
  `payments` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credits` decimal(15,2) NOT NULL DEFAULT 0.00,
  `financial_charges` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance_due` decimal(15,2) NOT NULL,
  `payment_terms` varchar(255) NOT NULL DEFAULT '30 DAYS',
  `due_date` date NOT NULL,
  `proof_of_delivery` varchar(255) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `status` enum('Submitted','Finance Review','Payment Processing','Paid') NOT NULL DEFAULT 'Submitted',
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `invoice_no`, `uuid`, `do_id`, `vendor_id`, `invoice_date`, `customer_no`, `line_total`, `service_tax`, `shipping`, `discount`, `penalty`, `total`, `payments`, `credits`, `financial_charges`, `balance_due`, `payment_terms`, `due_date`, `proof_of_delivery`, `pdf_path`, `status`, `reason`, `created_at`, `updated_at`) VALUES
(1, 'KTMB/INV/20260611/0001', '87254a74-3a66-49b3-8115-c0b1a4497e9e', 1, 'SAZ001', '2026-06-11', NULL, 300.00, 18.00, 0.00, 0.00, 0.00, 318.00, 0.00, 0.00, 0.00, 318.00, '30 DAYS', '2026-07-11', 'proofs/0ZVw6R716gqWSTRSKD60D9BUZcanq1MkqmtOk14T.png', NULL, 'Payment Processing', NULL, '2026-06-10 22:58:09', '2026-06-14 01:38:16'),
(2, 'KTMB/INV/20260611/0002', '3963626f-a92a-4e6d-b594-9a0af86875f6', 1, 'SAZ001', '2026-06-11', NULL, 2404.00, 144.24, 0.00, 0.00, 0.00, 2548.24, 0.00, 0.00, 0.00, 2548.24, '30 DAYS', '2026-07-11', 'proofs/GUHVie5j6740hjTZsZ0xgQdLRClj16k7PG4G0all.png', NULL, 'Payment Processing', NULL, '2026-06-10 23:04:55', '2026-06-10 23:04:55'),
(3, 'KTMB/INV/20260611/0003', 'bb72e5c6-2108-4a9a-a1f4-73975039db03', 1, 'SAZ001', '2026-06-11', NULL, 2403.94, 138.24, 0.00, 100.00, 0.00, 2442.18, 2442.18, 100.00, 0.00, 0.00, '30 DAYS', '2026-07-11', 'proofs/u9ejh7QIBEa0tZohuL6x4Db0oZsVKQgJaICHMZFp.png', 'invoices/invoice_KTMB_INV_20260611_0003.pdf', 'Paid', NULL, '2026-06-10 23:14:50', '2026-06-10 23:14:50');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `product_code` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `uom` varchar(255) NOT NULL DEFAULT 'EA',
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`item_id`, `invoice_id`, `product_code`, `description`, `uom`, `quantity`, `unit_price`, `amount`, `created_at`, `updated_at`) VALUES
(1, 1, 'KTMB-001', 'Jaring Keselamatan', 'EA', 2, 150.00, 300.00, '2026-06-10 22:58:09', '2026-06-10 22:58:09'),
(2, 2, 'KTMB-001', 'Jaring Keselamatan', 'EA', 2, 150.00, 300.00, '2026-06-10 23:04:55', '2026-06-10 23:04:55'),
(3, 2, 'KTMB-002', 'Besi C-channel 4 inch', 'EA', 8, 75.50, 604.00, '2026-06-10 23:04:55', '2026-06-10 23:04:55'),
(4, 2, 'KTMB-003', 'Skylift Rental', 'DAY', 3, 500.00, 1500.00, '2026-06-10 23:04:55', '2026-06-10 23:04:55'),
(5, 3, 'KTMB-001', 'Jaring Keselamatan', 'EA', 2, 150.00, 300.00, '2026-06-10 23:14:50', '2026-06-10 23:14:50'),
(6, 3, 'KTMB-002', 'Besi C-channel 4 inch', 'EA', 8, 75.50, 604.00, '2026-06-10 23:14:50', '2026-06-10 23:14:50'),
(7, 3, 'KTMB-003', 'Skylift Rental', 'DAY', 3, 499.98, 1499.94, '2026-06-10 23:14:50', '2026-06-10 23:14:50');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_06_11_054601_create_vendors_table', 1),
(6, '2026_06_11_054608_create_delivery_orders_table', 1),
(7, '2026_06_11_054614_create_do_items_table', 1),
(8, '2026_06_11_054620_create_invoices_table', 1),
(9, '2026_06_11_054627_create_invoice_items_table', 1),
(10, '2026_06_11_054634_create_payments_table', 1),
(11, '2026_06_11_054640_create_audit_logs_table', 1),
(12, '2026_06_11_070806_add_pdf_path_to_invoices', 2),
(13, '2026_06_14_091847_create_notifications_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'info',
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `title`, `message`, `type`, `link`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 'Invoice Approved', 'Your invoice has been approved! Payment is being processed.', 'info', 'http://127.0.0.1:8000/invoices/1', 0, '2026-06-14 01:32:58', '2026-06-14 01:32:58'),
(2, 1, 'Invoice Approved', 'Your invoice has been approved! Payment is being processed.', 'info', 'http://127.0.0.1:8000/invoices/1', 0, '2026-06-14 01:38:16', '2026-06-14 01:38:16'),
(3, 3, 'Invoice Approved', 'Your invoice has been approved! Payment is being processed.', 'info', '/dashboard', 1, '2026-06-14 01:44:08', '2026-06-14 18:10:21'),
(4, 3, 'Test Notification', 'This is a test notification to verify the bell icon is working.', 'success', '/dashboard', 1, '2026-06-14 18:11:49', '2026-06-14 18:30:52');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_status` enum('Pending','Completed','Failed','Refunded') NOT NULL DEFAULT 'Pending',
  `payment_method` enum('Bank Transfer','Credit Card','Cheque','Cash') NOT NULL,
  `transaction_ref` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('vendor','officer','admin') NOT NULL DEFAULT 'vendor',
  `vendor_id` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `vendor_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'SAZ Global Service', 'admin@sazglobal.com', NULL, '$2y$10$qB2kwa1VEtRgE8W/VEXExeYaWXFOFz0H6T6Pfmljzg6G2/pTVmdUu', 'vendor', 'SAZ001', NULL, '2026-06-10 22:36:25', '2026-06-10 22:36:25'),
(2, 'System Administrator', 'admin@ktmb.com.my', NULL, '$2y$10$IKXvero.Tlj4U5PcjpbBteHZbImsMr.DTpE1pPK53IycOcpiNW0qC', 'admin', NULL, NULL, '2026-06-11 01:02:33', '2026-06-11 01:02:33'),
(3, 'Ahmad Abdullah', 'ahmad@sazglobal.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', 'SAZ001', NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36'),
(4, 'Siti Hassan', 'siti@aszhandasa.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', 'ASZ002', NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36'),
(5, 'Faiz Rosli', 'faiz@majujaya.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', 'KTMB003', NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36'),
(6, 'Wong Chee Ming', 'wong@teknologielektrik.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', 'TECH005', NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36'),
(7, 'Rajesh Kumar', 'rajesh@sysasia.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', 'SYSTEM006', NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36'),
(8, 'Norazlin Ibrahim', 'nazlin@logistikcepat.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', 'LOG007', NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36'),
(9, 'Goh Soo Ling', 'gohs@safetyfirst.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', 'SAFETY008', NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36'),
(10, 'Khairul Azman', 'khairul@infraworks.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', 'INFRA009', NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36'),
(11, 'Wan Siti Sarah', 'sarah@cleanservices.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendor', 'CLEAN010', NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36'),
(12, 'Puan Noor Zeemah Shamseh', 'noor.zeemah@ktmb.gov.my', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'officer', NULL, NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36'),
(13, 'Encik Azman Bin Abdullah', 'azman.abdullah@ktmb.gov.my', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'officer', NULL, NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36'),
(14, 'Puan Siti Noraisyah', 'siti.noraisyah@ktmb.gov.my', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'officer', NULL, NULL, '2026-06-11 09:34:36', '2026-06-11 09:34:36');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplierid` varchar(25) NOT NULL,
  `supplier_comp_reg_no` varchar(200) DEFAULT NULL,
  `supplier_comp_name` varchar(200) DEFAULT NULL,
  `supplier_ctc_no` varchar(200) DEFAULT NULL,
  `supplier_ctc_person` varchar(100) DEFAULT NULL,
  `supplier_email_add` varchar(200) DEFAULT NULL,
  `supplier_expired_date` date DEFAULT NULL,
  `supplier_ctc_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `supplierid`, `supplier_comp_reg_no`, `supplier_comp_name`, `supplier_ctc_no`, `supplier_ctc_person`, `supplier_email_add`, `supplier_expired_date`, `supplier_ctc_status`, `created_at`, `updated_at`) VALUES
(1, 'SAZ001', '199101015631', 'SAZ GLOBAL SERVICES', '0123456789', 'Ahmad Bin Abdullah', 'admin@sazglobal.com', '2026-12-31', 'active', NULL, NULL),
(2, 'ASZ002', 'IG23603573080', 'ASZ HANDASA ENTERPRISE', '0198765432', 'Siti Binti Hassan', 'info@aszhandasa.com', '2026-12-31', 'active', '2026-06-11 09:30:43', '2026-06-11 09:30:43'),
(3, 'KTMB003', '199801023456', 'MAJU JAYA SDN BHD', '0176543210', 'Mohamad Faiz Bin Rosli', 'admin@majujaya.com', '2025-12-31', 'active', '2026-06-11 09:30:43', '2026-06-11 09:30:43'),
(4, 'BINA004', '200105067890', 'PEMBINAAN MEGAH SDN BHD', '0134567890', 'Nurul Aisyah Binti Zainal', 'pembinaanmegah@gmail.com', '2026-06-30', 'active', '2026-06-11 09:30:43', '2026-06-11 09:30:43'),
(5, 'TECH005', '201212345678', 'TEKNOLOGI ELEKTRIK MALAYSIA', '0187654321', 'Wong Chee Ming', 'wong@teknologielektrik.com', '2025-10-15', 'active', '2026-06-11 09:30:43', '2026-06-11 09:30:43'),
(6, 'SYSTEM006', '200505043210', 'SYSTEM INTEGRATOR ASIA', '0192345678', 'Rajesh Kumar A/L Muthusamy', 'rajesh@sysasia.com', '2026-12-31', 'inactive', '2026-06-11 09:30:43', '2026-06-11 09:30:43'),
(7, 'LOG007', '201808876543', 'LOGISTIK CEPAT SDN BHD', '0111234567', 'Norazlin Binti Ibrahim', 'nazlin@logistikcepat.com', '2025-09-01', 'active', '2026-06-11 09:30:43', '2026-06-11 09:30:43'),
(8, 'SAFETY008', '202001987654', 'SAFETY FIRST MALAYSIA', '0145678901', 'Goh Soo Ling', 'gohs@safetyfirst.com', '2026-03-31', 'active', '2026-06-11 09:30:43', '2026-06-11 09:30:43'),
(9, 'INFRA009', '200911112233', 'INFRASTRUCTURE WORKS SDN BHD', '0167890123', 'Khairul Anuar Bin Azman', 'khairul@infraworks.com', '2025-11-30', 'inactive', '2026-06-11 09:30:43', '2026-06-11 09:30:43'),
(10, 'CLEAN010', '202205055555', 'CLEAN SERVICES MALAYSIA', '0178901234', 'Wan Siti Sarah Binti Wan Majid', 'sarah@cleanservices.com', '2026-06-15', 'active', '2026-06-11 09:30:43', '2026-06-11 09:30:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `delivery_orders`
--
ALTER TABLE `delivery_orders`
  ADD PRIMARY KEY (`do_id`),
  ADD UNIQUE KEY `delivery_orders_do_number_unique` (`do_number`),
  ADD KEY `delivery_orders_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `do_items`
--
ALTER TABLE `do_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `do_items_do_id_foreign` (`do_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `invoices_invoice_no_unique` (`invoice_no`),
  ADD UNIQUE KEY `invoices_uuid_unique` (`uuid`),
  ADD KEY `invoices_do_id_foreign` (`do_id`),
  ADD KEY `invoices_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `invoice_items_invoice_id_foreign` (`invoice_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payments_invoice_id_foreign` (`invoice_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendors_supplierid_unique` (`supplierid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `delivery_orders`
--
ALTER TABLE `delivery_orders`
  MODIFY `do_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `do_items`
--
ALTER TABLE `do_items`
  MODIFY `item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delivery_orders`
--
ALTER TABLE `delivery_orders`
  ADD CONSTRAINT `delivery_orders_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`supplierid`) ON DELETE CASCADE;

--
-- Constraints for table `do_items`
--
ALTER TABLE `do_items`
  ADD CONSTRAINT `do_items_do_id_foreign` FOREIGN KEY (`do_id`) REFERENCES `delivery_orders` (`do_id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_do_id_foreign` FOREIGN KEY (`do_id`) REFERENCES `delivery_orders` (`do_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`supplierid`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`invoice_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`invoice_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
