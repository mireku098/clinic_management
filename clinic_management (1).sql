-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2026 at 07:51 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clinic_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `table_name` varchar(255) DEFAULT NULL,
  `record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_number` varchar(255) NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bill_type` enum('service','package','consultation') NOT NULL DEFAULT 'service',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','partial','paid','overdue','cancelled') NOT NULL DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `bill_number`, `patient_id`, `visit_id`, `bill_type`, `total_amount`, `amount_paid`, `balance`, `status`, `due_date`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'BILL-2026-WATDUI', 1, 11, 'package', '850.00', '850.00', '0.00', 'paid', NULL, 'Auto-generated bill for visit #11', 3, '2026-02-11 16:52:17', '2026-02-11 18:16:30');

-- --------------------------------------------------------

--
-- Table structure for table `bill_items`
--

CREATE TABLE `bill_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `item_type` enum('service','package') NOT NULL DEFAULT 'service',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bill_items`
--

INSERT INTO `bill_items` (`id`, `bill_id`, `service_id`, `package_id`, `description`, `quantity`, `unit_price`, `total_price`, `item_type`, `notes`, `created_at`, `updated_at`) VALUES
(3, 2, NULL, 6, 'Female Health & Hormonal Balance', 1, '850.00', '850.00', 'package', 'Package billing', '2026-02-11 16:52:17', '2026-02-11 16:52:17'),
(4, 2, 18, NULL, 'Naturopath Consultation', 1, '50.00', '50.00', 'service', 'Individual service from selection', '2026-02-11 16:52:17', '2026-02-11 16:52:17'),
(5, 2, 18, NULL, 'Naturopath Consultation', 1, '50.00', '50.00', 'service', 'Individual service from relationship', '2026-02-11 16:52:17', '2026-02-11 16:52:17');

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
(1, '2014_10_11_000000_create_roles_table', 1),
(2, '2014_10_12_000000_create_users_table', 1),
(3, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2026_01_29_000000_create_patients_table', 1),
(7, '2026_02_04_143856_add_height_to_patients_table', 2),
(8, '2026_02_04_161809_remove_height_from_patient_visits_table', 3),
(9, '2026_02_05_133000_add_missing_columns_to_package_services_table', 4),
(11, '2026_01_30_170000_update_patient_visits_schema', 6),
(12, '2026_02_02_161216_create_services_table', 6),
(13, '2026_02_02_165402_create_service_results_table', 6),
(14, '2026_02_02_173655_add_timestamps_to_service_results_table', 6),
(15, '2026_02_02_174019_add_missing_columns_to_service_results_table', 6),
(16, '2026_02_02_174519_create_patient_visits_table', 6),
(17, '2026_02_02_175127_remove_patient_service_id_from_service_results', 6),
(18, '2026_02_02_181202_add_package_service_fields_to_patient_visits_table', 6),
(19, '2026_02_03_162107_add_deleted_at_to_patient_visits_table', 6),
(20, '2026_02_05_175301_create_bills_table', 6),
(21, '2026_02_05_180226_add_missing_columns_to_bills_table', 6),
(22, '2026_02_06_090000_create_proper_bills_table', 7),
(23, '2026_02_06_090001_create_bill_items_table', 7),
(24, '2026_02_06_090002_modify_bills_table_structure', 8),
(25, '2026_02_06_090003_fix_bill_items_structure', 9),
(26, '2026_02_06_171700_add_package_fields_to_service_results_table', 10),
(28, '2026_02_10_101629_recreate_missing_tables', 11),
(29, '2026_02_10_101831_create_package_services_table_if_not_exists', 12),
(30, '2026_02_09_142825_make_service_id_nullable_in_service_results', 13),
(31, '2026_02_10_111156_add_missing_columns_to_patient_visits', 14),
(32, '2026_02_10_112012_add_deleted_at_to_patients_table', 15),
(33, '2026_02_10_115209_ensure_administrator_role_exists', 16),
(34, '2026_02_10_120600_add_missing_patient_columns', 17),
(35, '2026_02_10_121009_remove_unique_constraint_from_phone', 18),
(36, '2026_02_10_122407_add_remaining_missing_patient_columns', 19),
(37, '2026_02_10_124555_add_frequency_columns_to_package_services', 20),
(38, '2026_02_10_130625_fix_visit_type_column_length', 21),
(41, '2026_02_10_134711_create_proper_payments_table', 22),
(42, '2026_02_10_135750_add_payment_columns_to_patient_visits_table', 23),
(44, '2026_02_11_143000_fix_attended_by_column_in_patient_visits', 24),
(45, '2026_02_11_160000_update_visit_status_values', 25),
(46, '2026_02_11_160000_recreate_status_column_in_patient_visits', 26),
(47, '2026_02_11_160000_fix_patient_packages_status_enum', 27),
(48, '2026_02_10_115031_ensure_users_and_roles_tables_exist', 28),
(49, '2026_02_10_131913_add_bill_number_to_bills_table', 28),
(50, '2026_02_10_134422_add_payment_balance_columns_to_payments_table', 28),
(51, '2026_02_11_161000_add_package_to_service_results_result_type', 28);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_code` varchar(255) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `duration_weeks` int(11) DEFAULT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `result_type` enum('text','numeric','file') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `package_code`, `package_name`, `category`, `description`, `duration_weeks`, `total_cost`, `status`, `created_at`, `updated_at`, `result_type`) VALUES
(5, 'PKG-JIC1MW', 'Physiotherapy & Pain Management', 'physiotherapy', 'Conditions Treated: Sciatica • Arthritis • Back & Neck Pain • Muscle Weakness • Sports Injuries • Post-Surgery Rehabilitation • Joint Pain', 4, '850.00', 'active', '2026-02-10 12:48:10', '2026-02-10 12:48:10', 'text'),
(6, 'PKG-SLS2OB', 'Female Health & Hormonal Balance', 'wellness', 'Conditions Treated: Fibroid • Ovarian Cyst • Lump in Breast • PCOS • Irregular or Ceased Menses • Blocked Fallopian Tubes • Menstrual Cramps • Vaginal Infection', 9, '850.00', 'active', '2026-02-10 12:49:33', '2026-02-10 12:49:33', 'text'),
(7, 'PKG-B6KA9Y', 'Grant Blair', 'specialized', 'Itaque et a sed corp', 3, '650.00', 'active', '2026-02-11 18:40:05', '2026-02-11 18:40:05', 'file');

-- --------------------------------------------------------

--
-- Table structure for table `package_services`
--

CREATE TABLE `package_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `sessions` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `service_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `frequency_type` varchar(255) NOT NULL DEFAULT 'once',
  `frequency_value` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_services`
--

INSERT INTO `package_services` (`id`, `package_id`, `service_id`, `service_name`, `sessions`, `unit_price`, `service_total`, `notes`, `created_at`, `updated_at`, `frequency_type`, `frequency_value`) VALUES
(1, 7, 12, NULL, 1, '50.00', '50.00', NULL, '2026-02-11 18:40:05', '2026-02-11 18:40:05', 'once', 1),
(2, 7, 19, NULL, 3, '200.00', '600.00', NULL, '2026-02-11 18:40:05', '2026-02-11 18:40:05', 'per_week', 1);

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
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_code` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(255) DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `blood_type` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `marital_status` varchar(255) DEFAULT NULL,
  `blood_group` varchar(3) DEFAULT NULL,
  `sickle_cell_status` varchar(10) DEFAULT NULL,
  `chronic_conditions` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `registered_at` timestamp NULL DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_code`, `first_name`, `last_name`, `email`, `phone`, `date_of_birth`, `gender`, `address`, `city`, `state`, `postal_code`, `country`, `emergency_contact_name`, `emergency_contact_phone`, `medical_history`, `allergies`, `current_medications`, `height`, `weight`, `blood_type`, `status`, `created_at`, `updated_at`, `deleted_at`, `age`, `marital_status`, `blood_group`, `sickle_cell_status`, `chronic_conditions`, `photo_path`, `registered_at`, `occupation`) VALUES
(1, 'PAT-TVYWXW', 'Lara', 'Gates', 'huni@mailinator.com', '+1 (644) 113-6625', '1999-04-25', 'male', 'Sapiente cillum et e', NULL, NULL, NULL, NULL, 'Myra Rosales', '+1 (852) 163-6883', NULL, 'Voluptas ea sint exe', NULL, '182.00', NULL, NULL, 'active', '2026-02-11 14:51:23', '2026-02-11 14:52:08', NULL, 26, 'married', 'B+', 'AS', 'Est id nisi asperna', 'patient-photos/8WitXoyWys6CiDpbmZNBl4M0ykMBQjUE0ZncjAkW.jpg', '2026-02-11 14:51:23', 'Sit dolor optio quo');

-- --------------------------------------------------------

--
-- Table structure for table `patient_packages`
--

CREATE TABLE `patient_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `package_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `sessions_used` int(11) NOT NULL DEFAULT 0,
  `total_sessions` int(11) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient_packages`
--

INSERT INTO `patient_packages` (`id`, `patient_id`, `visit_id`, `package_id`, `package_price`, `status`, `start_date`, `end_date`, `sessions_used`, `total_sessions`, `notes`, `created_at`, `updated_at`) VALUES
(9, 1, 11, 6, '0.00', 'pending', NULL, NULL, 0, 1, NULL, '2026-02-11 16:52:17', '2026-02-11 16:52:17');

-- --------------------------------------------------------

--
-- Table structure for table `patient_services`
--

CREATE TABLE `patient_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `service_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `performed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `performed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient_services`
--

INSERT INTO `patient_services` (`id`, `patient_id`, `visit_id`, `service_id`, `service_price`, `status`, `notes`, `performed_by`, `performed_at`, `created_at`, `updated_at`) VALUES
(3, 1, 11, 18, '50.00', 'pending', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patient_visits`
--

CREATE TABLE `patient_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visit_date` date NOT NULL,
  `visit_time` time NOT NULL,
  `visit_type` varchar(50) NOT NULL DEFAULT 'consultation',
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `chief_complaint` text DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `attended_by` varchar(255) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(5,2) DEFAULT NULL,
  `blood_pressure` varchar(255) DEFAULT NULL,
  `heart_rate` int(11) DEFAULT NULL,
  `respiratory_rate` int(11) DEFAULT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance_due` decimal(10,2) NOT NULL DEFAULT 0.00,
  `package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `selected_services` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_services`)),
  `selected_package` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_package`)),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `history_present_illness` text DEFAULT NULL,
  `assessment` text DEFAULT NULL,
  `treatment_plan` text DEFAULT NULL,
  `practitioner` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `oxygen_saturation` int(11) DEFAULT NULL,
  `pulse_rate` int(11) DEFAULT NULL,
  `reason_for_visit` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient_visits`
--

INSERT INTO `patient_visits` (`id`, `patient_id`, `user_id`, `visit_date`, `visit_time`, `visit_type`, `status`, `chief_complaint`, `symptoms`, `diagnosis`, `treatment`, `notes`, `attended_by`, `height`, `weight`, `bmi`, `blood_pressure`, `heart_rate`, `respiratory_rate`, `temperature`, `payment_status`, `amount_paid`, `total_amount`, `balance_due`, `package_id`, `selected_services`, `selected_package`, `deleted_at`, `created_at`, `updated_at`, `history_present_illness`, `assessment`, `treatment_plan`, `practitioner`, `department`, `oxygen_saturation`, `pulse_rate`, `reason_for_visit`) VALUES
(11, 1, 3, '2026-02-11', '02:05:00', 'appointment', 'completed', 'Esse laborum quia i', NULL, NULL, NULL, 'Commodo quia aut qui', 'Michael Mireku', NULL, '54.00', '16.30', '156/78', 59, 21, '32.00', 'paid', '850.00', '900.00', '0.00', 6, '[{\"id\":\"18\",\"name\":\"Naturopath Consultation\",\"price\":50}]', '{\"id\":\"6\",\"name\":\"Female Health & Hormonal Balance\",\"price\":850}', NULL, NULL, NULL, 'Iure ut officia rati', 'Necessitatibus volup', 'Ut hic sit et reici', 'therapist-brown', 'general', 57, 77, 'Mollit Nam numquam r');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `bill_id` bigint(20) UNSIGNED NOT NULL,
  `amount_before` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(10,2) NOT NULL,
  `balance_after` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL,
  `received_by` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_date` date NOT NULL,
  `payment_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `patient_id`, `bill_id`, `amount_before`, `amount_paid`, `balance_after`, `payment_method`, `received_by`, `payment_date`, `payment_time`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '0.00', '850.00', '850.00', 'cash', 3, '2026-02-11', '2026-02-11 18:16:30', NULL, NULL);

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
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'Full system access', '2026-02-10 11:52:47', '2026-02-10 11:52:47'),
(2, 'Doctor / Naturopath', 'Clinical staff', '2026-02-04 13:15:12', '2026-02-04 13:15:12'),
(3, 'Physiotherapist', 'Therapy staff', '2026-02-04 13:15:12', '2026-02-04 13:15:12'),
(4, 'Front Desk', 'Front office staff', '2026-02-04 13:15:12', '2026-02-04 13:15:12'),
(5, 'Accountant', 'Finance staff', '2026-02-04 13:15:12', '2026-02-04 13:15:12');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_code` varchar(255) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `result_type` enum('text','numeric','file') NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_code`, `service_name`, `category`, `price`, `result_type`, `description`, `status`, `created_at`, `updated_at`) VALUES
(12, 'SVC-JX6BOR', 'Physiotherapist Consultation', 'consultation', '50.00', 'text', NULL, 'active', '2026-02-10 12:33:41', '2026-02-10 12:33:41'),
(13, 'SVC-7RDH1P', 'Physiotherapy Sessions', 'therapy', '100.00', 'text', NULL, 'active', '2026-02-10 12:34:13', '2026-02-10 12:35:43'),
(14, 'SVC-VLRRTF', 'Hand Massage', 'therapy', '150.00', 'text', 'Massage Therapy (Hand Massage)', 'active', '2026-02-10 12:34:52', '2026-02-10 12:34:52'),
(15, 'SVC-V0RDUC', 'Herbal Support for Pain & Inflammation', 'treatment', '200.00', 'file', NULL, 'active', '2026-02-10 12:35:26', '2026-02-10 12:35:26'),
(16, 'SVC-7YCVMQ', 'Review Session', 'consultation', '0.00', 'text', NULL, 'active', '2026-02-10 12:36:12', '2026-02-10 12:36:12'),
(17, 'SVC-6WAPZT', 'Herbal Therapy & Supplements', 'treatment', '200.00', 'text', NULL, 'active', '2026-02-10 12:36:57', '2026-02-10 12:36:57'),
(18, 'SVC-9GQQZK', 'Naturopath Consultation', 'consultation', '50.00', 'text', NULL, 'active', '2026-02-10 12:37:21', '2026-02-10 12:37:21'),
(19, 'SVC-P7IHIL', 'Herbal Support for Pain & Inflammation', 'therapy', '200.00', 'text', NULL, 'active', '2026-02-11 18:21:55', '2026-02-11 18:21:55');

-- --------------------------------------------------------

--
-- Table structure for table `service_results`
--

CREATE TABLE `service_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `result_type` enum('text','numeric','file','package') NOT NULL DEFAULT 'text',
  `result_text` text DEFAULT NULL,
  `result_numeric` decimal(10,2) DEFAULT NULL,
  `result_file_path` varchar(255) DEFAULT NULL,
  `result_file_name` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('draft','pending_approval','approved','rejected') NOT NULL DEFAULT 'draft',
  `recorded_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `recorded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_results`
--

INSERT INTO `service_results` (`id`, `patient_id`, `visit_id`, `service_id`, `package_id`, `patient_service_id`, `patient_package_id`, `result_type`, `result_text`, `result_numeric`, `result_file_path`, `result_file_name`, `notes`, `status`, `recorded_by`, `approved_by`, `approved_at`, `approval_notes`, `recorded_at`, `created_at`, `updated_at`) VALUES
(3, 1, 11, NULL, 6, NULL, 9, 'text', 'changed to text', NULL, NULL, NULL, NULL, 'approved', 3, NULL, NULL, NULL, NULL, '2026-02-11 16:52:17', '2026-02-11 18:06:52'),
(4, 1, 11, 18, NULL, 3, NULL, 'text', NULL, NULL, 'service-results/1770831795_0LbylvXrf9.pdf', 'MICHAEL NANA YAW MIREKU\'s Posting Letter.pdf', NULL, 'approved', 3, NULL, NULL, NULL, NULL, '2026-02-11 16:52:17', '2026-02-11 18:03:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `role_id`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(3, 'Michael Mireku', 'michaelmireku098@gmail.com', '0206546759', NULL, '$2y$10$47g6wHeVHH.zlWIcDs8EUexzHKj0FLSJaiytCbfFoHEe7la4ehGhK', 1, 'active', NULL, '2026-02-04 13:15:35', '2026-02-04 13:15:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bills_created_by_foreign` (`created_by`),
  ADD KEY `bills_patient_id_index` (`patient_id`),
  ADD KEY `bills_visit_id_index` (`visit_id`),
  ADD KEY `bills_status_index` (`status`),
  ADD KEY `bills_due_date_index` (`due_date`);

--
-- Indexes for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_items_bill_id_item_type_index` (`bill_id`,`item_type`),
  ADD KEY `bill_items_service_id_bill_id_index` (`service_id`,`bill_id`),
  ADD KEY `bill_items_package_id_bill_id_index` (`package_id`,`bill_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`),
  ADD KEY `notifications_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `packages_package_code_unique` (`package_code`);

--
-- Indexes for table `package_services`
--
ALTER TABLE `package_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_services_package_id_service_id_unique` (`package_id`,`service_id`),
  ADD KEY `package_services_service_id_foreign` (`service_id`),
  ADD KEY `package_services_package_id_service_id_index` (`package_id`,`service_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patients_patient_code_unique` (`patient_code`),
  ADD KEY `patients_patient_code_index` (`patient_code`),
  ADD KEY `patients_first_name_last_name_index` (`first_name`,`last_name`);

--
-- Indexes for table `patient_packages`
--
ALTER TABLE `patient_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_packages_package_id_foreign` (`package_id`),
  ADD KEY `patient_packages_patient_id_package_id_index` (`patient_id`,`package_id`),
  ADD KEY `patient_packages_visit_id_index` (`visit_id`);

--
-- Indexes for table `patient_services`
--
ALTER TABLE `patient_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_services_service_id_foreign` (`service_id`),
  ADD KEY `patient_services_performed_by_foreign` (`performed_by`),
  ADD KEY `patient_services_patient_id_service_id_index` (`patient_id`,`service_id`),
  ADD KEY `patient_services_visit_id_index` (`visit_id`),
  ADD KEY `patient_services_status_index` (`status`);

--
-- Indexes for table `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_visits_user_id_foreign` (`user_id`),
  ADD KEY `patient_visits_package_id_foreign` (`package_id`),
  ADD KEY `patient_visits_patient_id_visit_date_index` (`patient_id`,`visit_date`),
  ADD KEY `patient_visits_visit_date_index` (`visit_date`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_bill_id_foreign` (`bill_id`),
  ADD KEY `payments_received_by_foreign` (`received_by`),
  ADD KEY `payments_patient_id_bill_id_index` (`patient_id`,`bill_id`),
  ADD KEY `payments_payment_date_index` (`payment_date`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_role_name_unique` (`role_name`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `services_service_code_unique` (`service_code`);

--
-- Indexes for table `service_results`
--
ALTER TABLE `service_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_results_patient_id_foreign` (`patient_id`),
  ADD KEY `service_results_visit_id_foreign` (`visit_id`),
  ADD KEY `service_results_package_id_foreign` (`package_id`),
  ADD KEY `service_results_service_id_foreign` (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bill_items`
--
ALTER TABLE `bill_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `package_services`
--
ALTER TABLE `package_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `patient_packages`
--
ALTER TABLE `patient_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `patient_services`
--
ALTER TABLE `patient_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `patient_visits`
--
ALTER TABLE `patient_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `service_results`
--
ALTER TABLE `service_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bills_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bills_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD CONSTRAINT `bill_items_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bill_items_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bill_items_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `package_services`
--
ALTER TABLE `package_services`
  ADD CONSTRAINT `package_services_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_packages`
--
ALTER TABLE `patient_packages`
  ADD CONSTRAINT `patient_packages_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_packages_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_packages_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_services`
--
ALTER TABLE `patient_services`
  ADD CONSTRAINT `patient_services_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_services_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patient_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_services_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_visits`
--
ALTER TABLE `patient_visits`
  ADD CONSTRAINT `patient_visits_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patient_visits_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `service_results`
--
ALTER TABLE `service_results`
  ADD CONSTRAINT `service_results_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_results_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_results_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_results_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `patient_visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
