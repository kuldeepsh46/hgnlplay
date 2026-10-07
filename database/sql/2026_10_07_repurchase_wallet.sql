-- Same as migration 2026_10_07_000000_create_repurchase_wallet,
-- for running by hand (e.g. phpMyAdmin) if migrations are not used on the server.
ALTER TABLE `wallets`
  ADD COLUMN `repurchase_balance` decimal(15,2) NOT NULL DEFAULT 0.00;

ALTER TABLE `transactions`
  ADD COLUMN `main_wallet_amount` decimal(15,2) DEFAULT NULL,
  ADD COLUMN `repurchase_wallet_amount` decimal(15,2) DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `repurchase_wallet_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(10) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `source` varchar(50) NOT NULL,
  `transaction_id` bigint unsigned DEFAULT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rwt_user_created_index` (`user_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record it as migrated so `php artisan migrate` won't try to run it again.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT `m`.`migration`, `m`.`batch` FROM (
    SELECT '2026_10_07_000000_create_repurchase_wallet' AS `migration`, COALESCE(MAX(`batch`), 0) + 1 AS `batch` FROM `migrations`
) AS `m`
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_10_07_000000_create_repurchase_wallet');
