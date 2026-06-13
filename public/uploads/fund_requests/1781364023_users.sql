-- -------------------------------------------------------------
-- TablePlus 6.8.2(656)
--
-- https://tableplus.com/
--
-- Database: hgnl_local
-- Generation Time: 2026-03-24 01:24:13.8120
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- DROP TABLE IF EXISTS `users`;
-- CREATE TABLE `users` (
--   `id` bigint unsigned NOT NULL AUTO_INCREMENT,
--   `member_id` varchar(255) NOT NULL,
--   `name` varchar(255) NOT NULL,
--   `email` varchar(255) NOT NULL,
--   `password` varchar(255) NOT NULL,
--   `is_admin` tinyint(1) NOT NULL DEFAULT '0',
--   `is_superadmin` tinyint(1) NOT NULL DEFAULT '0',
--   `username` varchar(255) DEFAULT NULL,
--   `mobile` varchar(20) DEFAULT NULL,
--   `pan_number` varchar(20) DEFAULT NULL,
--   `state` varchar(100) DEFAULT NULL,
--   `city` varchar(100) DEFAULT NULL,
--   `pincode` varchar(20) DEFAULT NULL,
--   `dob` date DEFAULT NULL,
--   `address` text,
--   `account_number` varchar(50) DEFAULT NULL,
--   `account_holder_name` varchar(255) DEFAULT NULL,
--   `bank_name` varchar(100) DEFAULT NULL,
--   `branch_name` varchar(100) DEFAULT NULL,
--   `bank_address` text,
--   `ifsc_code` varchar(20) DEFAULT NULL,
--   `nominee_name` varchar(255) DEFAULT NULL,
--   `relation` varchar(100) DEFAULT NULL,
--   `nominee_dob` date DEFAULT NULL,
--   `nominee_gender` varchar(20) DEFAULT NULL,
--   `sponsor_id` bigint unsigned DEFAULT NULL,
--   `placement_id` bigint unsigned DEFAULT NULL,
--   `sponsor_name` varchar(255) DEFAULT NULL,
--   `position` varchar(50) DEFAULT NULL,
--   `leg` varchar(255) DEFAULT NULL,
--   `applied_for` varchar(100) DEFAULT NULL,
--   `wallet_balance` decimal(15,2) DEFAULT '0.00',
--   `id_proof` varchar(255) DEFAULT NULL,
--   `address_proof` varchar(255) DEFAULT NULL,
--   `account_proof` varchar(255) DEFAULT NULL,
--   `email_verified_at` timestamp NULL DEFAULT NULL,
--   `remember_token` varchar(100) DEFAULT NULL,
--   `created_at` timestamp NULL DEFAULT NULL,
--   `updated_at` timestamp NULL DEFAULT NULL,
--   `investment_count` int NOT NULL DEFAULT '0',
--   `emi_status` varchar(255) NOT NULL DEFAULT 'ongoing',
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `member_id` (`member_id`),
--   UNIQUE KEY `email` (`email`),
--   UNIQUE KEY `users_member_id_unique` (`member_id`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`member_id`, `name`, `email`, `password`, `is_admin`, `is_superadmin`, `username`, `mobile`, `pan_number`, `state`, `city`, `pincode`, `dob`, `address`, `account_number`, `account_holder_name`, `bank_name`, `branch_name`, `bank_address`, `ifsc_code`, `nominee_name`, `relation`, `nominee_dob`, `nominee_gender`, `sponsor_id`, `placement_id`, `sponsor_name`, `position`, `leg`, `applied_for`, `wallet_balance`, `id_proof`, `address_proof`, `account_proof`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`, `investment_count`, `emi_status`) VALUES
('HGNL1001', 'ADMIN', 'admin@hgnlpay.com', '$2y$12$/m7nDBJtf/vrZSRJArDKk.1kPKzpVStL5Xd0m3JqYr1mV3ZF.Tvxq', 0, 0, 'admin_hgnl', '9999988888', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-03-20 16:03:44', '2026-03-20 16:03:44', 0, 'ongoing'),
('HGNL1002', 'HGNLPAY', 'support@hgnlpay.com', '$2y$12$wi4sGN6sjCoh/eFgG0xyQeqLD27YSiHY57fWigpXu4J/2oW1sc8ry', 0, 0, 'HGNLPAY', '7777766666', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-03-20 16:03:44', '2026-03-20 17:00:08', 0, 'ongoing');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;